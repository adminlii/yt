<script type="text/javascript">
    EZ.url = '/auth/level/';
    EZ.getListData = function (json) {
        var html = '';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
            html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
            html += "<td class='ec-center'>" + (i++) + "</td>";
            
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
					<td class="dialog-module-title"><{t}>positionName<{/t}>:</td>
					<td>
						<input type="text" name="E1" id="E1" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>positionNameEn<{/t}>:</td>
					<td>
						<input type="text" name="E2" id="E2" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="search-module">
		<form id="searchForm" name="searchForm" class="submitReturnFalse">
			<div style="padding: 0">
				<{t}>positionName<{/t}>：
				<input type="text" name="E1" id="E1" class="input_text keyToSearch" />
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
				<td><{t}>positionName<{/t}></td>
				<td><{t}>positionNameEn<{/t}></td>
				<td><{t}>operate<{/t}></td>
			</tr>
			<tbody id="table-module-list-data"></tbody>
		</table>
	</div>
	<div class="pagination"></div>
</div>
