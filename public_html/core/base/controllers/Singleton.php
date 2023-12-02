<?php

namespace core\base\controllers;

use core\base\exceptions\DbException;

trait Singleton
{

    static private $_instance;

    /**
     * @throws DbException
     */
    static public function instance()
    {
        if (self::$_instance instanceof self) {
            return self::$_instance;
        }

        self::$_instance = new self;

        if (method_exists(self::$_instance, 'connect')) {
            self::$_instance->connect();
        }

        return self::$_instance;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }

}