<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<!--
<meta http-equiv="x-ua-compatible" content="ie=7" />
 -->
<title><{$system_title}></title>
<script type="text/javascript" src="/js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="/js/json2.js"></script>
<script type="text/javascript" src="/js/jquery-cookie.js"></script>
<link href="/css/ui-lightness/jquery-ui.min.css" rel="stylesheet" type="text/css" />
<link type="text/css" rel="stylesheet" href="/css/global.css" />
<link type="text/css" rel="stylesheet" href="/css/public/index.css?20140404" />
<link type="text/css" rel="stylesheet" href="/css/login/index.css?20140304"/>
</head>
<body>
<script type="text/javascript">
    //iframe嵌套
    if (top.location != location) {
    	top.location.href = location.href;
    }
	$(function () {

		$(".login_news > dl").mouseover(function(){
			var obj = $(this).find(".sys_introduction");
			obj.removeClass("ellipsis");
			obj.addClass("sys_introduction_panel");
			obj.css("width","250px");
		}).mouseout(function(){
			$(".sys_introduction").addClass("ellipsis");
			$(".sys_introduction").removeClass("sys_introduction_panel");
			$(".sys_introduction").css("width","212px");
		});

	    $("#userName").focus().css("background-color", "#FFFFCC");
	    $(".login_input").click(function () {
	        $(".login_input").css("background-color", "#FFFFFF");
	        $(this).css("background-color", "#FFFFCC");
	    });

	    //忘记密码
	    $("#forgetPassword").click(function(){
	    	$("#ec-forget_password-dialog").dialog("open");
		});

		  //忘记密码弹窗
	    $("#ec-forget_password-dialog").dialog({
            autoOpen: false,
            width: 400,
            height: 'auto',
            modal: true,
            show: "slide",
            buttons: [
                {
                    text: "确定(Ok)",
                    click: function () {
                    	var fp_username = $("#fp_user_name").val();
                    	fp_username = $.trim(fp_username);
                    	$("#fp_user_name").val(fp_username);
                    	var fp_email =  $("#fp_email").val();
                    	fp_email = $.trim(fp_email);
                    	$("#fp_email").val(fp_email);

                    	var reg = /^[a-zA-Z0-9_\.-]+\@([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,4}$/;
						if(fp_username == ''){
							alertTip('<span class="tip-error-message">账户不能为空.</span>');
							return;
						}else if(fp_email == ''){
							alertTip('<span class="tip-error-message">Email不能为空.</span>');
							return;
						}else if(!reg.test(fp_email)){
							alertTip('<span class="tip-error-message">Email格式不正确.</span>');
							return;
						}

						alertTip('<span class="tip-load-message">系统正在努力处理中...</span>');
						var _this = $(this);
                        var data = $("#forgetPasswordFrom").serialize();
                        $.ajax({
                            async:false,
                            type:'Post',
                            url:'/default/index/password-Recovery',
                            data:data,
                            dataType:'json',
                            success:function(json){
                            	$("#dialog-auto-alert-tip").dialog("close");
                                if(json.state == 0){
                                    alertTip('<span class="tip-error-message">' + json.message + '</span>');
                                }else{
                                	_this.dialog("close");
                                    alertTip('<span class="tip-success-message">' + json.message + '</span>');
                                }
                            }
                        })
                    }
                },
                {
                    text: "取消(Cancel)",
                    click: function () {
                        $(this).dialog("close");
                    }
                }
            ],
            close: function () {
                $(':input','#forgetPasswordFrom')
                 .not(':button, :submit, :reset, :hidden')
                 .val('')
                 .removeAttr('checked')
                 .removeAttr('selected');

            },
            open:function(){
                $(':input','#forgetPasswordFrom')
                 .not(':button, :submit, :reset, :hidden')
                 .val('')
                 .removeAttr('checked')
                 .removeAttr('selected');
            }
        });
	});

	function verc() {
	    $('.verifyCode').attr('src', '/default/index/verify-code/' + Math.random());
	}
	<{if isset($authCode) && $authCode=='1'}>
	<{/if}>

	function alertTip(tip, width, height) {
	    width = width ? width : 400;
	    height = height ? height : 'auto';
	    $('<div title="操作提示 (Esc)" id="dialog-auto-alert-tip"><p align="">' + tip + '</p></div>').dialog({
	        autoOpen: true,
	        width: width,
	        maxHeight: height,
	        modal: true,
	        show: "slide",
	        buttons: [
	            {
	                text: 'Close',
	                click: function () {
	                    $(this).dialog("close");
	                }
	            }
	        ],
	        close: function () {
	            $(this).detach();
	        }
	    });
	}
	
	
  	$(function(){
		if (!$.browser.mozilla) {
			$("#content").slideDown(500);
			
		    	
		}
  	}) 
  	
  	
  	function closeTip(){
	$("#content").hide();
}

</script>
<style>
 .tip-load-message{
   	background: url("/images/base/bg_tips04.gif") no-repeat scroll 0 0 rgba(0, 0, 0, 0);
    clear: both;
    float: left;
    height: 24px;
    line-height: 25px;
    margin-top: 2px;
    padding-left: 28px;
    width: 90%;
}
.tip-error-message{
    clear:both;
    background:url(/images/base/bg_tips03.png) no-repeat 0 0px;
    margin-top:2px;
    padding-left:16px;
}

.tip-success-message{
    float:left;
    width:90%;
    clear:both;
    background:url(/images/base/bg_tips02.png) no-repeat 0 2px;
    margin-top:2px;
    padding-left:16px;
}
.loginTab LI {
	/*
	width:419px;
	*/
	cursor: pointer;
}

.ellipsis {
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    width: 100%;
}

.sys_introduction_panel{
	position: absolute;
    width: 212px;
	background-color: #FAFAFA;
	border-radius: 0 0 5px 5px;
	box-shadow: 0 0 5px #DDDDDD;
}

.login_Btn_a{
	 background: none repeat scroll 0 0 #0097E3;
    border: medium none;
    border-radius: 5px;
    color: #FFFFFF;
    cursor: pointer;
    font-size: 14px;
    height: 30px;
    line-height: 30px;
    padding: 3px 13px;
    text-align: center;
}

.login_news dd{
	display: none;
}
.table-module {
	-moz-border-bottom-colors: none;
    -moz-border-left-colors: none;
    -moz-border-right-colors: none;
    -moz-border-top-colors: none;
    border-collapse: separate;
    border-color: #DDDDDD #DDDDDD #DDDDDD -moz-use-text-color;
    border-image: none;
    border-radius: 4px;
    border-style: none solid solid none;
    border-width: 1px 1px 1px 0;
}
.table-module tr {  }
.table-module td {
	border-top: 1px solid #DDDDDD;
    line-height: 20px;
    padding: 8px 4px 8px 4px;
    vertical-align: top;
}
.ec-f-left{
    float:left;
}
.ec-center{
    text-align:center;

}
.table-module td table {
 	-moz-border-bottom-colors: none;
    -moz-border-left-colors: none;
    -moz-border-right-colors: none;
    -moz-border-top-colors: none;
    border-collapse: separate;
    border-right: 1px solid #DDDDDD;
	border-bottom: 1px solid #DDDDDD;
}
.table-module,.table-module td{  }
.table-module-b1 { background:#fff; }
.table-module-b2 { background:#F9F9F9;}
.table-module-b3 { background:#fcf5dd;}
.table-module-b4 { background:#D5E3E2;}
.table-module-title {
	background: linear-gradient(to bottom, #F8F8F8, #ECECEC) repeat-x scroll 0 0 #F3F3F3;
    color: #707070;
    font-weight: normal;
}
.table-module-title td {
	font-weight: bold;
}
.table-module tr td:first-child {
    border-left-color: #DDDDDD;
}
.table-module tr td:last-child {
    border-left-color: #DDDDDD;
}
.table-module td {
    border-left: 1px solid #DDDDDD;

}

#table-module-list-data tr:hover{
    background: #EDEDED;
}
.msg{
	color: #FF0000;
} 
.login_logo{background-image:none;}
 
 
 
#content{ 
	width: 100%; 
	height:30px;
	border:1px solid #d5d5d5;
	background:#ffffdd; 
	overflow:hidden;
	position:absolute;
	display: none;
} 
#content #content_c {
	line-height:25px;
	font-size:16px;
	color:#484848;
	text-align:left;
	margin: 0 auto;
	padding: 0;
	width: 960px;
}

</style>
<div id="content"> 
	<div id="content_c">
  		尊敬的客户您好，使用火狐浏览器可获得更好的操作体验.&nbsp;&nbsp;<a href="javascript:closeTip();">[关闭]</a>
  	</div>
</div>
	<div class="login_head">
		<div class="login_logo">
			<a href="/"><img src="<{$logo}>" alt="" style='width:100%;height:100%;'/></a>
		</div>
		<div class="login_head_text">
			<a href="/">首页</a>
			|
			<a href="#">帮助文档</a>
			|
			<a href="#">常见问题</a>
			|
			<a href="#">技术支持</a>
		</div>
	</div>
	<div class="login_main">
		<div class="loginTab">
			<ul style="text-align: center;">
				<li class="loginTabLi chooseTag" show-data="erp-login">
					<a href="javascript:;">用户登录</a>
				</li>
			</ul>
		</div>
		<div class="login_box">
			<!-- 普及版登陆 -->
			<div id="erp-login" class="option-module">
				<div class="login_fill">
					<div style="padding-left: 40px; color: #1B9301; font-weight: bold;">
						<{if isset($smarty.get.register) && $smarty.get.register eq '1'}>
							<{t}>logo_account<{/t}>：<span style="color: #666666;"><{$smarty.get.user_code}></span> <{t}>register_success_01<{/t}>
						<{else if isset($successUserCode)}>
							<{t}>logo_account<{/t}>：<span style="color: #666666;"><{$successUserCode}></span> <{t}>register_email_verify<{/t}>
						<{/if}>
					</div>
					<div style="padding-left: 40px; color: red; font-weight: bold;"><{if isset($errMsg)}><{$errMsg}><{/if}></div>
					<form method="post" id="ec_login" action="/default/index/login">
						<table width="95%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td height="46" style="text-align: right;width: 20%;">
									<span style="color: red">*</span>
									<{t}>user_name<{/t}>：
								</td>
								<td style="width: 80%;">
									<input name="userName" autocomplete="off" maxlength="50" type="text" class="login_input" id="userName" />
								</td>
								<td style="color: #969696;"></td>
							</tr>
							<tr>
								<td height="46" style="text-align: right;">
									<span style="color: red">*</span>
									<{t}>user_password<{/t}>：
								</td>
								<td>
									<input name="userPass" autocomplete="off" type="password" class="login_input" id="userPass" />
									<!-- <a href='javascript:;' id="forgetPassword"><{t}>forget_password<{/t}></a> -->
								</td>
								<td style="width: 30%; color: #969696;"></td>
							</tr>
							<{if isset($authCode) && $authCode=='1'}>
							<tr>
								<td height="46" style="text-align: right;">
									<span style="color: red">*</span>
									<{t}>verification_code<{/t}>：
								</td>
								<td>
									<input name="authCode" autocomplete="off" style="width: 80px" type="text" class="login_input" id="authCode" />
									<label class="verifyCode"> <img id="verifyCode" align="absmiddle" src="/default/index/verify-code" width=72 height=23> &nbsp; &nbsp; <a href="javascript:void(0);" onclick="verc();">看不清？换一张</a></label>
								</td>
								<td style="width: 30%; color: #969696;"></td>
							</tr>
							<{/if}>
							<tr>
								<td height="60" style="text-align: right;"></td>
								<td>
									<input type="submit" id="login" value="<{t}>logoing<{/t}>" class="login_Btn" />
									<!-- &nbsp;&nbsp;<a href='/default/register/'><{t}>register<{/t}></a> -->
									
								</td>
							</tr>
						</table>
					</form>
				</div>
				<div class="login_news" style=''>
				    <h3 style='line-height:30px;font-weight:normal;font-size:14px;'>订单追踪</h3>
					<form action="/default/index/get-track-detail" method='post'> 
					    <textarea rows="" cols="" style='width:300px;height:70px;overflow:auto;font-size:13px;' placeholder='如需查询多个单号，请使用","隔开' name='code'></textarea>
					    <label>
					    <input type="text" class="input_text keyToSearch" name="authCode" style='width:70px;' placeholder='验证码'/>
						<img align="absmiddle" src="/default/index/verify-code" style='width:72px;height:23px;cursor:pointer;' class='verifyCode' onclick='verc();'></label>
						<input type="submit" class="baseBtn submitToSearch" value="<{t}>查询<{/t}>">
					    <p><{if $trackErrMsg}><{$trackErrMsg}><{/if}></p>
					</form>				
				</div>
			</div>
		</div>
		<div id="footer_box">
			<div class="footer">
				 
			</div>
		</div>
		<div id="ec-forget_password-dialog" style="display: none;" title="<{t}>password_recovery<{/t}>">
			<form onsubmit="return false" method="post" name="forgetPasswordFrom" id="forgetPasswordFrom">
				<table width="100%" border="0" cellspacing="0" cellpadding="0"
					style="margin-top: 10px;" class="table-module">
					<tbody class="table-module-list-data">
						<tr class="table-module-b1">
							<td style="width: 80px;text-align: right;color:#FF0000;">Tips：</td>
							<td>
								请填写您的账户名称以及注册时的Email，我们会将您的密码重置，并发送到您的邮箱中.
							</td>
						</tr>
						<tr class="table-module-b1">
							<td style="text-align: right;"><{t}>user_name<{/t}>:</td>
							<td>
								<input type="text" id="fp_user_name" name="fp_user_name" class="input_text">
								<span class="msg">*</span>
							</td>
						</tr>
						<tr class="table-module-b2">
							<td style="text-align: right;">Email:</td>
							<td>
								<input type="text" id="fp_email" name="fp_email" class="input_text">
								<span class="msg">*</span>
							</td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
</body>
</html>