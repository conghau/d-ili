<?php defined("APP") or die() ?>
<section>
  <div class="container">
    <div class="row">
 			<div class="col-md-2">
 				<div class="panel panel-default">
					<ul class="nav nav-pills nav-stacked tabs">
						<li class="active"><a href="#account"><?php echo e("Account Settings") ?></a></li>
						<li><a href="#profile"><?php echo e("Profile Settings") ?></a></li>
						<li><a href="#profile-cover"><?php echo e("Profile Cover") ?></a></li>
						<li><a href="#privacy"><?php echo e("Privacy Settings") ?></a></li>
					</ul>
 				</div>
 			</div>
 			<div class="col-md-10"> 				
				<div class="panel panel-default">
					<form action="<?php echo Main::href("user/settings") ?>" method="post" enctype='multipart/form-data'>
						<div id="account" class="tabbed">
		 					<div class="panel-heading">
		 						<?php echo e("Account Settings") ?>
		 					</div>
		 					<div class="panel-body">
			 					<?php if(empty($this->user->username)): ?>
			 						<div class="form-group has-error">
			 							<label for="username" class="control-label"><?php echo e("Username") ?></label>
			 							<p class="help-block"><?php echo e("Please choose a username before continuing.") ?></p>
			 							<input type="text" class="form-control" id="username" name="username" value="<?php echo $user->username ?>">
			 						</div>		 					
				 				<?php endif; ?>			 						
		 						<div class="form-group">
		 							<label for="name" class="control-label"><?php echo e("Full Name") ?></label>
		 							<input type="text" class="form-control" id="name" name="name" value="<?php echo $user->name ?>">
		 						</div>	 		 						
		 						<div class="form-group">
		 							<label for="dob" class="control-label"><?php echo e("Date of Birth") ?></label>
									<input type="date" name="dob" class="form-control" value="<?php echo $user->dob ?>" min="1914-01-01" max="<?php echo date("Y-m-d") ?>"><br>
		 						</div>			 		
		 						<hr>									
		 						<div class="form-group">
		 							<label for="email" class="control-label"><?php echo e("Email") ?></label>
		 							<input type="text" class="form-control" id="email" name="email" value="<?php echo $user->email ?>">
		 						</div>	 						
								<div class="form-group">
		 							<label for="npassword" class="control-label"><?php echo e("New Password") ?></label>
		 							<input type="password" class="form-control" name="npassword" id="npassword">
		 						</div> 
								<div class="form-group">
		 							<label for="cnpassword" class="control-label"><?php echo e("Confirm New Password") ?></label>
		 							<input type="password" class="form-control" name="cnpassword" id="cnpassword">
		 						</div> 		 											
		 					</div>						
						</div>
						<div id="profile" class="tabbed">
		 					<div class="panel-heading"><?php echo e("Profile Settings") ?></div>			
		 					<div class="panel-body">		 						
	 							<div class="form-group">
		 							<div class="row">
		 								<div class="col-xs-2">
		 									<img src="<?php echo $this->avatar($user,100) ?>" width="100%" alt="">
		 								</div>
		 								<div class="col-xs-10">
											<label for="avatar" class="control-label"><?php echo e("Avatar") ?></label>
		 									<input type="file" class="form-control" id="avatar" name="avatar">
		 									<p class="help-block"><?php echo e("The recommended size for the avatar is 200x200. Please note that if you don't upload an avatar, your email will be used to get your Gravatar.") ?></p>
		 								</div>
		 							</div>
		 						</div>	 	 	
		 						<hr>	
		 						<div class="form-group">
		 							<label for="profile[name]" class="control-label"><?php echo e("Profile Name") ?></label>
		 							<input type="text" class="form-control" id="profile[name]" name="profile[name]" value="<?php echo $user->profile->name ?>">
		 						</div>	 						
	 							<div class="form-group">
		 							<label for="profile[description]" class="control-label"><?php echo e("Profile Description") ?></label>
		 							<textarea class="form-control" id="profile[description]" name="profile[description]" rows="5"><?php echo $user->profile->description ?></textarea>
		 						</div>	 			
			          <div class='form-group'>
			            <label for='country' class='control-label'><?php echo e("Country") ?></label>
		              <select name='country' class="form-control" id='country'>
		                <?php echo Main::countries($user->country) ?>
		              </select>
			          </div>  	 						
		 					</div>	
						</div>
						<div id="profile-cover" class="tabbed">
							<div class="panel-heading"><?php echo e("Profile Cover") ?></div>
							<div class="panel-body">
								<div class="row">
									<div class="col-md-6 covers">
				 						<h3><?php echo e("Choose cover") ?></h3>
				 						<ul class="cover-selector">
				 							<li><a href="" data-value='cover-1.jpg' <?php if($user->profile->cover == "cover-1.jpg") echo "class='current'"?>><img src='<?php echo $this->config["url"] ?>/static/covers/cover-1.jpg' width='100%'></a></li>
				 							<li><a href="" data-value='cover-2.jpg' <?php if($user->profile->cover == "cover-2.jpg") echo "class='current'"?>><img src='<?php echo $this->config["url"] ?>/static/covers/cover-2.jpg' width='100%'></a></li>
				 							<li><a href="" data-value='cover-3.jpg' <?php if($user->profile->cover == "cover-3.jpg") echo "class='current'"?>><img src='<?php echo $this->config["url"] ?>/static/covers/cover-3.jpg' width='100%'></a></li>
				 						</ul>			
				 						<input type="hidden" name="cover_value" id="cover_value" value="<?php echo $user->profile->cover ?>"> 						
									</div>
									<div class="col-md-6">
					 						<?php if(isset($user->profile->cover) && !empty($user->profile->cover)): ?>					 						
					 							<?php if (!in_array($user->profile->cover, array("cover-1.jpg","cover-2.jpg","cover-3.jpg"))): ?>
							 						<div class="form-group">
							 							<label class="control-label"><?php echo e("Custom Cover") ?></label>					 							
							 							<img src="<?php echo $this->config["url"] ?>/content/user/<?php echo $user->profile->cover ?>" width="100%" alt="">
							 						</div>	
					 							<?php endif ?>
					 						<?php endif; ?>						 																		
										<div class="form-group">
				 							<label for="cover" class="control-label"><?php echo e("Upload Custom Cover") ?></label>				 							
				 							<p class="help-block">
				 								<?php echo e("You can choose from the gallery of covers or upload a custom cover. If you upload a custom cover, make sure that size is at least 1200x250 pixels.") ?>
				 							</p>
				 							<input type="file" class="form-control" id="cover" name="cover">				 							
				 						</div>								
									</div>
								</div>									
							</div>						
						</div>
						<div id="privacy" class="tabbed">
		 					<div class="panel-heading">
		 						<?php echo e("Privacy Settings") ?>
		 					</div>							
							<div class="panel-body">
								<ul class="form_opt" data-id="public">
									<li class="text-label"><?php echo e("Profile Access") ?> <small><?php echo e("Change the access to your profile page. By making it public anyone can see your videos and your stream.") ?></small></li>
									<li><a href="" class="last<?php echo ((!$user->public)?' current':'')?>" data-value="0"><?php echo e("Private") ?></a></li>
									<li><a href="" class="first<?php echo (($user->public)?' current':'')?>" data-value="1"><?php echo e("Public") ?></a></li>
								</ul>
								<input type="hidden" name="public" id="public" value="<?php echo $user->public ?>">	
																
								<ul class="form_opt" data-id="nsfw">
									<li class="text-label"><?php echo e("Show NSFW Media") ?> <small><?php echo e("Disable this option if you would like to hide NSFW (Not safe for work) media.") ?></small></li>
									<li><a href="" class="last<?php echo ((!$user->nsfw)?' current':'')?>" data-value="0"><?php echo e("Disable") ?></a></li>
									<li><a href="" class="first<?php echo (($user->nsfw)?' current':'')?>" data-value="1"><?php echo e("Enable") ?></a></li>
								</ul>
								<input type="hidden" name="nsfw" id="nsfw" value="<?php echo $user->nsfw ?>">

								<ul class="form_opt" data-id="digest">
									<li class="text-label"><?php echo e("Receive Digests") ?> <small><?php echo e("Digests are a summary of new media uploaded that you might like to see. They are only sent occasionally so you have nothing to worry about.") ?></small></li>
									<li><a href="" class="last<?php echo ((!$user->digest)?' current':'')?>" data-value="0"><?php echo e("Disable") ?></a></li>
									<li><a href="" class="first<?php echo (($user->digest)?' current':'')?>" data-value="1"><?php echo e("Enable") ?></a></li>
								</ul>
								<input type="hidden" name="digest" id="digest" value="<?php echo $user->digest ?>">								
							</div>
						</div>
						<div class="panel-body">
							<?php echo Main::csrf_token(TRUE) ?>
							<button class="btn btn-primary"><?php echo e("Save Changes") ?></button>
						</div>
					</form>
 				</div>  				
 			</div>
    </div>
  </div>
</section>