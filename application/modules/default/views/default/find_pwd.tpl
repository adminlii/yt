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
<div class="pop_up">
	<div class="bg closeBtn"></div>
	<div class="content">
		<div class="Title">
			<h4>系统提示</h4>
			<a href="javascript:;" class="closeBtn">x</a>
		</div>
		<div class="text">
			<p id="errmsg">提交成功！处理中...</p>
		</div>
	</div>
</div>
<div class="container">
<div class="logo_mm">
	<h1 class="fl"><a href="javascript:void(0)"><img src="/images/index/logo.png" alt="中国邮政速递物流" /></a></h1>
</div>
<div class="navbtn">
	<div class="wrapper">
		<p><a href="/default/index/login/">登陆</a> | <a href="/default/register/">注册</a></p>
	</div>
</div>
	<div class="layer_content">
		<div class="mmtitle">
			<h3>重置密码</h3>
		</div>
		<form method="post" id="ec_findpwd" action="" class="forgetForm">
			<ul class="inpList">
				<li>
					<input type="text"  placeholder="账 户" name="fp_user_name"/>
				</li>
				<li>
					<input type="text" placeholder="Email" name="fp_email"/>
				</li>
			</ul>
			<div class="Btns">
				<button type="button" id="findBtn">确认</button>
			</div>
		</form>
	</div>

</div>
<div class="footer forgetfoot">
	<div class="wrapper oflr">
			<p>Copyright 2014 © 中国邮政速递物流 All Rights Reservedf</p>
	</div>
</div>
</body>
<script>
$(function(){
	$('#findBtn').click(function(){
            var param = $('#ec_findpwd').serialize();
            $(".pop_up").fadeIn("fast");
        	$.ajax({
        		type: "POST",
//        		async: false,
        		dataType: "json",
        		url: '/default/index/password-recovery',
        		data: param,
        		success: function (json) {
        		    $("#errmsg").html(json.message);
        		}
        	});
        	

        })
      $(".closeBtn").click(function(){
      		$(".pop_up").fadeOut("fast");
      });

})
</script>
</html>