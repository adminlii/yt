<script type="text/javascript" src="/js/jquery-ui-timepicker-addon.js" ></script>
<script type="text/javascript" src="/js/pageEffects.js"></script>
<script type="text/javascript">
    EZ.url = '/customer/balance-log/';
    EZ.getListData = function (json) {
        var html = '';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
            html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
            html += "<td class='ec-center'>" + (i++) + "</td>";
            html += "<td>" + val.E14 + "</td>";
                    html += "<td>"
                        + "<span style='color:" + ((val.E3 == 0 || val.E3 == 2)?"red":"#1B9301") + ";'>"  
                        + val.E3_title  
                        + "</span></td>";
                    html += "<td>" + ((val.E3 == 0 || val.E3 == 2)?"-":"") + val.E4 + "</td>";
                    html += "<td>" + val.ft_name_cn + "</td>";
                    html += "<td>" + val.E11 + "</td>";
                    html += "<td>" + val.E12 + "</td>";
					/*
                    html += "<td>" + val.E5 + "</td>";
                    html += "<td>" + val.E6 + "</td>";
                    html += "<td>" + val.E7 + "</td>";
                    */
                    html += "<td class='ellipsis' title='"+val.E8+"'>" + (val.E8==''?'&nbsp;':val.E8) + "</td>";
                    
                    html += "<td>" + val.E15 + "</td>";
            html += "<td><a href=\"javascript:getDetailById(" + val.E0 + ")\">详情</a></td>";
            html += "</tr>";
        });
        return html;
    }

    
    $(function(){
    	var dayNamesMin = ['日', '一', '二', '三', '四', '五', '六'];
		var monthNamesShort = ['01月', '02月', '03月', '04月', '05月', '06月', '07月', '08月', '09月', '10月', '11月', '12月'];
		$.timepicker.regional['ru'] = {
			timeText : '选择时间',
			hourText : '小时',
			minuteText : '分钟',
			secondText : '秒',
			millisecText : '毫秒',
			currentText : '当前时间',
			closeText : '确定',
			ampm : false
		};
		$.timepicker.setDefaults($.timepicker.regional['ru']);
	    $('.dateFrom,.dateEnd').datetimepicker({
			dayNamesMin : dayNamesMin,
			monthNamesShort : monthNamesShort,
			changeMonth : true,
			changeYear : true,
			dateFormat : 'yy-mm-dd'
		});
        
    	$(".customer_code").keyup(function (e) {
            var key = e.which;
			
			if (key == 13) {
				if($.trim($(this).val()) == ''){
					$("#message_tip").html("请输入正确的客户代码").show();
				}
				setCustomerCode($(this).val());
            }
        });

        $(".submitToSearchOther").click(function(){
        	submitForm();
        });

        
        $(".keyToSearchOther").keyup(function (e) {
            var key = e.which;
            if (key == 13) {
            	submitForm();
            }
        });

        $(window).scroll(function () {
            var scrollTop = $(window).scrollTop();
            //如果距离顶部的距离小于浏览器滚动的距离，则显示或屏蔽浮动层
            //if (scrollTop - window._fixed_side_offset.top > 25) {
            if (scrollTop > 0) {
                $("#customer_info").css("top", (0 - scrollTop));
            } else {
            	$("#customer_info").css("top",0);
            }
        });

        $("#balanceLogDialog").dialog({
            autoOpen: false,
            width: 765,
            height: 'auto',
            modal: true,
            show: "slide",
            buttons: [
                {
                    text: "关闭(Close)",
                    click: function () {
                        $(this).dialog("close");
                    }
                }
            ],
            close: function () {
									
			},
			open:function(){
				 var params = {};
				 params['paramId'] = $("#cbl_id").val();
                 $.ajax({
                     async:false,
                     type:'Post',
                     url:'/customer/balance-log/get-By-Json',
                     data:params,
                     dataType:'json',
                     success:function(json){
                    	 if (json.state) {
                             $.each(json.data, function (k, v) {
                                 $("#" + k , "#balanceLogDialog").html(v);
                             });
                             $("#tips_trade_name", "#balanceLogDialog").html($("#trade_name").html());
                             $("#tips_customer_name", "#balanceLogDialog").html($("#customer_name").html());
                             $("#tips_customer_balance", "#balanceLogDialog").html($("#customer_balance").html());
                             $("#tips_customer_balance_hold", "#balanceLogDialog").html($("#customer_balance_hold").html());
                             $("#tips_customer_currency", "#balanceLogDialog").html($("#customer_currency").html());
                                 
                         }
                     }
                 })
			}
        });
    });

    function getDetailById(id){
        $("#cbl_id").val(id);
    	$("#balanceLogDialog").dialog("open");
    }

	function customerCodeIsExist(){
		return true;
	}
    
    function submitForm(){
    	if(customerCodeIsExist()){
			$(".submitToSearch").click();				
		}else{
			$("#message_tip").html("请输入正确的客户代码").show();
		}
    }

    function searchFilterSubmitOther(data1,data2,data3){
        if(customerCodeIsExist()){
        	searchFilterSubmit(data1,data2,data3);
        }else{
        	$("#message_tip").html("请输入正确的客户代码").show();
        }
	}
    
    function setCustomerCode(customerCode){
        if(customerCode==''){
        	resetData();
            return;
        }
        $("#message_tip").myLoading({textAlign:'left'});
        $.ajax({
            type:'POST',
            async:false,
            dataType:'json',
            url:'/customer/customer/get-customer-data',
            data:{
                customerCode:customerCode
            },
            success:function(json){
                if(json.state=='1'){
                    $("#message_tip").html('').hide();
                    $("#customer_code_is_exist").val(1);
                    //填充必须数据
                    
                    $('#trade_name').html(json.data.trade_name);
                    $('#customer_name').html(json.data.customer_firstname+json.data.customer_lastname);
                    $('#customer_currency').html(json.data.customer_currency);
                    $('#customer_balance').html(json.data.cb_value);
                    $('#customer_balance_hold').html(json.data.cb_hold_value);
					//提交查询
                    submitForm();
                }else{
                	$("#message_tip").html(json.message).show();
                	resetData();
                }
            }
        });

    }

    function resetData(){
    	$("#customer_code_is_exist").val(0);
   		$('#trade_name').html('');
    	$('#customer_name').html('');
    	$('#customer_currency').html('');
     	$('#customer_balance').html('');
     	$('#customer_balance_hold').html('');

        $("#table-module-list-data").html("");
        $("#pagination").html("");
    }
</script>
<style>
#message_tip{
	color: red;
	background: #FFEBD1;
	text-align: center;
	border: 1px solid #D8E0E4;
	text-align:left;
	padding:5px;
    font-weight:bold;
}
#message_tip DIV{
	line-height: 20px;
	height: 20px;
}
.table_module TD{
	padding: 8px 3px;
	border: 1px solid #D8E0E4;		
}
.module-title {
    font-weight: bold;
    padding-right: 5px;
    text-align: right;
}
.ellipsis {
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>
<div id="module-container">
    <div id="balanceLogDialog" style="display:none;" title="流水记录详情">
        <table class="table-module" border="0" cellpadding="0" cellspacing="0" style="float: left;">
            <tbody>
            <input type="hidden" id="cbl_id" value=""/>
            <tr>
	           <td colspan="4" class="ec-center">
	           		详情
	           </td>
	        </tr>
	        <tr class="table-module-b1">
	            <td class="module-title" style="width:75px;">操作类型:</td>
	            <td style="width: 155px;">
	            	<span id="E3"></span>
	            </td>
	           	<td class="module-title" style="width:75px;">费用类型:</td>
	            <td>
	            	<span id="E10"></span>
	            </td>
	        </tr>
	        <tr class="table-module-b2">
	            <td class="module-title">发起金额:</td>
	            <td>
	            	<span id="E5"></span>
	            </td>
	            <td class="module-title">当时余额:</td>
	            <td>
	            	<span id="E11"></span>
	            </td>
	        </tr>
	        <tr class="table-module-b1">
	            <td class="module-title">发起币种:</td>
	            <td>
	           		<span id="E7"></span>
	           	</td>
	           	<td class="module-title">冻结余额:</td>
	            <td>
	            	<span id="E12"></span>
	            </td>
	        </tr>
	        <tr class="table-module-b2">
	        	<td class="module-title">当时汇率:</td>
	            <td>
	            	<span id="E6"></span>
	            </td>
	            <td class="module-title">操作人:</td>
	            <td>
	            	<span id="E9"></span>
	            </td>	            
	        </tr>
	        <tr class="table-module-b1">
	        	<td class="module-title">实际金额:</td>
	            <td>
	            	<span id="E4"></span>
	            </td>
	            <td class="module-title">到账时间:</td>
	            <td>
	            	<span id="E15"></span>
	            </td>
	        </tr>
	        <tr class="table-module-b2">
	        	<td class="module-title">单号:</td>
	            <td>
	            	<span id="E14"></span>
	            </td>
	            <td class="module-title">发生时间:</td>
	            <td>
	            	<span id="E17"></span>
	            </td>
	        </tr>
	        <tr class="table-module-b1">
	        	<td class="module-title">交易号:</td>
	            <td colspan="3">
	            	<span id="E16"></span>
	            </td>
	        </tr>
	        <tr class="table-module-b2">
	        	<td class="module-title">备注:</td>
	            <td colspan="3">
	            	<span id="E8"></span>
	            </td>
	        </tr>
	        
            </tbody>
        </table>
        
        <table class="table-module" border="0" cellpadding="0" cellspacing="0" style="float: right;display:none;">
            <tbody>
            <tr>
	           <td colspan="4" class="ec-center">
	           		账户信息
	           </td>
	        </tr>
	        <tr class="table-module-b1">
	            <td class="module-title" style="width:75px;">公司名称:</td>
	            <td style="width: 155px;">
	            	<span id="tips_trade_name"></span>
	            </td>
	        </tr>
	        <tr class="table-module-b2">
	            <td class="module-title" style="width:75px;">姓名:</td>
	            <td style="width: 155px;">
	            	<span id="tips_customer_name"></span>
	            </td>
	        </tr>
	        <tr class="table-module-b1">
	            <td class="module-title" style="width:75px;">账户余额:</td>
	            <td style="width: 155px;">
	            	<span id="tips_customer_balance" style="color:#0090E1;font-size: 16px;font-weight: bold;"></span>
	            </td>
	        </tr>
	        <tr class="table-module-b2">
	            <td class="module-title" style="width:75px;">冻结金额:</td>
	            <td style="width: 155px;">
	            	<span id="tips_customer_balance_hold" style="color:#0090E1;font-size: 16px;font-weight: bold;"></span>
	            </td>
	        </tr>
	        <tr class="table-module-b1">
	            <td class="module-title" style="width:75px;">币种:</td>
	            <td style="width: 155px;">
	            	<span id="tips_customer_currency" style="font-weight: bold;"></span>
	            </td>
	        </tr>
            </tbody>
        </table>
        
        <table class="table-module" border="0" cellpadding="0" cellspacing="0" style="float: right;margin-top: 5px;">
            <tbody>
            <tr>
	           <td colspan="4" class="ec-center">
	           		术语解释
	           </td>
	        </tr>
	        <tr class="table-module-b2">
	            <td  style="width:241px;">
	            	<b>发起币种：</b>
	            	产生费用时的币种，与客户币种不同时，实际金额会转换为客户币种.
	            </td>
	        </tr>
	        <tr class="table-module-b1">
	            <td  >
	            	<b>实际金额：</b>
	            	是从发起金额币种转换为客户币种后的金额.
	            </td>
	        </tr>
            </tbody>
        </table>
    </div>

    <div id="search-module">
        <form id="searchForm" name="searchForm" class="submitReturnFalse">
            <div style="padding:0;display:none;">
            	<div class="pack_manager_content" style="padding: 0">
						<table width="100%" cellspacing="0" cellpadding="0" border="0" id="searchfilterArea">
							<tbody>
								<tr>
									<td>
										<div style="width: 90px;" class="searchFilterText">流水类型：</div>
										<div class="pack_manager">
											<input type="hidden" class="input_text keyToSearch" id="E3" name="E3">
											<a class="link_is_E3" id="link_is_E3" onclick="searchFilterSubmitOther('E3','',this)" href="javascript:void(0)">全部<span></span></a>
											<a class="link_is_E3" id="link_is_E34" onclick="searchFilterSubmitOther('E3','3',this)" href="javascript:void(0)">入款<span></span></a>
											<a class="link_is_E3" id="link_is_E33" onclick="searchFilterSubmitOther('E3','2',this)" href="javascript:void(0)">扣款<span></span></a>
											<a class="link_is_E3" id="link_is_E31" onclick="searchFilterSubmitOther('E3','0',this)" href="javascript:void(0)">冻结<span></span></a>
											<a class="link_is_E3" id="link_is_E32" onclick="searchFilterSubmitOther('E3','1',this)" href="javascript:void(0)">解冻<span></span></a>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
            	<span class="searchFilterText" style="width: 90px;display:none;" ><{t}>customerCode<{/t}>：</span>
       			<input type="hidden" name="E1" id="E1" value='<{$companyCode}>' style="text-transform: uppercase;font-weight: bold;" placeholder="填写客户代码并回车" class="input_text customer_code"/>
       			<input type="hidden" id="customer_code_is_exist" value="0">
            </div>
            <div id="search-module-baseSearch">
                <!--
                <div class="search-module-condition">
                    <span class="searchFilterText" style="width: 90px;">操作类型：</span>
                    <input type="text" name="E3" id="E3" class="input_text keyToSearchOther"/>
                </div>
                <div class="search-module-condition">
                    <span class="searchFilterText" style="width: 90px;">费用类型：</span>
                    <input type="text" name="E10" id="E10" class="input_text keyToSearchOther"/>
                </div>
                 -->
                <div class="search-module-condition">
                    <span class="searchFilterText" style="width: 90px;">单号：</span>
                    <input type="text" name="E14" id="E14" class="input_text keyToSearchOther"/>
                </div>
                <div class="search-module-condition">
                    <span class="searchFilterText" style="width: 90px;">发生时间：</span>
                    <input id="addDateFrom" class="datepicker input_text dateFrom" type="text" name="addDateFrom" style="cursor: pointer;"/>
                    ~
                    <input id="addDateEnd" class="datepicker input_text dateEnd" type="text" name="addDateEnd" style="cursor: pointer;"/>
                </div>
                <div class="search-module-condition">
					<span class="searchFilterText" style="width: 90px;"></span>
					<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearchOther"/>
					<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" style="display: none;"/>
                </div>
            </div>
        </form>
        
        <div id="customer_info" style="padding-left: 5px;padding-top: 5px;width: 365px;position: fixed; left: 430px;top: 0px;z-index: 1;display:none;">
			<h3 style="color:#E06B26;">客户信息：</h3>
			<table border="0" cellspacing="0" cellpadding="0" class="table_module" style="width: 100%;">
	            <tbody>
	            <tr>
	                <td id='message_tip' colspan="4" style="display:none;"></td>
	            </tr>
	            <tr>
	                <td style="wdith:70px;" class="module-title">公司名称：</td>
	                <td style="width:115px;">
	                    <span id="trade_name"></span>
	                </td>
	                <td style="wdith:70px;" class="module-title">姓名：</td>
	                <td style="width:100px;">
	                    <span id="customer_name"></span>
	                </td>
	            </tr>
	            <tr>
	                <td class="module-title">当前余额：</td>
	                <td >
	                    <span id='customer_balance' style="color:#0090E1;font-size: 16px;font-weight: bold;"></span>
	                </td>
	                <td class="module-title" rowspan="2">币种：</td>
	                <td rowspan="2">
	                    <span id="customer_currency" style="color:#0090E1;font-size: 16px;font-weight: bold;"></span>
	                </td>
	            </tr>
	            <tr>
	            	<td class="module-title">冻结金额：</td>
	                <td >
	                    <span id='customer_balance_hold' style="color:#0090E1;font-size: 16px;font-weight: bold;"></span>
	                </td>
	            </tr>
	        	</tbody>
	        </table>
		</div>
    </div>

    <div id="module-table" class="fixed_side">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
            <tr class="table-module-title">
                <td width="20" class="ec-center">NO.</td>
               <td width='135'>单号</td>
		       <td width='70'>流水类型</td>
		       <td width="100">实际金额(￥)</td>
		       <td width="100">费用类型</td>
		       <td width="100">账户余额</td>
		       <td width="100">冻结金额</td>
		       <!-- 
		       <td>发起金额</td>
		       <td>当时汇率</td>
		       <td>发起币种</td>
		       -->
		       <td >备注</td>		       
		       <td width="125">发生时间</td>
                <td width='30'><{t}>operate<{/t}></td>
            </tr>
            <tbody id="table-module-list-data"></tbody>
        </table>
    </div>
    <div class="pagination"></div>
</div>
