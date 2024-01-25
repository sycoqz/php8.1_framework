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

    protected array $cart = [];

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

        if (!$this->isAjax() &&  !$this->isPost()) {

            $this->getCartData();

        }

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

    protected function pagination(array $pages): void
    {

        $str = $_SERVER['REQUEST_URI'];

        if (preg_match('/page=\d+/i', $str)) {

            $str = preg_replace('/page=\d+/i', '', $str);

        }

        if (preg_match('/(\?&)|(\?amp;)/i', $str)) {

            $str = preg_replace('/(\?&)|(\?amp;)/i', '?', $str);

        }

        $basePageStr = $str;

        if (preg_match('/\?(.)?/i', $str,  $matches)) {

            if (!str_ends_with($str, '&') && !empty($matches[1])) {

                $str .= '&';

            } else {

                $basePageStr = preg_replace('/(\?$)|(&$)/', '', $str);

            }

        } else {

            $str .= '?';

        }

        $str .= 'page=';

        //SEO page 1
        $firstPageStr = !empty($pages['first']) ? ($pages['first'] === 1 ? $basePageStr : $str . $pages['first']) : '';

        $backPageStr = !empty($pages['back']) ? ($pages['back'] === 1 ? $basePageStr : $str . $pages['back']) : '';

        if (!empty($pages['first'])) {

            echo <<<HEREDOC
                <a href="$firstPageStr" class="catalog-section-pagination__item">
                    <<
                </a>
            HEREDOC;

        }

        if (!empty($pages['back'])) {

            echo <<<HEREDOC
                <a href="$backPageStr" class="catalog-section-pagination__item">
                    <
                </a>
            HEREDOC;

        }

        if (!empty($pages['previous'])) {

            foreach ($pages['previous'] as $item) {

                $href = $item === 1 ? $basePageStr : $str . $item;

                echo <<<HEREDOC
                    <a href="$href" class="catalog-section-pagination__item">
                        $item
                    </a>
                HEREDOC;

            }

        }

        if (!empty($pages['current'])) {

            echo <<<HEREDOC
                <a href="" class="catalog-section-pagination__item pagination-current">
                    {$pages['current']}
                </a>
            HEREDOC;

        }

        if (!empty($pages['next'])) {

            foreach ($pages['next'] as $item) {

                $href = $str . $item;
                echo <<<HEREDOC
                    <a href="$href" class="catalog-section-pagination__item">
                        $item
                    </a>
                HEREDOC;

            }

        }

        if (!empty($pages['forward'])) {

            $href = $str . $pages['forward'];

            echo <<<HEREDOC
                <a href="$href" class="catalog-section-pagination__item">
                    >
                </a>
            HEREDOC;

        }

        if (!empty($pages['last'])) {

            $href = $str . $pages['last'];

            echo <<<HEREDOC
                <a href="$href" class="catalog-section-pagination__item">
                    >>
                </a>
            HEREDOC;

        }

    }

    protected function addToCart(int|null $id, int $qty): bool|array
    {

        $id = $this->clearNum($id);

        $qty = $this->clearNum($qty) ?: 1;

        if (!$id) {

            return ['success' => 0, 'message' => 'Отсутствует идентификатор товара'];

        }

        $data = $this->model->read('goods', [
            'where' => ['id' => $id, 'visibility' => 1],
            'limit' => 1
        ]);

        if (!$data) {

            return ['success' => 0, 'message' => 'Отсутствует товар для добавления в корзину'];

        }

        $cart = &$this->getCart();

        $cart[$id] = $qty;

        $this->updateCart();

        // Формирование корзины
        $result = $this->getCartData(true);

        if ($result && !empty($result['goods'][$id])) {

            $result['current'] = $result['goods'][$id];

        }

        return $result;

    }

    protected function getCartData(bool $cartChanged = false): bool|array|null
    {

        if (!empty($this->cart) && !$cartChanged) {

            return $this->cart;

        }

        $cart = &$this->getCart();

        if (empty($cart)) {

            $this->clearCart();

            return false;

        }

        $goods = $this->model->getGoods([
            'where' => ['id' => array_keys($cart), 'visibility' => 1],
            'operand' => ['IN', '=']
        ], ...[false, false]);

        if (empty($goods)) {

            $this->clearCart();

            return false;

        }

        $cartChanged = false;

        foreach ($cart as $id => $qty) {

            if (empty($goods[$id])) {

                unset($cart[$id]);

                $cartChanged = true;

                continue;

            }

            $this->cart['goods'][$id] = $goods[$id];

            $this->cart['goods'][$id]['qty'] = $qty;

        }

        if ($cartChanged) {

            $this->updateCart();

        }

        return $this->totalSum();

    }

    protected function totalSum(): ?array
    {

        if (empty($this->cart['goods'])) {

            $this->clearCart();

            return null;

        }

        $this->cart['total_sum'] = $this->cart['total_old_sum'] = $this->cart['total_qty'] = 0;

        foreach ($this->cart['goods'] as $item) {

            $this->cart['total_qty'] += $item['qty'];

            $this->cart['total_sum'] += round($item['qty'] * $item['price'], 2);

            $this->cart['total_old_sum'] += round($item['qty'] * ($item['old_price'] ?? $item['price']), 2);

        }

        if ($this->cart['total_sum'] === $this->cart['total_old_sum']) {

            unset($this->cart['total_old_sum']);

        }

        return $this->cart;

    }

    protected function updateCart(): void
    {

        $cart = &$this->getCart();

        if (defined('CART') && strtolower(CART) === 'cookie') {

            setcookie('cart', json_encode($cart), time() + 3600 * 24 * 4, PATH);

        }

    }

    public function clearCart(): void
    {

        unset($_COOKIE['cart'], $_SESSION['cart']);

        if (defined('CART') && strtolower(CART) === 'cookie') {

            setcookie('cart', '', 1, PATH);

        }

        $this->cart = [];

    }

    protected function &getCart()
    {

        if (!defined('CART') || strtolower(CART) !== 'cookie') {

            if (!isset($_SESSION['cart'])) {

                $_SESSION['cart'] = [];

            }

            return $_SESSION['cart'];

        } else {

            if (!isset($_COOKIE['cart'])) {

                $_COOKIE['cart'] = [];

            } else {

                $_COOKIE['cart'] = is_string($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true)
                    : $_COOKIE['cart'];

            }

            return $_COOKIE['cart'];

        }

    }

    protected function deleteCartData(int $id): void
    {

        $id = $this->clearNum($id);

        if ($id) {

            $cart = &$this->getCart();

            unset($cart[$id]);

            $this->updateCart();

            $this->getCartData(true);

        }

    }

}