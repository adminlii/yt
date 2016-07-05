<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<style>
*{margin:0; border:0; padding:0; font-family:"微软雅黑","宋体"; font-size:14px;}

#warp{margin:auto;width:1006px;}
.warpdiv{margin:auto;width:500px;page-break-inside:avoid;}
</style>	
</head>
<body>	
	<{foreach from=$data  name=o item=o}>
	<div class="warpdiv">
		<{section name=loop loop=$o.boxnum}>
		<div style="width:100%;height:30px;overflow:hidden;margin-bottom:3px;    margin-top: 50px;">
				<img src="/default/index/barcode1/code/<{$o.shipper_hawbcode}><{$smarty.section.loop.index+1}>"/>
			</div>
			<div style="width:100%;margin-left: 26px;margin-bottom: 20px;">
				<b><{$o.shipper_hawbcode}><{$smarty.section.loop.index+1}></b>
			</div> 
		<{/section}> 
			
	</div>		
	<{/foreach}>	
</body>
</html>			