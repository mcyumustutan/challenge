<?php


namespace App\Http\Traits;

use Illuminate\Support\Arr;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

trait HttpClient
{

    protected $baseServer = null;

    function __construct()
    {
        $this->baseServer = config('app.baseServer');
    }

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
            $params = Arr::collapse([$default_params, $params]);
            $query = http_build_query($params);
            
            $response = $client->get($this->baseServer . "{$path}?{$query}");

            $body = $response->getBody()->getContents();

            return json_decode($body, true);
        } catch (ClientException  $exception) {
            $response = $exception->getResponse();
            $data = $response->getBody()->getContents();
            $data = json_decode($data, true);

            if (Arr::exists('error', $data)) {
                if (Arr::accessible($data['error']) && Arr::exists('message', $data['error'])) {
                    abort($response->getStatusCode(), $data['error']['message']);
                } else {
                    abort($response->getStatusCode(), $data['error']);
                }
            } else {
                abort($response->getStatusCode(), "Eksik Parametre: " . json_encode($data, true));
            }
        }
    }
}
