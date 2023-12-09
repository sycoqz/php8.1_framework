<?php

defined('VG_ACCESS') or die('Access denied');

const TEMPLATE = 'templates/default/';
const ADMIN_TEMPLATE = 'core/admin/views/';
const UPLOAD_DIR = 'userfiles/';

const COOKIE_VERSION = '1.0.0';
const CRYPT_KEY = 'zwwGMi2ARnb7FYap+nqvaLYpbSVahvoRRj+oKltu/AMv0Jpp9I7Eyg/P5mJxof0dgDB48SkzMmdQJTZ3WhmezX2MNShfAi3Zly9OwtOr7C5JUS+j+k9FvsPCIH5XGqk6FkAn+5Mm1HGHXVpzlQrR9zZ4iwv1DPa85wTIc3kFpDdsWdHCEnDaxmHLbmsYLAR6';
const COOKIE_TIME = 60;
const BLOCK_TIME = 3;

const QTY = 8;
const QTY_LINKS = 3;

const ADMIN_CSS_JS = [
    'styles' => ['css/main.css'],
    'scripts' => ['js/frameworkfunctions.js', 'js/scripts.js']
];

const USER_CSS_JS = [
    'styles' => [],
    'scripts' => []
];

use core\base\exceptions\RouteException;

function autoloadMainClasses($class_name): void
{

    $class_name = str_replace('\\', '/', $class_name);

    if (!@include_once $class_name . '.php') {
        new RouteException('Неверное имя файла для подключения - '.$class_name);
    }
}

spl_autoload_register('autoloadMainClasses');