<?php

class ProjectMod extends BaseMod
{
    public function index()
    {
        if (!isset ($_SESSION ['mtimes'])) {
            $_SESSION ['mtimes'] = time();
            $_SESSION ['mcomes'] = 8;
        }

        $view = View::Factory('ProjectLogin');

        $view->gid = HTML::CtlSelKVList(GroupBiz::Items()['data'], 'gid', '');

        echo $view->Render();
    }

    public function Login()
    {
        $name = $this->Req('name', '', 'str');
        $pass = $this->Req('pass', '', 'str');

        if (!isset ($_SESSION ['mcomes']) || !$_SESSION ['mtimes']) Json::ReturnError(ALERT_ERROR);
        if ($_SESSION ['mcomes'] < 0 && time() - $_SESSION ['mtimes'] < 3600) Json::ReturnError('登录次数过多,账号暂时被禁用');
        if (empty($name) || !Util::IsPassword($pass)) {
            $_SESSION ['mcomes'] -= 1;
            $_SESSION ['mtimes'] = time();
            Json::ReturnError('工程名称或登录密码错误');
        }

        $rs = ProjectCls::Login($name, $pass);
        if (empty($rs)) Json::ReturnError('工程名称或登录密码错误');

        LogLoginCls::Add(1, $rs['id'], Inet::GetIP());
        ProjectCls::SetLast($rs['id']);

        $_SESSION ['mid'] = $rs['id'];
        $_SESSION ['mname'] = $rs['name'];

        Json::ReturnSuccess('?m=Project&a=Main');
    }

    public function Join()
    {
        $gid = $this->Req('gid', 0, 'int');
        $name = $this->Req('name', '', 'str');
        $company = $this->Req('company', '', 'str');
        $pass = $this->Req('pass', '', 'str');
        $repass = $this->Req('repass', '', 'str');
        $contacts = $this->Req('contacts', '', 'str');
        $mobile = $this->Req('mobile', '', 'str');
        $email = $this->Req('email', '', 'str');

        if ($gid <= 0) Json::ReturnError('请选择所属区域');
        if (empty($name)) Json::ReturnError('请输入工程名称');
        if (!Util::IsMaxLen($name, 200)) Json::ReturnError('工程名称过长');
        if (empty($company)) Json::ReturnError('请输入申请单位');
        if (!Util::IsMaxLen($company, 200)) Json::ReturnError('申请单位过长');
        if (!Util::IsPassword($pass)) Json::ReturnError('请设置有效的登录密码');
        if ($pass != $repass) Json::ReturnError('登录密码与重复密码不一致');
        if (empty($contacts)) Json::ReturnError('请输入联系人');
        if (!Util::IsMaxLen($contacts, 50)) Json::ReturnError('联系人过长');
        if (!Util::IsMobile($mobile) && !Util::IsPhone($mobile)) Json::ReturnError('请输入正确的联系人手机或电话号码');
        if (!empty($email) && !Util::IsEmail($email)) Json::ReturnError('请输入正确的联系人电子邮箱');

        if (ProjectCls::ExistName($name)) Json::ReturnError('工程名称已经存在');

        $id = ProjectCls::Add($gid, $name, $company, $pass, $contacts, $mobile, $email);
        ProjectCls::SetNode($id, ProjectNodeCls::INIT, 0, ProjectStateCls::BEGIN);

        $_SESSION ['mid'] = $id;
        $_SESSION ['mname'] = $name;

        try {
            MsgCls::Add(1, MsgDirectCls::FROM_PROJECT, $id, 1, $name, '管理员', ProjectNodeCls::INIT, $id, '新注册');
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        Json::ReturnSuccess('?m=Project&a=Main');
    }

    public function Logout()
    {
        session_unset();
        Url::RedirectUrl('/');
    }

    public function Password()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::factory('Password');
        echo $view->render();

        $this->MemberFooter();
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
            ProjectCls::EditPassword($this->Mid(), $npass);
        } catch (Exception $e) {
            return Json::ReturnError(ALERT_ERROR);
        }

        return Json::ReturnSuccess();
    }

    public function Main()
    {
        $rs = MsgCls::GetProjectUnread($this->Mid());

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('Main');

        $view->rs = $rs;

        echo $view->Render();

        $this->MemberFooter();
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

    public function Progress()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('Progress');

        $view->rs = ProjectCls::Instance()->Item($this->Mid());

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow0List()
    {
        $this->ProjectFlow0();
    }

    public function ProjectFlow0()
    {
        $this->MemberAuth();

        $this->MemberHeader();


        $view = View::Factory('ProjectFlow0');

        $view->rs = ProjectCls::Instance()->Item($this->Mid());

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow1List()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow1List');

        $pid = $this->Mid();

        $new = true;
        $rr = array();
        $rl = Flow1Cls::GetLastItem($pid);
        if (!empty($rl) && count($rl) > 0) {
            $new = ProjectStateCls::IsNew(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::APPLY));
            if ($rl['replyid'] > 0) $rr = Reply1Cls::GetLastItem($pid, $rl['replyid']);
        }
        $rs = Flow1Cls::GetApprovedItems($pid);

        $view->rl = $rl;
        $view->rr = $rr;
        $view->rs = $rs;
        $view->new = $new;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::APPLY);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow1()
    {
        $id = $this->Req('id', 0, 'int');

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow1');

        $pid = $this->Mid();
        $gc = ProjectCls::GetGroupCompany($pid);

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow1Cls::Instance()->Item($id);
        else $rs = Flow1Cls::GetLastItem($pid);

        $no = '';
        $signer = '';
        $content = '';
        $date = '';
        $keywords = '';

        $edit = true;

        if (!empty($rs) && count($rs) > 0) {

            $no = $rs['no'];
            $signer = $rs['signer'];
            $content = $rs['content'];
            $date = $rs['date'];
            $keywords = $rs['keywords'];

            $edit = ProjectStateCls::IsEdit(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::APPLY));
        }

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->edit = $edit;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::APPLY);

        $view->rs = $rs;
        $view->no = $no;
        $view->signer = $signer;
        $view->content = $content;
        $view->date = $date;
        $view->keywords = $keywords;

        $view->pid = $pid;
        $view->atts = Atts::UploadFixed(Atts::$flow1, AttachmentCls::GetFixedItems($pid, 1), $edit);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow1Print()
    {
        $id = $this->Req('id', 0, 'int');
        $pid = Flow1Cls::Instance()->Pid($id);

        $this->HeadPrint();

        $view = View::Factory('ProjectFlow1Print');

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Flow1Cls::Instance()->Item($id);

        $view->rs = $rs;

        $view->name = $name;
        $view->company = $company;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::APPLY);

        $view->approve = false;

        $view->atts = Atts::UploadFixed(Atts::$flow1, AttachmentCls::GetFixedItems($pid, 1), false);

        echo $view->Render();

        $this->FootPrint();
    }

    public function OnProjectFlow1()
    {
        $no = $this->Req('no', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $keywords = $this->Req('keywords', '', 'str');
        $attachments = $this->Req('attachments', '', 'str');

        $pid = $this->Mid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($no)) Json::ReturnError('请输入文件编号');
        if (empty($signer)) Json::ReturnError('请输入签发单位');
        if (empty($content)) Json::ReturnError('请输入申报内容');
        if (empty($date)) Json::ReturnError('请输入申报日期');

        if (empty($keywords)) $keywords = '无';

        $id = Flow1Cls::Add($pid, $no, $signer, $content, $date, $keywords, $attachments);
        ProjectCls::SetNode($pid, ProjectNodeCls::APPLY, $id, ProjectStateCls::APPROVE);

        try {
            MsgCls::Add(1, MsgDirectCls::FROM_PROJECT, $this->Mid(), 1, ProjectCls::Instance()->Name($pid), '管理员', ProjectNodeCls::APPLY, $id, '新建' . ProjectNodeCls::Name(ProjectNodeCls::APPLY));
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        Json::ReturnSuccess();
    }

    //TODO:作废
    public function Flow1Reply1()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('Flow1Reply1');

        $mid = $this->Mid();
        $gc = MemberBiz::GetGroupCompany($mid);
        $nodeid = ProjectNodeCls::APPLY;

        $name = MemberBiz::Name($mid);
        $company = MemberBiz::Company($mid);

        list($cf, $flow) = FlowBiz::GetFlowItem($mid, $nodeid);
        $fid = $flow['id'];
        list($cr, $reply) = ReplyBiz::GetReplyItem($mid, $fid, $nodeid);

        $view->rs = $reply;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectReply1View()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow1Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Reply1Cls::GetLastItem($pid, $fid);

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectReply1View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow2List()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow2List');

        $pid = $this->Mid();

        $new = true;
        $rr = array();
        $rl = Flow2Cls::GetLastItem($pid);
        if (!empty($rl) && count($rl) > 0) {
            $new = ProjectStateCls::IsNew(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::DIVIDE));
            if ($rl['replyid'] > 0) $rr = Reply1Cls::GetLastItem($pid, $rl['replyid']);
        }
        $rs = Flow2Cls::GetApprovedItems($pid);

        $view->rl = $rl;
        $view->rr = $rr;
        $view->rs = $rs;
        $view->new = $new;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::DIVIDE);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow2()
    {
        $id = $this->Req('id', 0, 'int');

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow2');

        $pid = $this->Mid();
        $gc = ProjectCls::GetGroupCompany($pid);

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow2Cls::Instance()->Item($id);
        else $rs = Flow2Cls::GetLastItem($pid);

        $no = '';
        $signer = '';
        $content = '';
        $date = '';
        $keywords = '';

        $edit = true;

        if (!empty($rs) && count($rs) > 0) {

            $no = $rs['no'];
            $signer = $rs['signer'];
            $content = $rs['content'];
            $date = $rs['date'];
            $keywords = $rs['keywords'];

            $edit = ProjectStateCls::IsEdit(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::DIVIDE));
        }

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->edit = $edit;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::DIVIDE);

        $view->no = $no;
        $view->signer = $signer;
        $view->content = $content;
        $view->date = $date;
        $view->keywords = $keywords;

        $view->pid = $pid;
        $view->atts = Atts::UploadFixed(Atts::$flow2, AttachmentCls::GetFixedItems($pid, 2), $edit);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function OnProjectFlow2()
    {
        $no = $this->Req('no', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $keywords = $this->Req('keywords', '', 'str');
        $attachments = $this->Req('attachments', '', 'str');

        $pid = $this->Mid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($no)) Json::ReturnError('请输入文件编号');
        if (empty($signer)) Json::ReturnError('请输入签发单位');
        if (empty($content)) Json::ReturnError('请输入申报内容');
        if (empty($date)) Json::ReturnError('请输入申报日期');

        $id = Flow2Cls::Add($pid, $no, $signer, $content, $date, $keywords, $attachments);
        ProjectCls::SetNode($pid, ProjectNodeCls::DIVIDE, $id, ProjectStateCls::APPROVE);

        try {
            MsgCls::Add(1, MsgDirectCls::FROM_PROJECT, $this->Mid(), 1, ProjectCls::Instance()->Name($pid), '管理员', ProjectNodeCls::DIVIDE, $id, '新建' . ProjectNodeCls::Name(ProjectNodeCls::DIVIDE));
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        Json::ReturnSuccess();
    }

    //TODO: 作废
    public function Flow2Reply1()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('Flow2Reply1');

        $mid = $this->Mid();
        $gc = MemberBiz::GetGroupCompany($mid);
        $nodeid = ProjectNodeCls::DIVIDE;

        $name = MemberBiz::Name($mid);
        $company = MemberBiz::Company($mid);

        list($cf, $flow) = FlowBiz::GetFlowItem($mid, $nodeid);
        $fid = $flow['id'];
        list($cr, $reply) = ReplyBiz::GetReplyItem($mid, $fid, $nodeid);

        $view->rs = $reply;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectReply2View()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow2Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Reply2Cls::GetLastItem($pid, $fid);

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectReply2View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow3List()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow3List');

        $pid = $this->Mid();

        $new = true;
        $rr = array();
        $rl = Flow3Cls::GetLastItem($pid);
        if (!empty($rl) && count($rl) > 0) {
            $new = ProjectStateCls::IsNew(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::CONFIRM));
            if ($rl['replyid'] > 0) $rr = Reply1Cls::GetLastItem($pid, $rl['replyid']);
        }
        $rs = Flow3Cls::GetApprovedItems($pid);

        $view->rl = $rl;
        $view->rr = $rr;
        $view->rs = $rs;
        $view->new = $new;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::CONFIRM);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow3()
    {
        $id = $this->Req('id', 0, 'int');

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow3');

        $pid = $this->Mid();
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow3Cls::Instance()->Item($id);
        else $rs = Flow3Cls::GetLastItem($pid);

        $no = '';
        $signer = '';
        $content = '';
        $date = '';

        $comp = '';
        $date_ping = '';

        $datas = array();

        $m11 = '';
        $m12 = '';
        $m13 = '';
        $m21 = '';
        $m22 = '';
        $m23 = '';
        $m31 = '';
        $m32 = '';
        $m33 = '';
        $m41 = '';
        $m42 = '';
        $m43 = '';
        $m51 = '';
        $m52 = '';
        $m53 = '';
        $m61 = '';
        $m62 = '';
        $m63 = '';

        $edit = true;

        if (!empty($rs) && count($rs) > 0) {
            $name = $rs['name'];
            $no = $rs['no'];
            $signer = $rs['signer'];
            $content = $rs['content'];
            $date = $rs['date'];

            $comp = $rs['comp'];
            $date_ping = $rs['date_ping'];

            $datas = Json::Decode($rs['items']);

            $m11 = $rs['m11'];
            $m12 = $rs['m12'];
            $m13 = $rs['m13'];
            $m21 = $rs['m21'];
            $m22 = $rs['m22'];
            $m23 = $rs['m23'];
            $m31 = $rs['m31'];
            $m32 = $rs['m32'];
            $m33 = $rs['m33'];
            $m41 = $rs['m41'];
            $m42 = $rs['m42'];
            $m43 = $rs['m43'];
            $m51 = $rs['m51'];
            $m52 = $rs['m52'];
            $m53 = $rs['m53'];
            $m61 = $rs['m61'];
            $m62 = $rs['m62'];
            $m63 = $rs['m63'];

            $edit = ProjectStateCls::IsEdit(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::CONFIRM));
        }

        $table = isset($datas['table']) ? $datas['table'] : array();
        $items = isset($datas['items']) ? $datas['items'] : array();
        $totals = isset($datas['totals']) ? $datas['totals'] : array();
        $amounts = isset($datas['amounts']) ? $datas['amounts'] : array();

        $data = array();
        $maxcols = 0;
        if (!empty($table)) {
            list($data, $maxcols) = $table;
            $_SESSION['facade_ds'] = $table;
        }

        $tables = $this->FacadeTableOk($data, $maxcols, $items, $totals, $amounts, $edit);
        $_SESSION['facade_table'] = $tables;

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->edit = $edit;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::CONFIRM);

        $view->no = $no;
        $view->signer = $signer;
        $view->content = $content;
        $view->date = $date;

        $view->comp = $comp;
        $view->date_ping = $date_ping;

        $view->tables = $tables;
        $view->cols = $maxcols;

        $view->m11 = $m11;
        $view->m12 = $m12;
        $view->m13 = $m13;
        $view->m21 = $m21;
        $view->m22 = $m22;
        $view->m23 = $m23;
        $view->m31 = $m31;
        $view->m32 = $m32;
        $view->m33 = $m33;
        $view->m41 = $m41;
        $view->m42 = $m42;
        $view->m43 = $m43;
        $view->m51 = $m51;
        $view->m52 = $m52;
        $view->m53 = $m53;
        $view->m61 = $m61;
        $view->m62 = $m62;
        $view->m63 = $m63;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function OnProjectFlow3()
    {
        $name = $this->Req('name', '', 'str');
        $no = $this->Req('no', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $attachments = $this->Req('attachments', '', 'str');

        $comp = $this->Req('comp', '', 'str');
        $date_ping = $this->Req('date_ping', '', 'str');

        $m11 = $this->Req('m11', '', 'str');
        $m12 = $this->Req('m12', '', 'str');
        $m13 = $this->Req('m13', '', 'str');
        $m21 = $this->Req('m21', '', 'str');
        $m22 = $this->Req('m22', '', 'str');
        $m23 = $this->Req('m23', '', 'str');
        $m31 = $this->Req('m31', '', 'str');
        $m32 = $this->Req('m32', '', 'str');
        $m33 = $this->Req('m33', '', 'str');
        $m41 = $this->Req('m41', '', 'str');
        $m42 = $this->Req('m42', '', 'str');
        $m43 = $this->Req('m43', '', 'str');
        $m51 = $this->Req('m51', '', 'str');
        $m52 = $this->Req('m52', '', 'str');
        $m53 = $this->Req('m53', '', 'str');
        $m61 = $this->Req('m61', '', 'str');
        $m62 = $this->Req('m62', '', 'str');
        $m63 = $this->Req('m63', '', 'str');

        $items1 = $this->Req('items1', array(), 'array');
        $items2 = $this->Req('items2', array(), 'array');
        $items3 = $this->Req('items3', array(), 'array');
        $items4 = $this->Req('items4', array(), 'array');
        $items5 = $this->Req('items5', array(), 'array');
        $totals1 = $this->Req('totals1', array(), 'array');
        $totals2 = $this->Req('totals2', array(), 'array');
        $totals3 = $this->Req('totals3', array(), 'array');
        $totals4 = $this->Req('totals4', array(), 'array');
        $totals5 = $this->Req('totals5', array(), 'array');
        $amount1 = $this->Req('amount1', '', 'str');
        $amount2 = $this->Req('amount2', '', 'str');
        $amount3 = $this->Req('amount3', '', 'str');
        $amount4 = $this->Req('amount4', '', 'str');

        $items = array();
        $num1 = count($items1);
        $num2 = count($items2);
        $num3 = count($items3);
        $num4 = count($items4);
        $num5 = count($items5);
        $totals = array();
        $tnum1 = count($totals1);
        $tnum2 = count($totals2);
        $tnum3 = count($totals3);
        $tnum4 = count($totals4);
        $tnum5 = count($totals5);

        if (!isset($_SESSION['facade_ds'])) Json::ReturnError(ALERT_ERROR);

        if ($num1 != $num2 || $num1 != $num3 || $num1 != $num4 || $num1 != $num5) Json::ReturnError(ALERT_ERROR);
        if ($num1 <= 0) Json::ReturnError('请至少添加一个条目');
//        for ($i = 0; $i < $num1; $i++) {
//            if (empty($items1[$i]) || empty($items2[$i]) || empty($items3[$i]) || empty($items4[$i]) || empty($items5[$i])) Json::ReturnError('项目条目序号' . ($i + 1) . '有不完整的信息');
//            $items[$i] = array($items1[$i], $items2[$i], $items3[$i], $items4[$i], $items5[$i]);
//        }
//
//        if ($tnum1 != $tnum2 || $tnum1 != $tnum3 || $tnum1 != $tnum4 || $tnum1 != $tnum5) Json::ReturnError(ALERT_ERROR);
//        for ($i = 0; $i < $tnum1; $i++) {
//            if (empty($totals1[$i]) || empty($totals2[$i]) || empty($totals3[$i]) || empty($totals4[$i]) || empty($totals5[$i])) Json::ReturnError('合计条目' . ($i + 1) . '有不完整的信息');
//            $totals[$i] = array($totals1[$i], $totals2[$i], $totals3[$i], $totals4[$i], $totals5[$i]);
//        }

        $pid = $this->Mid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($name)) Json::ReturnError('请输入单位工程名称');
        if (empty($comp)) Json::ReturnError('请输入施工单位');
        if (empty($no)) Json::ReturnError('请输入工程编号');
        if (empty($date_ping)) Json::ReturnError('请输入评定日期');

        //if (empty($items)) Json::ReturnError('请设置评定项目');

//        if (empty($amount1)) Json::ReturnError('请输入应得分');
//        if (empty($amount2)) Json::ReturnError('请输入实得分');
//        if (empty($amount3)) Json::ReturnError('请输入得分率');
//        if (empty($amount4)) Json::ReturnError('请输入外观质量等级');

//        if (empty($m11)) Json::ReturnError('请输入项目法人单位名称');
//        if (empty($m12)) Json::ReturnError('请输入项目法人职称');
//        if (empty($m13)) Json::ReturnError('请输入项目法人签名');
//
//        if (empty($m21)) Json::ReturnError('请输入监理单位单位名称');
//        if (empty($m22)) Json::ReturnError('请输入监理单位职称');
//        if (empty($m23)) Json::ReturnError('请输入监理单位签名');
//
//        if (empty($m31)) Json::ReturnError('请输入设计单位单位名称');
//        if (empty($m32)) Json::ReturnError('请输入设计单位职称');
//        if (empty($m33)) Json::ReturnError('请输入设计单位签名');
//
//        if (empty($m41)) Json::ReturnError('请输入施工单位单位名称');
//        if (empty($m42)) Json::ReturnError('请输入施工单位职称');
//        if (empty($m43)) Json::ReturnError('请输入施工单位签名');
//
//        if (empty($m51)) Json::ReturnError('请输入检测单位单位名称');
//        if (empty($m52)) Json::ReturnError('请输入检测单位职称');
//        if (empty($m53)) Json::ReturnError('请输入检测单位签名');
//
//        if (empty($m61)) Json::ReturnError('请输入运行管理单位单位名称');
//        if (empty($m62)) Json::ReturnError('请输入运行管理单位职称');
//        if (empty($m63)) Json::ReturnError('请输入运行管理单位签名');
//
//        if (empty($content)) Json::ReturnError('请输入核定意见');
//        if (empty($signer)) Json::ReturnError('请输入核定人');
//        if (empty($date)) Json::ReturnError('请输入日期');

        $datas = array('table' => $_SESSION['facade_ds'], 'items' => $items, 'totals' => $totals, 'amounts' => array($amount1, $amount2, $amount3, $amount4));
        $datas = Json::Encode($datas);

        try {
            $id = Flow3Cls::Add($pid, $name, $no, $signer, $content, $date, $attachments, $comp, $date_ping, $datas, '', $m11, $m12, $m13, $m21, $m22, $m23, $m31, $m32, $m33, $m41, $m42, $m43, $m51, $m52, $m53, $m61, $m62, $m63);
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }
        ProjectCls::SetNode($pid, ProjectNodeCls::CONFIRM, $id, ProjectStateCls::APPROVE);

        if (isset($_SESSION['facade_ds'])) unset($_SESSION['facade_ds']);
        if (isset($_SESSION['facade_table'])) unset($_SESSION['facade_table']);

        try {
            MsgCls::Add(1, MsgDirectCls::FROM_PROJECT, $this->Mid(), 1, ProjectCls::Instance()->Name($pid), '管理员', ProjectNodeCls::CONFIRM, $id, '新建' . ProjectNodeCls::Name(ProjectNodeCls::CONFIRM));
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        Json::ReturnSuccess();
    }

    public function ProjectReply3View()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow3Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Reply3Cls::GetLastItem($pid, $fid);

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectReply3View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow4List()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow4List');

        $pid = $this->Mid();

        $new = true;
//        $rl = Flow4Cls::GetLastItem($pid);
//        if (!(!empty($rl) && count($rl) > 0 && $rl['replyid'] <= 0)) {
//            $rl = array();
//        }
        $rs = Flow4Cls::GetProjectItems($pid);

//        $view->rl = $rl;
        $view->rs = $rs;
        $view->new = $new;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::SUGGEST);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow4()
    {
        $id = $this->Req('id', 0, 'int');

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow4');

        $pid = $this->Mid();
        $gc = ProjectCls::GetGroupCompany($pid);

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow4Cls::Instance()->Item($id);
        else $rs = Flow4Cls::GetLastItem($pid);

        $view->rs = $rs;

        $view->id = $id;
        $view->pid = $pid;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::SUGGEST);
        $view->finished = ProjectStateCls::IsFinished(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::SUGGEST));

        $view->atts = Atts::UploadDynamic(AttachmentCls::GetDynamicItems($pid, 3), false);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function OnProjectFlow4Sign()
    {
        $id = $this->Req('id', 0, 'int');
        $writer = $this->Req('writer', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $writer_date = $this->Req('writer_date', '', 'str');

        $pid = $this->Mid();

        if ($id <= 0 || $pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($writer)) Json::ReturnError('请输入签收单位');
        if (empty($signer)) Json::ReturnError('请输入签收人');
        if (empty($writer_date)) Json::ReturnError('请输入日期');

        Flow4Cls::SetSign($id, $pid, $writer, $signer, $writer_date);

        Json::ReturnSuccess();
    }

    public function ProjectReply4()
    {
        $fid = $this->Req('fid', 0, 'int');

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectReply4');

        $pid = $this->Mid();
        $gc = ProjectCls::GetGroupCompany($pid);

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $view->fid = $fid;

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->pid = $pid;
        $view->atts = Atts::UploadDynamic(AttachmentCls::GetDynamicItems($pid, 4), true);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function OnProjectReply4()
    {
        $fid = $this->Req('fid', 0, 'int');

        $no = $this->Req('no', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $content = $this->Req('content', '', 'str');
        $comp = $this->Req('comp', '', 'str');
        $date = $this->Req('date', '', 'str');
        $uid = $this->Mid();

        $pid = Flow4Cls::Instance()->Pid($fid);

        if ($fid <= 0 || $pid <= 0 || $uid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($no)) Json::ReturnError('请输入文件编号');
        if (empty($signer)) Json::ReturnError('请输入签发人');
        if (empty($content)) Json::ReturnError('请输入说明内容');
        //if (empty($comp)) Json::ReturnError('请输入单位(项目法人)');
        //if (empty($date)) Json::ReturnError('请输入日期');

        $act = 1;

        $replyid = Reply4Cls::Add($pid, $fid, $no, $signer, $content, $comp, $date, $uid, $act);
        Flow4Cls::SetReply($fid, $uid, $replyid);
        ProjectCls::SetNode($pid, ProjectNodeCls::SUGGEST, $fid, ProjectStateCls::ALLOW);

        try {
            MsgCls::Add(1, MsgDirectCls::FROM_PROJECT, $this->Mid(), 1, ProjectCls::Instance()->Name($pid), '管理员', ProjectNodeCls::SUGGEST, $replyid, '回复' . ProjectNodeCls::Name(ProjectNodeCls::SUGGEST));
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        Json::ReturnSuccess();
    }

    public function ProjectReply4View()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow4Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Reply4Cls::GetLastItem($pid, $fid);

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectReply4View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow51List()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow51List');

        $pid = $this->Mid();

        $new = true;
        $rr = array();
        $rl = Flow51Cls::GetLastItem($pid);
        if (!empty($rl) && count($rl) > 0) {
            $new = ProjectStateCls::IsNew(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::MATERIAL_1));
            if ($rl['replyid'] > 0) $rr = Reply1Cls::GetLastItem($pid, $rl['replyid']);
        }
        $rs = Flow51Cls::GetApprovedItems($pid);

        $view->rl = $rl;
        $view->rr = $rr;
        $view->rs = $rs;
        $view->new = $new;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::MATERIAL_1);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow51()
    {
        $id = $this->Req('id', 0, 'int');

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow51');

        $pid = $this->Mid();
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow51Cls::Instance()->Item($id);
        else $rs = Flow51Cls::GetLastItem($pid);

        $comp = '';
        $no = '';
        $signer = '';
        $date = '';

        $items = array();
        $totals = array();

        $v1c = '';
        $v1n = '';
        $v1d = '';
        $v2c = '';
        $v2n = '';
        $v2d = '';
        $v3c = '';
        $v3n = '';
        $v3d = '';

        $edit = true;

        if (!empty($rs) && count($rs) > 0) {

            $comp = $rs['comp'];
            $no = $rs['no'];
            $signer = $rs['signer'];
            $date = $rs['date'];

            $items = Json::Decode($rs['items']);
            $totals = Json::Decode($rs['totals']);

            $v1c = $rs['v1c'];
            $v1n = $rs['v1n'];
            $v1d = $rs['v1d'];
            $v2c = $rs['v2c'];
            $v2n = $rs['v2n'];
            $v2d = $rs['v2d'];
            $v3c = $rs['v3c'];
            $v3n = $rs['v3n'];
            $v3d = $rs['v3d'];

            $edit = ProjectStateCls::IsEdit(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::MATERIAL_1));
        }

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->edit = $edit;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::MATERIAL_1);

        $view->comp = $comp;
        $view->no = $no;
        $view->signer = $signer;
        $view->date = $date;

        $view->items = $items;
        $view->total0 = isset($totals[0]) ? $totals[0] : '';
        $view->total1 = isset($totals[1]) ? $totals[1] : '';

        $view->v1c = $v1c;
        $view->v1n = $v1n;
        $view->v1d = $v1d;
        $view->v2c = $v2c;
        $view->v2n = $v2n;
        $view->v2d = $v2d;
        $view->v3c = $v3c;
        $view->v3n = $v3n;
        $view->v3d = $v3d;

        $view->pid = $pid;
        $view->atts = Atts::UploadFixed(Atts::$flow51, AttachmentCls::GetFixedItems($pid, 51), $edit);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function OnProjectFlow51()
    {
        $comp = $this->Req('comp', '', 'str');
        $no = $this->Req('no', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $date = $this->Req('date', '', 'str');

        $items1 = $this->Req('items1', array(), 'array');
        $items2 = $this->Req('items2', array(), 'array');
        $items3 = $this->Req('items3', array(), 'array');

        $total0 = $this->Req('total0', '', 'str');
        $total1 = $this->Req('total1', '', 'str');

        $v1c = $this->Req('v1c', '', 'str');
        $v1n = $this->Req('v1n', '', 'str');
        $v1d = $this->Req('v1d', '', 'str');
        $v2c = $this->Req('v2c', '', 'str');
        $v2n = $this->Req('v2n', '', 'str');
        $v2d = $this->Req('v2d', '', 'str');
        $v3c = $this->Req('v3c', '', 'str');
        $v3n = $this->Req('v3n', '', 'str');
        $v3d = $this->Req('v3d', '', 'str');

        $items = array();
        $num1 = count($items1);
        $num2 = count($items2);
        $num3 = count($items3);

        if ($num1 != $num2 || $num1 != $num3) Json::ReturnError(ALERT_ERROR);
        if ($num1 <= 0) Json::ReturnError('请至少添加一个条目');
        for ($i = 0; $i < $num1; $i++) {
            if (empty($items1[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的工程名称与编号');
            if (empty($items2[$i]) && empty($items3[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的优良或合格');
            $items[$i] = array($items1[$i], $items2[$i], $items3[$i]);
        }

        if (empty($total0)) Json::ReturnError('请输入单元工程优良个数');
        if (empty($total1)) Json::ReturnError('请输入单元工程优良率');

        $pid = $this->Mid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($comp)) Json::ReturnError('请输入单位工程名称');
        if (empty($signer)) Json::ReturnError('请输入项目法人');
        if (empty($no)) Json::ReturnError('请输入分部工程名称');
        if (empty($date)) Json::ReturnError('请输入核备时间');

        if (empty($v1c)) Json::ReturnError('请输入监理单位意见');
        if (empty($v1n)) Json::ReturnError('请输入总监理工程师');
        if (empty($v1d)) Json::ReturnError('请输入监理单位日期');
        if (empty($v2c)) Json::ReturnError('请输入项目法人意见');
        if (empty($v2n)) Json::ReturnError('请输入技术负责人');
        if (empty($v2d)) Json::ReturnError('请输入项目法人日期');
        if (empty($v3c)) Json::ReturnError('请输入质量监督机构核备意见');
        if (empty($v3n)) Json::ReturnError('请输入核备人');
        if (empty($v3d)) Json::ReturnError('请输入质量监督机构核备日期');

        $items = Json::Encode($items);
        $totals = Json::Encode(array($total0, $total1));

        try {
            $id = Flow51Cls::Add($pid, $comp, $no, $signer, $date, $items, $totals, $v1c, $v1n, $v1d, $v2c, $v2n, $v2d, $v3c, $v3n, $v3d);
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }
        ProjectCls::SetNode($pid, ProjectNodeCls::MATERIAL_1, $id, ProjectStateCls::APPROVE);

        try {
            MsgCls::Add(1, MsgDirectCls::FROM_PROJECT, $this->Mid(), 1, ProjectCls::Instance()->Name($pid), '管理员', ProjectNodeCls::MATERIAL_1, $id, '新建' . ProjectNodeCls::Name(ProjectNodeCls::MATERIAL_1));
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        Json::ReturnSuccess();
    }

    public function ProjectReply51View()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow51Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Reply51Cls::GetLastItem($pid, $fid);

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectReply51View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow52List()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow52List');

        $pid = $this->Mid();

        $new = true;
        $rr = array();
        $rl = Flow52Cls::GetLastItem($pid);
        if (!empty($rl) && count($rl) > 0) {
            $new = ProjectStateCls::IsNew(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::MATERIAL_2));
            if ($rl['replyid'] > 0) $rr = Reply1Cls::GetLastItem($pid, $rl['replyid']);
        }
        $rs = Flow52Cls::GetApprovedItems($pid);

        $view->rl = $rl;
        $view->rr = $rr;
        $view->rs = $rs;
        $view->new = $new;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::MATERIAL_2);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow52()
    {
        $id = $this->Req('id', 0, 'int');

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow52');

        $pid = $this->Mid();
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow52Cls::Instance()->Item($id);
        else $rs = Flow52Cls::GetLastItem($pid);

        $comp = '';
        $no = '';
        $signer = '';
        $date = '';

        $items = array();

        $v1c = '';
        $v1n = '';
        $v1d = '';
        $v2c = '';
        $v2n = '';
        $v2d = '';
        $v3c = '';
        $v3n = '';
        $v3d = '';

        $edit = true;

        if (!empty($rs) && count($rs) > 0) {

            $comp = $rs['comp'];
            $no = $rs['no'];
            $signer = $rs['signer'];
            $date = $rs['date'];

            $items = Json::Decode($rs['items']);

            $v1c = $rs['v1c'];
            $v1n = $rs['v1n'];
            $v1d = $rs['v1d'];
            $v2c = $rs['v2c'];
            $v2n = $rs['v2n'];
            $v2d = $rs['v2d'];
            $v3c = $rs['v3c'];
            $v3n = $rs['v3n'];
            $v3d = $rs['v3d'];

            $edit = ProjectStateCls::IsEdit(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::MATERIAL_2));
        }

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->edit = $edit;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::MATERIAL_2);

        $view->comp = $comp;
        $view->no = $no;
        $view->signer = $signer;
        $view->date = $date;

        $view->items = $items;

        $view->v1c = $v1c;
        $view->v1n = $v1n;
        $view->v1d = $v1d;
        $view->v2c = $v2c;
        $view->v2n = $v2n;
        $view->v2d = $v2d;
        $view->v3c = $v3c;
        $view->v3n = $v3n;
        $view->v3d = $v3d;

        $view->pid = $pid;
        $view->atts = Atts::UploadFixed(Atts::$flow52, AttachmentCls::GetFixedItems($pid, 52), $edit);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function OnProjectFlow52()
    {
        $comp = $this->Req('comp', '', 'str');
        $no = $this->Req('no', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $date = $this->Req('date', '', 'str');

        $items1 = $this->Req('items1', array(), 'array');
        $items2 = $this->Req('items2', array(), 'array');
        $items3 = $this->Req('items3', array(), 'array');
        $items4 = $this->Req('items4', array(), 'array');
        $items5 = $this->Req('items5', array(), 'array');
        $items6 = $this->Req('items6', array(), 'array');
        $items7 = $this->Req('items7', array(), 'array');
        $items8 = $this->Req('items8', array(), 'array');

        $v1c = $this->Req('v1c', '', 'str');
        $v1n = $this->Req('v1n', '', 'str');
        $v1d = $this->Req('v1d', '', 'str');
        $v2c = $this->Req('v2c', '', 'str');
        $v2n = $this->Req('v2n', '', 'str');
        $v2d = $this->Req('v2d', '', 'str');
        $v3c = $this->Req('v3c', '', 'str');
        $v3n = $this->Req('v3n', '', 'str');
        $v3d = $this->Req('v3d', '', 'str');

        $items = array();
        $num1 = count($items1);
        $num2 = count($items2);
        $num3 = count($items3);
        $num4 = count($items4);
        $num5 = count($items5);
        $num6 = count($items6);
        $num7 = count($items7);
        $num8 = count($items8);

        if ($num1 != $num2 || $num1 != $num3 || $num1 != $num4 || $num1 != $num5 || $num1 != $num6 || $num1 != $num7 || $num1 != $num8) Json::ReturnError(ALERT_ERROR);
        if ($num1 <= 0) Json::ReturnError('请至少添加一个条目');
        for ($i = 0; $i < $num1; $i++) {
            if (empty($items1[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的分部工程名称与编号');
            if (empty($items2[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的单元工程数量');
            if (empty($items3[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的单元工程优良数');
            if (empty($items4[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的单元工程优良率');
            if (empty($items5[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的重要隐蔽（关键部位）单元工程数量');
            if (empty($items6[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的重要隐蔽（关键部位）单元工程优良数');
            if (empty($items7[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的重要隐蔽（关键部位）单元工程优良率');
            if (empty($items8[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的质量等级');
            $items[$i] = array($items1[$i], $items2[$i], $items3[$i], $items4[$i], $items5[$i], $items6[$i], $items7[$i], $items8[$i]);
        }

        $pid = $this->Mid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($comp)) Json::ReturnError('请输入单位工程名称');
        if (empty($signer)) Json::ReturnError('请输入项目法人');
        if (empty($no)) Json::ReturnError('请输入单位工程编号');
        if (empty($date)) Json::ReturnError('请输入核备时间');

        if (empty($v1c)) Json::ReturnError('请输入监理单位意见');
        if (empty($v1n)) Json::ReturnError('请输入总监理工程师');
        if (empty($v1d)) Json::ReturnError('请输入监理单位日期');
        if (empty($v2c)) Json::ReturnError('请输入项目法人意见');
        if (empty($v2n)) Json::ReturnError('请输入技术负责人');
        if (empty($v2d)) Json::ReturnError('请输入项目法人日期');
        if (empty($v3c)) Json::ReturnError('请输入质量监督机构核备意见');
        if (empty($v3n)) Json::ReturnError('请输入核备人');
        if (empty($v3d)) Json::ReturnError('请输入质量监督机构核备日期');

        $items = Json::Encode($items);
        $totals = '';

        try {
            $id = Flow52Cls::Add($pid, $comp, $no, $signer, $date, $items, $totals, $v1c, $v1n, $v1d, $v2c, $v2n, $v2d, $v3c, $v3n, $v3d);
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }
        ProjectCls::SetNode($pid, ProjectNodeCls::MATERIAL_2, $id, ProjectStateCls::APPROVE);

        try {
            MsgCls::Add(1, MsgDirectCls::FROM_PROJECT, $this->Mid(), 1, ProjectCls::Instance()->Name($pid), '管理员', ProjectNodeCls::MATERIAL_2, $id, '新建' . ProjectNodeCls::Name(ProjectNodeCls::MATERIAL_2));
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        Json::ReturnSuccess();
    }

    public function ProjectReply52View()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow52Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Reply52Cls::GetLastItem($pid, $fid);

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectReply52View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow61List()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow61List');

        $pid = $this->Mid();

        $new = true;
        $rr = array();
        $rl = Flow61Cls::GetLastItem($pid);
        if (!empty($rl) && count($rl) > 0) {
            $new = ProjectStateCls::IsNew(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::CHECK_1));
            if ($rl['replyid'] > 0) $rr = Reply1Cls::GetLastItem($pid, $rl['replyid']);
        }
        $rs = Flow61Cls::GetApprovedItems($pid);

        $view->rl = $rl;
        $view->rr = $rr;
        $view->rs = $rs;
        $view->new = $new;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::CHECK_1);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow61()
    {
        $id = $this->Req('id', 0, 'int');

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow61');

        $pid = $this->Mid();
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow61Cls::Instance()->Item($id);
        else $rs = Flow61Cls::GetLastItem($pid);

        $comp = '';
        $no = '';
        $signer = '';
        $date = '';

        $items = array();

        $v1c = '';
        $v1n = '';
        $v1d = '';
        $v2c = '';
        $v2n = '';
        $v2d = '';
        $v3c = '';
        $v3n = '';
        $v3d = '';

        $edit = true;

        if (!empty($rs) && count($rs) > 0) {

            $comp = $rs['comp'];
            $no = $rs['no'];
            $signer = $rs['signer'];
            $date = $rs['date'];

            $items = Json::Decode($rs['items']);

            $v1c = $rs['v1c'];
            $v1n = $rs['v1n'];
            $v1d = $rs['v1d'];
            $v2c = $rs['v2c'];
            $v2n = $rs['v2n'];
            $v2d = $rs['v2d'];
            $v3c = $rs['v3c'];
            $v3n = $rs['v3n'];
            $v3d = $rs['v3d'];

            $edit = ProjectStateCls::IsEdit(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::CHECK_1));
        }

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->edit = $edit;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::CHECK_1);

        $view->comp = $comp;
        $view->no = $no;
        $view->signer = $signer;
        $view->date = $date;

        $view->items = $items;

        $view->v1c = $v1c;
        $view->v1n = $v1n;
        $view->v1d = $v1d;
        $view->v2c = $v2c;
        $view->v2n = $v2n;
        $view->v2d = $v2d;
        $view->v3c = $v3c;
        $view->v3n = $v3n;
        $view->v3d = $v3d;

        $view->pid = $pid;
        $view->atts = Atts::UploadFixed(Atts::$flow61, AttachmentCls::GetFixedItems($pid, 61), $edit);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function OnProjectFlow61()
    {
        $comp = $this->Req('comp', '', 'str');
        $no = $this->Req('no', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $date = $this->Req('date', '', 'str');

        $items1 = $this->Req('items1', array(), 'array');
        $items2 = $this->Req('items2', array(), 'array');
        $items3 = $this->Req('items3', array(), 'array');
        $items4 = $this->Req('items4', array(), 'array');
        $items5 = $this->Req('items5', array(), 'array');
        $items6 = $this->Req('items6', array(), 'array');
        $items7 = $this->Req('items7', array(), 'array');
        $items8 = $this->Req('items8', array(), 'array');

        $v1c = $this->Req('v1c', '', 'str');
        $v1n = $this->Req('v1n', '', 'str');
        $v1d = $this->Req('v1d', '', 'str');
        $v2c = $this->Req('v2c', '', 'str');
        $v2n = $this->Req('v2n', '', 'str');
        $v2d = $this->Req('v2d', '', 'str');
        $v3c = $this->Req('v3c', '', 'str');
        $v3n = $this->Req('v3n', '', 'str');
        $v3d = $this->Req('v3d', '', 'str');

        $items = array();
        $num1 = count($items1);
        $num2 = count($items2);
        $num3 = count($items3);
        $num4 = count($items4);
        $num5 = count($items5);
        $num6 = count($items6);
        $num7 = count($items7);
        $num8 = count($items8);

        if ($num1 != $num2 || $num1 != $num3 || $num1 != $num4 || $num1 != $num5 || $num1 != $num6 || $num1 != $num7 || $num1 != $num8) Json::ReturnError(ALERT_ERROR);
        if ($num1 <= 0) Json::ReturnError('请至少添加一个条目');
        for ($i = 0; $i < $num1; $i++) {
            if (empty($items1[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的分部工程名称与编号');
            if (empty($items2[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的单元工程数量');
            if (empty($items3[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的单元工程优良数');
            if (empty($items4[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的单元工程优良率');
            if (empty($items5[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的重要隐蔽（关键部位）单元工程数量');
            if (empty($items6[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的重要隐蔽（关键部位）单元工程优良数');
            if (empty($items7[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的重要隐蔽（关键部位）单元工程优良率');
            if (empty($items8[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的质量等级');
            $items[$i] = array($items1[$i], $items2[$i], $items3[$i], $items4[$i], $items5[$i], $items6[$i], $items7[$i], $items8[$i]);
        }

        $pid = $this->Mid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($comp)) Json::ReturnError('请输入单位工程名称');
        if (empty($signer)) Json::ReturnError('请输入项目法人');
        if (empty($no)) Json::ReturnError('请输入单位工程编号');
        if (empty($date)) Json::ReturnError('请输入核定时间');

        if (empty($v1c)) Json::ReturnError('请输入监理单位意见');
        if (empty($v1n)) Json::ReturnError('请输入总监理工程师');
        if (empty($v1d)) Json::ReturnError('请输入监理单位日期');
        if (empty($v2c)) Json::ReturnError('请输入项目法人意见');
        if (empty($v2n)) Json::ReturnError('请输入技术负责人');
        if (empty($v2d)) Json::ReturnError('请输入项目法人日期');
        if (empty($v3c)) Json::ReturnError('请输入质量监督机构核备意见');
        if (empty($v3n)) Json::ReturnError('请输入核备人');
        if (empty($v3d)) Json::ReturnError('请输入质量监督机构核备日期');

        $items = Json::Encode($items);
        $totals = '';

        try {
            $id = Flow61Cls::Add($pid, $comp, $no, $signer, $date, $items, $totals, $v1c, $v1n, $v1d, $v2c, $v2n, $v2d, $v3c, $v3n, $v3d);
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }
        ProjectCls::SetNode($pid, ProjectNodeCls::CHECK_1, $id, ProjectStateCls::APPROVE);

        try {
            MsgCls::Add(1, MsgDirectCls::FROM_PROJECT, $this->Mid(), 1, ProjectCls::Instance()->Name($pid), '管理员', ProjectNodeCls::CHECK_1, $id, '新建' . ProjectNodeCls::Name(ProjectNodeCls::CHECK_1));
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        Json::ReturnSuccess();
    }

    public function ProjectReply61View()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow61Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Reply61Cls::GetLastItem($pid, $fid);

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectReply61View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow62List()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow62List');

        $pid = $this->Mid();

        $new = true;
        $rr = array();
        $rl = Flow62Cls::GetLastItem($pid);
        if (!empty($rl) && count($rl) > 0) {
            $new = ProjectStateCls::IsNew(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::CHECK_2));
            if ($rl['replyid'] > 0) $rr = Reply1Cls::GetLastItem($pid, $rl['replyid']);
        }
        $rs = Flow62Cls::GetApprovedItems($pid);

        $import = Flow3Cls::GetApprovedItems($pid);

        $view->rl = $rl;
        $view->rr = $rr;
        $view->rs = $rs;
        $view->new = $new;
        $view->import = $import;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::CHECK_2);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow62()
    {
        $id = $this->Req('id', 0, 'int');
        $iid = $this->Req('iid', 0, 'int'); //import 3 id

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow62');

        $pid = $this->Mid();
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow62Cls::Instance()->Item($id);
        else $rs = Flow62Cls::GetLastItem($pid);

        $ri = array();
        if ($iid > 0) $ri = Flow3Cls::Instance()->Item($iid);

        $attachments = '';
        $no = '';
        $signer = '';
        $content = '';
        $date = '';

        $comp = '';
        $date_ping = '';

        $datas = array();

        $m11 = '';
        $m12 = '';
        $m13 = '';
        $m21 = '';
        $m22 = '';
        $m23 = '';
        $m31 = '';
        $m32 = '';
        $m33 = '';
        $m41 = '';
        $m42 = '';
        $m43 = '';
        $m51 = '';
        $m52 = '';
        $m53 = '';
        $m61 = '';
        $m62 = '';
        $m63 = '';

        $edit = true;

        if ($iid > 0 && !empty($ri)) {
            $attachments = $ri['name'];
            $no = $ri['no'];
            $signer = $ri['signer'];
            $content = $ri['content'];
            $date = $ri['date'];

            $comp = $ri['comp'];
            $date_ping = $ri['date_ping'];

            $datas = Json::Decode($ri['items']);

            $m11 = $ri['m11'];
            $m12 = $ri['m12'];
            $m13 = $ri['m13'];
            $m21 = $ri['m21'];
            $m22 = $ri['m22'];
            $m23 = $ri['m23'];
            $m31 = $ri['m31'];
            $m32 = $ri['m32'];
            $m33 = $ri['m33'];
            $m41 = $ri['m41'];
            $m42 = $ri['m42'];
            $m43 = $ri['m43'];
            $m51 = $ri['m51'];
            $m52 = $ri['m52'];
            $m53 = $ri['m53'];
            $m61 = $ri['m61'];
            $m62 = $ri['m62'];
            $m63 = $ri['m63'];

            $edit = ProjectStateCls::IsEdit(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::CHECK_2));
        } elseif (!empty($rs) && count($rs) > 0) {
            $attachments = $rs['attachments'];
            $no = $rs['no'];
            $signer = $rs['signer'];
            $content = $rs['content'];
            $date = $rs['date'];

            $comp = $rs['comp'];
            $date_ping = $rs['date_ping'];

            $datas = Json::Decode($rs['items']);

            $m11 = $rs['m11'];
            $m12 = $rs['m12'];
            $m13 = $rs['m13'];
            $m21 = $rs['m21'];
            $m22 = $rs['m22'];
            $m23 = $rs['m23'];
            $m31 = $rs['m31'];
            $m32 = $rs['m32'];
            $m33 = $rs['m33'];
            $m41 = $rs['m41'];
            $m42 = $rs['m42'];
            $m43 = $rs['m43'];
            $m51 = $rs['m51'];
            $m52 = $rs['m52'];
            $m53 = $rs['m53'];
            $m61 = $rs['m61'];
            $m62 = $rs['m62'];
            $m63 = $rs['m63'];

            $edit = ProjectStateCls::IsEdit(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::CHECK_2));
        }

        $table = isset($datas['table']) ? $datas['table'] : array();
        $items = isset($datas['items']) ? $datas['items'] : array();
        $totals = isset($datas['totals']) ? $datas['totals'] : array();
        $amounts = isset($datas['amounts']) ? $datas['amounts'] : array();

        $data = array();
        $maxcols = 0;
        if (!empty($table)) {
            list($data, $maxcols) = $table;
            $_SESSION['facade_ds'] = $table;
        }

        $tables = $this->FacadeTableOk($data, $maxcols, $items, $totals, $amounts, $edit);
        $_SESSION['facade_table'] = $tables;

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->edit = $edit;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::CHECK_2);

        $view->no = $no;
        $view->signer = $signer;
        $view->content = $content;
        $view->date = $date;
        $view->attachments = $attachments;

        $view->comp = $comp;
        $view->date_ping = $date_ping;

        $view->tables = $tables;
        $view->cols = $maxcols;

        $view->m11 = $m11;
        $view->m12 = $m12;
        $view->m13 = $m13;
        $view->m21 = $m21;
        $view->m22 = $m22;
        $view->m23 = $m23;
        $view->m31 = $m31;
        $view->m32 = $m32;
        $view->m33 = $m33;
        $view->m41 = $m41;
        $view->m42 = $m42;
        $view->m43 = $m43;
        $view->m51 = $m51;
        $view->m52 = $m52;
        $view->m53 = $m53;
        $view->m61 = $m61;
        $view->m62 = $m62;
        $view->m63 = $m63;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function OnProjectFlow62()
    {
        $no = $this->Req('no', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $attachments = $this->Req('attachments', '', 'str');

        $comp = $this->Req('comp', '', 'str');
        $date_ping = $this->Req('date_ping', '', 'str');

        $m11 = $this->Req('m11', '', 'str');
        $m12 = $this->Req('m12', '', 'str');
        $m13 = $this->Req('m13', '', 'str');
        $m21 = $this->Req('m21', '', 'str');
        $m22 = $this->Req('m22', '', 'str');
        $m23 = $this->Req('m23', '', 'str');
        $m31 = $this->Req('m31', '', 'str');
        $m32 = $this->Req('m32', '', 'str');
        $m33 = $this->Req('m33', '', 'str');
        $m41 = $this->Req('m41', '', 'str');
        $m42 = $this->Req('m42', '', 'str');
        $m43 = $this->Req('m43', '', 'str');
        $m51 = $this->Req('m51', '', 'str');
        $m52 = $this->Req('m52', '', 'str');
        $m53 = $this->Req('m53', '', 'str');
        $m61 = $this->Req('m61', '', 'str');
        $m62 = $this->Req('m62', '', 'str');
        $m63 = $this->Req('m63', '', 'str');

        $items1 = $this->Req('items1', array(), 'array');
        $items2 = $this->Req('items2', array(), 'array');
        $items3 = $this->Req('items3', array(), 'array');
        $items4 = $this->Req('items4', array(), 'array');
        $items5 = $this->Req('items5', array(), 'array');
        $totals1 = $this->Req('totals1', array(), 'array');
        $totals2 = $this->Req('totals2', array(), 'array');
        $totals3 = $this->Req('totals3', array(), 'array');
        $totals4 = $this->Req('totals4', array(), 'array');
        $totals5 = $this->Req('totals5', array(), 'array');
        $amount1 = $this->Req('amount1', '', 'str');
        $amount2 = $this->Req('amount2', '', 'str');
        $amount3 = $this->Req('amount3', '', 'str');
        $amount4 = $this->Req('amount4', '', 'str');

        $items = array();
        $num1 = count($items1);
        $num2 = count($items2);
        $num3 = count($items3);
        $num4 = count($items4);
        $num5 = count($items5);
        $totals = array();
        $tnum1 = count($totals1);
        $tnum2 = count($totals2);
        $tnum3 = count($totals3);
        $tnum4 = count($totals4);
        $tnum5 = count($totals5);

        if (!isset($_SESSION['facade_ds'])) Json::ReturnError(ALERT_ERROR);

        if ($num1 != $num2 || $num1 != $num3 || $num1 != $num4 || $num1 != $num5) Json::ReturnError(ALERT_ERROR);
        if ($num1 <= 0) Json::ReturnError('请至少添加一个条目');
        for ($i = 0; $i < $num1; $i++) {
            if (empty($items1[$i]) || empty($items2[$i]) || empty($items3[$i]) || empty($items4[$i]) || empty($items5[$i])) Json::ReturnError('项目条目序号' . ($i + 1) . '有不完整的信息');
            $items[$i] = array($items1[$i], $items2[$i], $items3[$i], $items4[$i], $items5[$i]);
        }

        if ($tnum1 != $tnum2 || $tnum1 != $tnum3 || $tnum1 != $tnum4 || $tnum1 != $tnum5) Json::ReturnError(ALERT_ERROR);
        for ($i = 0; $i < $tnum1; $i++) {
            if (empty($totals1[$i]) || empty($totals2[$i]) || empty($totals3[$i]) || empty($totals4[$i]) || empty($totals5[$i])) Json::ReturnError('合计条目' . ($i + 1) . '有不完整的信息');
            $totals[$i] = array($totals1[$i], $totals2[$i], $totals3[$i], $totals4[$i], $totals5[$i]);
        }

        $pid = $this->Mid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($attachments)) Json::ReturnError('请输入单位工程名称');
        if (empty($comp)) Json::ReturnError('请输入施工单位');
        if (empty($no)) Json::ReturnError('请输入工程编号');
        if (empty($date_ping)) Json::ReturnError('请输入评定日期');

        //if (empty($items)) Json::ReturnError('请设置评定项目');

        if (empty($amount1)) Json::ReturnError('请输入应得分');
        if (empty($amount2)) Json::ReturnError('请输入实得分');
        if (empty($amount3)) Json::ReturnError('请输入得分率');
        if (empty($amount4)) Json::ReturnError('请输入外观质量等级');

        if (empty($m11)) Json::ReturnError('请输入项目法人单位名称');
        if (empty($m12)) Json::ReturnError('请输入项目法人职称');
        if (empty($m13)) Json::ReturnError('请输入项目法人签名');

        if (empty($m21)) Json::ReturnError('请输入监理单位单位名称');
        if (empty($m22)) Json::ReturnError('请输入监理单位职称');
        if (empty($m23)) Json::ReturnError('请输入监理单位签名');

        if (empty($m31)) Json::ReturnError('请输入设计单位单位名称');
        if (empty($m32)) Json::ReturnError('请输入设计单位职称');
        if (empty($m33)) Json::ReturnError('请输入设计单位签名');

        if (empty($m41)) Json::ReturnError('请输入施工单位单位名称');
        if (empty($m42)) Json::ReturnError('请输入施工单位职称');
        if (empty($m43)) Json::ReturnError('请输入施工单位签名');

        if (empty($m51)) Json::ReturnError('请输入检测单位单位名称');
        if (empty($m52)) Json::ReturnError('请输入检测单位职称');
        if (empty($m53)) Json::ReturnError('请输入检测单位签名');

        if (empty($m61)) Json::ReturnError('请输入运行管理单位单位名称');
        if (empty($m62)) Json::ReturnError('请输入运行管理单位职称');
        if (empty($m63)) Json::ReturnError('请输入运行管理单位签名');

        if (empty($content)) Json::ReturnError('请输入核定意见');
        if (empty($signer)) Json::ReturnError('请输入核定人');
        if (empty($date)) Json::ReturnError('请输入日期');

        $datas = array('table' => $_SESSION['facade_ds'], 'items' => $items, 'totals' => $totals, 'amounts' => array($amount1, $amount2, $amount3, $amount4));
        $datas = Json::Encode($datas);

        try {
            $id = Flow62Cls::Add($pid, $no, $signer, $content, $date, $attachments, $comp, $date_ping, $datas, '', $m11, $m12, $m13, $m21, $m22, $m23, $m31, $m32, $m33, $m41, $m42, $m43, $m51, $m52, $m53, $m61, $m62, $m63);
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }
        ProjectCls::SetNode($pid, ProjectNodeCls::CHECK_2, $id, ProjectStateCls::APPROVE);

        if (isset($_SESSION['facade_ds'])) unset($_SESSION['facade_ds']);
        if (isset($_SESSION['facade_table'])) unset($_SESSION['facade_table']);

        try {
            MsgCls::Add(1, MsgDirectCls::FROM_PROJECT, $this->Mid(), 1, ProjectCls::Instance()->Name($pid), '管理员', ProjectNodeCls::CHECK_2, $id, '新建' . ProjectNodeCls::Name(ProjectNodeCls::CHECK_2));
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        Json::ReturnSuccess();
    }

    public function ProjectReply62View()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow62Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Reply62Cls::GetLastItem($pid, $fid);

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectReply62View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow63List()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow63List');

        $pid = $this->Mid();

        $new = true;
        $rr = array();
        $rl = Flow63Cls::GetLastItem($pid);
        if (!empty($rl) && count($rl) > 0) {
            $new = ProjectStateCls::IsNew(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::CHECK_3));
            if ($rl['replyid'] > 0) $rr = Reply1Cls::GetLastItem($pid, $rl['replyid']);
        }
        $rs = Flow63Cls::GetApprovedItems($pid);

        $view->rl = $rl;
        $view->rr = $rr;
        $view->rs = $rs;
        $view->new = $new;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::CHECK_3);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow63()
    {
        $id = $this->Req('id', 0, 'int');

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow63');

        $pid = $this->Mid();
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow63Cls::Instance()->Item($id);
        else $rs = Flow63Cls::GetLastItem($pid);

        $comp = '';
        $no = '';
        $signer = '';
        $date = '';

        $items = array();
        $totals = array();

        $v1c = '';
        $v1n = '';
        $v1d = '';
        $v2c = '';
        $v2n = '';
        $v2d = '';
        $v3c = '';
        $v3n = '';
        $v3d = '';

        $edit = true;

        if (!empty($rs) && count($rs) > 0) {

            $name = $rs['name'];

            $comp = $rs['comp'];
            $no = $rs['no'];
            $signer = $rs['signer'];
            $date = $rs['date'];

            $items = Json::Decode($rs['items']);
            $totals = Json::Decode($rs['totals']);

            $v1c = $rs['v1c'];
            $v1n = $rs['v1n'];
            $v1d = $rs['v1d'];
            $v2c = $rs['v2c'];
            $v2n = $rs['v2n'];
            $v2d = $rs['v2d'];
            $v3c = $rs['v3c'];
            $v3n = $rs['v3n'];
            $v3d = $rs['v3d'];

            $edit = ProjectStateCls::IsEdit(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::CHECK_3));
        }

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->edit = $edit;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::CHECK_3);

        $view->comp = $comp;
        $view->no = $no;
        $view->signer = $signer;
        $view->date = $date;

        $view->items = $items;
        $view->total0 = isset($totals[0]) ? $totals[0] : '';
        $view->total1 = isset($totals[1]) ? $totals[1] : '';
        $view->total2 = isset($totals[2]) ? $totals[2] : '';
        $view->total3 = isset($totals[3]) ? $totals[3] : '';
        $view->total4 = isset($totals[4]) ? $totals[4] : '';
        $view->total5 = isset($totals[5]) ? $totals[5] : '';

        $view->v1c = $v1c;
        $view->v1n = $v1n;
        $view->v1d = $v1d;
        $view->v2c = $v2c;
        $view->v2n = $v2n;
        $view->v2d = $v2d;
        $view->v3c = $v3c;
        $view->v3n = $v3n;
        $view->v3d = $v3d;

        $view->pid = $pid;
        $view->atts = Atts::UploadFixed(Atts::$flow63, AttachmentCls::GetFixedItems($pid, 63), $edit);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function OnProjectFlow63()
    {
        $name = $this->Req('name', '', 'str');
        $no = $this->Req('no', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $date = $this->Req('date', '', 'str');

        $items1 = $this->Req('items1', array(), 'array');
        $items2 = $this->Req('items2', array(), 'array');
        $items3 = $this->Req('items3', array(), 'array');

        $total0 = $this->Req('total0', '', 'str');
        $total1 = $this->Req('total1', '', 'str');
        $total2 = $this->Req('total2', '', 'str');
        $total3 = $this->Req('total3', '', 'str');
        $total4 = $this->Req('total4', '', 'str');
        $total5 = $this->Req('total5', '', 'str');

        $v1c = $this->Req('v1c', '', 'str');
        $v1n = $this->Req('v1n', '', 'str');
        $v1d = $this->Req('v1d', '', 'str');
        $v2c = $this->Req('v2c', '', 'str');
        $v2n = $this->Req('v2n', '', 'str');
        $v2d = $this->Req('v2d', '', 'str');
        $v3c = $this->Req('v3c', '', 'str');
        $v3n = $this->Req('v3n', '', 'str');
        $v3d = $this->Req('v3d', '', 'str');

        $items = array();
        $num1 = count($items1);
        $num2 = count($items2);
        $num3 = count($items3);

        if ($num1 != $num2 || $num1 != $num3) Json::ReturnError(ALERT_ERROR);
        if ($num1 <= 0) Json::ReturnError('请至少添加一个条目');
        for ($i = 0; $i < $num1; $i++) {
            if (empty($items1[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的工程名称与编号');
            if (empty($items2[$i]) && empty($items3[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的优良或合格');
            if (!empty($items2[$i]) && !empty($items3[$i])) Json::ReturnError('序号' . ($i + 1) . '条目的优良或合格只能填写一个');
            $items[$i] = array($items1[$i], $items2[$i], $items3[$i]);
        }

        if (empty($total0)) Json::ReturnError('请输入分部工程数');
        if (empty($total1)) Json::ReturnError('请输入分部工程优良数');
        if (empty($total2)) Json::ReturnError('请输入分部工程优良率');
        if (empty($total3)) Json::ReturnError('请输入主要分部工程优良率');
        if (empty($total4)) Json::ReturnError('请输入外观质量得分');
        if (empty($total5)) Json::ReturnError('请输入单位工程质量等级');

        $pid = $this->Mid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($name)) Json::ReturnError('请输入单位工程名称');
        if (empty($signer)) Json::ReturnError('请输入项目法人');
        if (empty($no)) Json::ReturnError('请输入单位工程编号');
        if (empty($date)) Json::ReturnError('请输入核定时间');

        if (empty($v1c)) Json::ReturnError('请输入监理单位意见');
        if (empty($v1n)) Json::ReturnError('请输入总监理工程师');
        if (empty($v1d)) Json::ReturnError('请输入监理单位日期');
        if (empty($v2c)) Json::ReturnError('请输入项目法人意见');
        if (empty($v2n)) Json::ReturnError('请输入技术负责人');
        if (empty($v2d)) Json::ReturnError('请输入项目法人日期');
        if (empty($v3c)) Json::ReturnError('请输入质量监督机构核备意见');
        if (empty($v3n)) Json::ReturnError('请输入核备人');
        if (empty($v3d)) Json::ReturnError('请输入质量监督机构核备日期');

        $items = Json::Encode($items);
        $totals = Json::Encode(array($total0, $total1, $total2, $total3, $total4, $total5));

        try {
            $id = Flow63Cls::Add($pid, $name, '', $no, $signer, $date, $items, $totals, $v1c, $v1n, $v1d, $v2c, $v2n, $v2d, $v3c, $v3n, $v3d);
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }
        ProjectCls::SetNode($pid, ProjectNodeCls::CHECK_3, $id, ProjectStateCls::APPROVE);

        try {
            MsgCls::Add(1, MsgDirectCls::FROM_PROJECT, $this->Mid(), 1, ProjectCls::Instance()->Name($pid), '管理员', ProjectNodeCls::CHECK_3, $id, '新建' . ProjectNodeCls::Name(ProjectNodeCls::CHECK_3));
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        Json::ReturnSuccess();
    }

    public function ProjectReply63View()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow63Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Reply63Cls::GetLastItem($pid, $fid);

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectReply63View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow64List()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow64List');

        $pid = $this->Mid();

        $new = true;
        $rr = array();
        $rl = Flow64Cls::GetLastItem($pid);
        if (!empty($rl) && count($rl) > 0) {
            $new = ProjectStateCls::IsNew(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::CHECK_4));
            if ($rl['replyid'] > 0) $rr = Reply1Cls::GetLastItem($pid, $rl['replyid']);
        }
        $rs = Flow64Cls::GetApprovedItems($pid);

        $view->rl = $rl;
        $view->rr = $rr;
        $view->rs = $rs;
        $view->new = $new;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::CHECK_4);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow64()
    {
        $id = $this->Req('id', 0, 'int');

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow64');

        $pid = $this->Mid();
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow64Cls::Instance()->Item($id);
        else $rs = Flow64Cls::GetLastItem($pid);

        $comp = '';
        $no = '';
        $signer = '';
        $date = '';

        $items = array();
        $totals = array();

        $v1c = '';
        $v1n = '';
        $v1d = '';
        $v2c = '';
        $v2n = '';
        $v2d = '';
        $v3c = '';
        $v3n = '';
        $v3d = '';

        $edit = true;

        if (!empty($rs) && count($rs) > 0) {
            $name = $rs['name'];

            $comp = $rs['comp'];
            $no = $rs['no'];
            $signer = $rs['signer'];
            $date = $rs['date'];

            $items = Json::Decode($rs['items']);
            $totals = Json::Decode($rs['totals']);

            $v1c = $rs['v1c'];
            $v1n = $rs['v1n'];
            $v1d = $rs['v1d'];
            $v2c = $rs['v2c'];
            $v2n = $rs['v2n'];
            $v2d = $rs['v2d'];
            $v3c = $rs['v3c'];
            $v3n = $rs['v3n'];
            $v3d = $rs['v3d'];

            $edit = ProjectStateCls::IsEdit(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::CHECK_4));
        }

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->edit = $edit;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::CHECK_4);

        $view->comp = $comp;
        $view->no = $no;
        $view->signer = $signer;
        $view->date = $date;

        $view->items = $items;
        $view->total0 = isset($totals[0]) ? $totals[0] : '';
        $view->total1 = isset($totals[1]) ? $totals[1] : '';
        $view->total2 = isset($totals[2]) ? $totals[2] : '';
        $view->total3 = isset($totals[3]) ? $totals[3] : '';
        $view->total4 = isset($totals[4]) ? $totals[4] : '';
        $view->total5 = isset($totals[5]) ? $totals[5] : '';

        $view->v1c = $v1c;
        $view->v1n = $v1n;
        $view->v1d = $v1d;
        $view->v2c = $v2c;
        $view->v2n = $v2n;
        $view->v2d = $v2d;
        $view->v3c = $v3c;
        $view->v3n = $v3n;
        $view->v3d = $v3d;

        $view->pid = $pid;
        $view->atts = Atts::UploadFixed(Atts::$flow64, AttachmentCls::GetFixedItems($pid, 64), $edit);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function OnProjectFlow64()
    {
        $name = $this->Req('name', '', 'str');
        $no = $this->Req('no', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $date = $this->Req('date', '', 'str');

        $items1 = $this->Req('items1', array(), 'array');
        $items2 = $this->Req('items2', array(), 'array');
        $items3 = $this->Req('items3', array(), 'array');

        $total0 = $this->Req('total0', '', 'str');
        $total1 = $this->Req('total1', '', 'str');
        $total2 = $this->Req('total2', '', 'str');
        $total3 = $this->Req('total3', '', 'str');
        $total4 = $this->Req('total4', '', 'str');

        $v1c = $this->Req('v1c', '', 'str');
        $v1n = $this->Req('v1n', '', 'str');
        $v1d = $this->Req('v1d', '', 'str');
        $v2c = $this->Req('v2c', '', 'str');
        $v2n = $this->Req('v2n', '', 'str');
        $v2d = $this->Req('v2d', '', 'str');
        $v3c = $this->Req('v3c', '', 'str');
        $v3n = $this->Req('v3n', '', 'str');
        $v3d = $this->Req('v3d', '', 'str');

        $items = array();
        $num1 = count($items1);
        $num2 = count($items2);
        $num3 = count($items3);

        if ($num1 != $num2 || $num1 != $num3) Json::ReturnError(ALERT_ERROR);
        if ($num1 <= 0) Json::ReturnError('请至少添加一个条目');
        for ($i = 0; $i < $num1; $i++) {
            if (empty($items1[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的工程名称与编号');
            if (empty($items2[$i]) && empty($items3[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的优良或合格');
            if (!empty($items2[$i]) && !empty($items3[$i])) Json::ReturnError('序号' . ($i + 1) . '条目的优良或合格只能填写一个');
            $items[$i] = array($items1[$i], $items2[$i], $items3[$i]);
        }

        if (empty($total0)) Json::ReturnError('请输入单位工程数');
        if (empty($total1)) Json::ReturnError('请输入单位工程优良数');
        if (empty($total2)) Json::ReturnError('请输入单位工程优良率');
        if (empty($total3)) Json::ReturnError('请输入主要单位工程优良率');
        if (empty($total4)) Json::ReturnError('请输入工程项目质量等级');

        $pid = $this->Mid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($name)) Json::ReturnError('请输入工程项目名称');
        if (empty($signer)) Json::ReturnError('请输入项目法人');
        if (empty($no)) Json::ReturnError('请输入单位工程编号');
        if (empty($date)) Json::ReturnError('请输入核定时间');

        if (empty($v1c)) Json::ReturnError('请输入监理单位意见');
        if (empty($v1n)) Json::ReturnError('请输入总监理工程师');
        if (empty($v1d)) Json::ReturnError('请输入监理单位日期');
        if (empty($v2c)) Json::ReturnError('请输入项目法人意见');
        if (empty($v2n)) Json::ReturnError('请输入技术负责人');
        if (empty($v2d)) Json::ReturnError('请输入项目法人日期');
        if (empty($v3c)) Json::ReturnError('请输入质量监督机构核备意见');
        if (empty($v3n)) Json::ReturnError('请输入核备人');
        if (empty($v3d)) Json::ReturnError('请输入质量监督机构核备日期');

        $items = Json::Encode($items);
        $totals = Json::Encode(array($total0, $total1, $total2, $total3, $total4));

        try {
            $id = Flow64Cls::Add($pid, $name, '', $no, $signer, $date, $items, $totals, $v1c, $v1n, $v1d, $v2c, $v2n, $v2d, $v3c, $v3n, $v3d);
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }
        ProjectCls::SetNode($pid, ProjectNodeCls::CHECK_4, $id, ProjectStateCls::APPROVE);

        try {
            MsgCls::Add(1, MsgDirectCls::FROM_PROJECT, $this->Mid(), 1, ProjectCls::Instance()->Name($pid), '管理员', ProjectNodeCls::CHECK_4, $id, '新建' . ProjectNodeCls::Name(ProjectNodeCls::CHECK_4));
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        Json::ReturnSuccess();
    }

    public function ProjectReply64View()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow64Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Reply64Cls::GetLastItem($pid, $fid);

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectReply64View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow71List()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow71List');

        $pid = $this->Mid();

        $new = true;
        $rr = array();
        $rl = Flow71Cls::GetLastItem($pid);
        if (!empty($rl) && count($rl) > 0) {
            $new = ProjectStateCls::IsNew(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::RECORD_1));
            if ($rl['replyid'] > 0) $rr = Reply1Cls::GetLastItem($pid, $rl['replyid']);
        }
        $rs = Flow71Cls::GetApprovedItems($pid);

        $view->rl = $rl;
        $view->rr = $rr;
        $view->rs = $rs;
        $view->new = $new;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::RECORD_1);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow71()
    {
        $id = $this->Req('id', 0, 'int');

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow71');

        $pid = $this->Mid();
        $gc = ProjectCls::GetGroupCompany($pid);

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow71Cls::Instance()->Item($id);
        else $rs = Flow71Cls::GetLastItem($pid);

        $no = '';
        $signer = '';
        $content = '';
        $date = '';
        $keywords = '';

        $edit = true;

        if (!empty($rs) && count($rs) > 0) {

            $no = $rs['no'];
            $signer = $rs['signer'];
            $content = $rs['content'];
            $date = $rs['date'];
            $keywords = $rs['keywords'];

            $edit = ProjectStateCls::IsEdit(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::RECORD_1));
        }

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->edit = $edit;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::RECORD_1);

        $view->no = $no;
        $view->signer = $signer;
        $view->content = $content;
        $view->date = $date;
        $view->keywords = $keywords;

        $view->pid = $pid;
        $view->atts = Atts::UploadDynamic(AttachmentCls::GetDynamicItems($pid, 71), $edit);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function OnProjectFlow71()
    {
        $no = $this->Req('no', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $keywords = $this->Req('keywords', '', 'str');
        $attachments = $this->Req('attachments', '', 'str');

        $pid = $this->Mid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($no)) Json::ReturnError('请输入文件编号');
        if (empty($signer)) Json::ReturnError('请输入签发单位');
        if (empty($content)) Json::ReturnError('请输入申报内容');
        if (empty($date)) Json::ReturnError('请输入申报日期');

        $id = Flow71Cls::Add($pid, $no, $signer, $content, $date, $keywords, $attachments);
        ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_1, $id, ProjectStateCls::APPROVE);

        try {
            MsgCls::Add(1, MsgDirectCls::FROM_PROJECT, $this->Mid(), 1, ProjectCls::Instance()->Name($pid), '管理员', ProjectNodeCls::RECORD_1, $id, '新建' . ProjectNodeCls::Name(ProjectNodeCls::RECORD_1));
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        Json::ReturnSuccess();
    }

    public function ProjectReply71View()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow71Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Reply71Cls::GetLastItem($pid, $fid);

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectReply71View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow72List()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow72List');

        $pid = $this->Mid();

        $new = true;
        $rr = array();
        $rl = Flow72Cls::GetLastItem($pid);
        if (!empty($rl) && count($rl) > 0) {
            $new = ProjectStateCls::IsNew(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::RECORD_2));
            if ($rl['replyid'] > 0) $rr = Reply1Cls::GetLastItem($pid, $rl['replyid']);
        }
        $rs = Flow72Cls::GetApprovedItems($pid);

        $view->rl = $rl;
        $view->rr = $rr;
        $view->rs = $rs;
        $view->new = $new;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::RECORD_2);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow72()
    {
        $id = $this->Req('id', 0, 'int');

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow72');

        $pid = $this->Mid();
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow72Cls::Instance()->Item($id);
        else $rs = Flow72Cls::GetLastItem($pid);

        $comp = '';
        $no = '';
        $signer = '';
        $date = '';
        $attachments = '';

        $c1 = '';
        $c2 = '';
        $c3 = '';
        $c4 = '';
        $c5 = '';
        $c6 = '';
        $c7 = '';

        $v1c = '';
        $v1n = '';
        $v1d = '';
        $v2c = '';
        $v2n = '';
        $v2d = '';
        $v3c = '';
        $v3n = '';
        $v3d = '';
        $v4c = '';
        $v4n = '';
        $v4d = '';
        $v5c = '';
        $v5n = '';
        $v5d = '';

        $edit = true;

        if (!empty($rs) && count($rs) > 0) {

            $comp = $rs['comp'];
            $no = $rs['no'];
            $signer = $rs['signer'];
            $date = $rs['date'];
            $attachments = $rs['attachments'];

            $c1 = $rs['c1'];
            $c2 = $rs['c2'];
            $c3 = $rs['c3'];
            $c4 = $rs['c4'];
            $c5 = $rs['c5'];
            $c6 = $rs['c6'];
            $c7 = $rs['c7'];

            $v1c = $rs['v1c'];
            $v1n = $rs['v1n'];
            $v1d = $rs['v1d'];
            $v2c = $rs['v2c'];
            $v2n = $rs['v2n'];
            $v2d = $rs['v2d'];
            $v3c = $rs['v3c'];
            $v3n = $rs['v3n'];
            $v3d = $rs['v3d'];
            $v4c = $rs['v4c'];
            $v4n = $rs['v4n'];
            $v4d = $rs['v4d'];
            $v5c = $rs['v5c'];
            $v5n = $rs['v5n'];
            $v5d = $rs['v5d'];

            $edit = ProjectStateCls::IsEdit(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::RECORD_2));
        }

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->edit = $edit;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::RECORD_2);

        $view->comp = $comp;
        $view->no = $no;
        $view->signer = $signer;
        $view->date = $date;
        $view->attachments = $attachments;

        $view->c1 = $c1;
        $view->c2 = $c2;
        $view->c3 = $c3;
        $view->c4 = $c4;
        $view->c5 = $c5;
        $view->c6 = $c6;
        $view->c7 = $c7;

        $view->v1c = $v1c;
        $view->v1n = $v1n;
        $view->v1d = $v1d;
        $view->v2c = $v2c;
        $view->v2n = $v2n;
        $view->v2d = $v2d;
        $view->v3c = $v3c;
        $view->v3n = $v3n;
        $view->v3d = $v3d;
        $view->v4c = $v4c;
        $view->v4n = $v4n;
        $view->v4d = $v4d;
        $view->v5c = $v5c;
        $view->v5n = $v5n;
        $view->v5d = $v5d;

        $view->pid = $pid;
        $view->atts = Atts::UploadDynamic(AttachmentCls::GetDynamicItems($pid, 72), $edit);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function OnProjectFlow72()
    {
        $no = $this->Req('no', '', 'str');
        $comp = $this->Req('comp', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $date = $this->Req('date', '', 'str');
        $attachments = $this->Req('attachments', '', 'str');

        $c1 = $this->Req('c1', '', 'str');
        $c2 = $this->Req('c2', '', 'str');
        $c3 = $this->Req('c3', '', 'str');
        $c4 = $this->Req('c4', '', 'str');
        $c5 = $this->Req('c5', '', 'str');
        $c6 = $this->Req('c6', '', 'str');
        $c7 = $this->Req('c7', '', 'str');

        $v1c = $this->Req('v1c', '', 'str');
        $v1n = $this->Req('v1n', '', 'str');
        $v1d = $this->Req('v1d', '', 'str');
        $v2c = $this->Req('v2c', '', 'str');
        $v2n = $this->Req('v2n', '', 'str');
        $v2d = $this->Req('v2d', '', 'str');
        $v3c = $this->Req('v3c', '', 'str');
        $v3n = $this->Req('v3n', '', 'str');
        $v3d = $this->Req('v3d', '', 'str');
        $v4c = $this->Req('v4c', '', 'str');
        $v4n = $this->Req('v4n', '', 'str');
        $v4d = $this->Req('v4d', '', 'str');
        $v5c = $this->Req('v5c', '', 'str');
        $v5n = $this->Req('v5n', '', 'str');
        $v5d = $this->Req('v5d', '', 'str');

        $pid = $this->Mid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($attachments)) Json::ReturnError('请输入单位工程名称');
        if (empty($no)) Json::ReturnError('请输入编号');
        if (empty($comp)) Json::ReturnError('请输入施工单位');
        if (empty($signer)) Json::ReturnError('请输入验槽部位');
        if (empty($date)) Json::ReturnError('请输入验槽时间');

        if (empty($c1)) Json::ReturnError('请输入基槽底地质报告土质情况');
        if (empty($c2)) Json::ReturnError('请输入基槽底实际土质情况');
        if (empty($c3)) Json::ReturnError('请输入基槽高程与尺寸');
        if (empty($c4)) Json::ReturnError('请输入降排水情况');
        if (empty($c5)) Json::ReturnError('请输入附图及说明');
        if (empty($c6)) Json::ReturnError('请输入施工单位初验意见');
        if (empty($c7)) Json::ReturnError('请输入验槽结论');

        if (empty($v1c)) Json::ReturnError('请输入项目法人单位名称');
        if (empty($v1n)) Json::ReturnError('请输入项目法人职务、职称');
        if (empty($v1d)) Json::ReturnError('请输入项目法人签字');
        if (empty($v2c)) Json::ReturnError('请输入监理单位单位名称');
        if (empty($v2n)) Json::ReturnError('请输入监理单位职务、职称');
        if (empty($v2d)) Json::ReturnError('请输入监理单位签字');
        if (empty($v3c)) Json::ReturnError('请输入勘测单位单位名称');
        if (empty($v3n)) Json::ReturnError('请输入勘测单位职务、职称');
        if (empty($v3d)) Json::ReturnError('请输入勘测单位签字');
        if (empty($v4c)) Json::ReturnError('请输入设计单位单位名称');
        if (empty($v4n)) Json::ReturnError('请输入设计单位职务、职称');
        if (empty($v4d)) Json::ReturnError('请输入设计单位签字');
        if (empty($v5c)) Json::ReturnError('请输入施工单位单位名称');
        if (empty($v5n)) Json::ReturnError('请输入施工单位职务、职称');
        if (empty($v5d)) Json::ReturnError('请输入施工单位签字');

        try {
            $id = Flow72Cls::Add($pid, $attachments, $no, $comp, $signer, $date, $c1, $c2, $c3, $c4, $c5, $c6, $c7, $v1c, $v1n, $v1d, $v2c, $v2n, $v2d, $v3c, $v3n, $v3d, $v4c, $v4n, $v4d, $v5c, $v5n, $v5d);
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }
        ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_2, $id, ProjectStateCls::APPROVE);

        try {
            MsgCls::Add(1, MsgDirectCls::FROM_PROJECT, $this->Mid(), 1, ProjectCls::Instance()->Name($pid), '管理员', ProjectNodeCls::RECORD_2, $id, '新建' . ProjectNodeCls::Name(ProjectNodeCls::RECORD_2));
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        Json::ReturnSuccess();
    }

    public function ProjectReply72View()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow72Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Reply72Cls::GetLastItem($pid, $fid);

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectReply72View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow73List()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow73List');

        $pid = $this->Mid();

        $new = true;
        $rr = array();
        $rl = Flow73Cls::GetLastItem($pid);
        if (!empty($rl) && count($rl) > 0) {
            $new = ProjectStateCls::IsNew(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::RECORD_3));
            if ($rl['replyid'] > 0) $rr = Reply1Cls::GetLastItem($pid, $rl['replyid']);
        }
        $rs = Flow73Cls::GetApprovedItems($pid);

        $view->rl = $rl;
        $view->rr = $rr;
        $view->rs = $rs;
        $view->new = $new;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::RECORD_3);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow73()
    {
        $id = $this->Req('id', 0, 'int');

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow73');

        $pid = $this->Mid();
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow73Cls::Instance()->Item($id);
        else $rs = Flow73Cls::GetLastItem($pid);

        $comp = '';
        $no = '';
        $signer = '';
        $date = '';
        $attachments = '';

        $c1 = '';
        $c2 = '';
        $c3 = '';
        $c4 = '';
        $c5 = '';
        $c6 = '';
        $c7 = '';

        $v1c = '';
        $v1n = '';
        $v1d = '';
        $v2c = '';
        $v2n = '';
        $v2d = '';
        $v3c = '';
        $v3n = '';
        $v3d = '';
        $v4c = '';
        $v4n = '';
        $v4d = '';
        $v5c = '';
        $v5n = '';
        $v5d = '';

        $edit = true;

        if (!empty($rs) && count($rs) > 0) {

            $comp = $rs['comp'];
            $no = $rs['no'];
            $signer = $rs['signer'];
            $date = $rs['date'];
            $attachments = $rs['attachments'];

            $c1 = $rs['c1'];
            $c2 = $rs['c2'];
            $c3 = $rs['c3'];
            $c4 = $rs['c4'];
            $c5 = $rs['c5'];
            $c6 = $rs['c6'];
            $c7 = $rs['c7'];

            $v1c = $rs['v1c'];
            $v1n = $rs['v1n'];
            $v1d = $rs['v1d'];
            $v2c = $rs['v2c'];
            $v2n = $rs['v2n'];
            $v2d = $rs['v2d'];
            $v3c = $rs['v3c'];
            $v3n = $rs['v3n'];
            $v3d = $rs['v3d'];
            $v4c = $rs['v4c'];
            $v4n = $rs['v4n'];
            $v4d = $rs['v4d'];
            $v5c = $rs['v5c'];
            $v5n = $rs['v5n'];
            $v5d = $rs['v5d'];

            $edit = ProjectStateCls::IsEdit(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::RECORD_3));
        }

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->edit = $edit;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::RECORD_3);

        $view->comp = $comp;
        $view->no = $no;
        $view->signer = $signer;
        $view->date = $date;
        $view->attachments = $attachments;

        $view->c1 = $c1;
        $view->c2 = $c2;
        $view->c3 = $c3;
        $view->c4 = $c4;
        $view->c5 = $c5;
        $view->c6 = $c6;
        $view->c7 = $c7;

        $view->v1c = $v1c;
        $view->v1n = $v1n;
        $view->v1d = $v1d;
        $view->v2c = $v2c;
        $view->v2n = $v2n;
        $view->v2d = $v2d;
        $view->v3c = $v3c;
        $view->v3n = $v3n;
        $view->v3d = $v3d;
        $view->v4c = $v4c;
        $view->v4n = $v4n;
        $view->v4d = $v4d;
        $view->v5c = $v5c;
        $view->v5n = $v5n;
        $view->v5d = $v5d;

        $view->pid = $pid;
        $view->atts = Atts::UploadDynamic(AttachmentCls::GetDynamicItems($pid, 73), $edit);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function OnProjectFlow73()
    {
        $no = $this->Req('no', '', 'str');
        $comp = $this->Req('comp', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $date = $this->Req('date', '', 'str');
        $attachments = $this->Req('attachments', '', 'str');

        $c1 = $this->Req('c1', '', 'str');
        $c2 = $this->Req('c2', '', 'str');
        $c3 = $this->Req('c3', '', 'str');
        $c4 = $this->Req('c4', '', 'str');
        $c5 = $this->Req('c5', '', 'str');
        $c6 = $this->Req('c6', '', 'str');
        $c7 = $this->Req('c7', '', 'str');

        $v1c = $this->Req('v1c', '', 'str');
        $v1n = $this->Req('v1n', '', 'str');
        $v1d = $this->Req('v1d', '', 'str');
        $v2c = $this->Req('v2c', '', 'str');
        $v2n = $this->Req('v2n', '', 'str');
        $v2d = $this->Req('v2d', '', 'str');
        $v3c = $this->Req('v3c', '', 'str');
        $v3n = $this->Req('v3n', '', 'str');
        $v3d = $this->Req('v3d', '', 'str');
        $v4c = $this->Req('v4c', '', 'str');
        $v4n = $this->Req('v4n', '', 'str');
        $v4d = $this->Req('v4d', '', 'str');
        $v5c = $this->Req('v5c', '', 'str');
        $v5n = $this->Req('v5n', '', 'str');
        $v5d = $this->Req('v5d', '', 'str');

        $pid = $this->Mid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($attachments)) Json::ReturnError('请输入单位工程名称');
        if (empty($no)) Json::ReturnError('请输入编号');
        if (empty($comp)) Json::ReturnError('请输入缺陷名称');
        if (empty($signer)) Json::ReturnError('请输入缺陷部位');
        if (empty($date)) Json::ReturnError('请输入备案日期');

        if (empty($c1)) Json::ReturnError('请输入质量缺陷产生的部位与特征');
        if (empty($c2)) Json::ReturnError('请输入质量缺陷产生的主要原因');
        if (empty($c3)) Json::ReturnError('请输入对工程的安全、功能和运用影响分析');
        if (empty($c4)) Json::ReturnError('请输入处理方案与或不处理原因');
        if (empty($c5)) Json::ReturnError('请输入保留意见');

        if (empty($v1c)) Json::ReturnError('请输入施工单位名称');
        if (empty($v1n)) Json::ReturnError('请输入质检部门负责人');
        if (empty($v1d)) Json::ReturnError('请输入技术负责人');
        if (empty($v2c)) Json::ReturnError('请输入设计单位名称');
        if (empty($v2n)) Json::ReturnError('请输入设计代表');
        if (empty($v3c)) Json::ReturnError('请输入监理单位名称');
        if (empty($v3n)) Json::ReturnError('请输入监理工程师');
        if (empty($v3d)) Json::ReturnError('请输入总监理工程师');
        if (empty($v4c)) Json::ReturnError('请输入项目法人名称');
        if (empty($v4n)) Json::ReturnError('请输入现场代表');
        if (empty($v4d)) Json::ReturnError('请输入技术负责人');

        try {
            $id = Flow73Cls::Add($pid, $attachments, $no, $comp, $signer, $date, $c1, $c2, $c3, $c4, $c5, $c6, $c7, $v1c, $v1n, $v1d, $v2c, $v2n, $v2d, $v3c, $v3n, $v3d, $v4c, $v4n, $v4d, $v5c, $v5n, $v5d);
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }
        ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_3, $id, ProjectStateCls::APPROVE);

        try {
            MsgCls::Add(1, MsgDirectCls::FROM_PROJECT, $this->Mid(), 1, ProjectCls::Instance()->Name($pid), '管理员', ProjectNodeCls::RECORD_3, $id, '新建' . ProjectNodeCls::Name(ProjectNodeCls::RECORD_3));
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        Json::ReturnSuccess();
    }

    public function ProjectReply73View()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow73Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Reply73Cls::GetLastItem($pid, $fid);

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectReply73View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow74List()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow74List');

        $pid = $this->Mid();

        $new = true;
        $rr = array();
        $rl = Flow74Cls::GetLastItem($pid);
        if (!empty($rl) && count($rl) > 0) {
            $new = ProjectStateCls::IsNew(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::RECORD_4));
            if ($rl['replyid'] > 0) $rr = Reply1Cls::GetLastItem($pid, $rl['replyid']);
        }
        $rs = Flow74Cls::GetApprovedItems($pid);

        $view->rl = $rl;
        $view->rr = $rr;
        $view->rs = $rs;
        $view->new = $new;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::RECORD_4);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow74()
    {
        $id = $this->Req('id', 0, 'int');

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow74');

        $pid = $this->Mid();
        $gc = ProjectCls::GetGroupCompany($pid);

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow74Cls::Instance()->Item($id);
        else $rs = Flow74Cls::GetLastItem($pid);

        $no = '';
        $signer = '';
        $content = '';
        $date = '';
        $keywords = '';

        $edit = true;

        if (!empty($rs) && count($rs) > 0) {

            $no = $rs['no'];
            $signer = $rs['signer'];
            $content = $rs['content'];
            $date = $rs['date'];
            $keywords = $rs['keywords'];

            $edit = ProjectStateCls::IsEdit(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::RECORD_4));
        }

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->edit = $edit;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::RECORD_4);

        $view->no = $no;
        $view->signer = $signer;
        $view->content = $content;
        $view->date = $date;
        $view->keywords = $keywords;

        $view->pid = $pid;
        $view->atts = Atts::UploadDynamic(AttachmentCls::GetDynamicItems($pid, 74), $edit);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function OnProjectFlow74()
    {
        $no = $this->Req('no', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $keywords = $this->Req('keywords', '', 'str');
        $attachments = $this->Req('attachments', '', 'str');

        $pid = $this->Mid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
//        if (empty($no)) Json::ReturnError('请输入文件编号');
//        if (empty($signer)) Json::ReturnError('请输入签发单位');
//        if (empty($content)) Json::ReturnError('请输入申报内容');
//        if (empty($date)) Json::ReturnError('请输入申报日期');

        $id = Flow74Cls::Add($pid, $no, $signer, $content, $date, $keywords, $attachments);
        ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_4, $id, ProjectStateCls::APPROVE);

        try {
            MsgCls::Add(1, MsgDirectCls::FROM_PROJECT, $this->Mid(), 1, ProjectCls::Instance()->Name($pid), '管理员', ProjectNodeCls::RECORD_4, $id, '新建' . ProjectNodeCls::Name(ProjectNodeCls::RECORD_4));
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        Json::ReturnSuccess();
    }

    public function ProjectReply74View()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow74Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Reply74Cls::GetLastItem($pid, $fid);

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectReply74View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow75List()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow75List');

        $pid = $this->Mid();

        $new = true;
        $rr = array();
        $rl = Flow75Cls::GetLastItem($pid);
        if (!empty($rl) && count($rl) > 0) {
            $new = ProjectStateCls::IsNew(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::RECORD_5));
            if ($rl['replyid'] > 0) $rr = Reply1Cls::GetLastItem($pid, $rl['replyid']);
        }
        $rs = Flow75Cls::GetApprovedItems($pid);

        $view->rl = $rl;
        $view->rr = $rr;
        $view->rs = $rs;
        $view->new = $new;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::RECORD_5);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow75()
    {
        $id = $this->Req('id', 0, 'int');

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow75');

        $pid = $this->Mid();
        $gc = ProjectCls::GetGroupCompany($pid);

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow75Cls::Instance()->Item($id);
        else $rs = Flow75Cls::GetLastItem($pid);

        $no = '';
        $signer = '';
        $content = '';
        $date = '';
        $keywords = '';

        $edit = true;

        if (!empty($rs) && count($rs) > 0) {

            $no = $rs['no'];
            $signer = $rs['signer'];
            $content = $rs['content'];
            $date = $rs['date'];
            $keywords = $rs['keywords'];

            $edit = ProjectStateCls::IsEdit(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::RECORD_5));
        }

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->edit = $edit;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::RECORD_5);

        $view->no = $no;
        $view->signer = $signer;
        $view->content = $content;
        $view->date = $date;
        $view->keywords = $keywords;

        $view->pid = $pid;
        $view->atts = Atts::UploadDynamic(AttachmentCls::GetDynamicItems($pid, 75), $edit);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function OnProjectFlow75()
    {
        $no = $this->Req('no', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $keywords = $this->Req('keywords', '', 'str');
        $attachments = $this->Req('attachments', '', 'str');

        $pid = $this->Mid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
//        if (empty($no)) Json::ReturnError('请输入文件编号');
//        if (empty($signer)) Json::ReturnError('请输入签发单位');
//        if (empty($content)) Json::ReturnError('请输入申报内容');
//        if (empty($date)) Json::ReturnError('请输入申报日期');

        $id = Flow75Cls::Add($pid, $no, $signer, $content, $date, $keywords, $attachments);
        ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_5, $id, ProjectStateCls::APPROVE);

        try {
            MsgCls::Add(1, MsgDirectCls::FROM_PROJECT, $this->Mid(), 1, ProjectCls::Instance()->Name($pid), '管理员', ProjectNodeCls::RECORD_5, $id, '新建' . ProjectNodeCls::Name(ProjectNodeCls::RECORD_5));
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        Json::ReturnSuccess();
    }

    public function ProjectReply75View()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow75Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Reply75Cls::GetLastItem($pid, $fid);

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectReply75View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow76List()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow76List');

        $pid = $this->Mid();

        $new = true;
        $rr = array();
        $rl = Flow76Cls::GetLastItem($pid);
        if (!empty($rl) && count($rl) > 0) {
            $new = ProjectStateCls::IsNew(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::RECORD_6));
            if ($rl['replyid'] > 0) $rr = Reply1Cls::GetLastItem($pid, $rl['replyid']);
        }
        $rs = Flow76Cls::GetApprovedItems($pid);

        $view->rl = $rl;
        $view->rr = $rr;
        $view->rs = $rs;
        $view->new = $new;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::RECORD_6);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow76()
    {
        $id = $this->Req('id', 0, 'int');

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow76');

        $pid = $this->Mid();
        $gc = ProjectCls::GetGroupCompany($pid);

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow76Cls::Instance()->Item($id);
        else $rs = Flow76Cls::GetLastItem($pid);

        $no = '';
        $signer = '';
        $content = '';
        $date = '';
        $keywords = '';

        $edit = true;

        if (!empty($rs) && count($rs) > 0) {

            $no = $rs['no'];
            $signer = $rs['signer'];
            $content = $rs['content'];
            $date = $rs['date'];
            $keywords = $rs['keywords'];

            $edit = ProjectStateCls::IsEdit(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::RECORD_6));
        }

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->edit = $edit;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::RECORD_6);

        $view->no = $no;
        $view->signer = $signer;
        $view->content = $content;
        $view->date = $date;
        $view->keywords = $keywords;

        $view->pid = $pid;
        $view->atts = Atts::UploadDynamic(AttachmentCls::GetDynamicItems($pid, 76), $edit);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function OnProjectFlow76()
    {
        $no = $this->Req('no', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $keywords = $this->Req('keywords', '', 'str');
        $attachments = $this->Req('attachments', '', 'str');

        $pid = $this->Mid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
//        if (empty($no)) Json::ReturnError('请输入文件编号');
//        if (empty($signer)) Json::ReturnError('请输入签发单位');
//        if (empty($content)) Json::ReturnError('请输入申报内容');
//        if (empty($date)) Json::ReturnError('请输入申报日期');

        $id = Flow76Cls::Add($pid, $no, $signer, $content, $date, $keywords, $attachments);
        ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_6, $id, ProjectStateCls::APPROVE);

        try {
            MsgCls::Add(1, MsgDirectCls::FROM_PROJECT, $this->Mid(), 1, ProjectCls::Instance()->Name($pid), '管理员', ProjectNodeCls::RECORD_6, $id, '新建' . ProjectNodeCls::Name(ProjectNodeCls::RECORD_6));
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        Json::ReturnSuccess();
    }

    public function ProjectReply76View()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow76Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Reply76Cls::GetLastItem($pid, $fid);

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectReply76View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow8List()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow8List');

        $pid = $this->Mid();

        $new = true;
        $rr = array();
        $rl = Flow8Cls::GetLastItem($pid);
        if (!empty($rl) && count($rl) > 0) {
            $new = ProjectStateCls::IsNew(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::PROGRESS));
            if ($rl['replyid'] > 0) $rr = Reply1Cls::GetLastItem($pid, $rl['replyid']);
        }
        $rs = Flow8Cls::GetApprovedItems($pid);

        $view->rl = $rl;
        $view->rr = $rr;
        $view->rs = $rs;
        $view->new = $new;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::PROGRESS);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow8()
    {
        $id = $this->Req('id', 0, 'int');

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow8');

        $pid = $this->Mid();
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow8Cls::Instance()->Item($id);
        else $rs = Flow8Cls::GetLastItem($pid);

        $comp = '';
        $no = '';
        $signer = '';
        $date = '';

        $items = array(array('地基验槽签证', '', ''), array('大型工程混凝土签证', '', ''), array('墙后回填土前', '', ''), array('各项验收前时间', '', ''));

        $edit = true;

        if (!empty($rs) && count($rs) > 0) {

            $comp = $rs['comp'];
            $no = $rs['no'];
            $signer = $rs['signer'];
            $date = $rs['date'];

            $items = Json::Decode($rs['items']);

            $edit = ProjectStateCls::IsEdit(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::PROGRESS));
        }

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->edit = $edit;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::PROGRESS);

        $view->comp = $comp;
        $view->no = $no;
        $view->signer = $signer;
        $view->date = $date;

        $view->items = $items;

        $view->pid = $pid;
        $view->atts = Atts::UploadDynamic(AttachmentCls::GetDynamicItems($pid, 8), $edit);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function OnProjectFlow8()
    {
        $no = $this->Req('no', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $date = $this->Req('date', '', 'str');

        $items1 = $this->Req('items1', array(), 'array');
        $items2 = $this->Req('items2', array(), 'array');
        $items3 = $this->Req('items3', array(), 'array');

        $items = array();
        $num1 = count($items1);
        $num2 = count($items2);
        $num3 = count($items3);

        if ($num1 != $num2 || $num1 != $num3) Json::ReturnError(ALERT_ERROR);
        if ($num1 <= 0) Json::ReturnError('请至少添加一个条目');
        for ($i = 0; $i < $num1; $i++) {
            if (empty($items1[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的节点内容');
            if (empty($items2[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的时间');
            if (empty($items3[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的备注');
            $items[$i] = array($items1[$i], $items2[$i], $items3[$i]);
        }

        $pid = $this->Mid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);

        $items = Json::Encode($items);
        $totals = '';

        try {
            $id = Flow8Cls::Add($pid, '', $no, $signer, $date, $items, $totals);
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }
        ProjectCls::SetNode($pid, ProjectNodeCls::PROGRESS, $id, ProjectStateCls::APPROVE);

        try {
            MsgCls::Add(1, MsgDirectCls::FROM_PROJECT, $this->Mid(), 1, ProjectCls::Instance()->Name($pid), '管理员', ProjectNodeCls::PROGRESS, $id, '新建' . ProjectNodeCls::Name(ProjectNodeCls::PROGRESS));
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        Json::ReturnSuccess();
    }

    public function ProjectReply8View()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow8Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Reply8Cls::GetLastItem($pid, $fid);

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectReply8View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow91List()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow91List');

        $pid = $this->Mid();

        $new = true;
        $rr = array();
        $rl = Flow91Cls::GetLastItem($pid);
        if (!empty($rl) && count($rl) > 0) {
            $new = ProjectStateCls::IsNew(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::ACCEPT_1));
            if ($rl['replyid'] > 0) $rr = Reply1Cls::GetLastItem($pid, $rl['replyid']);
        }
        $rs = Flow91Cls::GetApprovedItems($pid);

        $view->rl = $rl;
        $view->rr = $rr;
        $view->rs = $rs;
        $view->new = $new;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::ACCEPT_1);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow91()
    {
        $id = $this->Req('id', 0, 'int');

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow91');

        $pid = $this->Mid();
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow91Cls::Instance()->Item($id);
        else $rs = Flow91Cls::GetLastItem($pid);

        $comp = '';
        $no = '';
        $signer = '';
        $date = '';

        $items = array(array('施工围堰验收', '', ''), array('大型重要分部验收', '', ''), array('单位工程验收', '', ''), array('合同工程完工验收', '', ''));

        $edit = true;

        if (!empty($rs) && count($rs) > 0) {

            $comp = $rs['comp'];
            $no = $rs['no'];
            $signer = $rs['signer'];
            $date = $rs['date'];

            $items = Json::Decode($rs['items']);

            $edit = ProjectStateCls::IsEdit(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::ACCEPT_1));
        }

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->edit = $edit;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::ACCEPT_1);

        $view->comp = $comp;
        $view->no = $no;
        $view->signer = $signer;
        $view->date = $date;

        $view->items = $items;

        $view->pid = $pid;
        $view->atts = Atts::UploadDynamic(AttachmentCls::GetDynamicItems($pid, 91), $edit);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function OnProjectFlow91()
    {
        $no = $this->Req('no', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $date = $this->Req('date', '', 'str');

        $items1 = $this->Req('items1', array(), 'array');
        $items2 = $this->Req('items2', array(), 'array');
        $items3 = $this->Req('items3', array(), 'array');

        $items = array();
        $num1 = count($items1);
        $num2 = count($items2);
        $num3 = count($items3);

        if ($num1 != $num2 || $num1 != $num3) Json::ReturnError(ALERT_ERROR);
        if ($num1 <= 0) Json::ReturnError('请至少添加一个条目');
        for ($i = 0; $i < $num1; $i++) {
            if (empty($items1[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的验收内容');
            if (empty($items2[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的时间');
            if (empty($items3[$i])) Json::ReturnError('请输入序号' . ($i + 1) . '条目的备注');
            $items[$i] = array($items1[$i], $items2[$i], $items3[$i]);
        }

        $pid = $this->Mid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);

        $items = Json::Encode($items);
        $totals = '';

        try {
            $id = Flow91Cls::Add($pid, '', $no, $signer, $date, $items, $totals);
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }
        ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_1, $id, ProjectStateCls::APPROVE);

        try {
            MsgCls::Add(1, MsgDirectCls::FROM_PROJECT, $this->Mid(), 1, ProjectCls::Instance()->Name($pid), '管理员', ProjectNodeCls::ACCEPT_1, $id, '新建' . ProjectNodeCls::Name(ProjectNodeCls::ACCEPT_1));
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        Json::ReturnSuccess();
    }

    public function ProjectReply91View()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow91Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Reply91Cls::GetLastItem($pid, $fid);

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectReply91View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow921List()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow921List');

        $pid = $this->Mid();

        $new = true;
        $rr = array();
        $rl = Flow921Cls::GetLastItem($pid);
        if (!empty($rl) && count($rl) > 0) {
            $new = ProjectStateCls::IsNew(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::ACCEPT_21));
            if ($rl['replyid'] > 0) $rr = Reply1Cls::GetLastItem($pid, $rl['replyid']);
        }
        $rs = Flow921Cls::GetApprovedItems($pid);

        $view->rl = $rl;
        $view->rr = $rr;
        $view->rs = $rs;
        $view->new = $new;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::ACCEPT_21);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow921()
    {
        $id = $this->Req('id', 0, 'int');

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow921');

        $pid = $this->Mid();
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow921Cls::Instance()->Item($id);
        else $rs = Flow921Cls::GetLastItem($pid);

        $tno = '';
        $tcontent = '';
        $tdate = '';
        $tattachments = '';
        $tmemo = '';

        $no = '';
        $proj = '';
        $stage = '';
        $signer = '';
        $owner = '';
        $comp = '';
        $date = '';

        $c1 = '';
        $c2 = '';
        $c3 = '';
        $c4 = '';
        $c5 = '';
        $c6 = '';
        $c7 = '';
        $c8 = '';

        $edit = true;

        if (!empty($rs) && count($rs) > 0) {

            $tno = $rs['tno'];
            $tcontent = $rs['tcontent'];
            $tdate = $rs['tdate'];
            $tattachments = $rs['tattachments'];
            $tmemo = $rs['tmemo'];

            $no = $rs['no'];
            $proj = $rs['proj'];
            $stage = $rs['stage'];
            $signer = $rs['signer'];
            $owner = $rs['owner'];
            $comp = $rs['comp'];
            $date = $rs['date'];

            $c1 = $rs['c1'];
            $c2 = $rs['c2'];
            $c3 = $rs['c3'];
            $c4 = $rs['c4'];
            $c5 = $rs['c5'];
            $c6 = $rs['c6'];
            $c7 = $rs['c7'];
            $c8 = $rs['c8'];

            $edit = ProjectStateCls::IsEdit(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::ACCEPT_21));
        }

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->edit = $edit;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::ACCEPT_21);

        $view->tno = $tno;
        $view->tcontent = $tcontent;
        $view->tdate = $tdate;
        $view->tattachments = $tattachments;
        $view->tmemo = $tmemo;

        $view->no = $no;
        $view->proj = $proj;
        $view->stage = $stage;
        $view->signer = $signer;
        $view->owner = $owner;
        $view->comp = $comp;
        $view->date = $date;

        $view->c1 = $c1;
        $view->c2 = $c2;
        $view->c3 = $c3;
        $view->c4 = $c4;
        $view->c5 = $c5;
        $view->c6 = $c6;
        $view->c7 = $c7;
        $view->c8 = $c8;

        $view->pid = $pid;
        $view->atts = Atts::UploadFixed(Atts::$flow921, AttachmentCls::GetFixedItems($pid, 921), $edit);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function OnProjectFlow921()
    {
        $tno = $this->Req('tno', '', 'str');
        $tcontent = $this->Req('tcontent', '', 'str');
        $tdate = $this->Req('tdate', '', 'str');
        $tattachments = $this->Req('tattachments', '', 'str');
        $tmemo = $this->Req('tmemo', '', 'str');

        $no = $this->Req('no', '', 'str');
        $proj = $this->Req('proj', '', 'str');
        $stage = $this->Req('stage', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $owner = $this->Req('owner', '', 'str');
        $comp = $this->Req('comp', '', 'str');
        $date = $this->Req('date', '', 'str');

        $c1 = $this->Req('c1', '', 'str');
        $c2 = $this->Req('c2', '', 'str');
        $c3 = $this->Req('c3', '', 'str');
        $c4 = $this->Req('c4', '', 'str');
        $c5 = $this->Req('c5', '', 'str');
        $c6 = $this->Req('c6', '', 'str');
        $c7 = $this->Req('c7', '', 'str');
        $c8 = $this->Req('c8', '', 'str');

        $pid = $this->Mid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($tno)) Json::ReturnError('请输入文件编号');
        if (empty($tcontent)) Json::ReturnError('请输入文件内容');
        if (empty($tdate)) Json::ReturnError('请输入文件日期');

        if (empty($proj)) Json::ReturnError('请输入工程');
        if (empty($stage)) Json::ReturnError('请输入阶段');
        if (empty($signer)) Json::ReturnError('请输入编制');
        if (empty($owner)) Json::ReturnError('请输入项目法人');
        if (empty($comp)) Json::ReturnError('请输入工程建设项目法人名称');
        if (empty($date)) Json::ReturnError('请输入日期');

        if (empty($c1)) Json::ReturnError('请输入工程概况');
        if (empty($c2)) Json::ReturnError('请输入主要设计变更');
        if (empty($c3)) Json::ReturnError('请输入质量管理工作');
        if (empty($c4)) Json::ReturnError('请输入质量检测情况');
        if (empty($c5)) Json::ReturnError('请输入存在问题及处理情况');
        if (empty($c6)) Json::ReturnError('请输入遗留问题和尾工处理');
        if (empty($c7)) Json::ReturnError('请输入工程质量的自评、复核、认定结果');

        try {
            $id = Flow921Cls::Add($pid, $tno, $tcontent, $tdate, $tattachments, $tmemo, $no, $proj, $stage, $signer, $owner, $comp, $date, $c1, $c2, $c3, $c4, $c5, $c6, $c7, $c8);
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }
        ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_21, $id, ProjectStateCls::APPROVE);

        try {
            MsgCls::Add(1, MsgDirectCls::FROM_PROJECT, $this->Mid(), 1, ProjectCls::Instance()->Name($pid), '管理员', ProjectNodeCls::ACCEPT_21, $id, '新建' . ProjectNodeCls::Name(ProjectNodeCls::ACCEPT_21));
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        Json::ReturnSuccess();
    }

    public function ProjectReply921View()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow921Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Reply921Cls::GetLastItem($pid, $fid);

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectReply921View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow922List()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow922List');

        $pid = $this->Mid();

        $new = true;
        $rr = array();
        $rl = Flow922Cls::GetLastItem($pid);
        if (!empty($rl) && count($rl) > 0) {
            $new = ProjectStateCls::IsNew(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::ACCEPT_22));
            if ($rl['replyid'] > 0) $rr = Reply1Cls::GetLastItem($pid, $rl['replyid']);
        }
        $rs = Flow922Cls::GetApprovedItems($pid);

        $view->rl = $rl;
        $view->rr = $rr;
        $view->rs = $rs;
        $view->new = $new;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::ACCEPT_22);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow922()
    {
        $id = $this->Req('id', 0, 'int');

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow922');

        $pid = $this->Mid();
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow922Cls::Instance()->Item($id);
        else $rs = Flow922Cls::GetLastItem($pid);

        $tno = '';
        $tcontent = '';
        $tdate = '';
        $tattachments = '';
        $tmemo = '';

        $no = '';
        $proj = '';
        $stage = '';
        $signer = '';
        $owner = '';
        $comp = '';
        $date = '';

        $c1 = '';
        $c2 = '';
        $c3 = '';
        $c4 = '';
        $c5 = '';
        $c6 = '';
        $c7 = '';
        $c8 = '';

        $edit = true;

        if (!empty($rs) && count($rs) > 0) {

            $tno = $rs['tno'];
            $tcontent = $rs['tcontent'];
            $tdate = $rs['tdate'];
            $tattachments = $rs['tattachments'];
            $tmemo = $rs['tmemo'];

            $no = $rs['no'];
            $proj = $rs['proj'];
            $stage = $rs['stage'];
            $signer = $rs['signer'];
            $owner = $rs['owner'];
            $comp = $rs['comp'];
            $date = $rs['date'];

            $c1 = $rs['c1'];
            $c2 = $rs['c2'];
            $c3 = $rs['c3'];
            $c4 = $rs['c4'];
            $c5 = $rs['c5'];
            $c6 = $rs['c6'];
            $c7 = $rs['c7'];
            $c8 = $rs['c8'];

            $edit = ProjectStateCls::IsEdit(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::ACCEPT_22));
        }

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->edit = $edit;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::ACCEPT_22);

        $view->tno = $tno;
        $view->tcontent = $tcontent;
        $view->tdate = $tdate;
        $view->tattachments = $tattachments;
        $view->tmemo = $tmemo;

        $view->no = $no;
        $view->proj = $proj;
        $view->stage = $stage;
        $view->signer = $signer;
        $view->owner = $owner;
        $view->comp = $comp;
        $view->date = $date;

        $view->c1 = $c1;
        $view->c2 = $c2;
        $view->c3 = $c3;
        $view->c4 = $c4;
        $view->c5 = $c5;
        $view->c6 = $c6;
        $view->c7 = $c7;
        $view->c8 = $c8;

        $view->pid = $pid;
        $view->atts = Atts::UploadFixed(Atts::$flow922, AttachmentCls::GetFixedItems($pid, 922), $edit);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function OnProjectFlow922()
    {
        $tno = $this->Req('tno', '', 'str');
        $tcontent = $this->Req('tcontent', '', 'str');
        $tdate = $this->Req('tdate', '', 'str');
        $tattachments = $this->Req('tattachments', '', 'str');
        $tmemo = $this->Req('tmemo', '', 'str');

        $no = $this->Req('no', '', 'str');
        $proj = $this->Req('proj', '', 'str');
        $stage = $this->Req('stage', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $owner = $this->Req('owner', '', 'str');
        $comp = $this->Req('comp', '', 'str');
        $date = $this->Req('date', '', 'str');

        $c1 = $this->Req('c1', '', 'str');
        $c2 = $this->Req('c2', '', 'str');
        $c3 = $this->Req('c3', '', 'str');
        $c4 = $this->Req('c4', '', 'str');
        $c5 = $this->Req('c5', '', 'str');
        $c6 = $this->Req('c6', '', 'str');
        $c7 = $this->Req('c7', '', 'str');
        $c8 = $this->Req('c8', '', 'str');

        $pid = $this->Mid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($tno)) Json::ReturnError('请输入文件编号');
        if (empty($tcontent)) Json::ReturnError('请输入文件内容');
        if (empty($tdate)) Json::ReturnError('请输入文件日期');

        if (empty($proj)) Json::ReturnError('请输入工程');
        if (empty($stage)) Json::ReturnError('请输入阶段');
        if (empty($signer)) Json::ReturnError('请输入编制');
        if (empty($owner)) Json::ReturnError('请输入项目法人');
        if (empty($comp)) Json::ReturnError('请输入工程建设项目法人名称');
        if (empty($date)) Json::ReturnError('请输入日期');

        if (empty($c1)) Json::ReturnError('请输入工程概况');
        if (empty($c2)) Json::ReturnError('请输入主要设计变更');
        if (empty($c3)) Json::ReturnError('请输入质量管理工作');
        if (empty($c4)) Json::ReturnError('请输入质量检测情况');
        if (empty($c5)) Json::ReturnError('请输入存在问题及处理情况');
        if (empty($c6)) Json::ReturnError('请输入遗留问题和尾工处理');
        if (empty($c7)) Json::ReturnError('请输入工程质量的自评、复核、认定结果');

        try {
            $id = Flow922Cls::Add($pid, $tno, $tcontent, $tdate, $tattachments, $tmemo, $no, $proj, $stage, $signer, $owner, $comp, $date, $c1, $c2, $c3, $c4, $c5, $c6, $c7, $c8);
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }
        ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_22, $id, ProjectStateCls::APPROVE);

        try {
            MsgCls::Add(1, MsgDirectCls::FROM_PROJECT, $this->Mid(), 1, ProjectCls::Instance()->Name($pid), '管理员', ProjectNodeCls::ACCEPT_22, $id, '新建' . ProjectNodeCls::Name(ProjectNodeCls::ACCEPT_22));
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        Json::ReturnSuccess();
    }

    public function ProjectReply922View()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow922Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Reply922Cls::GetLastItem($pid, $fid);

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectReply922View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow923List()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow923List');

        $pid = $this->Mid();

        $new = true;
        $rr = array();
        $rl = Flow923Cls::GetLastItem($pid);
        if (!empty($rl) && count($rl) > 0) {
            $new = ProjectStateCls::IsNew(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::ACCEPT_23));
            if ($rl['replyid'] > 0) $rr = Reply1Cls::GetLastItem($pid, $rl['replyid']);
        }
        $rs = Flow923Cls::GetApprovedItems($pid);

        $view->rl = $rl;
        $view->rr = $rr;
        $view->rs = $rs;
        $view->new = $new;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::ACCEPT_23);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow923()
    {
        $id = $this->Req('id', 0, 'int');

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow923');

        $pid = $this->Mid();
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow923Cls::Instance()->Item($id);
        else $rs = Flow923Cls::GetLastItem($pid);

        $tno = '';
        $tcontent = '';
        $tdate = '';
        $tattachments = '';
        $tmemo = '';

        $no = '';
        $proj = '';
        $stage = '';
        $signer = '';
        $owner = '';
        $comp = '';
        $date = '';

        $c1 = '';
        $c2 = '';
        $c3 = '';
        $c4 = '';
        $c5 = '';
        $c6 = '';
        $c7 = '';
        $c8 = '';

        $edit = true;

        if (!empty($rs) && count($rs) > 0) {

            $tno = $rs['tno'];
            $tcontent = $rs['tcontent'];
            $tdate = $rs['tdate'];
            $tattachments = $rs['tattachments'];
            $tmemo = $rs['tmemo'];

            $no = $rs['no'];
            $proj = $rs['proj'];
            $stage = $rs['stage'];
            $signer = $rs['signer'];
            $owner = $rs['owner'];
            $comp = $rs['comp'];
            $date = $rs['date'];

            $c1 = $rs['c1'];
            $c2 = $rs['c2'];
            $c3 = $rs['c3'];
            $c4 = $rs['c4'];
            $c5 = $rs['c5'];
            $c6 = $rs['c6'];
            $c7 = $rs['c7'];
            $c8 = $rs['c8'];

            $edit = ProjectStateCls::IsEdit(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::ACCEPT_23));
        }

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->edit = $edit;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::ACCEPT_23);

        $view->tno = $tno;
        $view->tcontent = $tcontent;
        $view->tdate = $tdate;
        $view->tattachments = $tattachments;
        $view->tmemo = $tmemo;

        $view->no = $no;
        $view->proj = $proj;
        $view->stage = $stage;
        $view->signer = $signer;
        $view->owner = $owner;
        $view->comp = $comp;
        $view->date = $date;

        $view->c1 = $c1;
        $view->c2 = $c2;
        $view->c3 = $c3;
        $view->c4 = $c4;
        $view->c5 = $c5;
        $view->c6 = $c6;
        $view->c7 = $c7;
        $view->c8 = $c8;

        $view->pid = $pid;
        $view->atts = Atts::UploadFixed(Atts::$flow923, AttachmentCls::GetFixedItems($pid, 923), $edit);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function OnProjectFlow923()
    {
        $tno = $this->Req('tno', '', 'str');
        $tcontent = $this->Req('tcontent', '', 'str');
        $tdate = $this->Req('tdate', '', 'str');
        $tattachments = $this->Req('tattachments', '', 'str');
        $tmemo = $this->Req('tmemo', '', 'str');

        $no = $this->Req('no', '', 'str');
        $proj = $this->Req('proj', '', 'str');
        $stage = $this->Req('stage', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $owner = $this->Req('owner', '', 'str');
        $comp = $this->Req('comp', '', 'str');
        $date = $this->Req('date', '', 'str');

        $c1 = $this->Req('c1', '', 'str');
        $c2 = $this->Req('c2', '', 'str');
        $c3 = $this->Req('c3', '', 'str');
        $c4 = $this->Req('c4', '', 'str');
        $c5 = $this->Req('c5', '', 'str');
        $c6 = $this->Req('c6', '', 'str');
        $c7 = $this->Req('c7', '', 'str');
        $c8 = $this->Req('c8', '', 'str');

        $pid = $this->Mid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($tno)) Json::ReturnError('请输入文件编号');
        if (empty($tcontent)) Json::ReturnError('请输入文件内容');
        if (empty($tdate)) Json::ReturnError('请输入文件日期');

        if (empty($proj)) Json::ReturnError('请输入工程');
        if (empty($stage)) Json::ReturnError('请输入阶段');
        if (empty($signer)) Json::ReturnError('请输入编制');
        if (empty($owner)) Json::ReturnError('请输入项目法人');
        if (empty($comp)) Json::ReturnError('请输入工程建设项目法人名称');
        if (empty($date)) Json::ReturnError('请输入日期');

        if (empty($c1)) Json::ReturnError('请输入工程概况');
        if (empty($c2)) Json::ReturnError('请输入主要设计变更');
        if (empty($c3)) Json::ReturnError('请输入质量管理工作');
        if (empty($c4)) Json::ReturnError('请输入质量检测情况');
        if (empty($c5)) Json::ReturnError('请输入存在问题及处理情况');
        if (empty($c6)) Json::ReturnError('请输入遗留问题和尾工处理');
        if (empty($c7)) Json::ReturnError('请输入工程质量的自评、复核、认定结果');

        try {
            $id = Flow923Cls::Add($pid, $tno, $tcontent, $tdate, $tattachments, $tmemo, $no, $proj, $stage, $signer, $owner, $comp, $date, $c1, $c2, $c3, $c4, $c5, $c6, $c7, $c8);
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }
        ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_23, $id, ProjectStateCls::APPROVE);

        try {
            MsgCls::Add(1, MsgDirectCls::FROM_PROJECT, $this->Mid(), 1, ProjectCls::Instance()->Name($pid), '管理员', ProjectNodeCls::ACCEPT_23, $id, '新建' . ProjectNodeCls::Name(ProjectNodeCls::ACCEPT_23));
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        Json::ReturnSuccess();
    }

    public function ProjectReply923View()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow923Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Reply923Cls::GetLastItem($pid, $fid);

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectReply923View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow924List()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow924List');

        $pid = $this->Mid();

        $new = true;
        $rr = array();
        $rl = Flow924Cls::GetLastItem($pid);
        if (!empty($rl) && count($rl) > 0) {
            $new = ProjectStateCls::IsNew(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::ACCEPT_24));
            if ($rl['replyid'] > 0) $rr = Reply1Cls::GetLastItem($pid, $rl['replyid']);
        }
        $rs = Flow924Cls::GetApprovedItems($pid);

        $view->rl = $rl;
        $view->rr = $rr;
        $view->rs = $rs;
        $view->new = $new;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::ACCEPT_24);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow924()
    {
        $id = $this->Req('id', 0, 'int');

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow924');

        $pid = $this->Mid();
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow924Cls::Instance()->Item($id);
        else $rs = Flow924Cls::GetLastItem($pid);

        $tno = '';
        $tcontent = '';
        $tdate = '';
        $tattachments = '';
        $tmemo = '';

        $no = '';
        $proj = '';
        $stage = '';
        $signer = '';
        $owner = '';
        $comp = '';
        $date = '';

        $c1 = '';
        $c2 = '';
        $c3 = '';
        $c4 = '';
        $c5 = '';
        $c6 = '';
        $c7 = '';
        $c8 = '';

        $edit = true;

        if (!empty($rs) && count($rs) > 0) {

            $tno = $rs['tno'];
            $tcontent = $rs['tcontent'];
            $tdate = $rs['tdate'];
            $tattachments = $rs['tattachments'];
            $tmemo = $rs['tmemo'];

            $no = $rs['no'];
            $proj = $rs['proj'];
            $stage = $rs['stage'];
            $signer = $rs['signer'];
            $owner = $rs['owner'];
            $comp = $rs['comp'];
            $date = $rs['date'];

            $c1 = $rs['c1'];
            $c2 = $rs['c2'];
            $c3 = $rs['c3'];
            $c4 = $rs['c4'];
            $c5 = $rs['c5'];
            $c6 = $rs['c6'];
            $c7 = $rs['c7'];
            $c8 = $rs['c8'];

            $edit = ProjectStateCls::IsEdit(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::ACCEPT_24));
        }

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->edit = $edit;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::ACCEPT_24);

        $view->tno = $tno;
        $view->tcontent = $tcontent;
        $view->tdate = $tdate;
        $view->tattachments = $tattachments;
        $view->tmemo = $tmemo;

        $view->no = $no;
        $view->proj = $proj;
        $view->stage = $stage;
        $view->signer = $signer;
        $view->owner = $owner;
        $view->comp = $comp;
        $view->date = $date;

        $view->c1 = $c1;
        $view->c2 = $c2;
        $view->c3 = $c3;
        $view->c4 = $c4;
        $view->c5 = $c5;
        $view->c6 = $c6;
        $view->c7 = $c7;
        $view->c8 = $c8;

        $view->pid = $pid;
        $view->atts = Atts::UploadFixed(Atts::$flow924, AttachmentCls::GetFixedItems($pid, 924), $edit);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function OnProjectFlow924()
    {
        $tno = $this->Req('tno', '', 'str');
        $tcontent = $this->Req('tcontent', '', 'str');
        $tdate = $this->Req('tdate', '', 'str');
        $tattachments = $this->Req('tattachments', '', 'str');
        $tmemo = $this->Req('tmemo', '', 'str');

        $no = $this->Req('no', '', 'str');
        $proj = $this->Req('proj', '', 'str');
        $stage = $this->Req('stage', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $owner = $this->Req('owner', '', 'str');
        $comp = $this->Req('comp', '', 'str');
        $date = $this->Req('date', '', 'str');

        $c1 = $this->Req('c1', '', 'str');
        $c2 = $this->Req('c2', '', 'str');
        $c3 = $this->Req('c3', '', 'str');
        $c4 = $this->Req('c4', '', 'str');
        $c5 = $this->Req('c5', '', 'str');
        $c6 = $this->Req('c6', '', 'str');
        $c7 = $this->Req('c7', '', 'str');
        $c8 = $this->Req('c8', '', 'str');

        $pid = $this->Mid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($tno)) Json::ReturnError('请输入文件编号');
        if (empty($tcontent)) Json::ReturnError('请输入文件内容');
        if (empty($tdate)) Json::ReturnError('请输入文件日期');

        if (empty($proj)) Json::ReturnError('请输入工程');
        if (empty($stage)) Json::ReturnError('请输入阶段');
        if (empty($signer)) Json::ReturnError('请输入编制');
        if (empty($owner)) Json::ReturnError('请输入项目法人');
        if (empty($comp)) Json::ReturnError('请输入工程建设项目法人名称');
        if (empty($date)) Json::ReturnError('请输入日期');

        if (empty($c1)) Json::ReturnError('请输入工程概况');
        if (empty($c2)) Json::ReturnError('请输入主要设计变更');
        if (empty($c3)) Json::ReturnError('请输入质量管理工作');
        if (empty($c4)) Json::ReturnError('请输入质量检测情况');
        if (empty($c5)) Json::ReturnError('请输入存在问题及处理情况');
        if (empty($c6)) Json::ReturnError('请输入遗留问题和尾工处理');
        if (empty($c7)) Json::ReturnError('请输入工程质量的自评、复核、认定结果');

        try {
            $id = Flow924Cls::Add($pid, $tno, $tcontent, $tdate, $tattachments, $tmemo, $no, $proj, $stage, $signer, $owner, $comp, $date, $c1, $c2, $c3, $c4, $c5, $c6, $c7, $c8);
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }
        ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_24, $id, ProjectStateCls::APPROVE);

        try {
            MsgCls::Add(1, MsgDirectCls::FROM_PROJECT, $this->Mid(), 1, ProjectCls::Instance()->Name($pid), '管理员', ProjectNodeCls::ACCEPT_24, $id, '新建' . ProjectNodeCls::Name(ProjectNodeCls::ACCEPT_24));
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        Json::ReturnSuccess();
    }

    public function ProjectReply924View()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow924Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Reply924Cls::GetLastItem($pid, $fid);

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectReply924View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow925List()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow925List');

        $pid = $this->Mid();

        $new = true;
        $rr = array();
        $rl = Flow925Cls::GetLastItem($pid);
        if (!empty($rl) && count($rl) > 0) {
            $new = ProjectStateCls::IsNew(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::ACCEPT_25));
            if ($rl['replyid'] > 0) $rr = Reply1Cls::GetLastItem($pid, $rl['replyid']);
        }
        $rs = Flow925Cls::GetApprovedItems($pid);

        $view->rl = $rl;
        $view->rr = $rr;
        $view->rs = $rs;
        $view->new = $new;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::ACCEPT_25);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow925()
    {
        $id = $this->Req('id', 0, 'int');

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow925');

        $pid = $this->Mid();
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow925Cls::Instance()->Item($id);
        else $rs = Flow925Cls::GetLastItem($pid);

        $tno = '';
        $tcontent = '';
        $tdate = '';
        $tattachments = '';
        $tmemo = '';

        $no = '';
        $proj = '';
        $stage = '';
        $signer = '';
        $owner = '';
        $comp = '';
        $date = '';

        $c1 = '';
        $c2 = '';
        $c3 = '';
        $c4 = '';
        $c5 = '';
        $c6 = '';
        $c7 = '';
        $c8 = '';

        $edit = true;

        if (!empty($rs) && count($rs) > 0) {

            $tno = $rs['tno'];
            $tcontent = $rs['tcontent'];
            $tdate = $rs['tdate'];
            $tattachments = $rs['tattachments'];
            $tmemo = $rs['tmemo'];

            $no = $rs['no'];
            $proj = $rs['proj'];
            $stage = $rs['stage'];
            $signer = $rs['signer'];
            $owner = $rs['owner'];
            $comp = $rs['comp'];
            $date = $rs['date'];

            $c1 = $rs['c1'];
            $c2 = $rs['c2'];
            $c3 = $rs['c3'];
            $c4 = $rs['c4'];
            $c5 = $rs['c5'];
            $c6 = $rs['c6'];
            $c7 = $rs['c7'];
            $c8 = $rs['c8'];

            $edit = ProjectStateCls::IsEdit(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::ACCEPT_25));
        }

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->edit = $edit;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::ACCEPT_25);

        $view->tno = $tno;
        $view->tcontent = $tcontent;
        $view->tdate = $tdate;
        $view->tattachments = $tattachments;
        $view->tmemo = $tmemo;

        $view->no = $no;
        $view->proj = $proj;
        $view->stage = $stage;
        $view->signer = $signer;
        $view->owner = $owner;
        $view->comp = $comp;
        $view->date = $date;

        $view->c1 = $c1;
        $view->c2 = $c2;
        $view->c3 = $c3;
        $view->c4 = $c4;
        $view->c5 = $c5;
        $view->c6 = $c6;
        $view->c7 = $c7;
        $view->c8 = $c8;

        $view->pid = $pid;
        $view->atts = Atts::UploadFixed(Atts::$flow925, AttachmentCls::GetFixedItems($pid, 925), $edit);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function OnProjectFlow925()
    {
        $tno = $this->Req('tno', '', 'str');
        $tcontent = $this->Req('tcontent', '', 'str');
        $tdate = $this->Req('tdate', '', 'str');
        $tattachments = $this->Req('tattachments', '', 'str');
        $tmemo = $this->Req('tmemo', '', 'str');

        $no = $this->Req('no', '', 'str');
        $proj = $this->Req('proj', '', 'str');
        $stage = $this->Req('stage', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $owner = $this->Req('owner', '', 'str');
        $comp = $this->Req('comp', '', 'str');
        $date = $this->Req('date', '', 'str');

        $c1 = $this->Req('c1', '', 'str');
        $c2 = $this->Req('c2', '', 'str');
        $c3 = $this->Req('c3', '', 'str');
        $c4 = $this->Req('c4', '', 'str');
        $c5 = $this->Req('c5', '', 'str');
        $c6 = $this->Req('c6', '', 'str');
        $c7 = $this->Req('c7', '', 'str');
        $c8 = $this->Req('c8', '', 'str');

        $pid = $this->Mid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($tno)) Json::ReturnError('请输入文件编号');
        if (empty($tcontent)) Json::ReturnError('请输入文件内容');
        if (empty($tdate)) Json::ReturnError('请输入文件日期');

        if (empty($proj)) Json::ReturnError('请输入工程');
        if (empty($stage)) Json::ReturnError('请输入阶段');
        if (empty($signer)) Json::ReturnError('请输入编制');
        if (empty($owner)) Json::ReturnError('请输入项目法人');
        if (empty($comp)) Json::ReturnError('请输入工程建设项目法人名称');
        if (empty($date)) Json::ReturnError('请输入日期');

        if (empty($c1)) Json::ReturnError('请输入工程概况');
        if (empty($c2)) Json::ReturnError('请输入主要设计变更');
        if (empty($c3)) Json::ReturnError('请输入质量管理工作');
        if (empty($c4)) Json::ReturnError('请输入质量检测情况');
        if (empty($c5)) Json::ReturnError('请输入存在问题及处理情况');
        if (empty($c6)) Json::ReturnError('请输入遗留问题和尾工处理');
        if (empty($c7)) Json::ReturnError('请输入工程质量的自评、复核、认定结果');

        try {
            $id = Flow925Cls::Add($pid, $tno, $tcontent, $tdate, $tattachments, $tmemo, $no, $proj, $stage, $signer, $owner, $comp, $date, $c1, $c2, $c3, $c4, $c5, $c6, $c7, $c8);
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }
        ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_25, $id, ProjectStateCls::APPROVE);

        try {
            MsgCls::Add(1, MsgDirectCls::FROM_PROJECT, $this->Mid(), 1, ProjectCls::Instance()->Name($pid), '管理员', ProjectNodeCls::ACCEPT_25, $id, '新建' . ProjectNodeCls::Name(ProjectNodeCls::ACCEPT_25));
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        Json::ReturnSuccess();
    }

    public function ProjectReply925View()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow925Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Reply925Cls::GetLastItem($pid, $fid);

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectReply925View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow9999List()
    {
        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow9999List');

        $pid = $this->Mid();

        $new = true;
        $rl = Flow9999Cls::GetLastItem($pid);
        if (!(!empty($rl) && count($rl) > 0 && $rl['replyid'] <= 0)) {
            $rl = array();
        }
        $rs = Flow9999Cls::GetReplyItems($pid);

        $view->rl = $rl;
        $view->rs = $rs;
        $view->new = $new;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::INSPECT);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectFlow9999()
    {
        $id = $this->Req('id', 0, 'int');

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectFlow9999');

        $pid = $this->Mid();
        $gc = ProjectCls::GetGroupCompany($pid);

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow9999Cls::Instance()->Item($id);
        else $rs = Flow9999Cls::GetLastItem($pid);

        $view->rs = $rs;

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::INSPECT);
        $view->finished = ProjectStateCls::IsFinished(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::INSPECT));

        $view->atts = Atts::UploadDynamic(AttachmentCls::GetDynamicItems($pid, 3), false);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function ProjectReply9999()
    {
        $fid = $this->Req('fid', 0, 'int');

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectReply9999');

        $pid = $this->Mid();
        $gc = ProjectCls::GetGroupCompany($pid);

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $view->fid = $fid;

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->pid = $pid;
        $view->atts = Atts::UploadDynamic(AttachmentCls::GetDynamicItems($pid, 4), true);

        echo $view->Render();

        $this->MemberFooter();
    }

    public function OnProjectReply9999()
    {
        $fid = $this->Req('fid', 0, 'int');

        $no = $this->Req('no', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $content = $this->Req('content', '', 'str');
        $comp = $this->Req('comp', '', 'str');
        $date = $this->Req('date', '', 'str');
        $uid = $this->Mid();

        $pid = Flow9999Cls::Instance()->Pid($fid);

        if ($fid <= 0 || $pid <= 0 || $uid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($no)) Json::ReturnError('请输入文件编号');
        if (empty($signer)) Json::ReturnError('请输入签发人');
        if (empty($content)) Json::ReturnError('请输入说明内容');
        //if (empty($comp)) Json::ReturnError('请输入单位(项目法人)');
        //if (empty($date)) Json::ReturnError('请输入日期');

        $act = 1;

        $replyid = Reply9999Cls::Add($pid, $fid, $no, $signer, $content, $comp, $date, $uid, $act);
        Flow9999Cls::SetReply($fid, $uid, $replyid);
        ProjectCls::SetNode($pid, ProjectNodeCls::INSPECT, $fid, ProjectStateCls::ALLOW);

        try {
            MsgCls::Add(1, MsgDirectCls::FROM_PROJECT, $this->Mid(), 1, ProjectCls::Instance()->Name($pid), '管理员', ProjectNodeCls::INSPECT, $replyid, '回复' . ProjectNodeCls::Name(ProjectNodeCls::INSPECT));
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        Json::ReturnSuccess();
    }

    public function ProjectReply9999View()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow9999Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Reply9999Cls::GetLastItem($pid, $fid);

        $this->MemberAuth();

        $this->MemberHeader();

        $view = View::Factory('ProjectReply9999View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        echo $view->Render();

        $this->MemberFooter();
    }

    public function PopFacadeItemsEdit()
    {
        $this->MemberAuth();

        $this->MemberHead();

//        unset($_SESSION['facade_table']);
//        unset($_SESSION['facade_ds']);

        $data = array();
        $maxcols = 0;
        $table = '';
        if (isset($_SESSION['facade_ds'])) {
            list($data, $maxcols) = $_SESSION['facade_ds'];
            $table = $this->FacadeTableEdit($data, $maxcols);

            $_SESSION['facade_table'] = $table;
            $_SESSION['facade_ds'] = array($data, $maxcols);
        }

        $view = View::Factory('PopFacadeItemsEdit');

        $view->tree = json_encode(FacadeTypeBiz::Tree());

        $view->table = $table;
        $view->cols = $maxcols;

        echo $view->Render();

        $this->MemberFoot();
    }

    public function OnFacadeItemsAdd()
    {
        $id = $this->Req('id', 0, 'int');

        if ($id <= 0) Json::ReturnError('请选择项目');

        $p = FacadeTypeCls::GetParents($id);
        $level = count($p);
        if ($level <= 0) Json::ReturnError(ALERT_ERROR);

        $data = array();
        $maxcols = 0;

        if (isset($_SESSION['facade_ds'])) list($data, $maxcols) = $_SESSION['facade_ds'];

        if (!empty($data)) {
            foreach ($data as $k => $v) {
                foreach ($v as $m => $n) {
                    if ($n == $id) Json::ReturnError('项目条目已经存在');
                }
            }
        }

        $maxcols = $maxcols < $level ? $level : $maxcols;

        $data[] = $p;

        $data = $this->FacadeItemsSort($data, $maxcols);
        $table = $this->FacadeTableEdit($data, $maxcols);

        $_SESSION['facade_table'] = $table;
        $_SESSION['facade_ds'] = array($data, $maxcols);

        Json::ReturnSuccess();
    }

    public function OnFacadeItemsDelete()
    {
        $id = $this->Req('id', 0, 'int');

        $data = array();
        $maxcols = 0;

        if (isset($_SESSION['facade_ds'])) list($data, $maxcols) = $_SESSION['facade_ds'];

        if (isset($data[$id])) unset($data[$id]);

        $maxcols = 0;
        foreach ($data as $k => $v) {
            $maxcols = $maxcols < count($v) ? count($v) : $maxcols;
        }

        $data = $this->FacadeItemsSort($data, $maxcols);
        $table = $this->FacadeTableEdit($data, $maxcols);

        $_SESSION['facade_table'] = $table;
        $_SESSION['facade_ds'] = array($data, $maxcols);

        Json::ReturnSuccess();
    }

    public function OnFacadeItemsOK()
    {
        $data = array();
        $maxcols = 0;

        if (isset($_SESSION['facade_ds'])) list($data, $maxcols) = $_SESSION['facade_ds'];

        if (empty($data) || $maxcols == 0) Json::ReturnError('项目表格中没有条目');

        $table = $this->FacadeTableOk($data, $maxcols);

        Json::ReturnSuccess($maxcols, $table);
    }

    private function FacadeItemsSort($data, $maxcols)
    {
        /** 格式数组排序
         * Sample:
         * $data = array(
         *      array(2, 6, 9),
         *      array(3, 5),
         *      array(1, 7, 3),
         *      array(3, 4),
         *      array(2, 6, 8)
         * );
         * $maxcols = 3;
         *
         */
        /* sort begin */
        foreach ($data as $k => $v) {
            if (count($v) < $maxcols) {
                for ($i = 0; $i < $maxcols - count($v); $i++) {
                    $data[$k][] = '';
                }
            }
        }

        foreach ($data as $k => $v) {
            for ($i = 0; $i < $maxcols; $i++) {
                $s{$i}[$k] = $v[$i];
            }
        }

        for ($i = 0; $i < $maxcols; $i++) {
            array_multisort($data, $s{$i});
        }

        foreach ($data as $k => $v) {
            for ($i = $maxcols - 1; $i >= 0; $i--) {
                if (empty($data[$k][$i])) unset($data[$k][$i]);
            }
        }

        return $data;
    }

    private function FacadeTableEdit($data, $maxcols)
    {
        $i = 1;
        $table = '<table class="tx1" id="facade">';
        $table .= '<thead><tr><th colspan="2">项次</th><th colspan="' . ($maxcols - 1) . '">项目</th><th>操作</th></tr></thead>';
        $table .= '<tbody>';
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                $table .= '<tr>';
                $col = 0;
                foreach ($v as $m => $n) {
                    $table .= '<td cols="' . $col . '" class=""';
                    if ($col == count($v) - 1) $table .= ' colspan="' . ($maxcols - count($v) + 1) . '"';
                    $table .= '>' . FacadeTypeCls::Instance()->Name($n) . '</td>';
                    if ($col == 0) $table .= '<td>' . $i . '</td>';
                    $col++;
                }
                $table .= '<td class="c"><a href="javascript:;" class="del" did="' . $k . '">删除</a></td>';
                $table .= '</tr>';
                $i++;
            }
        }
        $table .= '</tbody>';
        $table .= '</table>';
        return $table;
    }

    private function FacadeTableOk($data, $maxcols, $items = array(), $totals = array(), $amounts = array(), $edit = true)
    {
        $table = '';
        if (!empty($data) && $maxcols > 0) {
            $table .= '<table class="tx1" id="facade">';
            $table .= '<thead><tr><th colspan="2">项次</th><th colspan="' . ($maxcols - 1) . '">项目</th><th>标准分</th><th>检查结论<br/>(检测点合格率)</th><th>等级</th><th>得分</th><th>备注</th></tr></thead>';
            $table .= '<tbody>';
            if (!empty($data)) {
                $id = 0;
                $tt = 0;
                for ($i = 0; $i < count($data); $i++) {
                    $id = $data[$i][0];

                    $table .= '<tr>';
                    $col = 0;
                    foreach ($data[$i] as $m => $n) {
                        $table .= '<td class=""';
                        if ($col == count($data[$i]) - 1) $table .= ' colspan="' . ($maxcols - count($data[$i]) + 1) . '"';
                        $table .= '>' . FacadeTypeCls::Instance()->Name($n) . '</td>';
                        if ($col == 0) $table .= '<td>' . ($i + 1) . '</td>';
                        $col++;
                    }

                    $item1 = $item2 = $item3 = $item4 = $item5 = '';
                    if (!empty($items)) {
                        $item1 = isset($items[$i][0]) ? $items[$i][0] : '';
                        $item2 = isset($items[$i][1]) ? $items[$i][1] : '';
                        $item3 = isset($items[$i][2]) ? $items[$i][2] : '';
                        $item4 = isset($items[$i][3]) ? $items[$i][3] : '';
                        $item5 = isset($items[$i][4]) ? $items[$i][4] : '';
                    }

                    $table .= '<td class="c">';
                    $table .= ($edit ? '<input type="text" class="c item1" cata="' . $tt . '" value="' . $item1 . '" size="5"/>' : $item1);
                    $table .= '</td>';
                    $table .= '<td class="c">';
                    $table .= ($edit ? '<input type="text" class="w item2" value="' . $item2 . '"/>' : $item2);
                    $table .= '</td>';
                    $table .= '<td class="c">';
                    $table .= ($edit ? '<input type="text" class="c item3" value="' . $item3 . '" size="5"/>' : $item3);
                    $table .= '</td>';
                    $table .= '<td class="c">';
                    $table .= ($edit ? '<input type="text" class="c item4" cata="' . $tt . '" value="' . $item4 . '" size="5"/>' : $item4);
                    $table .= '</td>';
                    $table .= '<td class="c">';
                    $table .= ($edit ? '<input type="text" class="c item5" value="' . $item5 . '" size="5"/>' : $item5);
                    $table .= '</td>';
                    $table .= '</tr>';

                    if ($i != 0 && ((isset($data[$i + 1]) && $data[$i + 1][0] != $id) || (!isset($data[$i + 1])))) {
                        $total1 = $total2 = $total3 = $total4 = $total5 = '';
                        if (!empty($totals)) {
                            $total1 = isset($totals[$tt][0]) ? $totals[$tt][0] : '';
                            $total2 = isset($totals[$tt][1]) ? $totals[$tt][1] : '';
                            $total3 = isset($totals[$tt][2]) ? $totals[$tt][2] : '';
                            $total4 = isset($totals[$tt][3]) ? $totals[$tt][3] : '';
                            $total5 = isset($totals[$tt][4]) ? $totals[$tt][4] : '';
                        }

                        $id = $data[$i][0];
                        $table .= '<tr><td>' . FacadeTypeCls::Instance()->Name($id) . '</td><td colspan="' . $maxcols . '">合计</td>';
                        $table .= '<td class="c">';
                        $table .= ($edit ? '<input type="text" class="c total1" id="cata1' . $tt . '" value="' . $total1 . '" size="5"/>' : $total1);
                        $table .= '</td>';
                        $table .= '<td class="c">';
                        $table .= ($edit ? '<input type="text" class="w total2" value="' . $total2 . '"/>' : $total2);
                        $table .= '</td>';
                        $table .= '<td class="c">';
                        $table .= ($edit ? '<input type="text" class="c total3" value="' . $total3 . '" size="5"/>' : $total3);
                        $table .= '</td>';
                        $table .= '<td class="c">';
                        $table .= ($edit ? '<input type="text" class="c total4" id="cata4' . $tt . '" value="' . $total4 . '" size="5"/>' : $total4);
                        $table .= '</td>';
                        $table .= '<td class="c">';
                        $table .= ($edit ? '<input type="text" class="c total5" value="' . $total5 . '" size="5"/>' : $total5);
                        $table .= '</td>';
                        $table .= '</tr>';

                        $tt++;
                    }

                }

                $amount1 = $amount2 = $amount3 = $amount4 = '';
                if (!empty($amounts)) {
                    $amount1 = isset($amounts[0]) ? $amounts[0] : '';
                    $amount2 = isset($amounts[1]) ? $amounts[1] : '';
                    $amount3 = isset($amounts[2]) ? $amounts[2] : '';
                    $amount4 = isset($amounts[3]) ? $amounts[3] : '';
                }

                $table .= '<tr><td colspan="' . ($maxcols + 6) . '">应得 ';
                $table .= ($edit ? '<input type="text" class="c" id="amount1" value="' . $amount1 . '" size="5"/>' : $amount1);
                $table .= ' 分, 实得 ';
                $table .= ($edit ? '<input type="text" class="c" id="amount2" value="' . $amount2 . '" size="5"/>' : $amount2);
                $table .= ' 分, 得分率 ';
                $table .= ($edit ? '<input type="text" class="c" id="amount3" value="' . $amount3 . '" size="5"/>' : $amount3);
                $table .= ' %, 外观质量为 ';
                $table .= ($edit ? '<input type="text" class="c" id="amount4" value="' . $amount4 . '" size="5"/>' : $amount4);
                $table .= ' 等级.</td></tr>';
            }
            $table .= '</tbody>';
            $table .= '</table>';
        }

        return $table;
    }

    public function OnUpFlowFixed()
    {
        $pid = $this->Req('pid', 0, 'int');
        $tid = $this->Req('tid', 0, 'int');
        $no = $this->Req('no', 0, 'int');
        $name = $this->Req('name', '', 'str');
        $file = $this->Req('file', '', 'str');
        $url = $this->Req('url', '', 'str');
        $ext = $this->Req('ext', '', 'str');
        $size = $this->Req('size', 0, 'int');

        if ($pid <= 0 || $tid <= 0 || empty($url)) Json::ReturnError(ALERT_ERROR);

        try {
            AttachmentCls::AddFixed($pid, $tid, $no, $name, $url, $file, $ext, $size);
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        Json::ReturnSuccess();
    }

    public function OnUpFlowDynamic()
    {
        $pid = $this->Req('pid', 0, 'int');
        $tid = $this->Req('tid', 0, 'int');
        $no = $this->Req('no', 0, 'int');
        $name = $this->Req('name', '', 'str');
        $file = $this->Req('file', '', 'str');
        $url = $this->Req('url', '', 'str');
        $ext = $this->Req('ext', '', 'str');
        $size = $this->Req('size', 0, 'int');

        if ($pid <= 0 || $tid <= 0 || empty($url)) Json::ReturnError(ALERT_ERROR);

        $id = 0;
        try {
            $id = AttachmentCls::Add($pid, $tid, $no, $name, $url, $file, $ext, $size);
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        Json::ReturnSuccess($id);
    }

    public function OnUpFlowDelete()
    {
        $id = $this->Req('id', 0, 'int');

        try {
            AttachmentCls::Delete($id);
        } catch (Exception $e) {
            Json::ReturnError(ALERT_ERROR);
        }

        Json::ReturnSuccess();
    }
}