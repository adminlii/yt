<?php
class Ec_Controller_Action_Helpers_PostContentParser extends Zend_Controller_Action_Helper_Abstract {
	public function __construct() {
	}
	/**
	 * Parse url
	 *
	 * @param String $url        	
	 * @return Array part of url
	 */
	public function contentParser($content) {
		$content = preg_replace_callback ( "/\s*<pre(?:lang=[\"']([\w-]*)[\"']|file=[\"']([\w-]*\.?[\w-]*)[\"']|colla=[\"']([\+\-])[\"']|line=[\"'](\d*|n)[\"']|\s)+>(.*)<\/pre>\s*/siU", __CLASS__ . "::paserCodebox", $content );
		
		$content = explode ( "\n", $content );
		$tem = "";
		foreach ( $content as $con ) {
			$con = trim ( $con );
			$tem .= empty ( $con ) ? $con : "<p>{$con}</p>";
		}
		$content = $tem;
		return $content;
	}
	private static function paserCodebox(&$m) {
		$html = "";
		if ($m [0]) {
			$html = '<div class="wp_codebox"><table><tbody>';
			
			$matches = explode ( "\n", $m [0] );
			foreach ( $matches as $k => $v ) {
				$html .= "<tr>";
				$html .= "<td>";
				
				$html .= $k + 1;
				$html .= "</td>";
				
				$html .= "<td class='border_left'>";
				$html .= htmlspecialchars ( $v );
				$html .= "</td>";
				
				$html .= "</tr>";
			}
			
			$html .= "</tbody></table></div>";
		}
		// print_r($html);
		return $html;
	}
}  