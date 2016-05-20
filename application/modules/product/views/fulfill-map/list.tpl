<script type="text/javascript"> 
    EZ.url = '/product/fulfill-map/';
    EZ.getListData = function (json) {
        var html = '';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
            html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
            html += "<td class='ec-center'>" + (i++) + "</td>";

            html += "<td >" + val.shipping_method + "</td>";
            html += "<td >[" +val.country_code +"]"+val.country_name + "</td>";
            html += "<td >" + val.shipping_method_mark+" " + "</td>";
            html += "<td >" + val.notify_customer+" " + "</td>";            
            html += "<td >";
            html += "<a href='javascript:editById("+val.id+")'>"+EZ.edit+"</a>&nbsp;&nbsp;";
            html += "<a href='javascript:deleteById("+val.id+")' class='delBtn'>"+EZ.del+"</a>";
            html += "</td>";
            html += "</tr>";
        });
        return html;
    }

    $(function(){
        initData(0);   
        $('#uploadDiv').dialog({
            autoOpen: false,
            width: 500, 
            height:180,
            modal: true,
            show: "slide",           
            close: function () {
                initData(paginationTotal-1);   
            }
        }); 

    });
    
    function upload(){
        $("#uploadDiv").dialog('open');
    }
    

</script>
<script>
$(function(){

})
</script>
<style>
#editRelationForm tr {
	height: 30px;
}

.delPcrSku {
	margin-left: 25px;
}
</style>
<!-- SKU关系上传 -->
<div id='uploadDiv' class='dialog_div' title='<{t}>Batch_Upload<{/t}>' style='display: none;'><{include file='product/views/fulfill-map/upload.tpl'}></div>
<div id="module-container">
	<div id="ez-wms-edit-dialog" style="display: none;">
		<table class="dialog-module" border="0" cellpadding="0" cellspacing="0">
			<tbody>
				<input type="hidden" name='id' value="" />
				<tr>
					<td class="dialog-module-title"><{t}>shipping_method<{/t}>:</td>
					<td>
						<input type="text" name="shipping_method" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>country_code<{/t}>:</td>
					<td>
					    <select name="country_code">
					    <{foreach from=$country name=c item=c}>
					        <option value='<{$c.country_code}>'>[<{$c.country_code}>]<{$c.country_name}></option>
					    <{/foreach}>
					    </select>
						<!-- <input type="text" name="country_code" validator="required " err-msg="必填" class="input_text" /> -->
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>shipping_method_mark<{/t}>:</td>
					<td>
						<input type="text" name="shipping_method_mark" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>notify_customer<{/t}>:</td>
					<td>
						 <select name="notify_customer">
					        <option value='N'>N</option>
					        <option value='Y'>Y</option>
					    </select>
						<span class="msg">*</span>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="search-module">
		<form id="searchForm" name="searchForm" class="submitReturnFalse">
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 120px;"><{t}>shippingMethod<{/t}>：</span>
				<input type="text" name="shipping_method" class="input_text keyToSearch" />
				<{t}>country_code<{/t}>:
				<select name="country_code">
		        <option value=''><{t}>-all-<{/t}></option>
			    <{foreach from=$country name=c item=c}>
			        <option value='<{$c.country_code}>'><{$c.country_code}>[<{$c.country_name}>]</option>
			    <{/foreach}>
			    </select>
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 120px;">&nbsp;</span>
				<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" />
			</div>
		</form>
	</div>
	<div id="module-table">
		<div class="opration_area">
    		<div style="width: 270px;float: right;">
				<input type="button" class="baseBtn uploadBtn" value="<{t}>Batch_Upload<{/t}>" onclick='upload();' style='float: right;'>
    		</div>
    	</div>	
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
			<tr class="table-module-title">
				<td width="3%" class="ec-center">NO.</td>
				<td><{t}>shipping_method<{/t}></td>
				<td><{t}>country_code<{/t}></td>
				<td><{t}>shipping_method_mark<{/t}></td>
				<td><{t}>notify_customer<{/t}></td>
				<td><{t}>operate<{/t}></td>
			</tr>
			<tbody id="table-module-list-data"></tbody>
		</table>
	</div>
	<div class="pagination"></div>
</div>