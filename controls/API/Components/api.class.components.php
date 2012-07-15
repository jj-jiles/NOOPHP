<?php
class component {
	
	function __construct() {
		return true;
	}
	
	static public function _include() {
		$content   = request::content();
		$component = request::component();
		
		$content = ( empty($content) ) ? 'default' : $content;
		include_once( path::views() . '/' . $component . '/' . $content . '.php');
	}
	
	static public function _path() {
		return path::views() . '/'.request::component();
	}
	
}

$component = new component();
?>