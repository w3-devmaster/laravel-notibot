<?php

namespace W3Devmaster\Notibot\Sender;

use Carbon\Carbon;
use W3Devmaster\Notibot\HasAlert;
use Illuminate\Support\Facades\Validator;
use W3Devmaster\Notibot\Support\Facade\Resources;

class LineNotify implements HasAlert
{
    public $app;
    public string $token;
    public string $message;
    public int $stickerPackageId;
    public int $stickerId;
    public bool $delay;
    public string $delayTime;

    public function __construct()
    {
        $this->app = app(Resources::class);
    }

    public function make(string $toEmail,?array $data = null) : self
    {
        $this->to($toEmail);
        if($data){
            $this->message($data['subject'] ?? null);
            $this->delay(
                $data['delay']
                ? $data['delayTime'] ?? '' : false
            );
        }

        return $this;
    }

    public function to(?string $to = null) : self
    {
        $this->token = $to ?? '';
        return $this;
    }

    public function message(?string $message = null) : self
    {
        $this->message = $message ?? '';
        return $this;
    }

    public function sticker(?int $stickerPackageId = null,?int $stickerId = null) : self
    {
        $this->stickerPackageId = $stickerPackageId;
        $this->stickerId = $stickerId;
        return $this;
    }

    /**
     * @param datetime|timestamp $dateTime วันเวลา หรือ timestamps
     */
    public function delay(string|int $dateTime = null) : self
    {
        $sendTime = Carbon::parse($dateTime)->format('Y-m-d H:i');
        if(now()->lt($sendTime)){
            $this->delay = true;
            $this->delayTime = $sendTime;
        }else{
            $this->delayTime = 'use time greater than now.';
        }

        return $this;
    }

    public function exec(?string $to = null, $data = null) : Resources
    {
        if($to != null){
            $this->make($to);
        }

        $validate = Validator::make([
            'token' => $this->token,
            'message' => $this->message,
        ],[
            'token' => 'required|string',
            'message' => 'required|string',
        ]);

        if($validate->fails()) {
            $this->app->status = 'failed';
            $this->app->errors = $validate->messages();
        }else{
            $this->app->send('line-notify',$this);
        }

        return $this->app;
    }
}
