<?php

namespace core\base\controllers;

use core\base\exceptions\DbException;
use core\base\settings\Settings;

class BaseAjax extends BaseController
{

    /**
     * @throws DbException
     */
    public function route()
    {

        $route = Settings::get('routes');

        $controller = $route['user']['path'] . 'AjaxController';

        $data = $this->isPost() ? $_POST : $_GET;

        if (!empty($data['ajax']) && $data['ajax'] === 'token') {

            return $this->generateToken();

        }

        $httpReferer = str_replace('/', '\/', $_SERVER['REQUEST_SCHEME'] .
            '://' . $_SERVER['SERVER_NAME'] . PATH . $route['admin']['alias']);

        if (isset($data['ADMIN_MODE']) ||
            preg_match('/^' . $httpReferer . '(\/?|$)/', $_SERVER['HTTP_REFERER'])) {

            unset($data['ADMIN_MODE']);

            $controller = $route['admin']['path'] . 'AjaxController';

        }

        $controller = str_replace('/', '\\', $controller);

        $ajax = new $controller;

        $ajax->ajaxData = $data;

        $result = $ajax->ajax();

        if (is_array($result) || is_object($result)) $result = json_encode($result);
        elseif (is_int($result)) $result = (float) $result;

        return $result;

    }

    protected function generateToken(): string
    {

        return $_SESSION['token'] = md5(mt_rand(0, 999999) . microtime());

    }

}