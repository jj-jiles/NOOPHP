<?php
class AES_System extends app {
	function __construct() { return true; }
	
	function decrypt($var) {
		if ( $var ) {
			$aes_password = config::aes_password;
			return "AES_DECRYPT(BINARY(UNHEX({$var})),'{$aes_password}')";
		}
		return null;
	}
	
	function encrypt($var) {
		if ( $var ) {
			$aes_password = config::aes_password;
			return "HEX(AES_ENCRYPT('{$var}','{$aes_password}'))";
		}
		return null;
	}
}
$app->aes = new AES_System;
?>