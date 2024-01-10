<?php

namespace core\base\models;

use core\base\controllers\BaseMethods;
use core\base\controllers\Singleton;
use core\base\exceptions\AuthException;
use core\base\exceptions\DbException;
use DateTime;
use Exception;

class UserModel extends BaseModel
{

    use Singleton;

    use BaseMethods;

    private string $cookieName = 'identifier';

    private string $cookieAdminName = 'framework';

    private array $userData = [];

    private string $error;

    private string $userTable = 'visitors';

    private string $adminTable = 'users';

    private string $blockedTable = 'blocked_access';

    public function getError(): string
    {
        return $this->error;
    }

    public function getAdminTable(): string
    {
        return $this->adminTable;
    }

    public function getBlockedTable(): string
    {
        return $this->blockedTable;
    }

    /**
     * @throws DbException
     */
    public function setAdmin(): void
    {

        $this->cookieName = $this->cookieAdminName;

        $this->userTable = $this->adminTable;

        // Создание таблиц при их отсутствие в БД

        if (!in_array($this->userTable, $this->showTables())) {

            $query = 'create table ' . $this->userTable . '
                (
                    id int auto_increment primary key,
                    name varchar(255) null,
                    login varchar(255) null,
                    password varchar(32) null,
                    credentials text null
                )
                charset = utf8
            ';

            if (!$this->query($query, 'u')) {

                exit('Ошибка создания таблицы: ' . $this->userTable);

            }

            $this->create($this->userTable, [
                'fields' => ['name' => 'admin', 'login' => 'admin', 'password' => md5('admin-panel')]
            ]);

        }

        if (!in_array($this->blockedTable, $this->showTables())) {

            $query = 'create table ' . $this->blockedTable . '
                (
                    id int auto_increment primary key,
                    login varchar(255) null,
                    ip varchar(32) null,
                    trying tinyint(1) null,
                    time datetime null
                )
                charset = utf8
            ';

            if (!$this->query($query, 'u')) {

                exit('Ошибка создания таблицы: ' . $this->blockedTable);

            }

        }

    }

    /**
     * @throws DbException
     */
    public function checkUser($id = false, $admin = false): bool|array
    {

        $admin && $this->userTable !== $this->adminTable && $this->setAdmin();

        $method = 'unPackage';

        if ($id) {

            $this->userData['id'] = $id;

            $method = 'setCookie';

        }

        try {

            $this->$method();

        } catch (AuthException $e) {

            $this->error = $e->getMessage();

            !empty($e->getCode()) && $this->writeLog($this->error, 'log_user.txt');

            return false;

        }

        return $this->userData;

    }

    public function logout(): void
    {

        setCookie($this->cookieName, '', 1, PATH);

    }

    /**
     * @throws AuthException
     * @throws DbException
     */
    private function setCookie(): bool
    {

        $cookieString = $this->package();

        if ($cookieString) {

            setCookie($this->cookieName, $cookieString, time() + 60 * 60 * 24 * 365 * 10, PATH);

            return true;

        }

        throw new AuthException('Ошибка формирования cookie', 1);

    }

    /**
     * @throws DbException
     * @throws AuthException
     */
    private function package(): string
    {

        if (!empty($this->userData['id'])) {

            $data['id'] = $this->userData['id'];

            $data['version'] = COOKIE_VERSION;

            $data['cookieTime'] = date('Y-m-d H:i:s');

            return Crypt::instance()->encrypt(json_encode($data));

        }

        throw new AuthException('Некорректный идентификатор пользователя - ' . $this->userData['id'], 1);

    }

    /**
     * @throws AuthException
     * @throws DbException
     */
    private function unPackage(): bool
    {

        if (empty($_COOKIE[$this->cookieName])) throw new AuthException('Отсутствует cookie пользователя');

        $data = json_decode(Crypt::instance()->decrypt($_COOKIE[$this->cookieName]), true);

        if (empty($data['id']) || empty($data['version']) || empty($data['cookieTime'])) {

            $this->logout();

            throw new AuthException('Некорректные данные в cookie пользователя', 1);

        }

        $this->validate($data);

        $this->userData = $this->read($this->userTable, [
            'where' => ['id' => $data['id']]
        ]);

        if (!$this->userData) {

            $this->logout();
            throw new AuthException('Не найдены данные в таблице ' . $this->userTable .
                ' по идентификатору ' . $data['id'], 1);

        }

        $this->userData = $this->userData[0];

        return true;

    }

    // Метод проверяет версию и время cookie

    /**
     * @throws AuthException
     * @throws Exception
     */
    private function validate($data): void
    {

        if (!empty(COOKIE_VERSION)) {

            if ($data['version'] !== COOKIE_VERSION) {

                $this->logout();
                throw new AuthException('Некорректная версия cookie');

            }

        }

        if (!empty(COOKIE_TIME)) {

            if ((new DateTime()) > (new DateTime($data['cookieTime']))->modify(COOKIE_TIME . ' minutes')) {

                $this->logout();
                throw new AuthException('Превышено время бездействия пользователя');

            }

        }

    }



}