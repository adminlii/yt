<script type="text/javascript">
    EZ.url = '/customer/Api/';
    EZ.getListData = function (json) {
        var html = '';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
            html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
            html += "<td class='ec-center'>" + (i++) + "</td>";
            
                    html += "<td >" + val.E2 + "</td>";
                    html += "<td >" + val.user.user_name + "</td>";
                    html += "<td >" + val.E4 + "</td>";
                    html += "<td >" + val.E5 + "</td>";
                    html += "<td >" + val.E6 + "</td>";
                    html += "<td >" + val.E7 + "</td>";
            html += "<td>";
            //html += "<a href=\"javascript:editById(" + val.E0 + ")\">" + EZ.edit + "</a>";
            //html += "&nbsp;&nbsp;|&nbsp;&nbsp;";
            html += "<a href=\"javascript:deleteById(" + val.E0 + ")\">" + EZ.del + "</a>";
            html += "</td>";
            html += "</tr>";
        });
        return html;
    }
    $(function(){
    	initData(0);
    })
</script>
<div id="module-container">
    <div id="ez-wms-edit-dialog" style="display:none;">
        <table class="dialog-module" border="0" cellpadding="0" cellspacing="0">
            <tbody>          
		        <tr>
		            <td class="dialog-module-title"><{t}>user<{/t}>:</td>
		            <td>
		            	<select  name="user_id" id="user_id" class="input_text">
		            		<{foreach from=$users name=u item=u}>
		            		<option value='<{$u.user_id}>'><{$u.user_code}></option>
		            		<{/foreach}>
		            	</select>		            	
		            	</td>
		        </tr>
            </tbody>
        </table>
        <p style='color:red;line-height:30px;'>如果该用户已经授权API设置,进行该操作,新的API将覆盖已有的API设置</p>
    </div>

    <div id="search-module">
        <form id="searchForm" name="searchForm" class="submitReturnFalse">
            <div style="padding:0">
                
       <{t}>customerCode<{/t}>：<input type="text" name="E2" id="E2" class="input_text keyToSearch"/>
                &nbsp;&nbsp;<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch"/>
                <input type="button" id="createButton" value="<{t}>create<{/t}>" class="baseBtn"/>
                <a href='/file/API.docx' target='_blank' style='float:right;line-height:25px;margin-right:15px;'>文档下载</a>
            </div>
        </form>
    </div>

    <div id="module-table">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
            <tr class="table-module-title">
                <td width="3%" class="ec-center">NO.</td>
                
       <td><{t}>customerCode<{/t}></td>
       <td><{t}>user<{/t}></td>
      <!--  <td><{t}>status<{/t}></td> -->
       <td><{t}>token<{/t}></td>
       <td><{t}>apiKey<{/t}></td>
       <td><{t}>addTime<{/t}></td>
       <td><{t}>updateTime<{/t}></td>
                <td><{t}>operate<{/t}></td>
            </tr>
            <tbody id="table-module-list-data"></tbody>
        </table>
    </div>
    <div class="pagination"></div>
</div>
