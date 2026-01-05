<?php

namespace Elmasry\Http;

use Elmasry\View\View;

class Route
{
    protected Request $request;
    protected Response $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public static array $routes = [];

    public static function get($route, $action)
    {
        self::$routes['GET'][$route] = $action;
    }

    public static function post($route, $action)
    {
        self::$routes['POST'][$route] = $action;
    }

    public function resolve()
    {
        $path = $this->request->path();
        $method = $this->request->method();

        $action = self::$routes[$method][$path] ?? false;

        if (!array_key_exists($path, self::$routes[$method] ?? []) || $action === false) {
            return View::makeError('404');
        }

        if (is_callable($action)) {
            return call_user_func_array($action, []);
        }

        if (is_array($action)) {
            return call_user_func_array(
                [new $action[0], $action[1]],
                []
            );
        }
    }
}
