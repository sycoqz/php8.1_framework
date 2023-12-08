<?php

namespace core\admin\controllers;

use core\base\controllers\BaseAjax;
use core\base\exceptions\DbException;
use DOMException;

class AjaxController extends BaseAjax
{

    /**
     * @throws DOMException
     * @throws DbException
     */
    public function ajax(): bool|string|null
    {

        if (isset($this->data['ajax'])) {

            switch ($this->data['ajax']) {

                case 'sitemap':

                    return (new CreatesitemapController())->inputData($this->data['linksCounter'], false);

            }

        }

        return json_encode(['success' => 0, 'message' => 'No ajax variable']);

    }

}