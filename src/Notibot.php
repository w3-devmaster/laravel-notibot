<?php

namespace W3Devmaster\Notibot;

class Notibot {

    public static function send(string $token,string $message = '',?string $title = null)
    {
        return $token . ' - ' . $message . ' | ' . $title;
    }

}
