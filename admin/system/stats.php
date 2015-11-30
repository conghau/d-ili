<?php if(!defined("APP")) die(); // Protect this page ?>
<div class="row">
	<div class="col-md-6">
		<div class="row">
			<div class="col-md-4">
        <div class="panel panel-default panel-red">
          <div class="panel-body">
            <p class="main-stats"><span><?php echo $media->today ?></span> Media</p>
            <p>Today</p>
          </div>
        </div>				
			</div>
			<div class="col-md-4">
        <div class="panel panel-default panel-red">
          <div class="panel-body">
            <p class="main-stats"><span><?php echo $media->yesterday ?></span> Media</p>
            <p>Yesterday</p>
          </div>
        </div>							
			</div>
			<div class="col-md-4">
        <div class="panel panel-default panel-red">
          <div class="panel-body">
            <p class="main-stats"><span><?php echo $media->total ?></span> Media</p>
            <p>Since launch</p>
          </div>
        </div>							
			</div>
		</div>
		<div class="row">
			<div class="col-md-4">
        <div class="panel panel-default panel-dark">
          <div class="panel-body">
            <p class="main-stats"><span><?php echo $comment->today ?></span> Comments</p>
            <p>Today</p>
          </div>
        </div>				
			</div>
			<div class="col-md-4">
        <div class="panel panel-default panel-dark">
          <div class="panel-body">
            <p class="main-stats"><span><?php echo $comment->yesterday ?></span> Comments</p>
            <p>Yesterday</p>
          </div>
        </div>							
			</div>
			<div class="col-md-4">
        <div class="panel panel-default panel-dark">
          <div class="panel-body">
            <p class="main-stats"><span><?php echo $comment->total ?></span> Comments</p>
            <p>Since launch</p>
          </div>
        </div>							
			</div>
		</div>						
		<div class="panel panel-default">
			<div class="panel-heading">Media Statistics</div>
			<div class="panel-body">
				<div class="indicator">
					<span class="progress-label"><?php echo $media->video ?> Videos</span>
					<div class="progress">
					  <div class="progress-bar" role="progressbar" data-now="<?php echo round($media->video*100/($media->total ? $media->total : 1),1) ?>" data-style="width: <?php echo round($media->video/($media->total ? $media->total : 1)*100,1) ?>%;">
					  </div>
					</div>					
				</div>

				<div class="indicator">
					<span class="progress-label"><?php echo $media->music ?> Music</span>
					<div class="progress">
					  <div class="progress-bar progress-bar-danger" role="progressbar" data-now="<?php echo round($media->music*100/($media->total ? $media->total : 1),1) ?>" data-style="width: <?php echo round($media->music/($media->total ? $media->total : 1)*100,1) ?>%;">
					  </div>
					</div>					
				</div>		

				<div class="indicator">
					<span class="progress-label"><?php echo $media->vine ?> Vines</span>
					<div class="progress">
					  <div class="progress-bar progress-bar-warning" role="progressbar" data-now="<?php echo round($media->vine*100/($media->total ? $media->total : 1),1) ?>" data-style="width: <?php echo round($media->vine/($media->total ? $media->total : 1)*100,1) ?>%;">
					  </div>
					</div>					
				</div>
				<div class="indicator">
					<span class="progress-label"><?php echo $media->picture ?> Pictures</span>
					<div class="progress">
					  <div class="progress-bar progress-bar-success" role="progressbar" data-now="<?php echo round($media->picture*100/($media->total ? $media->total : 1),1) ?>" data-style="width: <?php echo round($media->picture/($media->total ? $media->total : 1)*100,1) ?>%;">
					  </div>
					</div>					
				</div>															
			</div>
		</div>	
		<div class="panel panel-default">
			<div class="panel-heading">Media Analysis</div>
			<div class="panel-body">
         <ul class="media-list">
					 <?php foreach ($media->top as $top_media): ?>
							<?php
								if($this->config["local_thumbs"] || empty($top_media->ext_thumb)){
									if(empty($top_media->thumb)){
										$top_media->thumb = $top_media->ext_thumb;
									}else{
										$top_media->thumb = "{$this->config["url"]}/content/thumbs/{$top_media->thumb}";
									}
								}else{
									$top_media->thumb = $top_media->ext_thumb;
								}	    
							?> 					 
							<li class="media">
		            <a class="pull-left" href="<?php echo Main::href("view/{$top_media->url}") ?>" target="_blank">
		              <img class="media-object" src="<?php echo $top_media->thumb ?>" width="100">
		            </a>            
		            <div class="media-body">
		              <h5 class="media-heading">
		                <a href="<?php echo Main::href("view/{$top_media->url}") ?>" target="_blank"><?php echo $top_media->title ?></a> 
		              </h5>
		              Uploaded <?php echo Main::timeago($top_media->date) ?> - <?php echo number_format($top_media->views) ?> Views - <?php echo number_format($top_media->likes) ?> Likes - <?php echo number_format($top_media->dislikes) ?> Dislikes
		            </div>
		         </li> 
         	 <?php endforeach ?>          	
         </ul>				
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="row">
			<div class="col-md-4">
        <div class="panel panel-default panel-green">
          <div class="panel-body">
            <p class="main-stats"><span><?php echo $user->today ?></span> Users</p>
            <p>Today</p>
          </div>
        </div>				
			</div>
			<div class="col-md-4">
        <div class="panel panel-default panel-green">
          <div class="panel-body">
            <p class="main-stats"><span><?php echo $user->yesterday ?></span> Users</p>
            <p>Yesterday</p>
          </div>
        </div>							
			</div>
			<div class="col-md-4">
        <div class="panel panel-default panel-green">
          <div class="panel-body">
            <p class="main-stats"><span><?php echo $user->total ?></span> Users</p>
            <p>Since launch</p>
          </div>
        </div>							
			</div>
		</div>	
		<div class="row">
			<?php foreach ($user->country as $country): ?>
				<div class="col-md-4">
	        <div class="panel panel-default panel-blue">
	          <div class="panel-body">
	            <p class="main-stats"><span><?php echo $country->count ?></span> Users</p>
	            <p>From <?php echo empty($country->country) ? "Unknown" : Main::ccode($country->country) ?></p>
	          </div>
	        </div>				
				</div>				
			<?php endforeach ?>
		</div>			
		<div class="panel panel-default">
			<div class="panel-heading">User Statistics</div>
			<div class="panel-body">
				<div class="indicator">
					<span class="progress-label"><?php echo $user->active ?> Active Users</span>
					<div class="progress">
					  <div class="progress-bar" role="progressbar" data-now="<?php echo round($user->active*100/$user->total,1) ?>" data-style="width: <?php echo round($user->active/$user->total*100,1) ?>%;">
					  </div>
					</div>					
				</div>

				<div class="indicator">
					<span class="progress-label"><?php echo $user->inactive ?> Inactive Users</span>
					<div class="progress">
					  <div class="progress-bar progress-bar-danger" role="progressbar" data-now="<?php echo round($user->inactive*100/$user->total,1) ?>" data-style="width: <?php echo round($user->inactive/$user->total*100,1) ?>%;">
					  </div>
					</div>					
				</div>														
			</div>
			<hr>
			<div class="panel-body">
				<p><strong>Top Users</strong></p>
         <ul class="media-list">
					 <?php foreach ($user->subscribe as $top_user): ?>
							<li class="media">
		            <a class="pull-left" href="<?php echo Main::ahref("users/edit/{$top_user->id}") ?>">
		              <img class="media-object" src="<?php echo $this->avatar($top_user) ?>" width="48">
		            </a>            
		            <div class="media-body">
		              <h4 class="media-heading">
		                <a href="<?php echo Main::ahref("users/edit/{$top_user->id}") ?>"><?php echo $top_user->username ?></a> 
		                <small>
		                  Registered <?php echo Main::timeago($top_user->date) ?>
		                </small>
		                <div class="pull-right">
		                  <a href="<?php echo Main::ahref("users/edit/{$top_user->id}") ?>" class="btn btn-primary btn-xs">Edit</a> 
		                </div>
		              </h4>
		              <?php echo number_format($top_user->subscribers) ?> subscribers
		            </div>
		         </li> 
         	 <?php endforeach ?>          	
         </ul>
			</div>
		</div>	
		<div class="panel panel-default">
			<div class="panel-heading">Social Logins</div>
			<div class="panel-body">
				<div class="indicator">
					<span class="progress-label"><?php echo $user->facebook ?> Facebook Users</span>
					<div class="progress">
					  <div class="progress-bar progress-bar-primary progress-bar-facebook" role="progressbar" data-now="<?php echo round($user->facebook*100/$user->total,1) ?>" data-style="width: <?php echo round($user->facebook/$user->total*100,1) ?>%;">
					  </div>
					</div>					
				</div>	
				<div class="indicator">
					<span class="progress-label"><?php echo $user->twitter ?> Twitter Users</span>
					<div class="progress">
					  <div class="progress-bar progress-bar-primary progress-bar-twitter" role="progressbar" data-now="<?php echo round($user->twitter*100/$user->total,1) ?>" data-style="width: <?php echo round($user->twitter/$user->total*100,1) ?>%;">
					  </div>
					</div>					
				</div>		
				<div class="indicator">
					<span class="progress-label"><?php echo $user->google ?> Google Users</span>
					<div class="progress">
					  <div class="progress-bar progress-bar-primary progress-bar-google" role="progressbar" data-now="<?php echo round($user->google*100/$user->total,1) ?>" data-style="width: <?php echo round($user->google/$user->total*100,1) ?>%;">
					  </div>
					</div>					
				</div>					
			</div>
		</div>
	</div>
</div>