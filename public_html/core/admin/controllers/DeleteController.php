<?php

namespace core\admin\controllers;

use core\admin\controllers\BaseAdmin;
use core\base\settings\Settings;

class DeleteController extends BaseAdmin
{

    protected function inputData()
    {
        if (!isset($this->userID)) $this->executeBase();

        $this->createTableData();

        if (!empty($this->parameters[$this->table])) {

            $id = is_numeric($this->parameters[$this->table]) ?
                $this->clearNum($this->parameters[$this->table]) :
                $this->clearStr($this->parameters[$this->table]);

            if ($id) {

                $this->data = $this->model->read($this->table, [
                    'where' => [$this->columns['id_row'] => $id]
                ]);

                if ($this->data) {

                    // Удаление записи
                    $this->data = $this->data[0];

                    if (count($this->parameters) > 1) {

                        $this->checkDeleteFile();

                    }

                    $settings = $this->settings ?: Settings::instance();

                    $files = $settings::get('fileTemplates');

                    if ($files) {

                        foreach ($files as $file) {

                            foreach ($settings::get('templateArr')[$file] as $item) {

                                if (!empty($this->data[$item])) {

                                    if (preg_match('/^[\[\{].*?[\}\]]$/', $this->data[$item]))
                                        $fileData = json_decode($this->data[$item], true);
                                    else
                                        $fileData = $this->data[$item];

                                    if (is_array($fileData)) {

                                        foreach ($fileData as $file) {

                                            @unlink($_SERVER['DOCUMENT_ROOT'] . PATH . UPLOAD_DIR . $file);

                                        }

                                    } else {

                                        @unlink($_SERVER['DOCUMENT_ROOT'] . PATH . UPLOAD_DIR . $fileData);

                                    }

                                }

                            }

                        }

                    }

                    if (!empty($this->data['menu_position'])) {

                        $where = [];

                        if (!empty($this->data['parent_id'])) {

                            $pos = $this->model->read($this->table, [
                                'fields' => ['COUNT(*) as count'],
                                'where' => ['parent_id' => $this->data['parent_id']],
                                'no_concat' => true
                            ])[0]['count'];

                            $where = ['where' => 'parent_id'];

                        } else {

                            $pos = $this->model->read($this->table, [
                                'fields' => ['COUNT(*) as count'],
                                'no_concat' => true
                            ])[0]['count'];

                        }

                        $this->model->updateMenuPosition($this->table,
                            'menu_position', [$this->columns['id_row'] => $id], $pos, $where);

                    }

                    if ($this->model->delete($this->table, ['where' => [$this->columns['id_row'] => $id]])) {

                        $tables = $this->model->showTables();

                        if (in_array('old_alias', $tables)) {

                            $this->model->delete('old_alias', [
                                'where' => [
                                    'table_name' => $this->table,
                                    'table_id' => $id
                                ]
                            ]);

                        }

                        $manyToMany = $settings::get('manyToMany');

                        if ($manyToMany) {

                            foreach ($manyToMany as $mTable => $tables) {

                                $targetKey = array_search($this->table, $tables);

                                if ($targetKey !== false) {

                                    $this->model->delete($mTable, [
                                        'where' => [$tables[$targetKey] . '_' . $this->columns['id_row'] => $id]
                                    ]);

                                }

                            }

                        }

                        $_SESSION['result']['answer'] =
                            '<div class="success">' . $this->messages['deleteSuccess'] . '</div>';

                        $this->redirect($this->adminPath . 'show/' . $this->table);

                    }

                }

            }

        }

        $_SESSION['result']['answer'] = '<div class="error">' . $this->messages['deleteFail'] . '</div>';

        $this->redirect();

    }

    protected function checkDeleteFile()
    {

        $this->redirect();

    }

}