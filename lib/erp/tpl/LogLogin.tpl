<div class="toolbar">
	<div class="tool clear"><span class="cap">登录日志 <?php echo $name; ?></span><?php echo $paged; ?></div>
</div>
<div class="panel paneltool">
<?php
if(!empty($rs)):
?>
<table class="tl">
	<tr>
		<th>序号</th>
		<th>类型</th>
		<th>名称</th>
		<th>IP</th>
		<th>时间</th>
	</tr>
<?php
	foreach($rs as $k=>$v):
?>
	<tr>
		<td class="fsku"><?php echo $v['id']; ?></td>
		<td class="fsku"><?php echo $v['type']; ?></td>
		<td class="fusr"><?php echo $v['user']; ?></td>
		<td class="fip"><a href="http://www.ip138.com/ips138.asp?ip=<?php echo $v['ip']; ?>&action=2" target="_blank"><?php echo $v['ip']; ?></a></td>
		<td class="ftim"><?php echo $v['time']; ?></td>
	</tr>
<?php
	endforeach;
?>
</table>
<?php
else:
	echo HTML::AlertInfo();
endif;
?>
</div>