<?php

namespace Graze\Silex\ControllerProvider;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;

/**
 * A controller provider to convert requests missing a trailing slash into an
 * internal sub-request with a slash appended to the requests url.
 *
 * NOTE: You _must_ either mount this class after all other routes are defined,
 * or define _all_ other routes with a trailing slash.
 *
 * Usage:
 *
 * ```
 * $provider = new \Graze\Silex\ControllerProvider\TrailingSlashControllerProvider();
 * $app->register($provider);
 * $app->mount('/', $provider);
 * ```
 */
final class TrailingSlashControllerProvider implements ControllerProviderInterface, ServiceProviderInterface
{
    /**
     * @param Application $app
     *
     * @return \Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $handler = function ($resource) use ($app) {
            if ($app['logger']) {
                $app['logger']->debug(sprintf('Appending a trailing slash for the request to `/%s`.', $resource));
            }

            $request = Request::create(
                '/' . $resource . '/',
                $app['request']->getMethod(),
                array_merge($app['request']->query->all(), $app['request']->request->all()),
                $app['request']->cookies->all(),
                $app['request']->files->all(),
                $app['request']->server->all(),
                $app['request']->getContent()
            );

            // Make an internal sub-request based off the one that would have 404'd.
            // http://silex.sensiolabs.org/doc/usage.html#forwards
            return $app->handle($request, HttpKernelInterface::SUB_REQUEST);
        };

        /**
         * Register the catch-all route.
         *
         * Use a look behind assertion to ensure we only match routes
         * with no trailing slash.
         *
         * @link https://stackoverflow.com/questions/16398471/regex-not-ending-with
         */
        $controllers->match('/{resource}', $handler)
                    ->assert('resource', '.*(?<!\/)$')
                    ->bind('no_trailing_slash_handler');

        return $controllers;
    }

    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
        /**
         * We override the default RedirectableUrlMatcher so that Silex doesn't
         * respond with 301 to GET requests missing a trailing slash.
         */
        $app['url_matcher'] = $app->share(function () use ($app) {
            if ($app['logger']) {
                $app['logger']->debug(sprintf('Overriding the default Silex url matcher to %s.', UrlMatcher::class));
            }

            return new UrlMatcher($app['routes'], $app['request_context']);
        });
    }

    /**
     * @param Application $app
     */
    public function boot(Application $app)
    {
    }
}
