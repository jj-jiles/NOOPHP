<?php
	
	# display server errors
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	
	# define this as a valid call so we can include system files
	define( '_VALID_REFERENCE','frontend');

	# include the initial config file
	include_once(dirname(__FILE__) . '/controls/start.php');

	# output the content
	# use the /views folder to build templates for each component/content
	app::the_content(); 
?>