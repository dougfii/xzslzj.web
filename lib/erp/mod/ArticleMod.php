<?php

class ArticleMod extends BaseMod
{
    public function index()
    {
        $this->ArticleList();
    }

    public function ArticleList()
    {
        $tid = $this->Req('tid', 0, 'int');
        $fname = $this->Req('fname', '', 'str');

        $where = ' AND tid IN (' . Common::GetWhereInIdSql(ArticleTypeBiz::GetChildrenIds($tid, true, null)['data']) . ')';

        if (!empty ($fname)) $where .= ' AND ((LOWER(title) like \'%' . strtolower($fname) . '%\') OR (LOWER(content) like \'%' . strtolower($fname) . '%\')) ';

        $url = Url::QUERY_STRING_DELETE(array('page', 'fname'));

        $size = PAGE_SIZE_MEDIUM;
        $ret = ArticleBiz::Items($where, '', $this->Page(), $size);
        $count = $ret['msg'];
        $rs = $ret['data'];

        $this->Header();

        $view = View::Factory('ArticleList');

        $view->rs = $rs;
        $view->paged = HTML::PageJump($count, $this->Page(), $size, $url);

        $view->tid = $tid;
        $view->type = ArticleTypeBiz::Name($tid)['msg'];
        $view->path = $this->GetArticlePath($tid);
        $view->tree = json_encode(ArticleTypeBiz::Tree(null)['data']);

        $view->url = $url;
        $view->fname = $fname;

        echo $view->Render();

        $this->Footer();
    }

    public function ArticleAdd()
    {
        $tid = $this->Req('tid', 0, 'int');

        $this->Header();

        $view = View::Factory('ArticleAdd');

        $view->tid = $tid;
        $view->type = ArticleTypeBiz::Name($tid)['msg'];
        $view->tree = json_encode(ArticleTypeBiz::Tree(null)['data']);
        $view->tree1 = json_encode(ArticleTypeBiz::Tree(null)['data']);

        $view->time = Date::Datetime();

        echo $view->Render();

        $this->Footer();
    }

    public function ArticleEdit()
    {
        $id = $this->Req('id', 0, 'int');

        $this->Header();

        $view = View::Factory('ArticleEdit');

        $view->rs = ArticleBiz::Item($id)['data'];
        $view->tree = json_encode(ArticleTypeBiz::Tree(null)['data']);
        $view->tree1 = json_encode(ArticleTypeBiz::Tree(null)['data']);

        echo $view->Render();

        $this->Footer();
    }

    public function OnArticleAdd()
    {
        $tid = $this->Req('tid', 0, 'int');
        $pic = $this->Req('pic', '', 'str');
        $time = $this->Req('time', '', 'str');
        $title = $this->Req('title', '', 'str');
        $content = $this->Req('content', '', 'str');
        $html = $this->Req('html', '', 'str');
        $available = $this->Req('available', 0, 'int');
        $roll = $this->Req('roll', 0, 'int');
        $act = $this->Req('act', 1, 'int');

        $uid = $this->Uid();

        echo Json::Out(ArticleBiz::Add($tid, $uid, $pic, $title, $content, $html, $available, $roll, $act, $time));
    }

    public function OnArticleEdit()
    {
        $id = $this->Req('id', 0, 'int');
        $tid = $this->Req('tid', 0, 'int');
        $pic = $this->Req('pic', '', 'str');
        $time = $this->Req('time', '', 'str');
        $title = $this->Req('title', '', 'str');
        $content = $this->Req('content', '', 'str');
        $html = $this->Req('html', '', 'str');
        $available = $this->Req('available', 0, 'int');
        $roll = $this->Req('roll', 0, 'int');
        $act = $this->Req('act', 1, 'int');

        $uid = $this->Uid();

        echo Json::Out(ArticleBiz::Edit($id, $tid, $uid, $pic, $title, $content, $html, $available, $roll, $act, $time));
    }

    public function OnArticleDelete()
    {
        $id = $this->Req('id', 0, 'int');

        echo Json::Out(ArticleBiz::Delete($id));
    }

    public function ArticleTypeList()
    {
        $id = $this->Req('id', 0, 'int');

        $this->Header();

        $view = View::Factory('ArticleTypeList');

        $view->rs = ArticleTypeBiz::GetNextList($id, false, null)['data'];
        $view->id = $id;
        $view->path = $this->GetArticleTypePath($id);
        $view->tree = json_encode(ArticleTypeBiz::Tree(null)['data']);

        echo $view->Render();

        $this->Footer();
    }

    public function ArticleTypeAdd()
    {
        $id = $this->Req('id', 0, 'int');

        $this->Header();

        $view = View::Factory('ArticleTypeAdd');

        $view->pid = $id;
        $view->parent = ArticleTypeBiz::Name($id)['msg'];
        $view->tree = json_encode(ArticleTypeBiz::Tree(null)['data']);
        $view->tree1 = json_encode(ArticleTypeBiz::Tree(null)['data']);

        echo $view->Render();

        $this->Footer();
    }

    public function ArticleTypeEdit()
    {
        $id = $this->Req('id', 0, 'int');

        $this->Header();

        $view = View::Factory('ArticleTypeEdit');

        $pid = ArticleTypeBiz::Pid($id)['msg'];
        $view->pid = $pid;
        $view->parent = ArticleTypeBiz::Name($pid)['msg'];
        $view->rs = ArticleTypeBiz::Item($id)['data'];
        $view->tree = json_encode(ArticleTypeBiz::Tree(null)['data']);
        $view->tree1 = json_encode(ArticleTypeBiz::Tree(null)['data']);

        echo $view->Render();

        $this->Footer();
    }

    public function OnArticleTypeAdd()
    {
        $pid = $this->Req('pid', 0, 'int');
        $name = $this->Req('name', '', 'str');
        $dir = $this->Req('dir', 0, 'int');
        $act = $this->Req('act', 1, 'int');

        echo Json::Out(ArticleTypeBiz::Add($pid, $name, $act));
    }

    public function OnArticleTypeEdit()
    {
        $id = $this->Req('id', 0, 'int');
        $pid = $this->Req('pid', 0, 'int');
        $name = $this->Req('name', '', 'str');
        $act = $this->Req('act', 1, 'int');


        echo Json::Out(ArticleTypeBiz::Edit($id, $pid, $name, $act));
    }

    public function OnArticleTypeDelete()
    {
        $id = $this->Req('id', 0, 'int');

        echo Json::Out(ArticleTypeBiz::Delete($id));
    }

    public function FeedbackList()
    {
        $fname = $this->Req('fname', '', 'str');

        $where = '';
        if (!empty ($fname)) $where .= ' AND ((LOWER(project) like \'%' . strtolower($fname) . '%\') OR (LOWER(contacts) like \'%' . strtolower($fname) . '%\') OR (LOWER(content) like \'%' . strtolower($fname) . '%\')) ';

        $url = Url::QUERY_STRING_DELETE(array('page', 'fname'));

        $size = PAGE_SIZE_MEDIUM;
        list($count, $rs) = FeedbackCls::Results($where, '', $this->Page(), $size);

        $this->Header();

        $view = View::Factory('FeedbackList');

        $view->rs = $rs;
        $view->paged = HTML::PageJump($count, $this->Page(), $size, $url);

        $view->url = $url;
        $view->fname = $fname;

        echo $view->Render();

        $this->Footer();
    }

    public function FeedbackEdit()
    {
        $id = $this->Req('id', 0, 'int');

        $this->Header();

        $view = View::Factory('FeedbackEdit');

        $view->rs = FeedbackCls::Instance()->Item($id);

        echo $view->Render();

        $this->Footer();
    }

    public function OnFeedbackEdit()
    {
        $id = $this->Req('id', 0, 'int');
        $reply = $this->Req('reply', '', 'str');
        $act = $this->Req('act', 0, 'int');

        if ($id <= 0) Json::ReturnError(ALERT_ERROR);
        if (empty($reply)) Json::ReturnError('请输入答复内容');

        $act = Util::Bool($act);

        try {
            FeedbackCls::Edit($id, $reply, $act, $this->Uid());
        } catch (Exception $e) {
            Json::ReturnError(ALERT_ERROR);
        }

        Json::ReturnSuccess();
    }

    public function OnFeedbackDelete()
    {
        $id = $this->Req('id', 0, 'int');

        FeedbackCls::Delete($id);

        Json::ReturnSuccess();
    }

    public function ArticleAdvertiser()
    {
        $this->Header();

        $view = View::Factory('ArticleAdvertiser');

        $rs = AdvertiserArchiveBiz::Get()['msg'];
        if ($rs === false) $rs = array();
        $view->rs = $rs;

        echo $view->Render();

        $this->Footer();
    }

    public function ArticleAdvertiserEdit()
    {
        $id = $this->Req('id', 0, 'int');

        $this->Header();

        $view = View::Factory('ArticleAdvertiserEdit');

        $rs = AdvertiserArchiveBiz::Get()['msg'];

        if ($rs === false || !array_key_exists($id, $rs)) $rs = array();
        else $rs = $rs[$id];

        $view->rs = $rs;
        $view->id = $id;

        echo $view->Render();

        $this->Footer();
    }

    public function OnArticleAdvertiserAdd()
    {
        $pic = $this->Req('pic', '', 'str');
        $sort = $this->Req('sort', 1, 'int');

        if (empty($pic)) Json::ReturnError('图片地址无效，请重新上传图片');

        $rs = AdvertiserArchiveBiz::Get()['msg'];
        if ($rs === false) $rs = array();
        $rs[] = array('pic' => $pic, 'sort' => $sort);

        //sort
        $keys = array();
        if (!empty($rs)) {
            foreach ($rs as $k => $v) {
                $keys[$k] = $v['sort'];
            }
            array_multisort($keys, SORT_ASC, $rs);
        }

        AdvertiserArchiveBiz::Set($rs);

        Json::ReturnSuccess();
    }

    public function OnArticleAdvertiserEdit()
    {
        $id = $this->Req('id', -1, 'int');
        $sort = $this->Req('sort', 1, 'int');

        if ($id < 0) Json::ReturnError(ALERT_ERROR);

        $rs = AdvertiserArchiveBiz::Get()['msg'];
        if ($rs === false) $rs = array();

        if (!empty($rs)) {
            foreach ($rs as $k => $v) {
                if ($k == $id) {
                    $rs[$k]['sort'] = $sort;
                }
            }
        }

        //sort
        $keys = array();
        if (!empty($rs)) {
            foreach ($rs as $k => $v) {
                $keys[$k] = $v['sort'];
            }
            array_multisort($keys, SORT_ASC, $rs);
        }

        AdvertiserArchiveBiz::Set($rs);

        Json::ReturnSuccess();
    }

    public function OnArticleAdvertiserDelete()
    {
        $id = $this->Req('id', 0, 'int');

        $rs = AdvertiserArchiveBiz::Get()['msg'];
        if ($rs === false) $rs = array();
        if (array_key_exists($id, $rs)) unset($rs[$id]);

        //sort
        $keys = array();
        if (!empty($rs)) {
            foreach ($rs as $k => $v) {
                $keys[$k] = $v['sort'];
            }
            array_multisort($keys, SORT_ASC, $rs);
        }

        AdvertiserArchiveBiz::Set($rs);

        Json::ReturnSuccess();
    }

    public function ArticleLinker()
    {
        $this->Header();

        $view = View::Factory('ArticleLinker');

        $rs = LinkerArchiveBiz::Get()['msg'];
        if ($rs === false) $rs = array();
        $view->rs = $rs;

        echo $view->Render();

        $this->Footer();
    }

    public function ArticleLinkerEdit()
    {
        $id = $this->Req('id', 0, 'int');

        $this->Header();

        $view = View::Factory('ArticleLinkerEdit');

        $rs = LinkerArchiveBiz::Get()['msg'];

        if ($rs === false || !array_key_exists($id, $rs)) $rs = array();
        else $rs = $rs[$id];

        $view->rs = $rs;
        $view->id = $id;

        echo $view->Render();

        $this->Footer();
    }

    public function OnArticleLinkerAdd()
    {
        $pic = $this->Req('pic', '', 'str');
        $name = $this->Req('name', '', 'str');
        $url = $this->Req('url', '', 'str');
        $sort = $this->Req('sort', 1, 'int');

        if (empty($pic)) Json::ReturnError('图标地址无效，请重新上传图标');
        if (empty($name)) Json::ReturnError('请输入站名');
        if (!Util::IsURL($url)) Json::ReturnError('请输入有效的网址');

        $rs = LinkerArchiveBiz::Get()['msg'];
        if ($rs === false) $rs = array();
        $rs[] = array('pic' => $pic, 'name' => $name, 'url' => $url, 'sort' => $sort);

        //sort
        $keys = array();
        if (!empty($rs)) {
            foreach ($rs as $k => $v) {
                $keys[$k] = $v['sort'];
            }
            array_multisort($keys, SORT_ASC, $rs);
        }

        LinkerArchiveBiz::Set($rs);

        Json::ReturnSuccess();
    }

    public function OnArticleLinkerEdit()
    {
        $id = $this->Req('id', -1, 'int');
        $name = $this->Req('name', '', 'str');
        $url = $this->Req('url', '', 'str');
        $sort = $this->Req('sort', 1, 'int');

        if ($id < 0) Json::ReturnError(ALERT_ERROR);
        if (empty($name)) Json::ReturnError('请输入站名');
        if (!Util::IsURL($url)) Json::ReturnError('请输入有效的网址');

        $rs = LinkerArchiveBiz::Get()['msg'];
        if ($rs === false) $rs = array();

        if (!empty($rs)) {
            foreach ($rs as $k => $v) {
                if ($k == $id) {
                    $rs[$k]['name'] = $name;
                    $rs[$k]['url'] = $url;
                    $rs[$k]['sort'] = $sort;
                }
            }
        }

        //sort
        $keys = array();
        if (!empty($rs)) {
            foreach ($rs as $k => $v) {
                $keys[$k] = $v['sort'];
            }
            array_multisort($keys, SORT_ASC, $rs);
        }

        LinkerArchiveBiz::Set($rs);

        Json::ReturnSuccess();
    }

    public function OnArticleLinkerDelete()
    {
        $id = $this->Req('id', 0, 'int');

        $rs = LinkerArchiveBiz::Get()['msg'];
        if ($rs === false) $rs = array();
        if (array_key_exists($id, $rs)) unset($rs[$id]);

        //sort
        $keys = array();
        if (!empty($rs)) {
            foreach ($rs as $k => $v) {
                $keys[$k] = $v['sort'];
            }
            array_multisort($keys, SORT_ASC, $rs);
        }

        LinkerArchiveBiz::Set($rs);

        Json::ReturnSuccess();
    }

    public function ArticleCopyright()
    {
        $this->Header();

        $view = View::Factory('ArticleCopyright');

        $view->content = CopyrightArchiveBiz::Get()['msg'];

        echo $view->Render();

        $this->Footer();
    }

    public function OnArticleCopyright()
    {
        $content = $this->Req('content', '', 'str');

        CopyrightArchiveBiz::Set($content);

        echo Json::ReturnSuccess();
    }

    private function GetArticlePath($id)
    {
        $url = '?m=Article&a=ArticleList&tid=';
        $str = '<div class="path"><a href="' . $url . '">文章分类</a>';

        if ($id > 0) {
            $rs = ArticleTypeBiz::GetParentList($id)['data'];
            foreach ($rs as $k => $v) {
                $str .= ' &gt; <a href="' . $url . $k . '">' . $v . '</a>';
            }
        }

        $str .= '</div>';
        return $str;
    }

    private function GetArticleTypePath($id)
    {
        $url = '?m=Article&a=ArticleTypeList&id=';
        $str = '<div class="path"><a href="' . $url . '">文章分类</a>';

        if ($id > 0) {
            $rs = ArticleTypeBiz::GetParentList($id)['data'];
            foreach ($rs as $k => $v) {
                $str .= ' &gt; <a href="' . $url . $k . '">' . $v . '</a>';
            }
        }

        $str .= '</div>';
        return $str;
    }
}

?>