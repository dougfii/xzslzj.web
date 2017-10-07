<?php

class MainMod extends BaseMod
{

    public function index()
    {
        $rs = MsgCls::GetUserUnread();

        $this->Header();

        $view = View::Factory('Main');

        $view->rs = $rs;

        echo $view->Render();

        $this->Footer();
    }

    public function Password()
    {
        $this->Header();

        $view = View::factory('Password');
        echo $view->render();

        $this->Footer();
    }

    public function OnPassword()
    {
        $opass = $this->Req('opass', '', 'str');
        $npass = $this->Req('npass', '', 'str');
        $rpass = $this->Req('rpass', '', 'str');

        if (!Util::isPassword($opass)) return Json::ReturnError('原始密码错误');
        if (!Util::isPassword($npass)) return Json::ReturnError('新设密码错误');
        if ($npass != $rpass) return Json::ReturnError('重复密码应与新设密码相同');

        try {
            UserCls::EditPassword($this->Uid(), $npass);
        } catch (Exception $e) {
            return Json::ReturnError(ALERT_ERROR);
        }

        return Json::ReturnSuccess();
    }

    public function OnRead()
    {
        $id = $this->Req('id', 0, 'int');

        if ($id <= 0) return Json::ReturnError(ALERT_ERROR);

        try {
            MsgCls::SetRead($id);
        } catch (Exception $e) {
            return Json::ReturnError($e->getMessage());
        }

        return Json::ReturnSuccess();
    }
}

?>