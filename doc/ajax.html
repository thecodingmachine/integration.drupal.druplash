<h1>Going Ajax with Druplash</h1>

<p>You probably noticed in the tutorial that any outputed text in your controller is directly embeded into the Drupal's theme.
Let's see an example:</p>

<pre class="brush: php">
/**
 * @Component
 */
class MyController extends DrupalController {
	
	/**
	 * @Action
	 * @URL hello/world
	 */
	public function index() {
		echo "Hello world!";
	}
}
</pre>

<p>In the example above, when browsing to the page '/hello/world', the user will see the text "Hello world!" displayed in the
'content' section of the default Drupal's theme.</p>

<p>This default behaviour is great, because most of the time, this is the expected behaviour. But there are situations
where we would like to get rid of the theme and just output directly in the HTTP stream. For instance, you might want to:</p>

<ul>
	<li>Output a XML or JSON message that will be consumed by an Ajax script</li>
	<li>Output a dynamically generated image using GD2 / ImageGraph / Artichow or your favorite graph library</li>
	<li>Allow the download of a binary file</li>
</ul>

<p>For all those situations, Druplash provides a special tag: @DrupalAjax. Here is an example:</p>

<pre class="brush: php">
/**
 * @Component
 */
class MyAjaxController extends DrupalController {
	
	/**
	 * @Action
	 * @DrupalAjax
	 * @URL ajax/world
	 */
	public function index() {
		echo "{hello:'world!'}";
	}
}
</pre>

<p>In this exemple, when browsing to the page '/ajax/world', the user will directly see the text, and it will not be embeded into 
the Drupal's template.</p>

<p>In the rare case where you might not know before calling the action whether the answer will be embeded in the template or not,
Druplash provides you a handy method: <em>setAjaxStatus</em>. Here is an exemple:</p>

<pre class="brush: php">
/**
 * @Component
 */
class MyAjaxController extends DrupalController {
	
	/**
	 * @Action
	 * @URL conditional/ajax
	 */
	public function index($ajax = "false") {
		if ($ajax == "true") {
			$this->setAjaxStatus(true);
		}
		echo "Hello world!";
	}
}
</pre>

<p>In this exemple, when browsing to the page '/conditional/ajax', the user will see the text embeded in the template,
and when browsing '/conditional/ajax?ajax=true', the text will not be embeded into the Drupal's template.</p>

