<?php
namespace Momo\Crypto\CryptGPG;

use Crypt_GPG;

// Add `--ignore-mdc-error` to decrypt function
class Crypt_GPG_FixMDC extends Crypt_GPG {

  // }}}
  // {{{ _decrypt()

  /**
   * Decrypts data
   *
   * @param string  $data       the data to be decrypted.
   * @param boolean $isFile     whether or not the data is a filename.
   * @param string  $outputFile the name of the file to which the decrypted
   *                            data should be written. If null, the decrypted
   *                            data is returned as a string.
   *
   * @return void|string if the <kbd>$outputFile</kbd> parameter is null, a
   *                     string containing the decrypted data is returned.
   *
   * @throws Crypt_GPG_KeyNotFoundException if the private key needed to
   *         decrypt the data is not in the user's keyring.
   *
   * @throws Crypt_GPG_NoDataException if specified data does not contain
   *         GPG encrypted data.
   *
   * @throws Crypt_GPG_BadPassphraseException if a required passphrase is
   *         incorrect or if a required passphrase is not specified. See
   *         {@link Crypt_GPG::addDecryptKey()}.
   *
   * @throws Crypt_GPG_FileException if the output file is not writeable or
   *         if the input file is not readable.
   *
   * @throws Crypt_GPG_Exception if an unknown or unexpected error occurs.
   *         Use the <kbd>debug</kbd> option and file a bug report if these
   *         exceptions occur.
   */
  protected function _decrypt($data, $isFile, $outputFile)
  {
      $input  = $this->_prepareInput($data, $isFile, false);
      $output = $this->_prepareOutput($outputFile, $input);

      $this->engine->reset();
      $this->engine->setPins($this->decryptKeys);
      $this->engine->setOperation('--decrypt --skip-verify --ignore-mdc-error');
      $this->engine->setInput($input);
      $this->engine->setOutput($output);
      $this->engine->run();

      if ($outputFile === null) {
          return $output;
      }
  }

}
