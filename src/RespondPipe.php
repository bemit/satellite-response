<?php

namespace Satellite\Response;

use Equip\Dispatch\MiddlewareCollection;
use Middlewares\Utils\Factory;
use Narrowspark\HttpEmitter\SapiEmitter;
use Satellite\KernelRoute\RouteEvent;

class RespondPipe {

    protected $middlewares = [];

    public const ROUTER = '_ROUTER_';

    public function with($middleware) {
        $this->middlewares[] = $middleware;

        return $this;
    }

    public function emit(RouteEvent $resp) {
        if(!$resp->router || !$resp->request) {
            return $resp;
        }

        $mws = [];
        foreach($this->middlewares as $middleware) {
            if($middleware === static::ROUTER) {
                // add router to handle route-matching
                $mws[] = $resp->router;
            } else {
                $mws[] = $middleware;
            }
        }

        $collection = new MiddlewareCollection($mws);

        $response = $collection->dispatch($resp->request, static function() {
            // default handler for end of collection
            return Factory::getResponseFactory()->createResponse();
        });

        $emitter = new SapiEmitter();
        $emitter->emit($response);

        return $resp;
    }
}
