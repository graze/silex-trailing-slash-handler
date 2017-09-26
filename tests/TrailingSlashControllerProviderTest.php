<?php

namespace Graze\Silex\Tests\ControllerProvider;

use Graze\Silex\ControllerProvider\TrailingSlashControllerProvider;
use Mockery;
use Pimple\ServiceProviderInterface;
use Psr\Log\LoggerInterface;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
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

        $this->assertInstanceOf(UrlMatcher::class, $app['request_matcher']);
    }

    public function testShouldMount()
    {
        $app = new Application();

        // `mount` should return the application.
        $this->assertSame(
            $app,
            $app->mount('/', new TrailingSlashControllerProvider())
        );
    }

    /**
     * @dataProvider requestMethodProvider
     *
     * @param string $method
     */
    public function testShouldRespondOkWithoutTrailingSlash($method)
    {
        $app = new Application();

        $app->match('/foo/', function () {
            return 'hunter42';
        })->method($method);

        $app->match('/foo/bar/', function () {
            return 'What\'s the question?';
        })->method($method);

        $app->match('/foo/bar/baz/', function () {
            return 'Fizz Buzz';
        })->method($method);

        $app->register(new TrailingSlashControllerProvider());
        $app->mount('/', new TrailingSlashControllerProvider());

        $request = Request::create('/foo', $method);
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());

        $request = Request::create('/foo/bar', $method);
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());

        $request = Request::create('/foo/bar/baz', $method);
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * This just shows that the mount order for the controller provider doesn't
     * matter when all routes are defined with a trailing slash.
     *
     * @dataProvider requestMethodProvider
     *
     * @param string $method
     */
    public function testShouldRespondOkWithoutTrailingSlashWhenMountedFirst($method)
    {
        $app = new Application();

        $app->register(new TrailingSlashControllerProvider());
        $app->mount('/', new TrailingSlashControllerProvider());

        $app->match('/foo/', function () {
            return 'hunter42';
        })->method($method);

        $app->match('/foo/bar/', function () {
            return 'What\'s the question?';
        })->method($method);

        $app->match('/foo/bar/baz/', function () {
            return 'Fizz Buzz';
        })->method($method);

        $request = Request::create('/foo', $method);
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());

        $request = Request::create('/foo/bar', $method);
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());

        $request = Request::create('/foo/bar/baz', $method);
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * This just shows that the controller provider is compatiable with other
     * controller providers.
     *
     * @dataProvider requestMethodProvider
     *
     * @param string $method
     */
    public function testShouldRespondOkWithoutTrailingSlashWithMountedControllers($method)
    {
        $app = new Application();

        $app->register(new TrailingSlashControllerProvider());
        $app->mount('/', new TrailingSlashControllerProvider());

        $controller = $app['controllers_factory'];

        $controller->match('/foo/', function () {
            return 'hunter42';
        })->method($method);

        $controller->match('/foo/bar/', function () {
            return 'hunter42';
        })->method($method);

        $controller->match('/foo/bar/baz/', function () {
            return 'hunter42';
        })->method($method);

        $provider = Mockery::mock(ControllerProviderInterface::class);
        $provider->shouldReceive('connect')->andReturn($controller);

        $app->mount('/', $provider);

        $request = Request::create('/foo', $method);
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());

        $request = Request::create('/foo/bar', $method);
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());

        $request = Request::create('/foo/bar/baz', $method);
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @return array
     */
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

        $app->get('/foo/', function () {
            return 'hunter42';
        });

        $app->get('/foo/bar/', function () {
            return 'What\'s the question?';
        });

        $app->get('/foo/bar/baz/', function () {
            return 'Fizz Buzz';
        });

        $app->register(new TrailingSlashControllerProvider());
        $app->mount('/', new TrailingSlashControllerProvider());

        $request = Request::create('/foo', 'HEAD');
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());

        $request = Request::create('/foo/bar', 'HEAD');
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());

        $request = Request::create('/foo/bar/baz', 'HEAD');
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * This is just to show when defining routes that the trailing slash is
     * required when the controller provider is mounted before any other routes.
     *
     * @dataProvider requestMethodProvider
     *
     * @param string $method
     */
    public function testWillRespondWithNotFoundForRouteWithNoTrailingSlashWhenMountedFirst($method)
    {
        $app = new Application();

        $app->register(new TrailingSlashControllerProvider());
        $app->mount('/', new TrailingSlashControllerProvider());

        $app->match('/foo', function () {
            return 'hunter42';
        })->method($method);

        $app->match('/foo/bar', function () {
            return 'hunter42';
        })->method($method);

        $request = Request::create('/foo', $method);
        $response = $app->handle($request);

        $this->assertEquals(404, $response->getStatusCode());

        $request = Request::create('/foo/bar', $method);
        $response = $app->handle($request);

        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * This shows that by default it will return 404 when the routes are defined with trailing slashes
     *
     * @dataProvider requestMethodProvider
     *
     * @param string $method
     */
    public function testWillRespondWillNotFoundForRouteWithTrailingSlashWhenNotMounted($method)
    {
        $app = new Application();

        $app->match('/foo/', function () {
            return 'hunter42';
        })->method($method);

        $app->match('/foo/bar/', function () {
            return 'hunter42';
        })->method($method);

        $app->register(new TrailingSlashControllerProvider());

        $request = Request::create('/foo', $method);
        $response = $app->handle($request);

        $this->assertEquals(404, $response->getStatusCode());

        $request = Request::create('/foo/bar', $method);
        $response = $app->handle($request);

        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * This is just to show when defining routes with no trailing slash before
     * mounting the controller provider they should respond as expected.
     *
     * @dataProvider requestMethodProvider
     *
     * @param string $method
     */
    public function testWillRespondWithOkForRouteWithNoTrailingSlashWhenMountedLast($method)
    {
        $app = new Application();

        $app->match('/foo', function () {
            return 'hunter42';
        })->method($method);

        $app->match('/foo/bar', function () {
            return 'hunter42';
        })->method($method);

        $app->register(new TrailingSlashControllerProvider());
        $app->mount('/', new TrailingSlashControllerProvider());

        $request = Request::create('/foo', $method);
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());

        $request = Request::create('/foo/bar', $method);
        $response = $app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test the case in which a request should have both query
     * string params and body params
     */
    public function testWillHandleQueryAndBodySeparately()
    {
        $app = new Application();

        $app->match('/foo/', function (Request $request) {
            $response = [
                'query' => $request->query->all(),
                'request' => $request->request->all()
            ];
            return json_encode($response, true);
        })->method('POST');

        $app->register(new TrailingSlashControllerProvider());
        $app->mount('/', new TrailingSlashControllerProvider());

        $request = Request::create('/foo?q=1', 'POST', ['r' => 2]);
        $response = $app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getContent(), true);
        $this->assertRequestQuery($body);

        $request = Request::create('/foo/?q=1', 'POST', ['r' => 2]);
        $response = $app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getContent(), true);
        $this->assertRequestQuery($body);
    }

    /**
     * @param array $body
     */
    private function assertRequestQuery(array $body)
    {
        $this->assertArrayHasKey('query', $body);
        $this->assertArrayHasKey('request', $body);

        $this->assertArrayHasKey('q', $body['query']);
        $this->assertEquals(1, $body['query']['q']);

        $this->assertArrayHasKey('r', $body['request']);
        $this->assertEquals(2, $body['request']['r']);
    }

    public function testLogging()
    {
        $app = new Application();

        $logger = Mockery::mock(LoggerInterface::class)->makePartial()->shouldIgnoreMissing();
        $app['logger'] = $logger;

        $app->match('/foo/', function () {
            return 'hunter42';
        })->method('GET');

        $logger->shouldReceive('debug')
               ->with('Appending a trailing slash for the request to `/foo`.')
               ->once();
        $logger->shouldReceive('debug')
               ->with('Overriding the default Silex url matcher to Symfony\Component\Routing\Matcher\UrlMatcher.')
               ->once();

        $app->register(new TrailingSlashControllerProvider());
        $app->mount('/', new TrailingSlashControllerProvider());

        $request = Request::create('/foo', 'GET');
        $response = $app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
