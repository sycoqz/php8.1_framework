<?php

namespace core\user\controllers;

use core\base\controllers\BaseController;
use core\base\exceptions\DbException;
use core\base\models\crypt;
use JetBrains\PhpStorm\NoReturn;

class IndexController extends BaseController
{

    protected string $name;

    /**
     * @throws DbException
     */
    #[NoReturn] protected function inputData(): void
    {
        $str = '1234567890abcdefg';

        $en_str = crypt::instance()->encrypt($str);

        $dec_str = crypt::instance()->decrypt($en_str);

    }

}