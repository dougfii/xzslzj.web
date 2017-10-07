<script type="text/javascript">
var setting = {
	data: {
		simpleData: {
			enable: true,
			idKey: 'id',
			pIdKey: 'pid'
		}
	},
	callback: {
		onDblClick: onDblClick
	}
};
var nodes = <?php echo $tree; ?>;
function onDblClick(e, treeId, node) {
	if(!node.type){
		parent.AddApproveAuditor($('#gid').val() ,node.urlid);
		parent.box_auditor.close();
	}
}

$(function(){
	$.fn.zTree.init($("#tree"), setting, nodes);
});
</script>
<ul id="tree" class="ztree"></ul>
<input type="hidden" id="gid" value="<?php echo $gid; ?>" />
