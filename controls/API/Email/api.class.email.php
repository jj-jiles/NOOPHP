<?

class email {
	
	public function __construct() { return true; }
	
	
	
	
	
	
	
	
	#
	#	EXAMPLE: 
	#	htmlmail(
	#		to           = 'your_email@your_domain.com',
	#		$from        = array('name'=>'your name', 'email'=>'local_email@your_domain.com')
	#		replyto      = 'replyto@their_domain.com',
	#		$subject     = 'Subject Line',
	#		$message     = 'Email Message'
	#	);
	#
	#	Optional: 
	#		$from & $replyto
	public function send( $vars=array() ) {
		
		$boundary = md5( uniqid ( rand() ) );
		
		
		$to_name    = (isset($vars['to']['name'])) ? $vars['to']['name'] : $vars['to']['email'];
		$to_email   = $vars['to']['email'];
		$from_name  = (isset($vars['from']['name'])) ? $vars['from']['name'] : $vars['from']['email'];
		$from_email = $vars['from']['email'];
		$replyto    = isset($vars['replyto']) ? $vars['replyto'] : $vars['from']['email'];
		$subject    = $vars['subject'];
		$message    = $vars['message'];
		
		$headers  = "From: ".$from_email."\n";
		if (!empty($replyto)) :
			$headers  .= "Reply-To: ".$replyto."\n";
		endif;
		$headers .= "Received: \"".$to_name."\" <".$to_email.">\n";
		$headers .= "MIME-Version: 1.0\n";
		$headers .= "Subject: ".$subject."\n";
		$headers .= "Content-Type: multipart/related;";
		$headers .= "boundary=\"------------".$boundary."\"\n";
		$headers .= "This is a multi-part message in MIME format.\n";
		$headers .= "--------------".$boundary."\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\n";
		$headers .= "Content-Transfer-Encoding: 7bit";
		
		mail($to_email, $subject, $message, $headers);
	}
	# ### #
	
	
}

$email = new email();

?>