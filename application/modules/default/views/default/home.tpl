<style type="text/css">
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
var bulletinJson = <{$bulletin}>;
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
		<div><{$content}></div>
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
						<h2 class="ntitle"><{t}>账户总览<{/t}></h2>
					</span>
				</div>
				<table width="100%" cellspacing="0" cellpadding="0" border="0"
					class="table-module">
					<tbody>
						<tr class="table-module-title">
							<td>客户代码</td> <{if $level}>
							<td>客户等级</td><{/if}>
							<td>运费余额</td>
							<td><{t}>押金<{/t}></td>
							<td><{t}>总信用额度<{/t}></td>
							<td><{t}>剩余信用额度<{/t}></td>
						</tr>
					</tbody>
					<tbody>
						<tr class="table-module-b1">
							<td><{$customer_code}></td> 
							<{if $level}><td style="color: orange;"><{$level}></td><{/if}>
							<td<{if $zonglan.a lt 0}>style='color:red;'<{/if}>><{$zonglan.a}></td>
							<td><{$zonglan.b}></td>
							<td><{$zonglan.c}></td>
							<td><{$zonglan.d}></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div style='width: 30%; float: left;' class='nbox_c'>
				<div class="ntitle">
					<span style="">
						<h2 class="ntitle"><{t}>订单概况<{/t}></h2>
					</span>
				</div>
				<table width="100%" cellspacing="0" cellpadding="0" border="0"
					class="table-module">
					<tr class='table-module-title'>
						<td width='120'></td>
						<td><{t}>昨日数量<{/t}></td>
						<td><{t}>今日数量<{/t}></td>
					</tr>
					<tbody id="orderData">
						<tr class='table-module-b1'>
							<td><{t}>创建数<{/t}></td>
							<td><{$create_count}></td>
							<td><{$create_count_today}></td>
						</tr>
						<tr class='table-module-b1'>
							<td width='120'><{t}>收货数<{/t}></td>
							<td><{$checkin_count}></td>
							<td><{$checkin_count_today}></td>
						</tr>
						<tr class='table-module-b1'>
							<td width='120'><{t}>出货数<{/t}></td>
							<td><{$checkout_count}></td>
							<td><{$checkout_count_today}></td>
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
							<h2 class="ntitle"><{t}>联系人信息<{/t}></h2>
						</span>
					</div>
					<table width="100%" cellspacing="0" cellpadding="0" border="0"
						class="table-module">
						<tbody id="asnData">
							<tr class='table-module-title'>
								<td width='120'><{t}>联系人<{/t}></td>
								<td><{t}>服务热线<{/t}></td>
								<td><{t}>即时沟通<{/t}></td>
							</tr>
							<tr class='table-module-b1'>
								<td><{t}>客服员<{/t}>:<{if
									isset($shippersupporter)}><{$shippersupporter.st_name}><{/if}></td>
								<td><{if
									isset($shippersupporter)}><{$shippersupporter.st_phone}><{/if}></td>
								<td><{if isset($shippersupporter)}><a
									href="tencent://message/?uin=<{$shippersupporter.st_qq}>&amp;Site=有事Q我&amp;Menu=yes"><img
										style="border: 0px;"
										src=http://pub.idqqimg.com/wpa/images/counseling_style_52.png></a><{/if}>
								</td>
							</tr>
							<tr class='table-module-b1'>
								<td><{t}>业务员<{/t}>:<{if
									isset($saller)}><{$saller.st_name}><{/if}></td>
								<td><{if isset($saller)}><{$saller.st_phone}><{/if}></td>
								<td><{if isset($saller)}><a
									href="tencent://message/?uin=<{$saller.st_qq}>&amp;Site=有事Q我&amp;Menu=yes"><img
										style="border: 0px;"
										src=http://pub.idqqimg.com/wpa/images/counseling_style_52.png></a><{/if}>
								</td>
							</tr>
							<tr class='table-module-b1'>
								<td><{t}>结算员<{/t}>:<{if
									isset($dunner)}><{$dunner.st_name}><{/if}></td>
								<td><{if isset($dunner)}><{$dunner.st_phone}><{/if}></td>
								<td><{if isset($dunner)}><a
									href="tencent://message/?uin=<{$dunner.st_qq}>&amp;Site=有事Q我&amp;Menu=yes"><img
										style="border: 0px;"
										src=http://pub.idqqimg.com/wpa/images/counseling_style_52.png></a><{/if}>
								</td>
							</tr>
						</tbody>
					</table>
				</div>






				<div style='width: 33.5%;float:left;' class='nbox_c'>
					<div class="ntitle">
						<span style="">
							<h2 class="ntitle"><{t}>订单追踪<{/t}></h2>
						</span>
					</div>
					<form action="/default/index/get-track-detail" method='post'>
						<textarea rows="" cols=""
							style='width: 99%; height: 70px; overflow: auto; font-size: 13px;'
							placeholder='<{t}>如需查询多个单号，请使用","隔开<{/t}>' name='code'></textarea>
						<div style='height: 5px;'></div>
						<input type="submit" class="baseBtn submitToSearch"
							value="<{t}>查询<{/t}>">
					</form>
				</div>
				<div class='clear'></div>
			</div>

		</div>

	</div>
	<div style='width: 32.6%; float: left; clear: both;margin-left:8px;' class='nbox_c'>
		<div class="ntitle">
			<span style="">
				<h2 class="ntitle"><{t}>我要寄件<{/t}></h2>
			</span>
		</div>
		<div style='padding: 5px;'>
			<input type="button" value="<{t}>单票录入<{/t}>" class="baseBtn "
				style='padding: 5px 100px;'
				onclick="leftMenu('52','<{t}>单票录入<{/t}>','/order/order/create?quick=52')">
		</div>
		<div style='padding: 5px;'>
			<input type="button" value="<{t}>批量上传<{/t}>"
				class="baseBtn submitToSearch" style='padding: 5px 100px;'
				onclick="leftMenu('24','<{t}>批量上传<{/t}>','/order/order/import?quick=24')">
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

</script>