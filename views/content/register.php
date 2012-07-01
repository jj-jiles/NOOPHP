<?php app::header(); ?>
<?php form::start(); ?>
<p>Fill out the following to register</p>
<?php form::echo_error(); ?>
	<p>
		Email <br />
		<?php form::input('incl_registration_email'); ?>
	</p>
	<p>
		Password<br />
		<?php form::password('incl_password'); ?>
	</p>
	<p>
		Confirm Password<br />
		<?php form::password('confirm_password'); ?>
	</p>
	<p>
		<?php form::submit('submit', 'login'); ?> &nbsp; <?php form::reset('cancel','cancel'); ?>
	</p>
<?php form::close(); ?>
<?php app::footer(); ?>