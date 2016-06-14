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

function getpostcadeData(select,type,extend){
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