<script type="text/javascript">
    $(function () {
        $('#ok').click(function () {
            var act = $('#act').is(':checked') ? 1 : 0;
            $.post('?m=Article&a=OnFeedbackEdit', {id:$('#id').val(),reply:$('#reply').val(),act:act},
                    function (ret){if(ret.code==1)layer.msg('答复完成', 1, function(){window.history.back();});else layer.msg(ret.msg, 1);}, 'json');
        });
    });
</script>
<div class="toolbar">
    <div class="tool clear"><span class="cap">答复投诉</span></div>
</div>
<div class="panel paneltool">
    <?php
	if(!empty($rs)):
	?>
    <table class="ti">
        <tr>
            <td class="k">投诉工程</td>
            <td class="v"><?php echo $rs['project']; ?></td>
            <td class="k">联系人</td>
            <td class="v"><?php echo $rs['contacts']; ?></td>
            <td class="k">电话</td>
            <td class="v"><?php echo $rs['phone']; ?></td>
            <td class="k">邮箱</td>
            <td class="v"><?php echo $rs['email']; ?></td>
        </tr>
        <tr>
            <td class="k">投诉内容</td>
            <td class="v" colspan="7"><?php echo $rs['content']; ?></td>
        </tr>
        <tr>
            <td class="k">投诉时间</td>
            <td class="v" colspan="7"><?php echo $rs['time']; ?></td>
        </tr>
    </table>
    <br/>
    <table class="ti">
        <tr>
            <td class="k">答复内容</td>
            <td class="v" colspan="7"><textarea rows="10" id="reply"><?php echo $rs['replay']; ?></textarea></td>
        </tr>
        <tr>
            <td class="k">是否公示</td>
            <td class="v"><input type="checkbox" id="act" <?php echo $rs['act']?'checked':''; ?>/></td>
            <td class="k">答复人</td>
            <td class="v"><?php echo $rs['user']; ?></td>
            <td class="k">答复时间</td>
            <td class="v"><?php echo $rs['replytime']; ?></td>
            <td class="k"></td>
            <td class="v"></td>
        </tr>
    </table>
    <input type="hidden" id="id" value="<?php echo $rs['id']; ?>">
    <div class="bpan"><input type="button" id="ok" class="bok" value="确定"/><input type="button" class="bback" value="返回"/></div>
    <?php
	else:
		echo HTML::AlertError();
	endif;
	?>
</div>