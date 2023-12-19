<?php

namespace core\admin\controllers;

use core\base\exceptions\DbException;
use core\base\exceptions\RouteException;
use core\base\settings\Settings;

class AddController extends BaseAdmin
{

    protected string $action = 'add';

    /**
     * @throws RouteException
     * @throws DbException
     */
    protected function inputData()
    {
        if (!isset($this->userID)) $this->executeBase();

        $this->checkPost();

        $this->createTableData();

        $this->createForeignData();

        $this->createMenuPosition();

        $this->createRadio();

        $this->createOutputData();

        $this->createManyToMany();

        return $this->extension();

    }

}