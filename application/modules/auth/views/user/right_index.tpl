<script type="text/javascript">
    EZ.url = '/auth/user-right/';
    EZ.getListData = function (json) {
        var html = '';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
            html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
            html += "<td class='ec-center'>" + (i++) + "</td>";
            
                    html += "<td >" + val.E1 + "</td>";
                    html += "<td >" + val.E2 + "</td>";
                    html += "<td >" + val.E3 + "</td>";
                    html += "<td >" + val.E9 + "</td>";
                    html += "<td >" + val.E5 + "</td>";
                    html += "<td >" + val.E6 + "</td>";
                    html += "<td >" + val.E7 + "</td>";
            html += "<td><a href=\"javascript:bindAction(" + val.E0 + ")\">" + EZ.permission + "</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:editById(" + val.E0 + ")\">" + EZ.edit + "</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:deleteById(" + val.E0 + ")\">" + EZ.del + "</a></td>";
            html += "</tr>";
        });
        return html;
    }
</script>
<style type="text/css">
.searchFilterText {
	width: 90px
}
</style>
<div id="module-container">
	<div id="ez-wms-edit-dialog" style="display: none;">
		<table class="dialog-module" border="0" cellpadding="0" cellspacing="0">
			<tbody>
				<input type="hidden" name="E0" id="E0" value="" />
				<tr>
					<td class="dialog-module-title"><{t}>menu<{/t}>:</td>
					<td>
						<select name="E1" id="E1" class="input_select">
							<option value="">请选择</option>
							<{foreach from=$menuArray item=v}>
							<optgroup label="<{$v.um_title}>/<{$v.um_title_en}>" style="font-style: normal;">
								<{foreach from=$v.submenu item=val}>
								<option value="<{$val.um_id}>"><{$val.um_title}>/<{$val.um_title_en}></option>
								<{/foreach}>
							</optgroup>
							<{/foreach}>
						</select>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>rightName<{/t}>:</td>
					<td>
						<input type="text" name="E2" id="E2" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>rightNameEn<{/t}>:</td>
					<td>
						<input type="text" name="E3" id="E3" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>url<{/t}>:</td>
					<td>
						<input type="text" name="E5" id="E5" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>type<{/t}>:</td>
					<td>
						<select name="E6" id="E6" class="input_select">
							<{foreach from=$displayArray key=k item=val}>
							<option value="<{$k}>"><{$val}></option>
							<{/foreach}>
						</select>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>sort<{/t}>:</td>
					<td>
						<input type="text" name="E9" validator="numeric" err-msg="<{t}>numeric<{/t}>" maxlength="3" value="0" id="E9">
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>description<{/t}>:</td>
					<td>
						<input type="text" name="E4" id="E4">
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="search-module">
		<form id="searchForm" name="searchForm" class="submitReturnFalse">
			<div class="search-module-condition">
				<span class="searchFilterText"><{t}>TheMenu<{/t}>：</span>
				<select name="E1" id="E1" class="input_select">
					<option value="">全部</option>
					<{foreach from=$menuArray item=v}>
					<optgroup label="<{$v.um_title}>/<{$v.um_title_en}>" style="font-style: normal;">
						<{foreach from=$v.submenu item=val}>
						<option value="<{$val.um_id}>"><{$val.um_title}>/<{$val.um_title_en}></option>
						<{/foreach}>
					</optgroup>
					<{/foreach}>
				</select>
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText"><{t}>rightName<{/t}>：</span>
				<input type="text" name="E2" id="E2" placeholder="支持模糊查询" class="input_text keyToSearch" />
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText"></span>
				<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" />
			</div>
		</form>
	</div>
	<div id="module-table">
		<div class="opration_area">
			<div class="opration_area_lft">
				<input type="button" id="createButton" value="<{t}>create<{/t}>" class="baseBtn" />
			</div>
		</div>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
			<tr class="table-module-title">
				<td width="3%" class="ec-center">NO.</td>
				<td><{t}>menu<{/t}></td>
				<td><{t}>rightName<{/t}></td>
				<td><{t}>rightNameEn<{/t}></td>
				<td><{t}>sort<{/t}></td>
				<td><{t}>url<{/t}></td>
				<td><{t}>type<{/t}></td>
				<td><{t}>module<{/t}></td>
				<td><{t}>operate<{/t}></td>
			</tr>
			<tbody id="table-module-list-data"></tbody>
		</table>
	</div>
	<div class="pagination"></div>
</div>
<{include file='auth/views/permission/edit.tpl'}>
