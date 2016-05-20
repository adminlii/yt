<script type="text/javascript">
    EZ.url = '/common/Searchfilter/';
    EZ.getListData = function (json) {
        var html = '';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
            val.E2 = json.parentArr[val.E2] ? json.parentArr[val.E2]['search_label'] : '';
            html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
            html += "<td class='ec-center'>" + val.E0 + "</td>";
            html += "<td >" + val.urName + "</td>";
            html += "<td >" + val.E2 + "</td>";
            html += "<td >" + val.E3 + "</td>";
            html += "<td >" + val.E4 + "</td>";
            html += "<td >" + val.E5 + "</td>";
            html += "<td >" + val.E7 + "</td>";
            html += "<td >" + val.E8 + "</td>";
            html += "<td >" + val.E9 + "</td>";
            html += "<td><a href=\"javascript:editById(" + val.E0 + ")\">" + EZ.edit + "</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:deleteById(" + val.E0 + ")\">" + EZ.del + "</a></td>";
            html += "</tr>";
        });
        return html;
    }
    function parent(obj){
        if($(obj).val()!='0' &&  $(obj).val()!=''){
            $("#E6","#editDataForm").parent().parent().hide();
            $("#E8","#editDataForm").parent().parent().hide();
        }
    }
</script>
<div id="module-container">
    <div id="ez-wms-edit-dialog" style="display:none;">
        <table class="dialog-module" border="0" cellpadding="0" cellspacing="0">
            <tbody>
            <input type="hidden" name="E0" id="E0" value=""/>


            <tr>
                <td class="dialog-module-title">父级:</td>
                <td><select name="E2" id="E2" err-msg="必填" validator="required"  onchange="parent(this)">
                        <option value="">请选择</option>
                        <option value="0">项级</option>
                        <{foreach from=$parentArr item=val}>
                        <option value="<{$val.sf_id}>"><{$val.urName}>--<{$val.search_label}></option>
                        <{/foreach}>
                    </select></td>
            </tr>
            <tr>
                <td class="dialog-module-title" >搜索类型:</td>
                <td><input type="text" name="E3" err-msg="必填" validator="required"  id="E3" class="input_text"/></td>
            </tr>
            <tr>
                <td class="dialog-module-title">搜索值:</td>
                <td><input type="text" name="E4"   id="E4" class="input_text"/></td>
            </tr>
            <tr>
                <td class="dialog-module-title">搜索值排序:</td>
                <td><input type="text" name="E5" err-msg="必填" validator="required"  id="E5" class="input_text"/></td>
            </tr>
            <tr>
                <td class="dialog-module-title">页面:</td>
                <td><input type="text" name="E6" id="E6" class="input_text"/></td>
            </tr>
            <tr>
                <td class="dialog-module-title">搜索值提示:</td>
                <td><input type="text" name="E7" id="E7" class="input_text"/></td>
            </tr>
            <tr>
                <td class="dialog-module-title">表单ID:</td>
                <td><input type="text" name="E8" id="E8" class="input_text"/></td>
            </tr>
            <tr>
                <td class="dialog-module-title">备注:</td>
                <td><input type="text" name="E9" id="E9" class="input_text"/></td>
            </tr>
            </tbody>
        </table>
    </div>

    <div id="search-module">
        <form id="searchForm" name="searchForm" class="submitReturnFalse">
            <div style="padding:0">
                父级：
                <select name="E2" id="E2">
                    <option value=""><{t}>pleaseSelected<{/t}></option>
                    <{foreach from=$parentArr item=val}>
                    <option value="<{$val.sf_id}>"><{$val.urName}>--<{$val.search_label}></option>
                    <{/foreach}>
                </select>
                &nbsp;&nbsp;<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch"/>
                <input type="button" id="createButton" value="<{t}>create<{/t}>" class="baseBtn"/>
            </div>
        </form>
    </div>

    <div id="module-table">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
            <tr class="table-module-title">
                <td width="3%" class="ec-center">ID</td>
                <td>页面</td>
                <td>父级</td>
                <td>搜索类型</td>
                <td>搜索值</td>
                <td>搜索值排序</td>

                <td>搜索值提示</td>
                <td>表单ID</td>
                <td>备注</td>
                <td><{t}>operate<{/t}></td>
            </tr>
            <tbody id="table-module-list-data"></tbody>
        </table>
    </div>
    <div class="pagination"></div>
</div>
