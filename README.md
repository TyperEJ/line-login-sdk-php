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

```php
$httpClient = new \EJLin\LINELogin\GuzzleHTTPClient();
$LINELogin = new \EJLin\LINELogin($httpClient, ['clientId' => '<channel id>','clientSecret' => '<channel secret>']);
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