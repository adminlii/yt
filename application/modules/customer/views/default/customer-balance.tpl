<style type="text/css">
table {
	border-collapse: collapse;
	border-spacing: 0;
}
.data-table {
	width: 100%;
	font-size: 13px;
}
.data-table th {
	text-align: right;
	font-weight: bold;
	padding: 8px 5px;
	border: 1px solid #D8E0E4;
	/*background: none repeat scroll 0 0 #f4faff;*/
	background: none repeat scroll 0 0 #e1effb;
}
.data-table td {
	padding: 8px 3px;
	border: 1px solid #D8E0E4;	
}
.data-table td.left {
	width: 20%;
	font-weight: bold;
	text-align: right;
	border: 1px solid #D8E0E4;
}
.data-table td.right {
	width: 30%;
	text-align: left;
	border: 1px solid #D8E0E4;
}
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
.form-input {
	width: 160px;
	height: 28px;
	outline: 0;
	border: 1px solid #D8E0E4;
	padding: 0 5px;
}
    #customer_currency,#customer_balance{
        font-weight:bold;
    }
    #customer_code{
        font-size:14px;
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

</style>
<div id="content">
<form id='chongzhiform'>
<div style="width: 65%;border: 2px solid #D1D8DF;height:425px;overflow:hidden; margin-top: 2px;margin-bottom:5px; margin-left: auto;margin-right: auto;height:100%;">
	<div style="padding-left: 5px;padding-top: 5px;width: 250px;float: left;">
		<h2 style="color:#E06B26;">客户信息：</h2>
		<div id="message_tip">
        </div>
		<table border="0" cellspacing="0" cellpadding="0" class="table_module" style="width: 100%;">
            <tbody>
            <tr>
                <td class="module-title">客户代码:</td>
                <td style="width:125px;">
                    <input class="input_text" style="font-weight:bold;text-transform: uppercase;" placeholder="填写客户代码并回车" name="customer_code" id="customer_code">
                </td>
            </tr>
            <tr>
                <td class="module-title">姓名:</td>
                <td >
                    <input class="input_text" name="customer_name" id="customer_name">
                </td>
            </tr>
            <tr>
                <td class="module-title">客户币种:</td>
                <td >
                    <span id="customer_currency"></span>
                </td>
            </tr>
            <tr>
                <td class="module-title">当前余额:</td>
                <td >
                    <span id='customer_balance' style="color:#0090E1;font-size: 16px;"></span>
                </td>
            </tr>
        	</tbody>
        </table>
	</div>
	<div style="padding-left: 5px;padding-top: 5px;width: 510px;margin-right:5px; float: right;">
		<h2 style="color:#E06B26;">费用变动：</h2>		
		<table border="0" cellspacing="0" cellpadding="0" class="table_module" style="width: 100%;">
            <tbody>
            <tr>
                <td class="module-title">操作类型:</td>
                <td style="width:125px;">
                    <label style="cursor: pointer;"><input type="radio" name='type' value="3">入款</label>&nbsp;&nbsp;
					<label style="cursor: pointer;"><input type="radio" name="type" value="2">扣款</label>
                </td>
            </tr>
            <tr>
                <td class="module-title">支付平台:</td>
                <td >
                    <select name="pm_code" id="pm_code">
                        <{foreach from=$pmArr item="val"}>
                        <option value="<{$val.pm_code}>"><{$val.pm_code}>  <{$val.title}></option>
                        <{/foreach}>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="module-title">到账日期:</td>
                <td >
                    <input class="datepicker input_text" name='arrive_time' id="arrive_time">
                </td>
            </tr>
            <tr>
                <td class="module-title">充值币种:</td>
                <td >
                    <select name="chongzhi_currency_code" id="chongzhi_currency_code">
		                <{foreach from=$currencyAll item="val"}>
						    <option value="<{$val.currency_code}>"><{$val.currency_code}>  <{$val.currency_name}> <{$val.currency_rate}></option>
		                <{/foreach}>
					</select>
                </td>
            </tr>
            <tr>
                <td class="module-title">金额:</td>
                <td >
                    <input class="input_text" name="arriveMoney" id="arriveMoney">
                </td>
            </tr>
            
             <tr>
                <td class="module-title">汇率:</td>
                <td >
                    <input class="input_text" name="currency_rate" id="currency_rate">
                </td>
            </tr>
             <tr>
                <td class="module-title">交易号:</td>
                <td >
                    <input class="input_text" name="transaction_number" id="transaction_number">
                </td>
            </tr>
             <tr>
                <td class="module-title">备注:</td>
                <td >
                    <textarea name='cbl_note' rows='4' cols="50"></textarea>
                </td>
            </tr>
            <tr>
                <td class="module-title"></td>
                <td >
                    <input type="button" value="提交"  onclick="chongzhi()" class="baseBtn">
                </td>
            </tr>
            
        	</tbody>
        </table>
	</div>
</div>
</form>	
</div>


<script type="text/javascript">
	$(function(){
        $("#customer_code").keyup(function (e) {
            var key = e.which;
            if (key == 13 && $.trim($(this).val()) != '') {
               setCustomerCode($(this).val());
            }
        });
        $('#message_tip').html('').hide();

//        $("#customer_code").die().live('blur', function () {
//            var code = $.trim($('#customer_code').val());
//            if (code != '') {
//                setCustomerCode(code);
//            }
//        });
        $('#chongzhi_currency_code').die().live('change', getRate);
     //   getRate();
        $(".datepicker").datepicker({ dateFormat: "yy-mm-dd"});
        setTimeout(getRate,100);
    });

    function setCustomerCode(customerCode){
        if(customerCode==''){
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
                    //填充必须数据
                    $('#customer_name').val(json.data.customer_firstname+json.data.customer_lastname);
                    $('#customer_currency').html(json.data.customer_currency);
                    $('#customer_balance').html(json.data.cb_value);
                }else{
                    $('#customer_code').select().focus();
                    $('#customer_name').val('');
                    $('#customer_currency').html('RMB');
                    $('#customer_balance').html(0);
                    $("#message_tip").html(json.message).show();
                }
            }
        });
    }
	function valid(){
		var tip="";
		if($.trim($('#customer_code').val())==''){
			tip += "<div>客户编码必须填写</div>";
		}
		if($.trim($('#arrive_time').val())==''){
			tip += "<div>到账日期必填</div>";
		}
		if($.trim($('#arriveMoney').val())==''){
			tip += "<div>充值金额必填</div>";
		}
		if($.trim($('#currency_rate').val())==''){
			tip += "<div>汇率必填</div>";
		}
		if($.trim($('#transaction_number').val())==''){
			tip += "<div>交易号必填</div>";
		}
		if(!($('[name=type]:checked').val()=='2' || $('[name=type]:checked').val()=='3')){
			tip += "<div>请选择操作类型</div>";
		}
		if(tip){
			$("#message_tip").html(tip).show();
			return false;
		}
		return true;
	}
	function chongzhi(){
		$('#message_tip').hide();
		if (!valid()) {
       		return false;
    	}
		var formdata = $('#chongzhiform').serialize();
		$('<div title="操作警告"><span class="tip-warning-message">确定进行此操作？</span></div>').dialog({
			autoOpen: true,
            width: 460,
            modal: true,
            show: "slide",
            buttons: [
                {
                    text: '确认提交(Ok)',
                    click: function () {
                        //submitArea(0);
                        $.ajax({
                            type: "post",
                            async: false,
                            dataType: "json",
                            url: '/customer/customer/balance',
                            data: $("#chongzhiform").serialize(),
                            success: function (json) {
								console.log(json);
                                if (isJson(json)) {
                                    if (json.state == '1') {
                                        alertTip('<span class="tip-success-message">' + json.message + '</span>');
                                        //location.reload();
                                        $("#arriveMoney").val('');
                                        $("#customer_balance").html("<p style='color:#1B9301;'>" + json.data.cb_value + "</p>");
                                        return;
                                    }else{
										var html = '';
										html+=json.message;
	                                    $.each(json.errorMessage, function (k, v) {
	                                        html += '<span class="tip-error-message">' + v + '</span>'
	                                    });
	                                    alertTip(html);
									}
                                } else {
                                    alertTip(json.message);
                                }
                            }
                        });
                        $(this).dialog("close");
                    }
                },
                {
                    text: '取消(Cancel)',
                    click: function () {
                        $(this).dialog("close");
                    }
                }
            ],
            close: function () {
                $(this).detach();
            }
		});
	}
	//获取币种及其汇率信息
	function getRate(){
		var currencyCode = $('#chongzhi_currency_code').val();
		$.ajax({
			type:'POST',
			url:'/common/currency/get-by-code',
			dataType:"json",
			data:{
				'code':$('#chongzhi_currency_code').val()
			},
			success:function(json){
				if(json.state=='1'){
					$('#currency_rate').val(json.data.currency_rate);
				}
			}
		});
	}
	
</script>