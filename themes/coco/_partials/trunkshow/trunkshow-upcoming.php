<?php

$current_id = $GLOBALS['CURRENT_ID'];
$upcoming_shows = $GLOBALS['UPCOMING'];

?>


	<div class="row">										
		<div class="col-sm-12">
			<a href="<?php bloginfo('url') ?>/shows"><p class="h7 uppercase bold ">Upcoming Shows</p>
		</div>
	</div>
	<div class="row">	
		<div class="col-sm-12">
			<div class="bordered-dark-bottom m2"></div>
		</div>
	</div>
	<ul class="row">

	<?php foreach ($upcoming_shows as $show) : ?>
	
		<li class="col-sm-12 m2">
			<?php if ( $show->ID == $current_id ) { ?>

				<p class="h7 uppercase bold"><?php echo $show->post_title; ?></p>
				<p class="h11 numerals">
					<?php echo ws_render_date( get_field('trunk_show_date', $show->ID ) ); ?>
					<?php
						if ( $end = get_field('trunk_show_date_end', $show->ID ) ) {
							echo " – " . ws_render_date( $end );
						}
					?>
				</p>

			<?php } else { ?>

				<a href="<?php echo get_the_permalink( $show->ID ); ?>">
					<p class="h7 uppercase"><?php echo $show->post_title; ?></p>
					<p class="h11 numerals">
						<?php echo ws_render_date( get_field('trunk_show_date', $show->ID ) ); ?>
						<?php
							if ( $end = get_field('trunk_show_date_end', $show->ID ) ) {
								echo " – " . ws_render_date( $end );
							}
						?>
					</p>
				</a>

			<?php } ?>
		</li>

	<?php endforeach; ?>

	</ul>