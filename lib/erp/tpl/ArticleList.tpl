<script>
	var setting={data:{simpleData:{enable:true,idKey:'id',pIdKey:'pid'}},view:{dblClickExpand:dblClickExpand},callback:{onClick:onClick}};
	var nodes=<?php echo $tree; ?>;
	function onClick(e, treeId, node) {
		Redirect('?m=Article&a=ArticleList&tid=' + node.id);
	}
	function dblClickExpand(treeId, treeNode) {
		return treeNode.level > 0;
	}

	$(function(){
		$.fn.zTree.init($("#tree"), setting, nodes);

		$('.del').click(function(){
			var id=$(this).attr('did');
			layer.confirm('您确认需要删除吗?\n此操作不可恢复!', function(i){layer.close(i);$.post('?m=Article&a=OnArticleDelete', {id:id}, function(ret){if(ret.code==1)location.reload();else layer.msg(ret.msg, 2, -1);}, 'json');});
		});

		$('#fok').click(function(){
			location.href='<?php echo $url; ?>&fname=' + $('#fname').val();
		});
	});
</script>

<div class="toolbar">
	<div class="tool clear"><span class="cap">文章列表</span><?php echo $paged; ?><a class="tooladd" href="?m=Article&a=ArticleAdd&tid=<?php echo $tid; ?>">发表文章</a><span class="find clear"><span>关键词</span><input type="text" id="fname" value="<?php echo $fname; ?>"/><input type="button" class="" id="fok" value="查找"/></span></div>
</div>
<div class="sideorg">
	<ul id="tree" class="ztree"></ul>
</div>
<div class="panelgroup">
	<?php echo $path; ?>
	<table class="tl">
		<tr>
			<th>ID</th>
			<th>分类</th>
			<th>名称</th>
			<th>浏览数</th>
			<th>发布者</th>
			<th>最后更新</th>
			<th>首次发布</th>
			<th>操作</th>
		</tr>
		<?php
       if(!empty($rs)):
            foreach($rs as $k=>$v):
				$act = $v['act'] ? '/img/ztree_aon.gif' : '/img/ztree_aoff.gif';
				$acttitle = $v['act'] ? '有效' : '禁用';
				$pic = empty($v['pic']) ? '/img/ztree_poff.gif' : '/img/ztree_pon.gif';
				$pictitle = empty($v['pic']) ? '无首图' : '有首图';
				$roll = $v['roll'] ? '/img/ztree_ron.gif' : '/img/ztree_roff.gif';
				$rolltitle = $v['roll'] ? '首页轮播' : '非首页轮播';
		?>
		<tr>
			<td class="fid"><?php echo $v['id']; ?></td>
			<td class="ftyp"><?php echo $v['type']; ?></td>
			<td><img src="<?php echo $act; ?>" title="<?php echo $acttitle; ?>" /> <img src="<?php echo $pic; ?>" title="<?php echo $pictitle; ?>" /> <img src="<?php echo $roll; ?>" title="<?php echo $rolltitle; ?>" />　<a href="<?php echo WEBSITE_DOMAIN; ?>?a=article&id=<?php echo $v['id']; ?>" target="_blank"><?php echo $v['title']; ?></a></td>
			<td class="fint"><?php echo $v['views']; ?></td>
			<td class="fusr"><?php echo $v['user']; ?></td>
			<td class="fdat"><?php echo $v['last']; ?></td>
			<td class="fdat"><?php echo $v['time']; ?></td>
			<td class="fopr"><a href="?m=Article&a=ArticleEdit&id=<?php echo $v['id']; ?>">编辑</a> <a href="#" class="del" did="<?php echo $v['id']; ?>">删除</a></td>
		</tr>
		<?php
            endforeach;
        endif;
        ?>
	</table>
</div>