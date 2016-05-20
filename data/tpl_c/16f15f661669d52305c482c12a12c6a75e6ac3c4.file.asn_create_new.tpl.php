<?php /* Smarty version Smarty-3.1.13, created on 2014-07-22 17:04:37
         compiled from "E:\Zend\workspaces\ruston_oms\application\modules\receiving\views\default\asn_create_new.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1690753ce1248911684-87477766%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '16f15f661669d52305c482c12a12c6a75e6ac3c4' => 
    array (
      0 => 'E:\\Zend\\workspaces\\ruston_oms\\application\\modules\\receiving\\views\\default\\asn_create_new.tpl',
      1 => 1406019875,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1690753ce1248911684-87477766',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_53ce1248d71af2_60716245',
  'variables' => 
  array (
    'asn' => 0,
    'incomeType' => 0,
    'k' => 0,
    'v' => 0,
    'receivingType' => 0,
    'warehouse' => 0,
    'w' => 0,
    'packageType' => 0,
    'asnProduct' => 0,
    'product' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53ce1248d71af2_60716245')) {function content_53ce1248d71af2_60716245($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'E:\\Zend\\workspaces\\ruston_oms\\libs\\Smarty\\plugins\\block.t.php';
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

.datepicker {
	width: 90px;
}

.address {
	padding: 5px 0;
}

.module-title {
	text-align: right;
}
.op_btn{padding:0 5px;}
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
	        html += "<td><input type='checkbox' id='"+val.product_id+"' sku='"+val.product_sku+"' title='"+val.product_title_en+"' title_en='"+val.product_title+"' class='productAddBtn' "+checked+"/></td>";
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
	function addItem(){
		var productId = $('#auto_product_id').val();
		
		var productSku = $('#auto_product_sku').val();
		var productName = $('#auto_product_title').val();
		var quantity = $('#imQuantity').val();
		var boxNo = $('#imBoxNo').val();
		var packageType = $('#imPackage').val(); 
		var weight = parseFloat($('#auto_product_weight').val())*quantity;
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
			$('#imCodeSearch').focus();
			return;
		}        		
		if(quantity==''){
    		$('#imQuantity').focus();
			return;
		}
		if(boxNo==''){
    		$('#imBoxNo').focus();
			return;
		}
		if(packageType==''){
    		$('#imPackage').focus();
			return;
		}
		$('#package_type_tpl option').attr('selected',false);
		
		$('#package_type_tpl .'+packageType).attr('selected',true);

		$('#package_type_tpl select').attr('name','imPackage');
		
		var clone = $('#package_type_tpl').clone();
		
		var html = '';
		html+='<tr>';
	    html+='<td>'+productSku+'</td>';
	    html+='<td>'+productName+'<input class="inputbox inputMinbox product_id" type="hidden" value="'+productId+'" size="8"></td>';
	    html+='<td><input class="inputbox inputMinbox quantity" type="text" value="'+quantity+'" size="8"><span class="red">*</span></td>';
	    html+='<td><input class="inputbox inputMinbox box_no" type="text" value="'+boxNo+'" size="8"><span class="red">*</span></td>';
	    html+='<td >'+clone.html()+'<span class="red">*</span></td>';
		//html+='<td>'+weight+'</td>';
	    html+='<td><a href="javascript:;" class="deleteProductBtn"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
delete<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></td>';			

	    html+='</tr>';
			
        $("#products").append(html);
        //初始化
        $('#fillInItem .input_text').val('');
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
	    $("#selectProductBtn").click(function(){
	    	$('#search-module').dialog("open");
	    });
		$(".productAddBtn").live('click',function(){
			var productId = $(this).attr("id");
    		var productSku = $(this).attr("sku");
    		var productName = $(this).attr("title");
			if($(this).is(":checked")){
				var html = '';
			    html+='<tr id="product'+productId+'">';
			    html+='<td>'+productSku+'</td>';
			    html+='<td>'+productName+'</td>';
			    html+='<td><input class="inputbox inputMinbox" type="text" name="quantity['+productId+']" value="1" size="8"><span class="red">*</span></td>';
			    html+='<td><a href="javascript:;" class="deleteProductBtn"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
delete<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></td>';			
			    html+='</tr>';
	    			
		        $("#products").append(html);
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
		
		$('#addItem').click(function(){
			addItem();
		});
		$('#clearItem').click(function(){		    
		    jConfirm('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
are_you_sure<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
clear_all_item<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', function(r) {
		        if(r){
		        	$("#products").html('');
		        }
		    });	        
		})
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
	    			    html+='<td><input class="inputbox inputMinbox" type="text" name="quantity['+productId+']" value="1" size="8"><span class="red">*</span></td>';
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
	    $("#asnSubmitBtn").click(function(){
			var productHmlt = '';
			$('#products tr').each(function(k){
				var product_id = $('.product_id',this).val();
				var quantity = $('.quantity',this).val();
				var box_no = $('.box_no',this).val();
				var package_type = $('.package_type',this).val();
				productHmlt += '<div>';
				productHmlt+='<input type="text" name="product['+k+'][product_id]" value="'+product_id+'">';
				productHmlt+='<input type="text" name="product['+k+'][quantity]" value="'+quantity+'">';
				productHmlt+='<input type="text" name="product['+k+'][box_no]" value="'+box_no+'">';
				productHmlt+='<input type="text" name="product['+k+'][package_type]" value="'+package_type+'">';
				productHmlt += '</div>';
			});

			$('#product_wrap').html(productHmlt);
			var param = $("#asnForm").serialize();
			loadStart();			
			$.ajax({
			   type: "POST",
			   url: "/receiving/receiving/create",
			   data: param,
			   dataType:'json',
			   success: function(json){
				   loadEnd('');
				   var html = json.message;
				   if(json.ask){
					   //html+='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
asn_code<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:'+json.receiving_code;
					   if($.trim($('#receiving_code').val())==''){
							$('#refrence_no').val('');
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
	    /*
	    $('#receiving_type').change(function(){
	        var receiving_type = $(this).val();
	        if(receiving_type=='3'){
	            $('.transit_warehouse_div').show();
	        }else{
	            $('.transit_warehouse_div').hide();
	        }
	    });
	    setTimeout(function(){
	    	$('#receiving_type').change();
	    },200);
	    */
        $(".datepicker").datepicker({ dateFormat: "yy-mm-dd", defaultDate: +1});

        $('#income_type').change(function(){
            var income_type = $(this).val();
			if(income_type=='0'||income_type==0){
				$('.income_type_0').show();
				$('.income_type_1').hide();
			}else{
				$('.income_type_0').hide();	
				$('.income_type_1').show();			
			}
        }).change();

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
        		//alert(ui.item.template_content_id);
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
	})
</script>
<script type="text/javascript">

function regionInit(){
	$.ajax({
		   type: "POST",
		   url: "/common/region/get-region-for-receiving",
		   data: {'pid':'1'},
		   dataType:'json',
		   success: function(json){
		   		var html = '';
			var def = $('#c_level_0').attr('default');
				$.each(json,function(k,v){
					var select = '';
					if(def==v.region_id){
						select = 'selected';
					}
					html+='<option value="'+v.region_id+'" '+select+'>'+v.region_name+'</option>';		
				});
				//alert(html);
				$('#c_level_0').html(html);
				$('#c_level_1').html('');
				$('#c_level_2').html('');
				//if(def!=''){
					$('#c_level_0').change();
				//}
		   }
		});	
}
$(function(){	    
	regionInit();
    $('#c_level_0').change(function(){
    	var pid = $(this).val();
    	var param = {'pid':pid};
    	$.ajax({
		   type: "POST",
		   url: "/common/region/get-region-for-receiving",
		   data: param,
		   dataType:'json',
		   success: function(json){
		   		var html = '';
				var def = $('#c_level_1').attr('default');
				$.each(json,function(k,v){
					var select = '';
					if(def==v.region_id){
						select = 'selected';
					}
					html+='<option value="'+v.region_id+'" '+select+'>'+v.region_name+'</option>';		
					//html+='<option value="'+v.region_id+'">'+v.region_name+'</option>';					
				});
				$('#c_level_1').html(html);
				$('#c_level_2').html('');
				//if(def!=''){
					$('#c_level_1').change();
				//}
		   }
		});	
    });
    $('#c_level_1').change(function(){
    	var pid = $(this).val();
    	var param = {'pid':pid};
    	$.ajax({
		   type: "POST",
		   url: "/common/region/get-region-for-receiving",
		   data: param,
		   dataType:'json',
		   success: function(json){
		   		var html = '';
				var def = $('#c_level_2').attr('default');
				$.each(json,function(k,v){
					var select = '';
					if(def==v.region_id){
						select = 'selected';
					}
					html+='<option value="'+v.region_id+'" '+select+'>'+v.region_name+'</option>';					
				});
				$('#c_level_2').html(html);
		   }
		});	
    });
})
</script>
<link type="text/css" rel="stylesheet" href="/css/public/ajaxfileupload.css" />
<script type="text/javascript" src="/js/ajaxfileupload.js"></script>
<script type="text/javascript">
function ajaxFileUpload(formId,btnId)
{	loadStart();
	var param = {};
	$.ajaxFileUpload
	(
		{
			url:$('#'+formId).attr('action'), 
			secureuri:false,
			fileElementId:btnId,
			dataType: 'json',
			data:param,
			success: function (json, status)
			{		
				loadEnd('')		
				var html = json.message+"<br/>";
				$.each(json.err,function(k,v){
				    html+= v+'<br/>';
				})
				$('#dialog-auto-alert-tip p').html(html);
				if(json.ask){	
					$.each(json.data,function(k,v){
						$('#imCodeSearch').val(v.product_sku);
						$('#auto_product_id').val(v.product_id);						
						$('#auto_product_sku').val(v.product_sku);
						$('#auto_product_title').val(v.product_title);
						$('#imQuantity').val(v.rd_receiving_qty);
						$('#imBoxNo').val(v.box_no);
						$('#imPackage').val(v.package_type); 
						$('#auto_product_weight').val(v.product_weight);
						addItem();
					})				
								
				}else{
					var html = json.message;
					$.each(json.err,function(k,v){		
						html+='<p>'+(k+1)+':'+v+'</p>';
					});
					alertTip(html,500,500);	
				}
			},
			error: function (data, status, e)
			{
				//alert(e);
			}
		}
	)	
	return false;
}  
var addressJson = {};
function loadAddress(){
	$.ajax({
		   type: "POST",
		   url: "/receiving/address/list",
		   data: {},
		   dataType:'json',
		   success: function(json){
		   var html = '';
			   if(json.state){  
	  				addressJson = json.data;	  	  			    
	  			    $.each(json.data,function(k,v){
	  	  			    html+='<tr>';
	  	  			    html+='<td>'+v.region_0_title+'</td>';
	  	  			    html+='<td>'+v.region_1_title+'</td>';
	  	  			    html+='<td>'+v.region_2_title+'</td>';
	  	  			    html+='<td>'+v.street+'</td>';
	  	  			    html+='<td>'+v.contacter+'</td>';
	  	  			    html+='<td>'+v.contact_phone+'</td>';
	  	  			    html+='<td>'+v.is_default_title+'</td>';
	  	  			    html+='<td>';
      	  	  	  	    html+='<a href="javascript:;" class="select_address_btn op_btn" key="'+k+'"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
select_address_btn<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>';
      	  	  	  	    html+='<a href="javascript:;" class="set_address_default_btn op_btn" key="'+k+'"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
set_address_default_btn<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>';
      	  	  	  	    html+='<a href="javascript:;" class="deletet_address_btn op_btn" key="'+k+'"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
deletet_address_btn<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>';
	  	  			    html+='</td>';
	  	  			    html+='</tr>';
	  			    })
			   }else{
	  			    var size = $('#select_address_wrap .table-module-title>td').size();
	  	  			html+='<tr>';
	  			    html+='<td colspan="'+size+'">'+'No Data'+'</td>';
	  			    html+='</tr>';
			   }
			    $('#address_all').html(html);
		   }
		});	
	
}
$(function(){
    $('#batch_upload_wrap').dialog({
        autoOpen: false,
        width: 800,
        modal: true,
        show: "slide",
        buttons: [
            {
                text: 'Upload',
                click: function () {
                    $(this).dialog("close");
                    ajaxFileUpload("upload_form_new","fileToUpload_new");
                }
            },
            {
                text: 'Close',
                click: function () {
                    $(this).dialog("close");
                }
            }
        ],
        close: function () {
        }
    });
    
	$('#addItemBatch').click(function(){
	    $('#batch_upload_wrap').dialog('open');
	});
	$('#select_address_wrap').dialog({
        autoOpen: false,
        width: 800,
        modal: true,
        show: "slide",
        position:'top',
        buttons: [           
            {
                text: 'Close',
                click: function () {
                    $(this).dialog("close");
                }
            }
        ],
        close: function () {
        },
        open:function(){
        	loadAddress();
        }
    });
	$('#select_address').click(function(){
		$('#select_address_wrap').dialog('open');
	});

	$('.select_address_btn').live('click',function(){
	    var key = $(this).attr('key');
	    $('#c_level_0').attr('default',addressJson[key].region_0);
	    $('#c_level_1').attr('default',addressJson[key].region_1);
	    $('#c_level_2').attr('default',addressJson[key].region_2);

	    $('#address_street').val(addressJson[key].street);
	    $('#address_contacter').val(addressJson[key].contacter);
	    $('#address_phone').val(addressJson[key].contact_phone);
	    regionInit();
	    $('#select_address_wrap').dialog('close');
	});

	$('.set_address_default_btn').live('click',function(){
	    var key = $(this).attr('key');
	    var rd_id = addressJson[key].rd_id;
	    
	    jConfirm('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
are_you_sure<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
set_address_default_btn<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', function(r) {
	        if(r){
	        	$.ajax({
	     		   type: "POST",
	     		   url: "/receiving/address/set-default",
	     		   data: {'paramId':rd_id},
	     		   dataType:'json',
	     		   success: function(json){
	     			    var html = '';
	     			   if(json.state){ 
	     				    loadAddress();
	     			   }else{
	     	  			   
	     			   }
	     			    alertTip(json.message);
	     		   }
	     		});	
	        }
	    });
	    
	});

	$('.deletet_address_btn').live('click',function(){
	    var key = $(this).attr('key');
	    var rd_id = addressJson[key].rd_id;
	    jConfirm('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
are_you_sure<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
deletet_address_btn<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', function(r) {
	        if(r){
	        	$.ajax({
		     		   type: "POST",
		     		   url: "/receiving/address/delete",
		     		   data: {'paramId':rd_id},
		     		   dataType:'json',
		     		   success: function(json){
		     			    var html = '';
		     			   if(json.state){ 
		     				    loadAddress();
		     			   }else{
		     	  			   
		     			   }
		     			   alertTip(json.message);
		     		   }
		     		}); 
	        }
	    });
	    
	})
});

</script>
<div id="module-container">
	<div id='search-module' style='display: none;' title='Product List&nbsp;&nbsp;[each SKU separate with space or ","]'>
		<form class="submitReturnFalse" name="searchForm" id="searchForm">
			<div class="block ">
				<table cellspacing="3" cellpadding="3">
					<tbody>
						<tr>
							<th>SKUs:</th>
							<td>
								<input type="text" class="input_text" value="" name="product_sku" id='product_sku' size='35'>
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
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
SKU<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td>
						<input type="checkbox" class="checkAll">
						<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
operation<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

					</td>
				</tr>
			</tbody>
			<tbody id="table-module-list-data" class='table-module-list-data'>
			</tbody>
		</table>
		<div class="pagination"></div>
	</div>
	<input type="button" class="baseBtn" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
select_product<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" id="selectProductBtn" style='float: right; display: none;'>
	<div id="module-table" style='clear: both; padding: 0 45px;'>
		<div class="feedback_tips">
			<div class="feedback_tips_text">
				<p id="create_feedback_div"></p>
			</div>
			<div style="clear: both;"></div>
		</div>
		<form method='POST' action='' onsubmit='return false;' id='asnForm'>
			<h2 style="border-bottom: 1px dashed #CCCCCC; margin-bottom: 10px;">1、<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
ruku_info<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
				<tbody class="table-module-list-data">
					<tr class=''>
						<td width='150' class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
income_type<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
							<select class='input_select' name='income_type' default='<?php if (isset($_smarty_tpl->tpl_vars['asn']->value)){?><?php echo $_smarty_tpl->tpl_vars['asn']->value['income_type'];?>
<?php }?>' id='income_type'>
								<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['incomeType']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value){
$_smarty_tpl->tpl_vars['v']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['v']->key;
?>
								<option value="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['v']->value;?>
</option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr class='income_type_1'>
						<td width='150' class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
receiving_user_address<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
							<div style="background-color: #FFFFFF; border: 1px solid #DDDDDD; padding: 5px; width: 45%;">
								<div class='address'>
									<div style="width: 65px; float: left; text-align: right;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
state_or_province<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</div>
									<select id='c_level_0' name='region_0' class='c_level input_select' default='<?php if (isset($_smarty_tpl->tpl_vars['asn']->value)){?><?php echo $_smarty_tpl->tpl_vars['asn']->value['region_0'];?>
<?php }?>'></select>
									&nbsp;&nbsp;<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
city<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：&nbsp;&nbsp;
									<select id='c_level_1' name='region_1' class='c_level input_select' default='<?php if (isset($_smarty_tpl->tpl_vars['asn']->value)){?><?php echo $_smarty_tpl->tpl_vars['asn']->value['region_1'];?>
<?php }?>'></select>
									&nbsp;&nbsp;<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
zone<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：&nbsp;&nbsp;
									<select id='c_level_2' name='region_2' class='c_level input_select' default='<?php if (isset($_smarty_tpl->tpl_vars['asn']->value)){?><?php echo $_smarty_tpl->tpl_vars['asn']->value['region_2'];?>
<?php }?>'></select>
									<span class="red">&nbsp;*</span>
									<a href='javascript:;' id='select_address'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
-select-<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
								</div>
								<div class='address'>
									<div style="width: 65px; float: left; text-align: right;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
street<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</div>
									&nbsp;&nbsp;
									<div style="width: 300px; float: left;">
										<input type="text" id="address_street" name="street" value="<?php if (isset($_smarty_tpl->tpl_vars['asn']->value)){?><?php echo $_smarty_tpl->tpl_vars['asn']->value['street'];?>
<?php }?>" class="input_text">
										<span class="red">&nbsp;*</span>
									</div>
								</div>
								<div class='address'>
									<div style="width: 65px; float: left; text-align: right;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
contact<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</div>
									&nbsp;&nbsp;
									<div style="width: 300px; float: left;">
										<input type="text" id="address_contacter" name="contacter" value="<?php if (isset($_smarty_tpl->tpl_vars['asn']->value)){?><?php echo $_smarty_tpl->tpl_vars['asn']->value['contacter'];?>
<?php }?>" class="input_text">
										<span class="red">&nbsp;*</span>
									</div>
								</div>
								<div class='address'>
									<div style="width: 65px; float: left; text-align: right;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
phone<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</div>
									&nbsp;&nbsp;
									<div style="width: 300px; float: left;">
										<input type="text" id="address_phone" name="contact_phone" value="<?php if (isset($_smarty_tpl->tpl_vars['asn']->value)){?><?php echo $_smarty_tpl->tpl_vars['asn']->value['contact_phone'];?>
<?php }?>" class="input_text">
										<span class="red">&nbsp;*</span>
									</div>
								</div>
								<div class='address'>
									<div style="width: 65px; float: left; text-align: right;">&nbsp;</div>
									&nbsp;&nbsp;
									<div style="width: 300px; float: left;">
									    <label>
										<input type="checkbox" name="is_default" value="1" class="">
										<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
set_address_default_btn<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

										</label>
									</div>
								</div>
							</div>
						</td>
					</tr>
					<tr class='' style='display: none;'>
						<td width='150' class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
asn_type<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
							<select class='input_select' name='receiving_type' default='<?php if (isset($_smarty_tpl->tpl_vars['asn']->value)){?><?php echo $_smarty_tpl->tpl_vars['asn']->value['receiving_type'];?>
<?php }?>' id='receiving_type'>
								<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['receivingType']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value){
$_smarty_tpl->tpl_vars['v']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['v']->key;
?>
								<option value="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['v']->value;?>
</option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr class='income_type_0' style=''>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
receiving_warehouse_name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
							<select class='input_select' name='transit_warehouse_code' default='<?php if (isset($_smarty_tpl->tpl_vars['asn']->value)){?><?php echo $_smarty_tpl->tpl_vars['asn']->value['transit_warehouse_code'];?>
<?php }?>'>
								<!-- <option value=''><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
-select-<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option> -->
								<?php  $_smarty_tpl->tpl_vars['w'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['w']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['warehouse']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['w']->key => $_smarty_tpl->tpl_vars['w']->value){
$_smarty_tpl->tpl_vars['w']->_loop = true;
?>
								<option value='<?php echo $_smarty_tpl->tpl_vars['w']->value['warehouse_code'];?>
'><?php echo $_smarty_tpl->tpl_vars['w']->value['warehouse_code'];?>
 [<?php echo $_smarty_tpl->tpl_vars['w']->value['warehouse_desc'];?>
]</option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr class='' style=''>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
target_warehouse<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
							<select class='input_select' name='warehouse_code' default='<?php if (isset($_smarty_tpl->tpl_vars['asn']->value)){?><?php echo $_smarty_tpl->tpl_vars['asn']->value['warehouse_code'];?>
<?php }?>'>
								<!-- <option value=''><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
-select-<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option> -->
								<?php  $_smarty_tpl->tpl_vars['w'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['w']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['warehouse']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['w']->key => $_smarty_tpl->tpl_vars['w']->value){
$_smarty_tpl->tpl_vars['w']->_loop = true;
?> <?php if ($_smarty_tpl->tpl_vars['w']->value['warehouse_type']==0||$_smarty_tpl->tpl_vars['w']->value['warehouse_type']=='0'){?>
								<option value='<?php echo $_smarty_tpl->tpl_vars['w']->value['warehouse_code'];?>
'><?php echo $_smarty_tpl->tpl_vars['w']->value['warehouse_code'];?>
 [<?php echo $_smarty_tpl->tpl_vars['w']->value['warehouse_desc'];?>
]</option>
								<?php }?> <?php } ?>
							</select>
						</td>
					</tr>
					<tr class=''>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
reference_no<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
							<input type='text' class='input_text' value='<?php if (isset($_smarty_tpl->tpl_vars['asn']->value)){?><?php echo $_smarty_tpl->tpl_vars['asn']->value['reference_no'];?>
<?php }?>' name='refrence_no' id='refrence_no' />
						</td>
					</tr>
					<tr class='income_type_0'>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
shipping_method<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
							<input type='text' class='input_text' value='<?php if (isset($_smarty_tpl->tpl_vars['asn']->value)){?><?php echo $_smarty_tpl->tpl_vars['asn']->value['shipping_method'];?>
<?php }?>' name='shipping_method' />
							<span class="warmTips">如:顺丰.</span>
						</td>
					</tr>
					<tr class='income_type_0'>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
tracking_no<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
							<input type='text' class='input_text' value='<?php if (isset($_smarty_tpl->tpl_vars['asn']->value)){?><?php echo $_smarty_tpl->tpl_vars['asn']->value['tracking_number'];?>
<?php }?>' name='tracking_no' />
						</td>
					</tr>
					<tr class=''>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
eta_date<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
							<input type='text' class='input_text datepicker' value='<?php if (isset($_smarty_tpl->tpl_vars['asn']->value)){?><?php echo $_smarty_tpl->tpl_vars['asn']->value['expected_date'];?>
<?php }?>' name='eta_date' />
						</td>
					</tr>
					<tr class=''>
						<td class="module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
receiving_desc<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
						<td>
							<textarea cols="80" rows="2" name="receiving_desc" class='input_text' style='width: 400px; height: 40px;'><?php if (isset($_smarty_tpl->tpl_vars['asn']->value)){?><?php echo $_smarty_tpl->tpl_vars['asn']->value['receiving_description'];?>
<?php }?></textarea>
							<?php if (isset($_smarty_tpl->tpl_vars['asn']->value)&&$_smarty_tpl->tpl_vars['asn']->value['receiving_code']){?>
							<input type="hidden" name='receiving_code' value='<?php echo $_smarty_tpl->tpl_vars['asn']->value['receiving_code'];?>
' id='receiving_code'>
							<?php }?>
						</td>
					</tr>
				</tbody>
			</table>
			<div id='product_wrap' style='display: none;'></div>
		</form>
		<h2 style='padding: 35px 0 0 0; margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC;'>
			2、<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
receiving_select_sku<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

			<span style='font-size: 12px; font-weight: normal; padding-left: 5px;'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
receiving_select_sku_tip<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>
		</h2>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" id="fillInItem">
			<tbody>
				<tr>
					<td width="5%" style="text-align: right;">SKU：</td>
					<td width="11%" height="35">
						<input type="text" id="imCodeSearch" class="input_text" style="width: 140px; text-transform: uppercase;" placeholder="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
like_search_before&after<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
">
						<input type="hidden" id="auto_product_id" class="input_text" style="">
						<input type="hidden" id="auto_product_title" class="input_text" style="">
						<input type="hidden" id="auto_product_sku" class="input_text" style="">
						<input type="hidden" id="auto_product_weight" class="input_text" style="">
					</td>
					<td width="5%" style="text-align: right;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
quantity<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
					<td width="6%">
						<input type="text" id="imQuantity" class="input_text" style="width: 50px;" placeholder='> 0'>
					</td>
					<td width="5%" style="text-align: right;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
box_no<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
					<td width="6%">
						<input type="text" title="" id="imBoxNo" class="input_text" style="width: 50px;" placeholder='1~100'>
					</td>
					<td width="5%" style="text-align: right;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
package_type<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
					<td width="10%">
						<select title="" id="imPackage" class="input_select" style="width: 120px;" mark="packageCode">
							<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['packageType']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value){
$_smarty_tpl->tpl_vars['v']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['v']->key;
?>
							<option value="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['v']->value;?>
</option>
							<?php } ?>
						</select>
					</td>
					<td>
						<input type="button" id="addItem" class="baseBtn" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
add<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="publicBtn4">
						<input type="button" id="clearItem" class="baseBtn" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
clear<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="publicBtn4">
						&nbsp;&nbsp;&nbsp;&nbsp;
						<a id='addItemBatch' href='javascript:;'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
batch_upload<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
					</td>
				</tr>
			</tbody>
		</table>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td style="width: 80px;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
SKU<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td style="width: 40%"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
quantity<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
box_no<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
package_type<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					<!-- 
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
weight<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					 -->
					<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
operation<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				</tr>
			</tbody>
			<tbody class="table-module-list-data" id='products'>
				<?php if (isset($_smarty_tpl->tpl_vars['asnProduct']->value)){?> <?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['asnProduct']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value){
$_smarty_tpl->tpl_vars['product']->_loop = true;
?>
				<tr class="">
					<td style="width: 80px;"><?php echo $_smarty_tpl->tpl_vars['product']->value['product_sku'];?>
</td>
					<td style="width: 40%">
						<?php echo $_smarty_tpl->tpl_vars['product']->value['product_title'];?>

						<input class="inputbox inputMinbox product_id" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['product_id'];?>
" size="8">
					</td>
					<td>
						<input type="text" size="8" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['rd_receiving_qty'];?>
" class="inputbox inputMinbox quantity">
						<span class="red">*</span>
					</td>
					<td>
						<input type="text" size="8" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['box_no'];?>
" class="inputbox inputMinbox box_no">
						<span class="red">*</span>
					</td>
					<td>
						<select class="input_select package_type" title="" default='<?php echo $_smarty_tpl->tpl_vars['product']->value['package_type'];?>
'>
							<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['packageType']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value){
$_smarty_tpl->tpl_vars['v']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['v']->key;
?>
							<option value="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['v']->value;?>
</option>
							<?php } ?>
						</select>
						<span class="red">*</span>
					</td>
					<!-- 
					<td>
						<?php echo $_smarty_tpl->tpl_vars['product']->value['line_weight'];?>

					</td>
					 -->
					<td>
						<a class="deleteProductBtn" href="javascript:;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
delete<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
					</td>
				</tr>
				<?php } ?> <?php }?>
			</tbody>
		</table>
		<div style="width: 100%; text-align: center; margin-top: 25px; padding-top: 5px; border-top: 1px dashed #CCCCCC;">
			<input type='button' value='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
submit<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
' id='asnSubmitBtn' class='baseBtn submitBtn' style="width: 80px;" />
		</div>
	</div>
</div>
<div id='package_type_tpl' style='display: none;'>
	<select class="input_select package_type" title="">
		<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['packageType']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value){
$_smarty_tpl->tpl_vars['v']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['v']->key;
?>
		<option class='<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
' value="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['v']->value;?>
</option>
		<?php } ?>
	</select>
</div>
<div id='batch_upload_wrap' style='display: none;' title='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
batch_upload_products<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
'>
	<form method='post' enctype="multipart/form-data" action='/receiving/receiving/upload' id='upload_form_new' onsubmit='return false;' style='float: left; width: 100%;'>
		<table cellspacing="0" cellpadding="0" border="0" class="table-module" style='width: 100%;'>
			<tbody class="table-module-list-data">
				<tr class="table-module-b2">
					<td class="dialog-module-title" width='150'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
xls_file<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
					<td>
						<input type="file" name="fileToUpload" id="fileToUpload_new" class="input_text baseBtn" />
						<a href="/file/asn_product.xls" target="_blank" style='color: red;'>《<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
template<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
》</a>
					</td>
				</tr>
				<tr class="table-module-b2">
					<td class="dialog-module-title" width='150'></td>
					<td>
						<b style="color: #E06B26;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
please_note<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</b> <br> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
only_xls<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>
<div id='select_address_wrap' style='display: none;' title='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
select_address<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
'>
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table-module">
		<tbody>
			<tr class="table-module-title">
				<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
state_or_province<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
city<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
zone<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
address<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
contact<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
phone<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				<td>&nbsp;</td>
				<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
operation<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
			</tr>
		</tbody>
		<tbody id="address_all" class="table-module-list-data">
		</tbody>
	</table>
</div>
<?php }} ?>