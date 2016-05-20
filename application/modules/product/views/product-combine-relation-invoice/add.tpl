<style>
.hide_title_tr td {
    height: 1px;
    line-height: 1px;
    overflow: hidden;
    padding: 0 5px;
}
</style>
<script type="text/javascript">
	$(function(){
		$(".add_sub_sku_row").live('click',function(){
			var s = $("#clone_row");
	    	//填充标准数据
	        var html_bz = s.clone();
	        html_bz.attr("id","");
	        if($(".row_data").find("tr").size() > 0){
				$(".row_data").find("tr").last().after(html_bz);
	    	}else{
	    		$(".row_data").append(html_bz);
		    }
	        initRowColor();
		});

		$(".del_row").live('click',function(){
			$(this).closest('tr').remove();
			initRowColor();
		});
	});

	function initRowColor(){
		$('.row_data').each(function(){
			$("tr",this).removeClass('table-module-b1').removeClass('table-module-b2');
			$("tr:even",this).addClass('table-module-b1');
			$("tr:odd",this).addClass('table-module-b2');
		}); 
	}
</script>
<div id='add_product_combine' title='<{t}>create<{/t}>' style="display: none;"><!-- 添加 -->
	<form onsubmit="return false;" id="addRelationForm">
		<table cellspacing="0" cellpadding="0" border="0" class="dialog-module">
			<tbody>
				<tr>
					<td class="dialog-module-title" width='120'><{t}>Platform_Sales_SKU<{/t}>:</td>
					<td>
						<input type="text" class="input_text" id="product_sku" name="product_sku" style="text-transform: uppercase;">
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>platform_account<{/t}></td>
					<td>
						<select id="user_account" name="user_account" style="width:148px;">
							<option value=''><{t}>all<{/t}></option><!-- 全部 -->
		    				<{foreach from=$user_account_arr name=ob item=ob}>
		    				<option value='<{$ob.user_account}>'><{$ob.platform_user_name}></option>
		    				<{/foreach}>
						</select>
						<span class="msg">*</span>
					</td>
				</tr>
			</tbody>
		</table>
		
		<div style='border-top:1px solid #ccc;margin-top:5px;padding-top: 5px;width: 100%;'>
			<div style="width: 100%;margin-bottom: 2px;padding-right: 16px;" class="opration_area_rit">
			    <b style='color:red;'><{t}>sku_relation_ship_exist_will_be_override<{/t}></b>
				<input type="button" class="baseBtn add_sub_sku_row" value="<{t}>create<{/t}>">
			</div>
			
			<div style="clear: both;"></div>
			
			<div style="width: 100%;">
				<table cellspacing="0" cellpadding="0" border="0" class="dialog-module table-module">
					<tbody>
						<tr class="table-module-title">
							<td width='150'>
								<{t}>corresponding_sku<{/t}>
							</td>
							<td width="150">
								<{t}>quantity<{/t}>
							</td>
							<td width="150">
								<{t}>purchase_price<{/t}><!-- 采购单价 -->
							</td>
							<td width="65">
								<{t}>operate<{/t}>
							</td>
						</tr>
					</tbody>
				</table>
							
				<table cellspacing="0" cellpadding="0" border="0" class="table-module">
					<tbody>
						<tr class="hide_title_tr table-module-title">
							<td width='150'>&nbsp;</td>
							<td width="150">&nbsp;</td>
							<td width="150">&nbsp;</td>
							<td width="65">&nbsp;</td>
						</tr>
					</tbody>
					<tbody class="row_data">
					</tbody>
				</table>
			</div>
		</div>
	</form>
	
	<div id="validateTips" class="validateTips">
	</div>
	
	<!-- 隐藏的行数据 -->
	<table style="display: none;">
		<tr class="table-module-b1" id="clone_row">
			<td>
				<input type="text" class="input_text" name="pcr_product_sku[]" value='<{$o.pcr_product_sku}>'>
			</td>
			<td>
				<input type="text" class="input_text" name="pcr_quantity[]" value='<{$o.pcr_quantity}>'>
			</td>
			<td>
				<input type="text" class="input_text" name="pcr_pu_price[]" value='<{$o.pcr_pu_price}>'>
			</td>
			<td class="ec-center">
				<a href="javascript:;" class='del_row' style="color:#0090e1;"><{t}>delete<{/t}></a>
			</td>
		</tr>
	</table>
</div>