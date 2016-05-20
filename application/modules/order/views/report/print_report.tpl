<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<!-- 
<meta http-equiv="x-ua-compatible" content="ie=7" />
 -->
<title>打印</title>
<script type="text/javascript" src="/js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui.min.js"></script>
<style type="text/css">
*{margin:0;padding:0;text-align:center;}
#wrap{margin:0 auto;}
.png,.pdf{width:100%;}
<{if $LablePaperType==1||$LablePaperType=='1'}>
.png{width:8cm;height:auto;}
.pdf{width:10cm;height:10cm;}
<{else}>
.png{width:auto;height:285mm;}
.pdf{width:208mm;height:208mm;}
<{/if}>
</style>
</head>
<body>
    <div id='wrap'>
	<{foreach from=$lableArr item=o name=o}> 
	<div class='obj_wrap' style='<{if !$smarty.foreach.o.last}>page-break-after: always;<{/if}>'>
	<{if $LableFileType=='1'||$LableFileType==1}>
	<img src='data:image/png;base64,<{$o.LableFile}>' class='png' /> 
	<{else}>
	<object type="application/pdf" data="data:application/pdf;base64,<{$o.LableFile}>" class='pdf' width='100%' height='500'></object>
	<{/if}> 
	</div>
	<{/foreach}>
	</div>
</body>
</html>