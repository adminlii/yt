<?php /* Smarty version Smarty-3.1.13, created on 2014-07-22 14:10:27
         compiled from "E:\Zend\workspaces\ruston_oms\application\modules\order\views\order\order_create.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1331653cdffd79a3be0-20674428%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9148e8ade3fd4407d251dbf304ffda1821729709' => 
    array (
      0 => 'E:\\Zend\\workspaces\\ruston_oms\\application\\modules\\order\\views\\order\\order_create.tpl',
      1 => 1406009425,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1331653cdffd79a3be0-20674428',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_53cdffd7d3ce04_90589889',
  'variables' => 
  array (
    'warehouse' => 0,
    'w' => 0,
    'order' => 0,
    'orderProduct' => 0,
    'product' => 0,
    'country' => 0,
    'c' => 0,
    'platform' => 0,
    'shippingMethods' => 0,
    'order_type' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53cdffd7d3ce04_90589889')) {function content_53cdffd7d3ce04_90589889($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'E:\\Zend\\workspaces\\ruston_oms\\libs\\Smarty\\plugins\\block.t.php';
?><link type="text/css" rel="stylesheet" href="/css/public/layout.css" />
<style>
<!--
.input_text {
	width: 250px;
}

.hide {
	display: none;
}

#loading {
	display: none;
}

#search-module {
	overflow: auto;
}
.msg{color:red;padding:0 5px;}
.module-title{
	text-align: right;
}
-->
</style>
<script type="text/javascript">
	paginationPageSize = 8;
	EZ.url = '/order/order/product-';
	EZ.getListData = function (json) {
	    var html = '';
	    var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
	    $.each(json.data, function (key, val) {
	        html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";	        
            html += "<td >" + val.product_sku + "</td>";
            html += "<td >" + val.product_title + "</td>";
            var checked = '';
            if($("#product"+val.product_id).size()>0){
            	checked = 'checked';
            }
	        html += "<td class='ec-center'><input type='checkbox' id='"+val.product_id+"' sku='"+val.product_sku+"' title='"+val.product_title_en+"' title_en='"+val.product_title+"' class='productAddBtn' "+checked+"/></td>";
	        html += "</tr>";
	    });
	    setTimeout('productSelectedCheck()',50);
	    return html;
	}
	function clazzInit(){
		$('.table-module-list-data').each(function(){
			$("tr",this).removeClass('table-module-b1').removeClass('table-module-b2');
			$("tr:even",this).addClass('table-module-b1');
			$("tr:odd",this).addClass('table-module-b2');
		}); 
	}
	function productSelectedCheck(){
	    var size = $('.productAddBtn').size();
	    var checkedSize = $('.productAddBtn:checked').size();
	    if(size==checkedSize){
	        $('.checkAll').attr('checked',true);
	    }else{
	        $('.checkAll').attr('checked',false);
	    }
	}
	
	$(function(){
		clazzInit();		
		$('#search-module').dialog({       
	        autoOpen: false,
	        width: 800,
	        maxHeight: 500,
	        modal: true,
	        show:"slide",
	    	position:'top',
	        buttons: {            
	            'Close': function() {
	                $(this).dialog('close');
	            }
	        },
	        close: function() {
	            
	        },
	        open:function(){
				$(".submitToSearch").click();
	        }
	    });
	    $(".selectProductBtn").click(function(){
	    	$('#search-module').dialog("open");
	    });
		$(".productAddBtn").live('click',function(){
			var productId = $(this).attr("id");
    		var productSku = $(this).attr("sku");
    		var productName = $(this).attr("title");
			if($(this).is(":checked")){
				if($('#product'+productId).size()==0){
					var html = '';
				    html+='<tr id="product'+productId+'">';
				    html+='<td>'+productSku+'</td>';
				    html+='<td>'+productName+'</td>';
				    html+='<td class=""><input class="inputbox inputMinbox op_quantity" type="text" name="op_quantity['+productId+']" value="1" size="8"><span class="red">*</span></td>';
				    html+='<td><a href="javascript:;" class="deleteProductBtn"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
delete<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></td>';			
				    html+='</tr>';
		    			
			        $("#products").append(html);
				}				
			}else{
				$("#product"+productId).remove();
			}
			clazzInit();
		    setTimeout('productSelectedCheck()',50);
		});
		$(".deleteProductBtn").live('click',function(){
			$(this).parent().parent().remove();
			clazzInit();
		});
		$(".checkAll").live('click',function() {
	        if ($(this).is(':checked')) {
	            $(".productAddBtn").each(function(){
	            	$(this).attr('checked', true);
	            	var productId = $(this).attr("id");
	        		var productSku = $(this).attr("sku");
	        		var productName = $(this).attr("title");
	        		if($("#product"+productId).size()==0){
	    			    var html = '';
	    			    html+='<tr id="product'+productId+'">';
	    			    html+='<td>'+productSku+'</td>';
	    			    html+='<td>'+productName+'</td>';
	    			    html+='<td><input class="inputbox inputMinbox" type="text" name="op_quantity['+productId+']" value="1" size="8"><span class="red">*</span></td>';
	    			    html+='<td><a href="javascript:;" class="deleteProductBtn"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
delete<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></td>';			
	    			    html+='</tr>';
	    	    			
	    		        $("#products").append(html);
	    			}
	            	
	            });
	        } else {
	        	$(".productAddBtn").each(function(){
	            	$(this).attr('checked', false);
	            	var productId = $(this).attr("id");
	        		var productSku = $(this).attr("sku");
	        		var productName = $(this).attr("title");
	        		if($("#product"+productId).size()>0){
	        			$("#product"+productId).remove();
	    			}
	            	
	            });
	        }
			clazzInit();
	    });
		
	    $("#orderSubmitBtn").click(function(){
			var param = $("#orderForm").serialize();
			loadStart();
			$.ajax({
			   type: "POST",
			   url: "/order/order/create",
			   data: param,
			   dataType:'json',
			   success: function(json){
				   loadEnd('');
				   var html = json.message;
				   if(json.ask){
					   //html+='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
order_code<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:'+json.ref_id;
					   if($.trim($('#ref_id').val())==''){
							//$('#refrence_no').val('');
							$('#products').html('');
					   }
				   }else{
					   if(json.err){
						    $.each(json.err,function(k,v){
						    	html+="<p>"+v+"</p>";
						    })
					   }
				   }
				   //alertTip(html);

				   $('#create_feedback_div').html(html);
				   $(".feedback_tips").show();
				   setTimeout(function(){scrollTo(0,0);},500);
			   }
			});
	    });

	    $('select').each(function(){
	        var defVal = $(this).attr('default')||'';
	        $(this).val(defVal);
	    });
	    
		//只有一个仓库，默认选择
	    if($('.warehouse_code').size()==1){
			$('.warehouse_code').attr('checked',true);
	    }
	})
</script>
<script type="text/javascript">
	$(function(){
		$('#imCodeSearch').autocomplete({
        	//source: "/product/product/get-by-keyword/limit/20",
        	minLength: 2,
        	delay:100,
    		source:function(request, response) {
        		//增加等待图标
    			$("#imCodeSearch").addClass("autocomplete-loading");
                $("#imCodeSearch").autocomplete({delay: 100});
                var parameter = {};
                parameter["term"] = $("#imCodeSearch").val();
                $.ajax({
                    type :"POST",
                    url: "/product/product/get-by-keyword/limit/20",
                    dataType: "json",
                    data: parameter,
                    success: function(json) {
                    	//关闭等待图标
			            $("#imCodeSearch").removeClass("autocomplete-loading");
                        var objItem = {};
                        if(json != null){
                            objItem = json;
                        }
                        response($.map(objItem, function(item) {
                            /*
                             * 返回自定义的JSON对象
                             */
                            return item;
                        }));
                    }
                });
            },
        	select: function( event, ui ) {
        	    $('#auto_product_id').val(ui.item.product_id);
        	    $('#auto_product_title').val(ui.item.product_title);
        	    $('#auto_product_sku').val(ui.item.product_sku);
        	    $('#auto_product_weight').val(ui.item.product_weight);
        	},
        	search: function( event, ui ) {
        		$('#auto_product_id').val('');
        	    $('#auto_product_title').val('');
        	    $('#auto_product_sku').val('');
        	    $('#auto_product_weight').val('');
            }
            ,open: function() {
            	$(this).removeClass("ui-corner-all").addClass("ui-corner-top");
            }
            ,close: function() {
            	$(this).removeClass("ui-corner-top").addClass("ui-corner-all");
            } 
    	});

		$('#addItem').click(function(){
			var productId = $('#auto_product_id').val();
			var productSku = $('#auto_product_sku').val();
    		var productName = $('#auto_product_title').val();
    		var quantity = $('#imQuantity').val();
    		if($('#imCodeSearch').val()==''){
    			$('#imCodeSearch').focus();
				return;
    		}
    		if(productId==''){
	    		
    			//根据sku搜索产品信息
    			$.ajax({
 				   type: "POST",
 				   url: "/product/product/get-product-by-sku",
 				   data: {'sku':$('#imCodeSearch').val()},
 				   dataType:'json',
                   async: false,
 				   success: function(json){			 				   
 	 				   if(json.ask){
							var product = json.product;
							productId = product['product_id'];
							productSku = product['product_sku'];
							productName = product['product_title'];
 	 				   }else{
 	 	 					
 	 				   }
 				   }
 				});	
 				
        		
			}
    		if(productId==''){
	    		alert('SKU 不存在');
    			$('#imCodeSearch').focus();
				return;
    		}
    		if(quantity==''){
        		$('#imQuantity').focus();
				return;
			}
			//已经选择
			if($('#product'+productId).size()==0){
				var html = '';
			    html+='<tr id="product'+productId+'">';
			    html+='<td>'+productSku+'</td>';
			    html+='<td>'+productName+'</td>';
			    html+='<td><input class="inputbox inputMinbox op_quantity" type="text" name="op_quantity['+productId+']" value="'+quantity+'" size="8"><span class="red">*</span></td>';
			    html+='<td><a href="javascript:;" class="deleteProductBtn"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
delete<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></td>';			
			    html+='</tr>';	    	    			
    		    $("#products").append(html);
    		    clazzInit();

			}else{
				if(window.confirm('SKU'+productSku+'已经存在，是否进行数量累加')){
					var qty_exist = $('#product'+productId+' .op_quantity').val();
					$('#product'+productId+' .op_quantity').val(parseInt(qty_exist)+parseInt(quantity));
				}						
			}
    		  //初始化
	        $('#fillInItem .input_text').val('');
		});
	    function getSm(){
	        var warehouse_id = $(':input[name="warehouse_code"]:checked').attr('warehouse_id');
	        var country_id = $(':input[name="country"] option:selected').attr('country_id');
	        if(!warehouse_id||country_id!=''){
	            //return;
	        }
	        $(':input[name="courier"]').html('<option value=""><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
-select-<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>');
	        var param = {};
	        param.warehouse_id = warehouse_id;
	        param.country_id = country_id;
	        $.ajax({
			   type: "POST",
			   url: "/order/order/get-courier",
			   data: param,
			   dataType:'json',
			   success: function(json){
				  var def = $(':input[name="courier"]').attr('default');
				  var html = '';
				  $.each(json,function(k,v){
					  var selected = def==v.sm_code?'selected':'';
					  var option = '<option value="'+v.sm_code+'" '+selected+'>'+v.sm_code+'</option>'
					  $(':input[name="courier"]').append(option);
				  })
			   }
			});
	    }
		$(':input[name="country"]').change(function(){
			getSm();
		});

		$(':input[name="warehouse_code"]').click(function(){
			getSm();
		});
		
	});
</script>
<div id='loading'>Loading...</div>
<div id="module-container">
	<div id='search-module' style='display: none;' title='Product List&nbsp;&nbsp;[each SKU separate with space or ","]'>
		<form class="submitReturnFalse" name="searchForm" id="searchForm">
			<div class="block ">
				<table cellspacing="3" cellpadding="3">
					<tbody>
						<tr>
							<th>SKUs:</th>
							<td>
								<input type="text" class="input_text" value="" name="product_sku" id='product_sku' size='35' placeholder='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
multi_split_space<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
'>
								&nbsp;
								<input type="submit" class="baseBtn searchProductBtn submitToSearch" value="Search">
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</form>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td width="100"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
SKU<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td width="65" class="ec-center">
						<input type="checkbox" class="checkAll">
					</td>
				</tr>
			</tbody>
			<tbody id="table-module-list-data" class='table-module-list-data'>
			</tbody>
		</table>
		<div class="pagination"></div>
	</div>
	<div id="module-table" style='clear: both;padding:0 45px;'>
		<div class="feedback_tips" >
			<div class="feedback_tips_text" >
				<p id="create_feedback_div">
					
				</p>
			</div>
			<div style="clear: both;"></div>
		</div>		
		<form method='POST' action='' onsubmit='return false;' id='orderForm'>
		    <h1 style="text-align:right;padding: 10px 0 0 0;"><span style='color:#E06B26;'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
orders<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span></h1>
			<h2 style='margin-bottom:10px; border-bottom: 1px dashed #CCCCCC;'>1、<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
warehouse_name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			</h2>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module" style='margin-top: 10px;'>
				<tbody class="table-module-list-data">
					<tr>
						<td style="padding-left: 30px;">
							<?php  $_smarty_tpl->tpl_vars['w'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['w']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['warehouse']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['w']->key => $_smarty_tpl->tpl_vars['w']->value){
$_smarty_tpl->tpl_vars['w']->_loop = true;
?>
								<?php if ($_smarty_tpl->tpl_vars['w']->value['warehouse_type']==0||$_smarty_tpl->tpl_vars['w']->value['warehouse_type']=='0'){?>
								<label style="cursor: pointer;margin: 0 5px;">
									<input type="radio" name='warehouse_code' warehouse_id='<?php echo $_smarty_tpl->tpl_vars['w']->value['warehouse_id'];?>
' value="<?php echo $_smarty_tpl->tpl_vars['w']->value['warehouse_code'];?>
" class='warehouse_code' <?php if (isset($_smarty_tpl->tpl_vars['order']->value)&&$_smarty_tpl->tpl_vars['order']->value['warehouse_code']==$_smarty_tpl->tpl_vars['w']->value['warehouse_code']){?>checked="checked"<?php }?> >
									<?php echo $_smarty_tpl->tpl_vars['w']->value['warehouse_code'];?>
 [<?php echo $_smarty_tpl->tpl_vars['w']->value['warehouse_desc'];?>
]
								</label>
								<?php }?>
							<?php } ?>
						</td>
					</tr>
				</tbody>
			</table>
			
			<h2 style='padding: 35px 0 0 0;margin-bottom:10px; border-bottom: 1px dashed #CCCCCC;'>2、<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
order_select_sku<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2>
			<div style="width: 800px;min-width: 750px;">
			<div style='margin: 5px 0 5px 0;' id="fillInItem">
				<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
SKU<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：<input type="text" class="input_text" id="imCodeSearch" style="width:150px;margin-right: 10px;text-transform: uppercase;" placeholder="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
like_search_before&after<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
">
				<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
quantity<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：<input type="text" class="input_text" id="imQuantity" style="width:80px;margin-right: 10px;">
				<input type="button" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
add<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="baseBtn" id="addItem" style="margin-right: 10px;">
				
				<input type="hidden" id="auto_product_id">
				<input type="hidden" id="auto_product_title">
				<input type="hidden" id="auto_product_sku">
				<input type="hidden" id="auto_product_weight">
				<a class="selectProductBtn" href="javascript:;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
select_product<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
			</div>
			<div style='clear: both;'></div>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
				<tbody>
					<tr class="table-module-title">
						<td width="150"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
SKU<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width="120"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
quantity<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width="100"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
operation<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					</tr>
				</tbody>
				<tbody class="table-module-list-data" id='products'>
					<?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['orderProduct']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value){
$_smarty_tpl->tpl_vars['product']->_loop = true;
?>
					<tr id="product<?php echo $_smarty_tpl->tpl_vars['product']->value['product_id'];?>
" class="">
						<td><?php echo $_smarty_tpl->tpl_vars['product']->value['product_sku'];?>
</td>
						<td><?php echo $_smarty_tpl->tpl_vars['product']->value['product_title'];?>
</td>
						<td>
							<input type="text" size="8" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['op_quantity'];?>
" name="op_quantity[<?php echo $_smarty_tpl->tpl_vars['product']->value['product_id'];?>
]" class="inputbox inputMinbox op_quantity">
							<span class="red">*</span>
						</td>
						<td>
							<a class="deleteProductBtn" href="javascript:;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
delete<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			</div>
			
			<h2 style='padding: 35px 0 0 0;margin-bottom:10px; border-bottom: 1px dashed #CCCCCC;'>3、<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
consignee_info<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module" style='margin-top: 10px;'>
				<tbody class="table-module-list-data">
					<tr class=''>
						<td width="150" class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
consignee_country<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td width="480">
							<select class='input_select' name='country' default='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['consignee_country_code'];?>
<?php }?>'>					
								<!-- <option value=''><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
-select-<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option> -->
								<?php  $_smarty_tpl->tpl_vars['c'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['c']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['country']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['c']->key => $_smarty_tpl->tpl_vars['c']->value){
$_smarty_tpl->tpl_vars['c']->_loop = true;
?>
								<option value='<?php echo $_smarty_tpl->tpl_vars['c']->value['country_code'];?>
' country_id='<?php echo $_smarty_tpl->tpl_vars['c']->value['country_id'];?>
'><?php echo $_smarty_tpl->tpl_vars['c']->value['country_name_en'];?>
(<?php echo $_smarty_tpl->tpl_vars['c']->value['country_name'];?>
)</option>
								<?php } ?>
							</select>
							<span class="msg">*</span>
						</td>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
consignee_company<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
							<input type='text' class='input_text' value='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['consignee_company'];?>
<?php }?>' name='company' />
						</td>
					</tr>
					<tr class=''>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
consignee_state<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
							<input type='text' class='input_text' value='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['consignee_state'];?>
<?php }?>' name='province' />
						</td>
						<td width="150" class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
consignee_name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
							<input type='text' class='input_text' value='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['consignee_name'];?>
<?php }?>' name='name' />
							<span class="msg">*</span>
						</td>
					</tr>
					<tr class=''>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
consignee_city<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
							<input type='text' class='input_text' value='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['consignee_city'];?>
<?php }?>' name='city' />
							<span class="msg">*</span>
						</td>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
consignee_phone<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
							<input type='text' class='input_text' value='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['consignee_phone'];?>
<?php }?>' name='phone' />
							<span class="msg">*</span>
						</td>
					</tr>
					<tr class=''>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
consignee_zip<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
							<input type='text' class='input_text' value='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['consignee_postal_code'];?>
<?php }?>' name='zipcode' />
							<span class="msg">*</span>
						</td>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
consignee_email<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
							<input type='text' class='input_text' value='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['consignee_email'];?>
<?php }?>' name='email' />
						</td>
					</tr>
					<tr>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
consignee_doorplate<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td colspan="3">
							<input type='text' class='input_text' value='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['consignee_doorplate'];?>
<?php }?>' name='doorplate' />
						</td>
					</tr>
					<tr class=''>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
consignee_street1<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td colspan="3">
							<input type='text' class='input_text' value='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['consignee_street1'];?>
<?php }?>' name='address1' />
							<span class="msg">*</span>
						</td>
					</tr>
					<tr class=''>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
consignee_street2<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td colspan="3">
							<input type='text' class='input_text' value='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['consignee_street2'];?>
<?php }?>' name='address2' />
						</td>
					</tr>
				</tbody>
			</table>
			<h2 style='padding: 35px 0 0 0;margin-bottom:10px; border-bottom: 1px dashed #CCCCCC;'>4、<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
else_info<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module" style='margin-top: 10px;'>
				<tbody class="table-module-list-data">
					<tr>
						<td width='150' class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
platform<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td width='480'>
							<select class='input_select' name='platform' default='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['platform'];?>
<?php }?>'>							
								<option value=''><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
-select-<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
								<?php  $_smarty_tpl->tpl_vars['w'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['w']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['platform']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['w']->key => $_smarty_tpl->tpl_vars['w']->value){
$_smarty_tpl->tpl_vars['w']->_loop = true;
?>
								<option value='<?php echo $_smarty_tpl->tpl_vars['w']->value['platform'];?>
'><?php echo $_smarty_tpl->tpl_vars['w']->value['platform'];?>
</option>
								<?php } ?>
							</select>
							<span class="msg">*</span>
						</td>
						<td width="150" class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
reference_no<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
							<input type='text' class='input_text' value='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['refrence_no'];?>
<?php }?>' name='refrence_no' id='refrence_no'/>
							<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
customer_order_no<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

						</td>
						
					</tr>
					<tr>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
shipping_method<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td colspan="1">
							<select class='input_select' name='courier' default='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['shipping_method'];?>
<?php }?>'>					
								<option value=''><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
-select-<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
								<?php  $_smarty_tpl->tpl_vars['c'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['c']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['shippingMethods']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['c']->key => $_smarty_tpl->tpl_vars['c']->value){
$_smarty_tpl->tpl_vars['c']->_loop = true;
?>
								<option value='<?php echo $_smarty_tpl->tpl_vars['c']->value['sm_code'];?>
'><?php echo $_smarty_tpl->tpl_vars['c']->value['sm_code'];?>
 [<?php echo $_smarty_tpl->tpl_vars['c']->value['sm_name_cn'];?>
<!--  -- <?php echo $_smarty_tpl->tpl_vars['c']->value['sm_name'];?>
 -->]</option>
								<?php } ?>
							</select>
							<span class="msg">*</span>
						</td>
						<td width="150" class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
parcel_declared_value<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
							<input type='text' class='input_text' value='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['parcel_declared_value'];?>
<?php }?>' name='parcel_declared_value' id='parcel_declared_value'/>
							USD
						</td>
					</tr>
				</tbody>
			</table>
			<h2 style='padding: 35px 0 0 0;margin-bottom:10px; border-bottom: 1px dashed #CCCCCC;'>5、<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
order_remark<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module" style='margin-top: 10px;'>
				<tbody class="table-module-list-data">
					<tr class=''>
						<td width="150" class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
order_instructions<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td >
							<textarea cols="80" rows="2" name="order_desc" class='input_text' style='width: 450px; height: 55px;'><?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['order_desc'];?>
<?php }?></textarea>
							<br/><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
order_remark_tip<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

						</td>
					</tr>
				</tbody>
			</table>
			
			<?php if (isset($_smarty_tpl->tpl_vars['order']->value)&&$_smarty_tpl->tpl_vars['order']->value['refrence_no_platform']){?>
				<input type="hidden" name='ref_id' value='<?php echo $_smarty_tpl->tpl_vars['order']->value['refrence_no_platform'];?>
' id='ref_id'>
			<?php }?>
			
			<input type="hidden" name='order_type' value='<?php if (isset($_smarty_tpl->tpl_vars['order']->value)){?><?php echo $_smarty_tpl->tpl_vars['order']->value['order_type'];?>
<?php }else{ ?><?php echo $_smarty_tpl->tpl_vars['order_type']->value;?>
<?php }?>' id='order_type'>
			<div style="width: 100%;text-align: center;margin-top: 25px;padding-top:5px; border-top: 1px dashed #CCCCCC;">
				<input type='button' value='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
submit<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
' id='orderSubmitBtn' class='baseBtn submitBtn' style="width: 80px;"/>
			</div>
		</form>
	</div>
</div><?php }} ?>