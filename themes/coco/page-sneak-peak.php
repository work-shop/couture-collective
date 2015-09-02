<?php if ( get_field('sneak_peak_active', 'option') ) { ?>

<?php

$sneak_peak_description = get_field('sneak_peak_description', 'option');
$sneak_peak_dresses = get_field('sneak_peak_images', 'option');

?>

<?php get_header();?>

<div id="contact" class="template template-page">	
		
	<section id="sneak-peak-introduction" class="page-introduction block m2">	
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<h4 class="bordered serif centered m2"><?php echo get_field('sneak_peak_heading', 'option'); ?></h4>
				</div>
				<div class="col-sm-12 centered">
					<?php echo get_field('sneak_peak_description', 'option'); ?>
				</div>
			</div>
		</div>
	</section>
	
	<section id="dress-masonry" class="block m3">	
		<div class="container">
			<div class="row">
				<div id="dress-masonry-container" class="col-sm-12">
					<div class="row">
					<?php

						foreach ( $sneak_peak_dresses as $dress ) {

							$GLOBALS['dress'] = $dress;

							get_template_part('_partials/sneakpeak/sneakpeak', 'card');

							unset( $GLOBALS['dress'] );			

						}

					?>
					</div>
				</div>
			</div>	
		</div>
	</section>	

	<script type="text/javascript"></script>
	
</div>	

<?php get_footer(); ?>

<?php } else { get_template_part('_partials/placeholder/placeholder', 'forward'); } ?>