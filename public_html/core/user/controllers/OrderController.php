<?php

namespace core\user\controllers;

use core\user\traits\ValidationHelper;

class OrderController extends BaseUser
{

    use ValidationHelper;

    protected array $delivery = [];

    protected array $payment = [];

    protected function inputData(): void
    {
        parent::inputData();

        if ($this->isPost()) {

            $this->delivery = $this->model->read('delivery');

            $this->payment = $this->model->read('payment');

            $this->order();

        }

    }

    protected function order()
    {

        if (empty($this->cart['goods']) || empty($_POST)) {

            $this->sendError('Отсутствуют данные для оформления заказа');

        }

        $validation = [
            'name' => [
                'translate' => 'Ваше имя',
                'methods' => ['emptyField']
            ],
            'phone' => [
                'translate' => 'Телефон',
                'methods' => ['emptyField', 'phoneField', 'numericField']
            ],
            'email' => [
                'translate' => 'E-mail',
                'methods' => ['emptyField', 'emailField']
            ],
            'delivery_id' => [
                'translate' => 'Способ доставки',
                'methods' => ['emptyField', 'numericField']
            ],
            'payment_id' => [
                'translate' => 'Способ оплаты',
                'methods' => ['emptyField', 'numericField']
            ],
        ];

        $order = [];

        $visitior = [];

        $columnsOrders = $this->model->showColumns('orders');

        $columnsVisitors = $this->model->showColumns('visitors');

        foreach ($_POST as $key => $item) {

            if (!empty($validation[$key]['methods'])) {

                foreach ($validation[$key]['methods'] as $method) {

                    $_POST[$key] = $item = $this->$method($item, $validation[$key]['translate'] ?? $key);

                }

            }

            if (!empty($columnsOrders[$key])) {

                $order[$key] = $item;

            }

            if (!empty($columnsVisitors[$key])) {

                $visitior[$key] = $item;

            }

        }

    }

}