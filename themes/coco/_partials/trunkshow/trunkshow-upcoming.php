<?php

$upcoming_shows = CC_Controller::get_upcoming_trunkshows();

?>

<div class="col-xs-12 col-sm-4">
	<div class="row">										
		<div class="col-sm-12">
			<a href="<?php bloginfo('url') ?>/trunk-shows"><p class="h7 uppercase bold">Upcoming Shows</p>
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
			<?php if ( $show->ID == get_the_ID() ) { ?>

				<p class="h7 uppercase bold"><?php echo $show->post_title; ?></p>
				<p class="h11 uppercase brand"><?php echo ws_render_date( get_field( 'trunk_show_date', $show->ID ) ); ?></p>

			<?php } else { ?>

				<a href="<?php echo get_the_permalink( $show->ID ); ?>">
					<p class="h7 uppercase"><?php echo $show->post_title; ?></p>
					<p class="h11 uppercase brand"><?php echo ws_render_date( get_field( 'trunk_show_date', $show->ID ) ); ?></p>
				</a>

			<?php } ?>
		</li>

	<?php endforeach; ?>

	</ul>
</div>