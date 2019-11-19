<?php

namespace Satellite\Response;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Respond {

    /**
     * Middleware Factory for conditional usage
     *
     * @param $data
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     *
     * @throws \Exception
     * @return \Psr\Http\Message\ResponseInterface
     */
    public static function json($data, ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $request = $request->withAttribute('content_type', 'json');

        return (new RespondMiddleware($data))->process($request, $handler);
    }
}
