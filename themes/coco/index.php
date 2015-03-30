
<?php get_header();?>

<div id="home" class="template home">	
	<!-- This is pushed to the new environment : 3/30/15 -->
	<section id="home-introduction" class="home-introduction block fit">
	
			<div class="container">
			
				<div class="row m3" id="logo-large">
				
					<div class="col-sm-8 col-sm-offset-2">
					
						<img src="<?php bloginfo('template_directory'); ?>/_/img/logo-large-white.png">
						
					</div>
				
				</div>
				
				<div class="row m3">
				
					<div class="col-sm-8 col-sm-offset-2">
						<h4 class="centered white tagline">Don't Buy Couture.<br class="visible-xs"/> <span class="italic">Share It.</span></h4>
						
						<div class="nav-home visible-xs">
												
							<ul>	
															
								<li>
									<a class="white h7 uppercase"  href="<?php bloginfo('url'); ?>/look-book">
										Fall 2014 Look Book
									</a>
								</li>	
								<li>
									<a class="white h7 uppercase"  href="<?php bloginfo('url'); ?>/how-it-works">
										How it Works
									</a>
								</li>	
								
								<?php if ( !is_user_logged_in() ) : ?>
								<li>
									<a class="white h7 uppercase hidden"  href="<?php bloginfo('url'); ?>/join" class="hidden">
										Become a Member
									</a>
								</li>	
																		
								<?php get_template_part('_partials/login'); ?>
								
								<?php else: ?>
								<li>
									<a class="white h7 uppercase" href="<?php bloginfo('url'); ?>/my-account">
										My Account
									</a>		
								</li>															
								<?php endif; ?>
							</ul>
							
						</div>						
					</div>
				
				</div>				
			
			</div>	
					
	</section>	
			
</div>	

<?php get_footer(); ?>
