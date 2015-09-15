<li class="<?php if ( ! $GLOBALS['CC_POST_DATA']['active']) : ?>active<?php endif; ?> tab">
<?php


	get_template_part( '_partials/dress/dress', 'rental-make' );
	get_template_part('_partials/dress/dress', 'rental-history' );


?>
</li>