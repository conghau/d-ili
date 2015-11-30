<?php if(!defined("APP")) die(); // Protect this page ?>
<!DOCTYPE html>
<html lang="en">
  <head>    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">    
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, maximum-scale=1.0" />  
    <meta name="description" content="<?php echo Main::description() ?>" />
        
    <title><?php echo Main::title() ?></title>
    <!-- Bootstrap core CSS -->
    <link href="<?php echo $this->config["url"] ?>/static/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" type="text/css" href="<?php echo $this->url ?>/static/style.css"> -->
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config["url"] ?>/static/css/components.min.css"> 
    <link rel="stylesheet" type="text/css" href="<?php echo $this->url ?>/static/admin.css">
    <!-- Javascript Files -->
    <script type="text/javascript" src="<?php echo $this->config["url"] ?>/static/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->config["url"] ?>/static/js/chosen.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->config["url"] ?>/static/js/tagsinput.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->config["url"] ?>/static/application.fn.js"></script>
    <script type="text/javascript" src="<?php echo $this->config["url"] ?>/static/bootstrap.min.js"></script>  
    <script type="text/javascript" src="<?php echo $this->url ?>/static/dashboard.js"></script>  
    <?php Main::admin_enqueue() ?>  
    <script type="text/javascript">
      var appurl="<?php echo $this->url ?>";
      var token="<?php echo md5($this->config["public_token"]) ?>"
    </script>
  
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body id="main">
    <a href="#main" id="back-to-top">Back to top</a>
    <div class="navbar" role="navigation">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-2">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="glyphicon glyphicon-align-justify"></span>
              </button>
              <a class="navbar-brand" href="<?php echo $this->url ?>"><?php echo $this->config["title"] ?></a>
            </div>            
          </div>
          <div class="navbar-collapse collapse">         
            <form class="navbar-form navbar-left search" action="<?php echo Main::ahref("search") ?>">
              <input type="text" class="form-control" size="80" placeholder="Search for users and media and press enter." name="q" value="<?php echo Main::is_set("q") ?>">
            </form>             
            <ul class="nav navbar-nav navbar-right">
              <li><a href="<?php echo $this->config["url"] ?>" target="_blank"><span class="glyphicon glyphicon-globe"></span> View Site</a></li>
              <li><a href="<?php echo Main::href("user/logout") ?>"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
            </ul>           
          </div>        
        </div>
      </div>
    </div>
    <div class="container-fluid">
      <div class="row main-c">
        <div class="col-md-2 sidebar">    
          <ul class="sidenav">
            <li class="active"><a href="<?php echo Main::ahref("")?>"><span class="glyphicon glyphicon-dashboard"></span> Dashboard</a> </li>
            <li><a href=""><span class="glyphicon glyphicon-play-circle"></span> Add Media</a>
            <div>
              <a href="<?php echo Main::ahref("media/add")?>"><span class="glyphicon glyphicon-facetime-video"></span> Add a Media</a>              
              <a href="<?php echo Main::ahref("media/import")?>"><span class="glyphicon glyphicon-cloud-download"></span> Import via URL</a>           
              <a href="<?php echo Main::ahref("media/youtube")?>"><span class="glyphicon glyphicon-import"></span>Import from Youtube</a> 
              <a href="<?php echo Main::ahref("media/vimeo")?>"><span class="glyphicon glyphicon-import"></span>Import from Vimeo</a> 
              <a href="<?php echo Main::ahref("media/dailymotion")?>"><span class="glyphicon glyphicon-import"></span>Import from Dailymotion</a> 
            </div>
            </li>     
            <li><a href=""><span class="glyphicon glyphicon-th"></span> Media Management</a>
            <div>
              <a href="<?php echo Main::ahref("media/moderate")?>" class="moderate-link"><span class="glyphicon glyphicon-ok-sign"></span> Moderate Submitted Media</a>
              <a href="<?php echo Main::ahref("media/video")?>"><span class="glyphicon glyphicon-film"></span> Manage Videos</a> 
              <a href="<?php echo Main::ahref("media/music")?>"><span class="glyphicon glyphicon-music"></span> Manage Music Videos</a>    
              <a href="<?php echo Main::ahref("media/vine")?>"><span class="glyphicon glyphicon-facetime-video"></span> Manage Vines</a>
              <a href="<?php echo Main::ahref("media/picture")?>"><span class="glyphicon glyphicon-picture"></span> Manage Pictures</a>
              <a href="<?php echo Main::ahref("media/post")?>"><span class="glyphicon glyphicon glyphicon-align-left"></span> Manage Posts</a> 
              <a href="<?php echo Main::ahref("categories")?>"><span class="glyphicon glyphicon-th-list"></span> Manage Categories</a> 
            </div>
            </li>           
            <li><a href=""><span class="glyphicon glyphicon-user"></span> Users Management</a>
              <div>       
                <a href="<?php echo Main::ahref("users/add")?>"><span class="glyphicon glyphicon-plus"></span> Add a User</a>
                <a href="<?php echo Main::ahref("users")?>"><span class="glyphicon glyphicon-user"></span> Manage Users</a>
              </div>
            </li> 
            <li>
              <a href="<?php echo Main::ahref("comments")?>"><span class="glyphicon glyphicon-comment"></span> Comments Management</a>
            </li>
            <li><a href="<?php echo Main::ahref("reports")?>"><span class="glyphicon glyphicon-flag"></span> Report Management</a></li>
            <li><a href=""><span class="glyphicon glyphicon-th-large"></span> Page Management</a>
              <div>       
                <a href="<?php echo Main::ahref("pages/add")?>"><span class="glyphicon glyphicon-plus"></span> Add a Page</a>
                <a href="<?php echo Main::ahref("pages")?>"><span class="glyphicon glyphicon-th-large"></span> Manage Pages</a>
              </div>
            </li>   
            <li><a href=""><span class="glyphicon glyphicon-book"></span> Blog Management</a>
              <div>       
                <a href="<?php echo Main::ahref("blog/add")?>"><span class="glyphicon glyphicon-plus"></span> Add a Post</a>
                <a href="<?php echo Main::ahref("blog")?>"><span class="glyphicon glyphicon-th-large"></span> Manage Posts</a>
              </div>
            </li>              
            <li><a href="<?php echo Main::ahref("ads")?>"><span class="glyphicon glyphicon-usd"></span> Advertisement</a> </li>            
            <li><a href="<?php echo Main::ahref("stats")?>"><span class="glyphicon glyphicon-stats"></span> Statistics</a> </li>          
            <li><a href=""><span class="glyphicon glyphicon-cog"></span> Configuration</a>
              <div>
                <a href="<?php echo Main::ahref("settings")?>"><span class="glyphicon glyphicon-wrench"></span> Settings</a>                
                <a href="<?php echo Main::ahref("languages")?>"><span class="glyphicon glyphicon-font"></span> Languages</a>
                <?php if (ENABLE_PLUGINS): ?>
                  <a href="<?php echo Main::ahref("plugins")?>"><span class="glyphicon glyphicon-save"></span> Plugins</a>
                <?php endif ?>
              </div>
            </li>
            <li><a href="<?php echo Main::ahref("themes")?>"><span class="glyphicon glyphicon-eye-open"></span> Themes</a>
              <div>
                <a href="<?php echo Main::ahref("editor")?>"><span class="glyphicon glyphicon-pencil"></span> Template Editor</a>                    
                <a href="<?php echo Main::ahref("themes/less")?>"><span class="glyphicon glyphicon-cog"></span> LESS Editor</a>
                <a href="<?php echo Main::ahref("menu")?>"><span class="glyphicon glyphicon-list"></span> Menu Editor</a>            
              </div>
            </li>
            <li><a href=""><span class="glyphicon glyphicon-wrench"></span> Tools</a>
              <div>
                <a href="<?php echo Main::ahref("tools/sitemap")?>">Generate a sitemap</a> 
                <a href="<?php echo Main::ahref("tools/newsletter")?>">Send Newsletter</a>
                <a href="<?php echo Main::ahref("tools/export")?>">Export Data</a>
                <a href="<?php echo Main::ahref("tools/optimize") ?>">Optimize Database</a>
              </div>
            </li>
            <?php echo Main::plug("admin_sidebar") ?>         
          </ul>           
        </div>
        <div class="col-md-10 main">
          <?php echo Main::message() ?>