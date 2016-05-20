<script type="text/javascript">

$(function(){
	$("#user_account").keyup(function(){
		$("#platform_user_name").val($(this).val());
	});
	
	$('#token_tr').hide();
	$('#one_step').click(function(){
		var user_account = $.trim($("#user_account").val());		
	    if(user_account==''){
	        alertTip('请输入eaby店铺账号');return false;
	    }
		//var short_name = $.trim($("#short_name").val());
	    $("#short_name").val(user_account);
		var short_name = user_account;
		if(short_name==''){
	        alertTip('请输入店铺简称');return false;
	    }
		var platform_user_name = $.trim($("#platform_user_name").val());
		if(platform_user_name==''){
	        alertTip('请输入显示名称');return false;
	    }
		alertTip('账号验证中，请稍候...');
		var param = $('#authForm').serialize();
		//ebay连接参数初始化
        //$('#ebay_auth_link').attr('href','javascript:;').removeAttr('target');
		$.ajax({
	        type:'post',
	        dataType:'json',
	        data:param,
	        url: '/order/platform-user/get-sid',
	        error:function(){},
	        success:function(json){
		        $('#dialog-auto-alert-tip').dialog('close');
	        	if(json.status == 1){
		        	$('#sessionID').val(json.message);
		        	/*
		            *ebay 授权跨域的问题 只能用连接打开
		        	*/
		            //openIframeDialog(json.url,1024 ,600,'账号授权 -> Sign In -> I Agree');
		            $('#ebay_auth_link').attr('href',json.url).attr('target','_blank');
	                $('#ebay_auth_link_btn').click();
	                
		            $('#one_step').hide();
		            $('#two_step').show();
		            $('#three_step').hide();

		            $('#search-module li').css('color','#000');
		            $('#search-module #two').css('color','red');
	        	}else {
	        		alertTip(json.message);
	        		$('#one_step').show();
		            $('#two_step').hide();
		            $('#three_step').hide();

		            $('#search-module li').css('color','#000');
		            $('#search-module #one').css('color','red');
	        	}	            
	        }
	    });
	});
	$('#two_step').click(function(){	
		var param = $('#authForm').serialize();
	    var sessionID = $('#sessionID').val();
	    if(sessionID==''){
	        alertTip('获取sessionID失败');
	        return false;
	    }
		alertTip('获取Token中，请稍候...');
	    $.ajax({
	        type:'post',
	        dataType:'json',
	        data:param,
	        url: '/order/platform-user/get-Token',
	        error:function(){},
	        success:function(json){
		        $('#dialog-auto-alert-tip').dialog('close');
				if(json.status == 0){
					alertTip(json.msg+'获取Token失败,请确认授权账号已经登录ebay');
	        		$('#one_step').show();
		            $('#two_step').hide();
		            $('#three_step').hide();

		            $('#search-module li').css('color','#000');
		            $('#search-module #one').css('color','red');
				}else{
					$('#token_tr').show();
					$('#one_step').hide();
		            $('#two_step').hide();
		            $('#three_step').show();

		            $('#search-module li').css('color','#000');
		            $('#search-module #three').css('color','red');
	            	$('#token').val(json.token);
					alertTip(json.msg);
				}
	        }
	    });
	});
	$('#three_step').click(function(){	
		var param = $('#authForm').serialize();
	    $.ajax({
	        type:'post',
	        dataType:'json',
	        data:param,
	        url: '/order/platform-user/save',
	        error:function(){},
	        success:function(json){				
				if(json.status == 1){
					parent.reLoadWhenClose=1; 
					$('#one_step').hide();
		            $('#two_step').hide();
		            $('#three_step').show();
		            $('#search-module li').css('color','#000');
		            $('#search-module #three').css('color','red');
				}
				$('<div title="操作提示 (Esc)"><p align="">' + json.message + '</p></div>').dialog({
			        autoOpen: true,
			        width: 800,
			        modal: true,
			        show: "slide",
			        buttons: [
			            {
			                text: '关闭后新增账号授权',
			                click: function () {
			                    $(this).dialog("close");
			                	$('#token_tr').hide();
			                    $('#authForm .keyToSearch').val('');
			                    $('#one_step').show();
					            $('#two_step').hide();
					            $('#three_step').hide();
					            $('#search-module li').css('color','#000');
					            $('#search-module #one').css('color','red');
			                }
			            }
			        ],
			        close: function () {
			            $(this).detach();
			        }
			    });
	        }
	    });
	});

	
    $('#search-module li').eq(0).css('color','red');
})
</script>
<style>
#search-module {
    overflow: hidden;
}
</style>
<div id="module-container">
	<div id="search-module">
		<ul>
			<li id="one" style="width: 30%; float: left;">1,填写基本信息并登陆授权</li>
			<li id="two" style="width: 30%; float: left;">2,获取Token</li>
			<li id="three" style="width: 30%; float: left;">3,保存授权</li>
		</ul>
	</div>
	<div id="search-module" class="one">
		<div style="padding: 0">
		    <form id='authForm' onsubmit='return false;'>
    			<table style='width:100%;'>
    				<tr>
    					<td style="text-align: right" width='100'>ebay账号：</td>
    					<td style="padding-top: 20px;">
    						<input placeholder="请填写您的ebay账户" type="text" style="width: 400px;<{if isset($platformUser['user_account'])}>background:#eee;<{/if}>" value="<{if isset($platformUser['user_account'])}><{$platformUser['user_account']}><{/if}>" name="user_account" id="user_account" class="input_text keyToSearch" <{if isset($platformUser['user_account'])}>readonly<{/if}>/>
    						<br />
    						<br />
    					</td>
    				</tr>
    				<!-- 
    				<tr>
    					<td style="text-align: right">简称：</td>
    					<td>
    						<input type="text" style="width: 400px; height: 30px" value="<{if isset($platformUser['short_name'])}><{$platformUser['short_name']}><{/if}>" name="short_name" id="short_name" class="input_text keyToSearch" />
    						<br />
    						<br />
    					</td>
    				</tr>
    				 -->
    				<tr>
    					<td style="text-align: right">显示名称：</td>
    					<td style="padding-top: 20px;">
    						<input placeholder="系统中的显示名称，默认与ebay账户一致" type="text" style="width: 400px;" value="<{if isset($platformUser['platform_user_name'])}><{$platformUser['platform_user_name']}><{/if}>" name="platform_user_name" id="platform_user_name" class="input_text keyToSearch" />
    						<br />
    						<br />
    					</td>
    				</tr>
    				<tr id='token_tr'>
    					<td style="text-align: right">Token：</td>
    					<td style="padding-top: 20px;">
    						<input type="text" style="width: 90%;background:#eee;" id="token" name='token' class="input_text keyToSearch"  readonly/>
    						<br />
    						<br />
    					</td>
    				</tr>
    				<tr>
    					<td>&nbsp;</td>
    					<td>
    						<input type="hidden" value="" id="sessionID" name='sessionID' class='keyToSearch'/>
    						<input type="hidden" value="<{if isset($platformUser['pu_id'])}><{$platformUser['pu_id']}><{/if}>" id="pu_id" name='pu_id'/>
    						
    						<input id="one_step"  style="height: 25px" class="baseBtn step" step='one' type="button" value="填写基本信息并登陆授权">
    						<input id="two_step"  style="width: 100px; height: 25px;display:none;" class="baseBtn step" step='two' type="button" value="获取Token">
    						<input id="three_step" style="width: 100px; height: 25px;display:none;" class="baseBtn step" step='three' type="button" value="保存授权">
    					</td>
    				</tr>
    			</table>
			</form>
		</div>
	</div>
</div>
<a id='ebay_auth_link' href='http://www.baidu.com'  target='_blank'  style='display: block;position:absolute;top:-200px;left:-200px;'><input type='button' id='ebay_auth_link_btn' value='登陆ebay授权'/></a>