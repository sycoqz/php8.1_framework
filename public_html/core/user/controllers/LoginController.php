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

                case 'login':

                    $this->login();

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

        } elseif (!$this->userData && !trim($_POST['password'])) {

            $this->sendError('Заполните поле - пароль');

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

        if (!empty($_POST['password'])) {

            $_POST['password'] = md5($_POST['password']);

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

    /**
     * @throws DbException
     */
    protected function login(): void
    {

        $login = $this->clearStr($_POST['login'] ?? '');

        $password = $this->clearNum($_POST['password'] ?? '');

        if (!$login || !$password) {

            $this->sendError('Заполните поля авторизации');

        }

        $password = md5($password);

        if (preg_match('/@\w+\.\w+$/', $login)) {

            $result = $this->model->read('visitors', [
                'where' => ['email' => $login, 'password' => $password],
                'limit' => 1
            ]);

        } else {

            $result = $this->model->read('visitors', [
                'where' => ['phone' => $login, 'password' => $password],
                'limit' => 1
            ]);

        }

        if (!$result) {

            $this->sendError('Некорректные данные для входа');

        }

        if (UserModel::instance()->checkUser($result[0]['id'])) {

            $this->sendSuccess('Добро пожаловать, ' . $result[0]['name']);

        }

        $this->sendError('Произошла ошибка авторизации. Свяжитесь с администрацией сайта');


    }

}