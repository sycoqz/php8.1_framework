<?php

namespace core\user\controllers;

use core\user\controllers\BaseUser;

class CartController extends BaseUser
{

    protected array $delivery;

    protected array $payment;

    protected function inputData(): void
    {
        parent::inputData();

        $this->delivery = $this->model->read('delivery');

        $this->payment = $this->model->read('payment');

        if (!empty($this->parameters['alias']) && $this->parameters['alias'] === 'remove') {

            if (!empty($this->parameters['id'])) {

                $this->deleteCartData($this->parameters['id']);

            } else {

                $this->clearCart();

            }

            $this->redirect($this->alias('cart'));

        }

    }

}