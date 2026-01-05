<?php

namespace Elmasry\Http;

class Request
{
    public function path(): string
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        return str_contains($path, '?') ? explode('?', $path)[0] : $path;
    }

    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }
}