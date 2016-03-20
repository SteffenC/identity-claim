<?php
ini_set('session.save_path',"/tmp");
session_start();
require_once __DIR__ . '/facebook-sdk/src/Facebook/autoload.php';
require_once 'IDP.php';

$login = new Login();
class Login{

  public $fb;
	public function __construct(){
    if(isset($_GET["pw"]) && isset($_GET["username"])) {
      $this->formLogin($_GET["username"], $_GET["pw"]);
    } else {
      $this->initFacebook();
    }
	}

  private function formLogin($us, $pw){
    $db = new Database();
    $select = "SELECT *
                 FROM user
                WHERE name = '{$us}'
                  AND password = '{$pw}'";
    $result = mysqli_query($db->db, $select);
      if($result) {
      $this->grantAccess();
    }else{
      return false;
    }
  }

	private function initFacebook(){
    $this->fb = new Facebook\Facebook([
      'app_id' => "190690384598650",
      'app_secret' => 'e2e120f113f45c3a547199c179fa6a04',
      'default_graph_version' => 'v2.5',
    ]);

    $helper = $this->fb->getRedirectLoginHelper();
    try {
      $accessToken = $helper->getAccessToken();
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
      // When Graph returns an error
      echo 'Graph returned an error: ' . $e->getMessage();
      exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
      // When validation fails or other local issues
      echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
    }

    if (isset($accessToken)) {
      // Logged in!
      $_SESSION['facebook_access_token'] = (string) $accessToken;
      $this->fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
      $this->grantAccess();
    }
	}

	private function grantAccess(){
		echo '
<link rel="stylesheet" type="text/css" href="css/main.css">
<link rel="stylesheet" type="text/css" href="css/normalize.css">
<form action="RP.php">

<div class="form-style-8">
<h2> Welcome ' . $this->getName() . ' </h2>

<div class="form-img">
    <img src="' . $this->getPicture() . '" />
</div>
  <form>
    <input type="text" name="name" placeholder="Full Name" />
    <input type="tel" name="phone" placeholder="Phone number" />
    <input type="submit" value="Send Claim" />
  </form>
</div>
</form>';
		}

		private function getName(){
      if($this->fb) {
        $response = $this->fb->get('/me');
        $userNode = $response->getGraphUser();
        return $userNode->getName();
      }
		}

		private function getPicture(){
      if($this->fb) {
        $response = $this->fb->get('/me?fields=picture');
        $userNode = $response->getGraphUser();
        return $userNode->getPicture()->getUrl();
      }
		}
  }
