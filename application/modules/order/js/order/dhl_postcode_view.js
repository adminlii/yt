var province_view = [
  {id:1,province_name:"JIANGSU",province_cname:"江苏",dhlcount:603436301},                   
];
var postcade_view = [
{pid:1,postcode:"210000",city:"JS001",city_ename:"NANJING",city_cname:"南京"},
{pid:1,postcode:"214000",city:"JS002",city_ename:"WUXI",city_cname:"无锡"},
{pid:1,postcode:"221000",city:"JS003",city_ename:"XUZHOU",city_cname:"徐州"},
{pid:1,postcode:"213000",city:"JS004",city_ename:"CHANGZHOU",city_cname:"常州"},
{pid:1,postcode:"215000",city:"JS005",city_ename:"SUZHOU",city_cname:"苏州"},
{pid:1,postcode:"226000",city:"JS006",city_ename:"NANTONG",city_cname:"南通"},
{pid:1,postcode:"222000",city:"JS007",city_ename:"LIANYUNGANG",city_cname:"连云港"},
{pid:1,postcode:"223001",city:"JS008",city_ename:"HUAIAN",city_cname:"淮安"},
{pid:1,postcode:"224000",city:"JS009",city_ename:"YANCHENG",city_cname:"盐城"},
{pid:1,postcode:"225000",city:"JS010",city_ename:"YANGZHOU",city_cname:"扬州"},
{pid:1,postcode:"212000",city:"JS011",city_ename:"ZHENJIANG",city_cname:"镇江"},
{pid:1,postcode:"225300",city:"JS012",city_ename:"TAIZHOU",city_cname:"泰州"},
{pid:1,postcode:"223800",city:"JS013",city_ename:"SUQIAN",city_cname:"宿迁"},
{pid:1,postcode:"215300",city:"JS014",city_ename:"KUNSHAN",city_cname:"昆山"},
{pid:1,postcode:"215200",city:"JS015",city_ename:"WUJIANG",city_cname:"吴江"},
{pid:1,postcode:"215500",city:"JS016",city_ename:"CHANGSHU",city_cname:"常熟"},
{pid:1,postcode:"215400",city:"JS017",city_ename:"TAICANG",city_cname:"太仓"},
{pid:1,postcode:"215600",city:"JS018",city_ename:"ZHANGJIAGANG",city_cname:"张家港"},
{pid:1,postcode:"214200",city:"JS019",city_ename:"YIXING",city_cname:"宜兴"},
{pid:1,postcode:"214400",city:"JS020",city_ename:"JIANGYIN",city_cname:"江阴"},
{pid:1,postcode:"212300",city:"JS021",city_ename:"DANYANG",city_cname:"丹阳"},
];

function getprivinceBypostcade(pid){
	var data = {};
	for(var i in province_view){
		if(province_view[i]['id']==pid){
			data = province_view[i];
			break;
		}
	}
	return data;
}

function _getpostcadeData(select,type,extend){
	var data = [];
	for(var i in postcade_view){
		if(postcade_view[i][type].match(select)){
			if(extend){
				var provice = getprivinceBypostcade(postcade_view[i]['pid']);
				if(provice["id"]){
					for(var j in provice){
						postcade_view[i][j]=provice[j];
					}
				}
			}
			data.push(postcade_view[i]);
		}
	}
	
	return data;
		
}

var _postcade_view = postcade_view;
//为了减少循环 只有当筛选条件国家为中国的时候
for(var i in _postcade_view){
	var provice = getprivinceBypostcade(_postcade_view[i]['pid']);
	if(provice["id"]){
		for(var j in provice){
			_postcade_view[i][j]=provice[j];
		}
	}
}
function getpostcadeData(select,that,obj,obj1,isshow){
	if(!select.cd){
		that.blur();
		alert('没有选择国家');
		return false;
	}
	if(select.pc||select.cn){
		$.post("/order/order/get-postcode-list",select,function(data){
			var data ;
			if(data.state){
				//
				if(select.cd=='CN'){
					for(var i in data.data){
						data.data[i]['city'] = data.data[i]['cityename'];
						
						for(var j in _postcade_view){
							if(data.data[i]['cityename']==_postcade_view[j]['city_ename']){
								//data.data[i]['city'] 	= _postcade_view[j]['city'];
								//data.data[i]['province_name']= _postcade_view[j]['province_name'];
								data.data[i]['dhlcount'] = _postcade_view[j]['dhlcount'];
								break;
							}
						}
						
					}
				}
				
			}else{
				
			}
			//
			var listr= "";
			for(var i in data.data){
				var dhlcount = data.data[i]["dhlcount"]?data.data[i]["dhlcount"]:'';
				var city = data.data[i]["city"]?data.data[i]["city"]:data.data[i]["cityename"];
				if(city){
					var index = city.indexOf(",");
					if(index>=0){
						city =  city.substr(0,index);
					}
				}
				var provinceename = data.data[i]["provinceename"]?data.data[i]["provinceename"]:'';
				listr+="<li provinceename='"+provinceename+"' account='"+dhlcount+"' postcode='"+data.data[i]["postcode"]+"' citycode='"+city+"' class='check_li'>"+data.data[i]["cityename"]+":"+data.data[i]["postcode"]+"</li>";
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