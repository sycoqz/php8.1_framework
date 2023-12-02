<?php

namespace core\base\settings;

use core\base\controllers\Singleton;
use core\base\exceptions\DbException;

class Settings
{

    use Singleton;

    private array $routes = [
        'admin' => [
          'alias' => 'admin',
          'path' => 'core/admin/controllers/',
          'hrUrl' => false,
          'routes' => [

            ]
        ],
        'settings' => [
          'path' => 'core/base/settings/'
        ],
        'plugins' => [
            'path' => 'core/plugins/',
            'hrUrl' => false,
            'dir' => false
        ],
        'user' => [
            'path' => 'core/user/controllers/',
            'hrUrl' => true,
            'routes' => [

            ]
        ],
        'default' => [
            'controller' => 'IndexController',
            'inputMethod' => 'inputData',
            'outputMethod' => 'outputData'
        ]
    ];

    private array $templateArr = [
        'text' => ['name', 'phone', 'address'],
        'textarea' => ['content', 'keywords']
    ];

    private string $extension = 'core/admin/extension/';

    private string $defaultTable = 'users';

    private array $projectTables = [
        'users' => ['name' => 'Персонажи', 'img' => 'pages.png'],
        'titans' => ['name' => 'Титаны']
    ];

    private array $warningUser = [
        'name' => ['Название', 'Не более 100 символов'],
    ];
    private array $blockNeedle = [
        'vg-rows' => [],
        'vg-img' => ['id'],
        'vg-content' => ['content']
    ];

    /**
     * @param $property
     * @return mixed
     * @throws DbException
     */
    static public function get($property): mixed
    {
        return self::instance()->$property;
    }

    public function clueProperties($class): array
    {
        $baseProperties = [];

        foreach ($this as $name => $item) {
            $property = $class::get($name);

            if (is_array($property) && is_array($item)) {
                $baseProperties[$name] = $this->arrayMergeRecursive($this->$name, $property);
                continue;
            }

            if (!$property) $baseProperties[$name] = $this->$name;
        }

        return $baseProperties;
    }

    public function arrayMergeRecursive()
    {
        $arrays = func_get_args();

        $base = array_shift($arrays);

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (is_array($value) && is_array($base[$key])) {
                    $base[$key] = $this->arrayMergeRecursive($base[$key], $value);
                } else {
                    if (is_int($key)) {
                        if (!in_array($value, $base)) $base[] = $value;
                        continue;
                    }
                    $base[$key] = $value;
                }
            }
        }

        return $base;
    }
}