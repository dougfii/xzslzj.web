<div class="body body-index">
    <div class="panel-index clear">
        <div class="panel-index-left">
            <div class="clear">
                <div class="panel-index-box">
                    <div class="cap-works"><span class="panel-index-more"><a href="?a=articles&id=2" target="_blank">更多 &raquo;</a></span></div>
                    <div class="list-works">
                        <ul>
                            <?php
                            if(!empty($ra1))
                            {
                                foreach($ra1 as $v)
                                {
                                    echo '<li><a href="/?a=article&id=' . $v['id'] . '" target="_blank">' . $v['title'] . '</a><i>' . Format::DateString($v['time']) . '</i></li>';
                                    }
                                }
                             ?>
                        </ul>
                    </div>
                </div>
                <div class="panel-index-box">
                    <div class="cap-project"><span class="panel-index-more"><a href="?a=articles&id=4" target="_blank">更多 &raquo;</a></span></div>
                    <div class="list-works">
                        <ul>
                            <?php
                            if(!empty($ra2))
                            {
                                foreach($ra2 as $v)
                                {
                                    echo '<li><a href="/?a=article&id=' . $v['id'] . '" target="_blank">' . $v['title'] . '</a><i>' . Format::DateString($v['time']) . '</i></li>';
                                    }
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="clear">
                <div class="panel-index-box">
                    <div class="cap-policy"><span class="panel-index-more"><a href="?a=articles&id=3" target="_blank">更多 &raquo;</a></span></div>
                    <div class="list-works">
                        <ul>
                            <?php
                            if(!empty($ra3))
                            {
                                foreach($ra3 as $v)
                                {
                                    echo '<li><a href="/?a=article&id=' . $v['id'] . '" target="_blank">' . $v['title'] . '</a><i>' . Format::DateString($v['time']) . '</i></li>';
                            }
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <div class="panel-index-box">
                    <div class="cap-download"><span class="panel-index-more"><a href="?a=articles&id=6" target="_blank">更多 &raquo;</a></span></div>
                    <div class="list-works">
                        <ul>
                            <?php
                            if(!empty($ra4))
                            {
                                foreach($ra4 as $v)
                                {
                                    echo '<li><a href="/?a=article&id=' . $v['id'] . '" target="_blank">' . $v['title'] . '</a><i>' . Format::DateString($v['time']) . '</i></li>';
                            }
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-index-right">
            <div class="clear">
                <div class="panel-index">
                    <div class="cap-introduction"><span class="panel-index-more"><a href="?a=article&id=1" target="_blank">详细内容 &raquo;</a></span></div>
                    <div class="list-introduction"><?php if(!empty($rb)) echo '<img src="' . $rb['pic'] . '"/>　　' . $rb['content'] ; ?></div>
                </div>
            </div>
            <div class="clear">
                <div class="cap-operation"></div>
                <div class="list-operation">
                    <a href="?m=project" target="_blank" class="btn-apply"><i></i>工程监督在线申报</a>
                    <a href="http://erp.xzslzj.cn" target="_blank" class="btn-panel"><i></i>业务系统管理中心</a>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-roll">
        <div id="pic_list_1" class="scroll_horizontal">
            <div class="box">
                <ul class="list">
                    <?php
                 if(!empty($rc))
                 {
                    foreach($rc as $v)
                    {
                        echo '<li><a href="/?a=article&id=' . $v['id'] . '" target="_blank"><img src="' . $v['pic'] . '"/></a></li>';
                        }
                     }
                     ?>
                </ul>
            </div>
        </div>
    </div>
    <?php
    if(!empty($linker) && is_array($linker) && count($linker)>0)
    {
        echo '<div class="panel-linker">';
        foreach($linker as $k=>$v)
        {
            echo '<a href="' . $v['url'] . '" title="' . $v['name'] . '" target="_blank"><img src="' . $v['pic'] . '" /></a>';
        }
        echo '</div>';
    }
    ?>
</div>