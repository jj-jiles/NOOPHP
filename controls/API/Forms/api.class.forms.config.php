<?php
	//
	// specify what required/excluded element names will start with
	// EX: required: <input type="type" name="incl_fieldName" id="fieldName" value="" />
	// EX: excluded: <input type="type" name="_ex_fieldName" id="fieldName" value="" />
	self::$required_indicator = 'incl_';
	self::$excluded_indicator = '_ex_';
	
	//
	// this is a common template used to build an auto-response email
	// use the full absolute path to the template
	self::$email_template = '';
	
	// default classes
	// used unless specified during method call
	self::$classes = array(
		'text'     => 'input',
		'password' => 'password',
		'hidden'   => 'input',
		'submit'   => 'submit',
		'reset'    => 'reset',
		'button'   => 'button',
		'image'    => 'image',
		'textarea' => 'textarea',
		'select'   => 'select',
		'radio'    => 'radio',
		'checkbox' => 'checkbox',
		'file'     => 'input'
	);
	
	// list of attributes you do not want to have added to an element
	self::$attributes_not_allowed = array(
		'type',
		'default',
		'checked',
		'selected',
		'br',
		'is_array',
		'static'
	);
	
	// reserved items specifically for the db calls.
	self::$reserved = array(
		#
		# indicate, in an array, what you want to strip from a post name
		# when the post name is displayed to user during times such as
		# errors and/or emails. the replace_what and replace_with must be in the same order
		'replace_what'			=> array( self::$required_indicator, '_', '-' ),  // the excluded/included indicators are automatically added to this list
		'replace_with'			=> array( '', '', '', ' ', ' '),
		
		#
		# used to specify if you have field that should be unique in value
		# ex: once a user id is used, no one can use it
		'unique_column_name'	=> array(),
		
		#
		# if the database table contains a specific "created on" date field
		# this is the field name
		'created_date_column'	=> array(),
		
		#
		# this is name of the field to indicate the form submitted is the only form
		# you want to have automatically included in the "insert" statement
		'customFormIndicator'	=> 'custom_form'
	);
	self::$value_exclude = false;
?>