<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" type="text/css" href="css/main.css">
<link rel="stylesheet" type="text/css" href="css/normalize.css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>IAM Assignment</title>
</head>


<body>
<?php
ini_set('session.save_path',"/tmp");
session_start();
require_once __DIR__ . '/facebook-sdk/src/Facebook/autoload.php';

$fb = new Facebook\Facebook([
        'app_id' => '190690384598650',
        'app_secret' => 'e2e120f113f45c3a547199c179fa6a04',
        'default_graph_version' => 'v2.5',
      ]);


  $helper = $fb->getRedirectLoginHelper();
  $permissions = ['email', 'user_likes']; // optional
  $loginUrl = $helper->getLoginUrl('https://localhost/SP.php', $permissions);

  echo '<img height="400px  "; width="100%" src="css/logo.png" />
<form action="SP.php">
<div class="form-style-8">
<h2> Login </h2>
  <form>
	<input name="username" type="text" placeholder="username"/>
  <input name="pw" type="password" placeholder"Password" />
	<input type="submit" value="LOGIN" />
  </form>
</div>
<a class="centerMe" href="' . $loginUrl . '"> <img width:"900px" style="margin:0 auto" src="https://d106kdhqpps9un.cloudfront.net/assets/facebook-connect-button-390x60-9d058c556344123297a221621cb4220e.png" /> </a> ';
?>

</body>
<!-- <video autoplay muted loop poster="" id="promoter"> <source src="css/video.mp4" type="video/mp4"> </video> -->

</html>
