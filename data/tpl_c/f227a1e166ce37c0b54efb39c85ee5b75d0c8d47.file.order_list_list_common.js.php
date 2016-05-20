<?php /* Smarty version Smarty-3.1.13, created on 2016-04-25 15:32:14
         compiled from "F:\yt20160425\application\modules\platform\js\order\order_list_list_common.js" */ ?>
<?php /*%%SmartyHeaderCode:22604571dc7fe91f3e0-74983664%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f227a1e166ce37c0b54efb39c85ee5b75d0c8d47' => 
    array (
      0 => 'F:\\yt20160425\\application\\modules\\platform\\js\\order\\order_list_list_common.js',
      1 => 1457516431,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '22604571dc7fe91f3e0-74983664',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'statusArrJson' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_571dc7fe9752f5_83454064',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_571dc7fe9752f5_83454064')) {function content_571dc7fe9752f5_83454064($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'F:\\yt20160425\\libs\\Smarty\\plugins\\block.t.php';
?>var statusArr = <?php echo $_smarty_tpl->tpl_vars['statusArrJson']->value;?>
;
var warehouseJson = [];
var user_account_arr_json = [];
var warehouseShipping = {};

function tongji(){		
	$('.statusTag').each(function(k,v){
    	$('.count',this).html('(0)');
    })
    var platform='';
    var order_type='';
	$.ajax({
		type: "POST",
//		async: false,
		dataType: "json",
		url: '/platform/order/get-statistics',
		data: {'platform':platform,'order_type':order_type},
		success: function (tongji) {
			var total = 0;
			$.each(tongji,function(k,v){
		    	$('.statusTag'+v.order_status+' .count').html('('+v.count+')');
		    	total+=parseInt(v.count);
		    });
	    	$('.statusTagAll .count').html('('+total+')');
			
		}
	});
	/**/    
}

function tongjiPlatform(){		
	$('.statusTag').each(function(k,v){
    	$('.count',this).html('(0)');
    })
	$.ajax({
		type: "POST",
//		async: false,
		dataType: "json",
		url: '/platform/order/get-statistics-platform',
		data: {},
		success: function (tongji) {
			var total = 0;
			$('.link_platform'+ ' .count').html('(0)');
			$.each(tongji,function(k,v){
		    	$('.platform_'+v.platform+' .count').html('('+v.count+')');
		    	total+=parseInt(v.count);
		    });
	    	$('.platform_ .count').html('('+total+')');
			
		}
	});
	/**/    
}
$(function(){
})
$(function (){
	//
	tongji();
	tongjiPlatform();
	/**
	 * 鼠标悬停在SKU Title上，显示图片
	 */
	var _ImgPreviewMaxWidth = 165;// 预览图片最大宽度
	var _ImgPreviewTimeSort = null;
    $('body').append('<div id="orderPreviewDiv" style="display:none;"></div>');
    $('.imgPreview').live('mousemove',function(e){
    	clearTimeout(_ImgPreviewTimeSort);
		var _this = $(this);
		_ImgPreviewTimeSort = setTimeout(function(){
			var off = _this.offset();
	    	var x = off.left+20;
	    	var y = off.top + 20;
	    	var tempIMG = new Image();
	        var img = '<img src="'+_this.attr('url')+'" width="'+_ImgPreviewMaxWidth+'"/>';
	        $('#orderPreviewDiv:hidden').html(img).css({'position':'absolute','top':y+'px','left':x+'px','border':'1px solid #ccc'}).slideDown(100);
        },250);
    	
    }).live('mouseout',function(e){
    	clearTimeout(_ImgPreviewTimeSort);
    	$('#orderPreviewDiv').html("");
    	$('#orderPreviewDiv').hide();
    });
	
    
    
    //移动鼠标时，图片也需要隐藏
    $(window).scroll(function(){
    	clearTimeout(_ImgPreviewTimeSort);
    	$('#orderPreviewDiv:visible').html("");
    	$('#orderPreviewDiv:visible').hide();
    });
})


/**
 * 监听显示、隐藏多余sku的事件
 */
$(".product_data_hide").live("click",function(){
	var data_show = $(this).attr('data_show');
	if(parseInt(data_show)==1){
		$(this).attr('data_show','0');
		$(this).parent().siblings('.more_than_x').hide();
		$('.tips_title',this).text('显示过多的');
	}else{
		$(this).attr('data_show','1');
		$(this).parent().siblings('.more_than_x').show();
		$('.tips_title',this).text('隐藏过多的');
		
	}
});

/**
 * 设置订单条数
 */
function setPageTotal(total){
	var pageTotalHtml="<span>当前共</span>";
	pageTotalHtml+="<b style='color:red;font-weight:bold;padding:0 2px;'>";
	pageTotalHtml+=total;
	pageTotalHtml+="</b>条订单"+'，已选择了<span class="checked_count">0</span>条';
	
	$(".pageTotal").html(pageTotalHtml);
};
	
$(function(){
	$('.ellipsis').live('mouseover',function(){
		var content = $(this).text();
		if($(this).hasClass('order_no_copy')){
			content += " (在“订单”上双击可复制单号)";
		}
		$(this).attr('title',content);
	});
	
	$('#loading').click(function(evt) {
		evt.preventDefault();
		$(this).overlay({
			effect: 'fade',
			opacity: 0.5,
			closeOnClick: true,
			onShow: function() {
				
			},
			onHide: function() {
				
			},
		})
	});
	
	$('.submitToSearch').click(function(){
		//每次查询请，清空订单条数
		setPageTotal(0);
	});
});

/**
 * 状态点击处理
 * @param obj
 * @returns
 */
function statusBtnClick(obj){
	var status = obj.attr('status');
	$(".statusTag").removeClass('chooseTag');
	if(status==''){
		$(".statusTagAll").addClass('chooseTag');			
	}else{
		$(".statusTag"+status).addClass('chooseTag');			
	}
	var opHtml = '';
	if(status!='2'){
		$("#can_merge").val('');
		$('.merge_filter').removeClass('current');
	}		
	if(statusArr[status]){
		$.each(statusArr[status]['actions'],function(k,v){
			opHtml += v;
		})
	}	
	opHtml+='<div style="height:1px;overflow:hidden;clear:both;"></div>';
	$(".opDiv").html(opHtml);
	$('#status').val(obj.attr('status'));	
	$('.submitToSearch:visible').click();
	
	//控制按钮隐藏和显示
	//hideOperateBt();
	// loadData(1, paginationPageSize);
}
/**
 * 发件人信息
 * @returns
 */
function getSubmiter() {
	var param = {};
	param.order_id = '';
	var tip_id = 'ttttttt';
	////loadStart(tip_id);
	$.ajax({
		type : "POST",
		url : "/order/order/get-submiter",
		data : param,
		dataType : 'html',
		success : function(html) {
			////loadEnd('',tip_id);
			var obj = $(html);
			$('.shipper_account',obj).attr('name','shipper_account');
			$('#submiter_wrap').html(obj);
		}
	});
}

/**
 * 运输方式
 * @returns
 */
function getProduct() {
	var param = {};
	$.ajax({
		type : "POST",
		url : "/order/order/get-product",
		data : param,
		dataType : 'json',
		success : function(json) {
			var html = '';
			html +="<option value='' class='ALL'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
-select-<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</option>";
			$.each(json,function(k,v){
				html +="<option value='"+v.product_code+"' class='"+v.product_code+"'>"+v.product_code+" ["+v.product_cnname+"  "+v.product_enname+"]</option>";
			})
			$('#product_code').html(html);			
		}
	});
}
$(function() {	

	$("#module-container .statusTag").live('click', function() {
		var obj = $(this);		
		statusBtnClick(obj);
	});
	//发件人
	getSubmiter();
	//运输方式
	getProduct();

	function verify(type){
		if(!type){
			type = 'D';
		}
		var param = '';
		$('.checkItem:checked').each(function(){
			var ref_id = $(this).attr('ref_id');
			param+="&ref_id[]="+ref_id;
		})
		var varify_param = $('#verify_div_form').serialize();
		$.ajax({
			type : "POST",
			url : "/platform/order-op/verify?type="+type,
			data : varify_param+'&'+param,
			dataType : 'json',
			success : function(json) {
				if(!isJson(json)) {
					alertTip("系统错误");
				}
				
				var html = '';
				html+='<p>'+json.message+',处理结果如下:'+'</p>';
				html+='<div>';
				html+='<ul style="padding-left:20px;list-style-type:decimal;">';
				
				$.each(json.results,function(k,v){
					html+='<li>';
					var ref_id = v.ref_id;
					var msg = v.message;
					html+="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
平台单号<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:"+ref_id+"<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
处理结果<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:"+msg;
					if(v.err&&v.err.length>0){
						html+=",<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
不合法信息<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
";
						$.each(v.err,function(k,v){
							html+='<p>'+v+'</p>';							
						});
					}
					if(v.rs&&v.rs.err){
						$.each(v.rs.err,function(k,v){
							html+='<p>'+v+'</p>';							
						});
					}
					if(v.rs&&v.rs.message){
						//html+='<p>'+v.rs.message+'</p>';
					}	
					html+='</li>';						
				});
				html+='</ul>';
				html+='</div>';
				initData(paginationCurrentPage-1);
				alertTip(html,800,400);
				tongji();
			}
		});	
		
	}
	/**
	 * 订单审核
	 */
	$("#verify_div_verify").dialog({
		autoOpen : false,
		width : 920,
		maxHeight : 480,
		modal : true,
		show : "slide",
		close : function() {

		},
		open : function() {
//			//发件人
//			getSubmiter();
//			//运输方式
//			getProduct();
		},
		buttons : [{
			text : '生成草稿订单',
			click : function() {
				var this_ = $(this);
				
				if($('#product_code').val()==''){
					//alertTip("<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
运输方式不可为空<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
");
					//return;
				}
				if($('.shipper_account:checked').size()==0){
					alertTip("<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
发件人信息 不可为空<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
");
					return;
				}
				this_.dialog('close');
				verify('D');
							
			}
		},{
			text : '提交预报',
			click : function() {
				var this_ = $(this);
				
				if($('#product_code').val()==''){
					alertTip("<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
运输方式不可为空<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
");
					return;
				}
				if($('.shipper_account:checked').size()==0){
					alertTip("<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
发件人信息 不可为空<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
");
					return;
				}
				this_.dialog('close');
				verify('P');
							
			}
		}, {
			text : '关闭(Close)',
			click : function() {
				$(this).dialog("close");
			}
		}]
	});
	/**
	 * 订单审核按钮
	 */
	$('.orderVerify').live('click', function() {
		if ($(".checkItem:checked").size() == 0) {
			alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pls_select_orders<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
			return false;
		}
		$('#verify_div_verify').dialog('open');
	});
	/**
	 * 暂存
	 */
	$('.holdBtn').live('click', function() {
		if ($(".checkItem:checked").size() == 0) {
			alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pls_select_orders<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
			return false;
		}
		var param = '';
		$('.checkItem:checked').each(function(){
			var ref_id = $(this).attr('ref_id');
			param+="&ref_id[]="+ref_id;
		})
		jConfirm('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
are_you_sure<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
将选择的订单暂存<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', function(r) {
    		if(r){
    			$.ajax({
    				type : "POST",
    				url : "/platform/order-op/hold",
    				data : param,
    				dataType : 'json',
    				success : function(json) {
    					var html = '';
    					html+='<p>'+json.message+',处理结果如下:'+'</p>';
    					html+='<div>';
    					html+='<ul style="padding-left:20px;list-style-type:decimal;">';
    					
    					$.each(json.results,function(k,v){
    						html+='<li>';
    						var ref_id = v.ref_id;
    						var msg = v.message;
    						html+="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
平台单号<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:"+ref_id+"<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
处理结果<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:"+msg;
    						if(v.err&&v.err.length>0){
    							html+="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
不合法信息<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
";
    							$.each(v.err,function(k,v){
    								html+='<p>'+v+'</p>';							
    							});
    						}
    						if(v.rs&&v.rs.err){
    							$.each(v.rs.err,function(k,v){
    								html+='<p>'+v+'</p>';							
    							});
    						}
    						if(v.rs&&v.rs.message){
    							//html+='<p>'+v.rs.message+'</p>';
    						}	
    						html+='</li>';						
    					});
    					html+='</ul>';
    					html+='</div>';
    					initData(paginationCurrentPage-1);
    					alertTip(html,800,400);
    					tongji();
    				}
    			});	
    		}
    	});
		
	});
	/**
	 * 转草稿
	 */
	$('.draftBtn').live('click', function() {
		if ($(".checkItem:checked").size() == 0) {
			alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pls_select_orders<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
			return false;
		}
		var param = '';
		$('.checkItem:checked').each(function(){
			var ref_id = $(this).attr('ref_id');
			param+="&ref_id[]="+ref_id;
		})
		var varify_param = $('#verify_div_form').serialize();
		$.ajax({
			type : "POST",
			url : "/platform/order-op/hold",
			data : param,
			dataType : 'json',
			success : function(json) {
				var html = '';
				html+='<p>'+json.message+',处理结果如下:'+'</p>';
				html+='<div>';
				html+='<ul style="padding-left:20px;list-style-type:decimal;">';
				
				$.each(json.results,function(k,v){
					html+='<li>';
					var ref_id = v.ref_id;
					var msg = v.message;
					html+="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
平台单号<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:"+ref_id+"<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
处理结果<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:"+msg;
					if(v.err&&v.err.length>0){
						html+="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
不合法信息<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
";
						$.each(v.err,function(k,v){
							html+='<p>'+v+'</p>';							
						});
					}
					if(v.rs&&v.rs.err){
						$.each(v.rs.err,function(k,v){
							html+='<p>'+v+'</p>';							
						});
					}
					if(v.rs&&v.rs.message){
						//html+='<p>'+v.rs.message+'</p>';
					}	
					html+='</li>';						
				});
				html+='</ul>';
				html+='</div>';
				initData(paginationCurrentPage-1);
				alertTip(html,800,400);
				tongji();
			}
		});	
	});

	/**
	 * 手工拉单
	 */
	$("#load_order_div").dialog({
		autoOpen : false,
		width : 600,
		maxHeight : 480,
		modal : true,
		show : "slide",
		close : function() {

		},
		open : function() {
//			//发件人
//			getSubmiter();
//			//运输方式
//			getProduct();
		},
		buttons : [{
			text : '确定(Ok)',
			click : function() {
				var this_ = $(this);
				var user_account = $("#load_order_form select[name='user_account']").val();
				if(user_account==''){
					alertTip("<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
账号不可为空<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
");
					return;
				}
				this_.dialog("close");
				alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
数据拉取中<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
...');
				var param = $('#load_order_form').serialize();
				$.ajax({
					type : "POST",
					url : "/platform/order-op/load-platform-order",
					data : param,
					dataType : 'json', 
					success : function(json) {
						$("#dialog-auto-alert-tip").dialog("close");
						var html = "<h3>"+json.message+"</h3>";
						if(json.ask){							
							initData(paginationCurrentPage-1);	
							tongji();
							tongjiPlatform();
							switch(json.platform){
								case 'EBAY':
									html+="<ul class='load_order_list_msg'>";
									$.each(json.load_platform_order_rs.msgArr,function(k,v){
										html+="<li>"+v+"</li>";
									})									
									html+="</ul>";	
									break;
								case 'ALIEXPRESS':									
									html+="<ul class='load_order_list_msg'>";
									$.each(json.load_platform_order_rs.msgArr,function(k,v){
										html+="<li>"+v+"</li>";
									})									
									html+="</ul>";					
									break;

								case 'AMAZON':
									html+="<ul class='load_order_list_msg'>";
									$.each(json.load_platform_order_rs.msgArr,function(k,v){
										html+="<li>"+v+"</li>";
									})									
									html+="</ul>";	
									break;
								case 'MABANG':
									html+="<ul class='load_order_list_msg'>";
									$.each(json.load_platform_order_rs.msgArr,function(k,v){
										html+="<li>"+v+"</li>";
									})									
									html+="</ul>";	
									break;	
							}
						}

						alertTip(html,800,400);	
						
					
					}
				});	
							
			}
		}, {
			text : '关闭(Close)',
			click : function() {
				$(this).dialog("close");
			}
		}]
	});
	$('.getOrderBtn').live('click', function() {
		$("#load_order_div").dialog('open');
	});
	/**
	 * 标记发货
	 */
	$("#biaojifahuo_div").dialog({
		autoOpen : false,
		width : 920,
		maxHeight : 480,
		modal : true,
		show : "slide",
		close : function() {

		},
		open : function() {
		},
		buttons : [
//           {
//			text : '加入任务',
//			click : function() {
//				var this_ = $(this);				
//				this_.dialog('close');
//
//				var param = $('#biaojifahuo_form').serialize();
//				$.ajax({
//					type : "POST",
//					url : "/platform/order-op/ship-mark",
//					data : param,
//					dataType : 'json',
//					success : function(json) {
//
//						var html = '';
//						html+='<p>'+json.message+',处理结果如下:'+'</p>';
//						html+='<div>';
//						html+='<ul style="padding-left:20px;list-style-type:decimal;">';
//						
//						$.each(json.results,function(k,v){
//							html+='<li>';
//							var ref_id = v.ref_id;
//							var msg = v.message;
//							html+="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
平台单号<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:"+ref_id+"<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
处理结果<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:"+msg;
//							if(v.err&&v.err.length>0){
//								html+="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
不合法信息<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
";
//								$.each(v.err,function(k,v){
//									html+='<p>'+v+'</p>';							
//								});
//							}
//							if(v.rs&&v.rs.err){
//								$.each(v.rs.err,function(k,v){
//									html+='<p>'+v+'</p>';							
//								});
//							}
//							if(v.rs&&v.rs.message){
//								//html+='<p>'+v.rs.message+'</p>';
//							}	
//							html+='</li>';						
//						});
//						html+='</ul>';
//						html+='</div>';
//						alertTip(html,800,400);
//					
//					}
//				});			
//			}
//		},
		{
			text : '立刻标记',
			click : function() {
				var this_ = $(this);				
				this_.dialog('close');
				alertTip('处理中,请稍候...');
				var param = $('#biaojifahuo_form').serialize();
				$.ajax({
					type : "POST",
					url : "/platform/order-op/complete-sale",
					data : param,
					dataType : 'json',
					success : function(json) {
						$("#dialog-auto-alert-tip").dialog("close");
						var html = '';
						if(json.ask){
							html+='<p>'+json.message+',处理结果如下:'+'</p>';
							html+='<div>';
							html+='<ul style="padding-left:20px;list-style-type:decimal;">';
							
							$.each(json.results1,function(k,v){
								html+='<li>';
								var ref_id = v.ref_id;
								var msg = v.message;
								html+="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
平台单号<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:"+ref_id+"<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
处理结果<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:"+msg;
								if(v.err&&v.err.length>0){
									html+="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
不合法信息<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
";
									$.each(v.err,function(k,v){
										html+='<p>'+v+'</p>';							
									});
								}
								if(v.rs&&v.rs.err){
									$.each(v.rs.err,function(k,v){
										html+='<p>'+v+'</p>';							
									});
								}
								if(v.rs&&v.rs.message){
									//html+='<p>'+v.rs.message+'</p>';
								}	
								html+='</li>';						
							});
							html+='</ul>';
							html+='</div>';
						}else{
							html+='<p>'+json.message+'</p>';
							
						}
						
						alertTip(html,800,400);
					
					}
				});			
			}
		},
		{
			text : '关闭(Close)',
			click : function() {
				$(this).dialog("close");
			}
		}]
	});
	$('.biaojifahuoSelect').live('click', function() {
		if ($(".checkItem:checked").size() == 0) {
			alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pls_select_orders<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
			return false;
		}
		var html = '';
		$(".checkItem:checked").each(function(){
			var ref_id = $(this).attr('ref_id');
			var id = $(this).val();
			var order_id = $(this).val();
			var order = orderArr[id];
			var shipping_method_no = order&&order.shipping_method_no? order.shipping_method_no:'';
			var shipping_method = order&&order.shipping_method? order.shipping_method:'';
			var carrier_name = order&&order.carrier_name? order.carrier_name:'';
			html+='<tr class="table-module-b1">';						
			html+='<td class="ec-center"><input name="order['+ref_id+'][ref_id]" value="'+ref_id+'" type="hidden"/>'+ref_id+'</td>';	
			html+='<td class="ec-center">'+$('#country_'+order_id).text()+'</td>';						
			html+='<td class="ec-center"><input name="order['+ref_id+'][carrier_name]" value="'+carrier_name+'" type="text" class="input_text"/></td>';
			html+='<td class="ec-center"><input name="order['+ref_id+'][shipping_method_no]" value="'+shipping_method_no+'" type="text" class="input_text" readonly/></td>';						
			html+='</tr>';
		})
		$('#order_list_wrap').html(html);
		$("#biaojifahuo_div").dialog('open');
	});

	/**
	 * 标记发货
	 */
	$("#allot_div").dialog({
		autoOpen : false,
		width : 920,
		maxHeight : 480,
		modal : true,
		show : "slide",
		close : function() {

		},
		open : function() {
		},
		buttons : [
		{
			text : '确定(Ok)',
			click : function() {
				var this_ = $(this);				
				this_.dialog('close');
				alertTip('处理中,请稍候...');
				var param = $('#allot_form').serialize();
				$.ajax({
					type : "POST",
					url : "/platform/order-op/allot",
					data : param,
					dataType : 'json',
					success : function(json) {
						$("#dialog-auto-alert-tip").dialog("close");
						var html = '';
						html+='<p>'+json.message+',处理结果如下:'+'</p>';
						html+='<div>';
						html+='<ul style="padding-left:20px;list-style-type:decimal;">';
						
						$.each(json.results,function(k,v){
							html+='<li>';
							var ref_id = v.ref_id;
							var msg = v.message;
							html+="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
平台单号<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:"+ref_id+"<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
处理结果<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:"+msg;
							if(v.err&&v.err.length>0){
								html+="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
不合法信息<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
";
								$.each(v.err,function(k,v){
									html+='<p>'+v+'</p>';							
								});
							}
							if(v.rs&&v.rs.err){
								$.each(v.rs.err,function(k,v){
									html+='<p>'+v+'</p>';							
								});
							}
							if(v.rs&&v.rs.message){
								//html+='<p>'+v.rs.message+'</p>';
							}	
							html+='</li>';						
						});
						html+='</ul>';
						html+='</div>';
						alertTip(html,800,400);
						initData(paginationCurrentPage-1);						
					}
				});			
			}
		},
		{
			text : '关闭(Close)',
			click : function() {
				$(this).dialog("close");
			}
		}]
	});
	$('.allotBtn').live('click', function() {
		if ($(".checkItem:checked").size() == 0) {
			alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
pls_select_orders<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
			return false;
		}
		//运输方式
		//getProduct();
		var html = '';
		$(".checkItem:checked").each(function(){
			var ref_id = $(this).attr('ref_id');
			var order_id =$(this).val();
			var shipping_method = $('#shipping_method_platform_'+order_id).attr('shipping_method');
			html+='<tr class="table-module-b1">';						
			html+='<td class="ec-center"><input name="order['+ref_id+'][ref_id]" value="'+ref_id+'" type="hidden"/>'+ref_id+'</td>';						
			html+='<td class="ec-center">'+$('#country_'+order_id).text()+'</td>';					
			html+='<td class="ec-center">'+$('#shipping_method_platform_'+order_id).attr('shipping_method_platform')+'</td>';
			html+='<td class="ec-center">';
			html+='<select class="input_select " name="order['+ref_id+'][shipping_method]">';
			var clone = $('#product_code').clone();
			clone.removeAttr('id');
			if(shipping_method!=''){
				$('.'+shipping_method,clone).attr('selected',true);
			}
//			//alert(clone.html());
			html+=clone.html();
			html+='</select>';
			html+='</td>';						
			html+='</tr>';
		})
		$('#allot_list_wrap').html(html);
		$("#allot_div").dialog('open');
	});
});
$(function() {
	$(".checkAll").live('click', function() {
		$(".checkItem").attr('checked', $(this).is(':checked'));
		$(".checkAll").attr('checked', $(this).is(':checked'));
		
		var size = $('.checkItem:checked').size();
		$('.checked_count').text(size);
	});
	/**
	 * 选择订单checkbox
	 */
	$('.checkItem').live('click',function(){
		var size = $('.checkItem:checked').size();
		$('.checked_count').text(size);
	});
	
	/**
	 * 订单合并操作按钮
	 */
	$('.orderMerge').live('click',function(){
		if ($(".checkItem:checked").size() <= 1) {
			alertTip('请选择至少两个订单');
			return false;
		}
		//需要验证账号，买家ID，收件人姓名，收件人国家，收件人地址是否一致，否则给出错误提示
		var param = $("#listForm").serialize();
		$.ajax({
			type : "POST",
			url : "/platform/order/order-merge",
			data : param,
			dataType : 'json',
			success : function(json) {
				var html = '';
				html+=json.message;
				alertTip(html, 800);
				if(json.ask){
					loadData(paginationCurrentPage-1,paginationPageSize);
				}
			}
		});
	})
	
	
});
$(function(){
	var dayNamesMin = ['日', '一', '二', '三', '四', '五', '六'];
	var monthNamesShort = ['01月', '02月', '03月', '04月', '05月', '06月', '07月', '08月', '09月', '10月', '11月', '12月'];
	$.timepicker.regional['ru'] = {
		timeText : '选择时间',
		hourText : '小时',
		minuteText : '分钟',
		secondText : '秒',
		millisecText : '毫秒',
		currentText : '当前时间',
		closeText : '确定',
		ampm : false
	};
	$.timepicker.setDefaults($.timepicker.regional['ru']);

	$('#DateFrom,#DateEnd,.datepicker').datetimepicker({
		dayNamesMin : dayNamesMin,
		monthNamesShort : monthNamesShort,
		changeMonth : true,
		changeYear : true,
		dateFormat : 'yy-mm-dd'
	});
})
$(function(){
	/**
	 * 滚动保持头部固定
	 */
//	var clone = $('#fix_header_content .clone').clone();
//	$('#fix_header').append(clone);
//	var clone1 = $('#fix_header_content .table-module-list-header-tpl .table-module-title').clone();
//	$(':checkbox',clone1).remove();
//	$('#table-module-list-header').html(clone1);
//	$(window).scroll(function(){
//		var off = $('#fix_header_content').offset();
//		var top = off.top;
//		var scrollTop = $(this).scrollTop();
//		if(scrollTop>top){
//			$('#fix_header').show().width($('#module-table').width());
//			$('.sub_status_container').hide();
//		}else{
//			$('#fix_header').hide();			
//		}
//		
//	});
//	$(window).resize(function(){
//		$('#fix_header').width($('#module-table').width());	
//	});
	
});

$(function(){

	function autoInit(){
		$('.invoice_code').each(function(){
			var this_ = this;
			var tr = $(this).parent().parent();
			$(this).autocomplete({
				//source: "/product/product/get-by-keyword/limit/20",
				minLength: 2,
				delay:100,
				source:function(request, response) {
					//增加等待图标
					$(this_).addClass("autocomplete-loading");
					$(this_).autocomplete({delay: 100});
					var parameter = {};
					parameter["term"] = $(this_).val();
					$.ajax({
						type :"POST",
						url: "/invoice/invoice/get-by-keyword/limit/20",
						dataType: "json",
						data: parameter,
						success: function(json) {
							//关闭等待图标
							$(this_).removeClass("autocomplete-loading");
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
	        	    $('.invoice_enname',tr).text(ui.item.invoice_enname);
	        	    $('.invoice_unitcharge',tr).text(ui.item.invoice_unitcharge);
	        	    $('.hs_code',tr).text(ui.item.hs_code);
	        	    $('.invoice_note',tr).text(ui.item.invoice_note);
	        	    $('.invoice_url',tr).text(ui.item.invoice_url);
				},
				search: function( event, ui ) {
	        		$('.invoice_text',tr).html('');
				}
				,open: function() {
					$(this).removeClass("ui-corner-all").addClass("ui-corner-top");
				}
				,close: function() {
					$(this).removeClass("ui-corner-top").addClass("ui-corner-all");
				} 
			});
		})
	}

	/**
	 * sku关系映射
	 */
	$("#product_relation_div").dialog({
		autoOpen : false,
		width : 920,
		maxHeight : 480,
		modal : true,
		show : "slide",
		close : function() {
			$(this).html('');
		},
		open : function() {
		},
		buttons : [
		{
			text : '确定(Ok)',
			click : function() {
				var this_ = $(this);	
				alertTip('处理中,请稍候...');
				var param = $('#invoice_form').serialize();
				$.ajax({
					type : "POST",
					url : "/product/product-combine-relation-invoice/update-new",
					data : param,
					dataType : 'json',
					success : function(json) {
						$("#dialog-auto-alert-tip").dialog("close");
						var html = '';
						html+='<p>'+json.message+'</p>';						
						alertTip(html);
						if(json.ask){			
							this_.dialog('close');
							initData(paginationCurrentPage-1);	
						}
					}
				});			
			}
		},
		{
			text : '关闭(Close)',
			click : function() {
				$(this).dialog("close");
			}
		}]
	});
	$('.addInvoiceBtn').live('click',function(){
		var clone = $('#invoice_div .table-module-b1').eq(0).clone();	
		$(':input',clone).val('');
		$('.invoice_text',clone).text('');
		$('#products').append(clone);
		autoInit();
	});
	

	$('.delInvoiceBtn').live('click',function(){
		if($('.delInvoiceBtn').size()<=1){			
			alertTip('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
最后一个申报信息不可删除<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
');
			return;
		}
		var this_ = $(this);
		jConfirm('<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
are_you_sure<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', '<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
删除申报信息<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
', function(r) {
    		if(r){
    			this_.parent().parent().remove();
    		}
    	});       
	});
	
	$('.platform_sku').live('click',function(){
		var user_account = $(this).attr('acc');
		var company_code = $(this).attr('comp');
		var product_sku = $(this).attr('sku');
		$.ajax({
			type: "POST",
//			async: false,
			dataType: "html",
			url: '/product/product-combine-relation-invoice/edit-relation-single',
			data: {'user_account':user_account,'company_code':company_code,'product_sku':product_sku},
			success: function (html) {
				$("#product_relation_div").html(html).dialog('open');
				//alertTip(html,800,400);
				setTimeout(function(){
					autoInit();					
				},200);
				
			}
		});
	});	

})
$(function(){
	/**
	 * 查看订单操作日志
	 */
	$('.logIcon').live('click',function(){
		alertTip($.getMessage('sys_common_wait'));//请等待
		var ref_id = $(this).attr('ref_id');
		$.ajax({
			type: "POST",
			url: "/platform/order/get-order-log",
			data: {'ref_id':ref_id},
			dataType:'json',
			success: function(json){
				$("#dialog-auto-alert-tip").dialog('close');
				var html = '';
				if(json.ask){
					html +='<b>' + $.getMessage('order_code_title')+ ":" +ref_id+'</b><br/> ' + $.getMessage('sys_common_status_desc') + '：<br/>';
					html += '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table-module"><tbody><tr class="table-module-b1">';
					$.each(json.statusArr,function(k,v){
						html+= '<td>' + k +' : '+v.name+'</td>';
					});
					html += '</tr></tbody></table>';
					html += $.getMessage('sys_common_log_information') + ':<br/>';
					//html +='<ul class="log_list" style=" list-style-type: decimal;padding-left: 20px;">';
					html += '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table-module"><tbody>';
					$.each(json.data,function(k,v){
						/*
						html +='<li>';
						html +=v.create_time+' : '+v.log_content;
						//html +=',操作人ID:'+v.op_username;
						html +='</li>';
						*/
						html += (k + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
						html += '<td style="width:70px;">'+v.op_username+'</td>';
						html += '<td style="width:140px;">'+v.create_time+'</td>';
						html += '<td>'+v.log_content+'</td>';
						html += '</tr>';
					});
					//html +='</ul>';
					html += '</tbody></table>';
					alertTip(html,850,480);
				}else{
					var html = json.message;	
					alertTip(html,850,480);
					//alertTip(html);					   
				}
			}
		});
	});
})
$(function(){
	 $('#country').chosen({search_contains:true});
})<?php }} ?>