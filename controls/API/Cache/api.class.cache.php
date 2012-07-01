<?php

class cache {
	
	private static $fp;
	
	public function __construct() {
		return true;
	}
	
	
	static public function start() {
		
		if ( @config::$app->cache ) {
			if ( isset($_SERVER['SCRIPT_URI']) ) {
				$scipt_name = basename($_SERVER['SCRIPT_URI']);
			} else {
				$script_name = basename($_SERVER['SCRIPT_NAME']);
			}
			$cachefile = path::root() . '/templates/cache/'.$script_name;  
			$cachetime = 120 * 60; // 2 hours  
	
			// Serve from the cache if it is younger than $cachetime  
			if (file_exists($cachefile) && (time() - $cachetime < filemtime($cachefile))) {  
				include($cachefile);  
				echo "<!-- Cached ".date('jS F Y H:i', filemtime($cachefile))." -->";  
				exit;  
			}  
			ob_start(); // start the output buffer  
			// Your normal PHP script and HTML content here  
			// BOTTOM of your script  
			self::$fp = fopen($cachefile, 'w'); // open the cache file for writing  
			fwrite(self::$fp, ob_get_contents()); // save the contents of output buffer to the file  
		}
	}
	
	
	static public function close() {
		if ( @config::$app->cache ) {
			fclose(self::$fp); // close the file  
			ob_end_flush(); // Send the output to the browser
			return true;
		}
		return false;
	}
}

?>