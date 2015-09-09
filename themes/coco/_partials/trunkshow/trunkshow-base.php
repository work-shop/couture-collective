<?php

$trunkshow = $GLOBALS['TRUNKSHOW'];

?>



<div class="col-xs-12 col-sm-12 m1">
	<div class="row">
		<div class="col-sm-12">
			<p class="h11 numerals">
				<?php echo ws_render_date( get_field('trunk_show_date', $trunkshow->ID ) ); ?>
				<?php
					if ( $end = get_field('trunk_show_date_end', $trunkshow->ID ) ) {
						echo " – " . ws_render_date( $end );
					}
				?>
			</p>
		</div>										
		<div class="col-sm-12 hidden">
			<a href="<?php echo get_the_permalink( $trunkshow->ID ); ?>"><p class="h7 uppercase bold"><?php echo $trunkshow->post_title; ?></p></a>
		</div>
	</div>
	<div class="row">	
		<div class="col-sm-12">
			<div class="bordered-dark-bottom"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">

		<?php
		
			echo apply_filters('the_content', $trunkshow->post_content );

		?>

		</div>
	</div>
</div>