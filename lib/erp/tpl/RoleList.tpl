<script type="text/javascript">
$(function(){
    $('.del').click(function(){
        var id=$(this).attr('did');
		layer.confirm('您确认需要删除吗?\n此操作不可恢复!', function(i){	layer.close(i);$.post('?m=System&a=OnRoleDelete', {id:id}, function(data){if(data==1)location.href='<?php echo Url::REQUEST_URI(); ?>';else layer.msg(data, 2, -1);});});
    });

	$('#findok').click(function () {
		location.href = '<?php echo Url::URL_FULL(); ?>&name=' + $('#name').val();
	});
});
</script>
<div class="toolbar">
	<div class="tool clear"><span class="cap">角色列表</span><span id="openfind" title="查找"></span><a class="tooladd" href="?m=System&a=RoleAdd">增加</a></div>
	<div class="find clear">
		<span>角色名称</span><input type="text" id="name" />
		<input type="button" class="" id="findok" value="查找"/>
	</div>
</div>
<div class="panel paneltool">
<?php
if(!empty($rs)):
?>
<table class="tl">
	<tr>
		<th>编号</th>
		<th>角色名称</th>
		<th>状态</th>
		<th>创建时间</th>
		<th>操作</th>
	</tr>
<?php
	foreach($rs as $k=>$v):
?>
	<tr>
		<td class="fid"><?php echo $v['id']; ?></td>
		<td class=""><?php echo $v['name']; ?></td>
		<td class="fint"><?php echo Format::BoolString($v['act']); ?></td>
		<td class="ftim"><?php echo $v['time']; ?></td>
		<td class="fopr"><a href="?m=System&a=RoleEdit&id=<?php echo $v['id']; ?>">编辑</a> <a href="#" class="del" did="<?php echo $v['id']; ?>">删除</a></td>
	</tr>
<?php
	endforeach;
?>
</table>
<?php
	echo '';
else:
	echo HTML::AlertInfo();
endif;
?>
</div>