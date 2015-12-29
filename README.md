# Caslite

Caslite provides the Central Authentication Service ([CAS](https://www.apereo.org/projects/cas)) authentication to Laravel 5.1 applications.

> Inspired by [Laravel Socialite](https://github.com/laravel/socialite)

## Installation

To get started with Caslite, add to your `composer.json` file as a dependency:

```
composer require avvertix/caslite
```

## Configuration

After installing the Caslite library, register the Avvertix\Caslite\CasliteServiceProvider in your config/app.php configuration file:

```php
'providers' => [
    // Other service providers...

    Avvertix\Caslite\CasliteServiceProvider::class,
],
```

Also, add the Caslite facade to the aliases array in your app configuration file:

```php
'Caslite' => Avvertix\Caslite\Facades\Caslite::class,
```

You will also need to add credentials for the OAuth services your application utilizes. These credentials should be placed in your config/services.php configuration file, and should use the key facebook, twitter, linkedin, google, github or bitbucket, depending on the providers your application requires. For example:

```php
'cas' => [
    /*
    |--------------------------------------------------------------------------
    | phpCAS Debug
    |--------------------------------------------------------------------------
    |
    | @var boolean true to enable debug, log file will be written in storage/logs/cas.log
    |
    */
    'cas_debug' => env('CAS_DEBUG', false),
    
    /*
    |--------------------------------------------------------------------------
    | phpCAS Hostname
    |--------------------------------------------------------------------------
    |
    | Example: 'login.uksw.edu.pl'
    | @var string
    */
    'cas_hostname' => env('CAS_HOSTNAME', ''),
    
    /*
    |--------------------------------------------------------------------------
    | CAS Port
    |--------------------------------------------------------------------------
    |
    | Usually 443 is default
    | @var integer
    */
    'cas_port' => env('CAS_PORT', 443),
    
    /*
    |--------------------------------------------------------------------------
    | CAS URI
    |--------------------------------------------------------------------------
    |
    | Usually '/cas' is default
    | @var string
    */
    'cas_uri' => env('CAS_URI', '/cas'),
    
    /*
    |--------------------------------------------------------------------------
    | CAS login URI
    |--------------------------------------------------------------------------
    |
    | Empty is fine
    | @var string
    */
    'cas_login_uri' => env('CAS_LOGIN_URI', ''),
    
    /*
    |--------------------------------------------------------------------------
    | CAS logout URI
    |--------------------------------------------------------------------------
    |
    | Example: 'https://login.uksw.edu.pl/cas/logout?service='
    | Empty is fine
    | @var string
    */
    'cas_logout_uri' => env('CAS_LOGOUT_URI', ''),
    
    /*
    |--------------------------------------------------------------------------
    | CAS Validation
    |--------------------------------------------------------------------------
    |
    | CAS server SSL validation: 'self' for self-signed certificate, 'ca' for
    | certificate from a CA, empty for no SSL validation
    | @var string
    */
    'cas_validation' => env('CAS_VALIDATION', ''),
    
    /*
    |--------------------------------------------------------------------------
    | CAS Certificate
    |--------------------------------------------------------------------------
    |
    | Path to the CAS certificate file
    | @var string
    */
    'cas_cert' => env('CAS_CERT', ''),
    
    /*
    |--------------------------------------------------------------------------
    | Use SAML to retrieve user attributes
    |--------------------------------------------------------------------------
    |
    | CAS can be configured to return more than just the username to a given
    | service. It could for example use an LDAP backend to return the first name,
    | last name, and email of the user. This can be activated on the client side
    | by setting 'cas_saml' to true
    | @var boolean
    */
    'cas_saml' => env('CAS_SAML', false),
    
    /*
    |--------------------------------------------------------------------------
    | SAML group name attribute
    |--------------------------------------------------------------------------
    |
    | If you are using SAML with LDAP backend you can simply check if logged
    | user is member of specific group. Type below LDAP's group attribute
    | name
    | @var string
    */
    'cas_saml_attr_groups' => env('CAS_SAML_ATTR_GROUPS', 'Groups'),
    
    /*
    |--------------------------------------------------------------------------
    | CAS session name
    |--------------------------------------------------------------------------
    |
    | Define your CAS session name
    | @var string
    */
    'cas_session_name' => env('CAS_SESSION_NAME', 'CAS_SESSION'),
],
```

Typically you will only need to include in the `.env` file the following parameters (values are only for example)

```
CAS_HOSTNAME=cas-server-host.com
CAS_URI=cas
```


## Usage

Next, you are ready to authenticate users! You will need two routes: one for redirecting the user to the CAS provider, and another for receiving the callback from the provider after authentication. We will access Caslite using the Caslite facade:

```php
<?php

namespace App\Http\Controllers;

use Caslite;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{

    // The Laravel AuthController might contain other methods and traits, please preserve them while editing

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Caslite::authenticate();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return Response
     */
    public function handleProviderCallback()
    {
        $user = Caslite::user();

        // $user->getEmail;
        // here you can store the returned information in a local User model on your database (or storage). 
        // This is particularly usefull in case of profile construction with roles and other details
        // e.g. Auth::login($local_user);
    }
}
```

The `authenticate` method takes care of sending the user to the CAS Authentication provider, while the `user` method will read the incoming request and retrieve the user's information from the provider.

Of course, you will need to define routes to your controller methods:

```php
Route::get('auth/cas', 'Auth\AuthController@redirectToProvider');
Route::get('auth/cas/callback', 'Auth\AuthController@handleProviderCallback');
```


### Retrieving User Details

Once you have a user instance, you can grab a few more details about the user:

```php
$user = Caslite::user();
$user->getEmail();
```

### Logout

when a user logsout from your application, you have to call `Caslite::logout()` to perform also the CAS logout.


## Contributing

Please see [contributing](contributing.md) and [conduct](conduct.md) for details.

## Credits

- [:author_name][link-author]
- [All Contributors][link-contributors]

## License

Caslite is open-sources under the [MIT License](LICENSE.txt).
