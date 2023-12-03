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
    protected array $data = [];
    protected array $foreignData = [];

    protected string $adminPath;

    protected array $menu;
    protected string $title;
    protected array $warningUser; // $translate
    protected array $blocks = [];


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
        if (!isset($this->content)) {

            $args = func_get_arg(0);
            $vars = $args ?: [];

            if (!isset($this->template)) $this->template = ADMIN_TEMPLATE . 'show';

            $this->content = $this->render($this->template, $vars);

        }

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
    protected function createTableData(bool $settings = false): void
    {

        if (!isset($this->table)) {

            if (isset($this->parameters)) $this->table = array_keys($this->parameters)[0];
                else {

                    if (!$settings) $settings = Settings::instance();
                    $this->table = $settings::get('defaultTable');

                }

        }

        $this->columns = $this->model->showColumns($this->table);

        if (!isset($this->columns)) new RouteException('Не найдены поля в таблице - ' . $this->table, 2);

    }

    /**
     * @throws DbException
     */
    protected function extension(array $args = [], bool $settings = false): mixed
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

    /**
     * @throws DbException
     */
    protected function createOutputData(bool $settings = false): void
    {

        if (!$settings) $settings = Settings::instance();

        // Создания свойства для блоков данных.
        $blocks = $settings::get('blockNeedle');
        $this->warningUser = $settings::get('warningUser');

        // Если блоки не пришли
        if (!isset($blocks) || !is_array($blocks)) {

            foreach ($this->columns as $name => $item) {

                if ($name === 'id_row ') continue;

                if (!isset($this->warningUser[$name])) $this->warningUser[$name][] = $name;

                // Распределение подключения шаблона.
                $this->blocks[0][] = $name;
            }

            return;
        }

        $default = array_keys($blocks)[0];

        foreach ($this->columns as $name => $item) {

            if ($name === 'id_row') continue;

            $insert = false;

            foreach ($blocks as $block => $value) {

                // Создание ключа, при его отсутствие.
                if (!array_key_exists($block, $this->blocks)) $this->blocks[$block] = [];

                if (in_array($name, $value)) {

                    $this->blocks[$block][] = $name;
                    $insert = true;
                    break;

                }
            }

            if (!$insert) $this->blocks[$default][] = $name;
            if (!isset($this->warningUser[$name])) $this->warningUser[$name][] = $name;

        }

        return;
    }

    /**
     * @throws DbException
     */
    protected function createRadio(bool $settings = false): void
    {

        if (!$settings) $settings = Settings::instance('radio');

        $radio = $settings::get('radio');

        if (isset($radio)) {

            foreach ($this->columns  as $name => $item) {

                if (isset($radio[$name])) {

                    $this->foreignData[$name] = $radio[$name];

                }
            }

        }

    }

}