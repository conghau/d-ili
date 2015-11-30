<?php defined("APP") or die() ?>
<section id="profile" class="user-profile">
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
          <li class="profile-name">
            <?php echo empty($profile->name) ? ucfirst($user->username) : $profile->name ?>
            <?php if($user->active): ?>
              <span class="fa fa-check-circle"></span>
            <?php endif; ?>
          </li>
          <li>
            <a href="<?php echo Main::href("user/{$user->username}") ?>" class="current"><?php echo e("Videos") ?></a>
          </li>
          <li>
            <a href="<?php echo Main::href("user/{$user->username}/stream") ?>"><?php echo e("Public Stream") ?></a>
          </li>             
          <li class="pull-right subscribe">
            <a href="#subscribe" class="this-action" id="this-subscribe" data-action="subscribe" data-data='["id":<?php echo $user->id ?>]' ><?php echo e("Subscribe") ?>
              <span><?php echo Main::formatnumber($user->subscribers,1) ?></span>
            </a>            
          </li>
        </ul>
			</div>
		</div>
    <div class="row">
      <div class="col-md-3 profile-sidebar">
        <div class="panel panel-default">
          <div class="panel-heading">
            <?php echo e("About") ?> <?php echo $profile->name ?>
          </div>
          <div class="profile-about">
            <p><?php echo $profile->description ?></p>            
            <p><span class="fa fa-calendar"></span> <strong><?php echo Main::timeago($user->date) ?></strong></p>
            <p><span class="fa fa-thumbs-o-up"></span> <strong><?php echo number_format($user->subscribers) ?></strong> <?php echo e("Subscribers") ?></p>
            <?php if ($user->country): ?>
              <p><span class="fa fa-globe"></span> <strong><?php echo Main::ccode($user->country) ?></strong></p>
            <?php endif ?>
            <p><small><a href="" id="this-report" title="<?php echo e("Report this page") ?>" class="this-action" data-action="report" data-data='["id":<?php echo $user->id ?>,"check": "user"]' ?><?php echo e("Report") ?> <?php echo $profile->name ?></a></small></p>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-heading">
            <?php echo e("Latest Subscribers") ?>
          </div>
          <div class="panel-body subscribers">
            <?php if ($subscribers): ?>
              <?php foreach ($subscribers as $subscriber): ?>
                <a href="<?php echo Main::href("user/{$subscriber->username}") ?>"><img src="<?php echo $this->avatar($subscriber, 54) ?>" width="54" alt="<?php echo ucfirst($subscriber->username) ?>"></a>
              <?php endforeach ?>              
            <?php else: ?>
              <p class="text-center"><strong><?php echo e("No subscribers") ?></strong></p>
            <?php endif ?>
          </div>
        </div>
        <?php echo $this->ads('resp') ?>
      </div>
      <div class="col-md-9">
        <?php echo $content ?> 
        <?php echo $this->ads(728) ?>   
      </div>
    </div>  
  </div>
</section>