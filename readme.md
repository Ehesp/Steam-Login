# PHP Steam Login

This package enables you to easily log users in via Steam, using their OpenID service. However, this package does not require that you have the OpenID PHP module installed!

> The package can also easily be used with the Laravel 4 PHP Framework.

## Installation
Begin by installing this package via Composer:

```php
{
	"require": {
		"ehesp/steam-login": "dev-master"
	}
}
```

### Laravel Users
If you're using the Laravel 4 PHP Framework, a service provider is available:

```
// app/config/app.php
'providers' => array(
	'...',
	'Ehesp\SteamLogin\Laravel\SteamLoginServiceProvider',
),
```

```
// app/config/app.php
'aliases' => array(
	'...',
	'SteamLogin' => 'Ehesp\SteamLogin\Laravel\Facades\SteamLogin',
),
```
You how have access to the `SteamLogin` facade.

## Usage

> Before starting, please note you're unable to redirect a user to the Steam OpenID login portal - in other words, they must be able to click the link themselves.

### Standalone

Ensure your script requires the requires the Composer auto file: `require './vendor/autoload.php';`
Then, use the `SteamLogin` class and create a new instance of it:

```
// login.php
use Ehesp\SteamLogin\SteamLogin;

$login = new SteamLogin();
echo $login->url();
```
Once authenticated, Steam will return to your website root and attached GET parameters, which must be validated:

```
// index.php
use Ehesp\SteamLogin\SteamLogin;

$login = new SteamLogin();
echo $login->validate();
```
If everything was successful, the users Steam Community ID will be returned, else an Exception will be thrown.

### Laravel

You can either use blade to easily generate the login URL, or pass it through via a View Composer:

```
// view.blade.php
<a href="{{ SteamLogin::url() }}">Login via Steam!</a>
```
```
// app/filters.php 
App::before(function($request)
{
	View::share('url', SteamLogin::url());
});

// view.php
<a href="$url">Login via Steam!</a>
```
To validate the Steam Login:
```
// app/routes.php
Route::get('/', function()
{
	return SteamLogin::validate();
});

## Changing the return URL

If you want your users to be sent to a specific URL/route after login, this is easily done. Simply add the URL as a parameter in the `url()` method:

```
$login->url('http://mywebsite.com/login');
```

As a Laravel user, you may with to set a custom `Config` option with this URL:
```
// app/config/steam.php
return array (
	'login' => 'http://mywebsite.com/login',
	// 'login' => URL::to('login'),
);
```
Then simply access this in the url method:

```
SteamLogin::url(Config::get('steam.login'));
// SteamLogin::url(URL::to('login'));
```