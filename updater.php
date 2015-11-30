<?php  
/**
 * ====================================================================================
 *                           PREMIUM URL SHORTENER (c) KBRmedia
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
 * @package Premium URL Shortener
 * @subpackage Application updater
 */
	$step = 1;
	$message="";
	if(isset($_GET["step"]) && is_numeric($_GET["step"]) && $_GET["step"]<3){
		$step=$_GET["step"];
	}
	if($step==2){		
		include("includes/Config.php");
    $db = new PDO("mysql:host=".$dbinfo["host"].";dbname=".$dbinfo["db"]."", $dbinfo["user"], $dbinfo["password"]);
    $query=get_query($dbinfo);

		foreach ($query as $q) {
		 	$db->query($q);
		} 	
		header("Location: index.php"); 
		$_SESSION["msg"]="success::Database was successfully updated. Enjoy the new features!";
		if(file_exists("install.php")){
			unlink('install.php');
		}
		unlink(__FILE__);
	}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Premium Media Script Updater</title>
	<style type="text/css">
		body{background:#E2E6E7;font-family:Helvetica, Arial;width:860px;line-height:25px;font-size:13px;margin:0 auto;}a{color:#009ee4;font-weight:700;text-decoration:none;}a:hover{color:#000;text-decoration:none;}.container{background:#424451;border:1px solid #D6D6D6;border-radius:3px;display:block;overflow:hidden;margin:50px 0;}.container h1{font-size:22px;display:block;border-bottom:1px solid #eee;margin:0!important;padding:10px;}.container h2{color:#999;font-size:18px;margin:10px;}.container h3{background:#353742;color:#fff;border-bottom:1px solid #282B33;border-radius:3px 0 0 0;text-align:center;margin:0;padding:10px 0;}.left{float:left;width:258px; color:#fff;}.right{float:left;width:599px;border-left:1px solid #282B33; background: #fff;}.form{width:90%;display:block;padding:10px;}.form label{font-size:15px;font-weight:700;margin:5px 0;}.form label a{float:right;color:#009ee4;font:bold 12px Helvetica, Arial; padding-top: 5px;}.form .input{display:block;width:98%;height:15px;border:1px #ccc solid;font:bold 15px Helvetica, Arial;color:#aaa;border-radius:2px;box-shadow:inset 1px 1px 3px #ccc,0 0 0 3px #f8f8f8;margin:10px 0;padding:10px;}.form .input:focus{border:1px #73B9D9 solid;outline:none;color:#222;box-shadow:inset 1px 1px 3px #ccc,0 0 0 3px #DEF1FA;}.form .button{height:35px;}.button{background:#0080FF;height:20px;width:90%;display:block;text-decoration:none;text-align:center;border-radius: 2px;color:#fff;font:15px Helvetica, Arial bold;cursor:pointer;border-radius:3px;margin:30px auto;padding:5px 0;border:0;width: 98%;}.button:active,.button:hover{background:#0069D2;color:#fff;}.content{color:#999;display:block;border-top:1px solid #eee;margin:10px 0;padding:10px 25px;}li{color:#D0D2D9;}li.current{color:#FFFFFF;font-weight:700;}li span{float:right;margin-right:10px;font-size:11px;font-weight:700;color:#00B300;}.left > p{border-top:1px solid #282B33;color:#949AAB;font-size:12px;margin:0;padding:10px;}.left > p > a{color:#fff;}.content > p{color:#222;font-weight:700;}span.ok{float:right;border-radius:3px;background:#00B300;color:#fff;padding:2px 10px;}span.fail{float:right;border-radius:3px;background:#B30000;color:#fff;padding:2px 10px;}span.warning{float:right;border-radius:3px;background:#D27900;color:#fff;padding:2px 10px;}.message{background:#1F800D;color:#fff;font:bold 15px Helvetica, Arial;border:1px solid #000;padding:10px;}.error{background:#980E0E;color:#fff;font:bold 15px Helvetica, Arial;border-bottom:1px solid #740C0C;border-top:1px solid #740C0C;margin:0;padding:10px;}.inner,.right > p{margin:10px;}	
	</style>
  </head>
  <body>
  	<div class="container">
  		<div class="left">
			<h3>Updating to 1.5.1</h3>
			<ol>
				<li<?php echo ($step=="1")?" class='current'":""?>>Update Information <?php echo ($step>"1")?"<span>Complete</span>":"" ?></li>				
				<li<?php echo ($step=="2")?" class='current'":""?>>Update Complete</li>
			</ol>
			<p>
				<a href="http://gempixel.com/" target="_blank">Home</a> | 
				<a href="http://support.gempixel.com/" target="_blank">Support</a> | 
				<a href="http://gempixel.com/profile" target="_blank">Profile</a> <br />
				<?php echo date("Y") ?> &copy; <a href="http://gempixel.com" target="_blank">KBRmedia</a> - All Rights Reserved
			</p>
  		</div>
  		<div class="right">
				<h1>Upgrading Premium Media Script to 1.5.1</h1> 
				<p>
					You are about to upgrade this software to version <strong>1.5.1</strong>. Please note that this will only update your database and NOT your files. It is strongly recommended that you first backup your database then your existing files in case something unexpected occurs. 
				</p>
				<p>
					Version 1.5.1 adds many new functionality including improvements in performance, features and security. For this reason, <strong>a lot of files</strong> were updated. <strong>Please read</strong> the update manual carefully in order to make sure the update is done as smoothly as possible. 
				</p>			
				<p>					
					If you have made a lot of changes to the script and wish to keep those changes, <strong>DO NOT UPDATE</strong> as this will completely overwrite the affected files. Also if you are happy with the current version, <strong>don't update</strong>. Otherwise, click the button below to proceed. <strong>Please make sure that this file is deleted at the end.</strong>
				</p>

				<a href="updater.php?step=2" class="button">I am ready, please update my database</a>		
  		</div>  		
  	</div>
  </body>
</html>
<?php 
function get_query($dbinfo){
		// New Stuff 1.5.1
		$query[]="INSERT INTO `{$dbinfo["prefix"]}setting` (`config` ,`value`) VALUES
						('count_media', '');";	
		// New Stuff 1.5
		$query[]="ALTER TABLE `{$dbinfo["prefix"]}blog` ADD `approved` int(1) NOT NULL DEFAULT '1'";
  	$query[]="ALTER TABLE `{$dbinfo["prefix"]}blog` ADD `userid` int(11) NOT NULL DEFAULT '1'";
		$query[]="ALTER TABLE `{$dbinfo["prefix"]}media` ADD `duration` int(9) NOT NULL DEFAULT '0'";
		$query[]="INSERT INTO `{$dbinfo["prefix"]}setting` (`config` ,`value`) VALUES
						('yt_api', ''),
						('vm_api', ''),
						('dm_api', ''),
						('merge_comments', '0'),
						('aws', ''),
						('api', '1'),
						('api_key', '')";	

	// New Stuff 1.4
	$query[]="ALTER TABLE `{$dbinfo["prefix"]}comment` ADD `type` enum('media', 'post') NOT NULL DEFAULT 'media'";
	$query[]="INSERT INTO `{$dbinfo["prefix"]}setting` (`config` ,`value`) VALUES
						('upload', '1'),
						('ga', ''),
						('color', '#f8cb1c');";	
						
	$query [] = "CREATE TABLE IF NOT EXISTS `{$dbinfo["prefix"]}playlist` (
							  `id` int(11) NOT NULL AUTO_INCREMENT,
							  `uniqueid` varchar(255) DEFAULT NULL,
							  `userid` int(11) DEFAULT NULL,
							  `lastid` int(11) DEFAULT NULL,
							  `name` varchar(255) DEFAULT NULL,
							  `description` text,
							  `public` int(11) DEFAULT NULL,
							  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
							  `num` int(11) NOT NULL DEFAULT '0',
							  PRIMARY KEY (`id`)
							) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

	$query[] = "CREATE TABLE IF NOT EXISTS `{$dbinfo["prefix"]}toplaylist` (
							  `id` int(11) NOT NULL AUTO_INCREMENT,
							  `playlistid` int(11) DEFAULT NULL,
							  `mediaid` int(11) DEFAULT NULL,
							  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
							  PRIMARY KEY (`id`)
							) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	// New Stuff 1.4
	$query[]="ALTER TABLE `{$dbinfo["prefix"]}media` ADD `subscribe` int(1) NOT NULL DEFAULT '0'";
	$query[]="ALTER TABLE `{$dbinfo["prefix"]}category` ADD `parentid` int(12) NOT NULL DEFAULT '0'";

	$query[]="INSERT INTO `{$dbinfo["prefix"]}setting` (`config` ,`value`) VALUES
						('menus', ''),
						('plugins', ''),
						('extra', '');";
							
	// Add new Tables
	$query[] = "CREATE TABLE IF NOT EXISTS `{$dbinfo["prefix"]}ads` (
							  `id` int(11) NOT NULL AUTO_INCREMENT,
							  `name` varchar(255) NOT NULL,
							  `type` enum('728','468','300','resp','preroll') NOT NULL,
							  `code` text NOT NULL,
							  `impression` int(12) NOT NULL DEFAULT '0',
							  `enabled` enum('0','1') NOT NULL DEFAULT '1',
							  PRIMARY KEY (`id`)
							) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT= 1 ;";

	$query[] ="CREATE TABLE IF NOT EXISTS `{$dbinfo["prefix"]}point` (
						  `id` int(11) NOT NULL AUTO_INCREMENT,
						  `userid` int(11) NOT NULL,
						  `actionid` int(12) NOT NULL,
						  `action` varchar(255) NOT NULL,
						  `point` int(11) NOT NULL,
						  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
						  PRIMARY KEY (`id`)
						) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";


	$query[]="INSERT INTO `{$dbinfo["prefix"]}setting` (`config` ,`value`) VALUES
						('points', '0'),
						('amount_points', '{\"submit\":\"100\",\"comment\":\"2\",\"register\":\"50\",\"like\":\"5\",\"subscribe\":\"25\"}');";

		
	$query[]="ALTER TABLE  `{$dbinfo["prefix"]}media` CHANGE  `embed`  `embed` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;";
	$query[]="ALTER TABLE `{$dbinfo["prefix"]}user` ADD `points` bigint(100) NOT NULL DEFAULT '0'";

	return $query;
}

?>