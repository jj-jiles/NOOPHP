<?php
/* **********************************************************************
*	Framework_Database is a class created to manage database functions
*
*
*	The class manages all database interraction for the purpose of allowing certain things to be
*	turned on and off, as well as making development easier and more uniform.  
*   An example would be Error Reporting.  Once the application is on the live
*	server, we can set $config->errors['display'] to false. Error reporting for database errors will not be shown.
*
*	This is real simple to use
*
*
*
*
******************************
**
**
**
**
**	Configuration ***
**		Scroll to the __construct method if you need to change which object
**		the database class uses for $config->connection and $config->errors
**
**
**
**
******************************
*
*	Examples:
*
********************
*
*		Get number of rows returned in a query (you can also nest this:
*			$sql = "SELECT fieldA, fieldB FROM tableA"; // would return 4 rows
*			$sql = $db->query($sql);
*			$rows = $db->num_rows();
*			echo $rows; // 4
*		
*		Nested num_rows();
*			$a = "SELECT fieldA FROM tableA"; // would return 2 rows
*			$a = $db->query($a);
*			$a = $db->fetch_object($a);
*			$a_rows = $db->num_rows();
*			echo $a_rows; // 2
*
*			while ($a_row = $db->fetch_object($a) ) :
*				$b = "SELECT fieldA FROM tableA WHERE fieldA = {$a_row->fieldA}"; // would return 6 rows
*				$b = $db->fetch_object($b);
*				$b_rows = $db->num_rows();
*					echo $b_rows; // 6
*				while ( $b_row = $db->fetch_object($b)) :
*					echo $b_row->fieldA;
*				endwhile;
*			endwhile;
*
*
********************
*
*
*		Results as Object:
*			$sql = "SELECT fieldA, fieldB FROM tableA";
*			$sql = $db->query($sql);
*
*			while ($row = $db->fetch_object($sql) ) :
*				echo $row->fieldA . ': ' . $row->fieldB;
*			endwhile;
*
*
********************
*
*
*		Results as Array:
*			$sql = "SELECT fieldA, fieldB FROM tableA";
*			$sql = $db->query($sql);
*
*			while ($row = $db->fetch_array($sql) ) :
*				echo $row['fieldA'] . ': ' . $row['fieldB'];
*			endwhile;
*
*
********************
*
*
*		Return value of a row or field in a row 
*		( uses the mysql_result() method rather than the more inefficient mysql_fetch_row() )
*
*		Specify the field's position in the Result Set
*			$sql = "SELECT fieldA, fieldB FROM tableA";
*			$val = $db->fetch_row(1);        // would return fieldB's value
*
*		Specify the field name specifically
*			$val = $db->fetch_row('fieldA'); // would obviously return fieldA's value
*
*
********************
*
*
*		Return value of a query more efficiently (avoids the inefficient mysql_fetch_row() function)
*			$sql = "SELECT fieldA FROM tableA";
*			$fieldA = $db->fetch_value($sql); // would return fieldA's value
*
*
********************
*
*
*		 Return the query result set as an array:
*			$sql = "SELECT fieldA, fieldB FROM tableA"; // returns 4 rows
*			$results = $db->to_array($sql);
*
*			$results[0]['fieldA'] = 'valueA 1';
*			$results[0]['fieldB'] = 'valueB 1';
*
*			$results[1]['fieldA'] = 'valueA 2';
*			$results[1]['fieldB'] = 'valueB 2';
*
*			$results[2]['fieldA'] = 'valueA 3';
*			$results[2]['fieldB'] = 'valueB 3';
*
*			$results[3]['fieldA'] = 'valueA 4';
*			$results[3]['fieldB'] = 'valueB 4';
*
* 		Example with foreach loop:
*			foreach ($results as $key=>$result) :
*				echo $result['fieldA'].'<br />'.$results['fieldB'];
*			endforeach;
*
*
********************************************************************** */

class db {
	
	# set whether errors should be displayed or not
	# probably want to turn off once site is live
	private static $connect_type  = 'mysql';
	private static $sql_txt       = '';
	private static $config;
  	
	private static $instance = false;
	private static $db_link = false;
	private static $result;
	private static $error = '';
	private static $db_selected = false;
	
	
	
	
	
	
	
	
	/* ****************************************************************
	*
	* __construct
	*
	**************************************************************** */
	function __construct() { 
		return true;
	}
	/* ****************************************************************
	* end :::
	**************************************************************** */
	
	
	
	
	
		
	/* ****************************************************************
	*
	* Determines if a DB has been established in the config object
	* 
	**************************************************************** */
	static public function is_specified() {
		
		$has_db_host = (bool) ( isset(config::$db->host) && !empty(config::$db->host) );
		$has_db_name = (bool) ( isset(config::$db->db_name) && !empty(config::$db->db_name) );
		$has_db_user = (bool) ( isset(config::$db->user) && !empty(config::$db->user) );
		$has_db_table = (bool) ( isset(config::$db->config_lookup_table) && !empty( config::$db->config_lookup_table ) );
		
		return (bool) ( @$has_db_host && @$has_db_name && @$has_db_user && @$has_db_table );
	}
	/* ****************************************************************
	* end :::
	**************************************************************** */
	
	
	
	
	
	
	
		
	/* ****************************************************************
	*
	* Establish connection to the database
	* Be sure to set the connection info above
	*
	**************************************************************** */
	static public function connect() {
	
		if ( !empty(config::$db->db_name) && !empty( config::$db->config_lookup_table ) ) {
			
			#
			# try the connection
			self::$db_link = mysql_connect(config::$db->host, config::$db->user, config::$db->password) or ('Error connecting to the database: '.debug_backtrace());
	
			# The connection was successful, select the database
			if ( @self::$db_link ) {
				self::$db_selected = mysql_select_db(config::$db->db_name);
			
			}
		}
		
		return self::$db_selected;
	}
	/* ****************************************************************
	* end :::
	**************************************************************** */
	
	
	
	
	
	
	
		
	/* ****************************************************************
	*
	* Generates a random ID for the $sql object.
	* This allows nested query output
	* 
	**************************************************************** */
	static private function sqlId() {
		$id = rand();
		$uniqueId[] = 'sql' . $id;
		$uniqueId[] = 'row' . $id;
		return $uniqueId;
	}
	/* ****************************************************************
	* end :::
	**************************************************************** */
	
	
	
	
	
	
	
		
	/* ****************************************************************
	*
	* Error
	* If @config::$errors['display'], print errors to screen
	* Else, we format the SQL and SQL error and email to
	*	config::$errors['email_to']
	* 
	**************************************************************** */
	static public function error() {
		
		$display_error    = (bool) ( isset(config::$errors->display) && @config::$errors->display );
		$has_error        = (bool) ( isset(db::$error) && !empty(self::$error) );
		
		if ( @$display_error && @$has_error ) :
			echo self::$error;
		
		else :
		
			# ####
			#
			# Error has occurred
			# Send an email if Display errors is turned off
			#
			if ( @$has_error ) :
			
				$site_domain = preg_replace("/^(.*\.)?([^.]*\..*)$/", "$2", $_SERVER['HTTP_HOST']);
				# ####
				#
				# An error has occurred
				# Format the error email message so we can send it
				$vars['to']['email']   = config::$errors->email_to;
				$vars['from']['email'] = 'no-reply@'.$site_domain;
				
				$vars['subject'] = 'Database Error Occurred ' . date('m-d-Y H:i:s');
				
				$sql_txt = nl2br(self::$format_sql(self::$sql_txt));
				$sql_txt = str_replace("\t",'&nbsp;&nbsp;', $sql_txt);
				
				$sql_err = nl2br(self::$format_sql(self::$error));
				$sql_err = str_replace("\t",'&nbsp;&nbsp;', $sql_err);
				
				$vars['message'] = "<p><strong>SQL Statement:</strong><br />"
								 . $sql_txt
								 . "</p>"
								 . "<p><strong>Error Text:</strong><br />" 
								 . $sql_err
								 . "</p>";
				# END
				#
				# ####
				
				
				
							
				# ####
				#
				# Put together the email and send it
				$boundary = md5( uniqid ( rand() ) );
			
			
				$to_name    = (isset($vars['to']['name'])) ? $vars['to']['name'] : $vars['to']['email'];
				$to_email   = $vars['to']['email'];
				$from_name  = (isset($vars['from']['name'])) ? $vars['from']['name'] : $vars['from']['email'];
				$from_email = $vars['from']['email'];
				$replyto    = isset($vars['replyto']) ? $vars['replyto'] : $vars['from']['email'];
				$subject    = $vars['subject'];
				$message    = $vars['message'];
				
				$headers  = "From: ".$from_email."\n";
				if (!empty($replyto)) :
					$headers  .= "Reply-To: ".$replyto."\n";
				endif;
				$headers .= "Received: \"".$to_name."\" <".$to_email.">\n";
				$headers .= "MIME-Version: 1.0\n";
				$headers .= "Subject: ".$subject."\n";
				$headers .= "Content-Type: multipart/related;";
				$headers .= "boundary=\"------------".$boundary."\"\n";
				$headers .= "This is a multi-part message in MIME format.\n";
				$headers .= "--------------".$boundary."\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\n";
				$headers .= "Content-Transfer-Encoding: 7bit";
				
				mail($to_email, $subject, $message, $headers);
				# END
				#
				# ####
			endif;
			
		endif;
		self::$error='';
	}
	/* ****************************************************************
	* end :::
	**************************************************************** */
	
	
	
	
	
	
	
		
	/* ****************************************************************
	*
	* Formats the SQL Statement. Hand for screen print
	* Mainly used for the error email, to make it more readable
	*
	**************************************************************** */
	static public function format_sql( $sql ) {
		
		$reserved_words = array(
			"/SELECT/",
			"/FROM/",
			"/WHERE/",
			"/CONCAT/",
			"/INNER /",
			"/ JOIN/",
			"/LEFT /",
			"/RIGHT /",
			"/MATCH/",
			"/AGAINST/",
			"/AND/",
			"/ORDER BY/",
			"/ OR /",
			"/ASC/",
			"/DESC/",
			"/LIMIT/",
			"/ IN /",
			"/SUM/",
			"/GROUP BY/",
			"/GROUP_/",
			"/SEPARATOR/"
		);
		
		$sql_err = '';
		$sql_txt = $sql;
		if ( strstr($sql, 'Query: ') ) :
			$sql = explode('Query: ', $sql);
			$sql_err = $sql[0];
			$sql_txt = $sql[1];
		endif;
		
		$sql_txt = preg_replace($reserved_words, "<span class=\"reserved_word\" style=\"color: #3f97c6!important; font-weight: bold!important;\">$0</span>", $sql_txt);
		return $sql_err.$sql_txt;
	}
	/* ****************************************************************
	* end :::
	**************************************************************** */
	
	
	
	
	
	
	
		
	/* ****************************************************************
	*
	* Run the query
	* 
	**************************************************************** */
	static public function query( $sql='' ) {
		
		## keep the string value of the SQL statement in case of error
		self::$sql_txt = $sql;
		
		if ( self::$connect_type == 'mysql' ) :
			self::$result=mysql_query( $sql );
		
			if ( !self::$result ) :
				self::$error = mysql_error() . '<br />Query: '.$sql;
				self::error();
				self::$result = false;
			endif;
		
		else :
			$result = mysqli_query( self::$db_link, $sql );
			self::$result = $result;
		
			if ( !self::$result ) :
				self::$error = mysqli_error(self::$db_link) . '<br />Query: '.$sql;
				self::error();
				self::$result = false;
			endif;
		
		endif;
		
		return self::$result;
	}
	/* ****************************************************************
	* end :::
	**************************************************************** */
	
	
	
	
	
	
	
		
	/* ****************************************************************
	*
	* Connect to the server using the mysqli extension
	* This is used automatically during multi_query()
	* 
	**************************************************************** */
	static public function mySqliConnect() {
		self::$mySqli = new mysqli(config::$db->host, config::$db->user, config::$db->password, config::$db->db_name);
		return self::$mySqli;
	}
	/* ****************************************************************
	* end :::
	**************************************************************** */
	
	
	
	
	
	
	
		
	/* ****************************************************************
	*
	* Close the mysqli connection once we have finished
	* using the multi_query() method
	* 
	**************************************************************** */
	static public function mySqliClose() {
		mysqli_close(self::$mySqli);
	}
	/* ****************************************************************
	* end :::
	**************************************************************** */
	
	
	
	
	
	
	
		
	/* ****************************************************************
	*
	* Execture the multi-query
	* 
	**************************************************************** */
	static public function multi_query( $sql='' ) {
		
		## keep the string value of the SQL statement in case of error
		self::$sql_txt = $sql;

		$mysqli = self::mySqliConnect();

		self::$result = mysqli_multi_query( $mysqli, $sql );
		
		if ( !self::$result ) :
			self::$error = mysql_error() . '<br />Query: '.$sql;
			self::error();		
		else :		
			self::mySqliClose();
			return self::$result;		
		endif;
	}
	/* ****************************************************************
	* end :::
	**************************************************************** */
	
	
	
	
	
	
	
		
	/* ****************************************************************
	*
	* Returns the results of the query as an object
	*
	* Example:
	*	$sql = "SELECT fieldA, fieldB FROM tableA";
	*	$sql = $db->query($sql);
	*
	*	while ($row = $db->fetch_object($sql) ) :
	*		echo $row->fieldA . ': ' . $row->fieldB;
	*	endwhile;
	* 
	**************************************************************** */
	static public function fetch_object( $sql ) {
		if ( @self::$result ) :
			$uniqueIdentifier=rand();
			if ( self::$connect_type == 'mysql' ) :
				$$uniqueIdentifier=mysql_fetch_object( $sql );
			else :
				$$uniqueIdentifier=mysql_fetch_object( $sql );
			endif;				
			return $$uniqueIdentifier;
			unset( $$uniqueIdentifier );
		endif;
	}
	/* ****************************************************************
	* end :::
	**************************************************************** */
	
	
	
	
	
	
	
		
	/* ****************************************************************
	*
	* Returns the results of the query as an array
	*
	* Example:
	*	$sql = "SELECT fieldA, fieldB FROM tableA";
	*	$sql = $db->query($sql);
	*
	*	while ($row = $db->fetch_object($sql) ) :
	*		echo $row['fieldA'] . ': ' . $row['fieldB'];
	*	endwhile;
	* 
	**************************************************************** */
	static public function fetch_array( $sql ) {
		if ( @self::$result ) :
			$uniqueIdentifier=rand();
			$$uniqueIdentifier=mysql_fetch_array( $sql );
			return $$uniqueIdentifier;
			unset( $$uniqueIdentifier );
		endif;
	}
	/* ****************************************************************
	* end :::
	**************************************************************** */
	
	
	
	
	
	
	
		
	/* ****************************************************************
	*
	* Returns the number of rows returned in a query
	* 
	**************************************************************** */
	static public function num_rows($result='') {
		if ( @self::$result ) :
			$uniqueIdentifier=rand();
			if (empty($result)) $result = self::$result;
			$$uniqueIdentifier=mysql_num_rows( $result );
			return $$uniqueIdentifier;
			unset( $$uniqueIdentifier );
		else :
			return 0;
		endif;
	}
	/* ****************************************************************
	* end :::
	**************************************************************** */
	
	
	
	
	
	
	
		
	/* ****************************************************************
	*
	* Returns the number of rows affected by a query
	* 
	**************************************************************** */
	static public function rows_affected($result='') {
		if ( @self::$result ) :
			$uniqueIdentifier=rand();
			if (empty($result)) $result = self::$result;
			$$uniqueIdentifier = mysql_rows_affected( $result );
			return $$uniqueIdentifier;
			unset( $$uniqueIdentifier );
		else :
			return 0;
		endif;
	}
	/* ****************************************************************
	* end :::
	**************************************************************** */
	
	
	
	
	
	
	
		
	/* ****************************************************************
	*
	* Runs the mysql_result() function, but a little differently
	* 
	* if self::$fetch_row(1), it will return the second field
	* of the first row
	*
	* if self::$fetch_row('field_name'), it will return the value
	* for 'field_name' in the first row
	* 
	**************************************************************** */
	static public function fetch_row ( $fieldName ) {
		if (is_numeric( $fieldName )) {
			$fieldValue=mysql_result( self::$result, $fieldName );
		} else {
			$fieldValue=mysql_result( self::$result, 0, $fieldName );
		}
		return $fieldValue;
	}
	/* ****************************************************************
	* end :::
	**************************************************************** */
	
	
	
	
	
	
	
		
	/* ****************************************************************
	*
	* Returns the value of the specified field, in the specified
	* row.
	*
	* If no field or row is specified, it will default to first
	* row, first field
	* 
	**************************************************************** */
	static public function fetch_value ( $sql='', $fieldName='', $row='' ) {
		$i=0;
		$fieldValue = false;

		if (empty($fieldName)) { $fieldName = 0; }
		if (empty($row)) { $row = 0; }
		$ret = array();
		$sql = self::query($sql);
		if (self::num_rows() > 0) {
			while ($rec = mysql_fetch_array( $sql )) {
				for ($a=0; $a < count($rec); $a++) {
					$ret[$i] = $rec;
				}
				$i++;
			}
				
			$fieldValue=$ret[$row][$fieldName];
		}

		return ($fieldValue);
	}
	/* ****************************************************************
	* end :::
	**************************************************************** */
	
	
	
	
	
	
	
		
	/* ****************************************************************
	*
	* Returns the results of the query as an array
	*
	* Example:
	*	$sql = "SELECT fieldA, fieldB FROM tableA"; // returns 4 rows
	*	$results = $db->to_array($sql);
	*
	*	$results[0]['fieldA'] = 'valueA 1';
	*	$results[0]['fieldB'] = 'valueB 1';
	*
	*	$results[1]['fieldA'] = 'valueA 2';
	*	$results[1]['fieldB'] = 'valueB 2';
	*
	*	$results[2]['fieldA'] = 'valueA 3';
	*	$results[2]['fieldB'] = 'valueB 3';
	*
	*	$results[3]['fieldA'] = 'valueA 4';
	*	$results[3]['fieldB'] = 'valueB 4';
	*
	* Example with foreach loop:
	*	foreach ($results as $key=>$result) :
	*		echo $result['fieldA'].'<br />'.$results['fieldB'];
	*	endforeach;
	* 
	**************************************************************** */
	static public function to_array ( $sql='' ) {
		
		## keep the string value of the SQL statement in case of error
		self::$sql_txt = $sql;
		
		$i=0;
		$ret = array();
		$sql = self::query($sql);
		if ( self::$connect_type == 'mysql' ) :
			if ( $sql ) :
				while ($row = mysql_fetch_assoc( $sql )) :
					for ($a=0; $a < count($row); $a++) :
						$ret[$i] = $row;
					endfor;
					$i++;
				endwhile;
			endif;
		else :
			if ( $sql ) :
				while ($row = mysqli_fetch_assoc( $sql )) :
					for ($a=0; $a < count($row); $a++) :
						$ret[$i] = $row;
					endfor;
					$i++;
				endwhile;
			endif;
		endif;
		return ($ret);
	}
	/* ****************************************************************
	* end :::
	**************************************************************** */
	
	
	
	
	
	
	
		
	/* ****************************************************************
	*
	* closes the database connection
	*
	* PHP nowadays does a really good job of trashing these connection
	* automatically. However, just to be safe, I typically call this
	* method in an include that is loaded after everything else
	* 
	**************************************************************** */
	static public function close_db() {
		if (self::$db_link) :
			mysql_close(self::$db_link);
		endif;
	}
	/* ****************************************************************
	* end :::
	**************************************************************** */
	
	
	
	
}
/* *********************************************************************************
* END :::
********************************************************************************* */
$db = new db();
?>