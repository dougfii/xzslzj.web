<div class="popbar">项目：<input type="text" id="facadetype" class="r" readonly onclick="TreeBox(this)" value="" /><div id="facadetypebox" class="treebox"><ul id="tree" class="ztree"></ul></div><input type="hidden" id="id" value="" /> <input type="button" id="add" value="增加项目"/> <a href="?m=Project&a=PopFacadeItemsEdit">刷新</a> <input type="button" id="ok" value="完成编辑"/></a> (编辑表格将重置所有数据,请谨慎操作)</div>
<div class="box">
    <?php echo $table; ?>
</div>
<script>
    var setting={data:{simpleData:{enable:true,idKey:'id',pIdKey:'pid'}},view:{dblClickExpand:dblClickExpand},callback:{onClick:onClick}};
    var nodes=<?php echo $tree; ?>;

    function onClick(e, treeId, node) {
        if(node.level>1 && !node.isParent){
            $('#facadetype').val(node.name);
            $('#id').val(node.id);
        }
        else{
            $('#facadetype').val('');
            $('#id').val('');
        }
    }
    function dblClickExpand(treeId, treeNode) {
        return treeNode.level > 0;
    }

    function loadFacade(){
        $.post('?m=Project&a=OnFacadeItemsAdd', {id:$('#id').val()}, function (ret){
            if(ret.code==1)location.reload();else layer.msg(ret.msg, 1);
        }, 'json');
    }

    $(function(){
        $.fn.zTree.init($("#tree"), setting, nodes);

        $('#add').click(function(){
            loadFacade();
        });

        $('.del').click(function(){
            var id=$(this).attr('did');
            layer.confirm('您确认需要删除此行条目吗?', function(i){layer.close(i);$.post('?m=Project&a=OnFacadeItemsDelete', {id:id}, function(ret){if(ret.code==1)location.reload();else layer.msg(ret.msg, 2, -1);}, 'json');});
        });

        $('#ok').click(function(){
            $.post('?m=Project&a=OnFacadeItemsOK', {}, function (ret){
                if(ret.code==1){
                    $('#facadebox',parent.document.body).html(ret.data);
                    $('#rand',parent.document.body).val(Math.random());
                    $('#rand',parent.document.body).val(ret.msg);
                    parent.layer.close(parent.layer.getFrameIndex(window.name));
                }else layer.msg(ret.msg, 1);
            }, 'json');
        });
    });

    var cols = <?php echo $cols; ?>;
    var arr = new Array();
    for(var i=0;i<cols;i++) arr.push(i);
    $('#facade').mergeCell({cols: arr});
</script>