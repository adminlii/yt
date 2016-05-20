<style>
.order_pross_click{
	background: none repeat scroll 0 0 #F2F2F2;
}
.goodsTab {
    background: url("/images/help/base/bg_goodsTab02.gif") repeat-x scroll center bottom rgba(0, 0, 0, 0);
    border-left: 1px solid #CCCCCC;
    height: 32px;
}

.goodsTab ul {
	margin: 0;
    padding: 0;
    list-style: none outside none;
}
.goodsTab ul li.chooseTag {
    background: none repeat scroll 0 0 #B3C833;
    border: medium none;
    float: left;
    height: 26px;
    padding: 2px 45px;
    font-size:16px;
}
.goodsTab ul li.chooseTag a {
    color: #FFFFFF;
}
.goodsTab ul li {
    background: none repeat scroll 0 0 #FFFFFF;
    border-right: 1px solid #CCCCCC;
    border-top: 1px solid #CCCCCC;
    float: left;
    height: 25px;
    line-height: 25px;
    padding: 2px 45px;
    font-size:16px;
    cursor: pointer;
}
.goodsTab ul li a{
 	color:#666666;
 	text-decoration: none;
}
.clr {
    clear: both;
    font-size: 0;
    height: 0;
    line-height: 0;
    width: 0;
}
.main_content a{
	font-family: "Microsoft YaHei",'微软雅黑','Simsun',Verdana,Arial,Helvetica,sans-serif;
}
.to_jump_div{
	display: block;
	position: absolute;
	right: 52px;
	top: 125px;
	z-index: 1;
	width: 33px;
}

.to_jump_div .iconToPage{
	opacity: 0.65;
}

.to_jump_div .iconToPage:hover{
	opacity: 1;
}

.iconToPage {
    background: url("/images/base/next.png") no-repeat scroll 0 0 / 52px auto rgba(0, 0, 0, 0);
    display: block;
    height: 50px;
    vertical-align: middle;
    white-space: nowrap;
    width: 50px;
}
</style>
<!-- 系统初始化帮助，弹出窗体 -->
<div id="sys_init_help_dialog" style="display:none;">
	<div id="main_content" style="width: 100%;">
		<div style="width: 16%;min-width: 190px;float: left;overflow: hidden;">
			<div class="nav-title" >系统初始化</div>
			<div class="usernav" >
				<ul class="usernav-list1">
					<!-- 流程指引
				    <li><a href="javascript:;" data_val="11" data_title="eBay账户绑定" class="hover usernav_tag"><img width="15" height="15" src="/images/help/base/i-right.png">eBay账户绑定</a></li>
				    <li><a href="javascript:;" data_val="35" data_title="Paypal账户关联" class="usernav_tag"><img width="15" height="15" src="/images/help/base/i-right.png">Paypal账户关联</a></li>
				    <li><a href="javascript:;" data_val="22" data_title="用户店铺绑定" class="usernav_tag"><img width="15" height="15" src="/images/help/base/i-right.png">用户店铺绑定</a></li>
				    <li><a href="javascript:;" data_val="46#32" data_title="SKU建立#关系映射" class="usernav_tag"><img width="15" height="15" src="/images/help/base/i-right.png">SKU建立 + 关系映射</a></li>
				    <li><a href="javascript:;" data_val="47#48" data_title="仓库设置#运输方式" class="usernav_tag"><img width="15" height="15" src="/images/help/base/i-right.png">仓库设置 + 运输方式</a></li>
				    <li><a href="javascript:;" data_val="51#52" data_title="ASN入库#收货" class="usernav_tag"><img width="15" height="15" src="/images/help/base/i-right.png">ASN入库 + 收货</a></li>
				    <li><a href="javascript:;" data_val="0" data_title="完成" class="usernav_tag"><img width="15" height="15" src="/images/help/base/i-right.png">完成</a></li>
				     -->
				</ul>
			</div>
		</div>
		<div style="float: right; width: 82%;">
			<div class="goodsTab">
            	<ul class="usernav_tag_children">
            		<!-- 横向选项卡
                	<li class="chooseTag usernav_tag_children_li"><a href="javascript:;">SKU建立</a></li>
                    <li class="usernav_tag_children_li"><a href="javascript:;" >关系映射</a></li>
                     -->
           		</ul>
                <div class="clr"></div>
            </div>
			<{include file='default/views/default/help.tpl'}>
			
			<div class="to_jump_div">
				<span onclick="" class="iconToPage" title="前往设置页面" style="float: left;cursor: pointer;padding: 0px 2px;"></span>
			</div>
		</div>
	</div>
</div>