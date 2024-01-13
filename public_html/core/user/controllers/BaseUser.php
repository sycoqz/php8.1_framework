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

    /**
     * @throws DbException
     */
    protected function inputData()
    {

        $this->init();

        if (!isset($this->model)) $this->model = Model::instance(); // !$this->model && $this->model = Model::instance();

    }

    /**
     * @throws RouteException
     * @throws DbException
     */
    protected function outputData(): bool|string
    {

        if (!isset($this->content)) {

            $args = func_get_arg(0);
            $vars = $args ?: [];

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

                        foreach ($item as $value) {

                            $str .= $key . '=' . $value;

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

}