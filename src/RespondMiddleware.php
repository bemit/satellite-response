<?php

namespace Satellite\Response;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RespondMiddleware implements MiddlewareInterface {
    protected $content_types = [
        'application/json' => 'json',
    ];

    protected $data;

    public function __construct($data = []) {
        $this->data = $data;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Server\RequestHandlerInterface $handler
     *
     * @throws \Exception
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $type = $request->getAttribute('content_type');

        echo $this->parse($type);

        $resp = $handler->handle($request);

        switch($type) {
            case 'json':
                return $resp->withHeader('Content-Type', 'application/json');
                break;
        }

        return $resp;
    }

    protected function getType(ServerRequestInterface $request) {
        $ct = $request->getHeaderLine('Content-Type');

        foreach($this->content_types as $content_type => $type) {
            if(stripos($ct, $content_type) === 0) {
                return $type;
            }
        }

        return false;
    }

    protected function parse($type) {
        $data = $this->data;
        switch($type) {
            case 'json':
                if(defined('JSON_THROW_ON_ERROR')) {
                    $data = json_encode($this->data, JSON_THROW_ON_ERROR);
                } else {
                    $data = json_encode($this->data);
                    $code = json_last_error();

                    if($code !== JSON_ERROR_NONE) {
                        throw new \Exception(sprintf('JSON: %s', json_last_error_msg()), $code);
                    }
                }

                return $data;

            default:
                break;
        }

        return is_string($data) ? $data : '';
    }
}
