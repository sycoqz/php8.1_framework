<?php

namespace core\base\controllers;

use core\base\exceptions\DbException;
use core\base\exceptions\RouteException;
use core\base\settings\Settings;
use JetBrains\PhpStorm\NoReturn;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

abstract class BaseController
{

    use BaseMethods;

    protected string $controller;
    protected mixed $inputMethod;
    protected mixed $outputMethod;
    protected ?array $parameters;
    protected string|array $page;
    protected string|bool $header;
    protected string|bool $content;
    protected string|bool $footer;
    protected string|bool $template;
    protected string|bool $errors;
    protected array $styles;
    protected array $scripts;

    /**
     * @throws RouteException
     */
    public function route(): void
    {
        $controller = str_replace('/', '\\', $this->controller); // Имя класса в строковом виде

        try {

            $object = new ReflectionMethod($controller, 'request'); // Поиск метода request в классе $controller

            $args = [
                'parameters' => $this->parameters,
                'inputMethod' => $this->inputMethod,
                'outputMethod' => $this->outputMethod
            ];

            $object->invoke(new $controller, $args); // Вызов метода request на исполнение. Объект класса и массив аргументов

        }
        catch (ReflectionException $e) {
            throw new RouteException($e->getMessage());
        }

    }

    #[NoReturn] public function request(array $args): void
    {
        $this->parameters = $args['parameters'];

        $inputData = $args['inputMethod'];
        $outputData = $args['outputMethod'];

        $data = $this->inputData();

        if (method_exists($this, 'outputData')) {

            $page = $this->outputData($data); // Проверка для работы только с 1 методом
            if ($page) $this->page = $page;

        } elseif (isset($data)) {
            $this->page = $data; // Если что-то возвращается
        }

        if (isset($this->errors)) { // Логирование ошибок
            $this->writeLog($this->errors);
        }

        $this->getPage();
    }

    protected function init($admin = false): void
    {
        if (!$admin) {
            if (USER_CSS_JS['styles']) {
                foreach (USER_CSS_JS['styles'] as $item) $this->styles[] = PATH . TEMPLATE . trim($item, '/');
            }

            if (USER_CSS_JS['scripts']) {
                foreach (USER_CSS_JS['scripts'] as $item) $this->scripts[] = PATH . TEMPLATE . trim($item, '/');
            }
        } else {
            if (ADMIN_CSS_JS['styles']) {
                foreach (ADMIN_CSS_JS['styles'] as $item) $this->styles[] = PATH . ADMIN_TEMPLATE . trim($item, '/');
            }

            if (ADMIN_CSS_JS['scripts']) {
                foreach (ADMIN_CSS_JS['scripts'] as $item) $this->scripts[] = PATH . ADMIN_TEMPLATE . trim($item, '/');
            }
        }
    }

    /**
     * @throws RouteException
     * @throws DbException
     */
    protected function render(string $path = '', array $parameters = []): bool|string // Шаблонизатор
    {
        extract($parameters); // Разбор массива, создание переменных. Пример на вход: $arr = ['name' => 'value']. Создаётся переменная $name, содержащая строку 'value'

        if (!$path) { // Проверка на наличие пути
            // Подключение шаблонов
            $class = new ReflectionClass($this);

            $space = str_replace('\\', '/', $class->getNamespaceName() . '\\'); // Получение пространства имён
            $routes = Settings::get('routes');

            if ($space === $routes['user']['path']) $template = TEMPLATE;
                else $template = ADMIN_TEMPLATE;

            $path = $template . explode('controller', strtolower($class->getShortName()))[0]; // Если путь не указан.
        }

        ob_start(); // Открывает буфер обмена

        if (!@include_once $path . '.php') throw new RouteException('Отсутствует шаблон - '.$path);

        return ob_get_clean(); // Возвращает данные из буфера обмена (в переменную $template) и закроет буфер обмена (отчистит)

    }

    #[NoReturn] protected function getPage(): void
    {
        if (is_array($this->page)) {
            foreach ($this->page as $block) echo $block;
        } else {
            echo $this->page;
        }
        exit();

    }

}