<?php

class CalendarMod extends BaseMod
{
    public function index()
    {
        $this->CalendarList();
    }

    //日程列表
    public function CalendarList()
    {
        $this->Header();

        if (!P::Instance($this->Uid())->isCanView(R::PROGRAMMETASK_CALENDARLIST))
        {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $name = $this->Req('name', '', 'str');
        $stateid = $this->Req('stateid', 0, 'int');

        $where = '';

        if (!empty ($name))
            $where .= ' AND (LOWER(content) like \'%' . strtolower($name) . '%\') ';
        if ($stateid > 0)
            $where .= ' AND stateid=' . $stateid;

        $url = Url::QUERY_STRING_DELETE(array('pn'));
        $size = PAGE_SIZE_MEDIUM;
        $ret = CalendarInterface::CalendarItems($this->Uid(), R::PROGRAMMETASK_CALENDARLIST, $where, '', $this->Page(), $size, false);
        $count = $ret['msg'];
        $rs = $ret['data'];

        $view = View::factory('CalendarList');

        $view->rs = $rs;
        $view->paged = HTML::PageJump($count, $this->Page(), $size, $url);

        $view->name = $name;
        $view->stateid = HTML::CtlSelList(CalendarInterface::CalendarStateItems()['data'], 'stateid', '', $stateid, '全部');

        echo $view->render();

        $this->Footer();
    }

    //增加日程
    public function CalendarAdd()
    {
        $this->Header();

        if (!P::Instance($this->Uid())->isCanView(R::PROGRAMMETASK_CALENDARADD))
        {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $view = View::factory('CalendarAdd');

        $view->stateid = HTML::CtlSelList(CalendarInterface::CalendarStateItems()['data'], 'stateid', 'r', '');
        $view->date = Date::Format(time(), 'd', null);

        echo $view->render();

        $this->Footer();
    }

    //编辑日程
    public function CalendarEdit()
    {
        $id = $this->Req('id', 0, 'int');

        $this->Header();

        $view = View::Factory('CalendarEdit');

        $rs = CalendarInterface::CalendarItem($id)['data'];

        $view->rs = $rs;

        $view->stateid = HTML::CtlSelList(CalendarInterface::CalendarStateItems()['data'], 'stateid', 'r', $rs['stateid']);

        echo $view->Render();

        $this->Footer();
    }

    public function OnCalendarAdd()
    {
        $date = trim($this->Req('date', '', 'str'));
        $stateid = $this->Req('stateid', 0, 'int');
        $customer = trim($this->Req('customer', '', 'str'));
        $contacts = trim($this->Req('contacts', '', 'str'));
        $content = Url::req('content', '', 'str');
        $plan = Url::req('plan', '', 'str');
        $walkway = trim($this->Req('walkway', '', 'str'));
        $distance = trim($this->Req('distance', '', 'str'));
        $memo = $this->Req('memo', '', 'str');
        $uid = $this->Uid();

        echo Json::Out(CalendarInterface::CalendarAdd($date, $stateid, $customer, $contacts, $content, $plan, $walkway, $distance, $memo, $uid));
    }

    public function OnCalendarEdit()
    {
        $id = Url::Req('id', 0, 'int');

        $date = trim($this->Req('date', '', 'str'));
        $stateid = $this->Req('stateid', 0, 'int');
        $customer = trim($this->Req('customer', '', 'str'));
        $contacts = trim($this->Req('contacts', '', 'str'));
        $content = Url::req('content', '', 'str');
        $plan = Url::req('plan', '', 'str');
        $walkway = trim($this->Req('walkway', '', 'str'));
        $distance = trim($this->Req('distance', '', 'str'));
        $memo = $this->Req('memo', '', 'str');
        $uid = $this->Uid();

        echo Json::Out(CalendarInterface::CalendarEdit($id, $date, $stateid, $customer, $contacts, $content, $plan, $walkway, $distance, $memo, $uid));
    }

    public function OnCalendarDelete()
    {
        $id = $this->Req('id', 0, 'int');
        CalendarCls::delete($id);
        Json::ReturnSuccess();
    }

    //任务列表
    public function TaskList()
    {
        $this->Header();

        if (!P::Instance($this->Uid())->isCanAdd(R::PROGRAMMETASK_TASKLIST))
        {
            UtilMod::MessageBoxUnableAdd();
            return;
        }

        $name = $this->Req('name', '', 'str');
        $stateid = $this->Req('stateid', 0, 'int');
        $typeid = $this->Req('typeid', 0, 'int');

        $where = '';
        if (!empty ($name))
            $where .= ' AND (LOWER(content) like \'%' . strtolower($name) . '%\') ';
        if ($stateid > 0)
            $where .= ' AND stateid=' . $stateid;
        if ($typeid > 0)
            $where .= ' AND typeid=' . $typeid;

        $url = Url::QUERY_STRING_DELETE(array('pn'));
        $size = PAGE_SIZE_MEDIUM;
        $ret = CalendarInterface::TaskItems($this->Uid(), $where, '', $this->Page(), $size, false);
        $count = $ret['msg'];
        $rs = $ret['data'];

        $view = View::factory('TaskList');

        $view->rs = $rs;
        $view->paged = HTML::PageJump($count, $this->Page(), $size, $url);

        $view->name = $name;
        $view->typeid = HTML::CtlSelList(CalendarInterface::CalendarTypeItems()['data'], 'typeid', '', $typeid, '全部');
        $view->stateid = HTML::CtlSelList(CalendarInterface::CalendarStateItems()['data'], 'stateid', '', $stateid, '全部');

        echo $view->render();

        $this->Footer();
    }

    //增加任务
    public function TaskAdd()
    {
        $this->Header();

        if (!P::Instance($this->Uid())->isCanAdd(R::PROGRAMMETASK_TASKADD))
        {
            UtilMod::MessageBoxUnableAdd();
            return;
        }

        $view = View::factory('TaskAdd');

        $view->typeid = HTML::CtlSelList(CalendarInterface::CalendarTypeItems()['data'], 'typeid', 'r', '');
        $view->stateid = HTML::CtlSelList(CalendarInterface::CalendarStateItems()['data'], 'stateid', 'r', '');
        $view->date = Date::Format(time(), 'd', null);

        echo $view->render();

        $this->Footer();
    }

    public function TaskEdit()
    {
        $id = $this->Req('id', 0, 'int');

        $this->Header();

        $v = View::Factory('TaskEdit');

        $gid1 = UserCls::Instance()->GetGid($this->Uid());

        $v->dtree = json_encode(GroupBiz::TreeOgroups($gid1, true, true));
        $v->utree = json_encode(GroupBiz::Tree($gid1, true, true));

        $rs = TaskCls::Instance()->Item($id);
        $rs['customer'] = SalesBiz::GetName($rs['customerid']);
        $rs['tasktype'] = TaskTypeCls::Instance()->GetName($rs['tasktypeid']);
        $rs['state'] = CalendarStateCls::Instance()->GetStateName($rs['stateid']);
        $rs['creatorname'] = UserBiz::GetName($rs['creatorid']);
        $rs['chancep'] = ChanceCls::Instance()->GetName($rs['chancepid']);
        $rs['market'] = MarketCls::Instance()->GetName($rs['marketid']);
        $rs['clew'] = ClewCls::Instance()->GetName($rs['clewid']);
        $v->rs = $rs;

        $v->receiptor = TaskCls::Instance()->getReceiptorNameSpanString($id);

        $v->tastype = TaskTypeCls::Instance()->Items();
        $v->tstate = CalendarStateCls::Instance()->Items();

        $v->calendartypeid = HTML::CtlSelKVList(CalendarTypeCls::Instance()->Items(), 'calendartypeid', '', '');
        $v->stateid = HTML::CtlSelKVList(CalendarStateCls::Instance()->Items(), 'stateid', '', '');

        echo $v->Render();

        $this->Footer();
    }

    public function TaskView()
    {
        $this->Header();

        if (!P::Instance($this->Uid())->isCanEdit(R::CALENDAR_TASK))
        {
            UtilMod::MessageBoxUnableEdit();
            return;
        }

        $id = UUID::check(Url::req('id', '', 'str'));

        $view = View::factory('TaskView');

        $view->showexport = P::Instance($this->Uid())->isCanExport(R::CALENDAR_TASK);

        $rs = TaskCls::Instance()->Item($id);

        if (!empty ($rs))
        {
            $view->receiptor = TaskCls::Instance()->getReceiptorNameString($id);
            $view->reply = TaskCls::Instance()->getReplyNameString($id);
        }

        $view->rs = $rs;

        echo $view->render();

        $this->Footer();
    }

    public function OnTaskAdd()
    {
        $name = trim(Url::req('name', '', 'str'));
        $tasktypeid = $this->Req('tasktypeid', 0, 'int');
        $stateid = Url::req('stateid', 0, 'int');
        $customerid = $this->Req('customerid', 0, 'int');
        $marketid = $this->Req('marketid', 0, 'int');
        $chancepid = $this->Req('chancepid', 0, 'int');
        $chancestageid = $this->Req('chancestageid', 0, 'int');
        $clewid = $this->Req('clewid', 0, 'int');
        $receiptor = $this->Req('ou', '', 'str');
        $memo = trim(Url::req('memo', '', 'str'));
        $creatorid = UserCls::Instance()->GetId($this->Uid());

        if (Util::isBlank($name))
        {
            echo '请输入任务名称';
            return;
        }
        if (!Util::isMaxLen($name, 200))
        {
            echo '任务名称过长';
            return;
        }
        if (empty ($tasktypeid))
        {
            echo '请选择任务类型';
            return;
        }
        if (Util::isBlank($memo))
        {
            echo '请输入任务说明';
            return;
        }

        TaskCls::addTask($name, $tasktypeid, $stateid, $customerid, $marketid, $chancepid, $chancestageid, $clewid, $receiptor, $memo, $creatorid);

        echo 1;
    }

    public function OnTaskEdit()
    {
        $id = Url::Req('id', 0, 'int');
        $name = trim(Url::req('name', '', 'str'));
        $tastype = $this->Req('tastype', 0, 'int');
        $tstate = $this->Req('tstate', 0, 'int');
        $customerid = $this->Req('customerid', 0, 'int');
        $marketid = $this->Req('marketid', 0, 'int');
        $chancepid = $this->Req('chancepid', 0, 'int');
        $chancestageid = $this->Req('chancestageid', 0, 'int');
        $clewid = $this->Req('clewid', 0, 'int');
        $receiptor = $this->Req('ou', '', 'str');
        $memo = trim(Url::req('memo', '', 'str'));
        $creatorid = UserCls::Instance()->GetId($this->Uid());

        if (Util::isBlank($name))
        {
            echo '请输入任务名称';
            return;
        }
        if (!Util::isMaxLen($name, 200))
        {
            echo '任务名称过长';
            return;
        }
        if (Util::isBlank($memo))
        {
            echo '请输入任务说明';
            return;
        }

        TaskCls::editTask($id, $name, $tastype, $tstate, $customerid, $marketid, $chancepid, $chancestageid, $clewid, $receiptor, $memo, $creatorid);

        echo 1;
    }

    public function OnTaskDelete()
    {
        $id = $this->Req('id', 0, 'int');

        TaskCls::deleteTask($id);

        Json::ReturnSuccess();
    }

    public function OnTaskReply()
    {
        $id = UUID::check(Url::req('id', '', 'str'));
        if (empty ($id))
        {
            echo ALERT_ERROR;
            return;
        }

        $memo = trim(Url::req('memo', '', 'str'));

        if (Util::isBlank($memo))
        {
            echo '请输入回应内容';
            return;
        }

        TaskCls::addTaskReply(UUID::make(), $id, $memo, $this->Uid());

        echo 1;
    }

    //商机列表
    public function ChanceList()
    {
        $this->Header();

        if (!P::Instance($this->Uid())->isCanAdd(R::PROGRAMMETASK_CHANCELIST))
        {
            UtilMod::MessageBoxUnableAdd();
            return;
        }

        $findname = $this->Req('findname', '', 'str');
        $hopesuccess = $this->Req('hopesuccess', 0, 'int');
        $chancestageid = $this->Req('chancestageid', 0, 'int');

        $where = 'WHERE del=false';
        $order = 'ORDER BY id ASC';

        if (!empty ($findname))
            $where .= ' AND (LOWER(name) like \'%' . strtolower($findname) . '%\') ';
        if (!empty ($hopesuccess))
            $where .= ' AND hopesuccess = ' . $hopesuccess;
        if (!empty ($chancestageid))
            $where .= ' AND chancestageid = ' . $chancestageid;

        $pagesize = PAGE_SIZE_MEDIUM;
        $count = ChanceCls::queryCount($where);
        $pos = HTML::PagePos($count, $this->Page(), $pagesize);

        $url = Url::QUERY_STRING_DELETE(array('pn'));

        $v = View::factory('ChanceList');

        $rs = ChanceCls::queryList($where, $order, $pagesize, $pos);
        if ($rs && count($rs) > 0)
        {
            for ($i = 0; $i < count($rs); $i++)
            {
                $rs [$i] ['customer'] = VendorBiz::GetNameByID($rs [$i] ['customerid']);
                $rs [$i] ['chancestage'] = ChanceStagesCls::Instance()->GetName($rs [$i] ['chancestageid']);
                $rs [$i] ['state'] = CalendarStateCls::Instance()->GetStateName($rs [$i] ['stateid']);
                $rs [$i] ['creatorname'] = UserBiz::GetName($rs[$i]['creatorid']);
            }
        }

        $v->rs = $rs;

        $v->paged = HTML::PageGroup($count, $this->Page(), $url, $pagesize);


        $v->chancestageid = HTML::CtlSelKVList(ChanceStageCls::Instance()->Items(), 'chancestageid', '', '');
        $v->hopesuccess = HTML::CtlNumList('hopesuccess', 30, 100, 10, 'fss', null, true, 0, '%');

        echo $v->render();

        $this->Footer();
    }

    //增加商机
    public function ChanceAdd()
    {
        $this->Header();

        if (!P::Instance($this->Uid())->isCanAdd(R::PROGRAMMETASK_CHANCEADD))
        {
            UtilMod::MessageBoxUnableAdd();
            return;
        }

        $v = View::factory('ChanceAdd');

        $gid1 = UserCls::Instance()->GetGid($this->Uid());

        $v->dtree = json_encode(GroupBiz::TreeOgroups($gid1, true, true));
        $v->utree = json_encode(GroupBiz::Tree($gid1, true, true));

        $v->chancestageid = HTML::CtlSelKVList(ChanceStageCls::Instance()->Items(), 'chancestageid', '', '');
        $v->hopesuccess = HTML::CtlNumList('hopesuccess', 30, 100, 10, 'fss', null, true, 0, '%');

        echo $v->render();

        $this->Footer();
    }

    //编辑商机
    public function ChanceEdit()
    {
        $id = $this->Req('id', 0, 'int');

        $this->Header();

        $v = View::Factory('ChanceEdit');

        $rs = ChanceCls::Instance()->Item($id);
        $rs['customer'] = VendorBiz::GetNameByID($rs['customerid']);
        $rs['chancestage'] = ChanceStagesCls::Instance()->GetName($rs['chancestageid']);
        $rs['creatorname'] = UserBiz::GetName($rs['creatorid']);
        $v->rs = $rs;

        $v->calendartypeid = HTML::CtlSelKVList(CalendarTypeCls::Instance()->Items(), 'calendartypeid', '', '');
        $v->stateid = HTML::CtlSelKVList(CalendarStateCls::Instance()->Items(), 'stateid', '', '');

        echo $v->Render();

        $this->Footer();
    }

    public function OnChanceAdd()
    {
        $name = Url::Req('name', '', 'str');
        $chancestageid = Url::Req('chancestageid', 0, 'int');
        $hopesuccess = Url::Req('hopesuccess', '', 'str');
        $customerid = Url::Req('customerid', 0, 'int');
        $hopedate = Url::Req('hopedate', '', 'str');
        $hopemoney = Url::Req('hopemoney', '', 'str');
        $nextstep = Url::Req('nextstep', '', 'str');
        $hopeincome = Url::Req('hopeincome', '', 'str');
        $memo = Url::Req('memo', '', 'str');
        $creatorid = UserCls::Instance()->GetId($this->Uid());

        if (Util::isBlank($name))
        {
            echo '请输入商机名称';
            return;
        }
        if (!Util::isMaxLen($name, 200))
        {
            echo '商机名称过长';
            return;
        }
        if (empty ($chancestageid))
        {
            echo '请选择商机阶段';
            return;
        }
        if (empty ($hopesuccess))
        {
            echo '请选择预计成功率';
            return;
        }
        if (empty ($customerid))
        {
            echo '请选择所属客户';
            return;
        }
        if (empty ($hopedate))
        {
            echo '请选择预计成交日期';
            return;
        }
        ChanceCls::addChance($name, $chancestageid, $hopesuccess, $customerid, $hopedate, $hopemoney, $nextstep, $hopeincome, $memo, $creatorid);

        echo 1;
    }

    public function OnChanceEdit()
    {
        $id = Url::Req('id', 0, 'int');
        $name = Url::Req('name', '', 'str');
        $customerid = Url::Req('customerid', 0, 'int');
        $hopedate = Url::Req('hopedate', '', 'str');
        $hopemoney = Url::Req('hopemoney', '', 'str');
        $nextstep = Url::Req('nextstep', '', 'str');
        $hopeincome = Url::Req('hopeincome', '', 'str');
        $memo = Url::Req('memo', '', 'str');
        $creatorid = UserCls::Instance()->GetId($this->Uid());

        if (Util::isBlank($name))
        {
            echo '请输入商机名称';
            return;
        }
        if (!Util::isMaxLen($name, 200))
        {
            echo '商机名称过长';
            return;
        }
        if (empty ($customerid))
        {
            echo '请选择所属客户';
            return;
        }
        if (empty ($hopedate))
        {
            echo '请选择预计成交日期';
            return;
        }
        ChanceCls::editChance($id, $name, $customerid, $hopedate, $hopemoney, $nextstep, $hopeincome, $memo, $creatorid);

        echo 1;
    }

    public function OnChanceDelete()
    {
        $id = $this->Req('id', 0, 'int');

        ChanceCls::deleteChance($id);

        Json::ReturnSuccess();

    }

    //活动列表
    public function MarketList()
    {
        $this->Header();

        if (!P::Instance($this->Uid())->isCanView(R::PROGRAMMETASK_MARKETLIST))
        {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $findname = $this->Req('findname', '', 'str');
        $hopesuccess = $this->Req('hopesuccess', 0, 'int');
        $markettypeid = $this->Req('markettypeid', 0, 'int');

        $where = 'WHERE del=false';
        $order = 'ORDER BY id ASC';

        if (!empty ($findname))
            $where .= ' AND (LOWER(name) like \'%' . strtolower($findname) . '%\') ';
        if (!empty ($hopesuccess))
            $where .= ' AND hopesuccess = ' . $hopesuccess;
        if (!empty ($markettypeid))
            $where .= ' AND markettypeid = ' . $markettypeid;

        $pagesize = PAGE_SIZE_MEDIUM;
        $count = MarketCls::Count($where);
        $pos = HTML::PagePos($count, $this->Page(), $pagesize);

        $url = Url::QUERY_STRING_DELETE(array('pn'));

        $v = View::factory('MarketList');

        $rs = MarketCls::queryList($where, $order, $pagesize, $pos);
        if ($rs && count($rs) > 0)
        {
            for ($i = 0; $i < count($rs); $i++)
            {
                $rs [$i] ['markettypeid'] = MarketTypeCls::Instance()->GetName($rs[$i]['markettypeid']);
                $rs [$i] ['state'] = CalendarStateCls::Instance()->GetStateName($rs [$i] ['stateid']);
                $rs [$i] ['creatorname'] = UserBiz::GetName($rs[$i]['creatorid']);
            }
        }

        $v->rs = $rs;

        $v->paged = HTML::PageGroup($count, $this->Page(), $url, $pagesize);

        $v->markettypeid = HTML::CtlSelKVList(MarketTypeCls::Instance()->Items(), 'markettypeid', '', '');
        $v->hopesuccess = HTML::CtlNumList('hopesuccess', 30, 100, 10, 'fss', null, true, 0, '%');

        echo $v->render();

        $this->Footer();
    }

    //增加活动
    public function MarketAdd()
    {
        $this->Header();

        if (!P::Instance($this->Uid())->isCanAdd(R::PROGRAMMETASK_MARKETADD))
        {
            UtilMod::MessageBoxUnableAdd();
            return;
        }

        $v = View::factory('MarketAdd');

        $v->markettypeid = HTML::CtlSelKVList(MarketTypeCls::Instance()->Items(), 'markettypeid', '', '');
        $v->hopesuccessid = HTML::CtlSelKVList(MarketHopesCls::Instance()->Items(), 'hopesuccess', '', '');
        $v->stateid = HTML::CtlSelKVList(CalendarStateCls::Instance()->Items(), 'stateid', '', '');

        echo $v->render();

        $this->Footer();
    }

    //编辑活动
    public function MarketEdit()
    {
        $id = $this->Req('id', 0, 'int');

        $this->Header();

        $v = View::Factory('MarketEdit');

        $rs = MarketCls::Instance()->Item($id);
        $rs['markettypeid'] = MarketTypeCls::Instance()->GetName($rs['markettypeid']);
        $rs['stateid'] = CalendarStateCls::Instance()->GetStateName($rs['stateid']);
        $rs['creatorname'] = UserBiz::GetName($rs['creatorid']);

        $v->rs = $rs;
        $v->martype = MarketTypeCls::Instance()->Items();
        $v->mstate = CalendarStateCls::Instance()->Items();
        $v->mhopes = MarketHopesCls::Instance()->Items();

        echo $v->Render();

        $this->Footer();
    }

    public function OnMarketAdd()
    {
        $name = Url::Req('name', '', 'str');
        $markettypeid = Url::Req('markettypeid', 0, 'int');
        $stateid = Url::Req('stateid', 0, 'int');
        $hopecost = Url::Req('hopecost', 0, 'int');
        $hopesuccessid = Url::Req('hopesuccessid', 0, 'int');
        $hopeincome = Url::Req('hopeincome', '', 'str');
        $begintime = trim(Url::req('begintime', '', 'str'));
        $endtime = trim(Url::req('endtime', '', 'str'));
        $address = trim(Url::req('address', '', 'str'));
        $realcost = Url::Req('realcost', 0, 'int');
        $memo = Url::Req('memo', '', 'str');
        $creatorid = UserCls::Instance()->GetId($this->Uid());

        if (Util::isBlank($name))
        {
            echo '请输入商机名称';
            return;
        }

        $begintime = Date::Format($begintime, 's');
        if (empty ($endtime))
            $endtime = null;
        else $endtime = Date::Format($endtime, 's');

        MarketCls::Add($name, $markettypeid, $stateid, $hopecost, $hopesuccessid, $hopeincome, $begintime, $endtime, $address, $realcost, $memo, $creatorid);

        echo 1;
    }

    public function OnMarketEdit()
    {
        $id = Url::Req('id', 0, 'int');
        $name = Url::Req('name', '', 'str');
        $hopecost = Url::Req('hopecost', 0, 'int');
        $hopeincome = Url::Req('hopeincome', '', 'str');
        $begintime = trim(Url::req('begintime', '', 'str'));
        $endtime = trim(Url::req('endtime', '', 'str'));
        $address = trim(Url::req('address', '', 'str'));
        $realcost = Url::Req('realcost', 0, 'int');
        $memo = Url::Req('memo', '', 'str');
        $creatorid = UserCls::Instance()->GetId($this->Uid());

        $begintime = Date::Format($begintime, 's');
        if (empty ($endtime))
            $endtime = null;
        else $endtime = Date::Format($endtime, 's');

        MarketCls::Edit($id, $name, $hopecost, $hopeincome, $begintime, $endtime, $address, $realcost, $memo, $creatorid);

        echo 1;
    }

    //线索列表
    public function ClewList()
    {
        $this->Header();

        if (!P::Instance($this->Uid())->isCanView(R::PROGRAMMETASK_MARKETLIST))
        {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $findname = $this->Req('findname', '', 'str');
        $clewsourceid = $this->Req('clewsourceid', 0, 'int');
        $priorityid = $this->Req('priorityid', 0, 'int');

        $where = 'WHERE del=false';
        $order = 'ORDER BY id ASC';

        if (!empty ($findname))
            $where .= ' AND (LOWER(name) like \'%' . strtolower($findname) . '%\') ';
        if (!empty ($clewsourceid))
            $where .= ' AND clewsourceid = ' . $clewsourceid;
        if (!empty ($priorityid))
            $where .= ' AND priorityid = ' . $priorityid;

        $pagesize = PAGE_SIZE_MEDIUM;
        $count = ClewCls::queryCount($where);
        $pos = HTML::PagePos($count, $this->Page(), $pagesize);

        $url = Url::QUERY_STRING_DELETE(array('pn'));

        $v = View::factory('ClewList');

        $rs = ClewCls::queryList($where, $order, $pagesize, $pos);
        if ($rs && count($rs) > 0)
        {
            for ($i = 0; $i < count($rs); $i++)
            {
                $rs [$i] ['ownerid'] = UserBiz::GetName($rs[$i]['ownerid']);
                $rs [$i] ['clewsourceid'] = ClewSourceCls::Instance()->GetName($rs[$i]['clewsourceid']);
                $rs [$i] ['priorityid'] = ClewPriorityCls::Instance()->GetName($rs[$i]['priorityid']);
                $rs [$i] ['marketid'] = MarketCls::Instance()->GetName($rs[$i]['marketid']);
                $rs [$i] ['state'] = CalendarStateCls::Instance()->GetStateName($rs [$i] ['stateid']);
                $rs [$i] ['creatorname'] = UserBiz::GetName($rs[$i]['creatorid']);
            }
        }

        $v->rs = $rs;

        $v->paged = HTML::PageGroup($count, $this->Page(), $url, $pagesize);

        $v->clewsourceid = HTML::CtlSelKVList(ClewSourceCls::Instance()->Items(), 'clewsourceid', '', '');
        $v->priorityid = HTML::CtlSelKVList(ClewPriorityCls::Instance()->Items(), 'priorityid', '', '');

        echo $v->render();

        $this->Footer();
    }

    //增加线索
    public function ClewAdd()
    {
        $this->Header();

        if (!P::Instance($this->Uid())->isCanAdd(R::PROGRAMMETASK_CLEWADD))
        {
            UtilMod::MessageBoxUnableAdd();
            return;
        }

        $v = View::factory('ClewAdd');

        $gid1 = UserCls::Instance()->GetGid($this->Uid());
        $v->pid = $gid1;
        $v->pname = GroupBiz::GetName($gid1);
        $v->admintree = json_encode(GroupBiz::Tree($gid1, true, true));

        $v->clewsourceid = HTML::CtlSelKVList(ClewSourceCls::Instance()->Items(), 'clewsourceid', '', '');
        $v->priorityid = HTML::CtlSelKVList(ClewPriorityCls::Instance()->Items(), 'priorityid', '', '');

        echo $v->render();

        $this->Footer();
    }

    //编辑线索
    public function ClewEdit()
    {
        $id = $this->Req('id', 0, 'int');

        $this->Header();

        $v = View::Factory('ClewEdit');

        $gid1 = UserCls::Instance()->GetGid($this->Uid());
        $v->pid = $gid1;
        $v->pname = GroupBiz::GetName($gid1);
        $v->admintree = json_encode(GroupBiz::Tree($gid1, true, true));

        $rs = ClewCls::Instance()->Item($id);
        $rs['ownername'] = UserBiz::GetName($rs['ownerid']);
        $rs['marketname'] = MarketCls::Instance()->GetName($rs['marketid']);
        $rs['creatorname'] = UserBiz::GetName($rs['creatorid']);
        $v->rs = $rs;

        $v->clewsourceid = HTML::CtlSelKVList(ClewSourceCls::Instance()->Items(), 'clewsourceid', '', '');
        $v->priorityid = HTML::CtlSelKVList(ClewPriorityCls::Instance()->Items(), 'priorityid', '', '');

        echo $v->Render();

        $this->Footer();
    }

    public function OnClewAdd()
    {
        $name = Url::Req('name', '', 'str');
        $ownerid = Url::Req('adminid', 0, 'int');
        $clewsourceid = Url::Req('clewsourceid', 0, 'int');
        $priorityid = Url::Req('priorityid', 0, 'int');
        $customer = Url::Req('customer', '', 'str');
        $marketid = Url::Req('marketid', 0, 'int');
        $contact = Url::Req('contact', '', 'str');
        $address = Url::Req('address', '', 'str');
        $phone = Url::Req('phone', '', 'str');
        $postal = Url::Req('postal', '', 'str');
        $fax = Url::Req('fax', '', 'str');
        $homepage = Url::Req('homepage', '', 'str');
        $memo = Url::Req('memo', '', 'str');
        $creatorid = UserCls::Instance()->GetId($this->Uid());

        if (Util::isBlank($name))
        {
            echo '请输入线索名称';
            return;
        }
        if (!Util::isMaxLen($name, 200))
        {
            echo '线索名称过长';
            return;
        }
        if (empty ($ownerid))
        {
            echo '请选择负责人';
            return;
        }
        if (empty ($clewsourceid))
        {
            echo '请选择线索来源';
            return;
        }
        if (empty ($priorityid))
        {
            echo '请选择优先级';
            return;
        }

        ClewCls::addClew($name, $ownerid, $clewsourceid, $priorityid, $customer, $marketid, $contact, $address, $phone, $postal, $fax, $homepage, $memo, $creatorid);

        echo 1;
    }

    public function OnClewEdit()
    {
        $id = Url::Req('id', 0, 'int');
        $name = Url::Req('name', '', 'str');
        $ownerid = Url::Req('adminid', 0, 'int');
        $clewsourceid = Url::Req('clewsourceid', 0, 'int');
        $priorityid = Url::Req('priorityid', 0, 'int');
        $customer = Url::Req('customer', '', 'str');
        $marketid = Url::Req('marketid', 0, 'int');
        $contact = Url::Req('contact', '', 'str');
        $address = Url::Req('address', '', 'str');
        $phone = Url::Req('phone', '', 'str');
        $postal = Url::Req('postal', '', 'str');
        $fax = Url::Req('fax', '', 'str');
        $homepage = Url::Req('homepage', '', 'str');
        $memo = Url::Req('memo', '', 'str');

        if (Util::isBlank($name))
        {
            echo '请输入线索名称';
            return;
        }
        if (!Util::isMaxLen($name, 200))
        {
            echo '线索名称过长';
            return;
        }
        if (empty ($ownerid))
        {
            echo '请选择负责人';
            return;
        }
        if (empty ($clewsourceid))
        {
            echo '请选择线索来源';
            return;
        }
        if (empty ($priorityid))
        {
            echo '请选择优先级';
            return;
        }
        ClewCls::editClew($id, $name, $ownerid, $clewsourceid, $priorityid, $customer, $marketid, $contact, $address, $phone, $postal, $fax, $homepage, $memo);
        echo 1;
    }

    public function OnClewDelete()
    {
        $id = $this->Req('id', 0, 'int');

        ClewCls::deleteClew($id);

        Json::ReturnSuccess();

    }

    public function PopCalendarList()
    {
        $this->Head();

        if (!P::Instance($this->Uid())->isCanView(R::CALENDAR_CALENDAR))
        {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $findname = Url::req('findname', '', 'str');

        $orderkey = Url::req('orderkey', '', 'str');
        $orderval = Url::req('orderval', 0, 'int');

        $where = 'WHERE del=false';

        if (!$this->IsAdmin())
        {
            $perm = array($this->Uid());
            $lead = UserCls::Instance()->IsLead($this->Uid());
            if ($lead)
            {
                $dept = UserCls::Instance()->getDepartmentID($this->Uid());
                $depts = DepartmentCls::Instance()->GetChildren($dept);
                foreach ($depts as $v)
                {
                    $perm = array_merge($perm, array_keys(UserCls::Instance()->getUsersByDepartmentID($v)));
                }
            }

            $perm = array_merge($perm, AclCls::Instance($this->Uid())->getRoleResourceViewUserList(R::CALENDAR_CALENDAR));

            $perm = "'" . implode("','", $perm) . "'";
            $where .= ' AND (creatorid IN (' . $perm . ') OR team LIKE \'%' . $this->Uid() . '%\')';
        }

        $customerid = UUID::check(Url::req('customerid', '', 'str'));
        if (!empty ($customerid))
        {
            $where .= " AND customerid='$customerid'::UUID";
        }
        $supplierid = UUID::check(Url::req('supplierid', '', 'str'));
        if (!empty ($supplierid))
        {
            $where .= " AND supplierid='$supplierid'::UUID";
        }
        $marketid = UUID::check(Url::req('marketid', '', 'str'));
        if (!empty ($marketid))
        {
            $where .= " AND marketid='$marketid'::UUID";
        }
        $clewid = UUID::check(Url::req('clewid', '', 'str'));
        if (!empty ($clewid))
        {
            $where .= " AND clewid='$clewid'::UUID";
        }
        $chancepid = UUID::check(Url::req('chancepid', '', 'str'));
        if (!empty ($chancepid))
        {
            $where .= " AND chancepid='$chancepid'::UUID";
        }

        if (!empty ($findname))
            $where .= ' AND (name like \'%' . $findname . '%\' OR memo like \'%' . $findname . '%\') ';

        if (empty ($orderkey))
            $order = 'ORDER BY time DESC';
        else
        {
            $order = 'ORDER BY ' . $orderkey;
            if ($orderval)
                $order .= ' DESC';
            else $order .= ' ASC';
        }

        $view = View::factory('PopCalendarList');

        $pagesize = PAGE_SIZE_MEDIUM;
        $count = CalendarCls::queryCount($where);
        $pos = HTML::PagePos($count, $this->Page(), $pagesize);

        $url = Url::QUERY_STRING_DELETE(array('pn'));

        $view->order = HTML::OrderLinks($url, array('name', 'customerid', 'calendartypeid', 'begintime', 'time', 'stateid', 'creatorid'));

        $view->findname = $findname;

        $view->rs = CalendarCls::queryList($where, $order, $pagesize, $pos);
        $view->paged = HTML::Paged($count, $this->Page(), $url, $pagesize);

        echo $view->render();

        $this->Foot();
    }

    public function PopCalendarAdd()
    {
        $this->Head();

        if (!P::Instance($this->Uid())->isCanAdd(R::CALENDAR_CALENDAR))
        {
            UtilMod::MessageBoxUnableAdd();
            return;
        }

        $view = View::factory('PopCalendarAdd');

        $view->type = HTML::CtlSelKVList(CalendarTypeCls::Instance()->Items(), 'type', '', '');
        $view->state = HTML::CtlSelList(BaseStateCls::Instance()->Items(), 'state', '', '');

        echo $view->render();

        $this->Foot();
    }

    public function PopTaskList()
    {
        $this->Head();

        if (!P::Instance($this->Uid())->isCanView(R::CALENDAR_TASK))
        {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $findname = Url::req('findname', '', 'str');

        $orderkey = Url::req('orderkey', '', 'str');
        $orderval = Url::req('orderval', 0, 'int');

        $where = 'WHERE del=false AND pid IS NULL';

        $customerid = UUID::check(Url::req('customerid', '', 'str'));
        if (!empty ($customerid))
        {
            $where .= " AND customerid='$customerid'::UUID";
        }
        $supplierid = UUID::check(Url::req('supplierid', '', 'str'));
        if (!empty ($supplierid))
        {
            $where .= " AND supplierid='$supplierid'::UUID";
        }
        $marketid = UUID::check(Url::req('marketid', '', 'str'));
        if (!empty ($marketid))
        {
            $where .= " AND marketid='$marketid'::UUID";
        }
        $clewid = UUID::check(Url::req('clewid', '', 'str'));
        if (!empty ($clewid))
        {
            $where .= " AND clewid='$clewid'::UUID";
        }
        $chancepid = UUID::check(Url::req('chancepid', '', 'str'));
        if (!empty ($chancepid))
        {
            $where .= " AND chancepid='$chancepid'::UUID";
        }

        if (!$this->IsAdmin())
        {
            $perm = array($this->Uid());
            $lead = UserCls::Instance()->IsLead($this->Uid());
            if ($lead)
            {
                $dept = UserCls::Instance()->getDepartmentID($this->Uid());
                $depts = DepartmentCls::Instance()->GetChildren($dept);
                foreach ($depts as $v)
                {
                    $perm = array_merge($perm, array_keys(UserCls::Instance()->getUsersByDepartmentID($v)));
                }
            }

            $perm = array_merge($perm, AclCls::Instance($this->Uid())->getRoleResourceViewUserList(R::CALENDAR_TASK));

            $perm = "'" . implode("','", $perm) . "'";
            $where .= ' AND (creatorid IN (' . $perm . ') OR receiptor like \'%' . $this->Uid() . '%\')';
        }

        if (!empty ($findname))
            $where .= ' AND (name like \'%' . $findname . '%\' OR memo like \'%' . $findname . '%\') ';

        if (empty ($orderkey))
            $order = 'ORDER BY time DESC';
        else
        {
            $order = 'ORDER BY ' . $orderkey;
            if ($orderval)
                $order .= ' DESC';
            else $order .= ' ASC';
        }

        $view = View::factory('PopTaskList');

        $pagesize = PAGE_SIZE_MEDIUM;
        $count = TaskCls::queryCount($where);
        $pos = HTML::PagePos($count, $this->Page(), $pagesize);

        $url = Url::QUERY_STRING_DELETE(array('pn'));

        $view->order = HTML::OrderLinks($url, array('name', 'tasktypeid', 'state', 'last', 'time', 'creatorid'));

        $view->findname = $findname;

        $view->rs = TaskCls::queryList($where, $order, $pagesize, $pos);
        $view->paged = HTML::Paged($count, $this->Page(), $url, $pagesize);

        echo $view->render();

        $this->Foot();
    }

    public function PopTaskReplyList()
    {
        $this->Head();

        if (!P::Instance($this->Uid())->isCanView(R::CALENDAR_TASK))
        {
            UtilMod::MessageBoxUnableList();
            return;
        }

        $id = UUID::check(Url::req('id', '', 'str'));

        $view = View::factory('PopTaskReplyList');

        $view->rs = TaskCls::getReplyList($id);

        echo $view->render();

        $this->Foot();
    }

    //查询商机
    public function PopChanceSelect()
    {
        $this->Head();

        $findname = $this->Req('findname', '', 'str');
        $hopesuccess = $this->Req('hopesuccessid', 0, 'int');
        $chancestageid = $this->Req('chancestageid', 0, 'int');

        $where = 'WHERE del=false';
        $order = '';

        if (!empty ($findname))
            $where .= ' AND (LOWER(name) like \'%' . strtolower($findname) . '%\') ';
        if (!empty ($hopesuccess))
            $where .= ' AND hopesuccessid = ' . $hopesuccess;
        if (!empty ($chancestageid))
            $where .= ' AND chancestageid = ' . $chancestageid;

        $v = View::Factory('PopChanceSelect');

        $pagesize = PAGE_SIZE_MINI;
        $count = ChanceCls::queryCount($where);
        $pos = HTML::PagePos($count, $this->Page(), $pagesize);

        $rs = ChanceCls::ChanceItemlist($where, $this->Order(), $pos, $pagesize);
        if ($rs && count($rs) > 0)
        {
            for ($i = 0; $i < count($rs); $i++)
            {
                $rs [$i] ['hopesuccess'] = MarketHopesCls::Instance()->GetName($rs[$i]['hopesuccessid']);
                $rs [$i] ['customer'] = SalesBiz::GetName($rs[$i]['customerid']);
                $rs [$i] ['chancestage'] = ChanceStagesCls::Instance()->GetName($rs [$i] ['chancestageid']);
                $rs [$i] ['state'] = CalendarStateCls::Instance()->GetStateName($rs [$i] ['stateid']);
                $rs [$i] ['creatorname'] = UserBiz::GetName($rs[$i]['creatorid']);
            }
        }
        $url = Url::QUERY_STRING_DELETE(array('pn'));

        $v->rs = $rs;

        $v->orders = HTML::Orders($url, array('username', 'name', 'serial', 'act', 'time'));
        $v->paged = HTML::PageGroup($count, $this->Page(), $url, $pagesize);

        $v->chancestageid = HTML::CtlSelKVList(ChanceStageCls::Instance()->Items(), 'chancestageid', '', '');
        $v->hopesuccessid = HTML::CtlSelKVList(MarketHopesCls::Instance()->Items(), 'hopesuccessid', '', '');

        echo $v->Render();

        $this->Footer();
    }

    //查询活动
    public function PopMarketSelect()
    {
        $this->Head();

        $findname = $this->Req('findname', '', 'str');
        $hopesuccess = $this->Req('hopesuccessid', 0, 'int');
        $markettypeid = $this->Req('markettypeid', 0, 'int');

        $where = 'WHERE del=false';
        $order = 'ORDER BY id ASC';

        if (!empty ($findname))
            $where .= ' AND (LOWER(name) like \'%' . strtolower($findname) . '%\') ';
        if (!empty ($hopesuccess))
            $where .= ' AND hopesuccess = ' . $hopesuccess;
        if (!empty ($markettypeid))
            $where .= ' AND markettypeid = ' . $markettypeid;

        $pagesize = PAGE_SIZE_MEDIUM;
        $count = MarketCls::Count($where);
        $pos = HTML::PagePos($count, $this->Page(), $pagesize);

        $url = Url::QUERY_STRING_DELETE(array('pn'));

        $v = View::factory('PopMarketSelect');

        $rs = MarketCls::queryList($where, $order, $pagesize, $pos);
        if ($rs && count($rs) > 0)
        {
            for ($i = 0; $i < count($rs); $i++)
            {
                $rs [$i] ['hopesuccessid'] = MarketHopesCls::Instance()->GetName($rs[$i]['hopesuccess']);
                $rs [$i] ['markettypeid'] = MarketTypeCls::Instance()->GetName($rs[$i]['markettypeid']);
                $rs [$i] ['state'] = CalendarStateCls::Instance()->GetStateName($rs [$i] ['stateid']);
                $rs [$i] ['creatorname'] = UserBiz::GetName($rs[$i]['creatorid']);
            }
        }

        $v->rs = $rs;

        $v->paged = HTML::PageGroup($count, $this->Page(), $url, $pagesize);

        $v->markettypeid = HTML::CtlSelKVList(MarketTypeCls::Instance()->Items(), 'markettypeid', '', '');
        $v->hopesuccessid = HTML::CtlSelKVList(MarketHopesCls::Instance()->Items(), 'hopesuccessid', '', '');

        echo $v->render();

        $this->Footer();
    }

    //查询线索
    public function PopClewSelect()
    {
        $this->Head();

        $findname = $this->Req('findname', '', 'str');
        $clewsourceid = $this->Req('clewsourceid', 0, 'int');
        $priorityid = $this->Req('priorityid', 0, 'int');

        $where = 'WHERE del=false';
        $order = 'ORDER BY id ASC';

        if (!empty ($findname))
            $where .= ' AND (LOWER(name) like \'%' . strtolower($findname) . '%\') ';
        if (!empty ($clewsourceid))
            $where .= ' AND clewsourceid = ' . $clewsourceid;
        if (!empty ($priorityid))
            $where .= ' AND priorityid = ' . $priorityid;

        $pagesize = PAGE_SIZE_MEDIUM;
        $count = ClewCls::queryCount($where);
        $pos = HTML::PagePos($count, $this->Page(), $pagesize);

        $url = Url::QUERY_STRING_DELETE(array('pn'));

        $v = View::factory('PopClewSelect');

        $rs = ClewCls::queryList($where, $order, $pagesize, $pos);
        if ($rs && count($rs) > 0)
        {
            for ($i = 0; $i < count($rs); $i++)
            {
                $rs [$i] ['customer'] = SalesBiz::GetName($rs[$i]['customerid']);
                $rs [$i] ['ownerid'] = UserBiz::GetName($rs[$i]['ownerid']);
                $rs [$i] ['clewsourceid'] = ClewSourceCls::Instance()->GetName($rs[$i]['clewsourceid']);
                $rs [$i] ['priorityid'] = ClewPriorityCls::Instance()->GetName($rs[$i]['priorityid']);
                $rs [$i] ['marketid'] = MarketCls::Instance()->GetName($rs[$i]['marketid']);
                $rs [$i] ['state'] = CalendarStateCls::Instance()->GetStateName($rs [$i] ['stateid']);
                $rs [$i] ['creatorname'] = UserBiz::GetName($rs[$i]['creatorid']);
            }
        }

        $v->rs = $rs;

        $v->paged = HTML::PageGroup($count, $this->Page(), $url, $pagesize);

        $v->clewsourceid = HTML::CtlSelKVList(ClewSourceCls::Instance()->Items(), 'clewsourceid', '', '');
        $v->priorityid = HTML::CtlSelKVList(ClewPriorityCls::Instance()->Items(), 'priorityid', '', '');

        echo $v->render();

        $this->Footer();
    }

    public function SignedList()
    {
        $userid = $this->Req('userid', 0, 'int');
        $date = $this->Req('date', '', 'str');

        $date = Date::Format($date, 'd', null);
        $name = UserInterface::GetUserName($userid)['msg'];

        $this->Header();

        $view = View::factory('SignedList');

        $ret = HrInterface::SignedUserDayItems($userid, $date);
        $count = $ret['msg'];
        $rs = $ret['data'];

        $view->rs = $rs;
        $view->loc = json_encode($rs);

        $view->userid = $userid;
        $view->name = $name;
        $view->date = $date;

        $view->tree = json_encode(UserInterface::GroupTree()['data']);

        echo $view->render();

        $this->Footer();
    }

    public function SignedReport()
    {
        $this->Header();

        $name = $this->Req('name', '', 'str');
        $stateid = $this->Req('stateid', 0, 'int');

        $where = '';

        if (!empty ($name))
            $where .= ' AND (LOWER(content) like \'%' . strtolower($name) . '%\') ';
        if ($stateid > 0)
            $where .= ' AND stateid=' . $stateid;

        $url = Url::QUERY_STRING_DELETE(array('pn'));
        $size = PAGE_SIZE_MEDIUM;
        $ret = HrInterface::SignedItems($this->Uid(), R::PROGRAMMETASK_CALENDARREPORT, $where, '', $this->Page(), $size, false);
        $count = $ret['msg'];
        $rs = $ret['data'];

        $view = View::factory('SignedReport');

        $view->rs = $rs;
        $view->paged = HTML::PageJump($count, $this->Page(), $size, $url);

        $view->name = $name;

        echo $view->render();

        $this->Footer();
    }

    public function WalkwayList()
    {
        $userid = $this->Req('userid', 0, 'int');
        $date = $this->Req('date', '', 'str');

        $date = Date::Format($date, 'd', null);
        $name = UserInterface::GetUserName($userid)['msg'];

        $this->Header();

        $view = View::factory('WalkwayList');

        $ret = HrInterface::WalkwayItem($userid, $date);
        $rs = $ret['data'];
        $locs = array();

        if (!empty($rs) && count($rs) > 1)
        {
            $paths = Util::Explode($rs['locs'], '|');
            if (!empty($paths) && count($paths) > 1)
            {
                foreach ($paths as $path)
                {
                    $p = Util::Explode($path, ',');
                    if (count($p) == 6)
                    {
                        $locs[] = array('id' => $p[0], 'code' => $p[1], 'lat' => $p[2], 'lng' => $p[3], 'radius' => $p[4], 'time' => $p[5], 'mode' => HrInterface::GetLocationModeName($p[1])['msg']);
                    }
                }
            }
        }

        $view->rs = $locs;
        $view->loc = json_encode($locs);

        $view->userid = $userid;
        $view->name = $name;
        $view->date = $date;

        $view->tree = json_encode(UserInterface::GroupTree()['data']);

        echo $view->render();

        $this->Footer();
    }
}

?>