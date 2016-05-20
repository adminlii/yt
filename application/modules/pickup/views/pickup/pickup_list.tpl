<script src="/js/jquery-ui-timepicker-addon.js" type="text/javascript"></script>
<script src="/js/jquery.overlay.min.js" type="text/javascript"></script>
<script type="text/javascript"> 
<{include file='pickup/js/pickup/pickup_list.js'}>
</script> 
<style>
#editRelationForm tr{height:30px;}
.delPcrSku{margin-left:25px;display:none;}
.searchFilterText{
	width: 100px;
}
.input_text{width: 150px;}
</style>

<div id="module-container">
	<div id="ez-wms-edit-dialog" title="上传产品模板" style="display: none;"></div>
	<div id="search-module">
		<form id="searchForm" name="searchForm" class="submitReturnFalse">
			<table width="100%" cellspacing="0" cellpadding="0" border="0" id="searchfilterArea1" class="searchfilterArea">
			<tbody>
				<tr>
					<td>
						<div class="searchFilterText" style="width: 105px;"><{t}>status<{/t}>：</div><!-- 订单分类 -->
						<div class="pack_manager">
							<input type="hidden" class="input_text keyToSearch" id="status_type" name="status_type" value="">
							<a class="link_platform platform_" onclick="searchFilterSubmit('status_type','',this)" href="javascript:void(0)"><{t}>all<{/t}><span class='count'></span></a><!-- 全部 -->
							<{foreach from=$statusArr name=ob item=ob}>
							<a class="link_platform platform_<{$ob.status_type}>"  onclick="searchFilterSubmit('status_type','<{$ob.status_type}>',this)" href="javascript:void(0)"><{$ob.status_cnname}><span class='count'></span></a><!-- 全部 -->
							
							<{/foreach}>
						</div>
					</td>
				</tr>
				
			</tbody>
		</table>
		<div class="search-module-condition">
			<span class="searchFilterText">提货编号：</span>
			<input type="text" name="pickup_order_id" class="input_text keyToSearch "  style="" placeholder="" id='pickup_order_id' value=''/>			
						 
		</div>
		<div class="search-module-condition">  
			<span class="searchFilterText">&nbsp;&nbsp;&nbsp;&nbsp;<{t}>申请时间<{/t}>(KG)：</span>
			<input type="text" name="time_start" class="input_text keyToSearch datepicker"  style="" placeholder="" id='createDateFrom'/>
			~
			<input type="text" name="time_end" class="input_text keyToSearch datepicker "  style="" placeholder=""  id='createDateEnd'/>
		
		</div>
		<div class="search-module-condition">
			<span class="searchFilterText">&nbsp;</span> 
			<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" style=''/>
			<input type="button" value="<{t}>新建提货申请<{/t}>" class="baseBtn createPickupBtn" style='float:right;'/>
		</div>
		</form>
	</div>
	<div id="module-table" style="margin-top: 5px;">		 
		<input type="button" value="<{t}>取消提货申请<{/t}>" class="baseBtn " style='margin:0 0 5px 5px;display:none;'/>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
			<tr class="table-module-title">
            <td width="3%" class="ec-center">NO.</td>
			<td><{t}>提货编号<{/t}></td>   
			<td><{t}>状态<{/t}></td> 
			<td><{t}>申请时间<{/t}></td> 
			<td><{t}>提货信息<{/t}></td> 
			<td><{t}>预约时间<{/t}></td> 
			<td><{t}>到货总单<{/t}></td>  
			
			<td><{t}>操作<{/t}></td>  
			</tr>
			<tbody id="table-module-list-data"></tbody>
		</table>
	</div>
	<div class="pagination"></div>
</div>
<div id='query_div' class='dialog_div' title='轨迹信息' style='display:none;'>
	<h3>运单号:<span id='track_number'></span></h3>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
			<tr class="table-module-title">
            <td width="3%" class="ec-center">NO.</td>
			<td><{t}>时间<{/t}></td> 
			<td><{t}>轨迹信息<{/t}></td>  
			</tr>
			<tbody class="table-module-list-data"></tbody>
		</table>
</div>