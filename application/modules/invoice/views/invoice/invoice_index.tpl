<script type="text/javascript">

paginationPageSize = 20;
EZ.url = '/invoice/invoice/';
EZ.getListData = function(json) {
	//设置订单条数
	var html = '';
	$(".checkAll").attr('checked', false);
	var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;

	//买家ID
	$.each(json.data, function(key, val) {	
		var orderId = val.order_id;
		var clazz = key%2==1?'table-module-b2':'table-module-b1';
		html += "<tr class='"+clazz+"' id='order_wrap_"+val.order_id+"'>";
		//html += '<td class="ec-center"><input type="checkbox" class="checkItem" name="orderId[]" ref_id="'+val.shipper_hawbcode+'" value="' + val.order_id + '"/></td>';

		html += '<td  valign="top">';
		html +=val.invoice_code;
		html += '</td>';
		html += '<td  valign="top">';
		html +=val.invoice_enname;
		html += '</td>';
		html += '<td  valign="top">';
		html +=val.unit_code;
		html += '</td>';
		html += '<td  valign="top">';
		html +=val.invoice_unitcharge;
		html += '<td  valign="top">';
		html +=val.invoice_weight;
		html += '</td>';
		html += '<td  valign="top">';
		html +=val.hs_code;
		html += '</td>';
		html += '<td  valign="top">';
		html +=val.invoice_note;
		html += '</td>';
		html += '<td  valign="top">';;
		html +=val.invoice_url;		
		html += '</td>';
		html += '<td  valign="top">';
		html += '<a class="delBtn" href="javascript:;" id="'+val.id+'">删除</a>';
		html += '&nbsp;&nbsp;<a class="editBtn" href="javascript:;" id="'+val.id+'">编辑</a>';
		html += '</td>';
		/**操作 结束**/
		html += "</tr>";

	});

	return html;
}
var invoice_tpl = null;
$(function() {

	function formSubmit(){
		$("#invoice_form :input").each(function(){
			var val = $(this).val();
			val = $.trim(val)
			$(this).val(val);
		});
		var param = $("#invoice_form").serialize();
		
		$.ajax({
			type: "POST",
			url: "/invoice/invoice/create",
			data: param,
			dataType:'json',
			success: function(json){
				var html = '';
				html+=json.message;
			    $.each(json.err,function(k,v){
			        html+='<p>行'+k+':';
			        $.each(v,function(kk,vv){
			            html+=vv+';';
			        })
			        html+="</p>";
			    })
				alertTip(html,800,400);
				if(json.ask){
					initData(paginationCurrentPage - 1);				
				}
			}
		});
	}
	$('#invoice_div').dialog({
        autoOpen: false,
        //closeOnEscape:false,
        width: 920,
        maxHeight: 400,
        modal: true,
        show: "slide",
        buttons: [
            {
                text: '确定(Ok)',
                click: function () {
                    //formSubmit();
                    var this_ = $(this);
            		$("#invoice_form :input").each(function(){
            			var val = $(this).val();
            			val = $.trim(val)
            			$(this).val(val);
            		});
            		var param = $("#invoice_form").serialize();
            		
            		$.ajax({
            			type: "POST",
            			url: "/invoice/invoice/create",
            			data: param,
            			dataType:'json',
            			success: function(json){
            				var html = '';
            				html+=json.message;
            			    $.each(json.err,function(k,v){
            			        html+='<p>行'+k+':';
            			        $.each(v,function(kk,vv){
            			            html+=vv+';';
            			        })
            			        html+="</p>";
            			    })
            				alertTip(html,600 ,400);
            				if(json.ask){
            					initData(paginationCurrentPage - 1);
            					this_.dialog('close');				
            				}
            			}
            		});
                }
            },
            {
                text: '取消(Cancel)',
                click: function () {
                    $(this).dialog("close");
                }
            }
            
        ],
        close: function () {
            $('#products').text('');
            $('.addInvoiceBtn').click();
        },
        open:function(){
        	if(invoice_tpl==null){
        		$('#products select').each(function(){
            		var d = $(this).attr('default');
        			$('.'+d,this).attr('selected',true);
        		});        		
    			var clone = $('#products .table-module-b1').eq(0).clone();
        		$(':input',clone).val('');
        		var default_ = $('.unit_code',clone).attr('default');
        		//$('.unit_code .'+default_,clone).attr('selected',true);
        		$('.total',clone).html('0');
        		invoice_tpl = clone;
        	}
        }
    });
	$('.addBtn').live('click',function(){
		$('#invoice_div').dialog('open');
	});
	$('.addInvoiceBtn').live('click',function(){
		var clone = invoice_tpl.clone();
		//alert(clone);
		$('#products').append(clone);
	});
	

	$('.delInvoiceBtn').live('click',function(){
		if($('.delInvoiceBtn').size()<=1){			
			alertTip('<{t}>最后一个申报信息不可删除<{/t}>');
			return;
		}
		var this_ = $(this);
		jConfirm('<{t}>are_you_sure<{/t}>', '<{t}>删除申报信息<{/t}>', function(r) {
    		if(r){
    			this_.parent().parent().remove();
    		}
    	});       
	});
})

$(function(){
    $('.delBtn').live('click',function(){
        var id = $(this).attr('id');
        if(!window.confirm('确定删除? ')){
            return;
        }
        $.ajax({
            type: "POST",
            async: false,
            dataType: "json",
            url: "/invoice/invoice/delete",
            data: {'id':id},
            success: function (json) {
                var html = json.message;  
                if(json.ask){
					initData(paginationCurrentPage - 1);	
                }             
                alertTip(html);
            }
        });
    });
    $('#edit_invoice_div').dialog({
        autoOpen: false,
        //closeOnEscape:false,
        width: 920,
        maxHeight: 400,
        modal: true,
        show: "slide",
        buttons: [
            {
                text: '确定(Ok)',
                click: function () {
                    //formSubmit();
                    var this_ = $(this);
            		$("#edit_invoice_form :input").each(function(){
            			var val = $(this).val();
            			val = $.trim(val)
            			$(this).val(val);
            		});
            		var param = $("#edit_invoice_form").serialize();
            		
            		$.ajax({
            			type: "POST",
            			url: "/invoice/invoice/update",
            			data: param,
            			dataType:'json',
            			success: function(json){
            				var html = '';
            				html+=json.message;
            				alertTip(html);
            				if(json.ask){
            					initData(paginationCurrentPage - 1);
            					this_.dialog('close');				
            				}
            			}
            		});
                }
            },
            {
                text: '取消(Cancel)',
                click: function () {
                    $(this).dialog("close");
                }
            }
            
        ],
        close: function () {
        },
        open:function(){
        }
    });

    $('.editBtn').live('click',function(){    
        var id = $(this).attr('id');   
        $.ajax({
            type: "POST",
            async: false,
            dataType: "json",
            url: "/invoice/invoice/get-by-json",
            data: {'id':id},
            success: function (json) {
               if(json.ask){
                   var data = json.data;
                   $.each(data,function(k,v){
                       //alert('#edit_invoice_div .'+k+' '+v);
                	   $('#edit_invoice_div .'+k).val(v);
                   });
                   
            	   $('#edit_invoice_div').dialog('open');
               }else{
           	    alertTip(json.message);
               }
            }
        });
    });
        
})
</script>
<style>
#products .input_text {
	width: 75%;
}
#edit_products .input_text {
	width: 75%;
}
</style>
<div id="module-container">
	<div id="search-module">
		<form id="searchForm" name="searchForm">
			<!-- 
			<div class="pack_manager_content" style="padding: 0">
				<table width="100%" cellspacing="0" cellpadding="0" border="0" id="searchfilterArea">
					<tbody>
						<tr>
							<td>
								<div class="searchFilterText"><{t}>sale_status<{/t}>：</div>
								<div class="pack_manager">
									<input type="hidden" class="input_text keyToSearch" id="sale_status" name="sale_status" value="">
									<a onclick="searchFilterSubmit('sale_status','',this)" href="javascript:void(0)" class="current"><{t}>all<{/t}></a>
									<{foreach from=$saleStatus key=k name=o item=o}>
									<a onclick="searchFilterSubmit('sale_status','<{$k}>',this)" href="javascript:void(0)"><{$o}></a>
									<{/foreach}>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			 -->
			<!-- 产品代码 -->
			<div class="search-module-condition" name="purchase_AdvancedSearch">
				<span class="searchFilterText"> 品名代码/品名 : </span>
				<input type="text" name="keyword" id="keyword" class="input_text keyToSearch" placeholder='<{t}>like_search_before&after<{/t}>' />
			</div>
			<div class="search-module-condition" name="purchase_AdvancedSearch">
				<span class="searchFilterText">&nbsp;</span>
				<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" />
				<input type="button" value="<{t}>批量添加<{/t}>" class="baseBtn addBtn" style='float: right;' />
			</div>
		</form>
	</div>
	<div id="module-table">
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td width="80">品名代码</td>
					<td width="150">品名</td>
					<td width="80">单位</td>
					<td width="80">单价($)</td>
					<td width="80">重量(kg)</td>
					<td width="80">海关协制编号</td>
					<td width="80">配货信息</td>
					<td width="80">销售地址</td>
					<td width="100">操作</td>
				</tr>
			</tbody>
			<tbody id="table-module-list-data">
				<!-- 
				<tr class="table-module-b1">
					<td>
						<input type="text" value="" name="invoice[invoice_code][]" class="input_text invoice_code web_require">
						<span class="msg">*</span>
					</td>
					<td>
						<input type="text" value="" name="invoice[invoice_enname][]" class="input_text invoice_enname web_require">
						<span class="msg">*</span>
					</td>
					
					<td>
						<select default="PCE" name="invoice[unit_code][]" class="input_text input_select unit_code ">
							<{foreach from=$units name=s item=s key=k}>
							<option value="<{$s.unit_code}>"><{$s.unit_enname}>(<{$s.unit_cnname}>)</option>
							<{/foreach}>
						</select>
						<span class="msg"></span>
					</td>
					<td>
						<input type="text" value="" name="invoice[invoice_unitcharge][]" class="input_text invoice_unitcharge web_require">
						<span class="msg">*</span>
					</td>
					
					<td>
						<input type="text" value="" name="invoice[hs_code][]" class="input_text hs_code">
						<span class="msg"></span>
					</td>
					<td>
						<input type="text" value="" name="invoice[invoice_note][]" class="input_text invoice_note">
						<span class="msg"></span>
					</td>
					<td>
						<input type="text" value="" name="invoice[invoice_url][]" class="input_text invoice_url">
						<span class="msg"></span>
					</td>
					<td>
						<a class="delInvoiceBtn" href="javascript:;">删除</a>
					</td>
				</tr>
				 -->
			</tbody>
		</table>
	</div>
	<div class="pagination"></div>
</div>
<div id='invoice_div' class='dialog_div' style='display: none;' title='批量添加'>
	<form action="" id='invoice_form' onsubmit='return false;'>
		<table cellspacing="0" cellpadding="0" width="100%" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td width="80">品名代码</td>
					<td><{t}>品名<{/t}></td>
					<td width="80"><{t}>单位<{/t}></td>
					<td width="80"><{t}>单价<{/t}>($)</td>
					<td width="80"><{t}>重量<{/t}>(kg)</td>
					<td width="80"><{t}>海关协制编号<{/t}></td>
					<td width="80"><{t}>配货信息<{/t}></td>
					<td width="80"><{t}>销售地址<{/t}></td>
					<td width="60"><{t}>操作<{/t}></td>
				</tr>
			</tbody>
			<tbody id="products" class="table-module-list-data">
				<tr class="table-module-b1">
					<td>
						<input type="text" value="" name="invoice[invoice_code][]" class="input_text invoice_code web_require">
						<span class="msg">*</span>
					</td>
					<td>
						<input type='text' class='input_text invoice_enname' name='invoice[invoice_enname][]'>
						<span class="msg">*</span>
					</td>
					<td>
						<select default="PCE" name="invoice[unit_code][]" class="input_text input_select unit_code">
							<{foreach from=$units name=s item=s key=k}>
							<option value="<{$s.unit_code}>" class="<{$s.unit_code}>"><{$s.unit_enname}>(<{$s.unit_cnname}>)</option>
							<{/foreach}>
						</select>
						<span class="msg"></span>
					</td>
					<td>
						<input type='text' class='input_text invoice_unitcharge' name='invoice[invoice_unitcharge][]'>
						<span class="msg">*</span>
					</td>
					<td>
						<input type='text' class='input_text invoice_weight' name='invoice[invoice_weight][]'>
						<span class="msg">*</span>
					</td>
					<td>
						<input type='text' class='input_text hs_code' name='invoice[hs_code][]'>
						<span class="msg"></span>
					</td>
					<td>
						<input type='text' class='input_text invoice_note' name='invoice[invoice_note][]' value=''>
						<span class="msg"></span>
					</td>
					<td>
						<input type='text' class='input_text invoice_url' name='invoice[invoice_url][]' value=''>
						<span class="msg"></span>
					</td>
					<td>
						<a href='javascript:;' class='delInvoiceBtn'><{t}>delete<{/t}></a>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
	<div>
		<a href='javascript:;' style='font-weight: normal; font-size: 12px; line-height: 35px;' class='addInvoiceBtn'><{t}>点击添加<{/t}></a>
	</div>
</div>

<div id='edit_invoice_div' class='dialog_div' style='display: none;' title='编辑'>
	<form action="" id='edit_invoice_form' onsubmit='return false;'>
		<table cellspacing="0" cellpadding="0" width="100%" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td width="80">品名代码</td>
					<td><{t}>品名<{/t}></td>
					<td width="80"><{t}>单位<{/t}></td>
					<td width="80"><{t}>单价<{/t}>($)</td>
					<td width="80"><{t}>重量<{/t}>(kg)</td>
					<td width="80"><{t}>海关协制编号<{/t}></td>
					<td width="80"><{t}>配货信息<{/t}></td>
					<td width="80"><{t}>销售地址<{/t}></td>
				</tr>
			</tbody>
			<tbody id="edit_products" class="table-module-list-data">
				<tr class="table-module-b1">
					<td>
						<input type="hidden" value="" name="invoice[id]" class="input_text id web_require" >
						<input type="text" value="" name="invoice[invoice_code]" class="input_text invoice_code web_require" readonly style='background:#eee;'>
						<span class="msg">*</span>
					</td>
					<td>
						<input type='text' class='input_text invoice_enname' name='invoice[invoice_enname]'>
						<span class="msg">*</span>
					</td>
					<td>
						<select default="PCE" name="invoice[unit_code]" class="input_text input_select unit_code">
							<{foreach from=$units name=s item=s key=k}>
							<option value="<{$s.unit_code}>" class="<{$s.unit_code}>"><{$s.unit_enname}>(<{$s.unit_cnname}>)</option>
							<{/foreach}>
						</select>
						<span class="msg"></span>
					</td>
					<td>
						<input type='text' class='input_text invoice_unitcharge' name='invoice[invoice_unitcharge]'>
						<span class="msg">*</span>
					</td>
					<td>
						<input type='text' class='input_text invoice_weight' name='invoice[invoice_weight]'/>
						<span class="msg">*</span>
					</td>
					<td>
						<input type='text' class='input_text hs_code' name='invoice[hs_code]'>
						<span class="msg"></span>
					</td>
					<td>
						<input type='text' class='input_text invoice_note' name='invoice[invoice_note]' value=''>
						<span class="msg"></span>
					</td>
					<td>
						<input type='text' class='input_text invoice_url' name='invoice[invoice_url]' value=''>
						<span class="msg"></span>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>