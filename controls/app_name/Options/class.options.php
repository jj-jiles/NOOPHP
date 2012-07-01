<?



	
	
class App_Options {
	
	
	/* ******************************************
	*
	* Budgets Methods
	*
	*	get_budgets() - returns the list of budgets from DB
	*	get_budgets_select - return a Select element for HTML output
	*
	******** */
		function get_budgets() {
			global $db;
			
			$sql = "SELECT id, min_price, label, credits FROM options_budgets ORDER BY min_price";
			$sql = $db->to_array($sql);
			return $sql;
		
		}
		
		
		
		function get_budgets_select_list() {
			global $db;
			
			$sql = "SELECT id, label FROM options_budgets ORDER BY min_price";
			$sql = $db->to_array($sql);
			return $sql;
		}
	/* ********
	* End
	****************************************** */
	
	
	
	
	function get_categories_select_list() {
		global $db;
		
		$sql = "SELECT id, label FROM options_categories ORDER BY label";
		$sql = $db->to_array($sql);
		return $sql;	
	}
	
	
	
	
	function get_services_select_list() {
		global $db;
		
		$sql = "SELECT id, label FROM options_services ORDER BY label";
		$sql = $db->to_array($sql);
		return $sql;	
	}
	
	
	
	
	
	/* ******************************************
	*
	* Platform Methods
	*
	*	get_platforms() - returns the list of budgets from DB
	*	get_platforms_list - return a Select element for HTML output
	*
	******** */
		function get_platforms( $platforms_list='') {
			global $db;
			
			$where = '';
			
			if ( !empty($platforms_list) ) :
				if ( !is_array($platforms_list) ) :
					$platforms_list = explode(',',$platforms_list);
				endif;
				$platforms_list = implode("','",$platforms_list);
				$where = "WHERE id IN ('" . $platforms_list . "')";
			endif;
			
			$sql = "SELECT id, platform FROM options_platforms " . $where . " ORDER BY platform";
			$sql = $db->to_array($sql);
			return $sql;
		
		}
		
		
		function get_platforms_select_list() {
			global $db;
			
			$sql = "SELECT id, platform FROM options_platforms ORDER BY platform ASC";
			$sql = $db->to_array($sql);
			
			return $sql;
		}
		
		
		function get_platforms_list( $platforms_list = '' ) {
			global $form;
			
			$platforms = $this->get_platforms(); 
			$element  = '';
			
			$platforms_list = ( !empty($platforms_list) && !is_array($platforms_list)) ? explode(',',$platforms_list) : $platforms_list;
			
			$cnt=0;
			$br='';
			
			foreach ($platforms as  $platform ) :
				$platform_name = $platform['platform'];
				$platform_id   = $platform['id'];
				
				$checked = ( is_array($platforms_list) && in_array($platform_id, $platforms_list) ) ? ' checked': '';
				$checked = ( isset($form->platforms) && $form->platforms == $platform_id ) ? " checked" : '';
				$element .= $br.'<span><input type="checkbox" class="plaform_checkbox" name="incl_platforms[]" id="incl_platforms_' . $platform_id . '" value="' . $platform_id . '"' . $checked . ' /> <label for="platform_' . $platform_id . '">' . $platform_name . '</label></span>';
				$cnt++;
				$br='';
				if ( $cnt == 4 ):
					$br = '<br />';
					$cnt = 0;
				endif;
			endforeach;
			
			return $element;
		}
		
		
		
		function get_platform_details( $platforms_list = '' ) {
			global $form;
			
			$platforms = $this->get_platforms($platforms_list); 
			$element  = '';
			
			foreach ($platforms as  $platform ) :
				$platform_name = $platform['platform'];
				$platform_id   = $platform['id'];
				
				$element .= '<span>' . $platform_name . '</span>, ';
			endforeach;
			
			$element = rtrim($element,', ');
			return $element;
		}
	/* ********
	* End
	****************************************** */
	
	
	
	
	
	function get_countries_select_list() {
		global $db;
		
		$sql = "SELECT id, label FROM countries ORDER BY label ASC";
		$sql = $db->to_array($sql);
		
		return $sql;
	}
	
	
	
	
	
	function get_states_select_list($short=true) {
		global $db;
		
		$second_field = ( @$short ) ? "label as state, state as label" : "state, label";
		
		$sql = "SELECT {$second_field} FROM options_states ORDER BY label ASC";
		$sql = $db->to_array($sql);
		
		return $sql;
	}
	
}
$options = new App_Options;

?>