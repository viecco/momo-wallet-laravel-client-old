<?php

namespace Momo\Crypto;

use gnupg;

class GnupgCrypto implements CryptoInterface {

  protected $_gpg = null;

  public function __construct(string $publicKey, string $privateKey, string $passphrase = null) {

    // putenv('/tmp/momo/.gnupg');
    $gpg = new gnupg();
    $gpg->seterrormode(gnupg::ERROR_WARNING);

    // echo "public key: $publicKey\n";
    $publicKeyInfo = $gpg->import($publicKey);
    $fingerPrint = $publicKeyInfo['fingerprint'];
    echo "public key fingerprint: $fingerPrint\n";
    $gpg->addencryptkey($fingerPrint);

    $privateKeyInfo = $gpg->import($privateKey);
    $fingerPrint = $privateKeyInfo['fingerprint'];
    echo "private key fingerprint: $fingerPrint\n";
    $gpg->adddecryptkey($fingerPrint, $passphrase);
    //
    // // $gpg->addsignkey($fingerPrint);
    $gpg->addsignkey($fingerPrint, $passphrase);
    // $gpg->sign('asdf');

    $this->_gpg = $gpg;
  }

  public function encrypt($text) {
    return $this->_gpg->encrypt($text);
  }

  public function encryptSign($text) {
    return $this->_gpg->encryptsign($text);
  }

  public function sign($text) {
    $this->_gpg->setsignmode(GNUPG_SIG_MODE_CLEAR);
    // $this->_gpg->setsignmode(GNUPG_SIG_MODE_DETACH);
    $signature = $this->_gpg->sign($text);
    echo "signature:::\n";
    var_dump($signature);

    $result = $this->_gpg->verify($signature, false, $text);
    echo "verify result:::\n";
    var_dump($result);

    return $result;
  }

  public function decrypt($text) {
    // $plaintext = '';
    // $info = $this->_gpg->decryptverify($text, $plaintext);
    // var_dump($info);
    // var_dump($plaintext);
    // return $plaintext;
    try {
      $result = $this->_gpg->decrypt($text);
    }
    catch (\Exception $error) {
      // var_dump($error);
      echo $this->_gpg->geterror();
      return false;
    }

    return $result;
  }

  public function decryptSign($text) {
    $plaintext = '';
    $info = $this->_gpg->decryptverify($text, $plaintext);
    var_dump($info);
    // var_dump($plaintext);
    // return $plaintext;
    return $plaintext;
  }

  public function verify($signedText) {
    $plainText = '';
    $info = $this->_gpg->verify($signedText, false, $plainText);
    print_r($plainText);
    print_r($info);
    print_r($this->_gpg->keyinfo($info[0]['fingerprint']));
  }

}
