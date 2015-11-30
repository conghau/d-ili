<?php defined("APP") or die() ?>
<section id="login">
  <div class="container">    
    <div class="centered form panel panel-default">      
      <form role="form" id="login_form" method="post" action="<?php echo Main::href("user/login")?>"> 
        <?php if(!$this->config["maintenance"] && $this->config["user"] && ($this->config["fb_connect"] || $this->config["tw_connect"] || $this->config["gl_connect"])):?>
          <div class="social">
            <?php if($this->config["fb_connect"]):?>
            <a href="<?php echo $this->config["url"]?>/user/login/facebook" class="btn btn-facebook"><?php echo e('Login with Facebook')?></a>
            <?php endif;?>
            <?php if($this->config["tw_connect"]):?>
            <a href="<?php echo $this->config["url"]?>/user/login/twitter" class="btn btn-twitter"><?php echo e('Login with Twitter')?></a>
            <?php endif;?>
            <?php if($this->config["gl_connect"]):?>
            <a href="<?php echo $this->config["url"]?>/user/login/google" class="btn btn-google"><?php echo e('Login with Google')?></a>
            <?php endif;?>          
          </div>
        <?php endif;?>
        <div class="panel-body">
          <div class="form-group">
            <label for="email"><?php echo e("Email or username") ?>  
              <?php if($this->config["user"] && !$this->config["maintenance"]): ?>
                <a href="<?php echo Main::href("user/register")?>" class="pull-right">(<?php echo e("Create account")?>)</a>
              <?php endif ?>
            </label>
            <input type="text" class="form-control" id="email" placeholder="<?php echo e("Enter email") ?>" name="email">
          </div>
          <div class="form-group">
            <label for="pass"><?php echo e("Password")?> <a href="#forgot" class="pull-right" id="forgot-password">(<?php echo e("Forgot Password")?>)</a></label>
            <input type="password" class="form-control" id="pass" placeholder="<?php echo e("Password") ?>" name="password">
          </div>         
          <div class="form-group">
            <label>
                <input type="checkbox" name="rememberme" value="1" data-class="blue">  
                <span class="check-box"><?php echo e("Remember me")?></span>
            </label>
          </div>                  
          <?php echo Main::csrf_token(TRUE) ?>
          <button type="submit" class="btn btn-primary"><?php echo e("Login")?></button>
        </div>
      </form>  

      <form role="form" class="live_form" id="forgot_form" method="post" action="<?php echo Main::href("user/forgot")?>">
        <div class="panel-body">
          <div class="form-group">
            <label for="email1"><?php echo e("Email address")?></label>
            <input type="email" class="form-control" id="email1" placeholder="<?php echo e("Enter email") ?>" name="email">
          </div>        
          <?php echo Main::csrf_token(TRUE) ?>
          <button type="submit" class="btn btn-primary"><?php echo e("Reset Password")?></button>
          <a href="<?php echo Main::href("user/login") ?>" class="pull-right">(<?php echo e("Back to login")?>)</a>          
        </div>
      </form>        
    </div>
  </div>
</section>