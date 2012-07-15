<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
	<title>WretchedLocket &raquo; <?php app::echo_page_title(); ?></title>
	<meta name="keywords" content="<?php app::echo_meta_keywords(); ?>" />
	<meta name="description" content="<?php app::echo_meta_description(); ?>" />
	<link rel="stylesheet" href="<?= url::assets(); ?>/css/style.css" id="main-style" type="text/css" />
	<link rel="stylesheet" href="<?= url::assets(); ?>/css/forms.css" id="forms-style" type="text/css" />
	<?php app::css(); ?>
</head>
<body>
<div class="content">
	<ul class="top-navigation">
		<?php echo app::home_link(); ?>
		<li><a href="<?php echo url::root(); ?>/test">Test</a></li>
		<li><a href="<?php echo url::root(); ?>/test-form">A Form</a></li>
		<?php if ( session::is_logged_in() ) { ?>
			<li><a href="<?php echo url::root(); ?>/sign-out">Logout</a></li>
		<?php } else { ?>
			<li><a href="<?php echo url::root(); ?>/account">Account (Session Protected)</a></li>
		<?php } ?>
		<li><a href="https://github.com/WretchedLocket/NOOPHP">Github Page</a></li>
	</ul>
	<div class="clear"></div>