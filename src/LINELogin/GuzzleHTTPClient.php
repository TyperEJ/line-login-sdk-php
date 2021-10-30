<?php

namespace EJLin\LINELogin;

use EJLin\LINELogin\Exception\HTTPClientException;
use EJLin\LINELogin\Exception\LINEResponseException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

class GuzzleHTTPClient implements HTTPClient
{
    protected $guzzleClient;
    protected $defaultHeaders;

    public function __construct()
    {
        $this->guzzleClient = new Client();
        $this->defaultHeaders = [
            'User-Agent' => 'LINE-LINELogin-PHP',
        ];
    }

    /**
     * @param string $url
     * @param array $data
     * @param array $headers
     * @return Response
     * @throws HTTPClientException
     * @throws LINEResponseException
     */
    public function get(string $url, array $data = [], array $headers = [])
    {
        try {
            $response = $this->guzzleClient->get($url, [
                'headers' => array_merge($this->defaultHeaders, $headers),
                'query' => $data,
            ]);
        } catch (ClientException $clientException) {

            $response = $clientException->getResponse();

            throw new LINEResponseException($response->getBody()->getContents());

        } catch (GuzzleException $guzzleException) {

            throw new HTTPClientException($guzzleException->getMessage());

        }

        return new Response(
            $response->getStatusCode(),
            $response->getBody()->getContents(),
            $response->getHeaders()
        );
    }

    /**
     * @param string $url
     * @param array $data
     * @param array|null $headers
     * @return Response
     * @throws HTTPClientException
     * @throws LINEResponseException
     */
    public function post(string $url, array $data, array $headers = null)
    {
        try {
            $response = $this->guzzleClient->post($url, [
                'headers' => $headers ? array_merge($headers, $this->defaultHeaders) : $this->defaultHeaders,
                'form_params' => $data,
            ]);
        } catch (ClientException $clientException) {

            $response = $clientException->getResponse();

            throw new LINEResponseException($response->getBody()->getContents());

        } catch (GuzzleException $guzzleException) {

            throw new HTTPClientException($guzzleException->getMessage());

        }

        return new Response(
            $response->getStatusCode(),
            $response->getBody()->getContents(),
            $response->getHeaders()
        );
    }
}