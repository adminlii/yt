<script type="text/javascript"> 
    EZ.url = '/product/promotional-set/';
    EZ.getListData = function (json) {
        var html = '';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
            html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
            html += "<td class='ec-center'>" + (i++) + "</td>";

            html += "<td >" + val.item_id + "</td>";
            html += "<td >" + val.user_account + "</td>";
            html += "<td >" + val.percent+ "</td>";
            html += "<td >" + val.start_time+ "</td>";
            html += "<td >" + (val.end_time)+ "</td>";
            html += "<td >" + val.user_name + "</td>";
            html += "<td >" + val.status_title + "</td>";
            html += "<td >" + val.log + "</td>";
            if(val.auth){
                html += "<td class=\"center\">";
                html += "<a href=\"javascript:editById(" + val.sips_id + ")\">" + EZ.edit + "</a>&nbsp;&nbsp;";
                html += "|&nbsp;&nbsp;<a href=\"javascript:deleteById(" + val.sips_id + ")\">" + EZ.del + "</a>&nbsp;&nbsp;";
                html += "|&nbsp;&nbsp;<a href=\"javascript:sync(" + val.sips_id + ")\">"+$.getMessage('synchronized_to_eBay')+"</a>&nbsp;&nbsp;";//同步到eBay
                html += "</td>";
            }else{
                html += "<td class=\"center\">";
                html += "<span>" + EZ.edit + "</span>&nbsp;&nbsp;";
                html +="|&nbsp;&nbsp;<span>" + EZ.del + "</span>";
                html += "</td>";
            }
            
            html += "</tr>";
        });
        return html;
    }

    function loadStart(){
        //'数据处理中，请稍候'
        alertTip($.getMessage('sys_common_wait'),'600');
        setTimeout(function(){
        	$('.ui-button',$('#dialog-auto-alert-tip').parent()).hide();
        	$('#dialog-auto-alert-tip').dialog('option','closeOnEscape',false);   
        },100) 
    }

    function loadEnd(html){
    	$('#dialog-auto-alert-tip').dialog('option','title',$.getMessage('sys_common_complete'));//'完成'      	
      	$('.ui-dialog-content',$('#dialog-auto-alert-tip').parent()).html(html);
       	$('.ui-button',$('#dialog-auto-alert-tip').parent()).show();
    	$('#dialog-auto-alert-tip').dialog('option','closeOnEscape',true);
    }
    function sync(sips_id){
    	if(!window.confirm('Are You Sure?')){
            return;
        }
    	loadStart()
    	$.ajax({
            type: "post",
            async: true,
            dataType: "json",
            data:{'sips_id':sips_id},
            url: '/product/promotional-set/sync-single/',
            success: function (json) {	            	 
         	 var html = json.message;
         	 $.each(json.errors,function(k,v){
        		 html+="<p>[ItemID:"+json.item_id+"]"+v+"</p>";
        	 });
             loadEnd(html,800);
             
             if(json.ask){
            	 initData(paginationCurrentPage-1);
             }
            }
        });
    }

    $(function(){
        initData(0);   
        $('#uploadDiv').dialog({
            autoOpen: false,
            width: 600, 
            modal: true,
            show: "slide",           
            close: function () {
                
            }
        }); 
    });
    
    function upload(){
        $("#uploadDiv").dialog('open');
    }
    $(function(){
        $('.viewHistoryPromonationBtn').click(function(){
        	var w = parent.windowWidth();
	        var h = parent.windowHeight();
	        var url='/product/promotional/list/';
	        //'查看历史折扣记录'
	        parent.leftMenu('view_history_discounts',$.getMessage('view_history_discounts'),url);
	    	//parent.openIframeDialogNew(url,w-300,h-140,$.getMessage('view_history_discounts'));
        });        
    })

</script>
<style>
#editRelationForm tr {
	height: 30px;
}

.delPcrSku {
	margin-left: 25px;
}

.dialog-edit-alert-tip .dialog-module td input{width:280px;}
</style>
<!-- 折扣文件上传 -->
<div id='uploadDiv' class='dislog_div' title='<{t}>discounts_file_upload<{/t}>' style='display: none;'>
	<{include file='product/views/promotional-set/promotional_import.tpl'}>
</div>

<!-- 查看、编辑 -->
<div id="module-container">
	<div id="ez-wms-edit-dialog" style="display: none;">
		<table class="dialog-module" border="0" cellpadding="0" cellspacing="0">
			<tbody>
				<input type="hidden" name="sips_id" id="sips_id" value="" />
				<tr>
					<td class="dialog-module-title"><span style="color:#F2683E;"><{t}>notice<{/t}><!-- 注意 -->：</span></td>
					<td>
						<!-- 此处的时间为eBay时间(比北京时间慢8个小时)，请注意个站点间的时差. -->
						<{t}>discount_tips_02<{/t}>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><span style="color:#1B9301;"><{t}>examples<{/t}><!-- 举例 -->：</span></td>
					<td>
						<!-- 德国比eBay时间快2个小时，英国比eBay时间快1个小时. -->
						<{t}>discount_tips_03<{/t}>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>ItemID<{/t}>:</td>
					<td>
						<input type="text" name="item_id" id="item_id" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" placeholder='ItemID'/>
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>StartTime<{/t}>(eBay):</td>
					<td>
						<input type="text" name="start_time" id="start_time" validator="required" err-msg="<{t}>require<{/t}>" class="input_text"  placeholder='<{t}>format_ebay_time<{/t}>：2013-04-15 12:12'/>
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>EndTime<{/t}>(eBay):</td>
					<td>
						<input type="text" name="end_time" id="end_time" validator="required" err-msg="<{t}>require<{/t}>" class="input_text"  placeholder='<{t}>format_ebay_time<{/t}>：2013-04-15 12:12'/>
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>Percent(%)<{/t}>:</td>
					<td>
						<input type="text" name="percent" id="percent" validator="required" err-msg="<{t}>require<{/t}>" class="input_text"  placeholder='<{t}>set_the_number_of_discount_tips<{/t}>'/><!-- 折扣百分比，如5%请填写数字5 -->
						<span class="msg">*</span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="search-module">
		<form id="searchForm" name="searchForm" class="submitReturnFalse">
			<div class="search-module-condition">
				<span style="width: 90px;" class="searchFilterText"><{t}>ebay_account<{/t}>：</span><!-- ebay账号 -->
				<select name="user_account">
					<option value=''><{t}>all<{/t}></option>
					<{foreach from=$user_account_arr name=ob item=ob}>
					<option value='<{$ob.user_account}>'><{$ob.platform_user_name}></option>
					<{/foreach}>
				</select>
			</div>
			<div class="search-module-condition">
				<span style="width: 90px;" class="searchFilterText"><{t}>sync_status<{/t}>：</span><!-- 同步状态 -->
				<select name="status">
					<option value=''><{t}>all<{/t}></option>
					<{foreach from=$syncStatus name=ob item=ob key=k}>
					<option value='<{$k}>'><{$ob}></option>
					<{/foreach}>
				</select>
			</div>
			<div class="search-module-condition">
				<span style="width: 90px;" class="searchFilterText">ItemID：</span><!-- ItemID -->
				<input type="text" name="item_id" class="input_text keyToSearch" placeholder='多个ItemID用空格隔开'/>
			</div>
			<div style="padding: 0">
				<!-- 
				<span class="" style="width: 90px;">&nbsp;&nbsp;&nbsp;&nbsp;eBay帐号：</span>
				<select name="user_account">
					<option value=''><{t}>all<{/t}></option>
					<{foreach from=$user_account_arr name=ob item=ob}>
					<option value='<{$ob.user_account}>'><{$ob.platform_user_name}></option>
					<{/foreach}>
				</select>
				
				&nbsp;&nbsp;
				<span class="" style="width: 90px;">&nbsp;&nbsp;&nbsp;&nbsp;同步状态 ：</span>
				<select name="status">
					<option value=''><{t}>all<{/t}></option>
					<{foreach from=$syncStatus name=ob item=ob key=k}>
					<option value='<{$k}>'><{$ob}></option>
					<{/foreach}>
				</select>
				&nbsp;&nbsp; ItemID：
				<input type="text" name="item_id" class="input_text keyToSearch" placeholder='多个ItemID用空格隔开'/>
				&nbsp;&nbsp;
				 -->
				<div class="search-module-condition">
					<span style="width: 90px;" class="searchFilterText">&nbsp;</span>
					<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" />
				</div>
				<!-- 
				<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" />
                <input type="button" id="createButton" value="<{t}>create<{/t}>" class="baseBtn"/>
				<input type="button" class="baseBtn viewHistoryPromonationBtn" value="查看ebay历史折扣 " style='float: right;margin-left:8px;'>
				<input type="button" class="baseBtn uploadBtn" value="折扣文件上传" onclick='upload();' style='float: right;'>
				 -->
			</div>
		</form>
	</div>
	<div id="module-table">
		<div class="opration_area0">
			<div class="opration_area">
				<div class="opDiv">
					<input type="button" id="createButton" value="<{t}>create<{/t}>" class="baseBtn"/>
					<!-- 折扣文件上传 -->
					<input type="button" class="baseBtn uploadBtn" value="<{t}>discounts_file_upload<{/t}>" onclick='upload();'>
					<!-- 查看历史折扣 -->
					<input type="button" class="baseBtn viewHistoryPromonationBtn" value="<{t}>view_history_discounts<{/t}>" style='float: right;margin-left:8px;'>
				</div>
			</div>
		</div>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
			<tr class="table-module-title">
				<td width="3%" class="ec-center">NO.</td>
				<td>ItemID</td>
				<td><{t}>user_account<{/t}></td><!-- 账号 -->
				<td><{t}>discount_percentage<{/t}></td><!-- 折扣百分比 -->
				<td width="110"><{t}>start_time<{/t}></td><!-- 开始时间 -->
				<td width="110"><{t}>end_time<{/t}></td><!-- 结束时间 -->
				<td><{t}>created_id<{/t}></td><!-- 创建人 -->
				<td><{t}>sync_status<{/t}></td><!-- 同步状态 -->
				<td><{t}>sync_results<{/t}></td><!-- 同步结果 -->
				<td><{t}>operate<{/t}></td>
			</tr>
			<tbody id="table-module-list-data"></tbody>
		</table>
	</div>
	<div class="pagination"></div>
</div>