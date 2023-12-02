<?php

namespace core\base\exceptions;

use core\base\controllers\BaseMethods;
use Exception;

class DbException extends Exception
{

    use BaseMethods;
    protected $messages;

    public function __construct(string $message = "", int $code = 0)
    {
        parent::__construct($message, $code);

        $this->messages = include 'messages.php';

        $error = $this->getMessage() ?? $this->messages[$this->getCode()];

        $error .= "\r\n" . 'file ' . $this->getFile() . "\r\n" . 'In line ' . $this->getLine() . "\r\n";

        //if (($this->messages[$this->getCode()])) $this->message = $this->messages[$this->getCode()];

        $this->writeLog($error, 'db_log.txt');

    }
}