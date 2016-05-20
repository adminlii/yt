<?php /* Smarty version Smarty-3.1.13, created on 2016-05-06 20:05:02
         compiled from "D:\yt1\application\modules\default\views\default\header1.tpl" */ ?>
<?php /*%%SmartyHeaderCode:32090572c87ead33227-80637314%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f043efeebb3a7848f01e10c092214d732137dd9f' => 
    array (
      0 => 'D:\\yt1\\application\\modules\\default\\views\\default\\header1.tpl',
      1 => 1462536301,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '32090572c87ead33227-80637314',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_572c87ead6dbb7_69078928',
  'variables' => 
  array (
    'userId' => 0,
    'lang' => 0,
    'user' => 0,
    'logout' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_572c87ead6dbb7_69078928')) {function content_572c87ead6dbb7_69078928($_smarty_tpl) {?><script type="text/javascript">
EZ.userId=<?php echo $_smarty_tpl->tpl_vars['userId']->value;?>
;
EZ.lang='<?php echo $_smarty_tpl->tpl_vars['lang']->value;?>
';
</script>
<div class="head">
    		<h2>在线订单</h2>
    		<ul class="addfader">
    			<li class="userhead">
    				<p><img src="/images/imgren.jpg" alt="" /></p>
    				<p class="username"><?php echo $_smarty_tpl->tpl_vars['user']->value['user_name'];?>
</p>
    				<div class="openWindw">
		    			<a href="/?LANGUAGE=zh_CN">简体中文</a>
		    			<a href="/?LANGUAGE=zh_EN">English</a>
		    		</div>
    			</li>
    			<li class="message">
    				<a href=""><span></span><!-- <em>11</em> --></a>
    			</li>
    			<li class="logout">
    				<a href="<?php echo $_smarty_tpl->tpl_vars['logout']->value;?>
"></a>
    			</li>
    		</ul>
    	</div><?php }} ?>