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
<link type="text/css" rel="stylesheet" href="/css/layout_index.css?20140304"/>
<style type="text/css">

.login_news,.login_head_text{display:none;}
</style>
</head>
<body class="grayBg">
	<script type="text/javascript">
    function verc() {
         $('#verifyCode').attr('src', '/default/index/verify-code/' + Math.random());
     }  
    $(function () {
	    $(".change").click(function(){
	    	verc();
	    });
        $('#user_code').change(function(){
        	var user_code=$(this).val();
        	var this_ = $(this);
        	$.ajax({
        		type: "POST",
//        		async: false,
        		dataType: "json",
        		url: '/default/register/verify-user',
        		data: {'user_code':user_code},
        		success: function (json) {
        		    if(json.ask){
        		    	this_.next().html(json.message);
        		    }else{
        		    	this_.next().html('账户名可注册');
        		    }
        		}
        	});
        });

		$('#user_password').change(function(){
        	var user_password=$(this).val();
        	var this_ = $(this);
        	var mediumRegex = new RegExp("^(?=.{6,16})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))).*$", "g");
        	
        	if(!mediumRegex.test(user_password))
        		this_.next().html('密码必须为6~16位,同时包含数字字母组合，请检查.');
            else
              this_.next().html('');
        });

        $('#user_email').change(function(){
        	var user_email=$(this).val();
        	var this_ = $(this);
        	$.ajax({
        		type: "POST",
//        		async: false,
        		dataType: "json",
        		url: '/default/register/verify-email',
        		data: {'user_email':user_email},
        		success: function (json) {
        		    if(json.ask){
        		    	this_.next().html(json.message);
        		    }else{
        		    	this_.next().html('邮箱可注册');
        		    }
        		}
        	});
        });

        $('#registerBtn').click(function(){
            var param = $('#ec_register').serialize();
        	$.ajax({
        		type: "POST",
//        		async: false,
        		dataType: "json",
        		url: '/default/register/index',
        		data: param,
        		success: function (json) {
        			$(".remind").remove();
        			var html = '<li class="remind"><p>'+json.message+'</p></li>';
        		    if(json.ask){
        		    	$(".inpList li:eq(0)").before(html);
        		        setTimeout(function(){window.location.href='/default/index/login/?register=1&user_code=' + $("#user_code").val();},500);
        		    }else{
        		   	    $(".inpList li:eq(0)").before(html);
        		        verc();
        		    }
        		}
        	});

        })
    });
</script>
    <div class="Content">
    	<div class="logo"><a href="javascript:void(0)"><img src="/images/index/logo.png" alt="中国邮政速递物流" /></a></div>
    	<div class="wrapper">
	    	<div class="rgstitle">
				<h3>注册账户</h3>
				<a href="/default/index/login/">登陆</a>
			</div>
			<form method="post" id="ec_register" onsubmit='return false;' class="regForm">
				<ul class="inpList">
					<{if isset($regMsg)}> 
					<li class="remind">
					 <p><{$regMsg}>已注册用户？
						<a href='/default/index/login/'>立即登录</a></p>
					</li>
					<{/if}>
					
					<li>
						<label><i>*</i> 账户名：</label>
						<input type="text"  name="user_code" maxlength="50" id="user_code"/>
						<em></em>
					</li>
					<li>
						<label><i>*</i> 密码：</label>
						<input type="password" name="user_password" id="user_password"/>
						<em></em>
					</li>
					<li>
						<label><i>*</i> 确认密码：</label>
						<input type="password" name="user_password_confirm" id="user_password_confirm"/>
						<em></em>
					</li>
					<li>
						<label><i>*</i> 邮箱：</label>
						<input type="text" name="user_email" id="user_email"/>
						<em></em>
					</li>
					<li>
						<label><i>*</i> 姓名：</label>
						<input type="text" name="user_name" id="user_name"/>
						<em></em>
					</li>
					<li>
						<label><i>*</i> 公司：</label>
						<input type="text" name="company_name" id="company_name"/>
						<em></em>
					</li>
					<li>
						<label>手机：</label>
						<input type="text" name="user_mobile_phone" id="user_mobile_phone"/>
						<em></em>
					</li>
					<li>
						<label><i>*</i> 电话：</label>
						<div class="constinp">
							<input type="text" value="区号-电话号-分机号" onfocus="if (value =='区号-电话号-分机号'){value =''}" onblur="if (value ==''){value='区号-电话号-分机号'}" name="user_phone" id='user_phone'/>
							<em></em>
							<p>请填写真实的手机或联系电话，注册后客服人员将在1个工作日内与您取得联系进行信息确认以及开通账户。</p>
						</div>
					</li>
					<li>
						<label>大客户ID：</label>
						<input type="text" name="vip_code" id="vip_code"/>
						<em></em>
					</li>
				</ul>
				<{if isset($authCode) && $authCode=='1'}>
				<ul class="Codes">
					<li>
					<label><i>*</i> 验证码：</label>
					<input type="text" value="" name="authCode"/></li>
					<li><img id="verifyCode" style="width:72px;" src="/default/index/verify-code" alt="" /></li>
					<li class="change"><a href="javascript:void(0)"> 看不清？换一张</a></li>
				</ul>
				<{/if}>
				<div class="Btns">
					<button id="registerBtn">确认注册</button>
				</div>
				<!-- 隐藏的平台加密token -->
					<input type="hidden" name="user_sources" value="<{if isset($userSources)}><{$userSources}><{/if}>" />
					<input type="hidden" name="platform_token" value="<{if isset($eBayEIASTokenId)}><{$eBayEIASTokenId}><{/if}>" />
			</form>
		</div>		<!-- wrapper -->
		<div class="footer">
			<p>Copyright2016 @ 中国邮政速递物流 All Rights Reservedf</p>
		</div>
    </div>
</body>
</html>