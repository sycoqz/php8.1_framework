<?php

namespace core\base\controllers;

use core\base\exceptions\DbException;

trait Singleton
{

    static private ?object $_instance = null;

    /**
     * @throws DbException
     */
    static public function instance() : object
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