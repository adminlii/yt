<script type="text/javascript">
EZ.url = '/auth/run-control/';
EZ.getListData = function (json) {
    var html = '';
    var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
    $.each(json.data, function (key, val) {
        html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
        html += "<td class='ec-center'>" + (i++) + "</td>";
        //html += "<td >" + val.company_code + "</td>";
        html += "<td >" + val.platform + "</td>";
        html += "<td >" + val.user_account + "</td>";
        html += "<td >" + val.run_app + "</td>";
        html += "<td >" + val.start_time + "</td>";
        html += "<td >" + val.end_time + "</td>";
        html += "<td >" + val.run_interval_minute + "</td>";
        html += "<td >" + val.last_run_time + "</td>";
        html += "<td >" + val.status + "</td>";
        html += "<td>";
        html += "<a href=\"javascript:editById(" + val.run_id + ")\">" + EZ.edit + "</a>";
        html +="&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:deleteById(" + val.run_id + ")\">" + EZ.del + "</a>";

        html +="</td>";
        html += "</tr>";
    });
    return html;
}
$(function(){
	initData(0);
	$('.readonly').attr('readonly','readonly').css('background','#eee');

	$('#initAcc').click(function(){
	    $('#init_acc_div').dialog('open');
	});
	$('#init_acc_div').dialog(
			{
		        autoOpen: false,
		        width: 600,
		        maxHeight: 400,
		        modal: true,
		        show: "slide",
		        buttons: [
		            {
		                text: 'Submit',
		                click: function () {
		                	if(!window.confirm("如果账户已经存在，将会删除原先的定时任务\n然后建立新的定时任务,新的定时任务起始时间为当前时间的前3个月,\n你确定继续吗")){
		                        return;
		                    }
		                    if(!window.confirm('真的确认对该账号初始化定时任务？')){
		                        return;
		                    }
		                    if(!window.confirm('真的？')){
		                        return;
		                    }
		                    $(this).dialog("close");
		                    var param = $('#init_acc_form').serialize();
		                	$.ajax({
		            	        type:'post',
		            	        dataType:'html',
		            	        data:param,
		            	        url: '/auth/run-control/init-acc',
		            	        error:function(){},
		            	        success:function(html){    				
		            				alertTip(html,800);
		            				initData(paginationCurrentPage-1);
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
		        }
		    }
	);
	
})
</script>
<div id="module-container">
	<div id='init_acc_div' title='账号初始化' style='display: none;'>
		<form action="" onsubmit='return false' id='init_acc_form'>
			<table class="dialog-module dialog-edit-alert-tip" border="0" cellpadding="0" cellspacing="0">
				<tbody>
					<tr style='height: 35px;'>
						<td class="dialog-module-title"><{t}>平台<{/t}>:</td>
						<td>
							<select name='platform' class="input_select">
								<option value='ebay'>ebay</option>
								<option value='paypal'>paypal</option>
								<option value='amazon'>amazon</option>
							</select>
							<span class="msg">*</span>
						</td>
					</tr>
					<tr style='height: 35px;'>
						<td class="dialog-module-title"><{t}>账号<{/t}>:</td>
						<td>
							<input type="text" name="acc" class="input_text keyToSearch" />
							<span class="msg">*</span>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
	<div id="search-module">
		<form id="searchForm" name="searchForm" class="submitReturnFalse">
			<input type="hidden" value="" id="filterActionId" />
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;">平台：</span>
				<select name="platform" class="input_select">
					<option value=''>全部</option>
					<{foreach from=$platform item=o name=o key=k}>
					<option value='<{$o.platform}>'><{$o.platform_name}></option>
					<{/foreach}>
				</select>
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;">账号：</span>
				<select name="user_account" class="input_select">
					<option value=''>全部</option>
					<{foreach from=$platformUser item=o name=o key=k}>
					<option value='<{$o.user_account}>'>[<{$o.platform}>]<{$o.platform_user_name}>(<{$o.user_account}>)</option>
					<{/foreach}>
				</select>
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;">任务名：</span>
				<input type="text" name="run_app" class="input_text keyToSearch" />
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;"></span>
				<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" />
				<input type="button" style="float: right; margin-left: 5px;" id="initAcc" value="账号初始化" class="baseBtn">
			</div>
		</form>
	</div>
	<div id="module-table">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
			<tr class="table-module-title">
				<td width="3%" class="ec-center">NO.</td>
				<!-- 
				<td>company_code</td>
				 -->
				<td>平台</td>
				<td>账号</td>
				<td>任务名</td>
				<td>开始时间</td>
				<td>结束时间</td>
				<td>每次时间长度(分钟)</td>
				<td>最后一次运行时间</td>
				<td>状态</td>
				<td><{t}>operate<{/t}></td>
			</tr>
			<tbody id="table-module-list-data"></tbody>
		</table>
	</div>
	<div class="pagination"></div>
	<div id="ez-wms-edit-dialog" style="display: none;">
		<table class="dialog-module" border="0" cellpadding="0" cellspacing="0">
			<tbody>
				<input type="hidden" name="run_id" id="run_id" value="" />
				<tr>
					<td class="dialog-module-title"><{t}>platform<{/t}>:</td>
					<td>
						<input type="text" name="platform" id="platform" validator="required" err-msg="<{t}>require<{/t}>" class="input_text readonly" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>run_app<{/t}>:</td>
					<td>
						<input type="text" name="run_app" id="run_app" validator="required" err-msg="<{t}>require<{/t}>" class="input_text readonly" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>company_code<{/t}>:</td>
					<td>
						<input type="text" name="company_code" id="company_code" validator="required" err-msg="<{t}>require<{/t}>" class="input_text readonly" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>user_account<{/t}>:</td>
					<td>
						<input type="text" name="user_account" id="user_account" validator="required" err-msg="<{t}>require<{/t}>" class="input_text readonly" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>start_time<{/t}>:</td>
					<td>
						<input type="text" name="start_time" id="start_time" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>end_time<{/t}>:</td>
					<td>
						<input type="text" name="end_time" id="end_time" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>run_interval_minute<{/t}>:</td>
					<td>
						<input type="text" name="run_interval_minute" id="run_interval_minute" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>last_run_time<{/t}>:</td>
					<td>
						<input type="text" name="last_run_time" id="last_run_time" validator="required" err-msg="<{t}>require<{/t}>" class="input_text " />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>status<{/t}>:</td>
					<td>
						<select name='status' id='status' class="input_select">
							<option value='0'>禁用</option>
							<option value='1'>启用</option>
						</select>
						<span class="msg">*</span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>