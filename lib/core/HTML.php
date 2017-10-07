<?php

class HTML
{
    // select control
    public static function Select($rs, $name, $cls = '', $def = null, $prefix = null, $prefixkey = 0, $event = '')
    {
        if (!empty ($cls))
            $cls = " class='$cls'";
        $s = "<select id='$name' name='$name' $cls $event>";
        if ($prefix) {
            $s .= "<option value='$prefixkey'";
            if ($def == $prefixkey)
                $s .= ' selected';
            if ($prefix === true)
                $s .= ">请选择 ......</option>";
            else $s .= ">" . $prefix . "</option>";
        }
        foreach ($rs as $k => $v) {
            $s .= '<option value="' . $k . '"';
            if ($def == $k)
                $s .= ' selected';
            $s .= '>' . $v . '</option>';
        }
        $s .= "</select>";
        return $s;
    }

    public static function SelectNumeric($name, $first = 1, $last = 100, $step = 1, $cls = '', $def = null, $prefix = null, $prefixkey = 0, $suffixval = '', $event = '')
    {
        $asc = $first < $last;

        if (!empty ($cls))
            $cls = " class='$cls'";
        $s = "<select id='$name' name='$name' $cls $event>";
        if ($prefix) {
            $s .= "<option value='$prefixkey'";
            if ($def == $prefixkey)
                $s .= ' selected';
            if ($prefix === true)
                $s .= ">请选择 ......</option>";
            else $s .= ">" . $prefix . "</option>";
        }

        for ($i = $first; $i <= $last;) {
            $s .= '<option value="' . $i . '"';
            if ($def == $i)
                $s .= ' selected';
            $s .= '>' . $i . $suffixval . '</option>';
            $i += $step;
        }
        $s .= "</select>";
        return $s;
    }

    public static function SelectData($rs, $name, $cls = '', $def = null, $prefix = null, $prefixkey = 0, $event = '')
    {
        if (!empty ($cls))
            $cls = " class='$cls'";
        $s = "<select id='$name' name='$name' $cls $event>";
        if ($prefix) {
            $s .= "<option value='$prefixkey'";
            if ($def == $prefixkey)
                $s .= ' selected';
            if ($prefix === true)
                $s .= ">请选择 ......</option>";
            else $s .= ">" . $prefix . "</option>";
        }
        foreach ($rs as $k => $v) {
            $s .= '<option value="' . $k . '"';
            if ($def == $k)
                $s .= ' selected';
            $s .= '>' . $v ['name'] . '</option>';
        }
        $s .= "</select>";
        return $s;
    }

    public static function SelectDataIdName($rs, $name, $cls = '', $def = null, $prefix = null, $prefixkey = 0, $event = '')
    {
        if (!empty ($cls))
            $cls = " class='$cls'";
        $s = "<select id='$name' name='$name' $cls $event>";
        if ($prefix) {
            $s .= "<option value='$prefixkey'";
            if ($def == $prefixkey)
                $s .= ' selected';
            if ($prefix === true)
                $s .= ">请选择 ......</option>";
            else $s .= ">" . $prefix . "</option>";
        }
        foreach ($rs as $k => $v) {
            $s .= '<option value="' . $v ['id'] . '"';
            if ($def == $v ['id'])
                $s .= ' selected';
            $s .= '>' . $v ['name'] . '</option>';
        }
        $s .= "</select>";
        return $s;
    }

    public static function SelectDataGroup($rs, $root, $name, $css = '', $def = null, $prefix = null, $event = '')
    {
        $s = '<select id="' . $name . '" name="' . $name . '" class="' . $css . '" ' . $event . '>';
        if ($prefix) {
            $s .= "<option value='0'";
            if ($def == 0)
                $s .= ' selected';
            if ($prefix === true)
                $s .= ">请选择 ......</option>";
            else $s .= ">" . $prefix . "</option>";
        }
        foreach ($rs as $k => $v) {
            if ($v ['pid'] == $root) {
                $s .= '<optgroup label="' . $v ['name'] . '">';
                foreach ($rs as $m => $n) {
                    if ($n ['pid'] == $k) {
                        $s .= "<option value='$m'";
                        if ($def == $m)
                            $s .= ' selected';
                        $s .= '>' . $n ['name'] . '</option>';
                    }
                }
                $s .= '</optgroup>';
            }
        }
        $s .= '</select>';
        return $s;
    }

    public static function SelectDataTree($rs, $root, $name, $css = '', $def = null, $prefix = null, $event = '')
    {
        $s = '<select id="' . $name . '" name="' . $name . '" class="' . $css . '" ' . $event . '>';
        if ($prefix) {
            $s .= "<option value='0'";
            if ($def == 0)
                $s .= ' selected';
            if ($prefix === true)
                $s .= ">请选择 ......</option>";
            else $s .= ">" . $prefix . "</option>";
        }
        $s .= self::SelectDataLeap($rs, $root, $def);
        $s .= '</select>';
        return $s;
    }

    private static function SelectDataLeap($rs, $pid, $def = null, $spac = '')
    {
        $s = '';
        foreach ($rs as $k => $v) {
            if ($v ['pid'] == $pid) {
                $s .= '<option value="' . $k . '"';
                if ($def == $k)
                    $s .= ' selected';
                $s .= '>' . $spac . $v ['name'] . '</option>';
                $s .= self::SelectDataLeap($rs, $k, $def, $spac . '　');
            }
        }
        return $s;
    }

    public static function OrderLinks($url, $keys = array())
    {
        $rs = array();
        foreach ($keys as $v) {
            $rs [$v] ['asc'] = '<a href="' . $url . '&orderkey=' . $v . '&orderval=0">∧</a>';
            $rs [$v] ['desc'] = '<a href="' . $url . '&orderkey=' . $v . '&orderval=1">∨</a>';
        }
        return $rs;
    }

    public static function CtlSelKVList($rs, $name, $cls = '', $def = null, $prefix = true, $prefixkey = 0, $event = '')
    {
        if (!empty ($cls))
            $cls = " class='$cls'";
        $s = "<select id='$name' name='$name' $cls $event>";
        if ($prefix) {
            $s .= "<option value='$prefixkey'";
            if ($def == $prefixkey)
                $s .= ' selected';
            if ($prefix === true)
                $s .= ">请选择 ......</option>";
            else $s .= ">" . $prefix . "</option>";
        }
        foreach ($rs as $k => $v) {
            $s .= '<option value="' . $k . '"';
            if ($def == $k)
                $s .= ' selected';
            $s .= '>' . $v ['name'] . '</option>';
        }
        $s .= "</select>";
        return $s;
    }

    public static function SelectOptionList($rs, $name, $cls = '', $def = null, $prefix = true, $prefixkey = 0, $event = '')
    {
        if (!empty ($cls))
            $cls = " class='$cls'";
        $s = "<select id='$name' name='$name' $cls $event>";
        if ($prefix) {
            $s .= "<option value='$prefixkey'";
            if ($def == $prefixkey)
                $s .= ' selected';
            if ($prefix === true)
                $s .= "></option>";
            else $s .= ">" . $prefix . "</option>";
        }
        foreach ($rs as $k => $v) {
            $s .= '<option value="' . $k . '"';
            if ($def == $k)
                $s .= ' selected';
            $s .= '>' . $v ['name'] . '</option>';
        }
        $s .= "</select>";
        return $s;
    }

    public static function CtlSelCurrencyANDRate($rs, $name, $cls = '', $def = null, $prefix = true, $prefixkey = 0, $event = '')
    {
        if (!empty ($cls))
            $cls = " class='$cls'";
        $s = "<select id='$name' name='$name' $cls $event>";
        if ($prefix) {
            $s .= "<option value='$prefixkey'";
            if ($def == $prefixkey)
                $s .= ' selected';
            if ($prefix === true)
                $s .= "></option>";
            else $s .= ">" . $prefix . "</option>";
        }
        foreach ($rs as $k => $v) {
            $s .= '<option value="' . $k . '"';
            if ($def == $k)
                $s .= ' selected';
            $s .= '>' . $v ['name'] . $v ['rate'] . '</option>';
        }
        $s .= "</select>";
        return $s;
    }

    public static function CtlSelList($rs, $name, $cls = '', $def = null, $prefix = null, $prefixkey = 0, $event = '')
    {
        if (!empty ($cls))
            $cls = " class='$cls'";
        $s = "<select id='$name' name='$name' $cls $event>";
        if ($prefix) {
            $s .= "<option value='$prefixkey'";
            if ($def == $prefixkey)
                $s .= ' selected';
            if ($prefix === true)
                $s .= ">请选择 ......</option>";
            else $s .= ">" . $prefix . "</option>";
        }
        foreach ($rs as $k => $v) {
            $s .= '<option value="' . $k . '"';
            if ($def == $k)
                $s .= ' selected';
            $s .= '>' . $v . '</option>';
        }
        $s .= "</select>";
        return $s;
    }

    public static function CtlNumList($name, $first = 1, $last = 100, $step = 1, $cls = '', $def = null, $prefix = null, $prefixkey = 0, $suffixval = '', $event = '')
    {
        $asc = $first < $last;

        if (!empty ($cls))
            $cls = " class='$cls'";
        $s = "<select style='width:185px;' id='$name' name='$name' $cls $event>";
        if ($prefix) {
            $s .= "<option value='$prefixkey'";
            if ($def == $prefixkey)
                $s .= ' selected';
            if ($prefix === true)
                $s .= ">请选择 ......</option>";
            else $s .= ">" . $prefix . "</option>";
        }

        for ($i = $first; $i <= $last;) {
            $s .= '<option value="' . $i . '"';
            if ($def == $i)
                $s .= ' selected';
            $s .= '>' . $i . $suffixval . '</option>';
            $i += $step;
        }
        $s .= "</select>";
        return $s;
    }

    // ----- page nav -----//
    public static function PageGroup($count, $page, $url, $pagesize, $groupsize = 10, $title = '')
    {
        $pages = ceil($count / $pagesize);
        $groups = ceil($count / ($groupsize * $pagesize));
        $group = ceil($page / $groupsize);

        if ($page < 1)
            $page = 1;
        if ($page > $pages)
            $page = $pages;
        $s = '<div class="png">';
        if ($count > 0) {
            $s .= '<span class="pngc">' . $title . ' 总数: ' . $count . ' ， 页: <i>' . $page . '</i> / ' . $pages . '</span>';
        }
        $s .= '<span class="pngg">';
        if ($page > 1) {
            $s .= '<a href="' . $url . '&page=1" title="首页">&laquo;</a>';
        } else {
            $s .= '<a href="#" class="disabled" title="首页">&laquo;</a>';
        }
        if ($group > 1) {
            $s .= '<a href="' . $url . '&page=' . ($groupsize * ($group - 2) + 1) . '" title="上一组">&lsaquo;</a>';
        } else {
            $s .= '<a href="#" class="disabled" title="上一组">&lsaquo;</a>';
        }
        for ($i = $groupsize * ($group - 1) + 1; $i <= $groupsize * ($group); $i++) {
            if ($i <= $pages) {
                if ($i == $page) {
                    $s .= '<a href="#" class="active">' . $i . '</a>';
                } else {
                    $s .= '<a href="' . $url . '&page=' . $i . '">' . $i . '</a>';
                }
            }
        }
        if ($group >= $groups) {
            $s .= '<a href="#" class="disabled" title="下一组">&rsaquo;</a>';
        } else {
            $s .= '<a href="' . $url . '&page=' . ($groupsize * $group + 1) . '" title="下一组">&rsaquo;</a>';
        }
        if ($page >= $pages) {
            $s .= '<a href="#" class="disabled" title="末页">&raquo;</a>';
        } else {
            $s .= '<a href="' . $url . '&page=' . $pages . '" title="末页">&raquo;</a>';
        }
        $s .= '</span></div>';
        return $s;
    }

    public static function PageJump($count, $page, $size, $url)
    {
        $pages = ceil($count / $size);
        if ($page < 1)
            $page = 1;
        if ($page > $pages)
            $page = $pages;
        $s = '<div class="pnav">';
        if ($page > 1) {
            $s .= '<a href="' . $url . '&page=1" title="首页" class="first">&laquo;</a><a href="' . $url . '&page=' . ($page - 1) . '" title="上页" class="previous">&lsaquo;</a>';
        } else {
            $s .= '<a title="首页" class="first disable">&laquo;</a><a title="下页" class="next disable">&lsaquo;</a>';
        }
        $s .= '<input type="text" id="pnavtxt" onKeyDown="if(event.keyCode==13) {Redirect(\'' . $url . '&pn=\'+this.value); return false;}" value="' . $page . '"><a id="pnavnum">/ ' . $pages . '</a><input type="button" value="GO" id="pnavbtn" title="跳转" onclick="Redirect(\'' . $url . '&page=\'+$(\'#pnavtxt\').val())"/>';
        if ($page == $pages) {
            $s .= '<a title="下页" class="next disable">&rsaquo;</a><a title="末页" class="last disable">&raquo;</a>';
        } else {
            $s .= '<a href="' . $url . '&page=' . ($page + 1) . '" title="下页" class="next">&rsaquo;</a><a href="' . $url . '&page=' . $pages . '" title="末页" class="last">&raquo;</a>';
        }
        $s .= '</div>';
        return $s;
    }

    public static function PageJumps($total, $page, $pagesize, $url, $attr1 = null, $attr2 = null, $attr3 = null)
    {
        if (!empty ($attr1))
            $url .= $attr1 . '/';
        if (!empty ($attr2))
            $url .= $attr2 . '/';
        if (!empty ($attr3))
            $url .= $attr3 . '/';

        $max = ceil($total / $pagesize);
        if ($page < 1)
            $page = 1;
        if ($page > $max)
            $page = $max;
        $s = '<div class="pn clear">';
        $s .= '<span class="pnt">总数：' . $total . ' 个</span>';
        if ($page > 1) {
            $s .= '<a href="' . $url . '1" title="首页">&lt;&lt;</a><a href="' . $url . ($page - 1) . '" title="上页">&lt;</a>';
        } else {
            $s .= '<span class="pnd" title="首页">&lt;&lt;</span><span class="pnd" title="上页">&lt;</span>';
        }
        $s .= '<span class="pnc">' . $page . '</span>';
        if ($page == $max) {
            $s .= '<span class="pnd" title="下页">&gt;</span><span class="pnd" title="末页">&gt;&gt;</span>';
        } else {
            $s .= '<a href="' . $url . ($page + 1) . '" title="下页">&gt;</a><a href="' . $url . $max . '" title="末页">&gt;&gt;</a>';
        }
        $s .= '<span class="pnm">页次：<em>' . $page . '</em> / ' . $max . '<input type="text" id="pnj" onKeyDown="if(event.keyCode==13) {redirect(\'' . $url . '\'+this.value); return false;}"><input type="button" value="GO" id="pnb" onclick="redirect(\'' . $url . '\'+$(\'#pnj\').val())">';
        $s .= '</div>';
        return $s;
    }

    public static function PageJumps2($total, $page, $pagesize, $url, $attr1 = null, $attr2 = null, $attr3 = null)
    {
        if (!empty ($attr1))
            $url .= $attr1;
        if (!empty ($attr2))
            $url .= '-' . $attr2;
        if (!empty ($attr3))
            $url .= '-' . $attr3;

        $max = ceil($total / $pagesize);
        if ($page < 1)
            $page = 1;
        if ($page > $max)
            $page = $max;
        $s = '<div class="pn clear">';
        $s .= '<span class="pnt">总数：' . $total . ' 个</span>';
        if ($page > 1) {
            $s .= '<a href="' . $url . '-' . '1' . PAGE_EXTNAME . '" title="首页">&lt;&lt;</a><a href="' . $url . '-' . ($page - 1) . PAGE_EXTNAME . '" title="上页">&lt;</a>';
        } else {
            $s .= '<span class="pnd" title="首页">&lt;&lt;</span><span class="pnd" title="上页">&lt;</span>';
        }
        $s .= '<span class="pnc">' . $page . '</span>';
        if ($page == $max) {
            $s .= '<span class="pnd" title="下页">&gt;</span><span class="pnd" title="末页">&gt;&gt;</span>';
        } else {
            $s .= '<a href="' . $url . '-' . ($page + 1) . PAGE_EXTNAME . '" title="下页">&gt;</a><a href="' . $url . '-' . $max . PAGE_EXTNAME . '" title="末页">&gt;&gt;</a>';
        }
        $s .= '<span class="pnm">页次：<em>' . $page . '</em> / ' . $max . '<input type="text" id="pnj" onKeyDown="if(event.keyCode==13) {Redirect(\'' . $url . '-\'+this.value+\'' . PAGE_EXTNAME . '\'); return false;}" value="' . $page . '" /><input type="button" value="GO" id="pnb" onclick="redirect(\'' . $url . '-\'+$(\'#pnj\').val()+\'' . PAGE_EXTNAME . '\')" />';
        $s .= '</div>';
        return $s;
    }

    public static function PagePos($count, $page, $pagesize)
    {
        return $pagesize * (self::PageNum($count, $page, $pagesize) - 1);
    }

    public static function PageNum($count, $page, $pagesize)
    {
        $max = $pagesize == 0 ? 0 : ceil($count / $pagesize);
        if ($max < 1)
            return 1;
        if ($page < 1)
            return 1;
        if ($page > $max)
            return $max;
        return $page;
    }

    //页数
    public static function PageCount($count, $pagesize)
    {
        return ceil($count / $pagesize);
    }

    public static function Paged($count, $page = 1, $url = '', $pagesize = PAGE_SIZE_DEFAULT, $groupsize = PAGE_GROUP_DEFAULT, $title = '')
    {
        $pages = ceil($count / $pagesize);
        $groups = ceil($count / ($groupsize * $pagesize));
        $group = ceil($page / $groupsize);

        if ($page < 1)
            $page = 1;
        if ($page > $pages)
            $page = $pages;
        $s = '<div class="">';
        if ($count > 0) {
            $s .= '<span class="pull-left">' . $title . ' ' . $GLOBALS ['lang'] ['General_PagedTotal'] . ': ' . $count . ' ' . $GLOBALS ['lang'] ['General_PagedUnit'] . ', ' . $GLOBALS ['lang'] ['General_PagedPage'] . ': <font class="numb">' . $page . '</font>/' . $pages . '</span>';
        }
        $s .= '<div class="pagination pagination-small pagination-right"><ul>';
        if ($page > 1) {
            $s .= '<li><a href="' . $url . '&pn=1" title="' . $GLOBALS ['lang'] ['General_PagedFirst'] . '">&laquo;</a></li>';
        } else {
            $s .= '<li class="disabled" title="' . $GLOBALS ['lang'] ['General_PagedFirst'] . '"><a href="#">&laquo;</a></li>';
        }
        if ($group > 1) {
            $s .= '<li><a href="' . $url . '&pn=' . ($groupsize * ($group - 2) + 1) . '" title="' . $GLOBALS ['lang'] ['General_PagedGroupPrevious'] . '">&lsaquo;</a></li>';
        } else {
            $s .= '<li class="disabled" title="' . $GLOBALS ['lang'] ['General_PagedGroupPrevious'] . '"><a href="#">&lsaquo;</a></li>';
        }
        for ($i = $groupsize * ($group - 1) + 1; $i <= $groupsize * ($group); $i++) {
            if ($i <= $pages) {
                if ($i == $page) {
                    $s .= '<li class="active"><a href="#">' . $i . '</a></li>';
                } else {
                    $s .= '<li><a href="' . $url . '&pn=' . $i . '">' . $i . '</a></li>';
                }
            }
        }
        if ($group >= $groups) {
            $s .= '<li class="disabled" title="' . $GLOBALS ['lang'] ['General_PagedGroupNext'] . '"><a href="#">&rsaquo;</a></li>';
        } else {
            $s .= '<li><a href="' . $url . '&pn=' . ($groupsize * $group + 1) . '" title="' . $GLOBALS ['lang'] ['General_PagedGroupNext'] . '">&rsaquo;</a></li>';
        }
        if ($page >= $pages) {
            $s .= '<li class="disabled" title="' . $GLOBALS ['lang'] ['General_PagedLast'] . '"><a href="#">&raquo;</a></li>';
        } else {
            $s .= '<li><a href="' . $url . '&pn=' . $pages . '" title="' . $GLOBALS ['lang'] ['General_PagedLast'] . '">&raquo;</a></li>';
        }
        $s .= '</ul></div></div>';
        return $s;
    }

    // ----- alert box -----//
    public static function AlertInfo($title = '暂时没有相关数据', $css = 'alertinfo')
    {
        return '<div class="' . $css . '">' . $title . '</div>';
    }

    public static function AlertError($title = '数据处理错误', $css = 'alerterror')
    {
        return '<div class="' . $css . '">' . $title . '</div>';
    }

    public static function AlertNoData($title = '暂无数据', $css = 'alerterror')
    {
        return '<div class="' . $css . '">' . $title . '</div>';
    }

    public static function Orders($url, $keys = array())
    {
        $rs = array();
        foreach ($keys as $v) {
            $rs [$v] = '<a href="' . $url . '&orderkey=' . $v . '&orderval=0">∧</a><a href="' . $url . '&orderkey=' . $v . '&orderval=1">∨</a>';
        }
        return $rs;
    }
}

?>