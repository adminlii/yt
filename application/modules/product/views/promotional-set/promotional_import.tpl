<link type="text/css" rel="stylesheet" href="/css/public/ajaxfileupload.css" />
<script type="text/javascript" src="/js/ajaxfileupload.js"></script>
<script type="text/javascript">

function ajaxFileUpload()
{
	var param = $('#module-table form').serialize();
	$.ajaxFileUpload
	(
		{
			url:'/product/promotional-set/import?'+param, 
			secureuri:false,
			fileElementId:'fileToUpload',
			//data : param,
			dataType: 'json',
			success: function (json, status)
			{
				if(json.ask){
					initData(0);
					$("#uploadDiv").dialog('close');
					alert(json.message);
				}else{
					alert(json.message);
				}
			},
			error: function (data, status, e)
			{
				//alert(e);
			}
		}
	)	
	return false;
}  

</script>
<style>
<!--
.fill_in {
	display: none;
}
-->
</style>
<form enctype="multipart/form-data" method="POST" action="" name="form">
	<input type='file' class='input_text' id='fileToUpload' name='fileToUpload'>
	<input type='button' class='' onclick='return ajaxFileUpload();' value='Upload'>
</form>
<div class="search-module-condition">
	<{t}>notice<{/t}>：<{t}>only_xls<{/t}><!-- 注意 -->
	&nbsp;&nbsp;&nbsp;&nbsp;
	<a target="_blank" href="/file/promotion.xls" style='color:red;'><{t}>download_template<{/t}></a><!-- 下载模板 -->
</div>
<div class="search-module-condition">1、<{t}>discount_tips_01<{/t}></div><!-- StartTime,EndTime为eBay时间，时间格式为2013-04-15或2013-04-15 12:12 -->
<div class="search-module-condition">2、<{t}>discount_tips_02<{/t}></div><!-- eBay时间比北京时间慢8个小时，请注意个站点间的时差(如：德国比eBay时间快2个小时) -->
<div class="search-module-condition">3、<{t}>data_parity_error_import_the_data_will_all_fail<{/t}></div><!-- 当文件中数据存在异常时，全部的数据导入都会失败！ -->