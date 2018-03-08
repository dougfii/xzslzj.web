<?php

class ProjectMod extends BaseMod
{
    public function index()
    {
        $this->ProjectList();
    }

    public function ProjectList()
    {
        $tid = $this->Req('tid', 0, 'int');

        $fname = $this->Req('fname', '', 'str');
        $fcompany = $this->Req('fcompany', '', 'str');
        $fdate0 = $this->Req('fdate0', '', 'str');
        $fdate1 = $this->Req('fdate1', '', 'str');
        $year = $this->Req('year', 0, 'int');

        $where = '';

        if ($year >= YEAR_START && $year <= Date::Year()) {
            $where .= ' AND EXTRACT(YEAR FROM time)=' . $year;
        }

        if (!empty ($fname)) $where .= ' AND LOWER(name) like \'%' . strtolower($fname) . '%\'';
        if (!empty($fcompany)) $where .= ' AND LOWER(company) like \'%' . strtolower($fcompany) . '%\'';
        if (!empty($fdate0) && empty($fdate1)) $where .= " AND DATE_TRUNC('day', time)='{$fdate0}'";
        if (empty($fdate0) && !empty($fdate1)) $where .= " AND DATE_TRUNC('day', time)='{$fdate1}'";
        if (!empty($fdate0) && !empty($fdate1)) $where .= " AND DATE_TRUNC('day', time)>='{$fdate0}' AND DATE_TRUNC('day', time)<='{$fdate1}'";

        $url = Url::QUERY_STRING_DELETE(array('page', 'fname'));

        $size = PAGE_SIZE_MEDIUM;
        list($count, $rs) = ProjectCls::Results($where, '', $this->Page(), $size);

        $this->Header();

        $view = View::Factory('ProjectList');

        $view->rs = $rs;
        $view->paged = HTML::PageJump($count, $this->Page(), $size, $url);

        $view->tid = $tid;

        $view->url = $url;
        $view->fname = $fname;
        $view->fcompany = $fcompany;
        $view->fdate0 = $fdate0;
        $view->fdate1 = $fdate1;

        $view->isAdmin = $this->IsAdmin();

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectEdit()
    {
        $id = $this->Req('id', 0, 'int');

        $rs = ProjectCls::Instance()->Item($id);

        $this->Header();

        $view = View::Factory('ProjectEdit');

        $view->rs = $rs;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectPassword()
    {
        $id = $this->Req('id', 0, 'int');
        $pass = $this->Req('pass', '', 'str');

        if ($id <= 0) Json::ReturnError(ALERT_ERROR);
        if (!Util::IsPassword($pass)) Json::ReturnError('请设置有效的重置密码');

        try {
            ProjectCls::SetPassword($id, $pass);
        } catch (Exception $e) {
            Json::ReturnError(ALERT_ERROR);
        }

        Json::ReturnSuccess();
    }

    public function OnProjectInfo()
    {
        $id = $this->Req('id', 0, 'int');
        $name = $this->Req('name', '', 'str');
        $company = $this->Req('company', '', 'str');

        if ($id <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($name)) Json::ReturnError('请输入工程名称');
        if (!Util::IsMaxLen($name, 200)) Json::ReturnError('工程名称过长');
        if (empty($company)) Json::ReturnError('请输入申请单位');
        if (!Util::IsMaxLen($company, 200)) Json::ReturnError('申请单位过长');

        try {
            ProjectCls::SetInfo($id, $name, $company);
        } catch (Exception $e) {
            Json::ReturnError(ALERT_ERROR);
        }

        Json::ReturnSuccess();
    }

    public function OnProjectAct()
    {
        $id = $this->Req('id', 0, 'int');
        if ($id <= 0) Json::ReturnError(ALERT_ERROR);

        ProjectCls::SetAct($id);

        $act = ProjectCls::Instance()->Act($id);

        if ($act) {
            ProjectCls::SetNode($id, ProjectNodeCls::INIT, 1, ProjectStateCls::ALLOW);
            ProjectCls::SetNode($id, ProjectNodeCls::APPLY, 1, ProjectStateCls::BEGIN);

            MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $id, '管理员', ProjectCls::Instance()->Name($id), ProjectNodeCls::INIT, $id, '项目被管理员开放');
        } else {
            ProjectCls::SetNode($id, ProjectNodeCls::INIT, 1, ProjectStateCls::BEGIN);

            MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $id, '管理员', ProjectCls::Instance()->Name($id), ProjectNodeCls::INIT, $id, '项目被管理员关闭');
        }

        Json::ReturnSuccess();
    }

    public function OnProjectDelete()
    {
        $id = $this->Req('id', 0, 'int');
        if ($id <= 0) Json::ReturnError(ALERT_ERROR);

        ProjectCls::Delete($id);

        Json::ReturnSuccess();
    }

    public function ProjectFlow()
    {
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow');

        $view->rs = ProjectCls::Instance()->Item($pid);

        $view->pid = $pid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow0List()
    {
        $this->ProjectFlow0();
    }

    public function ProjectFlow0()
    {
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow0');

        $view->pid = $pid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow1List()
    {
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow1List');

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

        $view->pid = $pid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow1()
    {
        $id = $this->Req('id', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow1');

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow1Cls::Instance()->Item($id);
        else $rs = Flow1Cls::GetLastItem($pid);

        $view->rs = $rs;

        $view->name = $name;
        $view->company = $company;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::APPLY);

        if ($id > 0) $view->approve = false;
        else $view->approve = ProjectStateCls::IsApprove(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::APPLY));

        $view->atts = Atts::UploadFixed(Atts::$flow1, AttachmentCls::GetFixedItems($pid, 1), false, false, true);

        echo $view->Render();

        $this->Footer();
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

        $view->atts = Atts::UploadFixed(Atts::$flow1, AttachmentCls::GetFixedItems($pid, 1), false, false, true);

        echo $view->Render();

        $this->FootPrint();
    }

    public function OnProjectFlow1Deny()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 0;

        $replyid = Reply1Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow1Cls::SetReply($fid, $uid, $replyid);
        ProjectCls::SetNode($pid, ProjectNodeCls::APPLY, $fid, ProjectStateCls::DENY);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::APPLY, $fid, ProjectNodeCls::Name(ProjectNodeCls::APPLY) . '被审批退回');

        Json::ReturnSuccess();
    }

    public function ProjectReply1()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow1Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $this->Header();

        $view = View::Factory('ProjectReply1');

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectReply1()
    {
        $fid = $this->Req('fid', 0, 'int');
        $no = $this->Req('no', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $uid = $this->Uid();

        $pid = Flow1Cls::Instance()->Pid($fid);

        if ($fid <= 0 || $pid <= 0 || $uid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($no)) Json::ReturnError('请输入文件编号');
        if (empty($content)) Json::ReturnError('请输入批复内容');
        if (empty($date)) Json::ReturnError('请输入批复日期');

        $act = 1;

        $replyid = Reply1Cls::Add($pid, $fid, $no, $content, $date, $uid, $act);
        Flow1Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::APPLY, $fid, ProjectStateCls::ALLOW);
        ProjectCls::SetNode($pid, ProjectNodeCls::DIVIDE, $fid, ProjectStateCls::BEGIN);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::APPLY, $fid, ProjectNodeCls::Name(ProjectNodeCls::APPLY) . '有批复');

        Json::ReturnSuccess();
    }

    public function ProjectReply1View()
    {
        $fid = $this->Req('fid', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');
        //$pid = Flow1Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Reply1Cls::GetLastItem($pid, $fid);

        $this->Header();

        $view = View::Factory('ProjectReply1View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow2List()
    {
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow2List');

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

        $view->pid = $pid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow2()
    {
        $id = $this->Req('id', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow2');

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow2Cls::Instance()->Item($id);
        else $rs = Flow2Cls::GetLastItem($pid);

        $view->rs = $rs;

        $view->name = $name;
        $view->company = $company;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::DIVIDE);

        if ($id > 0) $view->approve = false;
        else $view->approve = ProjectStateCls::IsApprove(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::DIVIDE));

        $view->atts = Atts::UploadFixed(Atts::$flow2, AttachmentCls::GetFixedItems($pid, 2), false, false, true);

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectFlow2Deny()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 0;

        $replyid = Reply2Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow2Cls::SetReply($fid, $uid, $replyid);
        ProjectCls::SetNode($pid, ProjectNodeCls::DIVIDE, $fid, ProjectStateCls::DENY);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::DIVIDE, $fid, ProjectNodeCls::Name(ProjectNodeCls::DIVIDE) . '被审批退回');

        Json::ReturnSuccess();
    }

    public function ProjectReply2()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow2Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $this->Header();

        $view = View::Factory('ProjectReply2');

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectReply2()
    {
        $fid = $this->Req('fid', 0, 'int');
        $no = $this->Req('no', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $uid = $this->Uid();

        $n3 = $this->Req('n3', 0, 'int');
        $n4 = $this->Req('n4', 0, 'int');
        $n5 = $this->Req('n5', 0, 'int');
        $n6 = $this->Req('n6', 0, 'int');
        $n7 = $this->Req('n7', 0, 'int');
        $n8 = $this->Req('n8', 0, 'int');
        $n9 = $this->Req('n9', 0, 'int');

        $pid = Flow2Cls::Instance()->Pid($fid);

        if ($fid <= 0 || $pid <= 0 || $uid <= 0) Json::ReturnError(ALERT_ERROR);
        if (!($n3 || $n4 || $n5 || $n6 || $n7 || $n8 || $n9)) Json::ReturnError('请至少选择一个下一阶段节点');
        if (empty($no)) Json::ReturnError('请输入文件编号');
        if (empty($content)) Json::ReturnError('请输入批复内容');
        if (empty($date)) Json::ReturnError('请输入批复日期');

        $act = 1;

        $replyid = Reply2Cls::Add($pid, $fid, $no, $content, $date, $uid, $act);
        Flow2Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::DIVIDE, $fid, ProjectStateCls::ALLOW);

        if ($n3) ProjectCls::SetNode($pid, ProjectNodeCls::CONFIRM, $fid, ProjectStateCls::BEGIN);
        if ($n4) ProjectCls::SetNode($pid, ProjectNodeCls::SUGGEST, $fid, ProjectStateCls::BEGIN);
        if ($n5) {
            ProjectCls::SetNode($pid, ProjectNodeCls::MATERIAL, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::MATERIAL_1, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::MATERIAL_2, $fid, ProjectStateCls::BEGIN);
        }
        if ($n6) {
            ProjectCls::SetNode($pid, ProjectNodeCls::CHECK, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::CHECK_1, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::CHECK_2, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::CHECK_3, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::CHECK_4, $fid, ProjectStateCls::BEGIN);
        }
        if ($n7) {
            ProjectCls::SetNode($pid, ProjectNodeCls::RECORD, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_1, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_2, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_3, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_4, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_5, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_6, $fid, ProjectStateCls::BEGIN);
        }
        if ($n8) ProjectCls::SetNode($pid, ProjectNodeCls::PROGRESS, $fid, ProjectStateCls::BEGIN);
        if ($n9) {
            ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_1, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_2, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_21, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_22, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_23, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_24, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_25, $fid, ProjectStateCls::BEGIN);
        }

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::DIVIDE, $fid, ProjectNodeCls::Name(ProjectNodeCls::DIVIDE) . '有批复');

        Json::ReturnSuccess();
    }

    public function OnProjectReply2Node()
    {
        $fid = $this->Req('fid', 0, 'int');
        $uid = $this->Uid();

        $n3 = $this->Req('n3', 0, 'int');
        $n4 = $this->Req('n4', 0, 'int');
        $n5 = $this->Req('n5', 0, 'int');
        $n6 = $this->Req('n6', 0, 'int');
        $n7 = $this->Req('n7', 0, 'int');
        $n8 = $this->Req('n8', 0, 'int');
        $n9 = $this->Req('n9', 0, 'int');

        $pid = Flow2Cls::Instance()->Pid($fid);

        if ($fid <= 0 || $pid <= 0 || $uid <= 0) Json::ReturnError(ALERT_ERROR);
        if (!($n3 || $n4 || $n5 || $n6 || $n7 || $n8 || $n9)) Json::ReturnError('请至少选择一个下一阶段节点');

        if ($n3) ProjectCls::SetNode($pid, ProjectNodeCls::CONFIRM, $fid, ProjectStateCls::BEGIN);
        if ($n4) ProjectCls::SetNode($pid, ProjectNodeCls::SUGGEST, $fid, ProjectStateCls::BEGIN);
        if ($n5) {
            ProjectCls::SetNode($pid, ProjectNodeCls::MATERIAL, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::MATERIAL_1, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::MATERIAL_2, $fid, ProjectStateCls::BEGIN);
        }
        if ($n6) {
            ProjectCls::SetNode($pid, ProjectNodeCls::CHECK, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::CHECK_1, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::CHECK_2, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::CHECK_3, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::CHECK_4, $fid, ProjectStateCls::BEGIN);
        }
        if ($n7) {
            ProjectCls::SetNode($pid, ProjectNodeCls::RECORD, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_1, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_2, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_3, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_4, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_5, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_6, $fid, ProjectStateCls::BEGIN);
        }
        if ($n8) ProjectCls::SetNode($pid, ProjectNodeCls::PROGRESS, $fid, ProjectStateCls::BEGIN);
        if ($n9) {
            ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_1, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_2, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_21, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_22, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_23, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_24, $fid, ProjectStateCls::BEGIN);
            ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_25, $fid, ProjectStateCls::BEGIN);
        }

        Json::ReturnSuccess();
    }

    public function ProjectReply2View()
    {
        $fid = $this->Req('fid', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');
        //$pid = Flow1Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Reply2Cls::GetLastItem($pid, $fid);

        $this->Header();

        $view = View::Factory('ProjectReply2View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow3List()
    {
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow3List');

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

        $view->pid = $pid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow3()
    {
        $id = $this->Req('id', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow3');

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow3Cls::Instance()->Item($id);
        else $rs = Flow3Cls::GetLastItem($pid);

        $datas = array();
        if (!empty($rs)) {
            $datas = Json::Decode($rs['items']);
        }
        $table = isset($datas['table']) ? $datas['table'] : array();
        $items = isset($datas['items']) ? $datas['items'] : array();
        $totals = isset($datas['totals']) ? $datas['totals'] : array();
        $amounts = isset($datas['amounts']) ? $datas['amounts'] : array();
        $data = array();
        $maxcols = 0;
        if (!empty($table)) {
            list($data, $maxcols) = $table;
        }

        $tables = $this->FacadeTableOk($data, $maxcols, $items, $totals, $amounts, false);
        $view->tables = $tables;
        $view->cols = $maxcols;

        $view->rs = $rs;

        $view->name = $name;
        $view->company = $company;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::CONFIRM);

        if ($id > 0) $view->approve = false;
        else $view->approve = ProjectStateCls::IsApprove(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::CONFIRM));

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectFlow3Deny()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 0;

        $replyid = Reply3Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow3Cls::SetReply($fid, $uid, $replyid);
        ProjectCls::SetNode($pid, ProjectNodeCls::CONFIRM, $fid, ProjectStateCls::DENY);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::CONFIRM, $fid, ProjectNodeCls::Name(ProjectNodeCls::CONFIRM) . '被审批退回');

        Json::ReturnSuccess();
    }

    public function ProjectReply3()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow3Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $this->Header();

        $view = View::Factory('ProjectReply3');

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        $view->pid = $pid;
        $view->atts = Atts::UploadDynamic(AttachmentCls::GetDynamicItems($pid, 3), true, false, true);

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectReply3()
    {
        $fid = $this->Req('fid', 0, 'int');
        $no = $this->Req('no', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $uid = $this->Uid();

        $pid = Flow3Cls::Instance()->Pid($fid);

        if ($fid <= 0 || $pid <= 0 || $uid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($no)) Json::ReturnError('请输入文件编号');
        if (empty($content)) Json::ReturnError('请输入批复内容');
        if (empty($date)) Json::ReturnError('请输入批复日期');

        $act = 1;

        $replyid = Reply3Cls::Add($pid, $fid, $no, $content, $date, $uid, $act);
        Flow3Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::CONFIRM, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::CONFIRM, $fid, ProjectNodeCls::Name(ProjectNodeCls::CONFIRM) . '有批复');

        Json::ReturnSuccess();
    }

    public function ProjectReply3View()
    {
        $fid = $this->Req('fid', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');
        //$pid = Flow1Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $rs = Reply3Cls::GetLastItem($pid, $fid);

        $this->Header();

        $view = View::Factory('ProjectReply3View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow4List()
    {
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow4List');

        $new = true;
//        $rl = Flow4Cls::GetProjectItems($pid);
//        if (!(!empty($rl) && count($rl) > 0 && $rl['replyid'] <= 0)) {
//            $rl = array();
//        }
        $rs = Flow4Cls::GetProjectItems($pid);

//        $view->rl = $rl;
        $view->rs = $rs;
        $view->new = $new;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::SUGGEST);

        $view->pid = $pid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow4()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');

        if ($fid > 0) $pid = Flow4Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($fid > 0) $rs = Flow4Cls::Instance()->Item($fid);
        else $rs = Flow4Cls::GetLastItem($pid);

        $no = '';
        $code = '';
        $content = '';
        $date = '';
        $personals = '';
        $progress = '';
        $writer = '';
        $writer_date = '';
        $signer = '';
        $signer_date = '';

        $edit = $fid <= 0;

        if ($fid > 0 && !empty($rs) && count($rs) > 0) {
            $name = $rs['name'];
            $no = $rs['no'];
            $code = $rs['code'];
            $content = $rs['content'];
            $date = $rs['date'];
            $personals = $rs['personals'];
            $progress = $rs['progress'];
            $writer = $rs['writer'];
            $writer_date = $rs['writer_date'];
            $signer = $rs['signer'];
            $signer_date = $rs['signer_date'];

            // $edit = ProjectStateCls::IsEdit(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::SUGGEST));
            $edit = $rs['replyid'] <= 0;
        }

        $this->Header();

        $view = View::Factory('ProjectFlow4');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->edit = $edit;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::SUGGEST);

        $view->no = $no;
        $view->code = $code;
        $view->content = $content;
        $view->date = $date;
        $view->personals = $personals;
        $view->progress = $progress;
        $view->writer = $writer;
        $view->writer_date = $writer_date;
        $view->signer = $signer;
        $view->signer_date = $signer_date;

        $view->fid = $fid;
        $view->pid = $pid;
        $view->atts = Atts::UploadDynamic(AttachmentCls::GetDynamicItems($pid, 3), true, false, true);

        if ($fid > 0) $view->approve = false;
        else $view->approve = ProjectStateCls::IsApprove(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::SUGGEST));

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectReply4()
    {
        $id = $this->Req('id', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');

        $fid = $replyid = 0;

        if ($id > 0) $rs = Flow4Cls::Instance()->Item($id);
        else $rs = Flow4Cls::GetLastItem($pid);

        if (!empty($rs)) {
            $fid = $rs['id'];
            $replyid = $rs['replyid'];
        } else {
            Url::RedirectUrl('?m=Project&a=ProjectReply4&pid=' . $pid);
        }

        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $this->Header();

        $view = View::Factory('ProjectReply4');

        $view->rs = Reply4Cls::Instance()->Item($replyid);
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::SUGGEST);

        $view->fid = $fid;
        $view->pid = $pid;
        $view->atts = Atts::UploadDynamic(AttachmentCls::GetDynamicItems($pid, 4), false, false, true);

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectFlow4()
    {
        $pid = $this->Req('pid', 0, 'int');

        $name = $this->Req('name', '', 'str');
        $no = $this->Req('no', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $uid = $this->Uid();

        $code = $this->Req('code', '', 'str');
        $personals = $this->Req('personals', '', 'str');
        $progress = $this->Req('progress', '', 'str');
        $writer = $this->Req('writer', '', 'str');
        $writer_date = $this->Req('writer_date', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $signer_date = $this->Req('signer_date', '', 'str');

        //$pid = Flow4Cls::Instance()->Pid($fid);

        if ($pid <= 0 || $uid <= 0) Json::ReturnError(ALERT_ERROR);

        if (empty($no)) Json::ReturnError('请输入检查表编号');
        if (empty($name)) Json::ReturnError('请输入工程名称');
        if (empty($content)) Json::ReturnError('请输入检查意见');
        if (empty($date)) Json::ReturnError('请输入时间');

        //if (empty($code)) Json::ReturnError('质量监督人员和证号');
        //if (empty($personals)) Json::ReturnError('请输入参建单位人员');
        //if (empty($progress)) Json::ReturnError('请输入工程形象进度');
        //if (empty($writer)) Json::ReturnError('请输入被检查单位签收人');
        //if (empty($writer_date)) Json::ReturnError('请输入被检查单位签收人日期');
        //if (empty($signer)) Json::ReturnError('请输入填写人');
        //if (empty($signer_date)) Json::ReturnError('请输入填写人日期');

        $act = 1;

        try {
            $id = Flow4Cls::Add($pid, $name, $no, $code, $date, $content, $personals, $progress, $writer, $writer_date, $signer, $signer_date);
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        ProjectCls::SetNode($pid, ProjectNodeCls::SUGGEST, $id, ProjectStateCls::APPROVE);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::SUGGEST, $pid, ProjectNodeCls::Name(ProjectNodeCls::SUGGEST) . '有新消息');

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

        $this->Header();

        $view = View::Factory('ProjectReply4View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow51List()
    {
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow51List');

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

        $view->pid = $pid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow51()
    {
        $id = $this->Req('id', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow51');

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow51Cls::Instance()->Item($id);
        else $rs = Flow51Cls::GetLastItem($pid);

        $items = array();
        $totals = array();
        if (!empty($rs)) {
            $items = Json::Decode($rs['items']);
            $totals = Json::Decode($rs['totals']);
        }
        $view->items = $items;
        $view->total0 = isset($totals[0]) ? $totals[0] : '';
        $view->total1 = isset($totals[1]) ? $totals[1] : '';

        $view->rs = $rs;

        $view->name = $name;
        $view->company = $company;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::MATERIAL_1);

        if ($id > 0) $view->approve = false;
        else $view->approve = ProjectStateCls::IsApprove(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::MATERIAL_1));

        $view->atts = Atts::UploadFixed(Atts::$flow51, AttachmentCls::GetFixedItems($pid, 51), false, false, true);

        $view->edit = false;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectFlow51Allow()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 1;

        $replyid = Reply51Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow51Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::MATERIAL_1, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::MATERIAL_1, $fid, ProjectNodeCls::Name(ProjectNodeCls::MATERIAL_1) . '审批通过');

        Json::ReturnSuccess();
    }

    public function OnProjectFlow51Deny()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 0;

        $replyid = Reply51Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow51Cls::SetReply($fid, $uid, $replyid);
        ProjectCls::SetNode($pid, ProjectNodeCls::MATERIAL_1, $fid, ProjectStateCls::DENY);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::MATERIAL_1, $fid, ProjectNodeCls::Name(ProjectNodeCls::MATERIAL_1) . '审批退回');

        Json::ReturnSuccess();
    }

    public function ProjectReply51()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow51Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $this->Header();

        $view = View::Factory('ProjectReply51');

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectReply51()
    {
        $fid = $this->Req('fid', 0, 'int');
        $no = $this->Req('no', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $uid = $this->Uid();

        $pid = Flow51Cls::Instance()->Pid($fid);

        if ($fid <= 0 || $pid <= 0 || $uid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($no)) Json::ReturnError('请输入文件编号');
        if (empty($content)) Json::ReturnError('请输入批复内容');
        if (empty($date)) Json::ReturnError('请输入批复日期');

        $act = 1;

        $replyid = Reply51Cls::Add($pid, $fid, $no, $content, $date, $uid, $act);
        Flow51Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::MATERIAL_1, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::MATERIAL_1, $fid, ProjectNodeCls::Name(ProjectNodeCls::MATERIAL_1) . '有审批回复');

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

        $this->Header();

        $view = View::Factory('ProjectReply51View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow52List()
    {
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow52List');

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

        $view->pid = $pid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow52()
    {
        $id = $this->Req('id', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow52');

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow52Cls::Instance()->Item($id);
        else $rs = Flow52Cls::GetLastItem($pid);

        $items = array();
        if (!empty($rs)) {
            $items = Json::Decode($rs['items']);
        }
        $view->items = $items;

        $view->rs = $rs;

        $view->name = $name;
        $view->company = $company;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::MATERIAL_2);

        if ($id > 0) $view->approve = false;
        else $view->approve = ProjectStateCls::IsApprove(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::MATERIAL_2));

        $view->atts = Atts::UploadFixed(Atts::$flow52, AttachmentCls::GetFixedItems($pid, 52), false, false, true);

        $view->edit = false;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectFlow52Allow()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 1;

        $replyid = Reply52Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow52Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::MATERIAL_2, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::MATERIAL_2, $fid, ProjectNodeCls::Name(ProjectNodeCls::MATERIAL_2) . '审批通过');

        Json::ReturnSuccess();
    }

    public function OnProjectFlow52Deny()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 0;

        $replyid = Reply52Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow52Cls::SetReply($fid, $uid, $replyid);
        ProjectCls::SetNode($pid, ProjectNodeCls::MATERIAL_2, $fid, ProjectStateCls::DENY);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::MATERIAL_2, $fid, ProjectNodeCls::Name(ProjectNodeCls::MATERIAL_2) . '审批退回');

        Json::ReturnSuccess();
    }

    public function ProjectReply52()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow52Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $this->Header();

        $view = View::Factory('ProjectReply52');

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectReply52()
    {
        $fid = $this->Req('fid', 0, 'int');
        $no = $this->Req('no', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $uid = $this->Uid();

        $pid = Flow52Cls::Instance()->Pid($fid);

        if ($fid <= 0 || $pid <= 0 || $uid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($no)) Json::ReturnError('请输入文件编号');
        if (empty($content)) Json::ReturnError('请输入批复内容');
        if (empty($date)) Json::ReturnError('请输入批复日期');

        $act = 1;

        $replyid = Reply52Cls::Add($pid, $fid, $no, $content, $date, $uid, $act);
        Flow52Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::MATERIAL_2, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::MATERIAL_2, $fid, ProjectNodeCls::Name(ProjectNodeCls::MATERIAL_2) . '有审批答复');

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

        $this->Header();

        $view = View::Factory('ProjectReply52View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow61()
    {
        $id = $this->Req('id', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow61');

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow61Cls::Instance()->Item($id);
        else $rs = Flow61Cls::GetLastItem($pid);

        $items = array();
        if (!empty($rs)) {
            $items = Json::Decode($rs['items']);
        }
        $view->items = $items;

        $view->rs = $rs;

        $view->name = $name;
        $view->company = $company;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::CHECK_1);

        if ($id > 0) $view->approve = false;
        else $view->approve = ProjectStateCls::IsApprove(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::CHECK_1));

        $view->atts = Atts::UploadFixed(Atts::$flow61, AttachmentCls::GetFixedItems($pid, 61), false, false, true);

        $view->edit = false;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectFlow61Allow()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 1;

        $replyid = Reply61Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow61Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::CHECK_1, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::CHECK_1, $fid, ProjectNodeCls::Name(ProjectNodeCls::CHECK_1) . '审批通过');

        Json::ReturnSuccess();
    }

    public function OnProjectFlow61Deny()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 0;

        $replyid = Reply61Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow61Cls::SetReply($fid, $uid, $replyid);
        ProjectCls::SetNode($pid, ProjectNodeCls::CHECK_1, $fid, ProjectStateCls::DENY);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::CHECK_1, $fid, ProjectNodeCls::Name(ProjectNodeCls::CHECK_1) . '审批退回');

        Json::ReturnSuccess();
    }

    public function ProjectFlow61List()
    {
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow61List');

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

        $view->pid = $pid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectReply61()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow61Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $this->Header();

        $view = View::Factory('ProjectReply61');

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectReply61()
    {
        $fid = $this->Req('fid', 0, 'int');
        $no = $this->Req('no', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $uid = $this->Uid();

        $pid = Flow61Cls::Instance()->Pid($fid);

        if ($fid <= 0 || $pid <= 0 || $uid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($no)) Json::ReturnError('请输入文件编号');
        if (empty($content)) Json::ReturnError('请输入批复内容');
        if (empty($date)) Json::ReturnError('请输入批复日期');

        $act = 1;

        $replyid = Reply61Cls::Add($pid, $fid, $no, $content, $date, $uid, $act);
        Flow61Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::CHECK_1, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::CHECK_1, $fid, ProjectNodeCls::Name(ProjectNodeCls::CHECK_1) . '有审批答复');

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

        $this->Header();

        $view = View::Factory('ProjectReply61View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow62List()
    {
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow62List');

        $new = true;
        $rr = array();
        $rl = Flow62Cls::GetLastItem($pid);
        if (!empty($rl) && count($rl) > 0) {
            $new = ProjectStateCls::IsNew(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::CHECK_2));
            if ($rl['replyid'] > 0) $rr = Reply1Cls::GetLastItem($pid, $rl['replyid']);
        }
        $rs = Flow62Cls::GetApprovedItems($pid);

        $view->rl = $rl;
        $view->rr = $rr;
        $view->rs = $rs;
        $view->new = $new;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::CHECK_2);

        $view->pid = $pid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow62()
    {
        $id = $this->Req('id', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow62');

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow62Cls::Instance()->Item($id);
        else $rs = Flow62Cls::GetLastItem($pid);

        $datas = array();
        if (!empty($rs)) {
            $datas = Json::Decode($rs['items']);
        }
        $table = isset($datas['table']) ? $datas['table'] : array();
        $items = isset($datas['items']) ? $datas['items'] : array();
        $totals = isset($datas['totals']) ? $datas['totals'] : array();
        $amounts = isset($datas['amounts']) ? $datas['amounts'] : array();
        $data = array();
        $maxcols = 0;
        if (!empty($table)) {
            list($data, $maxcols) = $table;
        }

        $tables = $this->FacadeTableOk($data, $maxcols, $items, $totals, $amounts, false);
        $view->tables = $tables;
        $view->cols = $maxcols;

        $view->rs = $rs;

        $view->name = $name;
        $view->company = $company;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::CHECK_2);

        if ($id > 0) $view->approve = false;
        else $view->approve = ProjectStateCls::IsApprove(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::CHECK_2));

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectFlow62Allow()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 1;

        $replyid = Reply62Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow62Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::CHECK_2, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::CHECK_2, $fid, ProjectNodeCls::Name(ProjectNodeCls::CHECK_2) . '审批通过');

        Json::ReturnSuccess();
    }

    public function OnProjectFlow62Deny()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 0;

        $replyid = Reply62Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow62Cls::SetReply($fid, $uid, $replyid);
        ProjectCls::SetNode($pid, ProjectNodeCls::CHECK_2, $fid, ProjectStateCls::DENY);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::CHECK_2, $fid, ProjectNodeCls::Name(ProjectNodeCls::CHECK_2) . '审批退回');

        Json::ReturnSuccess();
    }

    public function ProjectReply62()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow62Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $this->Header();

        $view = View::Factory('ProjectReply62');

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectReply62()
    {
        $fid = $this->Req('fid', 0, 'int');
        $no = $this->Req('no', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $uid = $this->Uid();

        $pid = Flow63Cls::Instance()->Pid($fid);

        if ($fid <= 0 || $pid <= 0 || $uid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($no)) Json::ReturnError('请输入文件编号');
        if (empty($content)) Json::ReturnError('请输入批复内容');
        if (empty($date)) Json::ReturnError('请输入批复日期');

        $act = 1;

        $replyid = Reply62Cls::Add($pid, $fid, $no, $content, $date, $uid, $act);
        Flow62Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::CHECK_2, $fid, ProjectStateCls::ALLOW);


        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::CHECK_2, $fid, ProjectNodeCls::Name(ProjectNodeCls::CHECK_2) . '有审批答复');

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

        $this->Header();

        $view = View::Factory('ProjectReply62View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow63List()
    {
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow63List');

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

        $view->pid = $pid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow63()
    {
        $id = $this->Req('id', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow63');

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow63Cls::Instance()->Item($id);
        else $rs = Flow63Cls::GetLastItem($pid);

        $items = array();
        $totals = array();
        if (!empty($rs)) {
            $items = Json::Decode($rs['items']);
            $totals = Json::Decode($rs['totals']);
        }
        $view->items = $items;
        $view->total0 = isset($totals[0]) ? $totals[0] : '';
        $view->total1 = isset($totals[1]) ? $totals[1] : '';
        $view->total2 = isset($totals[2]) ? $totals[2] : '';
        $view->total3 = isset($totals[3]) ? $totals[3] : '';
        $view->total4 = isset($totals[4]) ? $totals[4] : '';
        $view->total5 = isset($totals[5]) ? $totals[5] : '';

        $view->rs = $rs;

        $view->name = $name;
        $view->company = $company;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::CHECK_3);

        if ($id > 0) $view->approve = false;
        else $view->approve = ProjectStateCls::IsApprove(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::CHECK_3));

        $view->atts = Atts::UploadFixed(Atts::$flow63, AttachmentCls::GetFixedItems($pid, 63), false, false, true);

        $view->edit = false;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectFlow63Allow()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 1;

        $replyid = Reply63Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow63Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::CHECK_3, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::CHECK_3, $fid, ProjectNodeCls::Name(ProjectNodeCls::CHECK_3) . '审批通过');

        Json::ReturnSuccess();
    }

    public function OnProjectFlow63Deny()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 0;

        $replyid = Reply63Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow63Cls::SetReply($fid, $uid, $replyid);
        ProjectCls::SetNode($pid, ProjectNodeCls::CHECK_3, $fid, ProjectStateCls::DENY);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::CHECK_3, $fid, ProjectNodeCls::Name(ProjectNodeCls::CHECK_3) . '审批退回');

        Json::ReturnSuccess();
    }

    public function ProjectReply63()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow63Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $this->Header();

        $view = View::Factory('ProjectReply63');

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectReply63()
    {
        $fid = $this->Req('fid', 0, 'int');
        $no = $this->Req('no', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $uid = $this->Uid();

        $pid = Flow63Cls::Instance()->Pid($fid);

        if ($fid <= 0 || $pid <= 0 || $uid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($no)) Json::ReturnError('请输入文件编号');
        if (empty($content)) Json::ReturnError('请输入批复内容');
        if (empty($date)) Json::ReturnError('请输入批复日期');

        $act = 1;

        $replyid = Reply63Cls::Add($pid, $fid, $no, $content, $date, $uid, $act);
        Flow63Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::CHECK_3, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::CHECK_3, $fid, ProjectNodeCls::Name(ProjectNodeCls::CHECK_3) . '有审批答复');

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

        $this->Header();

        $view = View::Factory('ProjectReply63View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow64List()
    {
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow64List');

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

        $view->pid = $pid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow64()
    {
        $id = $this->Req('id', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow64');

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow64Cls::Instance()->Item($id);
        else $rs = Flow64Cls::GetLastItem($pid);

        $items = array();
        $totals = array();
        if (!empty($rs)) {
            $items = Json::Decode($rs['items']);
            $totals = Json::Decode($rs['totals']);
        }
        $view->items = $items;
        $view->total0 = isset($totals[0]) ? $totals[0] : '';
        $view->total1 = isset($totals[1]) ? $totals[1] : '';
        $view->total2 = isset($totals[2]) ? $totals[2] : '';
        $view->total3 = isset($totals[3]) ? $totals[3] : '';
        $view->total4 = isset($totals[4]) ? $totals[4] : '';

        $view->rs = $rs;

        $view->name = $name;
        $view->company = $company;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::CHECK_4);

        if ($id > 0) $view->approve = false;
        else $view->approve = ProjectStateCls::IsApprove(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::CHECK_4));

        $view->atts = Atts::UploadFixed(Atts::$flow64, AttachmentCls::GetFixedItems($pid, 64), false, false, true);

        $view->edit = false;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectFlow64Allow()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 1;

        $replyid = Reply64Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow64Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::CHECK_4, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::CHECK_4, $fid, ProjectNodeCls::Name(ProjectNodeCls::CHECK_4) . '审批通过');

        Json::ReturnSuccess();
    }

    public function OnProjectFlow64Deny()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 0;

        $replyid = Reply64Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow64Cls::SetReply($fid, $uid, $replyid);
        ProjectCls::SetNode($pid, ProjectNodeCls::CHECK_4, $fid, ProjectStateCls::DENY);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::CHECK_4, $fid, ProjectNodeCls::Name(ProjectNodeCls::CHECK_4) . '审批退回');

        Json::ReturnSuccess();
    }

    public function ProjectReply64()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow64Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $this->Header();

        $view = View::Factory('ProjectReply64');

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectReply64()
    {
        $fid = $this->Req('fid', 0, 'int');
        $no = $this->Req('no', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $uid = $this->Uid();

        $pid = Flow64Cls::Instance()->Pid($fid);

        if ($fid <= 0 || $pid <= 0 || $uid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($no)) Json::ReturnError('请输入文件编号');
        if (empty($content)) Json::ReturnError('请输入批复内容');
        if (empty($date)) Json::ReturnError('请输入批复日期');

        $act = 1;

        $replyid = Reply64Cls::Add($pid, $fid, $no, $content, $date, $uid, $act);
        Flow64Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::CHECK_4, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::CHECK_4, $fid, ProjectNodeCls::Name(ProjectNodeCls::CHECK_4) . '有审批答复');

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

        $this->Header();

        $view = View::Factory('ProjectReply64View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow71List()
    {
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow71List');

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

        $view->pid = $pid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow71()
    {
        $id = $this->Req('id', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow71');

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow71Cls::Instance()->Item($id);
        else $rs = Flow71Cls::GetLastItem($pid);

        $view->rs = $rs;

        $view->name = $name;
        $view->company = $company;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::RECORD_1);

        if ($id > 0) $view->approve = false;
        else $view->approve = ProjectStateCls::IsApprove(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::RECORD_1));

        $view->atts = Atts::UploadDynamic(AttachmentCls::GetDynamicItems($pid, 71), false, false, true);

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectFlow71Deny()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 0;

        $replyid = Reply71Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow71Cls::SetReply($fid, $uid, $replyid);
        ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_1, $fid, ProjectStateCls::DENY);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::RECORD_1, $fid, ProjectNodeCls::Name(ProjectNodeCls::RECORD_1) . '审批退回');

        Json::ReturnSuccess();
    }

    public function ProjectReply71()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow71Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $this->Header();

        $view = View::Factory('ProjectReply71');

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectReply71()
    {
        $fid = $this->Req('fid', 0, 'int');
        $no = $this->Req('no', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $uid = $this->Uid();

        $pid = Flow71Cls::Instance()->Pid($fid);

        if ($fid <= 0 || $pid <= 0 || $uid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($no)) Json::ReturnError('请输入文件编号');
        if (empty($content)) Json::ReturnError('请输入批复内容');
        if (empty($date)) Json::ReturnError('请输入批复日期');

        $act = 1;

        $replyid = Reply71Cls::Add($pid, $fid, $no, $content, $date, $uid, $act);
        Flow71Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_1, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::RECORD_1, $fid, ProjectNodeCls::Name(ProjectNodeCls::RECORD_1) . '有审批答复');

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

        $this->Header();

        $view = View::Factory('ProjectReply71View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow72List()
    {
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow72List');

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

        $view->pid = $pid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow72()
    {
        $id = $this->Req('id', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow72');

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow72Cls::Instance()->Item($id);
        else $rs = Flow72Cls::GetLastItem($pid);

        $view->rs = $rs;

        $view->name = $name;
        $view->company = $company;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::RECORD_2);

        if ($id > 0) $view->approve = false;
        else $view->approve = ProjectStateCls::IsApprove(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::RECORD_2));

        $view->atts = Atts::UploadDynamic(AttachmentCls::GetDynamicItems($pid, 72), false, false, true);

        $view->edit = false;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectFlow72Allow()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 1;

        $replyid = Reply72Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow72Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_2, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::RECORD_2, $fid, ProjectNodeCls::Name(ProjectNodeCls::RECORD_2) . '审批通过');

        Json::ReturnSuccess();
    }

    public function OnProjectFlow72Deny()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 0;

        $replyid = Reply72Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow72Cls::SetReply($fid, $uid, $replyid);
        ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_2, $fid, ProjectStateCls::DENY);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::RECORD_2, $fid, ProjectNodeCls::Name(ProjectNodeCls::RECORD_2) . '审批退回');

        Json::ReturnSuccess();
    }

    public function ProjectReply72()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow72Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $this->Header();

        $view = View::Factory('ProjectReply72');

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectReply72()
    {
        $fid = $this->Req('fid', 0, 'int');
        $no = $this->Req('no', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $uid = $this->Uid();

        $pid = Flow64Cls::Instance()->Pid($fid);

        if ($fid <= 0 || $pid <= 0 || $uid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($no)) Json::ReturnError('请输入文件编号');
        if (empty($content)) Json::ReturnError('请输入批复内容');
        if (empty($date)) Json::ReturnError('请输入批复日期');

        $act = 1;

        $replyid = Reply72Cls::Add($pid, $fid, $no, $content, $date, $uid, $act);
        Flow72Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_2, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::RECORD_2, $fid, ProjectNodeCls::Name(ProjectNodeCls::RECORD_2) . '有审批答复');

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

        $this->Header();

        $view = View::Factory('ProjectReply72View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow73List()
    {
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow73List');

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

        $view->pid = $pid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow73()
    {
        $id = $this->Req('id', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow73');

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow73Cls::Instance()->Item($id);
        else $rs = Flow73Cls::GetLastItem($pid);

        $view->rs = $rs;

        $view->name = $name;
        $view->company = $company;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::RECORD_3);

        if ($id > 0) $view->approve = false;
        else $view->approve = ProjectStateCls::IsApprove(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::RECORD_3));

        $view->atts = Atts::UploadDynamic(AttachmentCls::GetDynamicItems($pid, 73), false, false, true);

        $view->edit = false;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectFlow73Allow()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 1;

        $replyid = Reply73Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow73Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_3, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::RECORD_3, $fid, ProjectNodeCls::Name(ProjectNodeCls::RECORD_3) . '审批通过');

        Json::ReturnSuccess();
    }

    public function OnProjectFlow73Deny()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 0;

        $replyid = Reply73Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow73Cls::SetReply($fid, $uid, $replyid);
        ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_3, $fid, ProjectStateCls::DENY);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::RECORD_3, $fid, ProjectNodeCls::Name(ProjectNodeCls::RECORD_3) . '审批退回');

        Json::ReturnSuccess();
    }

    public function ProjectReply73()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow73Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $this->Header();

        $view = View::Factory('ProjectReply73');

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectReply73()
    {
        $fid = $this->Req('fid', 0, 'int');
        $no = $this->Req('no', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $uid = $this->Uid();

        $pid = Flow64Cls::Instance()->Pid($fid);

        if ($fid <= 0 || $pid <= 0 || $uid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($no)) Json::ReturnError('请输入文件编号');
        if (empty($content)) Json::ReturnError('请输入批复内容');
        if (empty($date)) Json::ReturnError('请输入批复日期');

        $act = 1;

        $replyid = Reply73Cls::Add($pid, $fid, $no, $content, $date, $uid, $act);
        Flow73Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_3, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::RECORD_3, $fid, ProjectNodeCls::Name(ProjectNodeCls::RECORD_3) . '有审批答复');

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

        $this->Header();

        $view = View::Factory('ProjectReply73View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow74List()
    {
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow74List');

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

        $view->pid = $pid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow74()
    {
        $id = $this->Req('id', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow74');

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow74Cls::Instance()->Item($id);
        else $rs = Flow74Cls::GetLastItem($pid);

        $view->rs = $rs;

        $view->name = $name;
        $view->company = $company;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::RECORD_4);

        if ($id > 0) $view->approve = false;
        else $view->approve = ProjectStateCls::IsApprove(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::RECORD_4));

        $view->atts = Atts::UploadDynamic(AttachmentCls::GetDynamicItems($pid, 74), false, false, true);

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectFlow74Allow()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 1;

        $replyid = Reply74Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow74Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_4, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::RECORD_4, $fid, ProjectNodeCls::Name(ProjectNodeCls::RECORD_4) . '审批通过');

        Json::ReturnSuccess();
    }

    public function OnProjectFlow74Deny()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 0;

        $replyid = Reply74Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow74Cls::SetReply($fid, $uid, $replyid);
        ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_4, $fid, ProjectStateCls::DENY);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::RECORD_4, $fid, ProjectNodeCls::Name(ProjectNodeCls::RECORD_4) . '审批退回');

        Json::ReturnSuccess();
    }

    public function ProjectReply74()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow74Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $this->Header();

        $view = View::Factory('ProjectReply74');

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectReply74()
    {
        $fid = $this->Req('fid', 0, 'int');
        $no = $this->Req('no', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $uid = $this->Uid();

        $pid = Flow74Cls::Instance()->Pid($fid);

        if ($fid <= 0 || $pid <= 0 || $uid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($no)) Json::ReturnError('请输入文件编号');
        if (empty($content)) Json::ReturnError('请输入批复内容');
        if (empty($date)) Json::ReturnError('请输入批复日期');

        $act = 1;

        $replyid = Reply74Cls::Add($pid, $fid, $no, $content, $date, $uid, $act);
        Flow74Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_4, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::RECORD_4, $fid, ProjectNodeCls::Name(ProjectNodeCls::RECORD_4) . '有审批答复');

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

        $this->Header();

        $view = View::Factory('ProjectReply74View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow75List()
    {
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow75List');

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

        $view->pid = $pid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow75()
    {
        $id = $this->Req('id', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow75');

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow75Cls::Instance()->Item($id);
        else $rs = Flow75Cls::GetLastItem($pid);

        $view->rs = $rs;

        $view->name = $name;
        $view->company = $company;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::RECORD_5);

        if ($id > 0) $view->approve = false;
        else $view->approve = ProjectStateCls::IsApprove(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::RECORD_5));

        $view->atts = Atts::UploadDynamic(AttachmentCls::GetDynamicItems($pid, 75), false, false, true);

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectFlow75Allow()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 1;

        $replyid = Reply75Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow75Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_5, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::RECORD_5, $fid, ProjectNodeCls::Name(ProjectNodeCls::RECORD_5) . '审批通过');

        Json::ReturnSuccess();
    }

    public function OnProjectFlow75Deny()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 0;

        $replyid = Reply75Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow75Cls::SetReply($fid, $uid, $replyid);
        ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_5, $fid, ProjectStateCls::DENY);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::RECORD_5, $fid, ProjectNodeCls::Name(ProjectNodeCls::RECORD_5) . '审批退回');

        Json::ReturnSuccess();
    }

    public function ProjectReply75()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow75Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $this->Header();

        $view = View::Factory('ProjectReply75');

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectReply75()
    {
        $fid = $this->Req('fid', 0, 'int');
        $no = $this->Req('no', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $uid = $this->Uid();

        $pid = Flow75Cls::Instance()->Pid($fid);

        if ($fid <= 0 || $pid <= 0 || $uid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($no)) Json::ReturnError('请输入文件编号');
        if (empty($content)) Json::ReturnError('请输入批复内容');
        if (empty($date)) Json::ReturnError('请输入批复日期');

        $act = 1;

        $replyid = Reply75Cls::Add($pid, $fid, $no, $content, $date, $uid, $act);
        Flow75Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_5, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::RECORD_5, $fid, ProjectNodeCls::Name(ProjectNodeCls::RECORD_5) . '有审批答复');

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

        $this->Header();

        $view = View::Factory('ProjectReply75View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow76List()
    {
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow76List');

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

        $view->pid = $pid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow76()
    {
        $id = $this->Req('id', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow76');

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow76Cls::Instance()->Item($id);
        else $rs = Flow76Cls::GetLastItem($pid);

        $view->rs = $rs;

        $view->name = $name;
        $view->company = $company;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::RECORD_6);

        if ($id > 0) $view->approve = false;
        else $view->approve = ProjectStateCls::IsApprove(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::RECORD_6));

        $view->atts = Atts::UploadDynamic(AttachmentCls::GetDynamicItems($pid, 76), false, false, true);

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectFlow76Allow()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 1;

        $replyid = Reply76Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow76Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_6, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::RECORD_6, $fid, ProjectNodeCls::Name(ProjectNodeCls::RECORD_6) . '审批通过');

        Json::ReturnSuccess();
    }

    public function OnProjectFlow76Deny()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 0;

        $replyid = Reply76Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow76Cls::SetReply($fid, $uid, $replyid);
        ProjectCls::SetNode($pid, ProjectNodeCls::RECORD_6, $fid, ProjectStateCls::DENY);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::RECORD_6, $fid, ProjectNodeCls::Name(ProjectNodeCls::RECORD_6) . '审批退回');

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

        $this->Header();

        $view = View::Factory('ProjectReply76View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow8List()
    {
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow8List');

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

        $view->pid = $pid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow8()
    {
        $id = $this->Req('id', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow8');

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow8Cls::Instance()->Item($id);
        else $rs = Flow8Cls::GetLastItem($pid);

        $items = array();
        if (!empty($rs)) {
            $items = Json::Decode($rs['items']);
        }
        $view->items = $items;

        $view->rs = $rs;

        $view->name = $name;
        $view->company = $company;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::PROGRESS);

        if ($id > 0) $view->approve = false;
        else $view->approve = ProjectStateCls::IsApprove(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::PROGRESS));

        $view->atts = Atts::UploadDynamic(AttachmentCls::GetDynamicItems($pid, 8), false, false, true);

        $view->edit = false;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectFlow8Deny()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 0;

        $replyid = Reply8Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow8Cls::SetReply($fid, $uid, $replyid);
        ProjectCls::SetNode($pid, ProjectNodeCls::PROGRESS, $fid, ProjectStateCls::DENY);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::PROGRESS, $fid, ProjectNodeCls::Name(ProjectNodeCls::PROGRESS) . '审批退回');

        Json::ReturnSuccess();
    }

    public function ProjectReply8()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow8Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $this->Header();

        $view = View::Factory('ProjectReply8');

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectReply8()
    {
        $fid = $this->Req('fid', 0, 'int');
        $no = $this->Req('no', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $uid = $this->Uid();

        $pid = Flow8Cls::Instance()->Pid($fid);

        if ($fid <= 0 || $pid <= 0 || $uid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($no)) Json::ReturnError('请输入文件编号');
        if (empty($content)) Json::ReturnError('请输入批复内容');
        if (empty($date)) Json::ReturnError('请输入批复日期');

        $act = 1;

        $replyid = Reply8Cls::Add($pid, $fid, $no, $content, $date, $uid, $act);
        Flow8Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::PROGRESS, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::PROGRESS, $fid, ProjectNodeCls::Name(ProjectNodeCls::PROGRESS) . '有审批答复');

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

        $this->Header();

        $view = View::Factory('ProjectReply8View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow91List()
    {
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow91List');

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

        $view->pid = $pid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow91()
    {
        $id = $this->Req('id', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow91');

        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow91Cls::Instance()->Item($id);
        else $rs = Flow91Cls::GetLastItem($pid);

        $items = array();
        if (!empty($rs)) {
            $items = Json::Decode($rs['items']);
        }
        $view->items = $items;

        $view->rs = $rs;

        $view->name = $name;
        $view->company = $company;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::ACCEPT_1);

        if ($id > 0) $view->approve = false;
        else $view->approve = ProjectStateCls::IsApprove(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::ACCEPT_1));

        $view->atts = Atts::UploadDynamic(AttachmentCls::GetDynamicItems($pid, 91), false, false, true);

        $view->edit = false;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectFlow91Deny()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 0;

        $replyid = Reply91Cls::Add($pid, $fid, '', $content, '', $uid, $act);
        Flow91Cls::SetReply($fid, $uid, $replyid);
        ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_1, $fid, ProjectStateCls::DENY);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::ACCEPT_1, $fid, ProjectNodeCls::Name(ProjectNodeCls::ACCEPT_1) . '审批退回');

        Json::ReturnSuccess();
    }

    public function ProjectReply91()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow91Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $this->Header();

        $view = View::Factory('ProjectReply91');

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectReply91()
    {
        $fid = $this->Req('fid', 0, 'int');
        $no = $this->Req('no', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $uid = $this->Uid();

        $pid = Flow91Cls::Instance()->Pid($fid);

        if ($fid <= 0 || $pid <= 0 || $uid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($no)) Json::ReturnError('请输入文件编号');
        if (empty($content)) Json::ReturnError('请输入批复内容');
        if (empty($date)) Json::ReturnError('请输入批复日期');

        $act = 1;

        $replyid = Reply91Cls::Add($pid, $fid, $no, $content, $date, $uid, $act);
        Flow91Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_1, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::ACCEPT_1, $fid, ProjectNodeCls::Name(ProjectNodeCls::ACCEPT_1) . '有审批答复');

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

        $this->Header();

        $view = View::Factory('ProjectReply91View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow921List()
    {
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow921List');

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

        $view->pid = $pid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow921()
    {
        $id = $this->Req('id', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow921');

        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow921Cls::Instance()->Item($id);
        else $rs = Flow921Cls::GetLastItem($pid);

        $view->rs = $rs;

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::ACCEPT_21);

        if ($id > 0) $view->approve = false;
        else $view->approve = ProjectStateCls::IsApprove(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::ACCEPT_21));

        $view->atts = Atts::UploadFixed(Atts::$flow921, AttachmentCls::GetFixedItems($pid, 921), false, false, true);

        $view->edit = false;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectFlow921Deny()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 0;
        $replyid = Reply921Cls::Add($pid, $fid, '', $content, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', $uid, $act);
        Flow921Cls::SetReply($fid, $uid, $replyid);
        ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_21, $fid, ProjectStateCls::DENY);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::ACCEPT_21, $fid, ProjectNodeCls::Name(ProjectNodeCls::ACCEPT_21) . '审批退回');

        Json::ReturnSuccess();
    }

    public function ProjectReply921()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow921Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $this->Header();

        $view = View::Factory('ProjectReply921');

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectReply921()
    {
        $fid = $this->Req('fid', 0, 'int');
        $no = $this->Req('no', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $proj = $this->Req('proj', '', 'str');
        $stage = $this->Req('stage', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $signer_code = $this->Req('signer_code', '', 'str');
        $owner = $this->Req('owner', '', 'str');
        $owner_code = $this->Req('owner_code', '', 'str');
        $comp = $this->Req('comp', '', 'str');
        $c1 = $this->Req('c1', '', 'str');
        $c2 = $this->Req('c2', '', 'str');
        $c3 = $this->Req('c3', '', 'str');
        $c4 = $this->Req('c4', '', 'str');
        $c5 = $this->Req('c5', '', 'str');
        $c6 = $this->Req('c6', '', 'str');
        $c7 = $this->Req('c7', '', 'str');
        $c8 = $this->Req('c8', '', 'str');
        $uid = $this->Uid();

        $pid = Flow921Cls::Instance()->Pid($fid);

        if ($fid <= 0 || $pid <= 0 || $uid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($proj)) Json::ReturnError('请输入工程');
        if (empty($stage)) Json::ReturnError('请输入阶段');
        if (empty($signer)) Json::ReturnError('请输入编制');
        if (empty($signer_code)) Json::ReturnError('请输入编制质量监督员证书号');
        if (empty($owner)) Json::ReturnError('请输入审定');
        if (empty($owner_code)) Json::ReturnError('请输入审定质量监督员证书号');
        if (empty($comp)) Json::ReturnError('请输入工程质量监督机构名称');
        if (empty($date)) Json::ReturnError('请输入日期');

        if (empty($c1)) Json::ReturnError('请输入工程概况');
        if (empty($c2)) Json::ReturnError('请输入质量监督情况');
        if (empty($c3)) Json::ReturnError('请输入项目划分情况');
        if (empty($c4)) Json::ReturnError('请输入质量数据分析');
        if (empty($c5)) Json::ReturnError('请输入质量事故和缺陷处理情况');
        if (empty($c6)) Json::ReturnError('请输入遗留问题的说明');
        if (empty($c7)) Json::ReturnError('请输入质量评价意见');
        if (empty($c8)) Json::ReturnError('请输入建议');

        $act = 1;

        $replyid = Reply921Cls::Add($pid, $fid, $no, $content, $date, $proj, $stage, $signer, $signer_code, $owner, $owner_code, $comp, $c1, $c2, $c3, $c4, $c5, $c6, $c7, $c8, $uid, $act);
        Flow921Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_21, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::ACCEPT_21, $fid, ProjectNodeCls::Name(ProjectNodeCls::ACCEPT_21) . '有审批答复');

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

        $this->Header();

        $view = View::Factory('ProjectReply921View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow922List()
    {
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow922List');

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

        $view->pid = $pid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow922()
    {
        $id = $this->Req('id', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow922');

        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow922Cls::Instance()->Item($id);
        else $rs = Flow922Cls::GetLastItem($pid);

        $view->rs = $rs;

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::ACCEPT_22);

        if ($id > 0) $view->approve = false;
        else $view->approve = ProjectStateCls::IsApprove(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::ACCEPT_22));

        $view->atts = Atts::UploadFixed(Atts::$flow922, AttachmentCls::GetFixedItems($pid, 922), false, false, true);

        $view->edit = false;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectFlow922Deny()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 0;
        $replyid = Reply922Cls::Add($pid, $fid, '', $content, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', $uid, $act);
        Flow922Cls::SetReply($fid, $uid, $replyid);
        ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_22, $fid, ProjectStateCls::DENY);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::ACCEPT_22, $fid, ProjectNodeCls::Name(ProjectNodeCls::ACCEPT_22) . '审批退回');

        Json::ReturnSuccess();
    }

    public function ProjectReply922()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow922Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $this->Header();

        $view = View::Factory('ProjectReply922');

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectReply922()
    {
        $fid = $this->Req('fid', 0, 'int');
        $no = $this->Req('no', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $proj = $this->Req('proj', '', 'str');
        $stage = $this->Req('stage', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $signer_code = $this->Req('signer_code', '', 'str');
        $owner = $this->Req('owner', '', 'str');
        $owner_code = $this->Req('owner_code', '', 'str');
        $comp = $this->Req('comp', '', 'str');
        $c1 = $this->Req('c1', '', 'str');
        $c2 = $this->Req('c2', '', 'str');
        $c3 = $this->Req('c3', '', 'str');
        $c4 = $this->Req('c4', '', 'str');
        $c5 = $this->Req('c5', '', 'str');
        $c6 = $this->Req('c6', '', 'str');
        $c7 = $this->Req('c7', '', 'str');
        $c8 = $this->Req('c8', '', 'str');
        $uid = $this->Uid();

        $pid = Flow922Cls::Instance()->Pid($fid);

        if ($fid <= 0 || $pid <= 0 || $uid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($proj)) Json::ReturnError('请输入工程');
        if (empty($stage)) Json::ReturnError('请输入阶段');
        if (empty($signer)) Json::ReturnError('请输入编制');
        if (empty($signer_code)) Json::ReturnError('请输入编制质量监督员证书号');
        if (empty($owner)) Json::ReturnError('请输入审定');
        if (empty($owner_code)) Json::ReturnError('请输入审定质量监督员证书号');
        if (empty($comp)) Json::ReturnError('请输入工程质量监督机构名称');
        if (empty($date)) Json::ReturnError('请输入日期');

        if (empty($c1)) Json::ReturnError('请输入工程概况');
        if (empty($c2)) Json::ReturnError('请输入质量监督情况');
        if (empty($c3)) Json::ReturnError('请输入项目划分情况');
        if (empty($c4)) Json::ReturnError('请输入质量数据分析');
        if (empty($c5)) Json::ReturnError('请输入质量事故和缺陷处理情况');
        if (empty($c6)) Json::ReturnError('请输入遗留问题的说明');
        if (empty($c7)) Json::ReturnError('请输入质量评价意见');
        if (empty($c8)) Json::ReturnError('请输入建议');

        $act = 1;

        $replyid = Reply922Cls::Add($pid, $fid, $no, $content, $date, $proj, $stage, $signer, $signer_code, $owner, $owner_code, $comp, $c1, $c2, $c3, $c4, $c5, $c6, $c7, $c8, $uid, $act);
        Flow922Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_22, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::ACCEPT_22, $fid, ProjectNodeCls::Name(ProjectNodeCls::ACCEPT_22) . '有审批答复');

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

        $this->Header();

        $view = View::Factory('ProjectReply922View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow923List()
    {
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow923List');

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

        $view->pid = $pid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow923()
    {
        $id = $this->Req('id', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow923');

        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow923Cls::Instance()->Item($id);
        else $rs = Flow923Cls::GetLastItem($pid);

        $view->rs = $rs;

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::ACCEPT_23);

        if ($id > 0) $view->approve = false;
        else $view->approve = ProjectStateCls::IsApprove(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::ACCEPT_23));

        $view->atts = Atts::UploadFixed(Atts::$flow923, AttachmentCls::GetFixedItems($pid, 923), false, false, true);

        $view->edit = false;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectFlow923Deny()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 0;
        $replyid = Reply923Cls::Add($pid, $fid, '', $content, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', $uid, $act);
        Flow923Cls::SetReply($fid, $uid, $replyid);
        ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_23, $fid, ProjectStateCls::DENY);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::ACCEPT_23, $fid, ProjectNodeCls::Name(ProjectNodeCls::ACCEPT_23) . '审批退回');

        Json::ReturnSuccess();
    }

    public function ProjectReply923()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow923Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $this->Header();

        $view = View::Factory('ProjectReply923');

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectReply923()
    {
        $fid = $this->Req('fid', 0, 'int');
        $no = $this->Req('no', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $proj = $this->Req('proj', '', 'str');
        $stage = $this->Req('stage', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $signer_code = $this->Req('signer_code', '', 'str');
        $owner = $this->Req('owner', '', 'str');
        $owner_code = $this->Req('owner_code', '', 'str');
        $comp = $this->Req('comp', '', 'str');
        $c1 = $this->Req('c1', '', 'str');
        $c2 = $this->Req('c2', '', 'str');
        $c3 = $this->Req('c3', '', 'str');
        $c4 = $this->Req('c4', '', 'str');
        $c5 = $this->Req('c5', '', 'str');
        $c6 = $this->Req('c6', '', 'str');
        $c7 = $this->Req('c7', '', 'str');
        $c8 = $this->Req('c8', '', 'str');
        $uid = $this->Uid();

        $pid = Flow923Cls::Instance()->Pid($fid);

        if ($fid <= 0 || $pid <= 0 || $uid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($proj)) Json::ReturnError('请输入工程');
        if (empty($stage)) Json::ReturnError('请输入阶段');
        if (empty($signer)) Json::ReturnError('请输入编制');
        if (empty($signer_code)) Json::ReturnError('请输入编制质量监督员证书号');
        if (empty($owner)) Json::ReturnError('请输入审定');
        if (empty($owner_code)) Json::ReturnError('请输入审定质量监督员证书号');
        if (empty($comp)) Json::ReturnError('请输入工程质量监督机构名称');
        if (empty($date)) Json::ReturnError('请输入日期');

        if (empty($c1)) Json::ReturnError('请输入工程概况');
        if (empty($c2)) Json::ReturnError('请输入质量监督情况');
        if (empty($c3)) Json::ReturnError('请输入项目划分情况');
        if (empty($c4)) Json::ReturnError('请输入质量数据分析');
        if (empty($c5)) Json::ReturnError('请输入质量事故和缺陷处理情况');
        if (empty($c6)) Json::ReturnError('请输入遗留问题的说明');
        if (empty($c7)) Json::ReturnError('请输入质量评价意见');
        if (empty($c8)) Json::ReturnError('请输入建议');

        $act = 1;

        $replyid = Reply923Cls::Add($pid, $fid, $no, $content, $date, $proj, $stage, $signer, $signer_code, $owner, $owner_code, $comp, $c1, $c2, $c3, $c4, $c5, $c6, $c7, $c8, $uid, $act);
        Flow923Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_23, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::ACCEPT_23, $fid, ProjectNodeCls::Name(ProjectNodeCls::ACCEPT_23) . '有审批答复');

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

        $this->Header();

        $view = View::Factory('ProjectReply923View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow924List()
    {
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow924List');

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

        $view->pid = $pid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow924()
    {
        $id = $this->Req('id', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow924');

        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow924Cls::Instance()->Item($id);
        else $rs = Flow924Cls::GetLastItem($pid);

        $view->rs = $rs;

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::ACCEPT_24);

        if ($id > 0) $view->approve = false;
        else $view->approve = ProjectStateCls::IsApprove(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::ACCEPT_24));

        $view->atts = Atts::UploadFixed(Atts::$flow924, AttachmentCls::GetFixedItems($pid, 924), false, false, true);

        $view->edit = false;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectFlow924Deny()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 0;
        $replyid = Reply924Cls::Add($pid, $fid, '', $content, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', $uid, $act);
        Flow924Cls::SetReply($fid, $uid, $replyid);
        ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_24, $fid, ProjectStateCls::DENY);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::ACCEPT_24, $fid, ProjectNodeCls::Name(ProjectNodeCls::ACCEPT_24) . '审批退回');

        Json::ReturnSuccess();
    }

    public function ProjectReply924()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow924Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $this->Header();

        $view = View::Factory('ProjectReply924');

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;
        $view->pid = $pid;

        $view->atts = Atts::UploadFixed(Atts::$flow9242, AttachmentCls::GetFixedItems($pid, 9242), true, false, true);

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectReply924()
    {
        $fid = $this->Req('fid', 0, 'int');
        $no = $this->Req('no', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $proj = $this->Req('proj', '', 'str');
        $stage = $this->Req('stage', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $signer_code = $this->Req('signer_code', '', 'str');
        $owner = $this->Req('owner', '', 'str');
        $owner_code = $this->Req('owner_code', '', 'str');
        $approver = $this->Req('approver', '', 'str');
        $approver_code = $this->Req('approver_code', '', 'str');
        $comp = $this->Req('comp', '', 'str');
        $c1 = $this->Req('c1', '', 'str');
        $c2 = $this->Req('c2', '', 'str');
        $c3 = $this->Req('c3', '', 'str');
        $c4 = $this->Req('c4', '', 'str');
        $c5 = $this->Req('c5', '', 'str');
        $c6 = $this->Req('c6', '', 'str');
        $c7 = $this->Req('c7', '', 'str');
        $c8 = $this->Req('c8', '', 'str');
        $c9 = $this->Req('c9', '', 'str');
        $c10 = $this->Req('c10', '', 'str');
        $uid = $this->Uid();

        $pid = Flow924Cls::Instance()->Pid($fid);

        if ($fid <= 0 || $pid <= 0 || $uid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($proj)) Json::ReturnError('请输入工程');
        if (empty($signer)) Json::ReturnError('请输入编制');
        if (empty($signer_code)) Json::ReturnError('请输入编制质量监督员证书号');
        if (empty($owner)) Json::ReturnError('请输入审定');
        if (empty($owner_code)) Json::ReturnError('请输入审定质量监督员证书号');
        if (empty($approver)) Json::ReturnError('请输入批准');
        if (empty($approver_code)) Json::ReturnError('请输入批准质量监督员证书号');
        if (empty($comp)) Json::ReturnError('请输入工程质量监督机构名称');
        if (empty($date)) Json::ReturnError('请输入日期');

        if (empty($c1)) Json::ReturnError('请输入工程概况');
        if (empty($c2)) Json::ReturnError('请输入质量监督工作');
        if (empty($c3)) Json::ReturnError('请输入参建单位质量管理体系');
        if (empty($c4)) Json::ReturnError('请输入工程项目划分确认');
        if (empty($c5)) Json::ReturnError('请输入工程质量检测');
        if (empty($c6)) Json::ReturnError('请输入工程质量事故和质量缺陷处理');
        if (empty($c7)) Json::ReturnError('请输入工程质量核备与核定');
        if (empty($c8)) Json::ReturnError('请输入工程质量结论意见');
        if (empty($c9)) Json::ReturnError('请输入检验');

        $act = 1;

        $replyid = Reply924Cls::Add($pid, $fid, $no, $content, $date, $proj, $stage, $signer, $signer_code, $owner, $owner_code, $approver, $approver_code, $comp, $c1, $c2, $c3, $c4, $c5, $c6, $c7, $c8, $c9, $c10, $uid, $act);
        Flow924Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_24, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::ACCEPT_24, $fid, ProjectNodeCls::Name(ProjectNodeCls::ACCEPT_24) . '有审批答复');

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

        $this->Header();

        $view = View::Factory('ProjectReply924View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow925List()
    {
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow925List');

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

        $view->pid = $pid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow925()
    {
        $id = $this->Req('id', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow925');

        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($id > 0) $rs = Flow925Cls::Instance()->Item($id);
        else $rs = Flow925Cls::GetLastItem($pid);

        $view->rs = $rs;

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::ACCEPT_25);

        if ($id > 0) $view->approve = false;
        else $view->approve = ProjectStateCls::IsApprove(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::ACCEPT_25));

        $view->atts = Atts::UploadFixed(Atts::$flow925, AttachmentCls::GetFixedItems($pid, 925), false, false, true);

        $view->edit = false;

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectFlow925Deny()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $content = $this->Req('content', '', 'str');
        $uid = $this->Uid();

        if ($pid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($content)) Json::ReturnError('请输入审批意见');

        $act = 0;
        $replyid = Reply925Cls::Add($pid, $fid, '', $content, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', $uid, $act);
        Flow925Cls::SetReply($fid, $uid, $replyid);
        ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_25, $fid, ProjectStateCls::DENY);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::ACCEPT_25, $fid, ProjectNodeCls::Name(ProjectNodeCls::ACCEPT_25) . '审批退回');

        Json::ReturnSuccess();
    }

    public function ProjectReply925()
    {
        $fid = $this->Req('fid', 0, 'int');

        $pid = Flow925Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $this->Header();

        $view = View::Factory('ProjectReply925');

        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;
        $view->pid = $pid;

        $view->atts = Atts::UploadFixed(Atts::$flow9252, AttachmentCls::GetFixedItems($pid, 9252), true, false, true);

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectReply925()
    {
        $fid = $this->Req('fid', 0, 'int');
        $no = $this->Req('no', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $proj = $this->Req('proj', '', 'str');
        $stage = $this->Req('stage', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $signer_code = $this->Req('signer_code', '', 'str');
        $owner = $this->Req('owner', '', 'str');
        $owner_code = $this->Req('owner_code', '', 'str');
        $approver = $this->Req('approver', '', 'str');
        $approver_code = $this->Req('approver_code', '', 'str');
        $comp = $this->Req('comp', '', 'str');
        $c1 = $this->Req('c1', '', 'str');
        $c2 = $this->Req('c2', '', 'str');
        $c3 = $this->Req('c3', '', 'str');
        $c4 = $this->Req('c4', '', 'str');
        $c5 = $this->Req('c5', '', 'str');
        $c6 = $this->Req('c6', '', 'str');
        $c7 = $this->Req('c7', '', 'str');
        $c8 = $this->Req('c8', '', 'str');
        $c9 = $this->Req('c9', '', 'str');
        $c10 = $this->Req('c10', '', 'str');
        $uid = $this->Uid();

        $pid = Flow925Cls::Instance()->Pid($fid);

        if ($fid <= 0 || $pid <= 0 || $uid <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($proj)) Json::ReturnError('请输入工程');
        if (empty($signer)) Json::ReturnError('请输入编制');
        if (empty($signer_code)) Json::ReturnError('请输入编制质量监督员证书号');
        if (empty($owner)) Json::ReturnError('请输入审定');
        if (empty($owner_code)) Json::ReturnError('请输入审定质量监督员证书号');
        if (empty($approver)) Json::ReturnError('请输入批准');
        if (empty($approver_code)) Json::ReturnError('请输入批准质量监督员证书号');
        if (empty($comp)) Json::ReturnError('请输入工程质量监督机构名称');
        if (empty($date)) Json::ReturnError('请输入日期');

        if (empty($c1)) Json::ReturnError('请输入工程概况');
        if (empty($c2)) Json::ReturnError('请输入质量监督工作');
        if (empty($c3)) Json::ReturnError('请输入参建单位质量管理体系');
        if (empty($c4)) Json::ReturnError('请输入工程项目划分确认');
        if (empty($c5)) Json::ReturnError('请输入工程质量检测');
        if (empty($c6)) Json::ReturnError('请输入工程质量事故和质量缺陷处理');
        if (empty($c7)) Json::ReturnError('请输入工程质量核备与核定');
        if (empty($c8)) Json::ReturnError('请输入工程质量结论意见');
        if (empty($c9)) Json::ReturnError('请输入检验');

        $act = 1;

        $replyid = Reply925Cls::Add($pid, $fid, $no, $content, $date, $proj, $stage, $signer, $signer_code, $owner, $owner_code, $approver, $approver_code, $comp, $c1, $c2, $c3, $c4, $c5, $c6, $c7, $c8, $c9, $c10, $uid, $act);
        Flow925Cls::SetReply($fid, $uid, $replyid, $act);
        ProjectCls::SetNode($pid, ProjectNodeCls::ACCEPT_25, $fid, ProjectStateCls::ALLOW);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::ACCEPT_25, $fid, ProjectNodeCls::Name(ProjectNodeCls::ACCEPT_25) . '有审批答复');

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

        $this->Header();

        $view = View::Factory('ProjectReply925View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow9999List()
    {
        $pid = $this->Req('pid', 0, 'int');

        $this->Header();

        $view = View::Factory('ProjectFlow9999List');

        $new = true;echo 'dd';
        $rl = Flow9999Cls::GetLastItem($pid);
        if (!(!empty($rl) && count($rl) > 0 && $rl['replyid'] <= 0)) {
            $rl = array();
        }
        $rs = Flow9999Cls::GetReplyItems($pid);echo 'cc';

        $view->rl = $rl;
        $view->rs = $rs;
        $view->new = $new;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::INSPECT);

        $view->pid = $pid;

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectFlow9999()
    {
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');

        if ($fid > 0) $pid = Flow9999Cls::Instance()->Pid($fid);
        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        if ($fid > 0) $rs = Flow9999Cls::Instance()->Item($fid);
        else $rs = Flow9999Cls::GetLastItem($pid);

        $no = '';
        $code = '';
        $content = '';
        $date = '';
        $personals = '';
        $progress = '';
        $writer = '';
        $writer_date = '';
        $signer = '';
        $signer_date = '';

        $edit = $fid <= 0;

        if ($fid > 0 && !empty($rs) && count($rs) > 0) {
            $name = $rs['name'];
            $no = $rs['no'];
            $code = $rs['code'];
            $content = $rs['content'];
            $date = $rs['date'];
            $personals = $rs['personals'];
            $progress = $rs['progress'];
            $writer = $rs['writer'];
            $writer_date = $rs['writer_date'];
            $signer = $rs['signer'];
            $signer_date = $rs['signer_date'];

            // $edit = ProjectStateCls::IsEdit(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::SUGGEST));
            $edit = $rs['replyid'] <= 0;
        }

        $this->Header();

        $view = View::Factory('ProjectFlow9999');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->edit = $edit;
        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::INSPECT);

        $view->no = $no;
        $view->code = $code;
        $view->content = $content;
        $view->date = $date;
        $view->personals = $personals;
        $view->progress = $progress;
        $view->writer = $writer;
        $view->writer_date = $writer_date;
        $view->signer = $signer;
        $view->signer_date = $signer_date;

        $view->fid = $fid;
        $view->pid = $pid;
        $view->atts = Atts::UploadDynamic(AttachmentCls::GetDynamicItems($pid, 3), true, false, true);

        if ($fid > 0) $view->approve = false;
        else $view->approve = ProjectStateCls::IsApprove(ProjectCls::Instance()->StateId($pid, ProjectNodeCls::INSPECT));

        echo $view->Render();

        $this->Footer();
    }

    public function ProjectReply9999()
    {
        $id = $this->Req('id', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');

        $fid = $replyid = 0;

        if ($id > 0) $rs = Flow9999Cls::Instance()->Item($id);
        else $rs = Flow9999Cls::GetLastItem($pid);

        if (!empty($rs)) {
            $fid = $rs['id'];
            $replyid = $rs['replyid'];
        } else {
            Url::RedirectUrl('?m=Project&a=ProjectReply9999&pid=' . $pid);
        }

        $gc = ProjectCls::GetGroupCompany($pid);
        $name = ProjectCls::Instance()->Name($pid);
        $company = ProjectCls::Instance()->Company($pid);

        $this->Header();

        $view = View::Factory('ProjectReply9999');

        $view->rs = Reply9999Cls::Instance()->Item($replyid);
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->state = ProjectCls::Instance()->State($pid, ProjectNodeCls::INSPECT);

        $view->fid = $fid;
        $view->pid = $pid;
        $view->atts = Atts::UploadDynamic(AttachmentCls::GetDynamicItems($pid, 4), false, false, true);

        echo $view->Render();

        $this->Footer();
    }

    public function OnProjectFlow9999()
    {
        $pid = $this->Req('pid', 0, 'int');

        $name = $this->Req('name', '', 'str');
        $no = $this->Req('no', '', 'str');
        $content = $this->Req('content', '', 'str');
        $date = $this->Req('date', '', 'str');
        $uid = $this->Uid();

        $code = $this->Req('code', '', 'str');
        $personals = $this->Req('personals', '', 'str');
        $progress = $this->Req('progress', '', 'str');
        $writer = $this->Req('writer', '', 'str');
        $writer_date = $this->Req('writer_date', '', 'str');
        $signer = $this->Req('signer', '', 'str');
        $signer_date = $this->Req('signer_date', '', 'str');

        //$pid = Flow4Cls::Instance()->Pid($fid);

        if ($pid <= 0 || $uid <= 0) Json::ReturnError(ALERT_ERROR);

        if (empty($name)) Json::ReturnError('请输入工程名称');
        if (empty($no)) Json::ReturnError('请输入检查表编号');
        if (empty($code)) Json::ReturnError('质量监督人员和证号');
        if (empty($date)) Json::ReturnError('请输入检查时间');
        if (empty($personals)) Json::ReturnError('请输入参建单位人员');
        if (empty($progress)) Json::ReturnError('请输入工程形象进度');
        //if (empty($writer)) Json::ReturnError('请输入被检查单位签收人');
        //if (empty($writer_date)) Json::ReturnError('请输入被检查单位签收人日期');
        //if (empty($signer)) Json::ReturnError('请输入填写人');
        //if (empty($signer_date)) Json::ReturnError('请输入填写人日期');

        $act = 1;

        try {
            $id = Flow9999Cls::Add($pid, $name, $no, $code, $date, $content, $personals, $progress, $writer, $writer_date, $signer, $signer_date);
        } catch (Exception $e) {
            Json::ReturnError($e->getMessage());
        }

        ProjectCls::SetNode($pid, ProjectNodeCls::INSPECT, $id, ProjectStateCls::APPROVE);

        MsgCls::Add(1, MsgDirectCls::FROM_USER, $this->Uid(), $pid, '管理员', ProjectCls::Instance()->Name($pid), ProjectNodeCls::INSPECT, $pid, ProjectNodeCls::Name(ProjectNodeCls::INSPECT) . '有新消息');

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

        $this->Header();

        $view = View::Factory('ProjectReply9999View');

        $view->rs = $rs;
        $view->gc = $gc;
        $view->name = $name;
        $view->company = $company;

        $view->fid = $fid;

        echo $view->Render();

        $this->Footer();
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
                        if ($col == $maxcols - count($data[$i]) + 1) $table .= ' colspan="' . ($maxcols - count($data[$i]) + 1) . '"';
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
                    $table .= ($edit ? '<input type="text" class="c item1" value="' . $item1 . '" size="3"/>' : $item1);
                    $table .= '</td>';
                    $table .= '<td class="c">';
                    $table .= ($edit ? '<input type="text" class="w item2" value="' . $item2 . '"/>' : $item2);
                    $table .= '</td>';
                    $table .= '<td class="c">';
                    $table .= ($edit ? '<input type="text" class="c item3" value="' . $item3 . '" size="3"/>' : $item3);
                    $table .= '</td>';
                    $table .= '<td class="c">';
                    $table .= ($edit ? '<input type="text" class="c item4" value="' . $item4 . '" size="3"/>' : $item4);
                    $table .= '</td>';
                    $table .= '<td class="c">';
                    $table .= ($edit ? '<input type="text" class="c item5" value="' . $item5 . '" size="3"/>' : $item5);
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
                        $table .= ($edit ? '<input type="text" class="c total1" value="' . $total1 . '" size="3"/>' : $total1);
                        $table .= '</td>';
                        $table .= '<td class="c">';
                        $table .= ($edit ? '<input type="text" class="w total2" value="' . $total2 . '"/>' : $total2);
                        $table .= '</td>';
                        $table .= '<td class="c">';
                        $table .= ($edit ? '<input type="text" class="c total3" value="' . $total3 . '" size="3"/>' : $total3);
                        $table .= '</td>';
                        $table .= '<td class="c">';
                        $table .= ($edit ? '<input type="text" class="c total4" value="' . $total4 . '" size="3"/>' : $total4);
                        $table .= '</td>';
                        $table .= '<td class="c">';
                        $table .= ($edit ? '<input type="text" class="c total5" value="' . $total5 . '" size="3"/>' : $total5);
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
                $table .= ($edit ? '<input type="text" class="c" id="amount1" value="' . $amount1 . '" size="3"/>' : $amount1);
                $table .= ' 分, 实得 ';
                $table .= ($edit ? '<input type="text" class="c" id="amount2" value="' . $amount2 . '" size="3"/>' : $amount2);
                $table .= ' 分, 得分率 ';
                $table .= ($edit ? '<input type="text" class="c" id="amount3" value="' . $amount3 . '" size="3"/>' : $amount3);
                $table .= ' %, 外观质量为 ';
                $table .= ($edit ? '<input type="text" class="c" id="amount4" value="' . $amount4 . '" size="3"/>' : $amount4);
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

?>