<?php /* Smarty version Smarty-3.1.13, created on 2016-05-05 16:57:41
         compiled from "D:\yt\application\modules\default\views\default\left_menu.tpl" */ ?>
<?php /*%%SmartyHeaderCode:24113572b0b059aa568-21755115%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '631800b5b27547132c0e8519a61d5e013f66aeee' => 
    array (
      0 => 'D:\\yt\\application\\modules\\default\\views\\default\\left_menu.tpl',
      1 => 1457516431,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '24113572b0b059aa568-21755115',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'menu' => 0,
    'submenu' => 0,
    'sub' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.13',
  'unifunc' => 'content_572b0b05a75794_02376301',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_572b0b05a75794_02376301')) {function content_572b0b05a75794_02376301($_smarty_tpl) {?>

<div id="sidebarIcon" style="display: none;">

    <div class="categoryicon" style="text-align: center;padding-top:8px; font-size: 12px;    font-weight: bold;">
            <a href="#">
                <img src="/images/sidebar/icon_tab.png" align="absmiddle" alt="切换系统" />
            </a>

    </div>
    <div class="menuicon">
        <?php  $_smarty_tpl->tpl_vars['submenu'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['submenu']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['menu']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['submenu']->key => $_smarty_tpl->tpl_vars['submenu']->value){
$_smarty_tpl->tpl_vars['submenu']->_loop = true;
?>
        <dl onmouseover="showThisSubMenu(this,'menuicon<?php echo $_smarty_tpl->tpl_vars['submenu']->value['menu']['um_id'];?>
')" onmouseout="closeThisSubMenu(this,'menuicon<?php echo $_smarty_tpl->tpl_vars['submenu']->value['menu']['um_id'];?>
')">
            <dt   id="test">
                <a href="javascript:void(0)" style="width: 100%" >
                <img src="/images/sidebar/<?php echo $_smarty_tpl->tpl_vars['submenu']->value['menu']['um_css'];?>
" align="absmiddle" />
                </a>
            </dt>


            <div class="submenu" id="menuicon<?php echo $_smarty_tpl->tpl_vars['submenu']->value['menu']['um_id'];?>
" >
                <div class="submenu_cover"></div>

                    <?php  $_smarty_tpl->tpl_vars['sub'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['sub']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['submenu']->value['item']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['sub']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['sub']->iteration=0;
 $_smarty_tpl->tpl_vars['sub']->index=-1;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['submenu']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['sub']->key => $_smarty_tpl->tpl_vars['sub']->value){
$_smarty_tpl->tpl_vars['sub']->_loop = true;
 $_smarty_tpl->tpl_vars['sub']->iteration++;
 $_smarty_tpl->tpl_vars['sub']->index++;
 $_smarty_tpl->tpl_vars['sub']->first = $_smarty_tpl->tpl_vars['sub']->index === 0;
 $_smarty_tpl->tpl_vars['sub']->last = $_smarty_tpl->tpl_vars['sub']->iteration === $_smarty_tpl->tpl_vars['sub']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['submenu']['first'] = $_smarty_tpl->tpl_vars['sub']->first;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['submenu']['index']++;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['submenu']['last'] = $_smarty_tpl->tpl_vars['sub']->last;
?>
                        <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['submenu']['first']){?>
                             <ul>
                         <?php }else{ ?>
                                <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['submenu']['index']%2==0){?>
                                    </ul> <ul>
                                <?php }?>
                         <?php }?>
                        <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['submenu']['last']){?>
                             <li class="noline">
                        <?php }else{ ?>
                            <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['submenu']['index']%2==0){?>
                                <li >
                             <?php }else{ ?>
                         <li class="noline">
                            <?php }?>
                        <?php }?>
                        <a href="javascript:void(0);"  onclick="leftMenu('<?php echo $_smarty_tpl->tpl_vars['sub']->value['ur_id'];?>
','<?php echo $_smarty_tpl->tpl_vars['sub']->value['value'];?>
','<?php echo $_smarty_tpl->tpl_vars['sub']->value['ur_url'];?>
?quick=<?php echo $_smarty_tpl->tpl_vars['sub']->value['ur_id'];?>
')"><?php echo $_smarty_tpl->tpl_vars['sub']->value['value'];?>
</a>
                        </li>
                        <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['submenu']['last']){?>
                            </ul>
                        <?php }?>
                    <?php } ?>

            </div>

        </dl>
        <?php } ?>
    </div>
    <div class="menuiconquit">
        <a  href="/default/index/logout"><img src="/images/sidebar/icon_quit01.gif" align="absmiddle" />退出</a>
    </div>
</div>

<div id="sidebar">
    <div class="category">
        <div class="category_down">
            <a href="#"></a>
        </div>
        <div class="category_text">仓储系统</div>
        <div class="category_up"></div>
    </div>
    <div class="menu" id="menu-module">
        <?php  $_smarty_tpl->tpl_vars['submenu'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['submenu']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['menu']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['submenu']->key => $_smarty_tpl->tpl_vars['submenu']->value){
$_smarty_tpl->tpl_vars['submenu']->_loop = true;
?>
        <dl>
            <dt class="sub-menu" state='1'>
                <img src="/images/sidebar/<?php echo $_smarty_tpl->tpl_vars['submenu']->value['menu']['um_css'];?>
" align="absmiddle" /><?php echo $_smarty_tpl->tpl_vars['submenu']->value['menu']['value'];?>

            </dt>
            <?php  $_smarty_tpl->tpl_vars['sub'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['sub']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['submenu']->value['item']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['sub']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['sub']->iteration=0;
 $_smarty_tpl->tpl_vars['sub']->index=-1;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['submenu']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['sub']->key => $_smarty_tpl->tpl_vars['sub']->value){
$_smarty_tpl->tpl_vars['sub']->_loop = true;
 $_smarty_tpl->tpl_vars['sub']->iteration++;
 $_smarty_tpl->tpl_vars['sub']->index++;
 $_smarty_tpl->tpl_vars['sub']->first = $_smarty_tpl->tpl_vars['sub']->index === 0;
 $_smarty_tpl->tpl_vars['sub']->last = $_smarty_tpl->tpl_vars['sub']->iteration === $_smarty_tpl->tpl_vars['sub']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['submenu']['first'] = $_smarty_tpl->tpl_vars['sub']->first;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['submenu']['index']++;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['submenu']['last'] = $_smarty_tpl->tpl_vars['sub']->last;
?>
            <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['submenu']['last']){?>
            <dd style="padding-bottom: 10px;">
            <?php }else{ ?>
            <dd>
            <?php }?>
            <a href="javascript:void(0);" class="sub-menu-id-<?php echo $_smarty_tpl->tpl_vars['sub']->value['ur_id'];?>
" onclick="leftMenu('<?php echo $_smarty_tpl->tpl_vars['sub']->value['ur_id'];?>
','<?php echo $_smarty_tpl->tpl_vars['sub']->value['value'];?>
','<?php echo $_smarty_tpl->tpl_vars['sub']->value['ur_url'];?>
?quick=<?php echo $_smarty_tpl->tpl_vars['sub']->value['ur_id'];?>
')"><?php echo $_smarty_tpl->tpl_vars['sub']->value['value'];?>
</a></dd>
            <?php } ?>
        </dl>
        <?php } ?>
    </div>
    <div class="quit" >
        <a style="padding-bottom: 1px" href="/default/index/logout"><img src="/images/sidebar/icon_quit01.gif" align="absmiddle" />&nbsp;&nbsp;退出</a>
    </div> 
</div>

<script>

    function showThisSubMenu(obj,id){

        //document.getElementById("menuicon1").style.display="block";
       //alert(obj);
        $(obj).children().children("a").addClass("menuiconahover");
        // $(obj).children("div").style.display="block";
        document.getElementById(id).style.display="block";
      //  $("#"+id).children("div").show();

    }

    function closeThisSubMenu(obj,id){
        //  alert(document.getElementById("menuicon1").style.display);
        $(obj).children().children("a").removeClass("menuiconahover");
        document.getElementById(id).style.display="none";
      //  $(obj).children("div").hide();
    }

</script><?php }} ?>