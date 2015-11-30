<?php 
/**
 * ====================================================================================
 *                           Easy_Media_Script (c) KBRmedia
 * ----------------------------------------------------------------------------------
 *  @copyright - This software is exclusively sold at CodeCanyon.net. If you have downloaded this
 *  from another site or received it from someone else than me, then you are engaged
 *  in illegal activity. You must delete this software immediately or buy a proper
 *  license from http://codecanyon.net/user/KBRmedia/portfolio?ref=KBRmedia.
 *
 *  @license http://gempixel.com/license
 *
 *  Thank you for your cooperation and don't hesitate to contact me if anything :)
 * ====================================================================================
 *
 * @author KBRmedia (http://gempixel.com)
 * @link http://gempixel.com
 * @package Easy_Media_Script
 * @subpackage Admin_Class
 */

class Admin{
  /**
   * Authorized actions
   * @since 1.0
   **/
  protected $actions = array("ads","users","media","blog","pages","settings","themes","editor","languages","search","server", "categories","stats","activities", "tools", "comments", "reports","menu");
  /**
   * Config + DB
   * @since 1.0
   **/
  protected $config;
  protected $db;  

  /**
   * Admin Info + URL
   * @since 1.0
   **/
  protected $user;
  protected $url;  
  /**
   * Reserved Variable
   * @since 1.0
   **/
  protected $page;  
  protected $action;  
  protected $do;
  protected $id;
  /**
   * Admin Limit/Page
   * @since 1.0
   **/
  protected $limit=24;
  /**
   * Valid Media types
   * @since 1.0
   **/
  protected $formats = NULL;
  /**
   * Construct Admin
   * @since 1.0
   **/
  public function __construct($config,$db){
    $this->config=$config;
    $this->db=$db;     
    $this->url="{$this->config["url"]}/admin";
    $this->page=(isset($_GET["page"]) && is_numeric($_GET["page"]) && $_GET["page"]!="0") ? Main::clean($_GET["page"],3,TRUE):"1";  
    $this->check();
  }
  /**
   * Free Memory (don't need it but do it anyway)
   * @since 1.0
   **/
  public function __destruct(){
    unset($this->db, $this->user, $this->config);
  }
  /**
   * Check if user is logged and has admin privileges!
   * @since 1.0
   **/
  public function check(){
    if($info=Main::user()){
      if($user=$this->db->get("user",array("id"=>"?","auth_key"=>"?"),array("limit"=>1),array($info[0],$info[1]))){               
        if(!$user->admin) return Main::redirect("404");
        $this->logged=TRUE;
        $this->user=$user;     
        $user=NULL;        
        // Unset sensitive information
        unset($this->user->password);
        unset($this->user->auth_key);          
        return TRUE;
      }
    }
   return Main::redirect("404");
  }  
  /**
   * Run Admin Panel
   * @since 1.0
   **/
  public function run(){
    if(isset($_GET["a"]) && !empty($_GET["a"])){
      $var=explode("/",$_GET["a"]);
      if(in_array($var[0],$this->actions) && method_exists("Admin", $var[0])){
        $this->action=Main::clean($var[0],3,TRUE);
        if(isset($var[1]) && !empty($var[1])) $this->do=Main::clean($var[1],3,TRUE);
        if(isset($var[2]) && !empty($var[2])) $this->id=Main::clean($var[2],3,TRUE);
        return $this->{$var[0]}();
      } 
      return Main::redirect("admin",array("danger","Oups! The page you are looking for doesn't exist."));
    }else{
      return $this->home();
    }
  }  
  /**
   * Admin Home Page
   * @since 1.0
   **/
  protected function home(){
    if(empty($this->config["count_media"])){
      $count = $this->db->count("media");
      $this->db->update("setting","value = $count",array("config"=>"?"),array("count_media"));
    }
    // Generate Chart
    $this->charts();
    // Get Data
    $videos          = $this->db->get("media",array("approved" => "1"),array("limit"=>15,"order"=>"date"));
    $top_videos      = $this->db->get("media",array("approved" => "1"),array("limit"=>15,"order"=>"views"));
    $users           = $this->db->get("user","",array("limit"=> 15,"order"=>"date"));
    $comments        = $this->db->get(array("custom" => "{$this->config["prefix"]}comment.*, {$this->config["prefix"]}user.username as author, {$this->config["prefix"]}user.avatar, {$this->config["prefix"]}user.email as email, {$this->config["prefix"]}media.title as title, {$this->config["prefix"]}media.url as url FROM `{$this->config["prefix"]}comment` INNER JOIN `{$this->config["prefix"]}user` ON {$this->config["prefix"]}user.id = {$this->config["prefix"]}comment.userid INNER JOIN `{$this->config["prefix"]}media` ON {$this->config["prefix"]}media.id = {$this->config["prefix"]}comment.mediaid"),"",array("limit"=>10,"order"=>"date"));
    $video_reports   = $this->db->get("temp", array("type" => "media_report"), array("limit" => 15, "order"=>"date"));
    $comment_reports = $this->db->get("temp", array("type" => "comment_report"), array("limit" => 15, "order"=>"date"));
    $user_reports    = $this->db->get("temp", array("type" => "user_report"), array("limit" => 15, "order"=>"date"));
    $this->db->get("media",array("approved" => "0"));
    $videos_moderate = $this->db->rowCountAll;

    // if($videos_moderate > 0){
    //   Main::admin_add("<script>$('.moderate-link').addClass('current');</script>","custom",TRUE);
    // }

    Main::set("title","Admin cPanel");
    $this->header();
    include($this->t("index"));
    $this->footer();
  }
    /**
     *  Dashboard Chart Data Function
     *  Generate data and inject it into the homepage and append the flot library.
     *  @since 1.0
     */   
      protected function charts($filter="day",$span=30){
        if(isset($_GET["filter"])) $filter=$_GET["filter"];
        $new_date=array();  
        $new_clicks=array(); 
        $new_videos=array();        
        // Store as Array
        $this->db->object=FALSE;
        // Daily Stats
        if($filter=="monthly"){
          $span=12;

          $usersbydate = Main::cache_get("admin_user_month");
          if($usersbydate == null){
            $usersbydate=$this->db->get(array("count"=>"COUNT(MONTH(date)) as count, DATE(date) as date","table"=>"user"),"(date >= DATE_SUB(CURDATE(), INTERVAL $span MONTH))",array("group_custom"=>"MONTH(date)","limit"=>30));     
            Main::cache_set("admin_user_month", $usersbydate,15);
          }

          $urls=Main::cache_get("admin_url_month");
          if($urls == null){
            $urls=$this->db->get(array("count"=>"COUNT(MONTH(date)) as count, DATE(date) as date","table"=>"media"),"(date >= DATE_SUB(CURDATE(), INTERVAL $span MONTH))",array("group_custom"=>"MONTH(date)","limit"=>30));   
            Main::cache_set("admin_url_month", $urls,15);
          }
          
          $clicks=Main::cache_get("admin_click_month");
          if($clicks == null){
            $clicks=$this->db->get(array("count"=>"COUNT(MONTH(date)) as count, DATE(date) as date","table"=>"comment"),"(date >= DATE_SUB(CURDATE(), INTERVAL $span MONTH))",array("group_custom"=>"MONTH(date)","limit"=>30));   
            Main::cache_set("admin_click_month", $clicks,15);
          }


          foreach ($usersbydate as $user[0] => $data) {
            $new_date[date("F Y",strtotime($data["date"]))]=$data["count"];
          } 
          foreach ($urls as $urls[0] => $data) {
            $new_videos[date("F Y",strtotime($data["date"]))]=$data["count"];
          }
          foreach ($clicks as $clicks[0] => $data) {
            $new_clicks[date("F Y",strtotime($data["date"]))]=$data["count"];
          }        
          $timestamp = time();
          for ($i = 0 ; $i < $span ; $i++) {
              $array[date('F Y', $timestamp)]=0;
              $timestamp -= 30*24 * 3600;
          }
        }elseif($filter=="yearly"){

          $span=8;


          $usersbydate = Main::cache_get("admin_user_year");
          if($usersbydate == null){
           $usersbydate=$this->db->get(array("count"=>"COUNT(YEAR(date)) as count, DATE(date) as date","table"=>"user"),"(date >= DATE_SUB(CURDATE(), INTERVAL $span YEAR))",array("group_custom"=>"YEAR(date)","limit"=>30));      
            Main::cache_set("admin_user_year", $usersbydate,15);
          }

          $urls=Main::cache_get("admin_url_year");
          if($urls == null){
            $urls=$this->db->get(array("count"=>"COUNT(YEAR(date)) as count, DATE(date) as date","table"=>"media"),"(date >= DATE_SUB(CURDATE(), INTERVAL $span YEAR))",array("group_custom"=>"YEAR(date)","limit"=>30)); 
   
            Main::cache_set("admin_url_year", $urls,15);
          }
          
          $clicks=Main::cache_get("admin_click_year");
          if($clicks == null){
            $clicks=$this->db->get(array("count"=>"COUNT(YEAR(date)) as count, DATE(date) as date","table"=>"comment"),"(date >= DATE_SUB(CURDATE(), INTERVAL $span YEAR))",array("group_custom"=>"YEAR(date)","limit"=>30));  
            Main::cache_set("admin_click_year", $clicks,15);
          }

          foreach ($usersbydate as $user[0] => $data) {
            $new_date[date("Y",strtotime($data["date"]))]=$data["count"];
          } 
          foreach ($urls as $urls[0] => $data) {
            $new_videos[date("Y",strtotime($data["date"]))]=$data["count"];
          }
          foreach ($clicks as $clicks[0] => $data) {
            $new_clicks[date("Y",strtotime($data["date"]))]=$data["count"];
          }        
          $timestamp = time();
          for ($i = 0 ; $i < $span ; $i++) {
              $array[date('Y', $timestamp)]=0;
              $timestamp -= 12*30*24 * 3600;
          }
        }else{

          $usersbydate = Main::cache_get("admin_user_daily");
          if($usersbydate == null){
           $usersbydate=$this->db->get(array("count"=>"COUNT(DATE(date)) as count, DATE(date) as date","table"=>"user"),"(date >= CURDATE() - INTERVAL $span DAY)",array("group_custom"=>"DATE(date)","limit"=>"0 , $span"));        
            Main::cache_set("admin_user_daily", $usersbydate,15);
          }

          $videos=Main::cache_get("admin_video_daily");
          if($videos == null){
            $videos=$this->db->get(array("count"=>"COUNT(DATE(date)) as count, DATE(date) as date","table"=>"media"),"(date >= CURDATE() - INTERVAL $span DAY)",array("group_custom"=>"DATE(date)","limit"=>"0 , $span"));
            Main::cache_set("admin_video_daily", $videos,15);
          }
          
          $clicks=Main::cache_get("admin_click_daily");
          if($clicks == null){
            $clicks=$this->db->get(array("count"=>"COUNT(DATE(date)) as count, DATE(date) as date","table"=>"comment"),"(date >= CURDATE() - INTERVAL $span DAY)",array("group_custom"=>"DATE(date)","limit"=>"0 , $span"));  
            Main::cache_set("admin_click_daily", $clicks,15);
          }
          foreach ($usersbydate as $user[0] => $data) {
            $new_date[date("d M",strtotime($data["date"]))]=$data["count"];
          } 
          foreach ($videos as $videos[0] => $data) {
            $new_videos[date("d M",strtotime($data["date"]))]=$data["count"];
          }
          foreach ($clicks as $clicks[0] => $data) {
            $new_clicks[date("d M",strtotime($data["date"]))]=$data["count"];
          }        
          $timestamp = time();
          for ($i = 0 ; $i < $span ; $i++) {
              $array[date('d M', $timestamp)]=0;
              $timestamp -= 24 * 3600;
          }            
        }
       
        $this->db->object=TRUE;
        $date=""; $var=""; $date1=""; $var1=""; $date2=""; $var2=""; $i=0; 

        foreach ($array as $key => $value) {
          $i++;
          if(isset($new_date[$key])){
            $var.="[".($span-$i).", ".$new_date[$key]."], ";
            $date.="[".($span-$i).",\"$key\"], ";
          }else{
            $var.="[".($span-$i).", 0], ";
            $date.="[".($span-$i).", \"$key\"], ";
          }
          if(isset($new_videos[$key])){
            $var1.="[".($span-$i).", ".$new_videos[$key]."], ";
            $date1.="[".($span-$i).",\"$key\"], ";
          }else{
            $var1.="[".($span-$i).", 0], ";
            $date1.="[".($span-$i).", \"$key\"], ";
          }  
          if(isset($new_clicks[$key])){
            $var2.="[".($span-$i).", ".$new_clicks[$key]."], ";
            $date2.="[".($span-$i).",\"$key\"], ";
          }else{
            $var2.="[".($span-$i).", 0], ";
            $date2.="[".($span-$i).", \"$key\"], ";
          }             
        }
        $data=array("registered"=>array($var,$date),"videos"=>array($var1,$date1),"clicks"=>array($var2,$date2));
        Main::admin_add("{$this->config["url"]}/static/js/flot.js","script",FALSE);
        Main::admin_add("<script type='text/javascript'>var options = {
              series: {
                lines: { show: true, lineWidth: 2,fill: true},                
                points: { show: true, lineWidth: 2 }, 
                shadowSize: 0
              },
              grid: { hoverable: true, clickable: true, tickColor: 'transparent', borderWidth:0 },
              colors: ['#0da1f5', '#1ABC9C','#F11010'],
              xaxis: {ticks:[{$data["videos"][1]},{$data["clicks"][1]},{$data["registered"][1]}], tickDecimals: 0, color: '#999'},
              yaxis: {ticks:3, tickDecimals: 0, color: '#CFD2E0'},
              xaxes: [ { mode: 'time'} ]
          }; 
          var data = [{
              label: ' Media ',
              data: [{$data["videos"][0]}]
          },{
              label: ' Comments',
              data: [{$data["clicks"][0]}]
          },{
              label: ' Users ',
              data: [{$data['registered'][0]}]
          }];
          $.plot('#user-chart', data ,options);</script>",'custom',TRUE);        
      }  
  /**
    * Search
    * @since 1.0
    **/
  protected function search(){
    // Validate Q
    if(!isset($_GET["q"]) || empty($_GET["q"]) || strlen($_GET["q"])<3) {
      return Main::redirect("admin",array("danger","Keyword must be at least 3 characters."));
    }      
    $q = Main::clean($_GET["q"], 3, TRUE);
    // Generate Data
    $users=$this->db->search("user",array("name"=>":q","username"=>":q","email"=>":q"),array("order"=>"date","limit"=> $this->limit),array(":q"=>"%$q%"));
    $videos=$this->db->search("media",array("title"=>":q","description"=>":q","tags"=>":q"),array("order"=>"date","limit"=> $this->limit),array(":q"=>"%$q%"));
    $count = NULL;
    $pagination = NULL;
    // Header
    Main::set("title","{$q} - Unified Search");
    $this->header();
    // Show Not Found Error
    if(!$users && !$videos){
      echo "<h3>No results found</h3> <p>Your keyword did not match any results. Please try a different keyword.</p>";
    }else{
      echo "<h4>Results for <strong>{$q}</strong> </h4>";    
    }    
    // Show Videos
    if($videos){
      echo "<div id='media-holder'>
              <ul class='medialist'>";
                foreach ($videos as $media){
                  echo "<li>";
                    if($this->config['local_thumbs']){
                       echo "<img src='{$this->config['url']}/content/thumbs/{$media->thumb}' />";
                    }else{
                       echo "<img src='{$media->ext_thumb}' />";
                    }
                     echo "<a class='overlay' href='".Main::href('view/{$media->url}')."' target='_blank'>
                      <span>".Main::truncate($media->title,25)."</span>
                      <span>Views: {$media->views}</span>
                      <span>Likes / Dislikes: {$media->likes} / {$media->dislikes}</span>
                      <center><strong>Click to view this video</strong></center>
                    </a>
                    <div class='titles'>".ucfirst($media->type)."</div>                       
                    <div class='options'>
                      <a href='".Main::ahref("media/edit/{$media->id}")."' title='Edit' class='edit btn btn-xs btn-primary'>Edit</a>
                      <a href='".Main::ahref("media/delete/{$media->id}").Main::nonce("delete_media-{$media->id}")."' title='Delete this video' class='delete btn btn-xs btn-danger'>Delete</a>
                    </div>         
                  </li>";
                }
      echo "</ul>   
        </div><hr>";
    }
    // Show Users
    if($users){
      include($this->t("users"));
    }    

    // Footer
    $this->footer();    
  }
  /**
   * Media
   * @since 1.5.1
   **/
  protected function media(){
   // Switch functions
   if(in_array($this->do, array("add","delete","edit","moderate","approve","import","youtube","view","vimeo","dailymotion"))){
      $fn = __FUNCTION__."_{$this->do}";
      return $this->$fn();
    }  
    // Redirect to video  
    if(empty($this->do)){
      return Main::redirect(Main::ahref("media/video","",FALSE));
    }
    $this->db->get("media",array("approved" => "0"));
    $videos_moderate = $this->db->rowCountAll;    
    // Format Sort 
    $types = types();    
    $type=(array_key_exists($this->do, $types))?$types[$this->do]:"Video";
    $sort=isset($_GET["sort"])?$_GET["sort"]:'id';
    $by=(isset($_GET["order"]) && $_GET["order"]=="asc")?"1":"0";

    // Get Data
    $videos = $this->db->get("media",array("type"=>"?","approved"=>1),array("order"=>$sort,"asc" => $by,"count"=>1,"limit"=>($this->page-1)*$this->limit.", ".$this->limit),array($this->do));
    $count = $this->db->rowCount;

    // Calculate Pages
    if(($this->db->rowCount%$this->limit)<>0) {
      $max=floor($this->db->rowCount/$this->limit)+1;
    } else {
      $max=floor($this->db->rowCount/$this->limit);
    }
    $pagination=Main::pagination($max, $this->page, Main::ahref("media/{$this->do}")."?page=%d&filter=$sort");   

    Main::set("title","Manage $type");
    $this->header();
    include($this->t("media"));
    $this->footer();
  }
    /**
    * Add Media
    * @since 1.5.1
    */
    private function media_add(){

      // Add media to database
      if(isset($_POST["token"])){
        // Validate Token
        if(!Main::validate_csrf_token($_POST["token"])){
          Main::redirect(Main::ahref("media/add","",FALSE),array("danger","Invalid token, please try again."));
          return;
        }
        // Disable if demo
        if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));
        $error = NULL;
        if(empty($_POST["title"]) || strlen($_POST["title"]) < 3) $error.="<p>Title must be at least 3 characters.</p>"; 
        $slug = (empty($_POST["slug"]) || Main::slug($_POST["title"]) == $_POST["slug"]) ? Main::slug($_POST["title"]) : $_POST["slug"];

        // Prepare data
        $unique = $this->uniqueid();
        $data = array(
            ":type" => types($_POST["type"]) ? $_POST["type"] : 'video', 
            ":catid"=> isset($_POST["category"]) ? $_POST["category"] : "1", 
            ":featured"=> in_array($_POST["featured"], array("0","1")) ? $_POST["featured"] : "0", 
            ":uniqueid"=> $unique, 
            ":title"=> Main::clean($_POST["title"],3,TRUE), 
            ":url"=> $slug,
            ":description"=> Main::clean($_POST["description"],4,FALSE), 
            ":userid"=> $this->user->id,
            ":tags"=> Main::clean($_POST["tags"],3,TRUE),
            ":date"=> "NOW()",
            ":nsfw" => Main::clean($_POST["nsfw"],3,TRUE),
            ':duration'=> $_POST["duration"] > 0 ? toseconds($_POST["duration"]) : "", 
            ":subscribe" => isset($_POST["subscribe"]) && in_array($_POST["subscribe"], array("0","1")) ? $_POST["subscribe"] : "0", 
          );
        // Source of the URL
        if(isset($_POST["source"])) $data[":source"] = Main::clean($_POST["source"], 3);
        // Check Media
        
          if($_POST["media_switch"]=="0"){ // Upload video
            // Validate format
            $formats = formats();
            if(!isset($_FILES["media_file"]) || empty($_FILES["media_file"])) $error.="<p>You forgot to select a media file to upload.</p>";
            elseif(!isset($formats[$_FILES["media_file"]["type"]])) $error.="<p>The media type you uploaded is not supported.</p>";
            elseif($_FILES["media_file"]["size"] > $this->config["max_size"]*1024*1024) $error .= "<p>The media size must not exceed {$this->config["max_size"]} MB</p>";
            // File Name
            $file = md5($unique.rand(0,999)).".".$formats[$_FILES["media_file"]["type"]];
            $data[":file"] = $file;          

          }elseif($_POST["media_switch"]=="1"){ // Embed video

            if(empty($_POST["media_code"])){
              $error.="<p>You have selected <strong>embed</strong> but did not provide any embed  code.</p>";
            }else{
              $data[":embed"] = $_POST["media_code"];
            }        
          }elseif($_POST["media_switch"]=="2"){ // Link to video
            
            if(!Main::is_url($_POST["media_source"]) || !in_array(Main::extension($_POST["media_source"], FALSE), formats())){
              $error.="<p>The <strong>link</strong> to the media doesn't seem to be valid. Only these formats are supported: ".formats(NULL, TRUE)."</p>";
            }else{
              if($_POST["type"] == "picture" && $this->config["local_thumbs"] && in_array(Main::extension($_POST["media_source"], FALSE), array("jpg","gif","png"))){
                // Copy Image
                $file = md5($unique).".".Main::extension($_POST["media_source"], FALSE);
                copy($_POST["media_source"], MEDIA."/$file");
                $data[":file"] = $file;
              }else{
                $data[":link"] = Main::clean($_POST["media_source"],3,TRUE);
              }
            }
          }        
        // Check Thumbnail
        if($_POST["thumb_switch"]=="0"){ // Upload thumbnail
          // Check if it is possible to generate a thumb from main file
          if(in_array($_FILES["media_file"]["type"], array("image/jpeg", "image/png"))){
            $thumb = md5($unique.rand(0,999)).".".$formats[$_FILES["media_file"]["type"]];
            $data[":thumb"] = $thumb;   
          }else{            
            if($_POST["type"] == "picture" && $this->config["local_thumbs"] && !empty($_POST["media_source"]) && in_array(Main::extension($_POST["media_source"], FALSE), array("jpg","gif","png"))){
              $thumb = md5($unique.rand(0,999)).".".Main::extension($_POST["media_source"], FALSE);
              $data[":thumb"] = $thumb;
            }else{
              if(isset($_FILES["thumb"]) && empty($_FILES["thumb"])) $error.="<p>You forgot to select a thumbnail to upload.</p>";
              elseif($_FILES["thumb"]["size"] > 500*1024) $error .= "<p>The thumbnail size must not exceed {$this->config["max_size"]} MB</p>";              
              $thumb = md5($unique.rand(0,999)).".".$formats[$_FILES["thumb"]["type"]];
              $data[":thumb"] = $thumb;
            }
          }

        }elseif($_POST["thumb_switch"]=="1"){ // Link thumb
          if(empty($_POST["thumb_link"]) || !in_array(Main::extension($_POST["thumb_link"], FALSE), array("jpg", "png"))){
              $error.="<p>The <strong>link</strong> to the thumbnail doesn't seem to be valid. Only these formats are supported: jpg and png</p>";                    
          }
          $data[":ext_thumb"] = $_POST["thumb_link"];
          
        }elseif($_POST["thumb_switch"]=="3"){ // Import

          // Thumbnail
          if(!$this->config["local_thumbs"]){
            $data[":ext_thumb"] = $_POST["thumb"];
          }else{
            copy($_POST["thumb"], THUMBS."/".md5($unique).".jpg");
            $data[":thumb"] = "".md5($unique).".jpg";
          }             
          
        }

        // Return Error
        if(!is_null($error)) return Main::redirect(Main::ahref("media/add","",FALSE),array("danger",$error)); 
        // Add to database       
        if($this->db->insert("media", $data)){
          // Copy Video
          if($_POST["media_switch"] == "0" && $_POST["type"] !== "post"){
            move_uploaded_file($_FILES["media_file"]["tmp_name"], MEDIA."/".$file);
          }
          // Copy Thumb
          if($_POST["thumb_switch"] == "0"){
            if(empty($_FILES["thumb"]["tmp_name"])){
              Main::generatethumb(MEDIA."/".$file,THUMBS."/".$data[":thumb"],450);
            }else{
              move_uploaded_file($_FILES["thumb"]["tmp_name"], THUMBS."/".$data[":thumb"]);
            }            
          }          
          $this->db->update("setting","value = value + 1",array("config"=>"?"),array("count_media"));
          return Main::redirect(Main::ahref("media/{$_POST["type"]}","",FALSE),array("success", "Media has been added."));
        }
      }
      // Add CDN Editor
      Main::cdn("ckeditor","",1);
      Main::admin_add("<script>CKEDITOR.replace( 'description', {height: 350});</script>","custom",1);   

      Main::set("title","Add Media");
      $this->header();
      include($this->t("addmedia"));
      $this->footer();
    }

    /**
    * Delete Media
    * @since 1.1
    */
    private function media_delete($id = NULL){
      // Disable if demo
      if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));      
      if($this->id == "all"){
        return $this->media_delete_all();
      }
      if(is_null($id)){
        $id = $this->id;
        if(!Main::validate_nonce("delete_media-{$id}")) return Main::redirect(Main::ahref("media","",FALSE),array("danger","An unexpected error has occured. Please try again.")); 
      }
      if($media = $this->db->get("media",array("id"=>$id),array("limit"=>1))){
        //Remove Thumbnail
        if(!empty($media->thumb) && file_exists(THUMBS."/".$media->thumb)){
          unlink(THUMBS."/".$media->thumb);  
        }
        //Remove Video
        if(!empty($media->file) && file_exists(MEDIA."/".$media->file)){
          unlink(MEDIA."/".$media->file);  
        }        
        $this->db->delete("media",array("id" => $id)); // Delete media
        $this->db->delete("rating",array("mediaid" => $id)); // Delete rating
        $this->db->delete("favorite",array("mediaid" => $id)); // Delete favorites
        $this->db->delete("comment",array("mediaid" => $id)); // Delete comment
        $this->db->update("setting","value = value - 1",array("config"=>"?"),array("count_media"));
        return Main::redirect(Main::ahref("media/{$media->type}","",FALSE),array("success","The media and its associated information have been deleted."));
      }
      return Main::redirect(Main::ahref("media","",FALSE),array("danger","An unexpected error has occured. Please try again."));
    }
      /**
       * Manual Delete Media
       * @author KBRmedia
       * @since  1.0
       */
      private function media_manual_delete($id){
        if($media = $this->db->get("media",array("id"=>$id),array("limit"=>1))){
          //Remove Thumbnail
          if(!empty($media->thumb) && file_exists(THUMBS."/".$media->thumb)){
            unlink(THUMBS."/".$media->thumb);  
          }
          //Remove Video
          if(!empty($media->file) && file_exists(MEDIA."/".$media->file)){
            unlink(MEDIA."/".$media->file);  
          }        
          $this->db->delete("media",array("id" => $id)); // Delete media
          $this->db->delete("rating",array("mediaid" => $id)); // Delete rating
          $this->db->delete("favorite",array("mediaid" => $id)); // Delete favorites
          $this->db->delete("comment",array("mediaid" => $id)); // Delete comment
          $this->db->update("setting","value = value - 1",array("config"=>"?"),array("count_media"));
        }
      }
    /**
     * Delete All
     * @author KBRmedia
     * @since  1.5.1
     */
    private function media_delete_all(){
      if(empty($_POST["delete_media"]) || !is_array($_POST["delete_media"]))  return Main::redirect(Main::ahref("media","",FALSE),array("danger","An unexpected error has occured. Please try again."));
      foreach ($_POST["delete_media"] as $id) {
        if(!is_numeric($id)) continue;
        $this->media_manual_delete($id);
      }
      return Main::redirect(Main::ahref("media","",FALSE),array("success","Selected media and its associated information have been deleted."));
    }
    /**
    * Edit Media
    * @since 1.5.1
    */
    private function media_edit(){
      // Get Media
      if(!$media = $this->db->get("media",array("id"=>"?"),array("limit"=>1),array($this->id))){
        return Main::redirect("admin",array("danger","Media not found."));
      }      
      // Add media to database
      if(isset($_POST["token"])){
        // Disable if demo
        if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));
        // Validate Token
        if(!Main::validate_csrf_token($_POST["token"])){
          Main::redirect(Main::ahref("media/edit/{$this->id}","",FALSE),array("danger","Invalid token, please try again."));
          return;
        }
        $error = NULL;

        if(empty($_POST["title"]) || strlen($_POST["title"]) < 3) $error.="<p>Title must be at least 3 characters.</p>"; 
        $slug = (empty($_POST["slug"]) || Main::slug($_POST["title"]) == $_POST["slug"]) ? Main::slug($_POST["title"]) : $_POST["slug"];
        // Prepare data
        $data = array(
            ':type' => types($_POST["type"]) ? $_POST["type"] : 'video', 
            ':catid'=> is_numeric($_POST["category"]) ? $_POST["category"] : "1", 
            ':duration'=> toseconds($_POST["duration"]), 
            ":featured"=> in_array($_POST["featured"], array("0","1")) ? $_POST["featured"] : "0", 
            ":title"=> Main::clean($_POST["title"],3,FALSE), 
            ":url"=> $slug,
            ":description"=> Main::clean($_POST["description"],4), 
            ":tags"=> Main::clean($_POST["tags"],3,TRUE),
            ":nsfw" => in_array($_POST["nsfw"], array("0","1")) ? $_POST["nsfw"] : "0", 
            ":views" => Main::clean($_POST["views"], 3, TRUE),
            ":likes" => Main::clean($_POST["likes"], 3, TRUE),
            ":dislikes" => Main::clean($_POST["dislikes"], 3, TRUE),
            ":votes" => Main::clean($_POST["votes"], 3, TRUE),
            ":approved" => in_array($_POST["approved"], array("0","1")) ? $_POST["approved"] : "0", 
            ":subscribe" => in_array($_POST["subscribe"], array("0","1")) ? $_POST["subscribe"] : "0", 
            ":embed" => !empty($_POST["embed"]) ? $_POST["embed"] : "", 
            ":userid"=> $_POST["userid"],
            ":source"=> Main::clean($_POST["url"], 3, TRUE),
          );
          // Update thumbnail
          if(!empty($_FILES["thumb"]["tmp_name"])){
            $formats = formats();
            $thumb = md5($unique.rand(0,999)).".".$formats[$_FILES["thumb"]["type"]];
            $data[":thumb"] = $thumb;            
            move_uploaded_file($_FILES["thumb"]["tmp_name"], THUMBS."/".$thumb);
            unlink(THUMBS."/".$media->thumb);
          }    
          // Update featured image
          if($_POST["type"] == "post" && !empty($_FILES["file"]["tmp_name"])){
            $formats = formats();
            $file = md5($unique.rand(0,999)).".".$formats[$_FILES["file"]["type"]];
            $data[":file"] = $file;            
            move_uploaded_file($_FILES["file"]["tmp_name"], MEDIA."/".$file);
            unlink(THUMBS."/".$media->file);
          }                
        // Return Error
        if(!is_null($error)) return Main::redirect(Main::ahref("media/edit/{$this->id}","",FALSE),array("danger",$error)); 
        // Add to database       
        if($this->db->update("media","", array("id" => $this->id), $data)){          
         return Main::redirect(Main::ahref("media/edit/{$this->id}","",FALSE),array("success", "Media has been edited."));
        }
      }      
      
      $url = $media->url;
      $media = $this->formatMedia($media);
      $media->url = $url;
      $media->title = str_replace('"',"'", $media->title);
      $media->duration = totime($media->duration);
      Main::set("title","Edit Media");
      // Add CDN Editor
      Main::cdn("ckeditor","",1);
      Main::admin_add("<script>CKEDITOR.replace( 'description', {height: 350});</script>","custom",1);  

      $this->header();
      include($this->t("editmedia"));
      $this->footer();
    }
    /**
    * Moderate Media
    * @since 1.0
    */
    private function media_view(){
      // Get unapproved media    
      $videos = $this->db->get("media",array("approved"=> "1", "userid" => $this->id),array("order"=>"date","count"=>1,"limit"=>($this->page-1)*$this->limit.", ".$this->limit));
      $count = $this->db->rowCount;
      $type="User's Video";
      $sort=isset($_GET["sort"])?$_GET["sort"]:'id';
      $by=(isset($_GET["order"]) && $_GET["order"]=="asc")?"1":"0";
      // Calculate Pages
      if(($this->db->rowCount%$this->limit)<>0) {
        $max=floor($this->db->rowCount/$this->limit)+1;
      } else {
        $max=floor($this->db->rowCount/$this->limit);
      }
      $pagination=Main::pagination($max, $this->page, Main::ahref("media/moderate")."?page=%d");   

      Main::set("title","Manage User's Video");
      $this->header();
     include($this->t("media"));
      $this->footer();
    }
    /**
    * Moderate Media
    * @since 1.1
    */
    private function media_moderate(){
      // Delete All
      if($this->id == "delete"){
        if(Main::validate_nonce("delete_media-all")){
          $videos = $this->db->get("media",array("approved"=> "0"),array("order"=>"date","count"=>1,"limit"=>($this->page-1)*$this->limit.", ".$this->limit));  
          foreach ($videos as $video) {
            $this->media_manual_delete($video->id);
          }
          return Main::redirect(Main::ahref("media/moderate","",FALSE),array("success","All unapproved media were deleted."));
        }        

        return Main::redirect(Main::ahref("media/moderate","",FALSE),array("danger","An unexpected error has occured. Please try again."));;
      }
      // Get unapproved media    
      $videos = $this->db->get("media",array("approved"=> "0"),array("order"=>"date","count"=>1,"limit"=>($this->page-1)*$this->limit.", ".$this->limit));
      $count = $this->db->rowCount;

      // Calculate Pages
      if(($this->db->rowCount%$this->limit)<>0) {
        $max=floor($this->db->rowCount/$this->limit)+1;
      } else {
        $max=floor($this->db->rowCount/$this->limit);
      }
      $pagination=Main::pagination($max, $this->page, Main::ahref("media/moderate")."?page=%d");   

      Main::set("title","Moderate Media");
      $this->header();
      if(empty($videos)) {
        echo "<h3>No media to moderate.</h3> <p>There is nothing to moderate right now. You can go out for a coffee or tea and come back.</p>";
      }else{
        include($this->t("moderate"));  
      }
      $this->footer();
    }

    /**
    * Approve Media
    * @since 1.0
    */
    private function media_approve(){
      // Disable if demo
      if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));
      $media = $this->db->get("media", array("id" => $this->id), array("limit" => 1));
      $this->db->update("media",array("approved" => "1"),array("id" => "?"),array($this->id));
      // Add Points
      if($this->config["points"]){
        // Check if user has already been awarded points for this media
        $this->db->insert("point", array(":action" => "submit", ":userid" => $media->userid, ":actionid" => $media->id, ":point" => $this->config["amount_points"]["submit"]));
        $this->db->update("user", "points = points+{$this->config["amount_points"]["submit"]}", array("id" =>  $media->userid));
      }      
      return Main::redirect(Main::ahref("media/edit/{$this->id}","",FALSE),array("success","Media has been approved."));
    }

    /**
    * Import Media
    * @since 1.0
    */
    private function media_import(){
      // Get URL
      if(isset($_GET["url"])){
        $url = urldecode($_GET["url"]);
        if(empty($url)) return Main::redirect(Main::ahref("media/import","",FALSE),array("danger","Please enter a valid URL."));

        require(ROOT."/includes/Media.class.php");
                 $import = new Media(
                      array(
                          "yt_api" => $this->config["yt_api"],
                          "vm_api" => $this->config["vm_api"]
                          )
                      );
        $media = $import->import($url,"100%","500");
        if(!isset($media->src)) return Main::redirect(Main::ahref("media/import","",FALSE),array("danger", "For some reason this video cannot be imported."));
        // Return Error
        if(isset($media->error)) return Main::redirect(Main::ahref("media/import","",FALSE),array("danger",$media->error));
        Main::hook("admin_import_sidebar",array("Admin","media_import_hook"));
      }
      Main::set("title","Import Media");
      $this->header();  
      include($this->t("import"));
      $this->footer();
    }
      /**
       * Hook Help to sidebar
       * @since 1.0
       **/
      public static function media_import_hook(){
        echo '<div class="panel panel-dark sticky">
                <div class="panel-heading">Quick Tips</div>
                <div class="panel-body">
                  <ul class="cleanlist">
                    <li><span class="highlight red">Don\'t forget</span> to choose a category.</li>
                    <li><span class="highlight green">The thumbnail</span> will be automatically added.</li>
                    <li><b>Featured Videos</b> are always prioritized.</li>
                    <li><b>Make sure to edit</b> the description.</li>      
                    <li><b>Make sure to clean</b> the embed code from any unwanted elements.</li>   
                  </ul>         
                </div>
              </div>';
      }
    /**
    * Youtube Media
    * @since 1.0
    */
    private function media_youtube(){
      if(isset($_GET["q"])){
        if(empty($_GET["q"]) || strlen($_GET["q"]) < 3) return Main::redirect(Main::ahref("media/youtube","",FALSE),array("danger","Please enter a keyword or valid profile name."));
        $type=$_GET['type'];
        $h=$_GET['h'];
        $q=urlencode(Main::clean($_GET['q'],3));
        // set max results
        if (!isset($_GET['i']) || empty($_GET['i'])) {
          $i = 25;
        } else {
          $i = $_GET['i'];
        }
        if (!isset($_GET['s']) || empty($_GET['s'])) {
          $s ="";
        } else {
          $s ="&start-index=".$_GET['s'];
        }
        if (!isset($_GET['o']) || empty($_GET['o'])) {
          $o = "relevance";
        } else {
          $o = $_GET['o'];
        }
        if($h == "profile"){
          $channel = "https://www.googleapis.com/youtube/v3/channels?key={$this->config["yt_api"]}&part=snippet&forUsername=$q";
          if(!$channelID = Main::http($channel)) return Main::redirect(Main::ahref("media/youtube","",FALSE),array("danger","Profile not found. Please try again"));
          $channelID = json_decode($channelID);
          $channelID = $channelID->items[0]->id;
          $feed = "https://www.googleapis.com/youtube/v3/search?key={$this->config["yt_api"]}&part=snippet&order=$o&maxResults={$i}&channelId={$channelID}&type=video&videoEmbeddable=true";        
        }else{
          $feed = "https://www.googleapis.com/youtube/v3/search?key={$this->config["yt_api"]}&part=snippet&order=$o&maxResults={$i}&q={$q}&type=video&videoEmbeddable=true";        
        } 
        if(isset($_GET["page"])) $feed = $feed."&pageToken=".$_GET["page"];

        $headers = get_headers($feed);
        $code=substr($headers[0], 9, 3);
        if($code == "404"){
          return Main::redirect(Main::ahref("media/youtube","",FALSE),array("danger","Video or Profile not found. Please try again")); 
        }    
        $content=Main::http($feed);  
        if(!$content) return Main::redirect(Main::ahref("media/youtube","",FALSE),array("danger","Video or Profile not found. Please try again")); 
        $data=json_decode($content,TRUE);

        if(!isset($data["items"])) return Main::redirect(Main::ahref("media/youtube","",FALSE),array("danger","Video or Profile not found. Please try again"));        
        if($data["pageInfo"]["totalResults"]=="0") return Main::redirect(Main::ahref("media/youtube","",FALSE),array("danger","Video or Profile not found. Please try again"));
        
        // Get Categories
        $query = $this->db->get("category",array("type"=>$type, "parentid" => "0"));
        $categories = "";
        foreach ($query as $line) {
           $categories .= "<option value='{$line->id}'>{$line->name}</option>";
              $child = $this->db->get("category",array("parentid" => $line->id));
              foreach ($child as $ch){
                $categories .="<option value='{$ch->id}'>&nbsp;&nbsp;&nbsp;|_{$ch->name}</option>";
              }                  
        }         
      }      
      Main::set("title","Mass Import from Youtube");      
      $this->header();
      include($this->t("youtube"));
      $this->footer();
    }
    /**
     * Vimeo Media
     * @since  1.5
     */
    private function media_vimeo(){
      if(isset($_GET["q"])){
        if(empty($_GET["q"]) || strlen($_GET["q"]) < 3) return Main::redirect(Main::ahref("media/vimeo","",FALSE),array("danger","Please enter a keyword or valid profile name."));
        $type=$_GET['type'];
        $h = 1;
        $q=urlencode(Main::clean($_GET['q'],3));
        // set max results
        if (!isset($_GET['i']) || empty($_GET['i'])) {
          $i = 25;
        } else {
          $i = $_GET['i'];
        }
        if (!isset($_GET['o']) || empty($_GET['o'])) {
          $o = "relevant";
        } else {
          $o = $_GET['o'];
        }
        $feed = "https://api.vimeo.com/videos?access_token={$this->config["vm_api"]}&query={$q}&sort=$o&per_page={$i}";        
        if(isset($_GET["page"])) $feed = $feed."&page=".$_GET["page"];

        $headers = get_headers($feed);
        $code=substr($headers[0], 9, 3);
        if($code == "404"){
          return Main::redirect(Main::ahref("media/vimeo","",FALSE),array("danger","Video or Profile not found. Please try again")); 
        }    
        $content=Main::http($feed);  
        if(!$content) return Main::redirect(Main::ahref("media/vimeo","",FALSE),array("danger","Video or Profile not found. Please try again")); 
        $data=json_decode($content,TRUE);
        if(!isset($data["data"])) return Main::redirect(Main::ahref("media/vimeo","",FALSE),array("danger","Video or Profile not found. Please try again"));        
        if($data["total"]=="0") return Main::redirect(Main::ahref("media/vimeo","",FALSE),array("danger","Video or Profile not found. Please try again"));
        
        $next_p = array_reverse(explode("&", $data["paging"]["next"]));
        $pre_p = array_reverse(explode("&", $data["paging"]["previous"]));
        
        // Get Categories
        $query = $this->db->get("category",array("type"=>$type, "parentid" => "0"));
        $categories = "";
        foreach ($query as $line) {
           $categories .= "<option value='{$line->id}'>{$line->name}</option>";
              $child = $this->db->get("category",array("parentid" => $line->id));
              foreach ($child as $ch){
                $categories .="<option value='{$ch->id}'>&nbsp;&nbsp;&nbsp;|_{$ch->name}</option>";
              }                  
        }         
      }      
      Main::set("title","Mass Import from Vimeo");      
      $this->header();
      include($this->t("vimeo"));
      $this->footer();
    }
    /**
     * dailymotion Media
     * @since  1.5
     */
    private function media_dailymotion(){
      if(isset($_GET["q"])){
        if(empty($_GET["q"]) || strlen($_GET["q"]) < 3) return Main::redirect(Main::ahref("media/dailymotion","",FALSE),array("danger","Please enter a keyword or valid profile name."));
        $type=$_GET['type'];
        $q=urlencode(Main::clean($_GET['q'],3));
        // set max results
        if (!isset($_GET['i']) || empty($_GET['i'])) {
          $i = 25;
        } else {
          $i = $_GET['i'];
        }
        if (!isset($_GET['o']) || empty($_GET['o'])) {
          $o = "relevant";
        } else {
          $o = $_GET['o'];
        }
        $feed = "https://api.dailymotion.com/videos?fields=allow_embed,created_time,description,embed_url,tags,thumbnail_480_url,title,id,url&search=query={$q}&sort=$o&limit={$i}";          
        if(isset($_GET["page"])) $feed = $feed."&page=".$_GET["page"];

        $headers = get_headers($feed);
        $code=substr($headers[0], 9, 3);
        if($code == "404"){
          return Main::redirect(Main::ahref("media/dailymotion","",FALSE),array("danger","Video or Profile not found. Please try again")); 
        }    
        $content=Main::http($feed);  
        if(!$content) return Main::redirect(Main::ahref("media/dailymotion","",FALSE),array("danger","Video or Profile not found. Please try again")); 
        $data=json_decode($content,TRUE);
        if(!isset($data["list"])) return Main::redirect(Main::ahref("media/dailymotion","",FALSE),array("danger","Video or Profile not found. Please try again"));        
        if($data["total"]=="0") return Main::redirect(Main::ahref("media/dailymotion","",FALSE),array("danger","Video or Profile not found. Please try again"));
        
        if($data["has_more"]) $next_p = $this->page;
        $pre_p = $this->page - 1;
        
        // Get Categories
        $query = $this->db->get("category",array("type"=>$type, "parentid" => "0"));
        $categories = "";
        foreach ($query as $line) {
           $categories .= "<option value='{$line->id}'>{$line->name}</option>";
              $child = $this->db->get("category",array("parentid" => $line->id));
              foreach ($child as $ch){
                $categories .="<option value='{$ch->id}'>&nbsp;&nbsp;&nbsp;|_{$ch->name}</option>";
              }                  
        }         
      }      
      Main::set("title","Mass Import from Dailmotion");      
      $this->header();
      include($this->t("dailymotion"));
      $this->footer();
    }    
  /**
   * Categories Handler
   * @author KBRmedia
   * @since  1.0
   */
  protected function categories(){
    if(in_array($this->do, array("add","delete","edit"))){
      $fn = __FUNCTION__."_{$this->do}";
      return $this->$fn();
    }    
    // Get Data
    $categories = $this->db->get("category",array("parentid" => "0"),array("order"=>"name","asc" => 1,"count"=>1,"limit"=>($this->page-1)*$this->limit.", ".$this->limit));

    // Calculate Pages
    if(($this->db->rowCount%$this->limit)<>0) {
      $max=floor($this->db->rowCount/$this->limit)+1;
    } else {
      $max=floor($this->db->rowCount/$this->limit);
    }
    $pagination=Main::pagination($max, $this->page, Main::ahref("categories")."?page=%d");       
    Main::set("title","Manage Categories");      
    $this->header();
    include($this->t(__FUNCTION__));
    $this->footer();    
  }
      /**
       * Add Category
       * @author KBRmedia
       * @since  1.3.1
       */
      private function categories_add(){
        // Save Changes
        if(isset($_POST["token"])){
          // Disable if demo
          if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));
          // Validate Results
          if(!Main::validate_csrf_token($_POST["token"])){
            return Main::redirect(Main::ahref("categories","",FALSE),array("danger","Something went wrong, please try again."));
          }          
          if(empty($_POST["title"]) || strlen($_POST["title"]) < 2) return Main::redirect(Main::ahref("categories","",FALSE),array("danger",e("Please enter a valid name.")));
          $title = Main::clean($_POST["title"], 3);
          $slug = (!empty($_POST["slug"])) ? Main::slug($_POST["slug"]) :  Main::slug($_POST["title"]);
          $type = types($_POST["type"]) ? $_POST["type"] : 'video';

          if($this->db->get("category", "slug = ? AND type = ?",array("limit" => 1),array($slug, $type))){
            return Main::redirect(Main::ahref("categories","",FALSE),array("danger",e("This category already exists.")));
          }
          // Prepare Data
          $data = array(
              ":type" => types($_POST["type"]) ? $_POST["type"] : 'video',
              ":name" => Main::clean($_POST["title"], 3),
              ":description" => Main::clean($_POST["description"], 3),
              ":slug" => (!empty($_POST["slug"])) ? Main::slug($_POST["slug"]) :  Main::slug($_POST["title"]),
              ":parentid" => Main::clean($_POST["parent"], 3),
            );
          if($this->db->insert("category", $data)){
            return Main::redirect(Main::ahref("categories","",FALSE),array("success","Category has been added."));
          }
        }
        return Main::redirect(Main::ahref("categories","",FALSE),array("danger","Something went wrong, please try again."));
      }
      /**
       * Edit Category
       * @author KBRmedia
       * @since  1.0
       */
      private function categories_edit(){
        // Save Changes
        if(isset($_POST["token"])){
          // Disable if demo
          if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));
          // Validate Results
          if(!Main::validate_csrf_token($_POST["token"])){
            return Main::redirect(Main::ahref("categories","",FALSE),array("danger","Something went wrong, please try again."));
          }          
          if(empty($_POST["title"]) || strlen($_POST["title"]) < 2) return Main::redirect(Main::ahref("categories","",FALSE),array("danger",e("Please enter a valid name.")));
          // Prepare Data
          $data = array(
              ":type" => types($_POST["type"]) ? $_POST["type"] : 'video',
              ":name" => Main::clean($_POST["title"], 3),
              ":description" => Main::clean($_POST["description"], 3),
              ":slug" => (!empty($_POST["slug"])) ? Main::slug($_POST["slug"]) :  Main::slug($_POST["title"]),
              ":parentid" => Main::clean($_POST["parent"], 3)
            );
          if($this->db->update("category","", array("id" => $this->id), $data)){
            return Main::redirect(Main::ahref("categories/edit/{$this->id}","",FALSE),array("success","Category has been edited."));
          }
        }       
        if(!$category = $this->db->get("category", array("id" => "?"), array("limit" => "1"), array($this->id))){
          return Main::redirect(Main::ahref("categories","",FALSE),array("danger","Something went wrong, please try again."));
        }
        $header = "Edit {$category->name} ".types($category->type)." Category";
        if($this->id == "1"){
          $beforehead = "<p class='alert alert-info'>Please note that this category cannot be removed because it is the default category for all media.</p>";
        }
        $content = "
                <form class='form' action='".Main::ahref("categories/edit/{$category->id}")."' method='post'>
                  <div class='form-group'>
                    <label for='title'>Name</label>
                    <p class='help-block'>Don't append the media type after the name as this will be done automatically. For example <strong>Funny videos</strong> is not good.</p>
                    <input class='form-control' type='text' name='title' id='title' value='{$category->name}' placeholder='The name of the category.'  required>
                  </div>
                              
                  <div class='form-group'>
                    <label for='slug'>Slug (optional)</label>
                    <p class='help-block'>A slug is a short alias used to identify this category. Excellent for SEO. Leave it empty to generate one using the name.</p>
                    <input class='form-control' type='text' name='slug' id='slug' value='{$category->slug}' placeholder='The slug of the category. e.g. funny-and-cute'>           
                  </div>
                  
                  <div class='form-group'>
                    <label for='type'>Type</label>    
                    <p class='help-block'>Choose the category type to group this in.</p>  
                    ".types($category->type, TRUE)."             
                  </div>
                  <div class='form-group'>
                    <label for='parent'>Parent Category</label>   
                    <p class='help-block'>You can make this a sub-category by selecting a parent category.</p>"; 
                    $cat = $this->db->get('category', array('parentid' => '0'), array('order' => 'type'));
        $content .="<select name='parent' id='parent'>
                      <option value='0'>None</option>";
                      foreach ($cat as $c){
                        $content .="<option value='".$c->id."' ".($c->id == $category->parentid ? "selected" : "").">".ucfirst($c->type)." / ".$c->name."</option>";
                      }
        $content .="</select>
                  </div>  
                  <div class='form-group'>
                    <label for='description'>Description (optional)</label>
                    <textarea name='description' id='description' class='form-control' cols='30' rows='5' placeholder='This will override the auto meta description.'>{$category->description}</textarea>           
                  </div>

                  ".Main::csrf_token(TRUE)."  
                  <input type='submit' class='btn btn-primary' value='Edit Category'>
                </form>    
        ";
        Main::set("title", $header);
        $this->header();
        include($this->t("template"));
        $this->footer();           
      }
      /**
       * Delete user(s)
       * @since 1.0
       **/
      private function categories_delete(){
        // Disable if demo
        if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));        
     
        // Delete single URL
        if(!empty($this->id) && is_numeric($this->id)){
          if($this->id == "1"){
            return Main::redirect(Main::ahref("categories","",FALSE),array("danger","You cannot delete the default category."));   
          }
          // Validated Single Nonce
          if(Main::validate_nonce("delete-category-{$this->id}")){
            $this->db->delete("category",array("id"=>"?"),array($this->id));
            $this->db->update("media", array("catid" => "1"), array("catid" => $this->id));
            return Main::redirect(Main::ahref("categories","",FALSE),array("success","Category has been deleted."));
          }        
        } 
        return Main::redirect(Main::ahref("categories","",FALSE),array("danger","An unexpected error occurred."));          
      }      
  /**
   * Users
   * @since 1.0
   **/
  protected function users($limit=""){
    if(in_array($this->do, array("add","delete","edit","inactive","export"))){
      $fn = __FUNCTION__."_{$this->do}";
      return $this->$fn();
    }
    if(!empty($limit)) $this->limit=$limit;
    // Filters
    $where="";
    $filter="id";
    $order="";
    $asc=FALSE;    
    if(isset($_GET["filter"]) && in_array($_GET["filter"], array("old","admin"))){
        if($_GET["filter"]=="admin"){
          $filter="id";
          $order="admin";
          $where=array("admin"=>1);
        }elseif($_GET["filter"]=="old"){
          $filter="date";
          $order="old";
          $asc=TRUE;
        }
    }
    // Get urls from Database
    $users=$this->db->get("user",$where,array("count"=>TRUE,"order"=>$filter,"limit"=>(($this->page-1)*$this->limit).", {$this->limit}","asc"=>$asc));
    if($this->page > $this->db->rowCount) Main::redirect("admin/",array("danger","No Users found."));

    if(($this->db->rowCount%$this->limit)<>0) {
      $max=floor($this->db->rowCount/$this->limit)+1;
    } else {
      $max=floor($this->db->rowCount/$this->limit);
    }     
    $count="({$this->db->rowCount})";
    $pagination=Main::pagination($max, $this->page, Main::ahref("users")."?page=%d&filter=$order");    
    Main::set("title","Manage Users");
    Main::set("title","Admin cPanel");
    $this->header();
    include($this->t(__FUNCTION__));
    $this->footer();    
  }
      /**
       * Add user
       * @since 1.0
       **/
      private function users_add(){
        // Add User
        if(isset($_POST["token"])){
          // Disable if demo
          if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));

          // Validate Results
          if(!Main::validate_csrf_token($_POST["token"])){
            return Main::redirect(Main::ahref("users/add","",FALSE),array("danger","Something went wrong, please try again."));
          }
          if(!empty($_POST["username"])){
            if(!Main::username($_POST["username"])) return Main::redirect(Main::ahref("users/add","",FALSE),array("danger","Please enter a valid username."));
            if($this->db->get("user",array("username"=>"?"),array("limit"=>1),array($_POST["username"]))){
              Main::redirect(Main::ahref("users/add","",FALSE),array("danger","This username has already been used."));
              return;
            }
          }          
          // Get User info
          if(empty($_POST["email"]) || !Main::email($_POST["email"])){
            return Main::redirect(Main::ahref("users/add","",FALSE),array("danger","Please enter a valid email!"));
          }   
          if($this->db->get("user",array("email"=>"?"),array("limit"=>1),array($_POST["email"]))){
            Main::redirect(Main::ahref("users/add","",FALSE),array("danger","This email has already been used!"));
            return;
          }
          if(strlen($_POST["password"]) < 5) return Main::redirect(Main::ahref("user/ass","",FALSE),array("danger","Password has to be at least 5  characters."));          
          //if(empty($_POST["dob"]) || !strtotime($_POST["dob"]) || Main::timeago($_POST["dob"], TRUE) < 10) return Main::redirect(Main::ahref("users/add","",FALSE),array("danger","Aren't you too young to browse the internet?"));
          // Prepare Data
          $data = array(
            ":email" => Main::clean($_POST["email"],3),
            ":name" => Main::clean($_POST["name"],3),
            ":username" => Main::clean($_POST["username"],3),
            ":verifno" => Main::strrand(16),
            ":admin" => in_array($_POST["admin"],array("0","1")) ? Main::clean($_POST["admin"],3):"0",
            ":country" => Main::clean($_POST["country"],3),
            ":public" => in_array($_POST["public"],array("0","1")) ? Main::clean($_POST["public"],3):"0",
            ":digest" => in_array($_POST["digest"],array("0","1")) ? Main::clean($_POST["digest"],3):"0",
            ":nsfw" => in_array($_POST["nsfw"],array("0","1")) ? Main::clean($_POST["nsfw"],3):"0",
            ":dob" => date("Y-m-d",strtotime($_POST["dob"])),
            ":password" => Main::encode($_POST["password"]),
            ":auth_key" => Main::encode(Main::strrand(10)),
            ":date" => "NOW()",
            ":active" => "1",
            );        

          $this->db->insert("user",$data);
          return Main::redirect(Main::ahref("users","",FALSE),array("success","User has been added."));
        }
             
        $header="Add a User";
        $content="       
        <p class='alert alert-info'>The user's avatar and cover photo can only be update via the profile page.</p>
        <form action='".Main::ahref("users/add")."' method='post' class='form-horizontal' role='form'>
          <div class='form-group'>
            <label for='name' class='col-sm-3 control-label'>Full Name</label>
            <div class='col-sm-9'>
              <input type='text' class='form-control' name='name' id='name' value=''>
              <p class='help-block'>Please enter the full name of the user.</p>
            </div>
          </div> 
          <div class='form-group'>
            <label for='username' class='col-sm-3 control-label'>Username</label>
            <div class='col-sm-9'>
              <input type='text' class='form-control' name='username' id='username' value=''>
              <p class='help-block'>A username is required for the public profile to be visible.</p>
            </div>
          </div>  
          <div class='form-group'>
            <label for='email' class='col-sm-3 control-label'>Email</label>
            <div class='col-sm-9'>
              <input type='email' class='form-control' name='email' id='email' value=''>
              <p class='help-block'>Please make sure that email is valid.</p>
            </div>
          </div>  
          <div class='form-group'>
            <label for='password' class='col-sm-3 control-label'>Password</label>
            <div class='col-sm-9'>
              <input type='password' class='form-control' name='password' id='password' value=''>
              <p class='help-block'>Password needs to be at least 5 characters.</p>
            </div>
          </div> 
          <div class='form-group'>
            <label for='dob' class='col-sm-3 control-label'>Date of Birth</label>
            <div class='col-sm-9'>
              <input type='date' class='form-control' name='dob' id='dob'>
              <p class='help-block'>Please make sure that date of birth is in the following format: YYYY-MM-DD</p>              
            </div>            
          </div>            
          <div class='form-group'>
            <label for='country' class='col-sm-3 control-label'>Country</label>
            <div class='col-sm-9'>
              <select name='country' id='country'>
                ".Main::countries()."
              </select>
            </div>
          </div>                                
          <hr />
          <ul class='form_opt' data-id='admin'>
            <li class='text-label'>User Status<small>Do you want this user to be admin or just a regular user?</small></li>
            <li><a href='' class='last current' data-value='0'>User</a></li>
            <li><a href='' class='first' data-value='1'>Admin</a></li>
          </ul>
          <input type='hidden' name='admin' id='admin' value='0' />

          <ul class='form_opt' data-id='digest'>
            <li class='text-label'>Digest <small>Digests are automatic newsletters which contains a summary of new videos.</small></li>
            <li><a href='' class='last current' data-value='0'>Disable</a></li>
            <li><a href='' class='first' data-value='1'>Enable</a></li>
          </ul>
          <input type='hidden' name='digest' id='digest' value='0' />
          <ul class='form_opt' data-id='nsfw'>
            <li class='text-label'>NSFW Media<small>If enabled, not safe for work media will be shown for this user.</small></li>
            <li><a href='' class='last current' data-value='0'>Disable</a></li>
            <li><a href='' class='first' data-value='1'>Enable</a></li>
          </ul>
          <input type='hidden' name='nsfw' id='nsfw' value='0' />

          <ul class='form_opt' data-id='public'>
            <li class='text-label'>Profile Access <small>Private profiles are not accessible and will throw a 404 error.</small></li>
            <li><a href='' class='last current' data-value='0'>Private</a></li>
            <li><a href='' class='first' data-value='1'>Public</a></li>
          </ul>
          <input type='hidden' name='public' id='public' value='0' />   

          ".Main::csrf_token(TRUE)."
          <input type='submit' value='Add User' class='btn btn-primary' />";

        $content.="</form>";
        Main::set("title","Add a User");
        $this->header();
        include($this->t("template"));
        $this->footer();       
      }  
      /**
       * Edit user
       * @since 1.0
       **/
      private function users_edit(){
        // Save Changes
        if(isset($_POST["token"])){
          // Disable if demo
          if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));
          // Validate Results
          if(!Main::validate_csrf_token($_POST["token"])){
            return Main::redirect(Main::ahref("users/edit/{$this->id}","",FALSE),array("danger","Something went wrong, please try again."));
          }
          // Get User info
          $user=$this->db->get("user",array("id"=>"?"),array("limit"=>1),array($this->id));
          if($user->auth!="twitter" && (empty($_POST["email"]) || !Main::email($_POST["email"]))){
            Main::redirect(Main::ahref("users/edit/{$this->id}","",FALSE),array("danger","Please enter a valid email."));
            return;
          }
          if($_POST["email"]!==$user->email){
            if($this->db->get("user",array("email"=>"?"),array("limit"=>1),array($_POST["email"]))){
              Main::redirect(Main::ahref("users/edit/{$this->id}","",FALSE),array("danger","This email has already been used. Please try again."));
              return;
            }
          }   
          if(!empty($_POST["username"]) && $_POST["username"]!==$user->username){
            if($this->db->get("user",array("username"=>"?"),array("limit"=>1),array($_POST["username"]))){
              Main::redirect(Main::ahref("users/edit/{$this->id}","",FALSE),array("danger","This username has already been used. Please try again."));
              return;
            }
          }
         // if(empty($_POST["dob"]) || !strtotime($_POST["dob"]) || Main::timeago($_POST["dob"], TRUE) < 10) return Main::redirect(Main::ahref("users/edit/{$this->id}","",FALSE),array("danger","Aren't you too young to browse the internet?"));
          // Prepare Data
          $data = array(
            ":email" => Main::clean($_POST["email"],3),
            ":name" => Main::clean($_POST["name"],3),
            ":username" => Main::clean($_POST["username"],3),
            ":verifno" => Main::strrand(16),
            ":admin" => in_array($_POST["admin"],array("0","1")) ? Main::clean($_POST["admin"],3):"0",
            ":active" => in_array($_POST["active"],array("0","1")) ? Main::clean($_POST["active"],3):"1",
            ":country" => Main::clean($_POST["country"],3),
            ":public" => in_array($_POST["public"],array("0","1")) ? Main::clean($_POST["public"],3):"0",
            ":digest" => in_array($_POST["digest"],array("0","1")) ? Main::clean($_POST["digest"],3):"0",
            ":nsfw" => in_array($_POST["nsfw"],array("0","1")) ? Main::clean($_POST["nsfw"],3):"0",
            ":dob" => date("Y-m-d",strtotime($_POST["dob"])),
            );       
          if(is_numeric($_POST["points"])){
            $data[":points"] = $_POST["points"];
          }

          if(!empty($_POST["password"])){
            if(strlen($_POST["password"]) < 5) return Main::redirect(Main::ahref("users/edit/{$this->id}","",FALSE),array("danger","Password has to be at least 5 characters."));
            $data[":password"]=Main::encode($_POST["password"]);
          }
          $this->db->update("user","",array("id"=>$this->id),$data);
          return Main::redirect(Main::ahref("users/edit/{$this->id}","",FALSE),array("success","User has been updated."));
        }

        // Get URL Info
        if(!$user=$this->db->get("user",array("id"=>"?"),array("limit"=>1),array($this->id))){
          Main::redirect(Main::ahref("users","",TRUE),array("danger","This user doesn't exist."));
        }                 
        $header="Edit User";
        $user->subscribers = number_format($user->subscribers);
        $user->media = $this->db->count("media","userid='{$user->id}'");
        $user->comments = $this->db->count("comment","userid='{$user->id}'");
        $beforehead = "
            <div class='row'>
              <div class='col-sm-4'>
                 <div class='panel panel-default panel-body panel-red'>
                    <p class='main-stats'><span>{$user->subscribers}</span> Subscribers</p>
                 </div>
               </div>
               <div class='col-sm-4'>
                 <div class='panel panel-default panel-body panel-dark'>
                   <p class='main-stats'><span>{$user->media}</span> Media</p>
                 </div>
               </div>
               <div class='col-sm-4'>
                 <div class='panel panel-default panel-body panel-green'>
                   <p class='main-stats'><span>{$user->comments}</span>Comments</p>
                 </div>
               </div> 
            </div>
        ";
        $content="       
        ".($user->id==$this->user->id?"<p class='alert alert-info'><strong>This is your account!</strong> Be careful when editing the password or the admin status to prevent locking yourself out.</p>":"")."
        ".(($user->auth !== "system")?"<p class='alert alert-info'>This user has used ".ucfirst($user->auth)." to login.</p>":"")."         
        <form action='".Main::ahref("users/edit/{$user->id}")."' method='post' class='form-horizontal' role='form'>
          <div class='form-group'>
            <label for='name' class='col-sm-3 control-label'>Full Name</label>
            <div class='col-sm-9'>
              <input type='text' class='form-control' name='name' id='name' value='{$user->name}'>
              <p class='help-block'>Please enter the full name of the user. This is optional.</p>
            </div>
          </div>                             
          <div class='form-group'>
            <label for='username' class='col-sm-3 control-label'>Username</label>
            <div class='col-sm-9'>
              <input type='text' class='form-control' name='username' id='username' value='{$user->username}'>
              <p class='help-block'>A username is required for the public profile to be visible.</p>
            </div>
          </div>  
          <div class='form-group'>
            <label for='email' class='col-sm-3 control-label'>Email</label>
            <div class='col-sm-9'>
              <input type='email' class='form-control' name='email' id='email' value='{$user->email}'>
            </div>
          </div>  
          <div class='form-group'>
            <label for='password' class='col-sm-3 control-label'>Password</label>
            <div class='col-sm-9'>
              <input type='password' class='form-control' name='password' id='password' value=''>
              <p class='help-block'>Leave this field empty to keep the current password otherwise password needs to be at least 5 characters.</p>
            </div>
          </div>
          <div class='form-group'>
            <label for='dob' class='col-sm-3 control-label'>Date of Birth</label>
            <div class='col-sm-9'>
              <input type='date' class='form-control' name='dob' id='dob' value='{$user->dob}'>
              <p class='help-block'>This user is currently <strong>".Main::timeago($user->dob, "old")."</strong></p>
            </div>
          </div>  
          <div class='form-group'>
            <label for='points' class='col-sm-3 control-label'>Points</label>
            <div class='col-sm-9'>
              <input type='text' class='form-control' name='points' id='points' value='{$user->points}'>              
            </div>
          </div>                    
          <div class='form-group'>
            <label for='country' class='col-sm-3 control-label'>Country</label>
            <div class='col-sm-9'>
              <select name='country' id='country'>
                ".Main::countries($user->country)."
              </select>
            </div>
          </div>                   
          <hr />
          <ul class='form_opt' data-id='admin'>
            <li class='text-label'>User Status<small>Do you want this user to be admin or just a regular user?</small></li>
            <li><a href='' class='last".(!$user->admin?' current':'')."' data-value='0'>User</a></li>
            <li><a href='' class='first".($user->admin?' current':'')."' data-value='1'>Admin</a></li>
          </ul>
          <input type='hidden' name='admin' id='admin' value='".$user->admin."' />

          <ul class='form_opt' data-id='active'>
            <li class='text-label'>User Activity <small>Inactive users cannot login anymore but their URLs will still work.</small></li>
            <li><a href='' class='last".(!$user->active?' current':'')."' data-value='0'>Inactive</a></li>
            <li><a href='' class='first".($user->active?' current':'')."' data-value='1'>Active</a></li>
          </ul>
          <input type='hidden' name='active' id='active' value='".$user->active."' />

          <ul class='form_opt' data-id='digest'>
            <li class='text-label'>Digest <small>Digests are automatic newsletters which contains a summary of new videos.</small></li>
            <li><a href='' class='last".(!$user->digest?' current':'')."' data-value='0'>Disable</a></li>
            <li><a href='' class='first".($user->digest?' current':'')."' data-value='1'>Enable</a></li>
          </ul>
          <input type='hidden' name='digest' id='digest' value='".$user->digest."' />

          <ul class='form_opt' data-id='nsfw'>
            <li class='text-label'>NSFW Media<small>If enabled, not safe for work media will be shown for this user.</small></li>
            <li><a href='' class='last".(!$user->nsfw?' current':'')."' data-value='0'>Disable</a></li>
            <li><a href='' class='first".($user->nsfw?' current':'')."' data-value='1'>Enable</a></li>
          </ul>
          <input type='hidden' name='nsfw' id='nsfw' value='".$user->nsfw."' />

          <ul class='form_opt' data-id='public'>
            <li class='text-label'>Profile Access <small>Private profiles are not accessible and will throw a 404 error.</small></li>
            <li><a href='' class='last".(!$user->public?' current':'')."' data-value='0'>Private</a></li>
            <li><a href='' class='first".($user->public?' current':'')."' data-value='1'>Public</a></li>
          </ul>
          <input type='hidden' name='public' id='public' value='".$user->public."' />   

          ".Main::csrf_token(TRUE)."
          <input type='submit' value='Update User' class='btn btn-primary' />
          <a href='{$this->url}/users/delete/{$user->id}' class='btn btn-danger delete'>Delete</a>";

        $content.="</form>";
        Main::set("title","Edit User");
        $this->header();
        include($this->t("template"));
        $this->footer();        
      }
      /**
       * Delete user(s)
       * @since 1.2.1
       **/
      private function users_delete(){
        // Disable if demo
        if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));        
        // Mass Delete Users without deleting URLs
        if(isset($_POST["token"]) && isset($_POST["delete-id"]) && is_array($_POST["delete-id"])){
          // Validate Token
          if(!Main::validate_csrf_token($_POST["token"])){
            return Main::redirect(Main::ahref("users","",FALSE),array("danger","Invalid token. Please try again."));
          }             
          $query1="(";
          $query2="(";
          $query3="(";
          $c=count($_POST["delete-id"]);
          $p="";
          $i=1;
          foreach ($_POST["delete-id"] as $id) {
            $this->db->update("media",array("userid"=> "1"),array("userid"=>"?"),array($id));
            if($i>=$c){
              $query1.="`id` = :id$i";
              $query2.="`userid` = :id$i";
              $query3.="`authorid` = :id$i";
            }else{
              $query1.="`id` = :id$i OR ";
              $query2.="`userid` = :id$i OR ";
              $query3.="`authorid` = :id$i OR ";
            }                   
            $p[":id$i"]=$id;
            $i++;
          }  
          $query1.=")";
          $query2.=")";
          $query3.=")";
          if($query1!=="()") {
            $this->db->delete("user",$query1,$p);
            $this->db->delete("comment",$query2,$p);
            $this->db->delete("favorite",$query2,$p);
            $this->db->delete("rating",$query2,$p);
            $this->db->delete("subscription",$query3,$p);
          }
          return Main::redirect(Main::ahref("users","",FALSE),array("success","Selected users have been deleted but their URLs were not deleted."));
        }        
        // Delete single URL
        if(!empty($this->id) && is_numeric($this->id)){
          // Validated Single Nonce
          if(Main::validate_nonce("delete_user-{$this->id}")){
            $this->db->delete("user",array("id"=>"?"),array($this->id));
            $this->db->delete("comment",array("userid"=>"?"),array($this->id));
            $this->db->delete("favorite",array("userid"=>"?"),array($this->id));
            $this->db->delete("rating",array("userid"=>"?"),array($this->id));                                  
            return Main::redirect(Main::ahref("users","",FALSE),array("success","User has been deleted."));
          }
          // Validated Single Nonce
          if(Main::validate_nonce("delete_user_all-{$this->id}")){
            $media = $this->db->get("media",array("userid"=>"?"),array("limit"=>1),array($this->id));
            $this->db->update("setting","value = value - ".$this->db->rowCountAll,array("config"=>"?"),array("count_media"));
            $this->db->delete("comment",array("userid"=>"?"),array($this->id));
            $this->db->delete("favorite",array("userid"=>"?"),array($this->id));
            $this->db->delete("rating",array("userid"=>"?"),array($this->id));
            $this->db->delete("media",array("userid"=>"?"),array($this->id));
            $this->db->delete("user",array("id"=>"?"),array($this->id));
            return Main::redirect(Main::ahref("users","",FALSE),array("success","This user and everything associated have been successfully deleted."));
          }          
        } 
        return Main::redirect(Main::ahref("users","",FALSE),array("danger","An unexpected error occurred."));          
      } 
      /**
        * Delete Inactive Users
        * @since 1.0
        */    
      private function users_inactive(){
        // Disable if demo
        if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));  
        if(Main::validate_nonce("inactive_users")){
          $this->db->delete('user',array("active"=>'0',"admin"=>'0'));
          Main::redirect(Main::ahref("users","",FALSE),array("success","Inactive users have been removed from the database."));
          return;
        }else{
          Main::redirect(Main::ahref("users","",FALSE),array("danger","An error has occurred."));
          return;     
        }   
      }
      /**
       * Export User
       * @since 1.0
       */   
      protected function users_export(){
        // Disable if demo
        if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo.")); 
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=URL_Shortener_UserList.csv');
        $result = $this->db->get("user","",array("order"=>"id","all"=>1));
        echo "Username (empty=none), Email, Registration Date, Auth Method\n";
        foreach ($result as $line) {
          echo "{$line->username},{$line->email},{$line->date},{$line->auth}\n";
        }
        return;
      } 
  /**
   * Comment Section
   * @author KBRmedia
   * @since  1.0
   */
  protected function comments(){
    // Toggle
    if(in_array($this->do, array("delete", "edit"))){
      $fn = "comments_{$this->do}";
      return $this->$fn();
    }        
    $comments = $this->db->get(array("custom" => "{$this->config["prefix"]}comment.*, {$this->config["prefix"]}user.username as author, {$this->config["prefix"]}user.avatar, {$this->config["prefix"]}user.email as email, {$this->config["prefix"]}media.title as title, {$this->config["prefix"]}media.url as url FROM `{$this->config["prefix"]}comment` INNER JOIN `{$this->config["prefix"]}user` ON {$this->config["prefix"]}user.id = {$this->config["prefix"]}comment.userid INNER JOIN `{$this->config["prefix"]}media` ON {$this->config["prefix"]}media.id = {$this->config["prefix"]}comment.mediaid"),"",array("order"=>"id", "count" => TRUE, "limit" => ($this->page-1)*$this->limit.", {$this->limit}"));
    $count = $this->db->rowCount;    
    if(($this->db->rowCount%$this->limit)<>0) {
      $max=floor($this->db->rowCount/$this->limit)+1;
    } else {
      $max=floor($this->db->rowCount/$this->limit);
    }  
    $pagination = Main::pagination($max, $this->page, Main::ahref("comments?page=%d"));
    Main::set("title","Manage Comments");
    $this->header();
    include($this->t("comment"));
    $this->footer();    
  }  
      /**
       * Edit Comments
       * @author KBRmedia
       * @since  1.0
       */
      private function comments_edit(){
        // Process Data
        if(isset($_POST["token"])){
          // Disable if demo
          if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));
          // Validate Results
          if(!Main::validate_csrf_token($_POST["token"])){
            return Main::redirect(Main::ahref("comments/edit/{$this->id}","",FALSE),array("danger","Something went wrong, please try again."));
          }
          $this->db->update("comment", array("body" => "?"), array("id" => "?"), array(Main::clean($_POST["comment"],2), $this->id));
          return Main::redirect(Main::ahref("comments/edit/{$this->id}","",FALSE),array("success","Comment has been edited.")); 
        }        
        // Get data
        if(!$comment = $this->db->get(array("custom" => "{$this->config["prefix"]}comment.*, {$this->config["prefix"]}user.username as author, {$this->config["prefix"]}user.avatar, {$this->config["prefix"]}user.email as email, {$this->config["prefix"]}media.title as title, {$this->config["prefix"]}media.url as url FROM `{$this->config["prefix"]}comment` INNER JOIN `{$this->config["prefix"]}user` ON {$this->config["prefix"]}user.id = {$this->config["prefix"]}comment.userid INNER JOIN `{$this->config["prefix"]}media` ON {$this->config["prefix"]}media.id = {$this->config["prefix"]}comment.mediaid"),"{$this->config["prefix"]}comment.id = ?",array('limit' => 1),array($this->id))){
          return Main::redirect(Main::ahref("comments","",FALSE),array("danger","Comment does not exist."));  
        }
        $content ="<form action='".Main::ahref("comments/edit/{$this->id}")."' method='post' role='form'>
                      <p>
                        <img src='{$this->avatar($comment)}' width ='32' alt=''> &nbsp; &nbsp; <a href='".Main::href("user/{$comment->author}")."' target='_blank'>".ucfirst($comment->author)."</a> on
                        <a href='".Main::href("view/{$comment->url}#comments-{$comment->id}")."' target='_blank'>{$comment->title}</a> - ".Main::timeago($comment->date)."
                      </p>
                      <hr>
                      <div class='form-group'>
                        <label for='title' class='control-label'>Comment</label>
                        <textarea name='comment' class='form-control' rows='5'>{$comment->body}</textarea>
                      </div>  
                      ".Main::csrf_token(TRUE)."
                      <input type='submit' value='Edit Comment' class='btn btn-primary' />
                      <a href='".Main::ahref("comments/delete/{$comment->id}").Main::nonce("delete-comment-{$comment->id}")."' class='btn btn-danger delete'>Delete Comment</a>
                  </form>";

        if($comment->parentid != 0){
          $header = "Edit reply to a comment";
        }else{
          $header = "Edit Comment";
        }    
        Main::set("title", $header);
        $this->header();
        include($this->t("template"));
        $this->footer();           
      }
      /**
       * Delete Comments
       * @author KBRmedia
       * @since  1.0
       */
      private function comments_delete(){
        // Disable if demo
        if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));            
        // Delete single URL
        if(!empty($this->id) && is_numeric($this->id)){
          // Validated Single Nonce
          if(Main::validate_nonce("delete-comment-{$this->id}") && $comment = $this->db->get("comment",array("id" => "?"),array('limit' => 1),array($this->id))){            
            // Delete Comment
            $this->db->delete("comment",array("id"=>"?"),array($comment->id));
            // Delete Child Comments
            $count = $this->db->count("comment","parentid = {$comment->id}");
            $this->db->delete("comment",array("parentid"=>"?"),array($comment->id));
            // Update Media Comment count
            if($count){
              $this->db->update("media", "comments = comments - ".($count+1),array("id" => $comment->mediaid));
            }else{
              $this->db->update("media", "comments = comments - 1",array("id" => $comment->mediaid));
            }
            return Main::redirect(Main::ahref("comments","",FALSE),array("success","Comments and all replies to this comment have been deleted."));
          }        
        } 
        return Main::redirect(Main::ahref("comments","",FALSE),array("danger","An unexpected error occurred."));  
      }
  /**
   * Reports
   * @author KBRmedia
   * @since  1.0
   */
  protected function reports(){
    // Toggle
    if(in_array($this->do, array("delete"))){
      $fn = "reports_{$this->do}";
      return $this->$fn();
    }      
    $reports = $this->db->get("temp","type ='user_report' OR type = 'media_report' OR type = 'comment_report'",array("order"=>"id", "count" => TRUE, "limit" => ($this->page-1)*$this->limit.", {$this->limit}"));
    $count = $this->db->rowCount;    
    if(($this->db->rowCount%$this->limit)<>0) {
      $max=floor($this->db->rowCount/$this->limit)+1;
    } else {
      $max=floor($this->db->rowCount/$this->limit);
    }  
    $pagination = Main::pagination($max, $this->page, Main::ahref("reports?page=%d"));
    Main::set("title","Manage Reports");
    $this->header();
    include($this->t("reports"));
    $this->footer();    
  }   
      /**
       * Delete a Report
       * @author KBRmedia
       * @since  1.0
       */
      private function reports_delete(){
        // Disable if demo
        if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));            
        // Delete single URL
        if(!empty($this->id) && is_numeric($this->id)){
          $this->db->delete("temp", array("id"=>"?"), array($this->id));
        }
        return Main::redirect(Main::ahref("reports","",FALSE),array("success","Report has been deleted."));
      }
  /**
   * Pages
   * @since 1.0
   **/
  protected function pages(){
    // Toggle
    if(in_array($this->do, array("edit","delete","add"))){
      $fn = "pages_{$this->do}";
      return $this->$fn();
    }       
    $pages=$this->db->get("page","",array("order"=>"id"));
    $count=$this->db->rowCountAll;
    Main::set("title","Manage Pages");
    $this->header();
    include($this->t("page"));
    $this->footer();
  }
      /**
       * Add page
       * @since 1.0
       **/
      private function pages_add(){
        // Process Data
        if(isset($_POST["token"])){
          // Disable if demo
          if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));
          // Validate Results
          if(!Main::validate_csrf_token($_POST["token"])){
            return Main::redirect(Main::ahref("pages/add","",FALSE),array("danger","Something went wrong, please try again."));
          }

          if(!empty($_POST["name"]) && !empty($_POST["content"])){
            if($this->db->get("page",array("slug"=>Main::slug((!empty($_POST["slug"])?$_POST["slug"]:$_POST["name"]))))){
              Main::redirect(Main::ahref("pages/add","",FALSE),array("danger","This slug is already taken, please use another one."));
            }
            // Prepare Data
            $data = array(
              ":name" => Main::clean($_POST["name"],3),
              ":slug" => empty($_POST["slug"]) ? Main::slug($_POST["name"]) : Main::slug($_POST["slug"]),
              ":content" => $_POST["content"],
              ":menu" => in_array($_POST["menu"],array("0","1")) ? Main::clean($_POST["menu"],3):"0",
              ":publish" => in_array($_POST["publish"],array("0","1")) ? Main::clean($_POST["publish"],3):"0",
              ":meta_title" => Main::clean($_POST["meta_title"],3,TRUE),
              ":meta_description" => Main::clean($_POST["meta_description"],3,TRUE)
              );         

            $this->db->insert("page",$data);
            return Main::redirect(Main::ahref("pages","",FALSE),array("success","Page has been added."));        
          }
          Main::redirect(Main::ahref("pages/add","",FALSE),array("danger","Please make sure that you fill everything correctly."));            
        }

        // Add CDN Editor
        Main::cdn("ckeditor","",1);
        Main::admin_add("<script>CKEDITOR.replace( 'editor', {height: 350});</script>","custom",1);        
        $header="Add a Custom Page";
        $content="       
        <form action='".Main::ahref("pages/add")."' method='post' class='form-horizontal' role='form'>
          <div class='form-group'>
            <label for='title' class='col-sm-3 control-label'>Name</label>
            <div class='col-sm-9'>
              <input type='text' class='form-control' name='name' id='title' value=''>
            </div>
          </div>  
          <div class='form-group'>
            <label for='slug' class='col-sm-3 control-label'>Slug</label>
            <div class='col-sm-9'>
              <input type='text' class='form-control' name='slug' id='slug' value=''>
              <p class='help-block'>E.g. {$this->config["url"]}/page/<strong>Slug</strong>. Leave this empty to automatically generate it. You should <strong>not</strong> edit this if your pages are already indexed.</p>
            </div>
          </div>        
          <textarea class='form-control ckeditor' id='editor' name='content' rows='25'></textarea>
          <hr />
          <div class='row'>
            <div class='col-md-6'>
              <div class='input-group' style='width:90%;'>
                <label for='meta_title'>Custom Meta Title</label>
                <input type='text' class='form-control' name='meta_title' id='meta_title'>
                <p class='help-block'>Add a custom meta title to improve your SEO ranking. Leave empty to use the page title.</p>
              </div>             
            </div>
            <div class='col-md-6'>
              <div class='input-group' style='width:95%;display:block'>
                <label for='meta_description'>Custom Meta Description</label>
                <textarea class='form-control' name='meta_description' id='meta_description' rows='5'></textarea>
                <p class='help-block'>Add a custom meta description to improve your SEO ranking. Leave empty to use the content. Max 300 characters.</p>
              </div>             
            </div>
          </div>
          <hr />
          <ul class='form_opt' data-id='menu'>
            <li class='text-label'>Add to Menu<small>Do you want to add a link to this page in the menu?</small></li>
            <li><a href='' class='last current' data-value='0'>No</a></li>
            <li><a href='' class='first' data-value='1'>Yes</a></li>
          </ul>
          <input type='hidden' name='menu' id='menu' value='0' />  

          <ul class='form_opt' data-id='publish'>
            <li class='text-label'>Published<small>Do you want to publish this right now? If it is not ready, set it to <strong>No</strong> and work on it later.</small></li>
            <li><a href='' class='last' data-value='0'>No</a></li>
            <li><a href='' class='first  current' data-value='1'>Yes</a></li>
          </ul>
          <input type='hidden' name='publish' id='publish' value='1' />                      
          ".Main::csrf_token(TRUE)."
          <input type='submit' value='Add Page' class='btn btn-primary' />";

        $content.="</form>";
        Main::set("title","Add a Custom Page");
        $this->header();
        include($this->t("template"));
        $this->footer();       
      }  
      /**
       * Edit page
       * @since 1.0
       **/
      private function pages_edit(){
        // Add User
        if(isset($_POST["token"])){
          // Disable if demo
          if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));
          // Validate Results
          if(!Main::validate_csrf_token($_POST["token"])){
            return Main::redirect(Main::ahref("pages/edit/{$this->id}","",FALSE),array("danger","Something went wrong, please try again."));
          }

          if(!empty($_POST["name"])){
            if($this->db->get("page","slug=? AND id!=?","",array(Main::slug(!empty($_POST["slug"])?$_POST["slug"]:$_POST["name"]),$this->id))){
              Main::redirect(Main::ahref("pages/edit/{$this->id}","",FALSE),array("danger","This slug is already taken, please use another one."));
            }
            // Prepare Data
            $data = array(
              ":name" => Main::clean($_POST["name"],3),
              ":slug" => empty($_POST["slug"]) ? Main::slug($_POST["name"]) : Main::slug($_POST["slug"]),
              ":content" => $_POST["content"],
              ":menu" => in_array($_POST["menu"],array("0","1")) ? Main::clean($_POST["menu"],3):"0",
              ":publish" => in_array($_POST["publish"],array("0","1")) ? Main::clean($_POST["publish"],3):"0",
              ":meta_title" => Main::clean($_POST["meta_title"],3,TRUE),
              ":meta_description" => Main::clean($_POST["meta_description"],3,TRUE)              
              );         

            $this->db->update("page","",array("id"=>$this->id),$data);
            return Main::redirect(Main::ahref("pages/edit/{$this->id}","",FALSE),array("success","Page has been edited."));        
          }
          Main::redirect(Main::ahref("pages/edit/{$this->id}","",FALSE),array("danger","Please make sure that you fill everything correctly."));            
        }
        if(!$page=$this->db->get("page",array("id"=>"?"),array("limit"=>1),array($this->id))){
          return Main::redirect(Main::ahref("pages","",FALSE),array("danger","Page doesn't exist."));
        }
        // Add CDN Editor
        Main::cdn("ckeditor","",1);
        Main::admin_add("<script>CKEDITOR.replace( 'editor', {height: 350});</script>","custom",1);
        $header="Edit Page";
        $content="       
        <form action='".Main::ahref("pages/edit/{$this->id}")."' method='post' class='form-horizontal' role='form'>
          <div class='form-group'>
            <label for='name' class='col-sm-3 control-label'>Name</label>
            <div class='col-sm-9'>
              <input type='text' class='form-control' name='name' id='name' value='{$page->name}'>
            </div>
          </div>  
          <div class='form-group'>
            <label for='seo' class='col-sm-3 control-label'>Page Slug</label>
            <div class='col-sm-9'>
              <input type='text' class='form-control' name='slug' id='slug' value='{$page->slug}'>
              <p class='help-block'>Current Link: <strong>".Main::href("page/{$page->slug}")."</strong>. You should <strong>not</strong> edit this if your page is already indexed.</p>
            </div>
          </div>     
          <textarea class='form-control ckeditor' id='editor' name='content' rows='25'>{$page->content}</textarea>
          <hr />
          <div class='row'>
            <div class='col-md-6'>
              <div class='input-group' style='width:90%;'>
                <label for='meta_title'>Custom Meta Title</label>
                <input type='text' class='form-control' name='meta_title' id='meta_title' value='{$page->meta_title}'>
                <p class='help-block'>Add a custom meta title to improve your SEO ranking. Leave empty to use the page title.</p>
              </div>             
            </div>
            <div class='col-md-6'>
              <div class='input-group' style='width:95%;display:block'>
                <label for='meta_description'>Custom Meta Description</label>
                <textarea class='form-control' name='meta_description' id='meta_description' rows='5'>{$page->meta_description}</textarea>
                <p class='help-block'>Add a custom meta description to improve your SEO ranking. Leave empty to use the content. Max 300 characters.</p>
              </div>             
            </div>
          </div>
          <hr />
          <ul class='form_opt' data-id='menu'>
            <li class='text-label'>Add to Menu<small>Do you want to add a link to this page in the menu?</small></li>
            <li><a href='' class='last".(!$page->menu?' current':'')."' data-value='0'>No</a></li>
            <li><a href='' class='first".($page->menu?' current':'')."' data-value='1'>Yes</a></li>
          </ul>
          <input type='hidden' name='menu' id='menu' value='0' />  

          <ul class='form_opt' data-id='publish'>
            <li class='text-label'>Published<small>Do you want to publish this right now?</small></li>
            <li><a href='' class='last".(!$page->publish?' current':'')."' data-value='0'>No</a></li>
            <li><a href='' class='first".($page->publish?' current':'')."' data-value='1'>Yes</a></li>
          </ul>
          <input type='hidden' name='publish' id='publish' value='{$page->publish}' />           
          ".Main::csrf_token(TRUE)."
          <input type='submit' value='Edit Page' class='btn btn-primary' />
          <a href='".Main::href("page/{$page->slug}")."' class='btn btn-success' target='_blank'> View Page</a>";

        $content.="</form>";
        Main::set("title","Edit Page");
        $this->header();
        include($this->t("template"));
        $this->footer();       
      }
      /**
       * Delete page
       * @since 1.0
       **/
      private function pages_delete(){
        // Disable if demo
        if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));            
        // Delete single URL
        if(!empty($this->id) && is_numeric($this->id)){
          // Validated Single Nonce
          if(Main::validate_nonce("delete_page-{$this->id}")){
            $this->db->delete("page",array("id"=>"?"),array($this->id));
            return Main::redirect(Main::ahref("pages","",FALSE),array("success","Page has been deleted."));
          }        
        } 
        return Main::redirect(Main::ahref("pages","",FALSE),array("danger","An unexpected error occurred."));          
      }   
/**
   * Pages
   * @since 1.0
   **/
  protected function blog(){
    // Toggle
    if(in_array($this->do, array("edit","delete","add"))){
      $fn = "blog_{$this->do}";
      return $this->$fn();
    }       
    $posts = $this->db->get("blog","",array("order"=>"id"));
    $count = $this->db->rowCountAll;
    Main::set("title","Manage Blog");
    $this->header();
    include($this->t("blog"));
    $this->footer();
  }
      /**
       * Add page
       * @since 1.0
       **/
      private function blog_add(){
        // Process Data
        if(isset($_POST["token"])){
          // Disable if demo
          if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));
          // Validate Results
          if(!Main::validate_csrf_token($_POST["token"])){
            return Main::redirect(Main::ahref("blog/add","",FALSE),array("danger","Something went wrong, please try again."));
          }

          if(!empty($_POST["name"]) && !empty($_POST["content"])){
            if($this->db->get("blog",array("slug"=>Main::slug((!empty($_POST["slug"])?$_POST["slug"]:$_POST["name"]))))){
              Main::redirect(Main::ahref("blog/add","",FALSE),array("danger","This slug is already taken, please use another one."));
            }
            // Prepare Data
            $data = array(
              ":name" => Main::clean($_POST["name"],3),
              ":slug" => empty($_POST["slug"]) ? Main::slug($_POST["name"]) : Main::slug($_POST["slug"]),
              ":content" => $_POST["content"],
              ":publish" => in_array($_POST["publish"],array("0","1")) ? Main::clean($_POST["publish"],3):"0",
              ":meta_title" => Main::clean($_POST["meta_title"],3,TRUE),
              ":meta_description" => Main::clean($_POST["meta_description"],3,TRUE),
              ":date" => "NOW()"
              );         

            $this->db->insert("blog",$data);
            return Main::redirect(Main::ahref("blog","",FALSE),array("success","Post has been added."));        
          }
          Main::redirect(Main::ahref("blog/add","",FALSE),array("danger","Please make sure that you fill everything correctly."));            
        }

        // Add CDN Editor
        Main::cdn("ckeditor","",1);
        Main::admin_add("<script>CKEDITOR.replace( 'editor', {height: 350});</script>","custom",1);        
        $header="Add a Blog Post";
        $content="       
        <form action='".Main::ahref("blog/add")."' method='post' class='form-horizontal' role='form'>
          <div class='form-group'>
            <label for='title' class='col-sm-3 control-label'>Post Title</label>
            <div class='col-sm-9'>
              <input type='text' class='form-control' name='name' id='title' value=''>
            </div>
          </div>  
          <div class='form-group'>
            <label for='slug' class='col-sm-3 control-label'>Post Slug</label>
            <div class='col-sm-9'>
              <input type='text' class='form-control' name='slug' id='slug' value=''>
              <p class='help-block'>E.g. {$this->config["url"]}/blog/<strong>Slug</strong>. Leave this empty to automatically generate it. You should <strong>not</strong> edit this if your posts are already indexed.</p>
            </div>
          </div>       
          <p class='help-block'>To create an excerpt, use the code <strong>&lt;!--more--&gt;</strong> anywhere in your content.</p> 
          <textarea class='form-control ckeditor' id='editor' name='content' rows='25'></textarea>
          <hr />
          <div class='row'>
            <div class='col-md-6'>
              <div class='input-group' style='width:90%;'>
                <label for='meta_title'>Custom Meta Title</label>
                <input type='text' class='form-control' name='meta_title' id='meta_title'>
                <p class='help-block'>Add a custom meta title to improve your SEO ranking. Leave empty to use the post title.</p>
              </div>             
            </div>
            <div class='col-md-6'>
              <div class='input-group' style='width:95%;display:block'>
                <label for='meta_description'>Custom Meta Description</label>
                <textarea class='form-control' name='meta_description' id='meta_description' rows='5'></textarea>
                <p class='help-block'>Add a custom meta description to improve your SEO ranking. Leave empty to use the content. Max 300 characters.</p>
              </div>             
            </div>
          </div>
          <hr />

          <ul class='form_opt' data-id='publish'>
            <li class='text-label'>Published<small>Do you want to publish this right now? If it is not ready, set it to <strong>No</strong> and work on it later.</small></li>
            <li><a href='' class='last' data-value='0'>No</a></li>
            <li><a href='' class='first  current' data-value='1'>Yes</a></li>
          </ul>
          <input type='hidden' name='publish' id='publish' value='1' />                      
          ".Main::csrf_token(TRUE)."
          <input type='submit' value='Add Post' class='btn btn-primary' />";

        $content.="</form>";
        Main::set("title","Add a Blog Post");
        $this->header();
        include($this->t("template"));
        $this->footer();       
      }  
      /**
       * Edit page
       * @since 1.0
       **/
      private function blog_edit(){
        // Add User
        if(isset($_POST["token"])){
          // Disable if demo
          if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));
          // Validate Results
          if(!Main::validate_csrf_token($_POST["token"])){
            return Main::redirect(Main::ahref("blog/edit/{$this->id}","",FALSE),array("danger","Something went wrong, please try again."));
          }

          if(!empty($_POST["name"])){
            if($this->db->get("blog","slug=? AND id!=?","",array(Main::slug(!empty($_POST["slug"])?$_POST["slug"]:$_POST["name"]),$this->id))){
              Main::redirect(Main::ahref("blog/edit/{$this->id}","",FALSE),array("danger","This slug is already taken, please use another one."));
            }
            // Prepare Data
            $data = array(
              ":name" => Main::clean($_POST["name"],3),
              ":slug" => empty($_POST["slug"]) ? Main::slug($_POST["name"]) : Main::slug($_POST["slug"]),
              ":content" => $_POST["content"],
              ":publish" => in_array($_POST["publish"],array("0","1")) ? Main::clean($_POST["publish"],3):"0",
              ":meta_title" => Main::clean($_POST["meta_title"],3,TRUE),
              ":meta_description" => Main::clean($_POST["meta_description"],3,TRUE)              
              );         

            $this->db->update("blog","",array("id"=>$this->id),$data);
            return Main::redirect(Main::ahref("blog/edit/{$this->id}","",FALSE),array("success","Post has been edited."));        
          }
          Main::redirect(Main::ahref("blog/edit/{$this->id}","",FALSE),array("danger","Please make sure that you fill everything correctly."));            
        }
        if(!$post=$this->db->get("blog",array("id"=>"?"),array("limit"=>1),array($this->id))){
          return Main::redirect(Main::ahref("blog","",FALSE),array("danger","Post doesn't exist."));
        }
        // Add CDN Editor
        Main::cdn("ckeditor","",1);
        Main::admin_add("<script>CKEDITOR.replace( 'editor', {height: 350});</script>","custom",1);
        $header="Edit Page";
        $content="       
        <form action='".Main::ahref("blog/edit/{$this->id}")."' method='post' class='form-horizontal' role='form'>
          <div class='form-group'>
            <label for='name' class='col-sm-3 control-label'>Name</label>
            <div class='col-sm-9'>
              <input type='text' class='form-control' name='name' id='name' value='{$post->name}'>
            </div>
          </div>  
          <div class='form-group'>
            <label for='seo' class='col-sm-3 control-label'>Page Slug</label>
            <div class='col-sm-9'>
              <input type='text' class='form-control' name='slug' id='slug' value='{$post->slug}'>
              <p class='help-block'>Current Link: <strong>".Main::href("page/{$post->slug}")."</strong>. You should <strong>not</strong> edit this if your page is already indexed.</p>
            </div>
          </div>     
          <p class='help-block'>To create an excerpt, use the code <strong>&lt;!--more--&gt;</strong> anywhere in your content.</p> 
          <textarea class='form-control ckeditor' id='editor' name='content' rows='25'>{$post->content}</textarea>
          <hr />
          <div class='row'>
            <div class='col-md-6'>
              <div class='input-group' style='width:90%;'>
                <label for='meta_title'>Custom Meta Title</label>
                <input type='text' class='form-control' name='meta_title' id='meta_title' value='{$post->meta_title}'>
                <p class='help-block'>Add a custom meta title to improve your SEO ranking. Leave empty to use the page title.</p>
              </div>             
            </div>
            <div class='col-md-6'>
              <div class='input-group' style='width:95%;display:block'>
                <label for='meta_description'>Custom Meta Description</label>
                <textarea class='form-control' name='meta_description' id='meta_description' rows='5'>{$post->meta_description}</textarea>
                <p class='help-block'>Add a custom meta description to improve your SEO ranking. Leave empty to use the content. Max 300 characters.</p>
              </div>             
            </div>
          </div>
          <hr />

          <ul class='form_opt' data-id='publish'>
            <li class='text-label'>Published<small>Do you want to publish this right now?</small></li>
            <li><a href='' class='last".(!$post->publish?' current':'')."' data-value='0'>No</a></li>
            <li><a href='' class='first".($post->publish?' current':'')."' data-value='1'>Yes</a></li>
          </ul>
          <input type='hidden' name='publish' id='publish' value='{$post->publish}' />           
          ".Main::csrf_token(TRUE)."
          <input type='submit' value='Edit Post' class='btn btn-primary' />
          <a href='".Main::href("blog/{$post->slug}")."' class='btn btn-success' target='_blank'> View Post</a>";

        $content.="</form>";
        Main::set("title","Edit Post");
        $this->header();
        include($this->t("template"));
        $this->footer();       
      }
      /**
       * Delete page
       * @since 1.0
       **/
      private function blog_delete(){
        // Disable if demo
        if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));            
        // Delete single URL
        if(!empty($this->id) && is_numeric($this->id)){
          // Validated Single Nonce
          if(Main::validate_nonce("delete_post-{$this->id}")){
            $this->db->delete("blog",array("id"=>"?"),array($this->id));
            return Main::redirect(Main::ahref("blog","",FALSE),array("success","Post has been deleted."));
          }        
        } 
        return Main::redirect(Main::ahref("blog","",FALSE),array("danger","An unexpected error occurred."));          
      }         
  /**
   * Stats
   */
  protected function stats(){    
    // Get Counts
    $media = new stdClass;
    $user = new stdClass;
    $comment = new stdClass;

    $media->total   = $this->config["count_media"];
    $media->today   = $this->db->count("media","DAY(date) = DAY(CURDATE())");
    $media->yesterday   = $this->db->count("media","DATE(date) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY))");

    $comment->total   = $this->db->count("comment");
    $comment->today   = $this->db->count("comment","DAY(date) = DAY(CURDATE())");
    $comment->yesterday   = $this->db->count("comment","DATE(date) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY))");    

    $media->video   = $this->db->count("media", "type='video'");
    $media->music   = $this->db->count("media", "type='music'");
    $media->vine    = $this->db->count("media", "type='vine'");
    $media->picture = $this->db->count("media", "type='picture'");
    $media->top     = $this->db->get("media",array("approved" => 1), array("limit" => 5, "order" => "views"));

    $user->total = $this->db->count("user");
    $user->today   = $this->db->count("user","DAY(date) = DAY(CURDATE())");
    $user->yesterday   = $this->db->count("user","DATE(date) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY))");

    $user->country = $this->db->get(array("count" => "COUNT(country) as count, country as country","table" => "user"), array("active" => "1"), array("limit" => "3", "group" => "country", "order" => "count"));
    $user->active = $this->db->count("user", "active='1'");
    $user->inactive = $this->db->count("user", "active='0'");
    $user->facebook = $this->db->count("user", "auth='facebook'");
    $user->twitter = $this->db->count("user", "auth='twitter'");
    $user->google = $this->db->count("user", "auth='google'");
    $user->subscribe = $this->db->get("user","",array("limit" => 5, "order" => "subscribers"));
    
    
    Main::set("title", "Site Statistics");
    $this->header();
    include($this->t(__FUNCTION__)); 
    $this->footer();
  }
  /**
   * Menu
   * @since  1.2
   */
  protected function menu(){
    Main::set("title","Menu Editor");
    Main::cdn("jquery-ui","",TRUE);
    if(!$menus = json_decode($this->config["menus"], TRUE)){
        $default = array(
            array(
                "href" => Main::href(""),
                "text" => e("Home"),
                "icon" => "home"
              ),
            array(
                "href" => Main::href("trending"),
                "text" => e("Trending"),
                "icon" => "fire"
              )
          );
        if($this->config["type"]["video"]){
          $default[] = array(
              "href" => Main::href("video"),
              "text" => e("Video"),
              "icon" => "youtube-play"
            );            
        }
        if($this->config["type"]["music"]){
          $default[] = array(
              "href" => Main::href("music"),
              "text" => e("Music"),
              "icon" => "music"
            );                        
        }
        if($this->config["type"]["vine"]){
          $default[] = array(
              "href" => Main::href("vine"),
              "text" => e("Vine"),
              "icon" => "vine"
            );              
        }
        if($this->config["type"]["picture"]){
          $default[] = array(
              "href" => Main::href("picture"),
              "text" => e("Picture"),
              "icon" => "photo"
            );              
        }
        $default[] = array(
                "href" => Main::href("channels"),
                "text" => e("Channels"),
                "icon" => "list"
              );          
        if($this->config["type"]["blog"]){
          $default[] = array(
              "href" => Main::href("blog"),
              "text" => e("Blog"),
              "icon" => "newspaper-o"
            );              
        } 
        $menus = $default;
    }
    $categories = $this->db->get("category","",array("order"=>"type", "asc" => 1));
    $pages = $this->db->get("page","",array("order"=>"name"));
    $this->header();
    include($this->t(__FUNCTION__));
    $this->footer();    
  }
  /**
   * Settings
   * @since 1.1
   **/
  protected function settings(){
    if($this->do == "reset"){
        // Disable if demo
        if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));
        // Validated Single Nonce
        if(Main::validate_nonce("Reset-This-Awesome-Script-Please")){
          $this->db->run("TRUNCATE `{$this->config["prefix"]}comment`;");
          $this->db->run("TRUNCATE `{$this->config["prefix"]}media`;");
          $this->db->run("TRUNCATE `{$this->config["prefix"]}rating`;");
          $this->db->run("TRUNCATE `{$this->config["prefix"]}temp`;");
          $this->db->run("TRUNCATE `{$this->config["prefix"]}subscription`;");
          $this->db->run("TRUNCATE `{$this->config["prefix"]}blog`;");
          $this->db->delete("user", "id != '1'");     
          $this->db->run("ALTER TABLE  `{$this->config["prefix"]}user` AUTO_INCREMENT =2;");
          $this->db->delete("category", "id != '1'");
          $this->db->run("ALTER TABLE  `{$this->config["prefix"]}category` AUTO_INCREMENT =2;");
          $this->db->run("TRUNCATE `{$this->config["prefix"]}page`;");
          $this->db->run("TRUNCATE `{$this->config["prefix"]}favorite`;");
          return Main::redirect(Main::ahref("settings","",FALSE),array("success","Script has been reset. You will need to <strong>empty</strong> (don't delete) all folders in the content folder."));
        }          
      return Main::redirect(Main::ahref("settings","",FALSE),array("danger","An unexpected error has occured."));
    }
    // Update Settings
    if(isset($_POST["token"])){
      // Disable this for demo
      if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));
      // Check Token
      if(!Main::validate_csrf_token($_POST["token"])){
        Main::redirect(Main::ahref("settings","",FALSE),array("danger","Something went wrong, please try again."));
        return;
      }         
      // Upload Logo
      if(isset($_FILES["logo_path"]) && !empty($_FILES["logo_path"]["tmp_name"])) {
        $ext=array("image/png"=>"png","image/jpeg"=>"jpg","image/jpg"=>"jpg");            
        if(!isset($ext[$_FILES["logo_path"]["type"]])) return Main::redirect(Main::ahref("settings","",FALSE),array("danger","Logo must be either a PNG or a JPEG."));
        if($_FILES["logo_path"]["size"]>100*1024) return Main::redirect(Main::ahref("settings","",FALSE),array("danger","Logo must be either a PNG or a JPEG (Max 100KB)."));            
        $_POST["logo"]="auto_site_logo.".$ext[$_FILES["logo_path"]["type"]];
        move_uploaded_file($_FILES["logo_path"]['tmp_name'], ROOT."/content/auto_site_logo.".$ext[$_FILES["logo_path"]["type"]]);                
      }
      // Delete Logo
      if(isset($_POST["remove_logo"])){
        unlink(ROOT."/content/".$this->config["logo"]);
        $_POST["logo"]="";
      }       
      // Encode SMTP
      $_POST["smtp"]=json_encode($_POST["smtp"]);
      $_POST["type"]=json_encode($_POST["type"]);
      $_POST["amount_points"]=json_encode($_POST["amount_points"]);
      

      // Maximum Size Limit
      if($_POST["max_size"] > max_size()) $_POST["max_size"] = max_size();
      // Update Config
      foreach($_POST as $config => $var){
        if(in_array($config, array("ad728","ad300","ad468","adrep","adpreroll"))){
          $this->db->update("setting",array("value"=>"?"),array("config"=>"?"),array($var,$config));
        }else{
          $this->db->update("setting",array("value"=>"?"),array("config"=>"?"),array(Main::clean($var,2,TRUE),$config));
        }
      }
      Main::redirect(Main::ahref("settings","",FALSE),array("success","Settings have been updated.")); 
      return; 
    }       
    $lang="<option value='' ".($this->config["default_lang"]==""?" selected":"").">English</option>";
    foreach (new RecursiveDirectoryIterator(ROOT."/includes/languages/") as $path){
      if(!$path->isDir() && $path->getFilename()!=="." && $path->getFilename()!==".." && $path->getFilename()!=="lang_sample.php" && $path->getFilename()!=="index.php" && Main::extension($path->getFilename())==".php"){  
          $data=token_get_all(file_get_contents($path));
          $data=$data[1][1];
          if(preg_match("~Language:\s(.*)~", $data,$name)){
            $name="".strip_tags(trim($name[1]))."";
          }        
        $code=str_replace(".php", "" , $path->getFilename());
        $lang.="<option value='".$code."' ".($this->config["default_lang"]==$code?" selected":"").">$name</option>";
      }
    }     

    // Disabled in Demo Mode    
    $this->config["email"]=($this->config["demo"])?"Hidden":$this->config["email"];
    $this->config["captcha_public"]=($this->config["demo"])?"Hidden":$this->config["captcha_public"];
    $this->config["captcha_private"]=($this->config["demo"])?"Hidden":$this->config["captcha_private"];
    $this->config["facebook_secret"]=($this->config["demo"])?"Hidden":$this->config["facebook_secret"];
    $this->config["facebook_app_id"]=($this->config["demo"])?"Hidden":$this->config["facebook_app_id"];
    $this->config["twitter_key"]=($this->config["demo"])?"Hidden":$this->config["twitter_key"];
    $this->config["twitter_secret"]=($this->config["demo"])?"Hidden":$this->config["twitter_secret"];

    Main::set("title","Settings");
    Main::cdn("color", "", 1);
    $this->header();
    include($this->t(__FUNCTION__));
    $this->footer();
  }
      /**
       * Get Theme Styles
       * @since 1.0
       **/
      protected function style(){
        if(!is_dir(TEMPLATE."/styles/")) return FALSE;
        $html = '<div class="form-group">
              <label class="col-sm-3 control-label">Style</label>
              <div class="col-sm-9">
                <ul class="themes-style">
                <li class="dark"><a href="#" data-class="" '.($this->config["style"]==""?"class='current'":'').'>Dark</a></li>';        
        foreach (new RecursiveDirectoryIterator(TEMPLATE."/styles/") as $path){
          if(!$path->isDir() && Main::extension($path->getFilename())==".css"){  
            $name=str_replace(".css", "", $path->getFilename());
            $html.='<li class="'.$name.'"><a href="#" data-class="'.$name.'" '.($this->config["style"]==$name?"class='current'":'').'>'.ucfirst($name).'</a></li>';                  
          }
        }             
        $html.='</ul> 
              <input type="hidden" name="style" value="'.$this->config["style"].'" id="theme_value"> 
              <p class="help-block">The default theme supports these styles.</p>
            </div>
          </div>';
        return $html;
      }      
  /**
   * Themes
   * @since 1.0
   **/
  protected function themes(){
    // Error Handler
    if(!is_dir(ROOT."/themes/{$this->config["theme"]}/")) return Main::redirect("admin",array("danger","The active theme folder cannot be found!"));
    // LESS Editor
    if($this->do == "less"){
      return $this->theme_less();
    }    
    // Activate Theme
    if($this->do == "activate" && !empty($this->id)){
      // Disable this for demo
      if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));      
      // Check Security Token
      if(!Main::validate_nonce("theme-{$this->id}")){
        return Main::redirect(Main::ahref("themes","",FALSE),array("danger","Security token expired, please try again."));
      }       

      if(!file_exists(ROOT."/themes/{$this->id}/style.css")){
        return Main::redirect(Main::ahref("themes","",FALSE),array("danger","Sorry this theme cannot be activated because it is missing the stylesheet.")); 
      }
      if($this->db->update("setting",array("value"=>"?"),array("config"=>"?"),array(Main::clean($this->id,3,TRUE),"theme"))){
        Main::redirect(Main::ahref("themes","",FALSE),array("success","Theme has been activated."));
      }      
      return Main::redirect(Main::ahref("themes","",FALSE),array("danger","An unexpected issue occurred, please try again."));
    }
    // Clone Theme
    if($this->do == "copy" && !empty($this->id)){
      // Disable this for demo
      if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));      
      // Check Security Token
      if(!Main::validate_nonce("copy-{$this->id}")){
        return Main::redirect(Main::ahref("themes","",FALSE),array("danger","Security token expired, please try again."));
      }       
      $this->copy_folder(ROOT."/themes/{$this->id}",ROOT."/themes/{$this->id}".rand(0,9));
      return Main::redirect(Main::ahref("themes","",FALSE),array("success","Theme has been successfully cloned."));
    }     
    // Get Themes
    $theme_list="";
    foreach (new RecursiveDirectoryIterator(ROOT."/themes/") as $path){
      if($path->isDir() && $path->getFilename()!=="." && $path->getFilename()!==".." && file_exists(ROOT."/themes/".$path->getFilename()."/style.css")){          

        $data=token_get_all(file_get_contents(ROOT."/themes/".$path->getFilename()."/style.css"));
        $data=isset($data[0][1])?$data[0][1]:FALSE;
        if($data){
          if(preg_match("~Theme Name:\s(.*)~", $data,$name)){
            $name=strip_tags(trim($name[1]));
          }        
          if(preg_match("~Author:\s(.*)~", $data,$author)){
            $author=strip_tags(trim($author[1]));
          }        
          if(preg_match("~Author URI:\s(.*)~", $data,$url)){
            $url=strip_tags(trim($url[1]));
          }
          if(preg_match("~Version:\s(.*)~", $data,$version)){
            $version=strip_tags(trim($version[1]));
          }
          if(preg_match("~Date:\s(.*)~", $data,$date)){
            $date=strip_tags(trim($date[1]));
          }
        }
        $name=isset($name) && !is_array($name)? $name : "No Name";
        $author=isset($author) && !is_array($author)? $author : "Unknown";
        $url=isset($url) && !is_array($url)? $url : "#none";
        $version=isset($version) && !is_array($version)? $version : "1.0";
        $date=isset($date) && !is_array($date)? $date : "";

        if(file_exists(ROOT."/themes/".$path->getFilename()."/screenshot.png")){
          $screenshot=$this->config["url"]."/themes/".$path->getFilename()."/screenshot.png";
        }else{
          $screenshot=$this->config["url"]."/static/noscreen.png";
        }
        $theme_list.="<div class='theme-list'>";
          $theme_list.="<div class='theme-img'><img src='$screenshot' alt='$name'><p>By <a href='$url' rel='nofollow' target='_blank'>$author</a> (v$version)</p></div>";
          $theme_list.="<div class='theme-info'>";
          $theme_list.="<strong>$name</strong>";
          if($this->config["theme"]!==$path->getFilename()) {
            $theme_list.="<div class='btn-group btn-group-xs pull-right'><a href='".Main::ahref("themes/activate/{$path->getFilename()}").Main::nonce('theme-'.$path->getFilename())."' class='btn btn-success'>Activate</a><a href='".Main::ahref("themes/copy/{$path->getFilename()}").Main::nonce('copy-'.$path->getFilename())."' class='btn btn-info delete'>Clone</a></div>";
          }else{
            $theme_list.="<div class='btn-group btn-group-xs pull-right'><a class='btn btn-dark'>Active</a><a href='".Main::ahref("themes/copy/{$path->getFilename()}").Main::nonce('copy-'.$path->getFilename())."' class='btn btn-info delete'>Clone</a></div>";
          }
        $theme_list.="</div></div>";
      }
    }    

    Main::set("title","Themes");
    $this->header();
      echo "<div class='row themes'>
            ".$theme_list." 
          </div>";
    $this->footer();
  }
  /**
   * Show Theme Editor
   * @author KBRmedia
   * @since  1.0
   */
  protected function editor(){
    // Update Theme
    if(isset($_POST["token"])){
      // Disable this for demo
      if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));
      // Check Token
      if(!Main::validate_csrf_token($_POST["token"])){
       return Main::redirect(Main::ahref("editor","",FALSE),array("danger","Something went wrong, please try again."));
      }  
      if($_POST["theme_files"]=="style"){
        $file_path=THEME."/style.css";
      }else{
        $file_path=THEME."/".Main::clean($_POST["theme_files"],3,TRUE).".php";
      }
      if(file_exists($file_path)){
        $file = fopen($file_path, 'w') or die(Main::redirect(Main::ahref("editor","",FALSE),array("danger","Cannot open file. Please make sure that the file is writable.")));
        fwrite($file, $_POST["content"]);
        fclose($file);
        return Main::redirect(Main::ahref("editor/".Main::clean($_POST["theme_files"],3,TRUE),"",FALSE),array("success","File has been successfully edited."));
      }
    }         
    // Get Files
    $themeFiles=$this->themeFiles();
    // Get Current File
    $currentFile=$this->currentFile();
    // Add ACE from CDN
    Main::cdn("ace","",1);
    Main::admin_add('
      <script type="text/javascript">
        var editor = ace.edit("code-editor");
            editor.setTheme("ace/theme/xcode");
            editor.getSession().setMode("ace/mode/'.$currentFile["type"].'");
        $(document).ready(function(){
          $("#form-editor").submit(function(){
            $("#code").val(editor.getSession().getValue());
          });
        });
      </script>',"custom",1);        
    Main::set("title","Theme Editor");
    $this->header();
      echo "<h2>Theme Editor</h2><br><div class='editor'>
              <form action='".Main::ahref('editor/update')."' method='post' class='form' id='form-editor'>
                <textarea name='content' id='code' class='form-control hidden' rows='1'></textarea>
                <div class='header'>
                  <div class='row'>
                    <div class='col-sm-6'>
                      Currently editing: ".$currentFile['current']."
                    </div>
                    <div class='col-sm-6'>
                      <select name='theme_files' id='theme_files' style='max-width: 250px' class='pull-right'>
                        ".$themeFiles."
                      </select>
                    </div>
                  </div>
                </div>
                <div id='code-editor'>".$currentFile['content']."</div>
                <br class='clear'>
                ".Main::csrf_token(TRUE)."
                <button class='btn btn-primary btn-lg'>Update File</button>
              </form>  
            </div>";
    $this->footer();        
  }
      /**
       * Theme Files
       * @since 1.0
       **/
      protected function themeFiles(){
        $data="";
        foreach (new RecursiveDirectoryIterator(ROOT."/themes/{$this->config["theme"]}/") as $path){
          if(!$path->isDir() && $path->getFilename()!=="." && $path->getFilename()!==".." && (Main::extension($path->getFilename())==".php" || Main::extension($path->getFilename())==".css")){
            $file=explode(".",$path->getFilename());
            $file=$file[0];
            $name=ucwords(str_replace("_", " ", $file));
            $code=strtolower($file);      
            if($path->getFilename()=="style.css") {
              $name="Main Stylesheet";
              $data.="<option value='$code' ".(empty($this->do) || $this->do=="style" ? "selected":"").">$name ({$path->getFilename()})</option>";
            }elseif($path->getFilename()=="index.php"){
              $name="Home Page";              
              $data.="<option value='$code' ".($this->do==$code ? "selected":"").">$name ({$path->getFilename()})</option>";              
            }else{
              $data.="<option value='$code' ".($this->do==$code ? "selected":"").">$name ({$path->getFilename()})</option>";
            }
          }
        }
        return $data;
      }
      /**
       * Current Theme
       * @since 1.0
       **/
      protected function currentFile(){
        $data=array();
        // Get File
        if(!empty($this->do) && $this->do!=="style"){
          if(!empty($this->do) && file_exists(THEME."/{$this->do}.php")){
            $data["type"]="html";
            $data["current"]=ucfirst($this->do).".php";
             // Disable if demo
            if($this->config["demo"]){
              $data["content"]="Content is hidden in demo";
            }else{
              $data["content"]=htmlentities(file_get_contents(THEME."/{$this->do}.php", "r"));
            }            
          }else{
            return Main::redirect(Main::ahref("editor","",FALSE),array("danger","Theme file doesn't exist."));
          }
        }else{
          $data["type"]="css";
          $data["current"]="Main Stylesheet (style.css)";
          if($this->config["demo"]){
            $data["content"]="Content is hidden in demo";
          }else{          
            $data["content"]=htmlentities(file_get_contents(THEME."/style.css", "r"));
          }
        }
        return $data;
      }   
  /**
   * Show LESS Editor
   * @author KBRmedia
   * @since  1.5
   */
  protected function theme_less(){
    $file_path=THEME."/style.less";
    if(!file_exists($file_path)){
      return Main::redirect(Main::ahref("","",FALSE),array("danger","LESS file doesn't exist."));
    }
    // Update Theme
    if(isset($_POST["token"])){
      // Disable this for demo
      if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));
      // Check Token
      if(!Main::validate_csrf_token($_POST["token"])){
       return Main::redirect(Main::ahref("themes/less","",FALSE),array("danger","Something went wrong, please try again."));
      }  
      $file_path=THEME."/style.less";
      if(file_exists($file_path)){
        $file = fopen($file_path, 'w') or die(Main::redirect(Main::ahref("themes/less","",FALSE),array("danger","Cannot open file. Please make sure that the file is writable.")));
        fwrite($file, $_POST["content"]);
        fclose($file);
        include(ROOT."/includes/library/less.php");
        $f = $_POST["content"];
        $less = new Lessc;
$new = "/*
Theme Name: GemMedia
Author: KBRmedia
Author URI: http://gempixel.com  
Version: "._VERSION."
Date: ".date("Y-m-d")."
Copyright: This theme is designed to be used only with a valid license of Premium Media Script and cannot be resold or reused in any other applications without prior consent from the author. You are however free to customize this theme for your own purpose. Please contact the author, if needed, to clarify.

This file and formating is used to identify each theme and provide information about the author and version. Keep the formating as clean as possible.
*/
{$less->compile($f)}";
        file_put_contents(THEME."/style.css", $new);        
        return Main::redirect(Main::ahref("themes/less".Main::clean($_POST["theme_files"],3,TRUE),"",FALSE),array("success","New stylesheet has been successfully generated."));
      }
    }         
    // Get Current File
    $currentFile=htmlentities(file_get_contents(THEME."/style.less", "r"));
    // Add ACE from CDN
    Main::cdn("ace","",1);
    Main::admin_add('
      <script type="text/javascript">
        var editor = ace.edit("code-editor");
            editor.setTheme("ace/theme/xcode");
            editor.getSession().setMode("ace/mode/css");
        $(document).ready(function(){
          $("#form-editor").submit(function(){
            $("#code").val(editor.getSession().getValue());
          });
        });
      </script>',"custom",1);        
    Main::set("title","LESS Editor");
    $this->header();
      echo "<h2>LESS Editor</h2><br><div class='editor'>
              <form action='".Main::ahref('themes/less')."' method='post' class='form' id='form-editor'>
                <textarea name='content' id='code' class='form-control hidden' rows='1'></textarea>
                <div class='header'>
                  <div class='row'>
                    <div class='col-sm-12'>
                      Change variables and press compile. This will create a new CSS file overriding the default style.css and all changes made directly to the style.css file. To change the main color, change the @main: variable.
                    </div>
                  </div>
                </div>
                <div id='code-editor'>".$currentFile."</div>
                <br class='clear'>
                ".Main::csrf_token(TRUE)."
                <button class='btn btn-primary btn-lg'>Compile</button>
              </form>  
            </div>";
    $this->footer();        
  }        
  /**
   * Languages
   * @since 1.0
   **/
  protected function languages(){
    // Update Language
    if(isset($_POST["token"])){
      // Disable this for demo
      if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));
      // Check Token
      if(!Main::validate_csrf_token($_POST["token"])){
       return Main::redirect(Main::ahref("languages","",FALSE),array("danger","Something went wrong, please try again."));
      }  
      if(empty($_POST["language_name"])) return Main::redirect(Main::ahref("languages","",FALSE),array("danger","Language name cannot be empty!"));
      // Update Language
      $file = substr(strtolower(Main::clean(trim($_POST["language_name"]),3,TRUE)), 0, 2).".php";
      $handle = fopen(LANGPATH."/".$file, 'w') or Main::redirect(Main::ahref("languages","",FALSE),array("danger","Cannot create file. Make sure that the folder is writable."));

      $comment="<?php\n";
      $comment.="/*\n* Language: ".ucfirst(Main::clean($_POST["language_name"],3,TRUE))."\n* Author: You\n* Author URI: {$this->config["url"]}\n* Translator: PremiumMediaScript\n* Date: ".date("Y-m-d H:i:s",time())."\n* ---------------------------------------------------------------\n* Important Notice: Make sure to only change the right-hand side\n* DO NOT CHANGE THE LEFT-HAND SIDE\n* Edit the text between double-quotes \"DONT EDIT\"=> \"\" on the right side\n* Make sure to not forget any quotes \" and the comma , at the end\n* ---------------------------------------------------------------\n*/\n";
      $comment.='$lang=array(';

      fwrite($handle, $comment);
      foreach ($_POST["text"] as $o => $t) {
        fwrite($handle, "\n\"".strip_tags($o,"<b><i><s><u><strong>")."\"".'=>'."\"".strip_tags($t,"<b><i><s><u><strong>")."\",");
      }
      fwrite($handle, "); ?>");
      fclose($handle);    
      return Main::redirect(Main::ahref("languages","",FALSE),array("success","Language file has been successfully."));      
    }      
    // Delete Language
    if($this->do=="delete" && !empty($this->id) && strlen($this->id)=="2" && file_exists(LANGPATH."/{$this->id}.php")){
      // Disable this for demo
      if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));      
      // Check Security Token
      if(!Main::validate_nonce("delete-lang")){
        return Main::redirect(Main::ahref("languages","",FALSE),array("danger","Security token expired, please try again."));
      }
      unlink(LANGPATH."/{$this->id}.php");
      return Main::redirect(Main::ahref("languages","",FALSE),array("success","Language file has been deleted."));
    }
    $lang_list="";
    foreach (new RecursiveDirectoryIterator(LANGPATH."/") as $path){
      if(!$path->isDir() && $path->getFilename()!=="." && $path->getFilename()!==".." && $path->getFilename()!=="index.php" && $path->getFilename()!=="lang_sample.php" && Main::extension($path->getFilename())==".php"){  

        $file=explode(".", $path->getFilename());
        $file=$file[0];
        $code=strtolower($file);
        $data=token_get_all(file_get_contents($path));
        $data=isset($data[1][1])?$data[1][1]:FALSE;
          if($data){
            if(preg_match("~Language:\s(.*)~", $data,$name)){
              $name=Main::truncate(strip_tags(trim($name[1])),10);
            }
            if(preg_match("~Author:\s(.*)~", $data,$author)){
              $author=strip_tags(trim($author[1]));
            }           
            if(preg_match("~Date:\s(.*)~", $data,$date)){
              $date=strip_tags(trim($date[1]));
            }                                      
          }else{
            $name="Unknown";
            $author="Unknown";
            $date="Unknown";
          }
        $lang_list.="<a href='".Main::ahref("languages/edit/{$code}")."' class='list-group-item".($this->id==$code ? " active":"")."'>
            <h4 class='list-group-item-heading'>$name</h4>
            <p class='list-group-item-text'>By $author <small class='pull-right'>(".Main::timeago($date).")</small></p>
          </a>";
      }
    }
    $lang_content=$this->getLang();
    Main::set("title","Manage Translations");
    $this->header();
    include($this->t(__FUNCTION__));
    $this->footer();
  }     
      /**
       * Get Language
       * @since 1.0
       **/
      protected function getLang(){
        // Check if it needs to edited
        if($this->do=="edit" && !empty($this->id)){
          if(strlen($this->id)!="2" || !file_exists(LANGPATH."/{$this->id}.php")){
            return Main::redirect(Main::ahref("languages","",FALSE),array("danger","File doesn't exist!"));
          }
          $data=token_get_all(file_get_contents(LANGPATH."/{$this->id}.php"));
          $data=isset($data[1][1])?$data[1][1]:FALSE;
            if($data){
              if(preg_match("~Language:\s(.*)~", $data,$name)){
                $name=strip_tags(trim($name[1]));
              }
            }
          // Get File
          include(LANGPATH."/{$this->id}.php");          
          // Check if properly formated
          if(!isset($lang) || !is_array($lang)){
            return "<p class='alert alert-danger'>The translation file appears to be empty or corrupted. Please verify that it is properly formated!</p>";
          }
          // Generate form
          $data="";
          $data.="<form action='".Main::ahref("languages/")."' method='post' class='form'>";
          $data.='<p class="alert alert-warning">
                  For each of the strings below, write the translated text for the label in the textarea. HTML markup allowed: &lt;b&gt;&lt;i&gt;&lt;s&gt;&lt;u&gt;&lt;strong&gt;. It is highly recommended that you save frequently to prevent loss of data. It does not matter if you do not translate everything, just make sure to save periodically!
                  </p>';
          $data.="<div class='form-group'>
              <label for='language_name' class='control-label'>Edit Language Name (e.g. French)</label>
              <input type='text' class='form-control' name='language_name' id='language_name' value='$name'>                
            </div><h4 class='page-header'>To be translated</h4>";        
          foreach ($lang as $original => $translation){
            $data.="<div class='form-group'>
              <label class='control-label'>$original</label>
              <textarea name='text[$original]' class='form-control' style='min-height:60px;'>$translation</textarea>
            </div><hr />";
          }      
          $data.=Main::csrf_token(TRUE);          
          $data.="<button class='btn btn-primary'>Update Translation</button> ";
          $data.="<a href='".Main::ahref("languages/delete/{$this->id}").Main::nonce("delete-lang")."' class='btn btn-danger delete'>Delete</a></form>";           
          return $data;
        }
        // Add language from Sample
        $data="";
        if(!file_exists(LANGPATH."/lang_sample.php")){
          $data="<p class='alert alert-danger'>Sample file (lang_sample.php) is not available. Please upload that in the includes/languages/ folder. This editor will not work until that file is properly uploaded there and is accessible!</p>";
        }else{
          // Get File
          include(LANGPATH."/lang_sample.php");
          // Check if properly formated
          if(!isset($lang) || !is_array($lang)){
            return "<p class='alert alert-danger'>The sample translation file appears to be empty or corrupted. Please verify that it is properly formated!</p>";
          }          
          // Generate Form
          $data.="<form action='".Main::ahref("languages")."' method='post' class='form'>";
          $data.='<p class="alert alert-warning">
                   To create a new language file, write the language in the field below and translate each of the strings in the textarea just below it. The text will appear as they do right now so remember to respect the letter case. Remember that the language code will be the first two letters of the language: for example if the language name is French then the language code will be fr. If for some reason this editor doesn\'t work for you, you may manually translate it by following the documentation. It is highly recommended that you save frequently to prevent loss of data. It does not matter if you do not translate everything, just make sure to save periodically!
                  </p>';
          $data.="<div class='form-group'>
              <label for='language_name' class='control-label'>New Language Name (e.g. French)</label>
              <input type='text' class='form-control' name='language_name' id='language_name' value=''>                
            </div><h4 class='page-header'>To be translated</h4>";        
          foreach ($lang as $original => $translation){
            $data.="<div class='form-group'>
              <label class='control-label'>$original</label>
              <textarea name='text[$original]' class='form-control' style='min-height:60px;'>$translation</textarea>
            </div><hr />";
          }      
          $data.=Main::csrf_token(TRUE);
          $data.="<button class='btn btn-primary'>Create Translation</button></form>";        
        }
        return $data;
      }  
  /**
   * Tools
   */
  protected function tools(){
    $fn = "tools_".$this->do;
    if(method_exists(__CLASS__, $fn)){
      return $this->$fn();
    }
    return Main::redirect("admin",array("danger","Oups! The page you are looking for doesn't exist."));
  }
    /**
     * Sitemap
     * @since  1.1
     */
    protected function tools_sitemap(){
      // Generate Sitemap
      if(isset($_POST["token"])){
        // Disable if demo
        if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));
        // Validate Token
        if(!Main::validate_csrf_token($_POST["token"])){
          Main::redirect(Main::ahref("tools/sitemap","",FALSE),array("danger","Invalid token, please try again."));
          return;
        }
        return $this->generate_sitemap();
      }
      // Generate Template
      $header = "Generate XML Sitemap";
      $type = "<option value='all'>Everything (Media + Categories + Pages + Blog)</option>";
      foreach (types() as $slug => $name) {
        $type .= "<option value = '$slug'>{$name}s Only</option>";
      }

      $current = "";
      if(file_exists(ROOT."/sitemap.xml")){
        $current .= "<p><strong>General Sitemap</strong> <p><a class='btn btn-primary btn-xs' href='{$this->config["url"]}/sitemap.xml' target='_blank'>View Sitemap</a></p>";
      }
      
      if(file_exists(ROOT."/sitemap_video.xml")){
        $current .= "<p><strong>Videos Sitemap</strong> <p><a class='btn btn-primary btn-xs' href='{$this->config["url"]}/sitemap_video.xml' target='_blank'>View Sitemap</a></p>";
      }
      
      if(file_exists(ROOT."/sitemap_music.xml")){
        $current .= "<p><strong>Music Videos Sitemap</strong> <p><a class='btn btn-primary btn-xs' href='{$this->config["url"]}/sitemap_music.xml' target='_blank'>View Sitemap</a></p>";
      }
      
      if(file_exists(ROOT."/sitemap_picture.xml")){
        $current .= "<p><strong>Pictures Sitemap</strong> <p><a class='btn btn-primary btn-xs' href='{$this->config["url"]}/sitemap_picture.xml' target='_blank'>View Sitemap</a></p>";
      }

      if(file_exists(ROOT."/sitemap_vine.xml")){
        $current .= "<p><strong>Vines Sitemap</strong> <p><a class='btn btn-primary btn-xs' href='{$this->config["url"]}/sitemap_vine.xml' target='_blank'>View Sitemap</a></p>";
      }           
      $content = "
        <p>This tool allows you to generate xml sitemap and ping some search engines along the way. Please note that the number of URLs is infinite however most search engines accept up to 50,000 URLs or 10MB. Therefore the ultimate limit would be either of those limits.</p>
        $current
        <hr />
        <form class='form' role='form' action='".Main::ahref("tools/sitemap")."' method='post'>
          <div class='form-group'>
            <label class='control-label'>Sitemap Type</label>
            <select name='type'>
              $type
            </select>
          </div>
          ".Main::csrf_token(TRUE)."
          <button class='btn btn-primary'>Generate Sitemap</button>          
        </form>
      ";
      Main::set("title", "Generate XML Sitemaps");
      $this->header();
      include($this->t("template"));
      $this->footer();
    }
        /**
         * Generate Sitemap
         * @since 1.0
         **/
        protected function generate_sitemap(){
          // Filename
          if(isset($_POST["type"]) && types($_POST["type"])){
            $filename = "sitemap_{$_POST["type"]}.xml";
            $category = array("type" => $_POST["type"]);
            $media = array("approved" => 1, "type" => $_POST["type"]);
          }else{
            $filename = "sitemap.xml";
            $category = array();
            $media = array("approved" => 1);
          }
          if (!$xmlfile=fopen(ROOT."/".$filename, "w")) {
            return Main::redirect(Main::ahref("tools/sitemap","",FALSE), array("danger", "Cannot open file. Please check your PHP configuration."));
          }

          fwrite($xmlfile,
              "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
              <urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\">
                <url>
                  <loc>".$this->config["url"]."/</loc>
                  <lastmod>".date('Y-m-d')."</lastmod>
                  <changefreq>daily</changefreq>
                  <priority>0.9</priority>
                </url>"
          );

          if($filename == "sitemap.xml"){
            $query = $this->db->get("category", $category);
            foreach ($query as $line) {
              fwrite($xmlfile,
                "<url>
                  <loc>".Main::href("channel/{$line->type}/{$line->slug}")."</loc>
                  <lastmod>".date('Y-m-d')."</lastmod>
                  <changefreq>daily</changefreq>
                  <priority>0.7</priority>
                </url>"
              );
            } 

            $query = $this->db->get("page");
            foreach ($query as $line) {
              fwrite($xmlfile,
                "<url>
                  <loc>".Main::href("page/{$line->slug}")."</loc>
                  <lastmod>".date('Y-m-d')."</lastmod>
                  <changefreq>weekly</changefreq>
                  <priority>0.7</priority>
                </url>"
              );
            } 
            $query = $this->db->get("blog");
            foreach ($query as $line) {
              fwrite($xmlfile,
                "<url>
                  <loc>".Main::href("blog/{$line->slug}")."</loc>
                  <lastmod>".date('Y-m-d')."</lastmod>
                  <changefreq>weekly</changefreq>
                  <priority>0.7</priority>
                </url>"
              );
            }                        
          }

          $query = $this->db->get("media", $media,array("order"=>"date"));
          foreach ($query as $line) { 
            fwrite($xmlfile,
              "<url>
                <loc>".Main::href("view/{$line->url}")."</loc>
                <lastmod>".date("Y-m-d", strtotime($line->date))."</lastmod>
                <changefreq>weekly</changefreq>
                <priority>0.7</priority>
              </url>"
            );
          }

          fwrite($xmlfile,"</urlset>");
          fclose($xmlfile);

          Main::http('http://www.google.com/webmasters/tools/ping?sitemap='.urlencode($this->config["url"].'/sitemap.xml'));
          Main::http('http://www.bing.com/webmaster/ping.aspx?siteMap='.urlencode($this->config["url"].'/sitemap.xml'));
          Main::redirect(Main::ahref("tools/sitemap","",FALSE),array('success',"A sitemap has been generated and Google and Bing have been notified."));
        }
    /**
     * Export Data
     * @author KBRmedia
     * @since  1.0
     */
    protected function tools_export(){
      // Generate File
      if(isset($_POST["token"])){
        // Disable if demo
        if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));
        // Validate Token
        if(!Main::validate_csrf_token($_POST["token"])){
          Main::redirect(Main::ahref("tools/export","",FALSE),array("danger","Invalid token, please try again."));
          return;
        }
        $keys = explode(",",$_POST["data"]);
        $data = $this->db->get("user","",array("order" => "id"));
        $k = "";
        $d = "";
        if(in_array("id",$keys)){
          $k .= "ID,";
        }        
        if(in_array("email",$keys)){
          $k .= "Email,";
        }
        if(in_array("name",$keys)){
          $k .= "Full Name,";
        }
        if(in_array("country",$keys)){
          $k .= "Country,";
        }
        if(in_array("date",$keys)){
          $k .= "Date,";
        }                                
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=UserList.csv');
        echo "$k\n";
        foreach ($data as $line) {
          if(in_array("id",$keys)){
            echo $line->id.",";
          }        
          if(in_array("email",$keys)){
            echo $line->email.",";
          }
          if(in_array("name",$keys)){
            echo $line->name.",";
          }
          if(in_array("country",$keys)){
            echo $line->country.",";
          }
          if(in_array("date",$keys)){
            echo $line->date.",";
          } 
          echo "\n";
        }
        exit;        
      }
      // Generate Template
      $header = "Export Data as CSV";
      $content = "
        <p>This tool allows you to generate csv files of selected data. You can use this to backup data or import them on third-party sites like mailchimp.</p>
        <hr />
        <form class='form' role='form' action='".Main::ahref("tools/export")."' method='post'>
          <div class='form-group'>
              <label class='control-label'>Export Users</label>
              <input type='text' name='data' class='form-control'>            
            <p class='help-block'>Use this field to select which data to export for users.
              <h3>You can use any combination of the following keys:</h3>
              <ul>
                <li>email</li>
                <li>id</li>
                <li>name</li>
                <li>country</li>
                <li>date</li>
              </ul>

              <h3>Here is an example</h3> 
              If you input <strong>email,id,name</strong> then the csv file will contain the email, followed by the id and the name.
            </p>
          </div>
          ".Main::csrf_token(TRUE)."
          <button class='btn btn-primary'>Export Data</button>          
        </form>
      ";
      Main::set("title", $header);
      $this->header();
      include($this->t("template"));
      $this->footer();      
    }
    /**
     * Send Newsletter
     * @author KBRmedia
     * @since  1.0
     */
    protected function tools_newsletter(){
      if(in_array($this->id, array("send","digest"))){
        $fn = __FUNCTION__."_{$this->id}";
        return $this->$fn();
      }          
      $count = $this->db->count("user", "active = '1'");
      $beforehead = "<div class='panel panel-default panel-body panel-red'>
                        <p class='main-stats'><span>$count</span> emails will be sent simultaneously.</p>
                     </div>";
      $header = "Send a Newsletter to your Active Users";      
      $content = "
        <p>This tool allows you to send a self-generated newsletter to all of your users. The newsletter can be either a digest which is a summary of new media or a custom email.           
        </p>
        <p>
          <h3>Warning</h3>
          This tool can be very memory intensive so you absolutely have to make sure that your hosting provider supports this function or allows you send many emails at once otherwise it will most likely get you in trouble. Also please don't spam your users otherwise they will blacklist your domain name forever. Also don't send too many newsletters as your hosting provider will suspect you of spam.        
        </p>

        <hr />
        <h3>Send an Automatic Digest</h3>
        <p>An automatic digest is a summary of new media files approved in the current week. All you have to do is to click the button below to send. Again keep the warning above in mind. If you don't have any new media files, don't send any digests.</p>
        <form class='form' role='form' action='".Main::ahref("tools/newsletter/digest")."' method='post'>
          ".Main::csrf_token(TRUE)."
          <button class='btn btn-primary'>Send the Digest</button>          
        </form>

        <hr />
        <h3>Send a Custom Newsletter</h3>
        <p>
          You can also send a custom message to your users to let them know of changes or important announcements. Simply enter your message below and press send. You can also use some shortcodes to add dynamic data.
        </p>
        <form class='form' role='form' action='".Main::ahref("tools/newsletter/send")."' method='post'>
          <div class='form-group'>
            <div class='row'>
              <div class='col-md-6'>
                <div class='form-group'>
                  <label class='control-label'>Newsletter Subject</label>
                  <br><br>
                  <input type='text' name='subject' class='form-control'>           
                </div>            
                <div class='form-group'>
                  <label class='control-label'>Newsletter Message</label>
                  <br><br>
                  <textarea name='message' rows='10' class='form-control'></textarea>                 
                </div>        
              </div>
              <div class='col-md-6'>
                <div class='form-group'>
                  <label class='control-label'>Shortcodes</label>
                  <br><br>
                  <ul>
                    <li>User's Name: <strong>{name}</strong></li>
                    <li>User's Username: <strong>{username}</strong></li>
                    <li>User's Email: <strong>{email}</strong></li>
                    <li>User's Sign Up Date: <strong>{date}</strong></li>
                    <li>User's Last Login Date: <strong>{lastlogin}</strong></li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          ".Main::csrf_token(TRUE)."
          <button class='btn btn-primary'>Send Newsletter</button>          
        </form>        
      ";
      Main::set("title", $header);
      $this->header();
      include($this->t("template"));
      $this->footer();      
    }
        /**
         * Send Custom Newsletter
         * @author KBRmedia
         * @since  1.1
         */
        private function tools_newsletter_send(){
          if(isset($_POST["token"])){
            // Disable if demo
            if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));
            // Validate Token
            if(!Main::validate_csrf_token($_POST["token"])){
              Main::redirect(Main::ahref("tools/newsletter","",FALSE),array("danger","Invalid token, please try again."));
              return;
            }     
            // Check for empty email content      
            if(empty($_POST["subject"]) || empty($_POST["message"])) return Main::redirect(Main::ahref("tools/newsletter","",FALSE),array("danger","You are trying to send empty emails."));
            // Get Users
            $users = $this->db->get("user",array("active" => "1"));
            foreach ($users as $user) {
              if(!empty($user->email)){
                // Send Email
                $_POST["message"] = nl2br($_POST["message"]);
                $content = str_replace("{name}", $user->name, $_POST["message"]);
                $content = str_replace("{username}", $user->username, $content);
                $content = str_replace("{email}", $user->email, $content);
                $content = str_replace("{date}", date("F-m-d H:i", strtotime($user->date)), $content);
                $content = str_replace("{lastlogin}", date("F-m-d H:i", strtotime($user->lastlogin)), $content);

                $mail["to"]=$user->email;
                $mail["subject"] = $_POST["subject"];              
                $mail["message"] = "<td class='column' style='padding: 0;vertical-align: top;text-align: left'>
                                     <div>
                                        <div class='column-top' style='font-size: 50px;line-height: 50px'>&nbsp;</div>
                                     </div>
                                     <table class='contents' style='border-collapse: collapse;border-spacing: 0;width: 100%'>
                                        <tbody>
                                           <tr>
                                              <td class='padded' style='padding: 0;vertical-align: top;padding-left: 50px;padding-right: 50px'>
                                                $content
                                              </td>
                                           </tr>
                                        </tbody>
                                     </table>
                                     <div class='column-bottom' style='font-size: 26px;line-height: 26px'>&nbsp;</div>
                                  </td>";

                Main::send($mail);   
              }
            }
            return Main::redirect(Main::ahref("tools/newsletter","",FALSE),array("success","Your custom newsletter was sent."));               

          }
        }
        /**
         * Send Digest
         * @author KBRmedia
         * @since  1.1
         */
        private function tools_newsletter_digest(){
          if(isset($_POST["token"])){
            // Disable if demo
            if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));
            // Validate Token
            if(!Main::validate_csrf_token($_POST["token"])){
              Main::redirect(Main::ahref("tools/newsletter","",FALSE),array("danger","Invalid token, please try again."));
              return;
            }     
            $data = $this->db->get("media", array("approved = 1"), array("order" => "id","limit" => 3));
            $content = "";
            
            foreach ($data as $media) {
              $media = $this->formatMedia($media);
                $content .="<td class='column first' style='padding: 0;vertical-align: top;text-align: left;width: 200px'>
                             <div class='image' style='font-size: 0;Margin-bottom: 18px' align='center'>
                                <a href='{$media->url}'><img class='gnd-corner-image gnd-corner-image-center gnd-corner-image-top' style='border: 0;-ms-interpolation-mode: bicubic;display: block;max-width: 300px' src='{$media->thumb}' alt='{$media->title}' alt='' width='200' /></a>
                             </div>
                             <table class='contents' style='border-collapse: collapse;border-spacing: 0;width: 100%'>
                                <tbody>
                                   <tr>
                                      <td class='padded' style='padding: 0;vertical-align: top;padding-left: 50px;padding-right: 10px'>
                                         <h1 style='Margin-top: 0;color: #3b3e42;font-weight: 400;font-size: 24px;Margin-bottom: 16px;font-family: Avenir,sans-serif;line-height: 30px'>{$media->title}</h1>
                                         <p style='Margin-top: 0;color: #60666d;font-size: 12px;font-family: sans-serif;line-height: 18px;Margin-bottom: 18px'>".Main::truncate($media->description, 100)."</p>
                                      </td>
                                   </tr>
                                </tbody>
                             </table>
                             <table class='contents' style='border-collapse: collapse;border-spacing: 0;width: 100%'>
                                <tbody>
                                   <tr>
                                      <td class='padded' style='padding: 0;vertical-align: top;padding-left: 50px;padding-right: 10px'>
                                         <div class='btn' style='Margin-bottom: 21px'>
                                            <a style='mso-hide: all;border: 0;border-radius: 4px;display: inline-block;font-size: 10px;font-weight: 700;line-height: 16px;padding: 5px 17px 5px 17px;text-align: center;text-decoration: none;color: #fff;background-color: #444;box-shadow: 0 3px 0 #363636;font-family: sans-serif' href='{$media->url}'>View Media</a>
                                         </div>
                                      </td>
                                   </tr>
                                </tbody>
                             </table>
                             <div class='column-bottom' style='font-size: 32px;line-height: 32px'>&nbsp;</div>
                            </td>";                
            }
            // Get Users
            $users = $this->db->get("user",array("active" => "1"));
            foreach ($users as $user) {
              if(!empty($user->email)){
                $mail["to"] = $user->email;
                $mail["subject"]= "Weekly Digest from {$this->config["title"]}";              
                $mail["message"]= $content;
                Main::send($mail);   
              }
            }
           return Main::redirect(Main::ahref("tools/newsletter","",FALSE),array("success","Digests have been sent."));               
          }
        }        
    /**
      * Optimize Database
      * @since 1.0
      */    
    protected function tools_optimize(){
      // Disable this for demo
      if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));
      $this->db->delete("temp", "viewed = '1' AND type='notification' AND date < SUBDATE(CURDATE(), INTERVAL 1 MONTH)");
      $this->db->run("OPTIMIZE TABLE  `{$this->config["prefix"]}category` ,
                                      `{$this->config["prefix"]}comment` ,
                                      `{$this->config["prefix"]}favorite` ,
                                      `{$this->config["prefix"]}media` ,
                                      `{$this->config["prefix"]}page` ,
                                      `{$this->config["prefix"]}point` ,
                                      `{$this->config["prefix"]}rating` ,
                                      `{$this->config["prefix"]}setting` ,
                                      `{$this->config["prefix"]}subscription` ,
                                      `{$this->config["prefix"]}temp` ,
                                      `{$this->config["prefix"]}user`");
      Main::redirect(Main::ahref("","",FALSE),array("success","Database has been optimized."));
    }   
  /**
   * Ads
   * @author KBRmedia
   * @since  1.0
   */
  /**
   * Pages
   * @since 1.1
   **/
  protected function ads(){
    // Toggle
    if(in_array($this->do, array("edit","delete","add"))){
      $fn = "ads_{$this->do}";
      return $this->$fn();
    }       
    if(isset($_GET["filter"]) && in_array($_GET["filter"], array("impression", "enabled", "type"))){
      $order = array("order"=> $_GET["filter"]);
    }else{
      $order = array("order"=>"id");
    }
    $ads = $this->db->get("ads","",$order);
    $count = $this->db->rowCountAll;
    Main::set("title","Manage Advertisment");
    $this->header();
    include($this->t("ads"));
    $this->footer();
  }
      /**
       * Add Ad
       * @since 1.1
       **/
      private function ads_add(){
        // Process Data
        if(isset($_POST["token"])){
          // Disable if demo
          if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));
          // Validate Results
          if(!Main::validate_csrf_token($_POST["token"])){
            return Main::redirect(Main::ahref("ads/add","",FALSE),array("danger","Something went wrong, please try again."));
          }

          if(!empty($_POST["code"])){
            // Prepare Data
            $data = array(
              ":name" => Main::clean($_POST["name"],3),
              ":code" => $_POST["code"],
              ":type" => ad_type($_POST["type"]) ? $_POST["type"] : "728",
              ":enabled" => in_array($_POST["enabled"],array("0","1")) ? Main::clean($_POST["enabled"],3):"0"
              );         

            $this->db->insert("ads",$data);
            return Main::redirect(Main::ahref("ads","",FALSE),array("success","Advertisment has been added."));        
          }
          Main::redirect(Main::ahref("ads/add","",FALSE),array("danger","Please make sure that you fill everything correctly."));            
        }
      
        $header="Add an Advertisment";
        $content="       
        <form action='".Main::ahref("ads/add")."' method='post' class='form-horizontal' role='form'>
          <div class='form-group'>
            <label for='title' class='col-sm-3 control-label'>Name</label>
            <div class='col-sm-9'>
              <input type='text' class='form-control' name='name' id='title' value=''>
            </div>
          </div> 
          <div class='form-group'>
            <label for='type' class='col-sm-3 control-label'>Ad type</label>
            <div class='col-sm-9'>
              <select name='type'>
                <option value='728'>728x90</option>
                <option value='468'>468x60</option>
                <option value='300'>300x250</option>
                <option value='resp'>Responsive</option>
                <option value='preroll'>Media Pre-roll</option>
              </select>
            </div>
          </div>              
          <div class='form-group'>
            <label for='code' class='col-sm-3 control-label'>Ad Code</label>
            <div class='col-sm-9'>
              <textarea class='form-control' id='code' name='code' rows='10'></textarea>
            </div>
          </div>                          
          <hr />
          <ul class='form_opt' data-id='enabled'>
            <li class='text-label'>Enable Advertisment<small>Do you want to enable this ad?</small></li>
            <li><a href='' class='last' data-value='0'>No</a></li>
            <li><a href='' class='first current' data-value='1'>Yes</a></li>
          </ul>
          <input type='hidden' name='enabled' id='enabled' value='1' />  

          ".Main::csrf_token(TRUE)."
          <input type='submit' value='Add Advertisment' class='btn btn-primary' />";

        $content.="</form>";
        Main::set("title","Add an Advertisment");
        $this->header();
        include($this->t("template"));
        $this->footer();       
      }  
      /**
       * Edit Ad
       * @since 1.1
       **/
      private function ads_edit(){
        // Add User
        if(isset($_POST["token"])){
          // Disable if demo
          if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));
          // Validate Results
          if(!Main::validate_csrf_token($_POST["token"])){
            return Main::redirect(Main::ahref("ads/edit/{$this->id}","",FALSE),array("danger","Something went wrong, please try again."));
          }

          if(!empty($_POST["code"])){
            // Prepare Data
            $data = array(
              ":name" => Main::clean($_POST["name"],3),
              ":code" => $_POST["code"],
              ":type" => ad_type($_POST["type"]) ? $_POST["type"] : "728",
              ":enabled" => in_array($_POST["enabled"],array("0","1")) ? Main::clean($_POST["enabled"],3):"0"
              );         
        
            $this->db->update("ads","",array("id"=>$this->id),$data);
            return Main::redirect(Main::ahref("ads/edit/{$this->id}","",FALSE),array("success","Advertisment has been edited."));        
          }
          return Main::redirect(Main::ahref("ads/edit/{$this->id}","",FALSE),array("danger","Please make sure that you fill everything correctly."));            
        }
        if(!$ad=$this->db->get("ads",array("id"=>"?"),array("limit"=>1),array($this->id))){
          return Main::redirect(Main::ahref("ads","",FALSE),array("danger","Advertisment doesn't exist."));
        }
        // Add CDN Editor
        $header="Edit Advertisment";
        $content="       
        <form action='".Main::ahref("ads/edit/{$this->id}")."' method='post' class='form-horizontal' role='form'>
          <div class='form-group'>
            <label for='name' class='col-sm-3 control-label'>Name</label>
            <div class='col-sm-9'>
              <input type='text' class='form-control' name='name' id='name' value='{$ad->name}'>
            </div>
          </div>  
          <div class='form-group'>
            <label for='type' class='col-sm-3 control-label'>Ad type</label>
            <div class='col-sm-9'>
              <select name='type'>
                <option value='728' ".($ad->type == "728" ? "selected" : "").">728x90</option>
                <option value='468' ".($ad->type == "468" ? "selected" : "").">468x60</option>
                <option value='300' ".($ad->type == "300" ? "selected" : "").">300x250</option>
                <option value='resp' ".($ad->type == "resp" ? "selected" : "").">Responsive</option>
                <option value='preroll' ".($ad->type == "preroll" ? "selected" : "").">Media Pre-roll</option>
              </select>
            </div>
          </div>    
          <div class='form-group'>
            <label for='code' class='col-sm-3 control-label'>Ad Code</label>
            <div class='col-sm-9'>
              <textarea class='form-control' id='code' name='code' rows='10'>{$ad->code}</textarea>
            </div>
          </div>           
          <hr />
          <ul class='form_opt' data-id='enabled'>
            <li class='text-label'>Enable Advertisment<small>Do you want to enable this ad?</small></li>
            <li><a href='' class='last".(!$ad->enabled?' current':'')."' data-value='0'>No</a></li>
            <li><a href='' class='first".($ad->enabled?' current':'')."' data-value='1'>Yes</a></li>
          </ul>
          <input type='hidden' name='enabled' id='enabled' value='{$ad->enabled}' />  
      
          ".Main::csrf_token(TRUE)."
          <input type='submit' value='Edit Advertisment' class='btn btn-primary' />";

        $content.="</form>";
        Main::set("title","Edit Advertisment");
        $this->header();
        include($this->t("template"));
        $this->footer();       
      }
      /**
       * Delete Ad
       * @since 1.1
       **/
      private function ads_delete(){
        // Disable if demo
        if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));            
        // Delete single URL
        if(!empty($this->id) && is_numeric($this->id)){
          // Validated Single Nonce
          if(Main::validate_nonce("delete_ad-{$this->id}")){
            $this->db->delete("ads",array("id"=>"?"),array($this->id));
            return Main::redirect(Main::ahref("ads","",FALSE),array("success","Advertisment has been deleted."));
          }        
        } 
        return Main::redirect(Main::ahref("ads","",FALSE),array("danger","An unexpected error occurred."));          
      }
  /**
   * Server Requests
   * @since 1.0
   **/
  private function server(){
    // Valid Token    
    if(in_array($this->do, array("import","search","menu"))){
      $fn = __FUNCTION__."_{$this->do}";
      return $this->$fn();
    }         
    return $this->server_error();
  }
    /**
     * Server Error
     * @since 1.0
     **/
    private function server_error(){
      return die(header('HTTP/1.1 400 Bad Request', true, 400));
    }  
    /**
     * Import video from Youtube
     * @since 1.5.1
     **/
    private function server_import(){
      // Disable if demo
      if($this->config["demo"]) return json_encode(array("error"=>1,"msg"=>"Disabled in demo."));
      // Validate Request
      if(!isset($_POST["token"]) || $_POST["token"]!=md5($this->config["public_token"])) return $this->server_error();

      require(ROOT."/includes/Media.class.php");
      $import = new Media(
                      array(
                          "yt_api" => $this->config["yt_api"],
                          "vm_api" => $this->config["vm_api"]
                          )
                      );
      $media = $import->import($_POST["url"],"100%","500");

      if($mediaDB = $this->db->get("media",array("url"=>"?"),array("limit" => 1),array(Main::slug($media->title)))){
        $return = json_encode(array("error"=>1,"msg"=>"<a href='".Main::href("view/".Main::slug($mediaDB->title))."' class='btn btn-sm btn-danger pull-right' target='_blank'>Already in database!</a>"));
        exit($return);
      }
      $unique=$this->uniqueid();

      // Prepare array of data         
      $data = array(
        ":type" => types($_POST["type"]) ? $_POST["type"] : 'video', 
        ':catid' => $_POST["cat"],
        ':featured' => $_POST["feat"],
        ':uniqueid'=> $unique, 
        ':duration' => $media->duration,
        ':title'=> Main::clean($media->title,3), 
        ':url'=> Main::slug($media->title),
        ':description'=> Main::clean($media->desc,3), 
        ':thumb'=> md5($unique).".jpg",
        ':embed'=> $media->code, 
        ':userid'=> $this->user->id,
        ':tags'=> $media->tag,
        ':date'=> "NOW()",
        ":source" => $_POST["url"]
      );
      // Thumbnail
      if(!$this->config["local_thumbs"]){
        $data[":ext_thumb"] = $media->thumb;
        unset($data[":thumb"]);
      }else{
        copy($media->thumb, ROOT."/content/thumbs/".md5($unique).".jpg");
      }
      // Store
      if($this->db->insert("media",$data)){
        $this->db->update("setting","value = value + 1",array("config"=>"?"),array("count_media"));
        $return = json_encode(array("error"=>0,"msg"=>"<a href='".Main::href("view/".Main::slug($media->title)."/$unique","view/".Main::slug($media->title))."/$unique' class='btn btn-sm btn-success pull-right' target='_blank'>View Video</a>"));
        exit($return);          
      }
        $return = json_encode(array("error"=>1,"msg"=>"Unknown error!"));
        exit($return);
    } 
    /**
     * Ajax Media Search
     * @return string return formatted html
     * @since 1.0
     */
    private function server_search(){
      $q = Main::clean($_POST["q"],3,TRUE);
      $videos=$this->db->search("media",array("title"=>":q","description"=>":q","tags"=>":q"),array("count"=>1,"order"=>"id","limit"=>25),array(":q"=>"%$q%"));
      if(!$videos){
        echo "<h3>No results found</h3> <p>Your keyword did not match any results. Please try a different keyword.</p>";
      }else{
        echo "<h4>Results for <strong>$q</strong></h4>";
        echo "<ul class='medialist'>";
                foreach ($videos as $media){
            echo "<li>";
                    if($this->config["local_thumbs"]){
              echo "<img src='{$this->config['url']}/content/thumbs/{$media->thumb}' alt='{$media->title}'/>";
                    }else{
              echo "<img src='{$media->ext_thumb}' alt='{$media->title}'/>";                      
                    }
              echo "<a class='overlay' href='".Main::href("view/{$media->url}")."' target='_blank'>
                      <span>".Main::truncate($media->title,25)."</span>
                      <span>Views: {$media->views}</span>
                      <span>Likes / Dislikes: {$media->likes} / {$media->dislikes}</span>
                      <center><strong>Click to view this video</strong></center>
                    </a>
                    <div class='titles'>".ucfirst($media->type)."</div>                       
                    <div class='options'>
                      <a href='".Main::ahref("media/edit/{$media->id}")."' title='Edit' class='edit btn btn-xs btn-primary'>Edit</a>
                      <a href='".Main::ahref("media/delete/{$media->id}").Main::nonce("delete_media-{$media->id}")."' title='Delete this video' class='delete btn btn-xs btn-danger'>Delete</a>
                    </div>         
                  </li>";
                }
        echo "</ul>";
      }
    }
    /**
     * Add to menu
     * @return menu
     * @since  1.3
     */
    private function server_menu(){
      $menus = array();
      foreach ($_POST["menu"] as $j => $menu) {
        if(!is_numeric($j)) continue;
        $menu = json_decode($menu, TRUE);
        $menus[$j] = array(
                  "text" => $menu["text"],
                  "href" => $menu["href"],
                  "icon" => $menu["icon"]
                ); 
          if(isset($_POST["menu"]["child-$j"])){
            foreach ($_POST["menu"]["child-$j"] as $sub) {
              $sub = json_decode($sub, TRUE);
              $menus[$j]["child"][] = array(
                                    "text" => $sub["text"],
                                    "href" => $sub["href"],
                                    "icon" => $sub["icon"]
                                  );
            }
          }                         
      }
      if(empty($menus)){
        $value = "";
      }else{
        if (version_compare(phpversion(), '5.4', '>=')) {
          $value = json_encode($menus, JSON_UNESCAPED_UNICODE);
        }else{
          $value = json_encode($menus);
        }        
      }
      if($this->db->update("setting",array("value"=>"?"),array("config"=>"?"),array($value,"menus"))){        
        echo 1;
      }else {
        echo 0;
      }
    }
  /**
   * Update Notification  
   * @since 1.0
   */
  public function update_notification(){
    if($this->config["update_notification"]){
      $c=Main::http("http://gempixel.com/update.php?p=".md5('media'));
      $c=json_decode($c,TRUE);
      if(isset($c["status"]) && $c["status"]=="ok"){
        if(_VERSION < $c["current_version"]){
          return "<div class='alert alert-success'>This script has been updated to version {$c["current_version"]}. Please download it from <a href='http://codecanyon.net/downloads' target='_blank' class='button green small'>CodeCanyon</a></div>";
        }
      }
    }
  }      

  ///////////////////////////////////////////////////////////////////////
  // Admin helper methods: Please don't edit anything below this line! //
  ///////////////////////////////////////////////////////////////////////
  /**
   * Header
   * @since 1.0
   **/
  protected function header(){
    include($this->t(__FUNCTION__));
  }
  /**
   * Footer
   * @since 1.0
   **/
  protected function footer(){
    include($this->t(__FUNCTION__));
  }
  /**
   * Format single media data
   * @author KBRmedia
   * @since  1.3.1
   * @param  object $media Media data
   * @return object
   */
  protected function formatMedia($media){
    // Format URLs
    $media->url = Main::href("view/{$media->url}");
    // Get correct thumbnail path
    if($this->config["local_thumbs"] || empty($media->ext_thumb)){
      if(empty($media->thumb)){
        $media->thumb = $media->ext_thumb;
      }else{
        $media->thumb = "{$this->config["url"]}/content/thumbs/{$media->thumb}";
      }
    }else{
      $media->thumb = $media->ext_thumb;
    }
    unset($media->ext_thumb);
    // Get Media Player
    if($media->type == "picture"){
      if(empty($media->file)){
        $media->player = "<img src='{$media->source}' alt='{$media->title}'/>";
        $media->code = "<a href='{$media->url}' alt='{$media->title}'><img src='{$media->source}' alt='{$media->title}' /></a>";
      }else{
        $media->player = "<img src='{$this->config["url"]}/content/media/{$media->file}' alt='{$media->title}'/>";
        $media->code = "<a href='{$media->url}' alt='{$media->title}'><img src='{$this->config["url"]}/content/media/{$media->file}' alt='{$media->title}' /></a>";
      }     
    }else{
      $media->player = "<iframe src='".Main::href("embed/{$media->uniqueid}")."' frameborder='0' width='100%' height='450' scrolling='no' allowfullscreen></iframe>";
      $media->code = "<iframe src='".Main::href("embed/{$media->uniqueid}")."' frameborder='0' width='550' height='350' scrolling='no' allowfullscreen></iframe><p><a style='color: #000; text-decoration: none;' href='{$media->url}' title='{$media->title}'>{$media->title}</a></p>";
    }   
    // Format numbers
    $media->rating = $media->votes > 0 ? round($media->likes / $media->votes,1)*5 : 0;
    $media->views = number_format($media->views, 0, ".", ",");
    $media->votes = number_format($media->votes, 0, ".", ",");
    $media->likes = number_format($media->likes, 0, ".", ",");
    $media->dislikes = number_format($media->dislikes, 0, ".", ",");
    $media->date = Main::timeago($media->date);
    $media->tags_html = "";
    // Format Tags
    if(!empty($media->tags)){
      $tags = explode(",", $media->tags);
      $media->tags_html = "<p>
                        <strong>".e("Tags")."</strong>";
      foreach ($tags as $tag) {
        if(empty($tag)) continue;
        $media->tags_html .="<a href='".Main::href("search/".urlencode($tag))."' class='btn btn-info video-tag'>$tag</a> ";
      }           
    }
    
    if(isset($media->name)){
      // Format Author Data
      $profile = json_decode($media->name);
      if(is_object($profile)){
        $media->author = ucfirst($profile->name);   
      }
      $media->profile = Main::href("user/{$media->username}");  
    }
    return $media;
  }

  /**
   *  Generate & Validate Unique ID
   *  @param none
   *  @return valid id
   *  @since 1.0
   */ 
  protected function uniqueid(){
    $l=5; 
    $i=0; 
    while(1) {
      if($i >='100') { $i=0; $l=$l+1; };
      $unique=Main::strrand($l);
      if(!$this->db->get("media",array("uniqueid"=>$unique))) {
        return $unique;
        break;
      }
      $i++;
    }   
  }    
  public function avatar($user, $size = 48){
    if(empty($user->avatar)){
      return "http://gravatar.com/avatar/".md5($user->email)."?s={$size}&r=pg&d=mm";
    }else{
      return $this->config["url"]."/content/user/{$user->avatar}";
    }
  }  
  /**
   * Media Type
   * @since 1.0
   **/
  protected function type($t="type"){
    $array = types();
    if(isset($_POST[$t]) && in_array($_POST[$t], $array)) return $_POST[$t];
    return "video";
  }  
  /**
   * Template File
   * @since 1.0
   **/
  protected function t($file){
    if(file_exists(ROOT."/admin/system/$file.php")){
      return ROOT."/admin/system/$file.php";
    }else{
      die("<div class='alert alert-danger'>Template file '$file.php' not found. Please check this out.</div>");
    }
  }
  /**
   * Copy Folder
   * @since 1.0
   **/  
  protected function copy_folder($src,$dst) { 
    // Disable this for demo
    if($this->config["demo"]) return Main::redirect("admin",array("danger","Feature disabled in demo."));    
    $dir = opendir($src); 
    @mkdir($dst); 
    while(false !== ( $file = readdir($dir)) ) { 
      if (( $file != '.' ) && ( $file != '..' )) { 
        if ( is_dir($src . '/' . $file) ) { 
         $this->copy_folder($src . '/' . $file,$dst . '/' . $file); 
        } 
        else { 
          copy($src . '/' . $file,$dst . '/' . $file); 
        } 
      } 
    } 
    closedir($dir); 
  }   
  // End of File
}