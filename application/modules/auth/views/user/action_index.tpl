<script type="text/javascript">
    EZ.url = '/auth/Action/';
    EZ.getListData = function (json) {
        var html = '';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
            val.E4=EZ.allStatus[val.E4]?EZ.allStatus[val.E4]:'';
            val.E5=json.display[val.E5]?json.display[val.E5]:'';
            html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
            html += "<td class='ec-center'>" + (i++) + "</td>";

                    html += "<td >" + val.E1 + "</td>";
                    html += "<td >" + val.E2 + "</td>";
                    html += "<td >" + val.E3 + "</td>";
                    html += "<td >" + val.E4 + "</td>";
                    html += "<td >" + val.E5 + "</td>";
                    html += "<td >" + val.E6 + "</td>";
                    html += "<td >" + val.E7 + "</td>";
                    html += "<td >" + val.E8 + "</td>";
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
					<td class="dialog-module-title"><{t}>resourcName<{/t}>:</td>
					<td>
						<input type="text" name="E1" id="E1" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>resourcNameEn<{/t}>:</td>
					<td>
						<input type="text" name="E2" id="E2" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>alias<{/t}>:</td>
					<td>
						<input type="text" name="E3" id="E3" class="input_text" />
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>status<{/t}>:</td>
					<td>
						<select name="E4" id="E4" class="input_text">
							<{foreach from=$statusArr key=key item=val}>
							<option value="<{$key}>"><{$val}></option>
							<{/foreach}>
						</select>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>display<{/t}>:</td>
					<td>
						<select name="E5" id="E5" class="input_text">
							<{foreach from=$displayArr key=key item=val}>
							<option value="<{$key}>"><{$val}></option>
							<{/foreach}>
						</select>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>module<{/t}>:</td>
					<td>
						<select name="E6" id="E6" class="input_text" validator="required" err-msg="<{t}>require<{/t}>">
							<option value="">-Select-</option>
							<{foreach from=$module key=key item=val}>
							<option value="<{$val.ura_module}>"><{$val.ura_module}></option>
							<{/foreach}>
						</select>
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>controller<{/t}>:</td>
					<td>
						<input type="text" name="E7" id="E7" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>action<{/t}>:</td>
					<td>
						<input type="text" name="E8" id="E8" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="search-module">
		<form id="searchForm" name="searchForm" class="submitReturnFalse">
			<div style="padding: 0">
				<{t}>resourcName<{/t}>：
				<input type="text" name="E1" id="E1" class="input_text keyToSearch" />
				<{t}>module<{/t}>：
				<select name="E6" id="E6" class="input_text keyToSearch">
					<option value="">-Select-</option>
					<{foreach from=$module key=key item=val}>
					<option value="<{$val.ura_module}>"><{$val.ura_module}></option>
					<{/foreach}>
				</select>
				<{t}>controller<{/t}>：
				<input type="text" name="E7" id="E7" class="input_text keyToSearch" />
				<{t}>action<{/t}>：
				<input type="text" name="E8" id="E8" class="input_text keyToSearch" />
				&nbsp;&nbsp;
				<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" />
				<input type="button" id="createButton" value="<{t}>create<{/t}>" class="baseBtn" />
			</div>
		</form>
	</div>
	<div id="module-table">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
			<tr class="table-module-title">
				<td width="3%" class="ec-center">NO.</td>
				<td><{t}>resourcName<{/t}></td>
				<td><{t}>resourcNameEn<{/t}></td>
				<td><{t}>alias<{/t}></td>
				<td><{t}>status<{/t}></td>
				<td><{t}>display<{/t}></td>
				<td><{t}>module<{/t}></td>
				<td><{t}>controller<{/t}></td>
				<td><{t}>action<{/t}></td>
				<td><{t}>operate<{/t}></td>
			</tr>
			<tbody id="table-module-list-data"></tbody>
		</table>
	</div>
	<div class="pagination"></div>
</div>
