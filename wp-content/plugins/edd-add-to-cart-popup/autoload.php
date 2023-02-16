<?php

if (!function_exists('edd_acp_autoloader')) {
	/**
	 * Gets the EDD ACP Autoloader singleton instance.
	 * 
	 * @return Aventura\Edd\AddToCartPopup\Loader The loader singleton instance
	 */
	function edd_acp_autoloader() {
		// Loader instance
		static $loader = null;
		// Loader class name
		$className = 'Aventura\\Edd\\AddToCartPopup\\Loader';
		// If the class does not exist, attempt to load it's class file
		if (!class_exists($className)){
			$dir = EDD_ACP_INCLUDES_DIR;
			$classPath = str_replace('\\', DIRECTORY_SEPARATOR, $className);
			$classPath = "{$dir}/{$classPath}.php";
			require_once($classPath);
		}
		// If the loader is not yet instantiated, initialize it
		if ($loader === null) {
			/* @var $loader Aventura\Edd\AddToCartPopup\Loader */
			$loader = new $className();
			$loader->register();
		}
		// Return the loader instance
		return $loader;
	}
}
