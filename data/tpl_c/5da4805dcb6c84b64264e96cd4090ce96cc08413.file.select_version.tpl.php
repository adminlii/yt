<?php /* Smarty version Smarty-3.1.13, created on 2016-05-05 09:23:33
         compiled from "D:\yt\application\modules\default\views\default\select_version.tpl" */ ?>
<?php /*%%SmartyHeaderCode:13129572aa095e784c8-11857200%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5da4805dcb6c84b64264e96cd4090ce96cc08413' => 
    array (
      0 => 'D:\\yt\\application\\modules\\default\\views\\default\\select_version.tpl',
      1 => 1461722414,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13129572aa095e784c8-11857200',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'system_title' => 0,
    'authCode' => 0,
    'logo' => 0,
    'successUserCode' => 0,
    'errMsg' => 0,
    'trackErrMsg' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_572aa095f1c5f8_21530132',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_572aa095f1c5f8_21530132')) {function content_572aa095f1c5f8_21530132($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'D:\\yt\\libs\\Smarty\\plugins\\block.t.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<!--
<meta http-equiv="x-ua-compatible" content="ie=7" />
 -->
<title><?php echo $_smarty_tpl->tpl_vars['system_title']->value;?>
</title>
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
	<?php if (isset($_smarty_tpl->tpl_vars['authCode']->value)&&$_smarty_tpl->tpl_vars['authCode']->value=='1'){?>
	<?php }?>

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
			<a href="/"><img src="<?php echo $_smarty_tpl->tpl_vars['logo']->value;?>
" alt="" style='width:100%;height:60%;'/></a>
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

<div class="login_content">
	<div class="login_main">
		<div class="loginTab_cancel">
			<ul style="text-align: center;">
				<li class="loginTabLi chooseTag" show-data="erp-login">
					<a href="javascript:;"></a>
				</li>
			</ul>
		</div>
		<div class="login_box">
			<!-- 普及版登陆 -->
			<div id="erp-login" class="option-module">
				<div class="login_fill">
					<div style="padding-left: 40px; color: #1B9301; font-weight: bold;">
						<?php if (isset($_GET['register'])&&$_GET['register']=='1'){?>
							<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
logo_account<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：<span style="color: #666666;"><?php echo $_GET['user_code'];?>
</span> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
register_success_01<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

						<?php }elseif(isset($_smarty_tpl->tpl_vars['successUserCode']->value)){?>
							<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
logo_account<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：<span style="color: #666666;"><?php echo $_smarty_tpl->tpl_vars['successUserCode']->value;?>
</span> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
register_email_verify<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

						<?php }?>
					</div>
					<div style="padding-left: 40px; color: red; font-weight: bold;"><?php if (isset($_smarty_tpl->tpl_vars['errMsg']->value)){?><?php echo $_smarty_tpl->tpl_vars['errMsg']->value;?>
<?php }?></div>
					<form method="post" id="ec_login" action="/default/index/login">
						<table width="95%" border="0" cellpadding="0" cellspacing="0">
							<tr style="height:30px;">
								<td height="40" style="text-align: right;width: 14%;border:1px solid #016f50;border-right:none;">
									<img align="absmiddle" src="/images/login/username_img.png" style='width:30px;height:47px;margin-right:7px;margin-top:-2px;'>
								</td>
								<td style="width: 60%;border:1px solid #016f50;border-left:none;">
									<input name="userName" autocomplete="off" maxlength="50" type="text" class="login_input" id="userName" placeholder="用户名" />
								</td>
								<td style="color: #969696;"></td>
							</tr>
							<tr style="height:10px;"></tr>
							<tr style="height:30px;top:10px;">
								<td height="46" style="text-align: right;width: 10%;border:1px solid #016f50;border-right:none;">
									<img align="absmiddle" src="/images/login/password_img.png" style='width:30px;height:47px;margin-right:7px;margin-top:-2px;'>
								</td>
								<td style="width: 60%;border:1px solid #016f50;border-left:none;">
									<input name="userPass" autocomplete="off" type="password" class="login_input" id="userPass" placeholder="密  码" />
									<!-- <a href='javascript:;' id="forgetPassword"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
forget_password<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a> -->
								</td>
								<td style="width: 30%; color: #969696;"></td>
							</tr>
							<?php if (isset($_smarty_tpl->tpl_vars['authCode']->value)&&$_smarty_tpl->tpl_vars['authCode']->value=='1'){?>
							<tr>
								<td height="46" style="text-align: right;">
									<span style="color: red">*</span>
									<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
verification_code<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：
								</td>
								<td>
									<input name="authCode" autocomplete="off" style="width: 80px" type="text" class="login_input" id="authCode" />
									<label class="verifyCode"> <img id="verifyCode" align="absmiddle" src="/default/index/verify-code" width=72 height=23> &nbsp; &nbsp; <a href="javascript:void(0);" onclick="verc();">看不清？换一张</a></label>
								</td>
								<td style="width: 30%; color: #969696;"></td>
							</tr>
							<?php }?>
							<tr>
								<td height="60" style="text-align: right;"></td>
								<td>
									<input type="submit" id="login" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
logoing<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="login_Btn" style="width:120px;border-radius:10px;margin-left:30px;" />
									<!-- &nbsp;&nbsp;<a href='/default/register/'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
register<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a> -->
									
								</td>
							</tr>
						</table>
					</form>
				</div>
				<!--<div class="login_news" style=''>
				    <h3 style='line-height:30px;font-weight:normal;font-size:14px;'>订单追踪</h3>
					<form action="/default/index/get-track-detail" method='post'> 
					    <textarea rows="" cols="" style='width:300px;height:70px;overflow:auto;font-size:13px;' placeholder='如需查询多个单号，请使用","隔开' name='code'></textarea>
					    <label>
					    <input type="text" class="input_text keyToSearch" name="authCode" style='width:70px;' placeholder='验证码'/>
						<img align="absmiddle" src="/default/index/verify-code" style='width:72px;height:23px;cursor:pointer;' class='verifyCode' onclick='verc();'></label>
						<input type="submit" class="baseBtn submitToSearch" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
查询<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
">
					    <p><?php if ($_smarty_tpl->tpl_vars['trackErrMsg']->value){?><?php echo $_smarty_tpl->tpl_vars['trackErrMsg']->value;?>
<?php }?></p>
					</form>
				</div>-->
				<div class="login_left_img" style='width:400px;'>
					<p>中邮E速宝，想你所想，为你送达</p>
					<img align="absmiddle" src="/images/login/login_left_img.png" style='width:421px;height:408px;'>

				</div>
			</div>
		</div>
		<div id="footer_box">
			<div class="footer">
				<img align="absmiddle" src="/images/login/esb.png" style='width:142px;height:74px;margin-left:429px;'>
			</div>
		</div>
		<div id="ec-forget_password-dialog" style="display: none;" title="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
password_recovery<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
">
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
							<td style="text-align: right;"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
user_name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:</td>
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
	</div>
</body>
</html><?php }} ?>