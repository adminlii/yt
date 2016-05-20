<?php /* Smarty version Smarty-3.1.13, created on 2016-05-11 17:32:58
         compiled from "D:\yt1\application\modules\order\views\label-print\label-print.js" */ ?>
<?php /*%%SmartyHeaderCode:18925732fc4ab34504-97205536%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9a282de47af39ad7087578859d229f651d305e88' => 
    array (
      0 => 'D:\\yt1\\application\\modules\\order\\views\\label-print\\label-print.js',
      1 => 1457516431,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18925732fc4ab34504-97205536',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'w' => 0,
    'h' => 0,
    'title' => 0,
    'printerNo' => 0,
    'lableArr' => 0,
    'img' => 0,
    'return' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_5732fc4abc7311_80735187',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5732fc4abc7311_80735187')) {function content_5732fc4abc7311_80735187($_smarty_tpl) {?>
/**
 * 打印前，需要预先判断
 * 如LODOP是否初始化，是否设置了默认打印机，是否修改打印机
 * 
 */

var lodopCp = '北京中电亿商网络技术有限责任公司';
var lodopKey='653726081798577778794959892839';
var lodopToken = '';
function verify(){
	if(!LODOP){
        LODOP=getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM'));
    }
    return true;
}
function log(msg){
	return; 
}
function initContent(){
	//需要判断是否分页
	var w=<?php echo $_smarty_tpl->tpl_vars['w']->value;?>
;
	var h=<?php echo $_smarty_tpl->tpl_vars['h']->value;?>
;
	var left = right = 0;
	var top = 1; 
 
    var title = '<?php echo $_smarty_tpl->tpl_vars['title']->value;?>
';
    LODOP.PRINT_INITA("0mm","0mm",w+"mm",h+"mm",title);
    LODOP.SET_PRINT_PAGESIZE(0,w+'mm',h+'mm',title);
    LODOP.SET_PRINTER_INDEX(<?php echo $_smarty_tpl->tpl_vars['printerNo']->value;?>
);
    <?php  $_smarty_tpl->tpl_vars['img'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['img']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['lableArr']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['img']->key => $_smarty_tpl->tpl_vars['img']->value){
$_smarty_tpl->tpl_vars['img']->_loop = true;
?>
    LODOP.ADD_PRINT_IMAGE(0, 0, w+'mm', h+'mm', "<img border='0' src='data:image/gif;base64,<?php echo $_smarty_tpl->tpl_vars['img']->value['LableFile'];?>
'/>");
	LODOP.SET_PRINT_STYLEA(0,"Stretch",2);//按原图比例(不变形)缩放模式
	LODOP. NEWPAGE ();
    <?php } ?>
		
	LODOP.SET_PRINT_MODE("CUSTOM_TASK_NAME",title);//为每个打印单独设置任务名	
    LODOP.SET_LICENSES(lodopCp, lodopKey, lodopToken, "");
	LODOP.PRINT(); 
}

/**
 * 内容打印
 */
function lodop_print(){	
	<?php if ($_smarty_tpl->tpl_vars['return']->value['ask']){?>
	var bool = verify();
	if(!bool){
		return;
	}    
	initContent();
	<?php }else{ ?>	
	alertTip('<?php echo $_smarty_tpl->tpl_vars['return']->value['message'];?>
');
	<?php }?>
}
<?php }} ?>