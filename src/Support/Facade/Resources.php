<?php

namespace W3Devmaster\Notibot\Support\Facade;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Resources
{
    protected $authenticate = null;
    public $status = null;
    public $errors = null;
    public ?array $response = null;
    public ?array $payload = null;

    const APP_URI = Authenticate::APP_URI;
    const PATH = [
        'email' => self::APP_URI.'send/email',
        'line-notify' => self::APP_URI.'send/notify',
        'line-flex' => self::APP_URI.'send/lineflex',
        'sms' => self::APP_URI.'send/sms',
        // transaction system
        'transaction' => self::APP_URI.'transaction',
        'logs' => self::APP_URI.'sendlogs',
    ];


    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        $this->authenticate = new Authenticate();
    }

    private function makeData(string $type,$data) {
        switch ($type) {
            case 'email':
                return $this->email($data);
                break;

            case 'line-notify':
                return $this->lineNotify($data);
                break;
        }
    }

    private function email($data = null) : array
    {
        if(!$data) return [];
        return [
                "subject" => $data->subject ?? "หัวข้อที่ต้องการ",
                "toEmail" => $data->toEmail ?? "nongskype@gmail.com",
                "sender" => $data->sender ?? "test@test.co.th",
                "content" => [
                    "title" => $data->content['title'] ?? "TEst",
                    "message" => $data->content['message'] ?? "ทดสอบการส่งแบบ ด้วย Composer Package",
                    "footer" => $data->content['footer'] ?? "from compattana.com"
                ],
                "delay" => $data->delay ?? false,
                "delayTime" => $data->delayTime ?? "2023-11-12 23:20",
                "attachments" => [],
                "theme" => $data->theme ?? "default",
                "mode" => $data->mode ?? "success"
            ];
    }

    private function lineNotify($data = null) : array
    {
        if(!$data) return [];
        return [
                'token' => $data->token ?? 'AB2JtWl8pg9A9KXezlSWmIymNnG8dDJ7ly9SciqlWai',
                'message' => $data->message ?? 'สวัสดีครับ! @name คุณได้รับการแจ้งเตือนแล้ว',
                'stickerPackageId' => $data->stickerPackageId ?? null,
                'stickerId' => $data->stickerId ?? null,
                "delay" => $data->delay ?? false,
                "delayTime" => $data->delayTime ?? "2023-11-12 23:20",
            ];
    }

    public function send(string $type = '',$data = null)
    {
        if($type == '') {
            return;
        }

        $sendData = $this->makeData($type,$data);

        try {
            $client = new Client();
            $headers = [
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . $this->authenticate->getToken()
            ];

            $response = $client->request('POST', self::PATH[$type], [
                'headers' => $headers,
                'form_params' => $sendData,
            ]);

            $this->response = json_decode($response->getBody()->getContents(),true);
            $this->payload['type'] = $type;
            $this->payload['data'] = $sendData;
            $this->status = $this->response['status'] ?? 'failed';
            return $this;
        } catch (GuzzleException $e) {
            $this->status = 'failed';
            $this->errors = $e;
            return $this;
        }
    }

    public function response()
    {
        return $this->response;
    }

    public function getData()
    {
        return $this->response['data'] ?? null;
    }

    public function paginate()
    {
        return $this->response['paginate'] ?? null;
    }

    public function createTransaction(?array $data = null)
    {
        if($data == null) return null;

        try {
            $client = new Client();
            $headers = [
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . $this->authenticate->getToken()
            ];

            $response = $client->request('POST', self::PATH['transaction'], [
                'headers' => $headers,
                'form_params' => $data,
            ]);

            $this->response = json_decode($response->getBody()->getContents(),true);
            $this->payload['type'] = 'transaction.store';
            $this->payload['data'] = $data;
            $this->status = $this->response['status'] ?? 'failed';
            return $this;
        } catch (GuzzleException $e) {
            $this->status = 'failed';
            $this->errors = $e;
            return $this;
        }
    }

    public function updateTransaction(int $transactionId,?array $data = null)
    {
        if($data == null) return null;

        try {
            $client = new Client();
            $headers = [
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . $this->authenticate->getToken()
            ];

            $response = $client->request('PATCH', self::PATH['transaction'].'/'.$transactionId, [
                'headers' => $headers,
                'form_params' => $data,
            ]);

            $this->response = json_decode($response->getBody()->getContents(),true);
            $this->payload['type'] = 'transaction.update';
            $this->payload['data'] = $data;
            $this->status = $this->response['status'] ?? 'failed';
            return $this;
        } catch (GuzzleException $e) {
            $this->status = 'failed';
            $this->errors = $e;
            return $this;
        }
    }

    public function transactions(int $perPage = null,int $page = null)
    {
        if($perPage == null){
            $paginate = [];
        }else{
            $paginate = [
                'perPage' => $perPage,
                'page' => $page,
            ];
        }

        $query_string = http_build_query($paginate);

        try {
            $client = new Client();
            $headers = [
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . $this->authenticate->getToken()
            ];

            $response = $client->request('GET', self::PATH['transaction'], [
                'headers' => $headers,
                'query' => $query_string
            ]);

            $this->response = json_decode($response->getBody()->getContents(),true);
            $this->payload['type'] = 'transaction.index';
            $this->payload['data'] = $paginate;
            $this->status = $this->response['status'] ?? 'failed';
            return $this;
        } catch (GuzzleException $e) {
            $this->status = 'failed';
            $this->errors = $e;
            return $this;
        }
    }

    public function transaction(int $id)
    {
        try {
            $client = new Client();
            $headers = [
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . $this->authenticate->getToken()
            ];

            $response = $client->request('GET', self::PATH['transaction'].'/'.$id, [
                'headers' => $headers,
            ]);

            $this->response = json_decode($response->getBody()->getContents(),true);
            $this->payload['type'] = 'transaction.show';
            $this->status = $this->response['status'] ?? 'failed';
            return $this;
        } catch (GuzzleException $e) {
            $this->status = 'failed';
            $this->errors = $e;
            return $this;
        }
    }

    public function logs(int $perPage = null,int $page = null)
    {
        if($perPage == null){
            $paginate = [];
        }else{
            $paginate = [
                'perPage' => $perPage,
                'page' => $page,
            ];
        }

        $query_string = http_build_query($paginate);

        try {
            $client = new Client();
            $headers = [
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . $this->authenticate->getToken()
            ];

            $response = $client->request('GET', self::PATH['logs'], [
                'headers' => $headers,
                'query' => $query_string
            ]);

            $this->response = json_decode($response->getBody()->getContents(),true);
            $this->payload['type'] = 'sendlogs.index';
            $this->payload['data'] = $paginate;
            $this->status = $this->response['status'] ?? 'failed';
            return $this;
        } catch (GuzzleException $e) {
            $this->status = 'failed';
            $this->errors = $e;
            return $this;
        }
    }

    public function log(int $id)
    {
        try {
            $client = new Client();
            $headers = [
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . $this->authenticate->getToken()
            ];

            $response = $client->request('GET', self::PATH['logs'].'/'.$id, [
                'headers' => $headers,
            ]);

            $this->response = json_decode($response->getBody()->getContents(),true);
            $this->payload['type'] = 'sendlogs.index';
            $this->status = $this->response['status'] ?? 'failed';
            return $this;
        } catch (GuzzleException $e) {
            $this->status = 'failed';
            $this->errors = $e;
            return $this;
        }
    }


}
