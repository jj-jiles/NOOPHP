<?php
class component {
	
	function component() {
		return true;
	}
	
	function _include() {
		$content   = request::content();
		$component = request::component();
		
		$content = ( empty($content) ) ? 'default' : $content;
		include_once( path::views() . '/' . $component . '/' . $content . '.php');
	}
	
	function _path() {
		return path::views() . '/'.request::component();
	}
	
}

$component = new component();
?>