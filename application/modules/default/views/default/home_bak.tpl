<link rel="stylesheet" type="text/css" href="/css/public/step.css">
<link rel="stylesheet" type="text/css" href="/css/public/help.css">
<script type="text/javascript" src="/js/raphael/raphael.js?20131018"></script>
<script type="text/javascript" src="/js/raphael/popup.js?20131018"></script>
<script type="text/javascript" src="/js/raphael/analytics.js?20131018"></script>
<script type="text/javascript" src="/js/modules/panel/panel.js?20131018"></script>
<script type="text/javascript" src="/js/json2.js"></script>
<script type="text/javascript" src="/js/help.js?20140128"></script>
<script type="text/javascript" src="/js/step.js?20140128"></script>
<script type="text/javascript">
	var userAccount = <{$ebayUserAccount}>;
    $(function(){
        //加载公告
        loadVersions($("#main_right"),'ECERP');
        //加载热销SKU排行
        //loadHotSales($("#main_right"));
        //加载面板
        loadPanel($("#main_left"), ['CS'], [8,9], [], userAccount);

        //加载初始化帮助
        helpInitialize();
    });
</script>
<div id="main_left" class="main_area" style="width: 70%; float: left;">
	<!-- 统计，报表，任务面板 -->
</div>
<div id="main_right" style="float: right; width: 29%;" >
	<!-- 公告 -->
</div>


<!-- 系统初始化帮助，弹出窗体 -->
<{include file='default/views/default/help_initialize.tpl'}>