<?php
defined( '_VALID_REFERENCE' ) or die( 'Direct Access to this location is not allowed.' );
#
#
# corrects time zone error for the live Commerce server
	date_default_timezone_set('America/Chicago');
# end
#
#

/* *******************************************************************************************
*
*	create the $security object 
*
*
*	Configure the primary $security settings
*
******************************************************************************************* */










/* ********************************************************************************************
*	
*	Security Class for basic app security:
*
*		Runs through specific routines to validate requests and posts
*		Should the request or post contain anything malicious in appearance, we stop
*		processing and forward user to Error page
*
*
*		self::$settings Object: settings some basic settings for the app's security
*	
*
******************************************************************************************** */
class security {	
	
	private static $settings;
	private static $qs;
	private static $decoded_url;
	
	static public function init() {
		
		
			# configure the primary $security settings
			# these are developer controlled so adjust as needed
			self::$settings  = array(
				
				
			/* **************************************** */
			/* ******** Edit these accordingly ******** */
			
					# do you want to allow HTML to be used in form POSTS?
						'allow_html_in_posts'	=> true,
								
					# Gets the full url of the request
						'site_url' 	          => '',
								
					# where do you want the user redirected upon bad request
						'error_tracking'   => array(
							'show_errors'  => true,              # show the errors (this is overridden if set differntly in server_settings table)
							'email_to'     => 'error@domain.com',  # email the error to?
							'email_from'   => 'error@domain.com',
							'error_page'   => config::$url->root . '/error'
						),
								
					# should we track the attacks in the database
						'track_attempts'		=> false,
						'track_attempts_db'		=> '',		// if we track attempts, what db do we store
						'track_attempts_table'	=> '',		// what table is used
						'track_attempts_with'	=> '',		// or, we can specify a method/function for custom tracking
					
					# send an email when an attempted attack is caught?
						'email_alert'			=> false,
						'email_alert_to'		=> '',
						'email_alert_subject'	=> '',
						'email_alert_remplate'	=> '',
								
					# types of requests not allowed
						'bad_requests' => array(
							'globals',
							'mosconfig_absolute_path',
							'_session',
							'&amp;_request',
							'&_request',
							'&amp;_post',
							'&_post',
							'cftoken',
							'cfid',
							'src="',
							'/**/',
							'/*!',
							'/union/',
							' union ',
							'%20union%20',
							'drop table',
							'alter table',
							'drop/**/table',
							'alter/**/table',
							'load_file',
							'infile'
						),
				
			/* **************** END ******************* */
			/* **************************************** */
		
			
			
			/* **************************************** */
			/* ************* You NO Touchy ************ */
					
					
					'qs'			=> '', // used to store the query string
					'decoded_url'	=> '', // completed decodes incoming url string so we can evaluate the request
				
				
			/* **************** END ******************* */
			/* **************************************** */
				
			);
			# END: settings
		
			if ( empty(self::$settings['site_url']) ) :
				self::$settings['site_url'] = self::selfURL();
			endif;
			
			if ( @self::validate_request() ) :
				db::connect();
				
			else :
				echo 'There was a problem';
				die();
				
			endif;
	}



	/* *****************************************************
	* START :::
	*   builds a URL of request coming in
	***************************************************** */
	static private function selfURL() { 
		$root_url  = $_SERVER['HTTP_HOST'];		
		$s         = empty($_SERVER["HTTPS"]) ? '' : 's';
		$root_url  = "http{$s}://".$root_url;
		return $root_url;
	} 
	static private function strleft($s1, $s2) { 
		return substr($s1, 0, strpos($s1, $s2)); 
	}
	/* *****************************************************
	* END ::: selfURL && strleft
	***************************************************** */




	/* *****************************************************
	* START :::
	*   Parses the URL to get domain only
	***************************************************** */
	function parse_url_domain ($url) {
		$parsed = parse_url($url);
		$hostname = $parsed['host'];
		$hostname = str_replace ('www.','', $hostname);
		return $hostname;
	}
	/* *****************************************************
	* END ::: parse_url_domain
	***************************************************** */
	
	
	
	
	
	/* **************************************************************
	*
	* Default method that starts the security checks
	*
	************************************************************** */
	function start() {
		
		if ( empty(self::$settings['site_url']) ) :
			self::$settings['site_url'] = self::selfURL();
		endif;
		
		if ( @self::validate_request() ) :
			//db::connect();
			
		else :
			echo 'There was a problem';
			die();
			
		endif;
	}
	/* **************************************************************
	* END :::
	************************************************************** */
	
	
	
	
	
	
	
	
	/* **************************************************************
	*
	* Gets the real IP address of the user
	* Not just the shared IP
	*
	************************************************************** */
	static public function validate_request() {
		
		//parse_str($_SERVER['QUERY_STRING']);
		
		self::$qs			= urldecode($_SERVER['QUERY_STRING']);
		self::$qs			= str_replace("%5B%5D", "", self::$qs);
		self::$qs			= str_replace("$", "", self::$qs);
		self::$decoded_url	= urldecode(self::$qs);
		self::$decoded_url	= strtolower(self::$decoded_url);
		
		parse_str(self::$decoded_url);
		
		foreach (config::$security->bad_requests as $key) {
			if (isset($$key)) {
				# user is bad.
				self::send_away();
			}
		}
		
		self::validate_posts();
		
		return true;
	}
	/* **************************************************************
	* END :::
	************************************************************** */
	
	
	
	
	
	
	
	
	/* **************************************************************
	*
	* Gets the real IP address of the user
	* Not just the shared IP
	*
	************************************************************** */
	static public function validate_posts() {
		
		$is_admin = false;
		$is_admin = (bool) strchr(config::$url->root, "/admin");
		
		
		/* ***
		*
		* Evaluate the POST array */
		if ( !$is_admin && count($_POST) > 0 ) :
			foreach ($_POST as $key=>$val) :
				$has_html  = false;
				$is_array  = false;
				$has_spam  = false;
				$more_spam = false;
				
				$is_array = (bool) is_array($_POST[$key]);
				
				if ( !$is_array ) :
					
					# check for the typical URL spamming
					$has_spam 	= (bool) strchr($val, "url=");
					$more_spam 	= (bool) strchr($val, "[url");
					
					# check for the any kind of html if specified to do so
					if ( !config::$security->posts['allow_html'] ) :
						$has_html = preg_match("/(\<(\/?[^\>]+)\>)/i", $val, $match);
					endif;
				
					if ( @$has_spam || @$more_spam || $has_html ) :
						self::send_away();
					endif;
					
				endif;
			endforeach;
		endif;
		/* *** */
		
		
		/* ***
		*
		* Evaluate the REQUEST array */
		if ( !$is_admin && count($_REQUEST) > 0 ) :
			foreach ($_REQUEST as $key=>$val) :
				$has_html  = false;
				$is_array  = false;
				$has_spam  = false;
				$more_spam = false;
				
				$is_array = (bool) is_array($_REQUEST[$key]);
				
				if ( !$is_array ) :
					
					# check for the typical URL spamming
					$has_spam 	= (bool) strchr($val, "url=");
					$more_spam 	= (bool) strchr($val, "[url");
					
					# check for the any kind of html if specified to do so
					if ( !config::$security->posts['allow_html'] ) :
						$has_html = preg_match("/(\<(\/?[^\>]+)\>)/i", $val, $match);
					endif;
				
					if ( @$has_spam || @$more_spam || $has_html ) :
						self::send_away();
						
					endif;
				endif;
			endforeach;
		endif;
		/* *** */
	}
	/* **************************************************************
	* END :::
	************************************************************** */
	
	
	
	
	
	
	
	
	/* **************************************************************
	*
	* If this user is trying something stupid, get rid of them
	*
	************************************************************** */
	function send_away() {
		global $config, $security;
		
		# need to rewrite these these methods
		//self::record_attack();
		//self::email_attack_report();
		
		$error_page = @config::$security['handling']['error_page'] ? config::$security['handling']['error_url'] : config::$url->root;
		//header( "Location:" . $error_page );
		die();
	}
	/* **************************************************************
	* END :::
	************************************************************** */
	
	
	
}
	
		
##
## instantiate the primary $security class
##

##
## required files for app to work
	$dir = dirname(dirname(__FILE__));
	include_once( $dir . '/Email/api.class.email.php');
	include_once( $dir . '/Application/api.class.app.php');
##


##
## start processing the security methods
	security::init();
##


##
## everything is secure, so continue

	## connect to DB and get config settings
	## stored in server_settings table
	##

	##
	## start running main app functions
		include_once( $dir . '/URLs/api.class.urls.php');
		include_once( $dir . '/Paths/api.class.paths.php');
		include_once( $dir . '/Request/api.class.request.php');
		include_once( $dir . '/Forms/api.class.forms.php');
		include_once( $dir . '/Cache/api.class.cache.php');
		include_once( $dir . '/Pages/api.class.pages.php');
		include_once( $dir . '/Components/api.class.components.php');
		$dir = dirname(dirname(dirname(__FILE__)));
		include_once( $dir . '/' . config::$app->name . '/Application/class.app.init.php');
	##

##
##
?>