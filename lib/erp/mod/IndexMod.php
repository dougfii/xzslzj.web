<?php

class IndexMod implements IMod
{

    public function index()
    {
        if (!isset ($_SESSION ['admintime'])) {
            $_SESSION ['admintime'] = time();
            $_SESSION ['admincome'] = 8;
        }

        $view = View::Factory('Index');
        echo $view->Render();
    }

    public function Login()
    {
        $username = Url::Req('username', '', 'str');
        $password = Url::Req('password', '', 'str');

        if (!isset ($_SESSION ['admincome']) || !$_SESSION ['admintime']) Json::ReturnError(ALERT_ERROR);
        if ($_SESSION ['admincome'] < 0 && time() - $_SESSION ['admintime'] < 3600) Json::ReturnError('登录次数过多,账号暂时被禁用');
        if (empty($username) || !Util::IsPassword($password)) {
            $_SESSION ['admincome'] -= 1;
            $_SESSION ['admintime'] = time();
            Json::ReturnError('账号或密码错误');
        }

        $rs = UserBiz::Login($username, $password);

        if ($rs['code'] != Data::SUCCESS)
            Json::ReturnError($rs['msg']);

        $_SESSION ['uid'] = $rs['data']['id'];
        $_SESSION ['name'] = $rs['data']['name'];

        Json::ReturnSuccess('?m=Main');
    }

    public function Logout()
    {
        session_unset();
        Url::RedirectUrl('/');
    }
}

?>