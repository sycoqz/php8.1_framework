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

    private array $fileTemplates = ['img', 'gallery_img'];

    private string $extension = 'core/admin/extension/';

    private string $messages = 'core/base/messages/';

    private string $formTemplates = PATH . 'core/admin/views/include/form_templates/';

    private string $defaultTable = 'goods';

    private array $projectTables = [
        'catalog' => ['name' => 'Каталог'],
        'goods' => ['name' => 'Товары', 'img' => 'pages.png'],
        'filters' => ['name' => 'Фильтры'],
        'articles' => ['name' => 'Статьи'],
        'sales' => ['name' => 'Акции'],
        'news' => ['name' => 'Новости'],
        'information' => ['name' => 'Информация'],
        'advantages' => ['name' => 'Преимущества'],
        'social_networks' => ['name' => 'Социальные сети'],
        'settings' => ['name' => 'Настройки системы']
    ];

    private array $templateArr = [
        'text' => ['name', 'phone', 'email', 'alias', 'external_alias', 'sub_title', 'number_of_years', 'price', 'discount'],
        'textarea' => ['keywords', 'content', 'address', 'description', 'short_content'],
        'radio' => ['visibility', 'show_top_menu', 'hit', 'sale', 'new', 'hot'],
        'checkboxlist' => ['filters'],
        'select' => ['menu_position', 'parent_id'],
        'img' => ['img', 'main_img', 'img_years', 'promo_img'],
        'gallery_img' => ['gallery_img', 'new_gallery_img']
    ];

    private array $warningUser = [
        'name' => ['Название', 'Не более 100 символов'],
        'keywords' => ['Ключевые слова', 'Не более 70 символов'],
        'img' => ['Изображение'],
        'gallery_img' => ['Галерея изображений'],
        'visibility' => ['Видимость объекта'],
        'menu_position' => ['Позиция объекта в списке'],
        'content' => ['Описание'],
        'description' => ['SEO описание'],
        'phone' => ['Телефон'],
        'email' => ['Электронная почта'],
        'address' => ['Адрес'],
        'alias' => ['Ссылка ЧПУ'],
        'show_top_menu' => ['Показывать в верхнем меню'],
        'external_alias' => ['Внешняя ссылка'],
        'sub_title' => ['Подзаголовок'],
        'short_content' => ['Краткое описание'],
        'img_years' => ['Изображение количество лет на рынке'],
        'number_of_years' => ['Количество лет на рынке'],
        'price' => ['Цена товара'],
        'discount' => ['Скидка'],
        'hit' => ['Хит продаж'],
        'sale' => ['Акция'],
        'new' => ['Новинка'],
        'hot' => ['Горячее предложение'],
        'promo_img' => ['Изображение для главной страницы'],
    ];

    private array $manyToMany = [
        'goods_filters' => ['goods', 'filters'] // 'type' => 'child' || 'root'
    ];

    private array $blockNeedle = [
        'vg-rows' => [],
        'vg-img' => ['img', 'gallery_img', 'main_img', 'img_years', 'number_of_years', 'promo_img'],
        'vg-content' => ['content']
    ];

    private array $rootItems = [
        'name' => 'Корневая',
        'tables' => ['articles', 'filters', 'catalog']
    ];

    private array $radio = [
        'visibility' => ['Нет', 'Да', 'default' => 'Да'],
        'show_top_menu' => ['Нет', 'Да', 'default' => 'Да'],
        'hit' => ['Нет', 'Да', 'default' => 'Нет'],
        'sale' => ['Нет', 'Да', 'default' => 'Нет'],
        'new' => ['Нет', 'Да', 'default' => 'Нет'],
        'hot' => ['Нет', 'Да', 'default' => 'Нет'],
    ];

    private array $validation = [
        'name' => ['empty' => true, 'trim' => true],
        'price' => ['int' => true],
        'discount' => ['int' => true],
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