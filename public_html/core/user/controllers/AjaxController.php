<?php

namespace core\user\controllers;

use core\admin\controllers\CreatesitemapController;
use core\base\exceptions\DbException;

class AjaxController extends BaseUser
{

    /**
     * @throws DbException
     */
    public function ajax()
    {

        if (isset($this->ajaxData['ajax'])) {

            $this->inputData();

            foreach ($this->ajaxData as $key => $item) $this->ajaxData[$key] = $this->clearStr($item);

            switch ($this->ajaxData['ajax']) {

                case 'catalog_quantities':

                    $qty = $this->clearNum($this->ajaxData['qty'] ?? 0);

                    $qty && $_SESSION['quantities'] = $qty;

                    break;

                case 'add_to_cart':

                    return $this->_addToCart();

            }

        }

        return json_encode(['success' => '0', 'message' => 'No ajax variable']);

    }

    protected function _addToCart()
    {

        return $this->addToCart($this->ajaxData['id'] ?? null, $this->ajaxData['qty'] ?? 1);

    }

}