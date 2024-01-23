<?php

namespace core\user\controllers;

use core\base\exceptions\DbException;
use core\base\exceptions\RouteException;
use core\user\controllers\BaseUser;

class ProductController extends BaseUser
{

    /**
     * @throws RouteException
     * @throws DbException
     */
    protected function inputData()
    {
        parent::inputData();

        if (empty($this->parameters['alias'])) {

            throw new RouteException('Отсутствует ссылка на товар');

        }

        $data = $this->model->getGoods([
            'where' => ['alias' => $this->parameters['alias'], 'visibility' => 1]
        ]);

        if (!$data) {

            throw new RouteException('Отсутствует товар по ссылке ' . $this->parameters['alias']);

        }

        $data = array_shift($data);

        return compact('data');
    }

}