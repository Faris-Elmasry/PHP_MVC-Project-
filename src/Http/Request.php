<?php

namespace Elmasry\Http;
use Elmasry\Support\Arr;

//for getting the path and method and explode the url if there are parameters
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

    public function all(){
return $_REQUEST;
    }

    public function only ($keys) {
 return Arr::only($this->all() , $keys);
    }

    public function get($key){
        return Arr::get($this->all() ,$key); 
    }
}