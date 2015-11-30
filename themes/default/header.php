<?php defined("APP") or die() ?>
<!DOCTYPE html>
<html lang="en" prefix="og: http://ogp.me/ns#">
  <head>       
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">    
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, maximum-scale=1.0" />  
    <meta name="description" content="<?php echo Main::description() ?>" />
    <meta name="keywords" content="<?php echo $this->config["keywords"] ?>" />
    <!-- Open Graph Tags -->
    <?php echo Main::ogp(); ?> 

    <title><?php echo Main::title() ?></title> 
    <link href="<?php echo $this->config["url"] ?>/static/css/bootstrap.min.css" rel="stylesheet">        
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config["url"] ?>/themes/<?php echo $this->config["theme"] ?>/style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config["url"] ?>/static/css/components.min.css">
    
    <script type="text/javascript" src="<?php echo $this->config["url"] ?>/static/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->config["url"] ?>/static/js/notify.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->config["url"] ?>/static/application.fn.js"></script>
    <script type="text/javascript" src="<?php echo $this->config["url"] ?>/static/js/is.js"></script>
    <script type="text/javascript" src="<?php echo $this->config["url"] ?>/static/application.js"></script>
    <script type="text/javascript" src="<?php echo $this->config["url"] ?>/static/server.js"></script>    
    <?php Main::enqueue() // Add scripts/stylesheet when needed ?>    
    <script type="text/javascript">
      var appurl = "<?php echo $this->config["url"] ?>";
      var token = "<?php echo $this->config["public_token"] ?>";
    </script>
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body<?php echo Main::body_class() ?>>
    <?php echo $this->admin_menu() ?>     
    <header>
      <div class="container">
        <div class="row">
          <div class="col-md-2 logo">
            <a href="<?php echo $this->config["url"] ?>">
              <?php if (!empty($this->config["logo"])): ?>
                <img src="<?php echo $this->config["url"] ?>/content/<?php echo $this->config["logo"] ?>" height="35" alt="<?php echo $this->config["title"] ?>">
              <?php else: ?>
                <span><?php echo $this->config["title"] ?></span>
              <?php endif ?>              
            </a>          
            <button type="button" class="navbar-toggle this-toggle" data-target="#main-menu">
              <span class="fa fa-bars"></span>
            </button>
          </div>       
          <div class="col-md-6 search">
            <form action="<?php echo Main::href("search") ?>" id="main-search">
              <div class="input-group">
                <span class="input-group-addon"><label for="search-input" style="margin:0"><i class="glyphicon glyphicon-search"></i></label></span>
                <input type="text" class="form-control" id="search-input" placeholder="<?php echo e("Enter a keyword and press enter") ?>" name="q" autocomplete="off">
              </div>
            </form>
          </div>
          <div class="col-md-4 nav">           
            <ul class="nav-list">
              <?php if($this->logged()): ?>
                <?php if($this->admin()): ?>
                  <li class="visible-xs"><a href="<?php echo Main::ahref("") ?>" class="btn btn-success btn-xs"><?php echo e("Admin") ?></a></li>
                <?php endif; ?>
                <?php if ($this->config["submission"] == "2"): ?>
                  <li><a href="<?php echo Main::href("upload") ?>" class="btn btn-primary btn-xs"><?php echo e("Upload") ?></a></li>
                <?php endif ?>                
                <li class="dropdown dropdown-alt" id="notifications"><a href="#notifications" class="this-action" data-action="notification" ><span class="fa fa-bell<?php echo $notifications ? ' fa-new': '' ?>"></span></a>
                  <div class="dropdown-holder panel panel-default">
                    <div class="panel-heading"><?php echo e("Notifications") ?> <a href="<?php echo Main::href("user/account/notifications") ?>" class="pull-right"><small>(<?php echo e("view more") ?>)</small></a></div>
                    <ul class="panel-body">                      
                    </ul>                      
                  </div>
                </li>
                <li class="dropdown hover"><a href="<?php echo Main::href("user") ?>" class="btn btn-xs user-avatar"><img src="<?php echo $this->avatar($this->user) ?>" alt="<?php echo $this->user->profile->name ?>"> <?php echo Main::truncate($this->user->profile->name, 15) ?></a>
                  <ul class="panel panel-default panel-body">
                    <li><a href="<?php echo Main::href("user/{$this->user->username}") ?>"><span class="fa fa-eye"></span><?php echo e("My Profile") ?></a></li>
                    <li><a href="<?php echo Main::href("user/account") ?>"><span class="fa fa-user"></span> <?php echo e("My Account") ?></a></li>
                    <li><a href="<?php echo Main::href("user/account/playlists") ?>"><span class="fa fa-list"></span> <?php echo e("My Playlists") ?></a></li>
                    <li><a href="<?php echo Main::href("user/account/settings") ?>"><span class="fa fa-gear"></span> <?php echo e("My Settings") ?></a></li>
                    <li><a href="<?php echo Main::href("user/logout") ?>"><span class="fa fa-sign-out"></span> <?php echo e("Log Out") ?></a></li>
                  </ul>
                </li> 
              <?php else: ?>        
                <li><a href="<?php echo Main::href("user/login") ?>" class="btn btn-primary btn-xs"><?php echo e("Login") ?></a></li>
                <?php if ($this->config["user"]): ?>
                  <li><a href="<?php echo Main::href("user/register") ?>" class="btn btn-xs btn-primary"><?php echo e("Register") ?></a></li> 
                <?php endif ?>               
              <?php endif ?>
            </ul>
          </div>
        </div>
      </div>
    </header>
    <?php echo $this->menu() ?>
    <?php echo Main::message("style='border-radius:0 !important'") ?>