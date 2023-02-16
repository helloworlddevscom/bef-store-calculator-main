<?php

namespace Aventura\Edd\AddToCartPopup\Core;

/**
 * Settings controller class, which acts as a wrapper for the database option.
 */
class Settings extends Plugin\Module {

	/**
	 * The name of the option that EDD uses.
	 */
	const EDD_SETTINGS_OPTION_NAME = 'edd_settings';

	/**
	 * Default name of the db option
	 */
	const DEFAULT_DB_OPTION_NAME = 'acp';

    /**
     * The name of the transient that indicates the need to show the "options have been reset" notice.
     */
    const RESET_OPTIONS_NOTICE_TRANSIENT = 'edd_acp_reset_options_transient';

	/**
	 * The name of the db option.
	 *
	 * @var string
	 */
	protected $_dbOptionName;

	/**
	 * The value stored in the db option.
	 * 
	 * @var mixed
	 */
	protected $_value = null;

	/**
	 * The settings options.
	 * 
	 * @var array
	 */
	protected $_options = array();

	/**
	 * Settings tab label.
	 * 
	 * @var string
	 */
	protected $_sectionLabel = '';
	protected $_sectionSlug = 'acp';

	/**
	 * Constructor.
	 */
	protected function _construct() {
		$this->setDbOptionName(self::DEFAULT_DB_OPTION_NAME)
				->setSectionLabel( __('Add to Cart Popup', 'edd_acp') );
	}

	/**
	 * Execution method, run on 'edd_acp_on_run' action.
	 */
	public function run() {
		$this->register();
	}

	/**
	 * Gets the name of the DB option.
	 * 
	 * @return string
	 */
	public function getDbOptionName() {
		return $this->_dbOptionName;
	}
	
	/**
	 * Sets the name of the DB option.
	 *
	 * @param string $dbOptionName The name of the DB option
	 * @return Aventura\Edd\AddToCartPopup\Core\Settings This instance.
	 */
	public function setDbOptionName($dbOptionName) {
		$this->_dbOptionName = $dbOptionName;
		return $this;
	}

	/**
	 * Gets the option name for a subvalue.
	 * 
	 * @param  string $subName The name of the subvalue.
	 * @return string
	 */
	public function getSubValueOptionName($subName) {
		return sprintf('%1$s[%2$s][%3$s]', self::EDD_SETTINGS_OPTION_NAME, $this->getDbOptionName(), $subName);
	}

	/**
	 * Gets the section slug.
	 * 
	 * @return string
	 */
	public function getSectionSlug() {
		return $this->_sectionSlug;
	}

	/**
	 * Sets the section slug.
	 * 
	 * @param string $sectionSlug The new section slug.
	 * @return Aventura\Edd\AddToCartPopup\Core\Settings This instance
	 */
	public function setSectionSlug($sectionSlug) {
		$this->_sectionSlug = $sectionSlug;
		return $this;
	}

	/**
	 * Gets the settings section label text.
	 * 
	 * @return string
	 */
	public function getSectionLabel() {
		return $this->_sectionLabel;
	}

	/**
	 * Sets the settings section label.
	 * 
	 * @param string $sectionLabel The new label text
	 * @return Aventura\Edd\AddToCartPopup\Core\Settings This instance
	 */
	public function setSectionLabel($sectionLabel) {
		$this->_sectionLabel = $sectionLabel;
		return $this;
	}

	/**
	 * Gets the value stored in the DB option.
	 * 
	 * @param  string $sub     (Optional) The index of the subvalue to retrieve. Default: null
	 * @param  mixed  $default (Optional) The value for the function to return if $sub is not null and is not found. Default: null
	 * @return mixed           If not params are specificed, the value stored in the option is returned, or array() if the option does not exist.
	 *                         Otherwise, the subvalue with index $sub is returned, and if not found $default is returned.
	 */
	public function getValue($sub = null, $default = null) {
		if ($this->_value === null) {
			$eddSettings = get_option( self::EDD_SETTINGS_OPTION_NAME, array() );
			$this->_value = isset($eddSettings[ $this->getDbOptionName() ])
					? $eddSettings[ $this->getDbOptionName() ]
					: array();
		}
		return ($sub === null)
				? $this->_value
				: $this->getSubValue($sub, $default);
	}

	/**
	 * Gets a subvalue from the db value.
	 * 
	 * @param  string $sub     The index of the subvalue to retrieve
	 * @param  mixed  $default (Optional) The value for the function to return if the subvalue is not found. Default: null
	 * @return mixed           The subvalue, or the value of $default if not found.
	 */
	public function getSubValue($sub, $default = null) {
		return isset($this->_value[$sub])
				? $this->_value[$sub]
				: ( isset($this->_options[$sub])
							? $this->_options[$sub]->default
							: $default );
	}

    /**
     * Sets the interal values cache array.
     *
     * @param array $values The values array. Default: array
     * @return Settings This instance.
     */
    public function setValuesCache(array $values = array())
    {
        $this->_value = $values;
        return $this;
    }

	/**
	 * Adds a settings option.
	 * 
	 * @param string   $id       The option ID.
	 * @param string   $title    The option title.
	 * @param string   $desc     The option description.
	 * @param mixed    $default  The default value of the option.
	 * @param callable $callback The callback that renders the option.
	 * @return Aventura\Edd\AddToCartPopup\Core\Settings This instance
	 */
	public function addOption($id, $title, $desc = '', $default = null, $callback = null) {
                $type = (is_null($default) && is_null($callback))
                    ? 'header'
                    : 'hook';
                if ($type === 'header') {
                    $title = sprintf('<strong>%s</strong>', $title);
                }
		$this->_options[$id] = (object) compact('id', 'title', 'desc', 'default', 'callback', 'type');
		return $this;
	}

	/**
	 * Checks if a settings option has been registered.
	 * 
	 * @param  string  $id The ID of the option to search for.
	 * @return boolean     True if the option is found, false otherwise.
	 */
	public function hasOption($id) {
		return isset($this->_options[$id]);
	}

	/**
	 * Removes a settings option.
	 * 
	 * @param  string $id The ID of the option to remove.
	 */
	public function removeOption($id) {
		unset($this->_options[$id]);
	}

	/**
	 * Gets a settings options.
	 * 
	 * @param  string $id The ID of the settings option to return.
	 * @return mixed      The settings option as an array or null if a option did not match the given $id.
	 */
	public function getOption($id) {
		return $this->hasOption($id)
				? $this->_options[$id]
				: null;
	}

    /**
     * Gets the registered options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

	/**
	 * Registers the subsection in the EDD settings page Extensions tab.
	 * 
	 * @param  array $sections The subsections in the Extensions tab
	 * @return array           The sections with the added messages section
	 */
	public function filterEddSettingsSubsection($sections) {
		$sections[ $this->getSectionSlug() ] = $this->getSectionLabel();
		return $sections;
	}

	/**
	 * Registers the settings.
	 * 
	 * @param  array $settings The original EDD settings array.
	 * @return array
	 */
	public function filterEddSettings($settings) {
		// Create new entry for our settings section
		$section = $this->getSectionSlug();
		$acpSettings = array();
		// Iterate options
		foreach ($this->_options as $_optionId => $_option) {
			// Add the option to the EDD settings
			$acpSettings[$_optionId] = array(
				'id'		=>	$_optionId,
				'name'		=>	$_option->title,
				'desc'		=>	$_option->desc,
				'type'		=>	$_option->type,
			);
			// Add the action for the callback that renders this option
			$actionHook = sprintf('edd_%s', $_optionId);
			$this->getPlugin()->getHookLoader()->addAction($actionHook, $this, 'renderOption');
		}
		// If EDD is at version 2.5 or later...
		if ( version_compare( EDD_VERSION, 2.5, '>=' ) ) {
			// Use the previously noted array key as an array key again and next your settings
			$acpSettings = array( $this->getSectionSlug() => $acpSettings );
		}
		return array_merge($settings, $acpSettings);
	}

	/**
	 * Renders a settings option, by calling its registered callback.
	 * 
	 * @param array $args Option information provided by EDD's settings API.
	 */
	public function renderOption($args) {
		$option = $this->getOption($args['id']);
		if ($option !== null) {
			call_user_func_array($option->callback,
				array($this, $args['id'], $args)
			);
		} else {
			trigger_error(
				sprintf(
					__('Invalid callback given for settings option "%s"', 'edd_acp'),
					$args['id']
				)
			);
		}
	}

    /**
     * Sanitizes the settings after submission.
     *
     * @param array $input The input settings.
     * @return array The sanitized output.
     */
    public function sanitize($input) {
        $output = $input;
        if (isset($input[$this->getDbOptionName()])) {
            $options = $input[$this->getDbOptionName()];
            if (isset($options['reset']) && !empty($options['reset'])) {
                $options = array();
                set_transient(self::RESET_OPTIONS_NOTICE_TRANSIENT, '1');
            }
            $output[$this->getDbOptionName()] = $options;
        }
        return $output;
    }

    /**
     * Shows any notices related to the settings page.
     *
     * @since 1.1.0
     * @hook admin_notice
     */
    public function doNotices()
    {
        if (get_transient(self::RESET_OPTIONS_NOTICE_TRANSIENT) === '1') {
            delete_transient(self::RESET_OPTIONS_NOTICE_TRANSIENT);
            echo $this->getResetOptionsNotice();
        }
    }

    /**
     * Gets the notice that notifies the user that options have been reset.
     *
     * @since 1.1.0
     * @return string The HTML notice.
     */
    public function getResetOptionsNotice()
    {
        return $this->getPlugin()->getViewsController()->renderView('OptionsResetNotice', array());
    }

	/**
	 * Registers the settings with EDD.
	 * 
	 * @return Aventura\Edd\AddToCartPopup\Core\Settings This instance
	 */
	public function register() {
		$this->getPlugin()->getHookLoader()->queueFilter( 'edd_settings_sections_extensions', $this, 'filterEddSettingsSubsection' );
		$this->getPlugin()->getHookLoader()->queueFilter( 'edd_settings_extensions', $this, 'filterEddSettings' );
        $this->getPlugin()->getHookLoader()->queueFilter( 'edd_settings_extensions_sanitize', $this, 'sanitize');
        $this->getPlugin()->getHookLoader()->queueAction( 'admin_notices', $this, 'doNotices' );
		return $this;
	}

}
