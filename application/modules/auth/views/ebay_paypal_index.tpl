<script type="text/javascript" src="/js/pageEffects.js"></script>
<script type="text/javascript">
    EZ.url = '/auth/ebay-paypal/';
    EZ.getListData = function (json) {
        var html = '';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
            html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
            html += "<td class='ec-center'>" + (i++) + "</td>";
            
                    html += "<td >" + val.platform_user_name + "</td>";
                    html += "<td >" + val.E1 + "</td>";
                    html += "<td >" + val.E2 + "</td>";
            html += "<td><a href=\"javascript:editById(" + val.E0 + ")\">" + EZ.edit + "</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:deleteById(" + val.E0 + ")\">" + EZ.del + "</a></td>";
            html += "</tr>";
        });
        return html;
    }
</script>
<div id="module-container">
	<div id="ez-wms-edit-dialog" style="display: none;">
		<table class="dialog-module" border="0" cellpadding="0" cellspacing="0">
			<tbody>
				<input type="hidden" name="E0" id="E0" value="" />
				<tr>
					<td class="dialog-module-title">paypal账户:</td>
					<td>
						<input type="text" name="E1" id="E1" validator="required" class="input_text" placeholder="请填写paypal账户的邮箱" />
						<span class="msg err-msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title">API账户:</td>
					<td>
						<input type="text" name="E2" id="E2" validator="required" class="input_text" />
						<span class="msg err-msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title">API密码:</td>
					<td>
						<input type="text" name="E3" id="E3" validator="required" class="input_text" />
						<span class="msg err-msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title">API签名:</td>
					<td>
						<input type="text" name="E4" id="E4" validator="required" class="input_text" />
						<span class="msg err-msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title">ebay账户:</td>
					<td>
						<select name="E5" id="E5" style="width: 250px;" validator="required" class="input_select">
							<option value="">选择</option>
							<{foreach from=$user_account_arr name=ob item=ob}>
							<option value='<{$ob.user_account}>'><{$ob.platform_user_name}></option>
							<{/foreach}>
						</select>
						<span class="msg err-msg">*</span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="search-module">
		<form id="searchForm" name="searchForm" class="submitReturnFalse">
			<div style="padding: 0">
				<div class="search-module-condition">
					<table>
						<tr>
							<td width="250px">
								<span class="searchFilterText" style="width: 90px;">ebay账户：</span>
								<select id="E5" name="E5" class="keyToSearch input_select" style="widows: 140px;">
									<option value="">全部</option>
									<{foreach from=$user_account_arr name=ob item=ob}>
									<option value='<{$ob.user_account}>'><{$ob.platform_user_name}></option>
									<{/foreach}>
								</select>
							</td>
							<td>
								<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" />
							</td>
						</tr>
					</table>
				</div>
				<!-- 
                <input type="button" id="createButton" value="<{t}>create<{/t}>" class="baseBtn"/>
                <input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch"/>
                 -->
			</div>
		</form>
	</div>
	<div id="module-table" class="fixed_side">
		<div style="padding-top: 5px;" class="opration_area">
			<div>
				<input type="button" class="baseBtn" value="添加账户关联" id="createButton" style='float: right; margin-left: 5px;'>
			</div>
		</div>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
			<tr class="table-module-title">
				<td width="2%" class="ec-center">NO.</td>
				<td>ebay账户</td>
				<td>paypal账户</td>
				<td>API账户</td>
				<td width="7%"><{t}>operate<{/t}></td>
			</tr>
			<tbody id="table-module-list-data"></tbody>
		</table>
	</div>
	<div class="pagination"></div>
</div>
