<?php

namespace core\admin\controllers;

use core\admin\models\Model;
use core\base\controllers\BaseController;
use core\base\exceptions\DbException;
use core\base\exceptions\RouteException;
use core\base\settings\Settings;
use JetBrains\PhpStorm\NoReturn;
use libraries\FileEdit;
use libraries\TextModify;
use function libraries\mb_str_replace;

abstract class BaseAdmin extends BaseController
{

    protected object $model;

    protected string $table;

    protected array $columns = [];

    protected array $foreignData = [];

    protected string $adminPath;

    protected array $menu;

    protected string $title;

    protected array $warningUser; // $translate

    protected array $blocks = [];

    protected array $templateArr;

    protected string $formTemplates;

    protected ?bool $noDelete = null;

    protected array $messages;

    protected bool|Settings|null $settings = null;

    protected array $fileArray = [];

    protected string $alias = '';


    /**
     * @throws DbException
     */
    protected function inputData()
    {
        $this->init(true);

        $this->title = 'php 8.1 Framework';

        if (!isset($this->model)) $this->model = Model::instance();
        if (!isset($this->menu)) $this->menu = Settings::get('projectTables');
        if (!isset($this->adminPath)) $this->adminPath = PATH . Settings::get('routes')['admin']['alias'] . '/';

        if (!isset($this->templateArr)) $this->templateArr = Settings::get('templateArr');
        if (!isset($this->formTemplates)) $this->formTemplates = Settings::get('formTemplates');

        if (!isset($this->messages)) $this->messages = include $_SERVER['DOCUMENT_ROOT']
            . PATH . Settings::get('messages') . 'informationMessages.php';

        $this->sendNoCacheHeaders();

    }

    /**
     * @throws RouteException
     * @throws DbException
     */
    protected function outputData(): bool|string
    {
        if (!isset($this->content)) {

            $args = func_get_arg(0);
            $vars = $args ?: [];

            $this->content = $this->render($this->template, $vars);

        }

        $this->header = $this->render(ADMIN_TEMPLATE . 'include/header');
        $this->footer = $this->render(ADMIN_TEMPLATE . 'include/footer');

        return $this->render(ADMIN_TEMPLATE . 'layout/default');
    }

    protected function sendNoCacheHeaders(): void
    {
        header('Last-Modified: ' . gmdate('D, d m Y H:i:s') . ' GMT'); // Отправка заголовков последней модификации сайта браузеру.
        header('Cache-Control: no-cache, must-revalidate');
        header('Cache-Control: max-age=0');
        header('Cache-Control: post-check=0,pre-check=0');
    }

    /**
     * @throws DbException
     */
    protected function executeBase(): void
    {
        self::inputData();
    }

    /**
     * @throws DbException
     */
    protected function createTableData($settings = false): void
    {

        if (!isset($this->table)) {

            if (isset($this->parameters)) $this->table = array_keys($this->parameters)[0];
                else {

                    if (!$settings) $settings = Settings::instance();
                    $this->table = $settings::get('defaultTable');

                }

        }

        $this->columns = $this->model->showColumns($this->table);

        if (!isset($this->columns)) new RouteException('Не найдены поля в таблице - ' . $this->table, 2);

    }

    /**
     * @throws DbException
     */
    protected function extension(array $args = [], $settings = false): mixed
    {
        $filename = explode('_', $this->table);
        $className = '';

        foreach ($filename as $item) $className .= ucfirst($item);

        if (!$settings) {

            $path = Settings::get('extension');

        } elseif (is_object($settings)) {

            $path = $settings::get('extension');

        } else {

            $path = $settings;
        }

        $class = $path . $className . 'Extension';

        if (is_readable($_SERVER['DOCUMENT_ROOT'] . PATH . $class . '.php')) {

            $class = str_replace('/', '\\', $class);

            if (class_exists($class)) { // Проверка строки. Является ли классом.

                if (method_exists($class, 'instance')) { // Проверка есть ли метод instance в классе.

                    $ext = $class::instance();

                    foreach ($this as $name => $value) {

                        $ext->$name = &$this->$name; // Сохранение ссылок на свойства класса

                    }

                    return $ext->extension($args);

                }
            }

        } else {

            $file = $_SERVER['DOCUMENT_ROOT'] . PATH . $path . $this->table . '.php';

            extract($args);

            if (is_readable($file)) return include $file;

        }

        return false;

    }

    /**
     * @throws DbException
     */
    protected function createOutputData($settings = false): void
    {

        if (!$settings) $settings = Settings::instance();

        // Создания свойства для блоков данных.
        $blocks = $settings::get('blockNeedle');
        $this->warningUser = $settings::get('warningUser');

        // Если блоки не пришли
        if (!isset($blocks) || !is_array($blocks)) {

            foreach ($this->columns as $name => $item) {

                if ($name === 'id_row ') continue;

                if (!isset($this->warningUser[$name])) $this->warningUser[$name][] = $name;

                // Распределение подключения шаблона.
                $this->blocks[0][] = $name;
            }

            return;
        }

        $default = array_keys($blocks)[0];

        foreach ($this->columns as $name => $item) {

            if ($name === 'id_row') continue;

            $insert = false;

            foreach ($blocks as $block => $value) {

                // Создание ключа, при его отсутствие.
                if (!array_key_exists($block, $this->blocks)) $this->blocks[$block] = [];

                if (in_array($name, $value)) {

                    $this->blocks[$block][] = $name;
                    $insert = true;
                    break;

                }
            }

            if (!$insert) $this->blocks[$default][] = $name;
            if (!isset($this->warningUser[$name])) $this->warningUser[$name][] = $name;

        }

    }

    /**
     * @throws DbException
     */
    protected function createRadio($settings = false): void
    {

        if (!$settings) $settings = Settings::instance();

        $radio = $settings::get('radio');

        if (isset($radio)) {

            foreach ($this->columns  as $name => $item) {

                if (isset($radio[$name])) {

                    $this->foreignData[$name] = $radio[$name];

                }
            }

        }

    }

    /**
     * @throws DbException
     */
    protected function checkPost($settings = false): void
    {

        if ($this->isPost()) {

            $this->clearPostFields($settings);
            $this->table = $this->clearStr($_POST['table']);

            unset($_POST['table']);

            if ($this->table) {

                $this->createTableData($settings);
                $this->editData();

            }

        }

    }

    #[NoReturn] protected function addSessionData(array $arr = []): void
    {

        if (!isset($arr)) $arr = $_POST;

        foreach ($arr as $key => $item) {

            $_SESSION['result'][$key] = $item;

        }

        $this->redirect();

    }

    protected function countChar(string $str, int $counter, $answer, array $arr): void
    {
        if (mb_strlen($str) > $counter) {

            $str_result = mb_str_replace('$1', $answer, $this->messages['count']);
            $str_result = mb_str_replace('$2', $counter, $str_result);

            $_SESSION['result']['answer'] = '<div class="error">' . $str_result . '</div>';
            $this->addSessionData($arr);
        }
    }

    protected function emptyFields(string $str, $answer, array $arr = []): void
    {

        if (empty($str)) {

            $_SESSION['result']['answer'] = '<div class="error">' . $this->messages['empty'] . ' ' . $answer . '</div>';
            $this->addSessionData($arr);

        }

    }

    /**
     * @throws DbException
     */
    protected function clearPostFields($settings, array &$arr = []): bool
    {
        if (!$arr) $arr = &$_POST;

        if (!$settings) $settings = Settings::instance();

        if (isset($this->columns['id_row'])) $id = $_POST[$this->columns['id_row']] ?: false;

        $validate = $settings::get('validation');
        if (!isset($this->warningUser)) $this->warningUser = $settings::get('warningUser');

        //Рекурсивный метод
        foreach ($arr as $key => $item) {

            if (is_array($item)) {

                $this->clearPostFields($settings, $item);

            } else {

                if (is_numeric($item)) {

                    $arr[$key] = $this->clearNum($item);
                }

                // Проверка на наличие данных в массиве валидации.
                if ($validate) {

                    if (isset($validate[$key])) {

                        if (isset($this->warningUser[$key])) {

                            $answer = $this->warningUser[$key][0];

                        } else {

                            $answer = $key;

                        }

                        if (isset($validate[$key]['crypt'])) {

                            if (isset($id)) {

                                if (empty($item)) {
                                    unset($arr[$key]);
                                    continue;
                                }

                                $arr[$key] = md5($item);
                            }
                        }

                        if (isset($validate[$key]['empty'])) $this->emptyFields($item, $answer, $arr);

                        if (isset($validate[$key]['trim'])) $arr[$key] = trim($item);

                        if (isset($validate[$key]['int'])) $arr[$key] = $this->clearNum($item);

                        if (isset($validate[$key]['count'])) $this->countChar($item, $validate[$key]['count'], $answer, $arr);

                    }
                }
            }
        }

        return true;

    }

    /**
     * @throws DbException
     */
    protected function editData(bool $returnId = false)
    {
        $id = false;
        $method = 'create';
        $where = [];

        if (isset($_POST[$this->columns['id_row']])) {

            $id = is_numeric($_POST[$this->columns['id_row']]) ? $this->clearNum($_POST[$this->columns['id_row']])
                : $this->clearStr($_POST[$this->columns['id_row']]);

            // Переопределение метода с добавления на редактирование.
            if (!empty($id)) {

                $where = [$this->columns['id_row'] => $id];
                $method = 'edit';

            }
        }

        foreach ($this->columns as $key => $item) {

            if (is_array($item)) {

                if ($item['Type'] === 'date' || $item['Type'] === 'datetime') {

                    empty($_POST[$key]) && $_POST[$key] = 'NOW()';

                }
            }
        }

        $this->createFile();

        $this->createAlias($id);

        $this->updateMenuPosition();

        $except = $this->checkExceptFields();

        $resultId = $this->model->$method($this->table,[
            'files' => $this->fileArray,
            'where' => $where,
            'returnId' => true,
            'except' => $except
        ]);

        if (empty($id) && $method === 'create') {

            $_POST[$this->columns['id_row']] = $resultId;
            $answerSuccess = $this->messages['addSuccess'];
            $answerFail = $this->messages['addFail'];

        } else {

            $answerSuccess = $this->messages['editSuccess'];
            $answerFail = $this->messages['editFail'];

        }

        $this->checkManyToMany();

        $this->extension(get_defined_vars());

        $result = $this->checkAlias($_POST[$this->columns['id_row']] ?? '');

        if ($resultId) {

            $_SESSION['result']['answer'] = '<div class="success">' . $answerSuccess . '</div>';

            if (!$returnId) $this->redirect();

            return $_POST[$this->columns['id_row']];

        } else {

            $_SESSION['result']['answer'] = '<div class="error">' . $answerFail . '</div>';

            if (!$returnId) $this->redirect();

            return false;

        }

    }

    // Исключение полей из системы добавления БД.
    protected function checkExceptFields(array $arr = []): array
    {

        $except = [];

        if (empty($arr)) $arr = $_POST;

        if (!empty($arr)) {

            foreach ($arr as $key => $item) {

                if (empty($this->columns[$key])) $except[] = $key;

            }
        }

        return $except;

    }

    protected function createFile(): void
    {

        $fileEdit = new FileEdit();

        $this->fileArray = $fileEdit->addFile();

    }

    protected function updateMenuPosition($id = false): void
    {

        if (isset($_POST['menu_position'])) {

            $where = false;

            if ($id && $this->columns['id_row']) $where = [$this->columns['id_row'] => $id];

            if (array_key_exists('parent_id', $_POST)) {

                $this->model->updateMenuPosition($this->table, 'menu_position',
                    $where, $_POST['menu_position'], ['where' => 'parent_id']);

            } else {

                $this->model->updateMenuPosition($this->table, 'menu_position',
                    $where, $_POST['menu_position']);

            }

        }

    }

    protected function createAlias($id = false): void
    {

        $alias_str = '';

        if (isset($this->columns['alias'])) {

            if (!isset($_POST['alias'])) {

                if (isset($_POST['name'])) {

                    $alias_str = $this->clearStr($_POST['name']);

                } else {

                    foreach ($_POST as $key => $item) {

                        if (str_contains($key,'name') && $item) {

                            $alias_str = $this->clearStr($item);
                            break;

                        }
                    }
                }

            } else {

                // Обработка $_POST + перезапись данных в $_POST['alias'], а затем в alias_str.
                $alias_str = $_POST['alias'] = $this->clearStr($_POST['alias']);

            }

            $textModify = new TextModify();
            $alias = $textModify->translit($alias_str);

            $where['alias'] = $alias;
            $operand[] = '=';

            if ($id) {

                $where[$this->columns['id_row']] = $id;
                $operand[] = '<>';

            }

            $resultAlias = $this->model->read($this->table,[
                'fields' => 'alias',
                'where' => $where,
                'operand' => $operand,
                'limit' => '1'
            ]);

            if (!empty($resultAlias)) {
                $resultAlias = $resultAlias[0];
            } else {
                $resultAlias = null;
            }

            if (empty($resultAlias)) {

                $_POST['alias'] = $alias;

            } else {

                    $this->alias = $alias;
                    $_POST['alias'] = '';

            }

            if ($_POST['alias'] && $id) {

                method_exists($this, 'checkOldAlias') && $this->checkOldAlias($id);

            }

        }

    }

    protected function checkAlias($id): bool
    {

        if ($id) {

            if ($this->alias) {

                $this->alias .= '-' . $id;

                $this->model->update($this->table, [
                    'fields' => ['alias' => $this->alias],
                    'where' => [$this->columns['id_row'] => $id]
                ]);

                return true;

            }
        }

        return false;

    }

    /**
     * @throws RouteException
     */
    protected function createOrderData(string $table): array
    {

        $columns = $this->model->showColumns($table);

        if (!isset($columns))
            throw new RouteException('Отсутствуют поля в таблице: ' . $table);

        $name = '';
        $order_name = '';

        if (isset($columns['name'])) {
            $order_name = $name = 'name';
        } else {
            foreach ($columns as $key => $value) {

                if (str_contains($key, 'name')) {
                    $order_name = $key;
                    $name = $key . ' as name';
                }
            }

            if (!isset($name)) $name = $columns['id_row'] . ' as name';

        }

        $parent_id = '';

        $order = [];

        // Сортировка
        if (isset($columns['parent_id'])) $order[] = $parent_id = 'parent_id';

        if (isset($columns['menu_position'])) $order[] = 'menu_position';
        else $order[] = $order_name;

        return compact('name', 'parent_id', 'order', 'columns');

    }

    /**
     * @throws DbException
     * @throws RouteException
     */
    protected function createManyToMany($settings = false): void
    {

        if (!$settings) $settings = $this->settings ?: Settings::instance();

        $manyToMany = $settings::get('manyToMany');
        $blocks = $settings::get('blockNeedle');

        if ($manyToMany) {

            foreach ($manyToMany as $mTable => $tables) {

                // Поиск ключа
                $targetKey = array_search($this->table, $tables);

                if ($targetKey !== false) {

                    $otherKey = $targetKey ? 0 : 1;

                    $checkBoxList = $settings::get('templateArr')['checkboxlist'];

                    if (!$checkBoxList || !in_array($tables[$otherKey], $checkBoxList)) continue;

                    if (!isset($this->warningUser[$tables[$otherKey]])) {

                        if (isset($settings::get('projectTables')[$tables[$otherKey]])) {

                            $this->warningUser[$tables[$otherKey]] =
                                [$settings::get('projectTables')[$tables[$otherKey]]['name']];

                        }

                    }

                    $orderData = $this->createOrderData($tables[$otherKey]);

                    $insert = false;

                    if ($blocks) {

                        foreach ($blocks as $key => $item) {

                            if (in_array($tables[$otherKey], $item)) {

                                $this->blocks[$key][] = $tables[$otherKey];
                                $insert = true;
                                break;

                            }
                        }
                    }

                    if (!$insert) $this->blocks[array_keys($this->blocks)[0]][] = $tables[$otherKey];

                    $foreign = [];

                    // Для режима редактирования
                    if ($this->data) {

                        $result = $this->model->read($mTable, [
                            'fields' => [$tables[$otherKey] . '_' . $orderData['columns']['id_row']],
                            'where' => [$this->table . '_' . $this->columns['id_row']
                                    === $this->data[$this->columns['id_row']]],
                        ]);

                        if (isset($result)) {

                            foreach ($result as $item) {

                                $foreign[] = $item[$tables[$otherKey] . '_' . $orderData['columns']['id_row']];

                            }

                        }

                    }

                    // Если пришло поле type
                    if (isset($tables['type'])) {

                        $data = $this->model->read($tables[$otherKey], [
                            'fields' => [$orderData['columns']['id_row'] . ' as id',
                                $orderData['name'], $orderData['parent_id']],
                            'order' => $orderData['order'],
                        ]);

                        if ($data) {

                            $this->foreignData[$tables[$otherKey]][$tables[$otherKey]]['name'] = 'Выбрать';

                            foreach ($data as $item) {
                                // Child or Root
                                if ($tables['type'] === 'root' && $orderData['parent_id']) {
                                    // Если null - то это название группы, иначе объект
                                    if ($item[$orderData['parent_id']] === null) {

                                        $this->foreignData[$tables[$otherKey]][$tables[$otherKey]]['sub'][] = $item;

                                    }

                                } elseif ($tables['type'] === 'child' && $orderData['parent_id']) {

                                    if ($item[$orderData['parent_id']] !== null) {

                                        $this->foreignData[$tables[$otherKey]][$tables[$otherKey]]['sub'][] = $item;

                                    }

                                } else {

                                    $this->foreignData[$tables[$otherKey]][$tables[$otherKey]]['sub'][] = $item;

                                }

                                if (in_array($item['id'], $foreign)) {

                                    $this->data[$tables[$otherKey]][$tables[$otherKey]][] = $item['id'];

                                }

                            }

                        }

                    } elseif ($orderData['parent_id']) {

                        $parent = $tables[$otherKey];

                        $keys = $this->model->showForeignKeys($tables[$otherKey]);

                        if (isset($keys)) {

                            foreach ($keys as $item) {

                                if ($item['COLUMN_NAME'] === 'parent_id') {

                                    $parent = $item['REFERENCED_TABLE_NAME'];

                                    break;

                                }

                            }

                        }

                        if ($parent === $tables[$otherKey]) {

                            $data = $this->model->read($tables[$otherKey], [
                                'fields' => [$orderData['columns']['id_row'] . ' as id',
                                    $orderData['name'], $orderData['parent_id']],
                                'order' => $orderData['order'],
                            ]);

                            if (isset($data)) {

                                while (($key = key($data)) !== null) {

                                    if (!$data[$key]['parent_id']) {

                                        $this->foreignData[$tables[$otherKey]][$data[$key]['id']]['name']
                                            = $data[$key]['name'];

                                        unset($data[$key]);
                                        reset($data);
                                        continue;

                                    } else {

                                        if (isset($this->foreignData[$tables[$otherKey]]
                                            [$data[$key][$orderData['parent_id']]])) {

                                            $this->foreignData[$tables[$otherKey]][$data[$key]
                                            [$orderData['parent_id']]]['sub'][$data[$key]['id']] = $data[$key];

                                            if (in_array($data[$key]['id'], $foreign))
                                                $this->data[$tables[$otherKey]]
                                                [$data[$key][$orderData['parent_id']]][] = $data[$key]['id'];

                                            unset($data[$key]);
                                            reset($data);
                                            continue;

                                        } else {

                                            foreach ($this->foreignData[$tables[$otherKey]] as $id => $item) {

                                                $parent_id = $data[$key][$orderData['parent_id']];
                                                // Если есть ['sub']
                                                if (isset($item['sub'][$parent_id]) && $item['sub']) {

                                                    $this->foreignData[$tables[$otherKey]][$id]['sub'][$data[$key]['id']]
                                                        = $data[$key];

                                                    if (in_array($data[$key]['id'], $foreign))
                                                        $this->data[$tables[$otherKey]][$id][] = $data[$key]['id'];

                                                    unset($data[$key]);
                                                    reset($data);

                                                    continue 2;

                                                }

                                            }

                                        }

                                        next($data);

                                    }

                                }

                            }

                        } else {

                            $parentOrderData = $this->createOrderData($parent);

                            $data = $this->model->read($parent, [
                                'fields' => [$parentOrderData['name']],
                                'join' => [
                                    $tables[$otherKey] => [
                                        'fields' => [$orderData['columns']['id_row'] . ' as id', $orderData['name']],
                                        'on' => [$parentOrderData['columns']['id_row'], $orderData['parent_id']]
                                    ]
                                ],
                                'join_structure' => true
                            ]);

                            foreach ($data as $key => $item) {

                                if (isset($item['join'][$tables[$otherKey]]) && $item['join'][$tables[$otherKey]]) {

                                    $this->foreignData[$tables[$otherKey]][$key]['name'] = $item['name'];
                                    $this->foreignData[$tables[$otherKey]][$key]['sub'] = $item['join'][$tables[$otherKey]];

                                    foreach ($item['join'][$tables[$otherKey]] as $value) {

                                        if (in_array($value['id'], $foreign)) {

                                            $this->data[$tables[$otherKey]][$key][] = $value['id'];

                                        }
                                    }
                                }
                            }
                        }

                    } else {

                        $data = $this->model->read($tables[$otherKey], [
                            'fields' => [$orderData['columns']['id_row'] . ' as id', $orderData['name'], $orderData['parent_id']],
                            'order' => $orderData['order']
                        ]);

                        if ($data) {

                            $this->foreignData[$tables[$otherKey]][$tables[$otherKey]]['name'] = 'Выбрать';

                            foreach ($data as $item) {

                                $this->foreignData[$tables[$otherKey]][$tables[$otherKey]]['sub'][] = $item;

                                if (in_array($item['id'], $foreign)) {

                                    $this->data[$tables[$otherKey]][$tables[$otherKey]][] = $item['id'];

                                }

                            }

                        }

                    }

                }

            }

        }

    }

    /**
     * @throws DbException
     */
    protected function checkManyToMany($settings = false): void
    {

        if (!$settings) $settings = $this->settings ?: Settings::instance();

        $manyToMany = $settings::get('manyToMany');

        if ($manyToMany) {

            foreach ($manyToMany as $mTable => $tables) {

                $targetKey = array_search($this->table, $tables);

                if ($targetKey !== false) {

                    $otherKey = $targetKey ? 0 : 1;

                    $checkBoxList = $settings::get('templateArr')['checkboxlist'];

                    if (!$checkBoxList || !in_array($tables[$otherKey], $checkBoxList)) continue;

                    $columns = $this->model->showColumns($tables[$otherKey]);

                    $targetRow = $this->table . '_' . $this->columns['id_row'];

                    $otherRow = $tables[$otherKey] . '_' . $columns['id_row'];

                    $this->model->delete($mTable, [
                        'where' => [$targetRow => $_POST[$this->columns['id_row']]]
                    ]);

                    if (isset($_POST[$tables[$otherKey]])) {

                        $insertArr = [];
                        $i = 0;

                        foreach ($_POST[$tables[$otherKey]] as $value) {

                            foreach ($value as $item) {

                                if ($item) {

                                    $insertArr[$i][$targetRow] = $_POST[$this->columns['id_row']];
                                    $insertArr[$i][$otherRow] = $item;

                                    $i++;

                                }

                            }

                        }

                        if (isset($insertArr)) {

                            $this->model->create($mTable, [
                                'fields' => $insertArr
                            ]);

                        }

                    }

                }

            }

        }

    }

}