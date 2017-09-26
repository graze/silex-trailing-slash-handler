# graze/silex-trailing-slash-handler

[![Build Status][ico-build]][travis]
[![Coverage Status][ico-coverage]][coverage]
[![Quality Score][ico-quality]][quality] 
[![Latest Version][ico-package]][package]
[![PHP ~5.5][ico-engine]][lang]
[![MIT Licensed][ico-license]][license]

Handle requests missing a trailing slash in Silex by
appending a slash and issuing an internal sub-request.

See [silexphp/Silex #149](https://github.com/silexphp/Silex/issues/149) for more
information about the default Silex routing behavior.

We try to support all commonly used versions of Silex including:

- [Silex 2][silex-2] on [`master`][branch-master] branch, `^2.0` releases
- [Silex 1.3][silex-1] on [`1.x`][branch-1.x] branch, `^1.0` releases

<!-- Links -->
[travis]: https://travis-ci.org/graze/silex-trailing-slash-handler
[lang]: https://secure.php.net
[package]: https://packagist.org/packages/graze/silex-trailing-slash-handler
[license]: https://github.com/graze/silex-trailing-slash-handler/blob/master/LICENSE
[coverage]: https://scrutinizer-ci.com/g/graze/silex-trailing-slash-handler/code-structure
[quality]: https://scrutinizer-ci.com/g/graze/silex-trailing-slash-handler
[silex-2]: https://github.com/silexphp/Silex
[silex-1]: https://github.com/silexphp/Silex/tree/1.3
[branch-master]: https://github.com/graze/silex-trailing-slash-handler/tree/master
[branch-1.x]: https://github.com/graze/silex-trailing-slash-handler/tree/1.x

<!-- Images -->
[ico-license]: https://img.shields.io/packagist/l/graze/silex-trailing-slash-handler.svg?style=flat-square
[ico-package]: https://img.shields.io/packagist/v/graze/silex-trailing-slash-handler.svg?style=flat-square
[ico-build]: https://img.shields.io/travis/graze/silex-trailing-slash-handler/master.svg?style=flat-square
[ico-engine]: https://img.shields.io/badge/php-%3E%3D5.6-8892BF.svg?style=flat-square
[ico-coverage]: https://img.shields.io/scrutinizer/coverage/g/graze/silex-trailing-slash-handler.svg?style=flat-square
[ico-quality]: https://img.shields.io/scrutinizer/g/graze/silex-trailing-slash-handler.svg?style=flat-square

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

The content of this library is released under the **MIT License** by **Nature Delivered Ltd.**

You can find a copy of this license in [`LICENSE`][license] or at http://opensource.org/licenses/mit.
