<?php

namespace Aventura\Edd\AddToCartPopup\Core;

/**
 * This class is responsible for setting/loading the text domain for text localization.
 */
class TextDomain extends Plugin\Module {
	
	/**
	 * @string
	 */
	protected $_name;

	/**
	 * Languages directory.
	 * 
	 * @var string
	 */
	protected $_langDir;
	
	/**
	 * Constructor.
	 */
	public function __construct($plugin, $domain, $langDir) {
		parent::__construct($plugin);
		$this->setName($domain)->setDirectory($langDir);
	}

	/**
	 * Gets the text domain.
	 * 
	 * @return string
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * Sets the plugin text domain.
	 *
	 * @param string $domain The new text domain
	 * @return Aventura\Edd\AddToCartPopup\Core\TextDomain This instance
	 */
	public function setName( $domain ) {
		$this->_name = $domain;
		return $this;
	}

	/**
	 * Gets the languages directory.
	 * 
	 * @return string
	 */
	public function getDirectory() {
		return $this->_langDir;
	}

	/**
	 * Sets the language directory.
	 * 
	 * @param string $langDir The new languages directory.
	 * @return Aventura\Edd\AddToCartPopup\Core\TextDomain This instance
	 */
	public function setDirectory($langDir) {
		$this->_langDir = $langDir;
		return $this;
	}

	/**
	 * Loads the plugin text domain on "execution". This is triggered on action `edd_acp_on_run`.
	 */
	public function run() {
		load_plugin_textdomain(
			$this->getName(),
			false,
			$this->getDirectory()
		);
	}

	/**
	 * Returns the string representation of this text domain, which is its domain name.
	 * 
	 * @return string The domain name of this instance.
	 */
	public function __toString() {
		return $this->getName();
	}
	
}
