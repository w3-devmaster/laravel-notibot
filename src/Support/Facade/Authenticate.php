<?php

namespace W3Devmaster\Notibot\Support\Facade;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Authenticate {
    public $status = null;
    public $errors = null;
    public bool $auth = false;
    public $token = null;
    public $authorize = null;
    const APP_URI = 'https://service.notibot.me/api/';
    const PATH = [
        'auth' => self::APP_URI.'user',
    ];

    public function __construct()
    {
        $this->token = base64_encode(config('notibot.uuid').':'.config('notibot.secret'));
        $this->auth();
        return $this;
    }

    public function getToken() {
        return $this->token;
    }

    public function user() {
        return $this->authorize;
    }

    public function auth()
    {
        try {
            $client = new Client();
            $headers = [
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . $this->token
            ];
            $body = [];

            $response = $client->request('GET', self::PATH['auth'], [
                'headers' => $headers,
                'form_params' => $body,
            ]);

            $response = json_decode($response->getBody()->getContents(),true);
            $response['status'] == 1 ? $this->auth = true : $this->auth = false;
            $response['status'] == 1 ? $this->status = 'success' : $this->status = 'failed';
            $this->authorize = $response;
        } catch (GuzzleException $e) {
            $this->errors = $e;
        }
    }
}
