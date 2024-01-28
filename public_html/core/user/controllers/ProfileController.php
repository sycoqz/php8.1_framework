<?php

namespace core\user\controllers;

use core\user\controllers\BaseUser;
use core\user\models\Model;

class ProfileController extends BaseUser
{

    protected function inputData()
    {

        parent::inputData();

        if (!$this->userData) {

            $this->redirect();

        }

        $currentOrder =  null;

        $orders = $this->model->read('orders', [
            'where' => ['visitors_id' => $this->userData['id']],
            'order' => ['date'],
            'order_direction' => ['DESC'],
            'join' => [
                'orders_goods' => [
                    'on' => ['id', 'orders_id']
                ],
                'payment' => [
                    'on' => [
                        'table' => 'orders',
                        'fields' => ['payment_id', 'id']
                    ]
                ],
                'delivery' => [
                    'on' => [
                        'table' => 'orders',
                        'fields' => ['delivery_id', 'id']
                    ]
                ],
                'orders_statuses' => [
                    'on' => [
                        'table' => 'orders',
                        'fields' => ['orders_statuses_id', 'id']
                    ]
                ],
            ],
            'join_structure' => true
        ]);

        if ($orders) {

            foreach ($orders as $key => $item) {

                if (!empty($item['join'])) {

                    foreach ($item['join'] as $k => $value) {

                        if ($k !== 'orders_goods') {

                            $orders[$key]['join'][$k] = array_shift($value);

                        }

                    }

                }

            }

            if (!empty($this->parameters['id']) && !empty($orders[$this->parameters['id']])) {

                $currentOrder = $orders[$this->parameters['id']];

            }

        }

        $this->styles[] = PATH . TEMPLATE . 'assets/css/profile.css';

        return compact('orders', 'currentOrder');

    }

}