/**
 * ebay纠纷，控制的js
 * @author Frank
 * @date 2013-11-05 15:19:263
 */
$(function(){
	//滚动纠纷消息列表到底部
	caseMessageScrollTop(5);
	
	/**
	 * 响应方式时间
	 */
	$("input[name='case_response_type']").click(function(){
		$(".operate_box").hide();
		var operateClass =  $(this).val();
		clearForm($(".operate_box"));
		$("." + operateClass).show();
		//显示模板
		showTemplateOption();
	});
});

/**
 * 滚动纠纷消息列表到底部
 */
var _msgContentHeight = 0;
var _scrollTopVal = 0;
function caseMessageScrollTop(val){
	var obj = $("#message_content");
	var max_height = 300;
	if(_msgContentHeight == 0){
		//获得消息内容高度
		_msgContentHeight = parseInt(obj.height());
		//设置最大高度
		obj.css("max-height",max_height);
	}
	var pos = _msgContentHeight - max_height + 30;
	var tmp_scrollTop = val + val;
	var ss = setTimeout("caseMessageScrollTop("+tmp_scrollTop+")","100");
	if(val > pos){
		val = pos;
		_scrollTopVal = val;
		clearTimeout(ss);
	}
	obj.scrollTop(val);
}

/**
 * 提供额外的解决方案，验证参数
 */
function checkOfferOtherSolutionParams(){
	var content = $("#msg_content_common").val();
	content = $.trim(content);
	if(content == ""){
		alertErrorTip("请填写消息内容.");
		return;
	}else if(content.lenght > 1000){
		alertErrorTip("消息内容超过1000个字符，请修改.");
		return;
	}else{
		callCaseResponse();
		_callBackNum = 1;
	}
}

/**
 * 提供发货信息，验证参数
 */
function checkProvideShippingInfo(){
	var content = $("#msg_content_shipping").val();
	content = $.trim(content);
	var shipping_name = $("#shipping_name").val();
	shipping_name = $.trim(shipping_name);
	var shipping_date = $("#shipping_date").val();
	shipping_date = $.trim(shipping_date);
	
	if(shipping_name == ""){
		alertErrorTip("请填写承运商.");
		return;
	}else if(shipping_date == ""){
		alertErrorTip("请填写发货时间.");
		return;
	}else if(content != content && content.length > 1000){
		alertErrorTip("消息内容超过1000个字符，请修改.");
		return;
	}else{
		callCaseResponse();
		_callBackNum = 2;
	}
}

/**
 * 提供轨迹单号信息，验证参数
 */
function checkProvideTrackingInfo(){
	var content = $("#msg_content_tracking").val();
	content = $.trim(content);
	var tracking_name = $("#tracking_name").val();
	tracking_name = $.trim(tracking_name);
	var tracking_no = $("#tracking_no").val();
	tracking_no = $.trim(tracking_no);
	
	if(tracking_name == ""){
		alertErrorTip("请填写承运商.");
		return;
	}else if(tracking_no == ""){
		alertErrorTip("请填写跟踪号.");
		return;
	}else if(content != content && content.length > 1000){
		alertErrorTip("消息内容超过1000个字符，请修改.");
		return;
	}else{
		callCaseResponse();
		_callBackNum = 3;
	}
}

/**
 * 提交表单
 */
var _callBackNum = 0;
function callCaseResponse(){
	alertLoadTip('正在努力的请求中，请等待...');
	var params = $("#submitResponse").serialize();
	$.ajax({
		type: "POST",
		data: params,
		dataType:"json",
		url: "/case/ebay-user-cases/call-Ebp-Case-Response",
		async:true,
		success:function(jsonData){
			var tips;
			$("#dialog-auto-alert-tip").dialog("close");
			if(isJson(jsonData)){
				if(jsonData['state'] == 1){
					alertSuccessTip(jsonData.message);
					callCaseResponseBack();
					parent.reLoadWhenClose=1;
				}else if(jsonData['state'] == 0){
					alertErrorTip(jsonData.message);
				}else{
	                if (jsonData.errorMessage == null)return;
	                alertErrorTip(jsonData.errorMessage);
				}
				
			}else{
				alertErrorTip("请求异常，请联系相关技术人员.");
			}
		}
		});
}

/**
 * 处理完成后的回调函数
 */
function callCaseResponseBack(){
	var content = '';
	if(_callBackNum == 1){
		content = $("#msg_content_common").val();
	}else if(_callBackNum == 2){
		content = $("#msg_content_shipping").val();
	}else if(_callBackNum == 3){
		content = $("#msg_content_tracking").val();
	}
	content = $.trim(content);
	$("input:radio[name='case_response_type']:checked").click();
	if(content != ''){
		var pTag = $("<p>").appendTo("#message_content");
		var spanTag = $("<span>");
		spanTag.addClass("message_seller");
		var date = new Date();
	 	var dateStr = date.getFullYear();
	 	dateStr += "-" + (date.getMonth() + 1);
	 	dateStr += "-" + date.getDay();
		
	 	dateStr += " " + date.getHours();
	 	dateStr += ":" + date.getMinutes();
	 	dateStr += ":" + date.getSeconds();
		var title = "SELLER(" + dateStr + ")：<br/>";
		spanTag.html(title + "&nbsp;&nbsp;&nbsp;&nbsp;" + content);
		spanTag.appendTo(pTag);
		
		var scrollTopVal = _scrollTopVal + spanTag.height() + 10;
		$("#message_content").scrollTop(scrollTopVal);
	}
}

/**
 * 弹出错误对话框
 */
function alertErrorTip(strArr){
	if(typeof(strArr) == "string"){
		strArr = [strArr];
	}
	
	var tips = "";
	$.each(strArr, function (key, val) {
    	tips += "<span class='tip-error-message'>" + val + "</span>";
    });
	alertTip(tips);
}

/**
 * 弹出正确对话框
 */
function alertSuccessTip(strArr){
	if(typeof(strArr) == "string"){
		strArr = [strArr];
	}
	
	var tips = "";
	$.each(strArr, function (key, val) {
    	tips += "<span class='tip-success-message'>" + val + "</span>";
    });
	alertTip(tips);
}

/**
 * 弹出等等对话框
 */
function alertLoadTip(str){
	var tips = "<span class='tip-load-message'>" + str + "</span>";
	alertTip(tips);
}

/**
 * 清空表单数据
 */
function clearForm(objE){
    $(objE).find(':input').each(  
        function(){  
            switch(this.type){  
                case 'passsword':  
                case 'select-multiple':  
                case 'select-one':  
                case 'text':  
                case 'textarea':  
                    $(this).val('');  
                    break;  
                case 'checkbox':  
                case 'radio':  
                    this.checked = false;  
            }  
        }     
    );  
}