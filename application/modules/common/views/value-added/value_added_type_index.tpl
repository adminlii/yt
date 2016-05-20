<script type="text/javascript">
    EZ.url = '/common/value-added-type/';
    EZ.getListData = function (json) {
        var html = '';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
            html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
            html += "<td class='ec-center'>" + (i++) + "</td>";
            
                    html += "<td>" + val.E1 + "</td>";
                    html += "<td>" + (json.businessType[val.E2] ? json.businessType[val.E2] : '') + "</td>";
                    html += "<td>" + (json.effectiveStatus[val.E3] ? json.effectiveStatus[val.E3] : '') + "</td>";
                    html += "<td>" + val.E4 + "</td>";
                    html += "<td>" + val.E5 + "</td>";
                    html += "<td>" + val.E6 + "</td>";
            html += "<td><a href=\"javascript:editById(" + val.E0 + ")\">" + EZ.edit + "</a></td>";
            html += "</tr>";
        });
        return html;
    }
</script>
<div id="module-container">
    <div id="ez-wms-edit-dialog" style="display:none;">
        <table class="dialog-module" border="0" cellpadding="0" cellspacing="0">
            <tbody>
            <input type="hidden" name="E0" id="E0" value=""/>
            
        <tr>
            <td class="dialog-module-title">代码:</td>
            <td><input type="text" name="E1" id="E1" onkeyup="this.value=this.value.toUpperCase()" validator="required" err-msg="<{t}>require<{/t}>" class="input_text"/><span class="msg">*</span></td>
        </tr>
        <tr>
            <td class="dialog-module-title">业务类型:</td>
            <td>
	            <select name="E2" id="E2" class="input_select" style="width:300px;">
		            <{foreach from=$businessType item=btype name=btype key=k}>
		            	<option value="<{$k}>"><{$btype}></option>
		            <{/foreach}>
	            </select>
            </td>
        </tr>
        <tr>
            <td class="dialog-module-title">状态:</td>
            <td>
            	<select name="E3" id="E3" class="input_select" style="width:300px;">
		            <{foreach from=$effectiveStatus item=status name=status key=k}>
		            	<option value="<{$k}>"><{$status}></option>
		            <{/foreach}>
	            </select>
            </td>
        </tr>
        <tr>
            <td class="dialog-module-title">英文名称:</td>
            <td><input type="text" name="E4" id="E4" validator="required" err-msg="<{t}>require<{/t}>" class="input_text"/><span class="msg">*</span></td>
        </tr>
        <tr>
            <td class="dialog-module-title">中文名称:</td>
            <td><input type="text" name="E5" id="E5" validator="required" err-msg="<{t}>require<{/t}>" class="input_text"/><span class="msg">*</span></td>
        </tr>
        <tr>
            <td class="dialog-module-title">描述:</td>
            <td><input type="text" name="E6" id="E6"   class="input_text"/></td>
        </tr>
            </tbody>
        </table>
    </div>

    <div id="search-module">
        <form id="searchForm" name="searchForm" class="submitReturnFalse">
            <div style="padding:0">
				       代码：<input type="text" name="E1" id="E1" class="input_text keyToSearch"/>
				       中文名称：<input type="text" name="E5" id="E5" class="input_text keyToSearch"/>
                &nbsp;&nbsp;<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch"/>
                <input type="button" id="createButton" value="<{t}>create<{/t}>" class="baseBtn"/>
            </div>
        </form>
    </div>

    <div id="module-table">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
            <tr class="table-module-title">
                <td width="3%" class="ec-center">NO.</td>
			       <td>代码</td>
			       <td>业务类型</td>
			       <td>状态</td>
			       <td>英文名称</td>
			       <td>中文名称</td>
			       <td width="30%">描述</td>
                <td><{t}>operate<{/t}></td>
            </tr>
            <tbody id="table-module-list-data"></tbody>
        </table>
    </div>
    <div class="pagination"></div>
</div>
