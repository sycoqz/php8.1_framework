<?php

namespace core\base\settings;

use core\base\controllers\Singleton;
use core\base\exceptions\DbException;

trait BaseSettings
{

    use Singleton {
        instance as SingletonInstance;
    }

    private object $baseSettings;

    /**
     * @throws DbException
     */
    static public function get($property) {
        return self::instance()->$property;
    }

    /**
     * @throws DbException
     */
    static public function instance(): ?object
    {
        if (self::$_instance instanceof self) {
            return self::$_instance;
        }

        self::SingletonInstance()->baseSettings = Settings::instance();
        $baseProperties = self::$_instance->baseSettings->clueProperties(get_class());
        self::$_instance->setProperty($baseProperties);

        return self::$_instance;
    }

    protected function setProperty($properties): void
    {
        if ($properties) {
            foreach ($properties as $name => $property) {
                $this->$name = $property;
            }
        }
    }

}