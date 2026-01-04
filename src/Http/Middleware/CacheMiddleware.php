<?php

declare(strict_types=1);

namespace LemonSqueezy\Http\Middleware;

use LemonSqueezy\Cache\CacheInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class CacheMiddleware implements MiddlewareInterface
{
    private CacheInterface $cache;
    private int $ttl;

    public function __construct(CacheInterface $cache, int $ttl = 3600)
    {
        $this->cache = $cache;
        $this->ttl = $ttl;
    }

    public function process(RequestInterface $request, ClientInterface $client): ResponseInterface
    {
        if ($request->getMethod() !== 'GET') {
            return $client->sendRequest($request);
        }

        $key = (string) $request->getUri();

        if ($this->cache->has($key)) {
            $data = $this->cache->get($key);
            return new \GuzzleHttp\Psr7\Response($data['status'], $data['headers'], $data['body']);
        }

        $response = $client->sendRequest($request);

        if ($response->getStatusCode() === 200) {
            $data = [
                'status' => $response->getStatusCode(),
                'headers' => $response->getHeaders(),
                'body' => (string) $response->getBody(),
            ];
            $this->cache->set($key, $data, $this->ttl);
        }

        return $response;
    }
}
