<html>
<head>
<title>Print</title>
<meta charset="utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<link href="css/print.css" rel="stylesheet" type="text/css"/>
<object id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0>
	<embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0 pluginspage="/install_lodop32.exe"></embed>
</object>
<script src='http://localhost:8000/CLodopfuncs.js'></script>
<script language="javascript" type="text/javascript">
	function Printer(preview){
		//var strHTML=document.getElementsByTagName("html")[0].innerHTML;
		var strHTML="<!DOCTYPE html>"+document.getElementById("print_area").innerHTML;
//			LODOP.PRINT_INITA(10,20,810,610,"测试C-Lodop远程打印四步骤");
		//LODOP.SET_PRINTER_INDEXA(document.getElementById("Select01").value);
//			//LODOP.SET_PRINT_PAGESIZE(0,0,0,document.getElementById("Select02").value);
//			LODOP.ADD_PRINT_TEXT(1,1,300,200,"下面输出的是本页源代码及其展现效果：");
//			LODOP.ADD_PRINT_TEXT(20,10,"90%","95%",strHTML);
//			LODOP.SET_PRINT_STYLEA(0,"ItemType",4);
//			LODOP.NewPageA();
		LODOP.ADD_PRINT_HTM(20,10,"90%","95%",strHTML);
		if (preview)
			LODOP.PREVIEW();
		else
			LODOP.PRINT();
	}

	function demoCreatePrinterList(){
		CLODOP.Create_Printer_List(document.getElementById('Select01'));
		demoCreatePagSizeList();
	}

	function demoCreatePagSizeList(){
		var oSelect=document.getElementById('Select02');
		var iPrintIndex=document.getElementById("Select01").value;
		CLODOP.Create_PageSize_List(oSelect,iPrintIndex);
	}
	function demoCreateCLodopJSscript(strSrc){
		var ScriptSS=document.getElementsByTagName("script");
		for(var i in ScriptSS){
			if (ScriptSS[i].src && (ScriptSS[i].src.indexOf("CLodopfuncs.js")>=0)) {
				if (ScriptSS[i].parentNode) ScriptSS[i].parentNode.removeChild( ScriptSS[i] );
			};
		};
		var oscript=document.createElement("script");
		if (strSrc.indexOf("=")>=0) {strSrc=strSrc.match(/=[\',\"][^\',^\"].*(?=[\',\"])/i);strSrc=strSrc[0].slice(2);}
                         oscript.src=strSrc;
                         var head = document.head || document.getElementsByTagName("head")[0] || document.documentElement;
                         head.insertBefore(oscript,head.firstChild);
                         return oscript;
        };
        function demoSetClodopJS(strSrc){
                 var oscript=demoCreateCLodopJSscript(strSrc);
                 oscript.onload =oscript.onreadystatechange= function() {
                         if ((!oscript.readyState|| /loaded|complete/.test(oscript.readyState)))
                         demoCreatePrinterList();
                 };
        };

        demoSetClodopJS('http://localhost:8000/CLodopfuncs.js');

</script>
</head>
<body>
<div class="print_tool toolbar">
	<a href="javascript:Printer(true);">打印预览</a><a href="javascript:Printer();">直接打印</a> 打印机:<select id="Select01" size="1" onchange="demoCreatePagSizeList()"></select> 纸张类型:<select id="Select02" size="1"></select></a>
</div>