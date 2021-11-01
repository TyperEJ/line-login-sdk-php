# LINE Login for PHP

SDK of the LINE Login API for PHP

## Documentation

See the [official API documentation](https://developers.line.biz/en/docs/line-login/) for more information.

## Installation

Use the package manager [composer](https://getcomposer.org) to install package.

```bash
composer require ejlin/line-login-sdk-php
```

## Usage

###Pure PHP version:

```php
$httpClient = new \EJLin\LINELogin\GuzzleHTTPClient();

// Channel Basic Information: https://developers.line.biz/console/channel/<channel id>/basics
$LINELogin = new \EJLin\LINELogin($httpClient, ['clientId' => '<channel id>','clientSecret' => '<channel secret>']);

if(isset($_GET['code']) && isset($_GET['state']))
{
    //TODO: Check the state code same as what you requested
    
    // Request access token from the LINE platform
    $token = $LINELogin->requestToken(
        'https://yourdomain.com', // The url must be the same as requested
        $_GET['code'] // Each code only can request a token once
    );
    
    // Get user profile from the LINE platform
    $userProfile = $LINELogin->getUserProfile($token);
    
    printf("Hello %s !",$userProfile->getDisplayName());
    
    exit;
}

// A unique alphanumeric string used to prevent cross-site request forgery
$state = \EJLin\LINELogin\Helper::randomString(40);

$authorizeUrl = $LINELogin->makeAuthorizeUrl(
    'https://yourdomain.com', // Callback URL: https://developers.line.biz/console/channel/<channel id>/line-login
    'profile openid email', // Permissions requested from the user: https://developers.line.biz/en/docs/line-login/integrate-line-login/#scopes
    $state
);

// Redirect to authorize url
header("Location: $authorizeUrl");
exit;

```

###Laravel support:

After installed, add `LINE_LOGIN_CHANNEL_ID` and `LINE_LOGIN_CHANNEL_SECRET` to .env
```
LINE_LOGIN_CHANNEL_ID=<channel id>
LINE_LOGIN_CHANNEL_SECRET=<channel secret>
```
then you can use `LINELogin` and `LINELoginHelper` facades like following.

```php
$state = \EJLin\Laravel\Facades\LINELoginHelper::randomString(40);

$authorizeUrl = \EJLin\Laravel\Facades\LINELogin::makeAuthorizeUrl([
    'https://yourdomain.com',
    'profile openid email',
    $state
]);
```

## Reference 
- [kkdai / line-login-sdk-go](https://github.com/kkdai/line-login-sdk-go)

- [line / line-bot-sdk-php](https://github.com/line/line-bot-sdk-php)

- [laravel / laravel](https://github.com/laravel/laravel)

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)