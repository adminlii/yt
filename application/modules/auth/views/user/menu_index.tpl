<script type="text/javascript">
    EZ.url = '/auth/menu/';
    EZ.getListData = function (json) {
        var html = '';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
        	html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
            html += "<td class='ec-center'>" + (i++) + "</td>";
                    html += "<td >" + val.E1 + "</td>";
                    html += "<td >" + val.E2 + "</td>";
                    html += "<td >" + val.E7 + "</td>";
                    html += "<td >" + val.E4 + "</td>";
                    html += "<td >" + val.E6 + "</td>";
            html += "<td><a href=\"javascript:editById(" + val.E0 + ")\">" + EZ.edit + "</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:deleteById(" + val.E0 + ")\">" + EZ.del + "</a>";
            html += "&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:editSettings(" + val.E0 + ")\">添加子菜单</a></td>";
            html += "</tr>";


            html += "<tr><td style='background-color:#fff'></td><td colspan='4'>";
            html += '<table width="100%" class="settings" cellspacing="0" cellpadding="0" border="0">';
            html += '<tr class="table-module-head">';
            html += '<td>菜单标题</td>';
            html += '<td>英文标题</td>';
            html += '<td>排序</td>';
            html += '<td width="120px">操作</td>';
            html += '</tr>'

            if (val.submenu[0]) {
                $.each(val.submenu, function (k, v) {
                    html += (k + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
                    html += '<td>' + v.um_title + '</td>';
                    html += '<td>' + v.um_title_en + '</td>';
                    html += '<td>' + v.um_sort + '</td>';
                    html += "<td><a href=\"javascript:editSettingsById(" + v.um_id + ")\">" + EZ.edit + "</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:deleteSettingsById(" + v.um_id + ")\">" + EZ.del + "</a>";
                    html += '</td>'
                    html += "</tr>";
                });
            } else {
                html += '<tr class="table-module-b1">'
                html += '<td colspan="4"><span style="color:red">未添加子菜单,请添加子菜单;</span></td>';
                html += '</tr>'
            }
            html += '</table>';

            html += "</td><td colspan='2' style='background-color:#fff'></td></tr>";

        });
        return html;
    }
    //Del
    function deleteById(id) {
        if (id == '' || id == undefined) {
            return false;
        }
        $.EzWmsDel(
                {paramId: id,
                    Field: "paramId",
                    dMessage: "当存在子菜单时，同时删除子菜单，确认要删除吗?",
                    url: EZ.url + "delete"
                }, submitSearch
        );
    }
    $(function(){
        $("#edit-subMenu-dialog").dialog({
            autoOpen: false,
            modal: true,
            width: 550,
            show: "slide",
            buttons: {
                'Ok(确认)': function () {
                    $("#settingsForm").submit();
                },
                'Cancel(取消)': function () {
                    $(this).dialog('close');
                }
            },
            close: function () {

                $(this).dialog('close');
            }
        });
    });
    //Del配置
    function deleteSettingsById(id) {
        if (id == '' || id == undefined) {
            return false;
        }
        $.EzWmsDel(
                {paramId: id,
                    Field: "paramId",
                    url:"/auth/menu/delete"
                }, submitSearch
        );
    }
    //编辑配置
    function editSettingsById(id) {
        $("#edit-subMenu-con").EzWmsEditDataDialog({
            paramId: id,
            url: "/auth/menu/get-by-json",
            editUrl:"/auth/menu/edit-submenu",
            dWidth:660
        });
    }
    function editSettingsSuccess(json){
        var html = '';
        if (json.state == '1') {
            html += '<span class="tip-success-message">'+json.message+'</span>';
            $("#edit-subMenu-dialog").dialog('close');
        }else{
            $.each(json.errorMessage,function(k,v){
                html += '<span class="tip-error-message">'+v+'</span>';
            });

        }
        submitSearch();
        alertTip(html);
    }
    function editSettings(sid) {
        $("#edit-subMenu-dialog").html("<form id='settingsForm' name='settingsForm' class='submitReturnFalse'>"+$("#edit-subMenu-con").html()+'</form>');
        $("#settingsForm").myAjaxForm({api: '/auth/menu/edit-submenu', success: editSettingsSuccess});
        $("#E8","#settingsForm").val(sid);
        $("#edit-subMenu-dialog").dialog('open');
    }
</script>
<style type="text/css">
#module-container select {
	width: 150px;
}

.dialog-edit-alert-tip .dialog-module td .input_text {
	width: 180px;
}

.msg {
	color: red;
}

#edit-subMenu-dialog .dialog-module-title {
	font-weight: bold;
	padding-right: 5px;
	text-align: right;
}

#edit-subMenu-dialog td {
	height: 30px;
}

.settings td {
	line-height: 20px;
	padding: 5px;
}

.table-module-head {
	background-color: #E0E0E0;
}
</style>
<div id="module-container">
	<div id="ez-wms-edit-dialog" style="display: none;">
		<table class="dialog-module" border="0" cellpadding="0" cellspacing="0">
			<tbody>
				<input type="hidden" name="E0" id="E0" value="" />
				<tr>
					<td class="dialog-module-title"><{t}>menuName<{/t}>:</td>
					<td>
						<input type="text" name="E1" id="E1" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>menuNameEn<{/t}>:</td>
					<td>
						<input type="text" name="E2" id="E2" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>system<{/t}>:</td>
					<td>
						<select name="E7" id="E7" class="input_select">
							<{foreach from=$systemArray item=system}>
							<option value="<{$system.us_id}>"><{$system.us_title}>/<{$system.us_title_en}></option>
							<{/foreach}>
						</select>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>css<{/t}>:</td>
					<td>
						<input type="text" name="E4" id="E4" class="input_text" />
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>sort<{/t}>:</td>
					<td>
						<input type="text" name="E6" id="E6" validator="required numeric" err-msg="<{t}>require<{/t}>|<{t}>numeric<{/t}>" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="search-module">
		<form id="searchForm" name="searchForm" class="submitReturnFalse">
			<div class="search-module-condition">
				<span style="width: 90px;" class="searchFilterText"><{t}>menuName<{/t}>：</span>
				<input type="text" name="E1" id="E1" class="input_text keyToSearch" />
			</div>
			<div class="search-module-condition">
				<span style="width: 90px;" class="searchFilterText"><{t}>menuNameEn<{/t}>：</span>
				<input type="text" name="E2" id="E2" class="input_text keyToSearch" />
			</div>
			<div class="search-module-condition">
				<span style="width: 90px;" class="searchFilterText"></span>
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
				<td><{t}>menuName<{/t}></td>
				<td><{t}>menuNameEn<{/t}></td>
				<td><{t}>system<{/t}></td>
				<td><{t}>css<{/t}></td>
				<td><{t}>sort<{/t}></td>
				<td><{t}>operate<{/t}></td>
			</tr>
			<tbody id="table-module-list-data"></tbody>
		</table>
	</div>
	<div class="pagination"></div>
</div>
<div id="edit-subMenu-dialog" title="子菜单" style="display: none;"></div>
<div id="edit-subMenu-con" title="子菜单" style="display: none;">
	<table class="dialog-module" border="0" cellpadding="0" cellspacing="0">
		<tbody>
			<input type="hidden" name="E0" id="E0" value="" />
			<input type="hidden" name="E8" id="E8" value="" />
			<tr>
				<td class="dialog-module-title"><{t}>menuName<{/t}>:</td>
				<td>
					<input type="text" name="E1" id="E1" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" />
					<span class="msg">*</span>
				</td>
			</tr>
			<tr>
				<td class="dialog-module-title"><{t}>menuNameEn<{/t}>:</td>
				<td>
					<input type="text" name="E2" id="E2" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" />
					<span class="msg">*</span>
				</td>
			</tr>
			<tr>
				<td class="dialog-module-title"><{t}>css<{/t}>:</td>
				<td>
					<input type="text" name="E4" id="E4" class="input_text" />
				</td>
			</tr>
			<tr>
				<td class="dialog-module-title"><{t}>sort<{/t}>:</td>
				<td>
					<input type="text" name="E6" id="E6" validator="required numeric" err-msg="<{t}>require<{/t}>|<{t}>numeric<{/t}>" class="input_text" />
					<span class="msg">*</span>
				</td>
			</tr>
		</tbody>
	</table>
</div>
