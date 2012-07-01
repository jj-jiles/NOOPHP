<?php

class request {
	
	public static $url = array();
	public static $uri = array();
	public static $urd = array();
	public static $app_structure;
	public static $request;
	
	private static $query_string = array();
	
	public function __construct() {
		
		self::$app_structure = array(
			'component',
			'content',
			'subcontent',
			'extendedcontent'
		);
		
		self::$request = (object) array();
		
		self::build_request_structure();		
		return true;
	}
	
	static private function build_request_structure() {
		
		if ( count(self::$url) == 0 ) :
		
			# create each 
			foreach (self::$app_structure as $str) :
				self::$url[$str]=false;
			endforeach;
			
			parse_str($_SERVER['QUERY_STRING']);
			
			if (isset($urd)) :
			
				self::$urd = $urd;
				$vars = self::$urd;
				$vars = explode('/',$vars);
				
				$count=0;
				for ($i=0; $i < count($vars); $i++):
					if ( isset(self::$app_structure[$i]) ) :
						$str = self::$app_structure[$i];
						self::$request->$str = $vars[$i];
						self::$url[$str]     = $vars[$i];
					endif;
					
					self::$uri[$vars[$i]] = $vars[$i];
					$mixed = explode(',',$vars[$i]);
					
					if ( isset($mixed[1]) ) :
						self::$uri[$mixed[0]] = $mixed[1];
					endif;
											
					$count++;
				endfor;
			endif;
			
		endif;
			
			
			$qs = explode('&',$_SERVER['QUERY_STRING']);
			foreach ($qs as $q) :
				$q = explode('=',$q);
				$var = $q[0];
				$val = isset($q[1])?$q[1]:'';
				
				self::$query_string[$var]=$val;
			endforeach;
		
	}
	
	
	static public function component() {
		return self::$url['component'];
	}
	
	
	static public function content() {
		return self::$url['content'];
	}
	
	
	static public function subcontent() {
		return self::$url['subcontent'];
	}
	
	
	static public function extendedcontent() {
		return self::$url['extendedcontent'];
	}
	
	
}

$request = new request();
?>