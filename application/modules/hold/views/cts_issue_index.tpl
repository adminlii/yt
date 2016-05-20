<style>
	.searchFilterText{
		width:90px;
	}
.Tab ul li {
	padding: 0 0px;
}

.Tab ul li li {
	background: none repeat scroll 0 0 #FFFFFF;
	border: 0 none;
	float: none;
	height: 26px;
	line-height: 26px;
}
.Tab .sub_status_container {
	font-weight: normal;
	background: none repeat scroll 0 0 #FFFFFF;
	display: none;
	left: 0;
	position: absolute;
	text-align: center;
	border: 1px solid #CCCCCC;
	border-bottom: 0 none;
	margin-left: -1px;
	width: 100%;
	z-index: 10;
}

.statusTag {
	display: block;
	padding: 0 15px;
}

.statusTag .count {
	color: red;
}

.Tab a:hover {
	color: #000;
	border-bottom: 2px solid #87B87F;
	box-shadow: 0 0 1px #87B87F;
}

.Tab .chooseTag {
	font-weight: bold;
	color: #393939;
	border-bottom: 2px solid #87B87F
}

#module-container .Tab  .sub_status_container dt {
	border-bottom: 1px solid #CCCCCC;
}

#module-container .Tab  .sub_status_container a {
	font-weight: normal;
	padding: 0;
}

#module-container .Tab  .sub_status_container a:hover {
	color: #393939;
}

#module-container .Tab  .sub_status_container .selected {
	font-weight: bold;
	color: #000;
}
</style>
<script type="text/javascript">
    EZ.url = '/hold/cts-issue/';
    EZ.getListData = function (json) {
        var html = '';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
            html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
            html += "<td class='ec-center'>" + (i++) + "</td>";
                    html += "<td >" + val.shipper_hawbcode + "</td>";
                    html += "<td >" + val.server_hawbcode + "</td>";
                    html += "<td >" + val.product_cnname + "</td>";
                    html += "<td >" + (val.issuekind_cnname ? val.issuekind_cnname :'') + "</td>";
                    html += "<td >" + val.issue_status_cnname + "</td>";
                    html += "<td >" + (val.st_name ? val.st_name :'') + "</td>";
                    html += "<td >" + val.isu_createdate + "</td>";
            html += "<td><a href=\"javascript:getReponseMessage(" + val.issue_id + ")\">" + '查看' + "</a></td>";
            html += "</tr>";
        });
        $("#count_").html(json.total);
        return html;
    }

    $(function(){
    	/**
    	 * 订单状态切换
    	 */
    	$("#module-container .statusTag").live('click', function() {
    		var obj = $(this);
    		var status = obj.attr('status');
    		$(".statusTag").removeClass('chooseTag');
    		if(status==''){
    			$(".statusTagAll").addClass('chooseTag');			
    		}else{
    			$(".statusTag"+status).addClass('chooseTag');			
    		}

    		$('#issue_status').val(obj.attr('status'));	
    		$('.submitToSearch:visible').click();
    	});
    });

    setTimeout(function(){
    	$(".statusTagN").addClass('chooseTag');
    	submitSearch();
//     	$(".submitToSearch").trigger("click");
    },100);
    
//     $("");submitToSearch

    //获取问题消息
    function getReponseMessage(issId){
    	var w = parent.windowWidth();
        var h = parent.windowHeight();
    	parent.openIframeDialog('/hold/cts-issue/get-response-message?isId='+issId,720,550,"问题件处理");
    }
    
    $(function(){
    	$('.input_select').chosen({width:'260px',search_contains:true});
    })

</script>
<div id="module-container">
    <div id="ez-wms-edit-dialog" style="display:none;">
        <table class="dialog-module" border="0" cellpadding="0" cellspacing="0">
            <tbody>
            <input type="hidden" name="E0" id="E0" value=""/>
            
            </tbody>
        </table>
    </div>
	
    <div id="search-module">
        <form id="searchForm" name="searchForm" class="submitReturnFalse">
            <div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;">单号：</span>
				<input type="text" name="code" id="code" class="input_text keyToSearch"  style = "width:250px;" placeholder="支持客户单号、服务商单号模糊查询"/>
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;">问题类型：</span>
				<select name="issue_kind_code" class="input_select">
					<option value=''>全部</option>
					<{foreach from=$kind item=stat name=stat key=k}>
						<option value='<{$stat.issuekind_code}>'><{$stat.issuekind_cnname}></option>
					<{/foreach}>
				</select>
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;"></span>
				<input type="button" name = "code" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" />
			</div>
			<input name='issue_status' value='N' type='hidden'  id = "issue_status" />
        </form>
    </div>

    <div id="module-table">
    	<div class="Tab" style = "margin-bottom: 2px;">
				<ul>
					<{foreach from=$statusArr item=val key=key}>
					<{if $key != 'O' && $key != 'C'}>
					<li style='position: relative;' class='mainStatus'>
						<a href="javascript:void (0)" class="statusTag <{if <{$key}>!=''}>statusTag<{<{$key}>}><{else}>statusTagAll<{/if}>" status='<{$key}>' abnormal_type='' process_again=''>
							<span class='order_title<{$key}>' title='<{$val}>'><{$val}></span>
						</a>
					</li>
					<{/if}>
					<{/foreach}>
				</ul>
				<div class="pageTotal" style="text-align: right; float: right; line-height: 25px; padding: 0 5px;color:red;"><b>共&nbsp;<span id = "count_">0</span>&nbsp;条</b></div>
			</div>
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
            <tr class="table-module-title">
                <td width="3%" class="ec-center">NO.</td>
		       <td>客户单号</td>
		       <td>服务商单号</td>
		       <td>产品</td>
		       <td>问题类型</td>
		       <td>状态</td>
		       <td>处理人</td>
		       <td>发生时间</td>
                <td><{t}>operate<{/t}></td>
            </tr>
            <tbody id="table-module-list-data"></tbody>
        </table>
    </div>
    <div class="pagination"></div>
</div>
