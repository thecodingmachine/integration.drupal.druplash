<?php
namespace Mouf\Integration\Drupal\Druplash;

use Mouf\Reflection\MoufReflectionMethod;

use Mouf\Mvc\Splash\Services\FilterUtils;

use Mouf\Reflection\MoufReflectionClass;

use Mouf\Reflection\MoufPhpDocComment;

use Mouf\Reflection\MoufReflectionProxy;

use Mouf\Mvc\Splash\Utils\SplashException;

use Mouf\Mvc\Splash\Services\SplashUtils;
use Mouf\MoufManager;
use Mouf;
use Exception;

/**
 * Main class in charge of routing
 * @author David
 *
 */
class Druplash {
	/**
	 * Returns the list of URLs expected by hook_menu.
	 */
	public static function getDrupalMenus() {
		$allConstants = get_defined_constants();
		$urlsList = SplashUtils::getSplashUrlManager()->getUrlsList(false);
		
		$items = array();
		
		foreach ($urlsList as $urlCallback) {
			/* @var $urlCallback SplashCallback */
			
			$url = $urlCallback->url;
			// remove trailing slash
			$url = rtrim($url, "/");
			
			$title = 'Action '.$urlCallback->methodName.' for controller '.$urlCallback->controllerInstanceName;
			if ($urlCallback->title !== null) {
				$title = $urlCallback->title ;
			}
			
			
			//////////////// Let's analyze the URL for parameter ////////////////////
			$trimmedUrl = trim($url, '/');
			$urlParts = explode("/", $trimmedUrl);
			$urlPartsNew = array();
			$parametersList = array();
			
			for ($i=0; $i<count($urlParts); $i++) {
				$urlPart = $urlParts[$i];
				if (strpos($urlPart, "{") === 0 && strpos($urlPart, "}") === strlen($urlPart)-1) {
					// Parameterized URL element
					$varName = substr($urlPart, 1, strlen($urlPart)-2);
			
					$parametersList[$varName] = $i;
					$urlPartsNew[] = '%';
				} else {
					$urlPartsNew[] = $urlPart;
				}
			}
			
			// Let's rewrite the URL, but replacing the {var} parameters with a Drupal % wildcard
			$url = implode('/', $urlPartsNew);
			///////////////// End URL analysis ////////////////////
			
			
			
			//getItemMenuSettings from annotation
			$annotations = MoufManager::getMoufManager()->getInstanceDescriptor($urlCallback->controllerInstanceName)->getClassDescriptor()->getMethod($urlCallback->methodName)->getAnnotations("DrupalMenuSettings");
			$settings = array();
			if ($annotations){
				if (count($annotations) > 1){
					throw new SplashException('Action '.$urlCallback->methodName.' for controller '.$urlCallback->controllerInstanceName.' should have at most 1 "DrupalMenuSettings" annotation');
				}
				else{
					$settings = json_decode($annotations[0]);
				}
			}
			
			// Recover function filters
			$phpDocComment = new MoufPhpDocComment($urlCallback->fullComment);
			$requiresRightArray = $phpDocComment->getAnnotations('RequiresRight');
			$accessArguments = array();
			if(count($requiresRightArray)) {
				foreach ($requiresRightArray as $requiresRight) {
					/* @var $requiresRight RequiresRight */
					$accessArguments[] = $requiresRight->getName();
				}
			} else {
				$accessArguments[] = 'access content';
			}
			
			$httpMethods = $urlCallback->httpMethods;
			if (empty($httpMethods)) {
				$httpMethods[] = "default";
			}
			
			foreach ($httpMethods as $httpMethod) {
			
				if (isset($items[$url])) {
					// FIXME: support different 'access arguments' for different HTTP methods!
					// TODO: support for {params} in @URL
					
					// Check that the URL has not been already declared.
					if (isset($items[$url]['page arguments'][0][$httpMethod])) {
						$msg = "Error! The URL '".$url."' ";
						if ($httpMethod != "default") {
							$msg .= "for HTTP method '".$httpMethod."' ";
						}
						$msg .= " has been declared twice: once for instance '".$urlCallback->controllerInstanceName."' and method '".$urlCallback->methodName."' ";
						$oldCallback = $items[$url]['page arguments'][0][$httpMethod];
						$msg .= " and once for instance '".$oldCallback['instance']."' and method '".$oldCallback['method']."'. The instance  '".$oldCallback['instance']."', method '".$oldCallback['method']."' will be ignored.";
						//throw new MoufException($msg);
						drupal_set_message($msg, "error");
					}
					
					$items[$url]['page arguments'][0][$httpMethod] = array("instance"=>$urlCallback->controllerInstanceName, "method"=>$urlCallback->methodName, "urlParameters"=>$parametersList);
				} else {
					$items[$url] = array(
					    'title' => $title,
					    'page callback' => 'druplash_execute_action',
					    'access arguments' => $accessArguments,
						'page arguments' => array(array($httpMethod => array("instance"=>$urlCallback->controllerInstanceName, "method"=>$urlCallback->methodName, "urlParameters"=>$parametersList))),
					    'type' => MENU_VISIBLE_IN_BREADCRUMB
					);
					
					foreach ($settings as $key => $value){
						if ($key == "type"){
							$value = $allConstants[$value];
						}
						$items[$url][$key] = $value;
					}
				}
			}
			
		}
		
		return $items;
	}
	
	/**
	 * Executes an action.
	 * This method is triggered from the Druplash menu hook.
	 * 
	 * @param string $actions
	 */
	public static function executeAction($actions) {
		$httpMethod = $_SERVER['REQUEST_METHOD'];
		
		if (isset($actions[$httpMethod])) {
			$action = $actions[$httpMethod];
		} elseif (isset($actions["default"])) {
			$action = $actions["default"];
		} else {
			drupal_not_found();
		}

		$controller = MoufManager::getMoufManager()->getInstance($action['instance']);
		return self::callAction($controller, $action['method'], $action['urlParameters']);
	}
	
	protected static function callAction($controller, $method, $urlParameters) {
		// Default action is "defaultAction" or "index"
		
		if (empty($method)) {
			// Support for both defaultAction, and if not found "index" method.
			if (method_exists($this,"defaultAction")) {
				$method = "defaultAction";
			} else {
				$method = "index";
			}
		}
		
		if (method_exists($controller,$method)) {
			// Ok, is this method an action?
			$refClass = new MoufReflectionClass(get_class($controller));
			// FIXME: the analysis should be performed during getDrupalMenus for performance.
			$refMethod = $refClass->getMethod($method);    // $refMethod is an instance of MoufReflectionMethod
		
			try {
				$filters = FilterUtils::getFilters($refMethod, $controller);
		
				// Apply filters
				for ($i=count($filters)-1; $i>=0; $i--) {
					$filters[$i]->beforeAction();
				}
		
				// Ok, now, let's analyse the parameters.
				$argsArray = self::mapParameters($refMethod, $urlParameters);
				
				ob_start();
				try {
					echo call_user_func_array(array($controller,$method), $argsArray);
				} catch (Exception $e) {
					ob_end_clean();
					throw $e;
				}
				/*foreach ($this->content as $element) {
					$element->toHtml();
				}*/
				$drupalTemplate = Mouf::getDrupalTemplate();
				if ($drupalTemplate->isDisplayTriggered()) {
					$drupalTemplate->getContentBlock()->toHtml();
				}
				$result = ob_get_clean();
				
				
				// Apply filters
				for ($i=count($filters)-1; $i>=0; $i--) {
					$filters[$i]->afterAction();
				}
		
				// Now, let's see if we must output everything in the template or out the template.
				
				if ($drupalTemplate->isDisplayTriggered()) {
					return $result;
				} else {
					echo $result;
				}
		
			}
			catch (Exception $e) {
				// FIXME
				return $this->handleException($e);
			}
		} else {
			// "Method Not Found";
			//$debug = MoufManager::getMoufManager()->getInstance("splash")->debugMode;
			// FIXME: $debug non disponible car "splash" instance n'exite pas dans Drupal
			//self::FourOFour("404.wrong.method", $debug);
			
			// FIXME
			self::FourOFour("404.wrong.method", true);
			exit;
		}
	}
	
	/**
	 * Returns a list of blocks.
	 * This will return a list of all DrupalDynamicBlock instances to Drupal's hook_block
	 * (in the format described at http://api.drupal.org/api/function/hook_block/6)
	 */
	public static function getDrupalBlocks() {
		$moufManager = MoufManager::getMoufManager();
		
		$instanceNames = MoufReflectionProxy::getInstances("Mouf\\Integration\\Drupal\\Druplash\\DrupalDynamicBlockInterface", false);
		
		$blocks = array();
		
		foreach ($instanceNames as $instanceName) {
			$moufBlock = $moufManager->getInstance($instanceName);
			/* @var $moufBlock DrupalDynamicBlockInterface */
			$block = array("info"=>$moufBlock->getName(),
							"cache"=>(int)$moufBlock->getCache(),
							'weight'=>$moufBlock->getWeight(), 
							'status'=>$moufBlock->getStatus(),
							'region'=>$moufBlock->getRegion(),
							'visibility'=>$moufBlock->getVisibility(),
							'pages'=>$moufBlock->getPages());
			
			$blocks[$instanceName] = $block;
		}
		return $blocks;
	}
	
	/**
	 * Returns a Drupal node in the format expected for Drupal hooks.
	 * 
	 * @param string $instanceName
	 */
	public static function getDrupalBlock($instanceName) {
		$moufManager = MoufManager::getMoufManager();
		$moufBlock = $moufManager->getInstance($instanceName);
		/* @var $moufBlock DrupalDynamicBlock */
		return array('subject'=>$moufBlock->getSubject(),
					'content'=>$moufBlock->getContent());
	}
	
	/**
	 * Set user information in Druplash SESSION.
	 * 
	 * @param array $edit
	 * @param stdClass $account
	 */
	public static function onUserLogin($edit, $account) {
		//TODO: an admin page will be necessary to select which user service instance to use.
		$moufManager = MoufManager::getMoufManager();
		if($moufManager->instanceExists('userService') && isset($edit['values']['pass'])) {
			$userService = $moufManager->getInstance('userService');
			/* @var $userService MoufUserService */
			$pass = isset($edit['values'])?$edit['values']['pass']:$edit['pass'];
			$userService->login($account->name, $pass);
		}
	}
	
	/**
	 * Remove user information in Druplash SESSION.
	 * 
	 * @param stdClass $account
	 */
	public static function onUserLogout($account) {
		//TODO: an admin page will be necessary to select which user service instance to use.
		$moufManager = MoufManager::getMoufManager();
		if($moufManager->instanceExists('userService')) {
			$userService = $moufManager->getInstance('userService');
			/* @var $userService MoufUserService */
			$userService->logoff();
		}
	}
	
	/**
	 * Returns all permissions for hook_permission.
	 * 
	 */
	public static function getPermissions() {
		//TODO: an admin page will be necessary to select which right service instance to use.
		$moufManager = MoufManager::getMoufManager();
		if($moufManager->instanceExists('rightsService')) {
			$rightsService = $moufManager->getInstance('rightsService');
			if($rightsService instanceof DruplashRightService) {
				/* @var $rightsService DruplashRightService */
				return $rightsService->getDrupalPermissions();
			} else 
				return array();
		} else {
			return array();
		}
	}
	
	/**
	 * Analyses the method, the annotation parameters, and returns an array to be passed to the method.
	 * TODO: optimize, remove mapParameters and use preprocessed values
	 * 
	 * @param MoufReflectionMethod $refMethod
	 * @param array<string, int> $urlParameters An array mapping the parameter name to its position in the URL (0 being the left-most position)
	 * @throws \Mouf\Integration\Drupal\Druplash\ApplicationException
	 * @return array
	 */
	private static function mapParameters(MoufReflectionMethod $refMethod, array $urlParameters) {
		$parameters = $refMethod->getParameters();
	
		// Let's analyze the @param annotations.
		$paramAnnotations = $refMethod->getAnnotations('param');
	
		$values = array();
		foreach ($parameters as $parameter) {
			// First, is this a parameter from the path of the URL?
			if (isset($urlParameters[$parameter->getName()])) {
				$pos = $urlParameters[$parameter->getName()];
				$values[] = arg($pos);
				continue;
			}
			
			// Second step: let's see if there is an @param annotation for that parameter.
			$found = false;
			if ($paramAnnotations != null) {
				foreach ($paramAnnotations as $annotation) {
					/* @var paramAnnotation $annotation */
	
					if ($annotation->getParameterName() == $parameter->getName()) {
						$value = $annotation->getValue();
	
						if ($value !== null) {
							$values[] = $value;
						} else {
							if ($parameter->isDefaultValueAvailable()) {
								$values[] = $parameter->getDefaultValue();
							} else {
								// No default value and no parameter... this is an error!
								// TODO: we could provide a special annotation to redirect on another action on error.
								$application_exception = new ApplicationException();
								$application_exception->setTitle("controller.incorrect.parameter.title",$refMethod->getDeclaringClass()->getName(),$refMethod->getName(),$parameter->getName());
								$application_exception->setMessage("controller.incorrect.parameter.text",$refMethod->getDeclaringClass()->getName(),$refMethod->getName(),$parameter->getName());
								throw $application_exception;
							}
						}
						$found = true;
						break;
					}
				}
			}
	
			if (!$found) {
				// There is no annotation for the parameter.
				// Let's map it to the request.
				$paramValue = isset($_REQUEST[$parameter->getName()])?$_REQUEST[$parameter->getName()]:null;
	
				if ($paramValue !== null) {
					$values[] = $paramValue;
				} else {
					if ($parameter->isDefaultValueAvailable()) {
						$values[] = $parameter->getDefaultValue();
					} else {
						// No default value and no parameter... this is an error!
						// TODO: we could provide a special annotation to redirect on another action on error.
						$application_exception = new ApplicationException();
						$application_exception->setTitle("controller.incorrect.parameter.title",$refMethod->getDeclaringClass()->getName(),$refMethod->getName(),$parameter->getName());
						$application_exception->setMessage("controller.incorrect.parameter.text",$refMethod->getDeclaringClass()->getName(),$refMethod->getName(),$parameter->getName());
						throw $application_exception;
					}
				}
			}
	
	
		}
	
		return $values;
	}
	

}

?>