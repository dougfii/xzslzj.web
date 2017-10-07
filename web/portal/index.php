<?php
define('BASE_SYS_PATH', '../../');
define('BASE_LIB_PATH', '../../lib/');
define('BASE_TPL_PATH', '../../lib/portal/tpl/');

define('ALERT_ERROR', '系统处理错误');

define('PAGE_SIZE_MEDIUM', 20);

define('APP_NAME', '徐州水利工程质量监督管理站');
define('APP_SHORTNAME', '水利工程质监站');
define('COMPANY_NAME', '徐州市水利工程质量监督站');

ob_start();

require_once(BASE_LIB_PATH . 'portal/Loader.php');

try {
    Config::Instance()->Init(BASE_SYS_PATH . 'conf/portal.ini.php');
    Dispatcher::Instance()->Dispatch();
} catch (Exception $e) {
    echo ALERT_ERROR;
}