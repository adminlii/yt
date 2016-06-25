
function getpostcadeData(select,that,obj,obj1,isshow){
	if(!select.cd){
		that.blur();
		alert('没有选择国家');
		return false;
	}
	select.dc = $("#product_code").val();
	if(select.pc||select.cn){
		$.post("/order/order/get-postcode-list",select,function(data){
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
				listr+="<li class='li_extentd' onclick = \"getMorePostCode(this,'"+data.select.cd+"','"+data.select.cn+"','"+data.select.pc+"',"+data.nextpage+")\">更多</li>"
			}
			obj.empty().append(listr);
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
	select.dc = $("#product_code").val();
	$.post("/order/order/get-postcode-list",select,function(data){
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
			listr+="<li class='li_extentd' onclick = \"getMorePostCode(this,'"+data.select.cd+"','"+data.select.cn+"','"+data.select.pc+"',"+data.nextpage+")\">更多</li>"
		}
		var parent = $(that).parent();
		$(that).remove();
		parent.append(listr);
		setTimeout(function(){
			switch(parent.attr("_type")){
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