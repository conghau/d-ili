<?php 
/**
 * ====================================================================================
 *                           PremiumMediaScript (c) KBRmedia
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
 * @package PremiumMediaScript
 * @subpackage ApplicationHelper
 */
class Main{
    protected static $title = "";
    protected static $description = "";
    protected static $url = "";
    protected static $image = "";
    protected static $type = "website";    
    protected static $video = "";
    protected static $body_class = "";
    protected static $lang = "";
    private static $plugin = array();    
    private static $shortcode = array();    
    private static $config = array();
    private static $enqueue = array("footer" => "", "header" => "");
    private static $admin_enqueue = array("footer" => "", "header" => "");
  /**
  * Generate meta title
  * @param string Separator
  * @return title
  * @since v1.0
  */
    public static function title($separator="-"){      
      if(empty(self::$title)){
        return self::$config["title"];
      }else{
        return self::$title." $separator ".self::$config["title"];
      }
    }
  /**
  * Generate meta description
  * @param none
  * @return description
  * @since v1.0
  */
    public static function description(){      
      if(empty(self::$description)){
        return self::$config["description"];
      }else{
        return self::$description;
      }
    }
  /**
  * Generate URL
  * @param none
  * @return description
  * @since v1.0
  */
    public static function url(){      
      if(empty(self::$url)){
        return self::$config["url"];
      }else{
        return self::$url;
      }
    } 
  /**
  * Body Class inject
  * @param none
  * @return message
  * @since v1.0
  */     
    public static function body_class($prefix = ""){
      if(!empty(self::$body_class)) return " class='$prefix".self::$body_class."'";
    }    
  /**
  * Generate URL
  * @param none
  * @return description
  * @since v1.0
  */
    public static function image(){      
      return self::$image;
    }   
  /**
  * Set meta info
  * @param none
  * @return Formatted array
  * @since v1.0
  */
    public static function set($meta,$value){
      if(!empty($value)){
        self::$$meta=$value;
      }
    }  
  /**
  * Generate Open-graph tags
  * @param none
  * @return string
  * @since v1.5.1
  */  
    public static function ogp(){
      $meta="<meta property='og:type' content='".self::$type."' />\n\t";      
      $meta.="<meta property='og:url' content='".self::url()."' />\n\t"; 
      $meta.="<meta property='og:title' content='".self::title()."' />\n\t";
      $meta.="<meta property='og:description' content='".self::description()."' />\n\t";  
      $meta.="<meta property='og:image' content='".self::image()."' />\n"; 
      $meta.="<meta name='twitter:card' content='summary_large_image'>\n\t"; 
      $meta.="<meta name='twitter:site' content='@".str_replace("https://twitter.com/","",str_replace("http://www.twitter.com/", "", self::$config["twitter"]))."'>\n\t"; 
      $meta.="<meta name='twitter:title' content='".self::title()."'>\n\t"; 
      $meta.="<meta name='twitter:description' content='".self::description()."'>\n\t"; 
      $meta.="<meta name='twitter:creator' content='@".str_replace("https://twitter.com/","",str_replace("http://www.twitter.com/", "", self::$config["twitter"]))."'>\n\t"; 
      $meta.="<meta name='twitter:image:src' content='".self::image()."'>\n\t"; 
      $meta.="<meta name='twitter:domain' content='".str_replace("http://", "", self::$config["url"])."'>\n\t";  

      if(!empty(self::$video)){
        $meta.=self::$video; 
      }
      echo $meta; 
    } 

  /**
  * Generate Open-graph video tag
  * @param none
  * @return string
  * @since v2.0
  */  
  public static function video($url,$embed=FALSE){
    if(preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {                     
       Main::set("image","http://i1.ytimg.com/vi/{$match[1]}/maxresdefault.jpg?feature=og");
       Main::set("video",'<meta property="og:video" content="http://www.youtube.com/v/'.$match[1].'?version=3&amp;autohide=1">
      <meta property="og:video:type" content="application/x-shockwave-flash">
      <meta property="og:video:width" content="1920">
      <meta property="og:video:height" content="1080">');
     }  
     if($embed) return '<iframe id="ytplayer" type="text/html" width="640" height="390" src="http://www.youtube.com/v/'.$match[1].'?version=3&amp;autohide=1" frameborder="0"/>';
  }       
  /**
  * Clean a string
  * @param string, cleaning level (1=lowest,2,3=highest)
  * @return cleaned string
  */

    public static function clean($string,$level='1',$chars=FALSE,$leave=""){        
        if(is_array($string)) return array_map("Main::clean",$string);

        $string=preg_replace('/<script[^>]*>([\s\S]*?)<\/script[^>]*>/i', '', $string);      
        switch ($level) {
          case '4':
            if(empty($leave)){
              $search = array('@<script[^>]*?>.*?</script>@si',
                             '@<style[^>]*?>.*?</style>@siU'
              ); 
              $string = preg_replace($search, '', $string);              
            }   
            if($chars) {
              if(phpversion() >= 5.4){
                $string=htmlspecialchars($string, ENT_QUOTES | ENT_HTML5,"UTF-8");  
              }else{
                $string=htmlspecialchars($string, ENT_QUOTES,"UTF-8");  
              }
            }
            break;          
          case '3':
            if(empty($leave)){
              $search = array('@<script[^>]*?>.*?</script>@si',
                             '@<[\/\!]*?[^<>]*?>@si',
                             '@<style[^>]*?>.*?</style>@siU',
                             '@<![\s\S]*?--[ \t\n\r]*>@'
              ); 
              $string = preg_replace($search, '', $string);              
            }
            $string=strip_tags($string,$leave);      
            if($chars) {
              if(phpversion() >= 5.4){
                $string=htmlspecialchars($string, ENT_QUOTES | ENT_HTML5,"UTF-8");  
              }else{
                $string=htmlspecialchars($string, ENT_QUOTES,"UTF-8");  
              }
            }
            break;
          case '2':
            $string=strip_tags($string,'<b><i><s><p><u><strong><span>');
            break;
          case '1':
            $string=strip_tags($string,'<b><i><s><u><strong><a><pre><code><p><div><span>');
            break;

        }   
        if(!preg_match('!nofollow!', $string)) $string=str_replace('href=','rel="nofollow" href=', $string);   
        return $string; 
    }
  /**
   * Extract @user
   * @author KBRmedia
   * @since  1.0
   */
  public static function at($text, $url = NULL){
    preg_match_all("/(@\w+)/", $text, $at);
    if(!isset($at[0][0])) return $text;
    if(!is_null($url)){
      foreach ($at[0] as $ats) {
        $text = str_replace($ats, "<a href='$url".str_replace("@","",strtolower($ats))."' class='user-at'>$ats</a>", $text);
      }
      return $text;
    }
    return $at;
  }
  /**
   * Extract @hash
   * @author KBRmedia
   * @since  1.0
   */
  public static function hash($text, $url = NULL){
    preg_match_all("/(#\w+)/", $text, $at);
    if(!isset($at[0][0])) return $text;
    if(!is_null($url)){
      foreach ($at[0] as $ats) {
         $text =  str_replace($ats, "<a href='$url".str_replace("#","",strtolower($ats))."' class='hash'>$ats</a>", $text);
      }
      return $text;      
    }
    return $at;
  }  
  /**
  * Is Set and Equal to
  * @param key, value
  * @return boolean
  */ 
   public static function is_set($key,$value=NULL,$method="GET"){
      if(!in_array($method, array("GET","POST"))) return FALSE;
      if($method=="GET") {
        $method=$_GET;
      }elseif($method=="POST"){
        $method=$_POST;
      }
      if(!isset($method[$key])) return FALSE;
      if(!is_null($value) && $method[$key]!==$value) return FALSE;      
      return self::clean($method[$key],3,TRUE);
   }
  /**
  * Validate and sanitize email
  * @param string
  * @return email
  */  
    public static function email($email){
        $email=trim($email);
        if (preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{2,})+$/i', $email) && strlen($email)<=50 && filter_var($email, FILTER_SANITIZE_EMAIL)){
            return filter_var($email, FILTER_SANITIZE_EMAIL);
        }
        return FALSE;
    }

  /**
  * Validate and sanitize username
  * @param string
  * @return username
  */  

    public static function username($user){
      if(preg_match('/^\w{4,}$/', $user) && strlen($user)<=20 && filter_var($user,FILTER_SANITIZE_STRING)) {
        return filter_var(trim($user),FILTER_SANITIZE_STRING);
      }
      return false;    
    }
  /**
  * Validate Date
  * @param string
  */  
    public static function validatedate($date, $format = 'Y-m-d H:i:s'){
      if(!class_exists("DateTime")){
        if(!preg_match("!(.*)-(.*)-(.*)!",$date)) return false;
        return true;
      }
      $d = DateTime::createFromFormat($format, $date);
      return $d && $d->format($format) == $date;
    }
  /**
   * Get IP
   * @since 1.0 
   **/
  public static function ip(){
     $ipaddress = '';
      if(isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
          $ipaddress =  $_SERVER['HTTP_CF_CONNECTING_IP'];
      } else if (isset($_SERVER['HTTP_X_REAL_IP'])) {
          $ipaddress = $_SERVER['HTTP_X_REAL_IP'];
      }
      else if (isset($_SERVER['HTTP_CLIENT_IP']))
          $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
      else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
          $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
      else if(isset($_SERVER['HTTP_X_FORWARDED']))
          $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
      else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
          $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
      else if(isset($_SERVER['HTTP_FORWARDED']))
          $ipaddress = $_SERVER['HTTP_FORWARDED'];
      else if(isset($_SERVER['REMOTE_ADDR']))
          $ipaddress = $_SERVER['REMOTE_ADDR'];
      else
          $ipaddress = 'UNKNOWN';
      return $ipaddress;
  }
  /**
   * Validate URLs
   * @since 1.0
   **/
  public static function is_url($url){
    if (preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$url) && filter_var($url, FILTER_VALIDATE_URL)){
      return true;
    }    
    return false;     
  }

  /**
  * Encode string
  * @param string, encode= MD5, SHA1 or SHA256 
  * @return hash
  */   
    public static function encode($string,$encoding="phppass"){      
      if($encoding=="phppass"){
        if(!class_exists("PasswordHash")) require_once(ROOT."/includes/library/phpPass.class.php");
        $e = new PasswordHash(8, FALSE);
        return $e->HashPassword($string.self::$config["security"]);
      }else{
        return hash($encoding,$string.self::$config["security"]);
      }
    }
  /**
  * Check Password
  * @param string, encode= MD5, SHA1 or SHA256 
  * @return hash
  */   
    public static function validate_pass($string,$hash,$encoding="phppass"){      

      if($encoding=="phppass"){
        if(!class_exists("PasswordHash")) require_once(ROOT."/includes/library/phpPass.class.php");
        $e = new PasswordHash(8, FALSE);
        return $e->CheckPassword($string.self::$config["security"], $hash);
      }else{
        return hash($encoding,$string.self::$config["security"]);
      }
    }
/**
 * Read user cookie and extract user info
 * @param 
 * @return array of info
 * @since v1.0
 */
  public static function user(){
    if(isset($_COOKIE["login"])){
      $data=json_decode(base64_decode($_COOKIE["login"]),TRUE);
    }elseif(isset($_SESSION["login"])){
      $data=json_decode(base64_decode($_SESSION["login"]),TRUE);     
    }
    if(isset($data["loggedin"]) && !empty($data["key"])){  
      return array(self::clean(substr($data["key"],60)),self::clean(substr($data["key"],0,60)));
    }     
    return FALSE;  
  }    
  /**
  * Generate api or random string
  * @param length, start
  * @return 
  */    
    public static function strrand($length=12,$api=""){    
        $use = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890"; 
        srand((double)microtime()*1000000); 
        for($i=0; $i<$length; $i++) { 
          $api.= $use[rand()%strlen($use)]; 
        } 
      return $api; 
    }

  /**
  * Get extension
  * @param file name
  * @return extension
  */   
    public static function extension($file, $dot = TRUE){
        return $dot ? strrchr($file, ".") : str_replace(".","",strrchr($file, ".")); 
    }

  /**
  * Generate slug
  * @param string
  * @return slug
  */  

  public static function slug($text, $replace=array(), $delimiter='-') {
    $str = $text;
    if( !empty($replace) ) {
      $str = str_replace((array)$replace, ' ', $str);
    }

    $text = $str;
    static $translit = array(
      'a' => '/[ÀÁÂẦẤẪẨÃĀĂẰẮẴȦẲǠẢÅÅǺǍȀȂẠẬẶḀĄẚàáâầấẫẩãāăằắẵẳȧǡảåǻǎȁȃạậặḁą]/u',
      'b' => '/[ḂḄḆḃḅḇ]/u',     'c' => '/[ÇĆĈĊČḈçćĉċčḉ]/u',
      'd' => '/[ÐĎḊḌḎḐḒďḋḍḏḑḓð]/u',
      'e' => '/[ÈËÉĒĔĖĘĚȄȆȨḔḖḘḚḜẸẺẼẾỀỂỄỆèéëēĕėęěȅȇȩḕḗḙḛḝẹẻẽếềểễệ]/u',
      'f' => '/[Ḟḟ]/u',       'g' => '/[ĜĞĠĢǦǴḠĝğġģǧǵḡ]/u',
      'h' => '/[ĤȞḢḤḦḨḪĥȟḣḥḧḩḫẖ]/u',    'i' => '/[ÌÏĨĪĬĮİǏȈȊḬḮỈỊiìïĩīĭįǐȉȋḭḯỉị]/u',
      'j' => '/[Ĵĵǰ]/u',        'k' => '/[ĶǨḰḲḴKķǩḱḳḵ]/u',
      'l' => '/[ĹĻĽĿḶḸḺḼĺļľŀḷḹḻḽ]/u',   'm' => '/[ḾṀṂḿṁṃ]/u',
      'n' => '/[ÑŃŅŇǸṄṆṈṊñńņňǹṅṇṉṋ]/u',
      'o' => '/[ÒÖŌŎŐƠǑǪǬȌȎȪȬȮȰṌṎṐṒỌỎỐỒỔỖỘỚỜỞỠỢØǾòöōŏőơǒǫǭȍȏȫȭȯȱṍṏṑṓọỏốồổỗộớờởỡợøǿ]/u',
      'p' => '/[ṔṖṕṗ]/u',       'r' => '/[ŔŖŘȐȒṘṚṜṞŕŗřȑȓṙṛṝṟ]/u',
      's' => '/[ŚŜŞŠȘṠṢṤṦṨſśŝşšșṡṣṥṧṩ]/u',  'ss'  => '/[ß]/u',
      't' => '/[ŢŤȚṪṬṮṰţťțṫṭṯṱẗ]/u',    'th'  => '/[Þþ]/u',
      'u' => '/[ÙŨŪŬŮŰŲƯǓȔȖṲṴṶṸṺỤỦỨỪỬỮỰùũūŭůűųưǔȕȗṳṵṷṹṻụủứừửữựµ]/u',
      'v' => '/[ṼṾṽṿ]/u',       'w' => '/[ŴẀẂẄẆẈŵẁẃẅẇẉẘ]/u',
      'x' => '/[ẊẌẋẍ×]/u',      'y' => '/[ÝŶŸȲẎỲỴỶỸýÿŷȳẏẙỳỵỷỹ]/u',
      'z' => '/[ŹŻŽẐẒẔźżžẑẓẕ]/u',       
      //combined letters and ligatures:
      'ae'  => '/[ÄǞÆǼǢäǟæǽǣ]/u',     'oe'  => '/[Œœ]/u',
      'dz'  => '/[ǄǅǱǲǆǳ]/u',
      'ff'  => '/[ﬀ]/u',  'fi'  => '/[ﬃﬁ]/u', 'ffl' => '/[ﬄﬂ]/u',
      'ij'  => '/[Ĳĳ]/u', 'lj'  => '/[Ǉǈǉ]/u',  'nj'  => '/[Ǌǋǌ]/u',
      'st'  => '/[ﬅﬆ]/u', 'ue'  => '/[ÜǕǗǙǛüǖǘǚǜ]/u',
      //currencies:
      'eur'   => '/[€]/u',  'cents' => '/[¢]/u',  'lira'  => '/[₤]/u',  'dollars' => '/[$]/u',
      'won' => '/[₩]/u',  'rs'  => '/[₨]/u',  'yen' => '/[¥]/u',  'pounds'  => '/[£]/u',
      'pts' => '/[₧]/u',
      //misc:
      'degc'  => '/[℃]/u',  'degf'  => '/[℉]/u',
      'no'  => '/[№]/u',  'tm'  => '/[™]/u'
    );
    //do the manual transliteration first
    $str = preg_replace (array_values ($translit), array_keys ($translit), $str);
    
    //flatten the text down to just a-z0-9 and dash, with underscores instead of spaces
    $str = preg_replace (
      //remove punctuation  //replace non a-z //deduplicate //trim underscores from start & end
      array('/\p{P}/u',  '/[^A-Za-z0-9]/', '/-{2,}/', '/^-|-$/'),
      array('-',           '-',              '-',       '-'),
      
      //attempt transliteration with PHP5.4's transliteration engine (best):
      //(this method can handle near anything, including converting chinese and arabic letters to ASCII.
      // requires the 'intl' extension to be enabled)
      function_exists ('transliterator_transliterate') ? transliterator_transliterate (
        //split unicode accents and symbols, e.g. "Å" > "A°":
        'NFKD; '.
        //convert everything to the Latin charset e.g. "ま" > "ma":
        //(splitting the unicode before transliterating catches some complex cases,
        // such as: "㏳" >NFKD> "20日" >Latin> "20ri")
        'Latin; '.
        //because the Latin unicode table still contains a large number of non-pure-A-Z glyphs (e.g. "œ"),
        //convert what remains to an even stricter set of characters, the US-ASCII set:
        //(we must do this because "Latin/US-ASCII" alone is not able to transliterate non-Latin characters
        // such as "ま". this two-stage method also means we catch awkward characters such as:
        // "㏀" >Latin> "kΩ" >Latin/US-ASCII> "kO")
        'Latin/US-ASCII; '.
        //remove the now stand-alone diacritics from the string
        '[:Nonspacing Mark:] Remove; '.
        //change everything to lowercase; anything non A-Z 0-9 that remains will be removed by
        //the letter stripping above
        'Lower',
      $str)
      
      //attempt transliteration with iconv: <php.net/manual/en/function.iconv.php>
      : strtolower (function_exists ('iconv') ? str_replace (array ("'", '"', '`', '^', '~'), '', strtolower (
        //note: results of this are different depending on iconv version,
        //      sometimes the diacritics are written to the side e.g. "ñ" = "~n", which are removed
        iconv ('UTF-8', 'US-ASCII//IGNORE//TRANSLIT', $str)
      )) : $str)
    );
    
    //old iconv versions and certain inputs may cause a nullstring. don't allow a blank response
    if(!$str || $str =="_" || $str == "-"){
      $str = preg_replace("/[^A-Za-z0-9 -]/", '', $text);  
      $str = preg_replace("/[\/_|+ -]+/", $delimiter, $str);
      $str = str_replace("'","",$str);
      $str = str_replace('"','',$str);
      $str = strtolower(rtrim(trim($str,'-'), '-'));
      if(empty($str) || $str =="_" || $str == "-"){
        return Main::strrand(5);
      }else{
        return $str;
      }                
    }else{
      return $str;
    }           
  }

  /**
  * Clean cookie
  * @param cookie
  * @return cleaned cookie
  */  

    public static function get_cookie($cookie){
      return Main::clean($cookie,1);
    }

  /**
  * Convert a timestap into timeago format
  * @param time
  * @return timeago
  */  

    public static function timeago($time, $tense = "ago"){
      if(empty($time)) return "n/a";
       $time=strtotime($time);
       $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
       $lengths = array("60","60","24","7","4.35","12","10");
       $now = time();
         $difference = $now - $time;
         for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
           $difference /= $lengths[$j];
         }
         $difference = round($difference);
         if($difference != 1) {
           $periods[$j].= "s";
         }
       return "$difference $periods[$j] $tense ";
    } 

  /**
  * Redirect function
  * @param url/path (not including base), message and header code
  * @return nothing
  */   

    public static function redirect($url,$message=array(),$header="",$fullurl=FALSE){      

      if(!empty($message)){      
        $_SESSION["msg"]=self::clean("{$message[0]}::{$message[1]}",2);
      }
      switch ($header) {
        case '301':
          header('HTTP/1.1 301 Moved Permanently');
          break;
        case '404':
          header('HTTP/1.1 404 Not Found');
          break;
        case '503':
          header('HTTP/1.1 503 Service Temporarily Unavailable');
          header('Status: 503 Service Temporarily Unavailable');
          header('Retry-After: 60');
          break;
      }
      if($fullurl){
        header("Location: $url");
        exit;
      }    
      header("Location: ".self::$config["url"]."/$url");
      exit;
    }

  /**
  * Notification Function
  * @param none
  * @return message
  */     

    public static function message($style=""){
      if(isset($_SESSION["msg"]) && !empty($_SESSION["msg"])) {
        $message=explode("::",self::clean($_SESSION["msg"],2));
          $message="<div class='alert alert-{$message[0]}' $style>{$message[1]}</div>";
          unset($_SESSION["msg"]);
      }else {
        $message="";
      }
      return $message;
    }

  /**
  * Show error message
  * @param message
  * @return formatted message
  */  
    public static function error($message){
      return "<div class='alert alert-danger'>$message</div>";
    }

  /**
  * Truncate a string
  * @param string, delimiter, append string
  * @return truncated message
  */  
    public static function truncate($string,$del,$limit="...") {
      $len = strlen($string);
        if ($len > $del) {
           $new = substr($string,0,$del).$limit;
            return $new;
        }
        return $string;
    } 

  /**
  * Format Number
  * @param number, decimal
  * @return formatted number
  */  
    public static function formatnumber($number,$decimal="0") {
      if($number>1000000000000) $number= round($number /1000000000000, $decimal)."T";
      if($number>1000000000) $number= round($number /1000000000, $decimal)."B";
      if($number>1000000) $number= round($number /1000000, $decimal)."M";
      if($number>10000) $number= round($number /10000, $decimal)."K";

      return $number;
    }  

  /**
  * Get Facebook Likes
  * @param Facebook page
  * @return number of likes
  */   
  	public static function facebook_likes($url){
  		if(preg_match('((http://|https://|www.)facebook.+[\w-\d]+/(.*))', $url,$id)) {
          $id = $id[2];
          $count = Main::cache_get(__FUNCTION__);
          if($count == null){
            $content = json_decode(@file_get_contents("https://graph.facebook.com/$id/"),TRUE);
            $count = $content["likes"];
            Main::cache_set(__FUNCTION__,$count,60);
          }
        return $count;        
  		}
  	}

  /**
  * Ajax Button
  * @param type, max number of page, current page, url, text, class
  * @return formatted button
  */ 
    public static function ajax_button($url, $current, $max, $text='Load More', $class="btn btn-block btn-primary ajax_load"){
      if($current >= $max) return FALSE;
      return "<a href='".sprintf($url, $current+1)."' class='$class'>$text</a>";
    }

  /**
  * Generates url based on settings (seo or not)
  * @param default (non-seo), pretty urls (seo)
  * @return url
  */ 
    public static function href($default,$seo="",$base=TRUE){
      if(empty($seo)){
        if(self::$config["mod_rewrite"]){
          return (!$base)?"$default":self::$config["url"]."/$default";
        }else{
          return (!$base)?"$default":self::$config["url"]."/index.php?a=$default";
        }        
      }else{
        if(self::$config["mod_rewrite"]){
          return (!$base)?"$seo":self::$config["url"]."/$seo";
        }else{
          return (!$base)?"$default":self::$config["url"]."/index.php?a=$default";
        }
      }
    }

  /**
  * Generates admin url based on settings (seo or not)
  * @see Main::href()
  * @param default (non-seo), pretty urls (seo)
  * @return url
  */ 
    public static function ahref($default,$seo="",$base=TRUE){
      if(empty($seo)){
        if(self::$config["mod_rewrite"]){
          return (!$base)?"admin/$default":self::$config["url"]."/admin/$default";
        }else{
          return (!$base)?"admin/$default":self::$config["url"]."/admin/index.php?a=$default";
        }        
      }else{
        if(self::$config["mod_rewrite"]){
          return (!$base)?"admin/$seo":self::$config["url"]."/admin/$seo";
        }else{
          return (!$base)?"admin/$default":self::$config["url"]."/admin/index.php?a=$default";
        }
      }      
    }
  /**  
  * Generates pagination with class "pagination"
  * @param total number of pages, current pages, format of url
  * @return complete pagination elements
  */
  public static function pagination($total, $current, $format, $limit='1', $class='pager'){
         $page_count = ceil($total/$limit);
         $current_range = array(($current-5 < 1 ? 1 : $current-3), ($current+5 > $page_count ? $page_count : $current+3));

         $first_page = $current > 5 ? '<li><a href="'.sprintf($format, '1').'">'.Main::e("First").'</a></li>'.($current < 5 ? ' ' : '') : null;
         $last_page = $current < $page_count-2 ? ($current > $page_count-4 ? ' ' : '  ').'<li><a href="'.sprintf($format, $page_count).'">'.Main::e("Last").'</a></li>' : null;

         $previous_page = $current > 1 ? '<li class="previous"><a href="'.sprintf($format, ($current-1)).'">'.Main::e("Previous").'</a></li> ' : null;
         $next_page = $current < $page_count ? ' <li class="next"><a href="'.sprintf($format, ($current+1)).'">'.Main::e("Next").'</a></li> ' : null;

         for ($x=$current_range[0];$x <= $current_range[1]; ++$x)    
        $pages[] = ($x == $current ? '<li class="active"><a href="#">'.$x.'</a></li>' : '<li><a href="'.sprintf($format, $x).'"">'.$x.'</a></li>');
         if ($page_count > 1)
      return '<ul class="pager">'.$first_page.$previous_page.implode(' ', $pages).$next_page.$last_page.'</ul>';
  }

  /**  
  * Generates the path to the thumbnail based on mod-rewrite settings
  * @param file name, width, height
  * @return url
  */    
    public static function thumb($file,$width="",$height="",$base=TRUE){
      return Main::href("index.php?action=thumb&p=".Main::clean($file,3)."/$width/$height","thumb/".Main::clean($file,3)."/$width/$height",$base);
    }

/**  
* Validates the captcha based on settings
* @param data
* @return ok
* @since v2.0
*/  
  public static function check_captcha($array){
      if(self::$config["captcha"]=="1"){
        // Recaptcha
        require_once(ROOT."/includes/library/Recaptcha.php");
        $reCaptcha = new ReCaptcha(self::$config["captcha_private"]);
        $resp = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"],$array["g-recaptcha-response"]);
        if ($resp == null || !$resp->success) {
          return e("The CAPTCHA wasn't entered correctly. Please try it again.");
        }
      }elseif(self::$config["captcha"]=="2"){
        // SolveMedia
        require_once(ROOT."/includes/library/Solvemedia.php");
        if(empty($array["adcopy_response"])) {
          return e('Please enter the CAPTCHA.');  
        }else{
          $resp = solvemedia_check_answer(SV_PRIVKEY,$_SERVER["REMOTE_ADDR"],$array["adcopy_challenge"],$array["adcopy_response"],SV_HASHKEY);
          if (!$resp->is_valid) {
           return e("The CAPTCHA wasn't entered correctly. Please try it again.")."<script>ACPuzzle.reload()</script>";
          }       
        }       
      }
      return 'ok';    
    }

/**  
* Generates CAPTCHA html based on settings
* @param none
* @return captcha
* @since v2.0
*/     
    public static function captcha(){
        if(self::$config["captcha"]=="1"){
          require_once(ROOT."/includes/library/Recaptcha.php");
          echo recaptcha_get_html(self::$config["captcha_public"]);
        }elseif(self::$config["captcha"]=="2"){
          require_once(ROOT."/includes/library/Solvemedia.php");
          echo solvemedia_get_html(SV_CHALLENGE);
        }
    }

  /**
  * Generated CSRF Token
  * @param none
  * @return token
  * @since v1.0
  */   
    public static function csrf_token($form=FALSE,$echo=TRUE){
        if($form && $echo && isset($_SESSION["CSRF"])) return "<input type='hidden' name='token' value='{$_SESSION["CSRF"]}' />";      
        if($echo && isset($_SESSION["CSRF"])) return $_SESSION["CSRF"];

        $token = self::encode("csrf_token".rand(0,1000000).time().uniqid(),"SHA1");
        $_SESSION["CSRF"] = $token;

        if($form) return "<input type='hidden' name='token' value='$token' />";
      return $token;
    }

  /**
  * Validate CSRF Token
  * @param token
  * @return boolean
  * @since v1.0
  */   
    public static function validate_csrf_token($token,$redirect=""){
      if(isset($_SESSION["CSRF"]) && ($_SESSION["CSRF"] == trim($token))) {
        unset($_SESSION["CSRF"]);
        return TRUE;
      }
      if(!empty($redirect)) self::redirect($redirect,array("error",e("The CSRF token is not valid. Please try again.")));
      return FALSE;
    }  
/**
  * Create Nonce
  * @param action, duration in minutes
  * @return token
  * @since v4.0
  */   
    public static function nonce_create($action="",$duration="60"){
      $i = ceil( time() / ( $duration*60 / 2 ) );
      return md5( $i . $action . $action);
    }
/**
  * Return Nonce
  * @param action, GET key
  * @return token
  * @since v4.0
  */   
    public static function nonce($action="",$key="nonce"){
      return "?".$key."=".substr(self::nonce_create($action), -12, 10);
    }
  /**
  * Validate Nonce
  * @param action, GET key
  * @return boolean
  * @since v4.0
  */   
    public static function validate_nonce($action="",$key="nonce"){
      if(isset($_GET[$key]) && substr(self::nonce_create($action), -12, 10) == $_GET[$key]){
        return true;
      }
      return false;
    }  

  /**
   * Set ucookie
   * @param Name, value
   * @since v1.0
   */  
    public static function cookie($name,$value="",$time=1){
      if(empty($value)){
        if(isset($_COOKIE[$name])){
          return Main::clean($_COOKIE[$name],3,FALSE);
        }else{
          return FALSE;
        }
      }
      setcookie($name,$value, time()+($time*60), "/","",FALSE,TRUE);
    }
  /**
   * Enqueue scripts to header and footer
   * @since v1.0
   */
    public static function enqueue($where="header"){
      if($where=="footer"){  
        echo self::$enqueue["footer"];
      }else{
        echo self::$enqueue["header"];
      }
    }
  /**
   * Add scripts to header and footer
   * @since v1.0
   */  
    public static function add($url, $type="script", $footer=TRUE){
      if($type == "style"){
        $tag = '<link rel="stylesheet" type="text/css" href="'.$url.'">';
      }elseif($type=="custom"){
        $tag=$url;
      }else{
        $tag = '<script type="text/javascript" src="'.$url.'"></script>';
      }
      if($footer){        
        self::$enqueue["footer"] .= $tag."\n\t";
      }else{
        self::$enqueue["header"] .= $tag."\n\t";
      }
    } 
  /**
   * Enqueue scripts to header and footer
   * @since v1.0
   */
    public static function admin_enqueue($where="header"){
      if($where == "footer"){  
        echo self::$admin_enqueue["footer"];
      }else{
        echo self::$admin_enqueue["header"];
      } 
    }
  /**
   * Add scripts to header and footer
   * @since v1.0
   */  
    public static function admin_add($url,$type="script",$footer=TRUE){
      if($type=="style"){
        $tag='<link rel="stylesheet" type="text/css" href="'.$url.'">';
      }elseif($type=="custom"){
        $tag=$url;
      }else{
        $tag='<script type="text/javascript" src="'.$url.'"></script>';
      }
      if($footer){
        self::$admin_enqueue["footer"].=$tag."\n\t";
      }else{
        self::$admin_enqueue["header"].=$tag."\n\t";
      }
    }
   /**
    * List of CDNs
    * Powered by CloudFlare.com
    * @since 1.0
    **/  
    public static function cdn($cdn,$version="",$admin=FALSE){
      $cdns=array(
        "jquery"=> array(
            "src" => "//ajax.googleapis.com/ajax/libs/jquery/[version]/jquery.min.js",
            "latest" =>"2.0.3"
          ),
        "jquery-ui"=> array(
            "src" => "//cdnjs.cloudflare.com/ajax/libs/jqueryui/[version]/jquery-ui.min.js",
            "latest" =>"1.10.3"
          ),
        "ace"=>array(
            "src"=>"//cdnjs.cloudflare.com/ajax/libs/ace/[version]/ace.js",
            "latest" => "1.1.01"
          ),
        "icheck"=>array(
            "src"=>"//cdnjs.cloudflare.com/ajax/libs/iCheck/[version]/icheck.min.js",
            "latest" => "1.0.1"            
          ),
        "ckeditor"=>array(
            "src"=>"//cdnjs.cloudflare.com/ajax/libs/ckeditor/[version]/ckeditor.js",
            "latest" => "4.3.2"
          ),
        "selectize"=>array(
            "src"=>"//cdnjs.cloudflare.com/ajax/libs/selectize.js/[version]/js/standalone/selectize.min.js",
            "latest"=>"0.8.5",
            "css" => "//cdnjs.cloudflare.com/ajax/libs/selectize.js/0.8.5/css/selectize.css"
          ),
        "zlip"=>array(
            "src"=>"//cdnjs.cloudflare.com/ajax/libs/zclip/[version]/jquery.zclip.min.js",
            "latest"=>"1.1.2"
          ),
        "flot"=>array(
            "src"=>"//cdnjs.cloudflare.com/ajax/libs/flot/[version]/jquery.flot.min.js",
            "latest"=>"0.8.2",
            "js" => array(
                "//cdnjs.cloudflare.com/ajax/libs/flot/0.8.2/jquery.flot.time.min.js",
                "//cdnjs.cloudflare.com/ajax/libs/flot/0.8.2/jquery.flot.pie.min.js",
                "//cdnjs.cloudflare.com/ajax/libs/flot/0.8.2/excanvas.min.js"
              )
          ),
        "less"=>array(
            "src"=>"//cdnjs.cloudflare.com/ajax/libs/less.js/[version]/less.min.js",
            "latest"=>"1.6.2"
          ),
        "ckeditor"=>array(
            "src"=>"//cdnjs.cloudflare.com/ajax/libs/ckeditor/[version]/ckeditor.js",
            "latest"=>"4.3.2"
          ),
        "pace"=>array(
            "src"=>"//cdnjs.cloudflare.com/ajax/libs/pace/[version]/pace.js",
            "latest"=>"0.4.17"
          ),
        "chosen"=>array(
            "src"=>"//cdnjs.cloudflare.com/ajax/libs/chosen/[version]/chosen.jquery.min.js",
            "latest"=>"1.1.0",
            //"css"=> "//cdnjs.cloudflare.com/ajax/libs/chosen/1.1.0/chosen.css"
          ),
        "color" => array(
            "src" => "//cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/[version]/js/bootstrap-colorpicker.min.js",
            "latest" => "2.1.0",
            "css" => "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.1.0/css/bootstrap-colorpicker.min.css"
          )
        );
      if(array_key_exists($cdn, $cdns)){
        if(!empty($version)  && $version <= $cdns[$cdn]["latest"]){
          $js=str_replace("[version]", $version, $cdns[$cdn]["src"])."?v=$version";
        }else{
          $js=str_replace("[version]", $cdns[$cdn]["latest"], $cdns[$cdn]["src"])."?v={$cdns[$cdn]["latest"]}";
        }
       if($admin){                    
          Main::admin_add($js,"script",FALSE);          
          if(isset($cdns[$cdn]["css"])) Main::admin_add($cdns[$cdn]["css"]."?v={$cdns[$cdn]["latest"]}","style",FALSE);
          if(isset($cdns[$cdn]["js"])){
            foreach ($cdns[$cdn]["js"] as $key) {
              Main::admin_add($key."?v={$cdns[$cdn]["latest"]}","script",FALSE);
            }
          } 
          return TRUE;             
        }else{
          Main::add($js,"script",FALSE);          
          if(isset($cdns[$cdn]["css"])) Main::add($cdns[$cdn]["css"]."?v={$cdns[$cdn]["latest"]}","style",FALSE);
          if(isset($cdns[$cdn]["js"])){
            foreach ($cdns[$cdn]["js"] as $key) {
              Main::add($key."?v={$cdns[$cdn]["latest"]}","script",FALSE);
            }
          } 
          return TRUE;         
        }
      }
      return FALSE;
    }
  /**
   * Translate strings
   * @since v1.0
   */   
    public static function e($text){
      if(!is_array(Main::$lang)) return $text;
      if(isset(Main::$lang[$text]) && !empty(Main::$lang[$text])) {
        return ucfirst(Main::$lang[$text]);
      }
      return $text;    
    }
  /**
   * Check if user agent is bot
   * @since v1.0
   */  
  public static function bot($ua=""){
    if(empty($ua)){
      if(!isset($_SERVER['HTTP_USER_AGENT']) || empty($_SERVER['HTTP_USER_AGENT']) || is_null($_SERVER['HTTP_USER_AGENT'])){
        return TRUE;
      }
      $ua=$_SERVER['HTTP_USER_AGENT'];
    }
    $list = array("facebookexternalhit","Teoma", "alexa", "froogle", "Gigabot", "inktomi",
    "looksmart", "URL_Spider_SQL", "Firefly", "NationalDirectory",
    "Ask Jeeves", "TECNOSEEK", "InfoSeek", "WebFindBot", "girafabot",
    "crawler", "www.galaxy.com", "Googlebot", "Scooter", "Slurp",
    "msnbot", "appie", "FAST", "WebBug", "Spade", "ZyBorg", "rabaz",
    "Baiduspider", "Feedfetcher-Google", "TechnoratiSnoop", "Rankivabot",
    "Mediapartners-Google", "Sogou web spider", "WebAlta Crawler","TweetmemeBot",
    "Butterfly","Twitturls","Me.dium","Twiceler");
    foreach($list as $bot){
      if(strpos($ua,$bot)!==false)
      return true;
    }
    return false; 
  }  
  /**
   * Custom cURL Function
   * @since 1.0
   **/  
  public static function http($url, $option=array()){   
    if(ini_get('allow_url_fopen') && !isset($option["post"])){
      return @file_get_contents($url);
    }    
    if(in_array('curl', get_loaded_extensions())){ 
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $url);
      if(isset($option["post"]) && isset($option["data"]) && is_array($option["data"])){
        $fields="";
        //url-ify the data for the POST
        foreach($option["data"] as $key=>$value) { $fields .= $key.'='.$value.'&'; }

        rtrim($fields, '&');       
        curl_setopt($curl, CURLOPT_POST, count($option["data"]));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
      }
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      $resp = curl_exec($curl);
      curl_close($curl);
      return $resp;
    }    
    return FALSE;
  }  
  /**
   * Send Email
   * @param array
   * @return boolean
   */  
  public static function send(array $array){    
    require_once(ROOT."/includes/library/PHPMailer.class.php");
    $mail= new PHPMailer();  
    if(!empty(self::$config["smtp"]["host"])){
      $mail->IsSMTP();
      $mail->SMTPAuth = true;
      $mail->SMTPSecure = "tls";
      $mail->Host= self::$config["smtp"]["host"];
      $mail->Port = self::$config["smtp"]["port"]; 
      $mail->Username= self::$config["smtp"]["user"]; 
      $mail->Password  = self::$config["smtp"]["pass"];     
    }
    $mail->CharSet = "utf-8";
    $mail->IsHTML(true); 
    $mail->SetFrom(self::$config["email"], self::$config["title"]);
    $mail->AddReplyTo(self::$config["email"], self::$config["title"]);
    $mail->AddAddress($array["to"]);
    $mail->Subject= $array["subject"];    

    $content=file_get_contents(THEME."/email.php");
    $content=str_replace("[subject]",$array["subject"],$content);
    $content=str_replace("[message]",$array["message"],$content);
    $content=str_replace("[title]",self::$config["title"],$content);
    $content=str_replace("[url]",self::$config["url"],$content);
    if(!empty(self::$config["facebook"])){
      $content=str_replace("[facebook]"," <a href='".self::$config["facebook"]."'>Like us on Facebook</a>",$content);  
    }else{
      $content=str_replace("[facebook]","",$content);  
    }
    if(!empty(self::$config["twitter"])){
      $content=str_replace("[twitter]"," <a href='".self::$config["twitter"]."'>Follow us on Twitter</a>",$content);
    }else{
      $content=str_replace("[twitter]","",$content);  
    }
    $mail->Body = $content;
    if(!$mail->Send()) {
        error_log("SMTP Error: {$mail->ErrorInfo}");
        $headers  = 'From:  '.self::$config["title"].' <'.self::$config["email"].'>' . "\r\n";
        $headers .= 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        mail($array["to"], $array["subject"], $content, $headers);
        return TRUE;
    } else {
      return TRUE;
    }          
  }  
  /**
   * Country Codes
   * @since 1.0
   **/
 public static function ccode($code,$reverse=FALSE){
    $array=array('AF' => 'Afghanistan', 'AX' => 'Aland Islands', 'AL' => 'Albania', 'DZ' => 'Algeria', 'AS' => 'American Samoa', 'AD' => 'Andorra', 'AO' => 'Angola', 'AI' => 'Anguilla', 'AQ' => 'Antarctica', 'AG' => 'Antigua And Barbuda', 'AR' => 'Argentina', 'AM' => 'Armenia', 'AW' => 'Aruba', 'AU' => 'Australia', 'AT' => 'Austria', 'AZ' => 'Azerbaijan', 'BS' => 'Bahamas', 'BH' => 'Bahrain', 'BD' => 'Bangladesh', 'BB' => 'Barbados', 'BY' => 'Belarus', 'BE' => 'Belgium', 'BZ' => 'Belize', 'BJ' => 'Benin', 'BM' => 'Bermuda', 'BT' => 'Bhutan', 'BO' => 'Bolivia', 'BA' => 'Bosnia And Herzegovina', 'BW' => 'Botswana', 'BV' => 'Bouvet Island', 'BR' => 'Brazil', 'IO' => 'British Indian Ocean Territory', 'BN' => 'Brunei Darussalam', 'BG' => 'Bulgaria', 'BF' => 'Burkina Faso', 'BI' => 'Burundi', 'KH' => 'Cambodia', 'CM' => 'Cameroon', 'CA' => 'Canada', 'CV' => 'Cape Verde', 'KY' => 'Cayman Islands', 'CF' => 'Central African Republic', 'TD' => 'Chad', 'CL' => 'Chile', 'CN' => 'China', 'CX' => 'Christmas Island', 'CC' => 'Cocos (Keeling) Islands', 'CO' => 'Colombia', 'KM' => 'Comoros', 'CG' => 'Congo', 'CD' => 'Congo, Democratic Republic', 'CK' => 'Cook Islands', 'CR' => 'Costa Rica', 'CI' => 'Cote D\'Ivoire', 'HR' => 'Croatia', 'CU' => 'Cuba', 'CY' => 'Cyprus', 'CZ' => 'Czech Republic', 'DK' => 'Denmark', 'DJ' => 'Djibouti', 'DM' => 'Dominica', 'DO' => 'Dominican Republic', 'EC' => 'Ecuador', 'EG' => 'Egypt', 'SV' => 'El Salvador', 'GQ' => 'Equatorial Guinea', 'ER' => 'Eritrea', 'EE' => 'Estonia', 'ET' => 'Ethiopia', 'FK' => 'Falkland Islands (Malvinas)', 'FO' => 'Faroe Islands', 'FJ' => 'Fiji', 'FI' => 'Finland', 'FR' => 'France', 'GF' => 'French Guiana', 'PF' => 'French Polynesia', 'TF' => 'French Southern Territories', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgia', 'DE' => 'Germany', 'GH' => 'Ghana', 'GI' => 'Gibraltar', 'GR' => 'Greece', 'GL' => 'Greenland', 'GD' => 'Grenada', 'GP' => 'Guadeloupe', 'GU' => 'Guam', 'GT' => 'Guatemala', 'GG' => 'Guernsey', 'GN' => 'Guinea', 'GW' => 'Guinea-Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HM' => 'Heard Island & Mcdonald Islands', 'VA' => 'Holy See (Vatican City State)', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Hungary', 'IS' => 'Iceland', 'IN' => 'India', 'ID' => 'Indonesia', 'IR' => 'Iran, Islamic Republic Of', 'IQ' => 'Iraq', 'IE' => 'Ireland', 'IM' => 'Isle Of Man', 'IL' => 'Israel', 'IT' => 'Italy', 'JM' => 'Jamaica', 'JP' => 'Japan', 'JE' => 'Jersey', 'JO' => 'Jordan', 'KZ' => 'Kazakhstan', 'KE' => 'Kenya', 'KI' => 'Kiribati', 'KR' => 'Korea', 'KW' => 'Kuwait', 'KG' => 'Kyrgyzstan', 'LA' => 'Lao People\'s Democratic Republic', 'LV' => 'Latvia', 'LB' => 'Lebanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 'LY' => 'Libyan Arab Jamahiriya', 'LI' => 'Liechtenstein', 'LT' => 'Lithuania', 'LU' => 'Luxembourg', 'MO' => 'Macao', 'MK' => 'Macedonia', 'MG' => 'Madagascar', 'MW' => 'Malawi', 'MY' => 'Malaysia', 'MV' => 'Maldives', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshall Islands', 'MQ' => 'Martinique', 'MR' => 'Mauritania', 'MU' => 'Mauritius', 'YT' => 'Mayotte', 'MX' => 'Mexico', 'FM' => 'Micronesia, Federated States Of', 'MD' => 'Moldova', 'MC' => 'Monaco', 'MN' => 'Mongolia', 'ME' => 'Montenegro', 'MS' => 'Montserrat', 'MA' => 'Morocco', 'MZ' => 'Mozambique', 'MM' => 'Myanmar', 'NA' => 'Namibia', 'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Netherlands', 'AN' => 'Netherlands Antilles', 'NC' => 'New Caledonia', 'NZ' => 'New Zealand', 'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'NU' => 'Niue', 'NF' => 'Norfolk Island', 'MP' => 'Northern Mariana Islands', 'NO' => 'Norway', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 'PS' => 'Palestinian Territory, Occupied', 'PA' => 'Panama', 'PG' => 'Papua New Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Philippines', 'PN' => 'Pitcairn', 'PL' => 'Poland', 'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Qatar', 'RE' => 'Reunion', 'RO' => 'Romania', 'RU' => 'Russian Federation', 'RW' => 'Rwanda', 'BL' => 'Saint Barthelemy', 'SH' => 'Saint Helena', 'KN' => 'Saint Kitts And Nevis', 'LC' => 'Saint Lucia', 'MF' => 'Saint Martin', 'PM' => 'Saint Pierre And Miquelon', 'VC' => 'Saint Vincent And Grenadines', 'WS' => 'Samoa', 'SM' => 'San Marino', 'ST' => 'Sao Tome And Principe', 'SA' => 'Saudi Arabia', 'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychelles', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SK' => 'Slovakia', 'SI' => 'Slovenia', 'SB' => 'Solomon Islands', 'SO' => 'Somalia', 'ZA' => 'South Africa', 'GS' => 'South Georgia And Sandwich Isl.', 'ES' => 'Spain', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Suriname', 'SJ' => 'Svalbard And Jan Mayen', 'SZ' => 'Swaziland', 'SE' => 'Sweden', 'CH' => 'Switzerland', 'SY' => 'Syrian Arab Republic', 'TW' => 'Taiwan', 'TJ' => 'Tajikistan', 'TZ' => 'Tanzania', 'TH' => 'Thailand', 'TL' => 'Timor-Leste', 'TG' => 'Togo', 'TK' => 'Tokelau', 'TO' => 'Tonga', 'TT' => 'Trinidad And Tobago', 'TN' => 'Tunisia', 'TR' => 'Turkey', 'TM' => 'Turkmenistan', 'TC' => 'Turks And Caicos Islands', 'TV' => 'Tuvalu', 'UG' => 'Uganda', 'UA' => 'Ukraine', 'AE' => 'United Arab Emirates', 'GB' => 'United Kingdom', 'US' => 'United States', 'UM' => 'United States Outlying Islands', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistan', 'VU' => 'Vanuatu', 'VE' => 'Venezuela', 'VN' => 'Viet Nam', 'VG' => 'Virgin Islands, British', 'VI' => 'Virgin Islands, U.S.', 'WF' => 'Wallis And Futuna', 'EH' => 'Western Sahara', 'YE' => 'Yemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe');
    if($reverse){
      $array=array_flip($array);
      if(isset($array[$code])) return $array[$code];      
    }
    $code=strtoupper($code);
    if(isset($array[$code])) return $array[$code];
    return FALSE;
  }
  /**
   * List of Countries and ISO Code
   * @since 1.0
   **/
  public static function countries($code=""){
    $countries=array('AF'=>'Afghanistan','AX'=>'Aland Islands','AL'=>'Albania','DZ'=>'Algeria','AS'=>'American Samoa','AD'=>'Andorra','AO'=>'Angola','AI'=>'Anguilla','AQ'=>'Antarctica','AG'=>'Antigua And Barbuda','AR'=>'Argentina','AM'=>'Armenia','AW'=>'Aruba','AU'=>'Australia','AT'=>'Austria','AZ'=>'Azerbaijan','BS'=>'Bahamas','BH'=>'Bahrain','BD'=>'Bangladesh','BB'=>'Barbados','BY'=>'Belarus','BE'=>'Belgium','BZ'=>'Belize','BJ'=>'Benin','BM'=>'Bermuda','BT'=>'Bhutan','BO'=>'Bolivia','BA'=>'Bosnia And Herzegovina','BW'=>'Botswana','BV'=>'Bouvet Island','BR'=>'Brazil','IO'=>'British Indian Ocean Territory','BN'=>'Brunei Darussalam','BG'=>'Bulgaria','BF'=>'Burkina Faso','BI'=>'Burundi','KH'=>'Cambodia','CM'=>'Cameroon','CA'=>'Canada','CV'=>'Cape Verde','KY'=>'Cayman Islands','CF'=>'Central African Republic','TD'=>'Chad','CL'=>'Chile','CN'=>'China','CX'=>'Christmas Island','CC'=>'Cocos (Keeling) Islands','CO'=>'Colombia','KM'=>'Comoros','CG'=>'Congo','CD'=>'Congo, Democratic Republic','CK'=>'Cook Islands','CR'=>'Costa Rica','CI'=>'Cote D\'Ivoire','HR'=>'Croatia','CU'=>'Cuba','CY'=>'Cyprus','CZ'=>'Czech Republic','DK'=>'Denmark','DJ'=>'Djibouti','DM'=>'Dominica','DO'=>'Dominican Republic','EC'=>'Ecuador','EG'=>'Egypt','SV'=>'El Salvador','GQ'=>'Equatorial Guinea','ER'=>'Eritrea','EE'=>'Estonia','ET'=>'Ethiopia','FK'=>'Falkland Islands (Malvinas)','FO'=>'Faroe Islands','FJ'=>'Fiji','FI'=>'Finland','FR'=>'France','GF'=>'French Guiana','PF'=>'French Polynesia','TF'=>'French Southern Territories','GA'=>'Gabon','GM'=>'Gambia','GE'=>'Georgia','DE'=>'Germany','GH'=>'Ghana','GI'=>'Gibraltar','GR'=>'Greece','GL'=>'Greenland','GD'=>'Grenada','GP'=>'Guadeloupe','GU'=>'Guam','GT'=>'Guatemala','GG'=>'Guernsey','GN'=>'Guinea','GW'=>'Guinea-Bissau','GY'=>'Guyana','HT'=>'Haiti','HM'=>'Heard Island & Mcdonald Islands','VA'=>'Holy See (Vatican City State)','HN'=>'Honduras','HK'=>'Hong Kong','HU'=>'Hungary','IS'=>'Iceland','IN'=>'India','ID'=>'Indonesia','IR'=>'Iran, Islamic Republic Of','IQ'=>'Iraq','IE'=>'Ireland','IM'=>'Isle Of Man','IL'=>'Israel','IT'=>'Italy','JM'=>'Jamaica','JP'=>'Japan','JE'=>'Jersey','JO'=>'Jordan','KZ'=>'Kazakhstan','KE'=>'Kenya','KI'=>'Kiribati','KR'=>'Korea','KW'=>'Kuwait','KG'=>'Kyrgyzstan','LA'=>'Lao People\'s Democratic Republic','LV'=>'Latvia','LB'=>'Lebanon','LS'=>'Lesotho','LR'=>'Liberia','LY'=>'Libyan Arab Jamahiriya','LI'=>'Liechtenstein','LT'=>'Lithuania','LU'=>'Luxembourg','MO'=>'Macao','MK'=>'Macedonia','MG'=>'Madagascar','MW'=>'Malawi','MY'=>'Malaysia','MV'=>'Maldives','ML'=>'Mali','MT'=>'Malta','MH'=>'Marshall Islands','MQ'=>'Martinique','MR'=>'Mauritania','MU'=>'Mauritius','YT'=>'Mayotte','MX'=>'Mexico','FM'=>'Micronesia, Federated States Of','MD'=>'Moldova','MC'=>'Monaco','MN'=>'Mongolia','ME'=>'Montenegro','MS'=>'Montserrat','MA'=>'Morocco','MZ'=>'Mozambique','MM'=>'Myanmar','NA'=>'Namibia','NR'=>'Nauru','NP'=>'Nepal','NL'=>'Netherlands','AN'=>'Netherlands Antilles','NC'=>'New Caledonia','NZ'=>'New Zealand','NI'=>'Nicaragua','NE'=>'Niger','NG'=>'Nigeria','NU'=>'Niue','NF'=>'Norfolk Island','MP'=>'Northern Mariana Islands','NO'=>'Norway','OM'=>'Oman','PK'=>'Pakistan','PW'=>'Palau','PS'=>'Palestinian Territory, Occupied','PA'=>'Panama','PG'=>'Papua New Guinea','PY'=>'Paraguay','PE'=>'Peru','PH'=>'Philippines','PN'=>'Pitcairn','PL'=>'Poland','PT'=>'Portugal','PR'=>'Puerto Rico','QA'=>'Qatar','RE'=>'Reunion','RO'=>'Romania','RU'=>'Russian Federation','RW'=>'Rwanda','BL'=>'Saint Barthelemy','SH'=>'Saint Helena','KN'=>'Saint Kitts And Nevis','LC'=>'Saint Lucia','MF'=>'Saint Martin','PM'=>'Saint Pierre And Miquelon','VC'=>'Saint Vincent And Grenadines','WS'=>'Samoa','SM'=>'San Marino','ST'=>'Sao Tome And Principe','SA'=>'Saudi Arabia','SN'=>'Senegal','RS'=>'Serbia','SC'=>'Seychelles','SL'=>'Sierra Leone','SG'=>'Singapore','SK'=>'Slovakia','SI'=>'Slovenia','SB'=>'Solomon Islands','SO'=>'Somalia','ZA'=>'South Africa','GS'=>'South Georgia And Sandwich Isl.','ES'=>'Spain','LK'=>'Sri Lanka','SD'=>'Sudan','SR'=>'Suriname','SJ'=>'Svalbard And Jan Mayen','SZ'=>'Swaziland','SE'=>'Sweden','CH'=>'Switzerland','SY'=>'Syrian Arab Republic','TW'=>'Taiwan','TJ'=>'Tajikistan','TZ'=>'Tanzania','TH'=>'Thailand','TL'=>'Timor-Leste','TG'=>'Togo','TK'=>'Tokelau','TO'=>'Tonga','TT'=>'Trinidad And Tobago','TN'=>'Tunisia','TR'=>'Turkey','TM'=>'Turkmenistan','TC'=>'Turks And Caicos Islands','TV'=>'Tuvalu','UG'=>'Uganda','UA'=>'Ukraine','AE'=>'United Arab Emirates','GB'=>'United Kingdom','US'=>'United States','UM'=>'United States Outlying Islands','UY'=>'Uruguay','UZ'=>'Uzbekistan','VU'=>'Vanuatu','VE'=>'Venezuela','VN'=>'Viet Nam','VG'=>'Virgin Islands, British','VI'=>'Virgin Islands, U.S.','WF'=>'Wallis And Futuna','EH'=>'Western Sahara','YE'=>'Yemen','ZM'=>'Zambia','ZW'=>'Zimbabwe');
    $form="";
    foreach ($countries as $key => $value) {
      $form.='<option value="'.strtolower($key).'"'.($code==strtolower($key)?' selected':'').'>'.$value.'</option>';
    }
    return $form;
  }
  /**
   * Currency
   * @since 1.0
   **/
  public static function currency($code="",$amount=""){
    $array = array('AUD' => array('label'=>'Australian Dollar','format' => '$%s'),'CAD' => array('label' => 'Canadian Dollar','format' => '$%s'),'EUR' => array('label' => 'Euro','format' => '€ %s'),'GBP' => array('label' => 'Pound Sterling','format' => '£ %s'),'JPY' => array('label' => 'Japanese Yen','format' => '¥ %s'),'USD' => array('label' => 'U.S. Dollar','format' => '$%s'),'NZD' => array('label' => 'N.Z. Dollar','format' => '$%s'),'CHF' => array('label' => 'Swiss Franc','format' => '%s Fr'),'HKD' => array('label' => 'Hong Kong Dollar','format' => '$%s'),'SGD' => array('label' => 'Singapore Dollar','format' => '$%s'),'SEK' => array('label' => 'Swedish Krona','format' => '%s kr'),'DKK' => array('label' => 'Danish Krone','format' => '%s kr'),'PLN' => array('label' => 'Polish Zloty','format' => '%s zł'),'NOK' => array('label' => 'Norwegian Krone','format' => '%s kr'),'HUF' => array('label' => 'Hungarian Forint','format' => '%s Ft'),'CZK' => array('label' => 'Czech Koruna','format' => '%s Kč'),'ILS' => array('label' => 'Israeli New Sheqel','format' => '₪ %s'),'MXN' => array('label' => 'Mexican Peso','format' => '$%s'),'BRL' => array('label' => 'Brazilian Real','format' => 'R$%s'),'MYR' => array('label' => 'Malaysian Ringgit','format' => 'RM %s'),'PHP' => array('label' => 'Philippine Peso','format' => '₱ %s'),'TWD' => array('label' => 'New Taiwan Dollar','format' => 'NT$%s'),'THB' => array('label' => 'Thai Baht','format' => '฿ %s'),'TRY' => array('label' => 'Turkish Lira','format' => 'TRY %s'));
    if(empty($code)) return $array;
    
    $code=strtoupper($code);
    if(isset($array[$code])) return sprintf($array[$code]["format"],$amount);
  }   
 /**
  * Get Domain
  * @since 1.0
  **/  
  public static function domain($url,$scheme=TRUE,$http=TRUE){
    $url=parse_url($url);
    if(!isset($url["host"])) return false;
    $url["host"] = explode(".",str_replace("www.", "", $url["host"]));
    $url["host"] = $url["host"][0];
    return ($scheme ? ($http ? $url["scheme"]."://".$url["host"] : $url["host"] ) : $url["host"]);
  }
 /**
  * Cache Data
  * @since 4.0
  **/
  public static function cache_set($id,$data,$time){
    if(!isset(self::$config["cache"]) || !self::$config["cache"]) return NULL;
    return phpFastCache::set($id,$data,60*$time);
  }
 /**
  * Cache Get
  * @since 4.0
  **/
  public static function cache_get($id){
    if(!isset(self::$config["cache"]) || !self::$config["cache"]) return NULL;
    return phpFastCache::get($id);
  } 
 /**
  * Plug-in Function
  * @author KBRmedia
  * @since  1.0
  * @param  string $area  Area to plugin function
  * @param  array  $param Parameters sent by the function
  */
  public static function plug($area, $param = array()){
    $return = "";
    if(isset(self::$plugin[$area]) && is_array(self::$plugin[$area])) {
      foreach (self::$plugin[$area] as $fn) {
       if(is_array($fn) && class_exists($fn[0]) && method_exists($fn[0], $fn[1])){        
          $f = $fn[1];
          $return .= $fn[0]::$f($param);       
        }elseif(function_exists($fn)){
          $return .= $fn($param);
        }
      }
      return $return;
    }
  }
 /**
  * Register Plug in
  * @since 1.0
  **/
  public static function hook($area, $fn){
    if(is_array($fn) && class_exists($fn[0]) && method_exists($fn[0], $fn[1])){
      self::$plugin[$area][] = $fn;  
      return;    
    }
    if(function_exists($fn)) {
      self::$plugin[$area][] = $fn;  
      return;
    }
  }
  /**
   * Register Shortcode
   * @since 1.0
   */
  public static function register_shortcode($name, $fn){
    if(is_array($fn) && class_exists($fn[0]) && method_exists($fn[0], $fn[1])){
      self::$shortcode[$name][] = $fn;  
      return;    
    }
    if(function_exists($fn)) {
      self::$shortcode[$name][] = $fn;  
      return;
    }
  } 
  /**
   * Parse Shortcode
   * @author KBRmedia
   * @since  1.0
   */
  public static function parse_shortcode($content){
    $content = preg_match_all('/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/', trim( $content ), $c );
    list( $dummy, $keys, $values ) = array_values( $c );
    var_dump($dummy, $keys, $values);
  }   
  /**
   * Share Buttons
   * @author KBRmedia
   * @since  1.0
   */
  public static function share($url, $title, $site = array()){
    $html  = "";
    if(empty($site) || in_array("facebook",$site)){
      $html .="<a href='http://www.facebook.com/sharer.php?u=$url' target='_blank' class='popup'><span class='fa fa-facebook'></span></a>";
    }
    if(empty($site) || in_array("twitter",$site)){
      $html .="<a href='http://twitter.com/share?url=$url&text=$title' target='_blank' class='popup'><span class='fa fa-twitter'></span></a>";
    }
    if(empty($site) || in_array("google",$site)){
      $html .="<a href='https://plus.google.com/share?url=$url' target='_blank' class='popup'><span class='fa fa-google'></span></a>";
    }
    if(empty($site) || in_array("digg",$site)){
      $html .="<a href='http://www.digg.com/submit?url=$url' target='_blank' class='popup'><span class='fa fa-digg'></span></a>";
    }
    if(empty($site) || in_array("reddit",$site)){
      $html .="<a href='http://reddit.com/submit?url=$url&title=$title' target='_blank' class='popup'><span class='fa fa-reddit'></span></a>";
    }
    if(empty($site) || in_array("linkedin",$site)){
      $html .="<a href='http://www.linkedin.com/shareArticle?mini=true&url=$url' target='_blank' class='popup'><span class='fa fa-linkedin'></span></a>";
    }
    if(empty($site) || in_array("stumbleupon",$site)){
      $html .="<a href='http://www.stumbleupon.com/submit?url=$url&title=$title' target='_blank' class='popup'><span class='fa fa-stumbleupon'></span></a>";
    }

    return $html;
  }
  /**
   * Media Types
   * @author KBRmedia
   * @since  1.0
   */
  public static function types($type = NULL, $html = FALSE, $plural = FALSE, $class='type'){
    // Array of types
    $types = array();
    if(self::$config["type"]["video"]){
      $types["video"] = e("Video");
    }  
    if(self::$config["type"]["music"]){
      $types["music"] = e("Music Video");
    }
    if(self::$config["type"]["picture"]){
      $types["picture"] = e("Picture");
    }
    if(self::$config["type"]["vine"]){
      $types["vine"] = e("Vine");
    }
    if(self::$config["type"]["post"]){
      $types["post"] = e("Post");
    }    
    // Return HTML
    if($html){
      $html = "<select name='type' id='$class' class='$class'>";
        foreach ($types as $value => $text) {       
          $html .="<option value='$value' ".(!is_null($type) && $type == $value ? "selected" : "").">$text</option>";
        }
      $html .="</select>";
      return $html;
    }
    if(is_null($type)) return $types;
    if(isset($types[$type])) return ($plural) ? $types[$type]."s" : $types[$type];
    return FALSE;    
  }  
  /**
   * Generate Thumbnail
   * @author KBRmedia
   * @since  1.0
   */
  public static function generatethumb($src,$dest='',$desired_width, $desired_height = "auto", $quality='100'){

        if(!file_exists($src)) return array("error"=>TRUE, "msg"=> "This file ($src) doesn't exist."); 

        $extension = self::extension($src);   // Gets the extension of the file
        if(empty($dest)){
          $dest=str_replace($extension, $desired_width.$extension, $src); //Rename the file if the user didn't rename it.
        }
        $suffix = array(        
          '.jpeg' => 'jpeg',      
          '.jpg' => 'jpeg',      
          '.gif' => 'gif',        
          '.png' => 'png'      
        );
        if($suffix[$extension]=="png"){
          $quality=0;
        }elseif ($suffix[$extension]=="gif") {
          $quality=FALSE;
        }

        //Determines if the file has a valid extension, if not outputs an error.
        if(!isset($suffix[$extension])) return array("error"=>TRUE, "msg"=> "Unknown File Type. You can only resize jpeg (jpg), gif and png.");

        //Proceeds with resizing
        $image_suffix=$suffix[$extension];
        $createfrom='imagecreatefrom'.$image_suffix;    
        $image='image'.$image_suffix;
          $source_image = $createfrom($src);
          $width = imagesx($source_image);
          $height = imagesy($source_image);
          if($desired_height == "auto") $desired_height = floor($height*($desired_width/$width));
          $virtual_image = imagecreatetruecolor($desired_width,$desired_height);
          imagecopyresampled($virtual_image,$source_image,0,0,0,0,$desired_width,$desired_height,$width,$height);
          $image($virtual_image,$dest,$quality);

        return array("error"=>FALSE, "msg"=> "Image has been successfully resized.", "thumb"=>$dest);
  } 
  /**
   * Generate and Crop Thumbnail
   * @author KBRmedia
   * @since  1.3
   */
  public static function cropthumb($src,$dest='', $desired_width, $desired_height , $quality='100'){

        if(!file_exists($src)) return array("error"=>TRUE, "msg"=> "This file ($src) doesn't exist."); 

        $extension = self::extension($src);   // Gets the extension of the file
        if(empty($dest)){
          $dest=str_replace($extension, $desired_width.$extension, $src); //Rename the file if the user didn't rename it.
        }
        $suffix = array(        
          '.jpeg' => 'jpeg',      
          '.jpg' => 'jpeg',      
          '.gif' => 'gif',        
          '.png' => 'png'      
        );
        if($suffix[$extension]=="png"){
          $quality=0;
        }elseif ($suffix[$extension]=="gif") {
          $quality=FALSE;
        }

        //Determines if the file has a valid extension, if not outputs an error.
        if(!isset($suffix[$extension])) return array("error"=>TRUE, "msg"=> "Unknown File Type. You can only resize jpeg (jpg), gif and png.");
        
        $start_X = floor($desired_width / 4); 
        $start_Y = floor($desired_height / 4);
        $end_x = 3*$start_X;
        $end_y = 3*$start_Y;

        //Proceeds with resizing
        $image_suffix=$suffix[$extension];
        $createfrom='imagecreatefrom'.$image_suffix;    
        $image='image'.$image_suffix;
          $source_image = $createfrom($src);
          $width = imagesx($source_image);
          $height = imagesy($source_image);          
          $virtual_image = imagecreatetruecolor($desired_width,$desired_height);
          imagecopyresampled($virtual_image,$source_image, 0, 0, 0, 0,$desired_width,$desired_height,200, 200);
          $image($virtual_image,$dest,$quality);

        return array("error"=>FALSE, "msg"=> "Image has been successfully resized.", "thumb"=>$dest);
  }   
  /**
   * Filter Words
   * @author KBRmedia
   * @since  1.0
   */
  public static function filter($comment, $words){
    $words = explode(",", $words);
    foreach ($words as $word) {
      if(empty($word)) continue;
      $comment = str_ireplace(rtrim($word, " ") , str_repeat("*", strlen($word)) , $comment);
    }
    return $comment;
  }
  /**
   * Generate Menu
   * @author KBRmedia
   * @since  1.2  
   */
  public static function menu($menus = array(), $custom = FALSE){
    if(isset(self::$config["menus"]) && !empty(self::$config["menus"]) && !$custom){
      $sys_menus = json_decode(self::$config["menus"], TRUE);
      if(is_array($sys_menus)) $menus = $sys_menus;
    }

    $html = "";    
    if(is_array($menus)){
      foreach ($menus as $menu) {
        if(!isset($menu["href"]) || !isset($menu["text"])) continue;
        $icon = isset($menu["icon"]) && !empty($menu["icon"]) ? "<span class='fa fa-{$menu["icon"]}'></span> " : "";
        if(isset($menu["child"]) && is_array($menu["child"])){
          $html .="<li class='dropdown'>";
        }else{
          $html .="<li>";
        }
        $html .= "<a href='{$menu["href"]}' ".($menu["icon"] == "times-circle" ? "class='delete'":"").">$icon".e($menu["text"])."</a>";
          if(isset($menu["child"]) && is_array($menu["child"])){
            $html .= "<ul class='child'>";
              foreach ($menu["child"] as $child) {
                $icon = isset($child["icon"]) && !empty($menu["icon"]) ? "<span class='fa fa-{$child["icon"]}'></span> " : "";
                $html .= "<li class='child'><a href='{$child["href"]}'>$icon".e($child["text"])."</a>";
              }
            $html .= "</ul>";
          }
        $html .="</li>";
      }
    }
    return $html;
  }  

}