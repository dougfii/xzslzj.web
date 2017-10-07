<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta content="IE=EmulateIE7" http-equiv="X-UA-Compatible" />
<style type="text/css">
*{margin:0;padding:0;}
body{background:#fff;font-size:12px;}
</style>
</head>
<body>
<div class="bodypage">
<?php
if(!$do):
?>
<form action="" method="post" enctype="multipart/form-data" name="upload_file"> 
文件：<input type="file" name="upfile" /> <input type="submit" value="上传" />
<input type="hidden" id="m" name="m" value="Upload" />
<input type="hidden" id="do" name="do" value="1" />
<input type="hidden" id="format" name="format" value="<?php echo $format; ?>" />
<input type="hidden" id="field" name="field" value="<?php echo $field; ?>" />
<input type="hidden" id="dest" name="dest" value="<?php echo $dest; ?>" />
<input type="hidden" id="append" name="append" value="<?php echo $append; ?>" />
</form>
<?php
else:
	if($state):
		echo '<script language="JavaScript" type="text/javascript">'."\n";
		echo 'var parDoc = window.parent.document;';
		if($append):
			echo 'parDoc.getElementById("'.$field.'").value = parDoc.getElementById("'.$field.'").value + \'\n\' + "'.$state.'";';
		else:
			echo 'parDoc.getElementById("'.$field.'").value = "'.$state.'";';
		endif;
		echo "\n".'</script>';
	endif;
	echo $tips . '　<a href="#" onclick="history.back()">[返回]</a>';
endif;
?>
</div>
</body>
</html>