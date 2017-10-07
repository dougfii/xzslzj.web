<script>
    var setting={data:{simpleData:{enable:true,idKey:'id',pIdKey:'pid'}},view:{dblClickExpand:dblClickExpand},callback:{onClick:onClick}};
    var nodes=<?php echo $tree; ?>;
    function onClick(e, treeId, node) {
        Redirect('?m=System&a=FacadeTypeList&id=' + node.id);
    }
    function dblClickExpand(treeId, treeNode) {
        return treeNode.level > 0;
    }

    $(function(){
        $.fn.zTree.init($("#tree"), setting, nodes);

        $('.del').click(function(){
            var id=$(this).attr('did');
            layer.confirm('您确认需要删除吗?\n此操作不可恢复!', function(i){layer.close(i);$.post('?m=System&a=OnFacadeTypeDelete', {id:id}, function(ret){if(ret.code==1)window.reload();else layer.msg(ret.msg, 2, -1);}, 'json');});
        });
    });
</script>
<div class="toolbar">
	<div class="tool clear"><span class="cap">外观评测项目</span><a class="tooladd" href="?m=System&a=FacadeTypeAdd&id=<?php echo $id; ?>">增加项目</a></div>
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
            <th>操作</th>
        </tr>
        <?php
       if(!empty($rs)):
            foreach($rs as $k=>$v):
        ?>
        <tr>
            <td class="fid"><?php echo $v['id']; ?></td>
            <td><img src="/img/ztree_con.gif" />　<?php echo $v['name']; ?></a></td>
            <td class="fopr"><a href="?m=System&a=FacadeTypeEdit&id=<?php echo $k; ?>">编辑</a> <a href="#" class="del" did="<?php echo $k; ?>">删除</a></td>
        </tr>
        <?php
            endforeach;
        endif;
        ?>
    </table>
</div>