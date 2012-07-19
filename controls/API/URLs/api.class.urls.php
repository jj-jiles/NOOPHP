<?php
class url {
	
	private static $cfg = array();
	
	function __construct() {
		self::$cfg = config::$url;
		return true;
	}
	
	
	static public function absolute() {
		$absolute_url = config::make_full_url();
		return self::$cfg->absolute;
	}
	
	
	static public function ajax() {
		return self::$cfg->ajax;
	}
	
	
	static public function assets() {
		return self::$cfg->assets;
	}
	
	
	static public function component() {
		$url = self::$cfg->root . '/' . request::component();
		return $url;
	}
	
	
	static public function controls() {
		return self::$cfg->controls;
	}
	
	
	static public function error() {
		return self::$cfg->error;
	}
	
	
	static public function login() {
		return self::$cfg->login;
	}
	
	
	static public function modules() {
		return self::$cfg->modules;
	}
	
	
	static public function root() {
		return self::$cfg->root;
	}
	
	
	static public function views() {
		return self::$cfg->views;
	}
}

$url = new url;
?>