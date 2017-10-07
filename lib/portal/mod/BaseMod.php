<?php

abstract class BaseMod implements IMod
{
    private $_uid = null;
    private $_uname = '';
    private $_ulogin = false;
    private $_mid = null;
    private $_mname = '';
    private $_mlogin = false;
    private $_page = 1;

    public function __construct()
    {
        $this->_page = $this->Req('page', 1, 'int');

        if (isset ($_SESSION ['uid']) && !empty ($_SESSION ['uid']) && isset ($_SESSION ['uname']) && !empty ($_SESSION ['uname'])) {
            $this->_uid = $_SESSION ['uid'];
            $this->_uname = $_SESSION ['uname'];
            $this->_ulogin = true;
        } else {
            $this->_uid = null;
            $this->_uname = '';
            $this->_ulogin = false;
        }

        if (isset ($_SESSION ['mid']) && !empty ($_SESSION ['mid']) && isset ($_SESSION ['mname']) && !empty ($_SESSION ['mname'])) {
            $this->_mid = $_SESSION ['mid'];
            $this->_mname = $_SESSION ['mname'];
            $this->_mlogin = true;
        } else {
            $this->_mid = null;
            $this->_mname = '';
            $this->_mlogin = false;
        }
    }

    protected function UserAuth()
    {
        if (!$this->UserLogin())
            Url::RedirectUrl('/');
    }

    protected function MemberAuth()
    {
        if (!$this->MemberLogin())
            Url::RedirectUrl('/');
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

    protected function Head($title = '', $keywords = '', $description = '')
    {
        $view = View::Factory('Head');
        $view->title = $title;
        $view->keywords = $keywords;
        $view->description = $description;
        echo $view->Render();
    }

    protected function Foot()
    {
        $view = View::Factory('Foot');
        echo $view->Render();
    }

    protected function Header($title = '', $keywords = '', $description = '')
    {
        $this->Head($title, $keywords, $description);

        $view = View::Factory('Header');
        $view->advertiser = AdvertiserArchiveBiz::Get()['msg'];
        $view->nav = $this->articles_nav();
        echo $view->Render();
    }

    protected function Footer()
    {
        $view = View::Factory('Footer');
        $view->footer = CopyrightArchiveBiz::Get()['msg'];
        echo $view->Render();

        $this->Foot();
    }

    protected function UserHead()
    {
        $view = View::Factory('UserHead');
        echo $view->Render();
    }

    protected function UserFoot()
    {
        $view = View::Factory('UserFoot');
        echo $view->Render();
    }

    protected function UserHeader()
    {
        $this->UserHead();

        $view = View::Factory('UserHeader');
        $view->menu = $this->UserMenu()[0];
        $view->side = $this->UserMenu()[1];
        $view->name = $this->UserName();
        echo $view->Render();
    }

    protected function UserFooter()
    {
        $view = View::Factory('UserFooter');
        echo $view->Render();

        $this->UserFoot();
    }

    protected function MemberHead()
    {
        $view = View::Factory('MemberHead');
        echo $view->Render();
    }

    protected function MemberFoot()
    {
        $view = View::Factory('MemberFoot');
        echo $view->Render();
    }

    protected function MemberHeader()
    {
        $this->MemberHead();

        $view = View::Factory('MemberHeader');
        $view->menu = $this->MemberMenu()[0];
        $view->side = $this->MemberMenu()[1];
        $view->name = $this->MemberName();
        echo $view->Render();
    }

    protected function MemberFooter()
    {
        $view = View::Factory('MemberFooter');
        echo $view->Render();

        $this->MemberFoot();
    }

    private function UserMenu()
    {
        $mod = 'User';
        $act = $this->Req('a', '', 'str');
        $menus = array(//
            //首页
            array('Main', '首页', R::MENU_CENTER, null),//
            //工作流
            array('Project', '工程管理', R::MENU_PROJECT, array(//
                array('ProjectList', '工程列表', R::PROJECT_OTHER),//
                array('QuotationRebutS', '项目列表', R::PROJECT_OTHER),//
                array('QuotationRebutList', '项目列表', R::PROJECT_OTHER),//
                null,//
                array('ContractList', '项目成员', R::PROJECT_MEMBER),//
                array('ContractRebutS', '项目成员', R::PROJECT_MEMBER),//
                array('ContractRebutList', '项目成员', R::PROJECT_MEMBER),//
            )),//
            //内容
            array('Article', '网站管理', R::MENU_WEBSITE, array(//
                array('ArticleList', '文章列表', R::WEBSITE_ARTICLE_LIST),//
                array('ArticleTypeList', '文章分类', R::WEBSITE_ARTICLE_TYPE),//
                null,//
                array('ArticleAdvertiser', '页首切图', R::WEBSITE_ADVERTISER),//
                array('ArticleLinker', '友情链接', R::WEBSITE_LINKER),//
                array('ArticleCopyright', '页尾版权', R::WEBSITE_COPYRIGHT),//
            )),//
            //系统
            array('System', '系统管理', R::MENU_SYSTEM, array(//
                array('SystemOrgList', '部门列表', R::SYSTEM_USER),//
                array('SystemGroupList', '组织列表', R::SYSTEM_USER),//
                array('SystemApproveList', '审批流程', R::SYSTEM_USER),//
                null,//
                array('SystemLogLogin', '登录日志', R::SYSTEM_LOG),//
                array('SystemLogEvent', '事件日志', R::SYSTEM_LOG),//
                array('SystemCacheClean', '清理缓冲区', R::SYSTEM_CACHE),//
            )),//
        );

        $sides = null;
        $menu = '<span class="menu"><ul>';
        foreach ($menus as $v) {
            if (P::Instance($this->Uid())->isCanView($v [2])) {
                $menu .= '<li><a href="?m=' . $mod . '&a=' . $v [0] . '">' . $v [1] . '</a></li>';

                if (stripos($act, $v[0]) === 0) {
                    $sides = isset ($v [3]) && !empty($v[3]) ? $v [3] : null;
                }
            }
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

    private function articles_nav()
    {
        $rs = ArticleTypeCls::GetNextIds(0, false, true, true);
        $s = '<ul>';
        $s .= '<li><a href="/">首页</a></li>';
        if (!empty($rs)) {
            foreach ($rs as $k => $v) {
                $s .= '<li><a href="?a=articles&id=' . $v . '">' . ArticleTypeCls::Instance()->Name($v) . '</a></li>';
            }
        }
        $s .= '<li><a href="/?a=feedback">质量投诉</a></li>';
        $s .= '<li><a href="/?a=contacts">联系我们</a></li>';
        $s .= '</ul>';
        return $s;
    }

    private function MemberMenu()
    {
        $menu = '';
        $side = '<div class="side"><ul><li><a href="?m=Project&a=Main">消息通知</a></li><li><a href="?m=Project&a=Progress">工程进度</a></li></ul></div>';
        return array($menu, $side);
    }

    protected function UserName()
    {
        return $this->_uname;
    }

    protected function Uid()
    {
        return $this->_uid;
    }

    protected function UserLogin()
    {
        return $this->_ulogin;
    }

    protected function MemberName()
    {
        return $this->_mname;
    }

    protected function Mid()
    {
        return $this->_mid;
    }

    protected function MemberLogin()
    {
        return $this->_mlogin;
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

        if (empty ($ok)) return 'ORDER BY id DESC';
        return 'ORDER BY ' . $ok . (empty ($ov) ? ' DESC' : ' ASC');
    }
}