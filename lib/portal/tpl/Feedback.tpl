<script>
    $(function(){
        $('#ok').click(function(){
            $.post('?a=onfeedback', {project:$('#project').val(),content:$('#content').val(),contacts:$('#contacts').val(),phone:$('#phone').val(),email:$('#email').val()}, function (ret){if(ret.code==1)layer.msg('质量投诉提交成功，我们会尽快与您反馈的！', 1, function(){location.reload();});else layer.msg(ret.msg, 1);}, 'json');
        });

        $('#fok').click(function(){
            location.href='<?php echo $url; ?>&fname=' + $('#fname').val();
        });
    });
</script>
<div class="body body-index">
    <div class="articles-left">
        <?php echo $side; ?>
    </div>
    <div class="articles-right feedback">
        <div class="caption"><img src="img/feedback.jpg"/>质量投诉</div>
        <table class="tfeedback">
            <tr>
                <td class="k">投诉工程</td>
                <td class="v"><input type="text" id="project"/></td>
            </tr>
            <tr>
                <td class="k">投诉内容</td>
                <td class="v"><textarea rows="8" id="content"></textarea></td>
            </tr>
            <tr>
                <td class="k">联系人</td>
                <td class="v"><input type="text" id="contacts"/></td>
            </tr>
            <tr>
                <td class="k">电话</td>
                <td class="v"><input type="text" id="phone"/></td>
            </tr>
            <tr>
                <td class="k">邮箱</td>
                <td class="v"><input type="text" id="email"/></td>
            </tr>
        </table>
        <div class="btns"><a href="javascript:;" id="ok" class="btny">提交</a></div>
        <br/>
        <?php
        echo '<div class="feedbacks">';
        echo '<div class="caption">投诉反馈</div>';
        echo '<div><input type="text" id="fname" value="' . $fname . '"/><input type="button" id="fok" value="查找"/></div>';
        if(!empty($rs)){
            foreach($rs as $k=>$v){
                echo '<div class="feedbackitem"><div class="clear tit"><div class="left">投诉工程: ' . $v['project'] . '</div><div class="right">投诉日期: ' . Format::DateString($v['time']) . '</div></div>';
                echo '<div class="content">' . $v['content'] . '</div>';
                if(!empty($v['reply'])){
                    echo '<div class="reply"><span>答复内容:</span> ' . $v['reply'] . '</div>';
                }
                echo '</div>';
            }
            echo '<div class="paged">' . $paged . '</div>';
        }
        else{
            echo HTML::AlertInfo();
        }
        echo '</div>';
        ?>
    </div>
</div>
