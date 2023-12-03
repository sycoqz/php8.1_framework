<?php

namespace core\base\settings;

use core\base\controllers\Singleton;
use core\base\exceptions\DbException;
class ShopSettings
{

    use Singleton {
        instance as traitInstance;
    }
    private null|object $baseSettings;

    private array $routes = [
        'plugins' => [
            'dir' => false,
            'routes' => [

            ]
        ]
    ];
    private array $templateArr = [
        'text' => ['price', 'short'],
        'textarea' => ['goods_content']
    ];

    private string $extension = 'core/plugin/extension/';

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

        self::traitInstance()->baseSettings = Settings::instance();
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