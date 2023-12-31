<?php

namespace core\admin\controllers;

use core\base\controllers\BaseController;
use core\base\exceptions\DbException;
use core\base\settings\Settings;
use JetBrains\PhpStorm\NoReturn;

class IndexController extends BaseController
{

    /**
     * @throws DbException
     */
    #[NoReturn] protected function inputData(): void
    {
        $redirect = PATH . Settings::get('routes')['admin']['alias'] . '/show';
        $this->redirect($redirect);
    }



}
