<div class="col-xs-12 col-sm-8">
	<div class="row">										
		<div class="col-sm-12">
			<p class="h7 uppercase bold"><?php the_title(); ?></p>
		</div>
	</div>
	<div class="row">	
		<div class="col-sm-12">
			<div class="bordered-dark-bottom m2"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">

		<?php
		
		global $post;
		echo apply_filters('the_content', $post->post_content );

		?>

		</div>
	</div>
</div>