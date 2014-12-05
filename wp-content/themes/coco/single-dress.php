<?php get_header();?>
	
<?php wc_print_notices(); ?>

<article id="dress-<?php the_ID(); ?>" class="template dress">	
<div class="row">

<?php

	get_template_part('_partials/dress/dress', 'base');	

?>

</div>
</article>	

<?php get_footer(); ?>
