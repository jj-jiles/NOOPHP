<?

class cookie {
	
	
	## ##
	##
	## instantiates the object
	##
	## ##
		function __construct() { return true; }
	## ##
	##
	## end
	##
	## ##
	
	
	
	
	
	
	
	
	## ##
	##
	## Gets the domain for use with the cookie
	## Need to do this because a domain doesn't work on local installs of site
	##
	## ##
		static public function domain() {
			$domain = (config::$app->is_live == 'N') ? '' : config::$app->domain;
			return $domain;
		}
	## ##
	##
	## end
	##
	## ##
	
	
	
	
	
	
	
	
	## ##
	##
	## Sets a permanent cookie to bypass login
	##
	## ##
		static public function remember_me() {
			
			if ( isset($_SESSION['user_id']) && $session->rs ) :
				
				# set the expiration for a full year
					$int  = (60*60*24*60);
					$int  = (int) $int;
					$time = (int) time();
					$time = $time+$int;
				
				# set the cookie
				setcookie(
					config::$cookie->remember, 
					$_SESSION['user_id'] . '|' . $session->rs, 
					$time, 
					"/", 
					self::domain()
				);
			endif;
		}
	## ##
	##
	## end
	##
	## ##
	
	
	
	
	
	
	
	
	## ##
	##
	## The user successfully logged in
	## 	set the session cookie
	##
	## ##
		static public function set_session() {
			
			$time = (config::$cookie->lifespan == 0)? 0 : time()+config::$cookie->lifespan;
			
			setcookie(
				config::$cookie->session, 
				$_SESSION['user_id'] . '|' . $session->rs, 
				$time, 
				"/", 
				self::domain()
			);
		}
	## ##
	##
	## end
	##
	## ##
	
	
	
	
	
	
	
	
	## ##
	##
	## grab the structure of the request coming in
	## 		/component/process
	## 	or
	##		/component
	## ##
	static public function destroy() {
		session_destroy();
		setcookie(config::$cookie->general, '', time()-3600, "/", self::domain()); 
		setcookie(config::$cookie->remember, '', time()-3600, "/", self::domain());
		setcookie(config::$cookie->session, '', time()-3600, "/", self::domain()); 
	}
}
$cookie = new cookie;
?>