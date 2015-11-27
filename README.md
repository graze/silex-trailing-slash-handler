# graze/silex-trailing-slash-handler

[![Build Status][ico-build]][travis]
[![Latest Version][ico-package]][package]
[![PHP ~5.5][ico-engine]][lang]
[![MIT Licensed][ico-license]][license]

Handle requests missing a trailing slash in Silex by
appending a slash and issuing an internal sub-request.

See [silexphp/Silex #149](https://github.com/silexphp/Silex/issues/149) for more
information about the default Silex routing behavior.

<!-- Links -->
[travis]: https://travis-ci.org/graze/silex-trailing-slash-handler
[lang]: https://secure.php.net
[package]: https://packagist.org/packages/graze/silex-trailing-slash-handler
[license]: https://github.com/graze/silex-trailing-slash-handler/blob/master/LICENSE

<!-- Images -->
[ico-license]: https://img.shields.io/packagist/l/graze/silex-trailing-slash-handler.svg
[ico-package]: https://img.shields.io/packagist/v/graze/silex-trailing-slash-handler.svg
[ico-build]: https://img.shields.io/travis/graze/silex-trailing-slash-handler/master.svg
[ico-engine]: https://img.shields.io/badge/php-%3E%3D5.6-8892BF.svg

## Usage

```bash
~$ composer require graze/silex-trailing-slash-handler
```

```php
$app->get('/', function () {
    return 'Hello World!';
})

$provider = new \Graze\Silex\ControllerProvider\TrailingSlashControllerProvider();

$app->register($provider);
$app->mount('/', $provider);
```

:information_source: Define all your routes first before mounting the controller
provider if you want routes with no trailing slash to be matched.

## License

The content of this library is released under the **MIT License** by **Nature Delivered Ltd.**.

You can find a copy of this license in [`LICENSE`][license] or at http://opensource.org/licenses/mit.
