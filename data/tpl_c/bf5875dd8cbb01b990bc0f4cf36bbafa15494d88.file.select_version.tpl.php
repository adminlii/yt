<?php /* Smarty version Smarty-3.1.13, created on 2014-07-22 10:53:23
         compiled from "E:\Zend\workspaces\ruston_oms\application\modules\default\views\default\select_version.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2917353cdd223648400-70653601%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bf5875dd8cbb01b990bc0f4cf36bbafa15494d88' => 
    array (
      0 => 'E:\\Zend\\workspaces\\ruston_oms\\application\\modules\\default\\views\\default\\select_version.tpl',
      1 => 1405996338,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2917353cdd223648400-70653601',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'authCode' => 0,
    'successUserCode' => 0,
    'errMsg' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_53cdd22377ca28_57295546',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53cdd22377ca28_57295546')) {function content_53cdd22377ca28_57295546($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'E:\\Zend\\workspaces\\ruston_oms\\libs\\Smarty\\plugins\\block.t.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<!--
<meta http-equiv="x-ua-compatible" content="ie=7" />
 -->
<title>俄速通订单管理系统</title>
<script type="text/javascript" src="/js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="/js/json2.js"></script>
<script type="text/javascript" src="/js/jquery-cookie.js"></script>
<link href="/css/ui-lightness/jquery-ui.min.css" rel="stylesheet" type="text/css" />
<link type="text/css" rel="stylesheet" href="/css/global.css" />
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

	<?php if (isset($_smarty_tpl->tpl_vars['authCode']->value)&&$_smarty_tpl->tpl_vars['authCode']->value=='1'){?>
	function verc() {
	    $('#verifyCode').attr('src', '/default/index/verify-code/' + Math.random());
	}
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
</style>
	<div class="login_head">
		<div class="login_logo">
			<a href="http://www.ruston.cc"></a>
		</div>
		<div class="login_head_text">
			<a href="http://www.ruston.cc">首页</a>
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
							<tr>
								<td height="46" style="text-align: right;width: 20%;">
									<span style="color: red">*</span>
									<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
user_name<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：
								</td>
								<td style="width: 80%;">
									<input name="userName" autocomplete="off" maxlength="50" type="text" class="login_input" id="userName" />
								</td>
								<td style="color: #969696;"></td>
							</tr>
							<tr>
								<td height="46" style="text-align: right;">
									<span style="color: red">*</span>
									<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
user_password<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：
								</td>
								<td>
									<input name="userPass" autocomplete="off" type="password" class="login_input" id="userPass" />
									<a href='javascript:;' id="forgetPassword"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
forget_password<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
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
" class="login_Btn" />
									&nbsp;&nbsp;
									<a href='/default/register/'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
register<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
								</td>
							</tr>
						</table>
					</form>
				</div>
				<div class="login_news" style='display:none;'>
					<dl style="padding-top: 0px;">
						<dt>
							<img src="/images/login/login_news01.gif" />
						</dt>
						<dd>
							<strong>电商管理系统</strong>
							<p class="ellipsis sys_introduction">只要注册即可免费使用，限100单/天</p>
						</dd>
						<div class="clr"></div>
					</dl>
					<dl>
						<dt>
							<img src="/images/login/login_news02.gif" />
						</dt>
						<dd>
							<strong>功能全面操作简单</strong>
							<p class="ellipsis sys_introduction">对接多个电商平台，满足售后所有工作</p>
						</dd>
						<div class="clr"></div>
					</dl>
					<dl style="border-bottom: none;">
						<dt>
							<img src="/images/login/login_news03.gif" />
						</dt>
						<dd>
							<strong>为帮助B2C电商卖家而生</strong>
							<p class="ellipsis sys_introduction">专注解决您面临问题，致力于提高您的工作效率</p>
						</dd>
						<div class="clr"></div>
					</dl>
				</div>
			</div>
		</div>
		<div id="footer_box">
			<div class="footer">
				<p>关于俄速通 | 联系俄速通 | 使用条款 | 保密声明</p>
				<p>
					Copyright 2013©黑龙江俄速通国际物流有限公司
				</p>
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
</body>
</html><?php }} ?>