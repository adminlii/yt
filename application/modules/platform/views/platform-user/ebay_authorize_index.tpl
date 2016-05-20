<script type="text/javascript">

$(function(){
	$("#user_account").keyup(function(){
		$("#platform_user_name").val($(this).val());
	});

	$('#token_tr').hide();
	$('#one_step').click(function(){
		var user_account = $.trim($("#user_account").val());		
	    if(user_account==''){
		    //请输入eaby店铺账号
	        alertTip($.getMessage('please_enter_eaby_shops_account'));return false;
	    }
		var short_name = user_account;//$.trim($("#short_name").val());
		if(short_name==''){
			//请输入店铺简称
	        alertTip($.getMessage('please_enter_a_shops_referred'));return false;
	    }
		var platform_user_name = $.trim($("#platform_user_name").val());
		if(platform_user_name==''){
			//请输入显示名称
	        alertTip($.getMessage('please_enter_a_display_name'));return false;
	    }
	    //请求中，请稍候...
		alertTip($.getMessage('sys_common_wait'));
		var param = $('#authForm').serialize();
		$.ajax({
	        type:'post',
	        dataType:'json',
	        data:param,
	        url: '/platform/platform-user/get-sid',
	        error:function(){},
	        success:function(json){
		        $('#dialog-auto-alert-tip').dialog('close');
	        	if(json.status == 1){
		        	$('#sessionID').val(json.message);
		            /*
		             *ebay 授权跨域的问题 只能用连接打开
		        	 */
		        	//账号授权 -> Sign In -> I Agree
		            //openIframeDialog(json.url,1024 ,600,$.getMessage('authorized_account') + ' -> Sign In -> I Agree');
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
		    //获取SessionID失败
	        alertTip($.getMessage('failed_to_get_SessionID'));
	        return false;
	    }
		//获取Token中，请稍候...
		alertTip($.getMessage('sys_common_wait'));
	    $.ajax({
	        type:'post',
	        dataType:'json',
	        data:param,
	        url: '/platform/platform-user/get-token',
	        error:function(){},
	        success:function(json){
		        $('#dialog-auto-alert-tip').dialog('close');
				if(json.status == 0){
					//获取Token失败
					alertTip($.getMessage('get_token_failed')+json.msg);
	        		$('#one_step').hide();
		            $('#two_step').show();
		            $('#three_step').hide();

		            $('#search-module li').css('color','#000');
		            $('#search-module #two').css('color','red');
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
	        url: '/platform/platform-user/save',
	        error:function(){},
	        success:function(json){
				alertTip(json.message);
				if(json.status == 1){
					parent.reLoadWhenClose=1; 
					$('#token_tr').hide();
					$('#one_step').hide();
		            $('#two_step').hide();
		            $('#three_step').show();
		            $('#search-module li').css('color','#000');
		            $('#search-module #three').css('color','red');
				}
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
			<li id="one" style="width: 33%; float: left;"><h3>1、<{t}>fill_in_the_basics_and_landing_authorization<{/t}></h3></li><!-- 填写基本信息并登陆授权 -->
			<li id="two" style="width: 33%; float: left;"><h3>2、<{t}>get_token<{/t}></h3></li><!-- 获取Token -->
			<li id="three" style="width: 33%; float: left;"><h3>3、<{t}>save_authorization<{/t}></h3></li><!-- 保存授权 -->
		</ul>
	</div>
	<div id="search-module" class="one">
		<div style="padding: 0">
		    <form id='authForm' onsubmit='return false;'>
    			<table style='width:100%;'>
    				<tr>
    					<td style="text-align: right" width='100'><{t}>ebay_account<{/t}>：</td><!-- ebay账号 -->
    					<td>
    						<input type="text" style="width: 400px; height: 30px" value="<{if isset($platformUser['user_account'])}><{$platformUser['user_account']}><{/if}>" name="user_account" id="user_account" class="input_text keyToSearch" <{if isset($platformUser['user_account'])}>readonly<{/if}>/>
    						<br />
    						<br />
    					</td>
    				</tr>
    				<tr style='display: none;'>
    					<td style="text-align: right"><{t}>abbreviation<{/t}>：</td><!-- 简称 -->
    					<td>
    						<input type="text" style="width: 400px; height: 30px" value="<{if isset($platformUser['short_name'])}><{$platformUser['short_name']}><{/if}>" name="short_name" id="short_name" class="input_text keyToSearch" />
    						<br />
    						<br />
    					</td>
    				</tr>
    				<tr>
    					<td style="text-align: right"><{t}>display_name<{/t}>：</td><!-- 显示名称 -->
    					<td>
    						<input type="text" style="width: 400px; height: 30px" value="<{if isset($platformUser['platform_user_name'])}><{$platformUser['platform_user_name']}><{/if}>" name="platform_user_name" id="platform_user_name" class="input_text keyToSearch" />
    						<br />
    						<br />
    					</td>
    				</tr>
    				<tr id="token_tr">
    					<td style="text-align: right">Token：</td>
    					<td>
    						<input type="text" style="width: 90%; height: 30px" id="token" name='token' class="input_text keyToSearch" />
    						<br />
    						<br />
    					</td>
    				</tr>
    				<tr>
    					<td>&nbsp;</td>
    					<td>
    						<input type="hidden" value="" id="sessionID" name='sessionID'/>
    						<input type="hidden" value="<{if isset($platformUser['pu_id'])}><{$platformUser['pu_id']}><{/if}>" id="pu_id" name='pu_id'/>
    						<!-- 前往授权 -->
    						<input id="one_step"  style="height: 25px" class="baseBtn step" step='one' type="button" value="<{t}>to_authorize<{/t}>">
    						<!-- 获取Token -->
    						<input id="two_step"  style="width: 100px; height: 25px;display:none;" class="baseBtn step" step='two' type="button" value="<{t}>get_token<{/t}>">
    						<!-- 保存授权 -->
    						<input id="three_step" style="width: 100px; height: 25px;display:none;" class="baseBtn step" step='three' type="button" value="<{t}>save_authorization<{/t}>">
    					</td>
    				</tr>
    			</table>
			</form>
		</div>
	</div>
</div>
<a id='ebay_auth_link' href='http://www.baidu.com'  target='_blank'  style='display: block;position:absolute;top:-200px;left:-200px;'>
<input type='button' id='ebay_auth_link_btn' value='登陆ebay授权'/></a>