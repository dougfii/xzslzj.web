<div class="caption"><span class="title">审批历史</span></div>
<?php foreach($rs as $v): ?>
<table class="table table-list table-bordered table-condensed">
	<tr>
		<th>审批事由: <?php echo $v['causename']; ?></th>
		<th>状态: <?php echo $v['agreesname']; ?></th>
		<th>创建时间: <?php echo $v['time']; ?></th>
		<th>最后更新: <?php echo $v['last']; ?></th>
	</tr>
</table>
<?php if(!empty($v['auditorlist'])): ?>
<table class="table table-list table-bordered table-condensed">
	<?php foreach($v['auditorlist'] as $n): ?>
	<tr>
		<td class="fusr"><?php echo $n['auditor']; ?></td>
		<td class="fsta"><?php echo $n['agreename']; ?></td>
		<td class=""><?php echo $n['memo']; ?></td>
		<td class="ftim"><?php echo Format::DateString($n['time']); ?></td>
	<tr>
	<?php endforeach; ?>
</table>
<br /><br />
<?php endif; ?>
<?php endforeach; ?>