<form name="small-login" action="<?php bloginfo('url'); ?>/wp-login.php" method="post">  

<?php do_action( 'woocommerce_login_form_start' ); ?>

  <?php global $wp; $url = home_url(add_query_arg(array(),$wp->request)); ?>
  <p class="login-username">
    <input type="text" name="log" id="user_login" class="input" placeholder="USERNAME" >
  </p>
 <p class="h10 m"><?php 

      if ( ws_eq_get_var('login','failed') ) {
        echo "The username or password you entered is incorrect. Please try again. Your username and password were emailed to you. If you are having trouble, please email us at info@couturecollective.club";
      } else if ( ws_eq_get_var('login','pending') ) {
        echo "Thank you for your submission - you will be notified by email when your account is approved.";
      }

  ?></p> 

  <?php if ( !ws_eq_get_var('login','pending') ) : ?>

    <p class="login-password <?php echo ( ws_eq_get_var('login','failed') ) ? "red failed": "";?>">
      <input type="password" name="pwd" id="user_pass" class="input" value="" placeholder="PASSWORD" >
    </p>
    
    <?php do_action( 'woocommerce_login_form' ); ?>

    <p class="login-submit">
      <input type="submit" name="wp-submit" id="wp-submit" class="" value="LOG IN">
      <input type="hidden" name="redirect_to" value="<?php echo home_url().'/look-book'; ?>" >
    </p>  

    <?php do_action( 'woocommerce_login_form_end' ); ?>

  <?php endif; ?>

</form>	