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

            $this->delivery = $this->model->read('delivery', ['join_structure' => true]);

            $this->payment = $this->model->read('payment', ['join_structure' => true]);

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

        if (!($goods = $this->setOrdersGoods($order))) {

            $this->sendError('Ошибка сохранения товаров заказа. Обратитесь к администрации сайта');

        }

        $this->sendSuccess('Спасибо за заказ! Наши менеджеры свяжутся с вами в ближайшие время для уточнения деталей заказа');

        $order['delivery'] = $this->delivery[$order['delivery_id']]['name'] ?? '';

        $order['payment'] = $this->payment[$order['payment_id']]['name'] ?? '';

        $this->sendOrderEmail(['order' => $order, 'visitor' => $visitor, 'goods' => $goods]);

        $this->clearCart();

        $this->redirect();

    }

    protected function setOrdersGoods(array $order): ?array
    {

        if (in_array('orders_goods', $this->model->showTables())) {

            $ordersGoods = [];

            $preparedGoods = [];

            foreach ($this->cart['goods'] as $key => $item) {

                $ordersGoods[$key]['orders_id'] = $order['id'];

                $preparedGoods[$key] = $item;

                $preparedGoods[$key]['total_sum']  = $item['qty'] * $item['price'];

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

            if ($this->model->create('orders_goods',  [
                'fields' => $ordersGoods
            ])) {

                return $preparedGoods;

            }

        }

        return null;

    }

    protected function sendOrderEmail(array $orderData): void
    {

        $dir = TEMPLATE . 'include/orderTemplates/';

        $templateArr = [];

        if (is_dir($dir)) {

            $list = scandir($dir);

            foreach ($orderData as $name => $item) {

                if (($file = preg_grep('/^' . $name . '\./', $list))) {

                    $file = array_shift($file);

                    $template = file_get_contents($dir . $file);

                    // Проверка ключа
                    if  (!is_numeric(key($item))) {

                        $templateArr[] = $this->renderOrderMailTemplate($template, $item);

                    } else {

                        //Сборка header'a
                        if (($common = preg_grep('/' . $name . 'Header\./', $list))) {

                            $common = array_shift($common);

                            $templateArr[] = $this->renderOrderMailTemplate(file_get_contents($dir . $common), []);

                        }

                        //Сборка товаров
                        foreach ($item as $value) {

                            $templateArr[] = $this->renderOrderMailTemplate($template, $value);

                        }

                        //Сборка footer'a
                        if (($common = preg_grep('/' . $name . 'Footer\./', $list))) {

                            $common = array_shift($common);

                            $templateArr[] = $this->renderOrderMailTemplate(file_get_contents($dir . $common), []);

                        }

                    }

                }

            }

        }

    }

    protected function renderOrderMailTemplate(string $template, array $data): string
    {

        foreach ($data as $key => $item) {

            $template = preg_replace('/#' . $key . '#/i', $item ?? '', $template);

        }

        return $template;

    }

}