<?
class Forms_Validate {
	
	
	public function __construct() {}
	
	
	
	/* **************************************************************
	*
	* Validate the format of an email address
	*
	************************************************************** */
	public function is_valid_email( $email ) {
		/**
			Validate an email address.
			Provide email address (raw input)
			Returns true if the email address has the email 
			address format and the domain exists.
		*/
		$is_valid = true;
		if ( !empty( $email ) ) :
		
			$at_index = strrpos($email, "@");
			if (is_bool($at_index) && !$at_index) {
				$is_valid = false;
			} else {
				$domain    = substr($email, $at_index+1);
				$local     = substr($email, 0, $at_index);
				$localLen  = strlen($local);
				$domainLen = strlen($domain);
				if ($localLen < 1 || $localLen > 64) {
					// local part length exceeded
					$is_valid = false;
				} else if ($domainLen < 1 || $domainLen > 255) {
					// domain part length exceeded
					$is_valid = false;
				} else if ($local[0] == '.' || $local[$localLen-1] == '.') {
					// local part starts or ends with '.'
					$is_valid = false;
				} else if (preg_match('/\\.\\./', $local)) {
					// local part has two consecutive dots
					$is_valid = false;
				} else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
					// character not valid in domain part
					$is_valid = false;
				} else if (preg_match('/\\.\\./', $domain)) {
					// domain part has two consecutive dots
					$is_valid = false;
				} else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local))) {
					// character not valid in local part unless 
					// local part is quoted
					if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local))) {
						$is_valid = false;
					}
				}
				
				if ($is_valid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) {
					// domain not found in DNS
					$is_valid = false;
				}
			}
			if ( !$is_valid ) {
				form::$error .= "<li>You have entered an invalid email address.</li>";
			}
		
		endif;
		
		return $is_valid;

	}
	
	
	public function validate_email($email='') {
		return self::is_valid_email($email);
	}
	/* **************************************************************
	* END
	************************************************************** */
	
	
	
	
	
	/*
	*
	* Validate the phone number format
	*
	*/
	public function is_valid_phone( $phone_number='' ) {
		
		$pattern = '/^[\(]?(\d{0,3})[\)]?[\s]?[\-]?(\d{3})[\s]?[\-]?(\d{4})[\s]?[x]?(\d*)$/';
		if (preg_match($pattern, $phone_number, $matches)) {
			// we have a match, dump sub-patterns to $matches
			$phone_number = $matches[0]; // original number
			$area_code = $matches[1];    // 3-digit area code
			$exchange = $matches[2];     // 3-digit exchange
			$number = $matches[3];       // 4-digit number
			$extension = $matches[4];    // extension
		}
	}
}
?>