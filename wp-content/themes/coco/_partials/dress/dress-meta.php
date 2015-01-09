
	<div class="bordered-dark-top">
		<?php
		
			$owned = wc_customer_bought_product(
				$GLOBALS['CC_POST_DATA']['user']->user_email,
				$GLOBALS['CC_POST_DATA']['user']->ID,
				$GLOBALS['CC_POST_DATA']['share']->id
			);

			$id = $GLOBALS['CC_POST_DATA']['id'];
			$user = $GLOBALS['CC_POST_DATA']['logged_in'];
	
			$title = $GLOBALS['CC_POST_DATA']['title'];
			$description = $GLOBALS['CC_POST_DATA']['description'];
			$designer = $GLOBALS['CC_POST_DATA']['designer'];
	
			$size = $GLOBALS['CC_POST_DATA']['size'];


			// int representing the number of prereservations this user has left for this dress.
			$remaining_preresevations = CC_Controller::$maximum_prereservations - count( $GLOBALS['CC_POST_DATA']['prereservations'] );


			echo ws_ifdef_do( $designer, ws_ifdef_concat('<h1 class="uppercase dress-designer">',$designer,'</h1>') );
			echo ws_ifdef_do( $title, ws_ifdef_concat('<h6 class="dress-description">',$description,'</h6>') );
	
			echo ws_ifdef_do( $user, ws_ifdef_do( $size, ws_ifdef_concat('<p class="h7">SIZE: <span class="numerals h8">',$size,'</span></p>') ) );
			
			if( $owned ){ ?>
	
				<hr class="brand half" />
	
				<p class="h7 uppercase">
				<a href="<?php bloginfo('url'); ?>/closet" class="h7">
				<span class="icon svg"><?php get_template_part('_icons/delivery'); ?></span> 
				In My Closet
				</a>
				</p>
				
				<p class="remaining-reservations h7 uppercase">
				<?php 
				if($remaining_preresevations > 0){
					echo $remaining_preresevations;?> pre-reservations remaining
					<?php } else{ ?>
						<p class="h8">This dress has been pre-reserved the maximum number of times. You may delete an order and book a new one, or <a href="<?php bloginfo('url');?>/contact" target="_blank" class="underline">contact us</a> to change your order. </p>
					<?php } ?>
					
				
				<hr class="brand" />

			<?php } else{ ?>
			
				<hr class="brand" />			
			
			<?php }?>
				
	</div>
