<script type="text/javascript">
    EZ.url = '/customer/statement/';
    EZ.getListData = function (json) {
        var html = '';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
            html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
             		html += "<td class='ec-center'><input class='cs_id' type='checkbox' name='cs_id' value='"+val.E0+"'></td>";
            		html += "<td class='ec-center'>" + (i++) + "</td>";
                    html += "<td><a href='javascript:leftMenu(\"13\",\"客户详情\",\"/customer/customer/detail/customer_code/"+val.E1+"\")' title='客户详情'>" + val.E1 + "</a></td>";
                    html += "<td>" + val.E3 + "</td>";
                    html += "<td>" + val.E7 + ' ~ ' + val.E8 + "</td>";
                    html += "<td>" + val.cs_type_title + "</td>";
                    html += "<td>" + val.cs_status_title + "</td>";
                    html += "<td>" + val.E9 + "</td>";
                    html += "<td>" + ((val.E10 == 0)?"系统":val.cs_operator_title) + "</td>";
                    html += "<td>" + val.E12 + "</td>";
                    html += "<td>" + val.E11 + "</td>";
            html += "<td>"
                //+"<a href=\"javascript:getDetailById(" + val.E0 + ")\">账单明细</a>"
                +"<a href=\"javascript:exportDetailById(" + val.E0 + ")\">导出</a>"
            +"</td>";
            html += "</tr>";
        });
        return html;
    }
	$(function(){
		$(".checkAll").click(function () {
	          $(this).EzCheckAll($(".cs_id"));
	      });
			
		$("#exportBtn").click(function(){
	        if(!window.confirm("如果指定了账单，则导出相应的账单明细\n如果未指定账单，则导出搜索条件对应的账单\n你确定该操作吗？")){
	            return false;
	        }
	    	$('#exportForm').html('');
	        if($(".cs_id:checked").size()<1){
	            var param = $('#searchForm').serialize();
	            $.ajax({
	                type: "post",
	                async: false,
	                dataType: "json",
	                url: '/customer/statement/get-export-cs-id/',
	                data: param,
	                success: function (json) {                
	                   	$(json).each(function(k,v){
	                         var id = v.cs_id;
	                       	 var input = $('<input name="cs_id[]" type="text" value="'+id+'"/>');
	                       	 $('#exportForm').append(input);
	                    });
	                    setTimeout(function(){$('#exportForm').submit();},500);
	                }
	            });
	            
	        	
	        }else{
	         	 $(".cs_id:checked").each(function(){
	               var id = $(this).val();
	             	 var input = $('<input name="cs_id[]" type="text" value="'+id+'"/>');
	             	 $('#exportForm').append(input);
	           });
	           setTimeout(function(){$('#exportForm').submit();},500);
	        }
	         
	        
	    });
	});
	
	function exportDetailById(id){
		var input = $('<input name="cs_id[]" type="text" value="'+id+'"/>');
   		$('#exportForm').append(input);
   		setTimeout(function(){$('#exportForm').submit();},500);
	}
	
    function getDetailById(id){
		alert("暂无明细");
    }
</script>
<div id="module-container">
	<form action="/customer/statement/export" id='exportForm' target='_blank' style='display: none;' method='POST'>
	</form>
    <div id="search-module">
        <form id="searchForm" name="searchForm" class="submitReturnFalse">
            <div style="padding:0">
                <div class="search-module-condition">
                	<div class="pack_manager_content" style="padding: 0">
						<table width="100%" cellspacing="0" cellpadding="0" border="0" id="searchfilterArea">
							<tbody>
								<tr>
									<td>
										<div style="width: 90px;" class="searchFilterText">账单类型：</div>
										<div class="pack_manager">
											<input type="hidden" class="input_text keyToSearch" id="E5" name="E5">
											<a class="link_is_E5" id="link_is_E5" onclick="searchFilterSubmit('E5','',this)" href="javascript:void(0)">全部<span></span></a>
											<a class="link_is_E5" id="link_is_E51" onclick="searchFilterSubmit('E5','2',this)" href="javascript:void(0)">订单<span></span></a>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div style="width: 90px;" class="searchFilterText">账单状态：</div>
										<div class="pack_manager">
											<input type="hidden" class="input_text keyToSearch" id="E6" name="E6">
											<a class="link_is_E6" id="link_is_E6" onclick="searchFilterSubmit('E6','',this)" href="javascript:void(0)">全部<span></span></a>
											<a class="link_is_E6" id="link_is_E60" onclick="searchFilterSubmit('E6','0',this)" href="javascript:void(0)">草稿<span></span></a>
											<a class="link_is_E6" id="link_is_E61" onclick="searchFilterSubmit('E6','1',this)" href="javascript:void(0)">处理中<span></span></a>
											<a class="link_is_E6" id="link_is_E62" onclick="searchFilterSubmit('E6','2',this)" href="javascript:void(0)">已完成<span></span></a>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div style="width: 90px;" class="searchFilterText">核销标志：</div>
										<div class="pack_manager">
											<input type="hidden" class="input_text keyToSearch" id="E9" name="E9">
											<a class="link_is_E9" id="link_is_E9" onclick="searchFilterSubmit('E9','',this)" href="javascript:void(0)">全部<span></span></a>
											<a class="link_is_E9" id="link_is_E90" onclick="searchFilterSubmit('E9','Y',this)" href="javascript:void(0)">Y<span></span></a>
											<a class="link_is_E9" id="link_is_E91" onclick="searchFilterSubmit('E9','N',this)" href="javascript:void(0)">N<span></span></a>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
	                <span class="searchFilterText" style="width: 90px;" ><{t}>customerCode<{/t}>：</span>
	                <input type="text" name="customer_code" id="customer_code" class="input_text keyToSearch" style="text-transform: uppercase;"/>
            	</div>
                <div class="search-module-condition">
	                <span class="searchFilterText" style="width:90px;"></span>
	                <input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch"/>
	            </div>
            </div>
        </form>
    </div>

    <div id="module-table">
    	<div class="opration_area">
			<div class="opration_area_rit">
				<input type="button" class="baseBtn" value="导出账单" id="exportBtn" style='margin-right: 5px;'>
			</div>
		</div>
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
            <tr class="table-module-title">
               <td width="20" class="ec-center">
					<input type="checkbox" class="checkAll" />
			   </td>
               <td width="3%" class="ec-center">NO.</td>
		       <td>客户代码</td>
		       <td>账单号</td>
		       <td>计费时间段</td>
		       <td width="70">账单类型</td>
		       <td width="70">账单状态</td>
		       <td width="70">核销标志</td>
		       <td width="70">操作人</td>
		       <td width="120">添加时间</td>
		       <td width="120">完成时间</td>
                <td width="60"><{t}>operate<{/t}></td>
            </tr>
            <tbody id="table-module-list-data"></tbody>
        </table>
    </div>
    <div class="pagination"></div>
</div>
