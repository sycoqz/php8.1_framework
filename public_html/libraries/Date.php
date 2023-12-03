<?php

namespace libraries;

require_once 'functions.php';

use Exception;

class Date
{
    protected string $date;
    protected int $year;
    protected int $month;
    protected int $day;

    public function __construct(string $date)
    {
        $this->date = $date;

        $this->year = (int)substr($date, 0, -6);
        $this->month = (int)(substr($date, 5, -3));
        $this->day = (int)substr($date, 8);
    }

    public function getDay(): int
    {
        return $this->day;
    }

    public function getMonth(string $lang = ''): string|int
    {
        if ($lang === 'ru') {

           return match ($this->month) {
               1 => 'Январь',
               2 => 'Февраль',
               3 => 'Март',
               4 => 'Апрель',
               5 => 'Май',
               6 => 'Июнь',
               7 => 'Июль',
               8 => 'Август',
               9 => 'Сентябрь',
               10 => 'Октябрь',
               11 => 'Ноябрь',
               12 => 'Декабрь',
            };

        } elseif ($lang === 'en') {

            return match ($this->month) {
                1 => 'January',
                2 => 'February',
                3 => 'March',
                4 => 'April',
                5 => 'May',
                6 => 'June',
                7 => 'July',
                8 => 'August',
                9 => 'September',
                10 => 'October',
                11 => 'November',
                12 => 'December',
            };

        } else {

            return $this->month;

        }
    }

    public function getYear(): string
    {
        return $this->year;
    }

    /**
     * @throws Exception
     */
    public function getWeekDay(string $lang = ''): int|string
    {

        $weekday = (int)date('N', mktime(0,0,0,$this->getMonth(),$this->getDay(),$this->getYear()));

        if ($lang === 'ru') {

            return match ($weekday) {

                1 => 'Понедельник',
                2 => 'Вторник',
                3 => 'Среда',
                4 => 'Четверг',
                5 => 'Пятница',
                6 => 'Суббота',
                7 => 'Воскресенье',

            };

        } elseif ($lang === 'en') {

            return match($weekday) {

                1 => 'Monday',
                2 => 'Tuesday',
                3 => 'Wednesday',
                4 => 'Thursday',
                5 => 'Friday',
                6 => 'Saturday',
                7 => 'Sunday',

            };

        } else {

            return $weekday;

        }

    }

    public function addDay($value): static
    {
        $this->day += $value;
        return $this;
    }

    public function subDay($value): static
    {
        $this->day -= $value;
        return $this;
    }

    public function addMonth($value): static
    {
        $this->month += $value;
        return $this;
    }

    public function subMonth($value): static
    {
        $this->month += $value;
        return $this;
    }

    public function addYear($value): static
    {
        $this->year += $value;
        return $this;
    }

    public function subYear($value): static
    {
        $this->year += $value;
        return $this;
    }

    public function format(string $format) : string
    {

        $arr = explode('-', $format);

        $items = 0;
        foreach ($arr as $ignored) {
            $items++;
        }

        if (!($items <= 1)) {

            $i = 0;
            foreach ($arr as $value) {

                $symbol = match ($value) {
                    'год', 'year' => 'Y',
                    'месяц', 'month' => 'm',
                    'день', 'day' => 'd',

                };

                $arr[$i] = $symbol;
                $i++;

            }

            return implode('-', $arr);

        } else {

            return $arr[0];

        }

    }

    public function __toString(): string
    {
        // вывод даты 'год-месяц-день'
        return date($this->format('год-месяц-день'), mktime(0,0,0,
            $this->getMonth(),$this->getDay(),$this->getYear()));
    }

}
