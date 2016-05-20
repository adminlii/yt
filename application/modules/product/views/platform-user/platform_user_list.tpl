<script type="text/javascript">

paginationPageSize = 200;
function loadData(page, pageSize) {
	$('.cancelEditBtn').click();
    EZ.listDate.myLoading();
    $.ajax({
        type: "POST",
        async: false,
        dataType: "json",
        url: EZ.url + "list/page/" + page + "/pageSize/" + pageSize,
        data: EZ.searchObj.serializeArray(),
        error: function () {
            paginationTotal = 0;
            EZ.listDate.EzWmsSetSearchData({msg: 'The URL request error.'});
            return;
        },
        success: function (json) {
            if (!isJson(json)) {
                paginationTotal = 0;
                EZ.listDate.EzWmsSetSearchData({msg: 'Returns the data type error.'});
                return;
            }
            paginationTotal = json.total;
            if (json.state == '1') {
//            	EZ.listDate.html(EZ.getListData(json));
                EZ.listDate.empty();
                $(EZ.getListData(json)).appendTo(EZ.listDate);
                initOperate();


                $('#table-module-list-data select').each(function(){
                    $(this).val($(this).attr('default'));
                })
                
                $('.supply_type').each(function(){
                    $(this).change();
                })
                $('.editNode').addClass('hidden');
            } else {
                EZ.listDate.EzWmsSetSearchData({state: 1});
            }

        }
    });
}
EZ.url = '/product/platform-user/';
var data = {};
EZ.getListData = function (json) {
	$('.checkAll').attr('checked',false);
	var html = '';
	var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
	var c = $('#listForm .table-module-title td').size();
	data = json.data;
	var step = 0;
	$.each(json.data, function (key, val) {
		var clazz = (step + 1) % 2 == 1 ? "table-module-b1" : "table-module-b2";
		step++;
		clazz+=' item_'+val.pu_id;
		html += "<tr class='"+clazz+"' style='height:40px;'>" ;
	
		html+='<td>'+val.platform+'</td>';
		html+='<td>'+"["+val.platform_user_name+"]"+val.user_account+'</td>';
		
		html+='<td>';
		html+="<span class='orgNode'>"+val.supply_type_title+"</span>";

		html+="<div class='hidden editNode '>";
		html+="<select name='pu["+val.pu_id+"][supply_type]'  class='supply_type' default='"+val.supply_type+"'>";
		<{foreach from=$supply_type_arr key=k name=ob item=ob}>
	    html+="<option value='<{$k}>'><{$ob}></option>";
		<{/foreach}>
		html+="</select>";
		html+="<select name='pu["+val.pu_id+"][supply_warehouse]' class='warehouse_code' default='"+val.supply_warehouse+"'>";
		<{foreach from=$warehouseArr key=k name=ob item=ob}>
	    html+="<option value='<{$ob.warehouse_code}>'><{$ob.warehouse_code}>[<{$ob.warehouse_desc}>]</option>";
		<{/foreach}>
		html+="</select>";
		//html+="<input type='text' name='pu["+val.pu_id+"][supply_qty]' class='quantity input_text' default='"+val.supply_qty+"' placeholder='<{t}>supply_qty<{/t}>'  value='"+val.supply_qty+"'/>";
		html+="</div>";
		
		html+='</td>';

		html+='<td>';
		html+="<span class='orgNode'>"+val.status_title+"</span>";
		html+="<select name='pu["+val.pu_id+"][status]'  class='hidden editNode status' default='"+val.status+"'>";
		<{foreach from=$status_arr key=k name=ob item=ob}>
	    html+="<option value='<{$k}>'><{$ob}></option>";
		<{/foreach}>
		html+="</select>";
		html+='</td>';

		html+='<td>';
		html+='<a href="javascript:;" class="product_online_supply_qty_init_btn" pu_id="'+val.pu_id+'" tip="'+val.supply_type_title+'"><{t}>product_online_supply_qty_init<{/t}></a>';
		html+='</td>';
		html += "</tr>";	
	});
	return html;
}
$(function(){

    $('.supply_type').live('change',function(){
    	if($(this).val()=='1'){
            $(this).siblings('.warehouse_code').show().attr('disabled',false);
            $(this).siblings('.quantity').hide().attr('disabled',true);
        }else{
            $(this).siblings('.warehouse_code').hide().attr('disabled',true);
            $(this).siblings('.quantity').show().attr('disabled',false);
        }
    })
    $('.batchEditBtn').click(function(){
        $('.editNode').removeClass('hidden');
        $('.orgNode').addClass('hidden');
        
        $(this).addClass('hidden');
        $('.cancelEditBtn').removeClass('hidden');
        $('.saveEditBtn').removeClass('hidden');
    });
    $('.cancelEditBtn').click(function(){
        $('.editNode').addClass('hidden');
        $('.orgNode').removeClass('hidden');
        
        $('.cancelEditBtn').addClass('hidden');
        $('.saveEditBtn').addClass('hidden');
        $('.batchEditBtn').removeClass('hidden');
    });

    $('.saveEditBtn').click(function(){
        if(!window.confirm('<{t}>are_you_sure<{/t}>')){
            return;
        }
        var param = $('#listForm').serialize();
       	 $.ajax({
             type: "post",
             async: false,
             dataType: "json",
             url: '/product/platform-user/save-supply-type',
             data: param,
             success: function (json) {
                 var html = '';
            	 html+=json.message;
                 if(json.ask){
                	 $('.cancelEditBtn').addClass('hidden');
                     $('.saveEditBtn').addClass('hidden');
                     $('.batchEditBtn').removeClass('hidden');
                     initData(paginationCurrentPage -1);
                 }else{
                 }
           	    alertTip(html,800);
             }
         });
        
    });

    $('.product_online_supply_qty_init_btn').live('click',function(){
        var pu_id = $(this).attr('pu_id');
        var op = $(this).attr('op');
        var tip = $(this).attr('tip');
        var key = prompt('初始化该账号下的在线商品补货方式为 【'+tip+'】\n请输入解锁码','');
       
        if(!key||key==''){
            return;
        }
        var param = {};
        param.pu_id = pu_id;
        param.key = key;
       	 $.ajax({
             type: "post",
             async: false,
             dataType: "json",
             url: '/product/platform-user/product-online-supply-qty-init',
             data: param,
             success: function (json) {
                 var html = '';
            	 html+=json.message;
                 if(json.ask){
                     //initData(paginationCurrentPage -1);
                 }else{
                 }
           	    alertTip(html);
             }
         });
        
    });
    
})
</script>
<style>
<!--
#listForm .supply_qty {
	display: none;
}

#module-table .editForm .var_supply_qty .supply_qty {
	display: inline;
}

#module-table .editForm .var_supply_qty span {
	display: none;
}

.batchEditSubmitBtn,.batchEditQtySetBtn {
	display: none;
}

#listForm .table-module-b2 {
	background: none repeat scroll 0 0 #CCCCFF
}

.dialog_div {
	display: none;
}

.dropdown_show .not_first {
	display: block;
}

.dropdown_hide .not_first {
	display: none;
}

#listForm .getEbayItem {
	display: none;
}

#listForm .viewEbayItem {
	display: none;
}

#update_supply_qty_form #supply_qty_set_div .disableSupplyBtn {
	color: #0090E1;
}
.hidden{display:none;}
.quantity{width:70px;}
-->
</style>
<div id="module-container" style=''>
	<div id="search-module">
		<form class="submitReturnFalse" name="searchForm" id="searchForm">
			<table width="100%" cellspacing="0" cellpadding="0" border="0" id="searchfilterArea1" class="searchfilterArea">
				<tbody>
					<tr>
						<td>
							<div class="searchFilterText" style="width: 125px;"><{t}>platform<{/t}>：</div>
							<!-- 销售状态 -->
							<div class="pack_manager">
								<input type="hidden" class="input_text keyToSearch" id="platform" name="platform" value="">
								<a class="platform"  onclick="searchFilterSubmit('platform','',this)" href="javascript:void(0)"><{t}>all<{/t}></a>
								<{foreach from=$platforms name=ob item=ob}>
								<a class="platform" onclick="searchFilterSubmit('platform','<{$ob}>',this)" href="javascript:void(0)"><{$ob}></a>
            					<{/foreach}>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="searchFilterText" style="width: 125px;"><{t}>supply_type<{/t}>：</div>
							<div class="pack_manager">
								<input type="hidden" class="input_text keyToSearch" id="supply_type" name="supply_type" value="">
								<a class="supply_type"  onclick="searchFilterSubmit('supply_type','',this)" href="javascript:void(0)"><{t}>all<{/t}></a>
								<{foreach from=$supply_type_arr name=ob item=ob key=k}>							
								<a class="supply_type" onclick="searchFilterSubmit('supply_type','<{$k}>',this)" href="javascript:void(0)"><{$ob}></a>    
								<{/foreach}>							         					
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="searchFilterText" style="width: 125px;"><{t}>status<{/t}>：</div>
							<div class="pack_manager">
								<input type="hidden" class="input_text keyToSearch" id="status" name="status" value="">
								<a class="status"  onclick="searchFilterSubmit('status','',this)" href="javascript:void(0)"><{t}>all<{/t}></a>
								<{foreach from=$status_arr name=ob item=ob key=k}>							
								<a class="supply_type" onclick="searchFilterSubmit('status','<{$k}>',this)" href="javascript:void(0)"><{$ob}></a>    
								<{/foreach}>							         					
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="search-module-condition">
				<span style="width: 125px;" class="searchFilterText"><{t}>user_account<{/t}>：</span>
				<!-- ebay账号 -->
				<select id="user_account" name="user_account">
					<option value=''><{t}>all<{/t}></option>
					<{foreach from=$platformUsers name=ob item=ob}>
					<option value='<{$ob.user_account}>'><{$ob.platform}>[<{$ob.platform_user_name}>]</option>
					<{/foreach}>
				</select>
			</div>
			
			<div class="search-module-condition">
				<span style="width: 125px;" class="searchFilterText">&nbsp;</span>
				<input type="button" class="baseBtn submitToSearch" value="<{t}>search<{/t}>">
			</div>
		</form>
	</div>
	<div id="module-table" style='overflow: auto;'>
		<div class="opration_area0">
			<div class="opration_area">
				<div class="opDiv">
					<!-- 批量编辑 -->
					<input type="button" value="<{t}>batch_edit<{/t}>" class="batchEditBtn baseBtn" style='float: right;'>			
					<!-- 取消 -->
					<input type="button" value="<{t}>cancel_edit<{/t}>" class="cancelEditBtn baseBtn hidden" style='float: right;'>		
					<!-- 保存 -->
					<input type="button" value="<{t}>save_edit<{/t}>" class="saveEditBtn baseBtn hidden" style='float: right;margin-right:5px;'>			
				</div>
			</div>
		</div>
		<form action="" id='listForm' method='POST'>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
				<tbody>
					<tr class="table-module-title">						
						<td><{t}>platform<{/t}></td>
						<td><{t}>user_account<{/t}></td>
						<td><{t}>supply_type<{/t}></td>
						<td><{t}>status<{/t}></td>
						<td><{t}>operation<{/t}></td>
					</tr>
				</tbody>
				<tbody id="table-module-list-data">
				</tbody>
			</table>
		</form>
		<div class="pagination"></div>
	</div>
	<div id='loading'></div>
</div>