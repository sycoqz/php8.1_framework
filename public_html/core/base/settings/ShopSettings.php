<?php

namespace core\base\settings;

class ShopSettings
{

    use BaseSettings;

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

}