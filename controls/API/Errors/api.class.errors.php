<?php
/*
	*	Script Name: Forms
	*	Author: JJ Jiles
	*	Website: http://jjis.me
	*	Version: 1
	*	Copyright (C) March 31 2010
	*
	*	Error Catching and Debugging
	*
*/

class errors {
	#
	# a couple of basic settings
	# 
	# Display Errors?: 
	#	True|False
	#	This is overridden should there be a $config->show_errors var
	#
	# Email To:
	#	email for the person who needs to receive the error
	#
	# Style Sheet:
	#	Set the link to the style sheet. 
	#	Exclude from the server path ( http://domain.com )
	#	
	# Suppressed Error Message
	#	This is the error message that will be displayed if error reporting is turned off
	#
	
	public static $error_chunk = '';
	public static $error_chunk_header = '';
	public static $error_chunk_footer = '';
	public static $error_js    = '';
	public static $header_echo = false;
	public static $footer_echo = false;
	public static $display;
			

	
	public function __construct() {		
		#
		# check to see if the $config object exists. if so, use it's setting
		self::$display = (defined(_ERRORS_DISPLAY_)) ? _ERRORS_DISPLAY_ : false;
        register_shutdown_function(array($this, 'catch_error'));
		
		return true;
		
	}
	
	
	function debug_header() {
		if ( !self::$header_echo ) {
			//echo self::echo_css();
			//self::js_script();
			self::debug_layout();
			//echo "<div id=\"error-container\"><h1>An error has been caught</h1><div id=\"error-wrapper\">";
			//echo "<ul id=\"error-list\">";
			self::$header_echo = true;
		}
	}
	
	
	function debug_layout() {
			ob_start();
				$debug_file = dirname(__FILE__) . '/debug_layout.php';
				include($debug_file);
				$debug_file_contents = ob_get_contents();
			ob_end_clean();
			
			$content = str_replace('%%_DEBUG_OUTPUT_%%', self::$error_chunk, $debug_file_contents);
			
			echo $content;
	}
			
	
	
	function debug() {
		if (!empty(self::$error_chunk)) {
			self::debug_header();	
			//echo self::$error_chunk;
			//self::debug_footer();
		}
	}
	
	
	function js_script() {
		echo "\n
			<script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js\" type=\"text/javascript\"></script>\n
			<script type='text/javascript'>
				window.onload = function() {
					if (typeof(jQuery) == 'undefined' || typeof($) == 'undefined') {
					var jQ = document.createElement('script');
					jQ.type = 'text/javascript';
					jQ.onload=runthis;
					jQ.src = 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js';
					document.body.appendChild(jQ);
				} else {
					runthis();
				}
				
				function runthis() { 
						body_height = $('body').height();
						body_offset = $(window).scrollTop();
						
						$('body').append('<div id=\"error-dim\"></div>');
						$('#error-dim').css({
							top    : '0',
							left   : '0',
							height : body_height,
							width  : '100%'
						});					
						
						//$('#alert-message-overlay-wrapper').css({
						//	top : body_offset,
						//	marginLeft : '-141px'
						//});
						
						/* ***
						* REQUIRED
						* set you classes accordingly below. */
						var accordion = {
							parent      : '.error-error-item',    // parent element containing the clickable header and collapsible
							header      : '.error-error-view',    // clickable header
							collapsible : '.error-error-details'  // collapsible element
						};
						
						/* ***
						* hide all collapsible items */
						$(accordion.collapsible).css('display','none');
						
						/* ****
						* bind the accordion header click event */
						$(accordion.header).unbind('click');
						$(accordion.header).bind('click', function() {
							var header_obj      = $(this);          // The current header
							var next_parent_obj = $(this).parent(); // The parent of the header
						
							var content_obj = $(this).siblings(accordion.collapsible); // find the collapsible element
						
							if(content_obj.is(\":hidden\")) {
								// collapse all collapsible elements
								next_parent_obj.siblings(accordion.parent).each(function() {
									$(this).children(accordion.collapsible).slideUp('fast');
								});
								// show the content of the clicked header
								content_obj.slideDown('fast');
							} else {
								content_obj.slideUp('fast');
							}
						});
					$('#error-close> a').click(function() { $('#error-container').slideUp('fast',function() { $('#error-dim').remove(); }); }); }
				}
			</script>\n
		";
	}
	
	
	function debug_footer() {
		if ( !self::$footer_echo ) {
			echo "</ul>";
			echo "<p id=\"error-close\"><a href=\"#\">close</a></p></div></div>";
			self::$footer_echo = true;
		}
	}





	/* ***********************************************************************
	* START :::
	*
	*	Initial error catching method
	*	This will determine if it's a parse error, or other
	*	types of errors.
	*
	*	PARSE:
	*		stops everything dead in its tracks. If display
	*		errors is allowed, then show the error message
	*		otherwise suppress the error and email it
	*
	*
	*	ALL OTHERS:
	*		Run the backtrace through the previous called
	*		functions and display the information for debugging
	*		If display errors is false, suppress the error
	*		and email the error
	*********************************************************************** */
	static public function catch_error($n='', $s='', $f='', $l='') {
			#
			# backtrace the error and display the information for debugging
			$debug_mode = _DEBUG_MODE_;
			if ( @$debug_mode ) { 
				self::error_backtrace($n, $s, $f, $l);
				self::debug();
			
			#
			# suppress the error and email it
			} else {
			
				if ( !headers_sent() ) {
					header("HTTP/1.1 400 Bad Request");
				}
				
				self::error_thrown($n, $s, $f, $l); 
			
			}
			
			return true;
	} 
	/* ***********************************************************************
	* END :::
	*********************************************************************** */


	
	
	
	
	
	
	
	
	/* ***********************************************************************
	* START :::
	*
	*	Backtraces through the errors and displays a nice 
	*	It's pretty simple. Not a lot that needs explaining
	*
	*********************************************************************** */
	static private function error_backtrace($errno, $errstr, $error_file, $error_line) {
		
		
		$errorsThrown = debug_backtrace();
		
		#
		# we skip the first two errors
		# they are the initial calls from error catching
		# we can ignore them
		#
		if ( isset($errorsThrown[0]) ) {
			$errorThrown  = $errorsThrown[0]['args'];
			
			if ( !empty($errorThrown[1]) ) {
				
				#
				# this is the primary error that started it all
				self::$error_chunk .=  "<div class=\"error-error-item\">"
								. "<b class=\"error-error-view\">+</b>"
								. "<b class=\"error-error-header\">".$errorThrown[1]."</b>"
								. "<div class=\"error-error-details\">"
								. "<table id=\"primary-error\" cellspacing=2>\n"
								. "<tr><td class=\"left\"><strong>Error: </strong></td><td>" . $errorThrown[1] . "</td></tr>\n"
								. "<tr><td class=\"left\"><strong>Line: </strong></td><td>" . $errorThrown[3] . "</td></tr>\n"
								. "<tr><td class=\"left\"><strong>File: </strong></td><td>" . $errorThrown[2] . "</td></tr>\n</table>\n";
				
			} 
			
			#
			# for additional debugging, loop through all previous function calls
			# this assists in debugging if you call a method multiple times
			# now we can figure out which instance caused the error
			#
			self::$error_chunk_header .= "<p>All previously executed functions: </p>\n";
			
			self::$error_chunk_header .= "<table id=\"previous-errors\" cellpadding=5 cellspacing=0 border=0 style=\"border: 1px solid #333;\">"
							. "<tr style=\"background: #d3d3d3;\">"
							. "<th>order</th>"
							. "<th>function name</th>"
							. "<th>file name</th>"
							. "<th>line</th>"
							. "</tr>";
				
			#
			# loop thu the backtrace and output
			
			if (!empty(self::$error_chunk)) {
			
				$count = 0;
				array_reverse($errorsThrown);
				
				foreach ($errorsThrown as $error) {
					if ($count > 1 && !empty($error['args'][1]) ) {
						self::$error_chunk .= ($count == 1) ? self::$error_chunk_header : '';
						self::$error_chunk .= '<tr>'
							. '<td>' . ($count-1) . '</td>'
							. '<td>' . $error['args'][1] . '()</td>'
							. '<td>' . $error['args'][2] . '</td>'
							. '<td>' . $error['args'][3] . '</td>'
							. '</tr>';
					}
					$count++;
	
				}
				
				#
				# close it all up
				self::$error_chunk .= '</table></div></div>';
				
		
			} elseif ( true == ($err = error_get_last()) ) {
				self::$error_chunk = "<li class=\"error-error-item\">"
								. "<b class=\"error-error-view\">+</b>"
								. "<b class=\"error-error-header\">".$err['message']."</b>"
								. "<div class=\"error-error-details\">"
								. "<table id=\"primary-error\" cellspacing=2>\n"
								. "<tr><td class=\"left\"><strong>Error Type: </strong></td><td>" . $err['type'] . "</td></tr>\n"
								. "<tr><td class=\"left\"><strong>Message: </strong></td><td>" . $err['message'] . "</td></tr>\n"
								. "<tr><td class=\"left\"><strong>File: </strong></td><td>" . $err['file'] . "</td></tr>\n"
								. "<tr><td class=\"left\"><strong>On line: </strong></td><td>".$err['line']."</td></tr>\n</table></div></li>\n";
			}
		}
		
		return self::$error_chunk;
	}
	/* ***********************************************************************
	* END :::
	*********************************************************************** */


	
	
	
	
	
	
	
	
	/* ***********************************************************************
	* START :::
	*
	*	Backtraces through the errors and displays a nice 
	*	It's pretty simple. Not a lot that needs explaining
	*
	*********************************************************************** */
	function error_thrown($errno, $errstr, $error_file, $error_line) {
		global $security,$app;
		
		$errorThrown = debug_backtrace();
		$errorFunction = '';
		
		#
		# we skip the first two errors
		# they are the initial calls from error catching
		# we can ignore them
		#		
		if (isset($errorThrown[2])) {
			$errorThrown = $errorThrown[2];
			$errorFunction = "\n".'Calling Function: '.$errorThrown['function'];
		
			$referer = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';
			
			$server_r = '';
			$server_out = $_SERVER;
			foreach ($server_out as $key=>$val) {
				$server_r .= $key.' ::: '.$val."\n\r";
			}
			
			$message = '';	
			$message .= "Error Thrown: [{$errno}] {$errstr}{$errorFunction}\n"
					. "File Name: {$error_file}\n"
					. "On line: {$error_line}\n"
					. "Server: " . $_SERVER['SERVER_NAME'] . "\n"
					. "URL: " . $_SERVER['REQUEST_URI'] . "\n"
					. "Referer: {$referer}";
					
			
			if ( isset($errorsThrown[2]) ) {
				$errorThrown  = $errorsThrown[2];
				
				$count = 0;
				array_reverse($errorsThrown);
				
				#
				# loop thu the backtrace and output
				foreach ($errorsThrown as $error) {
					if ($count > 1) {
						$message .= "\n\n".'--------------------------------------------------------------------------'
								. 'Function: '  . $error['function'] . "\n"
								. 'File Name: ' . $error['file']     . "\n"
								. 'On Line: '   . $error['line']     . "\n";
					}
					$count++;	
				}
			}
			
			$message .= "\n\n\nServer Output:\n" . $server_r . "\n\n\n";
			
			//if ( !error_log( $message, 1, _ERRORS_EMAIL_TO_, "From: " . _ERRORS_EMAIL_FROM_ ) ) {
				echo _ERRORS_ERROR_MESSAGE_;
			//}
		}
	}
	/* ***********************************************************************
	* END :::
	*********************************************************************** */


	 
	 
	 function make_full_url() {	
		if ( isset($_SERVER['SCRIPT_NAME']) ) {
			$sub_dir = trim($_SERVER['SCRIPT_NAME'], '/');
			$sub_dir_array = explode('/',$sub_dir);
			$dir_count     = count($sub_dir_array)-1;
			$dir = '';
			for ( $i=0; $i < $dir_count; $i++ ) {
				$dir .= '/' . $sub_dir_array[$i];
			}
		}
	
		if ( isset($_SERVER['HTTP_HOST']) ) {
			$domain = $_SERVER['HTTP_HOST'];
			$url    = $domain . $dir;
			
		} else if ( isset($_SERVER['SERVER_NAME']) ) {
			self::$app->domain = $_SERVER['SERVER_NAME'];
			$domain = $_SERVER['SERVER_NAME'];
			$url    = $domain . $dir;
		}
	
		$s = empty($_SERVER["HTTPS"]) ? '' : 's';
		$url = "http{$s}://" . $url;

		if (defined('_APP_URL_') ) {
			$app_url = _APP_URL_;
			$url = empty($app_url) ? $url : $app_url;
		}
		return $url;
	 }
	
	
	
	
	
	
	
	
	/* ***********************************************************************
	* START :::
	*
	*	echo the css style sheet so the error reporting is pretty
	*
	*********************************************************************** */
	function echo_css() {
		$css_link = '<link rel="stylesheet" id="mainstyle" type="text/css" href="' . self::make_full_url() . '/assets/css/error-reports.css" />';
		return $css_link;
		
	}
	/* ***********************************************************************
	* END :::
	*********************************************************************** */

}
		# Upon All Errors, call the error handling function
			set_error_handler('errors::catch_error',E_ALL);
		
		# Register function to execute at the end of the script
			//register_shutdown_function('errors::catch_error');
			$errors = new errors();
		# Hide error messages
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
?>