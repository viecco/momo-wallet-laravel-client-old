<?php
namespace Momo\Client;

use Illuminate\Support\ServiceProvider;
use Momo\Client as MomoClient;
use Momo\Crypto\CryptGPG as Crypto;

class LaravelServiceProvider extends ServiceProvider
{

  /**
   * Bootstrap the application services.
   * Run after the service has successfully registered
   *
   * @return void
   */
    public function boot()
    {
        //
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
      $this->app->singleton('Momo\Client', function ($app) {
        $config = config('momo');

        $clientCrypto = new Crypto(
          $config['momo_public_key'],
          $config['partner_private_key'],
          $config['partner_passphrase']
        );

        return new MomoClient($config, $clientCrypto);
      });
    }
}
