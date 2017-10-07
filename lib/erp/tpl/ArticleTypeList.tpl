<script>
    var setting={data:{simpleData:{enable:true,idKey:'id',pIdKey:'pid'}},view:{dblClickExpand:dblClickExpand},callback:{onClick:onClick}};
    var nodes=<?php echo $tree; ?>;
    function onClick(e, treeId, node) {
        Redirect('?m=Article&a=ArticleTypeList&id=' + node.id);
    }
    function dblClickExpand(treeId, treeNode) {
        return treeNode.level > 0;
    }

    $(function(){
        $.fn.zTree.init($("#tree"), setting, nodes);

        $('.del').click(function(){
            var id=$(this).attr('did');
            layer.confirm('您确认需要删除吗?\n此操作不可恢复!', function(i){layer.close(i);$.post('?m=Article&a=OnArticleTypeDelete', {id:id}, function(ret){if(ret.code==1)location.href='<?php echo Url::REQUEST_URI(); ?>';else layer.msg(ret.msg, 2, -1);}, 'json');});
        });
    });
</script>
<div class="toolbar">
	<div class="tool clear"><span class="cap">文章分类</span><a class="tooladd" href="?m=Article&a=ArticleTypeAdd&id=<?php echo $id; ?>">增加分类</a></div>
</div>
<div class="sideorg">
    <ul id="tree" class="ztree"></ul>
</div>
<div class="panelgroup">
    <?php echo $path; ?>
    <table class="tl">
        <tr>
            <th>ID</th>
            <th>名称</th>
            <th>目录</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        <?php
       if(!empty($rs)):
            foreach($rs as $k=>$v):
                    $icon = $v['act'] ? '/img/ztree_con.gif' : '/img/ztree_coff.gif';
        ?>
        <tr>
            <td class="fid"><?php echo $v['id']; ?></td>
            <td><img src="<?php echo $icon; ?>" />　<?php echo $v['name']; ?></a></td>
            <td class="fbol"><?php echo Format::BoolString($v['dir']); ?></td>
            <td class="fbol"><?php echo Format::BoolString($v['act']); ?></td>
            <td class="fopr"><a href="?m=Article&a=ArticleTypeEdit&id=<?php echo $k; ?>">编辑</a> <a href="#" class="del" did="<?php echo $k; ?>">删除</a></td>
        </tr>
        <?php
            endforeach;
        endif;
        ?>
    </table>
</div>