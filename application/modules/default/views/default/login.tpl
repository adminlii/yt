<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<!--
<meta http-equiv="x-ua-compatible" content="ie=7" />
 -->
<title>Warehouse System</title>
<script type="text/javascript" src="/js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="/js/json2.js"></script>
<script type="text/javascript" src="/js/jquery-cookie.js"></script>
<link href="/css/ui-lightness/jquery-ui.min.css" rel="stylesheet" type="text/css" />
<link type="text/css" rel="stylesheet" href="/css/global.css" />
<link type="text/css" rel="stylesheet" href="/css/login/index.css?20140304" />
</head>
<body>
<script type="text/javascript">
    //iframe嵌套
    if (top.location != location) {
    	top.location.href = location.href;
    }
    $(function () {
        $("#userName").focus().css("background-color", "#FFFFCC");
        $(".login_input").click(function () {
            $(".login_input").css("background-color", "#FFFFFF");
            $(this).css("background-color", "#FFFFCC");
        });

        //浏览器提示
        initTips();

        /**
         * 不在提示
         */
        $(".not_prompt").live('click',function(){
        	var cookie_data =  loadTipsCookie();
        	var json = JSON.parse(cookie_data);
        	json.val = '1'
        	setTipsCookie(JSON.stringify(json));
        	_tipsDialog.dialog("close");
        });
    });

    <{if isset($authCode) && $authCode=='1'}>
    function verc() {
        $('#verifyCode').attr('src', '/default/index/verify-code/' + Math.random());
    }
    <{/if}>


   	/**
     * 关于浏览器的提示代码
     */
    function initTips(){
        //是否为火狐浏览器
        if($.browser.mozilla){
			return;
        }
        //cookie判断
        var version = 'V1.0.1';
        var bol = 0;
    	var cookie_data =  loadTipsCookie();
    	if(!cookie_data || JSON.parse(cookie_data).version != version){
    		var strData1 = '{"version" : "';
    		var strData2 = '","val" : "';
    		var strDataEnd = '"}';
    		var tmpData = strData1 + version + strData2 + bol + strDataEnd;
    		setTipsCookie(tmpData);
    	}else if(cookie_data != '' && JSON.parse(cookie_data).version == version){
    		if(JSON.parse(cookie_data).val != bol){
        		//不在提醒
    			return;
        	}
        }

        //组织语言
    	var fireFoxUrl = "http://www.firefox.com.cn/";
        var tips = "<p><span class='tip-warning-message'>为了更好的使用体验，易仓科技建议您使用<b style='color:#1B9301;'>Firefox</b>(火狐)浏览器。</span></p>";
        tips += "<p class='option'><b>●</b>&nbsp;我没有啊--><a href='"+fireFoxUrl+"' target='_blank'>前往Firefox主页</a></p>";
        tips += "<p class='option'><b>●</b>&nbsp;我不在乎--><a class='not_prompt' href='javascript:;' >不再提醒</a></p>";

        //温馨提示
        alertConfirmTip(tips);
    }
    /**
     * 温馨提示
     */
    var _tipsDialog = null;
    function alertConfirmTip(tip,callBack,width,height) {
        width = width ? width : 475;
        height = height ? height : 500;
        _tipsDialog = $('<div title="温馨提示" id="dialog-auto-alertConfirm-tip">' + tip + '</div>').dialog({
            autoOpen: true,
            width: width,
            maxHeight: height,
            modal: true,
            show: "slide",
            close: function () {
                $(this).detach();
           }
        });
    }

    var _tipsCookieName = 'tips_operating_data';
    var _tipsCookieExpires = 60;
    var _tipsCookiePath = '/';
    /**
     * 读取提醒
     */
    function loadTipsCookie(){
    	return $.cookie(_tipsCookieName);
    }
    /**
     * 设置提醒
     */
    function setTipsCookie(data){
    	 $.cookie(_tipsCookieName,data,{expires:_tipsCookieExpires, path: _tipsCookiePath});
    }
</script>
	<div class="login_head">
		<div class="login_logo">
			<a href="/"></a>
		</div>
		<div class="login_head_text">
			<a href="/">首页</a>
			|
			<a href="#">帮助文档</a>
			|
			<a href="#">常见问题</a>
			|
			<a href="/">技术支持</a>
		</div>
	</div>
	<div class="login_main">
		<div class="loginTab">
			<ul>
				<li class="chooseTag">
					<a href="#">用户登录</a>
				</li>
			</ul>
		</div>
		<div class="login_box">
			<div class="login_fill">
				<div style="padding-left: 40px; color: red; font-weight: bold;"><{if isset($errMsg)}><{$errMsg}><{/if}></div>
				<form method="post" id="ec_login" action="/default/index/login">
					<table width="95%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td height="46" style="text-align: right;">
								<span style="color: red">*</span>
								账户名：
							</td>
							<td>
								<input name="userName" autocomplete="off" maxlength="50" type="text" class="login_input" id="userName" />
							</td>
							<td style="color: #969696;"></td>
						</tr>
						<tr>
							<td height="46" style="text-align: right;">
								<span style="color: red">*</span>
								密码：
							</td>
							<td>
								<input name="userPass" autocomplete="off" type="password" class="login_input" id="userPass" />
							</td>
							<td style="width: 30%; color: #969696;"></td>
						</tr>
						<{if isset($authCode) && $authCode=='1'}>
						<tr>
							<td height="46" style="text-align: right;">
								<span style="color: red">*</span>
								验证码：
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
								<input type="submit" id="login" value="立刻登陆" class="login_Btn" />
								&nbsp;&nbsp;
								<a href='/default/register/'>立即注册</a>
							</td>
						</tr>
					</table>
				</form>
			</div>
			<!--
			<div class="login_news" style='display:none'>
				<dl style="padding-top: 0px;">
					<dt>
						<img src="/images/login/login_news01.gif" />
					</dt>
					<dd>
						<strong>免费的电商仓储系统</strong>
						<p>只要注册即可免费使用EZWMS系统</p>
					</dd>
					<div class="clr"></div>
				</dl>
				<dl>
					<dt>
						<img src="/images/login/login_news02.gif" />
					</dt>
					<dd>
						<strong>为B2C电商卖家量身打造</strong>
						<p>精细化的库存管理，销售平台无缝对接</p>
					</dd>
					<div class="clr"></div>
				</dl>
				<dl style="border-bottom: none;">
					<dt>
						<img src="/images/login/login_news03.gif" />
					</dt>
					<dd>
						<strong>强大的第三方仓储系统</strong>
						<p>全流程可配置，日人均最高可操作800单，错误率万分之一</p>
					</dd>
					<div class="clr"></div>
				</dl>
			</div>
			 -->
		</div>
		<div id="footer_box">
			<div class="footer">
				<!--<p>关于俄速通 | 联系俄速通 | 使用条款 | 保密声明</p>
				<p>
					Copyright 2013©黑龙江俄速通国际物流有限公司
				</p>-->
				<p>
					商业渠道发运
				</p>
			</div>
		</div>

</body>
</html>