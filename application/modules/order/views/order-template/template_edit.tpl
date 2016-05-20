<script type="text/javascript">
<{include file='order/js/order-template/template_upload.js'}>
</script>
<script type="text/javascript">
<{include file='order/js/order-template/template_edit.js'}>
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

.report_fmbox_lft {
	float: left;
	height: 400px;
	overflow-y: scroll;
	width: 320px;
}

.report_fmbox_rit {
	float: left;
	height: 400px;
	overflow-y: scroll;
	width: 320px;
}

#module-container {
	width: 800px;
}

.msg {
	color: red;
	padding: 0 5px;
}

#saveTemplateBtn {
	width: 80px;
	cursor: hand;
	float: right;
	margin: -30px 20px 0 0;
}

.report_form td {
	line-height: 20px;
	padding: 8px 4px;
}
-->
</style>
<div id="module-container">
	<div class="Tab">
		<ul>
			<li id="normal" style="position: relative;" class="mainStatus ">
				<a href="/order/order-template/upload" class="statusTag ">
					<span class="order_title"><{t}>新增模板<{/t}></span>
				</a>
			</li>
			<li id="abnormal" style="position: relative;" class="mainStatus chooseTag">
				<a href="/order/order-template/edit" class="statusTag ">
					<span class="order_title"><{t}>修改模板<{/t}></span>
				</a>
			</li>
		</ul>
	</div>
	<table cellspacing="0" cellpadding="0" width="100%" bordercolor="#e1e1e1" border="0" bgcolor="#FFFFFF" class="report_form ">
		<tbody>
			<tr>
				<td width="12%" class="main_right1_7">
					<div align="right">客户订单格式：</div>
				</td>
				<td width="25%">
					<div align="left">
						<select style="width: 190px;" id="customerReport">
							<{foreach from=$reportArr name=o item=o key=k}>
							<option value="<{$o.report_id}>"><{$o.report_filename}></option>
							<{/foreach}>
						</select>
					</div>
				</td>
				<td width="14%">
					<input type="button" style="float: right; margin-right: 8px; cursor: hand;" class="report_cx_btn" id="deleteTemplateBtn" value="<{t}>删除模板<{/t}>">
				</td>
				<td width="20%"></td>
				<td width="14%"></td>
				<td width="15%"></td>
			</tr>
		</tbody>
	</table>
	<div id='edit_wrap'></div>
</div>