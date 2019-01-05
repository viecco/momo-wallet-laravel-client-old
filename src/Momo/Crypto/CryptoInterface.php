<?php

namespace Momo\Crypto;

/**
 *
 */
interface CryptoInterface
{

  public function encrypt($text);

  public function encryptSign($text);

  public function decrypt($text);

}
