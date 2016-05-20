

function getDetail(order_id_arr){
	//订单明细
	$.ajax({
		type: "POST",
		async: false,
		dataType: "json",
		url: '/order/order-list/get-list-detail-list',
		data: {'order_id_arr':order_id_arr},
		success: function (results) {
			$.each(results,function(k,json){

				if(json.ask){
					var orderId = json.data.order_id;
					var html = '';
					var val = json.data;
					$.each(json.data.order_product, function(k, v) {
			            var target = (v.url&&v.url!='null'?" target='_blank'":"");
			            var pic = v.pic&&v.pic!='null'?v.pic:'/images/base/noimg.jpg';
			            var url = (v.url&&v.url!='null'?v.url:"javascript:;");
			            
			           
			            html +='';
			            
			            html+='<div style="clear:both;" class="product_data_line">';
			            
		                html +='<div style="float:left;width:75%;text-align:left;" class="ellipsis"><a href="'+url+'" '+target+' style="line-height: 25px;" class="imgPreview" url="'+pic+'">'+ v.product_title+'<img width="1" src="'+pic+'"></a></div>' ;

			            var give_up_title = parseInt(v.give_up)?'产品不发货':'产品正常处理';
			            html +='<span class="unitPrice give_up_'+v.give_up+'" title="'+give_up_title+'" style="width:100px;float:right;">单价:';
		                html += '<b>'+(v.unit_price ? v.unit_price:'0') + " " + (v.currency_code ? v.currency_code :'')+'</b>';
			            html +='</span>';

			            html+='</div>';

			            if(json.data.platform=='amazon'){
			            	v.op_record_id = v.op_ref_tnx;
			            }
	        			v.sku = v.sku!=undefined?v.sku:v.product_sku;
			        	if(v.sub_product){
			        		$.each(v.sub_product,function(sub_k,sub){

					            html += '<div style="clear:both;">';
					            
					        	html += '<div class="recordNo">recordNo :'+(v.op_record_id?v.op_record_id:'')+'</div>';   
					              
					            html += '<div class="itemId" style="display:none;">ItemID:'+(v.op_ref_item_id?v.op_ref_item_id:'')+'</div>';
					            
			    	            html += '<div class="sku">SKU: <b class="rmaSku">' + (v.sku)+'</b>' ;
	    			            html +='</div>';
			        			html += '<div class="warehouseSkuWrap">仓库SKU:<a  href="javascript:;" class="inventoryBtn warehouseSku" title="点击查看库存" warehouse_code="'+(val.warehouse_id&&warehouseJson[val.warehouse_id] ? warehouseJson[val.warehouse_id].warehouse_code:'')+'" sku="'+sub.pcr_product_sku+'">'+sub.pcr_product_sku + '</a></div>';
			    	            
	    			            html += '<div class="qty">Qty: <b class="rmaWarehouseSkuQty">' + (v.op_quantity*sub.pcr_quantity)+'</b></div>' ;

			    	            html+='<div style="width: 100px; float: right;">';
	    			            html+='<ul class="dropdown_nav">';
	    			            html+='<li>';
	    			            html+='<a href="javascript:;" class="orderRma" ordersId="'+orderId+'" rmaWarehouseSku="'+sub.pcr_product_sku+'" rmaSku="'+v.sku+'" rmaWarehouseSkuQty="'+(v.op_quantity*sub.pcr_quantity)+'">';
	    			            html+='RMA管理&nbsp;<strong style=""></strong>';
	    			            html+='</a>';
	    			            html+='</li>';
	    			            html+='</ul>';
	    			            html+='</div>';
	    			            
	    			            html+='</div>';
	    			            
			        		})
			        		
			        	}else{
    			            html+='<div style="clear:both;">';

				        	html += '<div class="recordNo">recordNo :'+(v.op_record_id?v.op_record_id:'')+'</div>';				              
				            html += '<div class="itemId" style="display:none;">ItemID:'+(v.op_ref_item_id?v.op_ref_item_id:'')+'</div>';
				            
		    	            html += '<div class="sku">SKU: <b class="rmaSku">' + (v.sku!=undefined?v.sku:v.product_sku)+'</b>' ;
    			            html +='</div>';
		        			html += '<div class="warehouseSkuWrap">仓库SKU:<a  href="javascript:;" class="inventoryBtn warehouseSku" title="点击查看库存" warehouse_code="'+(val.warehouse_id&&warehouseJson[val.warehouse_id] ? warehouseJson[val.warehouse_id].warehouse_code:'')+'"  sku="'+v.sku+'">'+v.sku + '</a></div>';
		    	            
    			            html += '<div class="qty">Qty: <b class="rmaWarehouseSkuQty">' + (v.op_quantity)+'</b></div>' ;

		    	            html+='<div style="width: 100px; float: right;">';
    			            html+='<ul class="dropdown_nav">';
    			            html+='<li>';
    			            html+='<a href="javascript:;" class="orderRma"  ordersId="'+orderId+'"  rmaWarehouseSku="'+v.sku+'" rmaSku="'+v.sku+'" rmaWarehouseSkuQty="'+v.op_quantity+'">';
    			            html+='RMA管理&nbsp;<strong style=""></strong>';
    			            html+='</a>';
    			            html+='</li>';
    			            html+='</ul>';
    			            html+='</div>';
    			            
    			            html += '</div>';
			        	}
			        });
					
					if(json.data.CheckoutStatus=='Complete'){
						$('#eBayPaymentStatus_'+json.data.order_id).addClass('iconmoney2');
					}
					if(json.data.eBayPaymentStatus=='PayPalPaymentInProcess'){
						$('#eBayPaymentStatus_'+json.data.order_id).addClass('iconpedding');
					}
					/**/
					$('#country_'+json.data.order_id).html(json.data.Country+'['+json.data.CountryName+']');  
					$('#consignee_'+json.data.order_id).html('收件人信息:姓名:'+json.data.Name+'国家:'+json.data.Country+'['+json.data.CountryName+']'+'收件地址:'+json.data.Street1+' '+json.data.Street2+'');

					
					$('#order_detail_'+json.data.order_id).html(html);
				};
			});
		}
	});
}



function loadData(page, pageSize) {
    EZ.listDate.myLoading();
    $.ajax({
        type: "POST",
        async: false,
        dataType: "json",
        url: EZ.url + "list/a/a/page/" + page + "/pageSize/" + pageSize,
        data: EZ.searchObj.serializeArray(),
        error: function () {
            paginationTotal = 0;
            EZ.listDate.EzWmsSetSearchData({msg: 'The URL request error.'});
            return;
        },
        success: function (json) {
            paginationTotal = json.total;
            if (!isJson(json)) {
                paginationTotal = 0;
                EZ.listDate.EzWmsSetSearchData({msg: 'Returns the data type error.'});
                return;
            }
            if (json.state == '1') {
            	EZ.listDate.html(EZ.getListData(json));
            	
            	var order_id_arr = [];
            	$.each(json.data,function(k,v){
            		order_id_arr.push(v.order_id);
            	});
            	//获取明细
            	getDetail(order_id_arr);
            	//隐藏多行的SKU
            	hideExcessSku();
            } else {
                EZ.listDate.EzWmsSetSearchData({state: 1});
            }
            if(json.reTongji){
            	setTimeout(function(){tongji();},500);
            }
            
        }
    });
}


EZ.getListData = function(json) {
	//设置订单条数
	setPageTotal(json.total);

    var clone = $('#fix_header_content .clone').clone();
    $('#fix_header').html(clone);

	var html = '';
	if($('.chooseTag').size()<1){
		$('.statusTag2').addClass('chooseTag');
		var opHtml = '';
		$.each(statusArr['2']['actions'],function(k,v){
			opHtml += v;
		})
		$(".opDiv").html(opHtml);
	}
	$(".checkAll").attr('checked', false);
	var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;

    //买家ID
    var _objBuyer="";
    var _tempClass="table-module-b2";
	$.each(json.data, function(key, val) {	
		var orderId = val.order_id;

        //买家ID，将相同买家ID的订单用一个颜色标记
        var objCurrtBuyer="";
        if(!_objBuyer){
            _objBuyer=val.buyer_mail;
        }else{
            objCurrtBuyer=val.buyer_mail;
            //如果当前买家与上一个买家相同，则背景颜色也相同
            if(objCurrtBuyer==_objBuyer){

            }else{
                //改变买家
                _objBuyer=objCurrtBuyer;
                //如果不是同一个买家，则用另外一种背景颜色
                if(_tempClass=="table-module-b2"){
                    _tempClass="table-module-b4";
                }else{
                    _tempClass="table-module-b2";
                }

            }
        }

		// alert(val.order_product[0].Site);
		html += "<tr class='"+_tempClass+"' id='order_wrap_"+val.order_id+"'>";
		html += '<td class="ec-center" valign="top"><input type="checkbox" class="checkItem" name="orderId[]" ref_id="'+val.refrence_no_platform+'" value="' + val.order_id + '"/></td>';
		        	

		/**单头信息 开始**/
    	html += '<td style="word-wrap:break-word;" class="order_line" valign="top">';
		html += '<p class="refrence_no_platform ellipsis order_no_copy" style="cursor: pointer;">订单:<a id="rmaOrderNo' + val.order_id + '" href="javascript:;" class="orderDetail" url="/order/order/detail/orderId/'+val.order_id+'" >'+ val.refrence_no_platform + '</a></p>';
		
		html += '<span style="display:none;" id="consignee_'+val.order_id+'"></span>';

//    		html += '<td style="word-wrap:break-word;">' + (val.order_desc == undefined ? '' : val.order_desc) + '</td>';
		html += '<p style="" class="buyerId ellipsis">买家ID:' +  val.buyer_id +'</p>'; 
		html += '<p style="" class="buyerId ellipsis">买家名称:' +  val.buyer_name +'</p>'; 
		html += '<p style="" class="buyerMail ellipsis">买家Email: ' +val.buyer_mail+'</p>'; 
		html += '<p style="" class="userAccount ellipsis">卖家: ' +  user_account_arr_json[val.user_account].platform_user_name + '</p>'; 
		
 
		if(val.refrence_no_warehouse){
			html += '<p style="" class="refrence_no_warehouse ellipsis">仓库单号:' + (val.refrence_no_warehouse) +'</p>'; 			
		} 


		if(val.refrence_no){
			html += '<p style="" class="refrence_no ellipsis">客户参考号:' + (val.refrence_no) +'</p>'; 			
		} 

		if($('#status').val()==''){
			html += '<p style="" class="order_status_title ellipsis">订单状态:' + (statusArr[val.order_status].name) +'</p>';  			
		}


		html+='<p>';
    
		html += ' <span class="logIcon " ref_id="'+val.refrence_no_platform+'" style="float: left;cursor: pointer;" title="查看操作日志"></span>';
		if(val.service_status){
			var tmp_docking_title = '';
			if(val.service_status == 1){
				tmp_docking_title = '正在同步到“'+val.service_provider+'”';
			}
			if(val.service_status == 2){
				tmp_docking_title = '已同步到“'+val.service_provider+'”';;
			}
			html += '<span class="icondcking'+val.service_status+'" title="'+tmp_docking_title+'" style="float: left;cursor: pointer;padding: 0px 2px;" >';
        	html += '</span>';
		}
		
		if(val.amountpaid&&val.amountpaid>0){
			html += ' <span class="iconmoney2 " id="eBayPaymentStatus_'+val.order_id+'" style="cursor: pointer;float:left;padding: 0 2px;" title="已付款"></span>';
		}
        if(val.sync_status==1){
            html += '<span class="iconshipebay" style="cursor: pointer;float:left;" title="已标记发货并同步到eBay"></span>';
        } 
        if(val.order_desc){
//        	html += '<span class="iconshipamazon" style="cursor: pointer;float:left;" title="订单备注"></span>';
		}
		if(val.operator_note){
//			html += '<span class="iconshipamazon" style="cursor: pointer;float:left;" title="客服留言"></span>';			
		} 
		if(val.abnormal_reason&&val.abnormal_reason!='null'){
//			html += '<span class="iconshipamazon" style="cursor: pointer;float:left;" title="异常信息"></span>';
		}
        if(val.ebay_case_type){
        	var case_inr = ['EBP_INR','INR','PAYPAL_INR'];
        	var case_snad = ['EBP_SNAD','PAYPAL_SNAD','SNAD'];
        	var case_tips = '';
        	if($.inArray(val.ebay_case_type, case_inr) != -1){
        		case_tips = '物品未收到';
        	}else if($.inArray(val.ebay_case_type, case_snad) != -1){
        		case_tips = '物品与描述不符';
        	}else if(val.ebay_case_type == 'UPI'){
        		case_tips = '未付款';
        	}else if(val.ebay_case_type == 'CANCEL_TRANSACTION'){
        		case_tips = '取消交易';
        	}        		
        	html += '<span class="iconcaseebay" style="float: left;cursor: pointer;padding: 0px 2px;" title="该订单存在纠纷('+case_tips+')"></span>';
        }
        if(val.leave_comment){
        	html += '<span class="iconfeedback_'+val.leave_comment+'" title="已评价(' + val.leave_comment + ')" style="float: left;cursor: pointer;padding: 0px 2px;" >';
        	html += '</span>';
        }
        if(val.order_refund){
        	html += '<span class="iconpaypal_refund" title="已存在Paypal退款申请" style="float: left;cursor: pointer;padding: 0px 2px;" >';
        	html += '</span>';
        }

        html += ' <span class="loadLogIcon hideIcon" ref_id="'+val.refrence_no_platform+'" style="float: left;cursor: pointer;" title="查看Load日志"></span>';
        html += ' <span class="completeIcon hideIcon" ref_id="'+val.refrence_no_platform+'" style="float: left;cursor: pointer;" title="手动标记发货到Ebay">手动标记发货到Ebay</span>'; 
        html += ' <span class="viewIcon hideIcon" ref_id="'+val.refrence_no_platform+'" style="float: left;cursor: pointer;" title="查看Ebay数据">查看Ebay数据</span>';    
        
		html+='</p>';

		html += '<p style="clear:both;"></p>';
		
		
		html += '</td>';
		/**单头信息 结束**/
		

		/**订单详情信息 开始**/
        html += '<td valign="top">';
        //此处定义一个样式，用以区别是否为合并订单，拆分订单(order_merge_x)
        html += '<div  class="product_data order_detail order_merge_'+val.is_merge+'" id="order_detail_'+val.order_id+'"><div style="text-align: center;margin-top:30px;"><img src="/images/base/loading.gif"/></div></div>'; 
        
        html +='<div style="width: 100%; clear:both; margin-top: 1px;">';
        if(val.order_desc){
			html += '<p class="note_span"  id="note_'+val.order_id+'"><b>订单备注</b>:' + (val.order_desc) +'</p>';
		}
		if(val.operator_note){
            html += '<p  class="operator_note_span" id="operator_note_'+val.order_id+'"><b>客服留言</b>:'+(val.operator_note)+'</p>';   				
		} 
//		if(val.order_status==7||val.order_status==0){
			if(val.abnormal_reason&&val.abnormal_reason!='null'){
				html += '<p style="word-wrap:break-word;">';
				html += '<b style="color:red;">异常信息</b>:<span>' + (val.abnormal_reason) +'</span>';		
				html += '</p>';
			}
//		}
        html += '</div>';
        
        html += '</td>';

		/**订单详情信息 结束**/

		/**订单金额 开始**/
        var pay_title='订单金额|运费|交易额|交易费|平台费';
        pay_title+="\n";
        pay_title+= val.amountpaid+"|";
        pay_title+= val.ship_fee+"|";
        pay_title+= val.subtotal+"|";
        pay_title+= val.finalvaluefee+"|";
        pay_title+= val.platform_fee+" ";
        pay_title+= val.currency+"";
        
        
        html += '<td  valign="top" title="'+pay_title+'">';
        html += '<span id="rmaAmountpaid'+orderId+'">' + (val.amountpaid == undefined ? '' : val.amountpaid) + '</span> <span id="rmaCurrencyCodeId'+orderId+'">' + (val.currency ? val.currency:'') + '</span>';
       
        html += '</td>';
		/**订单金额 结束**/

		/**配送信息 开始**/
        html += '<td style="word-wrap: break-word;" valign="top">';
        html += '<p class="ellipsis">国家:<span  id="country_'+val.order_id+'">' + (val.consignee_country?val.consignee_country:"") + '</span></p>';
        html += '<p class="ellipsis">平台配送:'+val.shipping_method_platform+" </p>";

		html += '<p style="" class="ellipsis">发运仓库:' + (val.warehouse_id ? warehouseJson[val.warehouse_id].warehouse_code:'未分配');
//		html += ' ['+warehouseJson[val.warehouse_id].warehouse_desc+']':'未分配' );
		html += '</p>';

        html += '<p class="ellipsis">仓库配送:'+(val.shipping_method?val.shipping_method:'未分配')+'</p>';    
		html += '<p style="" class="ellipsis">跟踪号:' + (val.shipping_method_no&&val.shipping_method_no!='null' ? val.shipping_method_no:'暂无') +'</p>';     
        html += '</td>';

		/**配送信息 结束**/

		/**时间 开始**/
        html += '<td  valign="top">';
        html += '创建:' + (val.date_create_platform?val.date_create_platform:'' ) + '<br/>';
		html += '付款:' + (val.date_paid_platform?val.date_paid_platform:'') + '<br/>';
		html += '审核:' + (val.date_release?val.date_release:'') + '<br/>';
		html += '发货:' + (val.date_warehouse_shipping ?val.date_warehouse_shipping:'') + '<br/>';;
        html += '</td>';
		/**时间 开始**/
        
		html += "</tr>";
	
	});
	
	return html;
}