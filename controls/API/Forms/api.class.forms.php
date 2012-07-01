<?php
/*
	Script Name: Forms
	Author: JJ Jiles
	Website: http://jjis.me
	Version: 1
	Copyright (C) March 31 2010
	
	This function allows an easy way to create consistent forms in your applications. It was not designed
	to be an auto form generator. It is designed for assistant in consistency.
	
	What it does:
		- Allows for consistent form generation from a programming standpoint
		- Automatically cycles through the $_POST array and creates $form objects accordingly
		- Creates an easy way to generate a form, have it submitted and inserted into specific DB and table
		- Takes a SQL statement, executes it, loops through and creates the $form objects according to the returned fields and values
	
	There are instructions above each function, but here are some sample uses:
	
	Radio/Checkbox:
			$options = array('class'=>'some-class');
			$form->checkbox('field_name',array('Display Text'=>'value','Display Text 2'=>'value 2'), $options);
		Outputs:
			<input type="checkbox" name="field_name[]" id="field_name0" value="value" class="checkbox" /><label for="field_name0">Display Text</label>
			<input type="checkbox" name="field_name[]" id="field_name1" value="value 2" class="checkbox" /><label for="field_name1">Display Text 2</label>
	
	Input:
			$options = array('maxlength'=>40, 'size'=>25)
			$form->input('field_name', $options);
		Outputs:
			<input type="text" name="field_name" id="field_name" value="" maxlength="40" size="25" class="input" />
	
	Submit:
			$form->submit('submit-button', 'Submit);
			<input type="submit" name="submit-button" id="submit-button" value="Submit" class="submit" />
			$form->submit('redo', 'Clear Form');
			<input type="submit" name="redo" id="redo" value="Clear Form" class="reset" />

*/



/* ****************************************************************************
*
* START :::
*	THE Form class and methods
*
**************************************************************************** */
class form {
	
	/* ***
	* User Forms.Config.php to establish defaults for the following objects */
	
	private static $input_name;
	private static $input_value;
	private static $input_return = false;
	private static $options      = array();	
	private static $attributes_not_allowed;
	private static $reserved;
	
	public static $email_template;
	public static $value_exclude;
	public static $classes;
	public static $has_been_validated = false;
	public static $variables_created  = false;
	public static $is_valid = false;
	public static $required_indicator  = 'incl_';
	public static $excluded_indicator  = '_ex_';
	public static $loop_count = 0;
	public static $field_count = 0;
	public static $posts = array();
	public static $format;
	public static $method;
	public static $post;
	public static $database;
	public static $validate;
	public static $error = '';
	public static $values = array();
	public static $is_checked = false;
	
	
	function __construct() {
		require('api.class.forms.config.php');
		require('api.class.forms.method.formatting.php');
		require('api.class.forms.method.methods.php');
		require('api.class.forms.method.database.php');
		require('api.class.forms.method.post.php');
		require('api.class.forms.method.validate.php');
		
		
		#
		# establish the required/excluded indicators as specified by developer
		self::$reserved['column_type_indicators'] = array(self::$required_indicator, self::$excluded_indicator);
		array_merge(array(self::$required_indicator, self::$excluded_indicator, self::$excluded_indicator));
		array_merge(self::$reserved['replace_with'], array('',''));
		
		
		self::$method     = new Forms_Methods;
		self::$database   = new Forms_DB;
		self::$format     = new Forms_Format;
		self::$post       = new Forms_Post;
		self::$validate   = new Forms_Validate;
	}
	
	
	
	
	
	
	
	/* **************************************************************************************************
	* START :::
	*   $options = array();
	*   $options['value'] = 'some value'; // whatever default you want
	*   $options['class'] = 'myClass // overrides the default "input" class
	*   $options['size'] = '65' // set $options['class']='' if you prefer to use "size"
	*
	*   $form->select('field_name', $options);
	*     -- OR --
	*   $form->select('field_name', array('value'=>'some value','class'=>'myClass');
	*   
	*     // Will output:
	*     <input type="text" name="field_name" id="field_name" class="myClass" value="some value" />
	************************************************************************************************** */
	static public function input( $name, $options='' ) {
		
		self::$input_return = false;
		self::$input_name 	= $name;
		self::$options		= $options;
		self::$options		= self::returnValue();
		
		$type = 'text';
		if (is_array(self::$options) && isset(self::$options['type'])) :
			$type = self::$options['type'];
		endif;
		self::$options['type'] = $type;
		
		
		$input = "<input type=\"{$type}\" name=\"{$name}\" id=\"{$name}\"";
		$value_exclude = $type=='password'?true:false;
		$input .= self::addOptions($value_exclude);
		$input .= " />";
		
		
		if (@self::$input_return) {
			return $input;
		} else {
			echo $input;

		}
	}
	/* ***********************************************************
	  * END :::
	*********************************************************** */
	
	

			/* ***********************************************
			* START :::
			*   The following are types of inputs
			*   typically the only difference is the TYPE
			*********************************************** */
			
				# PASSWORD
				static public function password( $name, $options='') {
					self::$options			= $options;
					self::$options			= self::returnValue();
					self::$options['value'] = '';
					self::$options['type'] 	= 'password';					
					
					return self::input($name,self::$options);
				}
				
				
				# FILE
				static public function _file( $name, $options='') {
					self::$options 		= $options;
					self::$options			= self::returnValue();
					self::$options['type'] 	= 'file';					
					return self::input($name,self::$options);	
				}
				
				
				# HIDDEN
				static public function hidden( $name, $options='') {
					self::$options 		= $options;
					self::$options			= self::returnValue();
					self::$options['type'] 	= 'hidden';					
					return self::input($name,self::$options);	
				}
				
				
				# SUBMIT
				static public function submit( $name, $options='') {
					self::$options 		= $options;
					self::$options			= self::returnValue();
					self::$options['type'] 	= 'submit';
					return self::input($name,self::$options);	
				}
				
				
				# RESEST
				static public function reset( $name, $options='') {
					self::$options 		= $options;
					self::$options			= self::returnValue();
					self::$options['type'] 	='reset';
					return self::input($name,self::$options);
				}
				
				
				# BUTTON
				static public function button( $name, $options='' ) {		
					self::$options 		= $options;
					self::$options			= self::returnValue();
					self::$options['type'] 	='button';
					return self::input($name,self::$options);	
				}
				
				
				# IMAGE
				static public function image( $name, $options='' ) {
					self::$options 		= $options;
					self::$options			= self::returnValue();
					self::$options['type'] 	='image';
					return self::input($name,self::$options);	
				}
				
				
				# FILES
				static public function upload( $name, $options='' ) {
					self::$options 		= $options;
					self::$options			= self::returnValue();
					self::$options['type'] 	='file';
					return self::input($name,self::$options);	
				}
			/* ***********************************************
			*	END $form->input method
			*********************************************** */
			
	/* **************************************************************************************************
	  * END :::
	************************************************************************************************** */
	
	
	
	/* ****************************************************************************************
	* START :::
	* 	See Input directions above
	**************************************************************************************** */
	static public function textarea( $name, $options='' ) {
		
		self::$input_return = false;
		self::$options 		= $options;
		self::$input_name 	= $name;
		self::$options 		= self::returnValue();
		self::$options['type'] 	= 'textarea';
		
		$input = '<textarea name="'.$name.'" id="'.$name.'"'
				. self::addOptions($value_exclude=true)
				. '>'
				. self::$input_value
				. '</textarea>';
		
		if (@self::$input_return) {
			return $input;
		} else {
			echo $input;
		}
	}
	/* ****************************************************************************************
	* END :::
	**************************************************************************************** */
		
		
		
		
		
		
	/* ****************************************************************************************
	*
	*	$selectArray = array();
	*	$selectArray['Display Label'] = "some value";
	*	$selectArray['Display Label 2'] = "some value 2";
	*	
	*	$options = array();
	*	$options['default'] = 'some value'; // should be the value of the checkbox you want checked by default
	*	$options['return'] = false; // default - if set to true, the results will not be echoed
	*	
	*	$form->select('field_name', $selectArray, $options);
	*		-- OR --
	*	$form->select('field_name', array('Display Label'=>'some value','Display Label 2'=>'some value 2'), array('default'=>some value'));
	*	
	*	// Will output:
	*		<select name="field_name" id="field_name" class="input"><option value=""></option><option value="some value" selected>Display Label</option><option value="some value 2" >Display Label 2</option></select>
	*
	**************************************************************************************** */
	static public function select( $name, $values='', $options='' ) {
		global $app;
		self::$input_return 	= false;
		self::$input_name 		= $name;
		self::$options			= $options;
		self::$options			= self::returnValue();
		self::$values			= $values;
		self::$options['type'] 	= 'select';
		
		self::$options['default'] = isset(self::$options['default'])?self::$options['default']:'';
		
		# php requires [] trailing the input name if you want the values
		# considered an array. we strip the brackets for evaluation only
		$post_value='';
		$post_name = str_replace('[]','',$name);
		
		
		$selectField = '<select name="'.$name.'" id="'.$name.'"'
					. self::addOptions($value_exclude=true)
					. '>';
					
		$selectFirst   = (isset(self::$options['first'])) ? '<option value="">'.self::$options['first'].'</option>' : '<option value=""></option>';
					
		$selectOptions = (isset(self::$options['multiple'])) ? '' : $selectFirst;
		
		if (!empty(self::$values)) {
			foreach (self::$values as $key=>$val) :
				$count=1; $value=''; $text='';
			
				if (is_array($val)) {
					foreach ($val as $keyb=>$valb) :
						$value 	= ($count==1)?$valb:$value;
						$text 	= ($count==2)?$valb:$text;
						$count++;
					endforeach;
				} else {
					$value 	= $key;
					$text 	= $val;
				}
				
				# determine if the current select option should be selected
				$selected = ( self::$options['default'] == $value && empty($selected)) ? ' selected' : ''; // non-array eval
				$selected = ( self::selectValue(self::$input_value,$value) ) ? ' selected' : $selected; // array eval
				
				# append the <option> to $selectOptions
				$selectOptions .= '<option value="'.$value.'"'.$selected.'>'.$text.'</option>';
				$s = (!empty($selected))?false:'selected';
				
			endforeach;
		}
		
		$selectField .= $selectOptions;
		$selectField .= '</select>';
		
		if (@$s) :
			$selectField = str_replace('%%selected%%',$s,$selectField);
		endif;
		
		if (@self::$input_return) {
			return $selectField;
		} else {
			echo $selectField;
		}
	}
	
	
		/* **********************************************
		* 	Used to determine if the value is selected
		*	in the SELECT element
		********************************************** */
		static private function selectValue($list,$value) {
			if ($value == $list || (is_array($list) && in_array($value,$list)) ) {
				return true;
			} else {
				return false;
			}
		}
		/* **********************************************
		* END :::
		********************************************** */
		
	
	/* ****************************************************************************************
	* END :::
	**************************************************************************************** */
	
	
	
	
	
	
	
	
			/* **********************************************************************
			* START :::
			* Creates a SELECT element containing US states
			*	Create a table called `state_list` with fields
			* 	`state`,`state_long`
			*
			*	CREATE TABLE `state_list` (
			*		`state` char(2) NOT NULL DEFAULT '',
			*		`state_long` varchar(100) NOT NULL DEFAULT '',
			*		PRIMARY KEY (`state`)
			*	) ENGINE=MyISAM DEFAULT CHARSET=latin1;
			*
			* Beyond that, it follows the same rules as the SELECT function above
			********************************************************************** */
			static public function state_select($name, $options='') {
				global $db, $config;
				
				$option_text = isset($options['short'])?'state':'state_long';
				
				$sql = "SELECT state, {$option_text} as state_text FROM state_list ORDER BY state_long ASC";
				$states = $db->to_array($sql);
				
				$return = (isset($options['return']) && $options['return'] === true)?true:false;
				$options['return']=true;
				$states = self::select($name, $states, $options);
				
				if (@$return) {
					return $states;
				} else {
					echo $states;
				}
			}
			/* **********************************************************************
			  * END :::
			********************************************************************** */
	
	
	
	
	
	
	
	
	/* ****************************************************************************************
	*	$radioArray = array();
	*	$radioArray['Display Label'] = "some value";
	*	$radioArray['Display Label 2'] = "some value 2";
	*	
	*	$options = array();
	*	$options['br'] = '<br />' // exclusion will create inline elements
	*	$options['default'] = 'some value'; // should be the value of the checkbox you want checked by default
	*	$options['return'] = false; // default - if set to true, the results will not be echoed
	*	
	*	$form->radio('field_name', $radioArray, $options);
	*		-- OR --
	*	$form->radio('field_name', array('Display Label'=>'some value','Display Label 2'=>'some value 2'), array('br'=>'<br />','default'=>'some value'));
	*	
	*	// Will outpu:
	*		<input type="radio" name="field_name" id="field_name" class="radio" value="some value" checked /><label>Dsiplay Label</label><br />
	*		<input type="radio" name="field_name" id="field_name" class="radio" value="some value 2" /><label>Dsiplay Label 2</label><br />
	*	
	**************************************************************************************** */
	static public function radio( $name, $values="", $options="" ) {
		global $app;
		self::$input_return = false;
		self::$input_name  = $name;
		self::$options     = $options;
		self::$options     = self::returnValue();
		self::$values      = $values;
		
		$posted_values = ( isset(self::$$name) ) ? self::$$name : -1;
		$posted_values = ( isset($_POST[$name]) ) ? $_POST[$name] : $posted_values;
		$no_post       = (@self::$method->hasPost()) ? false : true;
		
		# establish some defaults
		self::$options['type'] 		= 'radio';		
		self::$options['default']	= isset(self::$options['default'])?self::$options['default']:-1;
		self::$options['class'] 		= isset(self::$options['class'])?self::$options['class']:self::$options['type'];
		self::$options['checked']	= isset(self::$options['checked'])?self::$options['checked']:'';
		self::$options['br'] 		= isset(self::$options['br'])?self::$options['br']:'';
		self::$options['is_array'] 	= isset(self::$options['is_array'])?self::$options['is_array']:false;
		
		self::$is_checked = false;
		
		$input='';
		if (!empty($values)) {
			$counter=0;
			
			
			if ( @strchr($name,'[]') ) :
				$name = str_replace('[]','',$name);
				self::$input_name = $name;
				self::$options['is_array'] = true;
			endif;
			
			
			$array_name = (@self::$options['is_array'])?'[]':'';
			$single     = ( count($values) == 1 ) ? true : false;
			
			if ( !isset(self::$options['null']) || !self::$options['null'] ) :
				$input .= "<input type=\"hidden\" name=\"{$name}{$array_name}\" id=\"{$name}_false\" value=\"\" />";
			endif;
			
			foreach ($values as $key=>$val) {
				$count=1;
				
				if (is_array($val)) {
					foreach ($val as $k=>$v) :
						$val 	= ($count==1)?$v:$val;
						$key 	= ($count==2)?$v:$key;
						$count++;
					endforeach;
				}
				
				// the checked box is specified by number in array count
				$checked = ( $counter == self::$options['default'] && @$no_post ) ? ' checked' : '';
				// form's posted. see if this checkbox is checked
				$checked = ( @self::$options['is_array'] && (is_array($val) && in_array($val, $posted_values)) ) ? ' checked' : $checked;
				// form's posted. see if this checkbox is checked
				$checked = ( (is_array($posted_values) && in_array($val, $posted_values)) ) ? ' checked' : $checked;
				// a single instance of a checkbox
				$checked = ( @self::$options['checked'] ) ? ' checked' : $checked;
				//
				$checked = ( isset(self::$posts->$name) && self::$posts->$name == $val ) ? ' checked' : $checked;
				
				$val_no_space = '_' . str_replace(' ', '_', $val);
				
				$input .= "<span class=\"radio-span\"><i class=\"el\"><input type=\"radio\" "
						. "name=\"{$name}{$array_name}\" id=\"{$name}{$val}\" "
						. "value=\"{$val}\""
						. self::addOptions($value_excluded=true)
						. $checked
						. " /></i>"
						. "<label class=\"radio\" for=\"{$name}{$val}\">{$key}</label>" . self::$options['br'] . "</span>\n";
				$counter++;
			}
		}
		
		if (@self::$input_return) {
			return $input;
		} else {
			echo $input;
		}
	}
	/* ****************************************************************************************
	  * END :::
	**************************************************************************************** */
	
	
	
	
	
	
	
	
	/* ****************************************************************************************
	*  
	*	$checkboxArray = array();
	*	$checkboxArray['Display Label'] = "some value";
	*	$checkboxArray['Display Label 2'] = "some value 2";
	*	
	*	$options = array();
	*	$options['br'] = '<br />' // exclusion will create inline elements
	*	$options['default'] = 'some value'; // should be the value of the checkbox you want checked by default
	*	$options['return'] = false; // default - if set to true, the results will not be echoed
	* 
	*	$form->checkbox('field_name', $checkArray, $options);
	*		-- OR --
	*	$form->checkbox('field_name', array('Display Label'=>'some value','Display Label 2'=>'some value 2'), array('br'=>'<br />','default'=>'some value'));
	*	
	*	// Will outpu:
	*		<input type="checkbox" name="field_name[]" id="field_name" class="checkbox" value="some value" checked /><label>Dsiplay Label</label><br />
	*		<input type="checkbox" name="field_name[]" id="field_name" class="checkbox" value="some value 2" /><label>Dsiplay Label 2</label><br />
	*	
	**************************************************************************************** */
	static public function checkbox( $name, $values="", $options="" ) {
		global $app;
		self::$input_return 		= false;
		self::$input_name 			= $name;
		self::$options				= $options;
		self::$options				= self::returnValue();
		self::$values				= $values;
		
		
		# establish some defaults
		self::$options['type'] 		= 'checkbox';		
		self::$options['default']	= isset(self::$options['default'])?self::$options['default']:-1;
		self::$options['class'] 		= isset(self::$options['class'])?self::$options['class']:self::$options['type'];
		self::$options['checked']	= isset(self::$options['checked'])?self::$options['checked']:'';
		self::$options['br'] 		= isset(self::$options['br'])?self::$options['br']:'';
		self::$options['is_array'] 	= isset(self::$options['is_array'])?self::$options['is_array']:false;
		
		
		$input='';
		if (!empty($values)) {
			$counter=0;
			
			$name .= '[]';
			$name_is_array = (bool) strchr($name,'[]');
			self::$options['is_array'] = ( !$name_is_array ) ? self::$options['is_array'] : true;
			
			$array_name = '';
			
			if ( @self::$options['is_array'] ) :
			
				$name = str_replace('[]','',$name);
				self::$input_name = $name;
				$array_name = '[]';
				
			endif;
			
			$posted_values = array();			
			$posted_values = ( isset(self::$$name) ) ? self::$$name : -1;
			$posted_values = ( isset($_POST[$name]) ) ? $_POST[$name] : $posted_values;
			$no_post       = (@self::$method->hasPost()) ? false : true;
			
			$single = ( count($values) == 1 ) ? true : false;
			
			if ( !isset(self::$options['null']) || !self::$options['null'] ) :
				$input .= "<input type=\"hidden\" name=\"{$name}[]\" id=\"{$name}_false\" value=\"\" />";
			endif;
			
			foreach ($values as $key=>$val) {
				$count=1;
				
					if (is_array($val)) {
						foreach ($val as $k=>$v) :
							$val 	= ($count==1)?$v:$val;
							$key 	= ($count==2)?$v:$key;
							$count++;
						endforeach;
					}
					
					// the checked box is specified by number in array count
					$checked = ( $counter == self::$options['default'] && @$no_post ) ? ' checked' : '';
					// form's posted. see if this checkbox is checked
					$checked = ( @self::$options['is_array'] && (is_array($val) && in_array($val, $posted_values)) ) ? ' checked' : $checked;
					// form's posted. see if this checkbox is checked
					$checked = ( (is_array($posted_values) && in_array($val, $posted_values)) ) ? ' checked' : $checked;
					// a single instance of a checkbox
					$checked = ( @self::$options['checked'] ) ? ' checked' : $checked;
					//
					$checked = ( isset(self::$posts->$name) && self::$posts->$name == $val ) ? ' checked' : $checked;
					
					$val_no_space = '_' . str_replace(' ', '_', $val);
					
					$input .= "<span class=\"checkbox-span\"><i class=\"el\"><input type=\"checkbox\" "
							. "name=\"{$name}[]\" id=\"{$name}{$val_no_space}\" "
							. "value=\"{$val}\""
							. self::addOptions($value_excluded=true)
							. $checked
							. " /></i>"
							. "<label class=\"checkbox\" for=\"{$name}{$val_no_space}\">{$key}</label>" . self::$options['br'] . "</span>\n";
					$counter++;
			}
		}
		
		if (@self::$input_return) {
			return $input;
		} else {
			echo $input;
		}
		
	}
	/* ****************************************************************************************
	* END:::
	**************************************************************************************** */
	
	
	
	
	
	
	
/* ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
*
*
*
* 	The following methods are those that do not specifically involve form elements themselves
*	These methods assist in creating and formatting the elements and their values, but
*	hold no relevance on how to use the $forms class
*
*	No instructional value for use below this line. Only for core functionality
*
*
*
:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
	
	
	
		
	/* ***********************************************************
	* START :::
	*	Simply outputs a given message with ID 'forms-message'
	*	redundancy brought this on
	*********************************************************** */
	static public function message($mess, $urgent='') {
		$urgent = empty($urgent)?'':' class="'.$urgent.'"';
		if (!empty($mess)) :
			echo '<p id="forms-message"'.$urgent.'>'.$mess.'</p>';
		endif;
	}
	/* ***********************************************************
	* END :::
	*********************************************************** */
	
	
	
	
	
	
	
	
	/* **********************************************************
	* START :::
	* 	Handles opening and closing the <form> element tag
	********************************************************** */
		
		# open tag
		static public function start($params='') {
			
			self::createVariables();
			
			$attribs="";
			
			$id="";
			$name="";
			
			# loop params and create the desired vars
			if (is_array($params)) :
				foreach ($params as $key=>$val) :
					$$key = $val;
					$attribs .= " {$key}=\"{$val}\"";				
				endforeach;
			endif;
			
			$attribs .= (isset($action))?'':' action="'.self::$post->self().'"';
			
			echo "<form{$attribs} method=\"post\">";
			echo self::input('_ex_enter_your_name_here_',array('class'=>'default-form-field'));
			echo self::input('_ex_enter_your_email_here_',array('class'=>'default-form-field'));
		}
		
		# close tag
		static public function close() {
			echo '</form >';
		}
		
	/* ***********************************************************
	* END :::
	*********************************************************** */
	
	
	
	
	
	
	
	
	/* ***********************************************************
	* START :::
	* 	method cleans up values being passed for the object's
	* 	value it checks to see if there is a POST value or a manual
	* 	$form->input_name assigned. If neither of those exist, it
	* 	will use whatever value is password in the $params array
	*********************************************************** */
	static public function cleanUpValue($strip=true) {		
	
		self::$options['static'] = isset(self::$options['static']) ? self::$options['static'] : false;
		
		$input_name = self::$input_name;
		$input_name = str_replace('[]','',$input_name);
		
		$noninclude_name = str_replace('incl_','',$input_name);
		
		$input_value = self::$input_value;
		
		if (isset($_POST[$input_name]) && !empty($_POST[$input_name]) && !self::$options['static']) :
			$input_value 	= $_POST[$input_name];
		endif;
		
		if (isset(self::$$input_name)) :
			$input_value 	= self::$$input_name;
			
		elseif ( isset(self::$$noninclude_name) ) :
			$input_value 	= self::$$noninclude_name;
		endif;
		
		if (@$strip && is_string($input_value) ) :
			$input_value = (isset(self::$options['type']) && self::$options['type'] != 'textarea') ? str_replace('"', '', $input_value) : $input_value;
			$input_value = self::$format->strChars($input_value);
		endif;
		
		self::$input_value = $input_value;
		
		return $input_value;
	}
	/* ***********************************************************
	  * END :::
	*********************************************************** */
	
	
	
	
	
	
	
	
	/* ***********************************************************
	* START :::
	* 	Adds each attribute to the element's tag
	* 	this is limitless since we loop thru the $param array
	*
	* 	If there are specific attributes that should be ignored,
	* 	add them to the $attributes_not_allowed object
	*********************************************************** */
	static private function addOptions($value_exclude=false) {
		$input='';
		
		# $options is not an array
		# it only contains the form element's value
		# create the $form->options array and assign
		# value to it and establish all other default
		if (!is_array(self::$options)) :
		
			self::$input_value 		 = self::$options;
			self::$options			 = array();
			self::$options['value']	 = self::$input_value;
			self::$options['class']  = self::$classes[self::$options['type']];
			self::$input_return 	 = false;
			
		endif;
		
		
		self::$options['static'] = isset(self::$options['static']) ? self::$options['static'] : false;
		self::$options 		= self::returnValue();
		self::$input_value  = self::$options['value'];
		
		/* **************************************************
		  * handle all value adjustments here */
		
			# if there is no class being assigned manually
			# use the defualt
			if (!isset(self::$options['class'])) :
				self::$options['class'] = self::$classes[self::$options['type']];
			endif;
			
			# so we don't have to keep passing this value
			# from function to function, set the value
			# to a class object
			if (isset(self::$options['value'])) :
				self::$input_value  = self::$options['value'];
			endif;
			
			# clean the value and determine if there
			# has been a submission or a manual override
			if (!$value_exclude) :
				self::$options['value'] = self::cleanUpValue();
			else :
				self::$options['value'] = self::cleanUpValue();
				self::$input_value 		= self::$options['value'];
			endif;
			# does the value need to be formatted using a custom method?
			if (isset(self::$options['format'])) :
				
				$func = self::$options['format'];
				$func = 'return self::$format->'.$func.'(\''.self::$input_value.'\');';
								
				self::$input_value = eval($func);
				
				self::$options['value'] = self::$input_value;
			endif;
			
			if (@$value_exclude) :
				unset(self::$options['value']);
			endif;
			
		/* ************************************************* */
		
		
		# attach all additional attributes to this element
		foreach (self::$options as $key=>$val) :
		
			if ($key == 'return') :
				self::$input_return = $val;
			
			elseif ( !in_array($key, self::$attributes_not_allowed) ) :
				$input .= " {$key}=\"{$val}\"";
			endif;
			
		endforeach;
			
		return $input;
	}
	/* ***********************************************************
	  * END :::
	*********************************************************** */
	
	
	
	
	
	
	
	
	/* ***********************************************************
	* START :::
	* 	this evaluates the $value being passed for this object
	* 	the $options object needs to be an array, but if there
	* 	are times when the value is not passed as an array
	* 	(ie. $form->input('field_name','value');)
	*
	* 	this method takes that value and adds it to the $options
	* 	object in the $value key
	*********************************************************** */
	static private function returnValue() {
		
		if (!is_array(self::$options)) :
			self::$options = array('value'=>self::$options);
			
		elseif ( !isset( self::$options['value'] ) ) :
			self::$options['value'] = '';
			
		endif;
		
		return self::$options;
	}
	/* ***********************************************************
	  * END :::
	*********************************************************** */
		
		
		
		
		
		
		
		
	
	
	
	
	/* **********************************************************************
	* START :::
	* Avoid erroring legacy calls.
	********************************************************************** */
	static public function echo_error() {
		return self::$method->echo_error();
	}
	static public function echo_message() {
		return self::$method->echo_message();
	}
	
	static public function hasError() {
		return self::$method->hasError();
	}
	
	static public function has_error() {
		return self::$method->hasError();
	}
	
	static public function hasPost() {
		return self::$method->has_post();
	}
	
	static public function has_post() {
		return self::$method->hasPost();
	}
	
	static public function validate() {
		return self::$method->validate();
	}
	
	static public function clean($text_to_clean) {
		return self::$method->clean($text_to_clean);
	}
	
	static public function createVariables() {
		return self::$method->create_variables();
	}
	
	static public function create_variables() {
		return self::$method->create_variables();
	}
	
	static public function setResults($sql) {
		return self::$method->setResults($sql);
	}
	
	static public function set_results_from_array($arr) {
		return self::$method->set_results_from_array($arr);
	}
	
	static public function strChars( $value ) {
		return self::$method->strChars( $value );
	}
	
	static public function self() {
		return self::$post->self();
	}
	
	static public function httpReferer($set=false) {
		return self::$post->referer($set=false);
	}
	
	static public function in2Db($dbName='', $dbtable='', $uniqueKey='') {
		return self::$database->insert($dbName='', $dbtable='', $uniqueKey='');
	}
	
	
	static public function is_valid_email($email='') {
		return self::$validation->is_valid_email($email);
	}
	
	
	static public function is_valid_phone($email='') {
		return self::$validation->is_valid_phone($email);
	}
	/* **********************************************************************
	  * END :::
	********************************************************************** */
}
/* ************************************************************************************************
* END :::
************************************************************************************************ */

$form = new form();
?>