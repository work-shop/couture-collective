<form name="small-login" id="small-login" class="col-sm-4 right" action="http://localhost:8888/coco/wp-login.php" method="post">
  <p class="login-username">
    <input type="text" name="log" id="user_login" class="input" value="" placeholder="USERNAME" size="20">
  </p>
  <p class="login-password">
    <input type="password" name="pwd" id="user_pass" class="input" value="" placeholder="PASSWORD" size="20">
  </p>
  <p class="login-submit">
    <input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="Log In">
    <input type="hidden" name="redirect_to" value="http://localhost:8888/coco/"><!-- BAD -->
  </p>
  
</form>