<?php

namespace Satellite\Response;

use Narrowspark\HttpEmitter\SapiEmitter;
use Equip\Dispatch\MiddlewareCollection;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ResponsePipe implements RequestHandlerInterface {
    protected $middlewares = [];

    public function with(MiddlewareInterface $middleware) {
        $this->middlewares[] = $middleware;

        return $this;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface {
        $collection = new MiddlewareCollection($this->middlewares);

        return $collection->dispatch($request, static function() {
            // default handler for no-middlewares
            $factory = new Psr17Factory();
            return $factory->createResponse(404, 'Not Found')->withBody($factory->createStream());
        });
    }

    public function emit(ResponseInterface $response) {
        $emitter = new SapiEmitter();
        $emitter->emit($response);
    }
}
