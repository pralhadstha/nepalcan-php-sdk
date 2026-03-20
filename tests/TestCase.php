<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Tests;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use OmniCargo\NepalCan\Config;
use OmniCargo\NepalCan\Http\HttpClient;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class TestCase extends PHPUnitTestCase
{
    protected function mockHttpClient(array $responseBody, int $statusCode = 200): HttpClient
    {
        $mock = new MockHandler([
            new Response($statusCode, ['Content-Type' => 'application/json'], json_encode($responseBody)),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $guzzle = new GuzzleClient(['handler' => $handlerStack]);
        $config = new Config('test-api-token');

        return new HttpClient($config, $guzzle);
    }

    protected function loadFixture(string $name): array
    {
        $path = __DIR__ . '/Fixtures/' . $name;
        $json = file_get_contents($path);

        if ($json === false) {
            throw new \RuntimeException("Fixture file not found: {$name}");
        }

        return json_decode($json, true);
    }
}
