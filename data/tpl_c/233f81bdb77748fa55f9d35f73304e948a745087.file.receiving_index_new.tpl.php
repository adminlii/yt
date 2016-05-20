<?php /* Smarty version Smarty-3.1.13, created on 2014-07-22 15:27:24
         compiled from "E:\Zend\workspaces\ruston_oms\application\modules\receiving\views\default\receiving_index_new.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1047253ce125c1535c0-93654765%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '233f81bdb77748fa55f9d35f73304e948a745087' => 
    array (
      0 => 'E:\\Zend\\workspaces\\ruston_oms\\application\\modules\\receiving\\views\\default\\receiving_index_new.tpl',
      1 => 1405996339,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1047253ce125c1535c0-93654765',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'asnStatusActionJson' => 0,
    'session_id' => 0,
    'asnStatus' => 0,
    'k' => 0,
    's' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_53ce125c34b9b8_88364499',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53ce125c34b9b8_88364499')) {function content_53ce125c34b9b8_88364499($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'E:\\Zend\\workspaces\\ruston_oms\\libs\\Smarty\\plugins\\block.t.php';
if (!is_callable('smarty_block_ec')) include 'E:\\Zend\\workspaces\\ruston_oms\\libs\\Smarty\\plugins\\block.ec.php';
?><style type="text/css">
.table-module td {
	word-break: break-all;
	word-wrap: break-word;
}

.searchFilterText {
	width: 90px;
}
#hide_first_title td {
    height: 1px;
    line-height: 1px;
    overflow: hidden;
    padding-bottom: 0;
    padding-top: 0;
}
.opBtn{margin:5px 10px 0 0;}
</style>
<script language="javascript" src="/lodop/LodopFuncs.js"></script>
<script language="javascript" src="/lodop/lodop_print.js"></script>
<script src='/js/jquery-ui-timepicker-addon.js'></script>
<script type="text/javascript">
    EZ.url = '/receiving/receiving/';
    EZ.printList = '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
printList<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
';
    var asnStatusActionJson = <?php echo $_smarty_tpl->tpl_vars['asnStatusActionJson']->value;?>
;
    EZ.getListData = function (json) {
    	onDefualtStatus();
    	$('.checkAll').attr('checked',false);
        var html = '';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        var type, status, warehouse,standardWarehouse,transferStatus;
        $.each(json.data, function (key, val) {
            html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
            html += "<td class='ec-center'><input class='asnCodes' type='checkbox' name='asnCodes[]'id='asnCodes[]' value='" + val.receiving_code + "' eta='"+val.expected_date+"'></td>";
            html += "<td class='ec-center'>" + (i++) + "</td>";
            html += "<td ><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
asn_code<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：<a href='javascript:void(0)' " +
                    "onclick='leftMenu(\"rd-26\",\"<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
receivedDetail<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
\",\"/receiving/receiving/view-detail/code/" + val.receiving_code + "\")'>" + val.receiving_code + "</a><br>";
            html += "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
ref_no<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：" + val.reference_no + "<br>";
            html += "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
tracking_no<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：" + val.tracking_number + "<br>";
            html += "</td>";
            html += "<td ><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
target_warehouse<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: " + val.warehouse_code+' <br/><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
receiving_warehouse_name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
: '+val.transit_warehouse_code + "</td>";
            html += "<td >" + val.income_type_title + "</td>";
            html += "<td >" + val.sku_species +  "</td>";
            html += "<td >" + val.sku_total +  "</td>";
            html += "<td >" + val.receiving_status_title +  "</td>";
            html += "<td >" + (val.receiving_description) + "</td>";
            var add_time = val.receiving_add_time;
            add_time = (add_time != '')?add_time.substr(0,10):add_time;
            var update_time = val.receiving_update_time;
            update_time = (update_time != '')?update_time.substr(0,10):update_time;
            var expected_time = val.expected_date;
            expected_time = (expected_time != '')?expected_time.substr(0,10):expected_time;
            
            html += "<td ><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
create_time<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：" + add_time +'<br><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
update_time<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：'+update_time+ "<br><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
eta_date<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
："+expected_time+"</td>";

            html += "<td>";
            var htmldsh ='';//待审核
            
            if(val.receiving_status=='1'){
                //状态
            	html += "<a id='verify_"+val.receiving_code+"' href=\"javascript:verify('"+val.receiving_code+"')\" eta_data='"+val.receiving_code+"' "+" ><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
verify<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>";
            	html += "&nbsp;|&nbsp;<a  href='javascript:;' onclick=\"leftMenu('ASN-"+val.receiving_code+"','<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
edit_asn<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
','/receiving/receiving/create/receiving_code/"+val.receiving_code+"');\"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
edit<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>";
            	html += "<br/><a  href='javascript:;' onclick=\"leftMenu('ASN-cpy-"+val.receiving_code+"','<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
create_asn<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
','/receiving/receiving/create/cpy/1/receiving_code/"+val.receiving_code+"');\"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
cpy<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>";
            	
             }else{
            	html += "<a  href='javascript:;' onclick=\"leftMenu('ASN-cpy-"+val.receiving_code+"','<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
create_asn<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
','/receiving/receiving/create/cpy/1/receiving_code/"+val.receiving_code+"');\"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
cpy<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>";
            
            }
            html += "&nbsp;|&nbsp;<a  href='javascript:;' class='printReceivingBtn' receiving_code='"+val.receiving_code+"'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
print<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>";
            
            html += "</td>";
            html += "</tr>";
        });
        return html;
    };
    function printList(code) {
        window.open("/receiving/receiving/print-list/code/" + code);
    }
    function printBox(code,box_no_arr){
    	window.open("/receiving/receiving/print-box/code/" + code+'/box_no/'+box_no_arr.join(','));
    }

    function printLabel(id_qty,paper){
    	window.open("/receiving/receiving/print-label/paper/"+paper+"/param/"+id_qty.join(','));
    }
    function viewDetail(receivingCode) {
        window.open("/receiving/receiving/view-detail/code/" + receivingCode);
    }
    function verify(id){
        //审核
        /*var rec_id = $('#verify_'+id).attr('rec_id');
        alert(rec_id);*/
        $("#selectRecId").val(id);
        var selecteta_data = $('#verify_'+id).attr('eta_data');
        $("#selecteta_data").val(selecteta_data);
        $("#expected").val('');

        $("#verify_div").dialog('open');
        $('.ui-datepicker-div').hide();
    }
    $(function () {
        $("#E1").focus();
        $(".checkAll").live('click',function () {
            $(this).EzCheckAll($(".asnCodes"));
        });

        $('.printReceivingBtn').live('click',function(){
			var code = $(this).attr('receiving_code');
			var tip = '';
			$('<div title="Print"><p align="">' + tip + '</p></div>').dialog({
		        autoOpen: true,
		        width: 600,
		        maxHeight:500,
		        modal: true,
		        closeOnEscape:false,
		        position:'top',
		        show: "slide",
		        buttons: [		            
		            {
		                text: '页面打印',
		                click: function () {
		                	if($('.print_checkbox:checked').size()==0){
			                    alert('请选择入库清单/唛头箱号/产品条码');
			                    return;
			                }
			                //入库单
			                if($('.print_list').is(':checked')){
			                	printList(code);
			                }
			                //箱号
		                    var box_no_arr = [];
			                $('.print_box_no:checked').each(function(){
				                var box_no = $(this).val();
			                	box_no_arr.push(box_no);
			                });
			                if(box_no_arr.length>0){
			                	printBox(code,box_no_arr);
			                }
			                //条码
			                var product_label_arr = [];
			                $('.product_label:checked').each(function(){
				                var qty = $(this).val();
				                var product_id = $(this).attr('product_id');
				                product_label_arr.push(product_id+"_"+qty);
			                });
			                if(product_label_arr.length>0){
				                var paper = $('#paper_type').val();				                
			                	printLabel(product_label_arr,paper);
			                }
		                    $(this).dialog("close");
		                }
		            },
		            {
		                text: '控件打印',
		                click: function () {
			                var this_ = $(this);
		                	var paper = $('#paper_type').val();
			                if($('.print_checkbox:checked').size()==0){
			                    alert('请选择入库清单/唛头箱号/产品条码');
			                    return;
			                }
			                //入库单
			                if($('.print_list').is(':checked')){
				                var param = {'session_id':'<?php echo $_smarty_tpl->tpl_vars['session_id']->value;?>
'};
				                param.code = code;
				                param.paper = 'A4';
				                param.lodop = '1';
			                	 $.ajax({
								   type: "POST",
								   async: false,
								   url: "/receiving/receiving/print-list/",
								   data: param,
								   dataType:'script',
								   success: function(js){
									   print_lodop(); 
								   }
								});
			                	//printUrlForPrinter("/receiving/receiving/print-list/session_id/<?php echo $_smarty_tpl->tpl_vars['session_id']->value;?>
/code/" + code,210,290,0,0,'A4');
			                }
			                //箱号
		                    var box_no_arr = [];
			                $('.print_box_no:checked').each(function(){
				                var box_no = $(this).val();
			                	box_no_arr.push(box_no);
			                });

			                //条码
			                var product_label_arr = [];
			                $('.product_label:checked').each(function(){
				                var qty = $(this).val();
				                var product_id = $(this).attr('product_id');
				                product_label_arr.push(product_id+"_"+qty);
			                });

			                this_.dialog("close");
			                //箱号
			                if(box_no_arr.length>0){
				                var param = {'session_id':'<?php echo $_smarty_tpl->tpl_vars['session_id']->value;?>
'};
				                param.code = code;
				                param.paper = 'A4';
				                param.lodop = '1';
				                param.box_no = box_no_arr.join(',');
			                	 $.ajax({
								   type: "POST",
								   async: false,
								   url: "/receiving/receiving/print-box/",
								   data: param,
								   dataType:'script',
								   success: function(js){
									   print_lodop(); 
								   }
								});
			                	//printUrlForPrinter("/receiving/receiving/print-box/session_id/<?php echo $_smarty_tpl->tpl_vars['session_id']->value;?>
/code/" + code+'/box_no/'+box_no_arr.join(','),210,297,0,0,'A4');
			                }
			                //条码
			                if(product_label_arr.length>0){
				                var param = {'session_id':'<?php echo $_smarty_tpl->tpl_vars['session_id']->value;?>
'};				                
				                param.paper = paper;
				                param.lodop = '1';
				                param.param = product_label_arr.join(',');
			                	 $.ajax({
								   type: "POST",
								   async: false,
								   url: "/receiving/receiving/print-label/",
								   data: param,
								   dataType:'script',
								   success: function(js){
									   print_lodop(); 
								   }
								});
			                	//printUrlForPrinter("/receiving/receiving/print-label/session_id/<?php echo $_smarty_tpl->tpl_vars['session_id']->value;?>
/paper/A4/param/"+product_label_arr.join(','),210,297,0,0,'A4');
			                }
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
		            $(this).detach();
		        },
		        open:function(){
			        var this_ = this;
		        	$.ajax({
	                    type: "post",
	                    dataType: "json",
	                    url: '/receiving/receiving/get-box-no',
	                    data: {'code':code},
	                    success: function (json) {
		                    var html = '';
							html+="<label>入库清单:<input type='checkbox' class='print_list print_checkbox' name='code' value='"+code+"'/></label>";
		                    html +='<div>';
		                    html += '唛头箱号:';
							$.each(json.box_arr,function(k,v){
								html+="<label><input type='checkbox' class='print_box_no print_checkbox' name='box_no[]' value='"+v.box_no+"'/>"+v.box_no+"</label>";
							});
							html+="<label><input type='checkbox' class='print_box_noCheckAll'/>全选</label>";
							html+='</div>';
							html +='<div>';
		                    html += '产品条码:';
							$.each(json.product_arr,function(k,v){
								html+="<label><input type='checkbox' class='product_label print_checkbox' name='product_label["+v.product_id+"]' product_id='"+v.product_id+"' value='"+v.rd_receiving_qty+"'/>"+v.product_sku+'*'+v.rd_receiving_qty+"</label>";
							});
							html+="<label><input type='checkbox' class='product_labelCheckAll'/>全选</label>";
							html+='</div>';
							html +='<div>';
		                    html += '条码纸张:';
							html +="<select id='paper_type' class='input_text'>";
							html+="<option value='70x30'>70x30</option>";
							html+="<option value='100x30'>100x30</option>";
							html+="</select>";
							html+='</div>';
		                    $(this_).html(html);
	                    }
	                });
		        }
		    });
        });
        $('.print_box_noCheckAll').live('click',function(){
            var checked = $(this).is(':checked');
			$('.print_box_no').attr('checked',checked);
         });

        $('.print_box_no').live('click',function(){
            var checked = $(this).is(':checked');
            if(!checked){
            	$('.print_box_noCheckAll').attr('checked',false);
            }else{
				if($('.print_box_no:checked').size()==$('.print_box_no').size()){
					$('.print_box_noCheckAll').attr('checked',true);
				}else{
					$('.print_box_noCheckAll').attr('checked',false);
				}
            }			
         });


        $('.product_labelCheckAll').live('click',function(){
            var checked = $(this).is(':checked');
			$('.product_label').attr('checked',checked);
         });

        $('.product_label').live('click',function(){
            var checked = $(this).is(':checked');
            if(!checked){
            	$('.product_labelCheckAll').attr('checked',false);
            }else{
				if($('.product_label:checked').size()==$('.product_label').size()){
					$('.product_labelCheckAll').attr('checked',true);
				}else{
					$('.product_labelCheckAll').attr('checked',false);
				}
            }			
         });
        $(".datepicker").datepicker({ dateFormat: "yy-mm-dd", defaultDate: -1});
        $(".datepickerTo").datepicker({ dateFormat: "yy-mm-dd", defaultDate: +1});
        $(".datepickerdatetime").datetimepicker({ dateFormat: "yy-mm-dd",timeFormat: 'hh:mm:ss', defaultDate: +1});
        $('.ui-dialog-buttonpane').click();
        $('ui-dialog-titlebar').click();
        $("#verify_div").dialog({
            autoOpen: false,
            width: 300,
            maxHeight: 500,
            modal: true,
            show: "slide",
            buttons: [
                {
                    text: '提交',
                    click: function () {
                        var this_ = $(this);
                        var param = $("#verify_form").serialize();
                        if ($.trim($("#expected").val()) == '') {
                            alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
eta_date_can_not_empty<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
                            return false;
                        }
                        /*if (!window.confirm('确认领用单' + $("#selectCuCode").text() + '归还数量 ' + $("#cu_qty").val() + "个?")) {
                            return false;
                        }*/

                    	$('.ui-button',$(this).parent()).hide();
                    	$(this).dialog('option','closeOnEscape',false);  
                    	$('#verify_loading').html('Loading...');
                    	var ts_ = this;
                        $.ajax({
                            type: "POST",
                            url: "/receiving/receiving/verify",
                            data: param,
                            dataType: 'json',
                            success: function (json) {
                            	$('#verify_loading').html('');
                            	$('.ui-button',$(ts_).parent()).show();
                            	$(ts_).dialog('option','closeOnEscape',true);  
                                if (json.ask) {
                                    this_.dialog("close");
                                    initData(paginationCurrentPage-1);
                                }
                                alertTip(json.message);
                            }
                        });
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
			$("#E10").val(re_status);
			$(".statusTag").removeClass("chooseTag");
			$(".indexTag" + re_status).addClass("chooseTag");
			$('.opration_area_wrap .opDiv').html('');
			$(asnStatusActionJson[re_status]).each(function(k,v){
			    $('.opration_area_wrap .opDiv').append(v);
			})
			$('.submitToSearch:visible').click();
        });

    });
    /**
     * 处理默认查询时的状态选中
     */
    function onDefualtStatus(){
	    var status_chooseTag = $(".chooseTag");
		if(status_chooseTag.size() == 0){
			$(".statusTag").eq(0).addClass("chooseTag");
		}
    }
     
    function displaytime(){
       $(".datepickerdatetime").datepicker({ dateFormat: "yy-mm-dd", defaultDate: +1});
    }
    function forceComplete() {
        var html = '';
        if ($(".asnCodes:checked").size() < 1) {
            alertTip('<span class="tip-error-message">请选择入库单号</span>');
            return false;
        }
        html = '<span class="tip-warning-message">是否确认不再来货?</span>';
        html += '<span class="clear"><br>备注：<br></span>';
        html += '<span class="clear"><textarea name="reason" rows="2" cols="50" id="reason"></textarea></span>';
        html += '<span class="clear" id="validateReasonTips" style="color:red;"></span>';
        alertTip(html);
        $('#dialog-auto-alert-tip').dialog('option',
                'buttons',
                {
                    "确认(Ok)": function () {
                        if($.trim($("#reason").val())==''){
                            $("#validateReasonTips").html('<span class="tip-error-message">请填写备注</span>');
                            return;
                        }
                        confirmReceived();
                        $(this).dialog("close");
                    }, "取消(Cancel)": function () {
                    $(this).dialog("close");
                }
                });
    }

    function confirmReceived() {
        var html = '';
        $.ajax({
            type: "post",
            async: false,
            dataType: "json",
            url: "/receiving/receiving/confirm-received",
            data: $("#receivedForm").serialize()+'&reason='+$("#reason").val(),
            success: function (json) {
                html = '';
                if (isJson(json)) {
                    if (json.state == '1') {
                        $(".checkAll").attr('checked', false);
                        $(".asnCodes").attr('checked', false);
                        loadData(paginationCurrentPage, paginationPageSize);
                        var aClass = 'tip-success-message';
                        $.each(json.data, function (key, val) {
                            aClass = val.state == '1' ? 'tip-success-message' : 'tip-error-message';
                            html += '<span class="' + aClass + '">单号：' + val.asnCode + ' ' + val.message + '</span>';
                        });
                    } else {
                        html = '<span class="tip-error-message">' + json.message + '</span>';
                    }
                    alertTip(html,450);
                    return;
                }
                alertTip('<span>error</span>');
            }
        });
    }

    function searchFilterAttr(inputname, val, obj) {
        if (inputname != 'E10') {
            return;
        }
        var obj = $("#operate-received");
        if ( val == '0' || val == '7') {
            obj.hide();
            return;
        }
        obj.show();
        if (val == '6') {
            $("#operate-received-button").val('收货完成');
        } else if (val == '') {
            $("#operate-received-button").val('收货完成');
        } else {
            $("#operate-received-button").val('已移至删除');
        }
    }
    //导出
    function exportExcel(){
        if ($(".asnCodes:checked").size() < 1) {
            alertTip('<span class="tip-error-message"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pls_select_receiving_code<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>');
            return false;
        }
        var asnCodesString='';
        $(".asnCodes:checked").each(function(k,v){
            asnCodesString+=$(this).val()+',';
        });
        var form = $("<form>").attr('style','display:none').attr("target",'').attr("method","post").attr("action",'/receiving/receiving/export');
        var input = $('<input>').attr('type','hidden').attr('name','asnCodes').attr('value',asnCodesString);
        $('body').append(form);
        form.append(input);
        form.submit();
    }

    $(function(){
        $('.asnDiscardBtn').live('click',function(){
        	if ($(".asnCodes:checked").size() < 1) {
                alertTip('<span class="tip-error-message"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pls_select_receiving_code<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>');
                return false;
            }
        	jConfirm('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
are_you_sure<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
asn_discard<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', function(r) {
        		if(r){
                    var codeArr = [];
                    $(".asnCodes:checked").each(function(){
                        var code = $(this).val();
                        codeArr.push(code);
                    })
                    var param = {'code':codeArr};
                	$.ajax({
                        type: "post",
                        dataType: "json",
                        url: "/receiving/receiving/asn-discard",
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
         $('.asnForceFinishBtn').live('click',function(){
        	if ($(".asnCodes:checked").size() < 1) {
                alertTip('<span class="tip-error-message"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pls_select_receiving_code<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>');
                return false;
            }
        	jConfirm('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
are_you_sure_finish_asn<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
asn_force_finish<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', function(r) {
		        if(r){
		        	var codeArr = [];
		            $(".asnCodes:checked").each(function(){
		                var code = $(this).val();
		                codeArr.push(code);
		            })
		            var param = {'code':codeArr};
		        	$.ajax({
		                type: "post",
		                dataType: "json",
		                url: "/receiving/receiving/asn-force-finish",
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

        $("#verify_batch_div").dialog({
            autoOpen: false,
            //closeOnEscape:false,
            width: 500,
            maxHeight: 500,
            modal: true,
            show: "slide",
            buttons: [
                {
                    text: '提交',
                    click: function () {
                        var this_ = $(this);
                    	this_.dialog('option','closeOnEscape',false);  
                        $('.ui-button',this_.parent()).hide();
                    	this_.dialog('option','title','审核中...');  
                        var param = $('#verify_batch_form').serialize();
                        loadStart();
                    	$.ajax({
                            type: "post",
                            dataType: "json",
                            url: "/receiving/receiving/asn-verify",
                            data: param,
                            success: function (json) {
                            	loadEnd('');
                            	this_.dialog('option','closeOnEscape',true);  
                            	this_.dialog('option','title','审核完成');  
                                $('.ui-button',this_.parent()).show();
                            	this_.dialog("close");
                                var html = '';
                                $.each(json,function(k,v){
                                    html+='<p>'+v.receiving_code+':'+v.message+'</p>';
                                })
                                alertTip(html,800);
                                initData(paginationCurrentPage-1);
                            }
                        });
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

            },
            open:function(){
                var this_ = $(this);
            	this_.dialog('option','title','审核');  
            }
        });
        
        $('.asnVerifyBtn').live('click',function(){
        	if ($(".asnCodes:checked").size() < 1) {
                alertTip('<span class="tip-error-message"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pls_select_receiving_code<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</span>');
                return false;
            }        	
            var codeArr = [];
            $(".asnCodes:checked").each(function(){
                var code = $(this).val();
                codeArr.push(code);
            });
            var html = '';
            $('.asnCodes:checked').each(function(){
                var code = $(this).val();
                var eta = $(this).attr('eta');
            	html+='<tr>';
            	html+='<td>';
            	html+=''+code;
            	html+='</td>';
            	html+='<td>';
            	html+='<input name="code_eta['+code+']" value="'+eta+'" code="'+code+'" class="code_eta datepickerVerify input_text"/>';
            	html+='</td>';
            	html+='</tr>';
            });
            $('#verify_batch_wrap').html(html);
            $('.datepickerVerify').live('focus',function(){
            	$(this).datepicker({ dateFormat: "yy-mm-dd", defaultDate: -1});
            })
        	
            //$(".datepickerVerify").datepicker();
            $("#verify_batch_div").dialog('open');
            
        })
    })
</script>
<div id='verify_div' title='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
verify<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
' style="height: 300px; margin-top: 10px; line-height: 10px; display: none">
	<form action="" id='verify_form'>
		<table style="margin-bottom: 20px; height: 100px;">
			<tr>
				<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
receiving_code<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
				<td>
					<input name="selecteta_data" id="selecteta_data" value='' readonly="">
					<input type='hidden' id='selectRecId' value='' name='rec_id' />
				</td>
			</tr>
			<tr>
				<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
eta_date<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</td>
				<td>
					<input type='text' id='expected' name='expected' class="datepicker input_text" />
				</td>
			</tr>
		</table>
	</form>
	<div id='verify_loading'></div>
</div>

<div id='verify_batch_div' title='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
verify<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
' style="height: 300px; margin-top: 10px; line-height: 10px; display: none">
	<form action="" id='verify_batch_form' onsubmit='return false;'>
		<input style='height:1px;overflow:hidden;border:0 none;width:100%;' type='text'>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
			<tr class="table-module-title">
				<td width='50%'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
receiving_code<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				<td>
					<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
eta_date<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

				</td>
			</tr>
			<tbody id='verify_batch_wrap'>
			
			</tbody>
		</table>
	</form>
	<div id='verify_loading'></div>
</div>
<div id="module-container">
	<div id="search-module">
		<form id="searchForm" name="searchForm" class="submitReturnFalse">
			<div class="search-module-condition">
				<span class="searchFilterText"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Code<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</span>
				<input type="text" name="common_code" id="common_code" class="input_text keyToSearch"  placeholder=" <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
ASNCode<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 or <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
reference<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"/>
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
SKU<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</span>
				<input type="text" name="Sku" id="Sku" class="input_text keyToSearch" style="text-transform: uppercase;"/>
			</div>
			<div class="search-module-condition" style="display: none;">
				<span class="searchFilterText"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
ASNCode<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</span>
				<input type="text" name="E1" id="E1" class="input_text keyToSearch" />
			</div>
			<div class="search-module-condition" style="display: none;">
				<span class="searchFilterText"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
reference<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</span>
				<input type="text" name="E2" id="E2" class="input_text keyToSearch" />
			</div>
			<!-- 隐藏值区域 -->
			<input type="hidden" name="E10" id="E10" value="">
			<div id="search-module-baseSearch">
				<div class="search-module-condition">
					<span class="searchFilterText"></span>
					<input type="button" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
search<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="baseBtn submitToSearch" />
					&nbsp;
					<a href="javascript:void(0)" class="toAdvancedSearch" onclick="toAdvancedSearch()"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
showAdvancedSearch<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
				</div>
			</div>
			<div id="search-module-advancedSearch">
				<div class="search-module-condition">
					<span class="searchFilterText"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
target_warehouse<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</span>
					<?php $_smarty_tpl->smarty->_tag_stack[] = array('ec', array('name'=>'E16','default'=>'Y','search'=>'Y','class'=>'selectCss2 input_select')); $_block_repeat=true; echo smarty_block_ec(array('name'=>'E16','default'=>'Y','search'=>'Y','class'=>'selectCss2 input_select'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
userWarehouse<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_ec(array('name'=>'E16','default'=>'Y','search'=>'Y','class'=>'selectCss2 input_select'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

				</div>
				<div class="search-module-condition">
					<span class="searchFilterText"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
transit_warehouse<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</span>
					<?php $_smarty_tpl->smarty->_tag_stack[] = array('ec', array('name'=>'E3','default'=>'Y','search'=>'Y','class'=>'selectCss2 input_select')); $_block_repeat=true; echo smarty_block_ec(array('name'=>'E3','default'=>'Y','search'=>'Y','class'=>'selectCss2 input_select'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
userWarehouse<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_ec(array('name'=>'E3','default'=>'Y','search'=>'Y','class'=>'selectCss2 input_select'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

				</div>
				<div class="search-module-condition">
					<span class="searchFilterText"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
tracking_no<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</span>
					<input type="text" name="E19" id="E19" placeholder="支持模糊搜索" class="input_text keyToSearch" />
				</div>
				<div class="search-module-condition">
					<span class="searchFilterText"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
createDate<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</span>
					<input type="text" name="dateFor" id="dateFor" class="datepicker input_text keyToSearch" />
					~
					<input type="text" name="dateTo" id="dateTo" class="datepickerTo input_text keyToSearch" />
				</div>
				<div class="search-module-condition">
					<span class="searchFilterText"></span>
					<input type="button" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
search<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="baseBtn submitToSearch" />
					&nbsp;
					<a href="javascript:void(0)" class="toAdvancedSearch" onclick="toBaseSearch()"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
hideAdvancedSearch<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
				</div>
			</div>
		</form>
	</div>
	<div id="fix_header_content" style="z-index: 9;">
		<div class="clone">
			<div class="Tab">
				<ul>
				    <li class="">
						<a  data-id="" status="" class="indexTag statusTag" href="javascript:void (0)">
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
				</ul>
				<input style="background-color: orangered; margin-right: 12px;" type="button" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
export<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="baseBtn baseExport" onclick="exportExcel();" />
			</div>
			<div class="opration_area_wrap" style=" margin-left: 10px;">
				<div class="opDiv"></div>
			</div>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table-module">
				<tbody>
					<tr class="table-module-title">
						<td width="3%" class="ec-center">
							<input type="checkbox" class="checkAll" value="">
						</td>
						<td width="3%" class="ec-center">NO.</td>
						<td width="188"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
ASNCode<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
/<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
reference<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width="180"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
warehouse<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width="120"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
income_type<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width="74"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
sku_species<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width="70"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
sku_total<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width="70"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
status<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
note<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width='180'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
date<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td width="80"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
operate<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
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
				<tr class="table-module-title" id="hide_first_title">
					<td width="3%" class="ec-center"></td>
					<td width="3%" class="ec-center"></td>
					<td width="188"></td>
					<td width="180"></td>
					<td width="120"></td>
					<td width="74"></td>
					<td width="70"></td>
					<td width="70"></td>
					<td></td>
					<td width='180'></td>
					<td width="80"></td>
				</tr>
				<tbody id="table-module-list-data"></tbody>
			</table>
		</form>
	</div>
	<div class="pagination"></div>
</div>
<style>
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
</style>
<object id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0>
	<embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0 pluginspage="install_lodop32.exe"></embed>
</object>
<?php }} ?>