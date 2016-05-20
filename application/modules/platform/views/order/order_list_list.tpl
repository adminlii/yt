
<script src="/js/jquery-ui-timepicker-addon.js" type="text/javascript"></script>
<script src="/js/jquery.overlay.min.js" type="text/javascript"></script>
<script type="text/javascript">
<{include file='platform/js/order/order_list_list_common.js'}>
<{include file='platform/js/order/order_list_list.js'}>
</script>
<style>
<!--
.opDiv .baseBtn {
	margin: 3px 5px 5px;
}

.statusTag .count {
	color: red;
	padding: 0 3px;
}

.dialog_div {
	display: none;
}

.chooseTag {
	font-weight: bold;
	font-size: 14px;
	color: #000;
}

.datepicker {
	background-color: #eee;
}
.logIcon,.loadLogIcon {
	background: url(/images/sidebar/bg_mailSch.gif) center center;
	display: none;
	height: 16px;
	vertical-align: middle;
	white-space: nowrap;
	width: 18px;
}
.ellipsis {
	display: block;
	width: 100%; /*对宽度的定义,根据情况修改*/
	overflow: hidden;
	white-space: nowrap;
	text-overflow: ellipsis;
}

.ellipsis:after {
	/*content: "...";*/
}
.load_order_list_msg{list-style-type: decimal;padding-left:30px;}
 
-->
</style>
<!-- 订单审核 -->
<div id='verify_div_verify' title='<{t}>生成标准订单<{/t}>' class='dialog_div'>
	<form action="" id='verify_div_form' method='POST' onsubmit='return false;'>
		<h2 style="padding: 0px 0 0 0; margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC; line-height: 30px;">
			<span class=" web_require">1、<{t}>运输方式<{/t}></span>
			<span class="msg">*</span>
			<span style="font-weight: normal; font-size: 12px;">未分配运输方式的订单,将设置为该运输方式</span>
		</h2>
		<select id="product_code" name="product_code" class="input_select product_code web_require" style=''>
		</select>
		<div>
			<h2 style="padding: 15px 0 0 0; margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC;">
				<span class="submiter_wrap web_require">2、<{t}>发件人信息<{/t}></span>
				<span class="msg">*</span>
				<a onclick="getSubmiter();" style="font-weight: normal; font-size: 12px; padding: 0 10px;" href="javascript:;"><{t}>刷新<{/t}></a>
			</h2>
			<div id="submiter_wrap" style="line-height: 22px;">
				<div class="shipper_account"></div>
				<div>
					<a shipper_account="" class="submiterOpBtn" style="font-weight: normal; font-size: 12px; line-height: 35px; margin: 0 0px;" href="javascript:;" onclick="submiterOp(this);"><{t}>点击添加<{/t}></a>
				</div>
			</div>
		</div>
	</form>
</div>

<!-- 平台SKU申报信息映射 -->
<div id='product_relation_div' title='<{t}>平台SKU申报信息映射<{/t}>' class='dialog_div'>
	
</div>
<!-- 拉单 -->
<div id='load_order_div' title='<{t}>手工拉单<{/t}>' class='dialog_div'>
	<form action="" id='load_order_form' method='POST' onsubmit='return false;'>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="table-module">
			<tbody>
				<tr class="table-module-b1">
					<td class="ec-center">账号</td>
					<td class="">
						<select name="user_account" class="input_text_select">
							<option value=''><{t}>-select-<{/t}></option>
							<{foreach from=$user_account_arr name=ob item=ob}>
							<option value='<{$ob.user_account}>'><{$ob.platform}> [<{$ob.user_account}>] [<{$ob.platform_user_name}>]</option>
							<{/foreach}>
						</select>
					</td>
				</tr>
				<tr class="table-module-b1">
					<td class="ec-center">平台订单更新时间</td>
					<td class="">
						<input type="text" class="input_text datepicker" value="" name="start_time" style='width: 120px;' readonly>
						~
						<input type="text" class="input_text datepicker" value="" name="end_time" style='width: 120px;' readonly>
					</td>
				</tr>
			</tbody>
		</table>
		<p style='color: red;line-height:30px;'>时间间隔需小于<{$load_platform_order_day}>天</p>
	</form>
</div>
<div id='biaojifahuo_div' title='标记发货' class='dialog_div'>
	<form enctype="multipart/form-data" method="POST" action="" onsubmig='return false;' id='biaojifahuo_form'>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td class="ec-center">订单号</td>
					<td class="ec-center">目的国家</td>
					<td class="ec-center">承运商(标记运输方式)</td>
					<td class="ec-center">跟踪号</td>
				</tr>
			</tbody>
			<tbody id='order_list_wrap'>
			</tbody>
		</table>
	</form>
</div>

<div id='allot_div' title='分配运输方式' class='dialog_div'>
	<form enctype="multipart/form-data" method="POST" action="" onsubmig='return false;' id='allot_form'>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td class="ec-center">订单号</td>
					<td class="ec-center">目的国家</td>
					<td class="ec-center">平台运输方式</td>
					<td class="ec-center">运输方式</td>
				</tr>
			</tbody>
			<tbody id='allot_list_wrap'>
			</tbody>
		</table>
	</form>
</div>
<div id="module-container" style=''>
	 <{include file='platform/views/order/order_list_list_b2c.tpl'}>
	<div id="module-table" style='overflow: auto;'>
		<div class="Tab">
			<ul>
				<{foreach from=$statusArr item=status name=status key=k}>
				<li style='position: relative;' class='mainStatus'>
					<a href="javascript:void (0)" class="statusTag <{if $k!=''}>statusTag<{$k}><{else}>statusTagAll<{/if}>" status='<{$k}>'>
						<span class='order_title<{$k}>' title='<{$status.name}>'><{$status.name}></span>
						<span class='count'></span>
					</a>
				</li>
				<{/foreach}>
			</ul>
			<div id="" class="pageTotal" style="text-align: right; float: right; line-height: 25px; padding: 0 5px;"></div>
		</div>
		<div class="opration_area" style="margin-left: 0px; clear: both; height: auto;">
			<div class='opDiv'></div>
		</div>
		<form action="" id='listForm' method='POST'>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
				<tbody id='table-module-list-header'>
					<!-- 通过js复制表头到此处 -->
					<tr class="table-module-title">
						<td width="15px" class="ec-center">
							<input type="checkbox" class="checkAll">
						</td>
						<td width='185px' class="ec-center order_info_td"><{t}>order_info<{/t}></td>
						<!-- 订单信息 -->
						<td class="ec-center"><{t}>order_detail<{/t}></td>
						<!-- 订单明细 -->
						<td width='125px' class="ec-center"><{t}>order_amount<{/t}></td>
						<!-- 订单金额 -->
						<td width='165px' class="ec-center delivery_info_td"><{t}>delivery_information<{/t}></td>
						<!-- 配送信息 -->
						<td width='130px' class="ec-center date_info_td"><{t}>date<{/t}></td>
						<!-- 时间 -->
					</tr>
				</tbody>
				<tbody id="table-module-list-data">
					<!-- 
					
					 -->
				</tbody>
			</table>
		</form>
		<div class="pagination"></div>
	</div>
</div>