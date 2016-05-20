<script type="text/javascript">
    EZ.url = '/country/country/';
    EZ.getListData = function (json) {
        var html = '';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
            html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
            html += "<td class='ec-center'>" + (i++) + "</td>";
            html += "<td>" + val.E1 + "</td>";
            html += "<td>" + val.E2 + "</td>";
            html += "<td>" + val.E3 + "</td>";
            html += "<td><a href=\"javascript:deleteById(" + val.E0 + ")\">" + EZ.del + "</a></td>";
            html += "</tr>";
        });
        return html;
    }
    
 $(function(){
	$(".country_code").chosen({width:'350px',search_contains:true});
	
	$("#createBtn").click(function(){
		
		var params = $("#countryForm").serialize();
		$.ajax({
		    type: "POST",
		    async: false,
		    dataType: "json",
		    url: '/order/country-map/edit',
		    data: params,
		    success: function (json) {
		    	
		    	if (json.state) {
		           //parent.$('.dialogIframe').dialog('close');
		          alertTip(json.message);
		          initData(0);
		    	}else{
		    		var html = '';
	                if (json.errorMessage == null)return;
					html += '<span class="tip-error-message">' + json.errorMessage + '</span>';
	                alertTip(html); 
		    	}
		                
		               
		        
		    }
		});
	});

		
 })
 
   
</script>
<style>
    .searchFilterText{
        width:100px;
    }
    .table-module td{
        word-break: break-all;
    }
</style>

<div title="操作提示 (Esc)" id="dialog-auto-alert-tip"></div>

    <div id="search-module">
        <form id="countryForm" name="countryForm" class="submitReturnFalse">
            <div style="padding:0">
                <div class="search-module-condition">
                    <span class="searchFilterText" >标准国家：</span>
                    <input type = "hidden" id = "ct_id" name = "ct_id" value = "" />
                    <select class='input_select country_code' name='ct_code' id="ct_code"'>
								<option value=''  class='ALL'><{t}>-select-<{/t}></option>
								<{foreach from=$country item=c name=c}>
								 <option value='<{$c.country_code}>' country_id='<{$c.country_id}>' class='<{$c.country_code}>'><{$c.country_code}> [<{$c.country_cnname}>  <{$c.country_enname}>]</option>
								<{/foreach}>	 
					</select>
                
                </div>
                <div class="search-module-condition">
                    <span class="searchFilterText" >自定义国家：</span>
                    <input type="text" name="ct_name" id="ct_name" class="input_text keyToSearch" style="width:340px;"/>
                </div>
                <div class="search-module-condition"> 
                    <input type="button" id="createBtn" value="<{t}>提交<{/t}>" class="baseBtn" style="margin-left:100px;"/>
                </div>

            </div>
        </form>
    </div>
