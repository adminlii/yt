<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<style>
*{margin:0; border:0; padding:0; font-family:"微软雅黑","宋体"; font-size:14px;}

#warp{margin:auto;width:1006px;}
.warpdiv{margin:auto;width:500px;}
</style>	
</head>
<body>	
<div id="warp">
	<{foreach from=$data  name=o item=o}>
	<div class="warpdiv">
			<div style="width:100%;height:30px;overflow:hidden;margin-bottom:3px;">
				<img src="/default/index/barcode1/code/<{$o.shipper_hawbcode}>"/>
			</div>
			<div style="width:100%; margin-left: 14px;">
				<b><{$o.shipper_hawbcode}></b>
			</div>
	</div>		
	<{/foreach}>
</div>	
</body>
</html>			