<?php
	/**
	 * @todo refactor
	 */

	// if (
	// 	//pages that no one should ever see, ever!
	// 	is_shop() ||
	// 	is_product() ||
	// 	is_product_category() ||
	// 	is_product_tag() 
	// ) { 
	// 	get_template_part('_partials/placeholder/placeholder', 'forward' ); 
	// } elseif (
	// 	// pages that the guest user should not see
	// 	cc_user_is_guest() && ( is_page( array( 7 ) ) || is_admin() )
	// ) {
	// 	get_template_part('_partials/placeholder/placeholder', 'forward' ); 
	// } elseif (
	// 	//pages that anyone can see
	// 	is_home() ||
	// 	is_page(array( 9, 26, 30, 363, 7, 6 ))
	// ) {
	// 	// no-op
	// } else {
	// 	//pages that only logged in users should see
	// 	if ( !is_user_logged_in() ) {
	// 	 	get_template_part('_partials/login','modal');
	// 	} else if ( is_page( array( ) ) ) {

	// 	}
	// }

	if (is_shop() ||is_product() || is_product_category() || is_product_tag()  ) {

		get_template_part('_partials/placeholder/placeholder', 'forward' ); 

	} else if ( is_user_logged_in() ) {
		if ( cc_user_is_guest() && ( is_page( array( 7 ) ) || is_admin() && !cc_can_see_admin() )) {

			get_template_part('_partials/placeholder/placeholder', 'forward' ); 

		} else if ( is_page( array( 11 ) ) || (is_admin() && in_array( array('shop_manager','administrator')) ) ) {

			get_template_part('_partials/placeholder/placeholder', 'forward' ); 

		}
	} else {
		if ( !is_home() && !is_page(array( 9, 26, 30, 363, 11, 7, 6 )) ) {

			get_template_part('_partials/login','modal');

		} 
	}

	do_action('cc_remove_membership_items');
?>

<!DOCTYPE html>

<html class="header-closed image-closed">

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
	
	<link rel="apple-touch-icon"href="/apple-touch-icon.png">	
	<link rel="shortcut icon" href="<?php bloginfo('template_directory'); ?>/_/img/favicon.ico">
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
			
    <!--[if lt IE 9]>
      <script src="<?php bloginfo('template_directory'); ?>/_/js/html5shiv.js"></script>
      <script src="<?php bloginfo('template_directory'); ?>/_/js/respond.js"></script>
    <![endif]-->		
    
	<link href='http://fonts.googleapis.com/css?family=Muli:300,400,300italic,400italic' rel='stylesheet' type='text/css'>
        	
	<?php wp_head(); ?>
				
</head>

<body <?php body_class('before'); ?>>

<?php // get_template_part('landing'); ?>

<?php get_template_part('ie'); ?>

<div id="state" class="loading">

	<div id="background" class="<?php  if ( is_home()) : echo 'background-home'; endif; ?>
"></div>
	
		<header id="header" class="off">

			<nav id="nav">
			
				<div class="container">	
					<div class="row">	
					
						<div class="nav-left hidden-xs col-sm-4">
												
							<ul>	
															
								<li>
									<a href="<?php bloginfo('url'); ?>/look-book">
										Fall 2014 Look Book
									</a>
								</li>	
								<li>
									<a href="<?php bloginfo('url'); ?>/how-it-works">
										How it Works
									</a>
								</li>	
								
								<?php if ( is_home() && !is_user_logged_in() ) : ?>
								<li>
									<a href="<?php bloginfo('url'); ?>/join" class="">
										Become a Member
									</a>
								</li>							
								<?php endif; ?>
							</ul>
							
						</div>
						
						<div id="carrot" class="menu-toggle visible-xs">
							<a href="#menu">Menu</a>
						</div>						
									
						<a id="logo" class="logo col-sm-4" href="<?php bloginfo('url'); ?>">
						
							<img id="logo-whole" class="hidden-xs" src="<?php bloginfo('template_directory'); ?>/_/img/logo.png" alt="logo">
						
							<img id="logo-type" class="visible-xs" src="<?php bloginfo('template_directory'); ?>/_/img/mark.png" alt="logo">			
											
						</a>		
							
						<div class="nav-right col-sm-4 hidden-xs">
							
						 	<?php if ( is_user_logged_in() ) { 
							 	global $current_user;
							 	get_currentuserinfo();
							 	global $woocommerce;
							 	
							 	 ?>
									<ul class="right-logged-in <?php if ( cc_user_is_guest() ) : echo 'hidden'; endif; ?>">
										<li class="">
											<a href="<?php bloginfo('url'); ?>/cart" id="cart-link" class="<?php if($woocommerce->cart->cart_contents_count): echo 'active'; endif?>
">
												<?php get_template_part('_icons/cart'); ?>
												<?php if($woocommerce->cart->cart_contents_count): echo '<span class="cart-count">(' . $woocommerce->cart->cart_contents_count . ')</span>'; endif?>
											</a>
										</li>	
										<li class="">
											<a href="<?php bloginfo('url'); ?>/closet">
												My Closet
											</a>
										</li>	
										<li class="dropdown my-account-item">
											<a href="<?php bloginfo('url'); ?>/my-account">
												My Account<span class="icon icon-right" data-icon="&#8221;"></span>
											</a>
	 
											<ul class="dropdown-menu" role="menu">
												<li><a href="<?php bloginfo('url'); ?>/my-account">Settings</a></li>
												<li><a href="<?php echo wp_logout_url( home_url() ); ?>" title="Logout">Logout</a></li>
											</ul>
	
										</li>										
									
									</ul>
			
							<?php } else{ 
									if(is_home()){ ?>
									
									<div id="header-login">
										
										<?php get_template_part('_partials/login'); ?>
										
									</div>							
										
									<? }
									else{ ?>
										
										<ul class="right-logged-out">
											<li>
												<a href="<?php bloginfo('url'); ?>/login">
													Login
												</a>
											</li>	
											<li>
												<a href="<?php bloginfo('url'); ?>/join">
													Become a Member
												</a>
											</li>										
										
										</ul>									
									
									<?} } ?> 
												
							</div>							
									
								
						</div>							
					</div>	
				
			</nav>	
			
			<div id="menu-xs" class="menu-xs closed">
				<ul>								
					<li>
						<a href="<?php bloginfo('url'); ?>/look-book">
							Fall 2014 Look Book
						</a>
					</li>	
					<li>
						<a href="<?php bloginfo('url'); ?>/how-it-works">
							How it Works
						</a>
					</li>	
					
					<?php if ( is_user_logged_in() ) : ?>
					<li>
						<a href="<?php bloginfo('url'); ?>/my-account">
							My Account
						</a>		
					</li>
					<li>
						<a href="<?php bloginfo('url'); ?>/closet">
							My Closet
						</a>		
					</li>					
					
					<li><a href="<?php echo wp_logout_url( home_url() ); ?>" title="Logout">Logout</a></li>
							
					<?php else: ?>
					<li>
						<a href="<?php bloginfo('url'); ?>/join">
							Become a Member
						</a>		
					</li>					
					<?php endif; ?>
				</ul>						
			</div>	

			<?php if ( is_user_logged_in() ) :
			 	global $current_user;
			 	get_currentuserinfo();
			 	
			 	if(current_user_can( 'manage_options' )): ?>
			 		<div id="admin-login" class="hidden-xs"><a href="<?php bloginfo('url'); ?>/wp-admin"><span class="icon" data-icon="("></span></a></div>

			 <?php endif; endif; ?>			
			
		</header>

	<div id="headerfix"></div>
		
	<div id="content" class="">
