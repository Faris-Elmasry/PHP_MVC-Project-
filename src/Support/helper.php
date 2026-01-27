<?php

// Helper functions - NO namespace (these are global functions)

use Elmasry\Http\Request;
use Elmasry\Http\Response;
use Elmasry\View\View;
use Elmasry\Application;

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

if (!function_exists('app')) {

    function app()
    {
        //static for holding the values in all places not changing
        static $instance = null;

        if (!$instance) {
            return $instance = new Application;
        }
        return $instance;
    }
}

if (!function_exists('config')) {
    function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return app()->config();
        }

        if (is_array($key)) {
            return app()->config()->set($key);
        }

        return app()->config()->get($key, $default);
    }
}

if (!function_exists('config_path')) {
    function config_path()
    {
        return base_path() . 'config/';
    }
}

if (!function_exists('old')) {
    function old($key)
    {
        if (app()->session->hasflash('old')) {
            $oldData = app()->session->getFlash('old');
            // Assuming old data is an array
            return $oldData[$key] ?? null;
        }
        return null;
    }
}

if (!function_exists('request')) {

    function request($key = null)
    {
        $instance = new Request();

        // If Array
        if (is_array($key)) {
            return $instance->only($key);
        }

        // If String
        if ($key !== null) {
            return $instance->get($key);
        }

        // If nothing
        return $instance;
    }
}

if (!function_exists('back')) {
    function back()
    {
        return (new Response())->back();
    }
}

if (!function_exists('redirect')) {
    function redirect($path)
    {
        header("Location: {$path}");
        exit;
    }
}
