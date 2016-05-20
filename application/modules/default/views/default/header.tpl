<script type="text/javascript">
EZ.userId=<{$userId}>;
EZ.lang='<{$lang}>';
</script>
<script type="text/javascript" src="/js/menu.js"></script>
<div id="head">
	<div class="headProduct">&nbsp;&nbsp;<{t}>oms_system<{/t}></div>
	<div class="headNav">
		<{foreach from=$menu item=submenu1}>
		<ul onmouseover="showThisSubMenuHeader(this,'headmenu<{$submenu1.parent.um_id}>')" onmouseout="closeThisSubMenuHeader(this,'headmenu<{$submenu1.parent.um_id}>')">
			<li style="font-weight: bold;">
				<a href="#"><{$submenu1.parent.value}></a>
			</li>
			<{foreach from=$submenu1.item name=submenu item=submenu}> <{if $smarty.foreach.submenu.first}>
			<div class="topMenu" style="display: none" id="headmenu<{$submenu1.parent.um_id}>">
				<div class="topMenu_cover"></div>
				<{/if}>
				<ul>
					<li class="noline">
						<span><{$submenu.menu.value}></span>
					</li>
					<li class="noline">&nbsp;</li>
				</ul>
				<{foreach from=$submenu.item name=sub item=sub}> <{if $smarty.foreach.sub.first}>
				<ul><{else}> <{if $smarty.foreach.sub.index%2==0}>
				</ul>
				<ul>
					<{/if}> <{/if}> <{if $smarty.foreach.sub.index%2==0}>
					<li class="title"><{else}>
					
					
					<li class="title">
						<{/if}>
						<a href="javascript:void(0);" class="sub-menu-id-<{$sub.ur_id}>" onclick="leftMenu('<{$sub.ur_id}>','<{$sub.value}>','<{$sub.ur_url}>?quick=<{$sub.ur_id}>')"><{$sub.value}></a>
					</li>
					<{if $smarty.foreach.sub.last}> <{if $smarty.foreach.sub.index%2==0}>
					<li class="noline">&nbsp;</li>
					<{/if}>
				</ul>
				<{/if}> <{/foreach}> <{if $smarty.foreach.submenu.last}>
			</div>
			<{/if}> <{/foreach}>
		</ul>
		<{/foreach}>
	</div>
	<div class="clr"></div>
	<div class="headHelp" style="float: right; height: 34px; position: absolute; right: 5px;">
		<a href="/?LANGUAGE=zh_CN" style='color: #fff; line-height: 34px; padding: 0 5px;'>简体中文</a>
		<a href="/?LANGUAGE=en_US" style='color: #fff; line-height: 34px; padding: 0 5px;'>English</a>
		<!-- 
		<span style="line-height: 45px;">
			<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=2258801729&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:2258801729:41" alt="在线帮助" title="在线帮助"/></a>
		</span>
		 -->
	</div>
</div>
<div id="column">
	<div class="logo2" onclick="leftMenu('0','<{t}>home<{/t}>','/system/home')">
		<a href="javascript:void(0);"></a>
	</div>
	<div class="nav2"></div>
</div>
<div class="main_quit">
	<{t}>welcome<{/t}>，
	<a style='color: #0090E1;' onclick="leftMenu('user_set_1024','<{t}>user_center<{/t}>','/auth/user/user-set')" class="sub-menu-id-user_set_1024" href="javascript:void(0);"><{$user.user_name}></a>
	&nbsp;&nbsp;|&nbsp;&nbsp;
	<a href="<{$logout}>" class='logout'><{t}>logout<{/t}></a>
</div>