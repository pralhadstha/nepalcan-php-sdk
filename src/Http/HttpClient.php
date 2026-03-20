<?php

declare(strict_types=1);

namespace OmniCargo\NepalCan\Http;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use OmniCargo\NepalCan\Auth\ApiKeyAuthenticator;
use OmniCargo\NepalCan\Config;
use OmniCargo\NepalCan\Exceptions\ApiException;
use OmniCargo\NepalCan\Exceptions\AuthenticationException;
use OmniCargo\NepalCan\Exceptions\NotFoundException;
use OmniCargo\NepalCan\Exceptions\ValidationException;

final class HttpClient
{
    private GuzzleClient $client;
    private ApiKeyAuthenticator $authenticator;
    private Config $config;

    public function __construct(Config $config, ?GuzzleClient $client = null)
    {
        $this->config = $config;
        $this->authenticator = new ApiKeyAuthenticator($config);
        $this->client = $client ?? new GuzzleClient([
            'base_uri' => $config->getBaseUrl(),
            'timeout' => 30,
            'connect_timeout' => 10,
        ]);
    }

    public function send(Request $request): array
    {
        $options = [
            'headers' => array_merge($this->authenticator->getHeaders(), $request->getHeaders()),
        ];

        if (!empty($request->getQueryParams())) {
            $options['query'] = $request->getQueryParams();
        }

        if (!empty($request->getBody()) && in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'])) {
            $options['json'] = $request->getBody();
        }

        try {
            $response = $this->client->request(
                $request->getMethod(),
                $request->getUri(),
                $options
            );

            $body = $response->getBody()->getContents();
            $decoded = json_decode($body, true);

            return $decoded ?? [];
        } catch (ClientException $e) {
            $this->handleClientException($e);
        } catch (GuzzleException $e) {
            throw new ApiException(
                'HTTP request failed: ' . $e->getMessage(),
                (int) $e->getCode(),
                [],
                $e
            );
        }
    }

    public function get(string $uri, array $queryParams = []): array
    {
        return $this->send(new Request('GET', $uri, [], $queryParams));
    }

    public function post(string $uri, array $body = []): array
    {
        return $this->send(new Request('POST', $uri, [], [], $body));
    }

    private function handleClientException(ClientException $e): never
    {
        $response = $e->getResponse();
        $statusCode = $response->getStatusCode();
        $body = json_decode($response->getBody()->getContents(), true) ?? [];

        match ($statusCode) {
            401 => throw new AuthenticationException(
                $body['detail'] ?? 'Authentication credentials were not provided.',
                $e
            ),
            400 => throw new ValidationException(
                $body['Error'] ?? $body,
                $body['detail'] ?? $body['message'] ?? 'Validation failed',
                $e
            ),
            404 => throw new NotFoundException(
                $body['detail'] ?? $body['message'] ?? 'Not found.',
                $e
            ),
            default => throw new ApiException(
                $body['detail'] ?? $body['message'] ?? 'API request failed',
                $statusCode,
                $body,
                $e
            ),
        };
    }
}
