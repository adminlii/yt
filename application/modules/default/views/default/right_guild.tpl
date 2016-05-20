<link rel="stylesheet" type="text/css" href="/css/public/step.css">
<link rel="stylesheet" type="text/css" href="/css/public/help.css">
<script type="text/javascript" src="/js/help.js?20140128"></script>
<script type="text/javascript" src="/js/step.js?20140128"></script>
<div class="guild">
    <h2 style="cursor: pointer" id="menu0">
        <a href="javascript:void (0)" title="首页" onclick="leftMenu('0','首页','/system/home')">
            <{t}>home<{/t}>
        </a>
    </h2>
    <h3 class="guild_drop">
        <a href="javascript:;" onclick="showHistory()"><img id="menuBtn" src="/images/pack/bg_guild_drop01.gif" alt="显示历史标签"></a>
        <div class="guild_drop_list" style="display: none;top:118px" id="menuList" onmouseout="showHistory()">
            <a href="javascript:void(0);" style="border-bottom:1px solid #a8ccee;" onclick="clearHis(this)">清空历史标签</a>
        </div>
    </h3>
    <span class="sys_help" title="帮助" style='display:none;'>
        <a href="javascript:void (0)" style="text-decoration:none;">
        	<strong>?</strong>
        </a>
    </span>
</div>
<!-- 帮助页面，弹出窗体 -->
<div id="sys_help_dialog" style="display:none;">
	<{include file='default/views/default/help.tpl'}>
</div>