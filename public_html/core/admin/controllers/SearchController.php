<?php

namespace core\admin\controllers;

use core\base\controllers\BaseController;

class SearchController extends BaseAdmin
{

    protected function inputData()
    {

        if (!isset($this->userID)) $this->executeBase();

    }

}