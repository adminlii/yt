<script type="text/javascript">
    EZ.url = '/order/platform-user/';
    var quickId = '<{$quickId}>';
    EZ.getListData = function (json) {
        var html = '';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
            html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
            html += "<td class='ec-center'>" + (i++) + "</td>";
            
                    html += "<td >" + val.E1 + "</td>";
                    html += "<td >" + val.E2+' ['+val.E10+']' + "</td>";
                    html += "<td >" + val.E4 + "</td>";
                    html += "<td >" + val.E6 + "</td>";
            html += "<td>";
            html += "<a href=\"javascript:editById(" + val.E0 + ")\">" + EZ.edit + "</a>";
            html +="&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:deleteById(" + val.E0 + ")\">" + EZ.del + "</a>";
            html +="&nbsp;&nbsp;|&nbsp;&nbsp;<a href='javascript:;' class='reAuth' user_account='"+val.E2+"' short_name='"+val.E4+"' pu_id='"+val.E0+"'>重新授权</a>";
            html +="&nbsp;&nbsp;|&nbsp;&nbsp;<a href='javascript:;' class='initAcc' user_account='"+val.E2+"' short_name='"+val.E4+"' platform='"+val.E1+"'>定时任务初始化</a>";
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
            parent.openIframeDialogNew('/order/platform-user/new-ebay-auth/pu_id/'+pu_id,w-100,h-80,'ebay账号重新授权',quickId,paginationCurrentPage,paginationPageSize);
        	
        });
        $('.initAcc').live('click',function(){
            if(!window.confirm('确认重新对该账号初始化定时任务')){
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
            parent.openIframeDialogNew('/order/platform-user/new-ebay-auth/',w-100,h-80,'新增ebay账号授权',quickId,paginationCurrentPage,paginationPageSize);
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
                	        url: '/order/platform-user/save-amazon',
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
    });    
</script>
<div id="module-container">
	<div id="ez-wms-edit-dialog" style="display: none;">
		<table class="dialog-module" border="0" cellpadding="0" cellspacing="0">
			<tbody>
				<input type="hidden" name="E0" id="E0" value="" />
				<tr>
					<td class="dialog-module-title">平台:</td>
					<td>
						<input type="text" name="E1" id="E1" class="input_text" disabled="disabled" />
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title">账号名:</td>
					<td>
						<input type="text" name="E2" id="E2" class="input_text" disabled="disabled" />
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title">简称:</td>
					<td>
						<input type="text" name="E4" id="E4" class="input_text" />
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title">显示名称:</td>
					<td>
						<input type="text" name="E10" id="E10" class="input_text" />
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title">当前状态:</td>
					<td>
						<input type="text" name="E6" id="E6" class="input_text"/>&nbsp;1
					</td>
				</tr>
				
				<tr>
					<td class="dialog-module-title">amazon站点:</td>
					<td>
						<input type="text" name="E8" id="E8" class="input_text"/>&nbsp;amazon必填
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title">amazon销售ID/卖家ID:</td>
					<td>
						<input type="text" name="E9" id="E9" class="input_text"/>&nbsp;amazon必填
					</td>
				</tr>
				
				<tr>
					<td class="dialog-module-title">访问秘钥ID:</td>
					<td>
						<input type="text" name="E7" id="E7" class="input_text"/>&nbsp;amazon必填
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
								<div style="width: 90px;" class="searchFilterText"><{t}>data_platform<{/t}>：</div>
								<div class="pack_manager">
									<input type="hidden" class="input_text keyToSearch" id="platform" name="E1">
									<a class="link_is_E1" id="link_is_E1" onclick="searchFilterSubmit('E1','',this)" href="javascript:void(0)">全部<span></span></a>
									<a class="link_is_E1" id="link_is_E10" onclick="searchFilterSubmit('E1','ebay',this)" href="javascript:void(0)">eBay<span></span></a>
									<a class="link_is_E1" id="link_is_E11" onclick="searchFilterSubmit('E1','amazon',this)" href="javascript:void(0)">Amazon<span></span></a>
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
							<span class="searchFilterText" style="width: 90px;"><{t}>user_account<{/t}>：</span>
							<input type="text" name="E2" id="E2" class="input_text keyToSearch" />
			        	</td>
			        	<td>
			        		<span class="searchFilterText" style="width: 50px;"><{t}>abbreviation<{/t}>：</span>
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
		<div style="padding-top: 5px;" class="opration_area">
    		<div>
    			<input type="button" class="baseBtn" value="添加amazon账号" id="createAmazonButton" style='float:right;margin-left:5px;'>
				<input type="button" value="添加eBay账户" class="baseBtn"  style='float:right' id='addEbayAuth'/>
    		</div>
    	</div>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
			<tr class="table-module-title">
				<td width="3%" class="ec-center">NO.</td>
				<td><{t}>data_platform<{/t}></td>
				<td><{t}>user_account<{/t}>[<{t}>display_name<{/t}>]</td>
				<td><{t}>abbreviation<{/t}></td>
				<td><{t}>stats<{/t}></td>
				<td><{t}>operate<{/t}></td>
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
					<td class="dialog-module-title">平台:</td>
					<td>
						<input type="text" name="platform" id="platform" class="input_text" value='amazon' readonly/>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title">账号名:</td>
					<td>
						<input type="text" name="user_account" id="user_account" class="input_text" />
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title">简称:</td>
					<td>
						<input type="text" name="short_name" id="short_name" class="input_text" />
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title">显示名称:</td>
					<td>
						<input type="text" name="platform_user_name" id="platform_user_name" class="input_text" />
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title">当前状态:</td>
					<td>
						<input type="text" name="status" id="status" class="input_text" value='1'/>&nbsp;0：不可用，1：可用
					</td>
				</tr>
				
				<tr>
					<td class="dialog-module-title">amazon站点:</td>
					<td>
						<!-- 
						<input type="text" name="site" id="site" class="input_text"/>
						 -->
						<select name='site' id='site' class='input_select' style="width:175px;">
							<option value="">请选择</option>
							<option value="CA">加拿大</option>
							<option value="US">美国</option>
							<option value="DE">德国</option>
							<option value="ES">西班牙</option>
							<option value="FR">法国</option>
							<option value="IN">印度</option>
							<option value="IT">意大利</option>
							<option value="UK">英国</option>
							<option value="JP">日本</option>
							<option value="CN">中国(大陆地区)</option>
						</select>
						&nbsp;amazon必填
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title">amazon销售ID/卖家ID:</td>
					<td>
						<input type="text" name="seller_id" id="seller_id" class="input_text"/>&nbsp;amazon必填
					</td>
				</tr>				
				<tr>
					<td class="dialog-module-title">访问秘钥ID:</td>
					<td>
						<input type="text" name="user_token_id" id="user_token_id" class="input_text"/>&nbsp;amazon必填
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title">访问秘钥:</td>
					<td>
						<input type="text" name="user_token" id="user_token" class="input_text"/>&nbsp;amazon必填
					</td>
				</tr>
			</tbody>
		</table>
    </form>		
</div>