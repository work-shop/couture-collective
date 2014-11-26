<!DOCTYPE html>

<html class="header-closed">

<head>

	<meta charset="<?php bloginfo('charset'); ?>">
	
	<?php if (is_search()) { ?>
		<meta name="robots" content="noindex, nofollow" /> 
	<?php } ?>

	<title> 
	   <?php
	      if (function_exists('is_tag') && is_tag()) {
	         single_tag_title(); 
	         }
	      elseif (is_search()) {
	         echo 'Search for &quot;'.wp_specialchars($s).'&quot; - '; }
	      elseif (!(is_404()) && (is_single()) || (is_page())) {
	         wp_title(''); echo ' - '; }
	      elseif (is_404()) {
	         echo 'Not Found - '; 
	         }
	      if (is_home()) {
	         bloginfo('name'); echo ' - '; bloginfo('description'); }
	      else {
	          bloginfo('name'); }
	   ?>
	</title>
				   
	<meta name="description" content="<?php bloginfo('description'); ?>">
	<meta name="google-site-verification" content="">
	<meta name="author" content="Greg Nemes and Nic Schumann of Work-Shop">
		
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	
	<link rel="shortcut icon" href="<?php bloginfo('template_directory'); ?>/_/img/favicon.ico">
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
			
    <!--[if lt IE 9]>
      <script src="<?php bloginfo('template_directory'); ?>/_/js/html5shiv.js"></script>
      <script src="<?php bloginfo('template_directory'); ?>/_/js/respond.js"></script>
    <![endif]-->		
    
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,700,300,400' rel='stylesheet' type='text/css'> 
        	
	<?php wp_head(); ?>
				
</head>

<body <?php body_class('before'); ?>>

<?php get_template_part('landing'); ?>

<?php get_template_part('ie'); ?>

<div id="state" class="loading">
	
		<header id="header" class="closed">
			<div class="container">		
				<?php 
					$args = array(
						'theme_location' => 'top-left-menu', 
						'container' => 'nav',
						'container_class' => 'left hidden-xs col-sm-4',
					);
					wp_nav_menu( $args ); 
				?>

			
				<a id="logo" class="logo col-sm-4" href="<?php bloginfo('url'); ?>">
					<img src="<?php bloginfo('template_directory'); ?>/_/img/logo.png" alt="logo">					
				</a>		

				 <?php if ( is_user_logged_in() ) {
						$args = array(
							'theme_location' => 'top-right-menu', 
							'container' => 'nav',
							'container_class' => 'right hidden-xs col-sm-4',
						);
						wp_nav_menu( $args ); 

				} else{ 
					include '_partials/small-login.php';
				} ?> 

				<nav class="right hidden-xs hidden" id="nav">
					<ul class="main-menu">
						<li><a href="<?php bloginfo('url'); ?>/how-it-works"  class="">How it Works</a></li>
						<li><a href="<?php bloginfo('url'); ?>/look-book" class="">Look Book</a></li>								

					</ul>	
				</nav>	
				
				<!--
<a id="carrot" href="#" class="nav-toggle closed">
					<img src="<?php bloginfo('template_directory'); ?>/_/img/toggle.png" alt="navigation-toggle">
				</a>
-->
											
			</div>					
		</header>

	<div id="headerfix"></div>
		
	<div id="content" class="">
