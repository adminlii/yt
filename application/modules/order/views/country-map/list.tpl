<script type="text/javascript"> 
    EZ.url = '/order/country-map/';
    EZ.getListData = function (json) {
        var html = '';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
            html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
            html += "<td class='ec-center'>" + (i++) + "</td>";

            html += "<td class='' >"+val.original_countryname+ "</td>";
            html += "<td >" + val.country_code+" [" + val.country_cnname +"&nbsp;"+val.country_enname+ "]</td>";
            html += "<td >" + val.cmp_createdate + "</td>";
            html += "<td >";
            html += "<a href='javascript:;' class='delBtn'  cmp_id='"+val.cmp_id+"'>"+EZ.del+"</a>";
            html += "</td>";
            html += "</tr>";
        });
        return html;
    };

    $(function(){
        $('.delBtn').live('click',function(){
            var cmp_id = $(this).attr('cmp_id');
            if(!window.confirm('删除对照关系?')){
            	return;
            }
        	$.ajax({
    			type: "POST",
    			async: false,
    			dataType: "json",
    			url: '/order/country-map/delete',
    			data: {'cmp_id':cmp_id},
    			error: function () {
    				return;
    			},
    			success: function (json) {
    				alertTip(json.message);
    				initData(0);
    			}

    		});
        })
        
       $('#createBtn').live('click',function(){
    	   var url = "/order/country-map/edit";
    	   var title = "新增自定义国家";
    	   var reLoadWhenClose = 1
    	   openIframeDialogNew(url,600,350,title,'order/country-map/list',0,0);
    	   //openIframeDialogNew(url, width, height,title,quickId,page,pageSize)
       }) 
        
        
    });
    
    
    
</script>
<script>
$(function(){ 
	$('#country_code').chosen({search_contains:true});
})
</script>
<style>
#editRelationForm tr {
	height: 30px;
}

.delPcrSku {
	margin-left: 25px;
	display: none;
}

.searchFilterText {
	width: 100px;
}
</style>

<div id="module-container">
	<div id="ez-wms-edit-dialog" title="上传产品模板" style="display: none;"></div>
	<div id="search-module">
		<form id="searchForm" name="searchForm" class="submitReturnFalse">
			<div class="search-module-condition">
				<span class="searchFilterText"><{t}>自定义国家<{/t}>：</span>
				<!-- 平台销售SKU -->
				<input type="text" name="original_countryname" id="original_countryname" class="input_text keyToSearch" style="text-transform: uppercase; width: 262px;" placeholder="<{t}>like_search_before&after<{/t}>" />
			</div>
			
			<div class="search-module-condition">
				<span class="searchFilterText"><{t}>标准国家<{/t}>：</span>
				<!-- 店铺帐号 -->
				<select id="country_code" name="country_code" class="input_text_select" style="width: 432px;">
					<option value=''><{t}>all<{/t}></option>
					<!-- 全部 -->
					<{foreach from=$country name=ob item=ob}>
					<option value='<{$ob.country_code}>'><{$ob.country_code}> <{$ob.country_cnname}> <{$ob.country_enname}></option>
					<{/foreach}>
				</select>
			</div>
		
			<div class="search-module-condition">
				<span class="searchFilterText">&nbsp;</span>
				<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" style='' />
				<input id="createBtn" class="baseBtn" type="button" value="添加">
			</div>
		</form>
	</div>
	<div id="module-table" style="margin-top: 5px;">		
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
			<tr class="table-module-title">
				<td width="3%" class="ec-center">NO.</td>
				<td><{t}>自定义国家<{/t}></td>
				<td><{t}>标准国家<{/t}></td>
				<td><{t}>createDate<{/t}></td>
				<td><{t}>operate<{/t}></td>
			</tr>
			<tbody id="table-module-list-data"></tbody>
		</table>
	</div> 
</div>