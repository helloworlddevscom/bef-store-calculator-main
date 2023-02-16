<?php

namespace Aventura\Edd\AddToCartPopup\Core\Plugin;

/**
 * Plugin module class.
 */
abstract class Module {
	
	/**
	 * @var Aventura\Edd\AddToCartPopup\Core\Plugin
	 */
	protected $_plugin;

	/**
	 * Constructor.
	 */
	public function __construct($plugin = null) {
		$this->setPlugin($plugin);
		$this->getPlugin()->getHookLoader()->addAction('edd_acp_on_run', $this, 'run');
		$this->_construct();
	}

	/**
	 * Internal constructor.
	 */
	protected function _construct() {}

	/**
	 * Gets the parent plugin instance to which this istance belongs to.
	 * 
	 * @return Aventura\Edd\AddToCartPopup\Core\Plugin
	 */
	public function getPlugin() {
		return $this->_plugin;
	}

	/**
	 * Sets the parent plugin instance to which this instance belongs to.
	 * 
	 * @param Aventura\Edd\AddToCartPopup\Core\Plugin $plugin The plugin instance
	 * @return Aventura\Edd\AddToCartPopup\Core\Settings This instance
	 */
	public function setPlugin($plugin) {
		$this->_plugin = $plugin;
		return $this;
	}

	/**
	 * Executed on 'edd_acp_on_run', which is triggered on Plugin::run()
	 */
	abstract public function run();

}
