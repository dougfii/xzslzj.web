<?php

abstract class BaseMod implements IMod
{
    private $_uid = null;
    private $_username = '';
    private $_login = false;
    private $_page = 1;

    public function __construct()
    {
        $this->Init();
    }

    protected function Init()
    {
        $this->_page = $this->Req('page', 1, 'int');

        if (isset ($_SESSION ['uid']) && !empty ($_SESSION ['uid']) && isset ($_SESSION ['name']) && !empty ($_SESSION ['name'])) {
            $this->_uid = $_SESSION ['uid'];
            $this->_username = $_SESSION ['name'];
            $this->_login = true;
        } else {
            $this->_uid = null;
            $this->_username = '';
            $this->_login = false;
        }

        if (!$this->IsLogin())
            Url::RedirectUrl('/');
    }

    protected function Head()
    {
        $view = View::Factory('Head');
        echo $view->Render();
    }

    protected function Foot()
    {
        $v = View::Factory('Foot');
        echo $v->Render();
    }

    protected function Header()
    {
        $this->Head();

        $view = View::Factory('Header');
        $view->menu = $this->Menu()[0];
        $view->side = $this->Menu()[1];
        $view->username = $this->Username();
        echo $view->Render();
    }

    protected function Footer()
    {
        $view = View::Factory('Footer');
        echo $view->Render();

        $this->Foot();
    }

    protected function HeadPop()
    {
        $this->Head();

        $view = View::Factory('HeadPop');
        echo $view->Render();
    }

    protected function FootPop()
    {
        $view = View::Factory('FootPop');
        echo $view->Render();

        $this->Foot();
    }

    protected function HeadPrint()
    {
        $view = View::Factory('HeadPrint');
        echo $view->Render();
    }

    protected function FootPrint()
    {
        $view = View::Factory('FootPrint');
        echo $view->Render();
    }

    private function Menu()
    {
        $projects = array();
        $projects[] = array("ProjectList", "全部工程", R::PROJECT_OTHER);
        for ($i = Date::Year(); $i >= YEAR_START; $i--) {
            $projects[] = array("ProjectList&year={$i}", "工程 ({$i})", R::PROJECT_OTHER);
        }

        $mod = $this->Req('m', '', 'str');
        $menus = array(//
            //首页
            array('Main', '首页', R::MENU_CENTER, null),//
            //CRM销售部分（日程、活动、任务、商机）
            array('Project', '工程管理', R::MENU_PROJECT, $projects),//
            //销售
            array('Article', '网站管理', R::MENU_WEBSITE, array(//
                array('ArticleList', '文章列表', R::WEBSITE_ARTICLE_LIST),//
                array('ArticleTypeList', '文章分类', R::WEBSITE_ARTICLE_TYPE),//
                null,//
                array('FeedbackList', '质量投诉', R::WEBSITE_ADVERTISER),//
                null,//
                array('ArticleAdvertiser', '页首切图', R::WEBSITE_ADVERTISER),//
                array('ArticleLinker', '友情链接', R::WEBSITE_LINKER),//
                array('ArticleCopyright', '页尾版权', R::WEBSITE_COPYRIGHT),//
            )),//
            //系统
            array('System', '系统', R::MENU_SYSTEM, array(//
                array('GroupList', '组织机构', R::SYSTEM_USER),//
                array('RoleList', '角色管理', R::SYSTEM_USER),//
                array('ApproveList', '审批流程', R::SYSTEM_USER),//
                null,//
                array('FacadeTypeList', '外观评测项目', R::SYSTEM_USER),//
                null,//
                array('LogLogin', '登录日志', R::SYSTEM_LOG),//
                array('LogEvent', '事件日志', R::SYSTEM_LOG),//
                array('CacheClean', '清理缓冲区', R::SYSTEM_LOG),//
            )),//
        );

        $sides = null;
        $menu = '<span class="menu"><ul>';
        foreach ($menus as $v) {
            if (P::Instance($this->Uid())->isCanView($v [2])) {
                $menu .= '<li><a href="?m=' . $v [0] . '">' . $v [1] . '</a></li>';
            }

            if ($mod == $v[0])
                $sides = isset ($v [3]) && !empty($v[3]) ? $v [3] : null;
        }
        $menu .= '</ul></span>';

        $side = '';
        if (!empty($sides)) {
            $side .= '<div class="side"><ul>';
            foreach ($sides as $v) {
                if (P::Instance($this->Uid())->isCanView($v [2])) {
                    $side .= '<li><a href="?m=' . $mod . '&a=' . $v [0] . '">' . $v [1] . '</a></li>';
                } else {
                    $side .= '<li><a class="disable">' . $v [1] . '</a></li>';
                }
            }
            $side .= '</ul></div>';
        }

        return array($menu, $side);
    }

    protected function Username()
    {
        return $this->_username;
    }

    protected function Uid()
    {
        return $this->_uid;
    }

    protected function IsLogin()
    {
        return $this->_login;
    }

    protected function IsAdmin()
    {
        return UserCls::Instance()->Admin($this->Uid());
    }

    protected function Page()
    {
        return $this->_page;
    }

    protected function Req($name, $def = null, $type = null)
    {
        return Url::Req($name, $def, $type);
    }

    protected function Order()
    {
        $ok = Url::Req('ok', '', 'str');
        $ov = Url::Req('ov', 0, 'int');

        if (empty ($ok))
            return 'ORDER BY id DESC';
        return 'ORDER BY ' . $ok . (empty ($ov) ? ' DESC' : ' ASC');
    }
}

?>