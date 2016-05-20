<script type="text/javascript">
$(function(){
	$("#orderSubmitBtn").click(function(){
		var err = [];
		if($('input[name="street"]').size()>0&&$('input[name="street"]:checked').size()==0){
			err.push('请选择街道');			
		}
		if($.trim($('input[name="street1"]').val())==''){
			err.push('请填写详细地址');			
		}
		if($('input[name="contact"]').val()==''){
			err.push('请填写联系人');
			
		}
		if($('input[name="phone"]').val()==''){
			err.push('请填写电话');			
		}
		if($('input[name="address_name"]').val()==''){
			err.push('请填写自定义别名');			
		}
		
		if(err.length>0){
			var html = '';
			$.each(err,function(k,v){
				html+='<p>'+v+'</p>';
			})
			alertTip(html);
			return;
		}
		var pickup_og_id = ''
		if($('input[name="street"]:checked').size()>0){			
			pickup_og_id = $('input[name="street"]:checked').attr('pickup_og_id');
		}else{
			pickup_og_id = $('#district').attr('pickup_og_id');
		}
		$('input[name="pickup_og_id"]').val(pickup_og_id);
		
		var pickup_range_id = '';
		if($('input[name="street"]:checked').size()>0){			
			pickup_range_id = $('input[name="street"]:checked').attr('pickup_range_id');
		}else{
			pickup_range_id = $('#district').attr('pickup_range_id');
		}		
		$('input[name="pickup_range_id"]').val(pickup_range_id);
		
		loadStart();
		var params = $("#submiterForm").serialize();
		$.ajax({
		    type: "post",
		    async: false,
		    dataType: "json",
		    url: '/pickup/user-address/edit',
		    data: params,
		    success: function (json) {
		    	loadEnd();
		    	switch (json.state) {
		            case 1:
			            
		            case 2:
			            parent.getUserAddress();
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

var pickupRang = {};
function getPickupRang(){	 
    $.ajax({
		   type: "POST",
		   url: '/pickup/user-address/get-pickup-rang',
		   data: {},
		   dataType: "json",
		   success: function(json){
			    pickupRang = json;
			    var html = '';
			    var def = $('#state').attr('default');
				$.each(json,function(state_name,states){	
					var selected = '';
					if(def==state_name){
						selected = 'selected="selected"';
					}				
					html+='<option value="'+state_name+'" '+selected+'>'+state_name+'</option>';
				})
				//alert(html);
				$('#state').html(html);
				$('#state').change();
		   }
	});
}
$(function(){
	getPickupRang();
	$('#state').change(function(){
		var state = $(this).val();
		var json = pickupRang[state];
		var html = '';
	    var def = $('#city').attr('default');
		$.each(json,function(k,v){	
			var selected = '';
			if(def==k){
				selected = 'selected="selected"';
			}						
			html+='<option value="'+k+'" '+selected+'>'+k+'</option>';
		})
		//alert(html);
		$('#city').html(html);
		$('#city').change();
	});
	
	$('#city').change(function(){
		var state = $('#state').val();
		var city = $('#city').val();
		var json = pickupRang[state][city];
		var html = '';
	    var def = $('#district').attr('default');
		$.each(json,function(k,v){	
			var selected = '';
			if(def==k){
				selected = 'selected="selected"';
			}					
			html+='<option value="'+k+'" '+selected+'>'+k+'</option>';
		})
		//alert(html);
		$('#district').html(html);
		$('#district').change();
	});
	$('#district').change(function(){
		var state = $('#state').val();
		var city = $('#city').val();
		var district = $('#district').val();
		var json = pickupRang[state][city][district];
		var html = '';
	    var def = $('#street').attr('default');
		$.each(json,function(k,v){
			$('#district').attr('pickup_og_id',v.pickup_og_id);
			$('#district').attr('pickup_range_id',v.pickup_range_id);

			
			
			var checked = '';
			if(def==v.street){
				checked = 'checked="checked"';
			}
			
			if(v.street){			
				html+='<p><label><input '+checked+' type="radio" value="'+v.street+'" name="street" class=" " postal_code="'+(v.postal_code?v.postal_code:'')+'" pickup_range_id="'+v.pickup_range_id+'" pickup_og_id="'+v.pickup_og_id+'">'+v.street+'&nbsp;&nbsp;'+(v.postal_code?v.postal_code:'')+'</label></p>';
			}	
		})
		$('#street').html(html);
		if(html!=''){
			$('#street_wrap').show();
		}else{
			$('#street_wrap').hide();
			//$('#street_wrap').show();
		}
		//alert(html);
		
	});
})

function editUserAddress(address_id){	 
    $.ajax({
		   type: "POST",
		   url: '/pickup/user-address/get-by-json',
		   data: {'paramId':address_id},
		   async: true,
		   dataType: "json",
		   success: function(json){
			    if(json.state){
                    $.each(json.data,function(k,v){
                        v +='';
                        $("#submiterForm :input[name='"+k+"']").val(v).attr('default',v); 			    	    
                    })
                    $("#submiterForm :checkbox[name='is_default']").val('1');
                    if(json.data.is_default==1||json.data.is_default=='1'){
                    	$("#submiterForm :checkbox[name='is_default']").attr('checked', true);
                    }else{
                    	$("#submiterForm :checkbox[name='is_default']").attr('checked', false);
                    } 
                    $('#street').attr('default',json.data.street);
                    $('#state').change();
                    
                    
			    }else{
			       alertTip(json.message);
			    }
		   }
	});
}
<{if $smarty.get.address_id}>
setTimeout(function(){editUserAddress('<{$smarty.get.address_id}>');},500);
<{/if}>
</script>
<style>
.module-title {
	text-align: right;
}

label {
	cursor: pointer;
}
.red{padding-left:8px;}
</style>
<form id="submiterForm" onsubmit="return false;" action="" method="POST">
	<table cellspacing="0" cellpadding="0" width="100%" border="0"
		style="margin-top: 10px;" class="table-module">
		<tbody class="">
			<tr class="table-module-b1">
				<td class="module-title" width='100'><{t}>省<{/t}>：</td>
				<td><select id='state' name='state' default=''></select> <span
					class="red">*</span></td>
			</tr>
			<tr class="table-module-b1">
				<td class="module-title"><{t}>市<{/t}>：</td>
				<td><select id='city' name='city' default=''></select> <span
					class="red">*</span></td>
			</tr>
			<tr class="table-module-b1">
				<td class="module-title"><{t}>区<{/t}>：</td>
				<td><select id='district' name='district' default=''></select> <span
					class="red">*</span></td>
			</tr>
			<tr class="table-module-b1" id='street_wrap'>
				<td class="module-title"><{t}>街道<{/t}>：</td>
				<td id='street' default=''></td>
			</tr>
			
			<tr class="table-module-b1">
				<td class="module-title"><{t}>详细地址<{/t}>：</td>
				<td><input name='street1' class='input_text' style='width:300px;'><span
					class="red">*</span></td>
			</tr>

			<tr class="table-module-b1">
				<td class="module-title"><{t}>联系人<{/t}>：</td>
				<td><input name='contact' class='input_text'><span
					class="red">*</span></td>
			</tr>
			<tr class="table-module-b1">
				<td class="module-title"><{t}>联系电话<{/t}>：</td>
				<td><input name='phone' class='input_text'><span
					class="red">*</span></td>
			</tr>
			<tr class="table-module-b1">
				<td class="module-title">&nbsp;</td>
				<td><label><input type="checkbox" value="1" name="is_default"
						class=" "><{t}>设为默认<{/t}></label></td>
			</tr>

			<tr class="table-module-b1">
				<td class="module-title">自定义别名</td>
				<td><input name='address_name' class='input_text' placeholder='地址别名：如 公司、仓库、深圳仓库等'><span
					class="red">*</span></td>
			</tr>

			<tr class="table-module-b1">
				<td class="module-title">&nbsp;</td>
				<td><input type="hidden" name="address_id" value=""
					class="input_text">
					<input type="hidden" name="pickup_og_id" value=""
					class="input_text"> <input type="hidden" name="pickup_range_id" value=""
					class="input_text"> <input type="button" style="width: 80px;"
					class="baseBtn submitBtn" id="orderSubmitBtn" value="<{t}>提交<{/t}>">
				</td>
			</tr>
		</tbody>
	</table>
</form>
