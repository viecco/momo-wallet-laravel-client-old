<?php

namespace Momo\Crypto;

use Momo\Crypto\CryptGPG\Crypt_GPG_FixMDC as Crypt_GPG;

class CryptGPG implements CryptoInterface {

  protected $_gpg = null;

  public function __construct(string $publicKey, string $privateKey, string $passphrase = null) {

    $gpg = new Crypt_GPG();
    // $gpg->seterrormode(gnupg::ERROR_WARNING);

    // echo "public key: $publicKey\n";
    $publicKeyInfo = $gpg->importKey($publicKey);
    $fingerPrint = $publicKeyInfo['fingerprint'];
    // echo "public key fingerprint: $fingerPrint\n";
    $gpg->addEncryptKey($fingerPrint);

    $privateKeyInfo = $gpg->importKey($privateKey);
    $fingerPrint = $privateKeyInfo['fingerprint'];
    // echo "private key fingerprint: $fingerPrint\n";
    $gpg->addDecryptKey($fingerPrint, $passphrase);
    //
    // // $gpg->addsignkey($fingerPrint);
    $gpg->addSignKey($fingerPrint, $passphrase);
    // $gpg->sign('asdf');

    $this->_gpg = $gpg;
  }

  public function encrypt($text) {
    return $this->_gpg->encrypt($text);
  }

  public function encryptSign($text) {
    return $this->_gpg->encryptAndSign($text);
  }

  public function decrypt($text) {
    $result = $this->_gpg->decrypt($text);
    return $result;
  }

}
