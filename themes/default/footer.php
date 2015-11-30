<?php defined("APP") or die() ?>
  <footer>
    <div class="container">
      <div class="footer-logo">
        <div class="row">
          <div class="col-md-8">
            <h2><?php echo $this->config["title"] ?>
              <a href="<?php echo $this->config["url"] ?>"><?php echo e("Home") ?></a>
              <?php foreach ($pages as $page):?>        
                <a href='<?php echo Main::href("page/{$page->slug}") ?>' title='<?php echo _($page->name)?>'><?php echo _($page->name)?></a>
              <?php endforeach; ?>                
              <a href="<?php echo Main::href("contact") ?>"><?php echo e("Contact Us") ?></a>
              <small>&copy; <?php echo date("Y") ?> <?php echo e("All Rights Reserved.") ?></small>
            </h2>            
          </div>
          <div class="col-md-4 text-right">
            <a href="<?php echo Main::href("rss") ?>" class="rss-feed"><span class="fa fa-rss-square"></span></a>
            <div class="languages">
              <a href="#lang" class="active" id="show-language"><i class="fa fa-globe"></i></a>
              <div class="langs">
                <?php echo $this->lang(0) ?>
              </div>          
            </div>     
          </div>          
        </div>
      </div><!-- /.col-md-4 footer-logo -->
    </div>
  </footer>
  <div class="login-form hidden">
    <?php if(!$this->config["maintenance"] && $this->config["user"] && ($this->config["fb_connect"] || $this->config["tw_connect"] || $this->config["gl_connect"])):?>
      <?php if($this->config["fb_connect"]):?>
        <a href="<?php echo Main::href("user/login/facebook") ?>" class="btn btn-facebook btn-block"><?php echo e("Login with Facebook") ?></a>      
      <?php endif;?>
      <?php if($this->config["tw_connect"]):?>        
        <a href="<?php echo Main::href("user/login/twitter") ?>" class="btn btn-twitter btn-block"><?php echo e("Login with Twitter") ?></a>
      <?php endif;?>
      <?php if($this->config["gl_connect"]):?>        
        <a href="<?php echo Main::href("user/login/google") ?>" class="btn btn-google btn-block"><?php echo e("Login with Google") ?></a>
      <?php endif;?>
    <?php endif; ?>
    <hr>
    <form action="<?php echo Main::href("user/login") ?>" method="post">
      <div class="form-group">
        <label for="user" class="control-label"><?php echo e("Username or Email") ?></label>
        <input type="text" id="user" name="email" value="" placeholder="<?php echo e("Username or Email") ?>" class="form-control">
      </div>
      <div class="form-group">
        <label for="password" class="control-label"><?php echo e("Password") ?> </label>
        <input type="password" id="password" name="password" value="" placeholder="<?php echo e("Password") ?>" class="form-control">
      </div>  
      <input type="hidden" name="next" value="<?php echo Main::url() ?>">
      <?php echo Main::csrf_token(TRUE) ?>    
      <button type="submit" class="btn btn-primary"><?php echo e("Login") ?></button>
      <?php if($this->config["user"] && !$this->config["maintenance"]): ?>
        <a href="<?php echo Main::href("user/register") ?>" class="btn btn-primary"><?php echo e("Register") ?></a>
      <?php endif ?>
    </form>
  </div>
  <div class="this-return-data"></div>
  <?php echo Main::enqueue('footer') ?>
  </body>
</html>