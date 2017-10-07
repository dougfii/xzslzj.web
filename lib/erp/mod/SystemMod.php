<?php

class SystemMod extends BaseMod
{

    public function index()
    {
        $this->GroupList();
    }

    public function GroupList()
    {
        $gid = $this->Req('gid', 0, 'int');
        $oid = $this->Req('oid', 0, 'int');

        $this->Header();

        $view = View::Factory('GroupList');

        if ($oid > 0) {
            $p = OrgBiz::Permission($oid)['msg'];
            $name = OrgBiz::Name($oid)['msg'];
            $view->ro = OrgBiz::GetNextList($oid)['data'];
        } else {
            $p = GroupBiz::Permission($gid)['msg'];
            $name = GroupBiz::Name($gid)['msg'];
            $view->rg = GroupBiz::GetNextList($gid)['data'];
            $view->ro = OrgBiz::GetListByGid($gid)['data'];
        }
        $view->ru = UserBiz::GetListByGidAndOid($gid, $oid)['data'];

        $view->gid = $gid;
        $view->oid = $oid;

        $view->name = $name;
        $view->permission = $this->ShowPermission($name, P::GetPermission($p), true);
        $view->path = $this->GetGroupPath($gid, $oid);
        $view->tree = json_encode(GroupBiz::Tree()['data']);

        echo $view->Render();

        $this->Footer();
    }

    //组织树的路径
    private function GetGroupPath($gid, $oid)
    {
        $url = '?m=System&a=GroupList&gid=';
        $str = '<div class="path"><a href="' . $url . '">组织机构</a>';

        if ($gid > 0) {
            $rs = GroupBiz::GetParentList($gid)['data'];
            foreach ($rs as $k => $v) {
                $str .= ' &gt; <a href="' . $url . $k . '">' . $v . '</a>';
            }
        }
        if ($oid > 0) {
            $rs = OrgBiz::GetParentList($oid)['data'];
            foreach ($rs as $k => $v) {
                $str .= ' &gt; <a href="' . $url . $gid . '&oid=' . $k . '">' . $v . '</a>';
            }
        }

        $str .= '</div>';
        return $str;
    }

    private function ShowPermission($name, $permission, $inherit = false)
    {
        $view = View::Factory('Permission');

        $view->name = $name;
        $view->p = $permission;
        $view->inherit = $inherit;

        return $view->render();
    }

    public function GroupAdd()
    {
        $gid = $this->Req('gid', 0, 'int');

        $this->Header();

        $view = View::Factory('GroupAdd');

        $view->fid = $gid;
        $view->name = GroupBiz::Name($gid)['msg'];

        $view->tree = json_encode(GroupBiz::Tree()['data']);
        $view->tree1 = json_encode(GroupBiz::Tree(true, false, false)['data']);

        echo $view->Render();

        $this->Footer();
    }

    public function GroupEdit()
    {
        $id = $this->Req('id', 0, 'int');

        $this->Header();

        $view = View::Factory('GroupEdit');

        $fid = GroupBiz::Fid($id)['msg'];
        $view->fid = $fid;
        $view->name = GroupBiz::Name($fid)['msg'];

        $view->rs = GroupBiz::Item($id)['data'];

        $view->tree = json_encode(GroupBiz::Tree()['data']);
        $view->tree1 = json_encode(GroupBiz::Tree(true, false, false)['data']);

        echo $view->Render();

        $this->Footer();
    }

    public function OnGroupAdd()
    {
        $fid = $this->Req('fid', 0, 'int');
        $fid = 0;
        $name = $this->Req('name', '', 'str');
        $company = $this->Req('company', '', 'str');

        echo Json::Out(GroupBiz::Add($fid, $name, $company, true));
    }

    public function OnGroupEdit()
    {
        $id = $this->Req('id', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $fid = 0;
        $name = $this->Req('name', '', 'str');
        $company = $this->Req('company', '', 'str');

        echo Json::Out(GroupBiz::Edit($id, $fid, $name, $company, true));
    }

    public function OnGroupDelete()
    {
        $id = $this->Req('id', 0, 'int');

        echo GroupBiz::Delete($id);
    }

    public function OnGroupPermission()
    {
        $gid = $this->Req('gid', 0, 'int');
        $oid = $this->Req('oid', 0, 'int');
        $user = $this->Req('user', 0, 'int');
        $dept = $this->Req('dept', 0, 'int');
        $allu = $this->Req('allu', 0, 'int');

        if ($gid <= 0) Json::ReturnError(ALERT_ERROR);

        $s = trim(Url::Req('s', '', 'str'));

        $j = P::Clean($s);

        if ($oid > 0) {
            OrgCls::EditPermission($oid, $j);

            // user
            if ($user) {
                $users = UserCls::Instance()->GetIdsByOid($oid);
                if (!empty ($users)) UserCls::EditPermissions($users, $j);
            }

            // dept
            if ($dept) {
                $depts = OrgCls::Instance()->GetChildrenIds($oid, true);
                if (!empty ($depts)) OrgCls::EditPermissions($depts, $j);
            }

            // allu
            if ($allu) {
                $users = array();
                $depts = OrgCls::Instance()->GetChildrenIds($oid);

                foreach ($depts as $v) {
                    $users = array_merge($users, UserCls::Instance()->GetIdsByGid($v));
                }

                if (!empty ($depts)) OrgCls::EditPermissions($depts, $j);
                if (!empty ($users)) UserCls::EditPermissions($users, $j);
            }

        } else {
            GroupCls::EditPermission($gid, $j);

//            // user
//            if ($user) {
//                $users = UserCls::Instance()->GetIdsByGid($oid);
//                if (!empty ($users)) UserCls::EditPermissions($users, $j);
//            }


            // dept
            if ($dept) {
                $depts = array();
                $orgs = OrgCls::Instance()->GetIdsByGid($gid);
                foreach ($orgs as $k) {
                    $depts = array_merge($depts, OrgCls::Instance()->GetChildrenIds($k, true));
                }
                if (!empty ($depts)) OrgCls::EditPermissions($depts, $j);
            }

            // allu
            if ($allu) {
                $users = array();
                $depts = array();
                $orgs = OrgCls::Instance()->GetIdsByGid($gid);

                foreach ($orgs as $k) {
                    $ds = OrgCls::Instance()->GetChildrenIds($k, true);
                    $depts = array_merge($depts, $ds);

                    foreach ($ds as $v) {
                        $users = array_merge($users, UserCls::Instance()->GetIdsByGid($v));
                    }
                }

                if (!empty ($depts)) OrgCls::EditPermissions($depts, $j);
                if (!empty ($users)) UserCls::EditPermissions($users, $j);
            }
        }


        Json::ReturnSuccess();
    }

    public function OrgAdd()
    {
        $oid = $this->Req('oid', 0, 'int');
        $gid = $this->Req('gid', 0, 'int');

        $this->Header();

        $view = View::Factory('OrgAdd');

        $view->oid = $oid;
        $view->fid = $gid;
        $view->name = $oid <= 0 ? GroupBiz::Name($gid)['msg'] : OrgBiz::Name($oid)['msg'];

        $view->tree = json_encode(GroupBiz::Tree()['data']);
        $view->tree1 = json_encode(GroupBiz::Tree(true, true, false)['data']);

        echo $view->Render();

        $this->Footer();
    }

    public function OrgEdit()
    {
        $id = $this->Req('id', 0, 'int');

        $fid = OrgBiz::Fid($id)['msg'];
        $gid = OrgBiz::Gid($id)['msg'];

        $this->Header();

        $view = View::Factory('OrgEdit');

        $view->fid = $gid;
        $view->name = $fid <= 0 ? GroupBiz::Name($gid)['msg'] : OrgBiz::Name($fid)['msg'];

        $view->rs = OrgBiz::Item($id)['data'];

        $view->tree = json_encode(GroupBiz::Tree()['data']);
        $view->tree1 = json_encode(GroupBiz::Tree(true, true, false)['data']);

        echo $view->Render();

        $this->Footer();
    }

    public function OnOrgAdd()
    {
        $fid = $this->Req('fid', 0, 'int');
        $oid = $this->Req('oid', 0, 'int');
        $name = $this->Req('name', '', 'str');

        echo Json::Out(OrgBiz::Add($fid, $oid, $name, true));
    }

    public function OnOrgEdit()
    {
        Json::ReturnError('ddd');
        $id = $this->Req('id', 0, 'int');
        $fid = $this->Req('fid', 0, 'int');
        $gid = $this->Req('gid', -1, 'int');
        $name = $this->Req('name', '', 'str');

        echo Json::Out(OrgBiz::Edit($id, $gid, $fid, $name, true));
    }

    public function UserAdd()
    {
        $this->Header();

        $gid = $this->Req('gid', 0, 'int');

        $view = View::Factory('UserAdd');

        $view->tree = json_encode(GroupBiz::Tree()['data']);
        $view->tree1 = json_encode(GroupBiz::Tree(true, true, false)['data']);

        echo $view->Render();

        $this->Footer();
    }

    public function UserEdit()
    {
        $id = $this->Req('id', 0, 'int');
        $oid = $this->Req('oid', 0, 'int');

        $this->Header();

        $view = View::Factory('UserEdit');

        $rs = UserBiz::Item($id)['data'];
        $oname = OrgBiz::Name($oid)['msg'];

        $view->oname = $oname;
        $view->rs = $rs;

        $name = '';
        if (!empty ($rs)) $name = $rs ['name'];

        $view->permission = $this->ShowPermission($name, $this->GetPermission(UserCls::Instance()->Permissions($id)));

        $view->tree = json_encode(GroupBiz::Tree()['data']);
        $view->tree1 = json_encode(GroupBiz::Tree(true, true, false)['data']);

        echo $view->Render();

        $this->Footer();
    }

    public function OnUserAdd()
    {
        $gid = $this->Req('gid', 0, 'int');
        $oid = $this->Req('fid', 0, 'int');
        $username = $this->Req('username', '', 'str');
        $name = $this->Req('name', '', 'str');
        $password = $this->Req('password', '', 'str');
        $serial = $this->Req('serial', '', 'str');
        $sex = $this->Req('sex', 0, 'int');
        $mobile = $this->Req('mobile', '', 'str');
        $imei = $this->Req('imei', '', 'str');
        $email = $this->Req('email', '', 'str');
        $lead = $this->Req('lead', 0, 'int');
        $admin = $this->Req('admin', 0, 'int');
        $act = $this->Req('act', 1, 'int');

        echo Json::Out(UserBiz::Add($gid, $oid, $username, $name, $password, $serial, $sex, $mobile, $imei, $email, $lead, $admin, $act));
    }

    public function OnUsersEdit()
    {
        $id = $this->Req('id', 0, 'int');
        $gid = $this->Req('gid', 0, 'int');
        $oid = $this->Req('fid', 0, 'int');
        $username = $this->Req('username', '', 'str');
        $name = $this->Req('name', '', 'str');
        $password = $this->Req('password', '', 'str');
        $serial = $this->Req('serial', '', 'str');
        $sex = $this->Req('sex', 0, 'int');
        $mobile = $this->Req('mobile', '', 'str');
        $imei = $this->Req('imei', '', 'str');
        $email = $this->Req('email', '', 'str');
        $lead = $this->Req('lead', 0, 'int');
        $admin = $this->Req('admin', 0, 'int');
        $act = $this->Req('act', 1, 'int');

        if (!empty ($password) && !Util::IsPassword($password)) Json::ReturnError('请输入正确的重置密码');
        if (!Util::IsMaxLen($serial, 20)) Json::ReturnError('员工编号过长');
        if (Util::IsBlank($name)) Json::ReturnError('请输入姓名');
        if (!Util::IsMaxLen($name, 20)) Json::ReturnError('姓名过长');

        $lead = Util::Bool2Int($lead);
        $sex = Util::Bool2Int($sex);
        $act = Util::Bool2Int($act);
        $admin = Util::Bool2Int($admin);

        try {
            DB::db()->BeginTransaction();

            UserCls::Edit($id, $gid, $oid, $name, $serial, $sex, $mobile, $imei, $email, $lead, $admin, $act);
            if (!empty ($password)) UserCls::EditPassword($id, $password);

            DB::db()->Commit();
        } catch (Exception $e) {
            DB::db()->RollBack();
            Json::ReturnError(ALERT_ERROR);
        }

        Json::ReturnSuccess();
    }

    public function OnUserDelete()
    {
        $id = UUID::Check(Url::Req('id', '', 'str'));

        if ($id == md5('1')) {
            echo '系统管理员不允许删除, 您可以通过编辑员工界面修改密码';
            return;
        }

        // TODO:
        /*
         * 员工删除前需要查询是否有未移交的资料，如订单，商机等 删除用户前必须移交所有资料
         */

        try {
            DB::db()->BeginTransaction();

            UserCls::Delete($id);

            DB::db()->Commit();
        } catch (Exception $e) {
            DB::db()->RollBack();
            echo ALERT_ERROR;
            return;
        }

        echo 1;
    }

    public function OnUserPermission()
    {
        $id = $this->Req('id', 0, 'int');

        if ($id <= 0) Json::ReturnError(ALERT_ERROR);
        if ($id == 1) Json::ReturnError('不能修改系统管理员权限');

        $s = trim(Url::Req('s', '', 'str'));

        $j = P::Clean($s);

        UserCls::EditPermission($id, $j);

        Json::ReturnSuccess();
    }

    //角色列表
    public function RoleList()
    {
        $this->Header();

        if (!P::Instance($this->Uid())->isCanView(R::PROGRAMMETASK_CALENDARLIST)) {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $name = $this->Req('name', '', 'str');

        $where = '';
        if (!empty ($name))
            $where .= ' AND (LOWER(name) like \'%' . strtolower($name) . '%\') ';

        $ret = UserInterface::RoleItems();
        $count = $ret['msg'];
        $rs = $ret['data'];

        $view = View::factory('RoleList');

        $view->rs = $rs;

        $view->name = $name;

        echo $view->render();

        $this->Footer();
    }

    //增加角色
    public function RoleAdd()
    {
        $id = $this->Req('id', 0, 'int');

        $this->Header();

        $view = View::factory('RoleAdd');

        $name = '';
        if (!empty ($rs))
            $name = $rs ['name'];

        $view->treeSGroup = json_encode(UserInterface::GroupTree(true, false, false)['data']);
        $view->treeSOrg = json_encode(UserInterface::GroupTree(true, true, false)['data']);
        $view->treeSUser = json_encode(UserInterface::GroupTree(true, true, true)['data']);

        $view->treeTGroup = json_encode(UserInterface::GroupTree(true, false, false)['data']);
        $view->treeTOrg = json_encode(UserInterface::GroupTree(true, true, false)['data']);
        $view->treeTUser = json_encode(UserInterface::GroupTree(true, true, true)['data']);


        $view->permission = $this->ShowPermission($name, $this->GetPermission(UserCls::Instance()->Permissions($id)));

        echo $view->render();

        $this->Footer();
    }

    //编辑角色
    public function RoleEdit()
    {
        $id = $this->Req('id', 0, 'int');

        $this->Header();

        $view = View::Factory('RoleEdit');

        $rs = UserInterface::RoleItem($id)['data'];

        $view->treeSGroup = json_encode(UserInterface::GroupTree(true, false, false)['data']);
        $view->treeSOrg = json_encode(UserInterface::GroupTree(true, true, false)['data']);
        $view->treeSUser = json_encode(UserInterface::GroupTree(true, true, true)['data']);

        $view->treeTGroup = json_encode(UserInterface::GroupTree(true, false, false)['data']);
        $view->treeTOrg = json_encode(UserInterface::GroupTree(true, true, false)['data']);
        $view->treeTUser = json_encode(UserInterface::GroupTree(true, true, true)['data']);

        $view->rs = $rs;

        $name = '';
        if (!empty ($rs)) {
            $name = $rs ['name'];

            $view->permission = $this->ShowPermission($name, $this->GetPermission(RoleCls::Instance()->Permissions($id)));

            $sgroups = $rs['sgroup']; //起源组织列表
            $sgroupsexp = Util::Explode($sgroups, ',');

            $sgroup = '';
            foreach ($sgroupsexp as $v) {
                $v = trim($v);
                if (!empty ($v)) $sgroup .= '<span class="oplist_item badge badge-info" did="' . $v . '">' . UserInterface::GetGroupName($v)['msg'] . '</span> ';
            }

            $sorgs = $rs['sorg']; //起源部门列表
            $sorgsexp = Util::Explode($sorgs, ',');

            $sorg = '';
            foreach ($sorgsexp as $v) {
                $v = trim($v);
                if (!empty ($v)) $sorg .= '<span class="oplist_item badge badge-info" did="' . $v . '">' . UserInterface::GetOrgName($v)['msg'] . '</span> ';
            }

            $susers = $rs['suser']; //起源用户列表
            $susersexp = Util::Explode($susers, ',');

            $suser = '';
            foreach ($susersexp as $v) {
                $v = trim($v);
                if (!empty ($v)) $suser .= '<span class="oplist_item badge badge-info" did="' . $v . '">' . UserInterface::GetUserName($v)['msg'] . '</span> ';
            }

            $tgroups = $rs['tgroup']; //目标组织列表
            $tgroupsexp = Util::Explode($tgroups, ',');

            $tgroup = '';
            foreach ($tgroupsexp as $v) {
                $v = trim($v);
                if (!empty ($v)) $tgroup .= '<span class="oplist_item badge badge-info" did="' . $v . '">' . UserInterface::GetGroupName($v)['msg'] . '</span> ';
            }

            $torgs = $rs['torg']; //目标部门列表
            $torgsexp = Util::Explode($torgs, ',');

            $torg = '';
            foreach ($torgsexp as $v) {
                $v = trim($v);
                if (!empty ($v)) $torg .= '<span class="oplist_item badge badge-info" did="' . $v . '">' . UserInterface::GetOrgName($v)['msg'] . '</span> ';
            }

            $tusers = $rs['tuser']; //目标用户列表
            $tusersexp = Util::Explode($tusers, ',');

            $tuser = '';
            foreach ($tusersexp as $v) {
                $v = trim($v);
                if (!empty ($v)) $tuser .= '<span class="oplist_item badge badge-info" did="' . $v . '">' . UserInterface::GetUserName($v)['msg'] . '</span> ';
            }

            $view->sgrouplist = $sgroup;
            $view->sorglist = $sorg;
            $view->suserlist = $suser;
            $view->tgrouplist = $tgroup;
            $view->torglist = $torg;
            $view->tuserlist = $tuser;
        }

        echo $view->Render();

        $this->Footer();
    }

    public function OnRoleAdd()
    {
        $name = $this->Req('name', '', 'str');
        $sgroupi = $this->Req('sgroupi', 0, 'int');
        $sorgi = $this->Req('sorgi', 0, 'int');
        $sgroup = $this->Req('sgroup', '', 'str');
        $sorg = $this->Req('sorg', '', 'str');
        $suser = $this->Req('suser', '', 'str');
        $tgroupi = $this->Req('tgroupi', 0, 'int');
        $torgi = $this->Req('torgi', 0, 'int');
        $tgroup = $this->Req('tgroup', '', 'str');
        $torg = $this->Req('torg', '', 'str');
        $tuser = $this->Req('tuser', '', 'str');
        $s = $this->Req('s', '', 'str');
        $act = $this->Req('act', 0, 'int');

        echo Json::Out(UserInterface::RoleAdd($name, $sgroupi, $sorgi, $sgroup, $sorg, $suser, $tgroupi, $torgi, $tgroup, $torg, $tuser, $s, $act));
    }

    public function OnRoleEdit()
    {
        $id = Url::Req('id', 0, 'int');
        $name = $this->Req('name', '', 'str');
        $sgroupi = $this->Req('sgroupi', 0, 'int');
        $sorgi = $this->Req('sorgi', 0, 'int');
        $sgroup = $this->Req('sgroup', '', 'str');
        $sorg = $this->Req('sorg', '', 'str');
        $suser = $this->Req('suser', '', 'str');
        $tgroupi = $this->Req('tgroupi', 0, 'int');
        $torgi = $this->Req('torgi', 0, 'int');
        $tgroup = $this->Req('tgroup', '', 'str');
        $torg = $this->Req('torg', '', 'str');
        $tuser = $this->Req('tuser', '', 'str');
        $s = $this->Req('s', '', 'str');
        $act = $this->Req('act', 0, 'int');

        echo Json::Out(UserInterface::RoleEdit($id, $name, $sgroupi, $sorgi, $sgroup, $sorg, $suser, $tgroupi, $torgi, $tgroup, $torg, $tuser, $s, $act));
    }

    public function OnRoleDelete()
    {
        $id = $this->Req('id', 0, 'int');
        RoleCls::Delete($id);
        Json::ReturnSuccess();
    }

    //    //DIV部门
    //    public function PopDepartmentSelect()
    //    {
    //        $findname = $this->Req('findname', '', 'str');
    //
    //        if (!empty ($findname))
    //        {
    //            $findname = strtolower($findname);
    //            $where .= " AND (LOWER(name) like '%{$findname}%' OR LOWER(en) like '%{$findname}%' OR LOWER(place) like '%{$findname}%' OR LOWER(memo) like '%{$findname}%')";
    //        }
    //
    //        $this->Head();
    //
    //        $v = View::Factory('PopDepartmentSelect');
    //
    //        $gid = UserCls::Instance()->GetGid($this->Uid());
    //        $GRWhere = GroupCls::Instance->GetChildIds($gid);
    //        $GRGID = implode(",", $GRWhere);
    //        $where = 'WHERE del=false AND gid in (' . $GRGID . ')';
    //
    //        $size = PAGE_SIZE_POP;
    //        $count = OrgBiz::Count($where);
    //        $pos = HTML::PagePos($count, $this->Page(), $size);
    //
    //        $rs = OrgBiz::Items($where, $this->Order(), $pos, $size);
    //
    //        $url = Url::QUERY_STRING_DELETE(array('pn'));
    //
    //        $v->rs = $rs;
    //        $v->orders = HTML::Orders($url, array('id', 'name', 'zh', 'en', 'place', 'created', 'creator', 'total', 'sort', 'state'));
    //        $v->paged = HTML::PageGroup($count, $this->Page(), $url, $size);
    //
    //        $v->findname = $findname;
    //
    //        echo $v->Render();
    //
    //        $this->Foot();
    //    }

    //审批流程
    public function ApproveList()
    {
        $this->Header();

        if (!P::Instance($this->Uid())->isCanView(R::PROGRAMMETASK_CALENDARLIST)) {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $name = $this->Req('name', '', 'str');

        $where = '';
        if (!empty ($name))
            $where .= ' AND (LOWER(name) like \'%' . strtolower($name) . '%\') ';

        $url = Url::QUERY_STRING_DELETE(array('pn'));
        $size = PAGE_SIZE_MEDIUM;
        $ret = RoleInterface::RoleItems($this->Uid(), $where, '', $this->Page(), $size, false);
        $count = $ret['msg'];
        $rs = $ret['data'];

        $view = View::factory('ApproveList');

        $view->rs = $rs;
        $view->paged = HTML::PageJump($count, $this->Page(), $size, $url);

        $view->name = $name;

        echo $view->render();

        $this->Footer();

    }

    public function ApproveAdd()
    {
        $this->Header();

        $approveobjectid = Url::Req('approveobjectid', -1, 'int');

        if (isset ($_SESSION ['ApproveGroup']))
            unset ($_SESSION ['ApproveGroup']);
        if (isset ($_SESSION ['ApproveConditon']))
            unset ($_SESSION ['ApproveConditon']);

        $v = View::Factory('ApproveAdd');

        $gid1 = UserCls::Instance()->GetGid($this->Uid());
        $id = UserCls::Instance()->GetId($this->Uid());

        $rs = UserCls::Instance()->Item($id);
        $v->groupid = $rs['id'];
        $v->gname = GroupCls::Instance()->GetName($rs['gid']);
        $v->gid = UserCls::Instance()->GetGid($this->Uid());


        $v->dtree = json_encode(GroupBiz::TreeOgroups($gid1, true, true));
        $v->utree = json_encode(GroupBiz::Tree($gid1, true, true));
        $v->tree = json_encode(GroupBiz::Tree($gid1, true, true));

        $v->approveobjectid = HTML::Select(ApproveObjectCls::AllItems(), 'approveobjectid', 'input-medium', $approveobjectid, ' ');
        $v->cause = HTML::Select(ApproveCauseCls::AllItems(), 'cause', 'input-small', '');
        $field = HTML::Select(ApproveFieldCls::getFieldsMap($approveobjectid), 'field', 'input-medium', '', ' ');
        $v->field = $field;
        $v->operator = HTML::Select(ApproveFieldCls::getOperatorMap(), 'operator', 'input-mini', '', ' ');

        echo $v->Render();

        $this->Footer();
    }

    public function ApproveEdit()
    {
        $this->Header();

        $id = UUID::Check(Url::Req('id', '', 'str'));

        if (isset ($_SESSION ['ApproveGroup']))
            unset ($_SESSION ['ApproveGroup']);
        if (isset ($_SESSION ['ApproveConditon']))
            unset ($_SESSION ['ApproveConditon']);

        $rs = ApproveCls::Instance()->Item($id);

        $v = View::Factory('ApproveEdit');

        if (!empty ($rs)) {
            $v->approveobject = ApproveObjectCls::GetName($rs ['approveobjectid']);
            $v->cause = HTML::Select(ApproveCauseCls::AllItems(), 'cause', 'input-small', $rs ['cause']);
            $v->field = HTML::Select(ApproveFieldCls::getFieldsMap($rs ['approveobjectid']), 'field', 'input-medium', '', ' ');
            $v->operator = HTML::Select(ApproveFieldCls::getOperatorMap(), 'operator', 'input-mini', '', ' ');

            $_SESSION ['ApproveConditon'] = ApproveConditionCls::Instance()->getConditionList($id);
            $_SESSION ['ApproveGroup'] = ApproveGroupCls::Instance()->getGroupList($id);
        }

        $v->rs = $rs;

        echo $v->Render();

        $this->Footer();
    }

    public function OnApproveAdd()
    {
        $gid = Url::Req('gid', 0, 'int');
        $departmentid = Url::Req('departmentid', 0, 'int');
        $act = Url::Req('act', 0, 'int');
        $approveobjectid = Url::Req('approveobjectid', 0, 'int');
        $cause = Url::Req('cause', 0, 'int');
        $pu = Url::Req('pu', '', 'str');
        $flow = Url::Req('flow', '', 'str');

        $rc = array();
        $rg = array();

        if ($approveobjectid < 0) {
            echo '请选择审批对象';
            return;
        }
        if ($cause <= 0) {
            echo '请选择审批原因';
            return;
        }
        if (ApproveCls::existApproveObjectIDAndCause($approveobjectid, $cause)) {
            echo '审批对象已经存在';
            return;
        }
        if (Util::IsBlank($flow)) {
            echo '请输入审核流程';
            return;
        }
        if (!PhpHelper::CheckSyntax($flow)) {
            echo '审批流程语法错误';
            return;
        }

        $act = Util::Bool2Int($act);

        if (isset ($_SESSION ['ApproveConditon']) && is_array($_SESSION ['ApproveConditon']))
            $rc = $_SESSION ['ApproveConditon'];
        if (isset ($_SESSION ['ApproveGroup']) && is_array($_SESSION ['ApproveGroup']))
            $rg = $_SESSION ['ApproveGroup'];

        try {
            DB::db()->BeginTransaction();

            $apprid = ApproveCls::Add($gid, $departmentid, $act, $approveobjectid, $cause, $pu, $flow);
            $_SESSION['apprid'] = $apprid;

            $approveid = $_SESSION['oid'];
            foreach ($rc as $k => $v) {
                ApproveConditionCls::Add($gid, $k, $v ['field'], $v ['name'], $v ['type'], $v ['operator'], $v ['value'], $v ['realvalue']);
            }

            foreach ($rg as $k => $v) {
                foreach ($v as $m => $n) {
                    ApproveGroupCls::Add(UUID::Make(), $gid, $k, $m, $n);
                }
            }

            DB::db()->Commit();
        } catch (Exception $e) {
            DB::db()->RollBack();
            echo ALERT_ERROR;
            return;
        }

        // 		echo 1;
    }

    public function OnApproveEdit()
    {
        $id = UUID::Check(Url::Req('id', '', 'str'));
        if (empty ($id)) {
            echo ALERT_ERROR;
            return;
        }

        $approveobjectid = Url::Req('approveobjectid', -1, 'int');
        $cause = Url::Req('cause', 0, 'int');
        $flow = trim(Url::Req('flow', '', 'str'));
        $act = Url::Req('act', 0, 'int');
        $rc = array();
        $rg = array();
        if (isset ($_SESSION ['ApproveConditon']) && is_array($_SESSION ['ApproveConditon']))
            $rc = $_SESSION ['ApproveConditon'];
        if (isset ($_SESSION ['ApproveGroup']) && is_array($_SESSION ['ApproveGroup']))
            $rg = $_SESSION ['ApproveGroup'];

        if ($approveobjectid < 0) {
            echo '请选择审批对象';
            return;
        }
        if ($cause <= 0) {
            echo '请选择审批原因';
            return;
        }
        if (ApproveCls::existApproveObjectIDAndCause($approveobjectid, $cause, $id)) {
            echo '审批对象已经存在';
            return;
        }
        if (empty ($rc)) {
            echo '没有审批条件';
            return;
        }
        if (empty ($rg)) {
            echo '没有审批组';
            return;
        }
        foreach ($rg as $k => $v) {
            if (empty ($v)) {
                echo "审批组 \$group{$k} 没有审批者";
                return;
            }
        }
        if (Util::IsBlank($flow)) {
            echo '请输入审核流程';
            return;
        }
        if (!PhpHelper::CheckSyntax($flow)) {
            echo '审批流程语法错误';
            return;
        }

        $act = Util::Bool2Int($act);
        $method = Util::Bool2Int(false);

        $process = $this->MakeApproveProcess($rc, $rg, $flow);

        try {
            DB::db()->BeginTransaction();

            ApproveCls::Edit($id, $approveobjectid, $cause, $method, $flow, $process, $act);

            ApproveConditionCls::DeleteConditionList($id);
            foreach ($rc as $k => $v) {
                ApproveConditionCls::Add(UUID::Make(), $id, $k, $v ['field'], $v ['name'], $v ['type'], $v ['operator'], $v ['value'], $v ['realvalue']);
            }

            ApproveGroupCls::DeleteGroupList($id);
            foreach ($rg as $k => $v) {
                foreach ($v as $m => $n) {
                    ApproveGroupCls::Add(UUID::Make(), $id, $k, $m, $n);
                }
            }

            DB::db()->Commit();
        } catch (Exception $e) {
            DB::db()->RollBack();
            echo ALERT_ERROR;
            return;
        }

        echo 1;
    }

    public function OnApproveDelete()
    {
        $id = UUID::Check(Url::Req('id', '', 'str'));
        if (empty ($id)) {
            echo ALERT_ERROR;
            return;
        }

        ApproveCls::delete($id);

        echo 1;
    }

    public function ApproveConditionList()
    {
        // ajax date
        $rs = array();

        if (isset ($_SESSION ['ApproveConditon']) && is_array($_SESSION ['ApproveConditon']))
            $rs = $_SESSION ['ApproveConditon'];

        $s = '<table class="table table-condensed">';
        foreach ($rs as $k => $v) {
            $s .= '<tr><td class="fcod
			">$c' . $k . '</td><td>' . $v ['field'] . ' (' . $v ['name'] . ') ' . $v ['operator'] . ' ' . $v ['value'] . '</td><td class="fopr"><input type="button" class="deletecondition" did="' . $k . '" value="删除"  /></td></tr>';
        }
        $s .= '</table>';

        echo $s;
    }

    public function OnApproveConditionAdd()
    {
        $object = Url::Req('object', -1, 'int');
        $field = trim(Url::Req('field', '', 'str'));
        $operator = trim(Url::Req('operator', '', 'str'));
        $value = trim(Url::Req('value', '', 'str'));
        $realvalue = $value;

        if (empty ($field)) {
            echo '请选择字段';
            return;
        }
        if (empty ($operator)) {
            echo '请选择操作符';
            return;
        }

        $name = ApproveFieldCls::getFieldName($object, $field);
        $type = ApproveFieldCls::getFieldType($object, $field);

        if (empty ($type)) {
            echo ALERT_ERROR;
            return;
        }

        if ($type == 'DEPARTMENTID') {
            if ($operator != '==' && $operator != '!=') {
                echo '不适用的操作符';
                return;
            }

            $realvalue = UUID::Check(DepartmentCls::Instance()->GetIDByName($value));
            if (empty ($realvalue)) {
                echo '部门不存在';
                return;
            }
        }
        if ($type == 'USERID') {
            if ($operator != '==' && $operator != '!=') {
                echo '不适用的操作符';
                return;
            }

            $realvalue = UUID::Check(UserCls::Instance()->getIDByUName($value));
            if (empty ($realvalue)) {
                echo '用户不存在';
                return;
            }
        }

        if ($type == 'REPOSITORYID') {
            if ($operator != '==' && $operator != '!=') {
                echo '不适用的操作符';
                return;
            }

            $realvalue = UUID::Check(R::Instance()->GetIDByName($value));
            if (empty ($realvalue)) {
                echo '仓库不存在';
                return;
            }
        }

        if ($type == 'CUSTOMERID') {
            if ($operator != '==' && $operator != '!=') {
                echo '不适用的操作符';
                return;
            }

            $realvalue = UUID::Check(CustomerCls::Instance()->GetIDByName($value));
            if (empty ($realvalue)) {
                echo '客户不存在';
                return;
            }
        }

        if ($type == 'INVENTORYID') {
            if ($operator != '==' && $operator != '!=') {
                echo '不适用的操作符';
                return;
            }

            //$realvalue = UUID::Check ( InventoryCls::Instance ()->getIDByCode ( $value ) );
            if (empty ($realvalue)) {
                echo '库存号不存在';
                return;
            }
        }

        if ($type == 'DATE') {
            $realvalue = Date::Format($value, 'd', false);
            if ($realvalue === false) {
                echo '不是一个有效的日期';
                return;
            }
        }

        if ($type == 'PRICE') {
            if (!Util::IsPrice($value)) {
                echo '不是一个有效的价格';
                return;
            }
            $value = floatval($value);
            $realvalue = $value;
        }

        $rs = array();

        if (isset ($_SESSION ['ApproveConditon']) && is_array($_SESSION ['ApproveConditon']))
            $rs = $_SESSION ['ApproveConditon'];

        foreach ($rs as $v) {
            if ($v ['field'] == $field && $v ['operator'] == $operator && $v ['realvalue'] == $realvalue) {
                echo '审批条件已经存在';
                return;
            }
        }

        $rs [] = array('field' => $field, 'name' => $name, 'type' => $type, 'operator' => $operator, 'value' => $value, 'realvalue' => $realvalue);

        array_merge(array(), $rs);

        $_SESSION ['ApproveConditon'] = $rs;

        echo 1;
    }

    public function OnApproveConditionDelete()
    {
        $id = Url::Req('id', -1, 'int');

        $rs = array();

        if (isset ($_SESSION ['ApproveConditon']) && is_array($_SESSION ['ApproveConditon']))
            $rs = $_SESSION ['ApproveConditon'];

        if (array_key_exists($id, $rs)) {
            unset ($rs [$id]);
            $rs = array_merge(array(), $rs);
        }

        $_SESSION ['ApproveConditon'] = $rs;

        echo 1;
    }

    public function ApproveGroupList()
    {
        $rs = array();

        if (isset ($_SESSION ['ApproveGroup']) && is_array($_SESSION ['ApproveGroup']))
            $rs = $_SESSION ['ApproveGroup'];

        $s = '<button class="btn btn-warning addgroup" ><i class="icon-plus-sign icon-white"></i> 增加审批组</button><br /><br />';

        if (!empty ($rs)) {
            foreach ($rs as $k => $v) {
                $s .= '<div class="clearfix caption"><h4 class="pull-left">$group' . $k . '()</h4><div class="pull-right"><button class="btn btn-danger deletegroup" did="' . $k . '" ><i class="icon-plus-sign icon-white"></i> 删除审批组</button> <button class="btn btn-warning addauditor" gid="' . $k . '" ><i class="icon-minus-sign icon-white"></i> 增加审批者</button></div></div>';
                $s .= '<table class="table table-condensed">';

                foreach ($v as $m => $n) {
                    $s .= '<tr><td class="fcod">' . $m . '</td><td>' . UserCls::Instance()->GetUName($n) . '</td><td class="fopr"><a href="#" class="deleteauditor" gid="' . $k . '" did="' . $m . '" title="删除"><i class="icon-remove"></i></a></td></tr>';
                }

                $s .= '</table>';
            }
        }

        echo $s;
    }

    public function OnApproveGroupAdd()
    {
        $rs = array();

        if (isset ($_SESSION ['ApproveGroup']) && is_array($_SESSION ['ApproveGroup']))
            $rs = $_SESSION ['ApproveGroup'];

        $count = count($rs);

        $rs [$count] = array();

        $_SESSION ['ApproveGroup'] = $rs;

        echo 1;
    }

    public function OnApproveGroupDelete()
    {
        $id = Url::Req('id', -1, 'int');

        $rs = array();

        if (isset ($_SESSION ['ApproveGroup']) && is_array($_SESSION ['ApproveGroup']))
            $rs = $_SESSION ['ApproveGroup'];

        if (array_key_exists($id, $rs))
            unset ($rs [$id]);
        $rs = array_merge(array(), $rs);

        $_SESSION ['ApproveGroup'] = $rs;

        echo 1;
    }

    public function AuditorList()
    {
        $this->Head();

        $gid = Url::Req('gid', 0, 'int');

        $v = View::Factory('AuditorList');

        $v->gid = $gid;
        $v->tree = json_encode(DepartmentCls::Instance()->Tree());

        echo $v->Render();

        $this->Foot();
    }

    public function OnApproveAuditor()
    {
        $sequenceid = UUID::Check(Url::Req('sequenceid', '', 'str'));
        $agree = Url::Req('agree', 0, 'int');
        $memo = trim(Url::Req('memo', '', 'str'));

        if (empty ($sequenceid) || $agree <= 0) {
            echo ALERT_ERROR;
            return;
        }

        try {
            DB::db()->BeginTransaction();

            ApproveSequenceCls::processSequence($sequenceid, $this->Uid(), $agree, $memo);

            DB::db()->Commit();
        } catch (SyncApproveException $ea) {
            echo $ea->getMessage();
            return;
        } catch (SyncProductException $ep) {
            echo $ep->getMessage();
            return;
        } catch (Exception $e) {
            DB::db()->RollBack();
            echo ALERT_ERROR;
            return;
        }

        echo 1;
    }

    public function OnApproveAuditorAdd()
    {
        $id = UUID::Check(Url::Req('id', '', 'str'));
        $gid = Url::Req('gid', -1, 'int');

        if (!empty ($id)) {
            $rs = array();

            if (isset ($_SESSION ['ApproveGroup']) && is_array($_SESSION ['ApproveGroup']))
                $rs = $_SESSION ['ApproveGroup'];

            if (array_key_exists($gid, $rs)) {
                $rx = $rs [$gid];
                $rx [] = $id;
                $rx = array_merge(array(), $rx);
                $rs [$gid] = $rx;
            }

            array_merge(array(), $rs);

            $_SESSION ['ApproveGroup'] = $rs;
        }

        echo 1;
    }

    public function OnApproveAuditorDelete()
    {
        $gid = Url::Req('gid', -1, 'int');
        $id = Url::Req('id', -1, 'int');

        $rs = array();

        if (isset ($_SESSION ['ApproveGroup']) && is_array($_SESSION ['ApproveGroup']))
            $rs = $_SESSION ['ApproveGroup'];

        if (array_key_exists($gid, $rs)) {
            $rx = $rs [$gid];

            if (array_key_exists($id, $rx)) {
                unset ($rx [$id]);
                $rx = array_merge(array(), $rx);
            }

            $rs [$gid] = $rx;
        }

        $_SESSION ['ApproveGroup'] = $rs;

        echo 1;
    }

    public function OnApproveCheckSyntax()
    {
        $content = Url::Req('content', '', 'str');

        if (Util::IsBlank($content)) {
            echo '请输入审批流程';
            return;
        }

        echo PhpHelper::CheckSyntax($content) ? '语法正确' : '语法错误';
    }

    private function MakeApproveProcess($conditon, $group, $flow)
    {
        $s = '';

        foreach ($conditon as $k => $v) {
            $s .= '$c' . $k . '=($rd[\'' . $v ['field'] . '\']' . Url::Unsanitize($v ['operator']);

            if ($v ['type'] == 'PRICE') {
                $s .= $v ['realvalue'];
            } else {
                $s .= '\'' . $v ['realvalue'] . '\''; // 文本型需要加引号
            }

            $s .= ');';
        }

        foreach ($group as $k => $v) {
            $s .= '$group' . $k . ' = function($approveid, $approveobjectid, $approvetargetid, $cause, $ostate, $fstate){';
            $s .= '$sequenceid = UUID::Make();';
            $s .= 'ApproveSequenceCls::Add($sequenceid, $approveid, $approveobjectid, $approvetargetid, $cause, $ostate, $fstate);';
            foreach ($v as $m => $n) {
                $s .= 'ApproveAuditorCls::Add(UUID::Make(), $approveid, $sequenceid, \'' . $n . '\', ' . $m . ');';
            }
            $s .= '};';
        }

        $s .= Url::Unsanitize($flow);

        return $s;
    }

    public function LogLogin()
    {
        $tid = $this->Req('tid', 0, 'int');
        $uid = $this->Req('uid', 0, 'int');

        $this->Header();

        $name = '';
        $where = '';
        if (!empty($tid) && !empty($uid)) {
            $where .= " AND tid={$tid} AND uid={$uid}";
            $name = $tid == 1 ? ' (' . ProjectCls::Instance()->Name($uid) . ')' : ($tid == 2 ? ' (' . UserCls::Instance()->Username($uid) . ')' : '');
        }

        $view = View::Factory('LogLogin');

        $url = Url::QUERY_STRING_DELETE(array('pn'));

        $size = PAGE_SIZE_MEDIUM;
        list($count, $rs) = LogLoginCls::Results($where, '', $this->Page(), $size);

        $view->rs = $rs;
        $view->paged = HTML::PageJump($count, $this->Page(), $size, $url);

        $view->name = $name;

        echo $view->Render();

        $this->Footer();
    }

    public function LogEvent()
    {
        $this->Header();

        $findname = '';

        $where = '';

        $v = View::Factory('LogEvent');

        $pagesize = PAGE_SIZE_DEFAULT;
        $count = LogEventCls::Count($where);
        $pos = HTML::PagePos($count, $this->Page(), $pagesize);

        $url = Url::QUERY_STRING_DELETE(array('pn'));

        $v->orders = HTML::OrderLinks($url, array('id', 'event', 'spent', 'memory', 'time'));

        $v->findname = $findname;

        $v->rs = LogEventCls::Items($where, $this->Order(), $pagesize, $pos);
        $v->paged = HTML::PageGroup($count, $this->Page(), $url, $pagesize);

        echo $v->Render();

        $this->Footer();
    }

    public function CacheClean()
    {
        $this->Header();

        $v = View::Factory('CacheClean');
        echo $v->Render();

        $this->Foot();
    }

    public function OnCacheCleanSys()
    {
        $cache = new Cache ();
        $cache->Remove('system');

        echo '系统缓存已经清空';
    }

    public function OnCacheCleanUser()
    {
        $cache = new Cache ();
        $cache->RemoveAll();

        echo '全部用户缓存已经清空';
    }

    public function SystemTest()
    {
        $this->Header();

        $v = View::Factory('SystemTest');

        echo $v->Render();

        $this->Footer();
    }

    public function FacadeTypeList()
    {
        $id = $this->Req('id', 0, 'int');

        $this->Header();

        $view = View::Factory('FacadeTypeList');

        list($count, $rs) = FacadeTypeBiz::GetNextList($id, false);
        $view->rs = $rs;
        $view->id = $id;
        $view->path = $this->GetFacadeTypePath($id);
        $view->tree = json_encode(FacadeTypeBiz::Tree());

        echo $view->Render();

        $this->Footer();
    }

    public function FacadeTypeAdd()
    {
        $id = $this->Req('id', 0, 'int');

        $this->Header();

        $view = View::Factory('FacadeTypeAdd');

        $view->pid = $id;
        $view->parent = FacadeTypeBiz::Name($id);
        $view->tree = json_encode(FacadeTypeBiz::Tree());
        $view->tree1 = json_encode(FacadeTypeBiz::Tree());

        echo $view->Render();

        $this->Footer();
    }

    public function FacadeTypeEdit()
    {
        $id = $this->Req('id', 0, 'int');

        $this->Header();

        $view = View::Factory('FacadeTypeEdit');

        $pid = FacadeTypeBiz::Pid($id);
        $view->pid = $pid;
        $view->parent = FacadeTypeBiz::Name($pid);
        $view->rs = FacadeTypeBiz::Item($id);
        $view->tree = json_encode(FacadeTypeBiz::Tree());
        $view->tree1 = json_encode(FacadeTypeBiz::Tree());

        echo $view->Render();

        $this->Footer();
    }

    public function OnFacadeTypeAdd()
    {
        $pid = $this->Req('pid', 0, 'int');
        $name = $this->Req('name', '', 'str');
        $score = $this->Req('score', 0, 'int');

        echo Json::Out(FacadeTypeBiz::Add($pid, $name, $score));
    }

    public function OnFacadeTypeEdit()
    {
        $id = $this->Req('id', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');
        $name = $this->Req('name', '', 'str');
        $score = $this->Req('score', 0, 'int');


        echo Json::Out(FacadeTypeBiz::Edit($id, $pid, $name, $score));
    }

    public function OnFacadeTypeDelete()
    {
        $id = $this->Req('id', 0, 'int');

        echo Json::Out(FacadeTypeBiz::Delete($id));
    }

    private function GetFacadeTypePath($id)
    {
        $url = '?m=System&a=FacadeTypeList&tid=';
        $str = '<div class="path"><a href="' . $url . '">外观评测项目</a>';

        if ($id > 0) {
            list($count, $rs) = FacadeTypeBiz::GetParentList($id);
            foreach ($rs as $k => $v) {
                $str .= ' &gt; <a href="' . $url . $k . '">' . $v . '</a>';
            }
        }

        $str .= '</div>';
        return $str;
    }

    public function InitAdminPermission()
    {
        UserBiz::InitAdminPermission();

        echo 'OK';
    }

    private function getPermission($val)
    {
        $p = array();
        for ($i = 0; $i < R::TOTAL; $i++) {
            $p [$i . '_1'] = '';
            $p [$i . '_2'] = '';
            $p [$i . '_3'] = '';
            $p [$i . '_4'] = '';
            $p [$i . '_5'] = '';
            $p [$i . '_6'] = '';
            $p [$i . '_7'] = '';
            $p [$i . '_8'] = '';
            $p [$i . '_9'] = '';
            $p [$i . '_10'] = '';
            $p [$i . '_11'] = '';
            $p [$i . '_12'] = '';
            $p [$i . '_13'] = '';
            $p [$i . '_14'] = '';
            $p [$i . '_15'] = '';
            $p [$i . '_16'] = '';
        }

        //$m = explode ( ',', $val );
        foreach ($val as $k => $v) {
            if ($k < R::TOTAL) {
                if (P::isView($v))
                    $p [$k . '_1'] = 'checked';
                if (P::isAdd($v))
                    $p [$k . '_2'] = 'checked';
                if (P::isEdit($v))
                    $p [$k . '_3'] = 'checked';
                if (P::isDelete($v))
                    $p [$k . '_4'] = 'checked';
                if (P::isExport($v))
                    $p [$k . '_5'] = 'checked';
                if (P::isTeam($v))
                    $p [$k . '_6'] = 'checked';
                if (P::isPrice($v))
                    $p [$k . '_7'] = 'checked';
                if (P::isCost($v))
                    $p [$k . '_8'] = 'checked';
                if (P::isCustomer($v))
                    $p [$k . '_9'] = 'checked';
                if (P::isBIT10($v))
                    $p [$k . '_10'] = 'checked';
                if (P::isBIT11($v))
                    $p [$k . '_11'] = 'checked';
                if (P::isBIT12($v))
                    $p [$k . '_12'] = 'checked';
                if (P::isBIT13($v))
                    $p [$k . '_13'] = 'checked';
                if (P::isBIT14($v))
                    $p [$k . '_14'] = 'checked';
                if (P::isBIT15($v))
                    $p [$k . '_15'] = 'checked';
                if (P::isBIT16($v))
                    $p [$k . '_16'] = 'checked';
            }
        }
        return $p;
    }
}

?>