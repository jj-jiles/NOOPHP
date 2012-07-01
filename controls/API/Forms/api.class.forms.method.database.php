<?php
/* ************************************************************************************************
*
* START :::
* 	Two Methods:
* 		insert(): called when a form submission is supposed to be INSERTed into DB
* 			this is a simple & straight forward INSERT and nothing fancy
* 			If you have required/excluded fields, use the configuration variables at the top
* 			to set those
* 			REQUIRED FIELDS: left blank will have the specific chars removed and replaced
* 				The following are removed entirely
* 				$form->required_indicator
* 				$form->excluded_indicator
* 				'-'
* 			'_' is replaced with a space. The field name is then ucwords and used as the description
* 			of the required field for the error output
* 			EXAMPLE:
* 				Field Name: incl_first_name
* 				Error Output: First Name is required
* 				
* 		sql(): used by the insert() method to build the sql fields and values. not to be called
* 			independantly
*
************************************************************************************************ */
class Forms_DB {
			
	var $sqlFields='';
	var $sqlValues='';
	var $counter=0;
	var $comma=',';
	var $fieldCount=0;
				
				
	/* ******************************************************
	* START :::
	*   Loop through the $_POST array and build the insert
	*   statement.
	*   
	*   Any required fields left blank are added to an
	*   error object
	****************************************************** */
	function insert($database='', $table='', $unique_column_name='') {
		global  $config, 
				$db, 
				$app, 
				$form;
				
		$unique_columns = empty($unique_column_name) ? $form->reserved['unique_column_name'] : $unique_column_name;
		
		unset($_POST['x'], $_POST['y']); // can't remember the purpose of this. may have to remove and see what breaks
		
		if ( is_array($database) ) :
			foreach ($database as $key=>$val) :
				$$key = $val;
			endforeach;
		endif;
		

		form::$loop_count  =0;		
		form::$field_count = count($_POST);
		
		
		if ( isset($unique_columns) && is_array( $unique_columns ) ) :
			$uni_col = array();
			foreach ($unique_columns as $column) :
				$uni_col[] = $column;
			endforeach;
			$unique_columns = $uni_col;
		
		elseif ( isset($unique_columns) && !is_array($unique_columns)) :
			$uni_col = array();
			$uni_col[] = $unique_columns;
			$unique_columns = $uni_col;
			
		endif;
		
		$unique_count = 0;
		foreach ($unique_columns as $key=>$column) :
			
			# should column value be unique?
			if ($unique_count == 0 && isset($_POST[$column]) && !empty($_POST[$column]) ) :
				
				# strip the required/exluded indicators from the column name to get to the actual DB column name
				$column_name	= str_replace($form->reserved['column_type_indicators'], '', $column);
				$val 			= $_POST[$column];
				
				# check if a record exists with that value for that column
				$sql 			= "SELECT id FROM {$database}.{$table} WHERE {$column_name} = '$val'";
				$sql 			= $db->query($sql);
				$unique_count 	= $db->num_rows();
			
				# a record was found. create the error
				if ($unique_count > 0) :
					$error_key = str_replace( array('_','-'), ' ', $column_name );
					$error_key = ucwords($error_key);
					$form->error .= '<li>'.$error_key.' already exists</li>';
				endif;
				
			endif;
		endforeach;
		
		# no unique errors were encountered
		# build the SQL statement
		if ($unique_count == 0) :
		
			# Loop through the $_POST array
			foreach ($_POST as $key=>$val) :
				
				$excluded = (bool) strchr( $key, $form->excluded_indicator );
				
				# when you have multiple forms on a page and you want to use this function
				# sparingly, create a hidden input named whatever you wish.
				# $this->reserved->customFormIndicator should equal the name of that field
				# this ignores that field in the loop and deducts it from the counter
				if ($key == $form->reserved['customFormIndicator'] || @$excluded) :
					$this->fieldCount = $this->fieldCount-1;
					unset($_POST[$key]);
					
				
				else :
					
					if ( !$excluded ) :
					
						$post['key'] = $key;
						$val = (is_array($val))?implode(', ',$val):$val;
						$post['val'] = $val;
					
						# if a required field is left blank, create an error list
						# append to the existing list so we can output error
						if ( strchr($post['key'], $form->required_indicator) && empty($post['val']) ) :
							$error_key = str_replace( $form->reserved['replace_what'], $form->reserved['replace_with'], $post['key']);
							$error_key = ucwords($error_key);
							$form->error .= '<li>'.$error_key.' is required</li>';
						
						elseif ( empty($error_key) ):
						
							$val = (is_array($val))?implode(', ',$val):$val;
							$this->sql($post); // create the SQL code
							
							# You can create an email template so that the values are automatically plugged into the content
							# as we loop, this replaces %%field_name%% with its actual value
							# you can then use that to send an email with populated values
							if (!empty($form->email->body)) :
								$replace_what = str_replace('[]','',$post['key']);
								$form->email->body = str_replace('%%'.$replace_what.'%%', $post['val'], $form->email->body);
							endif;
							
						endif;
						
					endif;
					
				endif;
				
			endforeach;
			
		endif;
		
		
		# all required fields are filled out and there are no errors
		# insert into database table
		if ( empty($form->error) ) :
		
			if ( !empty( $form->reserved['created_date_column'] ) ) :
				$tableFields = mysql_list_fields($dbName, $dbtable);
				
				$columns = mysql_num_fields($tableFields);
		  
				for ($i = 0; $i < $columns; $i++) :
				    $field_array[] = mysql_field_name($tableFields, $i);
				endfor;
				
				if (in_array($form->reserved['created_date_column'], $field_array)) :
					$this->sqlFields .= ", " . $form->reserved['created_date_column'];
					$this->sqlValues .= ', NOW()';
				endif;
			endif;
		
			# insert into database
			$sql = "INSERT INTO {$database}.{$table} (".$this->sqlFields.") VALUES (".$this->sqlValues.")";
			$sql = $db->query($sql);
			
			# get the newly created ID from table
			$sql = "SELECT id FROM {$database}.{$table} WHERE id = LAST_INSERT_ID()";
			$this->new_id = $db->fetch_value($sql);
		
		# there were errors found. build out the complete error and pass it on
		else :
			$form->error = '<ul id="form-error">'.$form->error.'</ul>';
		endif;
	}
	
		// simply puts together the SQL fields and values for the INSERT statement
		// in the insert() method
		function sql($post) {
			global $app;
			
			$tableColumn = str_replace("incl_", "", $post['key']);
			$formField = $post['key'];
			
			# Lets setup the Field list for the Insert Statement
			$this->comma=($this->counter > 0)?',':'';
			
			# if the value is an array, split the values with a comma
			$post['val'] = (is_array($post['val']))?implode(", ", $post['val']):$post['val'];
			$post['val'] = mysql_real_escape_string($post['val']);
			
			$$formField = $post['val'];
			$this->sqlFields .= $this->comma.$tableColumn;
			$this->sqlValues .= $this->comma."'".$$formField."'";
			
			$this->counter++;
			# End Insert Build
		}
	/* ******************************************************
	* END :::
	****************************************************** */
}
/* *******************************************************************************************
* END :::
******************************************************************************************* */
?>