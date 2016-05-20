<div id="search-module">
	<form class="submitReturnFalse" name="searchForm" id="searchForm">
		<table width="100%" cellspacing="0" cellpadding="0" border="0" id="searchfilterArea1" class="searchfilterArea">
			<tbody>
				<tr>
					<td>
						<div class="searchFilterText" style="width: 90px;">订单分类：</div>
						<div class="pack_manager">
							<input type="hidden" class="input_text keyToSearch" id="order_type" name="order_type" value="">
							<a class="link_order_source" onclick="searchFilterSubmit('order_type','',this)" href="javascript:void(0)">全部</a>
							<a class="link_order_source" onclick="searchFilterSubmit('order_type','sale',this)" href="javascript:void(0)">正常销售订单</a>
							<a class="link_order_source" onclick="searchFilterSubmit('order_type','resend',this)" href="javascript:void(0)">重发订单</a>
							<a class="link_order_source" onclick="searchFilterSubmit('order_type','line',this)" href="javascript:void(0)">线下订单</a>
						</div>
					</td>
				</tr>
				<tr style='display: none;'>
					<td>
						<div class="searchFilterText" style="width: 90px;">创建类型：</div>
						<div class="pack_manager">
							<input type="hidden" class="input_text keyToSearch" id="create_type" name="create_type" value="">
							<a class="link_order_source" onclick="searchFilterSubmit('create_type','',this)" href="javascript:void(0)">全部</a>
							<a class="link_order_source" onclick="searchFilterSubmit('create_type','api',this)" href="javascript:void(0)">API下载订单</a>
							<a class="link_order_source" onclick="searchFilterSubmit('create_type','upload',this)" href="javascript:void(0)">批量上传订单</a>
							<a class="link_order_source" onclick="searchFilterSubmit('create_type','hand',this)" href="javascript:void(0)">手工创建订单</a>
						</div>
					</td>
				</tr>
				<!-- 	 -->
				<tr>
					<td>
						<div class="searchFilterText" style="width: 90px;">可合并订单：</div>
						<div class="pack_manager">
							<input type="hidden" class="input_text keyToSearch" id="can_merge" name="can_merge" value="">
							<a class="link_order_source" onclick="searchFilterSubmit('can_merge','',this)" href="javascript:void(0)">全部</a>
							<a class="link_order_source" onclick="searchFilterSubmit('can_merge','1',this)" href="javascript:void(0)">可合并订单</a>
							<!-- 
							<a class="link_order_source" onclick="searchFilterSubmit('can_merge','2',this)" href="javascript:void(0)">已合并订单</a>
							 -->
							
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="searchFilterText" style="width: 90px;">客户留言订单：</div>
						<div class="pack_manager">
							<input type="hidden" class="input_text keyToSearch" id="has_buyer_note" name="has_buyer_note" value="">
							<a class="link_order_source" onclick="searchFilterSubmit('has_buyer_note','',this)" href="javascript:void(0)">全部</a>
							<a class="link_order_source" onclick="searchFilterSubmit('has_buyer_note','0',this)" href="javascript:void(0)">无客户留言订单</a>
							<a class="link_order_source" onclick="searchFilterSubmit('has_buyer_note','1',this)" href="javascript:void(0)">有客户留言订单</a>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="searchFilterText" style="width: 90px;">订单类型：</div>
						<div class="pack_manager">
							<input type="hidden" class="input_text keyToSearch" id="is_one_piece" name="is_one_piece" value="">
							<a class="link_order_source" onclick="searchFilterSubmit('is_one_piece','',this)" href="javascript:void(0)">全部</a>
							<a class="link_order_source" onclick="searchFilterSubmit('is_one_piece','1',this)" href="javascript:void(0)">一票一件</a>
							<a class="link_order_source" onclick="searchFilterSubmit('is_one_piece','0',this)" href="javascript:void(0)">一票多件</a>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="search-module-condition">
			<span class="searchFilterText" style="width: 90px;">单号：</span>
			<select name="type" class='input_text_select' id='type'>
				<option value="0">订单号</option>
				<option value="6">仓库单号</option>
				<!-- -->
				<option value="1">客户参考号</option>
				<option value="2">跟踪号</option>
				<option value="3">ItemID</option>
				<option value="4">eBay SKU模糊搜索</option>
				<!-- 
				
				 -->
				<option value="5">eBay SKU批量搜索</option>
			</select>
			<input type="text" class="input_text keyToSearch" id="code" name="code" style='width: 400px;' placeholder='可输入多个，每个以空格隔开' />
		</div>
		<div class="search-module-condition">
			<span class="searchFilterText" style="width: 90px;">买家信息：</span>
			<select name="buyer_type" id='buyer_type' class='input_text_select'>
				<option value="buyer_id">买家账号</option>
				<option value="buyer_name">买家姓名</option>
				<option value="buyer_mail">买家Mail</option>
			</select>
			<input type="text" class="input_text keyToSearch" id="buyer_id" name="buyer_id" style="width: 200px;" />
			<select id='mult_order_buyer' style='width: 100px;'>
				<option value="">有多个订单的买家</option>
				<{foreach from=$mulBuyer name=ob item=ob}>
				<option value='<{$ob.buyer_id}>' name='<{$ob.buyer_name}>' mail='<{$ob.buyer_mail}>'><{$ob.buyer_id}>[<{$ob.buyer_name}>][<{$ob.buyer_mail}>]</option>
				<{/foreach}>
			</select>
			<select style='display: none;' name='mult_order_buyer[]' multiple="multiple">
				<{foreach from=$mulBuyer name=ob item=ob}>
				<option value='<{$ob.buyer_id}>' selected='selected' name='<{$ob.buyer_name}>' mail='<{$ob.buyer_mail}>'><{$ob.buyer_id}>[<{$ob.buyer_name}>][<{$ob.buyer_mail}>]</option>
				<{/foreach}>
			</select>
			<span class="" style="width: 90px;">&nbsp;&nbsp;&nbsp;&nbsp;站点：</span>
			<select id="site" name="site">
				<option value=''>全部</option>
				<{foreach from=$sites name=ob item=ob}>
				<option value='<{$ob.site_code}>'><{$ob.site_name_cn}></option>
				<{/foreach}>
			</select>
			<span class="" style="width: 90px;">&nbsp;&nbsp;&nbsp;&nbsp;店铺帐号：</span>
			<select id="user_account" name="user_account">
				<option value=''>全部</option>
				<{foreach from=$user_account_arr name=ob item=ob}>
				<option value='<{$ob.user_account}>'><{$ob.platform_user_name}></option>
				<{/foreach}>
			</select>
			
			<!-- 隐藏的提交属性 -->
			<input type="hidden" name='process_again' id='process_again'>
			<input type="hidden" name='ot_id' id='ot_id'>
			<input type="hidden" name='abnormal_type' id='abnormal_type'>
		</div>
		<div class="search-module-condition" style='display: none;'>
    		<span class="searchFilterText" style="width: 90px;">同步状态：</span>
    		<select id='sync_status' name='sync_status'>
    			<option value="">全部</option>
    			<option value='1'>已同步</option>
    			<option value='0'>未同步</option>
    			<option value='2'>同步异常</option>
    			<option value='3'>重新同步</option>
    			<option value='4'>数据异常</option>
    		</select>
    	</div>
		<div id='search_more_div'>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module-seach">
				<tr class="table-module-b2">
					<td class='table-module-seach-title'>
						目的国家：
					</td>
					<td>
						<select id="country" name="country" style="width: 250px;">
							<option value=''>全部</option>
							<{foreach from=$countryArr name=ob item=ob}>
							<option value='<{$ob.country_code}>'><{$ob.country_code}>[<{$ob.country_name}>]</option>
							<{/foreach}>
						</select>
					</td>
					<td class='table-module-seach-title'>
						发货仓库：
					</td>
					<td>
						<select id='ship_warehouse' style="width: 180px;">
							<option value="">全部</option>
							<{foreach from=$warehouse item=w name=w}>
							<option value='<{$w.warehouse_id}>'><{$w.warehouse_code}>[<{$w.warehouse_desc}>]</option>
							<{/foreach}>
						</select>						
					</td>
				</tr>
				<tr class="table-module-b1">
					<td class='table-module-seach-title'>
						创建时间：
					</td>
					<td>
						<input type="text" class="datepicker input_text  " id="createDateFrom" name="createDateFrom" />
						~
						<input type="text" class="datepicker input_text  " id="createDateEnd" name="createDateEnd" />
					</td>
					<td class='table-module-seach-title'>
						付款时间：
					</td>
					<td>
						<input type="text" class="datepicker input_text  " id="payDateFrom" name="payDateFrom" />
						~
						<input type="text" class="datepicker input_text  " id="payDateEnd" name="payDateEnd" />
					</td>
				</tr>
				<tr class="table-module-b2">
					<td class='table-module-seach-title'>
						审核时间：
					</td>
					<td>
						<input type="text" class="datepicker input_text  " id="verifyDateFrom" name="verifyDateFrom" />
						~
						<input type="text" class="datepicker input_text  " id="verifyDateEnd" name="verifyDateEnd" />
					</td>
					<td class='table-module-seach-title'>
						发货时间：
					</td>
					<td>
						<input type="text" class="datepicker input_text  " id="shipDateFrom" name="shipDateFrom" />
						~
						<input type="text" class="datepicker input_text  " id="shipDateEnd" name="shipDateEnd" />
					</td>
				</tr>
				<tr class="table-module-b1">
					<td class='table-module-seach-title'>
						售价范围：
					</td>
					<td>
						<input type="text" class="input_text keyToSearch" id="priceFrom" name="priceFrom" style='width:164px;'/>
						~
						<input type="text" class="input_text keyToSearch" id="priceEnd" name="priceEnd" style='width:164px;'/>
					</td>
					<td class='table-module-seach-title'>
					</td>
					<td>
						<!-- 更多过滤条件的触发点 -->
						<a href="javascript:;" id='show_more_float' style="float: left;">召唤更多过滤条件</a>
						<!-- 针对选中过滤条件的提示区域==已弃用
						<div class='float_selected_title' style='float:left;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;width:200px;padding-top:0px;padding-left:5px;color:#1B9301;'></div>
						 -->
					</td>
				</tr>
			</table>
		</div>
		<div class='table-module-seach-float'>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 2px;">
				<tbody>
					<tr class='table-module-title table-module-seach-float-handle' style='padding-bottom:5px;'>
						<td align="left">
							<div style="text-align: left;width: 60%;float: left;">
								<h2>过滤条件</h2>
							</div>
							<div style="text-align: right;width: 50px;float: right;">
								<h2><a href="javascript:;" id='hide_more_float' name='hide_more_float'>收起</a></h2>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<div class='table-module-seach-float-content'>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" id="searchfilterArea2" class="searchfilterArea">
				<tbody>
				<tr>
					<td>
						<div class="searchFilterText" style="width: 90px;">分仓类型：</div>
						<div class="pack_manager">
							<input type="hidden" class="input_text keyToSearch" id="has_warehouse" name="has_warehouse" value="">
							<a class="link_order_source link_option link_option_title" onclick="searchFilterSubmit('has_warehouse','',this)" href="javascript:void(0)">全部</a>
							<a class="link_order_source link_option" onclick="searchFilterSubmit('has_warehouse','1',this)" href="javascript:void(0)">已分仓</a>
							<a class="link_order_source link_option" onclick="searchFilterSubmit('has_warehouse','0',this)" href="javascript:void(0)">未分仓</a>										
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div style="width: 90px;" class="searchFilterText">是否存在纠纷：</div>
						<div class="pack_manager">
							<input type="hidden" value="" name="case_exist" id="case_exist" class="input_text keyToSearch">
							<a href="javascript:void(0)" onclick="searchFilterSubmit('case_exist','',this)" class="link_order_source link_option link_option_title">全部</a>
							<a href="javascript:void(0)" onclick="searchFilterSubmit('case_exist','1',this)" class="link_order_source link_option">存在</a>
							<a href="javascript:void(0)" onclick="searchFilterSubmit('case_exist','0',this)" class="link_order_source link_option">不存在</a>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="searchFilterText" style="width: 90px;">是否拆分订单：</div>
						<div class="pack_manager">
							<input type="hidden" class="input_text keyToSearch" id="is_split_order" name="is_split_order" value="">
							<a class="link_order_source link_option link_option_title" onclick="searchFilterSubmit('is_split_order','',this)" href="javascript:void(0)">全部</a>
							<a class="link_order_source link_option" onclick="searchFilterSubmit('is_split_order','4',this)" href="javascript:void(0)">是</a>
							<a class="link_order_source link_option" onclick="searchFilterSubmit('is_split_order','0',this)" href="javascript:void(0)">否</a>
						</div>
					</td>
				</tr>				
				</tbody>
			</table>
			</div>
		</div>
		<style>
			
		</style>
		
		<!-- 
		<div id='search_more_div'>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;">目的国家：</span>
				<select id="country" name="country" style="width: 200px;">
					<option value=''>全部</option>
					<{foreach from=$countryArr name=ob item=ob}>
					<option value='<{$ob.country_code}>'><{$ob.country_code}>[<{$ob.country_name}>]</option>
					<{/foreach}>
				</select>
				<span class="" style="width: 90px;">&nbsp;&nbsp;&nbsp;&nbsp;站点：</span>
				<select id="site" name="site">
					<option value=''>全部</option>
					<{foreach from=$sites name=ob item=ob}>
					<option value='<{$ob.site_code}>'><{$ob.site_name_cn}></option>
					<{/foreach}>
				</select>
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;">eBay运输方式：</span>
				<select id="shipping_method_platform" name="shipping_method_platform">
					<option value=''>全部</option>
					<{foreach from=$shippingMethodPlatform name=ob item=ob}>
					<option value='<{$ob.shipping_method_code}>'><{$ob.name_cn}></option>
					<{/foreach}>
				</select>
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;">售价范围：</span>
				<input type="text" class="input_text keyToSearch" id="priceFrom" name="priceFrom" />
				~
				<input type="text" class="input_text keyToSearch" id="priceEnd" name="priceEnd" />
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;">创建时间：</span>
				<input type="text" class="datepicker input_text  " id="createDateFrom" name="createDateFrom" />
				~
				<input type="text" class="datepicker input_text  " id="createDateEnd" name="createDateEnd" />
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;">付款时间：</span>
				<input type="text" class="datepicker input_text  " id="payDateFrom" name="payDateFrom" />
				~
				<input type="text" class="datepicker input_text  " id="payDateEnd" name="payDateEnd" />
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;">审核时间：</span>
				<input type="text" class="datepicker input_text  " id="verifyDateFrom" name="verifyDateFrom" />
				~
				<input type="text" class="datepicker input_text  " id="verifyDateEnd" name="verifyDateEnd" />
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;">发货时间：</span>
				<input type="text" class="datepicker input_text  " id="shipDateFrom" name="shipDateFrom" />
				~
				<input type="text" class="datepicker input_text  " id="shipDateEnd" name="shipDateEnd" />
			</div>
			<div class="search-module-condition" id='div_for_status_3_4_'>
				<span class="searchFilterText" style="width: 90px;">发货仓库：</span>
				<select id='ship_warehouse'>
					<option value="">全部</option>
					<{foreach from=$warehouse item=w name=w}>
					<option value='<{$w.warehouse_id}>'><{$w.warehouse_code}>[<{$w.warehouse_desc}>]</option>
					<{/foreach}>
				</select>
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;">是否分仓：</span>
				<select id='' has_warehouse'' name='has_warehouse'>
					<option value="">全部</option>
					<option value='1'>已分仓</option>
					<option value='0'>未分仓</option>
				</select>
			</div>
			<div class="search-module-condition" style='display: none;'>
				<span class="searchFilterText" style="width: 90px;">同步状态：</span>
				<select id='sync_status' name='sync_status'>
					<option value="">全部</option>
					<option value='1'>已同步</option>
					<option value='0'>未同步</option>
					<option value='2'>同步异常</option>
					<option value='3'>重新同步</option>
					<option value='4'>数据异常</option>
				</select>
			</div>
		</div>
		 -->
		<div class="search-module-condition">
			<span class="searchFilterText" style="width: 90px;">&nbsp;</span>
			<input type="button" class="baseBtn submitToSearch" value="搜索">
			&nbsp;&nbsp;
			<input type="button" class="baseBtn set_it_for_allot" value="保存搜索条件" id='set_it_for_allot'>
			&nbsp;&nbsp;
			<input type="button" class="baseBtn clearBtn" value="清空条件">
			<input type='hidden' name='keyword' id='keyword' value='' />
			<input type='hidden' name='platform' id='platform' value='<{$platform}>' />
			<input type='hidden' name='status' id='status' value='2' />
			<input type='hidden' name='is_more' id='is_more' value='0' />
			&nbsp;
			<a href='javascript:;' class='search_more' is_more='0'>高级搜索</a>
			&nbsp;&nbsp;&nbsp;
			<a id="select_allot_condition" class="set_it_for_allot" href="javascript:;">选择已保存搜索条件</a>
			<!-- 针对选中过滤条件的提示区域 -->
			<span class='float_selected_title' style='overflow: hidden;text-overflow: ellipsis;white-space: nowrap;width:200px;padding-top:0px;padding-left:5px;color:#1B9301;'></span>
		</div>
	</form>
</div>

<div class="load_div" id="load_ebay_order" title="手工下载eBay订单数据">
	<span style="" class="iconLoad">&nbsp;</span>
	<span style="" class="load_div_text">手工下载订单</span>
</div>
<div id='load_ebay_order_box' class="dialog-edit-alert-tip dialog_div" title='手工下载eBay订单'>
	<form action="" id="load_ebay_order_box_form" onsubmit='return false;' style=''>
		<input type="text" style='border:0 none;height:1px;'>
		<table class="dialog-module" border="0" cellpadding="0" cellspacing="0">
	    	<tbody>
		        <tr>
		            <td class="dialog-module-title">开始时间:</td>
		            <td>
		            	<input type="text" class="datepicker input_text baseDateFrom" id="loadDateFrom" name="loadDateFrom" style="width:110px;"/>
		            </td>
		        </tr>
		        <tr>
		            <td class="dialog-module-title">结束时间:</td>
		            <td>
		            	<input type="text" class="datepicker input_text baseDateEnd" id="loadDateEnd" name="loadDateEnd" style="width:110px;"/>
		            </td>
		        </tr>
		        <tr>
		            <td class="dialog-module-title">店铺账户:</td>
		            <td>
		            	<select id="user_account_load" name="user_account_load">
							<option value=''>请选择</option>
							<{foreach from=$user_account_arr name=ob item=ob}>
							<option value='<{$ob.user_account}>'><{$ob.platform_user_name}></option>
							<{/foreach}>
						</select>
		            </td>
		        </tr>
		        <tr>
		            <td class="dialog-module-title"><b style="color:#E06B26;">注意</b>：</td>
		            <td>
		            	1、此处数据为美国时间，并且，间隔时间不能超过3天(72小时).
		            </td>
		        </tr>
		         <tr>
		            <td class="dialog-module-title"></td>
		            <td>
		            	2、等待时间可能较长，请勿关闭页面.
		            </td>
		        </tr>
		    </tbody>
		</table>
	</form>
</div>
<script type="text/javascript">
	$(function(){
		$("#load_ebay_order").click(function(){
			$("#load_ebay_order_box").dialog("open");
		});
		/**
		 * 手工下载ebay订单
		 */
		$('#load_ebay_order_box').dialog({
			autoOpen: false,
			width: 480,
			maxHeight: 380,
			modal: true,
			show: "slide",
			buttons: [
				{
					text: '提交',
					click: function () {
						//检查数据正确性
						if(!checkLoadEbayOperator()){
							return;
						}
						//var this_ = $(this);
						//this_.dialog("close");
						var load_tips = "<span class='tip-load-message'>下载eBay订单可能需要较长时间，请勿关闭窗口...</span>"; 
                		alertTip(load_tips);

						var form = $("#load_ebay_order_box_form");
						var params = form.serialize();
						$.ajax({
							type : "POST",
							url : "/order/order/hander-Load-Ebay-Orders",
							data : params,
							dataType : 'json',
							success : function(jsonData) {
								var tips = "";
								if(isJson(jsonData)){
                					if(jsonData['state'] == 1){
                						tips = "<span class='tip-success-message'>" + jsonData.message + "</span>";
                					}else if(jsonData['state'] == 0){
                						tips = "<span class='tip-warning-message'>" + jsonData.message + "</span>"; 
                						confirmBol = true;
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
				},
				{
					text: '关闭',
					click: function () {
						var this_ = $(this);
						this_.dialog("close");	
					}
				}
				],
				close: function () {
					$(':input','#load_ebay_order_box_form')
					 .not(':button, :submit, :reset, :hidden')
					 .val('')
					 .removeAttr('checked')
					 .removeAttr('selected');
												
				},
				open:function(){
					$(':input','#load_ebay_order_box_form')
					 .not(':button, :submit, :reset, :hidden')
					 .val('')
					 .removeAttr('checked')
					 .removeAttr('selected');
				}
			});
	});

	function getTimestamp(dt1){
		var d1=dt1.split(" ")[0];
		var t1=dt1.split(" ")[1];
		var y1=d1.split("-")[0];
		var m1=parseInt(d1.split("-")[1])-1;
		var da1=d1.split("-")[2];
		var h1=t1.split(":")[0];
		var mi1=t1.split(":")[1];
		var dat1=new Date(y1,m1,da1,h1,mi1);
		return dat1;
	}

	function checkLoadEbayOperator(){
		var loadDateFrom = $('#loadDateFrom').val();
		var loadDateEnd = $('#loadDateEnd').val();
		var user_account = $('#user_account_load').val();
		var tips = "";
		var loadDateFromIsNull = true;
		var loadDateEndIsNull = true;
		if(loadDateFrom == ''){
			tips += "<span class='tip-error-message'>请选择‘开始时间’</span>";
		}else{
			loadDateFromIsNull = false;
		}
		if(loadDateEnd == ''){
			tips += "<span class='tip-error-message'>请选择‘结束时间’</span>";
		}else{
			loadDateEndIsNull = false;
		}

		if(!loadDateFromIsNull && !loadDateEndIsNull ){
			if(loadDateFrom > loadDateEnd){
				tips += "<span class='tip-error-message'>‘开始时间’不能大于‘结束时间’</span>";
			}else{
				var dat1=getTimestamp(loadDateFrom);
				var dat2=getTimestamp(loadDateEnd);
				var c=dat2-dat1;
				if(Math.floor(c/3600000) > 72){
					tips += "<span class='tip-error-message'>下载订单的时间间隔不能超过‘72’小时</span>";
				}
			}
		}
		if(user_account == ''){
			tips += "<span class='tip-error-message'>请选择店铺账户</span>";
		}
		if(tips != ""){
			alertTip(tips);
			return false;
		}
		return true;
	}
</script>