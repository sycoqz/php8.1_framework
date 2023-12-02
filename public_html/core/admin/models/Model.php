<?php

namespace core\admin\models;

use core\base\controllers\Singleton;
use core\base\exceptions\DbException;
use core\base\models\BaseModel;
use JetBrains\PhpStorm\NoReturn;

class Model extends BaseModel
{

    use Singleton;

    /**
     * @throws DbException
     */
    #[NoReturn] public function showForeignKeys(string $table, bool $key = false): bool|array|int|string|null
    {

        $db = DB_NAME;
        $where = '';

        if ($key) $where = "AND COLUMN_NAME' = '$key' LIMIT 1";

        $query = "SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE
                      WHERE TABLE_SCHEMA = '$db' AND TABLE_NAME = '$table' AND
                        CONSTRAINT_NAME <> 'PRIMARY' AND REFERENCED_TABLE_NAME is not null $where";

        return $this->query($query);

    }

}