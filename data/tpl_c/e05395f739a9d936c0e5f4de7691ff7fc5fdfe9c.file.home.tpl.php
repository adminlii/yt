<?php /* Smarty version Smarty-3.1.13, created on 2014-07-22 10:53:38
         compiled from "E:\Zend\workspaces\ruston_oms\application\modules\default\views\default\home.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2119853cdd2323741a9-90779386%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e05395f739a9d936c0e5f4de7691ff7fc5fdfe9c' => 
    array (
      0 => 'E:\\Zend\\workspaces\\ruston_oms\\application\\modules\\default\\views\\default\\home.tpl',
      1 => 1405996338,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2119853cdd2323741a9-90779386',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'warehouse' => 0,
    'k' => 0,
    'v' => 0,
    'orderStatus' => 0,
    'o' => 0,
    'receivingStatus' => 0,
    'wmsCustomer' => 0,
    'user' => 0,
    'auth' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_53cdd232510379_81080239',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53cdd232510379_81080239')) {function content_53cdd232510379_81080239($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'E:\\Zend\\workspaces\\ruston_oms\\libs\\Smarty\\plugins\\block.t.php';
?><style type="text/css">
h2.ntitle {
	line-height: 28px;
	background: #eee;
	border: 1px solid #DDDDDD;
	border-bottom: 0 none;
	padding: 0 5px;
}

.nbox_c {
	text-align: left;
	margin-bottom: 10px;
	margin-right: 10px;
}
</style>
<div class="center">
	<div style="width: 99%; height: 100%;">
		<div style="width: 100%; height: 100%; padding-left: 10px; clear: both;">
			
			<div style='width: 35%;float: left;' class='nbox_c'>
				<div class="ntitle">
					<span style="">
						<h2 class="ntitle" style="padding-left: 0px;">Orders</h2>
					</span>
					<!-- 
					<span style="">
						<select style="margin-top: 1px;" name="order" onchange="getData('order')">
							<option value="">-All-</option>
							<?php  $_smarty_tpl->tpl_vars["v"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["v"]->_loop = false;
 $_smarty_tpl->tpl_vars["k"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['warehouse']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["v"]->key => $_smarty_tpl->tpl_vars["v"]->value){
$_smarty_tpl->tpl_vars["v"]->_loop = true;
 $_smarty_tpl->tpl_vars["k"]->value = $_smarty_tpl->tpl_vars["v"]->key;
?>
							<option value="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['v']->value;?>
</option>
							<?php } ?>
						</select>
					</span>
					 -->
				</div>
				<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
					<tbody id="orderData">
						<?php  $_smarty_tpl->tpl_vars['o'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['o']->_loop = false;
 $_smarty_tpl->tpl_vars['status'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['orderStatus']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['o']->key => $_smarty_tpl->tpl_vars['o']->value){
$_smarty_tpl->tpl_vars['o']->_loop = true;
 $_smarty_tpl->tpl_vars['status']->value = $_smarty_tpl->tpl_vars['o']->key;
?>
						<tr class='table-module-b1'>
							<td width='120'><?php echo $_smarty_tpl->tpl_vars['o']->value['name'];?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['o']->value['sum'];?>
</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<div style='width: 35%;float: left;' class='nbox_c'>
				<div class="ntitle">
					<span style="">
						<h2 class="ntitle" style="padding-left: 0px;">ASN</h2>
					</span>
					<!-- 
					<span style="">
						<select style="margin-top: 1px;" name="asn" onchange="getData('asn')">
							<option value="">-All-</option>
							<?php  $_smarty_tpl->tpl_vars["v"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["v"]->_loop = false;
 $_smarty_tpl->tpl_vars["k"] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['warehouse']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["v"]->key => $_smarty_tpl->tpl_vars["v"]->value){
$_smarty_tpl->tpl_vars["v"]->_loop = true;
 $_smarty_tpl->tpl_vars["k"]->value = $_smarty_tpl->tpl_vars["v"]->key;
?>
							<option value="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['v']->value;?>
</option>
							<?php } ?>
						</select>
					</span>
					 -->
				</div>
				<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
					<tbody id="asnData">
						<?php  $_smarty_tpl->tpl_vars['o'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['o']->_loop = false;
 $_smarty_tpl->tpl_vars['status'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['receivingStatus']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['o']->key => $_smarty_tpl->tpl_vars['o']->value){
$_smarty_tpl->tpl_vars['o']->_loop = true;
 $_smarty_tpl->tpl_vars['status']->value = $_smarty_tpl->tpl_vars['o']->key;
?>
						<tr class='table-module-b1'>
							<td width='120'><?php echo $_smarty_tpl->tpl_vars['o']->value['name'];?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['o']->value['sum'];?>
</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<?php if (isset($_smarty_tpl->tpl_vars['wmsCustomer']->value)){?>
				<div style='clear: both; width: 35%;float: left;' class='nbox_c'>
					<h2 class="ntitle"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
customer_support<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2>
					<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
						<tbody>
							<tr class='table-module-b1'>
								<td width='120'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
sale_service<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
								<td>
									<?php if (isset($_smarty_tpl->tpl_vars['wmsCustomer']->value['saler'])){?><?php echo $_smarty_tpl->tpl_vars['wmsCustomer']->value['saler']['user_name'];?>
<br />
									Phone：<?php echo $_smarty_tpl->tpl_vars['wmsCustomer']->value['saler']['user_phone'];?>
 / <?php echo $_smarty_tpl->tpl_vars['wmsCustomer']->value['saler']['user_mobile_phone'];?>
<?php }?>
								</td>
							</tr>
							<tr class='table-module-b1'>
								<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
customer_service<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
								<td>
									<?php if (isset($_smarty_tpl->tpl_vars['wmsCustomer']->value['cser'])){?><?php echo $_smarty_tpl->tpl_vars['wmsCustomer']->value['cser']['user_name'];?>
<br/>
									Phone：<?php echo $_smarty_tpl->tpl_vars['wmsCustomer']->value['cser']['user_phone'];?>
 / <?php echo $_smarty_tpl->tpl_vars['wmsCustomer']->value['cser']['user_mobile_phone'];?>
<?php }?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div style='width: 35%;float: left;' class='nbox_c'>
					<h2 class="ntitle"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
account_amount<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2>
					<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
						<tbody>
							<tr class='table-module-b1'>
								<td width='120'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
account_balance<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
								<td><?php echo $_smarty_tpl->tpl_vars['wmsCustomer']->value['customer_balance']['cb_value'];?>
 <?php echo $_smarty_tpl->tpl_vars['wmsCustomer']->value['customer_currency'];?>
</td>
							</tr>
							<tr class='table-module-b1'>
								<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
account_hold<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
								<td><?php echo $_smarty_tpl->tpl_vars['wmsCustomer']->value['customer_balance']['cb_hold_value'];?>
 <?php echo $_smarty_tpl->tpl_vars['wmsCustomer']->value['customer_currency'];?>
</td>
							</tr>
						</tbody>
					</table>
				</div>
			<?php }?>
			<?php if ($_smarty_tpl->tpl_vars['user']->value['is_admin']=='1'){?>
			<div style='clear: both;width: 35%;float: left;' class='nbox_c'>
				<h2 class="ntitle">API</h2>
				<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
					<tbody>
						<tr class='table-module-b1'>
							<td width='120'>Token</td>
							<td id='api_token'><?php echo $_smarty_tpl->tpl_vars['auth']->value['app_token'];?>
</td>
						</tr>
						<tr class='table-module-b1'>
							<td>Key</td>
							<td id='api_key'><?php echo $_smarty_tpl->tpl_vars['auth']->value['app_key'];?>
</td>
						</tr>
						<tr class='table-module-b1'>
							<td>Document</td>
							<td><a target='_blank' href='/file/API-Ruston.docx'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
download<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a></td>
						</tr>
					</tbody>
				</table>
			</div>
			<?php }?>
		<div class='clear'></div>
	</div>
</div>
<script>
    $(function () {
        //getData('asn');
        //getData('order');
    });
    
    function getData(obj) {
        var type = 0;
        var wh = 0;
        var url = '';
        wh = $("[name=" + obj + "]").val();
        if (obj == 'asn') {
            type = 1;
            url = '/order/receiving/list';
        } else if (obj == 'order') {
            type = 2;
            url = '/order/order/list';
        }
        $.ajax({
            type:"POST",
            url:"/merchant/my-account/get-data",
            dataType:"json",
            data:{
                'type':type,
                'warehouse_id':wh
            },
            success:function (json) {
                if (json.ask) {
                    var html = '';
                    $.each(json.result, function (k, v) {
                        html += "<tr>";
                        html += "<td>" + k + "</td>";
                        html += "<td width='120' align='right'>" + v + "</td>";
                        html += "</tr>";
                    })
                    html += '<tr><td colspan="2" align="right"><a href="' + url + '">More</a></td></tr>';
                    $("#" + obj + "Data").html(html);
                }
            }
        });
    }

</script><?php }} ?>