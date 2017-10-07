<div class="cap"><div class="capl">事件日志</div></div>
<?php
if(!empty($rs)):
?>
<table class="tl">
	<tr>
		<th>序号<?php echo $orders['id']; ?></th>
		<th>事件记录<?php echo $orders['event']; ?></th>
		<th>耗时(MS)<?php echo $orders['spent']; ?></th>
		<th>使用内存<?php echo $orders['memory']; ?></th>
		<th>时间<?php echo $orders['time']; ?></th>
	</tr>
<?php
	foreach($rs as $k=>$v):
?>
	<tr>
		<td class="fcod"><?php echo $v['id']; ?></td>
		<td class=""><?php echo $v['event']; ?></td>
		<td class="fcet"><?php echo $v['spent']; ?></td>
		<td class="fcet"><?php echo $v['memory']; ?></td>
		<td class="ftim"><?php echo $v['time']; ?></td>
	</tr>
<?php
	endforeach;
?>
</table>
<?php
	echo $paged;
else:
	echo HTML::AlertInfo();
endif;
?>