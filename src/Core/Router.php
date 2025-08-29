<?php
namespace App\Core;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Container\ContainerInterface;

class Router
{
    private RouteCollection $routes;
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->routes = new RouteCollection();
        $this->container = $container;
    }

    public function add(string $name, string $path, array $controller, array $methods): void
    {
        $this->routes->add($name, new Route($path, ['_controller' => $controller], [], [], '', [], $methods));
    }

    public function dispatch(Request $request): Response
    {
        $context = new RequestContext();
        $context->fromRequest($request);

        $matcher = new UrlMatcher($this->routes, $context);

        try {
            $parameters = $matcher->match($request->getPathInfo());
            $controller = $parameters['_controller'];
            unset($parameters['_controller']);

            if (is_array($controller)) {
                $instance = $this->container->get($controller[0]);
                $result = call_user_func([$instance, $controller[1]], $request, $parameters);
            } else {
                $result = call_user_func($controller, $request, $parameters);
            }

            if ($result instanceof Response) {
                return $result;
            }

            if (is_array($result) || is_object($result)) {
                return new JsonResponse($result);
            }

            return new Response((string)$result);
        } catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $e) {
            return new Response('Not Found', 404);
        } catch (\Throwable $e) {
            return \App\Core\ErrorHandler::handle($e, $request);
        }
    }
}
