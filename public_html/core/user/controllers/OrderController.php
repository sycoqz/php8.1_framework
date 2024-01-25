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



    }

}