	<?php  if ( !is_user_logged_in() ) : ?>
			
		<div class="modal fade" id="look-book-modal" role="dialog" aria-hidden="true" data-keyboard="false" data-backdrop="static">
		    <div class="modal-dialog">
		      <div class="modal-content">	      
		        <div class="modal-login">
		        	<div class="row">
		        		<div class="col-sm-8 col-sm-offset-2">	        		
		        			<h5 class="centered m">Guest Login</h5>
							<?php get_template_part('_partials/login'); ?>
						</div>
		        	</div>		        
				</div>	
		        <div class="modal-invite">
		        	<div class="row">
		        		<div class="col-sm-8 col-sm-offset-2">
		        		
		        			<hr class="brand" />

							<h5 class="centered m">Want a peek in our closet?</h5>
		          
							<h6 class="centered m">Contact us at the email below to request a temporary password.</h6>
			
							<a href="mailto:info@couturecollective.club" target="_blank" class="h7 centered m bg-pink-darker display-block contact-link white">info@couturecollective.club</a>
							
		        			<hr class="brand" />							
							
							<a href="<?php bloginfo('url'); ?>/how-it-works" class="h7 centered m display-block">Learn More about Couture Collective</a>
								
							
		        		</div>
		        	</div>	          	        
				</div>					
		      </div><!-- /.modal-content -->
		    </div><!-- /.modal-dialog -->
		  </div><!-- /.modal -->	
	
	<?php endif; ?>

