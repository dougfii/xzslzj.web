<?php
define('BASE_SYS_PATH', '../../');

define('BASE_TMP_PATH', '../../tmp/');
define('BASE_LIB_PATH', '../../lib/');
define('BASE_DAT_PATH', '../../lib/data/');
define('BASE_TPL_PATH', '../../lib/erp/tpl/');

define('BASE_IMG_PATH', '/img/');

define('BASE_JS_PATH', '../../web/app/js/');
define('BASE_UPLOAD_BANNER', '../../web/erp/upload/banner/');
define('BASE_UPLOAD_BRAND', '../../web/erp/upload/brand/');
define('BASE_UPLOAD_VENDOR', '../../web/erp/upload/vendor/');
define('BASE_UPLOAD_HR', '../../web/erp/upload/hr/');
define('BASE_UPLOAD_HRFILE', '../../web/erp/upload/hrfile/');

define('PATH_UPLOAD_BANNER', '/upload/banner/'); // 广告
define('PATH_UPLOAD_BRAND', '/upload/brand/'); // 品牌
define('PATH_UPLOAD_VENDOR', '/upload/vendor/'); // 供应商
define('PATH_UPLOAD_TEMP', '/upload/temp/'); // 临时

define('PRODUCT_PAGE', '?m=Product&a=ProductView&id=');

define('WEBSITE_DOMAIN', 'http://www.xzslzj.cn/');

// 分页
define('PAGE_SIZE_DEFAULT', 50);
define('PAGE_SIZE_MEDIUM', 20);
define('PAGE_SIZE_SMALL', 15);
define('PAGE_SIZE_MINI', 10);
define('PAGE_SIZE_POP', 14);

// 消息
define('ALERT_ERROR', '系统处理错误');

//年度开始
define('YEAR_START', 2015);

//
define('APP_TITLE', '徐州水利工程质量管理站');
define('COMPANY_NAME', '徐州市水利工程质量监督站');

require_once BASE_LIB_PATH . 'erp/Loader.php';
ob_start();

try {
    Config::Instance()->Init(BASE_SYS_PATH . '/conf/erp.ini.php');

    $d = Dispatcher::Instance();
    $d->Dispatch();
} catch (Exception $e) {
    echo $e->getMessage();
}
?>