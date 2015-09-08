<?php get_header();?>
	

<div id="dress-single" class="template template-page template-single">	

	<article id="trunkshow-<?php the_ID(); ?>" class="template trunkshow m3">
		<div class="container">	
			<div class="row">
			
				<div class="col-sm-10 col-sm-offset-1 wc-notices">
					<?php wc_print_notices(); ?>
				</div>

			</div>

			<div class="row">
				<div class="col-sm-8 col-xs-12 p0">
					<?php

						global $post;
						$GLOBALS['TRUNKSHOW'] = $post;

						get_template_part('_partials/trunkshow/trunkshow', 'base');

						unset( $GLOBALS['TRUNKSHOW'] );

					?>

				</div>

				<div class="col-sm-4 col-xs-12">
					<?php

						$GLOBALS['UPCOMING'] = CC_Controller::get_upcoming_trunkshows();
						$GLOBALS['CURRENT_ID'] = $post->ID;

						get_template_part('_partials/trunkshow/trunkshow', 'upcoming');

						unset( $GLOBALS['UPCOMING'] );
						unset( $GLOBALS['CURRENT_ID'] );

					?>
				</div>

			</div>
		</div>
	</article>	

</div>

<?php get_footer(); ?>
