<?php

namespace Elmasry\Http;

class Response
{
    public function setStatusCode(int $code): void
    {
        http_response_code($code);
    }
}