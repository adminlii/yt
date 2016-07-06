
<{if isset($result)}> 
<{if $result.ask==0}>
<div class="Tab">
	<ul>
		<li class="mainStatus" style="position: relative;" id='normal'>
			<a class="statusTag " href="javascript:;">
				<span class="order_title"><{t}>正常订单<{/t}></span>
			</a>
		</li>
		<li class="mainStatus" style="position: relative;" id='abnormal'>
			<a class="statusTag " href="javascript:;">
				<span class="order_title"><{t}>异常订单<{/t}></span>
			</a>
		</li>
		
		<li class="" style="position: relative;border:0 none;">
			<a class="addPrefixBtn " href="javascript:;" style='color:#2283c5;'>
				<{t}>批量添加单号前缀<{/t}>
			</a>
		</li>
		
		<li class="" style="position: relative;border:0 none;">
			<a class="setProductCodeBtn " href="javascript:;" style='color:#2283c5;'>
				<{t}>批量设置运输方式<{/t}>
			</a>
		</li>
	</ul>
</div>
<div style='width: 100%; overflow: auto;position:relative;' id='data_wrapper'>
    <div class='data_wrap_title' style='position:absolute;top:0;left:0;'>
		<table cellspacing="0" cellpadding="0" width="100%" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
				    <td width='60'><{t}>Excel行<{/t}></td>
					<{foreach from=$result.excel_column name=o item=o key=k}>
					<td><{$o}></td>
					<{/foreach}>
					<td width="100"><{t}>operation<{/t}></td>
				</tr>
			</tbody>							
		</table>
	</div>
	<div id='normal_wrap' class='data_wrap'>
		<table cellspacing="0" cellpadding="0" width="100%" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
				    <td width='60'><{t}>Excel行<{/t}></td>
					<{foreach from=$result.excel_column name=o item=o key=k}>
					<td><{$o}></td>
					<{/foreach}>
					<td width="100"><{t}>operation<{/t}></td>
				</tr>
			</tbody>
			<tbody class="table-module-list-data">
				<{foreach from=$result.success_arr name=o item=o key=k}>
				<tr class="table-module-b1">
				    <td class='ec-center'><{$k}></td>
					<{foreach from=$o name=oo item=oo key=kk}>
					<td>					   
						<input class='input_text' name='fileData[<{$k}>][<{$kk}>]' value='<{$oo}>' />	
					</td>
					<{/foreach}>
					<td>
						<a href='javascript:;' class='delBtn'><{t}>删除<{/t}></a>
					</td>
				</tr>
				<{/foreach}>
			</tbody>
		</table>
	</div>
	<div id='abnormal_wrap' class='data_wrap'>
		<table cellspacing="0" cellpadding="0" width="100%" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
				    <td width='60'><{t}>Excel行<{/t}></td>
					<{foreach from=$result.excel_column name=o item=o key=k}>
					<td><{$o}></td>
					<{/foreach}>
					<td width="100"><{t}>operation<{/t}></td>
				</tr>
			</tbody>
			<tbody class="table-module-list-data">
			    <!-- 运输方式种类 -->
			    <{$productCount = $productKind|count}>
			    
				<{foreach from=$result.fail_arr name=o item=o key=k}>
				<tr class="table-module-b1" title='<{if isset($result.errTips[$k])}><{$result.errTips[$k]}><{/if}>'>
				    <td class='ec-center'><{$k}></td>
					<{foreach from=$o name=oo item=oo key=kk}>
					<td>
					    <{$clazz=''}>
					    <{if $kk=='运输方式'}>
					    <{$clazz='product_code'}>
					    <{elseif $kk=='客户单号'}>
					    <{$clazz='shipper_hawbcode'}>
					    <{/if}>
					    <{if $kk=='运输方式'}>
					    <select name="fileData[<{$k}>][<{$kk}>]" class="input_select <{$clazz}> product_code" style='width:200px;'>
					        <!-- 多个运输方式的时候，提供请选择选项 -->
					        <{if $productCount>1}>
                        	<option value='' class='ALL'><{t}>-select-<{/t}></option>
                        	<{/if}>
							<{foreach from=$productKind item=c name=c}>
							<option value='<{$c.product_code}>' <{if $oo==$c.product_code||$oo==$c.product_cnname||$oo==$c.product_enname}>selected<{/if}>><{$c.product_code}> [<{$c.product_cnname}>  <{$c.product_enname}>]</option>
							<{/foreach}>
                        </select>
						<!-- <input class='input_text input_text01' name='fileData[<{$k}>][<{$kk}>]' value='<{$oo}>' />	 -->				    
					    <{else}>
						<input class='input_text <{$clazz}>' name='fileData[<{$k}>][<{$kk}>]' value='<{$oo}>' />
						<{/if}>
					</td>
					<{/foreach}>
					<td>
						<a href='javascript:;' class='delBtn'><{t}>删除<{/t}></a>
					</td>
				</tr>
				<{/foreach}>
			</tbody>
		</table>
	</div>
</div>	
<{else}> 
<{/if}>

<{if $result.template_type==''}>
<!-- 没有找到匹配的模板  -->
<script type="text/javascript">
$(function(){
    <{if $result.error_code==1001}>
    alertTip('<{$result.message}>');
	<{else}>
	openIframeDialog('/order/order-template/upload',1000,600,'<{t}>新增模板<{/t}>');
	
	<{/if}>
});
</script>			
<{/if}>
<{/if}>		

<{if $notExistCountryArr}>
    <div id='notExistCountryMapWrap' style='width:800px;' title='<{t}>配对失败国家<{/t}>'>
       <table cellspacing="0" cellpadding="0" width="100%" border="0" class="table-module">
           <caption style='line-height:35px;font-weight:bold;font-size:14px;'><{t}>配对失败国家<{/t}></caption>
			<tbody>
				<tr class="table-module-title">
				    <td width='60'><{t}>序号<{/t}></td>				    
					<td width='120'><{t}>上传国家<{/t}></td>
					<td><{t}>标准国家<{/t}></td>
				</tr>
			</tbody>
			<tbody class="table-module-list-data">
				<{foreach from=$notExistCountryArr name=o item=o key=k}>
				<tr class="table-module-b1">
				    <td class='ec-center'><{$k+1}></td>			    
					<td><{$o}></td>
					<td>
					    <select class='notExistCountryMap' name='country_map[<{$o}>]' style=''>	
						    <option value=''><{t}>请选择<{/t}></option>
    					<{foreach from=$countrys name=oo item=oo key=kk}>					
    						<option value='<{$oo.country_code}>'><{$oo.country_code}>[<{$oo.country_cnname}> <{$oo.country_enname}>]</option>					
    					<{/foreach}>					    
					    </select>
					</td>
				</tr>
				<{/foreach}>
			</tbody>
		</table> 
    </div>
        
    <!-- 国家不存在  -->
    <script type="text/javascript">
    $(function(){
    	$('#notExistCountryMapWrap0').dialog({
            autoOpen: true,
            width: 500,
            maxHeight: 500,
            modal: true,
            show: "slide",
            buttons: [
                {
                    text: 'Close',
                    click: function () {
                        $(this).dialog("close");
                    }
                }
            ],
            close: function () {
                $(this).remove();
            }
        });
    });

    $(function(){
    	$('.notExistCountryMap').chosen('destroy');
    	$('.notExistCountryMap').chosen({width:'300px',search_contains:true});


    	//$('.table-module-list-data .product_code').chosen('destroy');
    	//$('.table-module-list-data .product_code').chosen({width:'300px',search_contains:true});
    	
    }) 
        
    </script>	
<{/if}>	
<{if $result}>
<div style='padding: 15px; line-height: 22px;max-height:250px;overflow:auto;border:1px solid #ccc;margin-top:10px;position:relative;' id='result_message'>
	<p style='font-weight: bold; font-size: 25px; line-height: 30px; color: red;'><{$result.message}>
	<{if $result.ask==0}>
	<input type='button' class='submitBtn' value='<{t}>验证并上传<{/t}>' style='margin-left:15px;margin-top:-5px;'>
	<{/if}>
	<{if $result.ask==1}>
		<{if $result.ansych==0}>
			<a href='javascript:;' onclick="leftMenu('order-list','<{t}>订单管理<{/t}>','/order/order-list/list?quick=39')" style='padding-left:10px;color:#0090e1;font-size:12px;'><{t}>订单管理<{/t}></a>
		<{else}>
			<a href='javascript:;' onclick="leftMenu('order_import_batch','<{t}>上传记录<{/t}>','/order/order/get-import-batch?quick=0')" style='padding-left:10px;color:#0090e1;font-size:12px;'><{t}>上传记录<{/t}></a>
		<{/if}>
	<{/if}>
	</p>
		
	<{foreach from=$result.errs name=o item=o key=k}>
	<p><{$o}></p>
	<{/foreach}>
</div>
<{/if}>
<div style='display:none;'><{$result_str}></div>