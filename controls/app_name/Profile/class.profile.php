<?

class Session_Profile {
	
	function Session_Profile() {
		return true;
	}
	# #### #
	
	
	
	
	
	
	
	
	#
	#
	# returns a url to the profile page of the user
	# determines what account type they are and builds URL accordingly
	function profile_page() {
		return url::root() . '/' . self::account_type_long() . '/profile';
	}
	# #### #
	
	
	
	
	
	
	
	function not_viewing_profile() {
		
		parse_str($_SERVER['QUERY_STRING']);
		
		$url = url::root();
		$url .= isset($urd) ? '/'.$urd : '';
		
		if ( $url != self::profile_page() ) :
			return true;
		endif;
		
		return false;
	}
		
	
	
	
	
	
	
	
	
	#
	#
	# determines if the user has completed their initial profile information.
	# returns true/false
	function not_completed() {
		if ( empty($_SESSION['profile']->first) ) :
			return true;
		endif;
		return false;
	}
	# #### #
	
	
	
	
	
	
	
	
	#
	#
	# return true if the user is currently logged in
	function is_logged_in() {
		if ( isset($_SESSION['profile']->id) && !empty($_SESSION['profile']->id) ) :
			return true;
		endif;
		return false;
	}
	# #### #
	
	
	
	
	
	
	
	
	#
	#
	# check if user is a developer and returns true if so
	function is_developer() {
		if ( $this->account_type() == 'D' ) :
			return true;
		endif;
		return false;
	}
	# #### #
	
	
	
	
	
	
	
	
	#
	#
	# returns the account type the user has
	function account_type() {
		$at = '';
		if ( isset($_SESSION['profile']->account_type) ) :
			$at = $_SESSION['profile']->account_type;
		endif;
		return $at;
	}
	# #### #
	
	
	
	
	
	
	
	
	#
	#
	# returns the account type the user has
	function account_type_long() {
		$at = '';
		if ( isset($_SESSION['profile']->account_type_long) ) :
			$at = $_SESSION['profile']->account_type_long;
		endif;
		return $at;
	}
	# #### #
	
	
	
	
	
	
	
	
	#
	#
	# simply updates the user's profile
	function update() {
		global $db, $form, $session;
		
		$password_sql='';
		
		if ( isset($form->new_password) && !empty($form->new_password) ) :
			if ( $form->new_password != $form->confirm_new_password ) :
				$form->error .= '<li>New password not confirmed successfully</li>';
			
			else :
				
				$md5_password = $session->encode_password($form->new_password);
				$password_sql = ", password = '{$md5_password}' ";
			
			endif;
		endif;
		
		if ( @$form->has_post() && !$form->has_error() ) :
			$id = $_SESSION['profile']->id;		
			
			$sql = "UPDATE accounts SET"
				. " first = '$form->incl_first',"
				. " last = '$form->incl_last',"
				. " company = '$form->incl_company',"
				. " website = '$form->incl_website',"
				. " address = '$form->incl_address',"
				. " city = '$form->incl_city',"
				. " state = '$form->incl_state',"
				. " zip = '$form->incl_zip',"
				. " country = '$form->incl_country',"
				. " phone = '$form->incl_phone',"
				. " email = '$form->incl_email'"
				. $password_sql
				. " WHERE id = $id";
			
			if ( !$db->query($sql) ) :
				return false;
			endif;
			
			return true;
		
		endif;		
		
	}
	
}

$profile = new Session_Profile();
?>