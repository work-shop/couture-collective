
<?php get_header();?>

<div id="look-book" class="template template-page">	
	
	<section id="look-book-introduction" class="look-book-introduction block">	
	
		<div class="filters">

			<div class="container">	
		
				<ul>
					<li class="key">Show: </li>
					<li><a href="#" class="active">ALL</a></li>
					<li><a href="#" class="">Available Tomorrow</a></li>
					<li><a href="#" class="">Shares Available</a></li>								
				</ul>
			
			</div>
		
		</div>
		
		
		<div class="products">
			<div class="container">
				<div class="row">
			<?php
				$args = array(
					'post_type' => 'product',
					'posts_per_page' => 12
					);
				$loop = new WP_Query( $args );
				
				if ( $loop->have_posts() ) {
					while ( $loop->have_posts() ) : $loop->the_post(); ?>
						<?php 
						$product = new WC_Product( get_the_ID() );
						$rent_price = $product->get_sale_price();
						$share_price = $product->price;
						?>
						
						<div class="col-sm-3 col-md-2 col-xs-6 product available available-tomorrow available-shares">
							<a href="<?php the_permalink(); ?>">
								
								<div class="product-image">
									<?php 
									if ( has_post_thumbnail() ) {
										the_post_thumbnail();
									}
									else {
										echo '<img src="' . get_bloginfo( 'template_directory' ) . '/_/img/thumbnail-default.jpg" />';
									}
									?>							
								</div>
								
						
								<div class="product-summary centered">
									<h4 class="product-summary-title bold uppercase"><?php the_title(); ?></h4>
									<h4 class="product-summary-subtitle">Lorem Ipsum Dolor Sit</h4>
									<h4 class="product-summary-rent">
										<a href="/something">Rent: <span class="bold">$<?php echo $rent_price; ?></span></a></h4>
									<h4 class="product-summary-share">
										<a href="/something">Share: <span class="bold">$<?php echo $share_price?></span></a>
									
									
								</div>
							
							</a>
						</div>
						
					<?php endwhile;
				} else { ?>
					<h1>The Look Book is currently empty. Please check back soon. </h1>
				<? }
				wp_reset_postdata();
			?>
				</div>
			</div>
		</div>
			
								
	</section>	
	
</div>	

<?php get_footer(); ?>
