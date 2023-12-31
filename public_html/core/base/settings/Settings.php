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
        'text' => ['name'],
        'textarea' => ['keywords', 'content'],
        'radio' => ['visibility'],
        'checkboxlist' => ['filters'],
        'select' => ['menu_position', 'parent_id'],
        'img' => ['img', 'main_img'],
        'gallery_img' => ['gallery_img', 'new_gallery_img']
    ];

    private array $fileTemplates = ['img', 'gallery_img'];

    private string $extension = 'core/admin/extension/';

    private string $messages = 'core/base/messages/';

    private string $formTemplates = PATH . 'core/admin/views/include/form_templates/';

    private string $defaultTable = 'goods';

    private array $projectTables = [
        'articles' => ['name' => 'Статьи'],
        'pages' => ['name' => 'Страницы'],
        'goods' => ['name' => 'Товары', 'img' => 'pages.png'],
        'filters' => ['name' => 'Фильтры']
    ];

    private array $warningUser = [
        'name' => ['Название', 'Не более 100 символов'],
        'keywords' => ['Ключевые слова', 'Не более 70 символов'],
        'content' => []
    ];

    private array $manyToMany = [
        'goods_filters' => ['goods', 'filters'] // 'type' => 'child' || 'root'
    ];

    private array $blockNeedle = [
        'vg-rows' => [],
        'vg-img' => ['img', 'gallery_img', 'main_img'],
        'vg-content' => ['content']
    ];

    private array $rootItems = [
        'name' => 'Корневая',
        'tables' => ['articles', 'filters']
    ];

    private array $radio = [
        'visibility' => ['Нет', 'Да', 'default' => 'Да']
    ];

    private array $validation = [
        'name' => ['empty' => true, 'trim' => true],
        'price' => ['int' => true],
        'login' => ['empty' => true, 'trim' => true],
        'password' => ['crypt' => true, 'empty' => true],
        'keywords' => ['count' => 70, 'trim' => true],
        'description' => ['count' => 160, 'trim' => true]
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
                if (isset($base[$key]) && is_array($value) && is_array($base[$key])) {
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