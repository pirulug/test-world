<?php

class SecureCrypt {
  private $method;
  private $key;
  private $ivLength;
  private $hmacKey;

  public function __construct($key, $method = 'aes-256-cbc') {
    $this->method   = $method;
    $this->key      = hash('sha256', $key, true);
    $this->ivLength = openssl_cipher_iv_length($this->method);
    $this->hmacKey  = hash_hmac('sha256', $key, 'HMAC_KEY', true);
  }

  public function encrypt($data) {
    $iv            = openssl_random_pseudo_bytes($this->ivLength);
    $encryptedData = openssl_encrypt($data, $this->method, $this->key, 0, $iv);
    $hmac          = hash_hmac('sha256', $iv . $encryptedData, $this->hmacKey, true);
    $encryptedData = base64_encode($iv . $hmac . $encryptedData);
    return $encryptedData;
  }

  public function decrypt($data) {
    $data           = base64_decode($data);
    $iv             = substr($data, 0, $this->ivLength);
    $hmac           = substr($data, $this->ivLength, 32);
    $encryptedData  = substr($data, $this->ivLength + 32);
    $calculatedHmac = hash_hmac('sha256', $iv . $encryptedData, $this->hmacKey, true);

    if (!hash_equals($hmac, $calculatedHmac)) {
      throw new Exception('HMAC verification failed.');
    }

    $decryptedData = openssl_decrypt($encryptedData, $this->method, $this->key, 0, $iv);
    return $decryptedData;
  }
}
