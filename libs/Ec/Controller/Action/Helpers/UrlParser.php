<?php
class Ec_Controller_Action_Helpers_UrlParser extends Zend_Controller_Action_Helper_Abstract {
	public function __construct() {
	}
	/**
	 * Parse url
	 *
	 * @param String $url        	
	 * @return Array part of url
	 */
	public function parse($url) {
		return parse_url ( $url );
	}
}  