/* *************************************************
*
* configuration: settings for class & ids of tab
* elements
************************************************* */
var tabs = {
    default_tab    : 0,                    // the # of the tab that should be shown on page load
    // the tab itself
    tab : {
        wrapper   : '.tabs',          // parent that wraps around each tab header
        container : 'b',                // the container that wraps around the clickable tab text
        clickable : 'a',                 // the clickable tab text
        _class     : {
            active   : 'active',   // class for the active tab
            inactive : 'inactive' // class for all inactive tabs
        }
    },
	
	hash_id_structure : '#%%_HASH_%%-tab',
	
    // the content for each tab
    content : {
        _class  : '.tab-content'                 // class of the content for the tabs
    },
 
    /* *************************************************
    *
    * bind tabbing events
    *
    ************************************************* */
    init : function() {
        $( tabs.tab.wrapper + ' ' + tabs.tab.clickable ).click(function() {
            var id  = $(this).attr('href');
            var par     = $(this).parent();
 
            // hide all tab content
            $.each( $( tabs.content._class ), function(i) {
                $( this ).css( 'display','none' );
            });
 
            // display the tab content chosen
            $( id ).css('display','block');
 
            // show the tab as clicked
            $.each( $( tabs.tab.wrapper + ' ' + tabs.tab.container ), function() {
                $( this ).removeClass( tabs.tab._class.active );
                $( this ).addClass( tabs.tab._class.inactive );
            });
 
            par.removeClass( tabs.tab._class.inactive );
            par.addClass( tabs.tab._class.active );
 
            return false;
        });
 
        // hide all tab content except the first tab
        $.each( $( tabs.content._class ), function(i) {
            if (i > tabs.default_tab) {
                $( this ).css('display','none');
            }
        });
		
		var _url = window.location.href;
		var _exploded = _url.split(/#!/);
			replace_what = '/';
			
		if ( typeof(_exploded[1]) != 'undefined' ) {
			hash    = _exploded[1];
			hash    = hash.replace(replace_what,'');
			hash_id = tabs.hash_id_structure.replace(/%%_HASH_%%/,hash);
			if ( $(hash_id).length > 0 ) {
				$(hash_id).click();									
				/* ** Scroll page to the new LI ** */
				window.setTimeout(function() {		
					tabs._offset = $(hash_id).offset();
					tabs.top     = parseInt(tabs._offset.top);
					$(window).scrollTo(tabs.top,0, {duration:200});
				},200);
				/* ** // Scroll Page ** */
			}
		}
    }
 
};
$(document).ready(function() {
	/* ************************************
	* only initiate the tab function if tabs are preset */
	if ( $('.tabs').length > 0 ) {
		tabs.init();
		
		if ( typeof(developer_tab_default) != 'undefined' &&  developer_tab_default !== false ) {
			$('#tab-' + developer_tab_default).click();
		}
	}
});