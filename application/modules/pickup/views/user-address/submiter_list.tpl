<script type="text/javascript">
var _url = '/order/submiter/';

/**
 * 展示发件人信息
 */
function showData(data_json){
	$(".table-module-list-data").myLoading();
	if(data_json && data_json.state == '1'){
		
		var html = '';
	    $.each(data_json.data, function (key, val) {
	        html += (val.E17 == '1')?"<tr class='table-module-b4'>":"<tr class='table-module-b1'>";
	        html += "<td>"+val.E2+"</td>";
	        html += "<td>"+val.E3+"</td>";
	        html += "<td>"+val.E4_title+"</td>";
	        html += "<td>"+val.E5 + " " + val.E6 + " " + val.E7 +"</td>";
	        html += "<td>"+val.E8+"</td>";
	        html += "<td>"+val.E10+"</td>";
	        html += "<td>";
	        //html += "<a href=\"javascript:editById(" + val.E0 + ")\">" + EZ.edit + "</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
	        html += "<a href='javascript:;' class='editShipperAccountBtn' shipper_account='"+val.E0+"'>" + EZ.edit + "</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
	        html += "<a href=\"javascript:deleteById(" + val.E0 + ")\">" + EZ.del + "</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
	        html += ((val.E17 != 1)?"<a href=\"javascript:setDefault(" + val.E0 + ")\">设为默认</a>":"<span>默认</span>");
	        html += "</td>";
	        html += "</tr>";
	    });
	    $(".table-module-list-data").html(html);
	}else{
		var html = '<tr class="table-module-b1"><td colspan="7">'+data_json.message;+'</td></tr>';
		$(".table-module-list-data").html(html);
	}
}
$(function(){
	
	$('.editShipperAccountBtn').live('click',function(){
		var shipper_account= $(this).attr('shipper_account');
	    var title = '';
	    if(shipper_account){
	        title = "<{t}>修改发件人<{/t}>";
	    }else{
	        title = "<{t}>新增发件人<{/t}>";
	    }
	    var date = new Date();
		var url = "/order/submiter/edit?shipper_account="+shipper_account+"&t="+date.getMinutes();
		
	    openIframeDialogNew(url,800 , 500,title,'submiter-edit-'+shipper_account,0,0);
	})
})
/**
 * 查询发件人信息
 */
function getData(){
	loadStart();
	$.ajax({
		   type: "POST",
		   url: _url + "list",
		   data: {},
		   async: true,
		   dataType: "json",
		   success: function(json){
			   loadEnd('');
			   showData(json);
		   }
		});
}

/**
 * 设置默认发件人
 */
function setDefault(paramid){
	loadStart();
	var params = {};
	params['paramid'] = paramid;
	$.ajax({
		   type: "POST",
		   url: _url + "set-default",
		   data: params,
		   async: true,
		   dataType: "json",
		   success: function(json){
			   	loadEnd();
				if(json.state == '1'){
					getData();
				}
		   }
	});
}

$(function(){
	//默认查询发件人信息
	getData();

});
</script>
<div id="module-container">
	
	<div id="module-table">
		<!-- 
		<div class="Tab">
			<ul>
				<li id="normal" style="position: relative;" class="mainStatus chooseTag">
					<a href="javascript:;" class="statusTag " onclick="leftMenu('submiter_list','发件人资料','/order/submiter/list')">
						<span class="order_title">发件人资料</span>
					</a>
				</li>
				<li id="abnormal" style="position: relative;" class="mainStatus ">
					<a href="javascript:;" class="statusTag " onclick="leftMenu('user_set','修改密码','/auth/user/user-set')">
						<span class="order_title">修改密码</span>
					</a>
				</li>
			</ul>
		</div>
		 -->
		<div style='padding: 5px;'></div>
		<h2 style='margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC; line-height: 40px;'>
			<{t}>现有发件人信息<{/t}>&nbsp;&nbsp;[
			<a href="javascript:getData();">刷新</a>
			]
		</h2>
		
		<a shipper_account="" class="editShipperAccountBtn baseBtn" href="javascript:;" style='float:right;margin-top:-45px;'><{t}>新增发件人<{/t}></a>
		<table cellspacing="0" cellpadding="0" width="100%" border="0" class="table-module">
			<tbody>
				<tr class="table-module-title">
					<td width="150">发件人</td>
					<td width="150">公司名</td>
					<td width="150">所在国家</td>
					<td width="120">发件地址</td>
					<td width="80">邮编</td>
					<td width="80">联系电话</td>
					<td width="100"><{t}>operate<{/t}></td>
				</tr>
			</tbody>
			<tbody id="table-module-list-data" class="table-module-list-data">
				<tr class="table-module-b1">
					<td colspan="7">No Data</td>
				</tr>
			</tbody>
		</table>
		<div style='padding: 10px;'></div>
	</div>
</div>