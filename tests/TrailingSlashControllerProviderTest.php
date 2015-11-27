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

        $app->register(new TrailingSlashControllerProvider());
        $app->mount('/', new TrailingSlashControllerProvider());

        $app->get('/index/', function () {
            return 'hunter42';
        });

        $request = Request::create('/index');
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
