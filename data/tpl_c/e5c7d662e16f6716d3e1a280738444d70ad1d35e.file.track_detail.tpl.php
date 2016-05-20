<?php /* Smarty version Smarty-3.1.13, created on 2016-04-27 14:04:58
         compiled from "F:\yt20160425\application\modules\order\views\track\track_detail.tpl" */ ?>
<?php /*%%SmartyHeaderCode:277735720568aa5c1b9-68252246%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e5c7d662e16f6716d3e1a280738444d70ad1d35e' => 
    array (
      0 => 'F:\\yt20160425\\application\\modules\\order\\views\\track\\track_detail.tpl',
      1 => 1457516431,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '277735720568aa5c1b9-68252246',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'user' => 0,
    'trackErrMsg' => 0,
    'rsArr' => 0,
    'rs_data' => 0,
    'rs' => 0,
    'd' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_5720568c480772_18382590',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5720568c480772_18382590')) {function content_5720568c480772_18382590($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'F:\\yt20160425\\libs\\Smarty\\plugins\\block.t.php';
?><script type="text/javascript">
<!--
function verc() {
    $('.verifyCode').attr('src', '/default/index/verify-code/' + Math.random());
}
$(function(){
	
        $(".tab").live('click',function(){
            $(".tabContent", "#dialog-auto-alert-tip").hide();
            $(this).parent().removeClass("chooseTag");
            $(this).parent().siblings().addClass("chooseTag");
            $("#"+$(this).attr("id").replace("tab_",""), "#dialog-auto-alert-tip").show();
        });
})

	
//-->
</script>
<div style='padding: 8px;' class='nbox_c'>
	<div class="ntitle">
		<span style="">
			<h2 class="ntitle" style="padding-left: 0px;">订单追踪</h2>
		</span>
	</div>
	<form action="/default/index/get-track-detail" method='post'>
		<textarea name="code" rows="" cols="" style='width: 90%; height: 70px; overflow: auto; font-size: 13px;' placeholder='如需查询多个单号，请使用","隔开(最多支持20个单号同时查询)'></textarea>
		<div style='height: 5px;'></div>
		<?php if (!$_smarty_tpl->tpl_vars['user']->value){?>
        <input type="text" class="input_text keyToSearch" name="authCode" style='width:70px;' placeholder='验证码'/>
    	<img align="absmiddle" src="/default/index/verify-code" style='width:72px;height:23px;cursor:pointer;' class='verifyCode' onclick='verc();'></label>
    	<?php }?>
		<input type="submit" class="baseBtn submitToSearch" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
查询<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"><span class='msg' style='color:red;padding:0 15px;'><?php if ($_smarty_tpl->tpl_vars['trackErrMsg']->value){?><?php echo $_smarty_tpl->tpl_vars['trackErrMsg']->value;?>
<?php }?></span>
	</form>
</div>
<div id="module-table">
	<table cellspacing="0" cellpadding="0" width="100%" border="0" class="table-module">
		<tbody>
			<tr class="table-module-title">
				<td width="175"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
服务商单号<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				<!-- <td width="165"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
客户单号<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td> -->
				<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
目的地<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
收件人<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				<td width="135"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
发生时间<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
发生地点<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
最新状态<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
转运记录<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
			</tr>
		</tbody>
		<tbody id="table-module-list-data">
			<?php if ($_smarty_tpl->tpl_vars['rsArr']->value){?> <?php  $_smarty_tpl->tpl_vars['rs_data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['rs_data']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['rsArr']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['rs_data']->key => $_smarty_tpl->tpl_vars['rs_data']->value){
$_smarty_tpl->tpl_vars['rs_data']->_loop = true;
?> <?php if ($_smarty_tpl->tpl_vars['rs_data']->value['ask']){?> <?php $_smarty_tpl->tpl_vars['rs'] = new Smarty_variable($_smarty_tpl->tpl_vars['rs_data']->value['data'], null, 0);?>
			<tr class="table-module-b1">
				<td><?php echo $_smarty_tpl->tpl_vars['rs']->value['server_hawbcode'];?>
</td>
				<!-- 
				<td><?php echo $_smarty_tpl->tpl_vars['rs']->value['shipper_hawbcode'];?>
</td>
				 -->
				<td><?php echo $_smarty_tpl->tpl_vars['rs']->value['country_code'];?>
</td>
				<td><?php echo $_smarty_tpl->tpl_vars['rs']->value['signatory_name'];?>
</td>
				<td><?php echo $_smarty_tpl->tpl_vars['rs']->value['new_track_date'];?>
</td>
				<td><?php echo $_smarty_tpl->tpl_vars['rs']->value['new_track_location'];?>
</td>
				<td><?php echo $_smarty_tpl->tpl_vars['rs']->value['new_track_comment'];?>
</td>
				<td>
					<a href="javascript:;" onclick='alertTip($("#track_<?php echo $_smarty_tpl->tpl_vars['rs']->value['tbs_id'];?>
").html(),800,400);'>查看</a>
				</td>
			</tr>
			<tr style='display: none;'>
				<td colspan='8' id='track_<?php echo $_smarty_tpl->tpl_vars['rs']->value['tbs_id'];?>
'>
					<div class="depotTab2">
						 <ul>
			                <li>
			                    <a hhref="javascript:void(0);" id='tab_cn_<?php echo $_smarty_tpl->tpl_vars['rs']->value['tbs_id'];?>
' class='tab' style="cursor:pointer">中文</a>
			                </li>
			                <li class="chooseTag">
			                    <a href="javascript:void(0);" id="tab_en_<?php echo $_smarty_tpl->tpl_vars['rs']->value['tbs_id'];?>
" class="tab" style="cursor:pointer">English</a>
			                </li>
			             </ul>
		            </div>
		            <div class="tabContent" id="cn_<?php echo $_smarty_tpl->tpl_vars['rs']->value['tbs_id'];?>
" style="display:block;">
						<table cellspacing="0" cellpadding="0" width="100%" border="0" class="table-module">
							<caption style='text-align: left;line-height:30px;'><!-- <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
服务商<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：<?php echo $_smarty_tpl->tpl_vars['rs']->value['product_code'];?>
&nbsp;&nbsp;&nbsp;&nbsp; --> <?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
服务商单号<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:<?php echo $_smarty_tpl->tpl_vars['rs']->value['server_hawbcode'];?>
</caption>
							<tbody>
								<tr class="table-module-title">
									<td width="135"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
发生时间<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
									<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
发生地点<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
									<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
轨迹内容<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
								</tr>
							</tbody>
							<?php  $_smarty_tpl->tpl_vars['d'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['d']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['rs']->value['detail']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['d']->key => $_smarty_tpl->tpl_vars['d']->value){
$_smarty_tpl->tpl_vars['d']->_loop = true;
?>
							<tr>
								<td width='135'><?php echo $_smarty_tpl->tpl_vars['d']->value['track_occur_date'];?>
</td>
								<td width='300'><?php echo $_smarty_tpl->tpl_vars['d']->value['track_area_description'];?>
</td>
								<td><?php echo $_smarty_tpl->tpl_vars['d']->value['track_description'];?>
</td>
							</tr>
							<?php } ?>
						</table>
					</div>
		            <div class="tabContent" id="en_<?php echo $_smarty_tpl->tpl_vars['rs']->value['tbs_id'];?>
" style="display:none;">
						<table cellspacing="0" cellpadding="0" width="100%" border="0" class="table-module">
							<caption style='text-align: left;line-height:30px;'>Code:<?php echo $_smarty_tpl->tpl_vars['rs']->value['server_hawbcode'];?>
</caption>
							<tbody>
								<tr class="table-module-title">
									<td width="135">Time</td>
									<td>Place</td>
									<td>Content</td>
								</tr>
							</tbody>
							<?php  $_smarty_tpl->tpl_vars['d'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['d']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['rs']->value['detail']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['d']->key => $_smarty_tpl->tpl_vars['d']->value){
$_smarty_tpl->tpl_vars['d']->_loop = true;
?>
							<tr>
								<td width='135'><?php echo $_smarty_tpl->tpl_vars['d']->value['track_occur_date'];?>
</td>
								<td width='300'><?php echo $_smarty_tpl->tpl_vars['d']->value['track_area_description_en'];?>
</td>
								<td><?php echo $_smarty_tpl->tpl_vars['d']->value['track_description_en'];?>
</td>
							</tr>
							<?php } ?>
						</table>
					</div>
				</td>
			</tr>
			<?php }else{ ?>
			<tr class="table-module-b1">
				<td colspan='8'><?php echo $_smarty_tpl->tpl_vars['rs_data']->value['server_hawbcode'];?>
<?php echo $_smarty_tpl->tpl_vars['rs_data']->value['message'];?>
</td>
			</tr>
			<?php }?> <?php } ?> <?php }?>
		</tbody>
	</table>
</div><?php }} ?>