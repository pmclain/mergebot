<?php
declare(strict_types=1);

namespace App\Github;

use App\Exception\HttpResponseException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class Adapter
{
    const STATUS_OK = 200;

    /**
     * @var string
     */
    private $userName;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var Client
     */
    private $client;

    public function __construct(
        string $userName,
        string $accessToken,
        Client $client
    ) {
        $this->userName = $userName;
        $this->accessToken = $accessToken;
        $this->client = $client;
    }

    /**
     * @param string $url
     * @return array
     * @throws HttpResponseException
     */
    public function get(string $url): array
    {
        try {
            $response = $this->client->get($url, [
                RequestOptions::AUTH => $this->getAuthArray(),
            ]);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            throw new HttpResponseException(
                'REQUEST: ' . $e->getRequest()->getBody() . '\nRESPONSE: ' . $response->getBody(),
                $response->getStatusCode()
            );
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param string $url
     * @return string
     * @throws HttpResponseException
     */
    public function getRaw(string $url): string
    {
        try {
            $response = $this->client->get($url, [
                RequestOptions::AUTH => $this->getAuthArray()
            ]);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            throw new HttpResponseException(
                'REQUEST: ' . $e->getRequest()->getBody() . '\nRESPONSE: ' . $response->getBody(),
                $response->getStatusCode()
            );
        }

        return $response->getBody()->getContents();
    }

    /**
     * @param string $url
     * @param array $data
     * @return array
     * @throws HttpResponseException
     */
    public function post(string $url, array $data = []): array
    {
        try {
            $response = $this->client->post($url, [
                RequestOptions::AUTH => $this->getAuthArray(),
                RequestOptions::JSON => $data,
            ]);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            throw new HttpResponseException(
                sprintf(
                    'REQUEST: %s\nRESPONSE: %s',
                    $e->getRequest()->getBody()->getContents(),
                    $response->getBody()->getContents()
                ),
                $response->getStatusCode()
            );
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param string $url
     * @param array $data
     * @return array
     * @throws HttpResponseException
     */
    public function put(string $url, ?array $data = null): array
    {
        $options = [RequestOptions::AUTH => $this->getAuthArray()];

        if ($data) {
            $options[RequestOptions::JSON] = $data;
        }

        try {
            $response = $this->client->put($url, $options);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            throw new HttpResponseException(
                sprintf(
                    'REQUEST: %s\nRESPONSE: %s',
                    $e->getRequest()->getBody()->getContents(),
                    $response->getBody()->getContents()
                ),
                $response->getStatusCode()
            );
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    private function getAuthArray(): array
    {
        return [
            $this->userName,
            $this->accessToken,
        ];
    }
}
