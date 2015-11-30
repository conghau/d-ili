<?php defined("APP") or die() ?>
<section id="login">
  <div class="container">    
    <div class="centered form panel panel-default">      
      <form role="form" class="live_form" method="post" action="<?php echo Main::href("user/forgot/{$this->id}")?>"> 
  
        <div class="panel-body">      
          <div class="form-group">
            <label for="pass1"><?php echo e("Password")?></label>
            <input type="password" class="form-control" id="pass1" placeholder="<?php echo e("Password") ?>" name="password">             
          </div>        
          <div class="form-group">
            <label for="pass2"><?php echo e("Confirm Password")?></label>
            <input type="password" class="form-control" id="pass2" placeholder="<?php echo e("Confirm Password") ?>" name="cpassword">               
          </div>
          <?php echo Main::csrf_token(TRUE) ?>
          <button type="submit" class="btn btn-primary"><?php echo e("Reset Password")?></button>        
        </div>
      </form>        
		</div>
	</div>
</section>