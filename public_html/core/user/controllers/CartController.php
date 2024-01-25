<?php

namespace core\user\controllers;

use core\user\controllers\BaseUser;

class CartController extends BaseUser
{

    protected array $delivery;

    protected array $payment;

    protected function inputData()
    {
        parent::inputData();

        $this->delivery = $this->model->read('delivery', []);
        $this->payment = $this->model->read('payment', []);

    }

}