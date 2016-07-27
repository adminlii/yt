
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>常用地址</title>
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
			params.cname=tdArr.eq(1).html();
			params.ename=tdArr.eq(2).html();
			params.dangerousgoods=tdArr.eq(3).html()=='是'?1:0;
			window.returnValue =params;
			window.close();
		}
		//删除行
		function deleteRow(it){
			if(confirm("是否确认要删除？")){
				$.post("/order/order/dhl-contents-del",{paramId:[it]},function(data){
					if(data.state){
						location.reload()
					}else{
						alert("删除失败");
					}
				},"json");
			}
		}
		
		
		function alldel(){
			if($("input[name='paramId[]']:checked").length<=0){
				alert("没有数据提交");
				return false;
			}
			 if(confirm("是否确认要删除？")){
				$.post("/order/order/dhl-contents-del",$("#connsigneeForm").serialize(),function(data){
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




<div style="width: 100%;; top: 0px; left: 0px;" id="ListConten1">
	<div style="margin-left: 0px; width: 100%; background-color: #FFFFFF; border: solid #000000 0px; margin: 0 auto;" align="center" id="ListConten">
		<form action="/connsignee.do?method=allDel" method="post" id="connsigneeForm" onsubmit="return false;">
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
						<th width="100px;">中文内容</th>
						<th width="110px;">英文内容</th>
						<th width="60px;">危险品</th>
						
						<th width="90px;">操作</th>
					</tr>
				</thead>
				<tbody>
				<{foreach from=$rows item=c name=c}>
					<tr class="even" style="text-align: center;">
						    <td>
						    <input style="width: 35" type="checkbox" name="paramId[]" value="<{$c.content_account}>">
						    </td>
							<td title="<{$c.cname}>"><{$c.cname}></td>
							<td title="<{$c.ename}>"><{$c.ename}></td>
							<td title="<{$c.dangerousgoods}>"><{if $c.dangerousgoods eq 1}>是<{else}>否<{/if}></td>
							<td>
								<input type="button" value="确定" class="submit" onclick="selectRow(this)"/>&nbsp;&nbsp;
								<input type="button" value="删除" class="abort" onclick="deleteRow('<{$c.content_account}>')"/>
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
