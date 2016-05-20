<link type="text/css" rel="stylesheet" href="/css/public/ajaxfileupload.css" />
<script type="text/javascript" src="/js/ajaxfileupload.js"></script>
<script type="text/javascript">
function ajaxFileUpload()
{	
	//'数据上传中....'
	alertTip($.getMessage('sys_common_wait'),640);
	$.ajaxFileUpload
	(
		{
			url:$('#upload_form').attr('action'), 
			secureuri:false,
			fileElementId:'fileToUpload',
			dataType: 'json',
			success: function (json, status)
			{				
				$('#dialog-auto-alert-tip').dialog('moveToTop');
				var html = json.message+"<br/>";
				$.each(json.err,function(k,v){
				    html+='<p>'+v+'</p>';
				})
				$('#dialog-auto-alert-tip p').html(html);
				if(json.ask && $('#uploadDiv').size()>0){
				    $('#uploadDiv').dialog('close');
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

$(function(){


	
});
</script>
<form method='post' enctype="multipart/form-data" action='/product/product-combine-relation-invoice/upload' id='upload_form' onsubmit='return false;'>
	<div class="search-module-condition">
		<{t}>upload_file<{/t}><!-- 上传文件 -->：
		<input type="file" name="fileToUpload" id="fileToUpload" class="input_text" />
		<input type="button" onclick='return ajaxFileUpload();' value="<{t}>confirm_upload<{/t}>"  /><!-- 确认上传 -->
	</div>
	<div class="search-module-condition" style="padding-left: 60px;">
		
	</div>
</form>
<div class="search-module-condition">
	<{t}>p_notice<{/t}>：<{t}>only_xls<{/t}><!-- 仅支持 xls 文件 -->&nbsp;&nbsp;&nbsp;&nbsp;
	<a href="/file/Product_relation.xls" target="_blank" style='color:red;'><{t}>download_template<{/t}><!-- 模板下载 --></a>
</div>
<div class="search-module-condition">1、<{t}>data_parity_error_import_the_data_will_all_fail<{/t}><!-- 当文件中数据存在异常时，全部的数据导入都会失败！ --></div> 
<div class="search-module-condition">2、<{t}>sku_correspondence_between_the_price_ratio<{/t}><!-- 价格比例为单个产品在整个组合产品中所占的比例 --></div> 
<div class="search-module-condition">3、<{t}>data_format_reference<{/t}><!-- 数据格式请参考模板 --></div>

 	
     