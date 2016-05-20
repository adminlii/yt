<script type="text/javascript">
$(function(){
	$("#orderSubmitBtn").click(function(){
		loadStart();
		var params = $("#submiterForm").serialize();
		$.ajax({
		    type: "post",
		    async: false,
		    dataType: "json",
		    url: '/order/submiter/edit',
		    data: params,
		    success: function (json) {
		    	loadEnd();
		    	switch (json.state) {
		            case 1:
		            	
		            case 2:
			            parent.getData();
		                parent.alertTip(json.message);
		                parent.$('.dialogIframe').dialog('close');
		                
		                break;
		            default:
		                var html = '';
		                if (json.errorMessage == null)return;
		                $.each(json.errorMessage, function (key, val) {
		                    html += '<span class="tip-error-message">' + val + '</span>';
		                });
		                alertTip(html);
		                break;
		        }
		    }
		});
	});

});

function editShipperAccount(shipper_account){	 
    $.ajax({
		   type: "POST",
		   url: '/order/submiter/get-by-json',
		   data: {'paramId':shipper_account},
		   async: true,
		   dataType: "json",
		   success: function(json){
			    if(json.state){
                    $.each(json.data,function(k,v){
                        v +='';
                        $("#submiterForm :input[name='"+k+"']").val(v); 			    	    
                    })
                    $("#submiterForm :checkbox[name='E17']").val('1');
                    if(json.data.E17==1||json.data.E17=='1'){
                    	$("#submiterForm :checkbox[name='E17']").attr('checked', true);
                    }else{
                    	$("#submiterForm :checkbox[name='E17']").attr('checked', false);
                    }  
					//setTimeout(function(){
	                	$('.input_select').chosen({width:'350px',search_contains:true});      
					//},50);
                	            
			    }else{
			       alertTip(json.message);
			    }
		   }
	});
}
<{if $smarty.get.shipper_account}>
editShipperAccount('<{$smarty.get.shipper_account}>');
<{else}>
$(function(){
	$('.input_select').chosen({width:'350px',search_contains:true});
});
<{/if}>
</script>
<style>
.module-title {
	text-align: right;
}

label {
	cursor: pointer;
}
</style>
<form id="submiterForm" onsubmit="return false;" action="" method="POST">
	<h2 style='margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC; line-height: 40px;'><{t}>新增发件人信息<{/t}></h2>
	<table cellspacing="0" cellpadding="0" width="100%" border="0" style="margin-top: 10px;" class="table-module">
		<tbody class="">
			<tr class="table-module-b1">
				<td class="module-title"><{t}>公司名<{/t}>：</td>
				<td>
					<input type="text" name="E3" value="" class="input_text">
					<span class="red">*</span>
				</td>
			</tr>
			<tr class="table-module-b2">
				<td class="module-title" width='100'><{t}>发件人姓名<{/t}>：</td>
				<td>
					<input type="text" name="E2" value="" class="input_text">
					<span class="red">*</span>
				</td>
			</tr>
			<tr class="table-module-b1">
				<td class="module-title"><{t}>发件人国家<{/t}>：</td>
				<td>
					<{ec name='E4' default='Y' search='N' class='input_select'}>country<{/ec}>
					<span class="red">*</span>
				</td>
			</tr>
			<tr class="table-module-b2">
				<td class="module-title"><{t}>省/州<{/t}>：</td>
				<td>					 
					<input type="text" name="E5" value="" class="input_text">
					<span class="red">*</span>
				</td>
			</tr>
			<tr class="table-module-b1">
				<td class="module-title"><{t}>城市<{/t}>：</td>
				<td>					 
					<input type="text" name="E6" value="" class="input_text">
					<span class="red">*</span>
				</td>
			</tr>
			<tr class="table-module-b2">
				<td class="module-title"><{t}>联系地址<{/t}>：</td>
				<td>
					<input type="text" name="E7" value="" class="input_text">
					<span class="red">*</span>
				</td>
			</tr>
			<tr class="table-module-b1">
				<td class="module-title"><{t}>邮编<{/t}>：</td>
				<td>
					<input type="text" name="E8" value="" class="input_text">
					<span class="red">*</span>
				</td>
			</tr>
			<tr class="table-module-b2">
				<td class="module-title"><{t}>联系电话<{/t}>：</td>
				<td>
					<input type="text" name="E10" value="" class="input_text">
					<span class="red">*</span>
				</td>
			</tr>
			<tr class="table-module-b1">
				<td class="module-title">&nbsp;</td>
				<td>
					<label><input type="checkbox" value="1" name="E17" class=" "><{t}>设为默认地址<{/t}></label>
				</td>
			</tr>
			<tr class="table-module-b1">
				<td class="module-title">&nbsp;</td>
				<td>
					<input type="hidden" name="E0" value="" class="input_text">
					<input type="button" style="width: 80px;" class="baseBtn submitBtn" id="orderSubmitBtn" value="<{t}>提交<{/t}>">
				</td>
			</tr>
		</tbody>
	</table>
</form>