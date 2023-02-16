<?php

namespace Aventura\Edd\AddToCartPopup\Core;

/**
 * Plugin object class.
 */
class Plugin {

	const EDD_SL_ITEM_NAME = 'Add to Cart Popup';
	const PARENT_PLUGIN_CLASS = 'Easy_Digital_Downloads';
	const PARENT_MIN_VERSION = 2.5;
	const TEXT_DOMAIN = 'edd_acp';

	/**
	 * @var string
	 */
	protected $_mainFile;

	/**
	 * @var array
	 */
	protected $_info;

	/**
	 * @var Aventura\Edd\AddToCartPopup\HookLoader
	 */
	protected $_hookLoader;

	/**
	 * @var Aventura\Edd\AddToCartPopup\Core\Settings
	 */
	protected $_settings;

	/**
	 * @var Aventura\Edd\AddToCartPopup\Core\AssetsController
	 */
	protected $_assets;

	/**
	 * @var Aventura\Edd\AddToCartPopup\Core\ViewsController
	 */
	protected $_views;

	/**
	 * @var Aventura\Edd\AddToCartPopup\Core\Popup
	 */
	protected $_popup;

	/**
	 * @var Aventura\Edd\AddToCartPopup\Core\TextDomain
	 */
	protected $_textDomain;

	/**
	 * @var string
	 */
	protected $_deactivationReason = '';

	/**
	 * @var EDD_License
	 */
	protected $_license;

	/**
	 * Constructor
	 * @param string $mainFile The plugin main file name.
	 */
	public function __construct($mainFile) {
		$this->_setMainFile($mainFile)
				->_loadInfo()
				->resetHookLoader()
				->setSettings(new Settings($this))
				->setAssetsController(new AssetsController($this))
				->setViewsController(new ViewsController($this))
				->setPopup(new Popup($this))
				->setTextDomain(new TextDomain($this, self::TEXT_DOMAIN, EDD_ACP_LANG_DIR));
		// Set EDD License if the class exists
		if ( class_exists('EDD_License') ) {
			$this->_setLicense(new \EDD_License($this->getMainFile(), self::EDD_SL_ITEM_NAME, $this->getInfo('Version'), $this->getInfo('Author')));
		}
	}

	/**
	 * Gets the plugin main file name.
	 * 
	 * @return string The plugin main file name.
	 */
	public function getMainFile() {
		return $this->_mainFile;
	}

	/**
	 * Sets the plugin main file name.
	 * 
	 * @param string $mainFile The plugin main file name.
	 * @return Aventura\Edd\AddToCartPopup\Core\Plugin This instance.
	 */
	protected function _setMainFile($mainFile) {
		$this->_mainFile = $mainFile;
		return $this;
	}

	/**
	 * Gets the hook loader.
	 * 
	 * @return Aventura\Edd\AddToCartPopup\HookLoader
	 */
	public function getHookLoader() {
		return $this->_hookLoader;
	}

	/**
	 * Resets the hook loader.
	 *
	 * @return Aventura\Edd\AddToCartPopup\Core\Plugin
	 */
	public function resetHookLoader() {
		$this->_hookLoader = new HookLoader();
		return $this;
	}

	/**
	 * Gets the settings.
	 * 
	 * @return Aventura\Edd\AddToCartPopup\Core\Settings
	 */
	public function getSettings() {
		return $this->_settings;
	}

	/**
	 * Sets the settings instance.
	 * 
	 * @param Aventura\Edd\AddToCartPopup\Core\Settings $settings
	 * @return Aventura\Edd\AddToCartPopup\Core\Plugin
	 */
	public function setSettings($settings) {
		$this->_settings = $settings;
		return $this;
	}

	/**
	 * Gets the assets controller instance.
	 * 
	 * @return Aventura\Edd\AddToCartPopup\Core\AssetsController
	 */
	public function getAssetsController() {
		return $this->_assets;
	}

	/**
	 * Sets the assets controller instance.
	 * 
	 * @param Aventura\Edd\AddToCartPopup\Core\AssetsController $assetsController
	 * @return Aventura\Edd\AddToCartPopup\Core\Plugin
	 */
	public function setAssetsController(AssetsController $assetsController) {
		$this->_assets = $assetsController;
		return $this;
	}

	/**
	 * Gets the views controller instance.
	 * 
	 * @return Aventura\Edd\AddToCartPopup\Core\ViewsController
	 */
	public function getViewsController() {
		return $this->_views;
	}

	/**
	 * Sets the views controller instance.
	 * 
	 * @param Aventura\Edd\AddToCartPopup\Core\ViewsController $viewsController
	 * @return Aventura\Edd\AddToCartPopup\Core\Plugin
	 */
	public function setViewsController(ViewsController $viewsController) {
		$this->_views = $viewsController;
		return $this;
	}

	/**
	 * Gets the popup instance.
	 * 
	 * @return Aventura\Edd\AddToCartPopup\Core\Popup
	 */
	public function getPopup() {
		return $this->_popup;
	}

	/**
	 * Sets the popup instance.
	 * 
	 * @param Aventura\Edd\AddToCartPopup\Core\Popup $popup
	 * @return Aventura\Edd\AddToCartPopup\Core\Plugin
	 */
	public function setPopup(Popup $popup) {
		$this->_popup = $popup;
		return $this;
	}

	/**
	 * Gets the text domain.
	 * 
	 * @return Aventura\Edd\AddToCartPopup\Core\TextDomain
	 */
	public function getTextDomain() {
		return $this->_textDomain;
	}

	/**
	 * Sets the text domain.
	 * 
	 * @param Aventura\Edd\AddToCartPopup\Core\TextDomain $textDomain
	 * @return Aventura\Edd\AddToCartPopup\Core\Plugin This instance
	 */
	public function setTextDomain($textDomain) {
		$this->_textDomain = $textDomain;
		return $this;
	}

	/**
	 * Gets the license.
	 * 
	 * @return EDD_License
	 */
	public function getLicense() {
		return $this->_license;
	}

	/**
	 * Sets the license.
	 * 
	 * @param EDD_License $license
	 * @return Aventura\Edd\AddToCartPopup\Core\Plugin This instance
	 */
	protected function _setLicense($license) {
		$this->_license = $license;
		return $this;
	}

	/**
	 * Loads the plugin info from its header in the main file.
	 * 
	 * @return Aventura\Edd\AddToCartPopup\Core\Plugin This instance
	 */
	protected function _loadInfo() {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}
		$this->_info = get_plugin_data( $this->getMainFile() );
		return $this;
	}

	/**
	 * Gets the plugin data, a single data entry or a given default as a fallback if given.
	 * 
	 * @param  string $key     The key of the data entry to return. Default: null
	 * @param  mixed  $default The value to return if the data entry is not found. Default: null
	 * @return mixed           If the $key arg is null, the entire data set is returned. Otherwise, the
	 *                         data entry with that key will be returned, or $default if not found.
	 */
	public function getInfo($key = null, $default = null) {
		if ( $key === null ) {
			return $this->_info;
		}
		if ( isset( $this->_info[ $key ] ) ) {
			return $this->_info[ $key ];
		}
		return $default;
	}

	/**
	 * Callback function triggered when the plugin is activated.
	 */
	public function onActivate() {
		if ( version_compare( phpversion(), EDD_ACP_MIN_PHP_VERSION, '<' ) ) {
			$this->deactivate();
			wp_die(
				sprintf(
					__('The Easy Digital Downloads - Add to Cart Popup plugin failed to activate: PHP version must be %s or later.', 'edd_acp'),
					EDD_ACP_MIN_PHP_VERSION
				),
				__('Error'),
				array('back_link' => true)
			);
		}
		if ( version_compare( get_bloginfo('version'), EDD_ACP_MIN_WP_VERSION, '<' ) ) {
			$this->deactivate();
			wp_die(
				sprintf(
					__('The Easy Digital Downloads - Add to Cart Popup plugin failed to activate: WordPress version must be %s or later.', 'edd_acp'),
					EDD_ACP_MIN_WP_VERSION
				),
				__('Error'),
				array('back_link' => true)
			);
		}
	}

	/**
	 * Callback function trigged when the plugin is deactivated.
	 */
	public function onDeactivate() {

	}

	/**
	 * Checks for dependancies.
	 */
	public function checkDependancies() {
		if (!class_exists(self::PARENT_PLUGIN_CLASS) || version_compare(EDD_VERSION, self::PARENT_MIN_VERSION, '<')) {
			$this->deactivate(
				sprintf(
					__(
						'The <strong>Add to Cart Popup</strong> extension has been deactivated, because it requires the <strong>Easy Digital Downloads</strong> plugin (at version <strong>%s</strong> or later) to be installed and activated.', 'edd_acp'
					),
					self::PARENT_MIN_VERSION
				)
			);
		}
	}

	/**
	 * Deactivates this plugin.
	 *
	 * @param callbable|string $arg The notice callback function, that will be hooked on `admin_notices` after deactivation, or a string specifying the reason for deactivation.
	 */
	public function deactivate( $arg = NULL ) {
		// load plugins.php file from WordPress if not loaded
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		// Deactivate this plugin
		deactivate_plugins( EDD_ACP );
		// If no arg is given, stop
		if ( $arg === NULL ) {
			return;
		}
		// If arg is a callabe, hook it into admin_notices
		if (is_callable($arg)) {
			$this->getHookLoader()->addAction('admin_notices', null, $arg);
		}
		// Otherwise, we use the "deactivation reason" buffer and callback
		else {
			$this->_deactivationReason = $arg;
			$this->getHookLoader()->addAction('admin_notices', $this, 'showDeactivationNotice');
		}
	}

	/**
	 * Shows the deactivation notice, stating the reason.
	 */
	public function showDeactivationNotice() {
		printf('<div class="error"><p>%s</p></div>', strval($this->_deactivationReason));
	}

	/**
	 * Code to execute after all initialization and before any hook triggers
	 * 
	 * @return Aventura\Edd\AddToCartPopup\Core\Plugin This instance
	 */
	public function run() {
		do_action('edd_acp_on_run');

		// Check for EDD core plugin
		$this->getHookLoader()->queueAction('admin_init', $this, 'checkDependancies');

		// Hook all queued hooks
		$this->getHookLoader()->registerQueue();

		return $this;
	}

}
