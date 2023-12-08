<?php

const VG_ACCESS = true;

header('Content-Type:text/html;charset=utf-8');
session_start();

require_once  'config.php';
require_once 'core/base/settings/internal_settings.php';
require_once 'libraries/functions.php';

use core\base\controllers\BaseRoute;
use core\base\exceptions\RouteException;
use core\base\exceptions\DbException;

try{
    BaseRoute::routeDirection();
}
catch (RouteException|DbException $e) {
    exit($e->getMessage());
}
