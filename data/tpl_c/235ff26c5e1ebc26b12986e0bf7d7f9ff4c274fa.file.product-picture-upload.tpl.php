<?php /* Smarty version Smarty-3.1.13, created on 2014-07-22 14:04:06
         compiled from "E:\Zend\workspaces\ruston_oms\application\modules\product\views\product\product-picture-upload.tpl" */ ?>
<?php /*%%SmartyHeaderCode:626053cdd95a3a8895-90354170%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '235ff26c5e1ebc26b12986e0bf7d7f9ff4c274fa' => 
    array (
      0 => 'E:\\Zend\\workspaces\\ruston_oms\\application\\modules\\product\\views\\product\\product-picture-upload.tpl',
      1 => 1406009044,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '626053cdd95a3a8895-90354170',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_53cdd95a3f1d57_93955825',
  'variables' => 
  array (
    'session_id' => 0,
    'attacheds' => 0,
    'att' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53cdd95a3f1d57_93955825')) {function content_53cdd95a3f1d57_93955825($_smarty_tpl) {?><?php if (!is_callable('smarty_block_t')) include 'E:\\Zend\\workspaces\\ruston_oms\\libs\\Smarty\\plugins\\block.t.php';
?><script language="javascript" type="text/javascript" src="/swfupload/swfupload.js"></script>
<script language="javascript" type="text/javascript" src="/swfupload/handlers.js"></script>
<script type="text/javascript">
<!--
var swfu = null;
function swfuInit() {
    swfu = new SWFUpload({
    // Backend Settings
    upload_url : "/product/product/swfupload",
    post_params : {'session_id':'<?php echo $_smarty_tpl->tpl_vars['session_id']->value;?>
'},
    file_post_name : "Filedata",	
    // File Upload Settings
    file_size_limit : "20480", // 2MB
    file_types : "*.jpg; *.gif; *.png;",
    file_types_description : "选择 JPEG/PNG/gif 格式文件",
    //file_upload_limit : "10",
    //file_queue_limit:"1",
    file_queue_error_handler : fileQueueError,
    file_dialog_complete_handler : fileDialogComplete,
    upload_progress_handler : uploadProgress,
    upload_error_handler : uploadError,
    upload_success_handler : uploadSuccess,
    upload_complete_handler : uploadComplete,
    upload_start_handler:uploadStart,
    
    
    button_image_url : "/swfupload/broker_140x1281.gif",
    button_placeholder_id : "spanButtonPlaceholder",
    button_width : 140,
    button_height : 32,
    button_text : '<span class="sud_btn">图片上传</span>',
    button_text_style: '.sud_btn { font-family: Helvetica, Arial, sans-serif; font-size: 12pt;font-weight:bold;text-align:center; } .sud_sbtn { font-size: 10pt; }',
    button_text_top_padding : 5,
    //button_text_left_padding : 30,
    
    button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT,
    button_cursor : SWFUpload.CURSOR.HAND,
    
    // Flash Settings
    flash_url : "/swfupload/swfupload.swf",
    
    custom_settings : {
    upload_target : "divFileProgressContainer"
    },
    
    // Debug Settings
    debug : false
    });
};
function isurl(str_url){
   // var strregex = "(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:/~\+#]*[\w\-\@?^=%&amp;/~\+#])?";
    //var re=new RegExp(strregex);
    var regexp = new RegExp("(http[s]{0,1})://[a-zA-Z0-9\\.\\-]+\\.([a-zA-Z]{2,4})(:\\d+)?(/[a-zA-Z0-9\\.\\-~!@#$%^&amp;*+?:_/=<>]*)?", "gi");
    if (regexp.test(str_url)){
        return (true);
    }else{
        return (false);
    }
}
jQuery(function() {
    swfuInit();

    $('#web_img_wrap').dialog({
        autoOpen: false,
        width: 500,
        maxHeight: 500,
        modal: true,
        show: "slide",
        buttons: [
            {
                text: 'OK',
                click: function () {
                    var url_arr = [];
                    var err = '';
                    $(".web_img_url",this).each(function(){
                        var url = $(this).val();
                        $(this).removeClass('web_img_url_err');
                        if(isurl(url)){
                        	url_arr.push(url);                        	
                        } else{
                            $(this).addClass('web_img_url_err');
                        	err = ('URL Format Error');
                        }
                    });
                    
                    if(err!=''){
                        alertTip(err);
                    }else{
                    	$.each(url_arr,function(k,v){
                        	$.ajax({
                			   type: "POST",
                			   url: "/product/product/save-link",
                			   data: {path: v},
                			   dataType:'json',
                			   success: function(json){
               				    if(json.ask){
               				    	_loadImage(json);
               				    }
                			   }
                			});	
                        })
                        
                        $(this).dialog("close");
                    }
                    
                }
            },
            {
                text: 'Close',
                click: function () {
                    $(this).dialog("close");
                }
            }
        ],
        close: function () {
          $(this).html('');   
        }
    });

    $("#uploadWebImage").click(function(){
        var web_wrap = $("<div class='web_wrap'>URL:<input type='text' class='web_img_url' value='' size='45'><img alt='delete' src='/images/minus_sign.gif' class='web_wrap_del'></div> ");
        var web_wrap_add = $("<div class='web_wrap_add'><img src='/images/plus_sign.gif' class='web_wrap_add_op'></div>");
        var web_wrapper= $("<div class='web_wrapper'></div>");
        web_wrapper.append(web_wrap_add).append(web_wrap);
        $('#web_img_wrap').html(web_wrapper).dialog('open');
    });

    $(".web_wrap_add_op").live('click',function(){
        var web_wrap = $("<div class='web_wrap'>URL:<input type='text' class='web_img_url' value='' size='45'><img alt='delete' src='/images/minus_sign.gif' class='web_wrap_del'></div> ");
        $(this).parent().parent().append(web_wrap);
    });
   
    $(".imgWrap").live("dblclick",function(){    	
    	$(this).remove();    	
    });
    $(".web_wrap_del").live('click',function(){
        $(this).parent().remove();
    });

});
$(function(){
    //已有图片初始化
	<?php if (isset($_smarty_tpl->tpl_vars['attacheds']->value)){?> 
	<?php  $_smarty_tpl->tpl_vars['att'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['att']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['attacheds']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['att']->key => $_smarty_tpl->tpl_vars['att']->value){
$_smarty_tpl->tpl_vars['att']->_loop = true;
?>
        _loadImage(<?php echo $_smarty_tpl->tpl_vars['att']->value['json'];?>
);
	<?php } ?> 
	<?php }?>
})
//-->
</script>
<style>
<!--
.imgWrap {
	border: 1px solid #ccc;
	margin: 0 5px 5px 0;
	float: left;
	width: 142px;
	height: 142px;
	background: #fff url(/image/ext_img_uploading.gif) center center no-repeat;
	position: relative;
}

.imgWrap img {
	width: 140px;
	height: 140px;
	margin: 1px;
}

.clear {
	clear: both;
}

.web_img_url_err {
	border: 2px solid red;
}
-->
</style>
<table width="100%" cellspacing="0" cellpadding="3" class="formtable">
	<tr>
		<th width="120" valign='middle'><?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
image_preview<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
：</th>
		<td valign='middle'>
			<div style="position: relative;">
				<div style='width: 150px; float: left; padding: 0px 0 0 0px;'>
					<span id="spanButtonPlaceholder"></span>
				</div>
				<input type="button" style="color: #000000; font-size: 14px; font-family: monospace; margin-top: 5px;" class="bgBtn4" value="Web Image" id='uploadWebImage'>
				<div id="divFileProgressContainer" class="divFileProgressContainer clear"></div>
				<div class='red clear' style='padding-top: 10px;'>Note: Double Click Picture To Remove</div>
				<div style="position: relative; padding: 10px 0 0;" id='pic_wrapper' class='clear'></div>
			</div>
		</td>
	</tr>
</table>
<div id='web_img_wrap' title='<?php $_smarty_tpl->smarty->_tag_stack[] = array('t', array()); $_block_repeat=true; echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
image_upload<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_t(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
'></div><?php }} ?>