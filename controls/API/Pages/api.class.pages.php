<?

class page {
	
	public static $details = array();
	
	function __construct() {
		
		self::$details['session_required'] = 'N';
		self::get_details();
		return true;
	}
	
	
	static public function get_details() {
				
		$sql = array();
		
		if ( db::is_specified() ) :
			$page = request::component();
			$page = empty($page) ? 'home' : $page;
			
			$sql = "SELECT id, page_title, page_description, page_url, session_required FROM pages WHERE page_url = '$page'";
			$sql = db::to_array($sql);
			if ( db::num_rows() > 0 ) :
				$sql = $sql[0];
				self::$details = $sql;
			endif;
		endif;
	}
	
	
	static public function session_required() {
		return (bool) (self::$details['session_required'] == 'Y' );
	}
	
}

$page = new page();
?>