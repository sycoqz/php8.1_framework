<?php

namespace core\admin\controllers;

use core\base\exceptions\DbException;
use DOMException;
use libraries\FileEdit;

class AjaxController extends BaseAdmin
{

    /**
     * @throws DOMException
     * @throws DbException
     */
    public function ajax(): bool|string|null|array
    {

        if (isset($this->ajaxData['ajax'])) {

            $this->executeBase();

            foreach ($this->ajaxData as $key => $item) $this->ajaxData[$key] = $this->clearStr($item);

            switch ($this->ajaxData['ajax']) {

                case 'sitemap':

                    $controller = new CreatesitemapController();

                    $controller->inputData($this->ajaxData['linksCounter'], false);

                case 'editData':

                    $_POST['return_id'] = true;

                    $this->checkPost();

                    return json_encode(['success' => 1]);

                case 'change_parent':

                    return $this->changeParent();

                case 'search':

                    return $this->search();

                case 'wyswyg_file':

                    $fileEdit = new FileEdit();

                    $fileEdit->setUniqueFile(false);

                    $file = $fileEdit->addFile($this->clearStr($this->ajaxData['table']) . '/content_files/');

                    return ['location' => PATH . UPLOAD_DIR . $file[key($file)]];

            }

        }

        return json_encode(['success' => 0, 'message' => 'No ajax variable']);

    }

    protected function search()
    {

        $data = $this->clearStr($this->ajaxData['data']);

        $table = $this->clearStr($this->ajaxData['table']);

        return $this->model->search($data, $table, 20);

    }

    protected function changeParent()
    {

        return $this->model->read($this->ajaxData['table'], [
            'fields' => ['COUNT(*) as count'],
            'where' => ['parent_id' => $this->ajaxData['parent_id']],
            'no_concat' => true
        ])[0]['count'] + $this->ajaxData['iteration'];

    }

}