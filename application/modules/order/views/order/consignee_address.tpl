
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>收件人地址簿</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="cache-control" content="no-cache">
	<meta http-equiv="expires" content="0">
	<link href="/css/address/EShip.css" rel="stylesheet" type="text/css" />
	<link href="/css/address/simpletable.css" type="text/css" rel="stylesheet">
	<script src="/js/jquery-1.11.3.min.js"></script>
	<script type="text/javascript">
		//选择行
		function selectRow(id){
			//根据按钮爬到行TR,然后行分析所有列
			//var count = $(id).parent().parent().children().size();
			var tdArr = $(id).parent().parent().children();
			var params = {};
			params.consignee_company=tdArr.eq(1).html();
			params.consignee_street=tdArr.eq(2).html();
			params.consignee_province=tdArr.eq(4).html();
			params.consignee_city=tdArr.eq(5).html();
			params.consignee_postcode=tdArr.eq(6).html();
			params.consignee_name=tdArr.eq(7).html();
			params.consignee_telephone=tdArr.eq(8).html();
			params.consignee_countrycode=tdArr.eq(9).html();
			window.returnValue =params;
			window.close();
		}
		//删除行
		function deleteRow(it){
			if(confirm("是否确认要删除？")){
				$.post("/order/order/consignee-adress-del",{paramId:[it]},function(data){
					if(data.state){
						location.reload()
					}else{
						alert("删除失败");
					}
				},"json");
			}
		}
		//上传文件
		function upload(){
			var f = $("#connectFile").val();
			if(f == ''){
				alert('请上传Connect地址簿文件！');
				return ;
			}
			$("#ConnectForm").submit();
		}
		//下载模版
		function down(ftype){
			window.location.href = "/file/";
		}
		
		function alldel(){
			if($("input[name='paramId[]']:checked").length<=0){
				alert("没有数据提交");
				return false;
			}
			 if(confirm("是否确认要删除？")){
				$.post("/order/order/consignee-adress-del",$("#connsigneeForm").serialize(),function(data){
					if(data.state){
						location.reload()
					}else{
						alert("删除失败");
					}
				},"json");
			}
		}
		
		$(function() {
	           $("#selectAll").click(function() {
	                $('input[name="paramId[]"]').prop("checked",this.checked);
	            });
	            var subBox = $("input[name='paramId[]']");
	            subBox.click(function(){
	                $("#selectAll").prop("checked",subBox.length == $("input[name='paramId[]']:checked").length ? true : false);
	            });
	        });
	    
	</script>
	<base target="_self">
</head>
<body>
<div style="padding-top: 2px; color: #333; padding-left:5px; text-align: left;height: 25px;line-height: 25px;border: #cccccc 1px solid;">
	<form action="/order/order/consignee-adress-upload" method="post" enctype="multipart/form-data" id="ConnectForm">
	<div style="display: inline;float: left;padding-left:5px;">
		<label>导入Connect地址簿</label>
		<input type="file" name="connectFile" id="connectFile" style="width: 200px;vertical-align: middle;" class="customerSearch"/> 
		<!-- <input type="button" name="connectFile" id="connectFile"  onclick="tishi()" style="width: 50px;vertical-align: middle;" value="浏览.." class="customerSearch"/><font size="1px">未选择文件。</font>-->
		<label style="font-size: 10px;">文件类型(<font style="font-size: 16px;"><a href="/file/收件人模板.xls" style="color: blue;" title="点击下载(xls)格式模版">xls</a></font>)模版</label>
		<input type="button" class="button" value="保存上传" onclick="upload()" style="margin-left: 5px;"/>
	</div>
	</form>
</div>

<div style="padding-top: 2px; color: #333; padding-left:5px; text-align: left;height: 25px;line-height: 25px;border: #cccccc 1px solid;">
	<form method="post" enctype="multipart/form-data" id="queryForm">
	<div style="display: none;float: left;padding-left:5px;">
		<table width="98%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<label>条件检索：&nbsp;&nbsp;&nbsp;
				公司名称				
					<input style="WIDTH: 100px;height: 18px" id="company_Name" type="text" name="company_Name" value="" />
				</label>
				<label>&nbsp;&nbsp;联系人
					<input style="WIDTH: 100px;height: 18px" id="person_Name" type="text" value="" name="person_Name" />
				</label>
				<label>
					<input class="submit" title="查询" value="查询" type="button" onclick="search_consignee()" />
					<input class="abort" title="清除" value="清除" type="button" onclick="clean_input()" style="margin-left: 5px;"/>
				</label>
			</tr>
		</table>
	</div>
	</form>
</div>

<div style="width: 100%;; top: 0px; left: 0px;" id="ListConten1">
	<div style="margin-left: 0px; width: 100%; background-color: #FFFFFF; border: solid #000000 0px; margin: 0 auto;" align="center" id="ListConten">
		<form action="/connsignee.do?method=allDel" method="post" id="connsigneeForm">
		<div style="float: left">
			<div>
			<span style="float: left">
			<input type="button" style="margin-left: " class="button" name="delmore" onclick="alldel()" value="批量删除"/>
			</span>
			</div>
			<table width="100%" border="0" cellspacing="0" class="gridBody">
				<thead>
					<tr>
					    <th width="50px;">
					    <div style=" width:50px; text-align:center; vertical-align:middle">
					    <input name="selectAll" id="selectAll" type="checkbox" style=" vertical-align:middle" />全选</div>
					    </th>
						<th id="sortByCompany" onclick="" width="110px;">公司名称</th>
						<th width="100px;">地址</th>
						<th width="110px;">国家</th>
						<th width="60px;">州/省</th>
						<th width="70px;">城市</th>
						<th width="50px;">邮编</th>
						<th id="sortByPerson" onclick="" width="70px;">联系人</th>
						<th width="80px;">电话号码</th>
						<th width="60px;" style="display: none;">国家编码</th>
						<th width="90px;">操作</th>
					</tr>
				</thead>
				<tbody>
				<{foreach from=$rows item=c name=c}>
					<tr class="even" style="text-align: center;">
						    <td>
						    <input style="width: 35" type="checkbox" name="paramId[]" value="<{$c.consignee_account}>">
						    </td>
							<td title="<{$c.consignee_company}>"><{$c.consignee_company}></td>
							<td title="<{$c.consignee_street}><{if $c.consignee_street1}>||<{$c.consignee_street1}><{/if}><{if $c.consignee_street2}>||<{$c.consignee_street2}><{/if}>"><{$c.consignee_street}><{if $c.consignee_street1}>||<{$c.consignee_street1}><{/if}><{if $c.consignee_street2}>||<{$c.consignee_street2}><{/if}></td>
							
							<td title="<{$c.country_cnname}>"><{$c.country_cnname}></td>
							<td title="<{$c.consignee_province}>"><{$c.consignee_province}></td>
							<td title="<{$c.consignee_city}>"><{$c.consignee_city}></td>
							<td><{$c.consignee_postcode}></td>
							<td><{$c.consignee_name}></td>
							<td><{$c.consignee_telephone}></td>
							<td style="display: none;"><{$c.consignee_countrycode}></td>
							<td>
								<input type="button" value="确定" class="submit" onclick="selectRow(this)"/>&nbsp;&nbsp;
								<input type="button" value="删除" class="abort" onclick="deleteRow('<{$c.consignee_account}>')"/>
							</td>
						</tr>
				 <{/foreach}>		
				</tbody>
			</table>
		</div>
		</form>
	</div>
</div>
</body>
</html>
