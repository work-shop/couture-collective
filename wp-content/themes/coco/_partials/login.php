<form name="small-login" action="<?php bloginfo('url'); ?>/wp-login.php" method="post">  
  <?php global $wp; $url = home_url(add_query_arg(array(),$wp->request)); ?>
  <p class="login-username">
    <input type="text" name="log" id="user_login" class="input hidden" value="guest" placeholder="USERNAME" >
  </p>
 <p class="h10 m"><?php echo ( ws_eq_get_var('login','failed') ) ? "The password you entered is incorrect. Please try again. Your temporary password was emailed to you. If you are having trouble, please email us at info@couturecollective.club": "";?></p> 
  <p class="login-password <?php echo ( ws_eq_get_var('login','failed') ) ? "red failed": "";?>">
    <input type="password" name="pwd" id="user_pass" class="input" value="" placeholder="PASSWORD" >
  </p>
  
  <p class="login-submit">
    <input type="submit" name="wp-submit" id="wp-submit" class="" value="LOG IN">
    <input type="hidden" name="redirect_to" value="<?php echo home_url().'/look-book'; ?>" >
  </p>  
</form>	