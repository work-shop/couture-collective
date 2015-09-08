
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
						<?php /*if ( get_field('sneak_peak_active', 'option') ) : ?>
							<a href="<?php bloginfo('url'); ?>/sneak-peak"><h4 class="centered white tagline">
								<?php echo get_field('sneak_peak_heading', 'option'); ?>
							</h4></a>
						<?php endif; */ ?>
						
						<div class="nav-home visible-xs">
												
							<ul>	
															
								<li>
									<a class="white h7 uppercase"  href="<?php bloginfo('url'); ?>/look-book">
										Look Book
									</a>
								</li>	

								<?php if ( is_user_logged_in() ) : ?>
								<li>
									<a class="white h7 uppercase"  href="<?php bloginfo('url'); ?>/shows">
										Upcoming Shows
									</a>
								</li>	
								<?php endif; ?>
								

								<?php if ( get_field('sneak_peak_active', 'option') ) : ?>
								<li>
									<a class="white h7 uppercase"  href="<?php bloginfo('url'); ?>/sneak-peek">
										<?php echo get_field('sneak_peak_heading', 'option'); ?>
									</a>
								</li>	
								<?php endif; ?>

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
									<?php if ( !cc_user_is_guest() ) { ?>
										<li>
											<a class="white h7 uppercase" href="<?php bloginfo('url'); ?>/my-account">
												My Account
											</a>		
										</li>	
									<?php } else { ?>
										<li>
											<a class="white h7 uppercase" href="<?php echo wp_logout_url( home_url() ); ?>">
												Logout
											</a>	
										</li>	
									<?php } ?>														
								<?php endif; ?>
							</ul>
							
						</div>						
					</div>
				
				</div>				
			
			</div>	
					
	</section>	
			
</div>	

<?php get_footer(); ?>
