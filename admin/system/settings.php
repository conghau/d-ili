<?php if(!defined("APP")) die(); // Protect this page ?>
<?php echo $this->update_notification() ?>
<div class="panel panel-default">
  <div class="panel-heading">
    Application Settings
  </div>      
  <div class="panel-body settings">
  	<div class="row">
  		<div class="col-md-3 sub-sidebar">
        <ul class="nav tabs">
          <li class="active"><a href="#general">General Settings</a></li>
					<li><a href="#app">Application Settings</a></li>		
					<li><a href="#media">Media Settings</a></li>					
					<li><a href="#points">Points Settings</a></li>
					<li><a href="#social">Social &amp; API Settings</a></li>						
					<li><a href="#security">Security Settings</a></li>
          <li><a href="#user">Users Settings</a></li>
          <li><a href="#tools">Extra Settings</a></li>
        </ul>
  		</div>
  		<div class="col-md-9">
				<form class="form-horizontal" role="form" id="setting-form" action="<?php echo Main::ahref("settings") ?>" method="post" enctype="multipart/form-data">
					<div id="general" class="tabbed">
						<div class="form-group">
					    <label for="url" class="col-sm-3 control-label">Site URL</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="url" id="url" value="<?php echo $this->config['url'] ?>">
					      <p class="help-block">Please make sure to include http:// (or https://) and remove the last slash</p>
					    </div>
					  </div>				
						<div class="form-group">
					    <label for="title" class="col-sm-3 control-label">Site Title</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="title" id="title" value="<?php echo $this->config['title'] ?>">
					      <p class="help-block">This is your site name as well as the site meta title.</p>
					    </div>
					  </div>				
						<div class="form-group">
					    <label for="description" class="col-sm-3 control-label">Site Description</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="description" id="description" value="<?php echo $this->config['description'] ?>">
					      <p class="help-block">This your site description as well as the site meta description.</p>
					    </div>
					  </div>
						<div class="form-group">
					    <label for="keywords" class="col-sm-3 control-label">Site Keywords</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="keywords" id="keywords" value="<?php echo $this->config['keywords'] ?>">
					      <p class="help-block">This your site keywords as well as the site meta keywords (only some important keywords).</p>
					    </div>
					  </div>					  
						<div class="form-group">
					    <label for="logo" class="col-sm-3 control-label">Logo
					    	<?php if(!empty($this->config["logo"])):  ?>
					    	<span class="help-block"><a href="#" id="remove_logo" class="btn btn-info btn-xs">Remove Logo</a></span>
					    	<?php endif ?>
					    </label>
					    <div class="col-sm-9">
								<?php if(!empty($this->config["logo"])):  ?>
									<img src="<?php echo $this->config["url"] ?>/content/<?php echo $this->config["logo"] ?>" height="80" alt=""> <br />
								<?php endif ?>					    	
					      <input type="file" name="logo_path" id="logo">
					      <p class="help-block">Please make sure that the logo is of adequate size and format (recommended: 300x90). You will need to first remove the logo then upload a new one.</p>
					    </div>
					  </div>					 
						<div class="form-group">
					    <label for="default_lang" class="col-sm-3 control-label">Default Language</label>
					    <div class="col-sm-9">
					      <select name="default_lang" id="default_lang" class="selectized">
					      	<?php echo $lang ?>
					      </select>
					      <p class="help-block">To add a new language, you may use the the <a href="<?php echo Main::ahref("languages") ?>">built-in editor</a>.</p>
					    </div>
					  </div>						  				  	
						<div class="form-group">
					    <label for="font" class="col-sm-3 control-label">Google Font</label>
					    <div class="col-sm-9">
					      <input class="form-control" name="font" id="font" value="<?php echo $this->config['font'] ?>">
					      <p class="help-block">Please add the exact name of the <a href="https://www.google.com/fonts" target="_blank">Google Font</a>: e.g. <strong>Open Sans</strong>.</p>
					    </div>
					  </div>				  			  
						<div class="form-group">
					    <label for="email" class="col-sm-3 control-label">Email</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="email" id="email" value="<?php echo $this->config['email'] ?>">
					      <p class="help-block">This email will be used to send emails and to receive emails.</p>
					    </div>
					  </div>
						<div class="form-group">
					    <label for="ga" class="col-sm-3 control-label">Google Analytics ID</label>
					    <div class="col-sm-9">
					      <input class="form-control" name="ga" id="ga" value="<?php echo $this->config['ga'] ?>">
					      <p class="help-block">Your Google Analytics account id e.g. UA-123456789</p>
					    </div>
					  </div>						  
					</div><!-- /#main.tabbed -->			
					<div id="app" class="tabbed">
						<ul class="form_opt" data-id="maintenance" data-callback="show_offline_message">
							<li class="text-label">Maintenance Mode <small>Enabling this will make your website inaccessible for all users but admins.</small></li>
							<li><a href="" class="last<?php echo ((!$this->config["maintenance"])?' current':'')?>" data-value="0">Disable</a></li>
							<li><a href="" class="first<?php echo (($this->config["maintenance"])?' current':'')?>" data-value="1">Enable</a></li>
						</ul>
						<input type="hidden" name="maintenance" id="maintenance" value="<?php echo $this->config["maintenance"]?>">	
						<div class="<?php if(!$this->config["maintenance"]) echo 'hide-callback'?>" id="offline_message_holder">
							<div class="form-group">
						    <label for="offline_message" class="col-sm-3 control-label">Offline Message</label>
						    <div class="col-sm-9">
						      <textarea name="offline_message" id="offline_message" cols="30" rows="10" class="form-control"><?php echo $this->config["offline_message"] ?></textarea>
						      <p class="help-block">You can use a custom message when the site is offline or under maintenance.</p>
						    </div>
						  </div>							
						</div>
						<hr>
						<ul class="form_opt" data-id="ads">
							<li class="text-label">Advertisement <small>Enable or disable advertisement throughout the site.</small></li>
							<li><a href="" class="last<?php echo ((!$this->config["ads"])?' current':'')?>" data-value="0">Disable</a></li>
							<li><a href="" class="first<?php echo (($this->config["ads"])?' current':'')?>" data-value="1">Enable</a></li>
						</ul>
						<input type="hidden" name="ads" id="ads" value="<?php echo $this->config["ads"]?>">			

						<div class="form-group">
					    <label for="preroll_timer" class="col-sm-3 control-label">Pre-roll Ad Timer</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="preroll_timer" id="preroll_timer" value="<?php echo $this->config['preroll_timer'] ?>">
					      <p class="help-block">The number of <strong>seconds</strong> to wait before the pre-roll ad is removed.</p>					      
					    </div>
					  </div>	
						<hr>
						<ul class="form_opt"  data-id="type-blog">
							<li class="text-label">Enable Blog<small>You will be able to post news and articles on your blog.</small></li>
							<li><a href="" class="<?php if(!$this->config["type"]["blog"]) echo "current"; ?>" data-value="0">Disable</a></li>							
							<li><a href="" class="<?php if($this->config["type"]["blog"]) echo "current"; ?> first" data-value="1">Enable</a></li>	
						</ul>
						<input type="hidden" name="type[blog]" id="type-blog" value="<?php echo $this->config["type"]["blog"];?>">

						<ul class="form_opt"  data-id="shorturl" data-callback="show_custom_short">
							<li class="text-label">URL Shortener  <small>Adfly requires registration. Check content/plugins/adfly.php. You can also use a custom API.</small></li>
							<li><a href="" class="<?php if($this->config["shorturl"]=="custom") echo "current"; ?> last" data-value="custom">Custom</a></li>
							<li><a href="" <?php if($this->config["shorturl"]=="google") echo "class='current'"; ?> data-value="google">Google </a></li>		
							<li><a href="" class="<?php if($this->config["shorturl"]=="adfly") echo "current"; ?>" data-value="adfly">AdFly </a></li>
							<li><a href="" class="<?php if($this->config["shorturl"]=="system") echo "current"; ?> first" data-value="system">System </a></li>
						</ul>
						<input type="hidden" name="shorturl" id="shorturl" value="<?php echo $this->config["shorturl"];?>">
						<div class="<?php if($this->config["shorturl"]!="custom") echo 'hide-callback'?>" id="shorturl_custom"><hr>
							<div class="form-group">
						    <label for="custom_shorturl" class="col-sm-3 control-label">Custom API Endpoint</label>
						    <div class="col-sm-9">
						      <input palceholder="e.g. http://shortener.com/api?key=123test&amp;url=@URL@" type="text" class="form-control" name="custom_shorturl" id="custom_shorturl" value="<?php echo $this->config['custom_shorturl'] ?>">
						      <p class="help-block">You can use a custom URL shortener by adding the endpoint here. Add <strong>@URL@</strong> as the URL placeholder.</p>
						    </div>
						  </div>							
						  <hr>
						</div>
						<ul class="form_opt"  data-id="comments">
							<li class="text-label">Enable/Disable Comments <small>Do you want users to be able to comment?</small></li>
							<li><a href="" class="<?php if(!$this->config["comments"]) echo "current"; ?> last" data-value="0">Disable</a></li>
							<li><a href="" class="<?php if($this->config["comments"]) echo "current"; ?> first" data-value="1">Enable</a></li>
						</ul>
						<input type="hidden" name="comments" id="comments" value="<?php echo $this->config["comments"];?>">

						<ul class="form_opt"  data-id="merge_comments">
							<li class="text-label">Merge Commenting Systems <small>Do you want to merge commenting systems (i.e. System + Facebook + Disqus)?</small></li>
							<li><a href="" class="<?php if(!$this->config["merge_comments"]) echo "current"; ?> last" data-value="0">Disable</a></li>
							<li><a href="" class="<?php if($this->config["merge_comments"]) echo "current"; ?> first" data-value="1">Enable</a></li>
						</ul>
						<input type="hidden" name="merge_comments" id="merge_comments" value="<?php echo $this->config["merge_comments"];?>">

						<ul class="form_opt"  data-id="comment_sys" id="comm_sys" data-callback="disqus">
							<li class="text-label">Default Commenting System <small>Choose the commenting system you want to use.</small></li>
							<li><a href="" class="<?php if($this->config["comment_sys"]=="disqus") echo "current"; ?> last" data-value="disqus">Disqus</a></li>
							<li><a href="" class="<?php if($this->config["comment_sys"]=="facebook") echo "current";?>" data-value="facebook">Facebook</a></li>
							<li><a href="" class="<?php if($this->config["comment_sys"]=="system") echo "current";?> first" data-value="system">System</a></li>
						</ul>
						<input type="hidden" name="comment_sys" id="comment_sys" value="<?php echo $this->config["comment_sys"];?>">			

						<div class="<?php if($this->config["comment_sys"]!="disqus") echo 'hide-callback'?>" id="disqus_sys"><hr>
							<div class="form-group">
						    <label for="disqus_username" class="col-sm-3 control-label">Disqus Username</label>
						    <div class="col-sm-9">
						      <input palceholder="" type="text" class="form-control" name="disqus_username" id="disqus_username" value="<?php echo $this->config['disqus_username'] ?>">
						      <p class="help-block">You need to input your disqus username to activate it otherwise commenting will be disabled.</p>
						    </div>
						  </div>							
						</div>
						<div class="form-group">
					    <label for="comment_blacklist" class="col-sm-3 control-label">Blacklist Words</label>
					    <div class="col-sm-9">
					      <textarea class="form-control" name="comment_blacklist" id="comment_blacklist"><?php echo $this->config['comment_blacklist'] ?></textarea>
					      <p class="help-block">Add as many words as you want to filter them from comments - separated by comma. E.g. word1, word2</p>
					    </div>
					  </div>								
						<hr>
						<ul class="form_opt" data-id="sharing">
							<li class="text-label">Sharing <small>Allow users to share their shorten URL through social networks such as facebook and twitter.</small></li>
							<li><a href="" class="last<?php echo ((!$this->config["sharing"])?' current':'')?>" data-value="0">Disable</a></li>
							<li><a href="" class="first<?php echo (($this->config["sharing"])?' current':'')?>" data-value="1">Enable</a></li>
						</ul>
						<input type="hidden" name="sharing" id="sharing" value="<?php echo $this->config["sharing"]?>">					

						<ul class="form_opt" data-id="update_notification">
							<li class="text-label">Update Notification <small>Be notified when an update is available.</small></li>
							<li><a href="" class="last<?php echo ((!$this->config["update_notification"])?' current':'')?>" data-value="0">Disable</a></li>
							 <li><a href="" class="first<?php echo (($this->config["update_notification"])?' current':'')?>" data-value="1">Enable</a></li>
						</ul>
						<input type="hidden" name="update_notification" id="update_notification" value="<?php echo $this->config["update_notification"]?>">								
					</div><!-- /#app.tabbed -->
					<div id="media" class="tabbed">	
						<div class="form-group">
							<div class="col-sm-4">
						    <label for="homelimit" class="control-label">HomePage Limit</label>
					      <input type="text" class="form-control" name="homelimit" id="homelimit" value="<?php echo $this->config['homelimit'] ?>">
					      <p class="help-block">The number of media to show on the home page.</p>
					    </div>					    	
							<div class="col-sm-4">
						    <label for="pagelimit" class="control-label">Browse Limit</label>
					      <input type="text" class="form-control" name="pagelimit" id="pagelimit" value="<?php echo $this->config['pagelimit'] ?>">
					      <p class="help-block">The number of media to show in other pages.</p>
					    </div>
							<div class="col-sm-4">
						    <label for="rsslimit" class="control-label">RSS Limit</label>
					      <input type="text" class="form-control" name="rsslimit" id="rsslimit" value="<?php echo $this->config['rsslimit'] ?>">
					      <p class="help-block">The number of media to show in the RSS Feed.</p>
					    </div>					    					    
					  </div>
					  <hr>	
						<ul class="form_opt" data-id="mode">
							<li class="text-label">Media Display Mode <small>Choose the way you want to display media. Grid is more like Youtube while Rows is more like 9gag. Unified will merge all media.</small></li>
							<li><a href="" class="last<?php echo (($this->config["mode"] =="grid")?' current':'')?>" data-value="grid">Grid</a></li>
							<li><a href="" class="<?php echo (($this->config["mode"]=="row")?' current':'')?>" data-value="row">Rows</a></li>
							<li><a href="" class="first<?php echo (($this->config["mode"]=="uni")?' current':'')?>" data-value="uni">Unified</a></li>
						</ul>
						<input type="hidden" name="mode" id="mode" value="<?php echo $this->config["mode"]?>">

						<ul class="form_opt"  data-id="local_thumbs">
							<li class="text-label">Store Thumbnail &amp; Pictures Locally<small>Enabling this option will copy the thumbnail and picture when you <strong>import</strong> a media or <strong>link</strong> (picture only) to it.</small></li>
							<li><a href="" class="<?php if(!$this->config["local_thumbs"]) echo "current"; ?>" data-value="0">Disable</a></li>		
							<li><a href="" class="<?php if($this->config["local_thumbs"]) echo "current"; ?> first" data-value="1">Enable</a></li>
						</ul>
						<input type="hidden" name="local_thumbs" id="local_thumbs" value="<?php echo $this->config["local_thumbs"];?>">	

						<ul class="form_opt"  data-id="player">
							<li class="text-label">Video Player<small>Use a custom video player whenever possible.</small></li>
							<li><a href="" class="<?php if($this->config["player"]=="videojs") echo "current"; ?> last" data-value="videojs">VideoJs</a></li>		
							<li><a href="" class="<?php if($this->config["player"]=="default") echo "current"; ?> first" data-value="default">Default</a></li>
						</ul>
						<input type="hidden" name="player" id="player" value="<?php echo $this->config["player"];?>">							
						<hr>
						<ul class="form_opt"  data-id="submission">
							<li class="text-label">Media Submission<small>Choose who can submit media - if you choose "Admin", the submission platform will be disabled.</small></li>
							<li><a href="" class="<?php if($this->config["submission"]==2) echo "current"; ?>" data-value="2">Registered users</a></li>		
							<li><a href="" class="<?php if(!$this->config["submission"]) echo "current"; ?> first" data-value="0">Admin</a></li>
						</ul>
						<input type="hidden" name="submission" id="submission" value="<?php echo $this->config["submission"];?>">
						
						<ul class="form_opt" data-id="upload">
							<li class="text-label">User Upload <small>Allow users to upload media. Disable this to allow only allow URL submissions.</small></li>
							<li><a href="" class="last<?php echo ((!$this->config["upload"])?' current':'')?>" data-value="0">Disable</a></li>
							<li><a href="" class="first<?php echo (($this->config["upload"])?' current':'')?>" data-value="1">Enable</a></li>
						</ul>
						<input type="hidden" name="upload" id="upload" value="<?php echo $this->config["upload"]?>">	

						<ul class="form_opt"  data-id="autoapprove">
							<li class="text-label">Auto Approve Media<small>Enable this option to automatically approve user-submitted media.</small></li>
							<li><a href="" class="<?php if(!$this->config["autoapprove"]) echo "current"; ?>" data-value="0">Disable</a></li>		
							<li><a href="" class="<?php if($this->config["autoapprove"]) echo "current"; ?> first" data-value="1">Enable</a></li>
						</ul>
						<input type="hidden" name="autoapprove" id="autoapprove" value="<?php echo $this->config["autoapprove"];?>">																
						<hr>
						<div class="form-group">
					    <label for="max_size" class="col-sm-3 control-label">Maximum Upload Size (MB)</label>
					    <div class="col-sm-9">
					      <input palceholder="" type="text" class="form-control" name="max_size" id="max_size" value="<?php echo $this->config['max_size'] ?>">
					      <p class="help-block">The maximum size limit will be the lower of this option and your server settings which is <?php echo max_size() ?> MB</p>
					    </div>
					  </div>							  										  
						<hr>
						<ul class="form_opt"  data-id="type-video">
							<li class="text-label">Video Type Media<small>Enable the video category. Disable to hide all media in this category and remove the option.</small></li>
							<li><a href="" class="<?php if(!$this->config["type"]["video"]) echo "current"; ?>" data-value="0">Disable</a></li>							
							<li><a href="" class="<?php if($this->config["type"]["video"]) echo "current"; ?> first" data-value="1">Enable</a></li>									
						</ul>
						<input type="hidden" name="type[video]" id="type-video" value="<?php echo $this->config["type"]["video"];?>">	

						<ul class="form_opt"  data-id="type-music">
							<li class="text-label">Music Type Media<small>Enable the music category. Disable to hide all media in this category and remove the option.</small></li>
							<li><a href="" class="<?php if(!$this->config["type"]["music"]) echo "current"; ?>" data-value="0">Disable</a></li>							
							<li><a href="" class="<?php if($this->config["type"]["music"]) echo "current"; ?> first" data-value="1">Enable</a></li>									
						</ul>
						<input type="hidden" name="type[music]" id="type-music" value="<?php echo $this->config["type"]["music"];?>">

						<ul class="form_opt"  data-id="type-vine">
							<li class="text-label">Vine Type Media<small>Enable the vine category. Disable to hide all media in this category and remove the option.</small></li>
							<li><a href="" class="<?php if(!$this->config["type"]["vine"]) echo "current"; ?>" data-value="0">Disable</a></li>							
							<li><a href="" class="<?php if($this->config["type"]["vine"]) echo "current"; ?> first" data-value="1">Enable</a></li>									
						</ul>
						<input type="hidden" name="type[vine]" id="type-vine" value="<?php echo $this->config["type"]["vine"];?>">

						<ul class="form_opt"  data-id="type-picture">
							<li class="text-label">Picture Type Media<small>Enable the picture category. Disable to hide all media in this category and remove the option.</small></li>
							<li><a href="" class="<?php if(!$this->config["type"]["picture"]) echo "current"; ?>" data-value="0">Disable</a></li>							
							<li><a href="" class="<?php if($this->config["type"]["picture"]) echo "current"; ?> first" data-value="1">Enable</a></li>	
						</ul>
						<input type="hidden" name="type[picture]" id="type-picture" value="<?php echo $this->config["type"]["picture"];?>">

						<ul class="form_opt"  data-id="type-post">
							<li class="text-label">Post Type Media<small>Enable the post category. Disable to hide all media in this category and remove the option.</small></li>
							<li><a href="" class="<?php if(!$this->config["type"]["post"]) echo "current"; ?>" data-value="0">Disable</a></li>							
							<li><a href="" class="<?php if($this->config["type"]["post"]) echo "current"; ?> first" data-value="1">Enable</a></li>	
						</ul>
						<input type="hidden" name="type[post]" id="type-post" value="<?php echo $this->config["type"]["post"];?>">

						<p><strong>Note</strong> You need to enable at least one of the types otherwise nothing will work. Also note that once you disable any of the types, all media categories related to type will be hidden until you enable the type back.</p>
					</div><!-- /#media.tabbed -->
					<div id="points" class="tabbed">
						<ul class="form_opt" data-id="points">
							<li class="text-label">Points Module<small>Give users points for doing specific actions.</small></li>
							<li><a href="" class="last<?php echo ((!$this->config["points"])?' current':'')?>" data-value="0">Disable</a></li>
							<li><a href="" class="first<?php echo (($this->config["points"])?' current':'')?>" data-value="1">Enabled</a></li>
						</ul>
						<input type="hidden" name="points" id="points" value="<?php echo $this->config["points"]?>">	
						<hr>
						<div class="form-group">
					    <label for="amount_points[submit]" class="col-sm-3 control-label">Points for Submitting</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="amount_points[submit]" id="amount_points[submit]" value="<?php echo $this->config['amount_points']['submit'] ?>">
					      <p class="help-block">Each time someone submits a media and it gets approved, the user will receive <strong><?php echo $this->config['amount_points']['submit'] ?></strong> points.</p>
					    </div>
					  </div>	
						<div class="form-group">
					    <label for="amount_points[comment]" class="col-sm-3 control-label">Points for Commenting</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="amount_points[comment]" id="amount_points[comment]" value="<?php echo $this->config['amount_points']['comment'] ?>">
					      <p class="help-block">Each time someone comments, the user will receive <strong><?php echo $this->config['amount_points']['comment'] ?></strong> points.</p>
					    </div>
					  </div>						  					  
						<div class="form-group">
					    <label for="amount_points[register]" class="col-sm-3 control-label">Points for Registering</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="amount_points[register]" id="amount_points[register]" value="<?php echo $this->config['amount_points']['register'] ?>">
					      <p class="help-block">Each time someone registers, the user will receive <strong><?php echo $this->config['amount_points']['register'] ?></strong> points.</p>
					    </div>
					  </div>
					  <div class="form-group">
					    <label for="amount_points[like]" class="col-sm-3 control-label">Points for Liking</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="amount_points[like]" id="amount_points[like]" value="<?php echo $this->config['amount_points']['like'] ?>">
					      <p class="help-block">Each time someone likes a media, the user will receive <strong><?php echo $this->config['amount_points']['like'] ?></strong> points.</p>
					    </div>
					  </div>	
					  <div class="form-group">
					    <label for="api_key" class="col-sm-3 control-label">Points for Subscribing</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="amount_points[subscribe]" id="amount_points[subscribe]" value="<?php echo $this->config['amount_points']['subscribe'] ?>">
					      <p class="help-block">Each time someone subscribes, the user will receive <strong><?php echo $this->config['amount_points']['subscribe'] ?></strong> points.</p>
					    </div>
					  </div>						  
					</div><!-- /#points.tabbed -->
					<div id="social" class="tabbed">
						<ul class="form_opt" data-id="api">
							<li class="text-label">API (View Documentation)<small>Enabling API will allow you to use the api endpoint to build other applications.</small></li>
							<li><a href="" class="last<?php echo ((!$this->config["api"])?' current':'')?>" data-value="0">Disable</a></li>
							<li><a href="" class="<?php echo (($this->config["api"]=="1")?' current':'')?>" data-value="1">Enabled</a></li>
						</ul>
						<input type="hidden" name="api" id="api" value="<?php echo $this->config["api"]?>">			
					  <div class="form-group">
					    <label for="api_key" class="col-sm-3 control-label">Your API Key</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="api_key" id="api_key" value="<?php echo empty($this->config["api_key"]) ? Main::strrand(8) : $this->config["api_key"] ?>">
					      <p class="help-block">Changing this prevent the app using old API key to access data.</p>
					    </div>
					  </div>							
						<hr>						
						<div class="form-group">
					    <label for="yt_api" class="col-sm-3 control-label">Youtube API Key</label>
					    <div class="col-sm-9">
					      <input palceholder="" type="text" class="form-control" name="yt_api" id="yt_api" value="<?php echo $this->config['yt_api'] ?>">
					      <p class="help-block">You need an API key to search and import from Youtube. Please check the documentation for more info.</p>
					    </div>
					  </div>	
						<div class="form-group">
					    <label for="vm_api" class="col-sm-3 control-label">Vimeo Access Token</label>
					    <div class="col-sm-9">
					      <input palceholder="" type="text" class="form-control" name="vm_api" id="vm_api" value="<?php echo $this->config['vm_api'] ?>">
					      <p class="help-block">You need to generate a vimeo access token to search and import from Vimeo. Please check the documentation for more info.</p>
					    </div>
					  </div>						  						  		
						<hr>
						<div class="form-group">
					    <label for="facebook" class="col-sm-3 control-label">Facebook Page</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="facebook" id="facebook" value="<?php echo $this->config['facebook'] ?>">
					      <p class="help-block">Link to your Facebook page e.g. http://facebook.com/gempixel</p>
					    </div>
					  </div>	
						<div class="form-group">
					    <label for="twitter" class="col-sm-3 control-label">Twitter Page</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="twitter" id="twitter" value="<?php echo $this->config['twitter'] ?>">
					      <p class="help-block">Link to your Twitter profile e.g. http://www.twitter.com/kbrmedia</p>
					    </div>
					  </div>				
						<div class="form-group">
					    <label for="google" class="col-sm-3 control-label">Google+ Page</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="google" id="google" value="<?php echo $this->config['google'] ?>">
					      <p class="help-block">Link to your Google+ profile e.g. https://plus.google.com/+Gempixel</p>
					    </div>
					  </div>												  		
					</div><!-- /#social.tabbed -->							
					<div id="security" class="tabbed">
						<ul class="form_opt" data-id="captcha">
							<li class="text-label">Captcha (View Documentation)<small>Users will be prompted to answer a captcha before processing their request.</small></li>
							<li><a href="" class="last<?php echo ((!$this->config["captcha"])?' current':'')?>" data-value="0">Disable</a></li>
							<li><a href="" class="<?php echo (($this->config["captcha"]=="1")?' current':'')?>" data-value="1">reCaptcha</a></li>
							<li><a href="" class="first<?php echo (($this->config["captcha"]=="2")?' current':'')?>" data-value="2">Solvemedia</a></li>
						</ul>
						<input type="hidden" name="captcha" id="captcha" value="<?php echo $this->config["captcha"]?>">					  

						<div class="form-group">
					    <label for="captcha_public" class="col-sm-3 control-label">reCaptcha Public Key</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="captcha_public" id="captcha_public" value="<?php echo $this->config['captcha_public'] ?>">
					      <p class="help-block">You can get your public key for free from <a href="https://www.google.com/recaptcha" target="_blank">Google</a></p>
					    </div>
					  </div>				
						<div class="form-group">
					    <label for="captcha_private" class="col-sm-3 control-label">reCaptcha Private Key</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="captcha_private" id="captcha_private" value="<?php echo $this->config['captcha_private'] ?>">
					      <p class="help-block">You can get your private key for free from <a href="https://www.google.com/recaptcha" target="_blank">Google</a></p>
					    </div>
					  </div>										
					</div><!-- /#security.tabbed -->
					<div id="user" class="tabbed">
						<ul class="form_opt" data-id="user_r">
							<li class="text-label">User Registration <small>Allow users to register and to bookmark their URLs. If disable registration links will be hidden.</small></li>
							<li><a href="" class="last<?php echo ((!$this->config["user"])?' current':'')?>" data-value="0">Disable</a></li>
							 <li><a href="" class="first<?php echo (($this->config["user"])?' current':'')?>" data-value="1">Enable</a></li>
						</ul>
						<input type="hidden" name="user" id="user_r" value="<?php echo $this->config["user"]?>">	

						<ul class="form_opt" data-id="require_activation">
							<li class="text-label">User Activation <small>If enabled, an email containing an activation link will be sent to the user.</small></li>
							<li><a href="" class="last<?php echo ((!$this->config["require_activation"])?' current':'')?>" data-value="0">Disable</a></li>
							 <li><a href="" class="first<?php echo (($this->config["require_activation"])?' current':'')?>" data-value="1">Enable</a></li>
						</ul>
						<input type="hidden" name="require_activation" id="require_activation" value="<?php echo $this->config["require_activation"]?>">	
						
						<hr>
						<ul class="form_opt" data-id="fb_connect">
							<li class="text-label">Enable Facebook Connect <small>Users can login and get registered using their facebook account.</small></li>
							<li><a href="" class="last<?php echo ((!$this->config["fb_connect"])?' current':'')?>" data-value="0">Disable</a></li>
							<li><a href="" class="first<?php echo (($this->config["fb_connect"])?' current':'')?>" data-value="1">Enable</a></li>
						</ul>
						<input type="hidden" name="fb_connect" id="fb_connect" value="<?php echo $this->config["fb_connect"]?>">
						<div class="form-group">
					    <label for="facebook_app_id" class="col-sm-3 control-label">Facebook App ID</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="facebook_app_id" id="facebook_app_id" value="<?php echo $this->config['facebook_app_id'] ?>">
					    </div>
					  </div>
						<div class="form-group">
					    <label for="facebook_secret" class="col-sm-3 control-label">Facebook Secret</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="facebook_secret" id="facebook_secret" value="<?php echo $this->config['facebook_secret'] ?>">
					    </div>
					  </div>					  
						<hr>
						<ul class="form_opt" data-id="tw_connect">
							<li class="text-label">Enable Twitter Connect <small>Users can login and get registered using their twitter account.</small></li>
							<li><a href="" class="last<?php echo ((!$this->config["tw_connect"])?' current':'')?>" data-value="0">Disable</a></li>
							<li><a href="" class="first<?php echo (($this->config["tw_connect"])?' current':'')?>" data-value="1">Enable</a></li>
						</ul>
						<input type="hidden" name="tw_connect" id="tw_connect" value="<?php echo $this->config["tw_connect"]?>">											
						<div class="form-group">
					    <label for="twitter_key" class="col-sm-3 control-label">Twitter Public Key</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="twitter_key" id="twitter_key" value="<?php echo $this->config['twitter_key'] ?>">
					    </div>
					  </div>
						<div class="form-group">
					    <label for="twitter_secret" class="col-sm-3 control-label">Twitter Secret Key</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="twitter_secret" id="twitter_secret" value="<?php echo $this->config['twitter_secret'] ?>">
					    </div>
					  </div>
					  <hr>
						<ul class="form_opt" data-id="gl_connect">
							<li class="text-label">Enable Google Authentication <small>Users can login and get registered using their google account. Make sure to fill the fields below!</small></li>
							<li><a href="" class="last<?php echo ((!$this->config["gl_connect"])?' current':'')?>" data-value="0">Disable</a></li>
							<li><a href="" class="first<?php echo (($this->config["gl_connect"])?' current':'')?>" data-value="1">Enable</a></li>
						</ul>
						<input type="hidden" name="gl_connect" id="gl_connect" value="<?php echo $this->config["gl_connect"]?>">			

						<div class="form-group">
					    <label for="google_cid" class="col-sm-3 control-label">Google Client ID</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="google_cid" id="google_cid" value="<?php echo $this->config['google_cid'] ?>">
					    </div>
					  </div>
						<div class="form-group">
					    <label for="google_cs" class="col-sm-3 control-label">Google Client Secret</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="google_cs" id="google_cs" value="<?php echo $this->config['google_cs'] ?>">
					    </div>
					  </div>										  					
					</div><!-- /#user.tabbed -->
					<div id="tools" class="tabbed">
					  <div class="alert alert-info"><strong>Tip:</strong> SMTP is recommend because it is much more reliable than the system mail module.</div>
						<div class="form-group">
					    <label for="smtp" class="col-sm-3 control-label">SMTP Host</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="smtp[host]" id="smtp" value="<?php echo $this->config['smtp']['host'] ?>">
					    </div>
					  </div>				
						<div class="form-group">
					    <label for="smtp" class="col-sm-3 control-label">SMTP Port</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="smtp[port]" id="smtp" value="<?php echo $this->config['smtp']['port'] ?>">
					    </div>
					  </div>		
						<div class="form-group">
					    <label for="smtp" class="col-sm-3 control-label">SMTP User</label>
					    <div class="col-sm-9">
					      <input type="text" class="form-control" name="smtp[user]" id="smtp" value="<?php echo $this->config['smtp']['user'] ?>">
					    </div>
					  </div>		
						<div class="form-group">
					    <label for="smtp" class="col-sm-3 control-label">SMTP Pass</label>
					    <div class="col-sm-9">
					      <input type="password" class="form-control" name="smtp[pass]" id="smtp" value="<?php echo $this->config['smtp']['pass'] ?>">
					    </div>
					  </div>
					  <hr>
					  <h3>Reset Script</h3>
					  <p>You can reset the script if you want by clicking the button below. This will delete all media, comments, ratings, users (except the first user), notifications, reports, subscriptions and all uploaded content. This is a great way to start fresh. <strong>Once you press that scary button, there is no going back!</strong></p>	

					  	<a href="<?php echo Main::ahref("settings/reset").Main::nonce("Reset-This-Awesome-Script-Please") ?>" class="btn btn-danger btn-xs delete hidden-xs">Restart Script</a>  					  				
					  <hr>	  			
					</div><!-- /#tools.tabbed -->

				  <div class="form-group">
				    <div class="col-sm-12">
				    	<?php echo Main::csrf_token(TRUE) ?>
				    	<br>
				      <button type="submit" class="btn btn-primary">Save Settings</button>
				    </div>
				  </div>
				</form>  			
  		</div>
  	</div>
  </div>
</div>