<?php get_header();?>

<?php

$split = CC_Controller::get_trunkshows_by_date_pivot( date('Ymd') );

?>	

<div id="trunkshow-archive" class="template template-page template-single">	

		<div class="container">	
			<div class="row">

				<div class="col-sm-10 col-sm-offset-1 wc-notices">
					<?php wc_print_notices(); ?>
				</div>

			</div>

			<div class="row">
				<?php if ( !isset( $split[0] ) ) : ?>

					<div class="col-sm-12 col-xs-12 p0">

						<hr class="page-header-rule"/>

						<h2 class="centered">There are no upcoming shows at the moment.</h2>

					</div>

				<?php else: ?>

					<div class="col-sm-8 col-xs-12 p0">

						<?php 

						
						foreach ( $split[0] as $trunkshow ) {

							$GLOBALS['TRUNKSHOW'] = $trunkshow;

							get_template_part('_partials/trunkshow/trunkshow', 'base'); 

							unset( $GLOBALS['TRUNKSHOW'] );

						}
						

						?>

					</div>

					<div class="col-sm-4 col-xs-12">

						<?php 

						$GLOBALS['UPCOMING'] = $split[0];
						$GLOBALS['CURRENT_ID'] = -1;

						get_template_part('_partials/trunkshow/trunkshow', 'upcoming'); 

						unset( $GLOBALS['UPCOMING'] );
						unset( $GLOBALS['CURRENT_ID'] );

						?>

					</div>

				<?php endif; ?>

			</div>
		</div>

</div>

<?php get_footer(); ?>
