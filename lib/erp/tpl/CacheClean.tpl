<script type="text/javascript">
$(function(){
    $('#cleansys').click(function(){
		layer.confirm('您确认需要清空系统缓存吗?', function(i){layer.close(i);$.post('?m=System&a=OnCacheCleanSys', {}, function(data){if(data==1)location.href='<?php echo Url::REQUEST_URI(); ?>';else layer.msg(data, 2, -1);});});
    });
    
    $('#cleanuser').click(function(){
		layer.confirm('您确认需要清空全部用户缓存吗?', function(i){layer.close(i);$.post('?m=System&a=OnCacheCleanUser', {}, function(data){if(data==1)location.href='<?php echo Url::REQUEST_URI(); ?>';else layer.msg(data, 2, -1);});});
    });
});
</script>
<div class="cap"><div class="capl">清理缓冲区</div></div>
<br />
<input type="button" id="cleansys" class="badd" value="清空系统缓存" />
<br /><br />
<input type="button" id="cleanuser" class="badd" value="清空用户缓存" />