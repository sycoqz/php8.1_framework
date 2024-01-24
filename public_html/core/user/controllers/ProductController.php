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

        $deliveryInfo = $this->model->read('information', [
            'where' => ['visibility' => 1, 'name' => 'доставка', 'name' => 'оплата'],
            'operand' => ['=', '%LIKE%'],
            'condition' => ['AND', 'OR'],
            'limit' => 1
        ]);

        $deliveryInfo && $deliveryInfo = $deliveryInfo[0];

        return compact('data', 'deliveryInfo');
    }

}