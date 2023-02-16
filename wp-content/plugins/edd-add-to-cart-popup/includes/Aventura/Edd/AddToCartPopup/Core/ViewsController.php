<?php

namespace Aventura\Edd\AddToCartPopup\Core;

/**
 * Views controller class, for loading and rendering views.
 */
class ViewsController extends Plugin\Module {

	/**
	 * @var string
	 */
	protected $_namespace;

	/**
	 * Constructor.
	 */
	protected function _construct() {
		$this->setViewsNamespace(__NAMESPACE__ . '\\Views');
	}

	/**
	 * Gets the namespace from where views are loaded.
	 * 
	 * @return string
	 */
	public function getViewsNamespace() {
		return $this->_namespace;
	}

	/**
	 * Sets the namespace from where the views are loaded.
	 * 
	 * @param string $namespace The namespace from where to load views
	 * @return Aventura\Edd\AddToCartPopup\Core\ViewsController This instance
	 */
	public function setViewsNamespace($namespace) {
		$this->_namespace = $namespace;
		return $this;
	}

	/**
	 * Gets the fully qualified name of a view.
	 * 
	 * @param  string $viewName The view name: class name without namespace.
	 * @return string           Fully qualified name of the view class.
	 */
	public function getFullyQualifiedViewName($viewName) {
		return sprintf('%1$s\\%2$s', $this->getViewsNamespace(), $viewName);
	}

	/**
	 * Renders a view.
	 * 
	 * @param  string $viewName The view name: class name without namespace.
	 * @param  array  $viewbag  Array of data to provide to the view. This will be cast into an object.
	 * @return string           The rendered content of the view.
	 */
	public function renderView($viewName, $viewbag = array()) {
		$fullName = $this->getFullyQualifiedViewName($viewName);
		// Cast viewbag
		$viewbag = (object) $viewbag;
		// Begin output buffering
		ob_start();
		// Load view file
		include trailingslashit( realpath(dirname(__FILE__)) ) . 'Views' . DS . $viewName . '.php';
		// Stop output buffering and return buffered content
		return ob_get_clean();
	}

	/**
	 * Execution method, run on 'edd_acp_on_run' action.
	 */
	public function run() {}

}
