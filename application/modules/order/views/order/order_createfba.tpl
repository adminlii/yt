<style>
.select1{
width: 100px;
}
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

</style>
<script type="text/javascript" src="/js/ajaxfileupload.js"></script>
<div class="location">
    		<p><a href="">在线订单</a> > <a href="">订单管理</a> > FBA录单</p>
    	</div>
    	<ul class="tabnav">
    		<li class="on"><a href="">FBA录单</a></li>
    		<li><a href="javascript:void(0)" onclick="leftMenu('order-list','订单管理','/order/order-list/list?quick=39')">订单管理</a></li>
    	</ul>
		
    	<div class="layerbody">
    		<div class="layTit">
    			<h3>基本信息</h3>
    		</div>
    		<table class="layTable" border="0" cellpadding="0" cellspacing="0">
    			<tr>
    				<td><p><i>*</i> 服务方式 : </p>
                        <select name="order[product_code]" id='product_code'>
                            <option value="">请选择</option>
                            <!--<option value="FBA">快递服务</option>-->
                            <option value="FBA1">空加派服务</option>
                            <!--<option value="FBA2">海运服务</option>-->
                        </select>
                    </td>
    				<td><p><i>*</i> FBA订单号 : </p><input type="text" name="order[refer_hawbcode]"/></td>
    			</tr>
    			<tr>
    				<td><p><i>*</i>箱数 : </p><input type="text" name="order[boxnum]" style="width:50%;"/></td>
    			
		
    				<td><p><i>*</i>上传装箱单 : </p><input type="file" id="invoicelist" name="invoicelist" style="width:60%;"/><button id='upid' type="button">上传</button><span style="margin-left: 2%;"><a href="/file/装箱单模板.xls" target="_blank">模板下载</a></span> </td>
    				<input type = 'hidden' name = "invoicelistrel" id="invoicelistrel"/>
    			</tr>
                <tr>
                    <td colspan="2"><p><i>*</i>上传发票 : </p><input type="file" id="invoice" name="invoice"/><button type="button" id="upid1">上传</button> <span style="margin-left: 2%;"><a href="/file/发票模板.xlsx" target="_blank">模板下载</a></span></td>
                	<input type = 'hidden' name = "invoicerel" id="invoicerel"/>
                </tr>
    		</table>
    	</div>
<form method="POST" action="" enctype="multipart/form-data" onsubmit="return false;" id="orderForm">
    	<div class="layerbody">
    		<div class="layTit">
    			<h3>收件人信息</h3>
    		</div>
    		<table class="layTable" border="0" cellpadding="0" cellspacing="0">
    			<tr>
    			<td><p>FBA仓库 : </p>
                        <select class="searchslect" name="consignee[storage]" style="width: 200px;">
                        	<option value="">请选择</option>
                            <{foreach from=$storageStore item=c name=c}>
								<option value='<{$c.storage}>'><{$c.storage}></option>
							<{/foreach}>
                        </select>
                    </td>
    				<td><p>国家 : </p>
                        <select disabled=true style="width:150px;"  id="consignee_countrycode">
                            <option value="">请选择</option>
                            <{foreach from=$country item=c name=c}>
								<option value='<{$c.country_code}>'  class='<{$c.country_code}>'><{$c.country_code}> [<{$c.country_cnname}>  <{$c.country_enname}>]</option>
							<{/foreach}>
                        </select>
                        <input type="hidden" name="consignee[consignee_countrycode]"/>
                    </td>
    				
    			</tr>
    			<tr>
    				<td><p>省/州 : </p><input type="text" name="consignee[consignee_province]"/></td>
    				<td><p>邮编 : </p><input type="text" name="consignee[consignee_postcode]"/></td>
    			</tr>
    			<tr>
    				<td><p>城市 : </p><input type="text"  name="consignee[consignee_city]"/></td>
    				<td><p>地址 : </p><input type="text" name="consignee[consignee_street]"/></td>
    			</tr>

    		</table>
    	</div>

    	<div class="layerbody shipperdiv">
    		<div class="layTit">
    			<h3>发件人信息 <i>*</i> <!--<a href="">刷新</a>--></h3>
    		</div>
    		<div class="TextList">
    			<{foreach from=$shipperCustom name=s item=s key=k}>
				<p>	
					<label><span class="inputsel"><input type="radio" name='consignee[shipper_account]'  value='<{$s.shipper_account}>'<{if $s.is_default}>checked<{/if}>/>
					</span> <span><{$s.shipper_company}></span>
					<span><{$s.shipper_countrycode}></span>
					<span><{$s.shipper_province}></span>
					<span><{$s.shipper_city}></span>
					<span><{$s.shipper_street}></span>
					<span><{$s.shipper_name}></span>
					<span><{$s.shipper_telephone}></span>
					
					<span><a href="javascript:;" class="open_btn" shipper_account="<{$s.shipper_account}>">修改信息</a></span></label>
			   </p>
			<{/foreach}>
    			
    			
    			</div>

<div class="addmore addlast">
    				<a href="javascript:;" class="open_btn">点击新增</a>
    			</div>
    		</div>
    	</div>

    	<div class="sendBtns">
    		<input class="btns1" type="submit" value="提交" />
    	</div>
		</form>
		<form id="submiterForm" onsubmit="return false;">
				<div class="WindowBox">
    				<div class="bgshaow closebtn"></div>
    				<div class="NewBody">
    					<div class="Title">
    						<h3><a href="javascript:;" class="closebtn"></a> 新增发件人</h3>
    					</div>
    					<div class="NewMain">
    						
    						<ul>
    							<li>
    								<span><i>*</i> 公司名：</span>
    								<input type="text" name="E3" value="" class="">
    							</li>
    							<li>
    								<span><i>*</i> 发件人姓名：</span>
    								<input type="text" name="E2" value="" class="">
    							</li>
    							<li>
    								<span><i>*</i> 发件人国家：</span>
    								<{ec name='E4' default='Y' search='N' class='select1'}>country<{/ec}>
    								<input type="text" name="E5" value="省/州" onfocus="if (value =='省/州'){value =''}" onblur="if (value ==''){value='省/州'}" class="input_text checkchar1">
    								<input type="text" name="E6" value="城市" onfocus="if (value =='城市'){value =''}" onblur="if (value ==''){value='城市'}" class="input_text checkchar3">
    							</li>
    							<li>
    								<span><i>*</i> 详细地址：</span>
    								<input type="text" name="E7" value="" class="">
    							</li>
    							<li>
    								<span><i>*</i> 邮编：</span>
    								<input type="text" name="E8" value="" class="">
    							</li>
    							<li>
    								<span><i>*</i> 联系电话：</span>
    								<input type="text" name="E10" value="" class="">
    							</li>
    							<li class="defult">
    								<label><input type="checkbox" value="1" name="E17" class=" "> 设为默认地址</label>
    							</li>
    						</ul>
    							<div class="sendBtns">
    							<input type="hidden" name="E0" value="" class="input_text">
    								<input type="submit" style="background: #005bac;border:1px solid #01447f;" id="orderSubmitBtn" value="提交" />
    							</div>
    						</form>
    					</div>
    				</div>
<script>
$(function(){
	//发件人js
		$('.shipperdiv').on('click','.open_btn',function(){
		
			//判断是否是新增或者修改
			var that = $(this);
			var text = $(this).text();
			if(text=='修改信息'){
				editShipperAccount(that.attr('shipper_account'));		
			}else{
				$('.NewMain input').each(function(index,element){
					var name = $(element).attr("name");
					if(name){
						switch(name){
							case 'E5':$(element).val('省/州');break;
							case 'E6':$(element).val('城市');break;
							case 'E17':$(element).attr('checked', false);break;
							default : $(element).val('');break;
						
						}
					}
				});
			}
			$('.WindowBox').fadeIn(300);
		
		
		});
		
		function editShipperAccount(shipper_account){	 
    $.ajax({
		   type: "POST",
		   url: '/order/submiter/get-by-json',
		   data: {'paramId':shipper_account},
		   async: true,
		   dataType: "json",
		   success: function(json){
			    if(json.state){
                    $.each(json.data,function(k,v){
                        v +='';
                        $("#submiterForm :input[name='"+k+"']").val(v); 			    	    
                    })
                    $("#submiterForm :checkbox[name='E17']").val('1');
                    if(json.data.E17==1||json.data.E17=='1'){
                    	$("#submiterForm :checkbox[name='E17']").attr('checked', true);
                    }else{
                    	$("#submiterForm :checkbox[name='E17']").attr('checked', false);
                    }                    
			    }else{
			       alertTip(json.message);
			       
			    }
		   }
	});
}
		
		$("#orderSubmitBtn").click(function(){
		loadStart();
		var params = $("#submiterForm").serialize();
		$.ajax({
		    type: "post",
		    async: false,
		    dataType: "json",
		    url: '/order/submiter/edit',
		    data: params,
		    success: function (json) {
		    	
		    	switch (json.state) {
		            case 1:
		            	//window.location.reload();
		            	//继续用ajax
		            	$.post("/order/order/shipper-adress-info", {},
							   function(data){
							   	  loadEnd();
							   	  $('.NewMain input').each(function(index,element){
										var name = $(element).attr("name");
										if(name){
											switch(name){
												case 'E5':$(element).val('省/州');break;
												case 'E6':$(element).val('城市');break;
												case 'E17':$(element).attr('checked', false);break;
												default : $(element).val('');break;
											
											}
										}
									});
							   	  $(".WindowBox").hide();
							   	  if(data.ask==1){
							   	  	var html = '';
							   	  	for(var i in data.data){
							   	  		if(data.data[i]['is_default']=='1'){
							   	  			html += '<p><label><span class="inputsel"><input type="radio" name="consignee[shipper_account]" checked value="'+data.data[i]["shipper_account"]+'"></span>';
							   	  		}else{
							   	  			html += '<p><label><span class="inputsel"><input type="radio" name="consignee[shipper_account]" value="'+data.data[i]["shipper_account"]+'"></span>';
							   	  		}
							   	  		html += '<span>'+data.data[i]["shipper_company"]+'</span><span>'+data.data[i]["shipper_countrycode"]+'</span><span></span><span>'+data.data[i]["shipper_city"]+'</span><span>'+data.data[i]["shipper_street"]+'</span><span>'+data.data[i]["shipper_name"]+'</span><span>'+data.data[i]["shipper_telephone"]+'</span><span><a href="javascript:;" class="open_btn" shipper_account="'+data.data[i]["shipper_account"]+'">修改信息</a></span></label></p>';
							   	  	}
							   	  	
							   	  	$(".TextList").empty().append(html);
							   	  }
							   }, "json");
		                break;
		            case 2:
			            //parent.getSubmiter();
		                //parent.alertTip(json.message);
		                //parent.$('.dialogIframe').dialog('close');
		                break;
		            default:
		           	 loadEnd();
		                var html = '';
		                if (json.errorMessage == null)return;
		                $.each(json.errorMessage, function (key, val) {
		                    html += '<span class="tip-error-message">' + val + '</span>';
		                });
		                alertTip(html);
		                break;
		        }
		    }
		});
	});
		
		$('.closebtn').click(function(event) {
		  $('.WindowBox').fadeOut(300);
		});
		//$('#country_code').chosen('destroy');
		$('.searchslect').chosen({search_contains:true});
		$('.searchslect').live('change',function(){
			var storage = $(this).val();
			if(!storage)
			 return false;
			$.ajax({
			   type: "POST",
			   url: '/order/order/get-storage',
			   data: {'storage':storage},
			   async: true,
			   dataType: "json",
			   success: function(json){
				    if(json.state){
				      $("input[name='consignee[consignee_countrycode]']").val(json.data.country);
	                  $("#consignee_countrycode").val(json.data.country);
	                  $("input[name='consignee[consignee_province]']").val(json.data.state);
	                  $("input[name='consignee[consignee_city]']").val(json.data.city);
	                  $("input[name='consignee[consignee_postcode]']").val(json.data.zip);
	                  $("input[name='consignee[consignee_street]']").val(json.data.stree);
				    }
			   }
			}); 
		});
		
		$(".btns1").click(function(){
		loadStart();
		//$("#orderForm").submit();
		var params = $("#orderForm").serialize();
		var productcode = $("#product_code").val();
		var refer_hawbcode = $("input[name='order[refer_hawbcode]']").val();
		var boxnum = $("input[name='order[boxnum]']").val(); 
		var invoicelistrel = $("#invoicelistrel").val();
		var invoicerel = $("#invoicerel").val();
		params+='&order[product_code]='+productcode+'&order[refer_hawbcode]='+refer_hawbcode+'&order[boxnum]='+boxnum+'&order[invoicelistrel]='+invoicelistrel+'&order[invoicerel]='+invoicerel;
		$.ajax({
		type: "POST",
		url: "/order/order/createfba",
		data: params,
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
	});
	
	
	//上传插件
	$("#upid").click(function(){
	
	$.ajaxFileUpload
	(
			{
				url: '/order/order/upload-file',
				secureuri: false,
				fileElementId: 'invoicelist',
				dataType: 'json',
				success: function (json, status) {
					if(json.state!=1){
						alert(json.message);
					}else{
						$("#invoicelistrel").val(json.data);
						alert('上传成功');
					}
				},
				error: function (data, status, e) {
					alert(e);
				}
			}
	)
	});
	$("#upid1").click(function(){
	$.ajaxFileUpload
	(
			{
				url: '/order/order/upload-file',
				secureuri: false,
				fileElementId: 'invoice',
				dataType: 'json',
				//data:{batchAddProduct_supplier:$("#batchAddProduct_supplier").val()},
				success: function (json, status) {
					if(json.state!=1){
						alert(json.message);
					}else{
						$("#invoicerel").val(json.data);
						alert('上传成功');
					}
				},
				error: function (data, status, e) {
					alert(e);
				}
			}
	)
	});	
	
	function successTip(tip, order) {
	if(order.order_status=='D'){
	
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
	                    //$('#shipper_hawbcode').val('');
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
	                    leftMenu('order-list','订单管理','/order/order-list/list');
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
	
});

</script>