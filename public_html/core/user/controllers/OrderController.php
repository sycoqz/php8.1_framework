<?php

namespace core\user\controllers;

use core\base\exceptions\DbException;
use core\base\models\UserModel;
use core\user\traits\ValidationHelper;
use JetBrains\PhpStorm\NoReturn;

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

    /**
     * @throws DbException
     */
    #[NoReturn] protected function order(): void
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

        $visitor = [];

        $columnsOrders = $this->model->showColumns('orders');

        $columnsVisitors = $this->model->showColumns('visitors');

        foreach ($_POST as $FormField => $item) {

            if (!empty($validation[$FormField]['methods'])) {

                foreach ($validation[$FormField]['methods'] as $method) {

                    $_POST[$FormField] = $item = $this->$method($item, $validation[$FormField]['translate'] ?? $FormField);

                }

            }

            if (!empty($columnsOrders[$FormField])) {

                $order[$FormField] = $item;

            }

            if (!empty($columnsVisitors[$FormField])) {

                $visitor[$FormField] = $item;

            }

        }

        if (empty($visitor['email']) && empty($visitor['phone'])) {

            $this->sendError('Отсутствуют данные пользователя для оформления заказа');

        }

        $visitorsWhere = $visitorsCondition = [];

        if (!empty($visitor['email']) && !empty($visitor['phone'])) {

            $visitorsWhere = [
                'email' => $visitor['email'],
                'phone' => $visitor['phone'],
            ];

            $visitorsCondition = ['OR'];

        } else {

            $visitorsKey = !empty($visitor['email']) ? 'email' : 'phone';

            $visitorsWhere[$visitorsKey] = $visitor[$visitorsKey];

        }

        $resultVisitor = $this->model->read('visitors', [
            'where' => $visitorsWhere,
            'condition' => $visitorsCondition,
            'limit' => 1
        ]);

        if ($resultVisitor) {

            $resultVisitor = $resultVisitor[0];

            $order['visitors_id'] = $resultVisitor['id'];

        } else {

            $order['visitors_id'] = $this->model->create('visitors',  [
                'fields' => $visitor,
                'return_id' => true
            ]);

        }

        $order['total_sum'] = $this->cart['total_sum'];

        $order['total_qty'] = $this->cart['total_qty'];

        $order['total_old_sum'] = $this->cart['total_old_sum'] ?? null;

        $baseStatus = $this->model->read('orders_statuses', [
            'fields' => ['id'],
            'order' => ['menu_position'],
            'limit' => 1
        ]);

        $baseStatus && $order['orders_statuses_id'] = $baseStatus[0]['id'];

        $order['id'] = $this->model->create('orders', [
            'fields' => $order,
            'return_id' => true,
        ]);

        if (!$order['id']) {

            $this->sendError('Ошибка сохранения заказа. Свяжитесь с администрацией сайта по телефону  - ' . $this->set['phone']);

        }

        if (!$resultVisitor) {

            UserModel::instance()->checkUser($order['visitors_id']);

        }

        if (!$this->setOrdersGoods($order)) {

            $this->sendError('Ошибка сохранения товаров заказа. Обратитесь к администрации сайта');

        }

        $this->sendSuccess('Спасибо за заказ! Наши менеджеры свяжутся с вами в ближайшие время для уточнения деталей заказа');

        $this->sendOrderEmail(['order' => $order, 'visitor' => $visitor]);

        $this->clearCart();

        $this->redirect();

    }

    protected function setOrdersGoods(array $order): bool
    {

        if (in_array('orders_goods', $this->model->showTables())) {

            $ordersGoods = [];

            foreach ($this->cart['goods'] as $key => $item) {

                $ordersGoods[$key]['orders_id'] = $order['id'];

                foreach ($item as $field => $value) {

                    if (!empty($this->model->showColumns('orders_goods')[$field])) {

                        if  ($this->model->showColumns('orders_goods')['id_row'] === $field) {

                            if (!empty($this->model->showColumns('orders_goods')['goods_id'])) {

                                $ordersGoods[$key]['goods_id'] = $value;

                            }

                        } else {

                            $ordersGoods[$key][$field] = $value;

                        }

                    }

                }

            }

            return $this->model->create('orders_goods',  [
                'fields' => $ordersGoods
            ]);

        }

        return false;

    }

    protected function sendOrderEmail(array $orderData)
    {



    }

}