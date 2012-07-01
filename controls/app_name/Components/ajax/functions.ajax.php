<?
include_once(path::root() . '/includes/constants.php');
include_once(path::root() . '/includes/constants.strings.php');
class Ajax_Component {
	
	function Ajax_Component() {
		
		$content = request::content();
		
		switch ($content) :
		
			case 'admin' :
				include_once( path::app() . '/Components/administrator/functions.administrator.php');
				$admin->ajax_display();
				break;
		
			case 'client' :
				include_once( path::app() . '/Components/client/functions.client.php');
				$client->ajax_display();
				break;
		
			case 'developer' :
				include_once( path::app() . '/Components/developer/functions.developer.php');
				$developer->ajax_display();
				break;
		
			case 'enterprise' :
				include_once( path::app() . '/Components/enterprise/functions.enterprise.php');
				$enterprise->ajax_display();
				break;
		
			case 'login' :
				$session->try_login();
				echo $session->login_message;
				break;
				
			case 'registration' :
				$session->create_account();
				echo $session->registration_message;
				break;
				
			case 'registration-overlay' :
				include_once( path::views() . '/ajax/registration-overlay.php' );
				break;
				
			default :
				return true;
				break;
		endswitch;
		
	}
	
}

$app->ajax = new Ajax_Component();
?>