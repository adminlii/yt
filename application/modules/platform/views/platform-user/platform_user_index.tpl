<script type="text/javascript">
    EZ.url = '/platform/platform-user/';
    var quickId = '<{$quickId}>';
    EZ.getListData = function (json) {
        var html = '';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
            html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
            html += "<td class='ec-center'>" + (i++) + "</td>";
            
            html += "<td >" + val.platform + "</td>";
            html += "<td ><a href='javascript:;' title='授权验证' class='auth_check' pu_id='"+val.pu_id+"'>" + val.user_account+' ['+val.platform_user_name+']' + "</a></td>";
            //html += "<td >" + val.E4 + "</td>";
            html += "<td >" + val.status + "</td>";
            html += "<td >" + val.add_time + "</td>";
            html += "<td >" + val.update_time + "</td>";
            html += "<td>";
            html += "<a href=\"javascript:editById(" + val.pu_id + ")\">" + EZ.edit + "</a>";
            html +="&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:deleteById(" + val.pu_id + ")\">" + EZ.del + "</a>";
            if(val.platform == 'ebay' || val.platform == 'aliexpress'){
                //重新授权
            	html +="&nbsp;&nbsp;|&nbsp;&nbsp;<a href='javascript:;' class='reAuth' platform='"+val.platform+"' user_account='"+val.user_account+"' short_name='"+val.short_name+"' pu_id='"+val.pu_id+"'><{t}>重新授权<{/t}></a>";//重新授权
            }
			//任务初始化
            if(val.platform == 'ebay' || val.platform == 'amazon'){
	            //html +="&nbsp;&nbsp;|&nbsp;&nbsp;<a href='javascript:;' class='initAcc' user_account='"+val.user_account+"' short_name='"+val.short_name+"' platform='"+val.platform+"'><{t}>定时任务初始化<{/t}></a>";//定时任务初始化
            }
            html +="</td>";
            html += "</tr>";
        });
        return html;
    }

    $(function(){
        $('.reAuth').live('click',function(){
        	//if(!window.confirm("确认重新授权码?")){
                //return false;
            //}
        	var w = parent.windowWidth();
            var h = parent.windowHeight();
            var pu_id = $(this).attr('pu_id');
            var platform = $(this).attr('platform');
			var url = '';
            var title = '';
            
            if(platform == 'ebay'){
            	url = '/platform/platform-user/new-ebay-auth/pu_id/' + pu_id;
            	title = $.getMessage('eBay_account_reauthorization');
            }else if(platform == 'aliexpress'){
            	url = '/platform/aliexpress-authorize/authorize/pu_id/' + pu_id;
            	title = $.getMessage('aliexpress_account_reauthorization');
            }
            //ebay账号重新授权
            parent.openIframeDialogNew(url,w-100,h-80,title,quickId,paginationCurrentPage,paginationPageSize);
        	
        });
        $('.initAcc').live('click',function(){
			//确认重新对该账号初始化定时任务
            if(!window.confirm($.getMessage('task_initialization_confirm'))){
                return;
            }
            var acc = $(this).attr('user_account');
            var platform = $(this).attr('platform');
        	$.ajax({
    	        type:'post',
    	        dataType:'html',
    	        data:{'acc':acc,'platform':platform},
    	        url: '/auth/run-control/init-acc',
    	        error:function(){},
    	        success:function(html){    				
    				alertTip(html,800);
    	        }
    	    });

        });

        $('#addEbayAuth').click(function(){
            var w = parent.windowWidth();
            var h = parent.windowHeight();            
            //新增ebay账号授权
            parent.openIframeDialogNew('/platform/platform-user/new-ebay-auth/',w-100,h-80,'<{t}>新增ebay账号授权<{/t}>',quickId,paginationCurrentPage,paginationPageSize);
        });

		$('#createAliexpressButton').click(function(){
			 var w = parent.windowWidth();
	         var h = parent.windowHeight();            
	         //
	         parent.openIframeDialogNew('/platform/aliexpress-authorize/authorize/',w-100,h-80,'<{t}>新增aliexpress账号授权<{/t}>',quickId,paginationCurrentPage,paginationPageSize);
		});
        
        $('#ez-wms-edit-dialog0').dialog({
            autoOpen: false,
            width: 600,
            maxHeight: 400,
            modal: true,
            show: "slide",
            buttons: [
                {
                    text: 'Save',
                    click: function () {
                        var this_ = $(this);
                        var param = $('#ez-wms-edit-dialog0_form').serialize();
                        $.ajax({
                	        type:'post',
                	        dataType:'json',
                	        data:param,
                	        url: '/platform/platform-user/save-amazon',
                	        error:function(){},
                	        success:function(json){
                				if(json.status == 1){
                					loadData(paginationCurrentPage,paginationPageSize);
                                    this_.dialog("close");
                				}
                				alertTip(json.msg);
                	        }
                	    });
                    }
                },
                {
                    text: 'Close',
                    click: function () {
                        $(this).dialog("close");
                    }
                }
            ],
            close: function () {
            }
        });
        $('#createAmazonButton').click(function(){
            $('#ez-wms-edit-dialog0').dialog('open');
        });

        //显示客户能够使用的授权按钮
        showAuthBtn();
    });    

    /*
     * 显示授权按钮
     */
    function showAuthBtn(){
		$.each($(".platform_show"),function(){
			var tmp_val = $(this).attr('data_val');
			$("." + tmp_val + "_auth_btn").show();
		});
    }
    
    
    
    
  
    $(function(){
    	//添加马帮账号对话框
        $('#ez-wms-edit-dialog1').dialog({
                autoOpen: false,
                width: 600,
                maxHeight: 400,
                modal: true,
                show: "slide",
                buttons: [
                    {
                        text: 'Save',
                        click: function () {
                            var this_ = $(this);
                            var param = $('#ez-wms-edit-dialog1_form').serialize();
                            $.ajax({
                    	        type:'post',
                    	        dataType:'json',
                    	        data:param,
                    	        url: '/platform/platform-user/save-mabang',
                    	        error:function(){},
                    	        success:function(json){
                    				if(json.status == 1){
                    					loadData(paginationCurrentPage,paginationPageSize);
                                        this_.dialog("close");
                    				}
                    				alertTip(json.msg);
                    	        }
                    	    });
                        }
                    },
                    {
                        text: 'Close',
                        click: function () {
                            $(this).dialog("close");
                        }
                    }
                ],
                close: function () {
                }
            });
    	
	    	$("#createMabangButton").click(function(){
	    	$("#ez-wms-edit-dialog1").dialog('open');
	    	
    	});
   })
  
    
</script>
<style>
.auth_btn{
	display: none;
}
</style>
<div id="module-container">
	<div id="ez-wms-edit-dialog" style="display: none;">
		<table class="dialog-module" border="0" cellpadding="0" cellspacing="0">
			<tbody>
				<input type="hidden" name="E0" id="E0" value="" />
				<tr>
					<td class="dialog-module-title"><{t}>data_platform<{/t}>:</td><!-- 平台 -->
					<td>
						<input type="text" name="E1" id="E1" class="input_text" disabled="disabled" />
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>user_account<{/t}>:</td><!-- 账号名 -->
					<td>
						<input type="text" name="E2" id="E2" class="input_text" disabled="disabled" />
					</td>
				</tr>
				<tr style="display: none;">
					<td class="dialog-module-title"><{t}>abbreviation<{/t}>:</td><!-- 简称 -->
					<td>
						<input type="text" name="E4" id="E4" class="input_text" />
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>display_name<{/t}>:</td><!-- 显示名称 -->
					<td>
						<input type="text" name="E10" id="E10" class="input_text" />
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>status<{/t}>:</td><!-- 状态 -->
					<td>
						<select name="E6" id="E6" class='input_select' style="width:175px;">
							<option value="1"><{t}>enable<{/t}></option><!-- 可用 -->
							<option value="0"><{t}>disable<{/t}></option><!-- 不可用 -->
						</select>
					</td>
				</tr>
				
				<tr>
					<td class="dialog-module-title"><{t}>site<{/t}>:</td><!-- 站点 -->
					<td>
						<!-- 
						<input type="text" name="E8" id="E8" class="input_text" placeholder="Amazon必填">&nbsp;
						 -->
						<select name='E8' id='E8' class='input_text' style="width:175px;">
							<option value=""><{t}>pleaseSelected<{/t}></option><!-- 请选择 -->
							<option value="CA">[CA] 加拿大</option>
							<option value="US">[US] 美国</option>
							<option value="DE">[DE] 德国</option>
							<option value="ES">[ES] 西班牙</option>
							<option value="FR">[FR] 法国</option>
							<option value="IN">[IN] 印度</option>
							<option value="IT">[IT] 意大利</option>
							<option value="UK">[UK] 英国</option>
							<option value="JP">[JP] 日本</option>
							<option value="CN">[CN] 中国(大陆地区)</option>
						</select>
						&nbsp;<{t}>amazon_required<{/t}>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>sales_id<{/t}>:</td><!-- 销售ID -->
					<td>
						<input type="text" name="E9" id="E9" class="input_text" placeholder="<{t}>amazon_required<{/t}>"/>&nbsp;
					</td>
				</tr>
				
				<tr>
					<td class="dialog-module-title"><{t}>secret_key_id<{/t}>:</td><!-- 秘钥ID -->
					<td>
						<input type="text" name="E7" id="E7" class="input_text" placeholder="<{t}>amazon_required<{/t}>"/>&nbsp;
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>secret_key<{/t}>:</td><!-- 秘钥 -->
					<td>
						<input type="text" name="E3" id="E3" class="input_text" placeholder="eBay、<{t}>amazon_required<{/t}>" />&nbsp;
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="search-module">
		<form id="searchForm" name="searchForm" class="submitReturnFalse">
			<input type="hidden" value="" id="filterActionId" />
			<div class="pack_manager_content" style="padding: 0">
				<table width="100%" cellspacing="0" cellpadding="0" border="0" id="searchfilterArea">
					<tbody>
						<tr>
							<td>
								<div style="width: 90px;" class="searchFilterText"><{t}>data_platform<{/t}>：</div><!-- 平台 -->
								<div class="pack_manager">
									<input type="hidden" class="input_text keyToSearch" id="platform" name="platform">
									<a class="link_is_platform" id="link_is_platform" onclick="searchFilterSubmit('platform','',this)" href="javascript:void(0)"><{t}>all<{/t}><span></span></a>
									<{foreach from=$platform item=i key=k}>
										<a class="link_is_platform platform_show" id="link_is_platform0" onclick="searchFilterSubmit('platform','<{$k}>',this)" href="javascript:void(0)" data_val="<{$k}>"><{$i.platform_name}><span></span></a>
									<{/foreach}>
									<!-- 
									<a class="link_is_platform" id="link_is_platform0" onclick="searchFilterSubmit('platform','ebay',this)" href="javascript:void(0)">eBay<span></span></a>
									<a class="link_is_platform" id="link_is_platform1" onclick="searchFilterSubmit('platform','amazon',this)" href="javascript:void(0)">Amazon<span></span></a>
									<a class="link_is_platform" id="link_is_platform1" onclick="searchFilterSubmit('platform','amazon',this)" href="javascript:void(0)">Amazon<span></span></a>
									 -->
								</div>
							</td>
						</tr>
					</tbody>
					<tbody>
						<tr>
							<td>
								<div style="width: 90px;" class="searchFilterText"><{t}>状态<{/t}>：</div><!-- 平台 -->
								<div class="pack_manager">
									<input type="hidden" class="input_text keyToSearch" id="status" name="status">
									<a onclick="searchFilterSubmit('status','',this)" href="javascript:void(0)"><{t}>all<{/t}><span></span></a>									
									<a onclick="searchFilterSubmit('status','1',this)" href="javascript:void(0)"><{t}>启用<{/t}><span></span></a>
									<a onclick="searchFilterSubmit('status','0',this)" href="javascript:void(0)"><{t}>禁用<{/t}><span></span></a>
									
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="search-module-condition">
				<table>
					<tr>
						<td width="265px">
							<span class="searchFilterText" style="width: 90px;"><{t}>user_account<{/t}>：</span><!-- 账号名 -->
							<input type="text" name="user_account" id="user_account" class="input_text keyToSearch" placeholder="<{t}>like_search_before&after<{/t}>" />
			        	</td>
			        	<td style="display: none;">
			        		<span class="searchFilterText" style="width: 50px;"><{t}>abbreviation<{/t}>：</span><!-- 简称 -->
					        <input type="text" name="E4" id="E4" class="input_text keyToSearch" />
			        	</td>
			        	<td>
							&nbsp;&nbsp;<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" />
			        	</td>
					</tr>
				</table>
				<!-- 
				<div style="padding: 0">
					账号名：
					<input type="text" name="E2" id="E2" class="input_text keyToSearch" />
					简称：
					<input type="text" name="E4" id="E4" class="input_text keyToSearch" />
					&nbsp;&nbsp;
					<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" />
					<input type="button" class="baseBtn" value="添加amazon账号" id="createAmazonButton" style='float:right;margin-left:5px;'>
					<input type="button" value="添加eBay账户" class="baseBtn"  style='float:right' id='addEbayAuth'/>
				</div>
				 -->
			</div>
		</form>
	</div>
	<div id="module-table">
		<div class="opration_area">
    		<div>
    		    <!-- 添加Mabang账号 -->
    			<input type="button" class="baseBtn auth_btn mabang_auth_btn" value="<{t}>添加mabang账号<{/t}>" id="createMabangButton" style='float:right;margin-left:5px;'>
    			<!-- 添加Aliexpress账号 -->
    			<input type="button" class="baseBtn auth_btn aliexpress_auth_btn" value="<{t}>添加aliexpress账号<{/t}>" id="createAliexpressButton" style='float:right;margin-left:5px;'>
    			<!-- 添加Amazon账号 -->
    			<input type="button" class="baseBtn auth_btn amazon_auth_btn" value="<{t}>add_amazon_account<{/t}>" id="createAmazonButton" style='float:right;margin-left:5px;'>
				<!-- 添加eBay账户 -->
				<input type="button" class="baseBtn auth_btn ebay_auth_btn" value="<{t}>add_eBay_account<{/t}>"  id='addEbayAuth' style='float:right' />
    		
    			<a href='/file/平台订单授权手册.doc' target='_blank' style='float:right;line-height:30px;margin-right:10px;'><{t}>平台订单授权指引<{/t}></a>
    		</div>
    	</div>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
			<tr class="table-module-title">
				<td width="3%" class="ec-center">NO.</td>
				<td width="150px"><{t}>data_platform<{/t}></td>
				<td ><{t}>user_account<{/t}>[<{t}>display_name<{/t}>]</td>
				<!-- 
				<td><{t}>abbreviation<{/t}></td>
				 -->
				<td width="80px"><{t}>status<{/t}></td>
				<td width="120px"><{t}>添加时间<{/t}></td>
				<td width="120px"><{t}>修改时间<{/t}></td>
				<td width="280px"><{t}>operate<{/t}></td>
			</tr>
			<tbody id="table-module-list-data"></tbody>
		</table>
	</div>
	<div class="pagination"></div>
</div>
<div id="ez-wms-edit-dialog0" style="display: none;" title='添加amazon账号' class='dialog-edit-alert-tip'>
    <form id='ez-wms-edit-dialog0_form' onsubmit='return false;'>
        <table class="dialog-module" border="0" cellpadding="0" cellspacing="0">
			<tbody>
				<tr>
					<td class="dialog-module-title"><{t}>data_platform<{/t}>:</td><!-- 平台 -->
					<td>
						<input type="text" name="platform" id="platform" class="input_text" value='amazon' readonly/>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>user_account<{/t}>:</td><!-- 账号名 -->
					<td>
						<input type="text" name="user_account" id="user_account" class="input_text" />
					</td>
				</tr>
				<!-- 
				<tr>
					<td class="dialog-module-title"><{t}>abbreviation<{/t}>:</td>
					<td>
						<input type="text" name="short_name" id="short_name" class="input_text" />
					</td>
				</tr>
				 -->
				<tr>
					<td class="dialog-module-title"><{t}>display_name<{/t}>:</td><!-- 显示名称 -->
					<td>
						<input type="text" name="platform_user_name" id="platform_user_name" class="input_text" />
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>status<{/t}>:</td><!-- 状态 -->
					<td>
						<select name="status" id="status" class='input_select' style="width:175px;">
							<option value="1"><{t}>enable<{/t}></option>
							<option value="0"><{t}>disable<{/t}></option>
						</select>
					</td>
				</tr>
				
				<tr>
					<td class="dialog-module-title"><{t}>site<{/t}>:</td><!-- 站点 -->
					<td>
						<!-- 
						<input type="text" name="site" id="site" class="input_text"/>
						 -->
						<select name='site' id='site' class='input_text' style="width:175px;">
							<option value=""><{t}>pleaseSelected<{/t}></option><!-- 请选择 -->
							<option value="CA">[CA] 加拿大</option>
							<option value="US">[US] 美国</option>
							<option value="DE">[DE] 德国</option>
							<option value="ES">[ES] 西班牙</option>
							<option value="FR">[FR] 法国</option>
							<option value="IN">[IN] 印度</option>
							<option value="IT">[IT] 意大利</option>
							<option value="UK">[UK] 英国</option>
							<option value="JP">[JP] 日本</option>
							<option value="CN">[CN] 中国(大陆地区)</option>
						</select>
						&nbsp;<{t}>amazon_required<{/t}>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>sales_id<{/t}>:</td><!-- 销售ID -->
					<td>
						<input type="text" name="seller_id" id="seller_id" class="input_text" placeholder="<{t}>amazon_required<{/t}>"/>&nbsp;
					</td>
				</tr>				
				<tr>
					<td class="dialog-module-title"><{t}>secret_key_id<{/t}>:</td><!-- 秘钥ID -->
					<td>
						<input type="text" name="user_token_id" id="user_token_id" class="input_text" placeholder="<{t}>amazon_required<{/t}>"/>&nbsp;
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>secret_key<{/t}>:</td><!-- 秘钥 -->
					<td>
						<input type="text" name="user_token" id="user_token" class="input_text" placeholder="<{t}>amazon_required<{/t}>"/>&nbsp;
					</td>
				</tr>
			</tbody>
		</table>
    </form>		
</div>


<div id="ez-wms-edit-dialog1" style="display: none;" title='添加mabang账号' class='dialog-edit-alert-tip'>
    <form id='ez-wms-edit-dialog1_form' onsubmit='return false;'>
        <table class="dialog-module" border="0" cellpadding="0" cellspacing="0">
			<tbody>
				<tr>
					<td class="dialog-module-title"><{t}>data_platform<{/t}>:</td><!-- 平台 -->
					<td>
						<input type="text" name="platform" id="platform" class="input_text" value='mabang' readonly/>
					</td>
				</tr>				
				<tr>
					<td class="dialog-module-title"><{t}>公司代码<{/t}>:</td><!-- 销售ID -->
					<td>
						
						<input type="text" class="input_text" disabled="disabled" value='<{$company_code}>'/><span class='tip'>对应马帮的“令牌”</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>user_account<{/t}>:</td><!-- 账号名 -->
					<td>
						<input type="text" name="user_account" id="user_account" class="input_text" /><span class='tip'>对应马帮的“秘钥”</span>
					</td>
				</tr>				
				<tr>
					<td class="dialog-module-title"><{t}>display_name<{/t}>:</td><!-- 显示名称 -->
					<td>
						<input type="text" name="platform_user_name" id="platform_user_name" class="input_text" />
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>status<{/t}>:</td><!-- 状态 -->
					<td>
						<select name="status" id="status" class='input_select' style="width:175px;">
							<option value="1"><{t}>enable<{/t}></option>
							<option value="0"><{t}>disable<{/t}></option>
						</select>
					</td>
				</tr>
				
				 				
				
			</tbody>
		</table>
    </form>		
</div>