<?php

class app_email extends email {
	
	private static $template_dir;
	
	public function __construct() {		
		self::$template_dir = path::root() . '/templates/email';		
		return true;
	}
	
}
?>