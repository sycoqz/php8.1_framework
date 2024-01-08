<?php

namespace core\admin\controllers;

use core\base\controllers\BaseController;

class SearchController extends BaseAdmin
{

    protected function inputData()
    {

        if (!isset($this->userID)) $this->executeBase();

        $text = $this->clearStr($_GET['search']);

        $table = $_GET['search_table'];

        $this->data = $this->model->search($text, $table);

        $this->template = ADMIN_TEMPLATE . 'show';

        return $this->extension();

    }

}