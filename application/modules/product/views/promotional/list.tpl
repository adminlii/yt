<script type="text/javascript"> 
    EZ.url = '/product/promotional/';
    paginationPageSize = 8;
    EZ.getListData = function (json) {
        var html = '';        
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
            html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
            html += "<td class='ec-center'>" + (i++) + "</td>";

            html += "<td >" + val.user_account + "</td>";
            html += "<td class='word-break'>" + val.promotional_sale_id +"/" + val.promotional_sale_name+ "</td>";
            html += "<td class='word-break'>" + val.promotional_sale_type+ "</td>";
            html += "<td class='word-break'>" + val.promotional_status + "</td>";
            html += "<td class='word-break'>" + val.promotional_sale_item_id_array + "</td>";
            html += "<td class='word-break'>" + val.discount_type +"/"+ val.discount_value + "</td>";
            html += "<td class='word-break'>" + val.promotional_sale_start_time +'<br/>~<br/>'+ val.promotional_sale_end_time + "</td>";
            html += "<td class='word-break'>";
            html += "<a href='javascript:;' class='reloadSingle' sip_id='"+val.sip_id+"'>ReLoad</a>&nbsp;";
            html += "|&nbsp;<a href='javascript:;' onclick='editById("+val.sip_id+");' sip_id='"+val.sip_id+"' class='editButton'>"+EZ.edit+"</a>&nbsp;";
            //html += "<br/><a href='javascript:;' class='syncSingle' sip_id='"+val.sip_id+"'>同步</a>&nbsp;&nbsp;";
            html += "|&nbsp;<a href='javascript:;' class='deleteSingle' sip_id='"+val.sip_id+"'>"+EZ.del+"</a>&nbsp;&nbsp;";
            html += "</td>";
            
            html += "</tr>";
        });
        return html;
    }
    
    function loadStart(){
		//数据处理中，请稍候
        alertTip($.getMessage('sys_common_wait'),'600');
        setTimeout(function(){
        	$('.ui-button',$('#dialog-auto-alert-tip').parent()).hide();
        	$('#dialog-auto-alert-tip').dialog('option','closeOnEscape',false);   
        },100);
    	     
    }

    function loadEnd(html){
    	$('#dialog-auto-alert-tip').dialog('option','title',$.getMessage('sys_common_complete'));//'完成'      	
      	$('.ui-dialog-content',$('#dialog-auto-alert-tip').parent()).html(html);
       	$('.ui-button',$('#dialog-auto-alert-tip').parent()).show();
    	$('#dialog-auto-alert-tip').dialog('option','closeOnEscape',true);
    }
    $(function(){ 
        $('#uploadDiv').dialog({
            autoOpen: false,
            width: 500, 
            height:180,
            modal: true,
            show: "slide",           
            close: function () {
                
            }
        });
        $('.reloadSingle').live('click',function(){
            var sip_id = $(this).attr('sip_id');
            if(!window.confirm('Are You Sure?')){
                return;
            }
            loadStart();
            $.ajax({
	               type: "post",
	               async: true,
	               dataType: "json",
	               data:{'sip_id':sip_id},
	               url: '/product/promotional/reload-single/',
	               success: function (json) {	            	 
	            	 var html = json.message;
	            	 $.each(json.errors,function(k,v){
	            		 html+="<p>"+v+"</p>";
	            	 });
	            	 loadEnd(html);
	            	 if(json.ask){
	                	 initData(paginationCurrentPage-1);
	                 }
	               }
	           });
        })

        $('.deleteSingle').live('click',function(){
            var sip_id = $(this).attr('sip_id');
            if(!window.confirm('Are You Sure?')){
                return;
            }
            loadStart();
            $.ajax({
	               type: "post",
	               async: true,
	               dataType: "json",
	               data:{'sip_id':sip_id},
	               url: '/product/promotional/delete-single/',
	               success: function (json) {	            	 
	            	 var html = json.message;
	            	 $.each(json.errors,function(k,v){
	            		 html+="<p>"+v+"</p>";
	            	 });	            	 
	            	 loadEnd(html);
	            	 if(json.ask){
	            		initData(paginationCurrentPage-1);
	            	 }
	               }
	           });
        })  
        
        $('.syncSingle').live('click',function(){
            var sip_id = $(this).attr('sip_id');
            if(!window.confirm('Are You Sure?')){
                return;
            }
            loadStart();
            $.ajax({
	               type: "post",
	               async: true,
	               dataType: "json",
	               data:{'sip_id':sip_id},
	               url: '/product/promotional/sync-single/',
	               success: function (json) {	            	 
	            	 var html = json.message;
	            	 $.each(json.errors,function(k,v){
	            		 html+="<p>"+v+"</p>";
	            	 });
	            	 loadEnd(html);
	            	 if(json.ask){
	                	 initData(paginationCurrentPage-1);
	                 }
	               }
	           });
        })
    });
    $(function(){
        $('.updateBtn').click(function(){
        	if(!window.confirm('Are You Sure Load All Promotional?')){
                return;
            }
            //数据更新中,请稍候...  --   数据更新中,请稍候...
           	 $('<div title="'+$.getMessage('sys_common_wait')+'">'+$.getMessage('sys_common_wait')+'</div>').dialog({
     	        autoOpen: true,
      	        closeOnEscape:false,
     	        width: 500,
     	        modal: true,
     	        show: "slide",
     	        buttons: [
     	            {
     	                text: 'Close',
     	                click: function () {
     	                    $(this).dialog("close");
     	                }
     	            }
     	        ],
     	        close: function () {
     	            $(this).detach();
     	        },
     	        open:function(){
         	        var this_ = $(this);
	            	$('.ui-button',this_.parent()).hide();
     	        	$.ajax({
      	               type: "post",
      	               async: true,
      	               dataType: "json",
      	               url: '/product/promotional/update/',
      	               success: function (json) {
        	            	 this_.dialog('option','title',$.getMessage('sys_common_complete'));//'数据完成'
        	            	 var html = json.message;
        	            	 $.each(json.errors,function(k,v){
        	            		 html+="<p>"+v+"</p>";
        	            	 });
        	            	 $('.ui-dialog-content',this_.parent()).html(html);
     		            	$('.ui-button',this_.parent()).show();
    		            	 if(json.ask){
         		               	 initData(paginationCurrentPage-1);
        		             }
      	               }
      	           });
     	        }
       	    });
 	    });
         
        $('#createButton').click(function(){
            //setTimeout(function(){
           	 $('.user_account_01').attr('disabled',false);
            //},200)            
           
        })
        
        $('.editButton').live('click',function(){  
       	 //setTimeout(function(){
       		 $('.user_account_01').attr('disabled',true);
         //},200)            
           
        })
    })

</script>
<style>
#editRelationForm tr {
	height: 30px;
}

.delPcrSku {
	margin-left: 25px;
}
.word-break{word-break:break-all; /*支持IE，chrome，FF不支持*/

　　word-wrap:break-word;/*支持IE，chrome，FF*/}
.dialog-edit-alert-tip .dialog-module td input{width:280px;}
</style>
<div id="module-container">
	<div id="ez-wms-edit-dialog" style="display: none;">
		<table class="dialog-module" border="0" cellpadding="0" cellspacing="0">
			<tbody>
				<input type="hidden" name="sip_id" id="sip_id" value="" />
				<tr>
					<td class="dialog-module-title"><span style="color:#F2683E;"><{t}>notice<{/t}><!-- 注意 -->：</span></td>
					<td>
						<!-- 此处的时间为eBay时间(比北京时间慢8个小时)，请注意个站点间的时差. -->
						<{t}>discount_tips_02<{/t}>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><span style="color:#1B9301;"><{t}>examples<{/t}><!-- 举例 -->：</span></td>
					<td>
						<!-- 德国比eBay时间快2个小时，英国比eBay时间快1个小时. -->
						<{t}>discount_tips_03<{/t}>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>ebay_account<{/t}>:</td>
					<td>
        				<select name="user_account" class='user_account_01' disabled='disabled'>
        					<{foreach from=$user_account_arr name=ob item=ob}>
        					<option value='<{$ob.user_account}>'><{$ob.platform_user_name}></option>
        					<{/foreach}>
        				</select>	 
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>Name<{/t}>:</td>
					<td>
						<input type="text" name="promotional_sale_name" id="promotional_sale_name" validator="required" err-msg="<{t}>require<{/t}>" class="input_text" placeholder='ItemID'/>
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>StartTime<{/t}>:</td>
					<td>
						<input type="text" name="promotional_sale_start_time" id="promotional_sale_start_time" validator="required" err-msg="<{t}>require<{/t}>" class="input_text"  placeholder='<{t}>format_ebay_time<{/t}>：2013-04-15'/>
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>EndTime<{/t}>:</td>
					<td>
						<input type="text" name="promotional_sale_end_time" id="promotional_sale_end_time" validator="required" err-msg="<{t}>require<{/t}>" class="input_text"  placeholder='<{t}>format_ebay_time<{/t}>：2013-04-15'/>
						<span class="msg">*</span>
					</td>
				</tr>
				<tr>
					<td class="dialog-module-title"><{t}>Percent(%)<{/t}>:</td>
					<td>
						<input type="text" name="discount_value" id="discount_value" validator="required" err-msg="<{t}>require<{/t}>" class="input_text"  placeholder='<{t}>set_the_number_of_discount_tips<{/t}>'/><!-- 折扣百分比，如5%请填写数字5 -->
						<span class="msg">*</span>
					</td>
				</tr>
			</tbody>
		</table>
		<div style='padding:5px 0px;color:red;'>
		End date of a promotional sale discount for items. Maximum listing durations vary by site from 14 days to 45 days. The minimum promotional sale duration is 1 day for most sites, but 3 days on some sites. 
	    </div>
	</div>
	<div id="search-module">
		<form id="searchForm" name="searchForm" class="submitReturnFalse">
			<div class="search-module-condition">
				<span style="width: 90px;" class="searchFilterText"><{t}>ebay_account<{/t}>：</span><!-- ebay账号 -->
				<select name="user_account">
					<option value=''><{t}>all<{/t}></option>
					<{foreach from=$user_account_arr name=ob item=ob}>
					<option value='<{$ob.user_account}>'><{$ob.platform_user_name}></option>
					<{/foreach}>
				</select>
			</div>
			<div class="search-module-condition">
				<span style="width: 90px;" class="searchFilterText"><{t}>promo_status<{/t}>：</span><!-- 促销状态 -->
				<select name="promotional_status">
					<option value=''><{t}>all<{/t}></option>
					<{foreach from=$syncStatus name=ob item=ob key=k}>
					<option value='<{$ob.promotional_status}>'><{$ob.promotional_status}></option>
					<!-- 					
					<option value='<{$ob.promotional_status}>' <{if $ob.promotional_status=='Active'}>selected<{/if}>><{$ob.promotional_status}></option>
					 -->
					<{/foreach}>
				</select>
			</div>
			<div class="search-module-condition">
				<span style="width: 90px;" class="searchFilterText">ItemID：</span>
				<input type="text" name="item_id" class="input_text keyToSearch"  placeholder='<{t}>after_the_support_fuzzy_search<{/t}>'/><!-- 模糊搜索 -->
			</div>
			<div style="padding: 0">
				<!-- 
				&nbsp;店铺帐号：
				<select name="user_account">
					<option value=''>全部</option>
					<{foreach from=$user_account_arr name=ob item=ob}>
					<option value='<{$ob.user_account}>'><{$ob.platform_user_name}></option>
					<{/foreach}>
				</select>
				
				&nbsp;&nbsp;
				<span class="" style="width: 90px;">&nbsp;&nbsp;&nbsp;&nbsp;促销状态：</span>
				<select name="promotional_status">
					<option value=''>全部</option>
					<{foreach from=$syncStatus name=ob item=ob key=k}>
					<option value='<{$ob.promotional_status}>'><{$ob.promotional_status}></option>
					<{/foreach}>
				</select>
				&nbsp; ItemID：
				<input type="text" name="item_id" class="input_text keyToSearch"  placeholder='模糊搜索'/>
				-->
			</div>   
			<div style="padding-top: 4px;">	
				<span style="width: 90px;" class="searchFilterText"> <{t}>promotional_id<{/t}>(eBay)：</span>	<!-- 促销ID -->	

				<input type="text" name="promotional_sale_id" class="input_text keyToSearch" style="float: left;"/>
				
				<span style="width: 90px;" class="searchFilterText"> <{t}>promotion_name<{/t}>：</span>	<!-- 促销名称 -->

				<input type="text" name="promotional_sale_name" class="input_text keyToSearch"  style="float: left;" placeholder='<{t}>after_the_support_fuzzy_search<{/t}>'/><!-- 模糊搜索 -->
				&nbsp; 
				<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" />
                <input type="button" id="createButton" value="<{t}>create<{/t}>" class="baseBtn"/>
				<input type="button" class="baseBtn updateBtn" value="<{t}>manually_update<{/t}>"  style='float: right;'><!-- 手动更新 -->
			</div>             
		</form>
	</div>
	
	<div id="module-table">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
			<tr class="table-module-title">
				<td width="3%" class="ec-center">NO.</td>				
				<td width='80'><{t}>user_account<{/t}></td><!-- 账号 -->
				<td width='140'><{t}>promotional_id<{/t}>(eBay)<!-- 促销ID -->/ <{t}>promotion_name<{/t}><!-- 促销名称 --></td>
				<td width='70'><{t}>promo_type<{/t}></td><!-- 促销类型 -->
				<td width='80'><{t}>promo_status<{/t}></td><!-- 促销状态 -->
				<td><{t}>promo_id<{/t}></td><!-- 促销ItemID -->
				<td width='95'><{t}>discount_type<{/t}><!-- 折扣类型 -->/<{t}>discount_value<{/t}><!-- 折扣值 --></td>
				<td width='160'><{t}>start_time<{/t}>~<{t}>end_time<{/t}>(eBay)<!-- 开始时间~结束时间 --></td>
				<td width='140'><{t}>operate<{/t}></td>
			</tr>
			<tbody id="table-module-list-data"></tbody>
		</table>
	</div>
	<div class="pagination"></div>
</div>