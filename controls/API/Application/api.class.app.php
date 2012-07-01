<?php

//defined( '_VALID_REFERENCE' ) or die( 'Direct Access to this location is not allowed.' );

abstract class API_app {
	
	public static $ua;
	
	public function __construct() { return true; }
	
	# ####
	#
	# Performs 64bit encoding of images for CSS embedding
	#
		function data_url($file, $mime='') {  
			
			# set a default mime type of PNG if nothing is specific */
			$mime = empty($mime) ? 'image/png' : $mime;
			
			# Create the stream context
			$context = stream_context_create(array(
				'http' => array(
					'timeout' => 3 // Timeout in seconds
				)
			));
			
			# Fetch the URL's contents
			$contents = file_get_contents($file, 0, $context);
			
			# Check for empties if no file was found
			if (!empty($contents)) {
				$contents = file_get_contents($file);
				$base64   = base64_encode($contents); 
				return ('data:' . $mime . ';base64,' . $base64);
			
			# exit the app so we don't get into large loops of files not found
			} else {
				echo '<br /><h4>an invalid file path was passed to data_url()</h4><br />';
				exit();
			}
		}
	#
	# END :: data_url
	# ####
	
	
	
	
	
	
	
	
	# ####
	#
	# Used for writing variables and arrays to screen
	# for debugging. If array, it will place a line break
	# after each value for easy on-screen viewing
	#	
	#	$app->debug($var);
	#
	# if var is empty, it will display "Hello World"
	# $show defaults to true. setting this false will wrap the
	# output in a div with a display:none so it won't show
	# except if you view HTML source
	#
		public function debug($var=false,$show=true) {

			$this_debug_code = '';
			
			if ($var == 'session') {
				$vars = array(
					'Session'=>$_SESSION,
					'Cookies'=>$_COOKIE
				);
				foreach ($vars as $key=>$val ) {
					$this_debug_code .= '<div class="debug-section">';
					$this_debug_code .= '<h2>'. $key .'</h2>';
					$this_debug_code .= self::show_debug($val);
					$this_debug_code .= '</div>';
				}
			} else {
				$this_debug_code .= self::show_debug($var,$show);
			}
			
			ob_start();
				$debug_file = dirname(__FILE__) . '/debug_layout.php';
				include($debug_file);
				$debug_file_contents = ob_get_contents();
			ob_end_clean();
			
			$content = str_replace('%%_DEBUG_OUTPUT_%%', $this_debug_code, $debug_file_contents);
			
			echo $content;
		}
		#
		# END
		#
		#		
		# show_debug() method
		# Child method of app::debug()
		#
		private function show_debug($var=false,$show=true) {			
			
			$display=(!$show)?' style="display: none;"':'';
			$debug_output = '';
			
			# start the output processes of each variable
			#
			# is an array/object
			if ( is_array($var) || is_object($var) ){
				# loop through each key
				foreach ($var as $key=>$val) {
				
					$debug_output .= '<p><b>'. $key .'</b>';
					
					#
					# the value is an array/object
					if (is_array($val) || is_object($val)) {
						$debug_output .= '<ul>';
						foreach ($val as $key1=>$val1) {
							$debug_output .= '<li><b>'.$key . '</b>[<i>'.$key1.'</i>]' . ' = ';
								if (is_array($val1) || is_object($val1)) { 
									$debug_output .= self::debug_array($val1);
								} else {
									$debug_output .= $val1.'</li>';
								}
						}
						$debug_output .= '</ul></p>';
					#
					# value is not an array/object
					} else {
						$debug_output .= ' = ' . $val.'</p>';
					}
				}
				
			#
			# is not an array/object
			} else {
				$debug_output .= '<p>' . $var . '</p>';
			}
			
			if (empty($var)) {
				$debug_output .= '<p>Hello World</p>';
			}
			
			return $debug_output;
		}
		#
		# END :: child show_debug()
		#
		# ####
		#
		# debug_array() method 
		# Child method of show_debug()
		#
		private function debug_array($var) {
			$debug_output = '';
			
			$debug_output .= '<ul>';
			foreach ($var as $key1=>$val1) {
				
				$debug_output .= '<li>[' . $key1 . '] = ';
					
				if (is_array($val1) || is_object($val1)) : 
					$debug_output .= self::debug_array($val1);
				else :
					$debug_output .= $val1.'</li>';
				endif;
					
			}
			$debug_output .= '</ul>';
			
			return $debug_output;
		}
		#
		# END ::: debug_array()
		# ####
	#
	# END ::: debug()
	#
	# ####
	
	
	
	
	
	
	
	
	# ####
	#
	# Parses the URL to get domain only
		public function parse_url_domain ($url) {
			$parsed   = parse_url($url);
			$hostname = $parsed['host'];
			$hostname = str_replace ('www.','', $hostname);
			return $hostname;
		}
	#
	# END
	# ####
	
	
	
	
	
	
	
	
	# ####
	#
	# validates the file type of a given file, is valid
	# returns true/false
	#
		public function file_type_is_valid($file_name) {
		
			$file_types = array('pdf','xls','doc','rtf','docx','xlsx','txt','ppt','pptx','pub','wmv','mov','mp4','mp3','png','jpg','jpeg','gif');
			foreach ($fileTypes as $fileType) {
				if ( stristr($file_name, $file_types) ) {
					$a = true;
					break;
				} else {
					$a = false;
				}
			}
			
			return $a;
		}
	#
	# END
	#
	# ####
	
	
	
	
	
	
	
	
	# ####
	#
	# redirects the user to the error page
	#
		public function error_page() {
			global $config;
			header("Location: ".config::$url->error);
			exit();
		}
	#
	# END
	# ### #
	
	
	
	
	
	
	
	
	# ####
	#
	# Breaks down the $_SERVER variable for debugging purposes
	#
		public function get_server_variable_details() {
			
			$referer = (isset($_SERVER['HTTP_REFERER']))?$_SERVER['HTTP_REFERER']:'';
			
			if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
				$referer .= '?'.$_SERVER['QUERY_STRING'];
			}
			
			$server_r = '';
			$server_out = $_SERVER;
			foreach ($server_out as $key=>$val) {
				$server_r .= $key.' ::: '.$val."<br />";
			}
			
			$additional_information = '
				Server: '.$_SERVER['SERVER_NAME']."<br />".'
				URL: '.self::referer(true)."<br />".'
				Referer: '.$referer."<br />".'
				Server Output: '."<br />".$server_r."<br />";
			
			return $additional_information;
		}
	#
	# END
	# ####
	
	
	
	
	
	
	
	
	# ####
	#
	# Gets referring URL, full path
	#
		public function referer($set=false) {
			$url = '';
			
			if ( isset($_SERVER['HTTP_HOST']) ) {
				$url = $_SERVER['HTTP_HOST'];
				
			} else if ( isset($_SERVER['SERVER_NAME']) ) {
				$url = $_SERVER['SERVER_NAME'];
			}
		
			$s = empty($_SERVER["HTTPS"]) ? '' : 's';
			$url = "http{$s}://" . $url.$_SERVER['REQUEST_URI'];
	
			// Are setting this to a variable, or echoing it?
			if (!$set) {
				echo $url;
			} else {
				return $url;
			}
		}
	#
	# END
	# ####






	# ####
	# 
	# Gets the real IP address of the user
	# Not just the shared IP
	#
		public function get_real_ip_address() {
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {  //check ip from share internet
			$ip=$_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  //to check ip is pass from proxy
			$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
			$ip=$_SERVER['REMOTE_ADDR'];
			}
			return $ip;
		}
	#
	# END ::: get_real_ip_address()
	# ####
	
	
	
	
	
	
	
	
	# ####
	#
	# Clean up funky Word characters
	#
		private function win1252toIso( $string ) {
			// These chars seem to be not contained
			// in php's CP1252 translation table
			static $extensions = array(
				142 => "&Zcaron;",
				158 => "&zcaron;"
			);
			// Go through string and decide char by char:
			// "leave as is or build entity?"
			$newStr = "";
			for( $i=0; $i < strlen( $string ); $i++ ) {
				$ord = ord( $string[$i] );
				if ( in_array( $ord, array_keys( $extensions ) ) ) {
					// build entity using extra translation table
					$newStr .= $extensions[$ord];
				
				} else {
					// build entity using php's translation table
					// or leave as is
					$newStr .= ( $ord > 127 && $ord < 160 ) ?
					htmlentities( $string[$i], ENT_NOQUOTES, "CP1252" )
					: $string[$i];
				}
			}
			return $newStr;
		}
	#
	# END
	# ####
	
	
	
	
	
	
	
	
	# ####
	#
	# In addition to cleaning specific Word characters, this
	# replaces single and double quotes with HTML equiv
	#
		public function clean($data='') {
			$needle = array("'", '"');
			$replace = array("&rsquo;","&quot;");
			
			$data = stripslashes($data);
			$data = $this->win1252toIso($data);
			$data = str_replace("  ", " ", $data);
			$data = addslashes($data);
			
			return $data;
		}
	# ### #
	
	
	
	
	
	
	
	
	# ####
	# determines if the browser agent is a mobile device
	# returns true/false
	#
		public function isMobile( $additional_agents='' ) {
			global $config;
					
			include_once( dirname(__FILE__) . '/browser-agent/xbd.php');
			$mobileAgent = array('ipad','iphone','ipod','android','presto');
			
			if (!empty($additional_agents)) :
				$results = array_merge($mobileAgent, $additional_agents);
				$mobileAgent = $results;
			endif;
			
			$ua = _browser();
			
			self::$ua = $ua;
			
			if (!empty($ua)) :
				$platform   = $ua['platform'];
				$browser    = $ua['browser'];
				$version    = $ua['version'];
				$type       = $ua['type'];
				$user_agent = $ua['useragent'];
			endif;
			
			$android_phone = (bool) ($browser == 'safari' && $platform == 'linux' && $type != 'desktop' );
			$is_mobile     = (bool) (strchr($user_agent, 'mobile') || strchr($user_agent, 'mobi') || strchr($user_agent, 'android') );
			
			if (in_array($browser,$mobileAgent) || @$android_phone || @$is_mobile) :
				return true;
			else :
				return false;
			endif;
			
		}
		
		# ##
		# Alias for isMobile()
		public function is_mobile( $x='' ) { $x = $x; return self::isMobile( $x ); }
		# ####
	# ### #
}
?>