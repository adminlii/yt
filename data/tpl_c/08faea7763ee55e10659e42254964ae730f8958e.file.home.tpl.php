<?php /* Smarty version Smarty-3.1.13, created on 2016-04-20 17:34:14
         compiled from "D:\phpStudy\toms\application\modules\default\views\default\home.tpl" */ ?>
<?php /*%%SmartyHeaderCode:588457174d169b62e0-18601987%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '08faea7763ee55e10659e42254964ae730f8958e' => 
    array (
      0 => 'D:\\phpStudy\\toms\\application\\modules\\default\\views\\default\\home.tpl',
      1 => 1457516431,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '588457174d169b62e0-18601987',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'bulletin' => 0,
    'content' => 0,
    'level' => 0,
    'customer_code' => 0,
    'zonglan' => 0,
    'create_count' => 0,
    'create_count_today' => 0,
    'checkin_count' => 0,
    'checkin_count_today' => 0,
    'checkout_count' => 0,
    'checkout_count_today' => 0,
    'shippersupporter' => 0,
    'saller' => 0,
    'dunner' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_57174d17aac833_29994469',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57174d17aac833_29994469')) {function content_57174d17aac833_29994469($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'D:\\phpStudy\\toms\\libs\\Smarty\\plugins\\block.t.php';
?><style type="text/css">
.nbox_c {
	text-align: left;
	margin-bottom: 10px;
	margin-right: 8px;
}

.admin_task {
	border: 1px solid #d9d9d9;
	padding-bottom: 4px;
}

.admin_task h2 {
	height: 30px;
	line-height: 30px;
	padding: 0px 10px;
	background: url(../images/index/bg_index02.gif) repeat-x;
	border-bottom: 1px solid #e3e3e3;
	font-size: 12px;
	font-weight: bold;
}

.admin_task ul {
	padding-bottom: 5px;
	max-height: 410px;
	overflow: auto;
}

.admin_task ul li {
	padding-left: 10px;
	margin: 5px 3px;
	height: 38px;
	border-bottom: 1px solid #d9d9d9;
	overflow: hidden;
}

.admin_task_text {
	width: 80%;
	float: left;
	height: 38px;
	line-height: 38px;
	overflow: hidden;
}

.admin_task_operate {
	width: 60px;
	height: 38px;
	line-height: 38px;
	float: right;
	text-align: right;
	color: #999;
	overflow: hidden;
}

.admin_task_title {
	float: left;
	font-size: 12px;
	height: 20px;
	overflow: hidden;
	text-align: left;
	text-overflow: ellipsis;
	white-space: nowrap;
	width: 240px;
}

a {
	outline: none;
	blr: expression(this.onFocus = this.blur ());
}

.admin_task_time {
	float: right;
	font-size: 10px;
	height: 18px;
}

.admin_task_more {
	float: right;
	height: 18px;
	padding-right: 10px;
}

.noneLine {
	display: none;
}

.depotTab2 ul  li {
	height: 30px;
	border-left: 1px solid #d9d9d9;
	border-right: 1px solid #d9d9d9;
	border-bottom: 0px;
	padding: 0px 10px;
}

.depotTab2 ul li.chooseTag {
	padding: 0px 10px;
}

.depotTab2 {
	margin-bottom: 10px;
	height: 37px;
}

#scrollDiv {
	width: 98.5%;
	height: 25px;
	line-height: 25px;
	border: #ccc 1px solid;
	margin-left: 10px;
	overflow: hidden;
}

#scrollDiv li {
	height: 25px;
	color: #E06B26;
}

#scrollDiv a {
	color: #E06B26;
	display: block;
	padding-left: 12px;
	font-size: 15px;
}

#scrollDiv .admin_task_title {
	width: 100%;
}

h2.ntitle {
	line-height: 28px;
	background: #eee;
	border: 1px solid #DDDDDD;
	border-bottom: 0 none;
	padding: 0 10px;
}

.nbox_c {
	text-align: left;
	margin-bottom: 10px;
	margin-right: 10px;
}

.admin_task {
	border: 1px solid #d9d9d9;
	padding-bottom: 4px;
}

.admin_task h2 {
	height: 30px;
	line-height: 30px;
	padding: 0px 10px;
	background: url(../images/index/bg_index02.gif) repeat-x;
	border-bottom: 1px solid #e3e3e3;
	font-size: 12px;
	font-weight: bold;
}

.admin_task ul {
	padding-bottom: 5px;
	max-height: 410px;
	overflow: auto;
}

.admin_task ul li {
	padding-left: 10px;
	margin: 5px 3px;
	height: 38px;
	border-bottom: 1px solid #d9d9d9;
	overflow: hidden;
}

.admin_task_text {
	width: 80%;
	float: left;
	height: 38px;
	line-height: 38px;
	overflow: hidden;
}

.admin_task_operate {
	width: 60px;
	height: 38px;
	line-height: 38px;
	float: right;
	text-align: right;
	color: #999;
	overflow: hidden;
}

.admin_task_title {
	float: left;
	font-size: 12px;
	height: 20px;
	overflow: hidden;
	text-align: left;
	text-overflow: ellipsis;
	white-space: nowrap;
	width: 240px;
}
</style>

<script type="text/javascript" src="/js/modules/panel/panel.js?20140722"></script>
<script type="text/javascript"> 

//自动滚动
function AutoScroll(obj) {

    $(obj).find("ul:first").animate({
        marginTop: "-25px"
    },400, function() {
        $(this).css({ marginTop: "0px" }).find("li:first").appendTo(this);
    });
}

$(function(){
	loadVersions($('#main_right'),'Notice',0);
	 clearInterval(myar);
	//追加点击事件
    $(".tab").click(function(){
         $(".tabContent").hide();

         $(this).parent().removeClass("chooseTag");
         $(this).parent().siblings().addClass("chooseTag");
         $(".versions-list-data-li").remove();
         loadVersions($('#main_right'),$(this).attr("id"),1);
     }); 
	//开始文字滚动
    var myar = setInterval('AutoScroll("#scrollDiv")', 2500);
    $("#scrollDiv").hover(function() {
    	             clearInterval(myar);
    	             }, 
    	    function() { 
    	             myar = setInterval('AutoScroll("#scrollDiv")', 2500); 
    		    }); 
});

//公告,弹出显示
var bulletinJson = <?php echo $_smarty_tpl->tpl_vars['bulletin']->value;?>
;
if (bulletinJson.state) {
	var currentNumber = 1;
	var totalList = bulletinJson.total;
	var html = '';
    html += '<div title="公告" id="dialog-auto-alertConfirm-tip">';
    html += '<table width="100%" border="0" cellpadding="0" cellspacing="0">';
    $.each(bulletinJson.data, function (key, val) {
    	html += '<tr number="'+ (key+1) +'" '+ (key+1==currentNumber ? '' : 'style="display:none;"') +'>';
    	html += '<td>';
    	html += '<p class="ec-center"><b>' + val.v_title + '</b></p>';
        html += '<p><hr/><p>';
        html += '<p id="bulletinContent" style="text-indent:25px;">' + val.v_content + '<p>';
        html += '<p style="text-align:right;">'+ val.v_published +'</p>';
        html += '</td>';
        html += '</tr>';
    });
    html += '</table>';
    html += '</div>';
    $(html).dialog({
        autoOpen: true,
        width: 800,
        maxHeight: 700,
        modal: true,
        show: "slide",
        buttons: [
			{
			    text: '上一条(Prev)',
			    id: 'bulletinPrev',
			    click: function () {
			    	currentNumber--;
			    	autoButton();
			        $("#dialog-auto-alertConfirm-tip").find("tr").hide();
			        $("#dialog-auto-alertConfirm-tip").find("tr[number="+currentNumber+"]").show();
			    }
			},
            {
                text: '下一条(Next)',
                id: 'bulletinNext',
                click: function () {
                	currentNumber++;
                	autoButton();
                	$("#dialog-auto-alertConfirm-tip").find("tr").hide();
                	$("#dialog-auto-alertConfirm-tip").find("tr[number="+currentNumber+"]").show();
                }
            }
        ],
        open: function () {
        	$("#bulletinPrev").hide();
        	$("#bulletinNext").show();
        },
        close: function () {
            $(this).detach();
        }
    });
}

//重置“上一条”、“下一条”按钮
function autoButton(){
	$("#bulletinPrev").hide();
	$("#bulletinNext").hide();
	if (totalList > 1) {
    	if (currentNumber == 1) {
    		$("#bulletinNext").show();
    	} else if (currentNumber == totalList) {
    		$("#bulletinPrev").show();
    	} else {
    		$("#bulletinNext").show();
    		$("#bulletinPrev").show();
    	}
    }
}
</script>
<div class="center">
	<div style="width: 99%; height: 100%;">
		<div><?php echo $_smarty_tpl->tpl_vars['content']->value;?>
</div>
		<fieldset>
			<legend>操作流程</legend>
			<div>

				1、开始-><a href='javascript:;'>创建订单</a>-> 2、提交预报-> 3、<a
					href='javascript:;'>打印标签</a>-> 4、标签贴到对应货物-> 5、交货-> 6、跟踪-> 7、完成
			</div>
		</fieldset>
		<div style='height: 20px;'></div>
		<div
			style="width: 100%; height: 100%; padding-left: 10px; clear: both;">
			<div style='width: 33%; float: left;' class='nbox_c'>
				<div class="ntitle">
					<span style="">
						<h2 class="ntitle"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
账户总览<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2>
					</span>
				</div>
				<table width="100%" cellspacing="0" cellpadding="0" border="0"
					class="table-module">
					<tbody>
						<tr class="table-module-title">
							<td>客户代码</td> <?php if ($_smarty_tpl->tpl_vars['level']->value){?>
							<td>客户等级</td><?php }?>
							<td>运费余额</td>
							<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
押金<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
							<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
总信用额度<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
							<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
剩余信用额度<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						</tr>
					</tbody>
					<tbody>
						<tr class="table-module-b1">
							<td><?php echo $_smarty_tpl->tpl_vars['customer_code']->value;?>
</td> 
							<?php if ($_smarty_tpl->tpl_vars['level']->value){?><td style="color: orange;"><?php echo $_smarty_tpl->tpl_vars['level']->value;?>
</td><?php }?>
							<td<?php if ($_smarty_tpl->tpl_vars['zonglan']->value['a']<0){?>style='color:red;'<?php }?>><?php echo $_smarty_tpl->tpl_vars['zonglan']->value['a'];?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['zonglan']->value['b'];?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['zonglan']->value['c'];?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['zonglan']->value['d'];?>
</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div style='width: 30%; float: left;' class='nbox_c'>
				<div class="ntitle">
					<span style="">
						<h2 class="ntitle"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
订单概况<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2>
					</span>
				</div>
				<table width="100%" cellspacing="0" cellpadding="0" border="0"
					class="table-module">
					<tr class='table-module-title'>
						<td width='120'></td>
						<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
昨日数量<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
						<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
今日数量<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
					</tr>
					<tbody id="orderData">
						<tr class='table-module-b1'>
							<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
创建数<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['create_count']->value;?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['create_count_today']->value;?>
</td>
						</tr>
						<tr class='table-module-b1'>
							<td width='120'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
收货数<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['checkin_count']->value;?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['checkin_count_today']->value;?>
</td>
						</tr>
						<tr class='table-module-b1'>
							<td width='120'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
出货数<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['checkout_count']->value;?>
</td>
							<td><?php echo $_smarty_tpl->tpl_vars['checkout_count_today']->value;?>
</td>
						</tr>
					</tbody>
				</table>
			</div>


			<div id="main_right"
				style="float: left; width: 33%; margin-right: 20px;">
				<div class="admin_task">
					<div class="table-module-title"
						style="line-height: 28px; height: 28px;">
						<text style="font-size:18px;padding:10px;">公告栏</text>
					</div>
					<div class="depotTab2">
						<ul>
							<li><a href="javascript:void(0);" id='Notice' class='tab'
								style="cursor: pointer">通知</a></li>
							<!--  <li class="chooseTag">
			                    <a href="javascript:void(0);" id='Update' class='tab' style="cursor:pointer">更新</a>
			                </li>    -->
						</ul>
					</div>
				</div>
			</div>









			<div style='width: 90%;' class='nbox_c'>
				<div style='width: 36.6%; float: left;'
					class='nbox_c'>
					<div class="ntitle">
						<span style="">
							<h2 class="ntitle"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
联系人信息<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2>
						</span>
					</div>
					<table width="100%" cellspacing="0" cellpadding="0" border="0"
						class="table-module">
						<tbody id="asnData">
							<tr class='table-module-title'>
								<td width='120'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
联系人<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
								<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
服务热线<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
								<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
即时沟通<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</td>
							</tr>
							<tr class='table-module-b1'>
								<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
客服员<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:<?php if (isset($_smarty_tpl->tpl_vars['shippersupporter']->value)){?><?php echo $_smarty_tpl->tpl_vars['shippersupporter']->value['st_name'];?>
<?php }?></td>
								<td><?php if (isset($_smarty_tpl->tpl_vars['shippersupporter']->value)){?><?php echo $_smarty_tpl->tpl_vars['shippersupporter']->value['st_phone'];?>
<?php }?></td>
								<td><?php if (isset($_smarty_tpl->tpl_vars['shippersupporter']->value)){?><a
									href="tencent://message/?uin=<?php echo $_smarty_tpl->tpl_vars['shippersupporter']->value['st_qq'];?>
&amp;Site=有事Q我&amp;Menu=yes"><img
										style="border: 0px;"
										src=http://pub.idqqimg.com/wpa/images/counseling_style_52.png></a><?php }?>
								</td>
							</tr>
							<tr class='table-module-b1'>
								<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
业务员<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:<?php if (isset($_smarty_tpl->tpl_vars['saller']->value)){?><?php echo $_smarty_tpl->tpl_vars['saller']->value['st_name'];?>
<?php }?></td>
								<td><?php if (isset($_smarty_tpl->tpl_vars['saller']->value)){?><?php echo $_smarty_tpl->tpl_vars['saller']->value['st_phone'];?>
<?php }?></td>
								<td><?php if (isset($_smarty_tpl->tpl_vars['saller']->value)){?><a
									href="tencent://message/?uin=<?php echo $_smarty_tpl->tpl_vars['saller']->value['st_qq'];?>
&amp;Site=有事Q我&amp;Menu=yes"><img
										style="border: 0px;"
										src=http://pub.idqqimg.com/wpa/images/counseling_style_52.png></a><?php }?>
								</td>
							</tr>
							<tr class='table-module-b1'>
								<td><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
结算员<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
:<?php if (isset($_smarty_tpl->tpl_vars['dunner']->value)){?><?php echo $_smarty_tpl->tpl_vars['dunner']->value['st_name'];?>
<?php }?></td>
								<td><?php if (isset($_smarty_tpl->tpl_vars['dunner']->value)){?><?php echo $_smarty_tpl->tpl_vars['dunner']->value['st_phone'];?>
<?php }?></td>
								<td><?php if (isset($_smarty_tpl->tpl_vars['dunner']->value)){?><a
									href="tencent://message/?uin=<?php echo $_smarty_tpl->tpl_vars['dunner']->value['st_qq'];?>
&amp;Site=有事Q我&amp;Menu=yes"><img
										style="border: 0px;"
										src=http://pub.idqqimg.com/wpa/images/counseling_style_52.png></a><?php }?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>






				<div style='width: 33.5%;float:left;' class='nbox_c'>
					<div class="ntitle">
						<span style="">
							<h2 class="ntitle"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
订单追踪<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2>
						</span>
					</div>
					<form action="/default/index/get-track-detail" method='post'>
						<textarea rows="" cols=""
							style='width: 99%; height: 70px; overflow: auto; font-size: 13px;'
							placeholder='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
如需查询多个单号，请使用","隔开<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
' name='code'></textarea>
						<div style='height: 5px;'></div>
						<input type="submit" class="baseBtn submitToSearch"
							value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
查询<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
">
					</form>
				</div>
				<div class='clear'></div>
			</div>

		</div>

	</div>
	<div style='width: 32.6%; float: left; clear: both;margin-left:8px;' class='nbox_c'>
		<div class="ntitle">
			<span style="">
				<h2 class="ntitle"><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
我要寄件<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
</h2>
			</span>
		</div>
		<div style='padding: 5px;'>
			<input type="button" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
单票录入<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
" class="baseBtn "
				style='padding: 5px 100px;'
				onclick="leftMenu('52','<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
单票录入<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
','/order/order/create?quick=52')">
		</div>
		<div style='padding: 5px;'>
			<input type="button" value="<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
批量上传<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
"
				class="baseBtn submitToSearch" style='padding: 5px 100px;'
				onclick="leftMenu('24','<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
批量上传<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
','/order/order/import?quick=24')">
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
                    });
                    html += '<tr><td colspan="2" align="right"><a href="' + url + '">More</a></td></tr>';
                    $("#" + obj + "Data").html(html);
                }
            }
        });
    }

</script><?php }} ?>