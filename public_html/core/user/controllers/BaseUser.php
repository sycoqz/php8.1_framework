<?php

namespace core\user\controllers;

use core\base\controllers\BaseController;
use core\base\exceptions\DbException;
use core\base\exceptions\RouteException;
use core\user\models\Model;

abstract class BaseUser extends BaseController
{

    protected object $model;

    protected string|null $table = null;

    protected array $set = [];

    protected array $menu = [];

    protected string $breadcrumbs;

    /* Проектные свойства */
    protected array $social_networks = [];
    /* Проектные свойства */

    /**
     * @throws DbException
     */
    protected function inputData()
    {

        $this->init();

        if (!isset($this->model)) $this->model = Model::instance(); // !$this->model && $this->model = Model::instance();

        $this->set = $this->model->read('settings', [
            'order' => ['id'],
            'limit' => 1
        ]);

        $this->set && $this->set = $this->set[0];

        // Сборка меню
        $this->menu['catalog'] = $this->model->read('catalog', [
            'where' => ['visibility' => 1, 'parent_id' => null],
            'order' => ['menu_position']
        ]);

        $this->menu['information'] = $this->model->read('information', [
            'where' => ['visibility' => 1, 'show_top_menu' => 1],
            'order' => ['menu_position']
        ]);

        $this->social_networks = $this->model->read('social_networks', [
            'where' => ['visibility' => 1],
            'order' => ['menu_position']
        ]);

    }

    /**
     * @throws RouteException
     * @throws DbException
     */
    protected function outputData(): bool|string
    {

        $args = func_get_arg(0);
        $vars = $args ?: [];

        $this->breadcrumbs = $this->render(TEMPLATE . 'include/breadcrumbs');

        if (!isset($this->content)) {

            $this->content = $this->render($this->template, $vars);

        }

        $this->header = $this->render(TEMPLATE . 'include/header', $vars);
        $this->footer = $this->render(TEMPLATE . 'include/footer', $vars);

        return $this->render(TEMPLATE . 'layout/default');

    }

    protected function img(string $img = '', bool $tag = false): string
    {

        if (!$img && is_dir($_SERVER['DOCUMENT_ROOT'] . PATH . UPLOAD_DIR . DEFAULT_IMG_DIRECTORY)) {

            $dir = scandir($_SERVER['DOCUMENT_ROOT'] . PATH . UPLOAD_DIR . DEFAULT_IMG_DIRECTORY);

            $imgArr = preg_grep('/' . $this->getController() .'\./i', $dir)
                ?: preg_grep('/default\./i', $dir);

            $imgArr && $img = DEFAULT_IMG_DIRECTORY . '/' . array_shift($imgArr);

        }

        if (!empty($img)) {

            $path = PATH . UPLOAD_DIR . $img;

            if (!$tag) {

                return $path;

            }

            echo '<img src="' . $path . '" alt="image" title="image">';

        }

        return  '';

    }

    protected function alias(string|array $alias = '', string|array $queryString = ''): array|string|null
    {

        $str = '';

        if (!empty($queryString)) {

            if (is_array($queryString)) {

                foreach ($queryString as $key => $item) {

                    $str .= (!$str ? '?' : '&');

                    if (is_array($item)) {

                        $key .= '[]';

                        foreach ($item as $filterId => $value) {

                            $str .= $key . '=' . $value . (!empty($item[$filterId+1]) ? '&' : '');

                        }

                    } else {

                        $str .= $key . '=' . $item;

                    }

                }

            } else {

                if (!str_contains($queryString, '?')) {

                    $str = '?' . $str;

                }

                $str .= $queryString;

            }

        }

        if (is_array($alias)) {

            $aliasStr = '';

            foreach ($alias as $key => $item) {

                if (!is_numeric($key) && $item) {

                    $aliasStr .= $key . '/' . $item . '/';

                } elseif ($item) {

                    $aliasStr .= $item . '/';

                }

            }

            $alias = trim($aliasStr, '/');

        }

        if (!$alias || $alias === '/') {

            return PATH . $str;

        }

        if (preg_match('/^\s*https?:\/\//i', $alias)) {

            return $alias . $str;

        }

        return preg_replace('/\/{2,}/', '/', PATH . $alias . END_SLASH . $str);

    }

    protected function wordsForCounter(int $counter, array|string $arrElement = 'years')
    {

        $arr = [
            'years' => [
                'лет',
                'год',
                'года'
            ]
        ];

        if (is_array($arrElement)) {

            $arr = $arrElement;

        } else {

            $arr = $arr[$arrElement] ?? array_shift($arr);

        }

        if (!$arr) return null;

        $char = (int)substr($counter, -1);

        $counter = (int)substr($counter, -2);

        if (($counter >= 10 && $counter <= 20) || ($char >= 5 && $char <= 9) || !$char)
            return $arr[0] ?? null;
        elseif ($char === 1)
            return $arr[1] ?? null;
        else
            return $arr[2] ?? null;

    }

    /**
     * @throws RouteException
     * @throws DbException
     */
    protected function showGoods(array $data, array $parameters = [], string $template = 'goodsItem'): void
    {

        if (!empty($data)) {

            echo $this->render(TEMPLATE . 'include/' . $template, compact('data', 'parameters'));

        }

    }

}