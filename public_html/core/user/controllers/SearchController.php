<?php

namespace core\user\controllers;

use core\base\exceptions\DbException;
use core\user\controllers\BaseUser;
use core\user\models\Model;

class SearchController extends BaseUser
{

    protected object $model;

    protected function inputData()
    {
        parent::inputData();

        $goods = $this->search();

        $data['name'] = 'Результаты поиска' . (!empty($_GET['search']) ? ' по запросу <b>' . $_GET['search'] . '</b>' : '');

        $this->template = TEMPLATE . 'catalog';

        $dontShowAside = true;

        return compact('goods', 'data', 'dontShowAside');

    }

    /**
     * @throws DbException
     */
    public function search(): array|string
    {

        if (!isset($this->model)) $this->model = Model::instance();

        $search_str = $this->clearStr($_GET['search'] ?? '');

        $data = [];

        if ($search_str) {

            $goodsIds = $this->model->searchGoodsIds($search_str);

            if ($goodsIds) {

                $data = $this->model->getGoods([
                    'where' => ['id' => $goodsIds],
                    'operand' => ['IN']
                ]);

            }

        }

        return $data;

    }

}