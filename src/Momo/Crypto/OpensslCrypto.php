<?php

namespace Momo\Crypto;

use gnupg;

class OpensslCrypto implements CryptoInterface {

  protected $_privateKey = null;
  protected $_publicKey = null;

  public function __construct(string $publicKey, string $privateKey, string $passphrase = null) {

    // putenv('/tmp/momo/.gnupg');
    echo $publicKey;
    $opensslPublicKey = openssl_pkey_get_public($publicKey);
    if (!$opensslPublicKey) {
      throw new \Exception("OpenSSL: Unable to get public key for encryption. Is the location correct? Does this key require a password?");
    }
    $this->_publicKey = $opensslPublicKey;

    $opensslPrivateKey = openssl_pkey_get_private($privateKey, $passphrase);
    if (!$opensslPrivateKey) {
        throw new \Exception("OpenSSL: Unable to get private key for decryption");
    }
    $this->_privateKey = $opensslPrivateKey;

  }

  public function encrypt($text) {
    return null;
  }

  public function encryptSign($text) {
    $encryptedData = '';
    $success = openssl_public_encrypt($text, $encrypted, $this->_publicKey);
    if (!$success) {
        throw new \Exception("Encryption failed. Ensure you are using a PUBLIC key.");
    }
  }

  public function decrypt($text) {
    // $plaintext = '';
    // $info = $this->_gpg->decryptverify($text, $plaintext);
    // var_dump($info);
    // var_dump($plaintext);
    // return $plaintext;
    return null;
  }

  public function verify($signedText) {
    // $plainText = '';
    // $info = $this->_gpg->verify($signedText, false, $plainText);
    // print_r($plainText);
    // print_r($info);
    // print_r($this->_gpg->keyinfo($info[0]['fingerprint']));
  }

  public function __destruct() {
    if (!empty($this->_publicKey))
      openssl_free_key($publicKey);

    if (!empty($this->_privateKey))
      openssl_free_key($this->_privateKey);

  }
}
