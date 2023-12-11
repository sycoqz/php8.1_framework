<?php

namespace core\user\controllers;

use core\admin\models\Model;
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

        $model = Model::instance();

        $result = $model->read('teachers', [
            'where' => ['id' => '1,2'],
            'operand' => ['IN'],
            'join' => [
                'stud_teach' => ['on' => ['id', 'teachers']],
                'students' => [
                    'fields' => ['name as student_name'],
                    'on' => ['students', 'id']
                ]
            ],
            'join_structure' => true
        ]);

        exit();

    }

}