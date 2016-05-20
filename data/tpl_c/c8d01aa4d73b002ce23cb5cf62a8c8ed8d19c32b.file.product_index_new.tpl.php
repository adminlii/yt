<?php /* Smarty version Smarty-3.1.13, created on 2014-07-22 10:53:43
         compiled from "E:\Zend\workspaces\ruston_oms\application\modules\product\views\product\product_index_new.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1380753cdd237f0c732-80310421%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c8d01aa4d73b002ce23cb5cf62a8c8ed8d19c32b' => 
    array (
      0 => 'E:\\Zend\\workspaces\\ruston_oms\\application\\modules\\product\\views\\product\\product_index_new.tpl',
      1 => 1405996337,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1380753cdd237f0c732-80310421',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'asnStatusActionJson' => 0,
    'session_id' => 0,
    'category' => 0,
    'o' => 0,
    'asnStatus' => 0,
    'k' => 0,
    's' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_53cdd2382cf8b4_24260489',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53cdd2382cf8b4_24260489')) {function content_53cdd2382cf8b4_24260489($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'E:\\Zend\\workspaces\\ruston_oms\\libs\\Smarty\\plugins\\block.t.php';
?><script type="text/javascript" src="/js/pageEffects.js"></script>
<script language="javascript" src="/lodop/LodopFuncs.js"></script>
<script language="javascript" src="/lodop/lodop_print.js"></script>
<script> 
var asnStatusActionJson = <?php echo $_smarty_tpl->tpl_vars['asnStatusActionJson']->value;?>
;
var LODOP;	
    EZ.url = '/product/Product/';
    EZ.getListData = function (json) {
        //alert(json.data[0][]);
    	$('.checkAll').attr('checked',false);
        var html = '';
        var status='';
        var receiving='';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
            

            html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1' id='product_"+val.product_id+"' product_id='"+val.product_id+"'>" : "<tr class='table-module-b2' id='product_"+val.product_id+"'  product_id='"+val.product_id+"'>";
            html += "<td class='ec-center' ><input type='checkbox' name='productCodes[]' class='productCodes'  value='"+val.product_id+"'/> </td>";
           
            		var have_asn = ((val.have_asn == 1)?"icon_sku_used":"icon_sku_un_used");
            		var have_asn_title = ((val.have_asn == 1)?"<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
has_asn<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
":"<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
not_has_asn<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
");
            		var contain_battery = ((val.contain_battery == 1)?"icon_battery":"icon_un_battery");
            		var contain_battery_title = ((val.contain_battery == 1)?"<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
contain_battery<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
":"<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
not_contain_battery<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
");
            		
                    html += "<td class=''>"
                    			+"<div style='width:100%;'><a class='product_sku' onclick='leftMenu(\""+"SKU"+val.product_sku+"\",\"<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:"+val.product_sku+"\","
                            	+ "\"/product/product/detail/skuid/" + val.product_id +"\")'"
                           	 	+ " href='javascript:void(0)'>" + val.product_sku + "</a></div>"
								
                           	 	+"<div style='width:100%;'><span class='"+have_asn+"' title='"+have_asn_title+"'></span><span class='"+contain_battery+"' title='"+contain_battery_title+"'></span></div>"
                            +"</td>";
                    //html += "<td class='product_title_en'>" + val.product_title_en + "</td>";
                    html += "<td class=''>" 
								+ "<div style='width:60%;float:left;' class='ellipsis ' title='"+val.product_title+"'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
p_product_title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：<span class='product_title'>"+ val.product_title +"</span></div><div style='width:35%;float:left;'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
ref_no<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
："+val.reference_no+"</div>"
								+ "<div style='width:60%;float:left;' class='ellipsis' title='"+val.product_declared_name+"'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_declared_name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
："+val.product_declared_name+"</div><div style='width:35%;float:left;'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_declared_value<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
："+val.product_declared_value+" USD</div>"
                           +"</td>";
                    html += "<td class='pc_title'>" + val.pc_title + "</td>";
                    html += "<td class='pc_le_wi_he'>" + val.product_length + " * " + val.product_width + " * " + val.product_height + "</td>";
                    html += "<td >" + val.product_weight + "</td>";
                    html += "<td class=''>" + val.product_status_title + "</td>";
                    //html += "<td >" + val.have_asn_title + "</td>";
            html += "<td>";
            //html +="<a href='javascript:void(0)' onclick='leftMenu(\""+"SKU"+val.product_sku+"\",\"<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:"+val.product_sku+"\",\"/product/product/detail/skuid/" + val.product_id +"\")'>"+EZ.view+"</a>";
            //html +='&nbsp;|&nbsp;<a href="javascript:;" class="editBtn" product_id="'+val.product_id+'"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
edit<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>';
            //html +='&nbsp;|&nbsp;';
            if(val.product_status=='2'){
            	html +='<a href="javascript:;" class="createBtn" product_id="'+val.product_id+'"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
edit<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>';
            	html +="&nbsp;|&nbsp;<a  href='javascript:;' onclick=\"leftMenu('product-cpy-"+val.product_sku+"','<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
create_product<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
','/product/product/create/cpy/1/product_id/"+val.product_id+"');\"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
cpy<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>";
            	
            }else{
            	html +="<a  href='javascript:;' onclick=\"leftMenu('product-cpy-"+val.product_sku+"','<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
create_product<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
','/product/product/create/cpy/1/product_id/"+val.product_id+"');\"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
cpy<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>";
            }
            
            html +="</td>";
            html += "</tr>";
        });
        return html;
    }

    function printLabel(paper,this_){

        var param = $("#printLabelForm").serialize();
        
        var product_label_arr = [];
        $('.id_qty').each(function(){
            var product_id = $(this).attr('product_id');
            var qty = $(this).val();
            product_label_arr.push(product_id+"_"+qty);
        });

        var param = {'session_id':'<?php echo $_smarty_tpl->tpl_vars['session_id']->value;?>
'};
        param.paper = paper;
        param.lodop = '1';
        param.param = product_label_arr.join(',');
       
        var p = '';
        for(var i in param){
        	p+= i+'/'+param[i]+'/';
        }

        this_.dialog("close");
        $.ajax({
		   type: "POST",
		   async: false,
		   url: "/product/product/print/",
		   data: param,
		   dataType:'script',
		   success: function(msg){
			   print_lodop(); 
		   }
		});
        $(".checkAll").attr('checked',false);
        
        $(".productCodes").attr('checked',false);
    }
    $(function(){
    	//setMainPrintDialog();
        $('.editBtn').live('click',function(){
            var product_id = $(this).attr('product_id');
            editById(product_id)
            $('#editDataForm #product_sku').attr('readonly',true).css('background','#ddd');
        });
        $('.createButton').live('click',function(){
            $('#editDataForm #product_sku').attr('readonly',false).css('background','#fff');
        });
        $('.createBtn').live('click',function(){
        	var product_id = $(this).attr('product_id');
        	leftMenu('product_'+product_id,'修改产品','/product/product/create/product_id/'+product_id);
        });
		
        $("#dialogPrint").dialog({
            autoOpen: false,
            width: 800,
            maxHeight: 500,
            modal: true,
            show: "slide",
            buttons: [
                {
                    text: "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
confirm<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
(70x30 Lodop)",
                    click: function () {
                    	$('#paper').val('70x30');
                    	$('#lodop').val('1');
                    	var this_ = $(this);
                    	printLabel('70x30',this_);
                    }
                },                
                /*
                {
                    text: "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
confirm<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
(70x30 Web)",
                    click: function () {
                    	$('#paper').val('70x30');
                    	$('#lodop').val('');
                       	 var product_label_arr = [];
                         $('.id_qty').each(function(){
                             var product_id = $(this).attr('product_id');
                             var qty = $(this).val();
    			                product_label_arr.push(product_id+"_"+qty);
                         });
                         $('#id_qty').val(product_label_arr.join(','));
                    	$("#printLabelForm").submit();
                        if($(".checkAll").attr('checked')){
                            $(".checkAll").attr('checked',false);
                        }
                        $(".productCodes").attr('checked',false);
                        $(this).dialog("close");
                    }
                } ,
                */
                {
                    text: "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
confirm<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
(100x30 Lodop)",
                    click: function () {
                    	$('#paper').val('100x30');
                    	$('#lodop').val('1');
                    	var this_ = $(this);
                    	printLabel('100x30',this_);
                    }
                },
                {
                    text: "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
confirm<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
(A4 Lodop)",
                    click: function () {
                    	$('#paper').val('A4');
                    	$('#lodop').val('1');
                    	var this_ = $(this);
                    	printLabel('A4',this_);
                    }
                },
                /**
                {
                    text: "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
confirm<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
(A4 Web)",
                    click: function () {
                    	$('#paper').val('A4');
                    	$('#lodop').val('');
                    	var product_label_arr = [];
                        $('.id_qty').each(function(){
                            var product_id = $(this).attr('product_id');
                            var qty = $(this).val();
    			                product_label_arr.push(product_id+"_"+qty);
                        });
                        $('#id_qty').val(product_label_arr.join(','));
                    	$("#printLabelForm").submit();
                        $(".checkAll").attr('checked',false);
                        $(".productCodes").attr('checked',false);
                        $(this).dialog("close");
                    }
                },
                */
                {
                    text: "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
cancel<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
",
                    click: function () {
                        $(this).dialog("close");
                    }
                }
            ]
        });

        $(".checkAll").live('click',function() {
            if ($(this).is(':checked')) {
                $(".productCodes").attr('checked', true);
            } else {
                $(".productCodes").attr('checked', false);
            }
        });


        $(".printButton").live('click',function(){
            productCodes = "";
            $("#printData").html("");
            $(".productCodes:checked").each(function(k,v){
                productCodes+=$(this).val()+",";
                var id = $(this).parents("tr").attr("id");
                var product_id = $(this).parents("tr").attr("product_id");
                atd = $("#"+id).children("td");
                var product_sku = $('.product_sku',$(this).parents("tr")).text();
                var product_title = $('.product_title',$(this).parents("tr")).text();
                var pc_title = $('.pc_title',$(this).parents("tr")).text();
                var clazz = k%2==0?'table-module-b1':'table-module-b2';
                html = "<tr class='"+clazz+"'><td>"+product_sku+"</td><td>"+product_title+"</td><td>"+pc_title+"</td>" +
                        "<td><input type='text' value='1' class='input_text id_qty' style='width:100px;' product_id='"+product_id+"' name='product["+product_id+"]'/> </td></tr>";
                $("#printData").append(html);
            });
            if(productCodes==""){                
                alert("<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pls_select_product<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
");
                return false;
            }
            $("[name='productCodes']").val(productCodes);
            $("#dialogPrint").dialog('open');
        });

        /**
         * 滚动保持头部固定
         */
        var clone = $('#fix_header_content .clone').clone();
        $('#fix_header').append(clone);
        $(window).scroll(function(){
            var off = $('#fix_header_content').offset();
            var top = off.top;
            var scrollTop = $(this).scrollTop();
            if(scrollTop>top){
                $('#fix_header').show();
                $('.sub_status_container').hide();
            }else{
                $('#fix_header').hide();            
            }
            
        });

        /**
         * 状态点击处理
         */
        $(".statusTag").click(function(){
			var re_status = $(this).attr("status");
			$("#product_status").val(re_status);
			$(".statusTag").removeClass("chooseTag");
			$(".indexTag" + re_status).addClass("chooseTag");
			$('.opration_area_wrap .opDiv').html('');
			$(asnStatusActionJson[re_status]).each(function(k,v){
			    $('.opration_area_wrap .opDiv').append(v);
			})
			$('.submitToSearch:visible').click();
        });

        $('.asnDiscardBtn').live('click',function(){
        	if ($(".productCodes:checked").size() < 1) {
                alertTip('<span class="tip-error-message"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pls_select_sku<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>');
                return false;
            }
        	jConfirm('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
are_you_sure<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_discard_tip<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', function(r) {
        		if(r){
                    var codeArr = [];
                    $(".productCodes:checked").each(function(){
                        var code = $(this).val();
                        codeArr.push(code);
                    })
                    var param = {'product_id':codeArr};
                	$.ajax({
                        type: "post",
                        dataType: "json",
                        url: "/product/product/discard",
                        data: param,
                        success: function (json) {
                            var html = '';
                            $.each(json,function(k,v){
                                html+='<p>'+v.message+'</p>';
                            })
                            alertTip(html,800);
                            initData(paginationCurrentPage-1);
                        }
                    });
        		}
        	});
        })
        $('.asnDeleteBtn').live('click',function(){
        	if ($(".productCodes:checked").size() < 1) {
                alertTip('<span class="tip-error-message"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pls_select_sku<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>');
                return false;
            }
        	jConfirm('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
are_you_sure<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_delete_tip<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', function(r) {
        		if(r){
                    var codeArr = [];
                    $(".productCodes:checked").each(function(){
                        var code = $(this).val();
                        codeArr.push(code);
                    })
                    var param = {'product_id':codeArr};
                	$.ajax({
                        type: "post",
                        dataType: "json",
                        url: "/product/product/delete",
                        data: param,
                        success: function (json) {
                            var html = '';
                            $.each(json,function(k,v){
                                html+='<p>'+v.message+'</p>';
                            })
                            alertTip(html,800);
                            initData(paginationCurrentPage-1);
                        }
                    });
        		}
        	});
        })
        
        $('.asnVerifyBtn').live('click',function(){
        	if ($(".productCodes:checked").size() < 1) {
                alertTip('<span class="tip-error-message"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pls_select_sku<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>');
                return false;
            }
        	$('<div title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
verify<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"><p align=""><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_verify_tip<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<br/><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
are_you_sure<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</p></div>').dialog({
                autoOpen: true,
                width: 400,
                maxHeight:500,
                modal: true,
                closeOnEscape:false,
                position:'top',
                show: "slide",
                buttons: [
                    {
                        id:'verify_ok',
                        text: 'Ok',
                        click: function () {
                            var this_ = $(this);
                        	$('.ui-button',this_.parent()).hide();
                            var codeArr = [];
                            $(".productCodes:checked").each(function(){
                                var code = $(this).val();
                                codeArr.push(code);
                            })
                            var param = {'product_id':codeArr};
                            this_.html('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_verifying_tip<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
                        	$.ajax({
                                type: "post",
                                dataType: "json",
                                url: "/product/product/verify",
                                data: param,
                                success: function (json) {
                                	$('.ui-button',this_.parent()).show();
                                	$('#verify_ok').hide();
                                    var html = '';
                                    $.each(json,function(k,v){
                                        html+='<p>'+v.message+'</p>';
                                    })
                                    this_.html(html);
                                    initData(paginationCurrentPage-1);
                                }
                            });
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
                    $(this).remove();
                },
                open:function(){
                	
                }
            });
            
        })
            
    });
$(function(){

	$('.sort').click(function(){		
	    var name= $(this).attr('name');
	    var sort= $(this).attr('sort');
	    var nowSort = '';
	    if(sort==''||sort=='asc'){
	    	nowSort = 'desc';
	    }else{
	    	nowSort = 'asc';
	    }
		$('.sort').attr('class','sort');
		$(".sort[name='"+name+"']").addClass(nowSort);
		$(".sort[name='"+name+"']").attr('sort',nowSort);
		$('.sort span').text('');
		$('.asc span').text('↑');
		$('.desc span').text('↓');
	    $('#order_by').val(name+' '+nowSort);
	    
	    initData(paginationCurrentPage-1);
		
	});

    $('.baseExport').click(function(){

    	jConfirm('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_export_tip<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
export<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', function(r) {
    		if(r){
    	        $('#exportForm').html('');
    	    	$('#exportForm').append($('<input/>').attr('name','ac').attr('value','export'));
    	        $('#searchForm :input').each(function(){
    	            var name =  $(this).attr('name');
    	            var val =  $(this).val();
    	        	$('#exportForm').append($('<input/>').attr('name',name).attr('value',val));
    	        });
    	        $('#exportForm').submit();
    		}
    	});
    })
})
</script>
<link type="text/css" rel="stylesheet" href="/css/public/ajaxfileupload.css" />
<script type="text/javascript" src="/js/ajaxfileupload.js"></script>
<script type="text/javascript">

function ajaxFileUpload()
{
	var param = $('#import_product_div form').serialize();
	alertTip('loading...');
	$.ajaxFileUpload
	(
		{
			url:'/product/product/upload?'+param, 
			secureuri:false,
			fileElementId:'fileToUpload',
			//data : param,
			dataType: 'json',
			success: function (json, status)
			{
				$('#dialog-auto-alert-tip').dialog('moveToTop');
				var html = json.message+"<br/>";
				$.each(json.err,function(k,v){
				    html+= v+'<br/>';
				})
				$('#dialog-auto-alert-tip p').html(html);
			},
			error: function (data, status, e)
			{
				$('#dialog-auto-alert-tip').dialog('close');
				//alert(e);
			}
		}
	)
	
	return false;

}  

$(function(){
	$("#import_product_div").dialog({
        autoOpen: false,
        width: 600,
        maxHeight: 500,
        modal: true,
        show: "slide",
        buttons: [
            {
                text: "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
confirm<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
",
                click: function () {                    
                    $(this).dialog("close");
                    $('#uploadBtn').click();
                }
            },
            {
                text: "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
cancel<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
",
                click: function () {
                    $(this).dialog("close");
                }
            }
        ]
    });

	$('#importButton').click(function(){
		$('#import_product_div').dialog('open');
	})
});
</script>
<style>
<!--
.f_right {
	float: right;
}

.mar_r10 {
	margin-right: 10px;
}

.dialog_div {
	display: none;
}
.ellipsis {
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    width: 100%;
}
.icon_un_battery {
    background: url("/images/base/battery_blue.png") no-repeat scroll 0 -1px / 24px auto rgba(0, 0, 0, 0);
    display: block;
    height: 18px;
    vertical-align: middle;
    white-space: nowrap;
    width: 20px;
    cursor: pointer;
    float: left;
}
.icon_battery {
    background: url("/images/base/battery_red.png") no-repeat scroll 0 -1px / 24px auto rgba(0, 0, 0, 0);
    display: block;
    height: 18px;
    vertical-align: middle;
    white-space: nowrap;
    width: 20px;
    cursor: pointer;
    float: left;
}
.icon_sku_un_used {
    background: url("/images/base/item-unused.png") no-repeat scroll 0 -2px / 20px auto rgba(0, 0, 0, 0);
    display: block;
    height: 20px;
    vertical-align: middle;
    white-space: nowrap;
    width: 20px;
    cursor: pointer;
    float: left;
}
.icon_sku_used {
    background: url("/images/base/Item-used.png") no-repeat scroll 0 -2px / 20px auto rgba(0, 0, 0, 0);
    display: block;
    height: 20px;
    vertical-align: middle;
    white-space: nowrap;
    width: 20px;
    cursor: pointer;
    float: left;
}

#hide_first_title td {
    height: 1px;
    line-height: 1px;
    overflow: hidden;
    padding-bottom: 0;
    padding-top: 0;
}

.opBtn{margin:5px 10px 0 0;}

.opration_area {
    height: auto;
}
.opration_area {
    height: 36px;
    margin-top: 3px;
    padding: 0 2px;
}

.opDiv {
    padding: 0 0 4px;
}
.baseExport {
    float: right;
}
.Tab ul li {
    padding: 0 0px;
}
.Tab ul li li {
	background: none repeat scroll 0 0 #FFFFFF;
	border: 0 none;
	float: none;
	height: 26px;
	line-height: 26px;
}
.Tab .sub_status_container{font-weight:normal;
    background: none repeat scroll 0 0 #FFFFFF;
    display: none;
    left: 0;
    position: absolute;
    text-align: center;
    border: 1px solid #CCCCCC;
    border-bottom: 0 none;
    margin-left:-1px;
    width: 100%;
    z-index: 10;
}
.statusTag{display:block;padding:0 25px;}
.Tab a:hover{color:#000; border-bottom: 2px solid #87B87F;box-shadow: 0 0 1px #87B87F;}
.Tab .chooseTag{font-weight:bold;color:#393939;border-bottom: 2px solid #87B87F}
.sort {
	cursor: pointer;
	color: red;
}

-->
</style>
<div id="module-container">
	<div id="ez-wms-edit-dialog" style="display: none;">
		<table class="dialog-module" border="0" cellpadding="0" cellspacing="0">
			<tbody>
				<input type="hidden" name="product_id" id="product_id" value="" />
				<tr>
					<td class="dialog-module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
SKU<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</td>
					<td>
						<input type="text" name="product_sku" id="product_sku" value="" class="input_text" validator="required" err-msg="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
require<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"  />
						<span class="msg">*</span>
					</td>
				</tr>
		        <!-- 
		        <tr>
					<td class="dialog-module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
p_product_title_en<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</td>
					<td>
						<input type="text" name="product_title_en" id="product_title_en" value="" class="input_text" validator="required" err-msg="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
require<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" />
						<span class="msg">*</span>
					</td>
				</tr>
		         -->
				<tr>
					<td class="dialog-module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
p_product_title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</td>
					<td>
						<input type="text" name="product_title" id="product_title" value="" class="input_text" validator="required" err-msg="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
require<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" />
						<span class="msg">*</span>
					</td>
				</tr>
				<!--
				<tr style="">
					<td class="dialog-module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_sales_value<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
(RMB):</td>
					<td>
						<input type="text" name="product_sales_value" id="product_sales_value" value="" class="input_text" />
					</td>
				</tr>
				<tr style="">
					<td class="dialog-module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_purchase_value<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
(RMB):</td>
					<td>
						<input type="text" name="product_purchase_value" id="product_purchase_value" value="" class="input_text" />
					</td>
				</tr>
				-->
				<tr>
					<td class="dialog-module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_declared_value<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
(USD):</td>
					<td>
						<input type="text" name="product_declared_value" id="product_declared_value" validator="required" err-msg="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
require<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_weight<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
(KG):</td>
					<td>
						<input type="text" name="product_weight" id="product_weight" validator="required" err-msg="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
require<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_length<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
(CM):</td>
					<td>
						<input type="text" name="product_length" id="product_length" validator="required" err-msg="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
require<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_width<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
(CM):</td>
					<td>
						<input type="text" name="product_width" id="product_width" validator="required" err-msg="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
require<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_height<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
(CM):</td>
					<td>
						<input type="text" name="product_height" id="product_height" validator="required" err-msg="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
require<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="input_text" />
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_category<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</td>
					<td>
						<select name="pc_id" id="pc_id" class="input_select">
							<?php  $_smarty_tpl->tpl_vars['o'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['o']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['category']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['o']->key => $_smarty_tpl->tpl_vars['o']->value){
$_smarty_tpl->tpl_vars['o']->_loop = true;
?>
							<option value="<?php echo $_smarty_tpl->tpl_vars['o']->value['pc_id'];?>
"><?php echo $_smarty_tpl->tpl_vars['o']->value['pc_shortname'];?>
[<?php echo $_smarty_tpl->tpl_vars['o']->value['pc_name'];?>
]</option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
contain_battery<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</td>
					<td>
						<select name="contain_battery" id="contain_battery" class="input_select">
							<option value="0"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
not_contain_battery<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
							<option value="1"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
contain_battery<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="search-module" style="border-bottom: 1px solid #E0E0E0;">
		<form id="searchForm" name="searchForm" class="submitReturnFalse">
			<input type="hidden" value="" id="filterActionId" />
			<div style="" class="search-module-condition">
				<span style="width: 90px;" class="searchFilterText"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
SKU<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</span>
				<input type="text" name="product_sku" id="product_sku" class="input_text keyToSearch" style="text-transform: uppercase;"  placeholder="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
like_search_before&after<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>
				<input type="hidden" name="product_status" id="product_status" class="input_text keyToSearch" />
				<input type="hidden" name="order_by" id="order_by" class="input_text keyToSearch" />
				
			</div>
			<div style="" class="search-module-condition">
				<span style="width: 90px;" class="searchFilterText"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</span>
				<input type="text" name="product_title_like" id="product_title_like" class="input_text keyToSearch" placeholder="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
like_search_before&after<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>
				&nbsp;&nbsp;
				<input type="button" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
search<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="baseBtn submitToSearch" />
				<!--   <a href="/product/Product/to-Add-Product"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
create<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>-->
				<!-- 
				<input type="button" class="baseBtn" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
add<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" id="createButton">
				 -->
				<!-- 
				<input type="button" id="printButton" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
printLabel<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="baseBtn f_right" />
				 -->
				<!-- 
				<input type="button" id="importButton" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
batch_upload<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="baseBtn f_right mar_r10" />
				 -->
			</div>
		</form>
	</div>
	
	<div id="fix_header_content" style="z-index: 9;">
		<div class="clone">
			<div class="Tab">
				<ul>
				    <li class="">
						<a  data-id="" status="" class="indexTag statusTag chooseTag" href="javascript:void (0)">
							<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
all<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

						</a>
					</li>
					<?php  $_smarty_tpl->tpl_vars['s'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['s']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['asnStatus']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['s']->key => $_smarty_tpl->tpl_vars['s']->value){
$_smarty_tpl->tpl_vars['s']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['s']->key;
?>
					<li class="">
						<a  data-id="" status="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" class="indexTag<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
 statusTag" href="javascript:void (0)">
							<?php echo $_smarty_tpl->tpl_vars['s']->value;?>

						</a>
					</li>
					<?php } ?>
					<li style='float:right;border:0 none;padding:0;'>
					<input type="button" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
printLabel<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="baseBtn f_right printButton" />
	                <input type="button" class="baseBtn baseExport f_right" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
export<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" style="margin-right:10px;">
					</li>					
				</ul>
			</div>
			<div class="opration_area_wrap" style=" margin-left: 10px;">
				<div class="opDiv"></div>
			</div>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table-module">
				<tbody>
					<tr class="table-module-title">
        				<td width="3%" class="ec-center">
        					<input type="checkbox" class="checkAll" />
        				</td>
        				<td width='80'  name="product_sku" sort="" class="sort" ><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
SKU<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<span></span></td>        				
        				<td class="ec-center" ><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
        				<td width="200"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
p_product_category<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
        				<td width="188"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
length<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 * <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
width<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 * <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
height<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 (CM)</td>
        				<td width='80'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
weight<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
(KG)</td>
        				<td width='80'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
p_product_status<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
        				<td width='70'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
operation<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
        			</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div id='fix_header' style='position: fixed; top: 0px; display: none; background: #fff;z-index:9;width: 99.2%'></div>
	<div id="module-table">
		<div class="opration_area" id="operate-received" style="display: none;">
			<div class="opration_area_rit">
				<input style="background-color: orangered" type="button" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
export<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="baseBtn" onclick="exportExcel();" />
			</div>
		</div>
		<form id="receivedForm" name="receivedForm" class="submitReturnFalse">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
				<tr class="table-module-title" id="hide_first_title" style='height:1px;'>
					<td width="3%" class="ec-center"></td>
					<td width="80"></td>
					<td ></td>
					<td width="200"></td>
					<td width="188"></td>
					<td width="80"></td>
					<td width="80"></td>
					<td width='70'></td>
				</tr>
				<tbody id="table-module-list-data"></tbody>
			</table>
		</form>
	</div>
	
	<div class="pagination"></div>
</div>
<div title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
p_print_label<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" id="dialogPrint" class="dialog-edit-alert-tip" style="display: none">
	<form id="printLabelForm" name="printLabelForm" action="/product/product/print" method="post" target="_blank">
		<input type="hidden" value="" name="productCodes" />
		<input type="hidden" value="A4" name="paper" id='paper'>
		<input type="hidden" value="" name="lodop" id='lodop'>
		<input type="hidden" value="" name="param" id='id_qty'>		
		<table class="table-module" cellspacing="0" cellpadding="0" border="0" width="100%">
			<tr>
				<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
SKU<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_title<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
product_category<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
p_quantity<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
			</tr>
			<tbody id="printData">
			</tbody>
		</table>
	</form>
</div>
<div class='dialog_div' title='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
p_batch_upload<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
' id='import_product_div'>
	<form enctype="multipart/form-data" method="POST" action="" name="form" onsubmit='return false;'>
		<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
select_file<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：
		<input type='file' class='input_text' id='fileToUpload' name='fileToUpload'>
		<input type='button' class='' onclick='return ajaxFileUpload();' value='Upload' id='uploadBtn' style='display: none;'>
	</form>
	<div class="search-module-condition" style='clear: both;'>
		<b style="color: #E06B26;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
p_notice<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</b>：<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
only_xls<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<a href="/file/product.xls" target="_blank" style="color: #0090E1;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
template<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
		
	</div>
	<div class="search-module-condition"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array('escape'=>'')); $_block_repeat=true; echo smarty_block_t(array('escape'=>''), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
p_notice_content<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array('escape'=>''), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</div>
</div>
<object id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0>
	<embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0 pluginspage="install_lodop32.exe"></embed>
</object>

<form id='exportForm' style='display:none;' method='post' target='_blank' action='/product/product/list'>
    
</form><?php }} ?>