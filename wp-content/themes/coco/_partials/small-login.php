<form name="small-login" id="small-login" class="col-sm-4 right" action="http://localhost:8888/coco/wp-login.php" method="post">  
  <?php global $wp; $url = home_url(add_query_arg(array(),$wp->request)); ?>

  <p class="login-username">
    <input type="text" name="log" id="user_login" class="input" value="" placeholder="USERNAME" size="20">
  </p>
  <p class="login-password">
    <input type="password" name="pwd" id="user_pass" class="input" value="" placeholder="PASSWORD" size="20">
  </p>
  <p class="login-submit">
    <input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="Log In">
    <input type="hidden" name="redirect_to" value="<?php echo $url; ?>">
  </p>
  
</form>