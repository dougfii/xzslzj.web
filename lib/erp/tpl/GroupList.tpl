<script type="text/javascript">
    var setting={data:{simpleData:{enable:true,idKey:'id',pIdKey:'fid'}},view:{dblClickExpand:dblClickExpand},callback:{onClick:onClick}};
    var nodes=<?php echo $tree; ?>;
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
    function dblClickExpand(treeId, treeNode) {
        return treeNode.level > 0;
    }

    $(function(){
        $.fn.zTree.init($("#tree"), setting, nodes);

        $('.delgroup').click(function(){
            var id=$(this).attr('did');
            layer.confirm('您确认需要删除吗?\n此操作不可恢复!', function(i){layer.close(i);$.post('?m=System&a=OnGroupDelete', {id:id}, function(ret){if(ret.code==1)location.href='<?php echo Url::REQUEST_URI(); ?>';else layer.msg(ret.msg, 2, -1);}, 'json');});
        });

        $('.deluser').click(function(){
            var id=$(this).attr('did');
            layer.confirm('您确认需要删除吗?\n此操作不可恢复!', function(i){layer.close(i);$.post('?m=System&a=OnUserDelete', {id:id}, function(ret){if(ret.code==1)location.href='<?php echo Url::REQUEST_URI(); ?>';else layer.msg(ret.msg, 2, -1);}, 'json');});
        });

        $('#ok').click(function(){
            var s = '';
            $('[name=permission]:checked').each(function(){ s += $(this).val() + ','; });
            var user = $('#inherituser').is(':checked') ? 1 : 0;
            var dept = $('#inheritdept').is(':checked') ? 1 : 0;
            var allu = $('#inheritallu').is(':checked') ? 1 : 0;
            $.post('?m=System&a=OnGroupPermission', {gid:$('#gid').val(),oid:$('#oid').val(),s:s,user:user,dept:dept,allu:allu}, function(ret){if(ret.code==1){layer.msg('权限修改成功', 2, function(){Refresh();});}else layer.msg(ret.msg, 2, -1);}, 'json');
        });
    });
</script>
<div class="toolbar">
	<div class="tool clear"><span class="cap">组织机构</span><a class="tooladd" href="?m=System&a=GroupAdd&gid=<?php echo $gid; ?>">增加组织</a><a class="tooladd" href="?m=System&a=OrgAdd&gid=<?php echo $gid; ?>&oid=<?php echo $oid; ?>">增加部门</a><a class="tooladd" href="?m=System&a=UserAdd">增加用户</a></div>
</div>
<div class="sideorg">
    <ul id="tree" class="ztree"></ul>
</div>
<div class="panelgroup">
		<?php echo $path; ?>
		<table class="tl">
			<tr>
				<th>名称</th>
				<th>操作</th>
			</tr>
			<?php
			if(isset($rg) && !empty($rg)):
				foreach($rg as $k=>$v):
			?>
			<tr>
				<td><a href="?m=System&a=GroupList&gid=<?php echo $k; ?>"><img src="/img/ztree_group1.gif" />　<?php echo $v; ?></a></td>
				<td class="fopr"><a href="?m=System&a=GroupEdit&id=<?php echo $k; ?>">编辑</a> <a href="#" class="delgroup" did="<?php echo $k; ?>">删除</a></td>
			</tr>
			<?php
				endforeach;
			endif;
			
			if(isset($ro) && !empty($ro)):
				foreach($ro as $k=>$v):
			?>
			<tr>
				<td><a href="?m=System&a=GroupList&gid=<?php echo $gid; ?>&oid=<?php echo $k; ?>"><img src="/img/ztree_org.gif" />　<?php echo $v; ?></a></td>
				<td class="fopr"><a href="?m=System&a=OrgEdit&id=<?php echo $k; ?>">编辑</a> <a href="#" class="delorg" did="<?php echo $k; ?>">删除</a></td>
			</tr>
			<?php
				endforeach;
			endif;

			if(isset($ru) && !empty($ru)):
				foreach($ru as $k=>$v):
					$icon = $v['act']?($v['lead']?'/img/ztree_ulead.gif':($v['sex']?'/img/ztree_uwoman.gif':'/img/ztree_uman.gif')):'/img/ztree_uact.gif';
			?>
			<tr>
				<td><img src="<?php echo $icon; ?>" />　<?php echo $v['name']; ?></a></td>
				<td class="fopr"><a href="?m=System&a=UserEdit&id=<?php echo $k; ?>&oid=<?php echo $oid; ?>">编辑</a> <a href="#" class="deluser" did="<?php echo $k; ?>">删除</a></td>
			</tr>
			<?php
				endforeach;
			endif;
			?>
		</table>
		<?php
		if(!empty($gid)||!empty($ord)):
			echo $permission;
		?>
		<input type="hidden" id="gid" value="<?php echo $gid; ?>" />
		<input type="hidden" id="oid" value="<?php echo $oid; ?>" />
		<div class="bpan"><input type="button" id="ok" class="bok" value="确定" /><input type="button" class="bback" value="返回" /></div>
		<?php endif; ?>
</div>