<script>
	$(function(){
		$('.del').click(function(){
			var id=$(this).attr('did');
			layer.confirm('您确认需要删除吗?\n此操作不可恢复!', function(i){layer.close(i);$.post('?m=Article&a=OnFeedbackDelete', {id:id}, function(ret){if(ret.code==1)location.reload();else layer.msg(ret.msg, 2, -1);}, 'json');});
		});

		$('.act').click(function(){
			var id=$(this).attr('aid');
			$.post('?m=Article&a=OnFeedbackAct', {id:id}, function(ret){if(ret.code==1)location.reload();else layer.msg(ret.msg, 2, -1);}, 'json');
		});

		$('#fok').click(function(){
			location.href='<?php echo $url; ?>&fname=' + $('#fname').val();
		});
	});
</script>

<div class="toolbar">
	<div class="tool clear"><span class="cap">质量投诉</span><?php echo $paged; ?><span class="find clear"><span>关键词</span><input type="text" id="fname" value="<?php echo $fname; ?>"/><input type="button" class="" id="fok" value="查找"/></span></div>
</div>
<div class="panel paneltool">
	<table class="tl">
		<tr>
			<th>ID</th>
			<th>投诉工程</th>
			<th>投诉内容</th>
			<th>联系人</th>
			<th>电话</th>
			<th>邮箱</th>
			<th>投诉时间</th>
			<th>答复内容</th>
			<th>答复时间</th>
			<th>答复人</th>
			<th>公示</th>
			<th>操作</th>
		</tr>
		<?php
       if(!empty($rs)):
            foreach($rs as $k=>$v):
		?>
		<tr>
			<td class="fid"><?php echo $v['id']; ?></td>
			<td class=""><?php echo $v['project']; ?></td>
			<td class=""><?php echo $v['content']; ?></td>
			<td class="fusr"><?php echo $v['contacts']; ?></td>
			<td class="fpho"><?php echo $v['phone']; ?></td>
			<td class="fema"><?php echo $v['email']; ?></td>
			<td class="fdat"><?php echo $v['time']; ?></td>
			<td class=""><?php echo $v['reply']; ?></td>
			<td class="fdat"><?php echo $v['replytime']; ?></td>
			<td class="fusr"><?php echo $v['user']; ?></td>
			<td class="fpos"><?php echo Format::BoolString($v['act']); ?></td>
			<td class="fopr"><a href="?m=Article&a=FeedbackEdit&id=<?php echo $v['id']; ?>">答复</a> <a href="javascript:;" class="del" did="<?php echo $v['id']; ?>">删除</a></td>
		</tr>
		<?php
            endforeach;
        endif;
        ?>
	</table>
</div>