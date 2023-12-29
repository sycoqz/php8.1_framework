<?php

namespace core\admin\controllers;

use core\base\exceptions\DbException;
use DOMException;

class AjaxController extends BaseAdmin
{

    /**
     * @throws DOMException
     * @throws DbException
     */
    public function ajax(): bool|string|null
    {

        if (isset($this->data['ajax'])) {

            $this->executeBase();

            switch ($this->data['ajax']) {

                case 'sitemap':

                    $controller = new CreatesitemapController();

                    $controller->inputData($this->data['linksCounter'], false);

                case 'editData':

                    $_POST['return_id'] = true;

                    $this->checkPost();

                    return json_encode(['success' => 1]);

            }

        }

        return json_encode(['success' => 0, 'message' => 'No ajax variable']);

    }

}