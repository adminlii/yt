<script type="text/javascript">
$(function(){
    $('#account_div').dialog({
        autoOpen: false,
        width: 300,
        maxHeight: 500,
        modal: true,
        show: "slide",
        buttons: [
            {
                text: 'Ok',
                click: function () {
                    var this_ = $(this);
                    if($('#user_account option:selected').size()==0){
                        alertTip('请选择需要绑定的店铺账号');
                        return false;
                    }
                    var param = $('#account_form').serialize();
                    $.ajax({
                        type: "post",
                        async: false,
                        dataType: "json",
                        data:param,
                        url: '/auth/service/bind-platform-user',
                        success: function (json) {
                           alertTip(json.message);
                           if(json.ask){
                               this_.dialog("close");//关闭dialog
                               loadData(paginationCurrentPage, paginationPageSize);//刷新结果
                           }
                        }
                    });
                }
            },
            {
                text: 'Close',
                click: function () {
                    $(this).dialog("close");
                }
            }
        ],
        close: function () {
            
        },
        open:function(){
        	$.ajax({
                type: "post",
                async: false,
                dataType: "json",
                url: '/auth/service/get-user-account',
                success: function (json) {
                    var html = '';
                    //html+="<option value=''>不限制店铺</option>";
                    //html+="<option value=''>不绑定店铺</option>";
                    $.each(json,function(k,v){                        
                        html+="<option value='"+v.platform+"*##*"+v.user_account+"*##*"+v.platform_user_name+"'>"+"["+v.platform+"]"+v.platform_user_name+"</option>";
                    });
                    $('#user_account').html(html);
                }
            });
        }
    });
});
function bindAction(id,type){
	$('#user_id').val(id);
	$('#app_type').val(type);
	var title = type=='do'?'操作员绑定店铺':'客服绑定店铺';
	$('#account_div').dialog('open').dialog('option','title',title);
}
    EZ.url = '/auth/service/';
    EZ.getListData = function (json) {
        var html = '';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
            html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
            html += "<td class='ec-center'>" + (i++) + "</td>";            
            html += "<td >" + val.user_code + "</td>";
            html += "<td >" + val.user_name + "</td>";
            html += "<td >" + val.user_name_en + "</td>";
            html += "<td >" + val.user_status + "</td>";
            //html += "<td >" + val.ud_name + "</td>";
            //html += "<td >" + val.up_name + "</td>";
            html += "<td >" ;
            if(val.bind['cs']){
                $.each(val.bind['cs'],function(k,v){
                    html+='<p>'+"["+v.platform+"]"+v.platform_user_name+'</p>';
                })
            }
            html += "</td>";
            html += "<td >" ;
            if(val.bind['do']){
                $.each(val.bind['do'],function(k,v){
                    html+='<p>'+"["+v.platform+"]"+v.platform_user_name+'</p>';
                })
            }
            html += "</td>";
            html += "<td>";


            html +=  "<a href=\"javascript:bindAction(" + val.user_id + ",'cs')\">客服绑定店铺</a>";
            html += "&nbsp;&nbsp;";
            html += "<a href=\"javascript:bindAction(" + val.user_id + ",'do')\">操作员绑定店铺</a>";
           
            
            
            html += "</td>";
            html += "</tr>";
        });
        return html;
    }
</script>
<div class='dialog_div' style='display: none;' id='account_div' title='店铺绑定'>
	请选择需要绑定的店铺账号：
	<form action="" onsubmit='return false;' id='account_form' style='height: 150px;'>
		<select multiple='multiple' id='user_account' name='account[]' style='width: 275px; height: 100%;' title='Ctrl+鼠标左键，可进行多选'>
		</select>
		<input type='hidden' name='user_id' id='user_id' value='' />
		<input type='hidden' name='type' id='app_type' value='' />
	</form>
	<div style='color: red; font-weight: bold;'>Ctrl+鼠标左键，可进行多选</div>
</div>
<div id="module-container">
	<div id="search-module">
		<form id="searchForm" name="searchForm" class="submitReturnFalse">
			<div class="search-module-condition">
				<span style="width: 90px;" class="searchFilterText"><{t}>userCode<{/t}>：</span>
				<input type="text" name="E1" id="E1" class="input_text keyToSearch" />
			</div>
			<div class="search-module-condition">
				<span style="width: 90px;" class="searchFilterText"><{t}>userName<{/t}>：</span>
				<input type="text" name="E3" id="E3" class="input_text keyToSearch" />
			</div>
			<div class="search-module-condition">
				<span style="width: 90px;" class="searchFilterText"><{t}>userNameEn<{/t}>：</span>
				<input type="text" name="E4" id="E4" class="input_text keyToSearch" />
			</div>
			<div class="search-module-condition">
				<span style="width: 90px;" class="searchFilterText"><{t}>状态<{/t}>：</span>
				<select name="E5" class="input_select">
					<option value=''>全部</option>
					<option value='0'>禁用</option>
					<option value='1'>可用</option>
					<option value='2'>未激活</option>
				</select>
			</div>
			<div class="search-module-condition">
				<span style="width: 90px;" class="searchFilterText"></span>
				<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" />
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
				<td>客服绑定账号</td>
				<td>操作员绑定账号</td>
				<td width='220'><{t}>operate<{/t}></td>
			</tr>
			<tbody id="table-module-list-data"></tbody>
		</table>
	</div>
	<div class="pagination"></div>
</div>