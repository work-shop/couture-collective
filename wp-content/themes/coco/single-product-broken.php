<?php get_header();?>
		
<div id="post" class="template post">	

	<div class="container">
		<div class="row">
		
			<div class="col-sm-12">
				<h1><?php the_title(); ?></h1>
			</div>
			
		</div>
		
	</div>		
	
	<?php the_content(); ?>	
	
</div>	

<?php get_footer(); ?>
