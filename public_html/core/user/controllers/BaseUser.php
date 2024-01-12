<?php

namespace core\user\controllers;

use core\base\controllers\BaseController;
use core\base\exceptions\DbException;
use core\base\exceptions\RouteException;
use core\user\models\Model;

abstract class BaseUser extends BaseController
{

    protected object $model;

    protected string|null $table = null;

    /**
     * @throws DbException
     */
    protected function inputData()
    {

        $this->init();

        if (!isset($this->model)) $this->model = Model::instance(); // !$this->model && $this->model = Model::instance();

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

            $this->content = $this->render($this->template, $vars);

        }

        $this->header = $this->render(TEMPLATE . 'include/header', $vars);
        $this->footer = $this->render(TEMPLATE . 'include/footer', $vars);

        return $this->render(TEMPLATE . 'layout/default');

    }

    protected function img(string $img = '')
    {

        if (!$img && is_dir($_SERVER['DOCUMENT_ROOT'] . PATH . UPLOAD_DIR . DEFAULT_IMG_DIRECTORY)) {

            $dir = scandir($_SERVER['DOCUMENT_ROOT'] . PATH . UPLOAD_DIR . DEFAULT_IMG_DIRECTORY);

            $imgArr = preg_grep('/' . $this->getController() .'\./i', $dir)
                ?: preg_grep('/default\./i', $dir);

            $imgArr && $img = array_shift($imgArr);

        }

        return  $img;

    }
}