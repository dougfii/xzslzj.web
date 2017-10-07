<script type="text/javascript">
$(function(){
    $('.del').click(function(){
        if(confirm('您确认需要删除吗?\n此操作不可恢复!')){
            $.post('?m=System&a=OnApproveDelete', {id:$(this).attr('did')},
                function(msg){
                    if(msg == 1){
                        location.href='<?php echo Url::REQUEST_URI(); ?>';
                    }
                    else{
                        alert(msg);
                    }
                });
        }
    });
    $('#findok').click(function(){
		location.href='<?php echo Url::URL_FULL(); ?>&findcata=' + $('#findcata').val() + '&findname=' + $('#findname').val();
    });
});
</script>
<div class="toolbar">
	<div class="tool clear"><span class="cap">审批列表</span><span id="openfind" title="查找"></span><?php echo $paged; ?><a class="tooladd" href="?m=System&a=ApproveAdd">增加</a></div>
	<div class="find clear">
		<span>名称</span><input type="text" id="name" />
		<input type="button" class="" id="findok" value="查找"/>
	</div>
</div>

<div class="panel paneltool">
<?php
if(!empty($rs)):
?>
	<table class="tl">
		<tr>
			<th>审批对象<br /></th>
			<th>审批原因<br /></th>
			<th>状态<br /></th>
			<th>创建时间<br /></th>
			<th>操作</th>
		</tr>
<?php
	foreach($rs as $k=>$v):
?>
		<tr>
			<td class="fobj"><?php echo $v['approveobject']; ?></td>
			<td class=""><?php echo $v['causename']; ?></td>
			<td class="fsta"><?php echo Format::BoolString($v['act']); ?></td>
			<td class="ftim"><?php echo Format::DateString($v['time']); ?></td>
			<td class="fopr"><a href="?m=System&a=ApproveEdit&id=<?php echo $v['id']; ?>" title="编辑"><i class="icon-edit"></i></a> <a href="#" class="del" did="<?php echo $v['id']; ?>" title="删除"><i class="icon-remove"></i></a></td>
		</tr>
<?php
	endforeach;
?>
	</table>
</div>
<?php
	echo $paged;
else:
	echo HTML::AlertInfo();
endif;
?>