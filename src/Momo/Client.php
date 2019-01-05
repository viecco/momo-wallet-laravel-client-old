<?php

namespace Momo;

use GuzzleHttp\Client as GuzzleHttpClient;
use Momo\Crypto\CryptoInterface;

class Client {

  protected $_config = null;
  protected $_cryptoService = null;
  protected $_httpClient = null;

  public function __construct(array $config = null, CryptoInterface $cryptoService) {
    $defaultConfig = require_once 'Config.php';
    $this->_config = array_merge_recursive($defaultConfig, $config);

    if (empty($this->_config['partner_code'])) {
      throw new Exception('partner_code cannot be null');
    }

    $this->_cryptoService = $cryptoService;

    $this->_httpClient = new GuzzleHttpClient([
        'base_uri' => $this->_config['base_uri'],
        'timeout'  => $this->_config['timeout'],
        'headers' => [
          'Content-Type' => 'application/pgp-encrypted',
          'Accept' => 'application/pgp-encrypted',
          'partner-code' => $this->_config['partner_code'],
        ]
    ]);
  }

  /**
   * @return string random number
   */
  protected function _generateRequestId() {
    return (string)rand(0, PHP_INT_MAX);
  }

  /**
   * Encrypt and Sign data following Momo's guideline
   */
  protected function _encryptData(array $data) : string {
    $dataText = json_encode($data);
    $encryptedData = $this->_cryptoService->encryptSign($dataText);
    return $encryptedData;
  }

  /**
   * Send data over POST protocol to MoMo API with encryption and
   * return json decoded response data
   */
  protected function _post(string $apiPath, array $data) : array {
    $response = $this->_httpClient->post($apiPath, [
        'body' => $this->_encryptData($data)
    ]);

    if (!in_array($response->getStatusCode(), [200, 201, 202])) {
      throw new \Exception("Error Processing Request", 1);
    }

    $encryptedContent = $response->getBody();
    $textContent = $this->_cryptoService->decrypt($encryptedContent);
    $data = json_decode($textContent, true);
    return $data;
  }

  public function testEncryption() {
    $requestData = [
        "input1" => "Mo",
        "input2" => "mo",
    ];
    return $this->_post('/testing/encryption', $requestData);
  }

  public function checkWalletInfo($telephone) {
    $requestData = [
      "requestId" => $this->_generateRequestId(),
      "walletId" => $telephone,
    ];
    return $this->_post('/api/pay/check-info', $requestData);
  }

  /**
   * Check how much money remaining in our wallet 
   */
  public function getBalance() : int {
    $requestData = [
      "requestId" => $this->_generateRequestId(),
      "password" => $this->_config['wallet_password'],
    ];

    $result = $this->_post('api/pay/balance', $requestData);

    return is_array($result) && isset($result['amount']) ? $result['amount'] : null;
  }

  /**
   * Transfer money from our wallet to customer's wallet
   * @param  [type] $telephone   customer wallet
   * @param  [type] $amount      [description]
   * @param  [type] $description [description]
   * @return [type]              [description]
   */
  public function transfer($telephone, $amount, $description) {
    $requestData = [
      "requestId" => $this->_generateRequestId(),
      "password" => $this->_config['wallet_password'],
      "created" => "2019-01-04T14:19:30+07:00",
      "walletId" => $telephone,
      "amount" => $amount,
      "amount" => $amount,
      "description" => $description
    ];

    return $this->_post('/api/payment/pay', $requestData);
  }

}
