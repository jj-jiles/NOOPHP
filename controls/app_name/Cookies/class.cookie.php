<?

class Cookie_System {
	
	
	## ##
	##
	## instantiates the object
	##
	## ##
		function Cookie_System() { return true; }
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
	function domain() {
		global $config;
		$domain = ($config->app->is_live == 'N') ? '' : $config->app->domain;
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
		function remember_me() {
			global $app, $config, $session;
			
			if ( isset($_SESSION['user_id']) && $session->rs ) :
				
				# set the expiration for a full year
					$int  = (60*60*24*60);
					$int  = (int) $int;
					$time = (int) time();
					$time = $time+$int;
				
				# set the cookie
				setcookie(
					$config->cookie->remember, 
					$_SESSION['user_id'] . '|' . $session->rs, 
					$time, 
					"/", 
					$this->domain()
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
		function set_session() {
			global $config, $app, $session;
			
			$time = ($config->cookie->lifespan == 0)? 0 : time()+$config->cookie->lifespan;
			
			setcookie(
				$config->cookie->session, 
				$_SESSION['user_id'] . '|' . $session->rs, 
				$time, 
				"/", 
				$this->domain()
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
	function destroy() {
		global $config;
		session_destroy();
		setcookie($config->cookie->general, '', time()-3600, "/", $this->domain()); 
		setcookie($config->cookie->remember, '', time()-3600, "/", $this->domain());
		setcookie($config->cookie->session, '', time()-3600, "/", $this->domain()); 
	}
}
$cookie = new Cookie_System;
?>