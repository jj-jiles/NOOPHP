var app = {
	
	make_clickables : function() {
		this.login_registration_href_click();
		this.footer.links();
	},
	
	login_registration_href_click : function() {
		//$('.logo-menu-register').click(function(e) { footer.display_login_form(); e.preventDefault(); });
		//$('.btn-sign-up').click(function(e) { app.display_login_form('reg'); e.preventDefault(); });
		$('.login-button').click(function() { app.display_login_form('login'); return false; });
		$('a.sign-in').click(function() { app.display_login_form('login'); return false; });
		//$('.login-link').click(function(e) { footer.display_login_form(); e.preventDefault(); });
	},
	
	display_login_form : function(_method) {
			
			app._method_call = _method;
		
			//
			//
			// the form has not been requested yet
			if ($('#registration-form-dim').length == 0) {
				//
				//
				// get the content from the reg/login page and display it
				$.get(app.urls.get_from.registration, function(data) {
					//
					// apped the content to the body
					$('body').append(data);
						
					body_height = $('body').height();
					body_offset = $(window).scrollTop();
					
					$('#registration-form-dim').css('height',body_height);					
					$('#registration-form-overlay-wrapper').css('top',body_offset);
					
					overlay_content_height = $('.overlay-form-content').height();
					overlay_content_width = $('.overlay-form-content').width();
					overlay_content_width = (parseInt(overlay_content_width)/2)+16;
					
					$('.overlay-form-content').css('height',overlay_content_height);
					$('.two-column').css('height',overlay_content_height);
					$('#registration-form-overlay-wrapper').css('margin-left', '-' + overlay_content_width + 'px');
					
					//
					//
					// after a half second delay (to give things time to draw),
					// add the event listeners to the reg/login content elements
					window.setTimeout(function() {
	
						// Set bind to Esc key for closing the alert
						$(document).unbind('keyup');
						$(document).bind('keyup',function(event) {
							if(event.keyCode == 27){
								$('._ex_cancel').click();
							}
						});
						
						//
						// focus on login field
						if ( app._method_call == 'login' )  {
							$('#incl_login_email').focus();
						} else {
							$('#incl_registration_email').focus();
						}
						
						//
						// validate user's nickname is available on lost focus
						$('#incl_email').blur(function() {
							var $_name  = $(this);
							var $_val  = $_name.val();
							if ( $_val.length > 0 ) {
								//
								//
								// post to setup new account
								$.post(app.urls.post_to.validate_nickname, {
									  _name  : $_val
								},function(data,status) {
									if (data == 0) {
										$_name.removeClass('nickname-taken');
										$_name.addClass('nickname-available');
									} else {
										$_name.removeClass('nickname-available');
										$_name.addClass('nickname-taken');
									}
								});
							}
						});
						
						//
						//
						// the user is loggin in
						$('#_ex_login').click(function() {
					
							$('#overlay-login-header').text('Logging In');
												    
							app._err   = '';
							app._email = $('#incl_login_email').val();
							app._pass  = $('#incl_login_password').val();
							app._rem   = ($('#login_remember_me_remember').is(':checked')) ? 'yes' : 'no';
							
							if (app._email == '') {
								app._err += '<span> &nbsp; &raquo; provide an email address</span>';
							}
							if (app._pass == '') {
								app._err += '<span> &nbsp; &raquo; provide a password</span>';
							}
							
							//
							// validate form values								
							if ( app._err != '' ) {
								app._try_again = '<div class="buttons"><input type="button" value="try again?" id="login_try_again" class="upload-button" onclick="$(\'#login-message\').slideUp(\'fast\',function() {$(\'#overlay-login-header\').text(\'Login\');$(\'#incl_login_email\').focus();});" />';
								app._cancel    = '<input type="button" value="cancel login?" id="cancel_login" class="upload-button" onclick="$(\'._ex_cancel\').click();" /></div>';
								app._err = '<div class="form-elements"><p>You need: <br />' + app._err + '</p></div><div class="clear"></div>' + app._try_again + ' - or - ' + app._cancel;
								$('#login-message').html(app._err);
								$('#login-message').slideDown('fast',function() {
									$('#overlay-login-header').text('Login');
								});
								window.setTimeout(function() {
									$('#login_try_again').focus();
								}, 500);
							//
							// all is well
							} else {
								
								app._login_ref = ( $('.enterprise-page').length > 0 ) ? 'enterprise' : '';
								
								//
								//
								// post to setup new account
								$.post(app.urls.post_to.login, {
									  _email : app._email,
									  _pass  : app._pass,
									  _rem   : app._rem,
									  _ref   : app._login_ref
								},function(data,status) {
									
										/* ***
										* buttons *** */
										app._try_again = '<div class="buttons"><input type="button" value="try again?" id="try_again" class="upload-button" onclick="$(\'#login-message\').slideUp(\'fast\',function() { $(\'#overlay-login-header\').text(\'Login\'); $(\'#incl_login_email\').focus(); });" tabindex="3" />';
										app._cancel    = '<input type="button" value="cancel login?" id="try_again" class="upload-button" onclick="$(\'._ex_cancel\').click();" tabindex="4" /></div>';
										
										app._message = 'you messed up something';
										
										/* ***
										* data returned from the post *** */
										data     = data.split(':::');
										_type    = data[0];
										_mess    = data[1];
										app._loc = window.location.href;
										
										/* ***
										* a successful login *** */
										if ( _type == 'location' ) {
											
											app._loc       = data[2];
											app._try_again = '';
											app._cancel    = '';
											$('#login-message').html( _mess );
											$('#login-message').slideDown('fast');
											
											/* ***
											* reload the current page so the session takes place *** */
											window.setTimeout(function() { window.location = app._loc; }, 2000);
										
										
										/* ***
										* a successful login *** */
										} else if ( _type == 'success' ) {
											app._try_again = '';
											app._cancel    = '';
											$('#login-message').html( _mess );
											$('#login-message').slideDown('fast');
											
											/* ***
											* reload the current page so the session takes place *** */										
											window.setTimeout(function() { window.location = app._loc; }, 2000);
										
										/* ***
										* invalid login for whatever reason. display the error *** */
										} else if ( _type = 'error' ) {
											$('#login-message').html(_mess + '<div class="clear"></div>' + app._try_again + ' - or - ' + app._cancel + '');
											$('#login-message').slideDown('fast');
											window.setTimeout(function() { $('#overlay-login-header').text('Login'); $('#try_again').focus(); }, 500);
										}
								});
							}
						});
						//__ login __//
						
						//
						//
						// adds event listener to close buttons
						$('._ex_cancel').click(function() { 
							$('#login-message').slideUp('fast');
							$('#register-message').slideUp('fast');
							window.setTimeout(function() {
								$('#incl_login_email').val('');
								$('#incl_login_password').val('');
								$('#incl_registration_email').val('');
								$('#incl_registration_password').val('');
								$('#incl_confirm_registration_password').val('');
								$('#incl_registration_referrer').val('');
								
								$('#registration-form-dim').fadeOut('fast', function() {
				
									// Set bind to Esc key for closing the alert
									$(document).unbind('keyup');
									
								});
							},300);
						});
						//__ close click __//
						
						
						//
						// catch the "enter key" press event for the login form
						app.enter_key_press( $('#incl_login_email'), $("#_ex_login") );
						app.enter_key_press( $('#incl_login_password'), $("#_ex_login") );
						app.enter_key_press( $('#login_remember_me_remember'), $("#_ex_login") );
						//
						// catch the "enter key" press event for the registration form
						app.enter_key_press( $('#incl_registration_email'), $("#_ex_register") );
						app.enter_key_press( $('#incl_registration_password'), $("#_ex_register") );
						app.enter_key_press( $('#incl_confirm_registration_password'), $("#_ex_register") );
						app.enter_key_press( $('#incl_registration_account_type'), $("#_ex_register") );
						app.enter_key_press( $('#incl_registration_privacy'), $("#_ex_register") );
						app.enter_key_press( $('#incl_registration_referrer'), $("#_ex_register") );
						
						$('#registration-form-dim').click(function(event) {
							_desired_id = $(this).attr('id');
							if (event.target.id == _desired_id) {
								$('._ex_cancel').click();
							}
						});
						
						
					}, 500);
					//__ window timeout __//
				});
				// *** //
			//
			//
			// the form has already been drawn so we just need to fade it in
			} else {
				body_height = $('body').height();
				body_offset = $('body').scrollTop();
				$('#registration-form-dim').css('height',body_height);					
				$('#registration-form-overlay-wrapper').css('top',body_offset);
				$('#registration-form-dim').fadeIn('normal',function() {
					//
					// focus on login field
					$('#incl_login_email').focus();
				});
			}
	},
	
	
	
	
	
	logout : {
		
		init : function() {		
			if (top.location == location) {
				window.setTimeout('app.logout.session_timeout_alert()',5000);
			}
		},

		go : function() {
			window.location = app.urls.root + '/sign-out';
		},
		
		renew_session : function() {
			$('#script-iframe').attr('src', app.urls.root + '/renew-session?__=' + Math.random());
			clearTimeout(app.logout.session_timeout);
			clearTimeout(app.logout.session_timeout_warning);
			window.setTimeout('app.logout.session_timeout_alert()',5000);
			app.alert.close();
		},
		
		clear_session_timeout_alert : function() {
			clearTimeout(app.logout.session_timeout);
			clearTimeout(app.logout.session_timeout_warning);
		},
		
		
		session_timeout_alert : function() {
				app.logout.session_timeout_warning = setTimeout(function() {
					
					_title = '<h3>Your Session Is About To Expire</h3>';
					_mess  = '<p>Your session is about to expire in 3 minutes.<br /><span>Would you like to renew it?</span></p>';
					_buttons = '<p class="align-right buttons"><input type="button" class="button" name="renew-session-yes" id="renew-session-yes" value="Yes" onClick="app.logout.renew_session();" />';
					_buttons += '&nbsp;&nbsp;<input type="button" class="button" name="renew-session-no" id="renew-session-no" value="No" onClick="app.logout.go();" /></p>';
					
					app.alert.message( 
						_title + _mess + _buttons, 
						{ 
							button : { 
								display: false 
							}, 
							modal : true 
						} 
					);
				},1000*60*27);
				
				app.logout.session_timeout = setTimeout("app.logout.go();",1000*60*30); //1800000	
		}
		
	},
	
	
	
	
	
	alert : {
		message : function( message, options, callback ) {
			
			body_height = $('body').height();
			body_offset = $(window).scrollTop();
			
			app.alert.message.callback = callback;
			
			app._cancel_text = 'Ok';
			
			has_button   = true;
			is_modal     = false;
			
			if ( typeof (eval(options)) == 'function' ) {
				app.alert.message.callback = options;
				
			} else if ( typeof(options) == 'object' ) {
				
				if ( typeof(options.button) == 'object' ) {
					has_button  = typeof(options.button.display) != 'undefined' ? options.button.display : true;
					button_text = typeof(options.button.text)    != 'undefined' ? options.button.text : app._cancel_text;
					
					is_modal    = typeof(options.modal) == 'undefined' ? false : options.modal;
					
				} else if ( options.button ) {
					app._cancel_text = options.button;
					
				}
			}
			
			app._cancel  = (has_button==false)? '' : '<p class="align-right buttons"><input type="button" value="' + app._cancel_text + '" id="close_alert" class="button" onclick="app.alert.close();" /></p>';
			
			$('#alert-message-content').html( message + app._cancel );
			
			$('#alert-message-dim').css({
				height : body_height,
				width  : '100%'
			});					
			
			$('#alert-message-overlay-wrapper').css({
				top : body_offset,
				marginLeft : '-141px'
			});
			
			//
			//
			// after a half second delay (to give things time to draw),
			// add the event listeners to the reg/login content elements			
			$('#alert-message-overlay-wrapper').css('top',body_offset);
			$('#alert-message-dim').fadeIn('fast', function() {
			
				overlay_content_height = $('#alert-message-content').height();
				overlay_content_width = $('#alert-message-content').width();
				overlay_content_width = (parseInt(overlay_content_width)/2)+16;
				
				
				$('#alert-message-content').css('height',overlay_content_height);
				
				$('#alert-message-overlay-wrapper').css({
					top        : body_offset,
					marginLeft : '-' + overlay_content_width + 'px'
				});
				
				// Set focus on the Ok button
				$('#close_alert').focus();
		
				if ( !is_modal ) {
					// Set bind to Esc key for closing the alert
					$(document).unbind('keyup');
					$(document).bind('keyup',function(event) {
						if(event.keyCode == 27){
							app.alert.close();
						}
					});
								
					$('#alert-message-dim').click(function(event) {
						_desired_id = $(this).attr('id');
						if (event.target.id == _desired_id) {
							app.alert.close();
						}
					});
				}
			
			});
			
			return false;
			
		},
	
		
		
		close : function() {
			$('#alert-message-dim').fadeOut('fast',function() {
				$('#alert-message-content').html('');
				$('#alert-message-content').css('height','auto');
				if ( typeof (eval(app.alert.message.callback)) == 'function' ) {
					eval(app.alert.message.callback);
				}
			});
		}
	},
	
	
	enter_key_press : function( obj, btn ) {
		var obj = obj;
		var btn = btn;
		
		obj.unbind('keyup');
		obj.bind('keyup',function(event) {
			if(event.keyCode == 13){
				btn.click();
			}
		});
	},
	
	
	
	
	
	footer : {
		
		links : function() {
		
			$('#a-privacy_modal').click(function() {
				
				var _mess = $('#privacy_modal').html();
				
				app.alert.message('<h3>Privacy Policy</h3>' + _mess);
				
			});
		
			$('#a-terms_modal').click(function() {
				
				var _mess = $('#terms_modal').html();
				
				app.alert.message('<h3>Terms &amp; Conditions</h3>' + _mess);
				
			});
			
		}
		
	}
	
	

}

$(document).ready(function() {
	
	//app.form_message = $('#form-message');
	//if ( app.form_message.length > 0 ) {
	//	window.setTimeout(function() { 
	//		app.form_message.slideUp('fast'); 
	//	}, 3000);
	//}
});