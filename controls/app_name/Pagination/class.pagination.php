<? 

class Pagination_Module {

	var $start_number = 1;
	var $end_number   = 1;
	var $current_page = 1;
	var $max_pages    = 4;
	
	var $page_count = 0;
	var $pages      = 0;

	function Pagination_Module() { return true; }
	
	
	function init( $vars=false ) {
		
		if ( is_array($vars) ) :
			foreach ( $vars as $key=>$val ) :
				$this->$key = $val;
			endforeach;
		endif;
		
		# get the number of pages the complete result set has
		$this->pages = ($this->page_count)/10;
		$this->pages = ceil($this->pages);
	
		# build the url for the page numbers
		$this->page_url    = url::component().'/dashboard/'.$this->page_tab;
		$this->page_anchor = '#!/' . $this->page_tab;
		
		# the URI has a page number
		$this->has_page_number = (bool) (isset($app->uri['page']));
		
		# if there is no page number in the URI, establish 1 as the default
		if ( !$this->has_page_number ) :
			$app->uri['page'] = 1;
			$this->has_page_number = true;
		endif;
	
		# ###
		# Establish some facts
		# ###
		# user is viewing a specific tab
		$this->viewing_this_tab = (bool) ( isset($app->uri[$this->page_tab]) );	
		# user is on page one
		$this->is_page_one      = (bool) ( @$this->has_page_number && $app->uri['page'] == 1 );
		# user is not viewing the last page of result set
		$this->not_last_page    = (bool) ( !$this->has_page_number || (@$this->has_page_number && $app->uri['page'] < $this->pages) );
		# should we display the Next/Last anchors
		$this->show_next_last   = (bool) ( $this->pages > 1 );
		
		# determine what the current page is that's being viewed
		$this->current_page = ( @$this->has_page_number && @$this->viewing_this_tab ) ? $app->uri['page'] : 0;
	}
	
	
	
	function is_too_high() {
		return (bool) ($this->current_page >= $this->max_pages);
	}
	
	
	function start_number() {
		$this->start_number = 1;
		$this->start_number = ( @$this->is_too_high() ) ? $this->current_page - $this->max_pages : $this->start_number;
		$this->start_number = ( $this->start_number < 1 ) ? 1 : $this->start_number;
		
		if ( $this->current_page > ($this->max_pages/2) ) :
			$this->start_number = ceil($this->current_page - ($this->max_pages/2));
		endif;
		
		return $this->start_number;
	}
	
	
	function end_number() {
		
		if ( $this->current_page < $this->max_pages ) :
			$this->end_number = $this->start_number + $this->max_pages;
		else :
			$this->end_number = $this->current_page + $this->max_pages;
		endif;
		
		if ( $this->current_page > ($this->max_pages/2) ) :
			$this->end_number = ceil($this->current_page + ($this->max_pages/2));
		endif;
		
		if ( $this->end_number >= $this->pages ) :
			$this->end_number = $this->pages;
		endif;
		
		return $this->end_number;
	}
	
	
	
	function page_number_anchors() {
		global $app;
		
		// loop through the number of pages for the display of pagination links
		for ( $i=$this->start_number(); $i<=$this->end_number(); $i++ ) :
			$this->is_this_page = (bool) ( @$this->viewing_this_tab && (@$this->has_page_number && $app->uri['page'] == $i) );
			$this->is_this_page = ( !$this->viewing_this_tab && $i == 1 ) ? true : $this->is_this_page;
			
			// we are viewing any other page, but this page
			if ( !$this->is_this_page ) :
				echo '<a href="' . $this->page_url . '/page,' . $i . $this->page_anchor . '">' . $i . '</a>';
	
			// we are viewing this page number
			else :
				echo '<b>' . $i . '</b>';
	
			endif;
			
		endfor; 
			
	}
	
	
	
	function first_previous_anchors() {
		global $app;
		
		$this->start_class = ( @$this->viewing_this_tab && !$this->is_page_one ) ? '' : 'class="prev-next hide"';		
		$this->start_class = ( $this->max_pages >= $this->pages ) ? 'class="prev-next hide"' : $this->start_class;
		
		echo '<a ' . $this->start_class . ' href="' . $this->page_url . $this->page_anchor . '">&laquo;</a>';
		echo '<a ' . $this->start_class . ' href="' . $this->page_url . '/page,' . ($app->uri['page']-1) .  $this->page_anchor . '">&lt;</a>';
		
	}
	
	
	function last_next_anchors() {
		global $app;
		
		# we are viewing any page other than the last
		$this->end_class = ( (!$this->viewing_this_tab || (@$this->viewing_this_tab && @$this->not_last_page)) && @$this->show_next_last ) ? '' : 'class="prev-next hide"';
		
		$this->end_class = ( $this->end_number >= $this->pages ) ? 'class="prev-next hide"' : $this->end_class;
		
		echo '<a ' . $this->end_class . ' href="' . $this->page_url . '/page,' .  ($app->uri['page']+1) . $this->page_anchor . '">&gt;</a>';
		echo '<a ' . $this->end_class . ' href="' . $this->page_url . '/page,' . $this->pages . $this->page_anchor .'">&raquo;</a>';
		
	}

	
}

$paginate = new Pagination_Module();
?>