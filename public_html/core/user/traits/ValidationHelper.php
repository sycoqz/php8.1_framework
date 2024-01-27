<?php

namespace core\user\traits;

use JetBrains\PhpStorm\NoReturn;

trait ValidationHelper
{

    protected function emptyField(string $value, string $answer): array|string
    {

        $value = $this->clearStr($value);

        if (empty($value)) {

            $this->sendError('Не  заполнено поле ' . $answer);

        }

        return $value;

    }

    protected function numericField(string $value, string $answer): array|string|null
    {
        // Подготовка номерованного поля
        $value = preg_replace('/\D/', '', $value);

        !$value && $this->sendError('Некорректное поле ' . $answer);

        return $value;

    }

    protected function phoneField(string $value, string $answer = null): array|string|null
    {
        // Подготовка поля с номером телефона
        $value = preg_replace('/\D/', '', $value);

        if (strlen($value) === 11) {

            $value = preg_replace('/^8/', '7', $value);

        }

        return $value;

    }

    protected function emailField(string $value, string $answer): array|string
    {
        // Подготовка поля с электронной почтой
        $value = $this->clearStr($value);

        if (!preg_match('/^[\w\-.]+@[\w\-]+\.[\w\-]+/', $value)) {

            $this->sendError('Некорректный формат поля ' . $answer);

        }

        return $value;

    }

    #[NoReturn] protected function sendError(string $text, string $class = 'error'): void
    {

        $_SESSION['result']['answer'] = '<div class="' . $class . '">' . $text . '</div>';

        if ($class === 'error') {

            $this->addSessionData();

        }

        $this->redirect();

    }

    protected function sendSuccess(string $text, string $class = 'success'): void
    {

        $this->sendError($text, $class);

    }

}