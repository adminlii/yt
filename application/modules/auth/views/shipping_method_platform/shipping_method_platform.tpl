<script type="text/javascript">
    EZ.url = '/auth/shipping-method-platform/';
    EZ.getListData = function (json) {
        var html = '';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
            html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
            html += "<td class='ec-center' >" + (i++) + "</td>";
            html += "<td >" + val.company_code + "</td>";
            html += "<td >" + val.platform + "</td>";
            html += "<td >" + val.name_cn + "</td>";
            html += "<td >" + val.name_en + "</td>";
            html += "<td >" + val.shipping_method_code + "</td>";
            html += "<td >" + val.short_code + "</td>";
            html += "<td >" + val.platform_shipping_mark + "</td>";
            html += "<td >" + val.level + "</td>";
            
            html += "<td class='center'>";
            if(val.can_edit){
            	html += "<a href=\"javascript:editById(" + val.shipping_method_id + ")\">" + EZ.edit + "</a>";
                html += "&nbsp;|&nbsp;<a href=\"javascript:deleteById(" + val.shipping_method_id + ")\">" + EZ.del + "</a>";
            }else{
            	html += "<span>" + EZ.edit + "</span>";
                html += "&nbsp;|&nbsp;<span>" + EZ.del + "</span>";
            }
            
            html += "</td>";
            html += "</tr>";
        });
        return html;
    }
    $(function(){
        $('#explanationBtn').click(function(){
            var html = $('#explanation_div').html();
            alertTip(html,550);
        });
        
    })
</script>
<style>
#table-module-list-data td {
	word-warp: break-word; /*内容将在边界内换行*/
	word-break: break-all; /*允许非亚洲语言文本行的任意字内断开*/
}
</style>
<div id="module-container">
	<div id="ez-wms-edit-dialog" style="display: none;">
		<table class="dialog-module" border="0" cellpadding="0" cellspacing="0">
			<tbody>
				<input type="hidden" name="shipping_method_id" id="shipping_method_id" value="" />
				<tr>
					<td class="dialog-module-title">所属平台:</td>
					<td>
						<select name="platform" id="platform" class="input_select">
							<option value='ebay'>ebay</option>
							<option value='amazon'>amazon</option>
						</select>
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title">中文名称</td>
					<td>
						<input type="text" name="name_cn" id="name_cn" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title">英文名称:</td>
					<td>
						<input type="text" name="name_en" id="name_en" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title">运输代码:</td>
					<td>
						<input type="text" name="shipping_method_code" id="shipping_method_code" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title">运输代码简称:</td>
					<td>
						<input type="text" name="short_code" id="short_code" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title">承运商:</td>
					<td>
						<input type="text" name="carrier" id="carrier" class="input_text" />
						<span class="msg">*amazon必填</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title">平台发货标记运输方式:</td>
					<td>
						<input type="text" name="platform_shipping_mark" id="platform" validator=" " value="" class="input_text" />
						<span class="msg"></span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title">优先级:</td>
					<td>
						<select name='level' id='level' title='越大优先级越高' class="input_select">
							<{assign var='count' value=50}> <{section name=loop loop=$count}>
							<option value='<{$smarty.section.loop.index}>'><{$smarty.section.loop.index}></option>
							<{/section}>
						</select>
						&nbsp;越大优先级越高
						<span class="msg"></span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="search-module">
		<form id="searchForm" name="searchForm" class="submitReturnFalse">
			<div style="padding: 0">
				<span style="width: 90px;" class="searchFilterText">运输代码：</span>
				<input type="text" name="shipping_method_code" class="input_text keyToSearch" />
				&nbsp;&nbsp;
				<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" />
				<input type="button" id="createButton" value="<{t}>create<{/t}>" class="baseBtn" />
				&nbsp;&nbsp;&nbsp;&nbsp;
				<a id="explanationBtn" href='javascript:;'>说明</a>
			</div>
		</form>
	</div>
	<div id="module-table">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
			<tr class="table-module-title">
				<td width="3%" class="ec-center">NO.</td>
				<td>所属账号</td>
				<td>所属平台</td>
				<td>中文名称</td>
				<td>英文名称</td>
				<td>运输代码</td>
				<td>运输代码简称</td>
				<td>平台发货标记运输方式</td>
				<td>优先级</td>
				<td>操作</td>
			</tr>
			<tbody id="table-module-list-data"></tbody>
		</table>
	</div>
	<div class="pagination"></div>
</div>
<div title='功能说明' class='dialog_div' id='explanation_div' style='display: none;'>
	<b style="color: #E06B26;">买家场景：</b>买家选择了订单的发货方式为DE_DHL<br /> <b style="color: #008000;">系统流程：</b>设置平台运输方式映射DE_DHL-->DHL<br />
	<div style="border: 1px solid #B5BDB9; padding: 0 10px;">
		<div class="search-module-condition">1、客服审核订单，选择订单发货方式使用“DHL”</div>
		<div class="search-module-condition">2、订单转入已发货状态，系统将自动标记订单已发货并同步到eBay.</div>
		<div class="search-module-condition">3、同步成功后，卖家可在eBay后台查看到，该订单的发货方式为DHL.</div>
		<div class="search-module-condition">PS:若没有对应的映射关系，则标记发货到eBay时，使用客户选择的那个发货方式.</div>
	</div>
	<!-- 
买家选择的发货方式为DE_DHL<br/>
操作员操作订单选择发货方式使用的是DHL，<br/>
操作员操作完成之后，系统自动标记订单已发货到eBay，同时选择映射运输方式DHL作为订单的发运方式。<br/>
卖家可在eBay后台查看到订单已经标记发货，发运方式为DHL，如果有订单号，系统也会将订单号同步到eBay。<br/>
系统后台任务会自动将订单标记发货同步到eBay。操作员也可手动操作，同步到eBay。<br/>
 -->
</div>