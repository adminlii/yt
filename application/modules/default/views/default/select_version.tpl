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
<link type="text/css" rel="stylesheet" href="/css/layout_index.css?20140304"/>
</head>
<body>
    <div class="head">
    	<div class="wrapper">
    		<h1><a href="javascript:void(0)"><img src="/images/index/logo.png" alt="中国邮政速递物流" /></a></h1>
    		<p>商业渠道在线发运</p>
    	</div>
    </div>
    <div style="display: none" id="browser_ie">
		<div class="brower_info">
			<div class="notice_info">
				<P>你的浏览器版本过低，可能导致网站不能正常访问！<br>为了你能正常使用网站功能，请使用这些浏览器。</P></div>
				<div class="browser_list">
				<span><a href="https://www.baidu.com/s?word=谷歌浏览器下载" target="_blank"><img src="/images/index/Chrome.png"><br>Chrome</a></span>
				<span><a href="https://www.baidu.com/s?word=火狐浏览器下载" target="_blank"><img src="/images/index/Firefox.png"><br>Firefox</a></span>
				<span><a href="https://www.baidu.com/s?word=最新版IE浏览器下载" target="_blank"><img src="/images/index/IE.png"><br>IE9及以上</a></span>
			</div>
		</div>
	</div>
    <div class="banner">
    	<div class="wrapper">
	    	<div class="loginBox Radius5">
	    		<form method="post" id="ec_login" action="/default/index/login" class="BoxRadius5"> 
				<ul class="accmm">
					<{if isset($smarty.get.register) && $smarty.get.register eq '1'}>
							<li><{t}>logo_account<{/t}>：<span style="color: #666666;"><{$smarty.get.user_code}></span> <{t}>register_success_01<{/t}></li>
						<{else if isset($successUserCode)}>
							<li><{t}>logo_account<{/t}>：<span style="color: #666666;"><{$successUserCode}></span> <{t}>register_email_verify<{/t}></li>
					<{/if}>
					<{if isset($errMsg)}><li><{$errMsg}></li><{/if}>
					<li><input name="userName" class="acc" type="text" placeholder="账户" /></li>
					<li><input name="userPass" class="mm" type="password" placeholder="密码" /></li>
				</ul>
				<{if isset($authCode) && $authCode=='1'}>
				<ul class="Codes">
					<li><input name="authCode" type="text" placeholder="验证码" /></li>
					<li><img id="verifyCode" style="width:72px;" src="/default/index/verify-code" alt="" /></li>
					<li class="change"><a href="javascript:void(0)">换一张</a></li>
				</ul>
				<{/if}>
				<button class="loginbtn Radius5">登&nbsp; &nbsp;陆</button>
			</form>
			<div class="resginer">
				<a class="forgotmm" href="/default/index/find-pwd">忘记密码？</a>
				<p class="fr">还没有账号？<a class="register" href="/default/register/">点击注册</a></p>
			</div>
	    	</div>
    	</div>
    </div>
    <div class="containter">
    	<div class="wrapper oflr">
    		<div class="title">
	    		<h3>关于我们 <span>About Us</span></h3>
	    	</div>
	    	<div class="floor">
	    		<div class="LeftCont fl">
	    			<h6>服务介绍：</h6>
	    			<p>中速国际快件业务即“China International Express”（以下简称“中速快件”）。中速快件业务是指中国邮政速递物流股份有限公司（以下简称邮政速递)与商业快递公司（DHL、TNT、佐川等）合作办理的国际快件业务。</p>
	    			<h6>服务特点：</h6>
	    			<p>“中速快件”通达全球220多个国家和地区，中速快件根据重量、运递时限和服务方式的不同，可提供“标准快件”、“经济快件”和“重货快件”等服务；同时提供门到门、门到港、港到港以及增值服务（收件人付费、代垫关税等）。</p>
	    		</div>
	    		<div class="RightCont fr">
	    			<p><img  src="/images/index/img1.jpg" alt="" /></p>
	    		</div>
	    	</div>
    	</div>
    </div>
    <div class="sevicepro">
    		<ul class="wrapper">
    			<li><h3>产品服务 :</h3></li>
    			<li><a href="javascript:void(0)"><img src="/images/index/pro2.jpg" alt="" /></a></li>
    			<li><a href="javascript:void(0)"><img src="/images/index/pro3.jpg" alt="" /></a></li>
    			<li><a href="javascript:void(0)"><img src="/images/index/pro4.jpg" alt="" /></a></li>
    			<li><a href="javascript:void(0)"><img src="/images/index/pro1.jpg" alt="" /></a></li>
    			<li><a href="javascript:void(0)"><img src="/images/index/pro5.jpg" alt="" /></a></li>
    			<li><a href="javascript:void(0)"><img src="/images/index/pro6.jpg" alt="" /></a></li>
    			<li><a href="javascript:void(0)"><img src="/images/index/pro7.jpg" alt="" /></a></li>
    			<li><a href="javascript:void(0)"><img src="/images/index/pro8.jpg" alt="" /></a></li>
    		</ul>
    	</div>
    <div class="footer">
    	<div class="wrapper">
    		<p><a href="http://www.ems.com.cn" target="_blank">中国邮政官网</a></p>
    		<p>Copyright2016 @ 中国邮政速递物流 All Rights Reservedf</p>
    	</div>
    </div>
    <script>
    if (!$.support.leadingWhitespace) {
        $("#browser_ie").show();
    }
    $(".change").click(function(){
    	$('#verifyCode').attr('src', '/default/index/verify-code/' + Math.random());
    });
    </script>
</body>
</html>