<?php
/* ****************************************************************************
  * START :::
  *
  * You can add aditional format functionality to the Forms class if you wish
  * This allows you to pass a 'format' attribute with the name of the method
  * and have the element's value formatted according to your custom method.
  *
  * An example below is for phone numbers. To use, simply do the following
  * 		$form->input('field_name',array('value'=>'9185551212','format'=>'phone'));
  *
  *		input value formatted to '(918) 555-1212'
  *
  * This looks for the "phone" method, passes the value to the method and
  * the phone method returns the formatted value
  * 
**************************************************************************** */
	
	class Forms_Format {
		
		
		public function __construct() {}
		
		
		/* ***********************************************************
		*
		* START :::
		*	formats phone number according to length of number
		* 	7 digits formatted to 555-1212
		* 	10 digits formatted to (555) 555-1212
		*
		*********************************************************** */
		
		function phone($value='') {
			$value = preg_replace("/[^0-9]/", "", $value);
			
			if(strlen($value) == 7) :
				$value = preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $value);
			
			elseif(strlen($value) == 10) :
				$value = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $value);
				
			endif;
			
			return $value;
		}
		
		/* ***********************************************************
		* END :::
		*********************************************************** */
	
	
	
	
	
	
	
	
	
		/* **********************************************************************
		* START :::
		*	Strips unwanted characters
		*	Replaces single and double quotes with HTML equivalents
		********************************************************************** */
			function strChars( $value ) {
				global $form;
				
				$bad = array('&quot;','&lsquot;');
				$good = array('"',"'");
				
				if (!is_array($value)) :
					$value = stripslashes($value);
					$value = str_replace($bad,$good,$value);
				endif;
				return $value;
			}
		/* **********************************************************************
		  * END :::
		********************************************************************** */
		
		
		
		
	}
	
/* ****************************************************************************
* END :::
**************************************************************************** */
?>