<?php /* Smarty version Smarty-3.1.13, created on 2016-05-05 09:24:55
         compiled from "D:\yt\application\modules\default\views\default\header_new.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6592572aa0e7433a51-07676108%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ed1d80527a871759294253d4399586af46fc68f5' => 
    array (
      0 => 'D:\\yt\\application\\modules\\default\\views\\default\\header_new.tpl',
      1 => 1457516431,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6592572aa0e7433a51-07676108',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'userId' => 0,
    'lang' => 0,
    'user' => 0,
    'logout' => 0,
    'logo' => 0,
    'menu' => 0,
    'submenu1' => 0,
    'submenu' => 0,
    'sub' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_572aa0e751e083_36688716',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_572aa0e751e083_36688716')) {function content_572aa0e751e083_36688716($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'D:\\yt\\libs\\Smarty\\plugins\\block.t.php';
?><script type="text/javascript">
EZ.userId=<?php echo $_smarty_tpl->tpl_vars['userId']->value;?>
;
EZ.lang='<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
';
</script>
<script type="text/javascript">
	$(function(){

		/**
		 * 用户信息设置打开
		 */
		$("#user_info_sys").click(function(){
			$(this).next().show();
		});

		$("#user_info_sys_menu > li").click(function(){
			closeUserInfoMenu();
		});
		/**
		 * 用户信息小面版监听，没有在面板上点击时，关闭面板
		 */
		$(document).bind("click",function(e){
			
			var target = $(e.target);
			//当前鼠标点击坐标是否在ul展示框内，如果在展示框内则不隐藏
			var inUserMenuUl = target.closest('ul[id^="user_info_sys_menu"]').length > 0;
			var inUserMenuA = target.closest('a[id^="user_info_sys"]').length > 0;
			
			var user_info_sys_menu_bol = $("#user_info_sys_menu").is(":visible");
			if(!inUserMenuUl && !inUserMenuA && user_info_sys_menu_bol){
				closeUserInfoMenu();
			}
		});

		/**
		 * 初始化快捷菜单
		 */
		initShortcutMenu();
	});

	/**
	 * 关闭用户信息面板
	 */
	function closeUserInfoMenu(){
		$("#user_info_sys_menu").hide();
	}
	
	/**
	 * 设置快捷菜单链接，靠齐位置
	 */
	function initShortcutMenu(){
		//设置菜单位置
		var headHelp_width = $(".headHelp").width();
		$(".headQuickNav").css("right",headHelp_width + 25);
	}

	// 登录ERP
	function loginErp(){
		$.post('/default/index/get-erp-login-token', function (json) {
			if (!isJson(json)) {
                return;
            }
			
            var form = $("<form>")
            	.attr('style', 'display:none')
            	.attr("target", '_blank')
            	.attr("method", "post")
            	.attr("action", json.url);
            form.append("<input type='text' name='access_token' value='" + json.access_token + "'/>");
        	
            $('body').append(form);
            form.submit();
            form.remove();
            
        }, 'json');
	}
</script>
<script type="text/javascript" src="/js/menu.js?20140404"></script>
<style>
	<!--
	.logo2{background:none;}
	-->
</style>	
<div id="head">
	    
	<div class="headProduct">
		<span style="width: 80%;float: left;text-align: center;">
			<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
oms_system<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

		</span>
		<i class="icon-caret-down"></i>
	</div>
	<!-- 
	<div class="headQuickNav">
		<ul>
			<li style="">
				<a href="javascript:;" onclick="loginErp();"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
在线ERP<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
			</li>
		</ul>
	</div>
	 -->
	<div class="headHelp" style="float: right; position: absolute; right: 10px;">
		<ul class="nav ace-nav pull-right">
			<!-- 任务栏 -->
			<li class="grey" style="display: none;">
				<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
					<i class="icon-tasks"></i>
					<span class="badge badge-grey">0</span>
				</a>
				
			</li>
			
			<!-- 通知 -->
			<li class="purple" style="display: none;">
				<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
					<i class="icon-bell-alt icon-animated-bell"></i>
					<span class="badge badge-important">8</span>
				</a>
				
			</li>
			
			<!-- 短消息 -->
			<li class="green" style="display: none;">
				<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
					<i class="icon-envelope icon-animated-vertical"></i>
					<span class="badge badge-success">5</span>
				</a>
				 
			</li>
			
			<li class="light-blue">
				<a id="user_info_sys" class="dropdown-toggle" href="javascript:;" data-toggle="dropdown">
					<img alt="User Photo" src="/images/modeling/user.png" width="30px" height="30px" class="nav-user-photo">
					<span class="user-info" title="<?php echo $_smarty_tpl->tpl_vars['user']->value['user_name'];?>
">
						<small>Welcome,</small>
						<?php echo $_smarty_tpl->tpl_vars['user']->value['user_name'];?>
</br>
						<?php echo $_smarty_tpl->tpl_vars['user']->value['csi_customer']['customer_shortname'];?>
(<?php echo $_smarty_tpl->tpl_vars['user']->value['csi_customer']['customer_code'];?>
)
					</span>
					<i class="icon-caret-down" style="margin-top: -8px;"></i>
				</a>
				<!-- 账户信息 -->
				<ul id="user_info_sys_menu" class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-closer">
					<li>
						<a href="/?LANGUAGE=zh_CN">
							<i class="icon-exchange" style="line-height: 19px;"></i>
							简体中文
						</a>
					</li>
					<li>
						<a href="/?LANGUAGE=en_US">
							<i class="icon-exchange" style="line-height: 19px;"></i>
							English
						</a>
					</li>
					<!-- 个人信息，暂时屏蔽 -->
					<!-- 
					<li>
						<a onclick="leftMenu('user_set_1024','<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
personal<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
','/auth/user/user-set')" class="sub-menu-id-user_set_1024" href="javascript:;">
							<i class="icon-user" style="line-height: 19px;"></i>
							<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
personal<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

						</a>
					</li>
					 -->
					<!-- 关于易仓 
					<li>
						<a class="sub-menu-id-user_set_1024" href="http://www.eccang.com" target="_blank">
							<i class="icon-user" style="line-height: 19px;"></i>
							<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
关于易仓<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

						</a>
					</li>
 					-->
					<li class="divider"></li>

					<li>
						<a href="<?php echo $_smarty_tpl->tpl_vars['logout']->value;?>
">
							<i class="icon-off" style="line-height: 19px;"></i>
							Logout
						</a>
					</li>
				</ul>
			</li>
		</ul>
	</div>
</div>

<div id="column">
	<div class="logo2" onclick="leftMenu('0','<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
home<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
','/system/home')">
		<a href="javascript:void(0);"><img src="<?php echo $_smarty_tpl->tpl_vars['logo']->value;?>
" alt="" style='padding-left:5px;padding-top:2px;width:100%;height:90%;'/></a>
	</div>
	<div class="headNav">
		<?php  $_smarty_tpl->tpl_vars['submenu1'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['submenu1']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['menu']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['submenu1']->key => $_smarty_tpl->tpl_vars['submenu1']->value){
$_smarty_tpl->tpl_vars['submenu1']->_loop = true;
?>
		<ul onmouseover="showThisSubMenuHeader(this,'headmenu<?php echo $_smarty_tpl->tpl_vars['submenu1']->value['parent']['um_id'];?>
')" onmouseout="closeThisSubMenuHeader(this,'headmenu<?php echo $_smarty_tpl->tpl_vars['submenu1']->value['parent']['um_id'];?>
')" >
			<li style="font-weight: bold;">
				<a href="#"><?php echo $_smarty_tpl->tpl_vars['submenu1']->value['parent']['value'];?>
</a>
			</li>
			<?php  $_smarty_tpl->tpl_vars['submenu'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['submenu']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['submenu1']->value['item']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['submenu']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['submenu']->iteration=0;
 $_smarty_tpl->tpl_vars['submenu']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['submenu']->key => $_smarty_tpl->tpl_vars['submenu']->value){
$_smarty_tpl->tpl_vars['submenu']->_loop = true;
 $_smarty_tpl->tpl_vars['submenu']->iteration++;
 $_smarty_tpl->tpl_vars['submenu']->index++;
 $_smarty_tpl->tpl_vars['submenu']->first = $_smarty_tpl->tpl_vars['submenu']->index === 0;
 $_smarty_tpl->tpl_vars['submenu']->last = $_smarty_tpl->tpl_vars['submenu']->iteration === $_smarty_tpl->tpl_vars['submenu']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['submenu']['first'] = $_smarty_tpl->tpl_vars['submenu']->first;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['submenu']['last'] = $_smarty_tpl->tpl_vars['submenu']->last;
?> <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['submenu']['first']){?>
			<div class="topMenu" style="display: none" id="headmenu<?php echo $_smarty_tpl->tpl_vars['submenu1']->value['parent']['um_id'];?>
">
				<div class="topMenu_cover"></div>
				<?php }?>
				<!-- <ul>
					<li class="noline">
						<span><?php echo $_smarty_tpl->tpl_vars['submenu']->value['menu']['value'];?>
</span>
					</li>
					<li class="noline">&nbsp;</li>
				</ul> -->
				<?php  $_smarty_tpl->tpl_vars['sub'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['sub']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['submenu']->value['item']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['sub']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['sub']->iteration=0;
 $_smarty_tpl->tpl_vars['sub']->index=-1;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['sub']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['sub']->key => $_smarty_tpl->tpl_vars['sub']->value){
$_smarty_tpl->tpl_vars['sub']->_loop = true;
 $_smarty_tpl->tpl_vars['sub']->iteration++;
 $_smarty_tpl->tpl_vars['sub']->index++;
 $_smarty_tpl->tpl_vars['sub']->first = $_smarty_tpl->tpl_vars['sub']->index === 0;
 $_smarty_tpl->tpl_vars['sub']->last = $_smarty_tpl->tpl_vars['sub']->iteration === $_smarty_tpl->tpl_vars['sub']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['sub']['first'] = $_smarty_tpl->tpl_vars['sub']->first;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['sub']['index']++;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['sub']['last'] = $_smarty_tpl->tpl_vars['sub']->last;
?> <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['sub']['first']){?>
				<ul><?php }else{ ?> <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['sub']['index']%2==0){?>
				</ul>
				<ul>
					<?php }?> <?php }?> <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['sub']['index']%2==0){?>
					<li class="title"><?php }else{ ?>
					<li class="title">
						<?php }?>
						<a href="javascript:void(0);" class="sub-menu-id-<?php echo $_smarty_tpl->tpl_vars['sub']->value['ur_id'];?>
" onclick="leftMenu('<?php echo $_smarty_tpl->tpl_vars['sub']->value['ur_id'];?>
','<?php echo $_smarty_tpl->tpl_vars['sub']->value['value'];?>
','<?php echo $_smarty_tpl->tpl_vars['sub']->value['ur_url'];?>
?quick=<?php echo $_smarty_tpl->tpl_vars['sub']->value['ur_id'];?>
')"><?php echo $_smarty_tpl->tpl_vars['sub']->value['value'];?>
</a>
					</li>
					<?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['sub']['last']){?> <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['sub']['index']%2==0){?>
					<li class="noline">&nbsp;</li>
					<?php }?>
				</ul>
				<?php }?> <?php } ?> <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['submenu']['last']){?>
			</div>
			<?php }?> <?php } ?>
		</ul>
		<?php } ?>
	</div>
</div>
<div class="clr" style=''></div>
<div class="main_quit">
	
</div>
<?php }} ?>