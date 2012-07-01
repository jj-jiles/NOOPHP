<?
/* ***********************************************************
* END :::
*********************************************************** */	
defined( '_VALID_REFERENCE' ) or die( 'Direct Access to this location is not allowed.' );

class config {
	
	private $complete;
		
	public static $app      = array();
	public static $url      = array();
	public static $path     = array();
	public static $db       = array();
	public static $errors   = array();
	public static $admin    = array();
	public static $security = array();
	public static $cookie   = array();
	public static $salts    = array();
	public static $contact  = array();
	
	public function __construct() {
		
		/* ************************************************************
		*
		* Set initial config variables below. These are manual app
		* settings. The settings in the server_settings DB table
		* will be created automatically
		*
		************************************************************ */
		
			self::$app = (object) array(
				'cache'            => _APP_CACHE_,
				'require_session'  => _APP_REQUIRE_SESSION_,
				'name'             => _APP_NAME_,  // name of your app. this is used for session name as well as controller folder
				'title'            => _APP_TITLE_,
				'meta_title'       => _APP_META_TITLE_,
				'meta_description' => _APP_META_DESCRIPTION_,
				'meta_keywords'    => _APP_META_KEYWORDS_,
				'aes_password'     => _APP_AES_PASSWORD_
			);
			
			self::$url     = (object) array();
			self::$cookie  = (object) array();
			self::$salts   = (object) array();
			self::$contact = (object) array();
			
			self::$admin = (object) array(
				'path' => _ADMIN_PATH_,
				'url'  => _ADMIN_URL_
			);
			
			self::$path = (object) array(
				'root' => dirname(dirname(dirname(dirname(__FILE__))))
			);
			
			self::$db   = (object) array(
				'host'     => _DB_HOST_,
				'user'     => _DB_USER_,
				'password' => _DB_PASSWORD_,
				'db_name'  => _DB_NAME_,
				'config_lookup_table' => _DB_CONFIG_LOOKUP_TABLE_
			);
			
			
			self::$security = (object) array(
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
			
			
			self::$errors = (object) array(
				'display'     => _ERRORS_DISPLAY_,
				'redirect_to' => _ERRORS_REDIRECT_TO_URL_,
				'email_to'    => _ERRORS_EMAIL_TO_,
				'email_from'  => _ERRORS_EMAIL_FROM_,
				'style_sheet' => _ERRORS_STYLE_SHEET_,
				'suppressed_error_message' => _ERRORS_ERROR_MESSAGE_
			);
			
			
			self::$security->bad_requests = explode(',', self::$security->bad_requests);
			self::$cookie->lifespan       = _APP_COOKIE_LIFESPAN_;			
			self::$salts->password        = _APP_SALTS_PASSWORD_;
			self::$url->cdn               = _APP_CDN_URL_;
			self::$app->fb_opengraph_meta = _APP_FB_OPENGRAPH_META_;
			
		
			#
			# application name. allows for placing the site
			# in a different directory other than root for testing
			# and versioning.
			# If set, this will be used to select the config
			# settings from the database tabel server_settings

			self::make_full_url();
							
			#
			# ####
				
		
		# ####
		# Start the Session
		#### #
			session_name(self::$app->name);
			session_cache_expire(30);
			session_start();
			set_time_limit(60);
		# #### #
		
		if ( empty(self::$db->config_lookup_table)) {
			self::no_database();
		} else {
			self::with_database();
		}
	}
	
	
	
	function no_database() {
		
		self::$admin->local = isset(self::$admin->local) ? self::$admin->local : true;
		self::$path         = isset(self::$path)    ? self::$path    : (object) array();
		self::$url          = isset(self::$url)     ? self::$url     : (object) array();
		self::$contact      = isset(self::$contact) ? self::$contact : (object) array();
		self::$app          = isset(self::$app)     ? self::$app     : (object) array();
		
		if ( empty(self::$db->config_lookup_table)) {
			
			if ( isset(self::$url->root) && !empty(self::$url->root) ) {
				$root_url = self::$url->root;
			
			} else {
				$root_url  = $_SERVER['HTTP_HOST'];		
				$s         = empty($_SERVER["HTTPS"]) ? '' : 's';
				$root_url  = "http{$s}://".$root_url;
				
			}
			
			
			$root_path = dirname(dirname(dirname(dirname(__FILE__))));
			
			#
			# general app variables
			self::$app->domain           = $_SERVER['HTTP_HOST'];
			self::$app->name             = self::$app->name;
			self::$app->title            = self::$app->title;
			self::$app->meta_title       = self::$app->meta_title;
			self::$app->meta_description = self::$app->meta_description;
			self::$app->meta_keywords    = self::$app->meta_keywords;
			self::$app->analytics_code   = isset(self::$app->analytics_code)   ? self::$app->analytics_code : '';
			self::$app->key_google_maps  = isset(self::$app->key_google_maps)  ? self::$app->key_google_maps: '';
			self::$app->is_live          = isset(self::$app->status_is_live)   ? self::$app->status_is_live : '';
			self::$app->is_active        = isset(self::$app->status_is_active) ? self::$app->status_is_active : '';
			
			self::$app->allowed_login_failures    = isset(self::$app_allowed_login_failures) ? self::$app_allowed_login_failures : '5';
			self::$app->account_lock_out_interval = isset(self::$app_allowed_login_failures) ? self::$app_account_lock_out_interval : '20';
			
			
			#
			# Cookie Settings
			self::$cookie = (object) array(
				'main'     => self::$app->name,
				'session'  => self::$app->name . '___session',
				'general'  => self::$app->name . '___general',
				'remember' => self::$app->name . '___remember',
				'lifespan' => self::$cookie->lifespan
			);
			
			
			
			self::$salts->password       = isset(self::$salts->password) ? self::$salts->password : '';
			self::$contact->name         = isset(self::$contact->name)   ? self::$contact->name : '';
			self::$contact->email        = isset(self::$contact->email)  ? self::$contact->email : '';
			
			
			
			self::$url = (object) array(
				'root'     => $root_url,
				'assets'   => $root_url  . '/assets',
				'views'    => $root_url  . '/views',
				'modules'  => $root_url  . '/modules',
				'controls' => $root_url  . '/controls',
				'ajax'     => $root_url  . '/ajax',
				'login'    => $root_url  . '/sign-in',
				'error'    => $root_url  . '/error'
			);
			
			self::$path = (object) array(
				'root'     => $root_path,
				'views'    => $root_path . '/views',
				'modules'  => $root_path . '/modules',
				'assets'   => $root_path . '/assets',
				'controls' => $root_path . '/controls',
				'app'      => $root_path . '/controls/' . self::$app->name
			);
			
			if ( @self::$admin->local ) {
				// Admin path
				self::$path->admin = self::$path->root.'/'.self::$admin->path;
				// Admin path
				self::$url->admin  = self::$url->root.'/'.self::$admin->url;
			} else {
				self::$path->admin = self::$admin->path;
				self::$path->url   = self::$admin->url;
			}
		}
	}
	
	
	/* *****************************************************
	 * START :::
	 *   Builds the $config object for the domain
	 ***************************************************** */
	function with_database() {
		
		if ( isset(self::$db) && (!empty(self::$db->db_name) && !empty(self::$db->config_lookup_table)) ) {
			
				#
				# Connect to the database
				db::connect();
				
				$config_lookup_table = self::$db->db_name . '.' . self::$db->config_lookup_table;
				

				self::make_full_url();
	
	
				if ( isset(self::$app->name) && !empty(self::$app->name)) {
					$sql = "SELECT * FROM {$config_lookup_table} WHERE app_name = '".self::$app->name."'";
					$sql = db::query($sql);
				
				} else {
					$sql = "SELECT * FROM {$config_lookup_table} WHERE app_domain = '" . self::$app->domain . "'";
					$sql = db::query($sql);
					
				}
				
				if ( db::num_rows() == 0 ) {
					$sql = "SELECT * FROM {$config_lookup_table} WHERE `default` = 'Y'";
					$sql = db::query($sql);
				}
				
				#
				# output your query here and set all settings accordingly
				# using the $config object
				# example:
				#	self::$path->root = $row->absolute_path;
				#	self::$url->root  = $row->base_url;
				while ($row = db::fetch_object($sql)) {
				
				
					self::$errors->display = ( $row->status_display_errors == 'Y' ) ? true : false;
				
				
					$prefix = '';
					//if ( @$app->isMobile() ) :
					//	$prefix = '/m';
					//endif;
				
				
					#
					# general app variables
					self::$app->domain           = $row->app_domain;
					self::$app->name             = $row->app_name;
					self::$app->title            = $row->app_title;
					self::$app->meta_title       = $row->app_meta_title;
					self::$app->meta_description = $row->app_meta_description;
					self::$app->meta_keywords    = $row->app_meta_keywords;
					self::$app->analytics_code   = $row->analytics_code;
					self::$app->key_google_maps  = $row->key_google_maps;
					self::$app->is_live          = $row->status_is_live;
					self::$app->is_active        = $row->status_is_active;
					
					self::$app->allowed_login_failures    = $row->app_allowed_login_failures;
					self::$app->account_lock_out_interval = $row->app_account_lock_out_interval;
					
					self::$cookie->main          = $row->cookie_app;
					self::$cookie->session       = $row->cookie_session;
					self::$cookie->general       = $row->cookie_general;
					self::$cookie->remember      = $row->cookie_session . '___remember';
					self::$cookie->lifespan      = $row->cookie_lifespan;
					self::$salts->password       = $row->salts_password;
					self::$contact->name         = $row->contact_name;
					self::$contact->email        = $row->contact_email;
				
				
					#
					# establish the structure of the app's urls
					self::$app->structure = explode(',',$row->app_structure);
				
					#
					# create the url and paths for the app
					self::$url->root       = $row->url_base;
					self::$url->absolute   = self::$url->root;
					self::$path->root      = $row->path_base;
					self::$path->absolute  = self::$path->root;
										
							
					$admin_url             = $row->url_admin;
					$admin_path            = $row->path_admin;
					
					#
					# if the reference is coming from the admin, map the paths/urls to the admin path
					if ( defined("_VALID_REFERENCE") && _VALID_REFERENCE == 'admin') :
						self::$url->root  .= $admin_url;
						self::$path->root .= $admin_path;
					endif;
					
					self::$url->assets   = self::$url->root  . '/assets' . $prefix;
					self::$url->views    = self::$url->root  . '/views' . $prefix;
					self::$url->modules  = self::$url->root  . '/modules' . $prefix;
					self::$url->controls = self::$url->root  . '/controls';
					self::$url->ajax     = self::$url->root  . '/ajax';
					self::$url->login    = self::$url->root  . '/sign-in';
					self::$url->error    = self::$url->root  . '/error';
					
					$s = (bool) empty($_SERVER["HTTPS"]);					
					if ( !$s ) :
						self::$url->assets = str_replace('http:','https:', self::$url->assets);
						self::$url->ajax   = str_replace('http:','https:', self::$url->ajax);
					endif;
					
					self::$path->views    = self::$path->root . '/views' . $prefix;
					self::$path->modules  = self::$path->root . '/modules' . $prefix;
					self::$path->assets   = self::$path->root . '/assets' . $prefix;
					self::$path->controls = self::$path->root . '/controls';
					self::$path->app      = self::$path->root . '/controls/' . self::$app->name;
					
					
					// Admin path
					self::$path->admin   = self::$path->root    . $row->path_admin;
					// Admin path
					self::$url->admin   = self::$url->root    . $row->url_admin;
					
				}
		}
	}
	/* *****************************************************
	 * END :::
	 ***************************************************** */
	 
	 
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
			self::$app->domain = $_SERVER['HTTP_HOST'];
			self::$url->root   = self::$app->domain . $dir;
			
		} else if ( isset($_SERVER['SERVER_NAME']) ) {
			self::$app->domain = $_SERVER['SERVER_NAME'];
			self::$url->root   = self::$app->domain . $dir;
		}
	
		$s = empty($_SERVER["HTTPS"]) ? '' : 's';
		self::$url->root = "http{$s}://" . self::$url->root;

		if (defined('_APP_URL_') ) {
			$app_url = _APP_URL_;
			self::$url->root = empty($app_url) ? self::$url->root : $app_url;
		}
	 }
	 
	 
	 
	 function analytics_code($return=false) {
		 self::$app->analytics_code = (self::$app->is_live == 'Y') ? self::$app->analytics_code : '';
		 if ( !$return ) {
			 echo self::$app->analytics_code;
		 } else {
			return self::$app->analytics_code;
		 }
	 }
}

$config = new config();
?>