
	</div><!--/#content-->
	
	
	
	<?php if(!is_home()): get_template_part('signpost'); endif; ?>
	
	<?php get_template_part('invitation'); ?>
	
	<?php get_template_part('contact'); ?>
  
</div><!-- /#state -->

<div id="foot" class="hidden">

	<script type="text/javascript">
		//get fonts
		WebFontConfig = { fontdeck: { id: '37770' } };
		
		(function() {
		  var wf = document.createElement('script');
		  wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
		  '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
		  wf.type = 'text/javascript';
		  wf.async = 'true';
		  var s = document.getElementsByTagName('script')[0];
		  s.parentNode.insertBefore(wf, s);
		})();	
	
		//google analytics
		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-43897729-1']);
		  _gaq.push(['_trackPageview']);
		
		  (function() {
		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();
		  
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