<?php

/**
 * This class represents a Splash Controller that has been adapted to run correctly with Drupal.
 * This is a usual controller expect it has a few utility functions added, like "addContentFile", "addContentText", "addContentFunction", etc...
 * 
 * Also, it will make sure that any output "echoed" is returned to Drupal, using a output buffer.
 * 
 * @author David
 */
abstract class DrupalController extends Controller {
	
	/**
	 * The HTML elements that will be written in the <head> tag.
	 *
	 * @Property
	 * @var array<HtmlElementInterface>
	 */	
	public $head;
	
	/**
	 * The HTML elements that will be displayed on the center of the screen.
	 *
	 * @Property
	 * @var array<HtmlElementInterface>
	 */
	public $content = array();
	
	/**
	 * Whether the current request is an Ajax request (value = true),
	 * a normal request (value = false), or undecided (value = null).
	 * 
	 * @var bool
	 */
	private $isAjax = null;
	
	/**
	 * Adds some content to the main panel by calling the function passed in parameter.
	 * @return SplashTemplate
	 */
	public function addContentFunction($function) {
		$arguments = func_get_args();
		// Remove the first argument
		array_shift($arguments);

		$content = new HtmlFromFunction();
		$content->functionPointer = $function;
		$content->parameters = $arguments;
		$this->content[] = $content;
		return $this;
	}

	/**
	 * Adds some content to the main panel by displaying the text passed in parameter.
	 * @return SplashTemplate
	 */
	public function addContentText($text) {
		$content = new HtmlString();
		$content->htmlString = $text;
		$this->content[] = $content;
		return $this;
	}
	
	/**
	 * Adds some content to the main panel by displaying the text in the file passed in parameter.
	 * The scope is the object that will refer the $this.
	 * @return SplashTemplate
	 */
	public function addContentFile($fileName, Scopable $scope = null) {
		$content = new HtmlFromFile();
		$content->fileName = $fileName;
		if ($scope != null) {
			$content->scope = $scope;
		} else {
			$content->scope = $this;
		}
		$this->content[] = $content;
		
		return $this;
	}
	
	/**
	 * Adds an object extending the HtmlElementInterface interface to the content of the page.
	 *
	 * @param HtmlElementInterface $element
	 * @return SplashTemplate
	 */
	public function addContentHtmlElement(HtmlElementInterface $element) {
		$this->content[] = $element;
		return $this;
	}
	
	/**
	 * Adds some content to the <head> tag by calling the function passed in parameter.
	 * @return SplashTemplate
	 */
	public function addHeadFunction($function) {
		$arguments = func_get_args();
		// Remove the first argument
		array_shift($arguments);

		$content = new HtmlFromFunction();
		$content->functionPointer = $function;
		$content->parameters = $arguments;
		$this->head[] = $content;
		return $this;
	}

	/**
	 * Adds some content to the <head> tag by displaying the text passed in parameter.
	 * @return SplashTemplate
	 */
	public function addHeadText($text) {
		$content = new HtmlString();
		$content->htmlString = $text;
		$this->head[] = $content;
		return $this;
	}

	/**
	 * Adds some content to the <head> tag by displaying the text in the file passed in parameter.
	 * The scope is the object that will refer the $this.
	 * @return SplashTemplate
	 */
	public function addHeadFile($fileName, Scopable $scope = null) {
		$content = new HtmlFromFile();
		$content->fileName = $fileName;
		$content->scope = $scope;
		$this->head[] = $content;
		
		return $this;
	}
	
	/**
	 * Adds an object extending the HtmlElementInterface interface to the head of the template.
	 *
	 * @param HtmlElementInterface $element
	 * @return SplashTemplate
	 */
	public function addHeadHtmlElement(HtmlElementInterface $element) {
		$this->head[] = $element;
		return $this;
	}
	
	/**
	 * Calls the action (and does only call the action).
	 * 
	 * @param string $methodName
	 * @param array $args
	 */
	protected function executeActionMethod($methodName, $args) {
		if (!empty($this->head)) {
			ob_start();
			foreach ($this->head as $element) {
				$element->toHtml();
			}
			$head = ob_get_clean();
			drupal_set_html_head($head);
		}
		
		ob_start();
		try {
			echo call_user_func_array(array($this,$methodName), $args);
		} catch (Exception $e) {
			ob_end_clean();
			throw $e;
		}
		foreach ($this->content as $element) {
			$element->toHtml();
		}
		return ob_get_clean();
	}
	
	/**
	 * Sets the title of the webpage.
	 * It also sets the title in the template.
	 * 
	 * @param $title
	 */
	public function setTitle($title) {
		drupal_set_title($title);
	}
	
	/**
	 * Executes the action passed in parameter.
	 * 
	 * @param string $method The method name to be called.
	 */
	public function callAction($method) {
		// Default action is "defaultAction" or "index"
		
		if (empty($method)) {
			// Support for both defaultAction, and if not foudn "index" method.
			if (method_exists($this,"defaultAction")) {
				$method = "defaultAction";
			} else {
				$method = "index";
			}
		}

		if (method_exists($this,$method)) {
			// Ok, is this method an action?
			$refClass = new MoufReflectionClass(get_class($this));
			$refMethod = $refClass->getMethod($method);    // $refMethod is an instance of stubReflectionMethod
			//$this->getLogger()->trace("REF METHOD : ".$refMethod." // has annotation Action ? ".$refMethod->hasAnnotation('Action'));
			
			/*if ($refMethod->hasAnnotation('Action') == false) {
				//$debug = MoufManager::getMoufManager()->getInstance("splash")->debugMode;
				//$debug = MoufManager::getMoufManager()->getInstance("splash")->debugMode;
				// This is not an action. Let's go in error.
				self::FourOFour(iMsg("controller.404.no.action", get_class($this), $method), true);
				exit;
			}*/

			try {
				$filters = FilterUtils::getFilters($refMethod, $this);
				
				// Apply filters
				for ($i=count($filters)-1; $i>=0; $i--) {
					$filters[$i]->beforeAction();
				}

				// Ok, now, let's analyse the parameters.
				$argsArray = $this->mapParameters($refMethod);

				//call_user_func_array(array($this,$method), AdminBag::getInstance()->argsArray);
				//$result = call_user_func_array(array($this,$method), $argsArray);
				$result = $this->executeActionMethod($method, $argsArray);
				
			// Apply filters
				for ($i=count($filters)-1; $i>=0; $i--) {
					$filters[$i]->afterAction();
				}
				
				// If @DrupalAjax is set, we must echo the result, instead of returning it. This way, Drupal will not theme the output.
				if ($this->isAjax === false) {
					return $result;
				} elseif ($refMethod->hasAnnotation('DrupalAjax') == true || $this->isAjax === true) {
					echo $result;
				} else {
					return $result;
				}
				
			}
			catch (Exception $e) {
				return $this->handleException($e);
			}
		}else {
			// "Method Not Found";
			//$debug = MoufManager::getMoufManager()->getInstance("splash")->debugMode;
			// FIXME: $debug non disponible car "splash" instance n'exite pas dans Drupal
			//self::FourOFour("404.wrong.method", $debug);
			self::FourOFour("404.wrong.method", true);
			exit;
		}
		
	}
	
	public function handleException (Exception $e) {
		ob_start();
		if (!headers_sent()) {			
			// FIXME: manage the debug mode again.
			if($e instanceof ApplicationException ) {
				FiveOO($e, true);
				//$template->addContentFunction("FiveOO",$e,$debug);
			}else {
				UnhandledException($e, true);
				//$template->addContentFunction("UnhandledException",$e,$debug);
			}
			//$template->draw();
			
		} else {
			UnhandledException($e,$debug);
		}
		return ob_get_clean();
	}
	
	/**
	 * Sets whether a page is displayed as an Ajax response (so without the theme), or as a normal page.
	 * Note: instead of using this method, we recommand using the @DrupalAjax annotation.
	 * 
	 * Pass "true" as a parameter to display the content directly.
	 * Pass "false" as a parameter to display the page normally (with the theme).
	 * Pass "null" to let the @DrupalAjax annotation decide.
	 * 
	 * 
	 * @param boolean $ajax
	 */
	public function setAjaxStatus($ajax) {
		$this->isAjax = $ajax;
	}
	
	/**
	 * Analyses the method, the annotation parameters, and returns an array to be passed to the method.
	 * TODO: optimize, remove mapParameters and use preprocessed values
	 */
	private function mapParameters(MoufReflectionMethod $refMethod) {
		$parameters = $refMethod->getParameters();
	
		// Let's analyze the @param annotations.
		$paramAnnotations = $refMethod->getAnnotations('param');
	
		$values = array();
		foreach ($parameters as $parameter) {
			// First step: let's see if there is an @param annotation for that parameter.
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
				$paramValue = get($parameter->getName());
	
				if ($paramValue !== false) {
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