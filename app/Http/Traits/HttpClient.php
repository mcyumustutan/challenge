<?php


namespace App\Http\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

trait HttpClient
{

    protected $baseServer = null;

    function get(string $path, array $params = [])
    {

        if (is_null($this->baseServer)) {
            $this->baseServer = env("MOCK_API_ADDR");
        }

        try {
            $client = new Client();
            $default_params = [
                "lang" => env("APP_DEFAULT_LANG"),
                "token" => request()->token,
                "accessToken" => request()->token
            ];
            $params = array_merge($default_params, $params);
            $query = http_build_query($params);
            $response = $client->get($this->baseServer . "{$path}?{$query}");

            $body = $response->getBody()->getContents();

            return json_decode($body, true);
        } catch (ClientException  $exception) {
            $response = $exception->getResponse();
            $data = $response->getBody()->getContents();
            $data = json_decode($data, true);

            if (array_key_exists('error', $data)) {
                if (is_array($data['error']) && array_key_exists('message', $data['error'])) {
                    return abort($response->getStatusCode(), $data['error']['message']);
                } else {
                    return abort($response->getStatusCode(), $data['error']);
                }
            } else {
                return abort($response->getStatusCode(), "Eksik Parametre: " . json_encode($data, true));
            }
        }
    }
}
