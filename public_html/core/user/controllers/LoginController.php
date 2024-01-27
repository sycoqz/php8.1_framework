<?php

namespace core\user\controllers;

use core\base\exceptions\DbException;
use core\base\exceptions\RouteException;
use core\base\models\UserModel;
use core\user\controllers\BaseUser;
use core\user\traits\ValidationHelper;

class LoginController extends BaseUser
{

    use ValidationHelper;

    /**
     * @throws RouteException
     * @throws DbException
     */
    protected function inputData()
    {
        parent::inputData();

        if (!empty($this->parameters['alias'])) {

            switch ($this->parameters['alias']) {

                case 'registration':

                    $this->registration();

                    break;

            }

        }

        throw new RouteException('Такой страницы не существует');

    }

    /**
     * @throws DbException
     * @throws RouteException
     */
    protected function registration(): void
    {

        if (!$this->isPost()) {

            throw new RouteException('Такой страницы не существует');

        }

        $_POST['password'] = trim($_POST['password'] ?? '');

        $_POST['confirm_password'] = trim($_POST['confirm_password'] ?? '');

        if ($this->userData && !$_POST['password']) {

            unset($_POST['password']);

        }

        if (isset($_POST['password']) && $_POST['password'] !== $_POST['confirm_password']) {

            $this->sendError('Пароли не совпадают');

        }

        unset($_POST['confirm_password']);

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
        ];

        foreach ($_POST as $FormField => $item) {

            if (!empty($validation[$FormField]['methods'])) {

                foreach ($validation[$FormField]['methods'] as $method) {

                    $_POST[$FormField] = $item = $this->$method($item, $validation[$FormField]['translate'] ?? $FormField);

                }

            }

        }

        $where = [
            'phone' => $_POST['phone'],
            'email' => $_POST['email'],
        ];

        $condition[] = 'OR';

        $result = $this->model->read('visitors', [
            'where' => $where,
            'condition' => $condition,
            'limit' => 1
        ]);

        if ($result) {

            $result = $result[0];

            $field = $result['phone'] === $_POST['phone'] ? 'телефон' : 'email';

            $this->sendError('Такой ' . $field . ' уже зарегистрирован');

        }

        $id = $this->model->create('visitors', [
            'return_id' => true
        ]);

        if (!empty($id)) {

            if (UserModel::instance()->checkUser($id)) {

                $this->sendSuccess('Регистрация успешно пройдена');

            }

        }

        $this->sendError('Произошла внутренняя ошибка. Свяжитесь с администрацией сайта');

    }

}