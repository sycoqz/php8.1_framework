<?php

namespace core\admin\controllers;

use core\base\controllers\BaseController;
use core\base\exceptions\DbException;
use core\base\exceptions\RouteException;
use core\base\models\UserModel;
use core\base\settings\Settings;

class LoginController extends BaseController
{

    protected UserModel $model;

    /**
     * @throws DbException
     * @throws RouteException
     */
    protected function inputData()
    {

        $this->model = UserModel::instance();

        if ($this->isPost()) {

            $a = 1;

        }

        return $this->render('', ['adminPath' => Settings::get('routes')['admin']['alias']]);

    }

}