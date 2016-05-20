<div class='address_id'>
	<{foreach from=$user_address_list name=s item=s key=k}>
	<p>
		<label> 
		<input type='radio' name='address_id' class='address_id_radio' value='<{$s.address_id}>'<{if $s.is_default}>checked<{/if}> pickup_og_id='<{$s.pickup_og_id}>'> 
		<{$s.address_name}>&nbsp;&nbsp;&nbsp;&nbsp; 
		<{$s.state}>&nbsp;&nbsp;&nbsp;&nbsp; 
		<{$s.city}>&nbsp;&nbsp;&nbsp;&nbsp; 
		<{$s.district}> <{$s.shipper_city}> <{$s.shipper_street}>&nbsp;&nbsp;&nbsp;&nbsp; 
		<{if $s.postal_code}><{$s.postal_code}>&nbsp;&nbsp;&nbsp;&nbsp;<{/if}> 
		<{if $s.street}><{$s.street}>&nbsp;&nbsp;&nbsp;&nbsp; <{/if}> 
		<{$s.contact}>&nbsp;&nbsp;&nbsp;&nbsp; 
		<{$s.phone}>&nbsp;&nbsp;&nbsp;&nbsp; 
		
		</label>
		<a onclick='userAddressOp(this);' href='javascript:;' class='userAddressOpBtn' address_id='<{$s.address_id}>'><{t}>修改信息<{/t}></a> 
		&nbsp;&nbsp;<a onclick='delUserAddressOpBtn(this);' href='javascript:;' class='delUserAddressOpBtn' address_id='<{$s.address_id}>'><{t}>删除<{/t}></a>
	</p>
	<{/foreach}>
</div>
<div>
	<a onclick='userAddressOp(this);' href='javascript:;' style='font-weight: normal; font-size: 12px; line-height: 35px; margin: 0 0px;' class='userAddressOpBtn' address_id=''><{t}>点击添加<{/t}></a>
</div>
<script type="text/javascript">
function userAddressOp(obj){
	var address_id= $(obj).attr('address_id');
    var title = '';
    if(address_id){
        title = "<{t}>修改提货地址<{/t}>";
    }else{
        title = "<{t}>新增提货地址<{/t}>";
    }
    var date = new Date();
	var url = "/pickup/user-address/for-pickup?address_id="+address_id+"&t="+date.getMinutes();	
    openIframeDialogNew(url,800 , 500,title,'userAddress-edit-'+address_id,0,0);
}

function delUserAddressOpBtn(obj){
	var address_id= $(obj).attr('address_id');
	jConfirm('<{t}>你确定该操作吗?<{/t}>','<{t}>删除提货地址<{/t}>',function(r){
		if(r){
			$.ajax({
		         type: "post",
		         async: false,
		         dataType: "json",
		         url: '/pickup/user-address/delete',
		         data: {paramId:address_id},
		         success: function (json) {
		             var state = json && json.state ? json.state : 0;
		             if (state && typeof(getUserAddress) == "function") {
		            	 getUserAddress();
		             }             
		         }
		     }); 
		}
	});
	 
   
}

$(function() {



});
</script>
