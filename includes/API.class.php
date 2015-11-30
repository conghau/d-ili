<?php 
/**
 * ====================================================================================
 *                           PremiumMediaScript (c) KBRmedia
 * ----------------------------------------------------------------------------------
 * @copyright This software is exclusively sold at CodeCanyon.net. If you have downloaded this
 *            from another site or received it from someone else than me, then you are engaged
 *            in an illegal activity. You must delete this software immediately or buy a proper
 *            license from http://codecanyon.net/user/KBRmedia/portfolio?ref=KBRmedia.
 * ====================================================================================
 *
 *
 * @author KBRmedia (http://gempixel.com)
 * @link http://gempixel.com 
 * @license http://gempixel.com/license
 * @package PremiumMediaScript
 * @subpackage API Handler
 */
class API extends App{
	/**
	 * Allowed actions
	 * @var array
	 */
	protected $actions = array("search","view","channel","user");
	/**
	 * Class Constructer 
	 */
	public function __construct($config, $db, $action){
  	$this->config = $config;
  	$this->db = $db;
  	$this->do = $action[0];
  	$this->id = $action[1];
  	// Clean Request
  	if(isset($_GET)) $_GET = array_map("Main::clean", $_GET);
		if(isset($_GET["page"]) && is_numeric($_GET["page"]) && $_GET["page"]>0) $this->page = Main::clean($_GET["page"]);
		$this->http = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://":"http://");		
		$this->check();	
		$this->index();
	}
	/**
	 * Index
	 * @since  1.5
	 */
	protected function index(){
		// Check KEY
		if(empty($_GET["key"]) || $_GET["key"] !== $this->config["api_key"]){
			return $this->error("002");
		}
		// Run Methods
		if(!empty($this->do)){
			if(in_array($this->do, $this->actions) && method_exists(__CLASS__, $this->do)){
				// Run Method
				return $this->{$this->do}();
			}				
		}		
		// Run Error
		return $this->error("001");
	}
	/**
	 * Media Endpoint
	 * @since 1.5
	 */
	protected function view(){
		if(isset($this->id)){
			if($media = $this->db->get(array("custom" => "{$this->config["prefix"]}media.*, {$this->config["prefix"]}user.username as username, {$this->config["prefix"]}user.profile as name FROM `{$this->config["prefix"]}media` INNER JOIN `{$this->config["prefix"]}user` ON {$this->config["prefix"]}user.id = {$this->config["prefix"]}media.userid"),array("uniqueid" => "?"),array("limit" => 1), array($this->id))){
				return $this->build(
									array(
											"status" => "ok",
											"data" => $this->format($media)
										)
								);
			}
		}
		return $this->error("003");
	}
	/**
	 * Search Endpoint
	 * @since 1.5
	 */
	protected function search(){
		if(isset($this->id) || strlen($this->id) > 3){
			$limit = isset($_GET["limit"]) && is_numeric($_GET["limit"]) && $_GET["limit"] > 0 && $_GET["limit"] < 25 ? $_GET["limit"] : 10;
			$order = isset($_GET["order"]) && in_array($_GET["order"], array("date","votes","views")) ? $_GET["order"] : "date";

			if($media = $this->db->search("media",array("title" => "?"), array("limit" => $limit, "order" => $order, "limit" => (($this->page-1)*$limit).", {$limit}", "count" => TRUE), array("%{$this->id}%"))){
		    if(($this->db->rowCount%$limit)<>0) {
		      $max = floor($this->db->rowCount/$limit)+1;
		    } else {
		      $max = floor($this->db->rowCount/$limit);
		    } 				
				return $this->build(
									array(
											"status" => "ok",
											"results" => $this->db->rowCount,
											"prev" => ($this->page-1),
											"next" =>  $this->page < $max ? $this->page + 1 : null,
											"data" => $this->format($media)
										)
								);
			}
		}
		return $this->error("004");		
					
	}	
	/**
	 * User Endpoint
	 * @since 1.5
	 */
	protected function user(){
		if(isset($this->id) || strlen($this->id) > 3){			
			
			$user = $this->db->get("user",array("username" => "?"),array("limit" => 1),array($this->id));
			$limit = isset($_GET["limit"]) && is_numeric($_GET["limit"]) && $_GET["limit"] > 0 && $_GET["limit"] < 25 ? $_GET["limit"] : 10;
			$order = isset($_GET["order"]) && in_array($_GET["order"], array("date","votes","views")) ? $_GET["order"] : "date";

			if($media = $this->db->search("media",array("userid" => $user->id), array("limit" => $limit, "order" => $order, "limit" => (($this->page-1)*$limit).", {$limit}", "count" => TRUE))){
		    if(($this->db->rowCount%$limit)<>0) {
		      $max = floor($this->db->rowCount/$limit)+1;
		    } else {
		      $max = floor($this->db->rowCount/$limit);
		    } 				
		     $profile = json_decode($user->profile);
				return $this->build(
									array(
											"status" => "ok",
											"results" => $this->db->rowCount,
											"prev" => ($this->page-1),
											"next" =>  $this->page < $max ? $this->page + 1 : null,
											"author" => array(
																		"name" => $profile->name,
																		"description" => $profile->description,
																		"cover" => in_array($profile->cover, array("cover-1.jpg","cover-2.jpg","cover-3.jpg")) ? $this->config["url"]."/static/covers/{$profile->cover}" : $this->config["url"]."/static/users/{$profile->cover}",
																		"avatar" => $this->config["url"]."/content/users/{$user->avatar}"
																	),
											"data" => $this->format($media)
										)
								);
			}			
		}
		return $this->error("003");
	}
	/**
	 * Format Video
	 * @since 1.5
	 */
	private function format($media){
		if(is_array($media)){
			foreach ($media as $m) {
				$m = $this->formatMedia($m);	
				$new = new stdClass;

				$new->id = $m->uniqueid;
				$new->url = $m->url;
				$new->title = $m->title;
				$new->description = $m->description;
				$new->duration = $m->duration;
				$new->votes = $m->votes;
				$new->views = $m->views;
				$new->likes = $m->likes;
				$new->comments = $m->comments;
				$new->embed = $m->player;

				$data[] = $new;
			}
			return $data;
		}else{
			$media = $this->formatMedia($media);	
			$new = new stdClass;

			$new->id = $media->uniqueid;
			$new->url = $media->url;
			$new->title = $media->title;
			$new->description = $media->description;
			$new->duration = $media->duration;
			$new->votes = $media->votes;
			$new->views = $media->views;
			$new->likes = $media->likes;
			$new->comments = $media->comments;
			$new->embed = $media->code;

			$new->author = new stdClass;
			$new->author->name = $media->author;
			$new->author->link = $media->profile;	
			return $new;
		}
	}
	/**
	 * API Build
	 * @since 1.5
	 **/
	private function build($array,$text=""){
		header("content-type: application/javascript");
		// JSONP Request
		if(isset($_GET["callback"])){
			return print("{$_GET["callback"]}(".json_encode($array).")");
		}
		// JSON
		return print(json_encode($array));		
	}	
	/**
	 * Error Codes
	 */
	private function error($code){
		$errors = array(
				"001" => "Please check documentation to use the API properly.",
				"002" => "The API key is not valid or not authorized to access resources.",				
				"003" => "Resource not available. Please try again.",
				"004" => "Resource not available or keyword less than 3 characters. Please try again.",
			);
		// Run Error
		return $this->build(
								array(
										"status" => "error",
										"code" => $code,
										"message" => $errors[$code]
									)
						);		
	}
	// End of File
}