<?

class app extends app_extends {
	
	var $alert_message;
	var $page_title;	
	
	function __construct() {
		parent::__construct();
	}
	
	## ##
	##
	## Returns the Page Title of the page request
	##
	## ##
	static public function page_title() {
		$page_title = ucwords(config::$app->title);
		$page_title .= empty(config::$app->meta_title) ? '' : ' | ' . config::$app->meta_title;
		return $page_title;
	}
	## ##
	##
	## end
	##
	## ##
	
	
	
	
	
	
	
	
	## ##
	##
	## Echos the Page Title of the page request
	##
	## ##
	static public function echo_page_title() {
		
		$page_title = self::page_title();
		$page_title .= ( !empty(app::$page_title) ) ? ' | ' . ucwords(app::$page_title) : '';
		
		echo $page_title;
	}
	## ##
	##
	## end
	##
	## ##
	
	
	
	
	
	
	
	
	## ##
	##
	## Outputs a static alert message that can be used as a modal, if styled in CSS
	##
	## ##
	static public function echo_meta_description() {
		echo config::$app->meta_description;
	}
	## ##
	##
	## end
	##
	## ##
	
	
	
	
	
	
	
	
	## ##
	##
	## Outputs a static alert message that can be used as a modal, if styled in CSS
	##
	## ##
	static public function echo_meta_keywords() {
		echo config::$app->meta_keywords;
	}
	## ##
	##
	## end
	##
	## ##
	
	
	
	
	
	
	
	
	## ##
	##
	## Outputs a static alert message that can be used as a modal, if styled in CSS
	##
	## ##
	static public function echo_alert() {
		if ( !empty($this->alert_message) ) :
			echo '<div class="alert-message">';
			echo $this->alert_message;
			echo '</div>';
		endif;
	}
	## ##
	##
	## end
	##
	## ##
	
	
	
	
	
	
	
	
	## ##
	##
	## Outputs Open Graph meta data if Open Graph infor is required
	##
	## ##
	static public function echo_opengraph() {
		
		if ( isset(config::$app->fb_opengraph_id) && !empty(config::$app->fb_opengraph_id) ) {
			
			$image_url      = '';
			$the_url        = url::root();
			$app_page_title = $this->page_title();
			$page_title     = $app_page_title;
			
			$is_image = ( request::component() == 'i' ) ? true : false;
			
			if ( @$is_image ) : 		
				$the_url    .= '/' . $this->urd;
				$image_url   = '<meta property="og:image" content="' . $img->image_url . '" />';
				$page_title  = $img->title;
				$page_title .= ' | ' . str_replace(' | ' . config::$app->meta_title, '', $app_page_title) . ' [Pic]';
			endif;
			
			echo '<meta property="og:title" content="' . $page_title . '" />'
				. '<meta property="og:type" content="website" />'
				. '<meta property="og:url" content="' . $the_url . '" />'
				. $image_url
				. '<meta property="og:site_name" content="' . config::$app->title . '" />'
				. '<meta property="fb:app_id" content="' . config::$app->fb_opengraph_id . '" />'
				. '<meta property="og:description" content="'. config::$app->title . ' ::: ' . config::$app->meta_description . '"/>';
		}
	}
	## ##
	##
	## end
	##
	## ##
	
	
	
	
	
	
	
	
	## ##
	##
	## Will echo a class="active" for a URL if user is viewing a specific component
	##
	## ##
	static public function header_link_class($x) {
		if ($x == request::component()) :
			echo ' class="active"';
		endif;
	}
	## ##
	##
	## end
	##
	## ##
	
	
	
	
	
	
	
	
	## ##
	##
	## Will 64bit encode an ID and return only [a-bA-b][0-9]
	##
	## ##
	static public function encode_id($id) {

		$id = base64_encode($id);
		$id = str_replace(
		array('+','/','='),
		array('-','_',''),
		$id
		);
		return $id;
	}
	## ##
	##
	## end
	##
	## ##
	
	
	
	
	
	
	
	
	## ##
	##
	## Includes the header when needed
	##
	## ##
	static public function post_values_are_valid() {
		
		$return = true;
		
		/* ***
		*
		* Evaluate the POST array */
		if ( count($_POST) > 0 ) :
			foreach ($_POST as $key=>$val) :
				$has_html  = false;
				$is_array  = false;
				$has_spam  = false;
				$more_spam = false;
				
				$is_array = (bool) is_array($_POST[$key]);
				
				if ( !$is_array ) :
					
					# check for the typical URL spamming
					$has_spam 	= (bool) strchr($val, "url=");
					$more_spam 	= (bool) strchr($val, "[url");
					
					# check for the any kind of html if specified to do so
					if ( !$security->settings['allow_html_in_posts'] ) :
						$has_html = preg_match("/(\<(\/?[^\>]+)\>)/i", $val, $match);
					endif;
				
					if ( @$has_spam || @$more_spam || $has_html ) :
						$return = false;
					endif;
					
				endif;
			endforeach;
		endif;
		
		return $return;
		/* *** */
	}
	
}
$app = new app();
?>