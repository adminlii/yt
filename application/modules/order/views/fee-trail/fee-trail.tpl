<script type="text/javascript"> 
    EZ.url = '/order/fee-trail/';
    var data = {};
    var country_code = '';
    EZ.getListData = function (json) {
        var html = '';
        if(json.ask){
        	 data = json.data;
        	 country_code = json.country_code;
	       	 var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
	         $.each(json.data, function (key, val) {
	             html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
	             html += "<td class='ec-center'>" + (i++) + "</td>";
	             html += "<td >" + val.ServiceCnName + "</td>";
	             //html += "<td >" + val.ServiceEnName + "</td>";
	             html += "<td >" + val.Effectiveness + "</td>";
	             html += "<td >" + val.FreightFee+" " + "</td>";
	             html += "<td >" + val.FuelFee + "</td>";
	             html += "<td >" + val.RegisteredFee + "</td>";
	             html += "<td >" + val.OtherFee+ "</td>"; 
	             //html += "<td >" + val.ProductSort + "</td>";
	             html += "<td >" + val.ChargeWeight + "</td>";
	             html += "<td >" ;
	             html += "<span style='font-weight:bold;color:green;'>"+val.TotalFee+'</span>';
	             html += "</td>";
	             html += "<td >" + val.Traceability + "</td>";
	             html += "<td >" + val.VolumeCharge + "</td>";
	             html += "<td >" + val.Remark + "</td>";
	             html += "<td ><a href='javascript:;' class='createOrderBtn' index='"+key+"'>立即下单</a></td>";
	             html += "</tr>";
	         });
        }else{
            var colspan = $('.table-module-title td').size();
             html +=  "<tr class='table-module-b1'>";
             html += "<td colspan='"+colspan+"' style='color:red;'>" + json.message + "</td>";
             html += "</tr>";
        }
       
        return html;
    };

    $(function(){
 		$('.createOrderBtn').live('click',function(){ 			
 			//alertTip('功能开发中...');
 			var index = $(this).attr('index');
 			var val = data[index]; 
 			product_code = val.ServiceCode;
 			var url = "/order/order/create?op=fast-create-order&product_code="+product_code+'&country_code='+country_code;
 			var index = $(this).attr('index'); 
 			var cp_order = "快速下单";
 			leftMenu('order-cp-'+product_code+'-'+country_code,cp_order,url);			
 	 	})
    });

    $(function(){
    	$('#search-module .input_select[name="country_code"]').chosen({width:'300px',search_contains:true});
    }) 
</script> 
<style>
#editRelationForm tr{height:30px;}
.delPcrSku{margin-left:25px;display:none;}
.searchFilterText{
	width: 100px;
}
</style>

<div id="module-container">
	<div id="ez-wms-edit-dialog" title="上传产品模板" style="display: none;"></div>
	<div id="search-module">
		<form id="searchForm" name="searchForm" class="submitReturnFalse">
			
			<div class="search-module-condition">
				<span class="searchFilterText">目的国家：</span>
				<select id="user_account" name="country_code" class="input_text_select input_select" style="width: 132px;">
    				<{foreach from=$country name=ob item=ob}>
    				<option value='<{$ob.country_code}>'><{$ob.country_code}> <{$ob.country_cnname}> <{$ob.country_enname}></option>
    				<{/foreach}>
    			</select>
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText"><{t}>weight<{/t}>(KG)：</span>
				<input type="text" name="weight" class="input_text keyToSearch"  style="width:50px;" placeholder="KG"/>
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText"><{t}>length<{/t}> ：</span>
				<input type="text" name="length" class="input_text keyToSearch"  style="width:50px;" placeholder="CM"/>
				&nbsp;&nbsp;<{t}>width<{/t}>&nbsp;&nbsp;
				<input type="text" name="width" class="input_text keyToSearch"  style="width:50px;" placeholder="CM"/>
				&nbsp;&nbsp;<{t}>height<{/t}>&nbsp;&nbsp;
				<input type="text" name="height" class="input_text keyToSearch"  style="width:50px;" placeholder="CM"/>
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText">&nbsp;</span>
				<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" style=''/>
			</div>
			
		</form>
	</div>
	<div id="module-table" style="margin-top: 5px;">
		 
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
			<tr class="table-module-title">
            <td width="3%" class="ec-center">NO.</td>
			<td><{t}>销售产品<{/t}></td>   
			<td><{t}>预估时效<{/t}></td> 
			<td><{t}>运费<{/t}></td> 
			<td><{t}>燃油费<{/t}></td> 
			<td><{t}>挂号费<{/t}></td> 
			<td><{t}>其他费用<{/t}></td>  
			<!-- 
			<td><{t}>产品排序权重值<{/t}></td>  
			 -->
			<!-- 
			<td><{t}>服务英文名称<{/t}></td> 
			 --> 
			<td><{t}>计费重量<{/t}></td>
			<td><{t}>总费用<{/t}></td>  
			<td><{t}>可追踪<{/t}></td>  
			<td><{t}>体积值<{/t}></td> 
			<td width='30%'><{t}>备注<{/t}></td>  
			<td><{t}>操作<{/t}></td>  
			</tr>
			<tbody id="table-module-list-data"></tbody>
		</table>
	</div>
	<div class="pagination"></div>
</div>