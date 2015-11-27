<?php

namespace Graze\Silex\Tests\ControllerProvider;

use Graze\Silex\ControllerProvider\TrailingSlashControllerProvider;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;

/**
 * TrailingSlashControllerProvider test cases.
 */
class TrailingSlashControllerProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldInitalize()
    {
        $provider = new TrailingSlashControllerProvider();

        $this->assertInstanceOf(ControllerProviderInterface::class, $provider);
        $this->assertInstanceOf(ServiceProviderInterface::class, $provider);
    }

    public function testShouldRegisterUrlMatcher()
    {
        $app = new Application();
        $app->register(new TrailingSlashControllerProvider());

        $this->assertInstanceOf(UrlMatcher::class, $app['url_matcher']);
    }

    public function testShouldMount()
    {
        $app = new Application();

        $this->assertInstanceOf(
            Application::class,
            $app->mount('/', new TrailingSlashControllerProvider())
        );
    }

    /**
     * @dataProvider requestMethodProvider
     */
    public function testShouldRespondOkWithoutTrailingSlash($method)
    {
        $app = new Application();
        $app->register(new TrailingSlashControllerProvider());
        $app->mount('/', new TrailingSlashControllerProvider());

        $app->match('/foo/', function () {
            return 'hunter42';
        })->method($method);

        $request = Request::create('/foo', $method);
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function requestMethodProvider()
    {
        return [
            ['GET'],
            ['POST'],
            ['PUT'],
            ['PATCH'],
            ['DELETE'],
            ['PURGE'],
            ['OPTIONS'],
            ['TRACE'],
            ['CONNECT'],
        ];
    }

    public function testShouldRespondOkToHeadWithoutTrailingSlash()
    {
        $app = new Application();
        $app->register(new TrailingSlashControllerProvider());
        $app->mount('/', new TrailingSlashControllerProvider());

        $app->get('/foo/', function () {
            return 'hunter42';
        });

        $request = Request::create('/foo', 'HEAD');
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * This is just to show when defining routes that the trailing slash is
     * required when the controller provider is mounted before any other routes.
     */
    public function testWillRespondWithNotFoundForRouteWithNoTrailingSlashWhenMountedFirst()
    {
        $app = new Application();
        $app->register(new TrailingSlashControllerProvider());
        $app->mount('/', new TrailingSlashControllerProvider());

        $app->get('/foo', function () {
            return 'hunter42';
        });

        $request = Request::create('/foo');
        $response = $app->handle($request);

        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * This is just to show when defining routes with no trailing slash before
     * mounting the controller provider they should respond as expected.
     */
    public function testWillRespondWithOkForRouteWithNoTrailingSlashWhenMountedLast()
    {
        $app = new Application();
        $app->register(new TrailingSlashControllerProvider());

        $app->get('/foo', function () {
            return 'hunter42';
        });

        $app->mount('/', new TrailingSlashControllerProvider());

        $request = Request::create('/foo');
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
