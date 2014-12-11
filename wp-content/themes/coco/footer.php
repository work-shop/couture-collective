
	</div><!--/#content-->
	
	<footer id="footer" class="bg-pink">
	
		<div class="container">
			<div class="row">
				
				<div class="col-sm-12">
					<nav id="footer-nav">
						<?php 
							$args = array(
								'theme_location' => 'footer-menu', 
								'container' => false,
								'menu_class' => 'centered',
							);
							wp_nav_menu( $args ); 
						?>
					</nav>
				</div>
			</div>
		</div>
	
	</footer>
  
</div><!-- /#state -->

<div id="foot" class="hidden">	 		

	<script type="text/javascript">
	</script>

</div>

<?php wp_footer(); ?>

</body>

</html>