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

        $result = $model->read('goods', [
            'where' => ['id' => '1,2'],
            'operand' => ['IN'],
            'join' => [
                'goods_filters' => [
                    'fields' => null,
                    'on' => ['id', 'goods_id']],
                'filters f' => [
                    'fields' => ['name as filter_name'],
                    'on' => ['filters_id', 'id']
                ],
                [
                    'table' => 'filters',
                    'on' => ['parent_id', 'id']
                ]
            ],
//            'join_structure' => true,
            'order' => ['id'],
            'order_direction' => ['DESC']
        ]);

        exit();

    }

}