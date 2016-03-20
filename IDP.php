<?php

$identity = new IdentityProvider();


if(isset($_POST['token'])) {
  $claim = $_POST['token'];
  $identity->assert($claim);
}

class IdentityProvider{

  public $db;

  public function __construct(){
    $this->db = new Database();
  }

  public function assert($claim){
    if($this->validateMac($claim)) {
      return $this->assertClaim($claim);
    }
  }

  private function getHeader($claim){
    $claim = explode(".", $claim);
    $header = $claim[0];
    return $header;
  }

  private function getPayload($claim){
    $claim = explode(".", $claim);
    $payload = $claim[1];
    return $payload;
  }

  private function validateMac($claim){
    $publicKey = $this->getPublicKey();
    $claimArr = explode(".", $claim);

    $mac = $claimArr[2];
    $payload = $this->getPayload($claim);
    $header = $this->getHeader($claim);

    $token = $header . "." . $payload;

    $challenge = hash_hmac("sha256", $token, $publicKey);
    $challenge = base64_encode($challenge);
    return $challenge == $mac ? true : false;
  }

  private function assertClaim($claim){
    $payload = $this->getPayload($claim);
    $json_payload = base64_decode($payload);
    $json_array = json_decode($json_payload);
    $firstName = $json_array[0];
    $phoneNumber = $json_array[1];

    $select = "SELECT *
                 FROM credential
                WHERE name = '{$firstName}'
                  AND phonenumber = {$phoneNumber}";
    $result = mysqli_query($this->db->db, $select);
    if($result) {
      echo 1;
    }else{
      echo 0;
    }
  }

  private function getPublicKey(){
    $publicKeyFile = file_get_contents("/etc/apache2/ssl/apache.crt");
    $pub_key = openssl_pkey_get_public($publicKeyFile);
    $keyData = openssl_pkey_get_details($pub_key);
    return $keyData['key'];
  }

}


class Database{

  public $db;
  public function __construct(){

    // Create connection
    $this->db = new mysqli("localhost", "root", "rotoreds", "idp");

    // Check connection
    if ($this->db->connect_error) {
        die("Connection failed: " . $this->db->connect_error);
    }
  }

}

 ?>
