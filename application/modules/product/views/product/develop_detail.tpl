<style>
<!--
.table-module {
	background: #fff;
}

.img_ul {
	float: left;
	width: 100%;
}

.img_ul li {
	float: left;
	margin: 8px 8px 0px 0;
}

.img_ul img {
	border: 1px solid #ccc;
}

.tabContent {
	display: none;
	padding: 5px;
}

.depotTab2 {
	
}

.clear {
	clear: both;
	height: 1px;
}

.depotState h2 {
	padding-left: 5px;
	font-size: 12px;
}
/**/
.depotTab2 ul li {
	background: none repeat scroll 0 0 #EAF2F8;
	border: 1px solid #D3E5EF;
	float: left;
	height: 30px;
	padding: 0 15px;
}

.depotTab2 ul li.chooseTag {
	background: none repeat scroll 0 0 #FFFFFF;
	float: left;
	font-size: 14px;
	padding: 0 15px;
}

.btnRight {
	float: right;
	margin: 3px 5px 0 0;
}

.table-module-list-data {
	margin-bottom: 10px;
}
-->
</style>
<script type="text/javascript">
<!--
var pd_id = '<{$productDev.pd_id}>';
$(function(){
	$(".tab").click(function(){
		var this_div = $(this).closest('.depotState');
		this_div.find(".tabContent").hide();
		this_div.find(".tab").removeClass("chooseTag");
        $(this).addClass("chooseTag");
        $("#tabContent_"+$(this).attr("id").replace("tab_","")).show();
    });
    $(".depotTab2").each(function(){
    	$(this).find(".tab").eq(0).click();
    });
	

})
var edit = false;
$(function(){

    $(".editSubmitBtn").click(function(){
        var form = $(this).parent().parent();
		var param = form.serialize();
		var product_id = $("#product_id").val();
		param+='&product_id='+product_id;
		var type = form.attr('action');
		param+='&type='+type;
		
		alertTip('数据处理中……');
		$.ajax({
		   type: "POST",
		   url: '/product/develop/edit',
		   data: param,
		   dataType:'json',
		   success: function(json){
			   $("#dialog-auto-alert-tip").html(json.message);
			   if(json.ask){
				   $('input.input_change',form).each(function(){
					   var val = $(this).val();
					    $(this).prev('span').text(val);
				   });

				   $('textarea.input_change',form).each(function(){
					   var val = $(this).val();
					    $(this).prev('span').text(val);
				   });
				   $('select.input_change',form).each(function(){
					   var val = $('option:selected',this).text();
					    $(this).prev('span').text(val);
				   });
				   $('.editBtn',form).click();
					
				    setTimeout(function(){
						//window.location.href= window.location.herf;
					},500);
			   }
		   }
		});
    });
    
    $('.editBtn').toggle(function(){
        var form = $(this).parent().parent();
		$('.span_text',form).hide();
		$('.input_change',form).show();
		$(this).val('取消编辑');
		$('.editSubmitBtn',form).show();
		$('.addBtn',form).attr('disabled',false);
		$('.delBtn',form).show();
		edit = true;
    },function(){
        var form = $(this).parent().parent();
		$('.span_text',form).show();
		$('.input_change',form).hide();
		$(this).val('编辑');
		$('.editSubmitBtn',form).hide();
		$('.addBtn',form).attr('disabled',true);
		$('.delBtn',form).hide();
		edit = false;
    });
    /**/

    $('.editDevBtn').live('click',function(){
        window.location.href='/product/develop-process/create/fd/1/pd_id/'+pd_id+'#'+$(this).attr('to');
    })
    $('.delBtn').live('click',function(){
        if(!window.confirm('删除该配件?')){
            return false;
        }
        $(this).parent().parent().remove();
    })

    $('#addProductLine').click(function(){
    	$('#fitting_div').dialog('open');
    });
    $('#fitting_div').dialog({
        autoOpen: false,
        width: 600,
        maxHeight: 500,
        modal: true,
        show: "slide",
        buttons: [
            {
                text: '确定',
                click: function () {
                	var fitting_img = $("#fitting_img").val();
                    if($.trim(fitting_img)==''){
                        alertTip('请输入图片URL');
                        return;
                    }
                    var fitting_code = $("#fitting_code").val();
                    if($.trim(fitting_code)==''){
                        alertTip('配件代码');
                        return;
                    }
                    var fitting_name = $("#fitting_name").val();
                    if($.trim(fitting_name)==''){
                        alertTip('配件名称');
                        return;
                    }
                    var fitting_desc = $("#fitting_desc").val();
                    if($.trim(fitting_desc)==''){
                        alertTip('配件描述');
                        return;
                    }
                    var html = '';
                    html+='<tr class="">';
                	html+='<td>';
            		html+='<img width="75" height="75" src="'+fitting_img+'">';
            		html+='<input type="hidden" value="'+fitting_img+'" name="fitting_img[]">';
                	html+='</td>';
                	html+='<td>';
            		html+=fitting_code;
            		html+='<input type="hidden" value="'+fitting_code+'" name="fitting_code[]">';
                	html+='</td>';
                	html+='<td>';
            		html+=fitting_name;
            		html+='<input type="hidden" value="'+fitting_name+'" name="fitting_name[]">';
                	html+='</td>';
                	html+='<td>';
            		html+=fitting_desc;
            		html+='<input type="hidden" value="'+fitting_desc+'" name="fitting_desc[]">';
                	html+='</td>';
                	html+='<td>';
            		html+='<a style="" class="delBtn" href="javascript:;">删除</a>';
                	html+='</td>';
                    html+='</tr>';
                    $("#fitting_wrap").append(html);
                    $(this).dialog("close");
                }
            },
            {
                text: '关闭',
                click: function () {
                    $(this).dialog("close");
                }
            }
        ],
        close: function () {
          
        }
    });

    $('#addDescLine').click(function(){
        if(!window.confirm('新增产品描述')){
            return false;
        }
        var clone = $('#template_desc tbody').clone();
        $('#tabContent_desc .table-module').append(clone);
    }); 

    $('#tabContent_desc .table-module .table-module-list-data').live('dblclick',function(){
        if(!window.confirm('删除产品该描述')){
            return false;
        }
        $(this).remove();
    })
    $('#addPicture').click(function(){
    	$('#image_div').dialog('open');
    });
    $('#image_div').dialog({
        autoOpen: false,
        width: 600,
        maxHeight: 500,
        modal: true,
        show: "slide",
        buttons: [
            {
                text: '确定',
                click: function () {
                    var val = $("#attach").val();
                    if($.trim(val)==''){
                        alertTip('请输入图片URL');
                        return;
                    }
                    $('.img_ul').append('<li><img width="75" height="75" src="'+val+'"><input type="hidden" name="attach[]" value="'+val+'"></li>');
                    $(this).dialog("close");
                }
            },
            {
                text: '关闭',
                click: function () {
                    $(this).dialog("close");
                }
            }
        ],
        close: function () {
          
        }
    });

    $('.img_ul img').live('dblclick',function(){
        if(edit){
            if(!window.confirm('删除图片?')){
                return;
            }
            $(this).parent().remove();
        }
    });
    

    $('.currency_code').change(function(){
        $('.currency_code').val($(this).val());
    })

    $('.img_ul li').live('mouseover',function(){
        if(edit){
        	$(this).attr('title','双击图片删除');
        }else{
        	$(this).attr('title','');
        }        
    })
    <{if $porder}>
    $('#tabContent_sale .editBtn').removeClass('baseBtn').attr('disabled',true);
    <{/if}>
})
$(function(){  
	$('#default_supplier_code').autocomplete({
		source : "/product/develop/get-supplier-by-keyword/pd_id/<{$product.pd_id}>/limit/20",
		minLength : 2,
		delay : 300,
		select : function(event, ui) {
			$(this).siblings('.default_supplier_code').val(ui.item.supplier_code);
			$(':input[name="product_purchase_value"]').val(ui.item.sp_last_price);
			$(':input[name="currency_code"]').val(ui.item.currency_code);
		}
	});
});
//-->
</script>
<style>
<!--
.input_change {
	display: none;
}

.dialog_div {
	display: none;
}

textarea {
	font-size: 13px;
}
-->
</style>
<div id="module-container" class='productInfo'>
	<input type='hidden' name='product_id' value='<{$product.product_id}>' id='product_id' />
	<div id="module-table">
		<div class='depotState' style="margin-top: 5px;">
			<form action="base" onsubmit='return false;' method='post'>
				<h2>
					<{t}>product_information<{/t}><!-- 产品基本信息 -->
					<{if $edit}>
						<!-- 
						<input type="button" value="编辑" class="baseBtn btnRight editDevBtn" to='product_develop_base_tag'>
						<input type="submit" value="提交编辑" class="baseBtn btnRight editSubmitBtn" style="display: none;">
						 -->
					<{/if}>
				</h2>
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="depotForm" >
					<tbody>
						<tr>
							<td class="depot_path" width="150px;">SKU：</td>
							<td width="20%"><{$product.product_barcode}></td>
							<td class="depot_path" width="150px;"><{t}>category<{/t}>：</td><!-- 品类 -->
							<td width="20%">
								<span class='span_text'>[<{$product.category.pc_shortname}>]<{$product.category.pc_name}></span>
								<select name="pc_id" class="input_change">
									<{foreach from=$categoryArr item=o name=o}>
									<option value='<{$o.pc_id}>'<{if $o.pc_id==$product.pc_id}>selected<{/if}>>(<{$o.pc_shortname}>)<{$o.pc_name}></option>
									<{/foreach}>
								</select>
							</td>
							<td class="depot_path" width="150px;"><{t}>default_supplier<{/t}>：</td><!-- 默认供应商 -->
							<td>
								<span class='span_text'><{$productDev.default_supplier_code}></span>
								
								<input type="text" value="<{$productDev.default_supplier_code}>"  class="input_text input_change" id='default_supplier_code' name='default_supplier_code_tag' style=''>
								<input type='hidden' name='default_supplier_code' class='default_supplier_code' value='<{$productDev.default_supplier_code}>'/>
								
							</td>
						</tr>
						<tr>
							<td class="depot_path"><{t}>customers_code<{/t}>：</td><!-- 所属客户 -->
							<td><{$product.customer_code}></td>
							<td class="depot_path"><{t}>product_title<{/t}>：</td><!-- 中文名称 -->
							<td rowspan="2" valign="top">
								<span class='span_text'><{$product.product_title}></span>
								<input type="text" value="<{$product.product_title}>" name="product_title" class="input_text input_change" style=''>
							</td>
							<td>
							</td>
							<td>
							</td>
						</tr>
						<tr>
							<td class="depot_path"><{t}>product_weight<{/t}>(kg)：</td><!-- 重量 -->
							<td>
								<span class='span_text'><{$product.product_weight}></span>
								<input type="text" value="<{$product.product_weight}>" name="product_weight" class="input_text input_change" style='width: 50px;'>
							</td>
							<td class="depot_path">&nbsp;</td>
							<td>
							</td>
							<td>
							</td>
						</tr>
						<tr>
							<td class="depot_path"><{t}>product_length<{/t}>*<{t}>product_width<{/t}>*<{t}>product_height<{/t}>(CM)：</td><!-- 长*宽*高 -->
							<td>
								<span class='span_text'><{$product.product_length}></span>
								<input type="text" value="<{$product.product_length}>" name="product_length" class="input_text input_change" style='width: 50px;'>
								*
								<span class='span_text'><{$product.product_width}></span>
								<input type="text" value="<{$product.product_width}>" name="product_width" class="input_text input_change" style='width: 50px;'>
								*
								<span class='span_text'><{$product.product_height}></span>
								<input type="text" value="<{$product.product_height}>" name="product_height" class="input_text input_change" style='width: 50px;'>
							</td>
							<td class="depot_path"><{t}>product_title_en<{/t}>：</td><!-- 产品英文名称 -->
							<td rowspan="2" valign="top">
								<span class='span_text'><{$product.product_title_en}></span>
								<input type="text" value="<{$product.product_title_en}>" name="product_title_en" class="input_text input_change" style=''>
							</td>
							<td>
							</td>
							<td>
							</td>
						</tr>
						<tr>
							<td class="depot_path"><{t}>product_purchase_value<{/t}>：</td><!-- 采购价 -->
							<td>
								<span class='span_text'><{$puPrice.sp_unit_price}></span>
								<input type="text" value="<{$puPrice.sp_unit_price}>" name="product_purchase_value" class="input_text input_change" style='width: 50px;'>
								<span class='span_text'><{$puPrice.currency_code}></span>
								<select name="currency_code" class=" input_change currency_code">
									<{foreach from=$currencyArr item=o name=o}>
									<option value='<{$o.currency_code}>'<{if $o.currency_code==$product.currency_code}>selected<{/if}>><{$o.currency_code}></option>
									<{/foreach}>
								</select>
							</td>
							<td class="depot_path">&nbsp;</td>
							<td>
							</td>
							<td>
							</td>
						</tr>
						<tr>
							<td class="depot_path"><{t}>product_declared_value<{/t}>：</td><!-- 申报价值 -->
							<td>
								<{$productDev.pd_declare_value}>
							</td>
							<td class="depot_path"><{t}>product_declared_name<{/t}>：</td><!-- 海关品名 -->
							<td rowspan="2" valign="top">
								<{$productDev.pd_oversea_code}>
							</td>
							<td>
							</td>
							<td>
							</td>
						</tr>
						<tr>
							<td class="depot_path"><{t}>product_declared_code<{/t}>：</td><!-- 海关申报代码 -->
							<td>
								<{$productDev.pd_oversea_type}>
							</td>
							<td class="depot_path">&nbsp;</td>
							<td>
							</td>
							<td>
							</td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
		
		<!-- 配件，描述，说明===开始 -->
		<div class='depotState'>
			<h2>
				<{t}>fitting<{/t}><!-- 配件 -->&nbsp;&nbsp;/&nbsp;&nbsp;<{t}>description<{/t}><!-- 描述 -->&nbsp;&nbsp;/&nbsp;&nbsp;<{t}>explanation<{/t}><!-- 说明 -->
			</h2>
			<div class="depotTab2">
				<ul>
					<li class="tab" id="tab_fitting">
						<a style="cursor: pointer" href="javascript:void(0);"><{t}>fitting<{/t}></a><!-- 配件信息 -->
					</li>
					<li class="tab" id="tab_desc">
						<a style="cursor: pointer" href="javascript:void(0);"><{t}>product_desc<{/t}></a><!-- 产品描述 -->
					</li>
					<li class="tab" id="tab_shuomin">
						<a style="cursor: pointer" href="javascript:void(0);"><{t}>product_description<{/t}></a><!-- 产品说明 -->
					</li>
				</ul>
			</div>
			<div class='tabContent' id='tabContent_fitting'>
				<form action="fitting" onsubmit='return false;' method='post'>
					<!-- 
					<h2>
						产品配件
						<input type="button" value="编辑" class="baseBtn btnRight editDevBtn"  to='product_develop_fitting_tag'>
						<input type="submit" value="提交编辑" class="baseBtn btnRight editSubmitBtn" style="display: none;">
						<input type="button" id="addProductLine" value="添加配件" disabled="disabled" class='btnRight addBtn'>
					</h2>
					 -->
					<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
						<tr class="table-module-title">
							<td width='160'><{t}>fittings_pictures<{/t}></td><!-- 配件图片 -->
							<td width='160'><{t}>fittings_code<{/t}></td><!-- 配件代码 -->
							<td width='200'><{t}>accessory_name<{/t}></td><!-- 配件名称 -->
							<td><{t}>fittings_description<{/t}></td><!-- 配件描述 -->
						</tr>
						<tbody class="table-module-list-data" id='fitting_wrap'>
							<{if $fitting}> 
								<{foreach from=$fitting name=o item=o}>
								<tr class="">
									<td>
										<img width="75" height="75" src="<{$o.fitting_img}>">
										<input type='hidden' name='fitting_img[]' value='<{$o.fitting_img}>'>
									</td>
									<td>
										<{$o.fitting_code}>
										<input type='hidden' name='fitting_code[]' value='<{$o.fitting_code}>'>
									</td>
									<td>
										<{$o.fitting_name}>
										<input type='hidden' name='fitting_name[]' value='<{$o.fitting_name}>'>
									</td>
									<td>
										<{$o.fitting_desc}>
										<input type='hidden' name='fitting_desc[]' value='<{$o.fitting_desc}>'>
									</td>
								</tr>
								<{/foreach}> 
							<{else}>
								<tr>
									<td colspan="4">
									<{t}>no_data<{/t}><!-- 暂无 -->
									</td>
								</tr>
							<{/if}>
						</tbody>
					</table>
				</form>
			</div>
			<div class='tabContent' id='tabContent_desc'>
				<form action="desc" onsubmit='return false;' method='post'>
					<!-- 
					<h2>
						产品描述
						<input type="button" value="编辑" class="baseBtn btnRight editDevBtn"  to='product_develop_desc_tag'>
						<input type="submit" value="提交编辑" class="baseBtn btnRight editSubmitBtn" style="display: none;">
						<input type="button" id="addDescLine" value="添加描述" disabled="disabled" class='btnRight addBtn'>
					</h2>
					 -->
					<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
						<tr class="table-module-title">
							<td width='100'><{t}>language<{/t}></td><!-- 语言 -->
							<td><{t}>product_desc<{/t}></td><!-- 产品描述 -->
						</tr>
						<{if $prodDesc}> 
							<{foreach from=$prodDesc name=o item=o}>
							<tbody class="table-module-list-data">
								<tr class="">
									<td>
										<span class='span_text'><{$o.language_name}></span>
									</td>								
									<td>
										<div>
											<span class='span_text'><{$o.pdd_description}></span>
											<textarea rows="3" cols="60" name="product_desc[pdd_description][]" class=" input_change"><{$o.pdd_description}></textarea>
										</div>
									</td>
								</tr>
							</tbody>
							<{/foreach}>
						<{else}>
							<tbody class="table-module-list-data">
							<tr>
								<td colspan="2">
								<{t}>no_data<{/t}><!-- 暂无 -->
								</td>
							</tr>
							</tbody>
						<{/if}>
					</table>
				</form>
			</div>
			<div class='tabContent' id='tabContent_shuomin'>
				<!--<h2> 产品说明 </h2>-->
				<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
					<tbody>
						<tr class="table-module-title">
							<td><{t}>tips_name<{/t}></td><!-- 名称 -->
							<td><{t}>content<{/t}></td><!-- 内容 -->
						</tr>
					</tbody>
					<tbody class='table-module-list-data'>
						<{if $explanation}>
							<{foreach from=$explanation name=o item=o}>
							<tr class="">
								<td><{$o.exp_name}></td>
								<td><{$o.exp_desc}></td>
							</tr>
							<{/foreach}>
						<{else}>
							<tr class="">
								<td colspan="2">
									<{t}>no_data<{/t}><!-- 暂无 -->
								</td>
							</tr>
						<{/if}>
					</tbody>
				</table>
			</div>
		</div>
		<!-- 配件，描述，说明===结束 -->
		
		<!-- 产品销量===开始 -->
		<div class='depotState'>
			<h2>
				<!-- 产品销量 -->
			</h2>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
				<tbody>
					<tr class="table-module-title">
						<td><{t}>warehouse<{/t}></td><!-- 仓库 -->
						<td><{t k1='3'}>x_days_average_sales<{/t}></td><!-- 3天平均销量 -->
						<td><{t k1='7'}>x_days_average_sales<{/t}></td><!-- 7天平均销量 -->
						<td><{t k1='14'}>x_days_average_sales<{/t}></td><!-- 14天平均销量 -->
						<td><{t k1='30'}>x_days_average_sales<{/t}></td><!-- 30天平均销量 -->
						<td><{t}>sales_trend<{/t}></td><!-- 销量趋势 -->
						<td><{t}>reference_sales<{/t}></td><!-- 销量参考值 -->
					</tr>
				</tbody>
				<tbody class='table-module-list-data'>
					<{foreach from=$avgSale name=o item=o}>
					<tr class="">
						<td><{$warehouseArr[$o.warehouse_id].warehouse_code}> [<{$warehouseArr[$o.warehouse_id].warehouse_desc}>]</td>
						<td>
							<span class="span_text"><{$o.qty_day3}></span>
							<input type="text" style="" class="input_text input_change" name="sale[qty_day3][<{$o.warehouse_id}>]" value="<{$o.qty_day3}>">
						</td>
						<td>
							<span class="span_text"><{$o.qty_day7}></span>
							<input type="text" style="" class="input_text input_change" name="sale[qty_day7][<{$o.warehouse_id}>]" value="<{$o.qty_day7}>">
						</td>
						<td>
							<span class="span_text"><{$o.qty_day14}></span>
							<input type="text" style="" class="input_text input_change" name="sale[qty_day14][<{$o.warehouse_id}>]" value="<{$o.qty_day14}>">
						</td>
						<td>
							<span class="span_text"><{$o.qty_day30}></span>
							<input type="text" style="" class="input_text input_change" name="sale[qty_day30][<{$o.warehouse_id}>]" value="<{$o.qty_day30}>">
						</td>
						<td><{$o.trend}></td>
						<td><{$o.qty_sales}></td>
					</tr>
					<{/foreach}>
				</tbody>
			</table>
		</div>
		<!-- 产品销量===结束 -->
		
		<!-- 其他信息===开始 -->
		<div class='depotState'>
			<div class="depotTab2">
				<ul>
					<li class="tab" id="tab_picture">
						<a style="cursor: pointer" href="javascript:void(0);"><{t}>product_image<{/t}></a><!-- 产品图片 -->
					</li>
					<li class="tab" id="tab_customer" style="display: none;">
						<a style="cursor: pointer" href="javascript:void(0);"><{t}>customs_info<{/t}></a><!-- 海关信息 -->
					</li>
					<li class="tab" id="tab_price_log">
						<a style="cursor: pointer" href="javascript:void(0);"><{t}>quote_log<{/t}></a><!-- 报价日志 -->
					</li>
					<li class="tab" id="tab_puchase">
						<a style="cursor: pointer" href="javascript:void(0);"><{t}>product_purchasing_info<{/t}></a><!-- 产品采购信息 -->
					</li>
					<li class="tab" id="tab_asn">
						<a style="cursor: pointer" href="javascript:void(0);"><{t}>product_ASN<{/t}></a><!-- 产品ASN -->
					</li>
					<li class="tab" id="tab_order">
						<a style="cursor: pointer" href="javascript:void(0);"><{t}>product_orders<{/t}></a><!-- 产品订单 -->
					</li>
					<li class="tab" id="tab_inventory">
						<a style="cursor: pointer" href="javascript:void(0);"><{t}>product_inventory<{/t}></a><!-- 产品库存 -->
					</li>
					<li class="tab" id="tab_log">
						<a style="cursor: pointer" href="javascript:void(0);"><{t}>product_log<{/t}></a><!-- 产品日志 -->
					</li>
				</ul>
			</div>
			<div class='tabContent' id='tabContent_order'>
				<h2><{t}>products_recently_100_orders<{/t}><!-- 产品最近100条订单信息 --></h2>
				<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
					<tbody>
						<tr class="table-module-title">
							<td><{t}>warehouse<{/t}></td><!-- 仓库 -->
							<td><{t}>orderCode<{/t}></td><!-- 订单号 -->
							<td><{t}>quantity<{/t}></td><!-- 数量 -->
							<td><{t}>status<{/t}></td><!-- 状态 -->
							<!--<td>买家ID</td>-->
							<td><{t}>payTime<{/t}></td><!-- 付款时间 -->
							<td><{t}>createDate<{/t}></td><!-- 添加时间 -->
							<td><{t}>lastUpdate<{/t}></td><!-- 最后修改时间 -->
						</tr>
					</tbody>
					<tbody class='table-module-list-data'>
						<{foreach from=$porder name=o item=o}>
						<tr class="">
							<td><{$warehouseArr[$o.warehouse_id].warehouse_code}></td>
							<td><{$o.order_code}></td>
							<td><{$o.op_quantity}></td>
							<td><{$orderStatusArr[$o.order_status]}></td>
							<!--<td>买家ID</td>-->
							<td><{$o.op_ref_paydate}></td>
							<td><{$o.op_add_time}></td>
							<td><{$o.op_update_time}></td>
						</tr>
						<{/foreach}>
					</tbody>
				</table>
			</div>
			
			<div class='tabContent' id='tabContent_asn'>
				<h2><{t}>products_recently_10_asn<{/t}><!-- 产品最近10条ASN信息 --></h2>
				<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
					<tbody>
						<tr class="table-module-title">
							<td><{t}>warehouse<{/t}></td><!-- 仓库 -->
							<td><{t}>receiving_code<{/t}></td><!-- 入库单号 -->
							<td><{t}>status<{/t}></td><!-- 状态 -->
							<td><{t}>receiving_quantity<{/t}></td><!-- 送货数量 -->
							<td><{t}>putaway_qty<{/t}></td><!-- 上架数量 -->
							<td><{t}>received_quantity<{/t}></td><!-- 收货数量 -->
							<td><{t}>whether_quality<{/t}></td><!-- 是否质检 -->
							<td><{t}>whether_priority<{/t}></td><!-- 是否需要优先处理 -->
						</tr>
					</tbody>
					<tbody class='table-module-list-data'>
						<{foreach from=$pasn name=o item=o}>
						<tr class="">
							<td><{$warehouseArr[$o.warehouse_id].warehouse_code}></td>
							<td><{$o.receiving_code}></td>
							<td><{if $o.rd_status eq "2"}>处理完成<{elseif $o.rd_status eq "1"}>收货中<{else}>在途<{/if}></td>
							<td><{$o.rd_receiving_qty}></td>
							<td><{$o.rd_putaway_qty}></td>
							<td><{$o.rd_received_qty}></td>
							<td><{if $o.is_qc eq "1"}>是<{else}>否<{/if}></td>
							<td><{if $o.is_priority eq "1"}>是<{else}>否<{/if}></td>
						</tr>
						<{/foreach}>
					</tbody>
				</table>
			</div>
			<div class='tabContent' id='tabContent_picture'>
				<form action="picture" onsubmit='return false;' method='post'>
					<!--
					<h2>
						产品图片
						
						<input type="button" value="编辑" class="baseBtn btnRight editDevBtn" to='product_develop_image_tag'>
						<input type="submit" value="提交编辑" class="baseBtn btnRight editSubmitBtn" style="display: none;">						
						<input type="button" id="addPicture" value="添加图片" disabled="disabled" class='addBtn btnRight'>						
					</h2>
					  -->
					<ul class='img_ul'>
						<{if $attachs}> 
							<{foreach from=$attachs name=o item=o}>
							<li>
								<img src='<{$o.src}>' width='75' height='75' />
								<input type='hidden' name='attach[]' value='<{$o.pi_id}>'>
							</li>
							<{/foreach}> 
						<{else}> 
							<li>
							<{t}>no_data<{/t}><!-- 暂无 -->
							</li>
						<{/if}>
					</ul>
					<div class='clear'></div>
				</form>
			</div>
			<div class='tabContent' id='tabContent_price_log'>
				<!-- 
				<h2>产品报价日志</h2>
				 -->
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
					<tr class="table-module-title">
						<td><{t}>supplier<{/t}></td><!-- 供应商 -->
						<td><{t}>product_barcode<{/t}></td><!-- 供应商产品代码 -->
						<td><{t}>product_title<{/t}></td><!-- 供应商产品名称 -->
						<td><{t}>unit_price<{/t}></td><!-- 供应商产单价 -->
						<td><{t}>latest_price<{/t}></td><!-- 供应商产最新单价 -->
						<td><{t}>currency<{/t}></td><!-- 币种 -->
						<td><{t}>delivery<{/t}></td><!-- 交期 -->
						<td><{t}>createrId<{/t}></td><!-- 创建人 -->
						<td><{t}>PurchaseID<{/t}></td><!-- 采购员 -->
						<td><{t}>ModifiedID<{/t}></td><!-- 更新人 -->
						<td><{t}>updateTime<{/t}></td><!-- 更新时间 -->
					</tr>
					<tbody class="table-module-list-data">
						<{if $splRows}> <{foreach from=$splRows name=o item=o}>
						<tr class="">
							<td><{$supplierKArr[$o.supplier_id].supplier_code}></td>
							<td><{$product.product_sku}></td>
							<td><{$o.sp_supplier_product_name}></td>
							<td><{$o.sp_unit_price}></td>
							<td><{$o.sp_last_price}></td>
							<td><{$o.currency_code}></td>
							<td><{$o.sp_eta_time}></td>
							<td><{$userKArr[$o.creater_id].user_name}></td>
							<td><{$userKArr[$o.buyer_id].user_name}></td>
							<td><{$userKArr[$o.updater_id].user_name}></td>
							<td><{$o.sp_update_time}></td>
						</tr>
						<{/foreach}> <{else}> 
							<tr>
								<td colspan="11">
									<{t}>no_data<{/t}><!-- 暂无 -->
								</td>
							</tr>
						<{/if}>
					</tbody>
				</table>
			</div>
			<div class='tabContent' id='tabContent_inventory'>
				<!-- 
				<h2>产品库存</h2>
				 -->
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
					<tr class="table-module-title">
						<td><{t}>warehouse<{/t}></td><!-- 仓库 -->
						<td><{t}>pi_onway<{/t}></td><!-- 在途数量 -->
						<td><{t}>pi_pending<{/t}></td><!-- 到货数量 -->
						<td><{t}>available<{/t}></td><!-- 可用数量 -->
						<td><{t}>pi_hold<{/t}></td><!-- 冻结数量 -->
						<td><{t}>pi_shipped<{/t}></td><!-- 出库数量 -->
						<td><{t}>pi_unsellable<{/t}></td><!-- 不良品数量 -->
						<!-- 
    						<td>最低安全数量</td>
    						<td>最高安全数量</td>
    						<td>最小采购量</td>
    						-->
					</tr>
					<tbody class="table-module-list-data">
						<{if $inventory}> <{foreach from=$inventory name=o item=o}>
						<tr class="">
							<td><{$o.warehouse_name}></td>
							<td><{$o.pi_onway}></td>
							<td><{$o.pi_pending}></td>
							<td><{$o.pi_sellable}></td>
							<td><{$o.pi_reserved}></td>
							<td><{$o.pi_shipped}></td>
							<td><{$o.pi_unsellable}></td>
							<!--
    							<td><{$o.pi_hold}></td>
    							<td><{$o.pi_hold}></td>
    							<td><{$o.pi_hold}></td>
    							-->
						</tr>
						<{/foreach}> <{else}>
						<tr>
							<td colspan="7">
								<{t}>no_data<{/t}><!-- 暂无 -->
							</td>
						</tr>
						<{/if}>
					</tbody>
				</table>
			</div>
			<div class='tabContent' id='tabContent_puchase'>
				<h2><{t}>products_recently_10_purchase<{/t}><!-- 产品最近10条采购信息 --></h2>
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
					<tbody class="table-module-list-data">
						<tr class="">
							<td valign='top'>
								<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
									<tr class="table-module-title">
										<td width='120'><{t}>po_no<{/t}></td><!-- 采购单号 -->
										<td><{t}>status<{/t}></td><!-- 采购状态 -->
										<td><{t}>expected_qty<{/t}></td><!-- 预期数量 -->
										<td><{t}>paid_qty<{/t}></td><!-- 实收数量 -->
										<td><{t}>amount_payable<{/t}></td><!-- 总应付金额 -->
										<td><{t}>actual_amount_paid<{/t}></td><!-- 总实际支付金额 -->
										<td><{t}>unit_price<{/t}></td><!-- 单价 -->
										<!-- <td>默认到目的仓的运输方式</td> -->
									</tr>
									<{if $poProducts}> <{foreach from=$poProducts name=o item=o}>
									<tr class="">
										<td width='120'><{$o.po_code}></td>
										<td><{$o.po_status}></td>
										<td><{$o.qty_expected}></td>
										<td><{$o.qty_receving}></td>
										<td><{$o.payable_amount}></td>
										<td><{$o.actually_amount}></td>
										<td><{$o.unit_price}></td>
									</tr>
									<{/foreach}> <{else}> 
									<tr>
										<td colspan="7">
											<{t}>no_data<{/t}><!-- 暂无 -->
										</td>
									</tr>
									<{/if}>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class='tabContent' id='tabContent_log'>
				<!-- 
				<h2>产品日志</h2>
				 -->
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
					<tr class="table-module-title">
						<td width='100'><{t}>log_type<{/t}></td><!-- 日志类型 -->
						<td width='80'><{t}>premodification_status<{/t}></td><!-- 修改前状态 -->
						<td width='80'><{t}>the_revised_state<{/t}></td><!-- 修改后状态 -->
						<td><{t}>explanation<{/t}></td><!-- 说明 -->
						<td width='130'><{t}>createDate<{/t}></td><!-- 添加时间 -->
						<td width='100'>IP</td>
					</tr>
					<tbody class="table-module-list-data">
						<{if $productLog}> <{foreach from=$productLog name=o item=o}>
						<tr class="">
							<td><{$o.pl_type_title}></td>
							<td><{$o.pl_statu_pre_title}></td>
							<td><{$o.pl_statu_now_title}></td>
							<td><{$o.pl_note}></td>
							<td><{$o.pl_add_time}></td>
							<td><{$o.pl_ip}></td>
						</tr>
						<{/foreach}> <{else}> 
						<tr>
							<td colspan="6">
								<{t}>no_data<{/t}><!-- 暂无 -->
							</td>
						</tr>
						<{/if}>
					</tbody>
				</table>
			</div>
		</div>
		<!-- 其他信息===结束 -->
	</div>
</div>