<?php


$party = new Party();

$name   = $_GET['name'];
$phone  = $_GET['phone'];

$party->createWebToken( array($name, $phone) );

class Party{

  public function createWebToken(array $payload){
    $publicKey = $this->getIDPCertificate();

    //create header
    $header = json_encode(["typ" => "JWT", "alg" => "HS256"]);
    $header = base64_encode($header);

    // create payload
    $payload = json_encode($payload);
    $payload = base64_encode($payload);

    //concat header and payload
    $token = $header . "." . $payload;

    //create mac
    $mac = hash_hmac("sha256", $token, $publicKey);
    $mac = base64_encode($mac);

    //return concatenated header, payload and mac.
    $token = $token . "." . $mac;

    return $this->sendToken($token);
  }


  public function sendToken($token){
    $url = 'http://127.0.0.1/IDP.php';
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_POSTFIELDS, array("token" => $token));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $text = ($response == true ? "Verified" : "Unverified");
    print '<link rel="stylesheet" type="text/css" href="css/main.css">
    <link rel="stylesheet" type="text/css" href="css/normalize.css">
    <div style="text-align:center;" class="form-style-8">
    <h3> Identity ' . $text . ' </h3>
    </div>
    </form>';
  }

  public function getIDPCertificate(){
    $publicKeyFile = file_get_contents("/etc/apache2/ssl/apache.crt");
    $pub_key = openssl_pkey_get_public($publicKeyFile);
    $keyData = openssl_pkey_get_details($pub_key);
    return $keyData['key'];
  }

}
