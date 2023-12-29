<?php

namespace core\admin\controllers;

use core\base\exceptions\DbException;
use core\base\exceptions\RouteException;

class EditController extends BaseAdmin
{

    protected string $action = 'edit';

    /**
     * @throws RouteException
     * @throws DbException
     */
    protected function inputData()
    {
        if (!isset($this->userID)) $this->executeBase();

        $this->checkPost();

        $this->createTableData();

        $this->createData();

        $this->createForeignData();

        $this->createMenuPosition();

        $this->createRadio();

        $this->createOutputData();

        $this->createManyToMany();

        $this->template = ADMIN_TEMPLATE . 'add';

        return $this->extension();

    }

    // Получение данных из БД

    /**
     * @throws RouteException
     */
    protected function createData(): void
    {

        $id = is_numeric($this->parameters[$this->table]) ?
            $this->clearNum($this->parameters[$this->table]) :
            $this->clearStr($this->parameters[$this->table]);

        if (!$id) throw new RouteException('Некорректный идентификатор - '
            . $id . ' при редактировании таблицы - ' . $this->table);

        $this->data = $this->model->read($this->table, [
            'where' => [$this->columns['id_row'] => $id]
        ]);

        $this->data && $this->data = $this->data[0];
    }

}