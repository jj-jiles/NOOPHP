<?
/* ***********************************************************
* END :::
*********************************************************** */	
defined( '_VALID_REFERENCE' ) or die( 'Direct Access to this location is not allowed.' );
include_once(dirname(dirname(dirname(__FILE__))) . '/constants.php');

class Config {
	
	var $complete;
		
	private static $app  = array();
	private static $url  = array();
	private static $path = array();
	private static $db   = array();
	
	function __construct() {
		
		/* ************************************************************
		*
		* Set initial config variables below. These are manual app
		* settings. The settings in the server_settings DB table
		* will be created automatically
		*
		************************************************************ */
		
			$this->app = (object) array(
				'cache'            => _APP_CACHE_,
				'require_session'  => _APP_REQUIRE_SESSION,
				'name'             => _APP_NAME,  // name of your app. this is used for session name as well as controller folder
				'title'            => _APP_TITLE,
				'meta_title'       => _APP_META_TITLE_,
				'meta_description' => _APP_META_DESCRIPTION,
				'meta_keywords'    => _APP_META_KEYWORDS_
			);
			
			$this->url = (object) array();
			
			$this->path = (object) array(
				'root' => dirname(dirname(dirname(_FILE_)))
			);
			
			$this->db   = (object) array(
				'host'     => _DB_HOST_,
				'user'     => _DB_USER_,
				'password' => _DB_PASSWORD,
				'db_name'  => _DB_NAME_,
				'config_lookup_table' => _DB_CONFIG_LOOKUP_TABLE_
			);
			
			
			$this->security = (object) array(
				'posts'        => array( 'allow_html' => _SECURITY_POSTS_ALLOW_HTML_ ),
				'bad_requests' => _SECURITY_ALERT_SEND_EMAIL_,
				'alerts'       => array(
					'send_email' => _SECURITY_ALERT_SEND_EMAIL_, 
					'send_to'    => _SECURITY_ALERT_SEND_TO_
				),
				'handling'     => array(
					'error_page' => _SECURITY_ERROR_PAGE_, 
					'error_url'  => _SECURITY_ERROR_PAGE_URL_
				)
			);
			
			
			$this->security->bad_requests = explode(',', $this->security->bad_requests);
			
		
			#
			# application name. allows for placing the site
			# in a different directory other than root for testing
			# and versioning.
			# If set, this will be used to select the config
			# settings from the database tabel server_settings
			
				# ###
				# Use if you are testing in a sub-folder or local machine
				#	$this->url->root = 'http://domain.com/subdfolder';
				#		
					if ( isset($_SERVER['HTTP_HOST']) ) {
						$this->app->domain = $_SERVER['HTTP_HOST'];
						$this->url->root   = $this->app->domain;
						
					} else if ( isset($_SERVER['SERVER_NAME']) ) {
						$this->app->domain = $_SERVER['SERVER_NAME'];
						$this->url->root   = $this->app->domain;
					}
				
					$s = empty($_SERVER["HTTPS"]) ? '' : 's';
					$this->url->root = "http{$s}://" . $this->url->root;
					
					#
					# Added for testing on local machine, in subfolder
					$this->url->root .= '/wretchedapi';
					#
					
				#
				# ####
				
		
		# ####
		# Start the Session
		#### #
			session_name($this->app->name);
			session_cache_expire(30);
			session_start();
			set_time_limit(60);
		# #### #
		
		if ( empty($this->db->config_lookup_table)) {
			$this->no_database();
		} else {
			$this->with_database();
		}
	}
	
	
	
	function no_database() {
		
		$this->admin->local = isset($this->admin->local) ? $this->admin->local : true;
		$this->path         = isset($this->path)    ? $this->path    : (object) array();
		$this->url          = isset($this->url)     ? $this->url     : (object) array();
		$this->contact      = isset($this->contact) ? $this->contact : (object) array();
		$this->app          = isset($this->app)     ? $this->app     : (object) array();
		
		if ( empty($this->db->config_lookup_table)) {
			
			if ( isset($this->url->root) && !empty($this->url->root) ) {
				$root_url = $this->url->root;
			
			} else {
				$root_url  = $_SERVER['HTTP_HOST'];		
				$s         = empty($_SERVER["HTTPS"]) ? '' : 's';
				$root_url  = "http{$s}://".$root_url;
				
			}
			
			
			$root_path = dirname(dirname(dirname(dirname(__FILE__))));
			
			#
			# general app variables
			$this->app->domain           = $_SERVER['HTTP_HOST'];
			$this->app->name             = $this->app->name;
			$this->app->title            = $this->app->title;
			$this->app->meta_title       = $this->app->meta_title;
			$this->app->meta_description = $this->app->meta_description;
			$this->app->meta_keywords    = $this->app->meta_keywords;
			$this->app->analytics_code   = isset($this->app->analytics_code)   ? $this->app->analytics_code : '';
			$this->app->key_google_maps  = isset($this->app->key_google_maps)  ? $this->app->key_google_maps: '';
			$this->app->is_live          = isset($this->app->status_is_live)   ? $this->app->status_is_live : '';
			$this->app->is_active        = isset($this->app->status_is_active) ? $this->app->status_is_active : '';
			
			$this->app->allowed_login_failures    = isset($this->app_allowed_login_failures) ? $this->app_allowed_login_failures : '5';
			$this->app->account_lock_out_interval = isset($this->app_allowed_login_failures) ? $this->app_account_lock_out_interval : '20';
			
			
			#
			# Cookie Settings
			$this->cookie = (object) array(
				'main'     => $this->app->name,
				'session'  => $this->app->name . '___session',
				'general'  => $this->app->name . '___general',
				'remember' => $this->app->name . '___remember',
				'lifespan' => $this->cookie->lifespan
			);
			
			
			
			$this->salts->password       = isset($this->salts->password) ? $this->salts->password : '';
			$this->contact->name         = isset($this->contact->name)   ? $this->contact->name : '';
			$this->contact->email        = isset($this->contact->email)  ? $this->contact->email : '';
			
			$this->url = (object) array(
				'root'     => $root_url,
				'assets'   => $root_url  . '/assets',
				'views'    => $root_url  . '/views',
				'modules'  => $root_url  . '/modules',
				'controls' => $root_url  . '/controls',
				'ajax'     => $root_url  . '/ajax',
				'login'    => $root_url  . '/sign-in'
			);
			
			$this->path = (object) array(
				'root'     => $root_path,
				'views'    => $root_path . '/views',
				'modules'  => $root_path . '/modules',
				'assets'   => $root_path . '/assets',
				'controls' => $root_path . '/controls',
				'app'      => $root_path . '/controls/' . $this->app->name
			);
			
			if ( @$this->admin->local ) :
				// Admin path
				$this->path->admin = $this->path->root.'/'.$this->admin->path;
				// Admin path
				$this->url->admin  = $this->url->root.'/'.$this->admin->url;
			else :
				$this->path->admin = $this->admin->path;
				$this->path->url   = $this->admin->url;
			endif;
	
	
	
		}
			
	
	
		/* *****************************************************
		 * START :::
		 *   Builds the $config object for the domain
		 ***************************************************** */
		function with_database($connectOnly=false) {
			global $connection, $db, $config, $security, $app;
			
			if ( isset($this->db) && (!empty($this->db['db_name']) && !empty($this->db->config_lookup_table)) ) {
				
				if ( !$connectOnly ) {
					$db->connect();
					
					$config_lookup_table = $this->db->db_name.'.'.$this->db->config_lookup_table;
					
						
					if ( !isset($this->url->root) || empty($this->url->root) ) {		
						if ( isset($_SERVER['HTTP_HOST']) ) {
							$this->app->domain = $_SERVER['HTTP_HOST'];
							$this->url->root   = $this->app->domain;
							
						} else if ( isset($_SERVER['SERVER_NAME']) ) {
							$this->app->domain = $_SERVER['SERVER_NAME'];
							$this->url->root   = $this->app->domain;
						}
					}
			
					$s = empty($_SERVER["HTTPS"]) ? '' : 's';
					$this->url->root = "http{$s}://" . $this->url->root;
	
		
					if ( isset($this->app->name) && !empty($this->app->name)) {
						$sql = "SELECT * FROM {$config_lookup_table} WHERE app_name = '".$this->app->name."'";
						$sql = $db->query($sql);
					
					} else {
						$sql = "SELECT * FROM {$config_lookup_table} WHERE domain = '" . $this->app->domain . "'";
						$sql = $db->query($sql);
						
					}
					
					if ( $db->num_rows() == 0 ) {
						$sql = "SELECT * FROM {$config_lookup_table} WHERE `default` = 'Y'";
						$sql = $db->query($sql);
					}
					
					#
					# output your query here and set all settings accordingly
					# using the $config object
					# example:
					#	$this->path->root = $row->absolute_path;
					#	$this->url->root  = $row->base_url;
					while ($row = $db->fetch_object($sql)) {
					
					
						$this->errors->display = ( $row->status_display_errors == 'Y' ) ? true : false;
					
					
						$prefix = '';
						//if ( @$app->isMobile() ) :
						//	$prefix = '/m';
						//endif;
					
					
						#
						# general app variables
						$this->app->domain           = $row->app_domain;
						$this->app->name             = $row->app_name;
						$this->app->title            = $row->app_title;
						$this->app->meta_title       = $row->app_meta_title;
						$this->app->meta_description = $row->app_meta_description;
						$this->app->meta_keywords    = $row->app_meta_keywords;
						$this->app->analytics_code   = $row->analytics_code;
						$this->app->key_google_maps  = $row->key_google_maps;
						$this->app->is_live          = $row->status_is_live;
						$this->app->is_active        = $row->status_is_active;
						
						$this->app->allowed_login_failures    = $row->app_allowed_login_failures;
						$this->app->account_lock_out_interval = $row->app_account_lock_out_interval;
						
						$this->cookie->main          = $row->cookie_app;
						$this->cookie->session       = $row->cookie_session;
						$this->cookie->general       = $row->cookie_general;
						$this->cookie->remember      = $row->cookie_session . '___remember';
						$this->cookie->lifespan      = $row->cookie_lifespan;
						$this->salts->password       = $row->salts_password;
						$this->contact->name         = $row->contact_name;
						$this->contact->email        = $row->contact_email;
					
					
						#
						# establish the structure of the app's urls
						$this->app->structure = explode(',',$row->app_structure);
					
						#
						# create the url and paths for the app
						$this->url->root       = $row->url_base;
						$this->url->absolute   = $this->url->root;
						$this->path->root      = $row->path_base;
						$this->path->absolute  = $this->path->root;
											
								
						$admin_url             = $row->url_admin;
						$admin_path            = $row->path_admin;
						
						#
						# if the reference is coming from the admin, map the paths/urls to the admin path
						if ( defined("_VALID_REFERENCE") && _VALID_REFERENCE == 'admin') :
							$this->url->root  .= $admin_url;
							$this->path->root .= $admin_path;
						endif;
						
						$this->url->assets   = $this->url->root  . '/assets' . $prefix;
						$this->url->views    = $this->url->root  . '/views' . $prefix;
						$this->url->modules  = $this->url->root  . '/modules' . $prefix;
						$this->url->controls = $this->url->root  . '/controls';
						$this->url->ajax     = $this->url->root  . '/ajax';
						$this->url->login    = $this->url->root  . '/sign-in';
						
						$s = (bool) empty($_SERVER["HTTPS"]);					
						if ( !$s ) :
							$this->url->assets = str_replace('http:','https:', $this->url->assets);
							$this->url->ajax   = str_replace('http:','https:', $this->url->ajax);
						endif;
						
						$this->path->views    = $this->path->root . '/views' . $prefix;
						$this->path->modules  = $this->path->root . '/modules' . $prefix;
						$this->path->assets   = $this->path->root . '/assets' . $prefix;
						$this->path->controls = $this->path->root . '/controls';
						$this->path->app      = $this->path->root . '/controls/' . $this->app->name;
						
						
						// Admin path
						$this->path->admin   = $this->path->root    . $row->path_admin;
						// Admin path
						$this->url->admin   = $this->url->root    . $row->url_admin;
						
					}
				}
			}
		}
		/* *****************************************************
		 * END :::
		 ***************************************************** */
		
		
	}
	 
	 
	 
	 
	 
	 
	 
	 function analytics_code($return=false) {
		 $this->app->analytics_code = ($this->app->is_live == 'Y') ? $this->app->analytics_code : '';
		 if ( !$return ) {
			 echo $this->app->analytics_code;
		 } else {
			return $this->app->analytics_code;
		 }
	 }
}

$config = new Config();
?>