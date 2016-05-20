<style type="text/css">
.org_box1{border-radius: 8px;width:400px; height:auto; line-height:20px; margin:40px auto; padding-left:2em; padding-top:1em;padding-bottom:1em;background:#a5d3eb;font-size:14px; position:relative; margin-left: 50px;}
.org_box2{border-radius: 8px;width:400px; height:auto; line-height:20px; margin:40px auto; padding-left:2em; padding-top:1em;padding-bottom:1em;background:#beceeb;font-size:14px; position:relative; margin-right: 50px;} 
.org_box_cor{ font-size:0;border-style:solid;overflow:hidden; position:absolute; }
.cor1{border-width:0px;border-color:#beceeb transparent transparent transparent;left:60px; bottom:-40px;}
.cor2{border-width:0px;border-color:transparent #a5d3eb transparent transparent;left:-40px; bottom:30px;}
.cor4{border-width:0px;border-color:transparent transparent transparent #beceeb;right:-40px; bottom:30px;}
</style>
<script>
$(function(){
// 	var w = 1000;
//     var h = parent.windowHeight();
// 	window.parent.$(".dialogIframe").dialog({
//         autoOpen: true,
//         width: w,
//         maxHeight: h,
//         modal: true,
//         show: "slide",
//         buttons: [
//             {
//             	id:"go",
//                 text: '放行',
//                 click: function () {
                	
//                 }
//             },
//             {
//             	id:"send",
//                 text: '发送',
//                 click: function () {
//                 	$.ajax({
//                         type: "POST",
//                         async: false,
//                         dataType: "json",
//                         url: "/hold/cts-issue/save-message",
//                         data:$("#content_form").serializeArray(),
//                         success: function (json) {
//                             if (isJson(json)) {
//                                 if (json.state) {
//                                 	showMessage();
//                                 }else{
//                                 	alertTip('<span class="tip-error-message">'+json.message+'</span>');
//                                 }
//                             }

//                         }
//                     });
//                 }
//             }
//         ],
//         open:function(){
//         },
//         close: function () {
//             $(this).detach();
//         }
//     }).width(w-25).height(h);
	
	var p = window.parent.$(".dialogIframe").parent();
    if(<{$responseMark}>  == '1'){
        $('#fxMessage').hide();
    }
    if(<{$responseMark}> == '3'){
    	$('#fxMessage').hide();
    	$('#sendMessage').hide();
    	$('#content').hide();
    	$("#showMark").show();
    }

    $("#sendMessage").live("click",function(){
        if($.trim($("#content").val()) == "" || $.trim($("#content")).length == 0){
			alertTip("请输入回复内容.");
			return;
        }
    	$.ajax({
         type: "POST",
         async: false,
         dataType: "json",
         url: "/hold/cts-issue/save-message",
         data:$("#content_form").serializeArray(),
         success: function (json) {
             if (isJson(json)) {
                 if (json.state) {
                 	showMessage();
                 	var ifId = window.parent.$('.iframe-container:visible').attr('id');
					window.parent.frames[ifId].initData(paginationCurrentPage);	
                 }else{
                 	alertTip('<span class="tip-error-message">'+json.message+'</span>');
                 }
             }

         }
     });
   	});

   	$("#fxMessage").live("click",function(){
   		var tip = '<span class="tip-warning-message">亲，您点击放行【确认】按钮后，代表您已知悉并认同我司对此问题提供的处理意见哦；如果您想提交不同的处理意见，请点击【取消】，然后输入内容后点击【发送】，谢谢~</span>';
 		alertTip(tip);
    	$('#dialog-auto-alert-tip').dialog('option',
                'buttons',
                {
    					'确认(OK)': function () {
                        $(this).dialog("close");
                        $.ajax({
                            type: "POST",
                            async: false,
                            dataType: "json",
                            url: "/hold/cts-issue/remove-hold",
                            data:$("#content_form").serializeArray(),
                            success: function (json){
                            	if (!isJson(json)) {
            	                    alertTip('Internal error.');
            	                }
                            	
                            	if (json.state) {
                            		alertTip('<span class="tip-success-message">操作成功.</span>');
                                 	var ifId = window.parent.$('.iframe-container:visible').attr('id');
                					window.parent.frames[ifId].initData(paginationCurrentPage);	
                                 }else{
                                 	alertTip('<span class="tip-error-message">'+json.message+'</span>');
                                 }
                        	}
                        });
                    }, '取消(Close)': function () {
                    	$(this).dialog("close");
                }
         });
   	});
        
});

function showMessage(){
	var dd = $("textarea[name='message_content']").val();
	$(".box_hide").find("span[class='cen']").html(dd);
	var clone = $(".box_hide").clone();
	clone.removeClass("box_hide");
	clone.show();
	clone.appendTo("#message_con");
	$("textarea[name='message_content']").val("");
}



</script>
<div style = "height:375px;overflow: auto;" id = "message_con">
	<{if $responseMessage}>
		<{foreach from=$responseMessage item=mes name=mes key=k}>
			
			<{if $mes.st_id_create != ""}>
				<div class="org_box1"><span class="org_box_cor cor2" style = "margin-right:300px;"><img alt="客服回复" src="/images/modeling/chat-support.png" style = "right:100" width = "40px" height="40px"></span><{$mes.message_content}></div>
			<{/if}>
			<{if $mes.replay_name != ""}>
				<div class="org_box2"><span class="org_box_cor cor4"><img alt="客户回复" src="/images/modeling/client.png" style = "right:100" width = "40px" height="40px"></span><{$mes.message_content}></div>
			<{/if}>
		<{/foreach}>
	<{/if}>
	
</div>
<div id = "hold_title" style = "background-color: #eeeeee;padding:9px;">
	<span><b>运单号：<{$issu.shipper_hawbcode}></b></span>
	<span style = "margin-left:30px;"><b>问题类型：<{$kind.issuekind_cnname}></b></span>
</div>
<div style = "text-align: center;">
	<span id = "showMark" style = "display:none;color: #d2d8fa;font-size: 18px;"><b>亲，此状态只允许查看哦，如有疑问请联系客服MM哦~~</b></span>
	<form action="" id = "content_form">
		<input style = "display: none;" name = "id" value = "<{$issu.issue_id}>"/>
		<textarea rows="" cols="" style = "width:693px;height:100px;margin-left: 10px;" name = "message_content" id = "content" placeholder="亲，请告诉我您的处理意见哦~"></textarea>
		</br>
		<input class="baseBtn" type="button" id = "sendMessage" value="发送" name="code" style = "float: right;margin-right:12px;margin-top:5px;background-color: #2283c5 !important;border: 4px solid #2283c5;">
		<input class="baseBtn" type="button" id = "fxMessage" value="放行" name="code" style = "float: right;margin-right:12px;margin-top:5px;background-color: #2283c5 !important;border: 4px solid #2283c5;">
	</form>
</div>
<div class="org_box2 box_hide" style = "display: none;"> <span class="org_box_cor cor4"><img alt="客户回复" src="/images/modeling/client.png" style = "right:100" width = "40px" height="40px"></span><span class = "cen"></span></div>