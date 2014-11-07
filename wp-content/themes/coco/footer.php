
	</div><!--/#content-->
	
	<footer id="footer">
	
		<div class="container">
			<div class="row">
			
				<div class="col-sm-12">
					<nav id="footer-nav" class="">
						<ul class="left">
							<li><?php edit_post_link('Edit This Page'); ?> </li>
						</ul>
						<ul class="right">
							<li><a href="<?php bloginfo('url'); ?>/contact"  class="">Contact</a></li>
							<li><a href="<?php bloginfo('url'); ?>/FAQ" class="">FAQ</a></li>				
							<li><a href="<?php bloginfo('url'); ?>/terms-and-privacy" class="">Terms & Privacy</a></li>				
						</ul>
					</nav>
				</div>
			</div>
		</div>
	
	</footer>
  
</div><!-- /#state -->

<div id="foot" class="hidden">

	<script type="text/javascript">
				  
		  //add this setup
		  var addthis_config = {"data_track_addressbar":true}; 
		  var addthis_config = {"data_track_clickback":true};
	
	</script> 

	<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-50f20b8a658458ce">
	</script>		 		

	<script type="text/javascript">
	     less.env = "production"; less.watch();
	</script>

</div>

<?php wp_footer(); ?>

</body>

</html>