<?php

namespace core\admin\controllers;

class AddController extends BaseAdmin
{

    protected function inputData()
    {
        if (!isset($this->userID)) $this->executeBase();

        $this->createTableData();

        $this->createOutputData();

    }
}