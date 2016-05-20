<link type="text/css" rel="stylesheet" href="/css/public/ajaxfileupload.css" />
<script type="text/javascript" src="/js/ajaxfileupload.js"></script>
<script type="text/javascript">
<{include file='order/js/order/order_import.js'}>
</script>
<style>
<!--
.fill_in {
	display: none;
}
.table-module tr{cursor:pointer;}
.table-module .selected td{background:none repeat scroll 0 0 #cccccc;}
.data_wrap,.data_wrap_title {
	width: 10000px;
}
.data_wrap{
	display: none;	
}

.chooseTag {
	background: #2283c5;
	font-weight: bold;
}

.Tab ul li.mainStatus {
	margin: 0 0px 0 0;
	padding: 0 30px;
}

.mainStatus {
	cursor: pointer;
}
.data_wrap input{width:80px;}
#file_name{padding:0 8px;display:none;}
.dialog_div{display:none;}
.module-title{text-align:right;}
-->
</style>
<div id="module-container">
	<div class="Tab">
		<ul>
			<li id="normal" style="position: relative;" class="mainStatus0 ">
				<a href="javascript:;" class="statusTag " onclick="leftMenu('52','<{t}>单票录入<{/t}>','/order/order/create?quick=52')">
					<span class="order_title"><{t}>单票录入<{/t}></span>
				</a>
			</li>
			<li id="abnormal" style="position: relative;" class="mainStatus0 chooseTag">
				<a href="javascript:;" class="statusTag " onclick="leftMenu('24','<{t}>批量上传<{/t}>','/order/order/import?quick=24')">
					<span class="order_title"><{t}>批量上传<{/t}></span>
				</a>
			</li>
			<li id="abnormal" style="position: relative;" class="mainStatus0">
				<a href="javascript:;" class="statusTag " onclick="leftMenu('get-import-batch','<{t}>上传记录<{/t}>','/order/order/get-import-batch?quick=0')">
					<span class="order_title"><{t}>上传记录<{/t}></span>
				</a>
			</li>
		</ul>
	</div>
	<div id="module-table" style='padding: 10px 20px;'>
		<h2 style='margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC;'>
			1、<{t}>下载模板并填写<{/t}>
			<span style="font-weight: normal; font-size: 12px; padding: 0 15px;">(若不需要，请略过)</span>
		</h2>
		<div style='padding: 10px;'>
			<{foreach from=$baseTemplate item=o name=o}>
			<a class="baseBtn submitToSearch" href='<{$o.report_file_path}>' target='_blank'><{$o.report_filename}></a>
			<{/foreach}>
			 
			<!--
			<a class="baseBtn submitToSearch" href='/file/小包模板.xls' target='_blank'><{t}>小包模板<{/t}></a>
			<a class="baseBtn submitToSearch" href='/file/标准模板.xls' target='_blank'><{t}>标准模板<{/t}></a>
			
			<a class="baseBtn submitToSearch" href='/file/normal_order3.xls' target='_blank'><{t}>标准模板3<{/t}></a> 
			
			<a class="  viewProductKindBtn" href='javascript:;' style='margin-left:10px;'><{t}>产品信息<{/t}></a>
			<a class="  viewRelationBtn" href='javascript:;' style='margin-left:10px;'><{t}>运输方式&目的国家&附加服务关系<{/t}></a>
			-->
		</div>
		<h2 style='margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC;'>
			2、<{t}>选择发件人资料<{/t}>
			<span style="font-weight: normal; font-size: 12px; padding: 0 15px;">(如果上传的资料中存在发件人信息，则按上传资料中保存，如果文件中没有，则按勾选的保存)</span>
			<a href='javascript:;' style='font-weight: normal; font-size: 12px; padding: 0 10px;' onclick='getSubmiter();'><{t}>刷新<{/t}></a>
		</h2>
		<div style='line-height: 22px;padding: 10px;' id='submiter_wrap'></div>
		<h2 style='margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC;'>
			3、<{t}>选择填好的文件上传<{/t}>
			<span style="font-weight: normal; font-size: 12px; padding: 0 15px;"><input type="checkbox" name="ansych" id="ansych" value="1"/> 只提交文件(内容超过1000条时勾选)</span>
    	</h2>	    
		<form enctype="multipart/form-data" method="POST" action="" name="form" id='uploadForm' onsubmit='return false;'>
			<label for='fileToUpload' style='padding:0;margin:0;display:none;'><input  type='button' class='baseBtn' value='<{t}>选择文件<{/t}>' style='margin-top:-4px;'/></label>
			<span id='file_name'></span>
			<input type='file' class='input_text fileToUpload' id='fileToUpload' name='fileToUpload' style=''>
			<input type='button' class='submitBtn' value='<{t}>确认上传(数据提交)<{/t}>' id='submitBtn'>
			<div id='file_upload_content_wrap'>
				<{include file='order/views/order/order_import_file_content.tpl'}>				
			</div>
		</form>
	</div>
</div>
<div class='dialog_div' title='<{t}>批量设置运输方式<{/t}>' id='set_product_code_div'>
<!-- 运输方式种类 -->
<{$productCount = $productKind|count}>
<select id='product_code_set'  class="input_select ">
    <!-- 多个运输方式的时候，提供请选择选项 -->
    <{if $productCount>1}>
	<option value='' class='ALL'><{t}>-select-<{/t}></option>
	<{/if}>
	<{foreach from=$productKind item=c name=c}>
	<option value='<{$c.product_code}>'><{$c.product_code}> [<{$c.product_cnname}>  <{$c.product_enname}>]</option>
	<{/foreach}>
</select>
</div>

<div class='dialog_div' title='<{t}>批量添加单号前缀<{/t}>' id='add_prefix_div'>
    <input value="" id='prefix_set' class="input_text">
</div>
<div class='dialog_div' title='<{t}>运输方式&目的国家&附加服务关系<{/t}>' id='shipping_method_country_extra_service_div'>
<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module" style='margin-top: 10px;'>
	<tbody class="table-module-list-data">
		<tr class=''>
			<td width="150" class="module-title"><{t}>运输方式<{/t}>：</td>
			<td>
				<select class='input_select product_code' name='order[product_code]' default='<{if isset($order)}><{$order.product_code}><{/if}>' id='product_code'>
					<option value='' class='ALL'><{t}>-select-<{/t}></option>
					<{foreach from=$productKind item=c name=c}>
					<option value='<{$c.product_code}>'><{$c.product_code}> [<{$c.product_cnname}>  <{$c.product_enname}>]</option>
					<{/foreach}>
				</select>
				<span class="msg">*</span>
			</td> 
		</tr>		
		<tr class=''>
			<td width="150" class="module-title"><{t}>consignee_country<{/t}>：</td>
			<td>
				<select class='input_select country_code' name='order[country_code]' default='<{if isset($order)}><{$order.country_code}><{/if}>' id='country_code' style='width:400px;max-width:400px;'>
					<option value=''  class='ALL'><{t}>-select-<{/t}></option>
					<!-- 
					<{foreach from=$country item=c name=c}>
					<option value='<{$c.country_code}>' country_id='<{$c.country_id}>' class='<{$c.country_code}>'><{$c.country_code}> [<{$c.country_name}>  <{$c.country_name_en}>]</option>
					<{/foreach}>
					 -->
				</select>
				
				<span class="msg">*</span>
			</td>
		</tr>
		<tr class=''>
			<td class="module-title"><{t}>附加服务<{/t}>：</td>
			<td>
				<span class='product_extraservice_wrap' id='product_extraservice_wrap'>
 							<!-- 
 							<label><input type='checkbox' class='' value='A1' name='extraservice[]' /><{t}>购买保险<{/t}></label> 
 							<label><input type='checkbox' class='' value='11' name='extraservice[]' /><{t}>支持退件<{/t}></label>
 							 -->
				</span>
				
				 <!-- -->
				 <span class="msg"></span>
			</td>
		</tr>		
	</tbody>
</table>
</div>