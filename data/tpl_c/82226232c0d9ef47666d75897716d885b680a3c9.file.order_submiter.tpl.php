<?php /* Smarty version Smarty-3.1.13, created on 2016-04-25 15:31:57
         compiled from "F:\yt20160425\application\modules\order\views\order\order_submiter.tpl" */ ?>
<?php /*%%SmartyHeaderCode:19977571dc7ed625960-07961206%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '82226232c0d9ef47666d75897716d885b680a3c9' => 
    array (
      0 => 'F:\\yt20160425\\application\\modules\\order\\views\\order\\order_submiter.tpl',
      1 => 1457516431,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '19977571dc7ed625960-07961206',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'submiters' => 0,
    's' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_571dc7ed6779f0_70181071',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_571dc7ed6779f0_70181071')) {function content_571dc7ed6779f0_70181071($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'F:\\yt20160425\\libs\\Smarty\\plugins\\block.t.php';
?><div class='shipper_account'>
	<?php  $_smarty_tpl->tpl_vars['s'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['s']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['submiters']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['s']->key => $_smarty_tpl->tpl_vars['s']->value){
$_smarty_tpl->tpl_vars['s']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['s']->key;
?>
	<p>
		<label> <input type='radio' name='consignee[shipper_account]' class='shipper_account' value='<?php echo $_smarty_tpl->tpl_vars['s']->value['shipper_account'];?>
'<?php if ($_smarty_tpl->tpl_vars['s']->value['is_default']){?>checked<?php }?>> 
		<?php echo $_smarty_tpl->tpl_vars['s']->value['shipper_company'];?>
&nbsp;&nbsp;&nbsp;&nbsp; 
		<?php echo $_smarty_tpl->tpl_vars['s']->value['shipper_countrycode'];?>
&nbsp;&nbsp;&nbsp;&nbsp; 
		<?php echo $_smarty_tpl->tpl_vars['s']->value['shipper_province'];?>
 <?php echo $_smarty_tpl->tpl_vars['s']->value['shipper_city'];?>
 <?php echo $_smarty_tpl->tpl_vars['s']->value['shipper_street'];?>
&nbsp;&nbsp;&nbsp;&nbsp; 
		<?php echo $_smarty_tpl->tpl_vars['s']->value['shipper_name'];?>
&nbsp;&nbsp;&nbsp;&nbsp; 
		<?php echo $_smarty_tpl->tpl_vars['s']->value['shipper_telephone'];?>
&nbsp;&nbsp;&nbsp;&nbsp;
		</label>
		<a onclick='submiterOp(this);' href='javascript:;' class='submiterOpBtn' shipper_account='<?php echo $_smarty_tpl->tpl_vars['s']->value['shipper_account'];?>
'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
修改信息<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
	</p>
	<?php } ?>
</div>
<div>
	<a onclick='submiterOp(this);' href='javascript:;' style='font-weight: normal; font-size: 12px; line-height: 35px; margin: 0 0px;' class='submiterOpBtn' shipper_account=''><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
点击添加<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
</div>
<script type="text/javascript">
function submiterOp(obj){
	var shipper_account= $(obj).attr('shipper_account');
    var title = '';
    if(shipper_account){
        title = "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
修改发件人<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
";
    }else{
        title = "<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
新增发件人<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
";
    }
    var date = new Date();
	var url = "/order/submiter/for-order?shipper_account="+shipper_account+"&t="+date.getMinutes();
	//alert(0);
    //leftMenu('submiter-edit-'+shipper_account,title,url); 
    openIframeDialogNew(url,800 , 500,title,'submiter-edit-'+shipper_account,0,0);
}

$(function() {



});
</script>
<?php }} ?>