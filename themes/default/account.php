<?php defined("APP") or die() ?>
<section id="profile" class="user-account/profile">
  <div class="container" id="main-container">
  	<div class="panel panel-default">
      <div class="profile-cover">
        <?php if (!empty($profile->cover)): ?>
          <?php if (in_array($profile->cover, array("cover-1.jpg","cover-2.jpg","cover-3.jpg"))): ?>
            <img src="<?php echo $this->config["url"] ?>/static/covers/<?php echo $profile->cover ?>" width="100%" alt="">
          <?php else: ?>
            <img src="<?php echo $this->config["url"] ?>/content/user/<?php echo $profile->cover ?>" width="100%" alt="">
          <?php endif ?>          
        <?php endif ?>
      </div>
			<div class="profile-menu">
				<a href="<?php echo Main::href("user/{$user->username}") ?>"><img src="<?php echo $this->avatar($user, 160) ?>" alt="<?php echo $profile->name ?>"></a>
        <ul class="profile-menu-list">          
          <li><a href="<?php echo Main::href("user/account") ?>" class="current"><?php echo e("Dashboard") ?></a></li>
          <li><a href="<?php echo Main::href("user/account/videos") ?>"><?php echo e("Uploads") ?></a> </li>               
          <li><a href="<?php echo Main::href("user/account/favorites") ?>"><?php echo e("Favorites") ?></a></li>             
          <li><a href="<?php echo Main::href("user/account/likes") ?>"><?php echo e("Likes") ?></a></li>                  
          <li><a href="<?php echo Main::href("user/account/playlists") ?>"><?php echo e("Playlists") ?></a></li>                  
          <li><a href="<?php echo Main::href("user/account/subscribers") ?>"><?php echo e("Subscribers") ?></a> </li>
          <li><a href="<?php echo Main::href("user/account/subscriptions") ?>"><?php echo e("Subscriptions") ?></a> </li>
          <li class="pull-right"><a href="<?php echo Main::href("user/account/settings") ?>"><span class="fa fa-gear"></span> <?php echo e("Settings") ?></a></li>                        
        </ul>
			</div>
		</div>
    <div class="row">
      <div class="col-md-3">
        <?php if ($this->config["points"]): ?>
          <div class="panel panel-default">
            <div class="panel-heading"><?php echo e("Points") ?> <a href="<?php echo Main::href("user/account/points") ?>" class='pull-right'><small>(<?php echo e("view history") ?>)</small></a></div>
            <div class="panel-body">
              <h1><?php echo $user->points ?> <small><?php echo e("Points") ?> </small></h1>
            </div>
          </div>           
        <?php endif ?>
        <div class="panel panel-default">
          <div class="panel-heading"><?php echo e("About") ?></div>
          <div class="profile-about">
						<p><?php echo $profile->description ?></p>            
            <p><span class="fa fa-calendar"></span> <strong><?php echo date("F d, Y", strtotime($user->date)) ?></strong></p>
            <p><span class="fa fa-sign-in"></span> <strong><?php echo Main::timeago($user->lastlogin) ?></strong></p>
            <p><span class="fa fa-thumbs-o-up"></span> <strong><?php echo number_format($user->subscribers) ?></strong> <?php echo e("Subscribers") ?></p>
            <p><span class="fa fa-eye"></span> <strong><?php echo ($user->public) ? e("Public") : e("Private") ?></strong> <?php echo e("Profile") ?></p>
          </div>
        </div>       
        <div class="panel panel-default">
          <div class="panel-heading">
            <?php echo e("Latest Activities") ?>
          </div>
          <div class="profile-about">
            <?php echo $activities_list ?>
          </div>
        </div>        
      </div>
      <div class="col-md-9">
        <?php echo $content ?>    
      </div>
    </div>  
  </div>
</section>