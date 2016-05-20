<script type="text/javascript" src="/js/xheditor/xheditor-1.1.13-en.min.js"></script>
<script type="text/javascript" src="/js/ajaxfileupload.js"></script>
<script src="/js/jquery-ui-timepicker-addon.js" type="text/javascript"></script>
<script src="/js/jquery.overlay.min.js" type="text/javascript"></script>
<script type="text/javascript">
<{include file='order/js/order/transfer_order_list.js'}>
</script>

<style>
<!--
#module-container .guild h2 {
	cursor: pointer;
	background: none repeat scroll 0% 0% transparent;
}

#module-container .guild h2.act {
	cursor: pointer;
	background: none repeat scroll 0% 0% #fff;
}

#search_more_div {
	display: none;
}

#search_more_div div {
	padding-top: 8px;
}

.input_text {
	width: 150px;
}
#priceFrom,#priceEnd{width:135px;}
.fast_link {
	padding: 8px 10px;
}

.fast_link a {
	margin: 5px 8px 5px 0;
}

.order_detail {
	width: 100%;
	border-collapse: collapse;
}

.order_detail td {
	border: 1px solid #ccc;
}

.opDiv .baseBtn {
	margin-right: 10px;
}

#tag_form_div {
	display: none;
}

#tag_form {
	position: relative;
}

#biaojifahuo_div {
	display: none;
}

.baseExport {
	float: right;
}

#tag_select ul {
	line-height: 22px;
}

.deleteTag {
	float: right;
}
.Tab ul li {
    padding: 0 0px;
}
.Tab ul li li {
	background: none repeat scroll 0 0 #FFFFFF;
	border: 0 none;
	float: none;
	height: 26px;
	line-height: 26px;
}

.define_item {
	display: none;
}

.dialog_div {
	display: none;
}

.set_it_for_allot {
	display: none;
}

#allot_condition_div p {
	line-height: 22px;
	width: 300px;
}

.del_oac_btn {
	
}

.pagination {
	text-align: right;
	height: auto;
}

#aaaaaaaaaaa td {
	height: 1px;
	line-height: 1px;
	overflow: hidden;
	padding-top: 0;
	padding-bottom: 0px;
}

.orderSetWarehouseShipBtn,.orderSetWarehouseShipAutoBtn ,.biaojifahuo{
	float: right;
}

.order_line span {
	padding-left: 0px;
}

.status_3 {
	
}

.status_6 {
	color: #0090E1;
}

.status_7 {
	color: red;
}

.checked_count {
	padding: 0 5px;
	font-weight: bold;
	font-size: 14px;
	color: red;
}

.opration_area {
	height: auto;
}

.opDiv {
	padding: 0px 0 4px 0;
}

.logIcon,.loadLogIcon {
	background: url(/images/sidebar/bg_mailSch.gif) center center;
	display: block;
	height: 16px;
	vertical-align: middle;
	white-space: nowrap;
	width: 18px;
}

.hideIcon{
	display: none;
}

.order_line .logIcon {
	padding: 0px;
}

.operator_note_span {
	
}

.recordNo,.qty,.sku,.unitPrice,.warehouseSkuWrap {
	width: 20%;
	float: left;
}

.recordNo{width:30%;}

.qty{
	width: 50px;
}

.sku {
	width: 50%;
}
.warehouseSkuWrap {
	width: 20%;
}
.unitPrice{
	width: 50px;
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
#searchForm .input_text_select{width:100px;}

.order_status_tag_container{
	position:absolute;
	z-index:10;
	background:#FFFFFF;
	text-align: center;
    width: 100%;
	left:0;
	display: none;
}
.order_status_tag_container dt{
	border-left: 1px solid #CCCCCC;
	border-right: 1px solid #CCCCCC;
	border-top: 1px solid #CCCCCC;
	border-bottom: 1px solid #CCCCCC;
}
.zidingyibiaoji{}
.completeSale{display:none;}
/* 更多的搜索条件--样式开始 */
.table-module-seach{
	background: none repeat scroll 0 0 #D1E7FC;
    border: 2px solid #D1E7FC;
    table-layout: fixed;
    width: 80%;
}			

.table-module-seach td{
	border-bottom: 1px solid #FFFFFF;
	border-right: 1px solid #FFFFFF;
	line-height: 25px;
}
	
.table-module-seach-title{
	width: 89px;
	text-align: right;
}
.table-module-seach-float{
	-moz-border-bottom-colors: none;
    -moz-border-left-colors: none;
    -moz-border-right-colors: none;
    -moz-border-top-colors: none;
	background-color: #FFFFFF;
    border-color: #D9D9D9;
    border-image: none;
    border-style: none solid solid;
    border-width: medium 1px 1px;
    right:5px;
    width: 35%;
	min-width: 500px;
    position: fixed;
    top: -450px;
    z-index: 105;
	border-top: 1px solid #CCCCCC;
    border-left: 1px solid #CCCCCC;
    border-right: 1px solid #CCCCCC;
    border-bottom: 1px solid #CCCCCC;
}
.table-module-seach-float-content{
	max-height: 300px;
	overflow: auto;
}
#search_more_div .table-module-seach-float{
	padding-top: 0px;
}
#search_more_div .table-module-seach-float div{
	padding-top: 1px;
}
.table-module-seach-float h2 {
    border-bottom: 1px solid #E3E3E3;
    font-size: 12px;
    font-weight: bold;
    height: 30px;
    line-height: 30px;
    padding: 0 10px;
}
.give_up_1{color:red;}
.give_up_0{color:green;}

.Tab .sub_status_container{font-weight:normal;
    background: none repeat scroll 0 0 #FFFFFF;
    display: none;
    left: 0;
    position: absolute;
    text-align: center;
    border: 1px solid #CCCCCC;
    border-bottom: 0 none;
    margin-left:-1px;
    width: 100%;
    z-index: 10;
}
.statusTag{display:block;padding:0 15px;}
.Tab a:hover{color:#000; border-bottom: 2px solid #87B87F;box-shadow: 0 0 1px #87B87F;}
.Tab .chooseTag{font-weight:bold;color:#393939;border-bottom: 2px solid #87B87F}
#module-container .Tab  .sub_status_container dt{	
    border-bottom: 1px solid #CCCCCC;	
}
#module-container .Tab  .sub_status_container a{font-weight: normal;padding:0;}
#module-container .Tab  .sub_status_container a:hover{color:#393939;}

#module-container .Tab  .sub_status_container .selected{font-weight:bold;color:#000;}


/* 更多的搜索条件--样式结束 */
-->
</style>
<link type="text/css" rel="stylesheet" href="/css/public/layout.css" />
<div id='tag_form_div' title='自定义标记' class='dialog_div'>
	<form id='tag_form' onsubmit='return false;'>
		<input type='text' id='tag_input' name='tag_input' value='' placeholder='输入文字或者从下方选择'/>
		<input type='hidden' id='ot_id_order_status' name='order_status' value='' />
		<br />
		<div id='tag_select'></div>
	</form>
</div>
<div id='inventory_div' title='产品库存' class='dialog_div'>
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
		<tbody>
			<tr class="table-module-title">
				<td>产品代码</td>
				<td width='200'>仓库</td>
				<td>在途数量</td>
				<td>待上架数量</td>
				<td>可用数量</td>
				<td>冻结库存</td>
			</tr>
		</tbody>
		<tbody id='inventory_detail'>
		</tbody>
	</table>
</div>
<div id='verify_div_verify' title='<{t}>order_verify<{/t}>' class='dialog_div'>
	<div>
		<label><input type='radio' name='verify_type' value='0' class='verify_type verify_type0' checked='checked' /><{t}>order_verify_default<{/t}></label>
	</div>
	<div>
		<label><input type='radio' name='verify_type' value='1' class='verify_type verify_type1' /><{t}>order_verify_re_assign_warehouse_shipping<{/t}></label>
	</div>
	<div class='verify_warehouse_sel' style='display: none;'>
		<select id='warehouse_verify' name='warehouse' class='input_text_select'>
		</select>
		<select id='shipping_method_arr_verify' name='shipping_method' class='input_text_select'>
		</select>
	</div>
</div>
<div id='allot_div' title='保存搜索条件作为自动匹配仓库和运输方式规则' class='dialog_div'>
	<div style='display: none;'>
		匹配仓库：
		<select id='warehouse_allot' class='input_text_select'>
		</select>
		匹配运输方式：
		<select id='shipping_method_allot' class='input_text_select'>
		</select>
	</div>
	<div style='padding: 8px 0 0 0;'>
		保存名称：
		<input type='text' id='save_allot_name' class='input_text' />
	</div>
</div>
<div id='export_wrap_div' title='订单导出' class='dialog_div'>
	请选择模板：
	<select id='export_template' name='export_template'>
	</select>
	<a href='' target='_blank'>下载模板</a>
	<table width="100%" cellspacing="1" cellpadding="0" border="0" style="line-height: 150%">
		<tbody>
			<tr>
				<td height="40" colspan="2">
					<table border="0">
						<tbody>
							<tr>
								<td>
									<font color="#808080"><b><img border="0" src="/images/base/bg_tips01.png"></b></font>
								</td>
								<td>
									<font color="#808080"><b>小提示</b></font>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td align="left">
					<table border="0">
						<tbody>
							<tr>
								<td>
									<ul class="list_style_1">
										<li><font color="#FF3399">该导出功能，只可导出第三方仓库订单，自营仓库订单，无法导出，系统会自动忽略系类订单</font></li>
										<li>如何将导出的报表，直接映射到第三方仓库的运输方式？</li>
										<li>
											<font color="#FF3399">配置第三方运输代码！登陆仓配管理系统-》物流管理-》运输方式管理-》搜索，选择对应的渠道-》编辑-》填写第三方运输方式代码-》保存，导出的文件当中将是您配置的第三方运输代码!</font>
										</li>
									</ul>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div id='cancel_order_div' title='<{t}>why_cancel_order<{/t}>' class='dialog_div'>
	<form action="" id='cancel_order_form' onsubmit='return false;'>
		<p>
			<{t}>why_cancel_order<{/t}>：
			<select id='reason_type' name='reason_type'>
				<option value=''><{t}>-select-<{/t}></option>
				<option value='1'><{t}>abnormal_change_address<{/t}></option>
				<option value='2'><{t}>abnormal_cancel_order<{/t}></option>
				<option value='3'><{t}>abnormal_change_sku<{/t}></option>
				<option value='4'><{t}>abnormal_other<{/t}></option>
			</select>
		</p>
		<textarea id='reason' style='width: 350px; height: 60px;' name='reason'></textarea>
		<input name='refIds' value='' type='hidden' id='cancel_order_ref_ids' />		
		<input name='status' value='2' type='hidden' id='cancel_order_status' />
	</form>
</div>
<div id='allot_condition_div' class='dialog_div' title='已保存搜索条件'></div>
<div id="module-container" style=''>
	<div id="search-module">
		<form class="submitReturnFalse" name="searchForm" id="searchForm">
			<input type="hidden" value="" id="filterActionId" />
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;"><{t}>-code-<{/t}>：</span>
				<input type="text" class="input_text keyToSearch" id="code" name="code" style='width: 400px;' placeholder='<{t}>multi_split_space<{/t}>' />
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;"><{t}>SKU<{/t}>：</span>
				<input type="text" class="input_text keyToSearch" id="SKU" name="SKU" style="width: 200px;" />
			</div>
			<div id="search-module-baseSearch">
				<div class="search-module-condition">
					<span class="searchFilterText" style="width: 90px;">&nbsp;</span>
					<input type="button" class="baseBtn submitToSearch" value="<{t}>search<{/t}>">
					&nbsp;&nbsp;
					<input type="button" class="baseBtn clearBtn" value="<{t}>reset<{/t}>">
					&nbsp;
					<a href="javascript:void(0)" class="toAdvancedSearch" onclick="toAdvancedSearch()"><{t}>showAdvancedSearch<{/t}></a>
				</div>
			</div>
			<div id="search-module-advancedSearch">
				<div class="search-module-condition">
					<span class="searchFilterText" style="width: 90px;"><{t}>目的仓库<{/t}>：</span>
					<select name="to_warehouse_id" style="width: 200px;" class="input_text_select input_select">
						<option value=""><{t}>-all-<{/t}></option>
						<{foreach from=$warehouse item=w name=w}>
						<option value='<{$w.warehouse_id}>'><{$w.warehouse_code}>[<{$w.warehouse_desc}>]</option>
						<{/foreach}>
					</select>
				</div>
				<div class="search-module-condition">
					<span class="searchFilterText" style="width: 90px;">出货<{t}>warehouse_name<{/t}>：</span>
					<select id='ship_warehouse' name="warehouse_id" class="input_text_select input_select" style="float: left;">
						<option value=""><{t}>-all-<{/t}></option>
						<{foreach from=$warehouse item=w name=w}>
						<option value='<{$w.warehouse_id}>'><{$w.warehouse_code}>[<{$w.warehouse_desc}>]</option>
						<{/foreach}>
					</select>
					<span class="searchFilterText" style="width: 90px;"><{t}>shipping_method<{/t}>：</span>
					<select name=shipping_method class="input_text_select input_select">
						<option value=""><{t}>-all-<{/t}></option>
						<{foreach from=$shippingMethods item=c name=c}>
						<option value='<{$c.sm_code}>'>
							<{$c.sm_code}> [<{$c.sm_name_cn}>
							<!--  -- <{$c.sm_name}> -->
							]
						</option>
						<{/foreach}>
					</select>
				</div>
				<div class="search-module-condition">
					<span class="searchFilterText" style="width: 90px;"><{t}>create_time<{/t}>：</span>
					<input type="text" class="datepicker input_text  " id="createDateFrom" name="createDateFrom" />
					~
					<input type="text" class="datepicker input_text  " id="createDateEnd" name="createDateEnd" />
				</div>
				<div class="search-module-condition" id='div_for_status_3_4_'></div>
				<div id='search_more_div'></div>
				<div class="search-module-condition">
					<span class="searchFilterText" style="width: 90px;">&nbsp;</span>
					<input type="button" class="baseBtn submitToSearch" value="<{t}>search<{/t}>">
					&nbsp;&nbsp;
					<input type="button" class="baseBtn clearBtn" value="<{t}>reset<{/t}>">
					<input type='hidden' name='keyword' id='keyword' value='' />
					<input type='hidden' name='status' id='status' value='' />
					<input type='hidden' name='is_more' id='is_more' value='0' />
					&nbsp;
					<a href="javascript:void(0)" class="toAdvancedSearch" onclick="toBaseSearch()"><{t}>hideAdvancedSearch<{/t}></a>
				</div>
			</div>
		</form>
	</div>

	<div id="fix_header_content" style="z-index:9;">
		<div class='clone' >
			<div class="Tab">
				<ul>
				    <li class="">
						<a href="javascript:void (0)" class='order_title_container statusTag statusTag-1'  status=''  data-id='' is_original='0'  abnormal_type='' process_again=''>
							<{t}>all_orders<{/t}>
							<span class='count' style='display: none;'></span>
						</a>
					</li>
					<{foreach from=$statusArr item=status name=status key=k}>
					<li style='position: relative;' class='mainStatus'>
						<!-- 订单状态存在处理等级，is_original为1，需要控制处理状态，否则为0（同时data-id也不做控制，设置为空）-->
						<a href="javascript:void (0)" class="statusTag statusTag<{$k}>"  status='<{$k}>' abnormal_type='' process_again=''>
							<span class='order_title<{$k}>' title='<{$status.name}>'><{$status.name}></span>
							<span class='count'></span>
						</a>
					</li>
					<{/foreach}>
				</ul>
				<input type="button" value="<{t}>order_export_base_btn<{/t}>" class="baseExport baseBtn" style="margin-right: 12px;">
				<div id="pageTotal" style="text-align: right; float: right; line-height: 25px; padding: 0 5px;"></div>
				<!--
               <a href="javascript:void (0)" style='line-height:30px;padding:0 10px;font-size:25px;' title='添加其它状态'>+</a>
                -->
			</div>
			<div class="opration_area0">
				<!--<div class="opration_area_lft"><input type="button" value="审核"  class="baseBtn" /></div>-->
				<div class="opration_area" style="margin-left: 10px">
					<div class='opDiv'></div>
				</div>
			</div>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
				<tbody>
					<tr class="table-module-title">
						<td width="15px" class="ec-center">
							<input type="checkbox" class="checkAll">
						</td>
						<td width='190px' class="ec-center"><{t}>order_info<{/t}></td>
						<td width='185px' class="ec-center"><{t}>order_detail<{/t}></td>
						<td width='185px' class="ec-center"><{t}>order_ship_info<{/t}></td>
						<td width='170px' class="ec-center"><{t}>order_time_info<{/t}></td>
						<td width='80px' class="ec-center"><{t}>operation<{/t}></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div id='fix_header' style='position: fixed; top: 0px; display: none; background: #fff;z-index:9;width: 99.2%'></div>
	<div id="module-table" style='overflow: auto;'>
		<form action="" id='listForm' method='POST'>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
				<tr class="table-module-title" id='aaaaaaaaaaa' style=''>
					<td width="15px" class="ec-center">&nbsp;</td>
					<td width='190px' class="ec-center">&nbsp;</td>
					<td width='185px' class="ec-center">&nbsp;</td>
					<td width='185px' class="ec-center">&nbsp;</td>
					<td width='170px' class="ec-center">&nbsp;</td>
					<td width='80px' class="ec-center">&nbsp;</td>
				</tr>
				<tbody id="table-module-list-data">
				</tbody>
			</table>
		</form>
		<div class="pagination"></div>
	</div>
	<div id='loading'></div>
	<div id='t' style='position: fixed; top: 0px;'></div>
</div>
