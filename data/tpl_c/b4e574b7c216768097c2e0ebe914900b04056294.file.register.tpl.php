<?php /* Smarty version Smarty-3.1.13, created on 2016-04-29 10:10:27
         compiled from "F:\yt20160425\application\modules\default\views\register\register.tpl" */ ?>
<?php /*%%SmartyHeaderCode:186485722c293e4b1d3-23301613%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b4e574b7c216768097c2e0ebe914900b04056294' => 
    array (
      0 => 'F:\\yt20160425\\application\\modules\\default\\views\\register\\register.tpl',
      1 => 1457516431,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '186485722c293e4b1d3-23301613',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'system_title' => 0,
    'regMsg' => 0,
    'authCode' => 0,
    'userSources' => 0,
    'eBayEIASTokenId' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_5722c2946c18a0_83025675',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5722c2946c18a0_83025675')) {function content_5722c2946c18a0_83025675($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'F:\\yt20160425\\libs\\Smarty\\plugins\\block.t.php';
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
<link href="/css/ui-lightness/jquery-ui.min.css" rel="stylesheet" type="text/css" />
<link type="text/css" rel="stylesheet" href="/css/global.css" />
<link type="text/css" rel="stylesheet" href="/css/login/index.css" />
<style type="text/css">

.login_news,.login_head_text{display:none;}
</style>
</head>
<body>
	<script type="text/javascript">
     function verc() {
         $('#verifyCode').attr('src', '/default/index/verify-code/' + Math.random());
     }
    $(function () {
        $("#userName").focus().css("background-color", "#FFFFCC");
        $(".login_input").click(function () {
            $(".login_input").css("background-color", "#FFFFFF");
            $(this).css("background-color", "#FFFFCC");
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
        		    	this_.parent().next().html(json.message);
        		    }else{
        		    	this_.parent().next().html('账户名可注册');
        		    }
        		}
        	});
        });

        $('#user_email').change(function(){
        	var user_email=$(this).val();
        	var this_ = $(this);
        	$.ajax({
        		type: "POST",
//        		async: false,
        		dataType: "json",
        		url: '/default/register/verify-user',
        		data: {'user_email':user_email},
        		success: function (json) {
        		    if(json.ask){
        		    	this_.parent().next().html(json.message);
        		    }else{
        		    	this_.parent().next().html('邮箱可注册');
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
        		    if(json.ask){
        		        $('#message_wrap').text(json.message);
        		        setTimeout(function(){window.location.href='/default/index/login/?register=1&user_code=' + $("#user_code").val();},500);
        		    }else{
        		        $('#message_wrap').text(json.message);
        		        verc();
        		    }
        		}
        	});

        })
    });
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
			<a href="#">技术支持</a>
		</div>
	</div>
	<div class="login_main">
		<div class="loginTab">
			<ul>
				<li class="chooseTag">
					<a href="#">用户注册</a>
				</li>
			</ul>
		</div>
		<div class="login_box" style='height: auto;'>
			<div class="login_fill" style='height: auto;'>
				<div style="padding-left: 40px; color: #1B9301; font-weight: bold; font-size: 16px;">
					<?php if (isset($_smarty_tpl->tpl_vars['regMsg']->value)){?> <?php echo $_smarty_tpl->tpl_vars['regMsg']->value;?>

					<span style="font-size: 14px; color: #969696;">
						已注册用户？
						<a href='/default/index/login/'>立即登录</a>
					</span>
					<?php }?>
				</div>
				<div style="padding-left: 40px; color: red; font-weight: bold;" id='message_wrap'></div>
				<form method="post" id="ec_register" onsubmit='return false;'>
					<table width="95%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td height="46" style="text-align: right;" width='100'>
								<span style="color: red">*</span>
								账户名：
							</td>
							<td>							
	                            <!-- 
	                            <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array('a'=>'a')); $_block_repeat=true; echo smarty_block_t(array('a'=>'a'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
test<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array('a'=>'a'), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

	                             -->
								<input name="user_code" autocomplete="off" maxlength="50" type="text" class="login_input" id="user_code" />
							</td>
							<td style="color: #969696;"></td>
						</tr>
						<tr>
							<td height="46" style="text-align: right;">
								<span style="color: red">*</span>
								密码：
							</td>
							<td>
								<input name="user_password" autocomplete="off" type="password" class="login_input" id="user_password" />
							</td>
							<td style="width: 30%; color: #969696;"></td>
						</tr>
						<tr>
							<td height="46" style="text-align: right;">
								<span style="color: red">*</span>
								确认密码：
							</td>
							<td>
								<input name="user_password_confirm" autocomplete="off" type="password" class="login_input" id="user_password_confirm" />
							</td>
							<td style="width: 30%; color: #969696;"></td>
						</tr>
						<tr>
							<td height="46" style="text-align: right;">
								<span style="color: red">*</span>
								邮箱：
							</td>
							<td>
								<input name="user_email" autocomplete="off" type="text" class="login_input" id="user_email" />
							</td>
							<td style="width: 30%; color: #969696;"></td>
						</tr>
						<tr>
							<td height="46" style="text-align: right;">
								<span style="color: red">*</span>
								姓名：
							</td>
							<td>
								<input name="user_name" autocomplete="off" type="text" class="login_input" id="user_name" />
							</td>
							<td style="width: 30%; color: #969696;"></td>
						</tr>
						<tr>
							<td height="46" style="text-align: right;">
								<span style="color: red">*</span>
								公司：
							</td>
							<td>
								<input name="company_name" autocomplete="off" type="text" class="login_input" id="company_name" />
							</td>
							<td style="width: 30%; color: #969696;"></td>
						</tr>
						<tr>
							<td height="46" style="text-align: right;">
								<span style="color: red"></span>
								手机：
							</td>
							<td>
								<input name="user_mobile_phone" autocomplete="off" type="text" class="login_input" id="user_mobile_phone" />
							</td>
							<td style="width: 30%; color: #969696;"></td>
						</tr>
						<tr>
							<td height="46" style="text-align: right;">
								<span style="color: red"></span>
								电话：
							</td>
							<td>
								<input autocomplete="off" type="text" class="login_input user_phone" name="user_phone" id='user_phone' placeholder='区号-电话号-分机号' />
							</td>
							<td style="width: 30%; color: #969696;"></td>
						</tr>
						<tr>
							<td height="46" style="text-align: right;">&nbsp;</td>
							<td colspan='2'>
								<span class="f12 green" style="clear: left;"> 请填写真实的手机或联系电话，注册后客服人员将在1个工作日内与您取得联系进行信息确认以及开通账户。 </span>
							</td>
						</tr>
						<?php if (isset($_smarty_tpl->tpl_vars['authCode']->value)&&$_smarty_tpl->tpl_vars['authCode']->value=='1'){?>
						<tr>
							<td height="46" style="text-align: right;">
								<span style="color: red">*</span>
								验证码：
							</td>
							<td>
								<input name="authCode" autocomplete="off" style="width: 60px" type="text" class="login_input" id="authCode" />
								<label class="verifyCode" onclick="verc();"> <img id="verifyCode" align="absmiddle" src="/default/index/verify-code" width=72 height=23> <a href="javascript:void(0);">换一张</a></label>
							</td>
							<td style="width: 30%; color: #969696;"></td>
						</tr>
						<?php }?>
						<tr>
							<td height="60" style="text-align: right;"></td>
							<td>
								<input type="submit" id="registerBtn" value="确认注册" class="login_Btn" />
								&nbsp;&nbsp;
								<a href='/default/index/login/'>立即登录</a>
							</td>
						</tr>
					</table>
					<!-- 隐藏的平台加密token -->
					<input type="hidden" name="user_sources" value="<?php if (isset($_smarty_tpl->tpl_vars['userSources']->value)){?><?php echo $_smarty_tpl->tpl_vars['userSources']->value;?>
<?php }?>" />
					<input type="hidden" name="platform_token" value="<?php if (isset($_smarty_tpl->tpl_vars['eBayEIASTokenId']->value)){?><?php echo $_smarty_tpl->tpl_vars['eBayEIASTokenId']->value;?>
<?php }?>" />
				</form>
			</div>
			<div class="login_news" style='display:none;'>
				
				<dl style="padding-top: 0px;">
					<dt>
						<img src="/images/login/login_news01.gif" />
					</dt>
					<dd>
						<strong>免费的电商管理系统</strong>
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
			<div style='clear: both;'></div>
		</div>
		<div id="footer_box">
			<div class="footer">
				<p>关于俄速通 | 联系俄速通 | 使用条款 | 保密声明</p>
				<p>
					Copyright 2013©黑龙江俄速通国际物流有限公司
				</p>
			</div>
		</div>

</body>
</html><?php }} ?>