<?php
class url {
	
	private static $cfg = array();
	
	function __construct() {
		self::$cfg = config::$url;
		return true;
	}
	
	
	static public function absolute() {
		return self::$cfg->absolute;
	}
	
	
	static public function login() {
		return self::$cfg->login;
	}
	
	
	static public function assets() {
		return self::$cfg->assets;
	}
	
	
	static public function controls() {
		return self::$cfg->controls;
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
	
	
	static public function component() {
		$url = self::$cfg->root . '/' . request::component();
		return $url;
	}
	
	
	static public function dashboard() {
		
		if ( isset($_SESSION['profile']) ) :
			$dash = '/account';
			$url = self::$cfg->root . $dash;
		else :
			$url = self::$cfg->root . '/sign-in';
		endif;
		
		return $url;
	}
	
	static public function ajax() {
		
		$url = self::$cfg->root . '/ajax';
		return $url;
	}
}

$url = new url;
?>