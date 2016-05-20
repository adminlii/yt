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
							<a class="link_order_source" onclick="searchFilterSubmit('can_merge','2',this)" href="javascript:void(0)">已合并订单</a>
							<!-- 
								<a href="javascript:void(0)" class="merge_filter link_can_merge" merge_type='' id='link_can_merge'>全部</a>
								<a href="javascript:void(0)" class="merge_filter link_can_merge" merge_type='1' id='link_can_merge1'>可合并订单</a>
								<a href="javascript:void(0)" class="merge_filter link_can_merge" merge_type='2' id='link_can_merge2'>已合并订单</a>
								 -->
						</div>
					</td>
				</tr>
				<!-- 
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
				 -->
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
		<input type="hidden" value="" id="filterActionId" />
		<div class="search-module-condition">
			<span class="searchFilterText" style="width: 90px;">单号：</span>
			<select name="type" class='input_text_select' id='type'>
				<option value="0">订单号</option>
				<option value="6">仓库单号</option>				
				<option value="1">客户参考号</option>
				<option value="2">跟踪号</option>				
				<option value="4">SKU模糊搜索</option>
				<!-- 
				<option value="5">eBay SKU批量搜索</option>
				 -->
			</select>
			<input type="text" class="input_text keyToSearch" id="code" name="code" style='width: 542px;' placeholder='可输入多个，每个以空格隔开' />
		</div>
		<div class="search-module-condition">
			<span class="searchFilterText" style="width: 90px;">买家信息：</span>
			<select name="buyer_type" id='buyer_type' class='input_text_select'>
				<option value="buyer_name">买家姓名</option>
				<option value="buyer_mail">买家Mail</option>
			</select>
			<input type="text" class="input_text keyToSearch" id="buyer_id" name="buyer_id" style="width: 200px;" />
			<select id='mult_order_buyer' style='width: 100px;display:none;'>
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
			<!--  -->
			<span class="" style="width: 90px;">&nbsp;&nbsp;&nbsp;&nbsp;站点：</span>
			<select id="site" name="site">
				<option value=''>全部</option>
				<{foreach from=$sites name=ob item=ob}>
				<option value='<{$ob.site_code}>'><{$ob.site_name_cn}></option>
				<{/foreach}>
			</select>
			<span class="" style="width: 90px;">&nbsp;&nbsp;&nbsp;&nbsp;店铺帐号：</span>
			<select id="user_account" name="user_account" class="input_text_select">
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