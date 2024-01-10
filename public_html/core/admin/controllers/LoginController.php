<?php

namespace core\admin\controllers;

use core\base\controllers\BaseController;
use core\base\exceptions\DbException;
use core\base\exceptions\RouteException;
use core\base\models\UserModel;
use core\base\settings\Settings;
use DateTime;

class LoginController extends BaseController
{

    protected UserModel $model;

    /**
     * @throws DbException
     * @throws RouteException
     */
    protected function inputData()
    {

        $this->model = UserModel::instance();

        // Установка административного доступа
        $this->model->setAdmin();

        if (isset($this->parameters['logout'])) {

            $this->checkAuth(true);

            // Логирование входа и выхода пользователя
            $userLogInfo = 'Выход пользователя ' . $this->userID['name'];

            $this->writeLog($userLogInfo, 'user_log.txt', 'Access user');

            $this->model->logout();

            $this->redirect(PATH);

        }

        if ($this->isPost()) {

            // Проверка наличия токена
            if (empty($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {

                exit('Cookie ERROR');

            }

            $timeClean = (new DateTime())->modify('-' . BLOCK_TIME . ' hours')->format('Y-m-d H:i:s');

            // Удаление всех записей о пользователе из БД, при условии, что time меньше чем текущие время
            $this->model->delete($this->model->getBlockedTable(), [
                'where' => ['time' => $timeClean],
                'operand' => ['<']
            ]);

            // Получение ip пользователя
            $ipUser = filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP) ?:
                (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP) ?: @$_SERVER['REMOTE_ADDR']);

            // Получение кол-ва попыток авторизации
            $trying = $this->model->read($this->model->getBlockedTable(), [
                'fields' => ['trying'],
                'where' => ['ip' => $ipUser]
            ]);

            $trying = !empty($trying) ? $this->clearNum($trying[0]['trying']) : 0;

            $success = 0;

            if (!empty($_POST['login']) && !empty($_POST['password']) && $trying < 3) {

                $login = $this->clearStr($_POST['login']);

                $password = md5($this->clearStr($_POST['password']));

                $userData = $this->model->read($this->model->getAdminTable(), [
                    'fields' => ['id', 'name'],
                    'where' => ['login' => $login, 'password' => $password]
                ]);

                // Если выше перечисленные данные не пришли
                if (!$userData) {

                    $method = 'create';

                    $where = [];

                    if ($trying) {

                        $method = 'update';

                        $where['ip'] = $ipUser;

                    }

                    $this->model->$method($this->model->getBlockedTable(), [
                        'fields' =>['login' => $login, 'ip' => $ipUser, 'time' => 'NOW()', 'trying' => ++$trying],
                        'where' => $where
                    ]);

                    $error = 'Неверные имя пользователя или пароль - ' . $ipUser . ', логин - ' . $login;

                } else {

                    if (!$this->model->checkUser($userData[0]['id'])) {

                        $error = $this->model->getError();

                    } else {

                        $error = 'Вход пользователя - ' . $login;

                        $success = 1;

                    }

                }

            } elseif ($trying >= 3) {

                // Удаление Cookie
                $this->model->logout();

                $error = 'Превышено максимальное количество попыток авторизации - ' . $ipUser;

            } else {

                $error = 'Заполните обязательные поля';

            }

            $_SESSION['result']['answer'] = $success ?
                '<div class="success">Добро пожаловать ' . ($userData[0]['name'] ?? '') . '</div>' :
                preg_split('/\s*-/', $error, 2, PREG_SPLIT_NO_EMPTY)[0];

            $this->writeLog($error, 'user_log.txt', 'Access user');

            $path = null;

            $success && $path = PATH . Settings::get('routes')['admin']['alias'];

            // Перенаправление в админ панель
            $this->redirect($path);

        }

        return $this->render('', ['adminPath' => Settings::get('routes')['admin']['alias']]);

    }

}