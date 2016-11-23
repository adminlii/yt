<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="cache-control" content="no-cache">
	<meta http-equiv="expires" content="0">
	<title>发件人地址簿</title>
	<link href="/css/address/EShip.css" rel="stylesheet" type="text/css" />
	<link href="/css/address/simpletable.css" type="text/css" rel="stylesheet">
	<script src="/js/jquery-1.11.3.min.js"></script>
	<script type="text/javascript">
		window.name = "win_test";
		var msg = '';
		if(msg != null && msg != ''){
			alert(msg);
		}
		//选择行
		function selectRow(id){
			//根据按钮爬到行TR,然后行分析所有列
			//var count = $(id).parent().parent().children().size();
			var tdArr = $(id).parent().parent().children();
			var params = {};
			params.shipper_name=tdArr.eq(0).html();
			params.shipper_company=tdArr.eq(1).html();
			params.shipper_street=tdArr.eq(2).html();
			params.shipper_city=tdArr.eq(3).html();
			params.shipper_postcode=tdArr.eq(4).html();
			params.shipper_telephone=tdArr.eq(5).html();
			window.returnValue =params;
			window.close();
		}
		//删除行
		function deleteRow(it){
			if(confirm("是否确认要删除？")){
				$.post("/order/order/shipper-adress-del",{paramId:it},function(data){
					if(data.state){
						location.reload()
					}else{
						alert("删除失败");
					}
				},"json");
			}
		}
	</script>
	<base target="_self">
</head>
<body>
<div style="width: 100%;; z-index: 9999; top: 0px; left: 0px;" id="ListConten1">
	<form action="/shipper.do?method=list" id="shipForm" method="post">
	<div style="margin-left: 0px; width: 100%; background-color: #FFFFFF; border: solid #000000 0px; margin: 0 auto;" align="center" id="ListConten">
		<div>
			<table width="100%" border="0" cellspacing="0" class="gridBody">
				<thead>
					<tr>
						<th width="70px">联系人姓名</th>
						<th width="130px">公司名称</th>
						<th width="*">地址</th>
						<th width="70px">城市</th>
						<th width="50px">邮编</th>
						<th style="width: 30px;">电话号码</th>
						<th width="90px;">操作</th>
					</tr>
				</thead>
				<tbody>
						<{foreach from=$rows item=c name=c}>
						<tr class="even" style="text-align: center;">
							<td title="<{$c.shipper_name}>"><{$c.shipper_name}></td>
							<td title="<{$c.shipper_company}>"><{$c.shipper_company}></td>
							<td title="<{$c.shipper_street}>"><{$c.shipper_street}></td>
							<td title="<{$c.shipper_city}>"><{$c.shipper_city}></td>
							<td title="<{$c.shipper_postcode}>"><{$c.shipper_postcode}></td>
							<td title="<{$c.shipper_telephone}>"><{$c.shipper_telephone}></td>
							<td >
								<input type="button" value="确定" class="submit" onclick="selectRow(this)"/>&nbsp;&nbsp;
								<input type="button" value="删除" class="abort" onclick="deleteRow('<{$c.shipper_account}>')"/> 
							</td>
						</tr>
					   <{/foreach}>
				</tbody>
			</table>
		</div>
	</div>
	</form>
</div>
</body>
</html>
