<div id="search-module">
	<form class="submitReturnFalse" name="searchForm" id="searchForm">
		<input type="hidden" value="" id="filterActionId" />
		<input class="order_by" type="hidden" value="create_date desc" name='orderBy' id='orderBy' /> 
		<div class="search-module-condition">
			<span class="searchFilterText" style="width: 90px;"><{t}>单号<{/t}>：</span>
			<select name="code_type" class='input_text_select input_select' id="code_type">
				<option value="shipper">运单号</option>
				<option value="refer">客户单号</option>
				<option value="server">跟踪单号</option>
			</select>
			<input type="text" class="input_text keyToSearch" id="code" name="shipper_hawbcode" style='width: 700px;' placeholder='<{t}>multi_split_space<{/t}>' />
		</div>
		<div id="search-module-baseSearch">
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;">&nbsp;</span>
				<input type="button" class="baseBtn submitToSearch" value="<{t}>search<{/t}>">
				&nbsp;&nbsp;
				<input type="button" class="baseBtn clearBtn" value="<{t}>reset<{/t}>">
				&nbsp;
				<a href="javascript:void(0)" class="toAdvancedSearch" onclick="toAdvancedSearch()"><{t}>showAdvancedSearch<{/t}></a>
			</div>
			<div class="search-module-condition">
				<!-- 隐藏的提交属性 -->
				<input type="hidden" name='hold_sign' id='process_again'>
				<input type="hidden" name='ot_id' id='ot_id'>
				<input type="hidden" name='sub_status' id='sub_status'>			
			</div>
		</div>
		<div id="search-module-advancedSearch">
			
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;"><{t}>consignee_country<{/t}>：</span>
				<select name="country_code" class='input_text_select input_select' style="float: left;">
					<option value=''><{t}>-all-<{/t}></option>
					<{foreach from=$countryArr name=ob item=ob}>
					<option value='<{$ob.country_code}>'><{$ob.country_code}>[<{$ob.country_cnname}> <{$ob.country_enname}>]</option>
					<{/foreach}>
				</select>
				<span class="searchFilterText" style="width: 90px;"><{t}>收件人名<{/t}>：</span>
				
				<input type="text" class="input_text keyToSearch" name="consignee_name_like" style="width: 200px;" />
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;"><{t}>shipping_method<{/t}>：</span>
				<select id="country" name="product_code" class='input_text_select input_select' style="float: left;">
					<option value=''><{t}>-all-<{/t}></option>
					<{foreach from=$productKind name=ob item=ob}>
					<option value='<{$ob.product_code}>'><{$ob.product_code}>[<{$ob.product_enname}> <{$ob.product_cnname}>]</option>
					<{/foreach}>
				</select>
				<span class="searchFilterText" style="width: 90px;"><{t}>create_time<{/t}>：</span>
				<input type="text" class="datepicker input_text  " name="create_date_from" id='createDateFrom'/>
				~
				<input type="text" class="datepicker input_text  " name="create_date_to" id='createDateEnd'/>
			</div>
			
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;"><{t}>打印标志<{/t}>：</span>
				<select name="print_sign" class='input_text_select input_select'>
					<option value=''><{t}>-all-<{/t}></option>
					<option value='0'>否</option>
					<option value='1'>是</option>
				</select>
			</div>
			<div class="search-module-condition" id='div_for_status_3_4_'></div>
			
			<div id='search_more_div'></div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;">&nbsp;</span>
				<input type="button" class="baseBtn submitToSearch" value="<{t}>search<{/t}>">
				&nbsp;&nbsp;
				<input type="button" class="baseBtn clearBtn" value="<{t}>reset<{/t}>">
				<input type='hidden' name='keyword' id='keyword' value='' />
				<input type='hidden' name='status' id='status' value='' />
				<input type='hidden' name='is_more' id='is_more' value='0' />
				&nbsp;
				<a href="javascript:void(0)" class="toAdvancedSearch" onclick="toBaseSearch()"><{t}>hideAdvancedSearch<{/t}></a>
			</div>
		</div>
	</form>
</div>