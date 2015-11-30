<?php 
/**
 * ====================================================================================
 *                           Easy Media Script (c) KBRmedia
 * ----------------------------------------------------------------------------------
 * @copyright This software is exclusively sold at CodeCanyon.net. If you have downloaded this
 *  from another site or received it from someone else than me, then you are engaged
 *  in an illegal activity. You must delete this software immediately or buy a proper
 *  license from http://codecanyon.net/user/KBRmedia/portfolio?ref=KBRmedia.
 *
 *  Thank you for your cooperation and don't hesitate to contact me if anything :)
 * ====================================================================================
 */
// Define your API Key here
define("APIKEY", "KEY HERE");
define("USERID", "USER ID HERE");

// Don't edit anything below
function shorten_url($url)  {  
	$ch = curl_init();  
	$timeout = 5;  
	$url = urlencode($url);
	curl_setopt($ch,CURLOPT_URL,'http://api.adf.ly/api.php?key='.APIKEY.'&uid='.USERID.'&advert_type=int&domain=adf.ly&url='.$url);  
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);  
	$data = curl_exec($ch);  
	curl_close($ch); 
	return $data;  
}