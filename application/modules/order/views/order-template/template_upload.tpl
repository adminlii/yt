<script type="text/javascript">
<{include file='order/js/order-template/template_upload.js'}>
</script>
<style>
<!--
.fill_in {
	display: none;
}

.table-module tr {
	cursor: pointer;
}

.table-module .selected td {
	background: none repeat scroll 0 0 #cccccc;
}

.data_wrap,.data_wrap_title {
	width: 10000px;
}

.data_wrap {
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

.data_wrap input {
	width: 80px;
}

.msg {
	color: red;
	padding: 0 5px;
}

#saveTemplateBtn {
	width: 80px;
	cursor: hand;
	float: right;
	margin: -40px 20px 0 0;
}
-->
</style>
<div id="module-container">
	<div class="Tab">
		<ul>
			<li id="normal" style="position: relative;" class="mainStatus chooseTag">
				<a href="/order/order-template/upload" class="statusTag ">
					<span class="order_title"><{t}>新增模板<{/t}></span>
				</a>
			</li>
			<li id="abnormal" style="position: relative;" class="mainStatus ">
				<a href="/order/order-template/edit" class="statusTag ">
					<span class="order_title"><{t}>修改模板<{/t}></span>
				</a>
			</li>
		</ul>
	</div>
	<div id="module-table" style='padding: 10px 20px;'>
		<h2 style='margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC;'><{t}>选择文件上传<{/t}></h2>
		<form enctype="multipart/form-data" method="POST" action="/order/order-template/upload" name="form" id='uploadForm'>
			<input type='file' class='input_text baseBtn' id='fileToUpload' name='fileToUpload'>
			<input type='button' class='submitBtn' id='uploadBtn' value='<{t}>模板上传<{/t}>'>
			<span style='padding: 0 20px;'><{if $file_name}><{$file_name}><{/if}></span>
		</form>
	</div>
	<{if $userTemplate}> <{include file='order/views/order-template/template_edit_form.tpl'}> <{/if}>
</div>