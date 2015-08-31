<?php

$split = CC_Controller::get_trunkshows_by_date_pivot( date('Ymd') );

?>

<?php get_header();?>

<div id="trunk-shows" class="template template-page">	

	<hr class="page-header-rule hidden"/>						

	<section id="trunk-shows" class="trunk-shows block">	
		
		<div class="container">



			<? if ( !empty( $split[0] ) ) : ?>

				<div class="row">
					
					<div class="col-sm-12">
					
						<h4 class=" bordered centered m25">Upcoming Events</h4>
												
						<p class="h3 centered m25">
						Please email <a href="mailto:info@couturecollective.club">info@couturecollective.club</a> to schedule an appointment for a private showing or arrange for a custom selection to be sent to you to try.
						</p>
										
					</div>
					
				</div>

				<div class="row upcoming-events">

					<?php foreach ($split[0] as $show) { ?>
					
					<?php 
						
						$GLOBALS['SHOW'] = $show;

						get_template_part('_partials/trunkshow/trunkshow', 'card');

						unset( $GLOBALS['SHOW'] ); 

					?>
	
					<? } ?>

				</div>

			<?php endif; ?>



			<? if ( !empty( $split[1] ) ) : ?>

				<div class="row">
					
					<div class="col-sm-12">
					
						<h4 class="bordered centered m25">Past Events</h4>
										
					</div>
					
				</div>

				<div class="row past-events">

					<?php foreach ($split[1] as $show) { ?>
					
					<?php 
						
						$GLOBALS['SHOW'] = $show;

						get_template_part('_partials/trunkshow/trunkshow', 'card');

						unset( $GLOBALS['SHOW'] ); 

					?>
	
					<? } ?>
					
				</div>

			<? endif; ?>

			

		</div>

	</section>

	
	
</div>	

<?php get_footer(); ?>
