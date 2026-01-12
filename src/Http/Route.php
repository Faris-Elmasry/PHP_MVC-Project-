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

    /**
     * Match a route pattern with the current path
     * Returns matched parameters or false if no match
     */
    protected function matchRoute($pattern, $path)
    {
        // Convert route pattern to regex
        // {id} becomes ([^/]+)
        $regex = preg_replace('/\{([a-zA-Z_]+)\}/', '([^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (preg_match($regex, $path, $matches)) {
            // Extract parameter names from pattern
            preg_match_all('/\{([a-zA-Z_]+)\}/', $pattern, $paramNames);
            
            // Remove the full match, keep only groups
            array_shift($matches);
            
            // Combine parameter names with values
            $params = [];
            foreach ($paramNames[1] as $index => $name) {
                $params[$name] = $matches[$index] ?? null;
            }
            
            return $params;
        }

        return false;
    }

    /**
     * Find matching route and return action with parameters
     */
    protected function findRoute($method, $path)
    {
        $routes = self::$routes[$method] ?? [];

        // First try exact match
        if (isset($routes[$path])) {
            return ['action' => $routes[$path], 'params' => []];
        }

        // Then try pattern matching
        foreach ($routes as $pattern => $action) {
            if (strpos($pattern, '{') !== false) {
                $params = $this->matchRoute($pattern, $path);
                if ($params !== false) {
                    return ['action' => $action, 'params' => $params];
                }
            }
        }

        return null;
    }

    //this function is to resolve the route and call the action
    public function resolve()
    {
        $path = $this->request->path();
        $method = $this->request->method();

        $route = $this->findRoute($method, $path);

        if ($route === null) {
            return View::makeError('404');
        }

        $action = $route['action'];
        $params = array_values($route['params']);

        if (is_callable($action)) {
            return call_user_func_array($action, $params);
        }

        if (is_array($action)) {
            return call_user_func_array(
                [new $action[0], $action[1]],
                $params
            );
        }
    }
}
