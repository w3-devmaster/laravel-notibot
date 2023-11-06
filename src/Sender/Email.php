<?php

namespace W3Devmaster\Notibot\Sender;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use W3Devmaster\Notibot\HasAlert;
use W3Devmaster\Notibot\Support\Facade\Resources;

class Email implements HasAlert
{
    const TYPE = 'email';
    public $app;
    public string $subject;
    public ?string $toEmail = null;
    public string $sender;
    public array $content;
    public bool $delay;
    public string $delayTime;
    public string $theme;
    public string $mode;
    public ?array $attachments;

    public function __construct(?string $toEmail = null)
    {
        $this->app = app(Resources::class);
        $this->to($toEmail);
    }

    public function make(string $toEmail,?array $data = null) : self
    {
        $this->to($toEmail);
        if($data){
            $this->subject($data['subject'] ?? null);
            $this->sender($data['sender'] ?? null);
            $this->content($data['content'] ?? []);
            $this->delay(
                $data['delay']
                ? $data['delayTime'] ?? '' : false
            );
            $this->theme($data['theme'] ?? '');
            $this->mode($data['mode'] ?? '');
            $this->attachments($data['attachments'] ?? []);
        }

        return $this;
    }

    public function subject(?string $subject = '') : self
    {
        $this->subject = $subject ?? '';
        return $this;
    }

    public function to(?string $toEmail = '') : self
    {
        $this->toEmail = $toEmail ?? '';
        return $this;
    }

    public function sender(?string $sender = '') : self
    {
        $this->sender = $sender ?? '';
        return $this;
    }

    public function content(?array $content = []) : self
    {
        $this->content = $content;
        return $this;
    }

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

    public function theme(?string $theme = '') : self
    {
        $this->theme = $theme ?? '';
        return $this;
    }

    public function mode(?string $mode = '') : self
    {
        $this->mode = $mode ?? '';
        return $this;
    }

    public function attachments(?array $attachments = null) : self
    {
        $this->attachments = $attachments ?? [];
        return $this;
    }

    public function data()
    {
        if($this->toEmail == null) return;
        return [
            "subject" => $this->subject ?? null,
            "toEmail" => $this->toEmail ?? null,
            "sender" => $this->sender ?? null,
            "content" => [
                "title" => $this->content['title'] ?? null,
                "message" => $this->content['message'] ?? null,
                "footer" => $this->content['footer'] ?? null
            ],
            "delay" => $this->delay ?? false,
            "delayTime" => $this->delayTime ?? null,
            "attachments" => [],
            "theme" => $this->theme ?? "default",
            "mode" => $this->mode ?? "success"
        ];
    }

    public function exec(?string $to = null,?array $data = null) : Resources
    {
        if($to != null){
            $this->make($to);
        }

        $validate = Validator::make([
            'toEmail' => $this->toEmail,
            'sender' => $this->sender,
        ],[
            'toEmail' => 'required|email',
            'sender' => 'required|email',
        ]);

        if($validate->fails()) {
            $this->app->status = 'failed';
            $this->app->errors = $validate->messages();
        }else{
            $this->app->send('email',$this);
        }
        return $this->app;
    }

}
