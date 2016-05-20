<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<style>
*{padding:0;margin:0;font-size:14px;text-align:center;}
div{margin:0 auto;font-weight:bold;}
.d6{width:6.6cm;margin:0 auto;height:20px;line-height:20px;overflow:hidden;}
</style>
</head>
<body>
<div style="width:7cm;height:1cm;overflow:hidden;margin-top:10px;">
	<img class="i1" src="/default/index/barcode?code=<{$row.product_barcode}>" style=""/>
</div>
<div class="d3" style="background:#fff;margin-top:0px;"><{$row.product_barcode}></div>
<div class="d6" style=""><{$row.pc_name}></div>
</body>
</html>
