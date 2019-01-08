# momo-laravel-client

## Installing

Add composer dependency:

```
composer require viecco/momo-wallet-laravel-client
```

Add the following code to the end of 'providers' array in `config/app.php`
```
'providers' => [
// ...
  Momo\Client\LaravelServiceProvider::class,
// ...  
```


Get a copy of config file

```
php artisan vendor:publish --provider="Momo\Client\LaravelServiceProvider"
```

then replace config with data provided by Momo team


## Usage

```
Route::get('/testmomo', function () {

  $momo = app('Momo\Client');
  $data = $momo->checkWalletInfo('0982551628');
  print_r($data);
});

```
