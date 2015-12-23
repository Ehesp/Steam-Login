# PHP Steam Login

This package enables you to easily log users in via Steam, using their OpenID service. However, this package does not require that you have the OpenID PHP module installed!

Note: The package can also easily be used with the Laravel 4 PHP Framework.

## Installation

Begin by installing this package via Composer:

```
{
    "require": {
		"ehesp/steam-login": "~1.0.1"
	}
}
```

### Laravel Users

If you're using the Laravel 4 PHP Framework, a service provider is available:

```php
<?php
// app/config/app.php
'providers' => array(
	'...',
	'Ehesp\SteamLogin\Laravel\SteamLoginServiceProvider',
),
```

```php
<?php
// app/config/app.php
'aliases' => array(
	'...',
	'SteamLogin' => 'Ehesp\SteamLogin\Laravel\Facades\SteamLogin',
),
```
You now have access to the `SteamLogin` facade.

## Usage

Before starting, please note you're unable to redirect a user to the Steam OpenID login portal. In other words, they must be able to click the link themselves.

### Standalone

Ensure your script requires the Composer autoload file: `require './vendor/autoload.php';`
Then, use the `SteamLogin` class and create a new instance of it:

```php
<?php
// login.php
use Ehesp\SteamLogin\SteamLogin;

$login = new SteamLogin();
echo $login->url();
```

Once authenticated, Steam will return to your website root with attached GET parameters, which must be validated:

```php
<?php
// index.php
use Ehesp\SteamLogin\SteamLogin;

$login = new SteamLogin();
echo $login->validate();
```

If everything was successful, the users Steam Community ID will be returned, or if anything went wrong an Exception will be thrown.

### Laravel

You can either use blade to easily generate the login URL, or pass it through via a View Composer:

```php
<?php
// view.blade.php
<a href="{{ SteamLogin::url() }}">Login via Steam!</a>
```

```php
<?php
// app/filters.php 
App::before(function($request)
{
	View::share('url', SteamLogin::url());
});

// view.php
<a href="$url">Login via Steam!</a>
```

To validate the Steam Login:

```php
<?php
// app/routes.php
Route::get('/', function()
{
	return SteamLogin::validate();
});
```

## Changing the return URL

The return URL must be a valid URL which contains either the http or https URI scheme.

If you want your users to be sent to a specific URL/route after login, this is easily done. Simply add the URL as a parameter in the `url()` method:

```php
<?php
$login->url('http://mywebsite.com/login');
```

As a Laravel user, you may with to set a custom `Config` option with this URL:

```php
<?php
// app/config/steam.php
return array (
	'login' => 'http://mywebsite.com/login',
	// 'login' => URL::to('login'),
);
```

Then simply access this in the `url` method:

```php
<?php
SteamLogin::url(Config::get('steam.login'));
// SteamLogin::url(URL::to('login'));
```

## To Do

* Add PHPUnit tests
* Integration with other frameworks: CodeIgniter, Symfony 2
