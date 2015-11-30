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
 * @author KBRmedia
 * @link http://gempixel.com
 * @package PremiumMediaScript
 * @subpackage MediaHandler
*/
class Media {
  /**
   * Media URL
   * @var string
   */
  private $url = NULL;
  /**
   * Player Dimension
   * @var integer
   */
  private $width = NULL;
  private $height = NULL;
  private $data = NULL;
  private $yt_api = NULL;
  private $vm_api = NULL;
  /**
   * Construct
   * @author KBRmedia
   * @since  1.0
   */
  public function __construct($api = array()){
    $this->data = new stdClass;
    if(isset($api["yt_api"])){
      $this->yt_api = $api["yt_api"];
    }
    if(isset($api["vm_api"])){
      $this->vm_api = $api["vm_api"];
    }
  }
  /**
   * Import Video
   * @author KBRmedia
   * @since  1.0
   * @param  string $url Supported URL
   * @return object      Formatted Data
   */
  public function import($url, $width = 500, $height = 480){
    // Clean URL
    $url=Main::clean($url, 3, TRUE);
    // Valid URL
    if(!Main::is_url($url)){
      return $this->out(array("error" => "Please enter a valid URL and make sure to include http:// or https://"));
    }
    $this->url    = $url;
    $this->width  = $width;
    $this->height = $height;  
    // Check if file is an image
    if(in_array(Main::extension($this->url, FALSE), array("jpg","png","gif"))){
      $this->import_image();
      return $this->out();
    }
    // Analyze URL
    $url  = parse_url($url);
    $url  = explode(".", $url["host"]);
    $url  = array_reverse($url); 
    $host = $url[1];
    // Check if Youtube
    if(in_array($host, array("youtube","youtu","youtube-nocookie"))){
      $host = "youtube";
    }    
    $host = "import_{$host}";
    // Use method
    if(method_exists(__CLASS__, $host)){
      // Get Content
      $this->{$host}();      
      return $this->out();
    }    
    return $this->out(array("error" => "Provider not supported or video not available."));
  }
  public function importID($id, $host){
    $host = "import_".$host;
    // Get Content
    $this->{$host}($id);      
    return $this->out();    
  }
  /**
   * Image Size
   * @author KBRmedia
   * @since  1.0
   */
  public function size($image, $array = FALSE){
    list($width,$height) = @getimagesize($image);
    if($array) return array("w" => $width, "h" => $height);
    return "($width x $height)";
  }  
  /**
   * Get Videos
   * @author KBRmedia
   * @since  1.0
   */
  public function out($response = NULL){
    // Return Custom Response
    if(!is_null($response)) return (object) $response;
    // Generate Object
    if(!is_object($this->data)) return (object) array("error" => "An unknown error has occured.");
    if(!isset($this->data->src)) return (object) array("error" => "An unknown error has occured.");
    
    if(!isset($this->data->duration)) $this->data->duration = "0";
    $this->data->desc = str_replace('"', "#&34;", $this->data->desc);
    $this->data->title = str_replace('"', " 554 ", $this->data->title);

    if(!isset($this->data->custom_code)){
      $this->data->code = "<iframe src='{$this->data->src}' width='{$this->width}' height='{$this->height}' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>";
    }else{
      $this->data->code = $this->data->custom_code;
    }
    return $this->data;
  }
  /**
   * Import Image
   * @since 1.1
   */
  public function import_image(){
    $file = getimagesize($this->url);
    if(!array_key_exists($file['mime'], formats())) return array("array" => "Sorry this picture cannot be accessed for some reason.");
    $this->data->title  = "";
    $this->data->slug   = "";
    $this->data->desc   = "";   
    $this->data->tag    = "";
    $this->data->thumb  = $this->url;
    $this->data->size   = "({$file[0]} x {$file[1]})"; 
    $this->data->src    = $this->url;   
    $this->data->custom_code    = "<img src='{$this->url}' width='100%'>";   
    $this->data->import = "Image";      
  }
  /**
   * Break oEmbed
   * @since 1.0
   **/
  public function import_break(){
    $array = get_meta_tags($this->url);
    if(!$array) return array("array" => "Sorry this video cannot be accessed for some reason.");
    $this->data->title  = $array["embed_video_title"];
    $this->data->slug   = Main::slug($this->data->title);
    $this->data->desc   = $array["description"];   
    $this->data->tag    = "";
    $this->data->thumb  =  $array["embed_video_thumb_url"];
    $this->data->size   = $this->size($this->data->thumb); 
    $this->data->src    = str_replace("http:", "", $array["embed_video_url"])."?embed=1";   
    $this->data->import = "Break";  
  }
  /**
   * Collegehumor oEmbed
   * @since 1.1.1
   **/
  public function import_collegehumor(){
    $json = @Main::http("http://www.collegehumor.com/oembed.json?url={$this->url}"); 
    if(!$json) return array('error' => "Sorry this video cannot be accessed for some reason.");
    $array = json_decode($json,TRUE);
    preg_match('|src="(.*)" width=|', $array["html"], $embed); 
    $this->data->title  = $array["title"];
    $this->data->slug   = Main::slug($this->data->title);
    $this->data->thumb  = $array["thumbnail_url"];
    $this->data->size   = $this->size($this->data->thumb);
    $this->data->src    = $embed[1];
    $this->data->desc   = "";      
    $this->data->tag    = "";         
    $this->data->import = "College Humor";             
  }
  /**
   * Dailymotion oEmbed
   * @since 1.0
   **/
  public function import_dailymotion(){
    $url=parse_url($this->url);
    $id=str_replace("/video/","",$url["path"]);
    $id=explode("_",$id);
    $this->data->id = $id[0];
    $dm = Main::http("https://api.dailymotion.com/video/{$this->data->id}?fields=title,description,embed_html,thumbnail_large_url,tag,allow_embed");    
    if(!$dm) return array("error" => "Sorry this video cannot be accessed for some reason.");
    $dm=json_decode($dm);
    if($dm->allow_embed == FALSE){
       return array("error" => "Sorry this video cannot be embedded because the author or the publisher prevented it.");
    } 
    $this->data->title  = $dm->title;
    $this->data->slug   = Main::slug($this->data->title);
    $this->data->desc   = strip_tags($dm->description);
    $this->data->src    = "//www.dailymotion.com/embed/video/{$this->data->id}";
    $this->data->thumb  = $dm->thumbnail_large_url;
    $this->data->size   = $this->size($this->data->thumb);    
    $this->data->tag    = $dm->tag;
    $this->data->tag    = @implode(",", $this->data->tag);
    $this->data->import = "Dailymotion";       
  }
  /**
   * Funny or Die oEmbed
   * @since 1.0
   **/
  public function import_funnyordie(){
    $file = @Main::http("http://www.funnyordie.com/oembed.json?url={$this->url}");
    $meta = get_meta_tags($this->url);
    if(!$file) return array('error' => "Sorry this video cannot be accessed for some reason.");
    $array=json_decode($file, TRUE);      
    $this->data->title  = $array["title"];
    $this->data->slug   = Main::slug($this->data->title);
    $this->data->desc   = strip_tags($meta["description"]);
    $this->data->src    = str_replace("/videos/", "/embed/", $this->url);
    $this->data->thumb  = $array["thumbnail_url"];
    $this->data->size   = $this->size($array["thumbnail_url"]);
    $this->data->tag    = $meta["keywords"];
    $this->data->import = "Funny or Die";       
  }
  /**
   * Metacafe Custom API
   * @since 1.0
   **/
  public function import_metacafe(){
    $array=@get_meta_tags($this->url);
    if(!$array) return array("error" => "Sorry this video cannot be accessed for some reason.");   

    $url = parse_url($this->url);
    $url = $url["path"];
    $url = explode("/",$url);
    $this->data->id = $url[2];

    $this->data->title  = str_replace(" - Video", "", $array["title"]);
    $this->data->slug   = Main::slug($this->data->title);
    $this->data->desc   = str_replace(" by Metacafe.com", "", $array["description"]);   
    $this->data->desc   = preg_replace('/Watch Video about (.*)/is', "", $this->data->desc);
    $this->data->tag    = $array["keywords"];
    $this->data->thumb  = "http://www.metacafe.com/thumb/{$this->data->id}.jpg";
    $this->data->size   =  $this->size($this->data->thumb);
    $this->data->src    =  "http://www.metacafe.com/embed/{$this->data->id}/";
    $this->data->import = "Metacafe";    
  }  
  /**
   * SoundCloud
   * @author KBRmedia
   * @since  1.0
   */
 public function import_soundcloud(){
    $file = @Main::http("http://soundcloud.com/oembed?url={$this->url}&format=json");    
    $array = json_decode($file, TRUE);
    if(!$array) return array("error" => "Sorry this video cannot be accessed for some reason.");       
    preg_match('|<iframe [^>]*(src="[^"]+")[^>]*|', $array["html"], $embed);
    
    $this->data->id = NULL;
    $this->data->title  = $array["title"];
    $this->data->slug   = Main::slug($this->data->title);
    $this->data->desc   = $array["description"];   
    $this->data->tag    = "";
    $this->data->thumb  = $array["thumbnail_url"];
    $this->data->size   =  $this->size($this->data->thumb);
    $this->data->src    =  str_replace('"', "", str_replace("src=", "", $embed[1]));     
  }     
  /**
   * Twitch 
   * @since  1.5
   */
  public function import_twitch(){
    $url = parse_url($this->url);
    $p = explode("/", $url["path"]);
    
    if(isset($p[2]) && $p[2] == "c" && !empty($p[3])){
      $file = @Main::http("https://api.twitch.tv/kraken/videos/c{$p[3]}");
      $array = json_decode($file, TRUE);
      $embed = '<object bgcolor="#000000" data="http://www.twitch.tv/swflibs/TwitchPlayer.swf" height="378" id="clip_embed_player_flash" type="application/x-shockwave-flash" width="620"><param name="movie" value="http://www.twitch.tv/swflibs/TwitchPlayer.swf" /><param name="allowScriptAccess" value="always" /><param name="allowNetworking" value="all" /><param name="allowFullScreen" value="true" /><param name="flashvars" value="channel=gamesradar&amp;auto_play=false&amp;start_volume=25&amp;videoId=c'.$p[3].'&amp;device_id=f17f1020d1c05763" /></object>';   
     $this->data->src = "n/a";
      $this->data->custom_code    =   $embed;        
    }else{
      $file = @Main::http("https://api.twitch.tv/kraken/channels/{$p[1]}");
      $array = json_decode($file, TRUE);
      $this->data->src = "http://www.twitch.tv/{$p[1]}/embed";
    }
    
    $this->data->id = NULL;
    $this->data->title  = $array["status"];
    $this->data->slug   = Main::slug($this->data->title);
    $this->data->desc   = $array["status"];   
    $this->data->tag    = "";
    $this->data->thumb  = $array["video_banner"];
    $this->data->size   =  $this->size($this->data->thumb);
  
  }
  /**
   * Import Vine
   * @author KBRmedia
   * @since  1.1.1
   */
  public function import_vine(){
    $array=@get_meta_tags($this->url);
    if(!$array) return array("error" => "Sorry this video cannot be accessed for some reason.");   
    $content=Main::http($this->url);
    preg_match('/property="og:image" content="(.*?)"/', $content, $matches);

    $url = parse_url($this->url);
    $url = $url["path"];
    $url = explode("/",$url);
    $this->data->id = $url[2];
    $array["description"] = str_replace('&#34;'," ", $array["description"]);
    $array["description"] =preg_replace('/[^\00-\255]+/u', '', $array["description"]);

    $this->data->title  =  str_replace("Vine is the best way to see and share life in motion. Create short, beautiful, looping videos in a simple and fun way for your friends and family to see.", "",  $array["description"]);
    $this->data->slug   = Main::slug($this->data->title);
    $this->data->desc   = $this->data->title;   
    $this->data->tag    = "";
    $this->data->thumb  = $matches[1];
    $this->data->size   =  $this->size($this->data->thumb);
    $this->data->src    =  "https://vine.co/v/{$this->data->id}/embed/simple";    
    $this->data->custom_code    = "<iframe src='https://vine.co/v/{$this->data->id}/embed/simple' width='{$this->width}' height='{$this->height}' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe><script src=’https://platform.vine.co/static/scripts/embed.js'></script>";   

  }
  /**
   * Import Vimeo
   * @author KBRmedia
   * @since  1.5
   */
  public function import_vimeo() {
      preg_match('((http://|https://|www.)+(vimeo.)+[\w-\d]+(/)+(\d+))',$this->url, $id);
      if(isset($id[4])){
          $id=$id[4];
       }else{
          return array("error" => "Sorry this video cannot be accessed for some reason.");
       }

      $json=Main::http("https://api.vimeo.com/videos/$id?access_token={$this->vm_api}");    
      if(!$json) return array("error" => "Sorry this video cannot be accessed for some reason.");

      $vm = json_decode($json); 
      if($vm->privacy->embed != "public") return array("error" => "Sorry this video cannot be embedded because the author or the publisher prevented it.");

      $this->data->id    = $id;
      $this->data->title = $vm->name;
      $this->data->slug  = Main::slug($this->data->title);
      $this->data->desc  = strip_tags($vm->description);
      $this->data->duration     = $vm->duration; 
      $this->data->src  = 'http://player.vimeo.com/video/'.$id.'?title=0&amp;byline=0&amp;portrait=0';
      $this->data->thumb = $vm->pictures->sizes[3]->link;
      $this->data->size   =  $this->size($this->data->thumb);     
      $tags = array();
      foreach ($vm->tags as $v) {
         $tags[] = $v->tag;
       } 
      $this->data->tag   = implode(",", $tags);
  }
  /**
   * Youtube Data API
   * @since 1.5
   **/
  public function import_youtube($id = NULL){
    if(is_null($id)){
      // Match Youtube Link
      if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $this->url, $match)) {
        $id = $match[1];
      }else{
        return array("error" => "Sorry this video cannot be accessed for some reason.");
      }
    }

    $part = "snippet,contentDetails,statistics,topicDetails,status";
    $url = "https://www.googleapis.com/youtube/v3/videos?key={$this->yt_api}&part=$part&id=$id";  

    $json=Main::http($url);    
    if(!$json) return array("error" => "Sorry this video cannot be accessed for some reason.");

    $json = json_decode($json);
    // Fix duration
    $start = new DateTime('@0'); // Unix epoch
    $start->add(new DateInterval($json->items[0]->contentDetails->duration));
    $seconds = $start->format('H')*24;    
    $seconds = $seconds + $start->format('i')*60;    
    $seconds = $seconds + $start->format('s');

    $this->data->title  = $json->items[0]->snippet->title;
    $this->data->slug   = Main::slug($this->data->title);
    $this->data->id     = $id;  
    $this->data->duration     = $seconds;  
    $this->data->desc   = Main::clean($json->items[0]->snippet->description, 3, TRUE);
    $this->data->thumb  = "http://img.youtube.com/vi/$id/hqdefault.jpg";
    $this->data->size   = $this->size($this->data->thumb);    
    $this->data->tag    = @implode(',',$json->items[0]->snippet->tags);
    $this->data->src    = "//www.youtube-nocookie.com/embed/$id?showinfo=0&rel=0&iv_load_policy=3&modestbranding=1";
    $this->data->import = "Youtube";

  }
  /**
   * WebTV
   * @author KBRmedia
   * @since  1.0
   */
  public function import_web(){
    $array = get_meta_tags($this->url);
    if(!$array) return array("error" => "Sorry this video cannot be accessed for some reason.");
    $id = explode("__", $this->url);
    $id = explode("?", $id[1]);
    $id = $id[0];
    $this->data->title  = $array["description"];
    $this->data->slug   = Main::slug($this->data->title);
    $this->data->desc   = Main::clean($array["description"], 3, TRUE);
    $this->data->thumb  =  $array["twitter:image:src"];
    $this->data->size   = $this->size($this->data->thumb);    
    $this->data->tag    = "";
    $this->data->src    = empty($array["twitter:player"]) ? "http://turkrapfm.web.tv/embed/{$id}/0/0" : $array["twitter:player"];
    $this->data->import = "Web.tv";    
  }
  /**
   * Youku
   */
  public function import_youku(){
    $id = explode("/", $this->url);
    $id = explode(".html", $id[4]);
    $id = str_replace("id_", "", str_replace(".html","",$id[0]));
    $array = get_meta_tags($this->url);
    $t = json_decode(file_get_contents("http://v.youku.com/player/getPlayList/VideoIDS/{$id}"));
    if(!$array) return array("error" => "Sorry this video cannot be accessed for some reason.");
    $this->data->title  = $array["title"];
    $this->data->slug   = $id;
    $this->data->desc   = Main::clean($array["description"], 3, TRUE);
    $this->data->thumb  =  $t->data[0]->logo;
    $this->data->size   = $this->size($this->data->thumb);    
    $this->data->tag    = "";
    $this->data->src    = "http://player.youku.com/embed/$id";
    $this->data->import = "Youku";  
  }
  // End of File
}