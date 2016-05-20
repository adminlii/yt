<?php /* Smarty version Smarty-3.1.13, created on 2016-04-25 15:31:22
         compiled from "F:\yt20160425\application\modules\default\views\default\right_guild.tpl" */ ?>
<?php /*%%SmartyHeaderCode:28982571dc7ca5fa9c0-86007076%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '27299d1f00523b8de5c3adf588eb788041924dfb' => 
    array (
      0 => 'F:\\yt20160425\\application\\modules\\default\\views\\default\\right_guild.tpl',
      1 => 1457516431,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '28982571dc7ca5fa9c0-86007076',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_571dc7ca6e8e73_29439976',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_571dc7ca6e8e73_29439976')) {function content_571dc7ca6e8e73_29439976($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'F:\\yt20160425\\libs\\Smarty\\plugins\\block.t.php';
?><link rel="stylesheet" type="text/css" href="/css/public/step.css">
<link rel="stylesheet" type="text/css" href="/css/public/help.css">
<script type="text/javascript" src="/js/help.js?20140128"></script>
<script type="text/javascript" src="/js/step.js?20140128"></script>
<div class="guild">
    <h2 style="cursor: pointer" id="menu0">
        <a href="javascript:void (0)" title="首页" onclick="leftMenu('0','首页','/system/home')">
            <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
home<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>

        </a>
    </h2>
    <h3 class="guild_drop">
        <a href="javascript:;" onclick="showHistory()"><img id="menuBtn" src="/images/pack/bg_guild_drop01.gif" alt="显示历史标签"></a>
        <div class="guild_drop_list" style="display: none;top:118px" id="menuList" onmouseout="showHistory()">
            <a href="javascript:void(0);" style="border-bottom:1px solid #a8ccee;" onclick="clearHis(this)">清空历史标签</a>
        </div>
    </h3>
    <span class="sys_help" title="帮助" style='display:none;'>
        <a href="javascript:void (0)" style="text-decoration:none;">
        	<strong>?</strong>
        </a>
    </span>
</div>
<!-- 帮助页面，弹出窗体 -->
<div id="sys_help_dialog" style="display:none;">
	<?php echo $_smarty_tpl->getSubTemplate ('default/views/default/help.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</div><?php }} ?>