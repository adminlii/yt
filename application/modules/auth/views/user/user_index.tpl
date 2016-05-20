<script type="text/javascript">
    EZ.url = '/auth/user/';
    EZ.getListData = function (json) {
        var html = '';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
            html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
            html += "<td class='ec-center'>" + (i++) + "</td>";
            
            html += "<td >" + val.E1 + "</td>";
            html += "<td >" + val.E3 + "</td>";
            html += "<td >" + val.E4 + "</td>";
            html += "<td >" + val.E5 + "</td>";
            //html += "<td >" + val.E7 + "</td>";
            //html += "<td >" + val.E8 + "</td>";
            html += "<td >" + val.E11 + "</td>";
            html += "<td >" + val.E17 + "</td>";
            html += "<td >" + val.E15 + "</td>";
            html += "<td >" + (val.parent_user?val.parent_user.user_name:'超级管理员') + "</td>";
            if(val.E17){
            	html += "<td>";
            	html += "<a href=\"javascript:bindAction(" + val.E0 + ")\">" + EZ.permission + "</a>&nbsp;&nbsp;";
                html += "|&nbsp;&nbsp;<a href=\"javascript:editById(" + val.E0 + ")\">" + EZ.edit + "</a>&nbsp;&nbsp;";
                //html += "|&nbsp;&nbsp;<a href=\"javascript:deleteById(" + val.E0 + ")\">" + EZ.del + "</a>";
                html += "</td>";
            }else{
            	html += "<td>";
            	html += "<span>" + EZ.permission + "</span>&nbsp;&nbsp;";
                html += "|&nbsp;&nbsp;<span>" + EZ.edit + "</span>&nbsp;&nbsp;";
                //html += "|&nbsp;&nbsp;<span>" + EZ.del + "</span>";
                html += "</td>";
            }
            
            html += "</tr>";
        });
        return html;
    }
    $(function(){
        
    })
</script>
<div id="module-container">
	<div id="ez-wms-edit-dialog" style="display: none;">
		<table class="dialog-module" border="0" cellpadding="0" cellspacing="0">
			<tbody>
				<input type="hidden" name="E0" id="E0" value="" />
				<tr>
					<td class="dialog-module-title"><{t}>userCode<{/t}>:</td>
					<td>
						<input type="text" name="E1" id="E1" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>userPass<{/t}>:</td>
					<td>
						<input type="password" name="E2" id="E2" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>userName<{/t}>:</td>
					<td>
						<input type="text" name="E3" id="E3" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>userNameEn<{/t}>:</td>
					<td>
						<input type="text" name="E4" id="E4" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>status<{/t}>:</td>
					<td>
						<select name="E5" id="E5" class="input_select">
							<{foreach from=$statusArr key=key item=val}>
							<option value="<{$key}>"><{$val}></option>
							<{/foreach}>
						</select>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>email<{/t}>:</td>
					<td>
						<input type="text" name="E6" id="E6" class="input_text" />
					</td>
				</tr>
				<!-- 
        <tr>
            <td class="dialog-module-title"><{t}>departmentName<{/t}>:</td>
            <td>
                <select  name="E7" id="E7"  class="input_text">
                    <{foreach from=$department item=val}>
                    <option value="<{$val.ud_id}>"><{$val.ud_name}>/<{$val.ud_name_en}></option>
                    <{/foreach}>
                </select>
            </td>
        </tr>
        <tr>
            <td class="dialog-module-title"><{t}>positionName<{/t}>:</td>
            <td>
                <select  name="E8" id="E8"  class="input_text">
                    <{foreach from=$position item=val}>
                    <option value="<{$val.up_id}>"><{$val.up_name}>/<{$val.up_name_en}></option>
                    <{/foreach}>
                </select>
            </td>
        </tr>
         -->
				<tr>
					<td class="dialog-module-title"><{t}>telphone<{/t}>:</td>
					<td>
						<input type="text" name="E10" id="E10" class="input_text" />
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>mobilePhone<{/t}>:</td>
					<td>
						<input type="text" name="E11" id="E11" class="input_text" />
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>note<{/t}>:</td>
					<td>
						<input type="text" name="E12" id="E12" class="input_text" />
					</td>
				</tr>
				<!-- 
        <tr>
            <td class="dialog-module-title"><{t}>supervisorName<{/t}>:</td>
            <td>
                <select  name="E13" id="E13"  class="input_text">
                    <option value="0">-Select-</option>
                    <{foreach from=$userArr key=key item=val}>
                    <option value="<{$val.user_id}>"><{$val.user_name_en}>/<{$val.user_name}></option>
                    <{/foreach}>
                </select>
            </td>
        </tr>
        -->
			</tbody>
		</table>
	</div>
	<div id="search-module">
		<form id="searchForm" name="searchForm" class="submitReturnFalse">
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;"><{t}>userCode<{/t}>：</span>
				<input type="text" name="E1" id="E1" class="input_text keyToSearch" />
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;"><{t}>userName<{/t}>：</span>
				<input type="text" name="E3" id="E3" class="input_text keyToSearch" />
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;"><{t}>userNameEn<{/t}>：</span>
				<input type="text" name="E4" id="E4" class="input_text keyToSearch" />
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;"><{t}>状态<{/t}>：</span>
				<select name="E5" class="input_select">
					<option value=''>全部</option>
					<option value='0'>禁用</option>
					<option value='1'>可用</option>
					<option value='2'>未激活</option>
				</select>
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;"></span>
				<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" />
				<input type="button" id="createButton" value="<{t}>create<{/t}>" class="baseBtn" />
			</div>
		</form>
	</div>
	<div id="module-table">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
			<tr class="table-module-title">
				<td width="3%" class="ec-center">NO.</td>
				<td><{t}>userCode<{/t}></td>
				<td><{t}>userName<{/t}></td>
				<td><{t}>userNameEn<{/t}></td>
				<td><{t}>status<{/t}></td>
				<!-- 
               <td><{t}>departmentName<{/t}></td>
               <td><{t}>positionName<{/t}></td>
                -->
				<td><{t}>mobilePhone<{/t}></td>
				<td><{t}>type<{/t}></td>
				<td><{t}>lastLogin<{/t}></td>
				<td><{t}>账号创建人<{/t}></td>
				<td><{t}>operate<{/t}></td>
			</tr>
			<tbody id="table-module-list-data"></tbody>
		</table>
	</div>
	<div class="pagination"></div>
</div>
<{include file='auth/views/permission/user-new.tpl'}>
