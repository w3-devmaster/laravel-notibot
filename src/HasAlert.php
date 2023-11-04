<?php

namespace W3Devmaster\Notibot;

use W3Devmaster\Notibot\Support\Facade\Resources;

interface HasAlert {

    public function exec(?string $to = null,?array $data = null) : Resources;

}
