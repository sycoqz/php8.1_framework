<?php

namespace core\admin\controllers;

use core\admin\models\Model;
use core\base\controllers\BaseController;
use core\base\exceptions\DbException;
use core\base\exceptions\RouteException;
use core\base\settings\Settings;

abstract class BaseAdmin extends BaseController
{

    protected object $model;

    protected string $table;
    protected array $columns;
    protected array $data;

    protected string $adminPath;

    protected array $menu;
    protected string $title;


    /**
     * @throws DbException
     */
    protected function inputData()
    {
        $this->init(true);

        $this->title = 'Shingeki no Kyojin';

        if (!isset($this->model)) $this->model = Model::instance();
        if (!isset($this->menu)) $this->menu = Settings::get('projectTables');
        if (!isset($this->adminPath)) $this->adminPath = PATH . Settings::get('routes')['admin']['alias'] . '/';

        $this->sendNoCacheHeaders();

    }

    /**
     * @throws RouteException
     * @throws DbException
     */
    protected function outputData(): bool|string
    {
        $this->header = $this->render(ADMIN_TEMPLATE . 'include/header');
        $this->footer = $this->render(ADMIN_TEMPLATE . 'include/footer');

        return $this->render(ADMIN_TEMPLATE . 'layout/default');
    }

    protected function sendNoCacheHeaders(): void
    {
        header('Last-Modified: ' . gmdate('D, d m Y H:i:s') . ' GMT'); // Отправка заголовков последней модификации сайта браузеру.
        header('Cache-Control: no-cache, must-revalidate');
        header('Cache-Control: max-age=0');
        header('Cache-Control: post-check=0,pre-check=0');
    }

    /**
     * @throws DbException
     */
    protected function executeBase(): void
    {
        self::inputData();
    }

    /**
     * @throws DbException
     */
    protected function createTableData(): void
    {

        if (!isset($this->table)) {

            if (isset($this->parameters)) $this->table = array_keys($this->parameters)[0];
                else $this->table = Settings::get('defaultTable');

        }

        $this->columns = $this->model->showColumns($this->table);

        if (!isset($this->columns)) new RouteException('Не найдены поля в таблице - ' . $this->table, 2);

    }

    /**
     * @throws DbException
     */
    protected function extension(array $args = [], bool|object $settings = false): mixed
    {
        $filename = explode('_', $this->table);
        $className = '';

        foreach ($filename as $item) $className .= ucfirst($item);

        if (!$settings) {

            $path = Settings::get('extension');

        } elseif (is_object($settings)) {

            $path = $settings::get('extension');

        } else {

            $path = $settings;
        }

        $class = $path . $className . 'Extension';

        if (is_readable($_SERVER['DOCUMENT_ROOT'] . PATH . $class . '.php')) {

            $class = str_replace('/', '\\', $class);

            if (class_exists($class)) { // Проверка строки. Является ли классом.

                if (method_exists($class, 'instance')) { // Проверка есть ли метод instance в классе.

                    $ext = $class::instance();

                    foreach ($this as $name => $value) {

                        $ext->$name = &$this->$name; // Сохранение ссылок на свойства класса

                    }

                    return $ext->extension($args);

                }
            }

        } else {

            $file = $_SERVER['DOCUMENT_ROOT'] . PATH . $path . $this->table . '.php';

            extract($args);

            if (is_readable($file)) return include $file;

        }

        return false;

    }

}