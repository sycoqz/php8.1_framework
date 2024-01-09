<?php

namespace core\admin\controllers;

use core\base\controllers\BaseController;
use core\base\exceptions\DbException;
use core\base\models\UserModel;

class LoginController extends BaseController
{

    protected UserModel $model;

    /**
     * @throws DbException
     */
    protected function inputData()
    {

        $this->model = UserModel::instance();

        $a = 1;

    }

}