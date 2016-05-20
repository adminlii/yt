function loadData(page, pageSize) {
	$.ajax({
		type : "POST",
		url : "/message/ebay/history-order",
		data : {
			'messageId' : _messageId
		},
		dataType : 'json',
		success : function(json) {
			var html = '';
			html += json.message + "<br/>";

		}
	});
}
var _templateCookieName = 'templateOperatingData_'+_userId;
var _templateCookieExpires = 60;
var _templateCookiePath = '/';
/**
 * 读取历史的模板信息
 */
function loadTemplateCookie(){
	return $.cookie(_templateCookieName);
}
/**
 * 设置历史模板信息
 */
function setTemplateCookie(data){
	 $.cookie(_templateCookieName,data,{expires:_templateCookieExpires, path: '/'});
}
/**
 * 将字符串转换为json对象
 */
function jsonParse(data){
	return JSON.parse(data);
}
/**
 * 将json数据转为字符串
 */
function stringifyJson(data){
	return JSON.stringify(data);
}
/**
 * 初始化历史模板信息
 */
function initTemplateCookie(){
	//var tmpTemplateData = "{'userId':_userId,'templateList':[{'id':'1','lan':'en','click':'1'},{'id':'2','lan':'ch','click':'2'}]}"; json模板
	var cookieMun = 10;		//存储的模板ID的最大数量
	var MaxCdMun = 50;		//最大点击数量
	var MinCdMun = 0;		//最小点击数量
	var version = "V1.0.1";
	var cookieData = loadTemplateCookie();
	
	//若为空,初始化该cookie信息
	if(!cookieData || jsonParse(cookieData).version != version){
		var tmpList = "";
		var strList1 = '{"id" : "';
		var strList2 = '","lan" : "';
		var strList3 = '","click" : "';
		var strListEnd = '"},';
		for ( var i = 0; i < cookieMun; i++) {
			tmpList += strList1+strList2+strList3+0+strListEnd;
		}
		tmpList = tmpList.substr(0,tmpList.length-1);
		
		var strData1 = '{"version" : "';
		var strData2 = '","userId" : "';
		var strData3 = '","templateList" : [';
		var strDataEnd = ']}';
		var tmpData = strData1 + version + strData2 + _userId + strData3 + tmpList + strDataEnd;
		setTemplateCookie(tmpData);
	}
}
/**
 * 设置模板ID参数至cookie
 */
function setTemplateCookieData(templateId,language){
	var cookieData = loadTemplateCookie();
	//若cookie不存在，初始化
	if(!cookieData){
		initTemplateCookie();
		cookieData = loadTemplateCookie();
	}
	
	var jsonData = jsonParse(cookieData);
	var templateList = jsonData.templateList;
	var existIndex = null;					//保存模板ID在JSON数据中的下标，用于排序		
	//若传入模板ID存在于cookie中，记录它的下标
	for ( var i = 0; i < templateList.length; i++) {
		if(templateList[i].id == templateId){
			existIndex = i;
			break;
		}
	}

	//模板ID不存在与Cookie中,进行进行赋值，然后记录下标
	if(existIndex == null){
		//优先为空白的数据赋值
		for ( var j = 0; j < templateList.length; j++) {
			if(templateList[j].id == ''){	
				templateList[j].id = templateId;
				templateList[j].lan = language;
				templateList[j].click = '1';
				existIndex = j;
				break;
			}
		}

		//无空白数据，将末尾数据替换掉，然后记录下标
		if(existIndex == null){
			existIndex = templateList.length -1;			
			templateList[existIndex].id = templateId;
			templateList[existIndex].lan = language;
			templateList[existIndex].click = '1';
		}
	}

	//进行排序,并保存至cookie
	var tmpData = setTemplateListSequence(templateList,existIndex);
	jsonData.templateList = tmpData;
	var tmpJson = stringifyJson(jsonData);
	setTemplateCookie(tmpJson);
}
/**
 * 按特定规则为模板ID数据集合进行排序
 */
function setTemplateListSequence(templateList,existIndex){
	//记录该下标的值
	var id = templateList[existIndex].id;
	var lan = templateList[existIndex].lan;
	var click = templateList[existIndex].click;

	//从大到小循环，将大于下标数据，全部后移一位
	for ( var k = templateList.length - 1; k > -1; k--) {
		if(k < existIndex){
			templateList[k+1].id = templateList[k].id;
			templateList[k+1].lan = templateList[k].lan;
			templateList[k+1].click = templateList[k].click; 
		}
	}

	//将传入下标的原始值，放置在第一位
	templateList[0].id = id;
	templateList[0].lan = lan;
	templateList[0].click = (click*1)+1; 

	return templateList;
}

function setHistoryMail(){
	var showMaxMun = 6;		//历史模板显示最大数量
	var cookieData = loadTemplateCookie();
	if(cookieData){
		var jsonData = jsonParse(cookieData);
		var templateList = jsonData.templateList;
		if(templateList[0].id == ""){
			return;
		}
		var e = $("#history_mail_div");
		e.empty();
		var ul = $("<ul>").appendTo(e);
		for ( var n = 0; n < showMaxMun; n++) {
			if(templateList[n].id != ""){
				var li = $("<li>").appendTo(ul);
				li.addClass("history_mail");
				li.addClass("showTemplateTitle");
				var a = $("<a>").appendTo(li);
				a.addClass("select_template");
				a.attr("href","javascript:;");
				a.attr("lan",templateList[n].lan);
				a.attr("template_content_id",templateList[n].id);
				var aHtml = $("#mail_menu_div").find("[template_content_id='"+templateList[n].id+"']").html();
				a.html(aHtml);
			}
		}
	}
}

//回复消息使用的模板ID
var _submitTemplateId = "";
$(function() {
	
	initTemplateCookie();
	setHistoryMail();
	
	$(window).keydown(function(e){
		//alert(e.which);
		if(e.ctrlKey && e.shiftKey && e.which == 38) { 
			//alert("ctrl + shift + pageUp"); 
			$("#prev_a").click();
		} else if (e.ctrlKey && e.shiftKey && e.which==40) { 
			//alert("ctrl + shift + pageDown");
			$("#next_a").click(); 
		} 
	});
	
	$(".showTemplateTitle").mouseenter(function(){
		var offset = $(this).offset();
		var aleft = offset.left;
		var atop = offset.top;
		
		$("#dialog-message").html($(this).find("a").html());
		var box = $("#dialog-box");
		var boxWidth = box.css("width");
		boxWidth = parseInt(boxWidth);
		box.css({'left':(aleft - boxWidth -10),'top':atop});
		box.show();
	}).mouseleave(function(){
		$('#dialog-box').hide();
	});
	//parent.reLoadWhenClose = 1;//刷新列表
	$('.select_template').click(function() {
		var template_content_id = $(this).attr('template_content_id');
		var content = templateList[template_content_id]['content'];
		var language = templateList[template_content_id]['language'];
		_submitTemplateId = template_content_id;

		setTemplateCookieData(_submitTemplateId, language);
		var ebayMsgId = $("#messageIds").val();
		alertTip("请等待...");
		var returnTmp = MTO.replaceMessageOperate(content,ebayMsgId);
		if(returnTmp.ask == 0){
			var tmpTips = "";
			var tmpArrErrorMsg = returnTmp.errorMsg;
			for ( var i = 0; i < tmpArrErrorMsg.length; i++) {
				tmpTips += tmpArrErrorMsg[i] + "<br/>";
			}
			$("#dialog-auto-alert-tip").dialog("close");
			alertTip(tmpTips,600);
		}else{
			$("#dialog-auto-alert-tip").dialog("close");
		}
		$('#content').val(returnTmp.message);
		$('#language').val(language);
	});
	
	/*	
	$('#content').xheditor({
		tools : 'Fullscreen,About'
	});
	 * $(".tab").click(function(){ var id = $(this).attr('id');
	 * $('.tabContent').hide(); $('#'+id+'Content').show();
	 * $('.tab').removeClass('chooseTag'); $(this).addClass('chooseTag'); });
	 * $('.tab').eq(0).click();
	 */
	$(".submitToSearch").click(function() {
		var param = $("#contentForm").serialize();
		var content = $('#content').val();
		if($("#msgPrcessStatus").val() == ''){
			alertTip("请选择处理进度！");
			return;
		}
		//检查是否有为替换的操作符
		var arrOperate =  MTO.getOperate(content);
		if(arrOperate.length != 0){
			alertTip("消息中存在未被替换的操作符，请仔细检查");
			return;
		}
		$.ajax({
			type : "POST",
			url : "/message/ebay/feed-back-message",
			data : param,
			dataType : 'json',
			success : function(json) {
				var html = '';
				html += json.message + "<br/>";
				if (json.ask) {
					parent.reLoadWhenClose = 1;
					$("#messageStatus_title").html("已回复未到eBay");
					$("#messageStatus_title").css("color","#0000FF");
					location.reload();
					/*
					$('<div title="操作提示 (Esc)"><p align="">' + json.message + '</p></div>').dialog({
						autoOpen : true,
						width : 500,
						modal : true,
						show : "slide",
						buttons : [{
							text : 'Close',
							click : function() {
								$(this).dialog("close");
							}
						}],
						close : function() {
							//window.location.href = window.location.href;
							$(this).detach();
						}
					});
					*/
				} else {
					alertTip(html, 500);
				}
			}
		});
	});
	$(".feedMessageTag").click(function() {
		if(!window.confirm('确定要将该message标记为已回复？')){
			   return false;
		}
		var param = $("#contentForm").serialize();
		$.ajax({
			type : "POST",
			url : "/message/ebay/feed-back-message-tag",
			data : {'messageId':_messageId},
			dataType : 'json',
			success : function(json) {
				var html = '';
				html += json.message + "<br/>";				
				alertTip(html, 500);
				if(json.ask){
					parent.reLoadWhenClose = 1;//刷新列表
				}
			}
		});
	});
	$(".messageAllot").click(function() {
		$('#allot_div').dialog('open');		
	});

	$('#allot_div').dialog({
        autoOpen: false,
        width: 400,
        maxHeight: 400,
        modal: true,
        show: "slide",
        buttons: [
            {
                text: '分配',
                click: function () {
                	var this_ = $(this);
                	$.ajax({
            			type : "POST",
            			url : "/message/ebay/message-allot",
            			data : {'messageId':_messageId,'customer_service_id':$('#customer_service_id').val()},
            			dataType : 'json',
            			success : function(json) {
            				parent.reLoadWhenClose = 1;//刷新列表
            				var html = '';
            				html += json.message + "<br/>";				
            				alertTip(html, 500);
                        	this_.dialog("close");
            			}
            		});
                }
            },
            {
                text: '关闭',
                click: function () {
                	var this_ = $(this);
                	this_.dialog("close");
                }
            }
        ],
        close: function () {
        }
    });
	// initData(0);
    $('#close').click(function(){
    	$(window.parent.document).find("#iframe-container-overlay").hide();    	
    	$("#iframe-container",parent.document.body).contents().find(".submitToSearch").click();    	
    });

    /**
     *	模板语言过滤
     */
    $('#lang_select').change(function(){
        var div_li_lang = $(".div_li_lang");	//三级栏目
        var dd_div_li = $(".dd_div_li");		//二级栏目
        var dt_dd_div_li = $(".dt_dd_div_li");	//一级栏目
        $('li.lang').show();
        div_li_lang.show();
        dd_div_li.show();
        dt_dd_div_li.show();
        
        if($(this).val()!=''){
        	$('li.lang').hide();
        	var langClass = 'li.'+$(this).val(); 
        	$(langClass).show();//显示匹配该语言的模板title
        	div_li_lang.hide();
        	/*
        	 *	三级模板过滤
          	 */
        	//遍历这些Li标签的父类DIV，循环查找，若该父类DIV标签下没有这些Li标签，隐藏这些父类DIV
        	div_li_lang.each(function(i){
				if($(this).find(langClass).length > 0){
					$(this).show();
					$(this).addClass("div_li_lang_show");
				}else{
					$(this).removeClass("div_li_lang_show");
				}
            });

            /*
             * 二级栏目过滤
             */
        	dd_div_li.each(function(j){
        		var tmpObj = $(this).next(); 
        		var tmpNum = tmpObj.find(".div_li_lang_show").length;
        		if(tmpNum == 0){
        			$(this).hide();
        			$(this).removeClass("dd_div_li_show");
        		}else{
        			$(this).addClass("dd_div_li_show");
        		}
        	});
        	
        	/*
        	 * 三级栏目过滤
        	 */
        	$(".dt_dd_div_li").each(function(k){
        		var tmpObj = $(this).next();
        		var tmpNum = tmpObj.find(".dd_div_li_show").length;
        		if(tmpNum == 0){
        			$(this).hide();
        			$(this).addClass("dd_div_li_hide");
        		}else{
        			$(this).removeClass("dd_div_li_hide");
        		}
        	});
        }
    });
    
    $( "#keyword" ).focus(function(){
    	$( this ).autocomplete({        
        	source: "/message/ebay/get-by-keyword/lang/"+$('#lang_select option:selected').val()+'/limit/20',
        	minLength: 2,
        	delay:100,
        	select: function( event, ui ) {
        		//alert(ui.item.template_content_id);
        		//触发隐藏的选项，这样可以同时触发替换操作符的响应
        		$("a[template_content_id='"+ui.item.template_content_id+"']").click();
//        	    $('#content').val(ui.item.template_content);
//        		$('#language').val(ui.item.language_code);   	    
        	}
    	});
    }).blur(function(){
    	$( this ).unbind('autocomplete');
    });
	
  
	
    $('#item_history_order').click(function(){
    	var orderID = $(this).attr("orderId");
    	openIframeDialog('/order/order/item-history-order-list/buyer_id/'+orderID,1024,600,'客户历史订单');
    });

    /**
     *	ebay消息的表头滚动
    
    //删除window的scroll事件，使用下面的方式处理
    $(window).unbind('scroll');
    //向DIV中追加表头，设置table的属性
	var scrollTable = $("<table>").appendTo(_fixed_side_scrollDiv);
	scrollTable.attr("width","100%");
	scrollTable.attr("border","0");
	scrollTable.attr("cellpadding","0");
	scrollTable.attr("cellspacing","0");
	scrollTable.addClass("table-module");

	//拿取.fixed_side样式中的第一个table，及tr（表头），copy到table中
	var tableTrTitle= _fixed_side.find(".fixed_side_tr");
	var tmpTitle = tableTrTitle.clone();
	scrollTable.append(tmpTitle);
    $("#left").scroll(function () {
    	_fixed_side_scrollDiv.css('width',_fixed_side.css('width'));
    	var scrollTop = $(this).scrollTop();
    	//如果距离顶部的距离小于浏览器滚动的距离，则显示或屏蔽浮动层
    	if (scrollTop - _fixed_side_offset.top > 0) {
    		_fixed_side_scrollDiv.show();
    	}else{
    		_fixed_side_scrollDiv.hide();
    	}
    }); 
     */
	/**  右侧选择模板控制 --开始 mouseenter **/
    $("#mail_menu_down").click(function(){
        if(parseInt($("#mail_menu_div").css("right")) == 0){
        	$("#mail_menu_div").animate({right:'-220px'},'slow');
            $("#mail_menu_down_show").show();
            $("#mail_menu_down_hide").hide();
        }else{
			$("#mail_menu_div").animate({right:'+0px'},'slow');
			$("#mail_menu_down_show").hide();
			$("#mail_menu_down_hide").show();
        }
	});
	/**
	$("#mail_menu_div").mouseleave(function(){
		$("#mail_menu_div").animate({right:'-250px'},'slow');
	});
	**/
	$(document).bind("click",function(e){
		
		var target = $(e.target);
		//当前鼠标点击坐标是否在div展示框内，如果在展示框内则不隐藏
		var inMailMenuDiv = target.closest('div[id^="mail_menu_div"]').length > 0;
		var inMailLangSelect = target.closest('select[id^="lang_select"]').length > 0;
		
		if(!inMailMenuDiv && !inMailLangSelect){
			$("#mail_menu_div").animate({right:'-220px'},'slow');
			$("#mail_menu_down_show").show();
			$("#mail_menu_down_hide").hide();
		}
		
	});
	/**  右侧选择模板控制 --结束  **/

	ebayMessageScrollTop(1);
});
/**
 * 控制ebay消息默认下拉到内容部分
 */
function ebayMessageScrollTop(val){
	var pos=120;
	var obj=document.getElementById("left");
	
	var tmp = val +val;
	var ss = setTimeout("ebayMessageScrollTop("+tmp+")","100");
	if(val > pos){
		val = pos;
		clearTimeout(ss);
	}
	obj.scrollTop = val;
	
}
function showOrderDetail(orderId){
	openIframeDialog("/order/order/detail/view/1/orderId/"+orderId,1000,600,'订单详情');
}

function showSubMail(obj){

    $(obj).children("p").children("a").addClass("submail_ahover");
    $(obj).children("div").show();

}

function closeSubMail(obj,id){
    //  alert(document.getElementById("menuicon1").style.display);
    $(obj).children().children("a").removeClass("submail_ahover");
//    document.getElementById(id).style.display="none";
    //  $(obj).children("div").hide();
    $(obj).children("div").hide();
}

function showThisMail(obj){

    var tmpObj= $(obj).parent().next("div").css("display");
    if(tmpObj=="none"){
     $(obj).parent().next("div").show();
     $(obj).css("background-image","url(/images/sidebar/bg_mail_menu01.gif)");

    }else{
    $(obj).parent().next("div").hide();
        $(obj).css("background-image","url(/images/sidebar/bg_mail_menu02.gif)");
    }

}

function showThis(obj){
    $(obj).show();
}

function closeThis(obj){
    $(obj).hide();
}

//改变过滤条件的时候，修改上下条消息的跳转值
function changeMes(objValue){


    $.ajax({
        type : "POST",
        url : "/message/ebay/jump-Change",
        data : {
            'userAccount' :objValue
        },
        dataType : 'json',
        success : function(json) {
        	if(json.length == 0){
        		$("#next_a").hide();
        		$("#prev_a").hide();
        		alertTip("该账户不存在邮件信息");
        	}else{
        		$("#next_a").show();
        		$("#prev_a").show();
        		if(json.next.ebay_message_id){
        			$("#next_a").attr("onclick","jumpMes('"+json.next.ebay_message_id+"')");
        		}else{
        			$("#next_a").attr("onclick","jumpMes('')");
        		}
        		if(json.prev.ebay_message_id){
        			$("#prev_a").attr("onclick","jumpMes('"+json.prev.ebay_message_id+"')");
        		}else{
        			$("#prev_a").attr("onclick","jumpMes('')");
        		}
        	}
        }
    });

}
function jumpMes(obj){

    if(!obj){
        alert("不存在消息");return;
    }
    var jHref="/message/ebay/detail/type/prev/messageId/";
    jHref=jHref+obj;
    jHref+="/append/"+$("#append").val();
    var user= $("#user_account_select").val();
    if(user){
        jHref+="/userAccount/"+user;
    }
    location.href=jHref;

}
