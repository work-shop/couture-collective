<?php get_template_part('_partials/dress/dress', 'setup-postdata'); ?>

<div class="col-sm-6 left">
<?php get_template_part('_partials/dress/dress', 'gallery'); ?>
</div>

<div class="col-sm-6 right">
<?php	
	get_template_part('_partials/dress/dress', 'meta');
	get_template_part('_partials/dress/dress', 'calendar' );

	if ( is_user_logged_in() ) {

		get_template_part('_partials/dress/dress', 'logic');

	} else {

		get_template_part('_partials/dress/dress', 'login');
		get_template_part('_partials/dress/dress', 'join');

	}
?>
</div>

<?php get_template_part('_partials/dress/dress', 'teardown-postdata'); ?>