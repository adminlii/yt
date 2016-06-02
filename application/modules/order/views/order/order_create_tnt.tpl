<style>
*{margin:0; border:0; padding:0; font-family:"微软雅黑","宋体"; font-size:12px;}
.wrapBox{width:1000px; overflow:hidden; margin:10px auto; border:1px solid #000;}
.wrapLeft{width:449px; overflow:hidden; float:left; border-right:1px solid #000}
.wrapRight{width:550px; overflow:hidden; float:left;}
.title{background:#000; line-height:30px; height:30px; color:#FFF; font-weight:bolder; font-size:14px; padding-left:9px;}
.widthLeft{width:440px;}
.widthRight{width:540px;  padding-left:10px;}
.title input[type="button"]{padding:0 5px; +padding:0 1px; border-radius:3px; background:#FFF; cursor:pointer; position:relative; right:0;}
.title .btn1{margin-left:145px; +margin-left:120px; margin-right:10px;}	

.contentLeft{width:449px; overflow:hidden;}
.contentType1{width:420px; padding-left:29px; font-weight:bolder; height:50px; line-height:50px; position:relative;}
.select{height:25px; background:#cfcfcf; border:1px solid #000; font-weight:bolder; padding-left:10px; margin-left:15px;}
.select option{padding-left:10px;}
.select1{width:155px;}
.select2{width:200px;}
.contentType3{width:419px; overflow:hidden; padding:5px 15px 0;}
input[type="text"]{width:94%; border:1px solid #000; height:30px; line-height:30px; padding:0 2%; margin:3px 0 8px; font-weight:bolder;}
.contentType4{width:194px; overflow:hidden; padding:5px 15px 0; float:left;}
.borderR{border-right:1px solid #000;}
.borderB{border-bottom:1px solid #000;}

.contentRight{width:540px; overflow:hidden;}
.contentType2{height:50px; line-height:50px; padding-left:180px; width:360px; font-weight:bolder;}
.contentType2 input[type="radio"]{margin:0 60px 0  10px;}
.table{width:100%;}
.table table{border:1px solid #000; width:100%;}
.table td{height:25px; line-height:25px; text-align:center; font-weight:bolder; border-right:1px solid #000;border-bottom:1px solid #000;}
.contentType5{width:520px; overflow:hidden; padding:5px 15px 0; float:left;}

.contentType6{width:244px; overflow:hidden; padding:5px 15px 0; float:left;}
.contentType7{width:245px; overflow:hidden; padding:5px 15px 0; float:left;}
.contentType6 h3,.contentType7 h3{font-weight:normal; }

.bottomBox{width:100%; height:70px; float:left; position:relative;}
.btn2{width:130px; height:30px; float:left; background:#d0cecf; text-align:center; line-height:30px; font-size:14px; font-weight:bold; border:1px solid #000; margin:19px 50px 0 300px;}
.btn3{width:180px; height:30px; float:left; background:#f00; text-align:center; line-height:30px; font-size:14px; font-weight:bold; border:1px solid #000; color:#fff; margin:19px 0 0 0;}
.btn4{font-size:14px; font-weight:bold; position:absolute; right:142px; top:26px;}

.seven{position:relative; padding-left:20px;}
.seven label{margin:0 20px;}
.seven input{height:22px; line-height:22px;}
.check{width:7px; height:8px; position:absolute; top:5px; left:13px;}

.info .Validform_wrong {
	background: url("/images/error.png") no-repeat scroll left center
		rgba(0, 0, 0, 0);
	color: red;
	padding-left: 20px;
	white-space: nowrap;
}

.Validform_checktip {
	color: #999;
	font-size: 12px;
	height: 20px;
	line-height: 20px;
	margin-left: 8px;
	overflow: hidden;
}


.Validform_checktip {
	margin-left: 0;
}

.info {
	border: 1px solid #ccc;
	padding: 2px 20px 2px 5px;
	color: #666;
	position: absolute;
	margin-top: -32px;
	margin-left: 10px;
	display: none;
	line-height: 20px;
	background-color: #fff;
	float: left;
}

.dec {
	bottom: -8px;
	display: block;
	height: 8px;
	overflow: hidden;
	position: absolute;
	left: 10px;
	width: 17px;
}

.dec s {
	font-family: simsun;
	font-size: 16px;
	height: 19px;
	left: 0;
	line-height: 21px;
	position: absolute;
	text-decoration: none;
	top: -9px;
	width: 17px;
}

.dec .dec1 {
	color: #ccc;
}

.dec .dec2 {
	color: #fff;
	top: -10px;
}
.use{
background:#cfcfcf;
}
</style>
</head>
<script>
function setWeight(obj){
	var weight_x = parseInt($(obj).val());
	if(weight_x){
		$("#order_weight").val(weight_x*parseInt($("#order_pieces").val()));
	}
}

function successTip(tip, order) {
	if(order.order_status=='D'){
		$('<div title="操作提示 (Esc)" id="success-tip"><p align="">' + tip + '</p></div>').dialog({
	        autoOpen: true,
	        closeOnEscape:false,
	        width: 600,
	        maxHeight: 400,
	        modal: true,
	        show: "slide",
	        buttons: [
	            {
	                text: '录入下一条订单',
	                click: function () {
	                    $(this).dialog("close");
	                    $('#order_id').val('');
	                    $('#shipper_hawbcode').val('');
	                    setTimeout(function(){
	                    	$('#shipper_hawbcode').focus()
	                    },100);
	                	$('.orderSubmitVerifyBtn').show();
	                	$('.orderSubmitDraftBtn').show();
	                }
	            },
	            {
	                text: '查看订单',
	                click: function () {
	                    $(this).dialog("close");
	                    leftMenu('order-list','订单管理','/order/order-list/list?shipper_hawbcode='+order.shipper_hawbcode);
	                }
	            },
	            {
	                text: '编辑订单',
	                click: function () {
	                    $(this).dialog("close");
	                    $('#order_id').val(order.order_id);
	                    if(order.order_status!='D'){
	                    	$('.orderSubmitVerifyBtn').hide();
	                    	$('.orderSubmitDraftBtn').hide();
	                    	alertTip('订单不是草稿不允许编辑');
	                    }else{
		                	$('.orderSubmitVerifyBtn').show();
		                	$('.orderSubmitDraftBtn').show();
	                    }
	                }
	            }
	            
	        ],
	        close: function () {
	            $(this).remove();
	        },
	        open:function(){
	        	$('.ui-dialog-titlebar-close',$(this).parent()).remove();
	        }
	    });
	}else{
		$('<div title="操作提示 (Esc)" id="success-tip"><p align="">' + tip + '</p></div>').dialog({
	        autoOpen: true,
	        closeOnEscape:false,
	        width: 600,
	        maxHeight: 400,
	        modal: true,
	        show: "slide",
	        buttons: [
	            {
	                text: '录入下一条订单',
	                click: function () {
	                    $(this).dialog("close");
	                    $('#order_id').val('');
	                    $('#shipper_hawbcode').val('');
	                    setTimeout(function(){
	                    	$('#shipper_hawbcode').focus()
	                    },100);
	                	$('.orderSubmitVerifyBtn').show();
	                	$('.orderSubmitDraftBtn').show();
	                }
	            },
	            {
	                text: '查看订单',
	                click: function () {
	                    $(this).dialog("close");
	                    leftMenu('order-list','订单管理','/order/order-list/list?shipper_hawbcode='+order.shipper_hawbcode);
	                }
	            }
	            
	        ],
	        close: function () {
	            $(this).remove();
	        },
	        open:function(){
	        	$('.ui-dialog-titlebar-close',$(this).parent()).remove();
	        }
	    });
	}
    
}

function formSubmit(status){
	$("#orderForm :input").each(function(){
		var val = $(this).val();
		val = $.trim(val)
		$(this).val(val);
	});
	var param = $("#orderForm").serialize();
	param+='&status='+status;
	//是否填写了投保
	var insurance_value= $("input[name='order[insurance_value]']").val();
	var insurance_value_gj = $("input[name='order[insurance_value_gj]'").val();
	if(insurance_value){
	param+='&order[insurance_value_gj]='+insurance_value_gj+'&order[insurance_value]='+insurance_value;
	}
	 loadStart();
	$.ajax({
		type: "POST",
		url: "/order/order/createtnt",
		data: param,
		dataType:'json',
		success: function(json){
			 loadEnd('');
			var html = json.message;
			if(json.ask){
				html+="<br/>系统单号:"+json.order.shipper_hawbcode;
                $('#shipper_hawbcode').val(json.order.shipper_hawbcode);                
				successTip(html,json.order);
			}else{
				 if(json.err){
					 html+="<ul style='padding:0 25px;list-style-type:decimal;'>";
					 $.each(json.err,function(k,v){
						 html+="<li>"+v+"</li>";
					 })
					 html+="</ul>";
				 }
				alertTip(html);
			}
		}
	});
}

</script>
<script type="text/javascript">
<{include file='order/js/order/order_create_tnt.js'}>
</script>
<body>
<div class="wrapBox">
<form method="POST" action="" onsubmit="return false;" id="orderForm">
	<div class="wrapLeft">
    	<div class="title widthLeft">1、产品及服务</div>
        <div class="contentLeft contentType1">
        	产品种类*
        	<select class='select1 select' 
							name='order[product_code]'
							
							id='product_code'>
								<option value='' class='ALL'><{t}>-select-<{/t}></option>
								<{foreach from=$productKind item=c name=c}>
								
								<option <{if $c.product_code eq "TNT"}>selected<{/if}> value='<{$c.product_code}>'><{$c.product_code}>
									[<{$c.product_cnname}> <{$c.product_enname}>]</option>
								<{/foreach}>
						</select>
            <!--<select name="" class="select2 select">
            	<option>上门揽件</option>
                <option></option>
            </select>-->
        </div>
    	<div class="title widthLeft">2、发件人<!--<input type="button" value="添加到地址簿" class="btn1"><input type="button" value="发件人地址簿">--></div>
        <input type="hidden" name="consignee[shipper_account]" value="<{if isset($shipperCustom)}><{$shipperCustom.shipper_account}><{/if}>">
        <div class="contentLeft contentType4 borderR borderB">
        	<h3>联系人姓名*</h3>
            <input type="text" name="shipper[shipper_name]" class="checkchar1" value="<{if isset($shipperCustom)}><{$shipperCustom.shipper_name}><{/if}>">
        </div>
        <div class="contentLeft contentType4 borderB">
        	<h3>发件人参考信息</h3>
            <input type="text" class="checkchar4"  value='<{if isset($order)}><{$order.refer_hawbcode}><{/if}>'
							name='order[refer_hawbcode]' id='refer_hawbcode' />
        </div>
        <div class="contentLeft contentType3 borderB">
        	<h3>公司名称*</h3>
            <input type="text" name="shipper[shipper_company]" class="checkchar" value="<{if isset($shipperCustom)}><{$shipperCustom.shipper_company}><{/if}>">
        </div>
        <div class="contentLeft contentType4 borderR borderB" style="height:138px;">
        	<h3>国家*</h3>
            <input type="text"  value="CN" disabled="disabled" style="background:#cfcfcf; margin-bottom:20px;">
        	<input type="hidden" name="shipper[shipper_countrycode]" value="<{if isset($shipperCustom)}><{$shipperCustom.shipper_countrycode}><{/if}>">
        	<h3>邮编*</h3>
            <input type="text" name="shipper[shipper_postcode]" value="<{if isset($shipperCustom)}><{$shipperCustom.shipper_postcode}><{/if}>">
        </div>
        <div class="contentLeft contentType4 borderB" style="height:138px;">
        	<h3>地址*</h3>
            <input type="text" name="shipper[shipper_street]" class="checkchar2" style="margin:0px 0 6px" value="<{if isset($shipperCustom)}><{$shipperCustom.shipper_street}><{/if}>">
            
        </div>
        <div class="contentLeft contentType4 borderR">
        	<h3>城市*</h3>
            <input type="text" name="shipper[shipper_city]" class="checkchar2" value="<{if isset($shipperCustom)}><{$shipperCustom.shipper_city}><{/if}>">
        </div>
        <div class="contentLeft contentType4">
        	<h3>电话号码*</h3>
            <input type="text" name="shipper[shipper_telephone]"  class="order_phone" value="<{if isset($shipperCustom)}><{$shipperCustom.shipper_telephone}><{/if}>">
        </div>
    	<div class="title widthLeft" style="float:left">3、收件人<!--<input type="button" value="添加到地址簿" class="btn1"><input type="button" value="收件人地址簿">--></div>
        <div class="contentLeft contentType3 borderB">
        	<h3>公司名称*</h3>
            <input type="text" class="checkchar" value="<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_company}><{/if}>"
							name='consignee[consignee_company]' id='consignee_company' />
        </div>
        <div class="contentLeft contentType4 borderR borderB" style="height:138px;">
        	<h3>国家*</h3>
        	<select class='input_select country_code'
							name='order[country_code]'
							default='<{if isset($order)}><{$order.country_code}><{/if}>'
							id='country_code' style='width: 400px; max-width: 400px;'>
								<option value='' class='ALL'><{t}>-select-<{/t}></option>
								
								<{foreach from=$country item=c name=c}>
								<option value='<{$c.country_code}>' country_id='<{$c.country_id}>' class='<{$c.country_code}>'><{$c.country_code}> [<{$c.country_cnname}>  <{$c.country_enname}>]</option>
								<{/foreach}>
								 
						</select>
            <!--<input type="text"  style="background:#cfcfcf; margin-bottom:0px;">-->
        	<h3>州*</h3>
        	<input type="text" class="checkchar2"  style="background:#cfcfcf; margin-bottom:0px;" placeholder='如果国家没有州应不填写' value="<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_province}><{/if}>"
							name='consignee[consignee_province]' id='consignee_province' />
        </div>
        <div class="contentLeft contentType4 borderB" style="height:138px;">
        	<h3>地址*</h3>
            <input type="text"  class="checkchar2" style="margin:0px 0 6px" value="<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_street}><{/if}>"
							name='consignee[consignee_street]' id='consignee_street' /><!--<span class="module-title"><{t}>收件人门牌号<{/t}>：</span>-->
						    <input type='hidden' class='input_text doorplate'
							value='<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_doorplate}><{/if}>'
							name='consignee[consignee_doorplate]' id='consignee_doorplate' />
            <input type="text" style="margin:0px 0 6px" value="<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_street2}><{/if}>"
							name='consignee[consignee_street2]' id='consignee_street2' />
            <input type="text" style="margin:0px 0 6px" value="<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_street3}><{/if}>"
							name='consignee[consignee_street3]' id='consignee_street3' />
        </div>
        <div class="contentLeft contentType4 borderR  borderB">
        	<h3>城市*</h3>
            <input type="text"  class="checkchar3" value="<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_city}><{/if}>"
							name='consignee[consignee_city]' id='consignee_city' />
        </div>
        <div class="contentLeft contentType4 borderB">
        	<h3>联系人*</h3>
            <input type="text"  class="checkchar5" value='<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_name}><{/if}>'
							name='consignee[consignee_name]' id='consignee_name' />
        </div>
        <div class="contentLeft contentType4 borderR  borderB">
        	<h3>邮编*</h3>
            <input type="text" value='<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_postcode}><{/if}>'
							name='consignee[consignee_postcode]' id='consignee_postcode' />
        </div>
        <div class="contentLeft contentType4 borderB">
        	<h3>电话号码*</h3>
            <input type="text" class="order_phone" value='<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_telephone}><{/if}>'
							name='consignee[consignee_telephone]' id=consignee_telephone />
        </div>
        <div class="contentLeft contentType3 borderB" style="height:144px;">
        </div>
        
    </div>
    <div class="wrapRight">
    	<div class="title widthRight">4、内件性质</div>
        <div class="contentRight contentType2">
        	<label>文件</label>
            <input type="radio"  name="order[mail_cargo_type]" <{if !isset($order.mail_cargo_type)}>checked<{/if}> <{if $order.mail_cargo_type eq 3}>checked<{/if}>  value="3"/>
        	<label>物品</label>
            <input type="radio" name="order[mail_cargo_type]" <{if $order.mail_cargo_type eq 4}>checked<{/if}> value="4"/>
        </div>
    	<div class="title widthRight">5、快递详细信息</div>
        <div class="contentLeft contentType5 borderB" style="height:126px;">
        	<h3>包装类型*</h3>
            <div class="table">
            	<table border="1" cellpadding="0" cellspacing="0">
                	<tr>
                    	<td>件数*</td>
                    	<td>单件重量*</td>
                    	<td>长度(厘米)</td>
                    	<td>宽度(厘米)</td>
                    	<td style="border-right:none">高度(厘米)</td>
                    </tr>
                    <{if $invoice}> <{foreach from=$invoice name=i item=i}>
	                    <tr>
	                    	<td style="border-bottom:none">
							<input type='text' class='quantity' name='invoice[invoice_quantity][]' value='<{$i.invoice_quantity}>'>
							</td>
	                    	<td style="border-bottom:none">
							<input type='text' class='weight' name='invoice[invoice_weight][]' value='<{$i.invoice_weight}>'></td>
	                    	<td style="border-bottom:none">
							<input type='text' class='order_volume1' name='invoice[invoice_length][]' value='<{$i.invoice_length}>'>
							</td>
	                    	<td style="border-bottom:none">
							<input type='text' class='order_volume1' name='invoice[invoice_width][]' value='<{$i.invoice_width}>'>
							</td>
	                    	<td style="border-bottom:none; border-right:none"  >
							<input type='text' class='order_volume1' name='invoice[invoice_height][]' value='<{$i.invoice_height}>'>
							</td>
	                    </tr>
                    
                    <{/foreach}> <{else}>
	                    <tr>
							<td style="border-bottom:none">
							<input type='text' class='quantity' name='invoice[invoice_quantity][]' value=''>
							</td>
	                    	<td style="border-bottom:none">
							<input type='text' class='weight' name='invoice[invoice_weight][]' value=''></td>
	                    	<td style="border-bottom:none">
							<input type='text' class='order_volume1' name='invoice[invoice_length][]' value=''>
							</td>
	                    	<td style="border-bottom:none">
							<input type='text' class='order_volume1' name='invoice[invoice_width][]' value=''>
							</td>
	                    	<td style="border-bottom:none; border-right:none"  >
							<input type='text' class='order_volume1' name='invoice[invoice_height][]' value=''>
							</td>
						
	                	</tr>
	                	 <tr>
							<td style="border-bottom:none">
							<input type='text' class='quantity' name='invoice[invoice_quantity][]' value=''>
							</td>
	                    	<td style="border-bottom:none">
							<input type='text' class='weight' name='invoice[invoice_weight][]' value=''></td>
	                    	<td style="border-bottom:none">
							<input type='text' class='order_volume1' name='invoice[invoice_length][]' value=''>
							</td>
	                    	<td style="border-bottom:none">
							<input type='text' class='order_volume1' name='invoice[invoice_width][]' value=''>
							</td>
	                    	<td style="border-bottom:none; border-right:none"  >
							<input type='text' class='order_volume1' name='invoice[invoice_height][]' value=''>
							</td>
						
	                	</tr>
	                		
                    <{/if}>
                    <!--
                	<tr>
                    	<td style="border-bottom:none"></td>
                    	<td style="border-bottom:none"></td>
                    	<td style="border-bottom:none"></td>
                    	<td style="border-bottom:none"></td>
                    	<td style="border:none"></td>
                    </tr>
                    -->
                </table>
                <!--<p>包裹总数量：<span>1</span>总重量：<span id="order_weight_ps">0</span>公斤</p>-->
                <input type='hidden' class='input_text weight'
							value='<{if isset($order)}><{$order.order_weight}><{/if}>'
							name='order[order_weight]' id='order_weight' />
            </div>
        </div>
    	<div class="title widthRight" style="float:left">6、交运物品详细说明<!--<input type="button" style="margin-left:210px;" value="添加到内容列表" class="btn1"><input type="button" value="内容列表">--></div>
        <div class="contentLeft contentType5 borderB" style="height:108px;">
        	<h3 style="margin-bottom:10px;">提供内容和数量*</h3>
            <div class="table">
            	<table border="1" cellpadding="0" cellspacing="0">
                	<tr>
                    	<td style="width:100px;">*中文品名：</td>
                    	<td style="border-right:none"><input type="text" class="" name='invoice[invoice_cnname][]' value='<{$invoice[0].invoice_cnname}>'></td>
                    </tr>
                	<tr>
                    	<td style="border-bottom:none">*英文品名：</td>
                    	<td style="border:none"><input type="text" class="invoename"  name='invoice[invoice_enname][]' value='<{$invoice[0].invoice_enname}>'></td>
                    </tr>
                </table>
            </div>
        </div>        
    	<div class="title widthRight" style="float:left">7、只针对包裹快件(海关要求)</div>
        <div class="contentLeft contentType5 borderB" style="height:30px;">
        	<h3 style="margin-bottom:10px;">附上形式发票或商业发票的原版和2份复印件。</h3>
        </div>        
        <div class="contentLeft contentType6 borderR borderB " style="height:89px;">
        	<h3>发件人增值税/商品服务税号</h3>
            <input type="text" disabled class="level7 use" name='invoice[invoice_shippertax][]' value='<{$invoice[0].invoice_shippertax}>'>
        </div>
        <div class="contentLeft contentType7 borderB" style="height:89px;">
        	<h3>收件人增值税/商品服务税号</h3>
            <input type="text" disabled class="level7 use" name='invoice[invoice_consigneetax][]' value='<{$invoice[0].invoice_consigneetax}>'>
        </div>
        <div class="contentLeft contentType6 borderR borderB">
        	<h3>通关的申报价值（用于商业/形式发票）</h3>
             <input type="text" style="width:203px;" disabled class="level7 use invoice_unitcharge" name='invoice[invoice_totalcharge_all][]' value='<{$invoice[0].invoice_totalcharge_all}>'>
        	 <span>USD</span>	
        </div>
        <div class="contentLeft contentType7 borderB">
        	<h3>协调商品代码（如果需要）</h3>
            <input type="text" disabled class="level7 use" name='invoice[hs_code][]' value='<{$invoice[0].hs_code}>'>
        </div>
        <div class="contentLeft  contentType5 borderB" style="height:72px;">
        	<p>快件保险（保险价值不得高于申报价值）参见 条款与条件</p>
            <div class="seven">
			<div class="check">
			<input id="C2" value="C2" class="level7 use" disabled name="extraservice[]" type="checkbox" ></div>
			<label>是</label><label>保险价值</label>
			<input type="text" disabled class="level7 use invoice_unitcharge" value="" name="order[insurance_value_gj]" disabled style="width:100px;">
			<label>保费</label>
			<input type="text" disabled  class="use invoice_unitcharge" value="" name="order[insurance_value]" disabled style="width:100px;">
			</div>
            <!--<div class="seven"><div class="check" style="top:-2px; left:112px;"><input name="" type="checkbox" value=""></div><label>制作发票</label><label>是</label></div>-->
        </div>        
    	<div class="title widthRight" style="float:left">8、额外服务选项</div>
        <div class="contentLeft contentType5 borderB" style="height:95px;">
            <div class="seven" style="margin:0 0 10px 0">
            <div class="checkgroup" style="position:absolute;left:7px;z-index:9000;width:20px;height:20px;"></div>
            <div class="check" style="top:-2px; left:12px;"><input value="C5" class="level8 " name="extraservice[]" type="checkbox" ></div><label>是</label><label>文件保障服务</label><label>附加费3元/票</label></div>
            <div class="seven" style="margin:0 0 10px 0">
            <div class="checkgroup" style="position:absolute;left:7px;z-index:9000;width:20px;height:20px;"></div>
            <div class="check" style="top:-2px; left:12px;"><input value="C6" class="level8 " name="extraservice[]" type="checkbox" ></div><label>是</label><label>文件保障服务</label><label>附加费12元/票</label></div>
            <p style="color:#f00">文件保障说明：3元中速险每票快件最高赔付5000元,12元中速险每票快件最高赔付22000元需要提供“文件保险通知书”（与文件价值无关）。</p>
        </div>
    	<div class="title widthRight" style="float:left">9、发件人协议</div>
        <div class="contentLeft contentType5 borderB" style="height:110px;">
        	<p style="margin-bottom:10px;">除非提供另外的书面协议，我/我们同意下述附件所列运输条款和条件将作为我/我们与中国邮政速递物流有限公司之间的合同条款</p>
            <div class="seven" style="margin:0 0 10px 0"><div class="check" style="top:-2px; left:212px;"><input name="" id="allow" type="checkbox" value=""></div><p style="text-align:center;">我同意</p></div>
        </div>
        
</div>
<!--一些配置-->
<input type="hidden" name="consignee[consignee_certificatetype]" value=""/>
<input type="hidden" value="1" name="order[order_pieces]"/>
<input type="hidden"  value="1" name="order[order_weight]" id="order_weight">
<input type="hidden" value="" name="consignee[consignee_certificatecode]" id="consignee_certificatecode">
<!--一些配置-->
<div class="bottomBox">
	<div class="btn2">清空内容</div>
	<div class="btn3" status="P" id="orderSubmitBtn">提交并打印运单</div>
	<!--
    <div class="btn4" style="display:none">
            <input type="radio" name="xingzhi1" />
        	<label style="margin-right:30px;">A4纸运单</label>
            <input type="radio" name="xingzhi1" checked />
        	<label>标签运单</label>
    </div>
    -->
</div>

</form>

</div>
<script>
$(function(){
 $("#orderSubmitBtn").click(function(){
 	if(!$("#allow").attr("checked")){
 		alert("你还没有同意发件人协议");
 		return false;
 	}
 	
 	formSubmit('P');
 });
 
 $("input[name='order[mail_cargo_type]']").change(function(){
 	var type  = $(this).val();
 	if(type==3){
 		$(".level7").attr("disabled","true");
 		$(".level7").removeClass("use").addClass("use");;
 		$(".level8").removeAttr("disabled");
 	}else{
 		$(".level8").attr("disabled","true");
 		$(".level7").addClass("use").removeClass("use");;
 		$(".level7").removeAttr("disabled");
 		$("input[name='order[insurance_value_gj]']").attr("disabled","true").addClass("use");
 	}
 })	
 //计算保费
 function getInsurance_value(){
 	//选中
 	//$("#C2").attr("checked","checked");
 	var gj = $("input[name='order[insurance_value_gj]']").val();
 	var obj = $("input[name='order[insurance_value]']");
 	var insurance_val=0;
 	if(!gj){
 	}else{
 		gj = parseFloat(gj);
 		if(gj<=0||isNaN(gj)){
 		
 		}else{
 			insurance_val=gj>10000?gj*0.0015:10;
 			insurance_val=parseInt(insurance_val*10)/10;
 		}
 	}
 	obj.val(insurance_val);
 }
 $("input[name='invoice[invoice_totalcharge_all][]']").keyup(function(){
 	var that = $("input[name='order[insurance_value_gj]']");
 	if(that.val()){
 		checkBf();
 	}
 
 });
 $(function(){
 	var tip = getTipTpl();
	 if( $("input[name='order[insurance_value_gj]']").siblings('.info').size()==0){
		 $("input[name='order[insurance_value_gj]']").parent().prepend(tip);
	 }
 })
 
 function checkBf(){
    var that = $("input[name='order[insurance_value_gj]']");
 	var now_value = parseFloat(that.val());
 	var invoice_totalcharge_all = parseFloat($("input[name='invoice[invoice_totalcharge_all][]']").val());
 	if(!invoice_totalcharge_all){
 		$("input[name='order[insurance_value]']").val('');
 		return false;
 	}
 	
 	var hl = 6.5;
 	var max_insurance_value = invoice_totalcharge_all*hl;
	var msg = '保险金额不得大于申报价值';
	var tip = $("input[name='order[insurance_value_gj]']").siblings('.info');
 	tip.hide("fast",function(){$(this).css("display","none")});
 	that.val(max_insurance_value);
 	getInsurance_value();
 
 }
 $("input[name='invoice[invoice_totalcharge_all][]']").keyup(function(){
 	//计算最高保费
 	checkBf();
 });
 
 //单选效果
 $(".checkgroup").click(function(){
 	if(!$("input[name='invoice[invoice_shippertax][]']").hasClass("use"))
 	  return false;
 	$("input[name='extraservice[]']").attr('checked',false);
 	if($(this).attr('checked')){
 		$(this).next('.check').find("input").attr('checked',false);
 		$(".checkgroup").attr('checked',false);
 		$(this).attr('checked',false);
 	}else{
 		$(this).next('.check').find("input").attr('checked',true);
 		$(".checkgroup").attr('checked',false);
        $(this).attr('checked',true);  	
 	}
 });
});
</script>