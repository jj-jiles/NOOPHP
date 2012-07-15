<?php
	$give_it_a_try = array('Yes I do' => 'Y', 'Maybe Later' => 'N');
	$how_many      = array('I have 10' => '10', 'I have 20' => '20');
	$some_select   = array(
		'10' => 'I have 10', 
		'20' => 'I have 20', 
		'30' => 'I have 30'
	);
?>
<?php app::header(); ?>
<?php form::start(); ?>
	<?php if ( form::has_post() ) {
			form::$validate->validate_email(form::$posts->incl_email);
		} ?>
	<?php form::echo_error(); ?>
	<p class="note"><em>* Required</em></p>
	<p>
		Name<i>*</i>: <br />
		<?php form::input('incl_name'); ?>
	</p>
	<p>
		Email<i>*</i>: <br />
		<?php form::input('incl_email'); ?>
	</p>
	<p>
		Phone: <br />
		<?php form::input('phone'); ?>
	</p>
	<p>
		Do you want to try it?<br />
		<?php form::checkbox('give_it_a_try', $give_it_a_try, array('default'=>0)); ?>
	</p>
	<p>
		How many do you have?<br />
		<?php form::radio('how_many', $how_many, array('default'=>0)); ?>
	</p>
	<p>
		How many do you have?<br />
		<?php form::select('some_select', $some_select, array('default'=>0)); ?>
	</p>
	<p>
		<?php form::submit('excl_submit','submit'); ?> &nbsp; <?php form::reset('cancel','cancel'); ?>
	</p>
<?php form::close(); ?>
<?php app::footer(); ?>