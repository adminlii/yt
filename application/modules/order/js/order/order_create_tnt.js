/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2016-08-12 18:10:19
 * @version $Id$
 */


$(function () {
	$('.Floor_comlu .slideTd input').click(function(){
		$(this).next('searchbar').show(300);
	});


$(".invoice h3").click(function(){
	$('.innerbox').stop().fadeToggle('fast');
	if($(this).find("em").hasClass("on")){
		$(this).find("em").removeClass("on");
	}else{
		$(this).find("em").addClass("on").siblings("em").removeClass("on");
		}
	});

	//选择物品
	$('.opends').click(function(){
		clearInvoice();
		$("#servicecode").html(getservicecode(1));
		/*$('#invoicediv').stop().fadeIn('fast');
		$('#servicediv').stop().fadeOut('fast');
		$('.invoice').stop().fadeIn('fast');*/
		$('#invoicediv').show();
		$('#servicediv').hide();
		$('.invoice').show();
		$('.activetable').show();
	});
	
	$('.clogds').click(function(){
		clearInvoice();
		$("#servicecode").html(getservicecode(0));
		/*$('#invoicediv').stop().fadeOut('fast');
		$('#servicediv').stop().fadeIn('fast');
		$('.invoice').stop().fadeOut('fast');*/
		$('#invoicediv').hide();
		$('#servicediv').show();
		$('.invoice').hide();
		$('.activetable').hide();
	});
});

//服务类型、
function getservicecode(type){
	var data_p = [];
	data_p.push({k:'P15D',v:'EXPRESS(DOCS)全球快递（文件）'});
	var data = [];
	data.push({k:'P15N',v:'EXPRESS(NON DOCS)全球快递（包裹）'});
	data.push({k:'P48N',v:'ECONOMY EXPRESS(NON DOCS)经济快递（包裹）'});
	data.push({k:'S48F',v:' ECONOMY FREIGHT经济空运'});
	data.push({k:'S728',v:'SPECIAL ECONOMY EXPRESS FBA亚马逊海外仓'});
	data.push({k:'S87',v:'AIRFREIGHT DOOR TO DOOR空运到门'});
	data.push({k:'S88',v:'AIRFREIGHT DOOR TO AIRPORT空运到港'});
	//物品
	var str = '<option value="">请选择</option>';
	if(type==1){
		$.each(data,function(k,v){
			str+='<option value="'+v['k']+'">'+v['v']+'</option>'
		});
	}else{
		$.each(data_p,function(k,v){
			str+='<option value="'+v['k']+'">'+v['v']+'</option>'
		});
	}
	return str;
}
//切换产品种类时数据清除
function clearInvoice(){
	//清空保险
	$("input[name='extraservice[]']").attr('checked',false);
	$("#invoicediv input:not(input[name='extraservice[]'])").val('');
}

$(function(){
	$('.innerbtn').click(function(){
		//判断是否填写了包裹信息
		if(!isEmptyTr($(this).parent().parent())){
			$(this).next('.pop_box').slideDown('400');
		}else{
			alert('请先填写包裹信息');
		}
		
	});
	$('.closepop').click(function(){
		
		if($(this).html()=="x"){
			var count = getInoviceCount($(this).parent().next('.textmian').find('table'));
			$(this).parents(".pop_box").prev().find("span").html(count);
		}else{
			//回调内件
			var count = getInoviceCount($(this).parent().prev());
			$(this).parents(".pop_box").prev().find("span").html(count);
		}
		$('.pop_box').slideUp('400');
	});

	$('.tbody1').on("click",".alonTr .innerbtn",function(){
		//判断是否填写了包裹信息
		if(!isEmptyTr($(this).parent().parent())){
			$(this).next('.pop_box').slideDown('400');
		}else{
			alert('请先填写包裹信息');
		}
	})
	$('.tbody1').on("click",".alonTr .closepop",function(){
		if($(this).html()=="x"){
			var count = getInoviceCount($(this).parent().next('.textmian').find('table'));
			$(this).parents(".pop_box").prev().find("span").html(count);
		}else{
			//回调内件
			var count = getInoviceCount($(this).parent().prev());
			$(this).parents(".pop_box").prev().find("span").html(count);
		}
		$('.pop_box').slideUp('400');
	})

});

function isEmptyTr(obj){
	
	console.log(obj);
	var flag = true;
	obj.children("td").each(function(index,dom){
		if($(dom).find("input").val()){
			flag = false;
			return false;
		}
	});
	return flag;
}

//统计内件数
function getInoviceCount(table){
	var tr = table.find("tr");
	var count = 0;
	tr.each(function(index,dom){
		if(!isEmptyTr($(dom))){
			count++;
		}
	});
	return count;
}

$(function () {
// 新增表单
	var show_count = 20;  
	var count = 1;  
	$(".AddTr").click(function () {
		var length = $(".tabInfo .tbody1>tr").length;
		//alert(length);
		if (length < show_count) 
		{
			$(".model1 tbody .alonTr").clone().appendTo(".tabInfo .tbody1");  
		}
	});
});
function deltr(opp) {
	var length = $(".tabInfo .tbody1>tr").length;
	//alert(length);
	if (length <= 1) {
		alert("至少保留一行");
	} else {
		$(opp).parent().parent().remove();//移除当前行
		
	}
}
// ----

$(function () {
// 新增内件
	var show_count2 = 20;  
	var count2 = 1;  
	$(".addtr2").click(function () {
		var length = $(this).parent('.btn_a1').prev('.neijian').children('.tbody2 tr').length;
		//alert(length);
		if (length < show_count2) 
		{
			$(".model2 tbody tr").clone().appendTo($(this).parent('.btn_a1').prev('.neijian').children('.tbody2'));  
		}
	});
});
function deltr2(opp) {
	var length = $(this).parent('.btn_a1').prev('.neijian').children('.tbody2 tr').length;
	//alert(length);
	if (length <= 1) {
		alert("至少保留一行");
	} else {
		$(opp).parent().parent().remove();//移除当前行
		
	}
}
// ----
$(function () {
// 动态的新增内件
	var show_count3 = 20;  
	var count3 = 1;  
	$(".tbody1").on("click",".dtadd",function () {
		var length = $(".neijian .tbody2 tr").length;
		//alert(length);
		if (length < show_count3) 
		{
			$('.model2 tbody tr').clone().appendTo($(this).parent('.btn_a1').prev('.neijian').children('.tbody2'));  
		}
	});
});
function deltr3(opp) {
	var length = $('.neijian .tbody2 tr').length;
	//alert(length);
	if (length <= 1) {
		alert("至少保留一行");
	} else {
		$(opp).parent().parent().remove();//移除当前行
	}
}


//获取海关信息
function getInovice(){
	var packData = [];
	var index = 0;
	var invoiceData = [];
	$(".tbody1>tr").each(function(index,ele){
		var that = $(ele);
		//遍历
		var _packData = {};
		_packData.ITEMS = that.find('td:eq(0) input').val();
		_packData.WEIGHT = that.find('td:eq(1) input').val();
		_packData.WEIGHTALL = that.find('td:eq(2) input').val();
		_packData.LENGTH = that.find('td:eq(3) input').val();
		_packData.WIDTH = that.find('td:eq(4) input').val();
		_packData.HEIGHT = that.find('td:eq(5) input').val();
		packData.push(_packData);
		$(".tbody1>tr:eq("+index+")>td:last>.pop_box>.contentP>.textmian>table tr:gt(0)").each(function(i,e){
			var cthat = $(e);
			var _invoiceData = {};
			_invoiceData.packId = index;
			_invoiceData.invoice_enname=cthat.find('td:eq(0) input').val();
			_invoiceData.invoice_quantity=cthat.find('td:eq(1) input').val();
			_invoiceData.invoice_weight=cthat.find('td:eq(2) input').val();
			_invoiceData.invoice_unitcharge=cthat.find('td:eq(3) input').val();
			_invoiceData.invoice_totalcharge=cthat.find('td:eq(4) input').val();
			_invoiceData.hs_code=cthat.find('td:eq(5) input').val();
			_invoiceData.invoice_proplace=cthat.find('td:eq(6) select').val();
			invoiceData.push(_invoiceData);
		});
	});
	return {"pack":packData,"invoice":invoiceData};
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
	
	if(insurance_value){
		var insurance_value_gj = $("input[name='order[insurance_value_gj]'").val();
		param+='&order[insurance_value_gj]='+insurance_value_gj+'&order[insurance_value]='+insurance_value; 
	 
	}
	//拼接发件人地址
	var shipperstree = "";
	$("#shipperstree").val()?shipperstree+=$("#shipperstree").val():'';
	$("#shipperstree1").val()?shipperstree+="||"+$("#shipperstree1").val():'';
	$("#shipperstree2").val()?shipperstree+="||"+$("#shipperstree2").val():'';
	param+='&shipper[shipper_street]='+shipperstree;
	//是否选择了发票
	if($("h3 em").hasClass("on")){
		param+='&order[invoice_print]=1';
	}
	//拼接物流的json串
	var invoice = getInovice();
	param+='&invoice='+JSON.stringify(invoice);
	 loadStart();
	$.ajax({
		type: "POST",
		url: "/order/order/createtnt",
		data: param,
		dataType:'json',
		success: function(json){
			window.cgavin = function(){
				var html = json.message;
				if(json.ask){
					html+="<br/>系统单号:"+json.order.shipper_hawbcode;
	                $('#shipper_hawbcode').val(json.order.shipper_hawbcode);                
					successTip(html,json.order);
					 //如果勾选了发票弹窗
	    			var invoice_type = $("input[name='order[invoice_type]']:checked").val();
					if($("h3 em").hasClass("on")&&json.ask==1)
					window.open("/order/invoice-print/invoice-label1/?orderId="+json.order.order_id+"&invoice_type="+invoice_type);
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
			//验证收发件人信息1
			$("#dialog-layer-tip p").html("验证收发件人信息");
			//setTimeout('$("#dialog-layer-tip p").html("验证收发件人信息...");',1000);
			//验证订单发票信息1
			setTimeout('$("#dialog-layer-tip p").html("验证订单发票信息...");',1000);
			//真正通知标签服务器1
			setTimeout('$("#dialog-layer-tip p").html("正在通知标签服务器...");',2000);
			window.dgavin = function(){loadEnd('')};
			
			setTimeout("dgavin()",3000);
			setTimeout("cgavin()",4000);
			
		}
	});
}
$(function(){
	//总重量计算
	$('.weight_do').live('keyup',function(){
		var that = $(this);
		//获取该tr对象
		var trobj = that.parent().parent();
		var weight = trobj.find('td:eq(1) input').val();
		if(!/(^0\.[1-9]$)|(^[1-9](\d{1,3})?(\.?\d?)$)/.test(weight)){
			weight = 0;
		}
		var pice = trobj.find('td:eq(0) input').val();
		if(!pice){
			pice = 0;
		}
		var weightall = accMul(weight,pice);
		trobj.find('td:eq(2) input').val(weightall);
	});
	
	//总价计算
	$('.invoice_unitcharge_do').live('keyup',function(){
		var that = $(this);
		//获取该tr对象
		var trobj = that.parent().parent();
		var unitcharge = trobj.find('td:eq(3) input').val();
		if(!/^\d+(\.\d{1,2})?$/.test(unitcharge)){
			unitcharge = 0;
		}
		var pice = trobj.find('td:eq(1) input').val();
		if(!pice){
			pice = 0;
		}
		var unitchargeall = accMul(unitcharge,pice);
		trobj.find('td:eq(4) input').val(unitchargeall);
	});
	//初始化汇率
	 window.huilv = {};
	 $.post("/order/order/get-currency-list",{},function(data){
						if(data.state){
							window.huilv = data.data;
							//生成汇率选择框
							var html = '';
							for(var i in data.data){
								html+="<option value="+i+">"+i+"</option>";
							}
							$("#currencetype").append(html);
						}else{
							
						}
					},"json");
	//保险筛选
	//计算保费
	 function getInsurance_value_tnt(gj){
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
	
	 $("input[name='order[invoice_totalcharge_all]']").keyup(function(){
		 var that = $(this);
		 var value = that.val();
		 var reg = /^\d+(\.\d{1,2})?$/;
		 err_tip(this,reg,'须为数字,且小数最多为2位');
		 if(!reg.test(value)){
			 $("input[name='order[insurance_value]']").val('');
			 $("input[name='order[insurance_value_gj]']").val('');
			 return false;
		 }
		//换算成汇率
		 var currency = $("#currencetype").val()?$("#currencetype").val():"USD";
		 var hl = huilv[currency];
		 var max_insurance_value = accMul(value,hl);
		 //取两位小数
		 max_insurance_value = parseInt(max_insurance_value*100)/100;
		 $("input[name='order[insurance_value_gj]']").val(max_insurance_value);
		 getInsurance_value_tnt(max_insurance_value);
		 
	 })
	
	$("#orderSubmitBtn").click(function(){
	 	if(!$("#allow").attr("checked")){
	 		alert("你还没有同意发件人协议");
	 		return false;
	 	}
	 	
	 	formSubmit('P');
	 });
	
	
})


/**
 * 验证器
 */


function getTipTpl(){
	return $('<div class="info" style=""><span class="Validform_checktip Validform_wrong"></span><span class="dec"><s class="dec1">◆</s><s class="dec2">◆</s></span></div>');
}
	$('#shipper_hawbcode').blur(function(){
		var order_id = $('#order_id').val();
		var refrence_no = $(this).val();
		var this_ = $(this);
		$.ajax({
			type: "POST",
			url: "/order/order/check-refrence-no",
			data: {'order_id':order_id,'refrence_no':refrence_no},
			dataType:'json',
			success: function(json){
				// loadEnd('',tip_id);
				var html = json.message;
				if(!json.ask){
					this_.siblings('.msg').text(html);
				}else{
					this_.siblings('.msg').text(html);
				}
			}
		});	
	});

	

	

	// 新增==============

	// 体积错误提示
		function vol_tip(obj,reg,msg){	
			var reg = reg;	
			var val = $(obj).val();
			var msg =msg;
			
			if(val==''){
				return;
			}
			var tip = getTipTpl();
			if($(obj).prev('.info').size()==0){
				$(obj).prev().after(tip);
			}
			
			var tip = $(obj).prev('.info');
			if(!reg.test(val)){
				$('.Validform_checktip',tip).text(msg);	
				var left = $(obj).position().left;
				var top = $(obj).position().top;
				tip.css({'left':(left-12),'top':(top-3)}).show();		
			}else{
				tip.hide();
			}
			
	}


	// 通用错误提示
function err_tip(obj,reg,msg){
			
			var reg = reg; 
			var val = $(obj).val();
			
			var tip = getTipTpl();
			
			if($(obj).siblings('.info').size()==0){
				$(obj).parent().prepend(tip);
			}else{
				
			}
			var tip = $(obj).siblings('.info');
			if(val==''){
				tip.hide();
				return;
			}
			if(!reg.test(val)){
				$('.Validform_checktip',tip).text(msg);	
				tip.show();			
			}else{
				tip.hide();
			}
			
		
	}
	
		function err_tip1(obj,reg,msg){
			
			var reg = reg; 
			var val = $(obj).val();
			if(val==''){
				return;
			}
			var nodeid = $(obj).attr("nodeid");
			var tip = $(obj).siblings('.info[nodeid="'+nodeid+'"]');
			if(!nodeid){
				var random_num = new Date().getTime()+''+parseInt(Math.random()*1000);
				tip = getTipTpl();
				tip.attr("nodeid",random_num);
				$(obj).attr("nodeid",random_num);
				$(obj).parent().prepend(tip);
			}
			var left = $(obj).position().left;
			if(!reg.test(val)){
				$('.Validform_checktip',tip).text(msg);	
				tip.show();			
			}else{
				tip.hide();
			}
			
		
	}
	
		

//公司
$('.checkchar').live('keyup',function(){
	if($(this).val()&&!/^[a-zA-Z0-9\s\.&,\-\/\(\)']{1,50}$/.test($(this).val())){
		alert('不允许出现非英文允许英文数字混合,长度最多50字符');
	}
})

//公司
$('.checkchar_company').live('keyup',function(){
	if($(this).val()&&!/^[a-zA-Z0-9\s\.&,\-\/\(\)']{1,50}$/.test($(this).val())){
		alert('不允许出现非英文允许英文数字混合,长度最多50字符');
	}
})

//发件人
$('.checkchar1').live('keyup',function(){
	if($(this).val()&&!/^[a-zA-Z0-9\s\.&,]{1,25}$/.test($(this).val())){
		alert('不允许出现非英文，长度最多25字符');
	}
})
//城市 
$('.checkchar3').live('keyup',function(){
	if($(this).val()&&!/^[a-zA-Z\s-]{0,30}$/.test($(this).val())){
		alert('不允许出现非英文，长度最多30字符');
	}
})

//地址
$('.checkchar2').live('keyup',function(){
	if($(this).val()&&!/^[0-9a-zA-Z,\s\.%&\(\)\{\},\$-;#@\*\[\]【】]{0,30}$/.test($(this).val())){
		alert('长度最多30字符');
	}
})

//发件人参考信息：商户订单号
$('.checkchar4').live('keyup',function(){
	err_tip(this,/^[\w\W]{0,35}$/,'长度最多35字符');
})
//收件人name
$('.checkchar5').live('keyup',function(){
	if($(this).val()&&!/^[a-zA-Z\s\.&,]{1,25}$/.test($(this).val())){
		alert('不允许出现非英文，长度最多25字符');
	}
})

// 体积
$('.order_volume').live('keyup',function(){
	err_tip(this,/^\d+(\.\d{1,2})?$/,'须为数字,且小数最多为2位');
})

$('.order_volume1').live('keyup',function(){
	err_tip(this,/^\d+(\.\d)?$/,'须为数字,且小数最多为1位');
})

// 电话
$('.order_phone').live('keyup',function(){
    //err_tip(this,/^\(\d+\)\d+-\d+$|^\d+\s\d+$/,'格式为(xxx)xxx-xxx 或xxx空格xxxxx');
	err_tip(this,/^(\d){4,25}$/,'格式为4-25位纯数字');
})

// 电话
$('.order_phone_consignee').live('keyup',function(){
    //err_tip(this,/^\(\d+\)\d+-\d+$|^\d+\s\d+$/,'格式为(xxx)xxx-xxx 或xxx空格xxxxx');
	err_tip(this,/(^\+[\d-\s\(\)]{7,15}$)|(^[\d-\s\(\)]{8,16}$)/,'电话格式不正确总长8-16位');
})

// 申报价值
   $('.invoice_unitcharge').live('keyup',function(){
	var reg = /^\d+(\.\d{1,2})?$/;
	err_tip(this,reg,'须为数字,且小数最多为2位');		
})

// 重量
$('.weight').live('keyup',function(){
	err_tip(this,/(^0\.[1-9]$)|(^[1-9](\d{1,3})?(\.\d)?$)/,'须为数字,且小数最多为1位,范围为0.1-9999.9');
})

// 数量
$('.quantity').live('keyup',function(){
	err_tip(this,/^[1-9](00|[0-9]?)$/,'须为正整数，范围为1-100');
})

// 海关商品名
$('.invoename').live('keyup',function(){
	var reg = /^[\w\s\.%&\(\)\{\},\$-;#@\*\[\]【】]+?$/;
	err_tip(this,reg,'须为英文');
})

//tp账号 
$('.tnt_tpacount').live('keyup',function(){
	var reg = /^[\w\W]{0,13}?$/;
	err_tip(this,reg,'小于13位');
})

//用户编码
$('.tntcoustomer_code').live('keyup',function(){
	var reg = /^[A-Za-z0-9]{8,10}$/;
	err_tip(this,reg,'用户编码格式应为字母数字8到10位');
})

//描述 
$('.customer_des').live('keyup',function(){
	var reg = /^[\w\W]{0,46}?$/;
	err_tip(this,reg,'小于46位');
})
// ==============结束
function checkjsonequire(obj1,obj2){ 
	if($.isEmptyObject(obj1)||$.isEmptyObject(obj2))
		return false;
	for(var i in obj1){
			if(obj1[i]!=obj2[i])
				return false;
		
	}
	return true;
}

function accMul(arg1, arg2) {
    var m = 0, s1 = arg1.toString(), s2 = arg2.toString();
    try { m += s1.split(".")[1].length } catch (e) { }
    try { m += s2.split(".")[1].length } catch (e) { }
    return Number(s1.replace(".", "")) * Number(s2.replace(".", "")) / Math.pow(10, m)
}; 
$(function(){
//弹窗
$("#shipperaddrs").click(function(){
	var url = "/order/order/shipper-adress?quick=77";
	if(isFirefox()){
		var params = "dialogWidth=800px;dialogHeight=400px";
		$("#refer_hawbcode").val('');
		var returnResult = window.showModalDialog(url, '',params);
		shipinglocdataBind(returnResult);
	}else{
		url += "&xhr=1";
		$.post(url,{},function(data){
			if(data.ack==1){
				var data = data.data;
				var html = '';
				for(var i in data){
					html+="<tr><td title='"+data[i]['shipper_name']+"'>"+data[i]['shipper_name']
					+"</td><td title='"+data[i]['shipper_company']+"'>"+data[i]['shipper_company']+"</td>"
					+"<td title='"+data[i]['shipper_street']+"'>"+data[i]['shipper_street']+"</td>"
					+"<td title='"+data[i]['shipper_city']+"'>"+data[i]['shipper_city']+"</td>"
					+"<td title='"+data[i]['shipper_postcode']+"'>"+data[i]['shipper_postcode']+"</td>"
					+"<td title='"+data[i]['shipper_telephone']+"'>"+data[i]['shipper_telephone']+"</td>"
					+"<td><a class='addtr2' href='javascript:;' onclick='selectShipRow(this)'>确定</a></td></tr>";
				}
				$("#shippingloc .contentP .textmian table tbody").empty().append(html);
				$("#shippingloc").show();
			}else{
			}
		},"json");
	}
});

 //弹窗
$("#consigneeaddrs").click(function(){
	var url = "/order/order/consignee-adress?quick=77";
	if(isFirefox()){
		
		var params = "dialogWidth=800px;dialogHeight=400px";
		var returnResult = window.showModalDialog(url, '',params);
		locdataBind(returnResult);
	}else{
		url += "&xhr=1";
		$.post(url,{},function(data){
			if(data.ack==1){
				var data = data.data;
				var html = '';
				for(var i in data){
					var stree = data[i]['consignee_street'];
					if(data[i]['consignee_street1']!=''){
						stree+="||"+data[i]['consignee_street1'];
					}
					if(data[i]['consignee_street2']!=''){
						stree+="||"+data[i]['consignee_street2'];
					}
					html+="<tr><td title='"+data[i]['consignee_company']+"'>"+data[i]['consignee_company']
					+"</td><td title='"+stree+"'>"+stree+"</td>"
					+"<td title='"+data[i]['country_cnname']+"'>"+data[i]['country_cnname']+"</td>"
					+"<td title='"+data[i]['consignee_province']+"'>"+data[i]['consignee_province']+"</td>"
					+"<td title='"+data[i]['consignee_city']+"'>"+data[i]['consignee_city']+"</td>"
					+"<td>"+data[i]['consignee_postcode']+"</td>"
					+"<td>"+data[i]['consignee_name']+"</td>"
					+"<td>"+data[i]['consignee_telephone']+"</td>"
					+"<td style='display: none;'>"+data[i]['consignee_countrycode']+"</td>"
					+"<td><a class='addtr2' href='javascript:;' onclick='selectRow(this)'>确定</a></td></tr>";
				}
				$("#consigneeloc .contentP .textmian table tbody").empty().append(html);
				$("#consigneeloc").show();
			}else{
			}
		},"json");
	}
	
	
});

 
//添加发件人地址
//记录上次请求的添加数据
window.lastparam = null;
$("#shipperaddaddress").click(function(){
   params={};
   params.E2=$("input[name='shipper[shipper_name]']").val();
	params.E3=$("input[name='shipper[shipper_company]']").val();
	var stree = "";
	$("#shipperstree").val()?stree+=$("#shipperstree").val():'';
	$("#shipperstree1").val()?stree+="||"+$("#shipperstree1").val():'';
	$("#shipperstree2").val()?stree+="||"+$("#shipperstree2").val():'';
	params.E7=stree;
	params.E6=$("input[name='shipper[shipper_city]']").val();
	params.E8=$("input[name='shipper[shipper_postcode]']").val();
	params.E10=$("input[name='shipper[shipper_telephone]']").val();
	params.E4 = "CN";
	params.E5 = "";
	//如果和上次请求的一致
	if(checkjsonequire(lastparam,params)){
		alert("和上次提交的内容一致,请勿提交");
		return false;
	}
	$.post("/order/order/shipper-adress-edit",params,function(data){
					if(data.state){
						alert("添加成功");
						window.lastparam=params;
					}else{
						alert(data.errorMessage);
						if(checkjsonequire(lastparam,params)){
							window.lastparam=null;
						}
					}
				},"json");
});
//添加收件人地址
$("#consigneeaddaddress").click(function(){
   params={};
   params.E2=$("#consignee_name").val();
	params.E3=$("#consignee_company").val();
	params.E7=$("#consignee_street").val();
	params.E21=$("#consignee_street2").val();
	params.E22=$("#consignee_street3").val();
	params.E6=$("#consignee_city").val();
	params.E8=$("#consignee_postcode").val();
	params.E10=$("#consignee_telephone").val();
	params.E4 =$("#country_code").val();
	params.E5 = $("#consignee_province").val();
	if(checkjsonequire(lastparam,params)){
		alert("和上次提交的内容一致,请勿提交");
		return false;
	}
	$.post("/order/order/consignee-adress-edit",params,function(data){
					if(data.state){
						alert("添加成功");
						window.lastparam=params;
					}else{
						alert(data.errorMessage);
						if(checkjsonequire(lastparam,params)){
							window.lastparam=null;
						}
					}
				},"json");
});

//邮编库选择
//dhl 邮编选择
$(document).on('click','.check_li',function(){
	switch($(this).parent().attr("_type")||$(this).prev().attr("_type")){
		case "postcode":
			$("input[name='shipper[shipper_city]']").val($(this).attr("city")?$(this).attr("city"):'');
			$("input[name='shipper[shipper_postcode]']").val($(this).attr("postcode")?$(this).attr("postcode"):'');
			$("#refer_hawbcode").val($(this).attr("citycode")?$(this).attr("citycode"):'');
			$("#checkpostcodediv").hide();
			$("#checkpostcode").empty();
			$("#checkpostcodediv a").remove();
			$("#checkcity").empty();
			$("#checkcity+a").remove();
			$("#checkpostcode1").empty();
			$("#checkpostcode1+a").remove();
			$("#checkcity1").empty();
			$("#checkcity1+a").remove();
		;break;
		case "city_ename":
			$("input[name='shipper[shipper_city]']").val($(this).attr("city")?$(this).attr("city"):'');
			$("input[name='shipper[shipper_postcode]']").val($(this).attr("postcode")?$(this).attr("postcode"):'');
			$("#refer_hawbcode").val($(this).attr("citycode")?$(this).attr("citycode"):'');
			$("#checkcitydiv").hide();
			$("#checkpostcode").empty();
			$("#checkpostcodediv a").remove();
			$("#checkcity").empty();
			$("#checkcity+a").remove();
			$("#checkpostcode1").empty();
			$("#checkpostcode1+a").remove();
			$("#checkcity1").empty();
			$("#checkcity1+a").remove();
		;break;
		case "postcode1":
			$("#consignee_city").val($(this).attr("city")?$(this).attr("city"):'');
			$("#consignee_postcode").val($(this).attr("postcode")?$(this).attr("postcode"):'');
			$("#consignee_province").val($(this).attr("provinceename")?$(this).attr("provinceename"):'');
			$("#checkpostcodediv1").hide();
			$("#checkpostcode").empty();
			$("#checkpostcodediv a").remove();
			$("#checkcity").empty();
			$("#checkcity+a").remove();
			$("#checkpostcode1").empty();
			$("#checkpostcode1+a").remove();
			$("#checkcity1").empty();
			$("#checkcity1+a").remove();
		;break;
		case "city_ename1":
			$("#consignee_city").val($(this).attr("city")?$(this).attr("city"):'');
			$("#consignee_postcode").val($(this).attr("postcode")?$(this).attr("postcode"):'');
			$("#consignee_province").val($(this).attr("provinceename")?$(this).attr("provinceename"):'');
			$("#checkcitydiv1").hide();
			$("#checkpostcode").empty();
			$("#checkpostcodediv a").remove();
			$("#checkcity").empty();
			$("#checkcity+a").remove();
			$("#checkpostcode1").empty();
			$("#checkpostcode1+a").remove();
			$("#checkcity1").empty();
			$("#checkcity1+a").remove();
		;break;
	}
});
//记录keyup时间的时间如果keyup时间太短则终止上次延迟执行的事件
 window.lastKeyUp = {};
 window.lastKeyUp.lasttime_1 = {time:0,timer:null}; //上次执行时间初始化
 
$("input[name='shipper[shipper_postcode]']").focus(function(){
	$("#checkpostcodediv").show();
		return false;
}).blur(function(){
	setTimeout('$("#checkpostcodediv").hide()',200);
}).keyup(function(){
	var select = {};
	select.cd = $("input[name='shipper[shipper_countrycode]']").val();
	select.pc = $(this).val().toUpperCase();
	var nowtime = new Date().getTime();
	var timer = setTimeout(function(){getpostcadeData(select,$(this),$("#checkpostcode"),$("#checkpostcodediv"),0)},200); 	
 	if(nowtime-window.lastKeyUp.lasttime_1.time<200){
 		clearTimeout(window.lastKeyUp.lasttime_1.timer);
 	}	
	window.lastKeyUp.lasttime_1.time = nowtime;
 	window.lastKeyUp.lasttime_1.timer=timer;
});

 $("input[name='shipper[shipper_city]']").focus(function(){
 	
 	//if($("#product_code").val()!="G_DHL")
	 	//return false;
	$("#checkcitydiv").show();
 	return false; 	
}).blur(function(){
	setTimeout('$("#checkcitydiv").hide()',200);
}).keyup(function(){
	
 	var select = {};
	select.cd = $("input[name='shipper[shipper_countrycode]']").val();
	select.cn = $(this).val().toUpperCase();
	var nowtime = new Date().getTime();
	var timer = setTimeout(function(){getpostcadeData(select,$(this),$("#checkcity"),$("#checkcitydiv"),0)},200); 	
 	if(nowtime-window.lastKeyUp.lasttime_1.time<200){
 		clearTimeout(window.lastKeyUp.lasttime_1.timer);
 	}	
	window.lastKeyUp.lasttime_1.time = nowtime;
 	window.lastKeyUp.lasttime_1.timer=timer;
});

$("input[name='consignee[consignee_postcode]").focus(function(){
	
	//if($("#product_code").val()!="G_DHL")
	 	//return false;
	$("#checkpostcodediv1").show();
		return false;
	var select = {};
	select.cd = $("#country_code").val();
	select.pc = $(this).val().toUpperCase();
	
	getpostcadeData(select,$(this),$("#checkpostcode1"),$("#checkpostcodediv1"),1);
}).blur(function(){
	//if($("#product_code").val()!="G_DHL")
	 	//return false;
	setTimeout('$("#checkpostcodediv1").hide()',200);
}).keyup(function(){
	//if($("#product_code").val()!="G_DHL")
	 //	return false;
	var select = {};
	select.cd = $("#country_code").val();
	select.pc = $(this).val().toUpperCase();
	var nowtime = new Date().getTime();
	var timer = setTimeout(function(){getpostcadeData(select,$(this),$("#checkpostcode1"),$("#checkpostcodediv1"),0)},200); 	
 	if(nowtime-window.lastKeyUp.lasttime_1.time<200){
 		clearTimeout(window.lastKeyUp.lasttime_1.timer);
 	}	
	window.lastKeyUp.lasttime_1.time = nowtime;
 	window.lastKeyUp.lasttime_1.timer=timer;
});

 $("input[name='consignee[consignee_city]']").focus(function(){
 	
 	//if($("#product_code").val()!="G_DHL")
	 	//return false;
	$("#checkcitydiv1").show();
 	return false; 	
	 	var select = {};
	select.cd = $("#country_code").val();
	select.cn = $(this).val().toUpperCase();
	
	getpostcadeData(select,$(this),$("#checkcity1"),$("#checkcitydiv1"),1);
}).blur(function(){
	//if($("#product_code").val()!="G_DHL")
	 	//return false;
	setTimeout('$("#checkcitydiv1").hide()',200);
}).keyup(function(){
	//if($("#product_code").val()!="G_DHL")
	 	//return false;
 	var select = {};
	select.cd = $("#country_code").val();
	select.cn = $(this).val().toUpperCase();
	var nowtime = new Date().getTime();
	var timer = setTimeout(function(){getpostcadeData(select,$(this),$("#checkcity1"),$("#checkcitydiv1"),0)},200); 	
 	if(nowtime-window.lastKeyUp.lasttime_1.time<200){
 		clearTimeout(window.lastKeyUp.lasttime_1.timer);
 	}	
	window.lastKeyUp.lasttime_1.time = nowtime;
 	window.lastKeyUp.lasttime_1.timer=timer;
});
 
 

});

//邮编库数据绑定

function getpostcadeData(select,that,obj,obj1,isshow){
	if(!select.cd){
		that.blur();
		alert('没有选择国家');
		return false;
	}
	select.dc = 'TNT';
	if(select.pc||select.cn){
		$.post("/order/order/get-tnt-postcode-list",select,function(data){
			if(data.state){
				
				
			}else{
				
			}
			var listr= "";
			for(var i in data.data){
				var dhlcount = data.data[i]["dhlcount"]?data.data[i]["dhlcount"]:'';
				var city = data.data[i]["cityename"];
				
				var citycode = data.data[i]["citycode"]?data.data[i]["citycode"]:'';
				var provinceename = data.data[i]["provinceename"]?data.data[i]["provinceename"]:'';
				listr+="<li provinceename='"+provinceename+"' account='"+dhlcount+"' postcode='"+data.data[i]["postcode"]+"' city='"+city+"' citycode='"+citycode+"' class='check_li'>"+data.data[i]["cityename"]+":"+data.data[i]["postcode"]+"</li>";
				
			}
			
			obj.empty().append(listr);
			obj1.find('.li_extentd').remove();
			//如果有下页的话
			if(data.nextpage>1){
				var alist="<a class='li_extentd' href='javascript:void(0)'  onclick = \"getMorePostCode(this,'"+data.select.cd+"','"+data.select.cn+"','"+data.select.pc+"',"+data.nextpage+")\">更多</a>"
				obj1.append(alist);
			}
			if(isshow){
				obj1.show();
			}
		},"json");	
	}else{
		obj.empty().append('');
		if(isshow){
			obj1.show();
		}
	}
}

function getMorePostCode(that,cd,cn,pc,p){
	var select = {};
	select.cd = cd ;
	select.cn = cn;
	select.pc= pc;
	select.p =p;
	select.dc = 'TNT';
	$.post("/order/order/get-tnt-postcode-list",select,function(data){
		if(data.state){
		
		}else{
			
		}
		//
		var listr= "";
		for(var i in data.data){
			var dhlcount = data.data[i]["dhlcount"]?data.data[i]["dhlcount"]:'';
			var city = data.data[i]["cityename"];
			if(city){
				var index = city.indexOf(",");
				if(index>=0){
					city =  city.substr(0,index);
				}
			}
			var citycode = data.data[i]["citycode"]?data.data[i]["citycode"]:'';
			var provinceename = data.data[i]["provinceename"]?data.data[i]["provinceename"]:'';
			listr+="<li provinceename='"+provinceename+"' account='"+dhlcount+"' postcode='"+data.data[i]["postcode"]+"' city='"+city+"' citycode='"+citycode+"' class='check_li'>"+data.data[i]["cityename"]+":"+data.data[i]["postcode"]+"</li>";
			
		}
		//如果有下页的话
		if(data.nextpage>1){
			var alist="<a class='li_extentd' href='javascript:void(0)'  onclick = \"getMorePostCode(this,'"+data.select.cd+"','"+data.select.cn+"','"+data.select.pc+"',"+data.nextpage+")\">更多</a>"
		}
		var prev = $(that).prev();
		$(that).remove();
		prev.append(listr);
		if(alist)
			prev.parent().append(alist);
		setTimeout(function(){
			switch(prev.attr("_type")){
	 		case "postcode":
	 			$("input[name='shipper[shipper_postcode]']")[0].focus();	
	 		;break;
	 		case "city_ename":
	 			$("input[name='shipper[shipper_city]']")[0].focus();
	 		;break;
	 		case "postcode1":
	 			$("#consignee_postcode")[0].focus();
	 		;break;
	 		case "city_ename1":
	 			$("#consignee_city")[0].focus();
	 		;break;
			}
			//parent.parent().show()
		},200);
	},"json");
}

//判断是否是火狐浏览器
function isFirefox(){
	/*
	var explorer =navigator.userAgent;
	return explorer.indexOf("Firefox") >= 0;
	*/
	return window.showModalDialog;
}

var selectRow = function (that){
	var tdArr = $(that).parent().parent().children();
	var params = {};
	params.consignee_company=tdArr.eq(0).html();
	params.consignee_street=tdArr.eq(1).html();
	params.consignee_province=tdArr.eq(3).html();
	params.consignee_city=tdArr.eq(4).html();
	params.consignee_postcode=tdArr.eq(5).html();
	params.consignee_name=tdArr.eq(6).html();
	params.consignee_telephone=tdArr.eq(7).html();
	params.consignee_countrycode=tdArr.eq(8).html();
	locdataBind(params);
	$("#consigneeloc").hide();
}

var selectShipRow = function (that){
	var tdArr = $(that).parent().parent().children();
	var params = {};
	params.shipper_name=tdArr.eq(0).html();
	params.shipper_company=tdArr.eq(1).html();
	params.shipper_street=tdArr.eq(2).html();
	params.shipper_city=tdArr.eq(3).html();
	params.shipper_postcode=tdArr.eq(4).html();
	params.shipper_telephone=tdArr.eq(5).html();
	shipinglocdataBind(params);
	$("#shippingloc").hide();
}
//绑定地址薄内容
//数据绑定
function locdataBind(returnResult){
	$("#consignee_company").val(returnResult.consignee_company);
	$("#country_code").val(returnResult.consignee_countrycode.toLocaleUpperCase());
	$("#consignee_province").val(returnResult.consignee_province);
	streeArr = returnResult.consignee_street.split("||");
	$("#consignee_street").val(streeArr[0]?streeArr[0]:'');
	$("#consignee_street2").val(streeArr[1]?streeArr[1]:'');
	$("#consignee_street3").val(streeArr[2]?streeArr[2]:'');
	$("#consignee_city").val(returnResult.consignee_city);
	$("#consignee_name").val(returnResult.consignee_name);
	$("#consignee_postcode").val(returnResult.consignee_postcode);
	$("#consignee_telephone").val(returnResult.consignee_telephone);
}

function shipinglocdataBind(returnResult){
	//数据绑定
	$("input[name='shipper[shipper_name]']").val(returnResult.shipper_name);
	$("input[name='shipper[shipper_company]']").val(returnResult.shipper_company);
	streeArr = returnResult.shipper_street.split("||");
	$("#shipperstree").val(streeArr[0]?streeArr[0]:'');
	$("#shipperstree1").val(streeArr[1]?streeArr[1]:'');
	$("#shipperstree2").val(streeArr[2]?streeArr[2]:'');
	$("input[name='shipper[shipper_city]']").val(returnResult.shipper_city);
	$("input[name='shipper[shipper_postcode]']").val(returnResult.shipper_postcode);
	$("input[name='shipper[shipper_telephone]']").val(returnResult.shipper_telephone);
	//获取citycode
	var _params = {};
	_params.dc = "TNT";
	_params.cn = returnResult.shipper_city;
	$.post("/order/order/get-post-code-rule",_params,function(data){
					if(data.state){
						$("#refer_hawbcode").val(data.data[0]['citycode']?data.data[0]['citycode']:'');
					}
				},"json");
}
