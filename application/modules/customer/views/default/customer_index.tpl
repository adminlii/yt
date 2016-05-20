<script type="text/javascript">
    EZ.url = '/customer/Customer/';
    EZ.getListData = function (json) {
        var html = '';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
            val.E10=json.customerStatus[val.E10]?json.customerStatus[val.E10]:'';
            html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
            html += "<td class='ec-center'>" + (i++) + "</td>";
                    html += "<td ><a href='javascript:leftMenu(\"13\",\"客户详情\",\"/customer/customer/detail/customer_code/"+val.E1+"\")' title='客户详情'>" + val.E1 + "</a></td>";
                    html += "<td >" + val.E3 +'&nbsp'+ val.E4 + "</td>";
                    var email_verify = (val.E24 == 1)?"bg_tips02.png":"bg_tips01.png";
                    var email_verify_title = (val.E24 == 1)?"已验证邮箱":"未验证邮箱";
                    var email_verify_img = (val.E5 != "")?"<img alt='' style='cursor: pointer;' src='/images/base/"+email_verify+"' title='"+email_verify_title+"'>":"";
                    html += "<td >邮箱：" + email_verify_img + " " + val.E5  + "<br>";
                    html += "电话：" + val.E8 + "<br>";
                    html += "传真：" + val.E9 + "</td>";
                    html += "<td >" + val.E10 + "</td>";
                    html += "<td >销售代表：" + ((val.saleName != '' && val.saleName != null)?val.saleName:"") + "<br>";
                    html += "客服代表：" + ((val.serviceName != '' && val.serviceName != null)?val.serviceName:"") + "</td>";
                    html += "<td >注册："+ val.E18 + "<br>";
                    html += "登录："+ val.E21 + "<br>";
                    html +="更新：" + val.E21 + "</td>";
            html += "<td><a href=\"javascript:edit(" + val.E0 + ")\">" + EZ.edit + "</a>&nbsp;&nbsp;" +
                    "<a href='javascript:allocation("+val.E0+")'>分配代表</a>&nbsp;&nbsp;" +
                    //"<a href='javascript:leftMenu(\"13\",\"客户详情\",\"/customer/customer/detail/customer_code/"+val.E1+"\")'>" +
                    //"<{t}>detail<{/t}></a>" +
                    "<a href='javascript:creditLine("+val.E0+")'>信用额度</a>" +
                    "</td>";
            html += "</tr>";
        });
        return html;
    };
    
    function edit(id){
        $.ajax({
            async:false,
            type:'post',
            url:'/customer/customer/get-info-by-id/id/'+id,
            dataType:'json',
            success:function(json){
                if(json.state=="1"){
                    $.each(json.data,function(k,v){
                        $('[name="'+k+'"]').val(v);
						if(k == 'customer_email_verify'){
							var email_verify_src = (v == 1)?"/images/base/bg_tips02.png":"/images/base/bg_tips01.png";
		                    var email_verify_title = (v == 1)?"已验证邮箱":"未验证邮箱";
		                    $("[name='customer_email_verify_img']").attr("src",email_verify_src);
		                    $("[name='customer_email_verify_img']").attr("title",email_verify_title);
						}
                    });
                    $("#dialogEdit").dialog('open');
                }else{
                    alertTip(json.message);
                }
            }
        });
    }

    function allocation(id){
        $.ajax({
            async:false,
            type:'post',
            url:'/customer/customer/get-info-by-id/id/'+id,
            dataType:'json',
            success:function(json){
                if(json.state=="1"){
                    $('[name="c_id"]').val(json.data.customer_id)
                    $("[name='customer_cser_user_id']").val(json.data.customer_cser_user_id);
                    $("[name='customer_saler_user_id']").val(json.data.customer_saler_user_id);
                    $("#allocationDialog").dialog('open');
                }else{
                    alertTip(json.message);
                }
            }
        });
    }

    function creditLine(id){
        $.ajax({
            async:false,
            type:'post',
            url:'/customer/customer/get-credit-Line-by-id/id/'+id,
            dataType:'json',
            success:function(json){
                if(json.state=="1"){
                    $('[name="c_id"]').val(json.data.customer_id)
                    $("[name='credit_line_now']").html(json.data.cb_credit_line);
                    $("[name='currency']").html(json.data.customer_currency);
                    $("#creditLineDialog").dialog('open');
                }else{
                    alertTip(json.message);
                }
            }
        });
    }

    $(function(){
        $("#dialogEdit").dialog({
            autoOpen: false,
            width: 550,
            height: 'auto',
            modal: true,
            show: "slide",
            buttons: [
                {
                    text: "确定(Ok)",
                    click: function () {
                        //$("#editDataForm").submit();
                        $(this).dialog("close");
                        var data = $("#editDataForm").serialize();
                        $.ajax({
                            async:false,
                            type:'Post',
                            url:'/customer/customer/change',
                            data:data,
                            dataType:'json',
                            success:function(json){
                                alertTip(json.message);
                                if(json.state){
                                    loadData(paginationCurrentPage, paginationPageSize);
                                }
                            }
                        })
                    }
                },
                {
                    text: "取消(Cancel)",
                    click: function () {
                        $(this).dialog("close");
                    }
                }
            ]
        });

        $("#allocationDialog").dialog({
            autoOpen: false,
            width: 550,
            height: 'auto',
            modal: true,
            show: "slide",
            buttons: [
                {
                    text: "确定(Ok)",
                    click: function () {
                        //$("#editDataForm").submit();
                        $(this).dialog("close");
                        var data = $("#alloctionForm").serialize();
                        $.ajax({
                            async:false,
                            type:'Post',
                            url:'/customer/customer/allocation',
                            data:data,
                            dataType:'json',
                            success:function(json){
                                if(json=="0"){
                                    alertTip("allocation Failure.");
                                }else{
                                    initData(0);
                                    alertTip(json.message);
                                }
                            }
                        })
                    }
                },
                {
                    text: "取消(Cancel)",
                    click: function () {
                        $(this).dialog("close");
                    }
                }
            ]
        });

        $("#creditLineDialog").dialog({
            autoOpen: false,
            width: 400,
            height: 'auto',
            modal: true,
            show: "slide",
            buttons: [
                {
                    text: "确定(Ok)",
                    click: function () {
                        $(this).dialog("close");
                        var data = $("#creditLineForm").serialize();
                        $.ajax({
                            async:false,
                            type:'Post',
                            url:'/customer/customer/credit-Line-Edit',
                            data:data,
                            dataType:'json',
                            success:function(json){
                                if(json.state == 0){
                                    alertTip('<span class="tip-error-message">' + json.message + '</span>');
                                }else{
                                    alertTip('<span class="tip-success-message">' + json.message + '</span>');
                                }
                            }
                        })
                    }
                },
                {
                    text: "取消(Cancel)",
                    click: function () {
                        $(this).dialog("close");
                    }
                }
            ],
            close: function () {
				$(':input','#creditLineForm')
				 .not(':button, :submit, :reset, :hidden')
				 .val('')
				 .removeAttr('checked')
				 .removeAttr('selected');
											
			},
			open:function(){
				$(':input','#creditLineForm')
				 .not(':button, :submit, :reset, :hidden')
				 .val('')
				 .removeAttr('checked')
				 .removeAttr('selected');
			}
        });
    });
</script>
<div id="module-container">
    <div id="search-module">
        <form id="searchForm" name="searchForm" class="submitReturnFalse">
            <input type="hidden" value="60" id="filterActionId"/>
            <div class="search-module-condition">
                <span class="searchFilterText"><{t}>quickQuery<{/t}>：</span>
                <select name="operationType" class="input_text2">
                    <option value="E1"><{t}>customerCode<{/t}></option>
                    <option value="E5"><{t}>email<{/t}></option>
                </select>
                <input type="text" name="operationCode" id="operationCode" class="input_text keyToSearch"/>
            </div>
            <div class="search-module-condition">
                <span class="searchFilterText"></span>
                <input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch"/>
            </div>
        </form>
    </div>

    <div id="module-table">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
            <tr class="table-module-title">
                <td width="3%" class="ec-center">NO.</td>
                
       <td><{t}>customerCode<{/t}></td>
       <td>Name</td>
       <td width='26%'>联系方式</td>
       <td width='6%'><{t}>status<{/t}></td>
       <td>服务</td>
       <td width='160'><{t}>date<{/t}></td>
                <td><{t}>operate<{/t}></td>
            </tr>
            <tbody id="table-module-list-data"></tbody>
        </table>
    </div>
    <div class="pagination"></div>
</div>

<div title="修改客户信息" id="dialogEdit" class="dialog-edit-alert-tip" style="display:none">
    <form id="editDataForm" name="editDataForm"  method="post" onsubmit="return false">
        <input type="hidden" value="" name="customer_id" />
        <table class="dialog-module" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td class="dialog-module-title">名:</td>
                <td></td>
                <td><input type="text" value="" class="input_text" name="customer_firstname"/> </td>
            </tr>
            <tr>
                <td class="dialog-module-title">姓:</td>
                <td></td>
                <td><input type="text" value="" class="input_text" name="customer_lastname"/> </td>
            </tr>
            <tr>
                <td class="dialog-module-title">邮箱:</td>
                <td></td>
                <td>
                	<input type="text" value="" class="input_text" name="customer_email" />
                	<input type="hidden" value="" class="input_text" name="customer_email_verify">
                	<img name="customer_email_verify_img" style='cursor: pointer;' src='' title=''>
                </td>
            </tr>
            <tr>
                <td class="dialog-module-title">海关代码:</td>
                <td></td>
                <td><input type="text" value="" class="input_text" name="customer_customs_hscode" /></td>
            </tr>
            <tr>
                <td class="dialog-module-title"><{t}>currencyCode<{/t}>:</td>
                <td></td>
                <td>
                    <{ec name='customer_currency' search='Y' validator="required" err-msg="<{t}>require<{/t}>" }>currency<{/ec}>
                    <span class="msg">*</span></td>
            </tr>
            <tr>
                <td class="dialog-module-title">注册状态:</td>
                <td></td>
                <td><{ec name='customer_status' search='Y' validator="required" err-msg="<{t}>require<{/t}>" data=$customerStatus }>foreach<{/ec}></td>
            </tr>
            <tr>
                <td class="dialog-module-title">电话:</td>
                <td></td>
                <td><input type="text" value="" class="input_text" name="customer_telephone" /></td>
            </tr>
            <tr>
                <td class="dialog-module-title">传真:</td>
                <td></td>
                <td><input type="text" value="" class="input_text" name="customer_fax" /></td>
            </tr>
            <tr>
                <td class="dialog-module-title">公司名称:</td>
                <td></td>
                <td><input type="text" value="" class="input_text" name="customer_company_name" /></td>
            </tr>
            <tr>
                <td class="dialog-module-title">经营单位名称:</td>
                <td></td>
                <td><input type="text" value="" class="input_text" name="trade_name" /></td>
            </tr>
            <tr>
                <td class="dialog-module-title">经营单位编码:</td>
                <td></td>
                <td><input type="text" value="" class="input_text" name="trade_co" /></td>
            </tr>
            <tr>
                <td class="dialog-module-title">客户logoUrl:</td>
                <td></td>
                <td><input type="text" value="" class="input_text" name="customer_logo" /></td>
            </tr>
            <tr>
                <td class="dialog-module-title">电子签名</td>
                <td></td>
                <td><input type="text" value="" class="input_text" name="customer_signature" /></td>
            </tr>
        </table>
    </form>
</div>
<div title="分配代表" id="allocationDialog" class="dialog-edit-alert-tip" style="display:none">
    <form id="alloctionForm" name="alloctionForm"  method="post" onsubmit="return false">
        <input type="hidden" value="" name="c_id" />
        <table class="dialog-module" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td class="dialog-module-title">客服代表:</td>
                <td></td>
                <td>
                    <select name="customer_cser_user_id" style="width:200px;">
                        <option value="">--SELECT--</option>
                        <{foreach from=$server item=svrow}>
                        <option value="<{$svrow.user_id}>"><{$svrow.user_code}> [<{$svrow.user_name}>]</option>
                        <{/foreach}>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="dialog-module-title">销售代表:</td>
                <td></td>
                <td>
                    <select name="customer_saler_user_id" style="width:200px;">
                        <option value="">--SELECT--</option>
                        <{foreach from=$seller item=slrow}>
                        <option value="<{$slrow.user_id}>"><{$slrow.user_code}> [<{$slrow.user_name}>]</option>
                        <{/foreach}>
                    </select>
                </td>
            </tr>
        </table>
    </form>
</div>
<div title="信用额度调整" id="creditLineDialog" class="dialog-edit-alert-tip" style="display:none">
    <form id="creditLineForm" name="creditLineForm"  method="post" onsubmit="return false">
        <input type="hidden" value="" name="c_id" />
        <table class="dialog-module" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td class="dialog-module-title">当前额度:</td>
                <td style="width:125px;">
                    <span style="color: #1B9301;" id="credit_line_now" name="credit_line_now"></span>
                </td>
                <td>
                	<span style="font-weight: bold;text-align: right;">
                		币种:
                	</span>
                	<span style="color: #1B9301;" id="currency" name="currency"></span>
                </td>
            </tr>
            <tr>
                <td class="dialog-module-title">调整额度:</td>
                <td colspan="2">
                    <input type="text" name="credit_line" class="input_text" style="width: 180px;" value="">
                </td>
            </tr>
        </table>
    </form>
</div>
