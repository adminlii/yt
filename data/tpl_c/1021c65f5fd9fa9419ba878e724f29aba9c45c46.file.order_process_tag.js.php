<?php /* Smarty version Smarty-3.1.13, created on 2016-04-25 15:32:02
         compiled from "F:\yt20160425\application\modules\order\js\order\order_process_tag.js" */ ?>
<?php /*%%SmartyHeaderCode:16983571dc7f275f399-05613753%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1021c65f5fd9fa9419ba878e724f29aba9c45c46' => 
    array (
      0 => 'F:\\yt20160425\\application\\modules\\order\\js\\order\\order_process_tag.js',
      1 => 1457516431,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '16983571dc7f275f399-05613753',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_571dc7f2763218_89091689',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_571dc7f2763218_89091689')) {function content_571dc7f2763218_89091689($_smarty_tpl) {?>/**
 * 控制订单状态下的特殊标记标签 / 悬浮“过滤条件”窗口
 * @author Frank
 * @date 2013-12-3 19:40:24
 */
//需要隐藏的操作按钮
var _OperateBtHide = new Array();
/**
 * 弹出等等对话框
 */
function alertLoadTip(str){
	var tips = "<span class='tip-load-message'>" + str + "</span>";
	alertTip(tips);
}
/*
 * 标记订单处理等级
 */
function orderProcessAgain(process_again){
	var param = $("#listForm").serialize();
	if ($(".checkItem:checked").size() == 0) {
		alertTip('请选择订单');
		return false;
	}
	alertLoadTip('正在努力的提交数据中,请等待...');
	param += "&process_again=" + process_again;
	$.ajax({
		type : "POST",
		url : "/order/order/orders-Mark",
		data : param,
		dataType : 'json',
		success : function(json) {
			$("#dialog-auto-alert-tip").dialog("close");
			if (json.ask) {
				var html = "<p>" + json.result.message + "</p>";
				alertTip(html, 600);
				initData(paginationCurrentPage - 1);
			} else {
				alertTip(json.message);
			}
		}
	});
};

/**
 * 隐藏指定操作按钮
 */
function hideOperateBt(){
	if(typeof(_OperateBtHide) == 'unfinished'){
		return;
	}
	//隐藏对应的操作啊按钮
	if(_OperateBtHide.length > 0){
		$('.opDiv').find('.baseBtn').show();
		for ( var int = 0; int < _OperateBtHide.length; int++) {
			$('.opDiv').find('.' + _OperateBtHide[int]).hide();
		};
	};
}

/**
 * 设置需要隐藏的按钮
 * @returns
 */
function setHideOpteratBtArr(classNameArr){
	_OperateBtHide = new Array();
	for ( var int = 0; int < classNameArr.length; int++) {
		_OperateBtHide[int] = classNameArr[int];
	};
}

$(function(){
	/*
	 * 标记订单为’已处理‘
	 */
	$('.ordersMark').live('click',function(){
		if ($(".checkItem:checked").size() == 0) {
			alertTip('请选择订单');
			return false;
		}
		if(confirm("是否确认将订单标记为：’已处理’？")){
			orderProcessAgain('2');
		}
		tongji();
	});
	/*
	 * 取消订单标记
	 */
	$('.unOrdersMark').live('click',function(){
		if ($(".checkItem:checked").size() == 0) {
			alertTip('请选择订单');
			return false;
		}
		if(confirm("是否取消订单当前标记？")){
			orderProcessAgain('1');
		}
		tongji();
	});
	/*
     * 控制订单状态的，处理标记显示和隐藏--开始
     */
	$('.order_title_container').click(function(){
		var is_original = $(this).attr('is_original');
		if(is_original == '1'){
			$('#process_again').val('1');
		}else{
			var process_again_id = $(this).attr('data-id');
			$('#process_again').val(process_again_id);
		}
	});
	
	$('.order_status_tag').click(function(){
		//隐藏域赋值
		var process_again_id = $(this).attr('data-id');
		$('#process_again').val(process_again_id);
		

		//更改文本显示内容
		$(this).parent().hide();
		var key = $(this).attr('data-status');
		$('.order_title' + key).html($(this).attr('title'));
		$('.order_title' + key).attr('title',$(this).attr('title'));
		$('.order_title' + key).parent().attr('is_original','0');
		$('.order_title' + key).parent().attr('data-id',process_again_id);
		//显示原始状态
		$('.order_status_text_parent' + key).show();

		//显示和隐藏操作按钮
		setHideOpteratBtArr(['ordersMark']);
	});

	$('.order_status_text').click(function(){
		var process_again_id = $(this).attr('data-id');
		$('#process_again').val(process_again_id);

		//更改文本显示内容
		$(this).parent().hide();
		var key = $(this).attr('data-status');
		$('.order_title' + key).html($(this).attr('title'));
		$('.order_title' + key).attr('title',$(this).attr('title'));
		$('.order_title' + key).parent().attr('is_original','1');
		$('.order_title' + key).parent().attr('data-id',process_again_id);

		//隐藏原始状态
		$('.order_status_text_parent' + key).hide();
		$('.order_status_tag_parent' + key).show();
		//显示和隐藏操作按钮
		setHideOpteratBtArr(['unOrdersMark']);
	});

	$(".statusTag").live({
		mouseout: function() {
			$('.Tab').find('.order_status_tag_container').hide();
		},
		mouseover: function() {
			
			$(this).find('.order_status_tag_container').show();
		}
	});
	
	/*
     * 控制订单状态的，处理标记显示和隐藏--结束
     */
	
	/*
	 * 控制“过滤条件”窗口，显示、隐藏，拖动--开始
	 */
	var _show_more_float_container_handle = $('.table-module-seach-float-handle');
	var _show_more_float_container = $('.table-module-seach-float');
	$("#show_more_float").click(function(){
		_show_more_float_container.animate({top:'0px'},'slow');
	});

	$("#hide_more_float").click(function(e){
		e.stopPropagation();
		_show_more_float_container.animate({top:'-450px'},'slow');
	});
	
	//鼠标拖动的监听
	_show_more_float_container_handle.mousedown(function(e){
		//点击"收起"，鼠标事件return
		var element = e.target;
		if(element.tagName == 'A' && (element.getAttribute("name") == 'hide_more_float' ||  element.getAttribute("class") == 'link_order_source')){
			return;
		}
		loadMasks();
		$(this).css("cursor","move");  
		var disX = e.clientX - _show_more_float_container[0].offsetLeft;
		var disY = e.clientY - _show_more_float_container[0].offsetTop;
		
		$(document).mousemove(function(e){
			xx=e.clientX - disX;
			yy=e.clientY - disY;
			_show_more_float_container.css({"left":xx,"top":yy});
		});
		
		$(document).mouseup(function(){
			closeMasks();
			_show_more_float_container.css("cursor","default");  
			$(document).unbind('mousemove');
			$(document).unbind('mouseup');
		});
		
		return false;
	});
	
	//显示过滤条件的选中项，在“高级搜索”中
	_show_more_float_container.find(".link_option").click(function(){
		var selected_val =  _show_more_float_container.find(".current");
		var content = '';
		selected_val.each(function(i){
			if(!$(this).hasClass('link_option_title')){
				var title_name =  $(this).parent().prev().html();//过滤条件的名称
				var title_vale =  $(this).html();				 //选中的具体类型
				content += "["+ title_name + title_vale +"]";
			}
		});
		$('.float_selected_title').html(content);
	});
	/*
	 * 控制“过滤条件”窗口，显示、隐藏，拖动--解释
	 */
});

/**
 * 加载遮罩  function loadMasks(top){ 
 */
var _masks_i = null;
var _masks_d = null;
function loadMasks(){                
	var height = $(window).height();
	if(height < $("body").height()){
		height = $("body").height();
	}
	_masks_i = $('<iframe></iframe>').appendTo($('body'));			
	_masks_i.css({"display":"block","width":"100%","height":height,"background":"gray","position":"absolute","top":"0","left":"0","z-index":"100","opacity":"0.1"});   //z-index控制层次用的，相当于一个垂直页面的的轴，
	_masks_d = $('<div></div>').appendTo($('body'));
	_masks_d.css({"display":"block","width":"100%","height":height,"background":"gray","position":"absolute","top":"0","left":"0","z-index":"101","opacity":"0.7"});
}
/**
 * 关闭遮罩 
 */
function closeMasks(){
	try{
		_masks_i.remove();
		_masks_d.remove();
		_masks_i = null;
		_masks_d = null;
	}catch (e) {
	}         
}<?php }} ?>