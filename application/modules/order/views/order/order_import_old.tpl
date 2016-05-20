<link type="text/css" rel="stylesheet" href="/css/public/ajaxfileupload.css" />
<script type="text/javascript" src="/js/ajaxfileupload.js"></script>
<script type="text/javascript">

function ajaxFileUpload()
{
	var param = $('#module-table form').serialize();
	$.ajaxFileUpload
	(
		{
			url:'/order/order/import?'+param, 
			secureuri:false,
			fileElementId:'fileToUpload',
			//data : param,
			dataType: 'json',
			success: function (json, status)
			{
				var html = '';
				html+=json.message;
				if(json.ask){
					
				}else{
				    if(json.errs){
				        $.each(json.errs,function(k,v){
				            html+='<p>第'+(k)+'行</p>';
				            //html+='<p>&nbsp;&nbsp;&nbsp;&nbsp;'+v.join(',')+'</p>';
				            $.each(v,function(kk,vv){
					            html+='<p>&nbsp;&nbsp;&nbsp;&nbsp;'+vv+'</p>';
				            })
				        })
				    }				
				}
				alertTip(html,500,500);
			},
			error: function (data, status, e)
			{
				//alert(e);
			}
		}
	)
	
	return false;

}  
function clazzInit(){
	$('#table-module-list-data').each(function(){
		$("tr",this).removeClass('table-module-b1').removeClass('table-module-b2');
		$("tr:even",this).addClass('table-module-b1');
		$("tr:odd",this).addClass('table-module-b2');
	}); 
}
</script>
<style>
<!--
.fill_in {
	display: none;
}
-->
</style>
<div id="module-container">
	<div id="module-table">
		<form enctype="multipart/form-data" method="POST" action="" name="form">			
			<div class="step_rowTitle"><{t}>order_batch_upload<{/t}></div>
			<table cellspacing="0" cellpadding="0" border="0" class="table-module" style='margin-top: 10px;width: 100%;'>
				<tbody class="table-module-list-data">
					<tr class="table-module-b2">
						<td class="dialog-module-title" width='150'><{t}>xls_file<{/t}>:</td>
						<td>
							<input type='file' class='input_text baseBtn' id='fileToUpload' name='fileToUpload'>
							<input type='button' class='submitBtn' onclick='return ajaxFileUpload();' value='Upload'>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<a target="_blank" href="/file/order.xls">《<{t}>template<{/t}>》</a>
						</td>
					</tr>
					<tr class="table-module-b1">
						<td class="dialog-module-title" width='150'></td>
						<td>
							<b style="color:#E06B26;"><{t}>please_note<{/t}></b>
							<br>
							<div style='padding-left: 5px;'>
								<{t}>order_file_content_illegal_01<{/t}>
							</div>
							<br>
							<b style="color:#E06B26;"><{t}>kind_tips<{/t}></b>
							<br>
							<div style='padding-left: 5px;'>
								<{t}>order_file_content_illegal_02<{/t}>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
</div>