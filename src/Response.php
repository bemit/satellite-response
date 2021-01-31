<?php

namespace Satellite\Response;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class Response implements ResponseFactoryInterface {
    protected $data;
    protected Psr17Factory $factory;

    public function __construct($data = null) {
        $this->factory = new Psr17Factory();
        $this->data = $data;
    }

    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface {
        return $this->factory->createResponse($code, $reasonPhrase);
    }

    /**
     * @param int $code
     * @param string $reasonPhrase
     * @return ResponseInterface
     * @throws \JsonException
     */
    public function json(int $code = 200, string $reasonPhrase = ''): ResponseInterface {
        $content = json_encode($this->data, JSON_THROW_ON_ERROR);

        $response = $this->createResponse($code, $reasonPhrase);
        return $response
            ->withBody($this->factory->createStream($content))
            ->withHeader('Content-Type', 'application/json');
    }

    /**
     * @param int $code
     * @param string $reasonPhrase
     * @return ResponseInterface
     */
    public function html(int $code = 200, string $reasonPhrase = ''): ResponseInterface {
        $response = $this->createResponse($code, $reasonPhrase);
        return $response
            ->withBody(
                $this->factory->createStream($this->data)
            )
            ->withHeader('Content-Type', 'text/html');
    }
}
