<?php /* Smarty version Smarty-3.1.13, created on 2016-05-10 13:55:48
         compiled from "D:\yt1\application\modules\order\views\invoice-label\invoice-label.tpl" */ ?>
<?php /*%%SmartyHeaderCode:31571573177e408fa02-20432159%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7972d367fcd26c92da90d0fcfc1c4bd82ba2d78e' => 
    array (
      0 => 'D:\\yt1\\application\\modules\\order\\views\\invoice-label\\invoice-label.tpl',
      1 => 1457516431,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '31571573177e408fa02-20432159',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'orderArr' => 0,
    'o' => 0,
    'iv' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_573177e4152f33_28963884',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_573177e4152f33_28963884')) {function content_573177e4152f33_28963884($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include 'D:\\yt1\\libs\\Smarty\\plugins\\modifier.date_format.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>打印形式发票</title>
<link href="/css/invoice.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<?php  $_smarty_tpl->tpl_vars['o'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['o']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['orderArr']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['o']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['o']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['o']->key => $_smarty_tpl->tpl_vars['o']->value){
$_smarty_tpl->tpl_vars['o']->_loop = true;
 $_smarty_tpl->tpl_vars['o']->iteration++;
 $_smarty_tpl->tpl_vars['o']->last = $_smarty_tpl->tpl_vars['o']->iteration === $_smarty_tpl->tpl_vars['o']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['o']['last'] = $_smarty_tpl->tpl_vars['o']->last;
?>
	<div class="content">
		<div style="float: right;">
			<!-- 如果是fedex产品打印fedex的形式发票格式 -->
			<img src="/default/index/barcode/code/<?php echo $_smarty_tpl->tpl_vars['o']->value['order']['server_hawbcode'];?>
">
		</div>
		<div class="title"><?php echo $_smarty_tpl->tpl_vars['o']->value['shipper_consignee']['shipper_company'];?>
</div>
		<!-- <div class="title">INDUSTRY CO LTD</div> -->

		<div class="b_from">
			<div class="b_from_content3_title">FROM:</div>
			<div class="b_from_content3_1"><?php echo $_smarty_tpl->tpl_vars['o']->value['shipper_consignee']['shipper_street'];?>
<br/><?php echo $_smarty_tpl->tpl_vars['o']->value['shipper_consignee']['shipper_city'];?>
<br/><?php echo $_smarty_tpl->tpl_vars['o']->value['shipper_consignee']['shipper_province'];?>
&nbsp;</div>
			<div class="b_from_content3">&nbsp;</div>
		</div>
		<div class="b_from">
			<div class="b_from_title_to">TO Messrs:</div>
			<div class="b_from_content_to"><?php echo $_smarty_tpl->tpl_vars['o']->value['shipper_consignee']['consignee_name'];?>
 &nbsp;</div>
		</div>
		<div class="b_from">

			<div class="b_from_content3">
				<div class="b_from_content3_title">Company:</div>
				<div class="b_from_content3_1">&nbsp;<?php echo $_smarty_tpl->tpl_vars['o']->value['shipper_consignee']['consignee_company'];?>
</div>
				<div class="b_from_content3_title">Add:</div>
				<div class="b_from_content3_1">
					<?php echo $_smarty_tpl->tpl_vars['o']->value['shipper_consignee']['consignee_street'];?>
 <?php echo $_smarty_tpl->tpl_vars['o']->value['shipper_consignee']['consignee_city'];?>
 <?php echo $_smarty_tpl->tpl_vars['o']->value['shipper_consignee']['consignee_province'];?>
&nbsp; <br /> 
					<?php echo $_smarty_tpl->tpl_vars['o']->value['shipper_consignee']['consignee_country_name'];?>

				</div>
			</div>

			<div class="b_from_content3">
				<div class="b_from_content3_title">Phone:</div>
				<div class="b_from_content3_1"><?php echo $_smarty_tpl->tpl_vars['o']->value['shipper_consignee']['consignee_telephone'];?>
&nbsp;</div>
				<div class="b_from_content3_title">Fax:</div>
				<div class="b_from_content3_1"><?php echo $_smarty_tpl->tpl_vars['o']->value['shipper_consignee']['consignee_fax'];?>
&nbsp;</div>
				<div class="b_from_content3_title">Zip_code:</div>
				<div class="b_from_content3_1"><?php echo $_smarty_tpl->tpl_vars['o']->value['shipper_consignee']['consignee_postcode'];?>
 &nbsp;</div>
				<div class="b_from_content3_title">Air Waybill NO.</div>
				<div class="b_from_content3_1">&nbsp;</div>
				<div class="b_from_content3_2"><?php echo $_smarty_tpl->tpl_vars['o']->value['order']['server_hawbcode'];?>
 &nbsp;</div>
			</div>
		</div>

	</div>

	<div class="content">
		<div class="title">PRO-FORMA INVOICE</div>
		<div>
			<br />
		</div>
		<table border="0" align="center" cellpadding="0" cellspacing="0"
			class="table">
			<tr>
				<td class="t_td">Commodities and Specifications <br />&nbsp;
				</td>
				<td class="t_td">Quantities <br />&nbsp;
				</td>
				<td class="t_td">Declare unit<br />&nbsp;
				</td>
				<td class="t_td">Unit Price <br /> (USD)
				</td>
				<td class="t_td1">Amount<br /> (USD)
				</td>
			</tr>
			<?php  $_smarty_tpl->tpl_vars['iv'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['iv']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['o']->value['invoice']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['iv']->key => $_smarty_tpl->tpl_vars['iv']->value){
$_smarty_tpl->tpl_vars['iv']->_loop = true;
?>
			<tr>
				<td class="t_td1"><?php echo $_smarty_tpl->tpl_vars['iv']->value['invoice_enname'];?>
</td>
				<td class="t_td1"><?php echo $_smarty_tpl->tpl_vars['iv']->value['invoice_quantity'];?>
</td>
				<td class="t_td1"><?php echo $_smarty_tpl->tpl_vars['iv']->value['unit_code'];?>
</td>
				<td class="t_td1"><?php echo $_smarty_tpl->tpl_vars['iv']->value['invoice_unitcharge'];?>
</td>
				<td class="t_td1"><?php echo $_smarty_tpl->tpl_vars['iv']->value['invoice_totalcharge'];?>
</td>
			</tr>
			<?php } ?>
			<tr>
				<td class="t_td2">&nbsp;</td>
				<td class="t_td2">&nbsp;</td>
				<td class="t_td2">&nbsp;</td>
				<td class="t_td2">Total Amount(USD):</td>
				<td class="t_td2"><?php echo $_smarty_tpl->tpl_vars['o']->value['order']['invoice_total'];?>
</td>
			</tr>
		</table>

	</div>

	<div class="content">
		<div class="b_from1">
			<div class="b_from_title1">REASON FOR EXPORT:</div>
		</div>
		<div class="b_from2">SAMPLE OF NO COMMERCIAL VALUE</div>
		<div class="b_from3">
			I/we hereby certify that the information on this invoice is true and
			correct<br /> and that the contents of this shipment are as stated
			above.
		</div>

		<div class="b_from">
			<div class="b_from_content3">
				SHIPPER'S SIGNATURE & WILL<br /><?php echo $_smarty_tpl->tpl_vars['o']->value['shipper_consignee']['shipper_name'];?>

			</div>
			<div class="b_from_content3">
				DATE<br /><?php echo smarty_modifier_date_format(time(),"%Y-%m-%d %H:%M:%S");?>

			</div>
		</div>

	</div>
	<?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['o']['last']){?> 
	<h1 style="page-break-after: always; clear: both;background:red;"></h1>
 	<?php }?>
	<?php } ?>

</body>
</html><?php }} ?>