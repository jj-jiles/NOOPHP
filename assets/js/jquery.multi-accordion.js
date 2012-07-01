

(function($){
	
	
	/* *******************************************************************************************
	*
	*	jQuery Multi-Accordion
	*	Copyright (c) 2001-2011. JJ Jiles
	*	site: http://jjis.me
	*
	*	Licences: MIT, GPL
	*	http://www.opensource.org/licenses/mit-license.php
	*	http://www.gnu.org/licenses/gpl.html
	*
	**********************************************************************************************
	*
	*	Use:
	*		Ability to have multiple accordions without limitations of HTML hierarchy
	*		It also allows the control of multiple accordions to interact
	*
	*		This accordion technique is not limited to HTML layout constraints. I use
	*		traversing to locate the specified elements so that you can design your
	*		HTML more freely. Hopefully I've compensated for everything
	*
	*		Callbacks are available for "afterclose" and "afteropen", but only on
	*		the accordion current accordion group. Callbacks will not fire on "afterclose"
	*		on the non-focused accordion. i.e. Accordion-2 closes Accordion-1 collapsibles
	*
	*	Required:
	*		parent      : '#id-of-element' or '.class-of-element' or 'tagName' !not an object
	*		header      : '#id-of-element' or '.class-of-element' or 'tagName' !not an object
	*		collapsible : '#id-of-element' or '.class-of-element' or 'tagName' !not an object
	*
	*	Optional:
	*		afterclose  : function() { alert('this fires after a collapsible is closed'); }
	*		afteropen   : function() { alert('this fires after a collapsible is opened'); }
	*
	*	HTML Markup Example:
	*		<style>
	*			.accordion-collapsible { display: none; }
	*		</style>
	*
	*		<div id="content-container">
	*			<div id="accordion-headers">
	*				<h1 class="clickable-header-accordion-1">Click Me</h1>
	*				<h1 class="clickable-header-accordion-1">Click Me</h1>
	*			</div>
	*			
	*			<div class="sub-content">
	*				<ul class="accordion-collapsibles">
	*					<li>Item 1</li>
	*					<li>Item 1</li>
	*					<li>Item 1</li>
	*					<li>Item 1</li>
	*				</ul>
	*				<ul class="accordion-collapsibles">
	*					<li>Item 1</li>
	*					<li>Item 1</li>
	*					<li>Item 1</li>
	*					<li>Item 1</li>
	*				</ul>
	*			</div>
	*		</div>
	*		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
	*		<script type="text/javascript" src="jquery.multi-accordion.js"></script>
	*		
	*
	*
	**********************************************************************************************
	*
	*
	*		var accordions = [
	*			{
	*				'parent'      : '#content-container',            // parent element containing the clickable header and collapsible
	*				'header'      : '.clickable-header-accordion-1', // clickable header
	*				'collapsible' : '.accordion-collapsibles'        // collapsible element
	*			},		
	*			{
	*				'parent'      : '#content-container',            // parent element containing the clickable header and collapsible
	*				'header'      : '.clickable-header-accordion-2', // clickable header
	*				'collapsible' : '.accordion-collapsibles',       // collapsible element
	*				'afterclose'  : function() { alert('after close'); },
	*				'afteropen'   : function() { alert('after open'); }
	*			}
	*		];
	*		$().accordion(accordions);
	*
	******************************************************************************************** */	
     $.fn.accordion = function(options) {
		 
		// establish the options
		var options         = typeof(options) != 'undefined' ? options : '';
		
		// establish some globals so we can run multiple accordions
		var options_len = options.length-1;
		var _cnt_;
		var _header      = [];
		var _collapsible = [];
		var _parent      = [];
		var _callback    = [];
		var _afteropen   = [];
		var _afterclose  = [];
		
		// loop through the accordions and bind events
		for ( _cnt_=0; _cnt_ <= options_len; _cnt_++ ) {
			
			/* ***
			* set counter as a global for the array */
			var $cnt = _cnt_;
			
			/* ***
			* primary elements specified */
			_parent[_cnt_]      = typeof(options[_cnt_].parent)      != 'undefined' ? options[_cnt_].parent      : '';
			_header[_cnt_]      = typeof(options[_cnt_].header)      != 'undefined' ? options[_cnt_].header      : '';
			_collapsible[_cnt_] = typeof(options[_cnt_].collapsible) != 'undefined' ? options[_cnt_].collapsible : '';
		
			/* ***
			* setup the callback function */
			_afterclose[_cnt_] = typeof(options[_cnt_].afterclose)!= 'undefined' ? options[_cnt_].afterclose : '';
			_afterclose[_cnt_] = _afterclose[_cnt_];
			_afteropen[_cnt_] = typeof(options[_cnt_].afteropen)!= 'undefined' ? options[_cnt_].afteropen : '';
			_afteropen[_cnt_] = _afteropen[_cnt_];
			
			
			/* ***
			* hide all collapsible items */
			$(_collapsible[_cnt_]).css({ 'display' : 'none' });
				
			/* ****
			* 	bind the accordion header click event. we handle the event binding this
			* 	so we don't overwrite existing clicks on previously established accordions
			**** */
			$(_parent[_cnt_]).find(_header[_cnt_]).each(function() {
				
				var _$cnt   = $cnt;
				// current header in the array
				var $header = $(this);
				
				/* ***
				* bind click event to it */
				$header.unbind('click');
				$header.bind('click',function() {				
					var $header        = $(this);
					
					/* ****
					* Traverse and find the header's parent and collapsibles */					
					var $header_parent = $header.parentsUntil(_parent[_$cnt]).parent(); // parent
					var content_obj    = $header_parent.find(_collapsible[_$cnt]);      // collapsible
					
					/* ****
					* if the clicked header collapsible is currently closed, open it */
					if(content_obj.is(":hidden")) {
					
						/* ***
						* loop through all established accordions and close open collapsibles */
						for ( j=options_len; j >=0; j-- ) {
							$(_parent[j]).find(_collapsible[j]).each(function() {
								$(this).slideUp('fast');
							});
						}
						
						/* ****
						* show the collapsible of the clicked header */
						content_obj.slideDown('fast',function() {
							/* ****
							*  fire the AfterOpen callback */
							if(typeof _afteropen[_$cnt] == 'function'){
								_afteropen[_$cnt].call(this);
							}
						});
						
						
					/* ****
					* if the clicked header collapsible is currently open, close it */
					} else {
						
						/* ****
						* close the collapsible of the clicked header */
						content_obj.slideUp('fast',function() {
							/* ****
							* fire the AfterClose callback */
							if(typeof _afterclose[_$cnt] == 'function'){
								_afterclose[_$cnt].call(this);
							}
						});
					}
					
					return false;
					
				});
			});
		}
     }
})(jQuery);