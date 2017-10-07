<script>
    $(function(){
        $('#add').click(function(){
            var items1 = makearray('.item1');
            var items2 = makearray('.item2');
            var items3 = makearray('.item3');
            $.post('?m=Project&a=OnProjectFlow51', {id:$('#id').val(),comp:$('#comp').val(),no:$('#no').val(),signer:$('#signer').val(),date:$('#date').val()
            ,v1c:$('#v1c').val(),v1n:$('#v1n').val(),v1d:$('#v1d').val(),v2c:$('#v2c').val(),v2n:$('#v2n').val(),v2d:$('#v2d').val(),v3c:$('#v3c').val(),v3n:$('#v3n').val(),v3d:$('#v3d').val()
            ,total0:$('#total0').val(),total1:$('#total1').val()
            ,'items1[]':items1,'items2[]':items2,'items3[]':items3}, function (ret){if(ret.code==1)layer.msg('提交成功', 1, function(){location.reload();});else layer.msg(ret.msg, 1);}, 'json');
        });

        function makearray(obj){
            var a = new Array();
            $.each($(obj), function(i, n){
                a.push($(this).val());
            });
            return a;
        };

        $('.upfile').change(function(){
            var pid = $('#pid').val();
            var tid = 51;
            var no = $(this).attr('fid');
            var name = $(this).attr('fname');
            upload(pid, tid, no, name);
        });

        function upload(pid, tid, no, name){
            $.ajaxFileUpload({
                url:'?m=Upload&a=UpFlowFixed&no='+no,
                secureuri:false,
                fileElementId:'upfile'+no,
                dataType:'json',
                success: function(ret, status) {
                    if(ret.state=='SUCCESS') {
                        $.post('?m=Project&a=OnUpFlowFixed', {pid:pid,tid:tid,no:no,name:name,file:ret.name,url:ret.url,ext:ret.type,size:ret.size}, function (rt){
                            if(rt.code==1)layer.msg('上传完成', 1, function(){
                                $('#atta'+no).html('<a href="'+ret.url+'" target="_blank">'+name+'</a>');
                            });else layer.msg(rt.msg, 1);
                        }, 'json');
                    }
                    else layer.msg(ret.state, 2, -1);
                },
                error:function(ret, status, e){
                    layer.msg('上传失败 ' + e, 2, -1);
                }
            });
        };

        $('#additem').click(function(){
            $('#items').append('<tr class="delitem"><td class="c item0"></td><td><input type="text" class="w item1"/></td><td class="c"><input type="text" size="5" class="c item2"/></td><td class="c"><input type="text" size="5" class="c item3"/></td></tr>');
            serials();
        });

        $('#items').on('dblclick', '.delitem', function(){
            var obj = $(this);
            layer.confirm('您确认需要删除当前条目吗?', function (i){
                layer.close(i);
                obj.remove();
                serials();
            });
        });

        function serials(){
            $.each($('.item0'), function(i, n){
                $(this).html(i+1);
            });
        }

        $('#items').on('blur', '.item2', function(){
            $.each($('.item2'), function(i, n){
                var v=$(this).val();
                if(v != '' && isNaN(v)){
                    layer.msg('条目序号'+(i+1)+'请输入正确的优良数', 2, -1);
                    return false;
                }
                else if(v != '') {
                    var x = $('.item3:eq(' + i + ')').val();
                    if (x != '' && !isNaN(x)) {
                        layer.msg('条目序号' + (i + 1) + '的优良数和合格数只能填写一项' + x, 2, -1);
                        return false;
                    }
                }
            });
            totals();
        });

        $('#items').on('blur', '.item3', function(){
            $.each($('.item3'), function(i, n){
                var v=$(this).val();
                if(v != '' && isNaN(v)) {
                    layer.msg('条目序号' + (i + 1) + '请输入正确的合格数', 2, -1);
                    return false;
                }
                else if(v != '') {
                    var x = $('.item2:eq(' + i + ')').val();
                    if (x != '' && !isNaN(x)) {
                        layer.msg('条目序号' + (i + 1) + '的优良数和合格数只能填写一项' + x, 2, -1);
                        return false;
                    }
                }
            });
            totals();
        });

        function totals(){
            var v2 = 0;
            var v3 = 0;
            $.each($('.item2'), function(i, n){
                var v = $(this).val();
                if(v != '' && !isNaN(v)) {
                    v2 += (v * 1);
                }
            });
            $.each($('.item3'), function(i, n){
                var v = $(this).val();
                if(v != '' && !isNaN(v)) {
                    v3 += (v * 1);
                }
            });
            $('#total0').val(v2);
            var v = v2+v3;
            if(v==0) $('#total1').val('0');
            else $('#total1').val(((v2/v)*100).toFixed(2));
        }
    });
</script>
<div class="toolbar">
    <div class="tool clear"><span class="cap">重要隐蔽（关键部位）单元工程核备 - [<?php echo $state; ?>]</span><a class="tooladd back" href="javascript:;">返回</a><?php if($edit) echo '<a class="tooladd" id="add" href="javascript:;">提交</a>'; ?></div>
</div>
<div class="panel paneltool">
    <div class="pagea4">
        <div class="pagea4info">
            <div class="center">重要隐蔽（关键部位）单元工程核备</div>
            <table class="tx1">
                <tr><td class="k">单位工程名称</td><td class="v"><?php if($edit) echo '<input type="text" class="" id="comp" value="' . $comp . '" />'; else echo $comp; ?></td><td class="k">项目法人</td><td class="v"><?php if($edit) echo '<input type="text" class="" id="signer" value="' . $signer . '" />'; else echo $signer; ?></td></tr>
                <tr><td class="k">分部工程名称</td><td class="v"><?php if($edit) echo '<input type="text" class="" id="no" value="' . $no . '" />'; else echo $no; ?></td><td class="k">核备时间</td><td class="v"><?php if($edit) echo '<input type="text" class="" id="date" value="' . $date . '" onclick="laydate();" readonly />'; else echo $date; ?></td></tr>
            </table>
            <table class="tx1">
                <thead>
                    <tr><td rowspan="2">序号</td><td rowspan="2">重要隐蔽（关键部位）单元工程名称与编号</td><td colspan="2">核备等级</td></tr>
                    <tr><td>优良</td><td>合格</td></tr>
                </thead>
                <tbody id="items">
                    <?php
                 if(!empty($items)){
                    foreach($items as $k=>$v){
                            echo '<tr class="delitem"><td class="c item0">' . ($k+1) . '</td><td>';
                            echo $edit ? '<input type="text" class="w item1" value="' . $v[0] . '"/>' : $v[0];
                            echo '</td><td class="c">';
                            echo $edit ? '<input type="text" size="5" class="c item2" value="' . $v[1] . '"/>' : $v[1];
                            echo '</td><td class="c">';
                            echo $edit ? '<input type="text" size="5" class="c item3" value="' . $v[2] . '"/>' : $v[2];
                            echo '</td></tr>';
                        }
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr><td colspan="4">重要隐蔽（关键部位）单元工程优良 <?php if($edit) echo '<input type="text" class="c" id="total0" size="5" value="' . $total0 . '" />'; else echo $total0; ?> 个，优良率 <?php if($edit) echo '<input type="text" class="c" id="total1" size="5" value="' . $total1 . '" />'; else echo $total1; ?> %。</td></tr>
                </tfoot>
            </table>
            <?php if($edit): ?><div><input type="button" value="添加条目" id="additem" /> （双击序号删除条目）</div><?php endif; ?>
            <table class="tx1">
                <tr><td colspan="2"><p>监理单位意见：</p><p><?php if($edit) echo '<textarea rows="5" class="w" id="v1c">' . $v1c . '</textarea>'; else echo $v1c; ?></p><p>总监理工程师（签字）：<?php if($edit) echo '<input type="text" class="" id="v1n" size="10" value="' . $v1n . '" />'; else echo $v1n; ?></p><p class="right">（盖公章）</p><p class="right">日期：<?php if($edit) echo '<input type="text" class="" id="v1d" size="10" value="' . $v1d . '" onclick="laydate();" readonly />'; else echo $v1d; ?></p></td><td colspan="2"><p>项目法人意见：</p><p><?php if($edit) echo '<textarea rows="5" class="w" id="v2c">' . $v2c . '</textarea>'; else echo $v2c; ?></p><p>技术负责人（签字）：<?php if($edit) echo '<input type="text" class="" id="v2n" size="10" value="' . $v2n . '" />'; else echo $v2n; ?></p><p class="right">（盖公章）</p><p class="right">日期：<?php if($edit) echo '<input type="text" class="" id="v2d" size="10" value="' . $v2d . '" onclick="laydate();" readonly />'; else echo $v2d; ?></p></td></tr>
                <tr><td colspan="4"><p>质量监督机构核备意见：</p><p><?php if($edit) echo '<textarea rows="5" class="w" id="v3c">' . $v3c . '</textarea>'; else echo $v3c; ?></p><p>核备人（签字）：<?php if($edit) echo '<input type="text" class="" id="v3n" size="10" value="' . $v3n . '" />'; else echo $v3n; ?></p><p class="right">（盖公章）</p><p class="right">日期：<?php if($edit) echo '<input type="text" class="" id="v3d" size="10" value="' . $v3d . '" onclick="laydate();" readonly />'; else echo $v3d; ?></p></td></tr>
            </table>
            <?php echo $atts; ?>
        </div>
    </div>
    <input type="hidden" id="id"/>
    <input type="hidden" id="pid" value="<?php echo $pid; ?>" />
</div>
