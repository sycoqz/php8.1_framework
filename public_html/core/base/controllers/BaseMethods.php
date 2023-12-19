<?php

namespace core\base\controllers;

use DateTime;
use JetBrains\PhpStorm\NoReturn;

trait BaseMethods
{

    protected function clearStr($str): array|string // Очищает строку от HTML тегов.
    {
        if (is_array($str)) {
            foreach ($str as $key => $item) $str[$key] = trim(strip_tags($item));
            return $str;
        } else {
            return trim(strip_tags($str));
        }
    }

    protected function clearNum($num): float|int // Возвращает число при вводе str или float.
    {
        return (!empty($num) && preg_match('/\d/', $num)) ?
            preg_replace('/[^\d.]/', '', $num) * 1 : 0;
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    protected function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    #[NoReturn] protected function redirect($http = false, $code = false): void
    {
        if ($code) {
            $codes = ['301' => 'HTTP/1.1 301 Move Permanently'];

            if ($codes[$code]) header($codes[$code]);
        }

        if ($http) $redirect = $http;
            else $redirect = $_SERVER['HTTP_REFERER'] ?? PATH;

            header("Location: $redirect");

            exit;
    }

    protected function getStyles(): void
    {

        if ($this->styles) {

            foreach ($this->styles as $style) echo '<link rel="stylesheet" href="' . $style . '">';

        }

    }

    protected function getScripts(): void
    {

        if ($this->scripts) {

            foreach ($this->scripts as $script) echo '<script src="' . $script . '"></script>';

        }

    }

    protected function writeLog(string $message, $file = 'log.txt', $event = 'Fault'): void
    {
        $dateTime = new DateTime();

        $str = $event . ': ' . $dateTime->format('d-m-Y G-i-s') . ' - ' . $message . "\r\n";

        file_put_contents('log/' . $file, $str, FILE_APPEND);
    }

}