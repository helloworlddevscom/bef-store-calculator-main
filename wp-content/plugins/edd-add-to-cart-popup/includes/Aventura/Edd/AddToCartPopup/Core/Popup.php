<?php

namespace Aventura\Edd\AddToCartPopup\Core;

/**
 * Represents the popup to be show on the front-end.
 */
class Popup extends Plugin\Module {

	/**
	 * Display options for the popup.
	 * 
	 * @var array
	 */
	protected $_display;

    /**
     * Flag to determine if Popup is currently rendering.
     * 
     * @var boolean
     */
    protected $_isRendering = false;

	/**
	 * Constructor.
	 */
	protected function _construct() {
		$this->setDisplayOptions(
				$this->getPlugin()->getSettings()->getValue('display', array())
		);
	}

	/**
	 * Gets the display options.
	 * 
	 * @return array
	 */
	public function getDisplayOptions() {
		return $this->_display;
	}

	/**
	 * Sets the display options.
	 * 
	 * @param array $display
	 */
	public function setDisplayOptions($display) {
		$this->_display = $display;
	}

    /**
     * Gets whether or not the popup is currently rendering.
     *
     * @return boolean True if the popup is rendering, false if not.
     */
    public function isRendering()
    {
        return $this->_isRendering;
    }

    /**
     * Sets whether the popup is rendering or not.
     *
     * @param boolean $isRendering Whether the popup is rendering or not. True for rendering, false for not.
     * @return \Aventura\Edd\AddToCartPopup\Core\Popup This instance.
     */
    public function setRendering($isRendering)
    {
        $this->_isRendering = (bool) $isRendering;
        return $this;
    }

	/**
	 * Renders the popup HTML.
	 * 
	 * @return string The rendered popup
	 */
	public function render($downloadId, Settings $settings = null) {
        $args = compact('downloadId', 'settings');
		$render = $this->getPlugin()->getViewsController()->renderView('Popup', $args);
        return $render;
	}

    /**
     * Adds a popup render after the purchase form.
     *
     * @param string $content The purchase form HTML being filtered.
     * @param array $args Array of arguments.
     * @return string The filtered HTML of the purchase form and the popup added after it.
     */
    public function renderAfterPurchaseForm($content, $args)
    {
        $downloadId = $args['download_id'];
        $render = $this->render($downloadId);
        return $content . $render;
    }

    /**
     * Generates a preview.
     */
    public function generatePreview(array $settings = array()) {
        // Create a dummy instance
        $dummyInstance = new Settings($this->getPlugin());
        $dummyInstance->setDbOptionName('acp_preview');
        // Register the options to the dummy instance
        eddAcpRegisterOptions($dummyInstance);
        $dummyInstance->setValuesCache($settings);
        // Generate the render using the dummy settings instance
        return $this->render(0, $dummyInstance, false);
    }

    /**
     * Generates a preview for an AJAX event.
     *
     * Expects the POST 'settings' index to contain an array of the settings values.
     */
    public function ajaxPreview() {
        $settings = filter_input(INPUT_POST, 'settings', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        echo $this->generatePreview($settings);
        die;
    }

	public function enqueueAssets() {
		// Register assets
		$this->getPlugin()->getAssetsController()
				->registerScript('edd_acp_bpopup', EDD_ACP_JS_URL . 'jquery.bpopup.min.js', array('jquery'))
				->registerScript('edd_acp_frontend_js', EDD_ACP_JS_URL . 'edd-acp.js', array('edd_acp_bpopup'))
				->registerStyle('edd_acp_frontend_css', EDD_ACP_CSS_URL . 'edd-acp-popup.css');
		// Enqueue front-end main script
		$this->getPlugin()->getAssetsController()
				->enqueueStyle('edd_acp_frontend_css')
				->enqueueScript('edd_acp_frontend_js');
	}

    /**
     * Adds the "Preview Popup" entry to the admin menu bar.
     *
     * @global WP_Admin_Bar $wp_admin_bar
     */
    public function previewAdminBarMenu() {
        if (!current_user_can('manage_shop_settings')) {
            return;
        }
        $screen = get_current_screen();
        if ($screen->id !== 'download_page_edd-settings') {
            return;
        }
        if (filter_input(INPUT_GET, 'tab', FILTER_SANITIZE_STRING) !== 'extensions') {
            return;
        }
        global $wp_admin_bar;
        $previewLink = array(
            'id'    => 'edd-acp-preview-admin-bar',
            'title' => __('Preview Popup', 'edd_acp'),
            'href'  => '#',
            'meta'  => array(
                'class' => 'edd-acp-preview',
            )
        );
        $wp_admin_bar->add_menu($previewLink);
    }

	/**
	 * Execution method, run on 'edd_acp_on_run' action.
	 */
	public function run() {
		// If the enabled toggle option is turned on
		if ($this->getPlugin()->getSettings()->getValue('enabled') == '1' || is_admin()) {
			$this->getPlugin()->getHookLoader()
					// Hook in the popup render
					->queueAction( 'edd_purchase_download_form', $this, 'renderAfterPurchaseForm', 10, 2 )
					->queueAction( AssetsController::HOOK_FRONTEND, $this, 'enqueueAssets' )
                    ->queueAction( AssetsController::HOOK_ADMIN, $this, 'enqueueAssets' );
		}
		// Check for EDD's AJAX option
		if ( is_admin() ) {
			$this->getPlugin()->getHookLoader()
                ->queueAction('wp_ajax_edd_acp_preview', $this, 'ajaxPreview')
                ->queueAction('admin_bar_menu', $this, 'previewAdminBarMenu', 999999);
		}
	}

}
