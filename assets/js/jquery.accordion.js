

var accordion = {};
$(document).ready(function() {
		
		$('.li-item> b').click(function() {
			accordion.b          = $(this);
			
			accordion.rel        = $(this).attr('rel');
			accordion.rel_str    = accordion.rel.split(/___/);
			accordion.pd_str     = accordion.rel_str[0];
			
			accordion.rel_str    = accordion.rel_str[0].split(/_/);
			accordion.ul_str     = accordion.rel_str[0];
			
			accordion.li         = $('#li-item___' + accordion.rel);
			accordion.parent     = $('#tab-ul___' + accordion.ul_str);
			accordion.expandable = $('#project-details___' + accordion.pd_str);
			
			accordion.selected   = accordion.parent.find('.li-selected');
			
			if ( accordion.selected.length > 0 ) {
				
				if ( accordion.li.hasClass('li-selected') ) {
					accordion.expandable.fadeOut('fast',function(){
						accordion.selected.removeClass('li-selected');
					});
				
				} else {
				
					$.each(accordion.selected,function(i) {
						var _previous_li       = $(this);
						var _previous_rel      = _previous_li.attr('id');
						var _previous_rel_str  = _previous_rel.split(/___/);
						var _previous_rel_str  = _previous_rel_str[1];
						
						/* ** Hide the old LI ** */
						$('#project-details___' + _previous_rel_str).fadeOut('fast',function(){
							_previous_li.removeClass('li-selected');
							accordion.li.addClass('li-selected');
							
							/* ** Show the new LI ** */
							accordion.expandable.fadeIn('fast',function() {
							
								/* ** Scroll page to the new LI ** */
								window.setTimeout(function() { 
						
									accordion.li.removeClass('lead-purchased');
									accordion.li._offset = accordion.li.offset();
									accordion.li.top     = parseInt(accordion.li._offset.top);
									$(window).scrollTo(accordion.li.top,0, {duration:200});
								},200);
								/* ** // Scroll Page ** */
								
								/* ** Add click to the Hide Details button ** */
								accordion.expandable.find('#hide_details').unbind('click');
								accordion.expandable.find('#hide_details').bind('click', function() {
									accordion.b.click();
								});
								/* ** // Hide Details ** */
								
								/* ** Add click to the Hide Details button ** */
								if ( $('.manage-project-save-button').length > 0 ) {
									$('.manage-project-save-button').unbind('click');
									accordion.expandable.find('.manage-project-save-button').bind('click', function() {
										client.manage_project_save_changes($(this));
									});
								}
								/* ** // Hide Details ** */
								
								/* ** Add click to the Hide Details button ** */
								if ( $('.manage-project-close-button').length > 0 ) {
									$('.manage-project-close-button').unbind('click');
									accordion.expandable.find('.manage-project-close-button').bind('click', function() {
										client.manage_project_close_project($(this));
									});
								}
								/* ** // Hide Details ** */
								
							});
							/* ** // Show New ** */
							
						});
						/* ** // Hide Old ** */
						
					});
				}
				
			} else {
				accordion.li.addClass('li-selected');
				accordion.expandable.fadeIn('fast',function() {
					
					accordion.li.removeClass('lead-purchased');
							
					/* ** Scroll page to the new LI ** */
					accordion.li._offset = accordion.li.offset();
					accordion.li.top     = parseInt(accordion.li._offset.top);
					window.setTimeout(function() { 
						$(window).scrollTo(accordion.li.top,0, {duration:200});
					},200);
					/* ** // Scroll Page ** */
								
					/* ** Add click to the Hide Details button ** */
					accordion.expandable.find('#hide_details').unbind('click');
					accordion.expandable.find('#hide_details').bind('click', function() {
						accordion.b.click();
					});
					/* ** // Hide Details ** */
								
					/* ** Add click to the Hide Details button ** */
					if ( $('.manage-project-save-button').length > 0 ) {
									
						$('.manage-project-save-button').unbind('click');
						accordion.expandable.find('.manage-project-save-button').bind('click', function() {
							client.manage_project_save_changes($(this));
						});
					}
					/* ** // Hide Details ** */
								
					/* ** Add click to the Hide Details button ** */
					if ( $('.manage-project-close-button').length > 0 ) {
						$('.manage-project-close-button').unbind('click');
						accordion.expandable.find('.manage-project-close-button').bind('click', function() {
							client.manage_project_close_project($(this));
						});
					}
					/* ** // Hide Details ** */
					
				});
			}
		});
			
});