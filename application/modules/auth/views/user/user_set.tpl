<link rel="stylesheet" type="text/css" href="/css/public/step.css">
<link rel="stylesheet" type="text/css" href="/css/public/help.css">
<script type="text/javascript" src="/js/json2.js"></script>
<script type="text/javascript" src="/js/help.js?20140128"></script>
<script type="text/javascript" src="/js/step.js?20140128"></script>
<style>
#main_left p {
	padding-left: 20px;
	line-height: 15px;
	height: 16px;
	padding-top: 3px;
}

.priority_login {
	width: 13px;
	height: 13px;
	line-height: 13px;
	margin-right: 2px;
	vertical-align: -2px;
	*vertical-align: middle;
	_vertical-align: 3px;
}

.publicBtn2 {
	background: url("/images/base/btn_public02.gif") no-repeat scroll 0 0 rgba(0, 0, 0, 0);
	border: medium none;
	color: #FFFFFF;
	cursor: pointer;
	height: 24px;
	line-height: 24px;
	text-align: center;
	width: 55px;
}

.publicBtn6 {
	background: url("/images/base/btn_public06.gif") no-repeat scroll 0 0 rgba(0, 0, 0, 0);
	border: medium none;
	color: #333333;
	cursor: pointer;
	height: 24px;
	line-height: 24px;
	text-align: center;
	width: 71px;
}
</style>
<script type="text/javascript">
	$(function(){
		//变换头像
		$("#avatar").click(function(){
			setAvatarInfo();
		});
	})

	function getRand(min, max){
		return Math.floor(Math.random()*(max-min)+min);
	}

	function setAvatarInfo(){
		var imgJsonStr = '['+
						'{"item":{"url":"animal_avatar_1.png","title":"我的目标是可达鸭,呱呱..."}},' +
						'{"item":{"url":"animal_avatar_2.png","title":"我是汪星人,汪汪."}},' +
						'{"item":{"url":"animal_avatar_3.png","title":"猜猜我和Twitter什么关系?"}},' +
						'{"item":{"url":"animal_avatar_4.png","title":"我是黑夜使者."}},' +
						'{"item":{"url":"animal_avatar_5.png","title":"没错,我就是来卖萌的."}},' +
						'{"item":{"url":"animal_avatar_6.png","title":"其实,我没看出来这货是什么."}},' +
						'{"item":{"url":"animal_avatar_7.png","title":"吾乃火星使者,颤抖吧,凡人!!!"}},' +
						'{"item":{"url":"animal_avatar_8.png","title":"小刺猬,好可耐."}},' +
						'{"item":{"url":"animal_avatar_9.png","title":"这货,好像是考拉?恩,没错..."}},' +
						'{"item":{"url":"animal_avatar_10.png","title":"继续卖萌."}},' +
						'{"item":{"url":"animal_avatar_11.png","title":"你们别欺负我,腾讯跟我是亲戚!"}},' +
						'{"item":{"url":"animal_avatar_12.png","title":"进击的大猩猩."}},' +
						'{"item":{"url":"animal_avatar_13.png","title":"熊猫(*￣(エ)￣) "}},' +
						'{"item":{"url":"animal_avatar_14.png","title":"巴哥犬,么么哒."}}' +
						']';
		var imgJson = JSON.parse(imgJsonStr);
		var rand = getRand(1,14);
		var imgDetailJson = imgJson[rand].item;
		$("#avatar").attr("src","/images/modeling/" + imgDetailJson.url);
		$("#avatar").attr("title",imgDetailJson.title);
	}
	$(function(){
	    $('#transactionDetails').click(function(){
	    	leftMenu('64','充值明细','/customer/balance-log/in?quick=64');
	    });
	})
</script>
<div id="module-container">
	<!-- 
	<div class="Tab">
		<ul>
			<li id="normal" style="position: relative;" class="mainStatus ">
				<a href="javascript:;" class="statusTag " onclick="leftMenu('submiter_list','发件人资料','/order/submiter/list')">
					<span class="order_title">发件人资料</span>
				</a>
			</li>
			<li id="abnormal" style="position: relative;" class="mainStatus chooseTag">
				<a href="javascript:;" class="statusTag "  onclick="leftMenu('user_set','修改密码','/auth/user/user-set')">
					<span class="order_title">修改密码</span>
				</a>
			</li>
		</ul>
	</div>
	 -->
	<!-- 修改密码--开始 -->
	<form id="password_params" style='padding:8px 0px;'>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module" style="height: 120px; margin-bottom: 5px;" id="password_table">
			
			<tr class="table-module-b1">
				<td>
					<div style="margin-bottom: 5px;">
						<span class="searchFilterText" style="width: 90px; padding-top: 5px;">原始密码：</span>
						<input type="password" class="input_text" id="old_password" name="old_password" value="" style="width: 275px; height: 30px">
						<span class="tip-error-message" style="margin-left: 5px; display: none;" id="old_password_message"></span>
					</div>
					<div style="margin-bottom: 5px;">
						<span class="searchFilterText" style="width: 90px; padding-top: 5px;">新密码：</span>
						<input type="password" class="input_text" id="new_password" name="new_password" value="" style="width: 275px; height: 30px">
						<span class="tip-error-message" style="margin-left: 5px; display: none;" id="new_password_message"></span>
					</div>
					<div style="margin-bottom: 5px;">
						<span class="searchFilterText" style="width: 90px; padding-top: 5px;">确认新密码：</span>
						<input type="password" class="input_text" id="new_password_again" name="new_password_again" value="" style="width: 275px; height: 30px">
						<span class="tip-error-message" style="margin-left: 5px; display: none;" id="new_password_again_message"></span>
					</div>
					<div style="padding-left: 90px; padding-top: 5px;">
						<input type="hidden" name='type' value='password'>
						<input id="mod_password" type="button" class="baseBtn" value="确认" />
					</div>
				</td>
			</tr>
		</table>
	</form>
</div>
<script type="text/javascript">
$(function(){
	/**
	 *	修改密码
	 */
	$("#mod_password").click(function(){
		var checkBol = checkPassword();
		if(checkBol){
			var params = $("#password_params").serialize(); 
			callUserSet(params);
		}
	});

});

/**
 * 发送请求
 */
function callUserSet(params){
	alertLoadTip("系统正在努力处理中...");
	$.ajax({
		type: "POST",
		data: params,
		dataType:"json",
		url: "/auth/user/modify-User-Profile",
		async:true,
		success:function(jsonData){
			var tips;
			if(isJson(jsonData)){
				if(jsonData['state'] == 1){
					tips = "<span class='tip-success-message'>" + jsonData.message + "</span>";
				}else if(jsonData['state'] == 0){
					tips = "<span class='tip-error-message'>" + jsonData.message + "</span>"; 
				}else{
	                if (jsonData.errorMessage == null)return;
	                $.each(jsonData.errorMessage, function (key, val) {
	                	tips += "<span class='tip-error-message'>" + val + "</span>";
	                });
				}
			}else{
				tips = "<span class='tip-error-message'>请求异常，请联系相关技术人员.</span>"; 
			}
			$("#dialog-auto-alert-tip").dialog("close");
			alertTip(tips);
		}
		});
}

/**
 * 验证密码规则
 */
function checkPassword(){
	var old_password =  $.trim($('#old_password').val());
	$('#old_password').val(old_password);
	var old_password_message =  $('#old_password_message');
	
	var new_password =  $.trim($('#new_password').val());
	$('#new_password').val(new_password);
	var new_password_message =  $('#new_password_message');
	
	var new_password_again = $.trim($('#new_password_again').val());
	$('#new_password_again').val(new_password_again);
	var new_password_again_message = $('#new_password_again_message'); 

	//var reg = new RegExp(/^(?=.*[a-z])[a-z0-9]+/ig);// 创建正则表达式对象
	var reg = /^(?=.*[a-z])[a-z0-9]+/ig;
	//re = new RegExp("ain","g"); 

	　　//r = s.match(re);

	//验证原始密码
	var bol1 = true;
	old_password_message.show();
	if(old_password == ""){
		old_password_message.html("请填写原始密码.");
		bol1 = false;
	}else{
		old_password_message.hide();
		old_password_message.html("");
	}

	//验证新密码
	var bol2 = true;
	new_password_message.show();
	if(new_password == ""){
		new_password_message.html("请填写密码.");
		bol2 = false;
	}else if(new_password.length < 6){
		new_password_message.html("密码不能少于6位.");
		bol2 = false;
	}else if(new_password.length > 16){
		new_password_message.html("密码不能超过16位.");
		bol2 = false;
	}else if(!new_password.match(reg)){
		new_password_message.html("密码必须存在英文字母和数字,请检查.");
		bol2 = false;
	}else{
		new_password_message.hide();
		new_password_message.html("");
	}

	//验证确认新密码
	var bol3 = true;
	new_password_again_message.show();
	if(new_password_again == ""){
		new_password_again_message.html("请填写密码.");
		bol3 = false;
	}else if(new_password_again.length < 6){
		new_password_again_message.html("密码不能少于6位.");
		bol3 = false;
	}else if(new_password_again.length > 16){
		new_password_again_message.html("密码不能超过16位.");
		bol3 = false;
	}else if(!new_password_again.match(reg)){
		new_password_again_message.html("密码必须存在英文字母和数字,请检查.");
		bol3 = false;
	}else if(new_password_again != new_password){
		new_password_again_message.html("两次密码不一致,请检查.");
		bol3 = false;
	}else{
		new_password_again_message.hide();
		new_password_again_message.html("");
	}
	if(bol1 && bol2 && bol3){
		return true;
	}else{
		return false;
	}
}

/**
 * 弹出等等对话框
 */
function alertLoadTip(str){
	var tips = "<span class='tip-load-message'>" + str + "</span>";
	alertTip(tips);
}
</script>
