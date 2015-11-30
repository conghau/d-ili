<?php defined("APP") or die() ?>
<section id="login">
  <div class="container">    
    <div class="centered form panel panel-default">      
      <form role="form" id="login_form" method="post" action="<?php echo Main::href("user/register")?>"> 
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
            <label for="email"><?php echo e("Email") ?>  </label>
            <input type="text" class="form-control" id="email" placeholder="<?php echo e("Enter email") ?>" name="email">
          </div>
          <div class="form-group">
            <label for="username"><?php echo e("Username") ?>  </label>
            <input type="text" class="form-control" id="username" placeholder="<?php echo e("Enter username") ?>" name="username">
          </div>        
          <div class="form-group">
            <label for="pass"><?php echo e("Password")?></label>
            <input type="password" class="form-control" id="pass" placeholder="<?php echo e("Password") ?>" name="password">
          </div>   
          <div class="form-group">
            <label for="cpass"><?php echo e("Confirm Password")?></label>
            <input type="password" class="form-control" id="cpass" placeholder="<?php echo e("Password") ?>" name="cpassword">
          </div>  
          <div class='form-group'>
            <label for='country' class='control-label'><?php echo e("Country") ?></label>
            <select name='country' class="form-control" id='country'>
              <?php echo Main::countries() ?>
            </select>
          </div>         
          <div class="form-group">
            <?php echo Main::captcha() ?>       
          </div>
          <div class="form-group">
            <label>
                <input type="checkbox" name="terms" value="1" data-class="blue">  
                <span class="check-box"><?php echo e("I agree to the")?>
                  <a href="<?php echo Main::href("page/terms") ?>"><?php echo e("terms and conditions") ?></a>
                </span>
            </label>
          </div>           
          <?php echo Main::csrf_token(TRUE) ?>
          <button type="submit" class="btn btn-primary"><?php echo e("Register")?></button>
        </div>        
      </form>      
    </div>
  </div>
</section>