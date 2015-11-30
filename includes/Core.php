<?php
/**
 * ====================================================================================
 *                           PremiumMediaScript (c) KBRmedia
 * ----------------------------------------------------------------------------------
 * @copyright - This software is exclusively sold at CodeCanyon.net. If you have downloaded this
 *  from another site or received it from someone else than me, then you are engaged
 *  in illegal activity. You must delete this software immediately or buy a proper
 *  license from http://codecanyon.net/user/KBRmedia/portfolio?ref=KBRmedia.
 *
 *  Thank you for your cooperation and don't hesitate to contact me if anything :)
 * ====================================================================================
 *
 * @author KBRmedia (http://gempixel.com)
 * @link http://gempixel.com 
 * @license http://gempixel.com/license
 * @package Easy_Media_Script
 * @subpackage Core_File
 */
  // Defined Constants
	define("_VERSION","1.5.1");
	define("APP", TRUE);
	define("ENABLE_PLUGINS", FALSE);	
  // Defined Paths
  define("ROOT",dirname(dirname(__FILE__)));
  define("LIB",ROOT."/includes/library");
	define("LANGPATH",ROOT."/includes/languages");
	// Define Media Paths
	define("MEDIA",ROOT."/content/media");
	define("THUMBS",ROOT."/content/thumbs");
	define("PLUGINS",ROOT."/content/plugins");
	// Compress Page
	if($config["gzip"]){
	  ob_start("ob_gzhandler"); 
	}
	// Starts a session
	if(!isset($_SESSION)){
	  session_start();
	}
	// Error Reporting
	if(!isset($config["debug"]) || $config["debug"]==0) {
	  error_reporting(0);
	}else{
		ini_set("display_error", "1");
		ini_set('error_reporting', E_ALL);		
	  error_reporting(-1);
	}

	// If Magic Quotes is ON then Remove Slashes
	if(function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc()) {
	  if($_GET) $_GET = array_map('stripslashes', $_GET);  
	  if($_POST) $_POST = array_map('stripslashes', $_POST);  
	  if($_COOKIE) $_COOKIE = array_map('stripslashes', $_COOKIE);
	} 
	// Define Timezone
	if(!empty($config["timezone"]) || $config["timezone"]!=="RTZ"){
		date_default_timezone_set($config["timezone"]);
	}
	// Core Functions
	include(ROOT."/includes/Functions.php");
	
	// Connect to database
	include(ROOT."/includes/Database.class.php");	
	$db = new Database($config, $dbinfo);
	$config = $db->get_config();


	$config["prefix"] = $dbinfo["prefix"];
	$config["smtp"] = json_decode($config["smtp"],TRUE);
	if(!isset($config["smtp"]["host"])){
		$config["smtp"]["host"] = "";
		$config["smtp"]["port"] = "";
		$config["smtp"]["user"] = "";
		$config["smtp"]["pass"] = "";		
	}
	$config["type"] = json_decode($config["type"],TRUE);
	if(!isset($config["type"]["video"])){
		$config["type"]["video"] = 1;
	}
	if(!isset($config["type"]["music"])){
		$config["type"]["music"] = 1;
	}	
	if(!isset($config["type"]["vine"])){
		$config["type"]["vine"] = 1;
	}	
	if(!isset($config["type"]["picture"])){
		$config["type"]["picture"] = 1;
	}	
	if(!isset($config["type"]["post"])){
		$config["type"]["post"] = 1;
	}	
	if(!isset($config["type"]["blog"])){
		$config["type"]["blog"] = 1;
	}					
	$config["amount_points"] = json_decode($config["amount_points"],TRUE);

	// Defines Template Path
	define("THEME",ROOT."/themes/{$config["theme"]}");
	
	// Application Helper
	include(ROOT."/includes/Main.class.php");
	Main::set("config", $config);

  	// Start Application		
	include(ROOT."/includes/App.class.php");	
	$app = new App($db, $config);	
		
	// Default Language
	$_language=$config["default_lang"];
	// Set Language from Cookie
	if(isset($_COOKIE["lang"])) $_language=Main::clean($_COOKIE["lang"],3,TRUE);	
	// Set Language
	if(isset($_GET["lang"]) && strlen($_GET["lang"])=="2"){
		setcookie("lang",strip_tags($_GET["lang"]), strtotime('+30 days'), '/', NULL, 0);
		$_language = Main::clean($_GET["lang"],3,TRUE);
	}		
	// Get Language File
	if(isset($_language) && $_language!="en" && file_exists(ROOT."/includes/languages/".Main::clean($_language,3,TRUE).".php")) {
  	include(ROOT."/includes/languages/".Main::clean($_language).".php");
  	if(isset($lang) && is_array($lang)) {
  		Main::set("lang",$lang);
  		$app->lang = $_language;
  	}
	}
