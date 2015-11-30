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
 * @subpackage User Handler
 */
class User extends App{
	/**
	 * Allowed actions
	 * @var array
	 */
	protected $actions = array("login","logout","settings","register","account","forgot","activate");
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
		$this->run();
	}
	/**
	 * User Handler
	 * @author KBRmedia
	 * @since  1.0
	 */
	public function run(){
		if(!empty($this->do)){
			if(in_array($this->do, $this->actions) && method_exists(__CLASS__, $this->do)){
				// Run Method
				return $this->{$this->do}();
			}			
			// Get User's Profile
			if($user = $this->db->get("user",array("username" => "?"),array("limit" => 1),array($this->do))){
				return $this->profile($user);
			}			
		}
		if($this->logged()) return Main::redirect(Main::href("user/account","",FALSE));
		return $this->_404();
	}
	/**
	 * User Login
	 * @author KBRmedia
	 * @since 1.5.1
	 **/
	protected function login(){
		// If Logged
		if($this->logged()) return Main::redirect(Main::href("user/account","", FALSE));

		if(!empty($this->id)){
			// Check if private
			if($this->config["maintenance"]) Main::redirect("?error",array("danger", e("Sorry, we are not accepting users right now.")));	
			// Get method		
			$fn = "login_{$this->id}";
			if(in_array($this->id, array("facebook","google","twitter")) && method_exists("User",$fn)){				
				return $this->$fn();
			}else{
				return $this->_404();
			}
		}
		// Login Count
		if(!isset($_SESSION["login_count"])){
			$_SESSION["login_count"]=0;
		}
		// Check if form is posted
		if(isset($_POST["token"])){
			// Prevent Bots from submitting the form
			if(Main::bot()) return $this->_404();			
			// Validate CSRF Token
			if(!Main::validate_csrf_token($_POST["token"])){
				return Main::redirect(Main::href("user/login","",FALSE),array("danger",e("Invalid token. Please try again.")));
			}
			// Clean Current Session
			$this->logout(FALSE);
			// Block User
			if(Main::cookie("__bl")){
				return Main::redirect(Main::href("user/login","",FALSE),array("danger",e("You have been blocked for 1 hour due to many unsuccessful login attempts.")));
			}			
			// Validate Email
			if(empty($_POST["email"])) return Main::redirect(Main::href("user/login","",FALSE),array("danger",e("Please enter a valid email or username.")));
			
			// Validate Password
			if(empty($_POST["password"]) || strlen($_POST["password"])<5) return Main::redirect(Main::href("user/login","",FALSE),array("danger",e("Wrong email and password combination.")));
			
			// Check if user exists - Check username and email
			if(!Main::email($_POST["email"])){
				$user = $this->db->get("user",array("username"=>"?"),array("limit"=>1),array($_POST["email"]));
			}else{
				$user = $this->db->get("user",array("email"=>"?"),array("limit"=>1),array($_POST["email"]));
			}		
			if(!$user){
				return Main::redirect(Main::href("user/login","",FALSE),array("danger",e("Wrong email and password combination")));
			}			
			// Upgrade password from MD5
			if($user->password===md5($this->config["security"].Main::clean($_POST["password"],3,FALSE))){
				$this->db->update("user",array("password"=>"?"),array("id"=>$user->id),array(Main::encode($_POST["password"])));
			}else{
				// Check new Password
				if(!Main::validate_pass($_POST["password"],$user->password)){
					// Login Attempt Count
					$max=5;
					$_SESSION["login_count"]++;
					if($_SESSION["login_count"] >= $max){
						// Block user for 1 hour
						Main::cookie("__bl",1,60);
					}		
					return Main::redirect(Main::href("user/login","",FALSE),array("danger",e("Wrong email and password combination")));
				}
			}
			// Check Auth Key: If empty generate one
			if(empty($user->auth_key)){	
				$user->auth_key = Main::encode(Main::strrand(12));
				// Update database
				$this->db->update("user",array("auth_key"=>"?"),array("id"=>$user->id),array($user->auth_key));
			}

			// Check if inactive
			if(!$user->active){
				return Main::redirect(Main::href("user/login","",FALSE),array("danger",e("You haven't activated your account. Please check your email for the activation link. If you haven't received any emails from us, please contact us.")));
			}
			// Log Last login
			$this->db->update("user",array("lastlogin"=>"NOW()"),array("id"=>$user->id));
			// Set Session
			$json = base64_encode(json_encode(array("loggedin"=>TRUE,"key"=>$user->auth_key.$user->id)));
			if(isset($_POST["rememberme"]) && $_POST["rememberme"]=="1"){
				// Set Cookie for 14 days
				setcookie("login",$json, time()+60*60*24*14, "/","",FALSE,TRUE);
			}else{
				$_SESSION["login"]=$json;
			}
			// Return to /user or custom redirect
			if(isset($_POST["next"])){
				$_POST["next"] = str_replace($this->config["url"],"", Main::clean($_POST["next"], 3, TRUE));
			}else{
				$_POST["next"] = "";
			}
			return Main::redirect($_POST["next"],array("success", e("You have been successfully logged in.")));
		}

		// Set meta info
		Main::set("title", e("Login to your account"));
		Main::set("description","Login to your account and upload your favorite videos.");		
		Main::set("url","{$this->config["url"]}/user/login");	

		$this->headerShow=FALSE;
		$this->footerShow=FALSE;
		$this->header();
		include($this->t(__FUNCTION__));
		$this->footer();		
	}
			/**
			 * User Login with Facebook
			 * @author KBRmedia
			 * @since 1.5.1
			 **/
			private function login_facebook(){
		    //Facebook Auth
		    if(!$this->config["user"] || !$this->config["fb_connect"] || empty($this->config["facebook_app_id"])) Main::redirect("",array("danger",e("Sorry, Facebook connect is not available right now.")));
		    if(isset($_GET["error"])) Main::redirect("",array("danger",e("You must grant access to this application to use your facebook account.")));

				include 'library/auth/autoload.php';
		    // Creating the facebook object  
		    Facebook\FacebookSession::setDefaultApplication($this->config["facebook_app_id"], $this->config["facebook_secret"]);

				$helper = new Facebook\FacebookRedirectLoginHelper(Main::href("user/login/facebook"));
				try {
				  $session = $helper->getSessionFromRedirect();
				} catch(FacebookRequestException $ex) {
				  return Main::redirect("",array("danger",e("An error has occured. Please try again later.")));
				} catch(\Exception $ex) {
				  return Main::redirect("",array("danger",e("An error has occured. Please try again later.")));
				}
				if ($session) {
				  // Logged in
				  $request = new Facebook\FacebookRequest($session, 'GET', '/me?fields=email,name');
		 			$user_profile = $request->execute()->getGraphObject(Facebook\GraphUser::className());
					$fb = $user_profile->asArray();

					if(!isset($fb["email"])) Main::redirect("",array("danger",e("You must grant permission to this application to use your profile information.")));
		      // Check if email is already taken
		      if($this->db->get("user","auth!='facebook' AND email='{$fb["email"]}'",array("limit"=>1))){
		      	 return Main::redirect("user/login",array("danger",e("The email linked to your account has been already used. If you have used that, please login to your existing account otherwise please contact us."))); 
		      }

		      // Let's see if the user is registered
		      if($user=$this->db->get("user","auth='facebook' AND (email='{$fb["email"]}' OR authid='{$fb["id"]}')",array("limit"=>1))){

						// Check Auth Key: If empty generate one
						if(empty($user->auth_key)){	
							$user->auth_key=Main::encode(Main::strrand(12));
							// Update database
							$this->db->update("user",array("auth_key"=>"?"),array("id"=>$user->id),array($user->auth_key));
						}
						// Inser AuthID
						if(empty($user->authid) && isset($fb["authid"])){	
							// Update database
							$this->db->update("user",array("authid"=>"?"),array("id"=>$user->id),array($fb["authid"]));
						}

						// Check if inactive
						if(!$user->active){
							return Main::redirect(Main::href("user/login","",FALSE),array("danger",e("You haven't activated your account. Please check your email for the activation link. If you haven't received any emails from us, please contact us.")));
						}

		      }else{		      	
		      	// Let's register the user
		      	$auth_key = Main::encode(Main::strrand(12));
		      	// Prepare Array
		      	$data = array(
		      			":email" => Main::clean($fb["email"],3,TRUE),
		      			":username" => isset($fb["username"]) ? Main::clean($fb["username"],3,TRUE) : "",
		      			":password" => Main::encode(Main::strrand(12)),
		      			":active" => "1",
		      			":date" => "NOW()",
		      			":auth" => "facebook",
		      			":authid" => isset($fb["id"]) ? Main::clean($fb["id"],3,TRUE) : "",
		      			":verifno" => Main::strrand(12),
		      			":auth_key" => $auth_key,
		      			":profile" => '{"name":"","description":"","cover":""}'
		      		);
		      	// Add Name
		      	if(isset($fb["name"])) $data[":name"] = $fb["name"];
		      	// Add Birthday
		      	if(isset($fb["birthday"])) $data[":dob"] = date("Y-m-d",strtotime($fb["birthday"]));
		      	// Save to DB
		      	if($this->db->insert("user",$data)){
							$user=$this->db->get("user",array("auth"=>"facebook","email"=>$fb["email"]),array("limit"=>1));    		
		      	}
		      }
					// Log Last login
					$this->db->update("user",array("lastlogin"=>"NOW()"),array("id"=>$user->id));		      
					// Ok Let's login te user
					$json=base64_encode(json_encode(array("loggedin"=>TRUE,"key"=>$user->auth_key.$user->id)));
					$_SESSION["login"]=$json;

					// Return to /user
					return Main::redirect("",array("success",e("You have been successfully logged in.")));
				}else{
					$loginUrl = $helper->getLoginUrl(array('scope' => 'email'));
		      header("Location: ".$loginUrl);  
		      return;
				}			 
			}
			/**
			 * User Login with Twitter
			 * @author KBRmedia
			 * @since 1.0
			 **/			
			private function login_twitter(){
				// Check for error
		    if(isset($_GET["denied"])) Main::redirect("",array("danger",e("You must grant permission to this application to use your twitter account.")));

		    if(!$this->config["user"] || !$this->config["tw_connect"]) Main::redirect("",array("danger",e("Sorry, Twitter connect is not available right now.")));
		    // Get Library
				require(ROOT."/includes/library/auth/twitter.php"); 

		    if(!empty($_GET['oauth_verifier']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret'])){

		      $twitteroauth = new TwitterOAuth($this->config["twitter_key"], $this->config["twitter_secret"], $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
		      $access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']); 
		      // Save it in a session var 
		      $_SESSION['access_token'] = $access_token; 
		      // Let's get the user's info 
		      $tw = $twitteroauth->get('account/verify_credentials');

		      if(!isset($tw->id)) Main::redirect("",array("danger",e("An error occured, please try again later.")));
		      // Let's see if the user is registered
		      if($user=$this->db->get("user","auth='twitter' AND authid='{$tw->id}'",array("limit"=>1))){

						// Check Auth Key: If empty generate one
						if(empty($user->auth_key)){	
							$user->auth_key=Main::encode(Main::strrand(12));
							// Update database
							$this->db->update("user",array("auth_key"=>"?"),array("id"=>$user->id),array($user->auth_key));
						}

						// Check if inactive
						if(!$user->active){
							return Main::redirect(Main::href("user/login","",FALSE),array("danger",e("You haven't activated your account. Please check your email for the activation link. If you haven't received any emails from us, please contact us.")));
						}

		      }else{
		      	if($this->db->get("user","auth='twitter' AND authid='{$tw->id}' AND username='{$tw->screen_name}'",array("limit"=>1))){
		      		$tw->screen_name = $tw->screen_name.rand(0,20);
		      	}
		      	// Let's register the user
		      	$auth_key = Main::encode(Main::strrand(12));
		      	$data = array(
		      			":email" => "",
		      			":username" => isset($tw->screen_name) ? Main::clean($tw->screen_name,3,TRUE) : "",
		      			":password" => Main::encode(Main::strrand(12)),
		      			":active" => "1",
		      			":date" => "NOW()",
		      			":auth" => "twitter",
		      			":authid" => isset($tw->id) ? Main::clean($tw->id,3,TRUE) : "",
		      			":verifno" => Main::strrand(12),
		      			":auth_key" => $auth_key,
		      			":profile" => '{"name":"","description":"","cover":""}'
		      		);
		      	if($this->db->insert("user",$data)){
							$user=$this->db->get("user",array("auth"=>"twitter","authid"=>$tw->id),array("limit"=>1));    		
		      	}
		      }
					// Log Last login
					$this->db->update("user",array("lastlogin"=>"NOW()"),array("id"=>$user->id));		      
					// Ok Let's login te user
					$json=base64_encode(json_encode(array("loggedin"=>TRUE,"key"=>$user->auth_key.$user->id)));
					$_SESSION["login"]=$json;

					// Return to /user
					return Main::redirect("",array("success",e("You have been successfully logged in.")));

		    }
		    // The TwitterOAuth instance  
		    $twitteroauth = new TwitterOAuth($this->config["twitter_key"],$this->config["twitter_secret"]); 
		    // Requesting authentication tokens, the parameter is the URL we will be redirected to  
		    $request_token = $twitteroauth->getRequestToken("{$this->config["url"]}/user/login/twitter");
		    // Saving them into the session  
		    $_SESSION['oauth_token'] = $request_token['oauth_token'];  
		    $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];  
		    // If everything goes well..  
		    if($twitteroauth->http_code==200){  
		        // Let's generate the URL and redirect  
		        $url = $twitteroauth->getAuthorizeURL($request_token['oauth_token']); 
		        header('Location: '. $url); 
		        exit;
		    } else { 
		      Main::redirect("user/login",array('danger','An error has occured! Please make sure that you have set up this application as instructed.'));  
		    }		    
			}
			/**
			 * User Login with Google
			 * @author KBRmedia
			 * @since 1.0
			 **/			
			private function login_google(){
				// Check to make sure Google Auth is enabled
				if(!$this->config["user"] || !$this->config["gl_connect"] || empty($this->config["google_cid"]) || empty($this->config["google_cs"])) {
					return Main::redirect("",array("danger",e("Sorry, Google connect is not available right now.")));
				}
				// Get Class
				require(ROOT."/includes/library/auth/google.php"); 
		    try {
		    	$google = new Google_Auth($this->config["google_cid"], $this->config["google_cs"], Main::href("user/login/google"), FALSE);

		    	if(!is_null($google->error)){
		    		return Main::redirect("",array("danger",$google->error));
		    	}
		    	
		    	$go = $google->info();

		    	if($go){
						if(!isset($go->email) || empty($go->email)){
							return Main::redirect("",array("danger",e("You must grant permission to this application to use your google account.")));
			    	}
						// Check if email is already taken
						if($this->db->get("user","auth!='google' AND email='{$go->email}'",array("limit"=>1))){
							 return Main::redirect("user/login",array("danger",e("The email linked to your account has been already used. If you have used that, please login to you existing account otherwise please contact us."))); 
						}

						// Let's see if the user is registered
						if($user=$this->db->get("user",array("auth"=>"google","email"=>$go->email),array("limit"=>1))){

							// Check Auth Key: If empty generate one
							if(empty($user->auth_key)){	
								$user->auth_key=Main::encode(Main::strrand(12));
								// Update database
								$this->db->update("user",array("auth_key"=>"?"),array("id"=>$user->id),array($user->auth_key));
							}
							// Check if inactive
							if(!$user->active){
								return Main::redirect(Main::href("user/login","",FALSE),array("danger",e("You haven't activated your account. Please check your email for the activation link. If you haven't received any emails from us, please contact us.")));
							}
						}else{
							// Let's register the user
							$auth_key = Main::encode(Main::strrand(12));
							$data = array(
									":email" => Main::clean($go->email,3,TRUE),
									":username" => isset($go->name) ? Main::slug($go->name).rand(1,100) : "",
									":password" => Main::encode(Main::strrand(12)),
									":active" => "1",
									":date" => "NOW()",
									":auth" => "google",
									":authid" => $go->id,
									":verifno" => Main::strrand(12),
									":auth_key" => $auth_key,
									":profile" => '{"name":"","description":"","cover":""}'
								);
							// Add Name
			      	if(isset($go->name)) $data[":name"] = $go->name;							
							if($this->db->insert("user",$data)){
								$user=$this->db->get("user",array("auth"=>"google","email"=>$go->email),array("limit"=>1));    		
							}
						}
						// Log Last login
						$this->db->update("user",array("lastlogin"=>"NOW()"),array("id"=>$user->id));						
						// Ok Let's login te user
						$json=base64_encode(json_encode(array("loggedin"=>TRUE,"key"=>$user->auth_key.$user->id)));
						$_SESSION["login"]=$json;

						// Return to /user
						return Main::redirect("",array("success",e("You have been successfully logged in.")));	
		    	}
        
		    } catch(ErrorException $e) {
		      return Main::redirect("",array("danger",e("An error occured, please try again later.")));
		    }
    		exit;
			}
	/**
	 * User Logout
	 * @author KBRmedia
	 * @since 1.0
	 **/
	protected function logout($redirect=TRUE){
		// Destroy Cookie
		if(isset($_COOKIE["login"])) setcookie('login','',time()-3600,'/');
		// Destroy Session
		if(isset($_SESSION["login"])) unset($_SESSION["login"]);
		if($redirect) return Main::redirect("");
	}
	/**
	 * User Register
	 * @author KBRmedia
	 * @since 1.1
	 **/
	protected function register(){
		// If Logged
		if($this->logged()) return Main::redirect(Main::href("user/account","", FALSE));		
		// If user Module is disabled		
		if(!$this->config["user"] || $this->config["maintenance"]) return Main::redirect("",array("danger",e("We are not accepting users at this time.")));

		// Filter ID
		$this->filter($this->id);
		// Check if form is posted
		if(isset($_POST["token"])){
			// Don't let bots register
			if(Main::bot()) return $this->_404();			
			// Validate CSRF Token
			if(!Main::validate_csrf_token($_POST["token"])){
				return Main::redirect(Main::href("user/register","",FALSE),array("danger",e("Invalid token. Please try again.")));
			}
			$error="";	
			// Validate Email
			if(empty($_POST["email"]) || !Main::email($_POST["email"])) $error.="<span>".e("Please enter a valid email.")."</span>";
			// Check email in database
			if(!empty($_POST["email"]) && $this->db->get("user",array("email"=>"?"),"",array($_POST["email"]))) return Main::redirect(Main::href("user/register","",FALSE),array("danger",e("An account is already associated with this email.")));
			// Check Password
			if(empty($_POST["password"]) || strlen($_POST["password"])<5) $error.="<span>".e("Password must contain at least 5 characters.")."</span>";
			// Check second password
			if(empty($_POST["cpassword"]) || $_POST["password"]!==$_POST["cpassword"]) $error.="<span>".e("Passwords don't match.")."</span>";

			// Check captcha
			// Check Captcha
			if($this->config["captcha"]){
				$captcha = Main::check_captcha($_POST);
				if($captcha!='ok'){
					$error .= "<span>".$captcha."</span>";
				}
			}	
			// Check terms
			if(!isset($_POST["terms"]) || (empty($_POST["terms"]) || $_POST["terms"]!=="1")) $error.="<span>".e("You must agree to our terms of service.")."</span>";

			// Generate unique auth key
			$auth_key = Main::encode($this->config["security"].Main::strrand());
			$unique = Main::strrand(12);
			// Prepare Data
			$data=array(
					":email"=>Main::clean($_POST["email"],3),
					":password"=>Main::encode($_POST["password"]),
					":auth_key"=>$auth_key,
					":verifno"=>$unique,
					":date"=>"NOW()",
					":profile" => '{"name":"","description":"","cover":""}',
					":country" => Main::clean($_POST["country"], 3, TRUE)
				);
				// Validate username
				if(empty($_POST["username"]) || !Main::username($_POST["username"])){
				  $error.="<span>".e("Please enter a valid username.")."</span>";
				}elseif($this->db->get("user",array("username"=>"?"),array("limit"=>1),array($_POST["username"]))){
					$error.="<span>".e("An account is already associated with this username.")."</span>";
				}else{
					$data[":username"]=Main::slug(Main::clean($_POST["username"],3,TRUE));
				}					

			// Return errors
			if(!empty($error)) Main::redirect(Main::href("user/register","",FALSE),array("danger",$error));
				
			// Check if user activation is required
			if(!$this->config["require_activation"]) $data[":active"]="1";

			// Register User
			if($this->db->insert("user",$data)){		
				// Add Points
	      if($this->config["points"]){
	        // Check if user has already been awarded points for this media
	        $id = $this->db->lastID();
	        $this->db->insert("point", array(":action" => "register", ":userid" => $id, ":actionid" => $id, ":point" => $this->config["amount_points"]["register"]));
	        $this->db->update("user", "points = points+{$this->config["amount_points"]["register"]}", array("id" =>  $id));
	      }   
				// Send Activation Email
				if($this->config["require_activation"]){
					// Send Email
					$mail["to"]=Main::clean($_POST["email"],3);
					$key=str_replace("=","",base64_encode("P1U2{$unique}".Main::strrand(5)));
					$activate="{$this->config["url"]}/user/activate/$key?email={$mail["to"]}";

					$mail["subject"]="[{$this->config["title"]}] Registration has been successful.";							
          $mail["message"] = "<td class='column' style='padding: 0;vertical-align: top;text-align: left'>
                               <div>
                                  <div class='column-top' style='font-size: 50px;line-height: 50px'>&nbsp;</div>
                               </div>
                               <table class='contents' style='border-collapse: collapse;border-spacing: 0;width: 100%'>
                                  <tbody>
                                     <tr>
                                        <td class='padded' style='padding: 0;vertical-align: top;padding-left: 50px;padding-right: 50px'>
                                          <h1 style='Margin-top: 0;color: #3b3e42;font-weight: 400;font-size: 40px;Margin-bottom: 20px;font-family: Avenir,sans-serif;line-height: 46px'>Hello!</h1>
																	      	<p style='Margin-top: 0;color: #60666d;font-size: 15px;font-family: sans-serif;line-height: 24px;Margin-bottom: 24px'>You have been successfully registered at {$this->config["title"]}. To login you will have to activate your account by clicking the URL below.</p>
																					<div class='btn' style='Margin-bottom: 21px'>
											                  		<a style='mso-hide: all;border: 0;border-radius: 4px;display: inline-block;font-size: 10px;font-weight: 700;line-height: 16px;padding: 5px 17px 5px 17px;text-align: center;text-decoration: none;color: #fff;background-color: #444;box-shadow: 0 3px 0 #363636;font-family: sans-serif' href='$activate'>Activate your account</a>
                													</div>				      
                                        </td>
                                     </tr>
                                  </tbody>
                               </table>
                               <div class='column-bottom' style='font-size: 26px;line-height: 26px'>&nbsp;</div>
                            </td>";
		      Main::send($mail);
					return Main::redirect(Main::href("user/login","",FALSE),array("success",e("An email has been sent to activate your account. Please check your spam folder if you didn't receive it.")));
				}

				// Send Email
				$mail["to"]=Main::clean($_POST["email"],3);
				$mail["subject"]="[{$this->config["title"]}] Registration has been successful.";
        $mail["message"] = "<td class='column' style='padding: 0;vertical-align: top;text-align: left'>
                             <div>
                                <div class='column-top' style='font-size: 50px;line-height: 50px'>&nbsp;</div>
                             </div>
                             <table class='contents' style='border-collapse: collapse;border-spacing: 0;width: 100%'>
                                <tbody>
                                   <tr>
                                      <td class='padded' style='padding: 0;vertical-align: top;padding-left: 50px;padding-right: 50px'>
                                        <h1 style='Margin-top: 0;color: #3b3e42;font-weight: 400;font-size: 40px;Margin-bottom: 20px;font-family: Avenir,sans-serif;line-height: 46px'>Hello!</h1>
																      	<p style='Margin-top: 0;color: #60666d;font-size: 15px;font-family: sans-serif;line-height: 24px;Margin-bottom: 24px'>You have been successfully registered at {$this->config["title"]}. You can now login to our site at <a href='{$this->config["url"]}'>{$this->config["url"]}</a></p>			      
                                      </td>
                                   </tr>
                                </tbody>
                             </table>
                             <div class='column-bottom' style='font-size: 26px;line-height: 26px'>&nbsp;</div>
                          </td>";
        
	      Main::send($mail);				
				return Main::redirect(Main::href("user/login","",FALSE),array("success",e("You have been successfully registered.")));				
			}
		}
		// Set Meta titles
		Main::set("body_class","dark");
		Main::set("title",e("Register and manage your media."));
		Main::set("description","Register an account and gain control over your media.");
		
		$this->header();
		include($this->t(__FUNCTION__));
		$this->footer();		
	}	
	/**
	 * User Activate
	 * @author KBRmedia
	 * @since 1.1
	 **/
	protected function activate(){
		if(Main::bot()) return $this->_404();
		if(!empty($this->id)){
			$email=Main::clean($_GET["email"],3,TRUE);
			$id=str_replace("P1U2","",base64_decode($this->id));
			$id=substr($id, 0,12);
			if($user=$this->db->get("user",array("verifno"=>"?","active"=>"0","email"=>"?"),array("limit"=>1),array($id,$email))){
				$this->db->update("user",array("active"=>"1"),array("id"=>$user->id));
				// Send Email
				$mail["to"]=Main::clean($user->email,3);
				$mail["subject"]="[{$this->config["title"]}] Your account has been activated.";
				$mail["message"] = "<td class='column' style='padding: 0;vertical-align: top;text-align: left'>
                             <div>
                                <div class='column-top' style='font-size: 50px;line-height: 50px'>&nbsp;</div>
                             </div>
                             <table class='contents' style='border-collapse: collapse;border-spacing: 0;width: 100%'>
                                <tbody>
                                   <tr>
                                      <td class='padded' style='padding: 0;vertical-align: top;padding-left: 50px;padding-right: 50px'>
                                        <h1 style='Margin-top: 0;color: #3b3e42;font-weight: 400;font-size: 40px;Margin-bottom: 20px;font-family: Avenir,sans-serif;line-height: 46px'>Hello!</h1>
																      	<p style='Margin-top: 0;color: #60666d;font-size: 15px;font-family: sans-serif;line-height: 24px;Margin-bottom: 24px'>Your account has been successfully activated at {$this->config["title"]}.</p>			      
                                      </td>
                                   </tr>
                                </tbody>
                             </table>
                             <div class='column-bottom' style='font-size: 26px;line-height: 26px'>&nbsp;</div>
                          </td>";
	      Main::send($mail);
				return Main::redirect(Main::href("user/login","",FALSE),array("success",e("Your account has been successfully activated.")));
			}
		}
		return Main::redirect(Main::href("user/login","",FALSE),array("danger",e("Wrong activation token or account already activated.")));
	}
	/**
	 * User Forgot
	 * @author KBRmedia
	 * @since 1.1
	 **/
	protected function forgot(){
		// Change Password if valid token
		if(isset($this->id) && !empty($this->id)){
			$new=base64_decode($this->id);
			$key=substr($new, 12);
			$unique=substr($new, 0,12);
			if($key==Main::encode($this->config["security"].": Expires on".strtotime(date('Y-m-d')),"md5")){
				// Change Password
				if(isset($_POST["token"])){
					// Validate CSRF Token
					if(!Main::validate_csrf_token($_POST["token"])){
						return Main::redirect(Main::href("user/forgot/{$this->id}","",FALSE),array("danger",e("Invalid token. Please try again.")));
					}
					// Check Password
					if(empty($_POST["password"]) || strlen($_POST["password"])<5) return Main::redirect(Main::href("user/forgot/{$this->id}","",FALSE),array("danger",e("Password must contain at least 5 characters.")));
					// Check second password
					if(empty($_POST["cpassword"]) || $_POST["password"]!==$_POST["cpassword"]) return Main::redirect(Main::href("user/forgot/{$this->id}","",FALSE),array("danger",e("Passwords don't match.")));
					// Add to database
					$auth_key=Main::encode(Main::strrand(12));
					if($this->db->update("user",array("password"=>"?","auth_key"=>"?"),array("verifno"=>"?"),array(Main::encode($_POST["password"]),$auth_key,$unique))){
						return Main::redirect(Main::href("user/login","",FALSE),array("success",e("Your password has been changed.")));
					}
				}
				// Set Meta titles
				Main::set("body_class","dark");
				Main::set("title",e("Reset Password"));
				$this->headerShow=FALSE;
				$this->footerShow=FALSE;

				$this->header();
				include($this->t(__FUNCTION__));
				$this->footer();
				return;
			}
			return Main::redirect(Main::href("user/login#forgot","",FALSE),array("danger",e("Token has expired, please request another link.")));
		}		
		// Check if form is posted to send token
		if(isset($_POST["token"])){
			// Validate CSRF Token
			if(!Main::validate_csrf_token($_POST["token"])){
				return Main::redirect(Main::href("user/login#forgot","",FALSE),array("danger",e("Invalid token. Please try again.")));
			}
			// Validate email
			if(empty($_POST["email"]) || !Main::email($_POST["email"])) return Main::redirect(Main::href("user/login#forgot","",FALSE),array("danger",e("Please enter a valid email.")));
			// Check email
			if($user=$this->db->get("user",array("email"=>"?","active"=>"1"),array("limit"=>1),array($_POST["email"]))){
				// Generate key
				$forgot_url=Main::href("user/forgot/".str_replace("=","", base64_encode($user->verifno.Main::encode($this->config["security"].": Expires on".strtotime(date('Y-m-d')),"md5"))));
		 		$mail["to"] = Main::clean($user->email);
		    $mail["subject"] = "[{$this->config["title"]}] Password Reset Instructions";		
				$mail["message"] = "<td class='column' style='padding: 0;vertical-align: top;text-align: left'>
                             <div>
                                <div class='column-top' style='font-size: 50px;line-height: 50px'>&nbsp;</div>
                             </div>
                             <table class='contents' style='border-collapse: collapse;border-spacing: 0;width: 100%'>
                                <tbody>
                                   <tr>
                                      <td class='padded' style='padding: 0;vertical-align: top;padding-left: 50px;padding-right: 50px'>
                                        <h1 style='Margin-top: 0;color: #3b3e42;font-weight: 400;font-size: 40px;Margin-bottom: 20px;font-family: Avenir,sans-serif;line-height: 46px'>Hello!</h1>
																      	<p style='Margin-top: 0;color: #60666d;font-size: 15px;font-family: sans-serif;line-height: 24px;Margin-bottom: 24px'>A request to reset your password was made.</b> If you <b>didn't</b> make this request, please ignore and delete this email otherwise click the link below to reset your password.</p>		
																					<div class='btn' style='Margin-bottom: 21px'>
											                  		<a style='mso-hide: all;border: 0;border-radius: 4px;display: inline-block;font-size: 10px;font-weight: 700;line-height: 16px;padding: 5px 17px 5px 17px;text-align: center;text-decoration: none;color: #fff;background-color: #444;box-shadow: 0 3px 0 #363636;font-family: sans-serif' href='$forgot_url'>Reset Password</a>
                													</div>																	      	
																      	<p style='Margin-top: 0;color: #60666d;font-size: 12px;font-family: sans-serif;line-height: 24px;Margin-bottom: 24px'>If you cannot click on the link above, simply copy &amp; paste the following link into your browser.</p>
																      	<p style='Margin-top: 0;color: #60666d;font-size: 12px;font-family: sans-serif;line-height: 24px;Margin-bottom: 24px'><a href='$forgot_url'>$forgot_url</a></p>		
																				<p style='Margin-top: 0;color: #60666d;font-size: 12px;font-family: sans-serif;line-height: 24px;Margin-bottom: 24px'>Note: This link is only valid for one day. If it expires, you can request another one.</p>																	      																      		      
                                      </td>
                                   </tr>
                                </tbody>
                             </table>
                             <div class='column-bottom' style='font-size: 26px;line-height: 26px'>&nbsp;</div>
                          </td>";		      
		    // Send email
		    Main::send($mail);
			}			
			return Main::redirect(Main::href("user/login","",FALSE),array("success",e("If an active account is associated with this email, you should receive an email shortly.")));
		}
		return Main::redirect(Main::href("user/login#forgot","",FALSE));
	}	
	/**
	 * User Dashboard
	 * @author KBRmedia
	 * @since  1.4
	 */
	private function account(){
		if(!$this->logged()) return $this->_404();
		// Edit Profile
		if($this->id == "settings"){
			return $this->settings();
		}
		if(empty($this->user->username)) return Main::redirect(Main::href("user/settings","",FALSE),array("danger",e("Please choose a username before continuing.")));
		// Filter
		if(!empty($this->id)){
			if(in_array($this->id, array("favorites","likes","subscribers","videos","points","playlists","subscriptions","notifications"))){
				$fn = "dashboard_{$this->id}";
				$data = $this->$fn();
				$text = $data["title"];
				$content = $data["content"];
			}else{
				return $this->_404();
			}
		}else{
			$text = e("Dashboard");
			$content = $this->getSubscription("5");			
		}
		// Get user data
		$user = $this->user;
		$profile = $this->user->profile;		
		// Get user activities
		$activities = $this->db->get("temp",array("filter" => $this->user->id, "type" => "notification"), array("limit" => 10, "order" => "date"));
		$activities_list = "";
		foreach ($activities as $activity) {
			$data = json_decode($activity->data);
			if($data->type == "liked"){
				$media = $this->db->get("media", array("id" => "?"),array("limit" => 1), array($data->media));
				$_user = $this->db->get("user", array("id" => "?"), array("limit" => 1), array($data->user));
				if($_user && $media){
					$_user->username = ucfirst($_user->username);
					$activities_list .="<p><a href='".Main::href("user/".strtolower($_user->username))."'><strong>{$_user->username}</strong></a> ".e("liked")." <a href='".Main::href("view/{$media->url}")."'><strong>{$media->title}</strong></a></p>";					
				}
			}elseif($data->type == "subbed"){
				$_user = $this->db->get("user", array("id" => "?"), array("limit" => 1), array($data->user));
				if($_user){
					$_user->username = ucfirst($_user->username);
					$activities_list .="<p><a href='".Main::href("user/".strtolower($_user->username))."'><strong>{$_user->username}</strong></a> ".e("subscribed to you")."</p>";					
				}
			}elseif($data->type == "faved"){
				$media = $this->db->get("media", array("id" => "?"),array("limit" => 1), array($data->media));
				$_user = $this->db->get("user", array("id" => "?"), array("limit" => 1), array($data->user));
				if($media && $_user){
					$_user->username = ucfirst($_user->username);
					$activities_list .="<p><a href='".Main::href("user/".strtolower($_user->username))."'><strong>{$_user->username}</strong></a> ".e("added to favorites")." <a href='".Main::href("view/{$media->url}")."'><strong>{$media->title}</strong></a></p>";					
				}
			}elseif($data->type == "commented"){
				$media = $this->db->get("media", array("id" => "?"),array("limit" => 1), array($data->media));
				$_user = $this->db->get("user", array("id" => "?"), array("limit" => 1), array($data->user));
				if($media && $_user){
					$_user->username = ucfirst($_user->username);
					$activities_list .="<p><a href='".Main::href("user/".strtolower($_user->username))."'><strong>{$_user->username}</strong></a> ".e("commented on")." <a href='".Main::href("view/{$media->url}")."'><strong>{$media->title}</strong></a></p>";					
				}
			}
		}		
		Main::set("title",$text);

		$this->header();
		include($this->t(__FUNCTION__));
		$this->footer();
		return;
	}	
			/**
			 * Generate User Videos
			 * @author KBRmedia
			 * @since  1.0
			 */
			private function dashboard_videos(){
				// Get Latest Videos
				$videos = $this->listMedia($this->getMedia(array("userid" => $this->user->id,"pagination" => TRUE,"limit" => 24)));
				// Generate Pagination
				$pagination = Main::pagination($this->count, $this->page, Main::href("user/?page=%d"));
				$text = e("Uploads")." ({$this->db->rowCount})";
				$content = "<div class='panel panel-default'>
											<div class='panel-heading'>$text</div>
											<div class='panel-body'>
												<ul class='media media-row'>
								          $videos
								        </ul>
								        $pagination										
											</div>
										</div>";		
				return array("title" => $text , "content" => $content);
			}		
			/**
			 * Generate User Favorites
			 * @author KBRmedia
			 * @since  1.1.1
			 */
			private function dashboard_favorites(){
				// Get Favorite Videos
				$videos = $this->listMedia($this->db->get(array("custom" => "{$this->config["prefix"]}media.*, {$this->config["prefix"]}favorite.* FROM `{$this->config["prefix"]}media` INNER JOIN `{$this->config["prefix"]}favorite` ON {$this->config["prefix"]}favorite.userid = {$this->user->id} AND {$this->config["prefix"]}media.id = {$this->config["prefix"]}favorite.mediaid"),array("approved" => 1),array("count"=> TRUE, "limit" => (($this->page-1)*$this->limit).", {$this->limit}")));	
				// Generate Pagination
				$pagination = Main::pagination($this->count, $this->page, Main::href("user/favorites?page=%d"));
				// Generate Template
				$text = e("Favorites")." ({$this->db->rowCount})";
				$content = "<div class='panel panel-default'>
											<div class='panel-heading'>$text</div>
											<div class='panel-body'>
												<ul class='media media-row'>
								          $videos
								        </ul>
								        $pagination										
											</div>
										</div>";				
				return array("title" => $text , "content" => $content);
			}
			/**
			 * Generate User Likes
			 * @author KBRmedia
			 * @since  1.1.1
			 */
			private function dashboard_likes(){
				// Get Liked Videos
				$videos = $this->listMedia($this->db->get(array("custom" => "{$this->config["prefix"]}media.*, {$this->config["prefix"]}rating.* FROM `{$this->config["prefix"]}media` INNER JOIN `{$this->config["prefix"]}rating` ON {$this->config["prefix"]}rating.userid = {$this->user->id} AND {$this->config["prefix"]}rating.rating = 'liked' AND {$this->config["prefix"]}media.id = {$this->config["prefix"]}rating.mediaid"),array("approved" => 1),array("count"=> TRUE, "limit" => (($this->page-1)*$this->limit).", {$this->limit}")));	
				// Get Pagination
				$pagination = Main::pagination($this->count, $this->page, Main::href("user/likes?page=%d"));
				// Generate Template
				$text = e("Likes")." ({$this->db->rowCount})";
				$content = "<div class='panel panel-default'>
											<div class='panel-heading'>$text</div>
											<div class='panel-body'>
												<ul class='media media-row'>
								          $videos
								        </ul>
								        $pagination										
											</div>
										</div>";				
				return array("title" => $text , "content" => $content);
			}	
			/**
			 * Generate User Subscribers
			 * @author KBRmedia
			 * @since  1.1.1
			 */
			private function dashboard_subscribers(){
				// Get Subscribers
				$html ="";
				$subscribers = $this->db->get(array("custom" => "{$this->config["prefix"]}user.*, {$this->config["prefix"]}subscription.* FROM `{$this->config["prefix"]}user` INNER JOIN `{$this->config["prefix"]}subscription` ON {$this->config["prefix"]}subscription.authorid = {$this->user->id} AND {$this->config["prefix"]}user.id = {$this->config["prefix"]}subscription.userid"),"",array("count"=> TRUE, "limit" => (($this->page-1)*$this->limit).", {$this->limit}"));	
				// Generate HTML
        foreach ($subscribers as $subscriber){
          $html .="<a href='".Main::href("user/{$subscriber->username}")."' title='{$subscriber->username}'><img src='".$this->avatar($subscriber, 54)."' width='54' alt='".ucfirst($subscriber->username)."' class='subs'></a>";
        }
        // Generate Pagination
				$pagination = Main::pagination($this->count, $this->page, Main::href("user/subscribers?page=%d"));
				// Generate Template
				$text = e("Subscribers")." ({$this->db->rowCount})";
				$content = "<div class='panel panel-default'>
											<div class='panel-heading'>$text</div>
											<div class='panel-body'>
								         $html
								        $pagination										
											</div>
										</div>";		
				return array("title" => $text , "content" => $content);
			}
			/**
			 * Generate User Subscribers
			 * @author KBRmedia
			 * @since  1.0
			 */
			private function dashboard_subscriptions(){
				// Generate Template
				// Get Subscribers
				$html ="";
				$subscribers = $this->db->get(array("custom" => "{$this->config["prefix"]}user.*, {$this->config["prefix"]}subscription.* FROM `{$this->config["prefix"]}user` INNER JOIN `{$this->config["prefix"]}subscription` ON {$this->config["prefix"]}subscription.userid = {$this->user->id} AND {$this->config["prefix"]}user.id = {$this->config["prefix"]}subscription.authorid"),"",array("count"=> TRUE, "limit" => (($this->page-1)*$this->limit).", {$this->limit}"));	
				// Generate HTML
        foreach ($subscribers as $subscriber){
          $html .="<a href='".Main::href("user/{$subscriber->username}")."' title='{$subscriber->username}'><img src='".$this->avatar($subscriber, 54)."' width='54' alt='".ucfirst($subscriber->username)."' class='subs'></a>";
        }
        // Generate Pagination
				$pagination = Main::pagination($this->count, $this->page, Main::href("user/subscribers?page=%d"));
				// Generate Template
				$text = e("Subscriptions")." ({$this->db->rowCount})";
				$content = "<div class='panel panel-default'>
											<div class='panel-heading'>$text</div>
											<div class='panel-body'>
								         $html
								        $pagination										
											</div>
										</div>";		
				return array("title" => $text , "content" => $content);
			}		
			/**
			 * Points
			 * @since 1.4
			 */
			private function dashboard_points(){

				// Get Points
				$points = $this->db->get("point", array("userid" => $this->user->id), array("count"=> TRUE,"order" => "date", "limit" => (($this->page-1)*25).", 25"));
		    if(($this->db->rowCount%25)<>0) {
		      $max = floor($this->db->rowCount/25)+1;
		    } else {
		      $max = floor($this->db->rowCount/25);
		    } 
				$content = "<div class='panel panel-default'>";
					$content .= "<div class='panel-heading'>".e("Points History")."</div>";
					$content .= "<div class='panel-body'>";
						$content .= "<div class='table-responsive'>";
							$content .= "<table class='table'>";
								$content .= "<tbody>";
								$content .= "<thead>
																<tr>
																	<th>".e("Action")."</th>
																	<th>".e("Media")."</th>
																	<th>".e("Date")."</th>
																	<th>".e("Points")."</th>
																</tr>
															</thead>";
								foreach ($points as $point) {
									if(in_array($point->action, array("like","comment","submit"))){
										$media = $this->db->get("media", array("id" => $point->actionid), array("limit" => 1));
										if($media){
											$media = $this->formatMedia($media);
										}
									}else{
										$media = "";
									}
									$content .= "<tr>
															  <td>".ucfirst(e($point->action))."</td>
															  <td><strong>".(!empty($media) ? "<a href='{$media->url}'>{$media->title}</a>" : "n.a.")."</strong></td>
															  <td>".date("j/n/Y", strtotime($point->date))."</td>
															  <td>{$point->point}</td>
															</tr>";
								}
								$content .= "</tbody>";
							$content .= "</table>";
							// Get Pagination
							$content .= Main::pagination($max, $this->page, Main::href("user/account/points?page=%d"));
						$content .= "</div>";
					$content .= "</div>";
				$content .= "</div>";
				// Generate Template
				$text = e("Points");							
				return array("title" => $text , "content" => $content);
			}		
			/**
			 * Notifications
			 * @since 1.5
			 */
			private function dashboard_notifications(){

				// Get Points
				$notifications = $this->db->get("temp", array("type" => "notification","filter" => $this->user->id), array("count"=> TRUE,"order" => "date", "limit" => (($this->page-1)*25).", 25"));
		    if(($this->db->rowCount%25)<>0) {
		      $max = floor($this->db->rowCount/25)+1;
		    } else {
		      $max = floor($this->db->rowCount/25);
		    } 
				$content = "<div class='panel panel-default'>";
					$content .= "<div class='panel-heading'>".e("Notifications")." <a href='' class='btn btn-primary btn-xs pull-right this-action' id='this-clear-notifications' data-action='clear_notifications' title='".e("Clear")."'>".e("Clear")."</a></div>";
					$content .= "<div class='panel-body'>";
						$content .= "<div class='table-responsive'>";
							$content .= "<table class='table'>";
								$content .= "<tbody>";
								$content .= "<thead>
																<tr>
																	<th>".e("Action")."</th>
																	<th>".e("Reference")."</th>
																	<th>".e("Date")."</th>
																</tr>
															</thead>";
								foreach ($notifications as $notification) {
									$data = json_decode($notification->data);
									if($data->type == "liked"){
										$media = $this->db->get("media", array("id" => "?"),array("limit" => 1), array($data->media));
										$_user = $this->db->get("user", array("id" => "?"), array("limit" => 1), array($data->user));
										if($_user && $media){
											$_user->username = ucfirst($_user->username);
												$content .= "<tr>
															  <td>".ucfirst(e("liked"))."</td>
															  <td><strong>".(!empty($media) ? "<a href='{$media->url}'>{$media->title}</a>" : "n.a.")."</strong></td>
															  <td>".date("j/n/Y", strtotime($notification->date))."</td>
															</tr>";																
										}
									}elseif($data->type == "subbed"){
										$_user = $this->db->get("user", array("id" => "?"), array("limit" => 1), array($data->user));
										if($_user){
											$_user->username = ucfirst($_user->username);
												$content .= "<tr>
															  <td>".ucfirst(e("subscribed to you"))."</td>
															  <td><a href='".Main::href("user/".strtolower($_user->username))."'><strong>{$_user->username}</strong></a></td>
															  <td>".date("j/n/Y", strtotime($notification->date))."</td>
															</tr>";																	
										}
									}elseif($data->type == "commented"){
										$media = $this->db->get("media", array("id" => "?"),array("limit" => 1), array($data->media));
										$_user = $this->db->get("user", array("id" => "?"), array("limit" => 1), array($data->user));
										if($media && $_user){
											$_user->username = ucfirst($_user->username);		
												$content .= "<tr>
															  <td>".ucfirst(e("commented on"))."</td>
															  <td><a href='".Main::href("view/{$media->url}")."'><strong>{$media->title}</strong></a></td>
															  <td>".date("j/n/Y", strtotime($notification->date))."</td>
															</tr>";													
										}
									}
								}
								$content .= "</tbody>";
							$content .= "</table>";
							// Get Pagination
							$content .= Main::pagination($max, $this->page, Main::href("user/account/notifications?page=%d"));
						$content .= "</div>";
					$content .= "</div>";
				$content .= "</div>";
				// Generate Template
				$text = e("Notifications")."({$this->db->rowCount})";							
				return array("title" => $text , "content" => $content);
			}					
			/**
			 * Playlists
			 * @since  1.4
			 */
			private function dashboard_playlists(){
				// Add Playlist
				if(isset($_POST["playlist_add_token"])){
					if(empty($_POST["name"]) || strlen($_POST["name"]) < 3){
						return Main::redirect(Main::href("user/account/playlists","",FALSE),array("danger",e("Please fill all fields.")));
					}
					$data = array(
										":uniqueid" => Main::strrand(8),
										":name" => Main::clean($_POST["name"],3,TRUE),
										":description" => Main::clean($_POST["description"],3,TRUE),
										":public" => Main::clean($_POST["public"],3,TRUE),
										":userid" => $this->user->id
									);					
					$this->db->insert("playlist",$data);
					return Main::redirect(Main::href("user/account/playlists","",FALSE),array("success",e("Playlist has been added.")));
				}
				// Edit Playlist
				if(isset($_GET["id"])){
					return $this->dashboard_playlists_edit();
				}
				// Return Playlists
				$playlists = $this->db->get("playlist",array("userid" => $this->user->id),array("limit" => 35));

				$content = "<div class='panel panel-default'>";
					$content .= "<div class='panel-heading'>".e("My Playlists")." <a href='' class='btn btn-primary btn-xs pull-right this-action' id='this-playlist' data-action='playlist_add' title='".e("Add Playlist")."'>".e("Add Playlist")."</a></div>";
					$content .= "<div class='panel-body'>";
						$content .= "<ul class='media'>";
							foreach ($playlists as $playlist) {
								if($media = $this->db->get("media", array("id" => $playlist->lastid), array("limit" => 1))){
									$media = $this->formatMedia($media);
								}else{
									$media = new stdClass;
									$media->title = $media->thumb = $media->url = "";
								}
								$content .= "<li>
                <div class='mediathumb'>
                  <a href='{$media->url}?playlist={$playlist->uniqueid}&index=1' title='".htmlentities($playlist->name)."'>
                    <span class='mediabg' style='background-image:url({$media->thumb})'>{$media->title}</span>
                    <small class='mediacount'><p class='text-center'><span class='fa fa-play-circle'></span></p>{$playlist->num} ".e("media")."</small>
                  </a>                  	                  
                </div> 
                <div class='mediainfo'>
	                <h4>
	                	<a href='".Main::href("user/account/playlists?id={$playlist->uniqueid}")."' title='".htmlentities($playlist->name)."' class='medialink'>{$playlist->name}
	                		<sup>".($playlist->public ? e("public") : e("private"))."</sup>
	                	</a>
	                	<span class='buttons'>
		                	<a href='".Main::href("user/account/playlists?id={$playlist->uniqueid}")."'' class='btn btn-xs btn-success'>".e("Edit")."</a>
		                	<a href='".Main::href("playlist/{$playlist->uniqueid}")."'' class='btn btn-xs btn-primary'>".e("View")."</a>		                	
	                	</p>      	
	                </h4>	   	                         
                </div>
              	</li>";	
							}
						$content .= "</ul>";
					$content .= "</div>";
				$content .= "</div>";
				// Generate Template				
				return array("title" => e("My Playlists") , "content" => $content);
			}
				/**
				 * Dashboard Playlist Edit
				 * @since 1.5.1
				 */
				private function dashboard_playlists_edit(){
					// Get List
					if(!$playlist = $this->db->get("playlist",array("uniqueid" => "?", "userid" => "?"), array("limit" => 1), array($_GET["id"], $this->user->id))){
						return $this->_404();
					}
					// Save Playlist
					if(isset($_POST["playlist_token"])){
						$data = array(
											":name" => Main::clean($_POST["name"],3,TRUE),
											":description" => Main::clean($_POST["description"],3,TRUE),
											":public" => Main::clean($_POST["public"],3,TRUE)
										);
						$this->db->update("playlist","",array("id" => $playlist->id), $data);
						return Main::redirect(Main::href("user/account/playlists?id={$playlist->uniqueid}","",FALSE), array("success", e("Playlist has been updated.")));
					}
					// Delete Playlist
					if(isset($_GET["nonce"]) && Main::validate_nonce("delete_playlist-{$_GET["id"]}")){
						$playlist = $this->db->get("playlist",array("uniqueid" => "?"),"", array($_GET["id"]));
						if($playlist){
							$this->db->delete("toplaylist", array("playlistid" => "?"), array($playlist->id));
							$this->db->delete("playlist", array("uniqueid" => "?"), array($_GET["id"]));
						}
						return Main::redirect(Main::href("user/account/playlists","",FALSE), array("success", e("Playlist has been deleted.")));
					}
					// Get Media List
					$videos = $this->db->get(array("custom" => "{$this->config["prefix"]}media.*, {$this->config["prefix"]}toplaylist.mediaid as mediaid FROM `{$this->config["prefix"]}toplaylist` INNER JOIN `{$this->config["prefix"]}media` ON {$this->config["prefix"]}media.id = mediaid"), array("playlistid" => $playlist->id),array("limit" => 20, "order" => "date"));
					
					// Meta Tags
					$title = $playlist->name;
					$pagination = NULL;
					$content = "<div class='panel panel-default'>";
							$content .= "<div class='panel-heading'>{$title} <a href='' class='btn btn-primary btn-xs pull-right this-action' data-action='playlist_settings' id='this-playlist-settings' data-data='[\"id\":{$playlist->id}]' title='".e("Settings")."'><span class='fa fa-gear fa-spin'></span> ".e("Settings")."</a></div>";
							$content .= "<div class='panel-body'>";
								$content .= "<ul class='media'>";
								foreach ($videos as $media) {
									$media = $this->formatMedia($media);
									$content .= "<li class='{$media->type} {$media->type}-{$media->catid}".(isset($options["current"]) && $options["current"] == ($i+1) ? " playlist-current": "")."' id='media-{$media->id}'>
								                <div class='mediathumb'>
								                  <a href='{$media->url}' title='".htmlentities($media->title)."'>
								                    <span class='mediabg' style='background-image:url({$media->thumb})'>{$media->title}</span>
								                    ".($media->nsfw ? "<span class='mediansfw'>NSFW</span>": "" )."
								                    <small>{$media->date}</small>
								                  </a>                  	                  
								                </div> 
								                <div class='mediainfo'>
									                <h4>
									                	<a href='{$media->url}' title='".htmlentities($media->title)."' class='medialink'>".Main::truncate($media->title, 35)."</a>                	
									                </h4>
									                <a href='' class='btn btn-xs btn-danger this-action' data-action='playlist_remove' data-data='[\"id\":{$media->id},\"check\":{$playlist->id}]'>".e("Remove")."</a>                               
								                </div>
								              </li>";	
								}
								$content .= "</ul>";
							$content .= "</div>";
						$content .= "</div>";
				// Generate Template				
				return array("title" => e("My Playlists") , "content" => $content);					
				}
	/**
	 * Settings Page
	 * @author KBRmedia
	 * @since  1.3
	 */
	private function settings(){
		// Make sure user is logged in
		if(!$this->logged()) return $this->_404();
		// Save settings		
  	if(isset($_POST["token"])){
			if(!Main::validate_csrf_token($_POST["token"])) {
				Main::redirect(Main::href("user/settings","",FALSE),array("danger",e("Something went wrong, please try again.")));
				return;
			}			

			$error = "";
			// Validate Text
			if(empty($_POST["name"]) || strlen($_POST["name"]) < 2) $error .= "<span>".e("Please enter your full name. This information is private.")."</span>";

			if(empty($_POST["email"]) || !Main::email($_POST["email"])) $error .= "<span>".e("Please enter a valid email.")."</span>";
			if(!empty($_POST["email"]) && $_POST["email"] != $this->user->email && $this->db->get("user", array("email" => "?"),array("limit" => 1), array($_POST["email"]))) $error .= "<span>".e("The new email seems to be already used.")."</span>";

			if(!Main::ccode($_POST["country"])) $_POST["country"] = $user->country;
			// Validate DOB
			if(!empty($_POST["dob"]) && (!strtotime($_POST["dob"]) || Main::timeago($_POST["dob"], TRUE) < 10)) $error .= "<span>".e("Please enter a valid date of birth. e.g. 1994/08/21")."</span>";
			
			// Prepare data
			$data= array(
				":name" => Main::clean($_POST["name"],3,TRUE),					
				":email" => Main::clean($_POST["email"],3,TRUE),
				":country" => Main::clean($_POST["country"],3,TRUE),
				":dob" => Main::clean($_POST["dob"],3,TRUE),
				":digest" => in_array($_POST["digest"], array("0","1")) ? Main::clean($_POST["digest"],3,TRUE) : 0,
				":public" => in_array($_POST["public"], array("0","1")) ? Main::clean($_POST["public"],3,TRUE) : 0,
				":nsfw" => in_array($_POST["nsfw"], array("0","1")) ? Main::clean($_POST["nsfw"],3,TRUE) : 0,
			);		
			if(empty($this->user->username)){
				// Validate username
				if(empty($_POST["username"]) || !Main::username($_POST["username"])){
				  $error.="<span>".e("Please enter a valid username.")."</span>";
				}elseif($this->db->get("user",array("username"=>"?"),array("limit"=>1),array($_POST["username"]))){
					$error.="<span>".e("An account is already associated with this username.")."</span>";
				}else{
					$data[":username"]=Main::slug(Main::clean($_POST["username"],3,TRUE));
				}							
			}				
			// Validate Profile Info
			$profile = array("name" => "", "description" =>"", "cover" => "");
			if(isset($_POST["profile"]["name"]) && !empty($_POST["profile"]["name"])){
				$profile["name"] = Main::clean($_POST["profile"]["name"],3,TRUE);				
			}
			if(isset($_POST["profile"]["description"]) && !empty($_POST["profile"]["description"])){
				$profile["description"] = Main::clean($_POST["profile"]["description"],2);
			}			
			// Check Uploaded files
			$upload_path=ROOT."/content/user/";
			$ext = array("image/png"=>"png","image/jpeg"=>"jpg","image/jpg"=>"jpg");
			
			$unique=Main::strrand(8);
			// Check Uploaded Files
			if(isset($_FILES["avatar"]) && !empty($_FILES["avatar"]["tmp_name"])){
				// Validate Avatar
				list($width, $height) = getimagesize($_FILES["avatar"]["tmp_name"]);
				if(!isset($ext[$_FILES["avatar"]["type"]])) $error .= "<span>".e("Avatar must be either a PNG or a JPEG.")."</span>";
				if($_FILES["avatar"]["size"] > 300*1024) $error .= "<span>".e("Avatar must be at least 200x200px PNG or a JPEG of max 300KB.")."</span>";	
				if(($width < 100 || $width > 600) && ($height < 100 || $height > 600))	$error .= "<span>".e("Avatar must be at least a 200x200px PNG or a JPEG of max 300KB.")."</span>";
				$avatar = $data[":avatar"] = $unique."_avatar.".$ext[$_FILES["avatar"]["type"]];
			}

			if(!empty($_POST["cover_value"]) && file_exists(ROOT."/static/covers/{$_POST["cover_value"]}")){
				$cover = $profile["cover"] = $_POST["cover_value"];
			}	

			if(isset($_FILES["cover"]) && !empty($_FILES["cover"]["tmp_name"])){
				// Validate Cover
				list($width, $height) = getimagesize($_FILES["cover"]["tmp_name"]);
				if(!isset($ext[$_FILES["cover"]["type"]])) $error .= "<span>".e("Profile cover must be either a PNG or a JPEG.")."</span>";
				if($_FILES["cover"]["size"] > 1000*1024) $error .= "<span>".e("Profile cover must be a PNG or a JPEG of at least 1200x250 and max 1MB.")."</span>";	
				if($width < 1000 || ($height < 250 || $height > 500))	$error .= "<span>".e("Profile cover must be a PNG or a JPEG of at least 1200x250 and max 1MB.")."</span>";	
				$cover = $profile["cover"] = $unique."_cover.".$ext[$_FILES["cover"]["type"]];	
			}


			$data[":profile"] = json_encode($profile);

			// Change Password
			if(!empty($_POST["npassword"])){
				if(strlen($_POST["npassword"]) < 5 ) $error .= "<span>".e("The password must contain at least 5 characters.")."</span>";
				if(empty($_POST["cnpassword"])) $error .= "<span>".e("Please confirm your password.")."</span>";
				$data[":password"] = Main::encode($_POST["npassword"]);
			}

			// Return errors
			if(!empty($error)) return Main::redirect(Main::href("user/settings","",FALSE),array("danger", $error));
			// Save to DB
			if($this->db->update("user","",array("id"=>$this->user->id), $data)){
				// Upload Avatar + Cover
				if(isset($avatar)){
					move_uploaded_file($_FILES["avatar"]['tmp_name'], $upload_path.$avatar);
					if($width > 300 || $height > 200) Main::cropthumb($upload_path.$avatar,$upload_path.$avatar, 200, 200);
				}
				if(isset($cover)){
					move_uploaded_file($_FILES["cover"]['tmp_name'], $upload_path.$cover);	
				}
				// Redirect			
				return Main::redirect(Main::href("user/settings","",FALSE),array("success",e("Profile has been successfully updated.")));
			}
			return Main::redirect(Main::href("user/settings","",FALSE));			
		}		
		$user = $this->user;
		Main::set("title",e("My Settings"));
		
		$this->header();
		include($this->t(__FUNCTION__));
		$this->footer();
		return;
	}
	/**
	 * Generate User's Public Profile
	 * @author KBRmedia
	 * @since  1.0
	 */
	private function profile($user = NULL){
		if(!empty($this->id) && !in_array($this->id, array("stream","subscribe"))){
			return $this->_404();
		}
		if($user && empty($user->username)) return $this->_404();
		// Check User
		if(is_null($user)){
			$user = $this->db->get("user",array("username" => "?"),array("limit" => 1),array($this->do));
			if(!$user || $user->username) return $this->_404(); 
		}
		if($this->logged()){
			if($this->db->get("subscription", array("userid" => $this->user->id, "authorid" => $user->id))){
				Main::add("<script>$('#this-subscribe').addClass('active');</script>","custom",TRUE);
			}					
		}
		// Check Profile is public
		if(!$user->public) return $this->_404();

		if(!$profile = json_decode($user->profile)){
			$profile = new stdClass();
		}
		if(!isset($profile->name)) $profile->name = ucfirst($user->username)."'s ".e("Profile");
		if(!isset($profile->description)) $profile->description = "Welcome to {$profile->name}. Feel free to browse videos and subscribe for more fun.";
		// Plug Admin control links
		$this->admin_menu_html = build_menu(array(
				array("href" => Main::ahref("users/edit/{$user->id}"), "text" => "Edit User", "icon" => "edit"),
			), TRUE);		
		// Get some subscribers
		$subscribers = $this->db->get(array("custom" => "{$this->config["prefix"]}subscription.*, {$this->config["prefix"]}user.username as username, {$this->config["prefix"]}user.avatar, {$this->config["prefix"]}user.email as email FROM `{$this->config["prefix"]}subscription` INNER JOIN `{$this->config["prefix"]}user` ON {$this->config["prefix"]}user.id = {$this->config["prefix"]}subscription.userid"),array("authorid"=>$user->id),array("limit"=>20,"order"=>"date"));
	
		// Show Pages
		if($this->id == "stream"){

			$media = $this->db->run("(SELECT date as thisdate, mediaid AS id, rating AS num, 'rating' AS type FROM {$this->config["prefix"]}rating WHERE userid = :userid) UNION (SELECT date as thisdate, mediaid AS id, id AS num, 'fav' AS type FROM {$this->config["prefix"]}favorite WHERE userid = :userid) UNION (SELECT date as thisdate, id, title, 'media' AS type FROM {$this->config["prefix"]}media WHERE userid =:userid AND approved ='1') ORDER BY  `thisdate` DESC LIMIT 20",array(":userid"=>$user->id), TRUE);

			$text = e("Public Stream");

			$content = $this->listActivity($media, ucfirst($user->username));	
		}else{

			$videos = $this->listMedia($this->getMedia(array("userid" => $user->id,"pagination" => TRUE,"limit" => 24)));
			// Generate Pagination
			$pagination = Main::pagination($this->count, $this->page, Main::href("user/{$user->username}?page=%d"));
			$text = e("Uploads")." ({$this->db->rowCount})";
			$content = "<div class='panel panel-default'>
										<div class='panel-heading'>$text</div>
										<div class='panel-body'>
											<ul class='media media-row'>
							          $videos
							        </ul>
							        $pagination										
										</div>
									</div>";
		}

		// Generate Template
		Main::set("title", $profile->name." ".$text);
		Main::set("description", Main::clean($profile->description, 3, TRUE));
		
		$this->header();
		include($this->t(__FUNCTION__));
		$this->footer();
		return;		
	}
	// End of File
}
