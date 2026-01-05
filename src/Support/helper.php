<?php

// Helper functions - NO namespace (these are global functions)

use Elmasry\View\View;

if (!function_exists('env')) {
    function env($key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }
}

if (!function_exists('value')) {
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (!function_exists('base_path')) {
    function base_path($path = '')
    {
        return dirname(__DIR__) . '/../' . $path;
    }
}

if (!function_exists('view_path')) {
    function view_path($path = '')
    {
        return base_path() . 'views/' . $path;
    }
}

if (!function_exists('view')) {
    function view($view, $params = [])
    {
        return View::make($view, $params);
    }
}