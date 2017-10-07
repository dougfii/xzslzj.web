<script type="text/javascript">
    var setting={data:{simpleData:{enable:true,idKey:'id',pIdKey:'fid'}},view:{dblClickExpand:dblClickExpand},callback:{onClick:onClick}};
    var setting1={data:{simpleData:{enable:true,idKey:'id',pIdKey:'fid'}},view:{dblClickExpand:false},callback:{onClick:onClick1}};
    var nodes=<?php echo $tree; ?>;
    var nodes1=<?php echo $tree1; ?>;
    function onClick(e, treeId, node) {
        if(node.type==1){
            Redirect('?m=System&a=GroupList&gid=' + node.gid);
        }
        else if(node.type==2){
            Redirect('?m=System&a=GroupList&gid=' + node.gid + '&oid=' + node.oid);
        }
        else{
            Redirect('?m=System&a=UserEdit&id=' + node.uid + '&oid='+node.oid);
        }
    }
    function onClick1(e, treeId, node) {
        if(node.level>0 && node.type==1){
            $('#group').val(node.name);
            $('#fid').val(node.gid);
        }
        else{
            $('#group').val('');
            $('#fid').val('');
        }
    }
    function dblClickExpand(treeId, treeNode) {
        return treeNode.level > 0;
    }

    $(function(){
        $.fn.zTree.init($("#tree"), setting, nodes);
        $.fn.zTree.init($("#tree1"), setting1, nodes1);

        $('#ok').click(function(){
            $.post('?m=System&a=OnGroupAdd', {fid:$('#fid').val(),name:$('#name').val(),company:$('#company').val()}, function (ret){if(ret.code==1)layer.msg('增加成功', 1, function(){window.history.back();});else layer.msg(ret.msg, 1);}, 'json');
        });
    });
</script>
<div class="toolbar">
	<div class="tool clear"><span class="cap">增加组织</span></div>
</div>
<div class="sideorg">
	<ul id="tree" class="ztree"></ul>
</div>
<div class="panelgroup">
    <table class="ti">
        <tr>
            <td class="k hidden">上级组织</td>
            <td class="v hidden"><input type="text" id="group" class="r" readonly onclick="TreeBox(this)" value="<?php echo $name; ?>" /><div id="groupbox" class="treebox"><ul id="tree1" class="ztree"></ul></div><input type="hidden" id="fid" value="<?php echo $fid; ?>" /></td>
            <td class="k">组织名称</td>
            <td class="v"><input type="text" id="name" class="r"/></td>
            <td class="k">单位名称</td>
            <td class="v"><input type="text" id="company" class="r"/></td>
            <td class="k"></td>
            <td class="v"></td>
            <td class="k"></td>
            <td class="v"></td>
        </tr>
    </table>
    <div class="bpan"><input type="button" id="ok" class="bok" value="确定" /><input type="button" class="bback" value="返回" /></div>
</div>