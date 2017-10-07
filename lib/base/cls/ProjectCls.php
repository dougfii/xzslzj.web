<?php

class ProjectCls
{
    const TABLE = 't_project';
    private static $list = array();
    private static $instance = null;

    public static function Instance()
    {
        if (self::$instance == null) {
            self::$instance = new self ();
        }
        return self::$instance;
    }

    private function __construct()
    {
    }

    public function Item($id)
    {
        $this->LoadItem($id);
        if (array_key_exists($id, self::$list))
            return self::$list [$id];
        return array();
    }

    public function Gid($id)
    {
        $this->LoadItem($id);
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['gid'];
        return 0;
    }

    public function Name($id)
    {
        $this->LoadItem($id);
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['name'];
        return '';
    }

    public function Company($id)
    {
        $this->LoadItem($id);
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['company'];
        return '';
    }

    public function Act($id)
    {
        $this->LoadItem($id);
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['act'];
        return false;
    }

    public function NodeId($id, $nodeid)
    {
        $this->LoadItem($id);
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ["n{$nodeid}"];
        return 0;
    }

    public function Node($id, $nodeid)
    {
        $this->LoadItem($id);
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ["n{$nodeid}n"];
        return '';
    }

    public function StateId($id, $nodeid)
    {
        $this->LoadItem($id);
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ["s{$nodeid}"];
        return 0;
    }

    public function State($id, $nodeid)
    {
        $this->LoadItem($id);
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ["s{$nodeid}n"];
        return '';
    }

    private function LoadItem($id)
    {
        if (!array_key_exists($id, self::$list)) {
            $rs = self::Items("WHERE del=false AND id={$id}", '', 0, 1);
            if ($rs && count($rs) > 0)
                self::$list [$rs [0] ['id']] = $rs [0];
        }
    }

    public static function Count($where = '')
    {
        $rs = DB::db()->Fetch("
				SELECT COUNT(id) AS count
				FROM " . self::TABLE . "
				$where
				", array());
        if ($rs && count($rs) > 0 && $rs ['count'] > 0)
            return $rs ['count'];
        return 0;
    }

    public static function Items($where, $order, $start, $size)
    {
        $limit = DB::db()->Limit($start, $size);
        $rs = DB::db()->FetchAll("
				SELECT *
				FROM " . self::TABLE . "
				$where
				$order
				$limit
				");
        if ($rs && count($rs) > 0) {
            for ($i = 0; $i < count($rs); $i++) {
                $rs[$i]['group'] = GroupCls::Instance()->Name($rs[$i]['gid']);

                $rs[$i]['n0n'] = ProjectNodeCls::Name($rs[$i]['n0']);
                $rs[$i]['n1n'] = ProjectNodeCls::Name($rs[$i]['n1']);
                $rs[$i]['n2n'] = ProjectNodeCls::Name($rs[$i]['n2']);
                $rs[$i]['n3n'] = ProjectNodeCls::Name($rs[$i]['n3']);
                $rs[$i]['n4n'] = ProjectNodeCls::Name($rs[$i]['n4']);
                $rs[$i]['n5n'] = ProjectNodeCls::Name($rs[$i]['n5']);
                $rs[$i]['n51n'] = ProjectNodeCls::Name($rs[$i]['n51']);
                $rs[$i]['n52n'] = ProjectNodeCls::Name($rs[$i]['n52']);
                $rs[$i]['n6n'] = ProjectNodeCls::Name($rs[$i]['n6']);
                $rs[$i]['n61n'] = ProjectNodeCls::Name($rs[$i]['n61']);
                $rs[$i]['n62n'] = ProjectNodeCls::Name($rs[$i]['n62']);
                $rs[$i]['n63n'] = ProjectNodeCls::Name($rs[$i]['n63']);
                $rs[$i]['n64n'] = ProjectNodeCls::Name($rs[$i]['n64']);
                $rs[$i]['n7n'] = ProjectNodeCls::Name($rs[$i]['n7']);
                $rs[$i]['n71n'] = ProjectNodeCls::Name($rs[$i]['n71']);
                $rs[$i]['n72n'] = ProjectNodeCls::Name($rs[$i]['n72']);
                $rs[$i]['n73n'] = ProjectNodeCls::Name($rs[$i]['n73']);
                $rs[$i]['n74n'] = ProjectNodeCls::Name($rs[$i]['n74']);
                $rs[$i]['n75n'] = ProjectNodeCls::Name($rs[$i]['n75']);
                $rs[$i]['n76n'] = ProjectNodeCls::Name($rs[$i]['n76']);
                $rs[$i]['n8n'] = ProjectNodeCls::Name($rs[$i]['n8']);
                $rs[$i]['n9n'] = ProjectNodeCls::Name($rs[$i]['n9']);
                $rs[$i]['n91n'] = ProjectNodeCls::Name($rs[$i]['n91']);
                $rs[$i]['n92n'] = ProjectNodeCls::Name($rs[$i]['n92']);
                $rs[$i]['n921n'] = ProjectNodeCls::Name($rs[$i]['n921']);
                $rs[$i]['n922n'] = ProjectNodeCls::Name($rs[$i]['n922']);
                $rs[$i]['n923n'] = ProjectNodeCls::Name($rs[$i]['n923']);
                $rs[$i]['n924n'] = ProjectNodeCls::Name($rs[$i]['n924']);
                $rs[$i]['n925n'] = ProjectNodeCls::Name($rs[$i]['n925']);
                $rs[$i]['n10n'] = ProjectNodeCls::Name($rs[$i]['n10']);

                $rs[$i]['s0n'] = ProjectStateCls::Name($rs[$i]['s0']);
                $rs[$i]['s1n'] = ProjectStateCls::Name($rs[$i]['s1']);
                $rs[$i]['s2n'] = ProjectStateCls::Name($rs[$i]['s2']);
                $rs[$i]['s3n'] = ProjectStateCls::Name($rs[$i]['s3']);
                $rs[$i]['s4n'] = ProjectStateCls::Name($rs[$i]['s4']);
                $rs[$i]['s5n'] = ProjectStateCls::Name($rs[$i]['s5']);
                $rs[$i]['s51n'] = ProjectStateCls::Name($rs[$i]['s51']);
                $rs[$i]['s52n'] = ProjectStateCls::Name($rs[$i]['s52']);
                $rs[$i]['s6n'] = ProjectStateCls::Name($rs[$i]['s6']);
                $rs[$i]['s61n'] = ProjectStateCls::Name($rs[$i]['s61']);
                $rs[$i]['s62n'] = ProjectStateCls::Name($rs[$i]['s62']);
                $rs[$i]['s63n'] = ProjectStateCls::Name($rs[$i]['s63']);
                $rs[$i]['s64n'] = ProjectStateCls::Name($rs[$i]['s64']);
                $rs[$i]['s7n'] = ProjectStateCls::Name($rs[$i]['s7']);
                $rs[$i]['s71n'] = ProjectStateCls::Name($rs[$i]['s71']);
                $rs[$i]['s72n'] = ProjectStateCls::Name($rs[$i]['s72']);
                $rs[$i]['s73n'] = ProjectStateCls::Name($rs[$i]['s73']);
                $rs[$i]['s74n'] = ProjectStateCls::Name($rs[$i]['s74']);
                $rs[$i]['s75n'] = ProjectStateCls::Name($rs[$i]['s75']);
                $rs[$i]['s76n'] = ProjectStateCls::Name($rs[$i]['s76']);
                $rs[$i]['s8n'] = ProjectStateCls::Name($rs[$i]['s8']);
                $rs[$i]['s9n'] = ProjectStateCls::Name($rs[$i]['s9']);
                $rs[$i]['s91n'] = ProjectStateCls::Name($rs[$i]['s91']);
                $rs[$i]['s92n'] = ProjectStateCls::Name($rs[$i]['s92']);
                $rs[$i]['s921n'] = ProjectStateCls::Name($rs[$i]['s921']);
                $rs[$i]['s922n'] = ProjectStateCls::Name($rs[$i]['s922']);
                $rs[$i]['s923n'] = ProjectStateCls::Name($rs[$i]['s923']);
                $rs[$i]['s924n'] = ProjectStateCls::Name($rs[$i]['s924']);
                $rs[$i]['s925n'] = ProjectStateCls::Name($rs[$i]['s925']);
                $rs[$i]['s10n'] = ProjectStateCls::Name($rs[$i]['s10']);
            }
            return $rs;
        }

        return array();
    }

    public static function Results($where, $order, $page, $size)
    {
        $where = 'WHERE del=false ' . $where;
        $order = empty($order) ? 'ORDER BY id DESC' : $order;

        $count = self::Count($where);
        $page = HTML::PageNum($count, $page, $size);
        $start = HTML::PagePos($count, $page, $size);
        $rs = self::Items($where, $order, $start, $size);

        return array($count, $rs);
    }

    public static function Add($gid, $name, $company, $pass, $contacts, $mobile, $email)
    {
        $rs = DB::db()->Fetch("
                INSERT INTO " . self::TABLE . "(gid, name, company, pass, contacts, mobile, email)
				VALUES(?,?,?,md5(?),?,?,?)
				RETURNING id
				", array($gid, $name, $company, $pass, $contacts, $mobile, $email));
        return $rs ['id'];
    }

    public static function Edit($id, $tid, $uid, $pic, $title, $content, $html, $roll, $act, $last)
    {
        DB::db()->Query("
                UPDATE " . self::TABLE . "
                SET tid=?, uid=?, pic=?, title=?, content=?, html=?, roll=?, act=?, last=?
                WHERE id=?
                ", array($tid, $uid, $pic, $title, $content, $html, $roll, $act, $last, $id));
    }

    public static function Delete($id)
    {
        DB::db()->Query("
				UPDATE " . self::TABLE . "
				SET del=true
				WHERE id=?
				", array($id));
    }

    public static function EditPassword($id, $pass)
    {
        self::SetPassword($id, $pass);
    }

    public static function SetPassword($id, $pass)
    {
        DB::db()->Query("
				UPDATE " . self::TABLE . "
				SET pass=md5(?)
				WHERE id=?
				", array($pass, $id));
    }

    public static function SetInfo($id, $name, $company)
    {
        DB::db()->Query("
				UPDATE " . self::TABLE . "
				SET name=?, company=?
				WHERE id=?
				", array($name, $company, $id));
    }

    public static function SetAct($id)
    {
        DB::db()->Query("
				UPDATE " . self::TABLE . "
				SET act=NOT act
				WHERE id=?
				", array($id));
    }

    public static function SetNode($id, $node, $nodeid, $stateid)
    {
        DB::db()->Query("
				UPDATE " . self::TABLE . "
				SET n{$node}=?, s{$node}=?
				WHERE id=?
				", array($nodeid, $stateid, $id));
    }

    public static function SetLast($id)
    {
        DB::db()->Query("
				UPDATE " . self::TABLE . "
				SET last=CURRENT_TIMESTAMP
				WHERE id=?
				", array($id));
    }

    public static function ExistName($name, $selfid = null)
    {
        return $selfid ? self::Count("WHERE del=false AND LOWER(name)=LOWER('{$name}') AND id!={$selfid}") : self::Count("WHERE del=false AND LOWER(name)=LOWER('{$name}')");
    }

    public static function Login($name, $pass)
    {
        $rs = DB::db()->Fetch("
				SELECT *
				FROM " . self::TABLE . "
				WHERE LOWER(name)=LOWER(?) AND pass=? AND del=false
				LIMIT 1
				", array($name, md5($pass)));
        return $rs;
    }

    public static function Forget($name, $email)
    {
        $rs = DB::db()->Fetch("
				SELECT *
				FROM " . self::TABLE . "
				WHERE LOWER(name)=LOWER(?) AND LOWER(email)=LOWER(?) AND del=false
				LIMIT 1
				", array($name, $email));
        return $rs;
    }

    //主管单位名称
    public static function GetGroupCompany($mid)
    {
        return GroupCls::Instance()->Company(self::Instance()->Gid($mid));
    }
}

?>