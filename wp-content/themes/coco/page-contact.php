
<?php get_header();?>

<div id="contact" class="template template-page">	
	
	<section id="contact-introduction" class="page-introduction block m2">	
	
		<hr class="page-header-rule"/>					

		<div class="container">
				
			<div class="row">
			
				<div class="col-sm-10 col-sm-offset-1">
				
					<h1 class="serif centered m">Contact Us</h1>
					
					<h2 class="centered m2">Please direct all inquiries to the below address, or submit a message directly from this page.</h2>
									
				</div>
				
				<div class="col-sm-6 col-sm-offset-3">
				
					<a href="mailto:info@couturecollective.club" target="_blank" class="h7 centered m2 bg-pink-darker display-block contact-link white">info@couturecollective.club</a>
														
				</div>				

			</div>
			
		</div>
	</section>
	
	<section id="contact-form" class="block m3">	
		<div class="container">
			
			<div class="row">
			
				<div class="col-sm-6 col-sm-offset-3" id="contact-form-container">
					<?php gravity_form(1, $display_title=false, $display_description=false, $display_inactive=false, $field_values=null, $ajax=true); ?>
				</div>
					
			</div>
								
		</div>			
	</section>	
	
</div>	

<?php get_footer(); ?>
