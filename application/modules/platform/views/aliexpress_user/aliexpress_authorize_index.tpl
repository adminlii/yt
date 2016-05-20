<script type="text/javascript">

$(function(){
	$("#user_account").keyup(function(){
		$("#platform_user_name").val($(this).val());
	});

	$('#one_step').click(function(){
		var user_account = $.trim($("#user_account").val());		
	    if(user_account==''){
		    //请输入aliexpress店铺账号
	        alertTip($.getMessage('please_enter_aliexpress_shops_account'));return false;
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
		var app_key = $.trim($("#app_key").val());
		if(app_key==''){
			//请输入APP Key
	        alertTip($.getMessage('please_enter_app_key'));return false;
	    }
		var app_signature = $.trim($("#app_signature").val());
		if(app_signature==''){
			//请输入APP Signature
	        alertTip($.getMessage('please_enter_app_signature'));return false;
	    }
		
		
	    //请求中，请稍候...
		alertTip($.getMessage('sys_common_wait'));
		var param = $('#authForm').serialize();
		$.ajax({
	        type:'post',
	        dataType:'json',
	        data:param,
	        url: '/platform/aliexpress-authorize/get-Sid',
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
		            $('#two_step_01').show();
		            $('#three_step').hide();
					$("#code_tr").show();
		            $('#search-module li').css('color','#000');
		            $('#search-module #two_01').css('color','red');
	        	}else {
	        		alertTip(json.message);
	        		$('#one_step').show();
		            $('#two_step_01').hide();
		            $('#three_step').hide();

		            $('#search-module li').css('color','#000');
		            $('#search-module #one').css('color','red');
	        	}	            
	        }
	    });
	});

	$("#two_step_01").click(function(){
		var code = $.trim($("#code").val());		
	    if(code==''){
		    //请输入aliexpress临时授权码
	        alertTip($.getMessage('please_enter_temp_authorization_code'));return false;
	    }
	    
	    $('#one_step').hide();
	    $('#two_step_01').hide();
        $('#two_step_02').show();
        $('#three_step').hide();

        $('#search-module li').css('color','#000');
        $('#search-module #two_02').css('color','red');
	});
	
	$('#two_step_02').click(function(){	
		var param = $('#authForm').serialize();
		//获取临时授权码中，请稍候...
		alertTip($.getMessage('sys_common_wait'));
	    $.ajax({
	        type:'post',
	        dataType:'json',
	        data:param,
	        url: '/platform/aliexpress-authorize/get-Token',
	        error:function(){},
	        success:function(json){
		        $('#dialog-auto-alert-tip').dialog('close');
				if(json.status == 0){
					//获取Token失败
					alertTip($.getMessage('get_token_failed'));
	        		$('#one_step').hide();
		            $('#two_step_02').show();
		            $('#three_step').hide();

		            $('#search-module li').css('color','#000');
		            $('#search-module #two_02').css('color','red');
		            return;
				}else{
					$('#token_tr').show();
					$('#one_step').hide();
		            $('#two_step_02').hide();
		            $('#three_step').show();

		            $('#search-module li').css('color','#000');
		            $('#search-module #three').css('color','red');
	            	$('#token').val(json.data.access_token);

	            	$('#aliId').val(json.data.aliId);
	            	$('#resource_owner').val(json.data.resource_owner);
	            	$('#expires_in').val(json.data.expires_in);
	            	$('#refresh_token').val(json.data.refresh_token);
	            	$('#refresh_token_timeout').val(json.data.refresh_token_timeout);

				}
				alertTip(json.message);
	        }
	    });
	});
	$('#three_step').click(function(){	
		//获取临时授权码中，请稍候...
		alertTip($.getMessage('sys_common_wait'));
		var param = $('#authForm').serialize();
	    $.ajax({
	        type:'post',
	        dataType:'json',
	        data:param,
	        url: '/platform/aliexpress-authorize/save',
	        error:function(){},
	        success:function(json){
	        	$('#dialog-auto-alert-tip').dialog('close');
				alertTip(json.message);
				if(json.status == 1){
					parent.reLoadWhenClose=1; 
					$('#token_tr').hide();
					$('#code_tr').hide();
					$('#one_step').show();
		            $('#two_step_01').hide();
		            $('#two_step_02').hide();
		            $('#three_step').hide();
		            
		            $('#search-module li').css('color','#000');
		            $('#search-module #one').css('color','red');

		            //清空表单
		            $(':input','#authForm')
		            .not(':button, :submit, :reset, :hidden')
		            .val('')
		            .removeAttr('checked')
		            .removeAttr('selected');
		           		            
				}
	        }
	    });
	});

	
    $('#search-module li').eq(0).css('color','red');
})
</script>
<style>
.au_tab td{
	padding: 5px 2px;
}
#token_tr,#code_tr{
	display: none;
}
#search-module {
    overflow: hidden;
}
</style>
<div id="module-container">
	<div id="search-module">
		<ul>
			<li id="one" style="width: 25%; float: left;"><h3>1、<{t}>fill_in_the_basics_and_landing_authorization<{/t}></h3></li><!-- 填写基本信息并登陆授权 -->
			<li id="two_01" style="width: 25%; float: left;"><h3>2、<{t}>aliexpress_fill_in_temporary_authorization_code<{/t}></h3></li><!-- 填写临时授权码 -->
			<li id="two_02" style="width: 25%; float: left;"><h3>3、<{t}>get_token<{/t}></h3></li><!-- 获取Token -->
			<li id="three" style="width: 25%; float: left;"><h3>4、<{t}>save_authorization<{/t}></h3></li><!-- 保存授权 -->
		</ul>
	</div>
	<div id="search-module" class="one">
		<div style="padding: 0">
		    <form id='authForm' onsubmit='return false;'>
    			<table style='width:100%;' class="au_tab">
    				<tr>
    					<td style="text-align: right" width='100'><{t}>shop_account<{/t}>：</td><!-- 店铺 -->
    					<td>
    						<input type="text" style="width: 400px; height: 30px" value="<{if isset($platformUser['user_account'])}><{$platformUser['user_account']}><{/if}>" name="user_account" id="user_account" class="input_text keyToSearch" <{if isset($platformUser['user_account'])}>readonly<{/if}>/>
    					</td>
    				</tr>
    				<tr style='display: none;'>
    					<td style="text-align: right"><{t}>abbreviation<{/t}>：</td><!-- 简称 -->
    					<td>
    						<input type="text" style="width: 400px; height: 30px" value="<{if isset($platformUser['short_name'])}><{$platformUser['short_name']}><{/if}>" name="short_name" id="short_name" class="input_text keyToSearch" />
    					</td>
    				</tr>
    				<tr>
    					<td style="text-align: right"><{t}>display_name<{/t}>：</td><!-- 显示名称 -->
    					<td>
    						<input type="text" style="width: 400px; height: 30px" value="<{if isset($platformUser['platform_user_name'])}><{$platformUser['platform_user_name']}><{/if}>" name="platform_user_name" id="platform_user_name" class="input_text keyToSearch" />
    					</td>
    				</tr>
    				<tr>
    					<td style="text-align: right">APP Key：</td>
    					<td>
    						<input type="text" style="width: 400px; height: 30px" value="<{if isset($platformUser['app_key'])}><{$platformUser['app_key']}><{/if}>" name="app_key" id="app_key" class="input_text keyToSearch" placeholder="速卖通开放平台申请卖家自用APP，即可得到"/>
    					</td>
    				</tr>
    				<tr>
    					<td style="text-align: right">APP Signature：</td>
    					<td>
    						<input type="text" style="width: 400px; height: 30px" value="<{if isset($platformUser['app_signature'])}><{$platformUser['app_signature']}><{/if}>" name="app_signature" id="app_signature" class="input_text keyToSearch" placeholder="速卖通开放平台申请卖家自用APP，即可得到"/>
    					</td>
    				</tr>
    				<tr id="code_tr">
    					<td style="text-align: right"><{t}>aliexpress_temporary_authorization_code<{/t}>：</td><!-- 临时授权码 -->
    					<td>
    						<input type="text" style="width: 400px; height: 30px" id="code" name='code' class="input_text keyToSearch" placeholder="请在同意授权后的页面中，复制临时授权码" />
    					</td>
    				</tr>
    				<tr id="token_tr">
    					<td style="text-align: right">Token：</td>
    					<td>
    						<input type="text" style="width: 400px; height: 30px" id="token" name='token' class="input_text keyToSearch" />
    						
    						<input type="hidden" style="width: 90%; height: 30px" id="aliId" name='aliId' class="input_text keyToSearch" />
    						<input type="hidden" style="width: 90%; height: 30px" id="resource_owner" name='resource_owner' class="input_text keyToSearch" />
    						<input type="hidden" style="width: 90%; height: 30px" id="expires_in" name='expires_in' class="input_text keyToSearch" />
    						<input type="hidden" style="width: 90%; height: 30px" id="refresh_token" name='refresh_token' class="input_text keyToSearch" />
    						<input type="hidden" style="width: 90%; height: 30px" id="refresh_token_timeout" name='refresh_token_timeout' class="input_text keyToSearch" />
    						
    					</td>
    				</tr>
    				<tr>
    					<td>&nbsp;</td>
    					<td>
    						<input type="hidden" value="<{if isset($platformUser['pu_id'])}><{$platformUser['pu_id']}><{/if}>" id="pu_id" name='pu_id'/>
    						<!-- 前往授权 -->
    						<input id="one_step"  style="height: 25px" class="baseBtn step" step='one' type="button" value="<{t}>to_authorize<{/t}>">
    						<!-- 继续 -->
    						<input id="two_step_01"  style="width: 100px; height: 25px;display:none;" class="baseBtn step" step='two_01' type="button" value="<{t}>define<{/t}>">
    						<!-- 获取Token -->
    						<input id="two_step_02"  style="width: 100px; height: 25px;display:none;" class="baseBtn step" step='two_02' type="button" value="<{t}>get_token<{/t}>">
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