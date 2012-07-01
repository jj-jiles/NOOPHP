<?php
/* ************************************************************************************************
*
* START ::: 
* 	These are all additional methods that are used for processing form elements.
* 	in order to better separate some of the code, we are pulling those methods away
* 	from the elements methods and placing them in the Forms_Methods class
*
************************************************************************************************ */
class Forms_Methods {
	
	
	public function __construct() {}
	
	
	/* **********************************************************************
	* START :::
	* test to verify a given value, in the specified DB/table is unique
	*
	* Required:
	*	$field		: the name of $_POST object (ie. form::$fieldname)
	*				  which we need to lookup. should match both the
	*				  table's field name and $_POST object
	*	$table		: the table to use for the lookup
	*
	*	Optional:
	*		$db_name 	: you can specify a DB name if the table is not
	*				  in the current DB
	*
	********************************************************************** */
	function isItUnique( $params ) {
	
		$db_name = '';
		
		foreach ($params as $key=>$val) :
			// prevent reserved words from being used
			if ($key == 'db' || $key == 'database') :
				$db_name = $val;
			else :
				$$key = $val;
			endif;
		endforeach;		
		
		$db_field_name = str_replace( array(form::$required_indicator,form::$excluded_indicator), '', $field);
		
		$field_value = form::$$field;
		
		$sql = "SELECT $db_field_name FROM $db_name.$table WHERE $db_field_name = '$field_value'";
		$sql = db::query($sql);
		
		$unique = (db::num_rows() > 0) ? false : true;
		return $unique;
	}
	/* **********************************************************************
	  * END :::
	********************************************************************** */
	
	
	
	
	
	
	
	
	/* **********************************************************************
	* START :::
	* 	echos the form error to screen
	********************************************************************** */
	function echo_error() {
		
		if ( !empty(form::$message) ) :
			echo '<div id="form-message">' . form::$message . '</div>';
		endif;
		
		if ( !empty(form::$error) ) :
			form::$error = '<ul id="form-error">'
						. '<li class="form-error-header">The following problems were found:</li>'
						. form::$error
						. '</ul>';
			echo form::$error;
		endif;
	}
	/* **********************************************************************
	  * END :::
	********************************************************************** */
	
	
	function echo_message() {
		self::echo_error();
	}
	
	
	
	
	
	
	
	/* **********************************************************************
	* START :::
	* returns true if the form has any errors such as missing required fields
	********************************************************************** */
	function hasError() {
		if ( empty(form::$error) ) :
			return false;
		endif;
		return true;
	}
	/* **********************************************************************
	  * END :::
	********************************************************************** */
	
	
	
	
	
	
	
	
	/* **********************************************************************
	* START :::
	* returns true if the $_POST array is set
	********************************************************************** */
	function hasPost() {
		if (count($_POST) > 0 ) :
			return true;
		endif;
		
		return false;
	}
	/* **********************************************************************
	  * END :::
	********************************************************************** */
	
	
	
	
	
	
	
	
	/* **********************************************************************
	* START :::
	* Validates form submission.
	* 
	* Required fields should have the form::$included_indicator at the
	* beginning of their names.
	* 
	* Fields to be excluded should have form::$excluded_indicator at
	* the beginning
	********************************************************************** */
	function validate() {
		
		form::$loop_count=0;
		unset($_POST['x'], $_POST['y']);
		
		$is_spam = false;
		$incl = form::$required_indicator;
		$excl = form::$excluded_indicator;
		
		form::$field_count = count($_POST);
		$is_valid = true;
		
		# Loop through the $_POST array
		foreach ($_POST as $key=>$val) {
				
			$excluded = (bool) strchr($key,$excl);
			
			if (!@$excluded) :
				
				$post['key'] = $key;
				$empty_array = (is_array($val)) ? implode('',$val) : $val;
				$val         = (is_array($val)) ? implode(', ',$val) : $val;
				$post['val'] = $val;
					
				
				# if a required field is left blank, create an error list
				# append to the existing list so we can output error
				if ( strchr($post['key'], $incl) && ( empty($post['val']) || empty($empty_array) ) ) :
					$error_key    = str_replace(array('incl_','_id','_'),array('','',' '), $post['key']);
					$error_key    = ucwords($error_key);
					form::$error .= '<li>'.$error_key.' is required</li>';
					$is_valid     = false;
				endif;
				
			endif;
			
			$name_is_spam  = (bool) ($key == '_ex_enter_your_name_here_' && !empty($val));
			$email_is_spam = (bool) ($key == '_ex_enter_your_email_here_' && !empty($val));
			
			$is_spam = (bool) (@$name_is_spam || @$email_is_spam);
				
			if ( @$is_spam ) :
				form::$error = '<li>Things don\'t look right</li>';
				$is_valid = false;
				break;
			endif;
				
		}
		
		//if (empty(form::$error)) :
			$this->create_variables();
		//endif;
		
		return $is_valid;
		
	}
	/* **********************************************************************
	  * END :::
	********************************************************************** */
	
	
	
	
	
	
	
	
	/* ******************************************************
	* START :::
	* loops thru $_POST array and creates $form objects
	* based on the $_POST array
	* 	$_POST['user_id']
	* 	$_POST['first_name']
	* would yield
	* 	form::$user_id
	* 	form::$first_name
	****************************************************** */
	function create_variables() {
		
		foreach ($_POST as $key=>$val) {
			if (!is_array($val) && !empty($val)) {
				$post_array[$key] = isset(db::$mysqlLink) ? mysql_real_escape_string($val) : $val;
			} else {
				$key = str_replace('[]','',$key);
				$post_array[$key] = $val;
			}
			form::$posts = (object) $post_array;
		}
	}
	/* ******************************************************
	  * END :::
	****************************************************** */
	
	
	
	
	
	
	
	
	/* ******************************************************
	* START :::
	* 	Will automatically create the Form variables based
	* 	on SQL results
	* 	
	*	Will use the SQL result set you pass to it and
	*	automcatically create $form objects from the results
	*
	*	Example:
	*		$sql = "SELECT id, name, email FROM contacts";
	*		form::$setResults($sql);
	*
	*		// will create the following variables
	*			form::$id;
	*			form::$name;
	*			form::$email;
	****************************************************** */
	function setResults($sql) {
		
		$i=0;
		$ret = array();
		$sql = $db->query($sql);
		while ($row = mysql_fetch_assoc( $sql )) {
			foreach ($row as $key=>$val) {
				$post_array[$key] = $val;
			}
		}
		form::$posts = (object) $post_array;
	}
	/* ******************************************************
	  * END :::
	****************************************************** */
	
	
	
	
	
	
	
	
	/* ******************************************************
	* START :::
	* 	Will automatically create the Form variables based
	* 	on SQL results
	* 	
	*	Will use the SQL result set you pass to it and
	*	automcatically create $form objects from the results
	*
	*	Example:
	*		$sql = "SELECT id, name, email FROM contacts";
	*		form::$setResults($sql);
	*
	*		// will create the following variables
	*			form::$id;
	*			form::$name;
	*			form::$email;
	****************************************************** */
	function set_results_from_array($arr) {
		
		foreach ($arr as $key=>$val) :
			if ( !strstr($key,'password') ) :
				$post_array[$key] = $val;
			endif;
		endforeach;
		
		form::$posts = (object) $post_array;
		
	}
	/* ******************************************************
	  * END :::
	****************************************************** */
	
	
	
	
	
	
	
	
	function email() {
		
		if ( !empty(form::$email_template) && is_file(form::$email_template) ) :
			ob_start();
				include(form::$email_template);
				$file_contents = ob_get_contents();
			ob_end_clean();
			$this->body = form::$file_contents;
		endif;
		
	}
	
	
	
	
	
	function clean($text_to_clean) {
		return addslashes(filter_var($text_to_clean, FILTER_SANITIZE_STRING));
	}
	
	
}
/* ************************************************************************************************
* END :::
************************************************************************************************ */
?>