<?php /* Smarty version Smarty-3.1.13, created on 2016-05-13 18:10:35
         compiled from "D:\yt1\application\modules\admin\views\tool\init-system.tpl" */ ?>
<?php /*%%SmartyHeaderCode:233405735a81ba60259-10312637%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '342f4f7a8f6f5cbc31e0828fc203ec9417a9622c' => 
    array (
      0 => 'D:\\yt1\\application\\modules\\admin\\views\\tool\\init-system.tpl',
      1 => 1457516431,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '233405735a81ba60259-10312637',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'page' => 0,
    'runFiles' => 0,
    'msg' => 0,
    'modules' => 0,
    'val' => 0,
    'tables' => 0,
    'table' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_5735a81bac99e3_98299239',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5735a81bac99e3_98299239')) {function content_5735a81bac99e3_98299239($_smarty_tpl) {?><style type="text/css">
    .table td{
        height:32px;
        line-height:32px;
    }
    .table{
        width:480px;
    }
    .showtable{
        width:700px;
    }
    .disabledText{
        background:#cccccc;
    }
    .editName{
        width:50px;
    }
    .menuLink span{
        margin-right:15px;
    }

    .bdrcontent select{
        height:22px;
    }
    .add-lang input{
        width:250px;
    }
</style>
<script>

    function loadSelect() {
        $.ajax({
            type: "post",
            async: false,
            dataType: "json",
            url: "/admin/tool/get-table",
            data: {
                tableName:$("#select_table").val()
            },
            success: function(json) {
                var list = "";
                var field=$("#field").val();
                list+="<table>";

                    $.each(json, function(key, val) {
                        list+="<tr>";
                        list+="<td>"+val['Field']+":</td><td><input type='checkbox' checked name='columns["+val['Field']+"]' value='"+val['Field']+"'></td>";
                        list+="<td>&nbsp;&nbsp;标题:</td><td><input type='text' tabName="+val['Field']+"  name='titles["+val['Field']+"]' value=''></td>";
                        list+="</tr>";
                    });

                list+="</table>";

                $("#showFile").html(list);



                list='';
                list+="<table style='width:100%'>";

                $.each(json, function(key, val) {
                    list+="<tr>";
                    list+="<td>"+val['Field']+":</td><td><input type='checkbox'  name='search["+val['Field']+"]' value='"+val['Field']+"'></td>";
                   if(val['Key']=='PRI'){
                       list+="<td width='80'>主键:</td><td><input type='text' disabled class='disabledText'  name='editTitles["+val['Field']+"]' value=''></td>";
                       list+="<td width='90'>更名为:</td><td><input type='text'  class='disabledText editName'  name='editColumns["+val['Field']+"]' value='"+field+key+"'></td>";
                        list+="<td>---</td>";
                   }else{
                       list+="<td>标题:</td><td><input type='text' class='edit_"+val['Field']+"'  name='editTitles["+val['Field']+"]' value=''></td>";
                       list+="<td width='90'>更名为:</td><td><input type='text' class='editName'  name='editColumns["+val['Field']+"]' value='"+field+key+"'></td>";

                       list+="<td width='90'>验证:</td><td><select name='validator["+val['Field']+"]'><option value=''>不验证</option><option value='1'>不能为空</option><option value='2'>不能为空且数值类型</option><option value='3'>int类型</option></select></td>";
                   }



                    list+="</tr>";
                });

                list+="</table>";


                $("#editFile").html(list);
            }
        });
    }

    function loadSelectView() {
        $.ajax({
            type: "post",
            async: false,
            dataType: "json",
            url: "/admin/tool/get-view",
            data: {
                modules:$("#modules").val()
            },
            success: function(json) {
                var list='';
                list+='<option value="">根目录</option>'
                $.each(json,function(k,v){
                    list+='<option value="'+v+'">'+v+'</option>'
                });
                $("#fileDir").html(list);
            }
        });
    }

    function goUpdate(){
        $.each($("#showFile input"),function(k,v){
           if( $(this).attr('tabName')){
              $(".edit_"+$(this).attr('tabName')).val($(this).val());
           }
        })
    }

    $(function(){
        $('#modules').change(loadSelectView).change();
    })
</script>
<div class="bdrcontent">
    <div class="menuLink" style="width:100%;height:50px;padding-top:10px">
        <span><a href="/admin/tool/index">初始化模块</a> </span>
        <span><a href="/admin/tool/add-language">添加语言字段</a> </span>
        <span><a href="/admin/tool/update-Json">更新语言Json文件</a> </span>
        <span><a href="/admin/tool/add-acl">自动添加Action</a> </span>
    </div>
  <?php if ($_smarty_tpl->tpl_vars['page']->value=='1'){?>
    <div class="title">
		<h3>初始化</h3>
		<h3>Services,Models，DbTable</h3>
        <h3>tpl,Js,Controller</h3>
	</div>
    <div>
        <?php echo $_smarty_tpl->tpl_vars['runFiles']->value;?>

    </div>
	<form action="" method="post" id='SqlForm' name="createForm" style="margin: 0; padding: 0;">

		<h3 style='padding: 10px 5px;'><?php echo $_smarty_tpl->tpl_vars['msg']->value;?>
</h3>
		<table class="table" style="float:left">
			<tbody>
            <tr>
                <th width='80'>文件:</th>
                <td>控制器:<input type="checkbox" name="Controller"  value="1">&nbsp;&nbsp;Tpl:<input type="checkbox"   name="tpl" value="1"></td>
            </tr>
            <tr>
                <th width='80'>模块:</th>
                <td>
                    <select name="modules" id="modules" >
                        <?php  $_smarty_tpl->tpl_vars['val'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['val']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['modules']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['val']->key => $_smarty_tpl->tpl_vars['val']->value){
$_smarty_tpl->tpl_vars['val']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['val']->key;
?>
                        <option value="<?php echo $_smarty_tpl->tpl_vars['val']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['val']->value;?>
</option>
                        <?php } ?>
                        </select>
                   </td>
            </tr>
            <tr>
                <th width='80'>自定义字段:</th>
                <td><input type="text" name="field" id="field" onblur="loadSelect()" value="E" ></td>
            </tr>
            <tr>
                <th width='80'>自定义控制器文件名:</th>
                <td><input type="text" name="ControllerName" ></td>
            </tr>
            <tr>
                <th width='80'>自定义Js\Tpl文件名:</th>
                <td><input type="text" name="fileName" ></td>
            </tr>
            <tr>
                <th width='80'>自定义Tpl文件夹:</th>
                <td>

                    <select name="fileDir" id="fileDir">
                        <option value="">选择文件夹</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th width='80'>清除时间类型:</th>
                <td><input type="checkbox" name="dataType" value="1"   ></td>
            </tr>
            <tr>
                <th width='80'>强制替换文件:</th>
                <td><input type="checkbox" name="replaceFile" value="1"></td>
            </tr>
				<tr>
					<th width='80'>table:</th>
					<td><select onchange="loadSelect()" name="select_table" id="select_table">
                            <option value="">请选择</option>
                            <?php  $_smarty_tpl->tpl_vars['table'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['table']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['tables']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['table']->key => $_smarty_tpl->tpl_vars['table']->value){
$_smarty_tpl->tpl_vars['table']->_loop = true;
?>
                            <option value="<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['table']->value;?>
</option>
                            <?php } ?>
					</select> </td>
				</tr>
				<tr>
					<th width='150'>&nbsp;</th>
					<td><input type="submit" class="bgBtn" id="submitForm" value="Submit" name="submitForm"></td>
				</tr>
			</tbody>
		</table>
        <table class="showtable" style="float:left">

            <tr>
                <td style="width:100px">显示字段：</td>
                <td id="showFile"></td>
            </tr>
            <tr>
                <td colspan="2" style="width:100%;border-top:2px solid #ccc;"></td>
            </tr>
            <tr>
                <td style="width:100px">同步标题：</td>
                <td><input type="checkbox" value="1" onclick="goUpdate();" name="update"></td>
            </tr>
            <tr>
                <td style="width:100px">编辑字段：</td>
                <td id="editFile"></td>
            </tr>
        </table>
	</form>
    <?php }?>


    <?php if ($_smarty_tpl->tpl_vars['page']->value=='2'){?>
    <div class="title">
        <h3>添加语言字段</h3>
    </div>
    <div>
        <form action="" method="post" id='SqlForm' name="createForm" class="add-lang" style="margin: 0; padding: 0;">
            <table class="table" style="float:left">
                <tr>
                    <th width='150'>字段</th>
                    <td><input type="text" name="files" value="" ></td>
                </tr>
                <tr>
                    <th width='150'>中文</th>
                    <td><input type="text" name="cnValue" ></td>
                </tr>
                <tr>
                    <th width='150'>英文</th>
                    <td><input type="text" name="enValue" ></td>
                </tr>
                <tr>
                    <th width='150'>&nbsp;</th>
                    <td><input type="submit" class="bgBtn" id="submitForm" value="Submit" name="submitForm"></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
    <?php }?>

</div>
<?php }} ?>