<?php

namespace Satellite\Response;

use Equip\Dispatch\MiddlewareCollection;
use Middlewares\Utils\Factory;
use Narrowspark\HttpEmitter\SapiEmitter;
use Psr\Http\Message\ServerRequestInterface;

class RespondPipe {

    protected $middlewares = [];

    public function with($middleware) {
        $this->middlewares[] = $middleware;

        return $this;
    }

    public function emit(ServerRequestInterface $request) {
        $collection = new MiddlewareCollection($this->middlewares);

        $response = $collection->dispatch($request, static function() {
            // default handler for end of collection
            return Factory::getResponseFactory()->createResponse();
        });

        $emitter = new SapiEmitter();
        $emitter->emit($response);
    }
}
