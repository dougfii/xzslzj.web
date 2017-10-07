<script>
	$(function(){
		var start = {elem: '#fdate0',choose: function(datas){end.min = datas;end.start = datas;}};
		var end = {elem: '#fdate1',choose: function(datas){start.max = datas;}};
		laydate(start);
		laydate(end);

		$('.aid').click(function(){
			var id=$(this).attr('aid');
			var name=$(this).attr('aname');
			layer.confirm('您确认需要'+name+'项目吗?', function(i){layer.close(i);$.post('?m=Project&a=OnProjectAct', {id:id}, function(ret){if(ret.code==1)layer.msg('立项状态切换成功', 2, function(){location.reload();});else layer.msg(ret.msg, 2, -1);}, 'json');});
		});

		$('.del').click(function(){
			var id=$(this).attr('did');
			layer.confirm('您确认需要删除吗?\n此操作不可恢复!', function(i){layer.close(i);$.post('?m=Project&a=OnProjectDelete', {id:id}, function(ret){if(ret.code==1)layer.msg('删除成功', 2, function(){location.reload();});else layer.msg(ret.msg, 2, -1);}, 'json');});
		});

		$('#fok').click(function(){
			Redirect(window.location.href + '&fname=' + $('#fname').val() + '&fcompany=' + $('#fcompany').val() + '&fdate0=' + $('#fdate0').val() + '&fdate1=' + $('#fdate1').val());
		});

		$('#export').click(function(){
			Export('?m=Export&a=ExcelProjectList' + '&fname=' + $('#fname').val() + '&fcompany=' + $('#fcompany').val() + '&fdate0=' + $('#fdate0').val() + '&fdate1=' + $('#fdate1').val());
		});
	});
</script>
<div class="toolbar">
	<div class="tool clear"><span class="cap">工程列表</span><?php echo $paged; ?><span class="find clear"><span>名称</span><input type="text" id="fname" size="8" value="<?php echo $fname; ?>"/><span>单位</span><input type="text" id="fcompany" size="8" value="<?php echo $fcompany; ?>"/><span>日期起</span><input type="text" id="fdate0" size="10" class="c" value="<?php echo $fdate0; ?>" readonly/><span>日期止</span><input type="text" id="fdate1" size="10" class="c" value="<?php echo $fdate1; ?>" readonly/><input type="button" class="" id="fok" value="查找"/></span><a href="javascript:;" id="export" class="tooladd">导出</a></div>
</div>
<div class="panel paneltool">
	<table class="tl">
		<tr>
			<th>序号</th>
			<th>组织</th>
			<th>立项</th>
			<th>工程名称</th>
			<th>申报单位</th>
			<th>联系人</th>
			<th>联系方式</th>
			<th>邮箱</th>
			<th>最后登录</th>
			<th>注册时间</th>
			<th>操作</th>
		</tr>
		<?php
       if(!empty($rs)):
            foreach($rs as $k=>$v):
		?>
		<tr>
			<td class="fid"><?php echo $v['id']; ?></td>
			<td class="fcet"><?php echo $v['group']; ?></td>
			<td class="fint"><a href="javascript:;" class="aid" aid="<?php echo $v['id']; ?>" aname="<?php echo $v['act']?'关闭':'开放'; ?>"><?php echo $v['act']?'开放':'关闭'; ?></a></td>
			<td class="fcet"><a href="?m=Project&a=ProjectFlow&pid=<?php echo $v['id']; ?>"><?php echo $v['name']; ?></a></td>
			<td class="fcet"><?php echo $v['company']; ?></td>
			<td class="fusr"><?php echo $v['contacts']; ?></td>
			<td class="fmob"><?php echo $v['mobile']; ?></td>
			<td class="fema"><?php echo $v['email']; ?></td>
			<td class="fdat"><a href="?m=System&a=LogLogin&tid=1&uid=<?php echo $v['id']; ?>" target="_blank"><?php echo $v['last']; ?></a></td>
			<td class="fdat"><?php echo $v['time']; ?></td>
			<td class="fopr"><?php if($isAdmin) echo '<a href="?m=Project&a=ProjectEdit&id=' . $v['id'] . '">编辑</a> '; ?><a href="#" class="del" did="<?php echo $v['id']; ?>">删除</a></td>
		</tr>
		<?php
            endforeach;
        endif;
        ?>
	</table>
</div>