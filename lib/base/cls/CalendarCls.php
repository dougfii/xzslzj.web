<?php

//momo
class CalendarCls
{
    const TABLE = 't_calendar';
    private static $list = array();
    private static $instance = null;

    public static function Instance()
    {
        if (self::$instance == null)
        {
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

    public function StateId($id)
    {
        $this->LoadItem($id);
        if (array_key_exists($id, self::$list))
            return self::$list [$id]['stateid'];
        return 0;
    }

    public function Uid($id)
    {
        $this->LoadItem($id);
        if (array_key_exists($id, self::$list))
            return self::$list [$id] ['uid'];
        return 0;
    }

    public function IsEdit($id, $uid)
    {
        $stateid = $this->StateId($id);
        $userid = $this->Uid($id);

        if ($userid > 0 && $userid == $uid && $stateid > 0 && CalendarStateCls::IsEdit($stateid))
            return true;
        return false;
    }

    public function IsDelete($id, $uid)
    {
        $stateid = $this->StateId($id);
        $userid = $this->Uid($id);

        if ($userid > 0 && $userid == $uid && $stateid > 0 && CalendarStateCls::IsEdit($stateid))
            return true;
        return false;
    }

    private function LoadItem($id)
    {
        if (!array_key_exists($id, self::$list))
        {
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
        if ($rs && count($rs) > 0)
        {
            for ($i = 0; $i < count($rs); $i++)
            {
                $rs[$i]['state'] = CalendarStateCls::Name($rs[$i]['stateid']);
                $rs[$i]['user'] = UserCls::Instance()->Name($rs[$i]['uid']);
            }
            return $rs;
        }

        return array();
    }


    public static function Add($date, $stateid, $customer, $contacts, $content, $plan, $walkway, $distance, $memo, $uid)
    {
        $rs = DB::db()->Fetch("
                INSERT INTO " . self::TABLE . "(date, stateid, customer, contacts, content, plan, walkway, distance, memo, uid)
				VALUES(?,?,?,?,?,?,?,?,?,?)
				RETURNING id
				", array($date, $stateid, $customer, $contacts, $content, $plan, $walkway, $distance, $memo, $uid));
        return $rs ['id'];
    }

    public static function Edit($id, $date, $stateid, $customer, $contacts, $content, $plan, $walkway, $distance, $memo)
    {
        DB::db()->Query("
                UPDATE " . self::TABLE . "
                SET date=?, stateid=?, customer=?, contacts=?, content=?, plan=?, walkway=?, distance=?, memo=?
                WHERE id=?
                ", array($date, $stateid, $customer, $contacts, $content, $plan, $walkway, $distance, $memo, $id));
    }

    public static function Delete($id)
    {
        DB::db()->Query("
				UPDATE " . self::TABLE . "
				SET del=true
				WHERE id=?
				", array($id));
    }
}

?>