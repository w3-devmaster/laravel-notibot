# Notibot Services : API Package for any alert

[![Latest Version](https://img.shields.io/github/release/w3-devmaster/laravel-notibot.svg?style=flat-square)](https://github.com/w3-devmaster/laravel-notibot/releases)
![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/w3-devmaster/laravel-notibot/run-tests.yml?branch=master&style=flat-square&label=tests)
[![Total Downloads](https://img.shields.io/packagist/dt/w3-devmaster/laravel-notibot.svg?style=flat-square)](https://packagist.org/packages/w3-devmaster/laravel-notibot)

## Installation

Install with composer : 

```bash
composer require w3-devmaster/laravel-notibot
```
Publish package config file :

```bash
php artisan vendor:publish --provider="W3Devmaster\Notibot\NotibotServiceProvider"
```

Add below to .env file : 

```env
ALERT_UUID=<your uuid from service.notibot.me>
ALERT_TOKEN=<your secret token from service.notibot.me>
```

## Basic Use

Email Alert (send now) :

```php
use W3Devmaster\Notibot\Sender\Email;

public function test()
{
    $email = new Email();
    $send = $email
        ->to('to-email@domain.com')
        ->subject('test')
        ->sender('sender-email@domain.com')
        ->content([
            'title' => 'Email Title',
            'message' => 'Email Message',
            'footer' => 'Email Footer',
        ])
        ->exec();
    return $send;
}
```

Line notify (send now) :

```php
use W3Devmaster\Notibot\Sender\LineNotify;

public function line() {
    $line = new LineNotify();

    $send = $line->to('<your line notify token>')
            ->message('test message')
            ->delay(true)
            ->delayTime('2023-11-10 15:00')
            ->exec();

    return $send;
}
```

## Advanced Use

Use For :

```php
$newsItem->addMedia($smallFile)->toMediaCollection('downloads', 'local');
$newsItem->addMedia($bigFile)->toMediaCollection('downloads', 's3');
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.
