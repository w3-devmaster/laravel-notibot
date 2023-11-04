<?php

namespace W3Devmaster\Notibot\Support\Facade;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Resources
{
    protected $authenticate = null;
    protected $status = null;
    protected $errors = null;
    protected ?array $response = null;
    protected ?array $payload = null;

    const APP_URI = Authenticate::APP_URI;
    const PATH = [
        'email' => self::APP_URI.'send/email',
        'line-notify' => self::APP_URI.'send/notify',
        'line-flex' => self::APP_URI.'send/lineflex',
        'sms' => self::APP_URI.'send/sms'
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

}
