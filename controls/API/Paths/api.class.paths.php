<?php
class path {
	
	private static $cfg;
	
	public function __construct() {
		self::$cfg = config::$path;
		return true;
	}
	
	
	static public function absolute() {
		return config::make_absolute_path();
	}
	
	
	static public function ajax() {
		return self::$cfg->ajax;
	}
	
	
	static public function app() {
		return self::$cfg->app;
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
		self::$req = request::component();
		$path = self::$cfg->app . '/Components/' . self::$req->component;
		return $path;
	}
	
}

$path = new path();
?>