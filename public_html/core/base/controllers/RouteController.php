<?php

namespace core\base\controllers;

use core\base\exceptions\DbException;
use core\base\exceptions\RouteException;
use core\base\settings\Settings;

class RouteController extends BaseController
// Разбор адресной строки
{
    use Singleton;

    protected array $routes;
    protected string $controller;
    protected ?string $inputMethod = null;
    protected ?string $outputMethod = null;
    protected ?array $parameters = null;

    /**
     * @throws RouteException
     * @throws DbException
     */
    private function __construct()
    {

        $address_str = $_SERVER['REQUEST_URI'];

        $path = substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], 'index.php'));

        if ($path === PATH) {

            $this->routes = Settings::get('routes');

            if (!$this->routes) throw new RouteException('Отсутствуют маршруты в базовых настройках.', 1);

            $url = preg_split('/(\/)|(\?.*)/', $address_str, 0, PREG_SPLIT_NO_EMPTY);

            // Проверка на наличие первого элемента и строго равен подстроке Админ

            if (isset($url[0]) && $url[0] === $this->routes['admin']['alias']) { // Административная часть

                array_shift($url); // Выкидывает первый элемент из массива, а затем пересортировывает массив

                if (isset($url[0]) && is_dir($_SERVER['DOCUMENT_ROOT'] . PATH . $this->routes['plugins']['path'] . $url[0])) {

                    $plugin = array_shift($url);

                    $pluginSettings = $this->routes['settings']['path'] . ucfirst($plugin . 'Settings'); // Запись пути к файлу с настройками для плагина.

                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . PATH . $pluginSettings . '.php')) { // Проверка есть ли такой файл с настройками.

                        $pluginSettings = str_replace('/', '\\', $pluginSettings); // Запись имени ссылающегося на класс вместе с полным namespace'ом
                        $pluginSettingsClass = new $pluginSettings(); // Создание объекта класса
                        $this->routes = $pluginSettingsClass->get('routes'); // Вызов метода 'get' на объекте класса

                    }

                    $dir = $this->routes['plugins']['dir'] ? '/' . $this->routes['plugins']['dir'] . '/' : '/';

                    // Защита от непредвиденных изменений
                    $dir = str_replace('//', '/', $dir);

                    // Формирование строки exp: core/plugins/shop/controller
                    $this->controller = $this->routes['plugins']['path'] . $plugin . $dir;

                    $hrUrl  = $this->routes['plugins']['hrUrl'];

                    $route = 'plugins';

                } else {

                    $this->controller = $this->routes['admin']['path'];

                    $hrUrl  = $this->routes['admin']['hrUrl'];

                    $route = 'admin';
                }

            } else { // Пользовательская часть

                if (!$this->isPost()) {

                    $pattern = '';

                    $replacement = '';

                    if (END_SLASH) {

                        if (!preg_match('/\/(\?|$)/', $address_str)) {

                            $pattern = '/(^.*?)(\?.*)?$/';

                            $replacement = '$1/';

                            }

                        } else {

                        if (preg_match('/\/(\?|$)/', $address_str)) {

                            $pattern = '/(^.*?)\/(\?.*)?$/';

                            $replacement = '$1';

                        }

                    }

                    if ($pattern) {

                        $address_str = preg_replace($pattern, $replacement, $address_str);

                        if (!empty($_SERVER['QUERY_STRING'])) {

                            $address_str .= '?' . $_SERVER['QUERY_STRING'];

                        }

                        $this->redirect($address_str, 301);

                    }

                }

                $hrUrl = $this->routes['user']['hrUrl'];

                $this->controller = $this->routes['user']['path'];

                $route = 'user';
            }
            // Создание маршрута
            $this->createRoute($route, $url);

            // Создание набора параметров в адресной строке

            if (isset($url[1])) {
                $count = count($url);
                $key = '';

                if (!$hrUrl) {
                    $i = 1;
                } else {
                    $this->parameters['alias'] = $url[1]; // Сохранение ЧПУ
                    $i = 2;
                }

                for (; $i < $count; $i++) {
                    if (!$key) {
                        $key = $url[$i];
                        $this->parameters[$key] = '';
                    } else {
                        $this->parameters[$key] = $url[$i];
                        $key = '';
                    }
                }
            }

        } else {
            throw new RouteException('Некорректная директория сайта.', 1);
        }

    }

    private function createRoute(string $var, array $arr): void
    {
        $route = [];

        if (!empty($arr[0])) {
            if (isset($this->routes[$var]['routes'][$arr[0]])) {
                $route = explode('/', $this->routes[$var]['routes'][$arr[0]]);

                $this->controller .= ucfirst($route[0] . 'Controller');
            } else {
                $this->controller .= ucfirst($arr[0] . 'Controller');
            }
        } else {
            $this->controller .= $this->routes['default']['controller'];
        }

        if (isset($route[1])) $this->inputMethod = $route[1] ?: $this->routes['default']['inputMethod'];
        if (isset($route[2])) $this->outputMethod = $route[2] ?: $this->routes['default']['outputMethod'];
    }

}