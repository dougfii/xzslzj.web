<?php

class UploadMod extends BaseMod
{
    protected static $LIMIT_FORMAT_ALL = array('.gif', '.jpg', '.jpeg', '.png', '.bmp', '.txt', '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx', '.pdf', '.rar', '.zip', '.7z', '.avi', '.mpg', '.mpeg', '.3gs', '.rm', '.fla', '.swf');
    protected static $LIMIT_FORMAT_IMAGE = array('.gif', '.jpg', '.jpeg', '.png', '.bmp');
    protected static $LIMIT_FORMAT_DOCUMENT = array('.txt', '.doc', '.docx', '.xls', '.xlsx');
    protected static $MAX_SIZE = 20971520; // 20M

    //
    public function index()
    {
    }

    public function UpFlowFixed()
    {
        $no = $this->Req('no', 0, 'int');

        $this->settings();

        //上传配置
        $config = array(
            "savePath" => "../../web/erp/upload/flow/",             //存储文件夹
            "urlPath" => "/upload/flow/",//返回URL根路径
            "maxSize" => 50000,                   //允许的文件最大尺寸，单位KB
            "allowFiles" => self::$LIMIT_FORMAT_ALL //允许的文件格式
        );

        $up = new Uploader2("upfile{$no}", $config);

        $info = $up->getFileInfo();

        echo json_encode($info);
    }

    public function UpFlowDynamic()
    {
        $this->settings();

        //上传配置
        $config = array(
            "savePath" => "../../web/erp/upload/flow/",             //存储文件夹
            "urlPath" => "/upload/flow/",//返回URL根路径
            "maxSize" => 50000,                   //允许的文件最大尺寸，单位KB
            "allowFiles" => self::$LIMIT_FORMAT_ALL //允许的文件格式
        );

        $up = new Uploader2("upfile", $config);

        $info = $up->getFileInfo();

        echo json_encode($info);
    }

    public function ArticlePic()
    {
        $this->settings();

        //上传配置
        $config = array(
            "savePath" => "../../web/erp/upload/article/",             //存储文件夹
            "urlPath" => "/upload/article/",//返回URL根路径
            "maxSize" => 20000,                   //允许的文件最大尺寸，单位KB
            "allowFiles" => array(".gif", ".png", ".jpg", ".jpeg", ".bmp")  //允许的文件格式
        );

        $up = new Uploader2("upfile", $config);

        $info = $up->getFileInfo();

        echo json_encode($info);
    }

    public function AdvertiserPic()
    {
        $this->settings();

        //上传配置
        $config = array(
            "savePath" => "../../web/erp/upload/advertiser/",             //存储文件夹
            "urlPath" => "/upload/advertiser/",//返回URL根路径
            "maxSize" => 20000,                   //允许的文件最大尺寸，单位KB
            "allowFiles" => array(".gif", ".png", ".jpg", ".jpeg", ".bmp")  //允许的文件格式
        );

        $up = new Uploader2("upfile", $config);

        $info = $up->getFileInfo();

        echo json_encode($info);
    }

    public function LinkerPic()
    {
        $this->settings();

        //上传配置
        $config = array(
            "savePath" => "../../web/erp/upload/linker/",             //存储文件夹
            "urlPath" => "/upload/linker/",//返回URL根路径
            "maxSize" => 20000,                   //允许的文件最大尺寸，单位KB
            "allowFiles" => array(".gif", ".png", ".jpg", ".jpeg", ".bmp")  //允许的文件格式
        );

        $up = new Uploader2("upfile", $config);

        $info = $up->getFileInfo();

        echo json_encode($info);
    }

    private function settings()
    {
        header("Content-Type:text/html;charset=utf-8");
        error_reporting(E_ERROR | E_WARNING);
        date_default_timezone_set("Asia/chongqing");
    }
}

?>