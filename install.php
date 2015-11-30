<?php 
/**
 * ====================================================================================
 *                           PREMIUM Media Script (c) KBRmedia
 * ----------------------------------------------------------------------------------
 * @copyright This software is exclusively sold at CodeCanyon.net. If you have downloaded this
 *  from another site or received it from someone else than me, then you are engaged
 *  in an illegal activity. You must delete this software immediately or buy a proper
 *  license from http://gempixel.com/buy/short.
 *
 *  Thank you for your cooperation and don't hesitate to contact me if anything :)
 * ====================================================================================
 *
 * @author KBRmedia (http://gempixel.com)
 * @link http://gempixel.com 
 * @package Premium Media Script
 * @subpackage Application installer
 */
	if(!isset($_SESSION)) session_start();
	$error="";
	$message=(isset($_SESSION["msg"])?$_SESSION["msg"]:"");
	if(!isset($_GET["step"]) || $_GET["step"]=="1" || $_GET["step"] < "1"){
		$step = "1";
	}elseif($_GET["step"] > "1" && $_GET["step"]<="5"){
		$step = $_GET["step"];
	}else{
		die("Oups. Looks like you did not follow the instructions! Please follow the instructions otherwise you will not be able to install this script.");
	}
	switch ($step) {
		case '2':
			if(file_exists("includes/Config.php")) $error='Configuration file already exists. Please delete or rename "Config.php" and recopy "Config.sample.php" from the original zip file. You cannot continue until you do this.';  

			if(isset($_POST["step2"])){
					if (empty($_POST["host"]))  $error.="<p>- You forgot to enter your host.</p>"; 
          if (empty($_POST["name"])) $error.="<p>- You forgot to enter your database name.</p>"; 
          if (empty($_POST["user"])) $error.="<p>- You forgot to enter your username.</p>"; 
	        if(empty($error)){
					 try{
					    $db = new PDO("mysql:host=".$_POST["host"].";dbname=".$_POST["name"]."", $_POST["user"], $_POST["pass"]);
							generate_config($_POST);
							foreach (get_query() as $q) {
							  $db->query($q);
							} 
							$_SESSION["msg"]="Database has been successfully imported and configuration file has been created.";
							header("Location: install.php?step=3");
					  }catch (PDOException $e){
					    $error = $e->getMessage();
					  }
          }							
			}
		break;
		case '3':			
			@include("includes/Config.php");
				if(!file_exists("includes/Config.php")) $error .="<div class='error'>The file includes/Config.php cannot be found. If the file includes/Config.sample.php exists rename that to includes/Config.php</div>";			
				$_SESSION["msg"]="";
			    if(isset($_POST["step3"])){
			            if (empty($_POST["email"]))  $error.="<div class='error'>You forgot to enter your email.</div>"; 
			            if (empty($_POST["pass"])) $error.="<div class='error'>You forgot to enter your password.</div>"; 
			            if (empty($_POST["url"])) $error.="<div class='error'>You forgot to enter the url.</div>"; 
			    	if(!$error){

			    	$data=array(
				    	":admin"=>"1",
				    	":email"=>$_POST["email"],
				    	":username"=>$_POST["username"],
				    	":password"=>Main::encode($_POST["pass"]),
				    	":active"=>"1",
				    	":verifno"=> Main::strrand(20),
				    	":name" => "Mr. Admin",
				    	":auth_key" => Main::encode(Main::strrand())
			    	);

					  $db->insert("user",$data);					  
					  $db->update("setting",array("value"=>"?"),array("config"=>"?"),array($_POST["url"],"url"));
					  $db->update("setting",array("value"=>"?"),array("config"=>"?"),array($_POST["email"],"email"));
					  $_SESSION["msg"]="Your admin account has been successfully created.";
					  $_SESSION["site"]=$_POST["url"];
					  $_SESSION["username"]=$_POST["username"];
					  $_SESSION["email"]=$_POST["email"];
					  $_SESSION["password"]=$_POST["pass"];
					  header("Location: install.php?step=4"); 
			        }   
			    }		
		break;
		case '4':
			$_SESSION["msg"]="";
			@include("includes/Config.php");
		break;
		case '5':
			header("Location: index.php"); 
			unset($_SESSION);
			unlink(__FILE__);
			
			if(file_exists("main.zip")){
				unlink('main.zip');
			}
			if(file_exists("updater.php")){
				unlink('updater.php');
			}
		break;
	}
 ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Premium Media Script Installation</title>
	<style type="text/css">
		body{background:#E2E6E7;font-family:Helvetica, Arial;width:860px;line-height:25px;font-size:13px;margin:0 auto;}a{color:#009ee4;font-weight:700;text-decoration:none;}a:hover{color:#000;text-decoration:none;}.container{background:#424451;border:1px solid #D6D6D6;border-radius:3px;display:block;overflow:hidden;margin:50px 0;}.container h1{font-size:22px;display:block;border-bottom:1px solid #eee;margin:0!important;padding:10px;}.container h2{color:#999;font-size:18px;margin:10px;}.container h3{background:#353742;color:#fff;border-bottom:1px solid #282B33;border-radius:3px 0 0 0;text-align:center;margin:0;padding:10px 0;}.left{float:left;width:258px; color:#fff;}.right{float:left;width:599px;border-left:1px solid #282B33; background: #fff;}.form{width:90%;display:block;padding:10px;}.form label{font-size:15px;font-weight:700;margin:5px 0;}.form label a{float:right;color:#009ee4;font:bold 12px Helvetica, Arial; padding-top: 5px;}.form .input{display:block;width:98%;height:15px;border:1px #ccc solid;font:bold 15px Helvetica, Arial;color:#aaa;border-radius:2px;box-shadow:inset 1px 1px 3px #ccc,0 0 0 3px #f8f8f8;margin:10px 0;padding:10px;}.form .input:focus{border:1px #73B9D9 solid;outline:none;color:#222;box-shadow:inset 1px 1px 3px #ccc,0 0 0 3px #DEF1FA;}.form .button{height:35px;}.button{background:#0080FF;height:20px;width:90%;display:block;text-decoration:none;text-align:center;border-radius: 2px;color:#fff;font:15px Helvetica, Arial bold;cursor:pointer;border-radius:3px;margin:30px auto;padding:5px 0;border:0;width: 98%;}.button:active,.button:hover{background:#0069D2;color:#fff;}.content{color:#999;display:block;border-top:1px solid #eee;margin:10px 0;padding:10px 25px;}li{color:#D0D2D9;}li.current{color:#FFFFFF;font-weight:700;}li span{float:right;margin-right:10px;font-size:11px;font-weight:700;color:#00B300;}.left > p{border-top:1px solid #282B33;color:#949AAB;font-size:12px;margin:0;padding:10px;}.left > p > a{color:#fff;}.content > p{color:#222;font-weight:700;}span.ok{float:right;border-radius:3px;background:#00B300;color:#fff;padding:2px 10px;}span.fail{float:right;border-radius:3px;background:#B30000;color:#fff;padding:2px 10px;}span.warning{float:right;border-radius:3px;background:#D27900;color:#fff;padding:2px 10px;}.message{background:#1F800D;color:#fff;font:bold 15px Helvetica, Arial;border:1px solid #000;padding:10px;}.error{background:#980E0E;color:#fff;font:bold 15px Helvetica, Arial;border-bottom:1px solid #740C0C;border-top:1px solid #740C0C;margin:0;padding:10px;}.inner,.right > p{margin:10px;}	
	</style>
  </head>
  <body>
  	<div class="container">
  		<div class="left">
			<h3>Installation Process</h3>
			<ol>
				<li<?php echo ($step=="1")?" class='current'":""?>>Requirement Check <?php echo ($step>"1")?"<span>Completed</span>":"" ?></li>
				<li<?php echo ($step=="2")?" class='current'":""?>>Database Configuration<?php echo ($step>"2")?"<span>Completed</span>":"" ?></li>
				<li<?php echo ($step=="3")?" class='current'":""?>>Basic Configuration<?php echo ($step>"3")?"<span>Completed</span>":"" ?></li>
				<li<?php echo ($step=="4")?" class='current'":""?>>Installation Complete</li>
			</ol>
			<p>
				<a href="http://gempixel.com/" target="_blank">Home</a> | 
				<a href="http://support.gempixel.com/" target="_blank">Support</a> | 
				<a href="http://gempixel.com/profile" target="_blank">Profile</a> <br />
				2012-<?php echo date("Y") ?> &copy; <a href="http://gempixel.com" target="_blank">KBRmedia</a> - All Rights Reserved
			</p>
  		</div>
  		<div class="right">
			<h1>Installation of Premium Media Script</h1> 
			<?php if(!empty($message)) echo "<div class='message'>$message</div>"; ?>
			<?php if(!empty($error)) echo "<div class='error'>$error</div>"; ?>
			<?php if($step=="1"): ?>		
				<h2>1.0 Requirement Check</h2>
				<div class="content">
					These are some of the important requirements for this software. "Red" means it is vital to this script, "Orange" means it is required but not vital and "Green" means it is good. If one of the checks is "Red", you will not be able to install this script because without that requirement, the script will not work.
				</div>
				<div class="content">
					<p>
					PHP Version (need at least version 5.3.7)
					<?php echo check('version')?>
					</p>
					It is very important to have at least PHP Version 5.3.7. It is strongly recommended that you use PHP 5.4 otherwise <strong>Facebook</strong> Login will <strong>not work</strong>.
				</div>
				<div class="content">
					<p>PDO Driver must be enabled 
						<?php echo check('pdo')?>
					</p>
					PDO driver is very important so it must enabled. Without this, the script will not connect to the database hence it will not work at all. If this check fails, you will need to contact your web host and ask them to either enable it or configure it properly.
				</div>					
				<div class="content">
					<p><i>Config.sample.php</i> must be accessible. 
						<?php echo check('config')?>
					</p>
					This installation will open that file to put values in so it must be accessible. Make sure that file is there in the <b>includes</b> folder and is writable.
				</div>		
				<div class="content">
					<p><i>content/</i> folder must writable. 
						<?php echo check('content')?>
					</p>
					Many things will be uploaded to that folder so please make sure it has the proper permission.
				</div>												
				<div class="content">
					<p><i>allow_url_fopen</i> Enabled
						<?php echo check('file')?>
					</p>
					The function <strong>file_get_contents</strong> is used to get videos from Youtube and fetch video info using an API.. This function is not required but some features may not work properly.
				</div>
				<div class="content">
					<p>cURL Enabled <?php echo check('curl')?></p>
					cURL is mainly used to get videos from Youtube and fetch video info using an API.
				</div>				
			<?php if(!$error) echo '<a href="?step=2" class="button">Requirements are met. You can now Proceed.</a>'?>
		<?php elseif($step=="2"): ?>	
		<h2>2.0 Database Configuration</h2>
		<p>
			Now you have to set up your database by filling the following fields. Make sure you fill them correctly.
		</p>
		<form method="post" action="?step=2" class="form">
		    <label>Database Host <a>Usually it is localhost.</a></label>
		    <input type="text" name="host" class="input" required />
		    
		    <label>Database Name</label>
		    <input type="text" name="name" class="input" required />
		    
		    <label>Database User </label>
		    <input type="text" name="user" class="input" required />    
		    
		    <label>Database Password</label>
		    <input type="password" name="pass" class="input" />   

		    <label>Database Prefix <a>Prefix for your tables (Optional) e.g. media_</a></label>
		    <input type="text" name="prefix" class="input" value="" />       

		    <label>Security Key (Keep this secret) <a>This should never be changed!</a></label>
		    <input type="text" name="key" class="input" value="<?php echo md5(rand(0,100000)) ?>" />   

		    <button type="submit" name="step2" class='button'>Create my configuration file and go to step 3</button>    
		</form>
		<?php elseif($step=="3"): ?>
		<p>
			Now you have to create an admin account by filling the fields below. Make sure to add a valid email and a strong password. For the site URL, make sure to remove the last slash.
		</p>
		  <form method="post" action="?step=3" class="form">
		        <label>Admin Email</label>
		        <input type="text" name="email" class="input" required />

		        <label>Admin Username</label>
		        <input type="text" name="username" class="input" required />	

		        <label>Admin Password</label>
		        <input type="password" name="pass" class="input" required />   

		        <label>Site URL <a>Including http:// but without the ending slash "/"</a></label>
		        <input type="text" name="url" class="input" value="<?php echo get_domain() ?>" placeholder="http://" required />  

		        <input type="submit" name="step3" value="Finish Up Installation" class='button' />     
		  </form>		
		<?php elseif($step=="4"): ?>
	       <p>
 				The script has been successfully installed and your admin account has been created. Please click "Delete Install" button below to attempt to delete this file. Please make sure that it has been successfully deleted. 
	       </p>
	       <p>
	       	  Once clicked, you may see a blank page otherwise you will be redirected to your main page. If you see a blank, don't worry it is normal. All you have to do is to go to your main site, login using the info below and configure your site by clicking the "Admin" menu and then "Settings". Thanks for your purchase and enjoy :)
	       </p>
	       <p>
	       <strong>Login URL: <a href="<?php get('site') ?>/user/login" target="_blank"><?php get('site') ?>/user/login</a></strong> <br />
	       <strong>Email: <?php get('email') ?></strong> <br />
	       <strong>Username: <?php get('username') ?></strong> <br />
	       <strong>Password: <?php get('password') ?></strong>
	       </p>	       
	       <a href="?step=5" class="button">Delete install.php</a>	       
		<?php endif; ?>
  		</div>  		
  	</div>
  </body>
</html>
<?php 
function get_domain(){
	$url="http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$url=str_replace("/install.php?step=3", "", $url);
	return $url;
	//return "http://{$url[2]}/{$url[3]}";
}
function get($what){
	if(isset($_SESSION[strip_tags(trim($what))])){
		echo $_SESSION[strip_tags(trim($what))];
	}
}
function check($what){
	switch ($what) {
		case 'version':
			if(version_compare(PHP_VERSION, "5.3.7",'>=')){
				return "<span class='ok'>You have ".PHP_VERSION."</span>";
			}else{
				global $error;
				$error.=1;
				return "<span class='fail'>You have ".PHP_VERSION."</span>";
			}
			break;
		case 'config':
			if(@file_get_contents('includes/Config.sample.php') && is_writable('includes/Config.sample.php')){
				return "<span class='ok'>Accessible</span>";
			}else{
				global $error;
				$error.=1;
				return "<span class='fail'>Not Accessible</span>";
			}
			break;
		case 'content':
			if(is_writable('content')){
				return "<span class='ok'>Accessible</span>";
			}else{
				global $error;
				$error.= 1;
				return "<span class='fail'>Not Accessible</span>";
			}
			break;			
		case 'pdo':
			if(defined('PDO::ATTR_DRIVER_NAME') && class_exists("PDO")){
				return "<span class='ok'>Enabled</span>";
			}else{
				global $error;
				$error.= 1;
				return "<span class='fail'>Disabled</span>";
			}
			break;
		case 'file':
			if(ini_get('allow_url_fopen')){
				return "<span class='ok'>Enabled</span>";
			}else{
				return "<span class='warning'>Disabled</span>";
			}
			break;	
		case 'curl':
			if(in_array('curl', get_loaded_extensions())){
				return "<span class='ok'>Enabled</span>";
			}else{
				return "<span class='warning'>Disabled</span>";
			}
			break;						
	}
}
function get_query(){
$query[] = "CREATE TABLE IF NOT EXISTS `".trim($_POST["prefix"])."ads` (
  `id` int(11) NULL AUTO_INCREMENT,
  `name` varchar(255) NULL,
  `type` enum('728','468','300','resp','preroll') NULL,
  `code` text NULL,
  `impression` int(12) NULL DEFAULT '0',
  `enabled` enum('0','1') NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT= 1 ;";

$query[] = "CREATE TABLE IF NOT EXISTS `".trim($_POST["prefix"])."blog` (
  `id` int(11) NULL AUTO_INCREMENT,
  `userid` int(11) NULL DEFAULT '1',
  `approved` int(1) NULL DEFAULT '1',
  `publish` int(1) NULL DEFAULT '1',
  `slug` varchar(255) NULL,
  `name` varchar(250) NULL,
  `meta_title` varchar(255) NULL,
  `meta_description` varchar(300) NULL,
  `content` text NULL,
  `date` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT= 1 ;";

$query[] = "CREATE TABLE IF NOT EXISTS `".trim($_POST["prefix"])."category` (
  `id` bigint(11) NULL AUTO_INCREMENT,
  `parentid` bigint(12) NULL DEFAULT '0',
  `type` varchar(16) NULL,
  `name` varchar(60) NULL,
  `description` varchar(255) NULL,
  `slug` varchar(60) NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

$query[] = "INSERT INTO `".trim($_POST["prefix"])."category` (`type`,`name`,`description`,`slug`) VALUES ('video','General','','general');";

$query[] = "CREATE TABLE IF NOT EXISTS `".trim($_POST["prefix"])."comment` (
  `id` int(11) NULL AUTO_INCREMENT,
  `mediaid` bigint(11) NULL,
  `userid` bigint(11) NULL,
  `parentid` bigint(20) NULL DEFAULT '0',
  `date` timestamp DEFAULT CURRENT_TIMESTAMP,
  `body` text NULL,
  `type` enum('media', 'post') NULL DEFAULT 'media',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

$query[] = "CREATE TABLE IF NOT EXISTS `".trim($_POST["prefix"])."favorite` (
  `id` int(11) NULL AUTO_INCREMENT,
  `userid` bigint(11) unsigned NULL,
  `mediaid` bigint(11) unsigned NULL,
  `date` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";


$query[] = "CREATE TABLE IF NOT EXISTS `".trim($_POST["prefix"])."media` (
  `id` int(11) NULL AUTO_INCREMENT,
  `uniqueid` varchar(255) NULL,
  `type` varchar(7) NULL DEFAULT 'video',
  `catid` int(11) NULL DEFAULT '1',
  `featured` int(11) NULL DEFAULT '0',
  `title` varchar(128) NULL,
  `url` varchar(128) NULL,
  `description` text NULL,
  `file` varchar(128) NULL DEFAULT '',
  `link` text NULL,
  `embed` text NULL,
  `thumb` varchar(255) NULL,
  `ext_thumb` text NULL,
  `userid` mediumint(8) NULL DEFAULT '0',
  `date` timestamp DEFAULT CURRENT_TIMESTAMP,
  `nsfw` int(1) NULL DEFAULT '0',
  `votes` int(12) NULL DEFAULT '0',
  `views` bigint(100) NULL DEFAULT '0',
  `tags` text NULL,
  `approved` int(1) NULL DEFAULT '1',
  `likes` bigint(12) NULL DEFAULT '0',
  `dislikes` bigint(12) NULL DEFAULT '0',
  `comments` int(11) NULL DEFAULT '0',
  `source` text NULL,
  `subscribe` int(1) NULL DEFAULT '0',
  `duration` int(9) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `userid` (`userid`),
  FULLTEXT KEY `title` (`title`,`description`,`tags`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";


$query[] = "CREATE TABLE IF NOT EXISTS `".trim($_POST["prefix"])."page` (
  `id` int(11) NULL AUTO_INCREMENT,
  `publish` int(1) DEFAULT '1',
  `slug` varchar(255) NULL,
  `name` varchar(250) NULL,
  `meta_title` varchar(255) NULL,
  `meta_description` varchar(300) NULL,
  `content` text NULL,
  `menu` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

$query[] ="CREATE TABLE IF NOT EXISTS `".trim($_POST["prefix"])."point` (
  `id` int(11) NULL AUTO_INCREMENT,
  `userid` int(11) NULL,
  `actionid` int(12) NULL,
  `action` varchar(255) NULL,
  `point` int(11) NULL,
  `date` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

$query[] = "CREATE TABLE IF NOT EXISTS `".trim($_POST["prefix"])."rating` (
  `id` int(11) NULL AUTO_INCREMENT,
  `userid` mediumint(8) unsigned NULL DEFAULT '0',
  `mediaid` mediumint(8) unsigned NULL DEFAULT '0',
  `rating` varchar(10) NULL,
  `date` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";


$query[] = "CREATE TABLE IF NOT EXISTS `".trim($_POST["prefix"])."setting` (
  `config` varchar(255) NULL,
  `value` longtext NULL,
  PRIMARY KEY (`config`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

	$query [] = "CREATE TABLE IF NOT EXISTS `".trim($_POST["prefix"])."playlist` (
							  `id` int(11) NULL AUTO_INCREMENT,
							  `uniqueid` varchar(255) DEFAULT NULL,
							  `userid` int(11) DEFAULT NULL,
							  `lastid` int(11) DEFAULT NULL,
							  `name` varchar(255) DEFAULT NULL,
							  `description` text,
							  `public` int(11) DEFAULT NULL,
							  `date` timestamp DEFAULT CURRENT_TIMESTAMP,
							  `num` int(11) NULL DEFAULT '0',
							  PRIMARY KEY (`id`)
							) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

	$query[] = "CREATE TABLE IF NOT EXISTS `".trim($_POST["prefix"])."toplaylist` (
							  `id` int(11) NULL AUTO_INCREMENT,
							  `playlistid` int(11) DEFAULT NULL,
							  `mediaid` int(11) DEFAULT NULL,
							  `date` timestamp DEFAULT CURRENT_TIMESTAMP,
							  PRIMARY KEY (`id`)
							) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

$query[] = "INSERT INTO `".trim($_POST["prefix"])."setting` (`config`, `value`) VALUES
('url', ''),
('title', ''),
('description', ''),
('keywords', ''),
('logo', ''),
('default_lang', 'en'),
('email', ''),
('twitter', ''),
('facebook', ''),
('google', ''),
('require_activation', '1'),
('captcha', '0'),
('maintenance', '0'),
('homelimit', '8'),
('pagelimit', '16'),
('rsslimit', '25'),
('sharing', '1'),
('shorturl', 'system'),
('custom_shorturl', ''),
('comments', '1'),
('comment_sys', 'system'),
('disqus_username', ''),
('comment_blacklist', ''),
('ads', '1'),
('ad300', ''),
('ad468', ''),
('ad728', ''),
('adrep', ''),
('adpreroll', ''),
('preroll_timer', ''),
('user', '1'),
('submission', '2'),
('user_activate', '0'),
('captcha_public', ''),
('captcha_private', ''),
('fb_connect', '1'),
('facebook_app_id', ''),
('facebook_secret', ''),
('tw_connect', '1'),
('twitter_secret', ''),
('twitter_key', ''),
('gl_connect', '1'), 
('google_cid', ''),
('google_cs', ''),
('offline_message', ''),
('theme', 'default'),
('local_thumbs', '1'),
('font', ''),
('update_notification', '0'),
('smtp', '{\"host\":\"\",\"port\":\"\",\"user\":\"\",\"pass\":\"\"}'),
('autoapprove', '0'),
('max_size', '10'),
('mode', 'grid'),
('type', '{\"video\":\"1\",\"music\":\"1\",\"vine\":\"1\",\"picture\":\"1\",\"blog\":\"1\"}'),
('player', 'videojs'),
('points', '0'),
('amount_points', '{\"submit\":\"100\",\"comment\":\"2\",\"register\":\"50\",\"like\":\"5\",\"subscribe\":\"25\"}'),
('menus', ''),
('plugins', ''),
('extra', ''),
('upload', '1'),
('ga', ''),
('color', '#f8cb1c'),
('yt_api', ''),
('vm_api', ''),
('dm_api', ''),
('merge_comments', '0'),
('aws', ''),
('api', '1'),
('api_key', '');";


$query[] = "CREATE TABLE IF NOT EXISTS `".trim($_POST["prefix"])."subscription` (
  `id` int(11) NULL AUTO_INCREMENT,
  `authorid` int(11) NULL,
  `userid` int(11) NULL,
  `date` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

$query[] ="CREATE TABLE IF NOT EXISTS `".trim($_POST["prefix"])."temp` (
  `id` int(11) NULL AUTO_INCREMENT,
  `type` varchar(255) NULL,
  `filter` varchar(10) NULL,
  `data` text NULL,
  `viewed` int(1) NULL DEFAULT '0',
  `date` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='This table is used to store temporary data.' AUTO_INCREMENT=1 ;";

$query[] = "CREATE TABLE IF NOT EXISTS `".trim($_POST["prefix"])."user` (
  `id` int(255) NULL AUTO_INCREMENT,
  `auth` varchar(255) NULL DEFAULT 'system',
  `authid` varchar(255) NULL,
  `name` varchar(60) NULL DEFAULT '',
  `dob` varchar(255) NULL,
  `admin` int(1) NULL DEFAULT '0' COMMENT 'Admin?',
  `username` varchar(20) NULL DEFAULT '',
  `email` varchar(50) NULL DEFAULT '',
  `password` varchar(255) NULL,
  `avatar` varchar(255) NULL,
  `active` int(1) NULL DEFAULT '0',
  `lastlogin` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `country` varchar(255) NULL DEFAULT '',
  `digest` int(1) NULL DEFAULT '0',
  `date` timestamp DEFAULT CURRENT_TIMESTAMP,
  `verifno` varchar(20) NULL,
  `auth_key` varchar(255) NULL,
  `public` int(1) NULL DEFAULT '1',
  `profile` text NULL,
  `subscribers` bigint(20) NULL DEFAULT '0',
  `points` bigint(100) NULL DEFAULT '0',
  `nsfw` int(1) NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `nick` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

return $query;
}
function generate_config($array){
	if(!empty($array)){
	    $file=file_get_contents('includes/Config.sample.php');
	    $file=str_replace("RHOST",trim($array["host"]),$file);
	    $file=str_replace("RDB",trim($array["name"]),$file);
	    $file=str_replace("RUSER",trim($array["user"]),$file);
	    $file=str_replace("RPASS",trim($array["pass"]),$file);                
	    $file=str_replace("RPRE",trim($array["prefix"]),$file);  
	    $file=str_replace("RTZ",trim($array["tz"]),$file);  
	    $file=str_replace("RPUB",trim(md5(api())),$file);
	    $file=str_replace("RKEY",trim($array["key"]),$file);
	    $fh = fopen('includes/Config.sample.php', 'w') or die("Can't open Config.sample.php. Make sure it is writable.");
	    fwrite($fh, $file);
	    fclose($fh); 
	    rename("includes/Config.sample.php", "includes/Config.php");
	}
}
function api(){
          $l='12';
          $api="";
          $r= "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"; 
            srand((double)microtime()*1000000); 
            for($i=0; $i<$l; $i++) { 
              $api.= $r[rand()%strlen($r)]; 
            } 
          return $api;    
      }
?>
