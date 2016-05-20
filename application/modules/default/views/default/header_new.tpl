<script type="text/javascript">
EZ.userId=<{$userId}>;
EZ.lang='<{$lang}>';
</script>
<script type="text/javascript">
	$(function(){

		/**
		 * 用户信息设置打开
		 */
		$("#user_info_sys").click(function(){
			$(this).next().show();
		});

		$("#user_info_sys_menu > li").click(function(){
			closeUserInfoMenu();
		});
		/**
		 * 用户信息小面版监听，没有在面板上点击时，关闭面板
		 */
		$(document).bind("click",function(e){
			
			var target = $(e.target);
			//当前鼠标点击坐标是否在ul展示框内，如果在展示框内则不隐藏
			var inUserMenuUl = target.closest('ul[id^="user_info_sys_menu"]').length > 0;
			var inUserMenuA = target.closest('a[id^="user_info_sys"]').length > 0;
			
			var user_info_sys_menu_bol = $("#user_info_sys_menu").is(":visible");
			if(!inUserMenuUl && !inUserMenuA && user_info_sys_menu_bol){
				closeUserInfoMenu();
			}
		});

		/**
		 * 初始化快捷菜单
		 */
		initShortcutMenu();
	});

	/**
	 * 关闭用户信息面板
	 */
	function closeUserInfoMenu(){
		$("#user_info_sys_menu").hide();
	}
	
	/**
	 * 设置快捷菜单链接，靠齐位置
	 */
	function initShortcutMenu(){
		//设置菜单位置
		var headHelp_width = $(".headHelp").width();
		$(".headQuickNav").css("right",headHelp_width + 25);
	}

	// 登录ERP
	function loginErp(){
		$.post('/default/index/get-erp-login-token', function (json) {
			if (!isJson(json)) {
                return;
            }
			
            var form = $("<form>")
            	.attr('style', 'display:none')
            	.attr("target", '_blank')
            	.attr("method", "post")
            	.attr("action", json.url);
            form.append("<input type='text' name='access_token' value='" + json.access_token + "'/>");
        	
            $('body').append(form);
            form.submit();
            form.remove();
            
        }, 'json');
	}
</script>
<script type="text/javascript" src="/js/menu.js?20140404"></script>
<style>
	<!--
	.logo2{background:none;}
	-->
</style>	
<div id="head">
	    
	<div class="headProduct">
		<span style="width: 80%;float: left;text-align: center;">
			<{t}>oms_system<{/t}>
		</span>
		<i class="icon-caret-down"></i>
	</div>
	<!-- 
	<div class="headQuickNav">
		<ul>
			<li style="">
				<a href="javascript:;" onclick="loginErp();"><{t}>在线ERP<{/t}></a>
			</li>
		</ul>
	</div>
	 -->
	<div class="headHelp" style="float: right; position: absolute; right: 10px;">
		<ul class="nav ace-nav pull-right">
			<!-- 任务栏 -->
			<li class="grey" style="display: none;">
				<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
					<i class="icon-tasks"></i>
					<span class="badge badge-grey">0</span>
				</a>
				
			</li>
			
			<!-- 通知 -->
			<li class="purple" style="display: none;">
				<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
					<i class="icon-bell-alt icon-animated-bell"></i>
					<span class="badge badge-important">8</span>
				</a>
				
			</li>
			
			<!-- 短消息 -->
			<li class="green" style="display: none;">
				<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
					<i class="icon-envelope icon-animated-vertical"></i>
					<span class="badge badge-success">5</span>
				</a>
				 
			</li>
			
			<li class="light-blue">
				<a id="user_info_sys" class="dropdown-toggle" href="javascript:;" data-toggle="dropdown">
					<img alt="User Photo" src="/images/modeling/user.png" width="30px" height="30px" class="nav-user-photo">
					<span class="user-info" title="<{$user.user_name}>">
						<small>Welcome,</small>
						<{$user.user_name}></br>
						<{$user.csi_customer.customer_shortname}>(<{$user.csi_customer.customer_code}>)
					</span>
					<i class="icon-caret-down" style="margin-top: -8px;"></i>
				</a>
				<!-- 账户信息 -->
				<ul id="user_info_sys_menu" class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-closer">
					<li>
						<a href="/?LANGUAGE=zh_CN">
							<i class="icon-exchange" style="line-height: 19px;"></i>
							简体中文
						</a>
					</li>
					<li>
						<a href="/?LANGUAGE=en_US">
							<i class="icon-exchange" style="line-height: 19px;"></i>
							English
						</a>
					</li>
					<!-- 个人信息，暂时屏蔽 -->
					<!-- 
					<li>
						<a onclick="leftMenu('user_set_1024','<{t}>personal<{/t}>','/auth/user/user-set')" class="sub-menu-id-user_set_1024" href="javascript:;">
							<i class="icon-user" style="line-height: 19px;"></i>
							<{t}>personal<{/t}>
						</a>
					</li>
					 -->
					<!-- 关于易仓 
					<li>
						<a class="sub-menu-id-user_set_1024" href="http://www.eccang.com" target="_blank">
							<i class="icon-user" style="line-height: 19px;"></i>
							<{t}>关于易仓<{/t}>
						</a>
					</li>
 					-->
					<li class="divider"></li>

					<li>
						<a href="<{$logout}>">
							<i class="icon-off" style="line-height: 19px;"></i>
							Logout
						</a>
					</li>
				</ul>
			</li>
		</ul>
	</div>
</div>

<div id="column">
	<div class="logo2" onclick="leftMenu('0','<{t}>home<{/t}>','/system/home')">
		<a href="javascript:void(0);"><img src="<{$logo}>" alt="" style='padding-left:5px;padding-top:2px;width:100%;height:90%;'/></a>
	</div>
	<div class="headNav">
		<{foreach from=$menu item=submenu1}>
		<ul onmouseover="showThisSubMenuHeader(this,'headmenu<{$submenu1.parent.um_id}>')" onmouseout="closeThisSubMenuHeader(this,'headmenu<{$submenu1.parent.um_id}>')" >
			<li style="font-weight: bold;">
				<a href="#"><{$submenu1.parent.value}></a>
			</li>
			<{foreach from=$submenu1.item name=submenu item=submenu}> <{if $smarty.foreach.submenu.first}>
			<div class="topMenu" style="display: none" id="headmenu<{$submenu1.parent.um_id}>">
				<div class="topMenu_cover"></div>
				<{/if}>
				<!-- <ul>
					<li class="noline">
						<span><{$submenu.menu.value}></span>
					</li>
					<li class="noline">&nbsp;</li>
				</ul> -->
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
</div>
<div class="clr" style=''></div>
<div class="main_quit">
	
</div>
