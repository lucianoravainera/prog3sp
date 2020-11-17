<?php

namespace Middlewares;

use Components\GenericResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Components\Token;
use Slim\Psr7\Response;

class FormDataMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler)
    {
     
    }
}
