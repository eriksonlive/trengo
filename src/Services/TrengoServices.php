<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TrengoServices
{
    private Client $client;
    private CacheInterface $cache;
    private LoggerInterface $logger;
    private string $apiUrl;
    private int $cacheTtl;
    private string $token;

    public function __construct(CacheInterface $cache, LoggerInterface $logger, ParameterBagInterface $params, int $cacheTtl = 3600)
    {
        $this->client = new Client();
        $this->cache = $cache;
        $this->logger = $logger;
        $this->apiUrl = $_ENV['API_URL'];
        $this->token = $_ENV['API_TOKEN'];
        $this->cacheTtl = $cacheTtl;
    }

    public function request(string $endpoint, array $queryParams = [], string $method = 'GET', array $body = []): array
    {
        // Crear una clave de caché única
        $cacheKey = 'api_data_' . md5($method . $endpoint . json_encode($queryParams) . json_encode($body));

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($endpoint, $method, $queryParams, $body) {
            $item->expiresAfter($this->cacheTtl);

            dump('Fetching from API');

            try {
                $options = [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->token,
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json',
                    ],
                    'query' => $queryParams, // Parámetros en la URL
                    // 'verify' => false,
                ];

                // Agregar el cuerpo de la petición si el método lo requiere
                if (!empty($body) && in_array($method, ['POST', 'PUT', 'PATCH'])) {
                    $options['json'] = $body;
                }

                $response = $this->client->request($method, $this->apiUrl . '/' . ltrim($endpoint, '/'), $options);

                return json_decode($response->getBody()->getContents(), true);
            } catch (GuzzleException $e) {
                $this->logger->error('Error fetching data from API: ' . $e->getMessage());
                return []; // Retornar un array vacío en caso de error
            }
        });
    }
}
