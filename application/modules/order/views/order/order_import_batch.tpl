<link type="text/css" rel="stylesheet" href="/css/public/ajaxfileupload.css" />
<style>
<!--
.fill_in {
	display: none;
}
.table-module tr{cursor:pointer;}
.table-module .selected td{background:none repeat scroll 0 0 #cccccc;}
.data_wrap,.data_wrap_title {
	width: 10000px;
}
.data_wrap{
	display: none;	
}

.chooseTag {
	background: #2283c5;
	font-weight: bold;
}

.Tab ul li.mainStatus {
	margin: 0 0px 0 0;
	padding: 0 30px;
}

.mainStatus {
	cursor: pointer;
}
.data_wrap input{width:80px;}
#file_name{padding:0 8px;display:none;}
.dialog_div{display:none;}
.module-title{text-align:right;}
-->
</style>
<script type="text/javascript">
<!--
function detail(id) {
	$('#detail-table-module-list-data').html("");
	$.ajax({
		  type : "POST",
		  url : "/order/order/get-import-batch-detail",
		  data : {'id':id},
		  dataType : 'json',
		  success : function(json) {
			  if(!isJson(json)) {
				  alertTip("Internal error");
			  }
			  if(json.data  && json.data.length > 0) {
				  var html = '';
				  $.each(json.data,function(k,v){
			          html += (k + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
					  html+='<td class="ec-center">'+v.line_row+'</td>';
					  html+='<td>'+v.shipper_hawbcode+'</td>';
					  html+='<td>'+v.note+'</td>';
					  html+='</tr>';
				  });
			  } else {
				  html+="<tr><td colspan='3'>无明细数据</td></tr>";
			  }
			  
			  $('#detail-table-module-list-data').html(html);
		  }
	});
}

function getBatch() {
	$('#table-module-list-data').html("");
	$('#detail-table-module-list-data').html("");
	
	$.ajax({
		  type : "POST",
		  url : "/order/order/get-import-batch",
		  dataType : 'json',
		  success : function(json) {
			  if(!isJson(json)) {
				  alertTip("Internal error");
			  }
			  if(json.data){
				  var html = '';
				  $.each(json.data,function(k,v){
			          html += (k + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
					  html+='<td class="ec-center">'+(k+1)+'</td>';
					  html+='<td class="ec-center">'+v.filename+'</td>';
					  html+='<td class="ec-center">'+v.success_count+'</td>';
					  html+='<td class="ec-center">'+v.fail_count+'</td>';
					  html+='<td class="ec-center">'+v.createdate+'</td>';
					  html+='<td class="ec-center">'+v.modifydate+'</td>';
					  html+='<td class="ec-center"><a href="javascript:void(0)" onclick="detail(' + v.ccib_id + ')"><{t}>detail<{/t}></a></td>';
					  html+='</tr>';
				  });
			  } else {
				  html+="<tr><td colspan='3'>无批准数据</td></tr>";
			  }
			  
			  $('#table-module-list-data').html(html);
		  }
	});
}
//-->
</script>
<div id="module-container">
	<div class="Tab">
		<ul>
			<li id="normal" style="position: relative;" class="mainStatus0 ">
				<a href="javascript:;" class="statusTag " onclick="leftMenu('order_create','<{t}>单票录入<{/t}>','/order/order/create?quick=52')">
					<span class="order_title"><{t}>单票录入<{/t}></span>
				</a>
			</li>
			<li id="abnormal" style="position: relative;" class="mainStatus0">
				<a href="javascript:;" class="statusTag " onclick="leftMenu('order_import','<{t}>批量上传<{/t}>','/order/order/import?quick=24')">
					<span class="order_title"><{t}>批量上传<{/t}></span>
				</a>
			</li>
			<li id="abnormal" style="position: relative;" class="mainStatus0 chooseTag">
				<a href="javascript:;" class="statusTag " onclick="leftMenu('order_import_batch','<{t}>上传记录<{/t}>','/order/order/get-import-batch?quick=0')">
					<span class="order_title"><{t}>上传记录<{/t}></span>
				</a>
			</li>
		</ul>
	</div>
	
	<h2 style='margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC;'>
		1、<{t}>近10次上传记录<{/t}>
		<a href='javascript:;' style='font-weight: normal; font-size: 12px; padding: 0 10px;' onclick='getBatch();'><{t}>刷新<{/t}></a>
	</h2>
	<div id="module-table" style='padding: 10px 20px;'>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
				<tbody id='table-module-title'>
					<tr class="table-module-b1">
    					<td class="ec-center">序号</td>
    					<td class="ec-center"><{t}>文件名<{/t}></td>
    					<td class="ec-center"><{t}>成功数<{/t}></td>
    					<td class="ec-center"><{t}>失败数<{/t}></td>
    					<td class="ec-center"><{t}>创建时间<{/t}></td>
    					<td class="ec-center"><{t}>完成时间<{/t}></td>    					
    					<td class="ec-center"><{t}>operation<{/t}></td>
    				</tr>
				</tbody>
				<tbody id="table-module-list-data">
					<{foreach from=$batch key=k item=o name=o}>
						<tr class="<{if $k%2 == 1}>table-module-b2<{else}>table-module-b1<{/if}>">
	    					<td class="ec-center"><{$k+1}></td>
	    					<td class="ec-center"><{$o.filename}></td>
	    					<td class="ec-center"><{$o.success_count}></td>
	    					<td class="ec-center"><{$o.fail_count}></td>
	    					<td class="ec-center"><{$o.createdate}></td>
	    					<td class="ec-center"><{$o.modifydate}></td>
	    					<td class="ec-center"><a href="javascript:void(0)" onclick="detail(<{$o.ccib_id}>)"><{t}>detail<{/t}></a></td>
	    				</tr>
    				<{/foreach}>
				</tbody>
			</table>
	</div>
	
	<h2 style='margin-bottom: 10px; border-bottom: 1px dashed #CCCCCC;'>
		2、<{t}>明细<{/t}>
	</h2>
	<div id="module-table" style='padding: 10px 20px;'>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
			<tbody id='table-module-title'>
				<tr class="table-module-b1">
    				<td class="ec-center" width="5%">行号</td>
    				<td class="ec-center" width="20%"><{t}>单号<{/t}></td>
    				<td class="ec-center" width="75%"><{t}>失败原因<{/t}></td>
    			</tr>
			</tbody>
			<tbody id="detail-table-module-list-data">
			</tbody>
		</table>
	</div>
</div>