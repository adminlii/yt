<?php /* Smarty version Smarty-3.1.13, created on 2016-05-06 17:06:54
         compiled from "D:\yt1\application\modules\order\views\submiter\submiter_list.tpl" */ ?>
<?php /*%%SmartyHeaderCode:17597572c5eae098941-00798603%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd3ea1c4f132b778757f6e9571c0d9f8479345696' => 
    array (
      0 => 'D:\\yt1\\application\\modules\\order\\views\\submiter\\submiter_list.tpl',
      1 => 1457516431,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17597572c5eae098941-00798603',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_572c5eae0d7159_12625419',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_572c5eae0d7159_12625419')) {function content_572c5eae0d7159_12625419($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'D:\\yt1\\libs\\Smarty\\plugins\\block.t.php';
?><script type="text/javascript">
var _url = '/order/submiter/';

/**
 * 展示发件人信息
 */
function showData(data_json){
	$(".table-module-list-data").myLoading();
	if(data_json && data_json.state == '1'){
		
		var html = '';
	    $.each(data_json.data, function (key, val) {
	        html += (val.E17 == '1')?"<tr class='table-module-b4'>":"<tr class='table-module-b1'>";
	        html += "<td>"+val.E2+"</td>";
	        html += "<td>"+val.E3+"</td>";
	        html += "<td>"+val.E4_title+"</td>";
	        html += "<td>"+val.E5 + " " + val.E6 + " " + val.E7 +"</td>";
	        html += "<td>"+val.E8+"</td>";
	        html += "<td>"+val.E10+"</td>";
	        html += "<td>";
	        //html += "<a href=\"javascript:editById(" + val.E0 + ")\">" + EZ.edit + "</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
	        html += "<a href='javascript:;' class='editShipperAccountBtn' shipper_account='"+val.E0+"'>" + EZ.edit + "</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
	        html += "<a href='javascript:;' class='delBtn' paramId='"+val.E0+"'>" + EZ.del + "</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
	        html += ((val.E17 != 1)?"<a href=\"javascript:setDefault(" + val.E0 + ")\">设为默认</a>":"<span>默认</span>");
	        html += "</td>";
	        html += "</tr>";
	    });
	    $(".table-module-list-data").html(html);
	}else{
		var html = '<tr class="table-module-b1"><td colspan="7">'+data_json.message;+'</td></tr>';
		$(".table-module-list-data").html(html);
	}
}
$(function(){
	
	$('.editShipperAccountBtn').live('click',function(){
		var shipper_account= $(this).attr('shipper_account');
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
		var url = "/order/submiter/edit?shipper_account="+shipper_account+"&t="+date.getMinutes();
		
	    openIframeDialogNew(url,800 , 500,title,'submiter-edit-'+shipper_account,0,0);
	});

	$('.delBtn').live('click',function(){
		var paramId= $(this).attr('paramId');
	    if(!window.confirm('删除？')){
			return;
	    }
	    $.ajax({
		   type: "POST",
		   url: '/order/submiter/delete',
		   data: {paramId:paramId},
		   async: true,
		   dataType: "json",
		   success: function(json){
			     if(json.state){
			    	 getData();
			     }else{
			     }
				alertTip(json.message);
		   }
		});
	});
	
})
/**
 * 查询发件人信息
 */
function getData(){
	loadStart();
	$.ajax({
		   type: "POST",
		   url: _url + "list",
		   data: {},
		   async: true,
		   dataType: "json",
		   success: function(json){
			   loadEnd('');
			   showData(json);
		   }
		});
}

/**
 * 设置默认发件人
 */
function setDefault(paramid){
	loadStart();
	var params = {};
	params['paramid'] = paramid;
	$.ajax({
		   type: "POST",
		   url: _url + "set-default",
		   data: params,
		   async: true,
		   dataType: "json",
		   success: function(json){
			   	loadEnd();
				if(json.state == '1'){
					getData();
				}
		   }
	});
}

$(function(){
	//默认查询发件人信息
	getData();

});
</script>
<div id="module-container">
	
	<div id="module-table">
		<!-- 
		<div class="Tab">
			<ul>
				<li id="normal" style="position: relative;" class="mainStatus chooseTag">
					<a href="javascript:;" class="statusTag " onclick="leftMenu('submiter_list','发件人资料','/order/submiter/list')">
						<span class="order_title">发件人资料</span>
					</a>
				</li>
				<li id="abnormal" style="position: relative;" class="mainStatus ">
					<a href="javascript:;" class="statusTag " onclick="leftMenu('user_set','修改密码','/auth/user/user-set')">
						<span class="order_title">修改密码</span>
					</a>
				</li>
			</ul>
		</div>
		 -->
		<div style='padding: 5px;'></div>
		<h2 style='margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC; line-height: 40px;'>
			<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
现有发件人信息<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
&nbsp;&nbsp;[
			<a href="javascript:getData();">刷新</a>
			]
		</h2>
		
		<a shipper_account="" class="editShipperAccountBtn baseBtn" href="javascript:;" style='float:right;margin-top:-45px;'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
新增发件人<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</a>
		<table cellspacing="0" cellpadding="0" width="100%" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td width="150">发件人</td>
					<td width="150">公司名</td>
					<td width="150">所在国家</td>
					<td width="120">发件地址</td>
					<td width="80">邮编</td>
					<td width="80">联系电话</td>
					<td width="100"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
operate<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
				</tr>
			</tbody>
			<tbody id="table-module-list-data" class="table-module-list-data">
				<tr class="table-module-b1">
					<td colspan="7">No Data</td>
				</tr>
			</tbody>
		</table>
		<div style='padding: 10px;'></div>
	</div>
</div><?php }} ?>