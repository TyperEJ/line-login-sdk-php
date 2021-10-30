<?php

namespace EJLin\LINELogin;

interface HTTPClient
{
    /**
     * Sends GET request
     *
     * @param string $url Request URL.
     * @param array $data URL parameters.
     * @param array $headers
     * @return Response Response of API request.
     */
    public function get(string $url, array $data = [], array $headers = []);

    /**
     * Sends POST request
     *
     * @param string $url Request URL.
     * @param array $data Request body.
     * @param array|null $headers Request headers.
     * @return Response Response of API request.
     */
    public function post(string $url, array $data, array $headers = null);
}
