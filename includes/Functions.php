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
 * @subpackage PHP_Functions
 */
/**
 * Allowable Types:: Alias
 */
function types($type = NULL, $html = FALSE, $plural = FALSE, $class = 'type'){
	return Main::types($type, $html, $plural, $class);
}
function types_icon($type = NULL){
	$icons = array(
							"video" => "youtube-play",
							"music" => "music",
							"picture" => "photo",
							"vine" => "vine",
							"post" => "pencil-square",
						);
	return isset($icons[$type]) ? $icons[$type] : "";
}
/**
 * Allowable Formats
 */
function formats($format = NULL, $extensions_only = FALSE){
	// Array of allowed extensions
	$formats = array( 
		'image/jpeg' => 'jpg', 
		'image/gif' => 'gif',
		'image/png' => 'png', 
		'video/mp4' => 'mp4',
		"audio/mp3" => 'mp3'
	);

	if($extensions_only) return implode(", ", $formats);
	if(is_null($format)) return $formats;
	if(isset($formats[$format])) return $formats[$format];
	return FALSE;
}
/**
 * Max Upload size
 * @since 1.0
 **/
function max_size(){
  $value = ini_get( 'upload_max_filesize' );
  if ( is_numeric( $value ) ) {
        return $value;
  } else {
    $value_length = strlen( $value );
    $qty = substr( $value, 0, $value_length - 1 );
    $unit = strtolower( substr( $value, $value_length - 1 ) );
    switch ( $unit ) {
      case 'k':
          $qty *= 1024;
          break;
      case 'm':
          $qty *= 1048576;
          break;
      case 'g':
          $qty *= 1073741824;
          break;
    }
    return $qty/(1024*1024);
  }  
  return $value;
}
/**
 * Detect Mobile
 * @author KBRmedia
 * @since  1.0
 */
function mobile(){
	return (bool)preg_match('#\b(ip(hone|od)|android\b.+\bmobile|opera m(ob|in)i|windows (phone|ce)|blackberry'.
                    '|s(ymbian|eries60|amsung)|p(alm|rofile/midp|laystation portable)|nokia|fennec|htc[\-_]'.
                    '|up\.browser|[1-4][0-9]{2}x[1-4][0-9]{2})\b#i', $_SERVER['HTTP_USER_AGENT'] );	
}
/**
 *	Plugin Aliases
 */
function plug($area, $param = array()){
	return Main::plug($area, $param = array());
}
function hook($area, $fn){
	return Main::hook($area, $fn);
}
/**
 * Build menu item
 * @author KBRmedia
 * @since  1.2
 * @param  array  $menus
 */
function build_menu($menus = array(), $custom = FALSE){
	return Main::menu($menus, $custom);
}
/**
 * Translate strings: Two functions
 * @since  1.0
 * @author KBRmedia
 */
if(!function_exists("_")){
	function _($text){
		return Main::e($text);
	}
}
if(!function_exists("e")){
	function e($text){
		return Main::e($text);
	}
}
/**
 * Ad Type
 */
function ad_type($type){
	$types = array(
			"728" => "728x90",
			"300" => "300x250",
			"468" => "468x60",
			"resp" => "Responsive",
			"preroll" => "Pre-Roll"
		);
	if(!isset($types[$type])) return FALSE;
	return $types[$type];
}
/**
 * [filter description]
 * @param  [type] $type [description]
 * @return [type]       [description]
 */
function filter($type){
	if($type == "trending") return FALSE;
	echo ' <select class="filter" data-key="filter" style="display: none">
            <optgroup label="'.e("Sort by").'">
              <option value="date" '.(Main::is_set('filter','date') ? 'selected': '').'>'.e("Date").'</option>
              <option value="views" '.(Main::is_set('filter','views') ? 'selected': '').'>'.e("Views").'</option>
              <option value="likes" '.(Main::is_set('filter','likes') ? 'selected': '').'>'.e("Likes").'</option>
              <option value="comments" '.(Main::is_set('filter','comments') ? 'selected': '').'>'.e("Comments").'</option>         
            </optgroup>
          </select>';

}
/**
 * [providers description]
 * @return [type] [description]
 */
function providers(){
	include(ROOT."/includes/Media.class.php");
	$list = get_class_methods(new Media());
	unset($list[0],$list[1],$list[2],$list[3],$list[4]);
	foreach ($list as $key) {
		if($key == "import_web") continue;
		$providers[] = ucfirst(str_replace("import_","", $key));
	}
	return implode(", ", $providers);
}
/**
 * [get_plugins description]
 * @return [type] [description]
 */
function get_plugins(){
		$return = array();
	  foreach (new RecursiveDirectoryIterator(PLUGINS."/") as $path){
      if(!$path->isDir() && $path->getFilename()!=="." && $path->getFilename()!==".." && $path->getFilename()!=="index.php"){  
        $file=explode(".", $path->getFilename());
        $file=$file[0];
        $code=strtolower($file);
        $data=token_get_all(file_get_contents($path));
        $data=isset($data[1][1])?$data[1][1]:FALSE;
        if(strlen($data) > 5){
          if(preg_match("~Language:\s(.*)~", $data,$name)){
            $name=Main::truncate(strip_tags(trim($name[1])),10);
          }
          if(preg_match("~Author:\s(.*)~", $data,$author)){
            $author=strip_tags(trim($author[1]));
          }           
          if(preg_match("~Date:\s(.*)~", $data,$date)){
            $date=strip_tags(trim($date[1]));
          } 
        }
      }
    }
    return $return;
}
/**
 * To Seconds
 */
function toseconds($time){
	$time = explode(":", $time);
	if(isset($time[2])){
		return $time[0]*24 + $time[1]*60 + $time[2];
	}else{
		return $time[0]*60 + $time[1];
	}
}
/**
 * To Time
 */
function totime($time){
	if($time == "0") return "0";
	if($time >= 3600) return date("G:i:s",$time);
	if($time < 60) return "0:".date("s",$time);
	return trim(date("i:s",$time), "0");
}