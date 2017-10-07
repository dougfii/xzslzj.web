<div class="toolbar">
    <div class="tool clear"><span class="cap">单位工程外观质量结论 - [<?php echo $state; ?>]</span><a class="tooladd back" href="javascript:;">返回</a><?php if($edit) echo '<a class="tooladd" id="add" href="javascript:;">提交</a>'; ?></div>
</div>
<div class="panel paneltool">
    <div class="pagea4">
        <div class="pagea4info">
            <div class="center">单位工程外观质量结论</div>
            <table class="tx1">
                <tr><td class="k">单位工程名称</td><td><?php if($edit) echo '<input type="text" class="" id="attachments" value="' . $attachments . '" />'; else echo $attachments; ?></td><td class="k">施工单位</td><td><?php if($edit) echo '<input type="text" class="" id="comp" value="' . $comp . '" />'; else echo $comp; ?></td></tr>
                <tr><td class="k">单位工程编号</td><td><?php if($edit) echo '<input type="text" class="" id="no" value="' . $no . '" />'; else echo $no; ?></td><td class="k">评定日期</td><td><?php if($edit) echo '<input type="text" class="" id="date_ping" value="' . $date_ping . '" onclick="laydate();" readonly />'; else echo $date_ping; ?></td></tr>
            </table>
            <div id="facadebox"><?php echo $tables; ?></div>
            <!-- <?php if($edit): ?><div><input type="button" id="edititems" value="编辑项目"/></div><?php endif; ?> -->
            <table class="tx1">
                <tr><td rowspan="7" class="k">评定组成员</td><td colspan="2" class="k">单位名称</td><td class="k">职称</td><td class="k">签名</td></tr>
                <tr><td class="k">项目法人</td><td class="v"><?php if($edit) echo '<input type="text" class="" id="m11" value="' . $m11 . '" />'; else echo $m11; ?></td><td><?php if($edit) echo '<input type="text" class="" id="m12" value="' . $m12 . '" />'; else echo $m12; ?></td><td><?php if($edit) echo '<input type="text" class="" id="m13" value="' . $m13 . '" />'; else echo $m13; ?></td></tr>
                <tr><td class="k">监理单位</td><td class="v"><?php if($edit) echo '<input type="text" class="" id="m21" value="' . $m21 . '" />'; else echo $m21; ?></td><td><?php if($edit) echo '<input type="text" class="" id="m22" value="' . $m22 . '" />'; else echo $m22; ?></td><td><?php if($edit) echo '<input type="text" class="" id="m23" value="' . $m23 . '" />'; else echo $m23; ?></td></tr>
                <tr><td class="k">设计单位</td><td class="v"><?php if($edit) echo '<input type="text" class="" id="m31" value="' . $m31 . '" />'; else echo $m31; ?></td><td><?php if($edit) echo '<input type="text" class="" id="m32" value="' . $m32 . '" />'; else echo $m32; ?></td><td><?php if($edit) echo '<input type="text" class="" id="m33" value="' . $m33 . '" />'; else echo $m33; ?></td></tr>
                <tr><td class="k">施工单位</td><td class="v"><?php if($edit) echo '<input type="text" class="" id="m41" value="' . $m41 . '" />'; else echo $m41; ?></td><td><?php if($edit) echo '<input type="text" class="" id="m42" value="' . $m42 . '" />'; else echo $m42; ?></td><td><?php if($edit) echo '<input type="text" class="" id="m43" value="' . $m43 . '" />'; else echo $m43; ?></td></tr>
                <tr><td class="k">检测单位</td><td class="v"><?php if($edit) echo '<input type="text" class="" id="m51" value="' . $m51 . '" />'; else echo $m51; ?></td><td><?php if($edit) echo '<input type="text" class="" id="m52" value="' . $m52 . '" />'; else echo $m52; ?></td><td><?php if($edit) echo '<input type="text" class="" id="m53" value="' . $m53 . '" />'; else echo $m53; ?></td></tr>
                <tr><td class="k">运行管理单位</td><td class="v"><?php if($edit) echo '<input type="text" class="" id="m61" value="' . $m61 . '" />'; else echo $m61; ?></td><td><?php if($edit) echo '<input type="text" class="" id="m62" value="' . $m62 . '" />'; else echo $m62; ?></td><td><?php if($edit) echo '<input type="text" class="" id="m63" value="' . $m63 . '" />'; else echo $m63; ?></td></tr>
                <tr><td class="k">质量监督机构</td><td colspan="4"><p>核定意见：<?php if($edit) echo '<textarea rows="5" class="w" id="content">' . $content . '</textarea>'; else echo $content; ?></p><p class="right center">核定人（签字）：<?php if($edit) echo '<input type="text" class="" id="signer" value="' . $signer . '" />'; else echo $signer; ?><br/>（盖公章）<br/>日期：<?php if($edit) echo '<input type="text" class="" id="date" value="' . $date . '" onclick="laydate();" readonly />'; else echo $date; ?></p></tr>
            </table>
            <input type="hidden" id="id"/>
            <input type="hidden" id="rand"/>
            <input type="hidden" id="cols" value="0"/>
        </div>
    </div>

    <script>
        $(function(){
            $('#add').click(function(){
                var items1 = makearray('.item1');
                var items2 = makearray('.item2');
                var items3 = makearray('.item3');
                var items4 = makearray('.item4');
                var items5 = makearray('.item5');
                var totals1 = makearray('.total1');
                var totals2 = makearray('.total2');
                var totals3 = makearray('.total3');
                var totals4 = makearray('.total4');
                var totals5 = makearray('.total5');
                $.post('?m=Project&a=OnProjectFlow62', {id:$('#id').val(),attachments:$('#attachments').val(),no:$('#no').val(),signer:$('#signer').val(),content:$('#content').val(),date:$('#date').val(),comp:$('#comp').val(),date_ping:$('#date_ping').val()
                ,'items1[]':items1,'items2[]':items2,'items3[]':items3,'items4[]':items4,'items5[]':items5,'totals1[]':totals1,'totals2[]':totals2,'totals3[]':totals3,'totals4[]':totals4,'totals5[]':totals5
                ,amount1:$('#amount1').val(),amount2:$('#amount2').val(),amount3:$('#amount3').val(),amount4:$('#amount4').val()
                ,m11:$('#m11').val(),m12:$('#m12').val(),m13:$('#m13').val()
                ,m21:$('#m21').val(),m22:$('#m22').val(),m23:$('#m23').val()
                ,m31:$('#m31').val(),m32:$('#m32').val(),m33:$('#m33').val()
                ,m41:$('#m41').val(),m42:$('#m42').val(),m43:$('#m43').val()
                ,m51:$('#m51').val(),m52:$('#m52').val(),m53:$('#m53').val()
                ,m61:$('#m61').val(),m62:$('#m62').val(),m63:$('#m63').val()
                }, function (ret){if(ret.code==1)layer.msg('提交成功', 1, function(){location.reload();});else layer.msg(ret.msg, 1);}, 'json');
            });

            function makearray(obj){
                var a = new Array();
                $.each($(obj), function(i, n){
                    a.push($(this).val());
                });
                return a;
            };

            $('#edititems').click(function(){layer.pop('?m=Project&a=PopFacadeItemsEdit&id=62','编辑项目');});

            var cols = <?php echo $cols; ?>;
            var arr = new Array();
            for(var i=0;i<cols;i++) arr.push(i);
            $('#facade').mergeCell({cols: arr});

        $('#facadebox').on('blur', '.item1', function(){
            $.each($('.item1'), function(i, n){
                var v=$(this).val();
                if(v != '' && isNaN(v)){
                    layer.msg('条目序号'+(i+1)+'请输入正确的标准分', 2, -1);
                    return false;
                }
            });
            totals();
        });

        $('#facadebox').on('blur', '.item4', function(){
            $.each($('.item4'), function(i, n){
                var v=$(this).val();
                if(v != '' && isNaN(v)) {
                    layer.msg('条目序号' + (i + 1) + '请输入正确的得分', 2, -1);
                    return false;
                }
            });
            totals();
        });

        function totals(){
            var v1 = 0;
            var v4 = 0;

            var x = 0;
            var c = 0;
            $.each($('.item1'), function(i, n){
                var v = $(this).val();
                var cata = $(this).attr('cata');
                if(v != '' && !isNaN(v)) {
                    v1 += (v * 1);
                    if(cata!=c){
                        x=0;
                        c=cata;
                    }
                    x += (v * 1);
                    $('#cata1'+c).val(x);
                }
            });

            var x = 0;
            var c = 0;
            $.each($('.item4'), function(i, n){
                var v = $(this).val();
                var cata = $(this).attr('cata');
                if(v != '' && !isNaN(v)) {
                    v4 += (v * 1);
                    if(cata!=c){
                        x=0;
                        c=cata;
                    }
                    x += (v * 1);
                    $('#cata4'+c).val(x);
                }
            });

            $('#amount1').val(v1);
            $('#amount2').val(v4);
            var v = v1+v4;
            if(v==0) $('#amount3').val('0');
            else $('#amount3').val(((v4/v1)*100).toFixed(2));
            }
        });

        var rand = '';
        function checkchange(){
            if($('#rand').val() != rand){
                rand = $('#rand').val();
                var cols = parseInt($('#cols').val());
                var arr = new Array();
                for(var i=0;i<=cols;i++) arr.push(i);
                $('#facadebox table').mergeCell({cols: arr});
            }
        }
        setInterval('checkchange()',500);
    </script>
