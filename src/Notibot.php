<?php

namespace W3Devmaster\Notibot;

use W3Devmaster\Notibot\Support\Facade\Resources;

class Notibot {
    public static $app;
    public static ?array $objectData = [];

    public function __construct()
    {
        self::$app = app(Resources::class);
    }

    /**
     * @param array|null $tranxData เป็นข้อมูลสำหรับการสร้าง Transaction
     * @param mixed ...$objects เช่น new Email() , new Notify() ที่มีการ สร้าง instant แล้ว
     * @return string|array
     */
    public static function create(array $tranxData = null,... $objects)
    {
        if($tranxData == null) return 'please send transaction data';
        foreach ($objects as $object) {
            if($object->data()){
                self::$objectData[$object::TYPE] = $object->data();
            }
        }
        if(self::$objectData == null) return 'please input data object';

        self::$app = app(Resources::class);
        $tranxData['data'] = self::$objectData;

        return self::$app->createTransaction($tranxData);
    }

    /**
     * @param array|null $tranxData เป็นข้อมูลสำหรับการสร้าง Transaction
     * @param mixed ...$objects เช่น new Email() , new Notify() ที่มีการ สร้าง instant แล้ว
     * @return string|array
     */
    public static function update(int $transactionId,array $tranxData = null,... $objects)
    {
        if($tranxData == null) return 'please send transaction data';
        foreach ($objects as $object) {
            if($object->data()){
                self::$objectData[$object::TYPE] = $object->data();
            }
        }
        if(self::$objectData == null) return 'please input data object';

        self::$app = app(Resources::class);
        $tranxData['data'] = self::$objectData;

        return self::$app->updateTransaction($transactionId,$tranxData);
    }

    public static function transactions(int $perPage = null,int $page = null) {
        return self::$app->transactions();
    }

    public static function transaction(int $id = null) {
        return self::$app->transaction($id);
    }

    public static function delete(int $id = null) {
        return self::$app->transaction($id);
    }
}
