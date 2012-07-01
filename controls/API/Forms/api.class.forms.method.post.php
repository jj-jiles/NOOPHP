<?php
/* *******************************************************************************************
  * START :::
  * 		This class has two methods
  * 		self(): used in the form tag's "action" attrib. Will get the URL of the form
  * 			so the form posts back to itself
  *
  * 		referer(): returns the full URL of the referring page
  *
******************************************************************************************* */
class Forms_Post {

	/* ***********************************************************
	* START :::
	*	Returns a full URL for the referer
	*	Differs from other methods because ensures the full
	*	domain, path and query string are included
	*********************************************************** */
	function self() {
	    /*** check for https ***/
	    $protocol = (isset($_SERVER['HTTPS'])) ? 'https' : 'http';
	    /*** return the full address ***/
	    return $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	/* ***********************************************************
	* END :::
	*********************************************************** */

	/* ***********************************************************
	* START :::
	*	Returns a full URL for the referer
	*	Differs from other methods because ensures the full
	*	domain, path and query string are included
	*********************************************************** */
	function referer($set=false) {
	    /*** check for https ***/
	    $protocol = (isset($_SERVER['HTTPS'])) ? 'https' : 'http';
	    /*** return the full address ***/
	    $url = $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

		// Are setting this to a variable, or echoing it?
		if (!$set) :
			echo $url;
		else :
			return $url;
		endif;
	}
	/* ***********************************************************
	* END :::
	*********************************************************** */

}
/* *******************************************************************************************
* END :::
******************************************************************************************* */
?>
