# graze/silex-trailing-slash-handler

:leftwards_arrow_with_hook: Handle requests missing a trailing slash in Silex by appending a slash and issuing an internal sub-request.

See [silexphp/Silex #149](https://github.com/silexphp/Silex/issues/149) for more information about the default Silex routing behavior.

## Usage

```php
$provider = new \Graze\Silex\ControllerProvider\TrailingSlashControllerProvider();

$app->register($provider);
$app->mount('/', $provider);
```
