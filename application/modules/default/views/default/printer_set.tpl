<script language="javascript" src="/lodop/LodopFuncs.js?v=20140314"></script>
<script type="text/javascript" src="/lodop/lodop_print.js?v=20140314"></script>
<style type="text/css">
table {
	border-collapse: collapse;
	border-spacing: 0;
}

.data-table {
	width: 100%;
	font-size: 13px;
}

.data-table th {
	text-align: right;
	font-weight: bold;
	padding: 8px 5px;
	border: 1px solid #D8E0E4;
	/*background: none repeat scroll 0 0 #f4faff;*/
	background: none repeat scroll 0 0 #e1effb;
}

.data-table td {
	padding: 8px 3px;
	border: 1px solid #D8E0E4;
}

.data-table td.left {
	width: 20%;
	font-weight: bold;
	text-align: right;
	border: 1px solid #D8E0E4;
}

.data-table td.right {
	width: 30%;
	text-align: left;
	border: 1px solid #D8E0E4;
}

#message_tip {
	color: red;
	background: #FFEBD1;
	text-align: center;
	border: 1px solid #D8E0E4;
	text-align: left;
	padding: 5px;
	font-weight: bold;
}

#message_tip DIV {
	line-height: 20px;
	height: 20px;
}

.form-input {
	width: 160px;
	height: 28px;
	outline: 0;
	border: 1px solid #D8E0E4;
	padding: 0 5px;
}

#customer_currency,#customer_balance {
	font-weight: bold;
}

#customer_code {
	font-size: 14px;
}

.table_module TD {
	padding: 8px 3px;
	border: 1px solid #D8E0E4;
}

.module-title {
	font-weight: bold;
	padding-right: 5px;
	text-align: right;
}

.item_pint_set {
	width: 225px;
}

.save_Btn {
	background: none repeat scroll 0 0 #0097E3;
	border: medium none;
	border-radius: 5px;
	color: #FFFFFF;
	cursor: pointer;
	font-size: 14px;
	height: 30px;
	line-height: 30px;
	padding: 0 12px;
	text-align: center;
}
</style>
<script>

function setPrinter(){	 
	/*
	 * 遍历打印机
	 */
	var print_count = LODOP.GET_PRINTER_COUNT();
	var printers = {};
	for (var i = 0; i < print_count; i++) {
	    printers[i] = LODOP.GET_PRINTER_NAME(i);
	}
	
	//设置统一打印选项中的打印机项
	var unifiedSetPrint = new Array('#unifiedPaperSetPrint','#unifiedSmSetPrint','.item_pint_set');
	for ( var i = 0; i < unifiedSetPrint.length; i++) {
		var usp = unifiedSetPrint[i];
		var html = "";
		for (var b in printers) {
            html = "<option value='" + b + "'>" + printers[b]
                 + "</option>";
	        $(html).appendTo(usp);
        }
	}

	//选择纸张打印设置
	var paperJson = <{$paperJson}>;
	for (var p1 in paperJson) {
		var p1_val = $.cookie(p1);
		if(p1_val != '' && p1_val != null){
			$("#paper_print_set_" + p1).val(p1_val);
		}
	}
}

function myInitPrinter(){
	var bool = printerSetup();
	
	if(bool){
		setPrinter();
	}
}
$(function(){
	myInitPrinter();
	/*
	 * 统一设置纸张打印
	 */
	$("#unifiedPaperSetPrint").live('change',function(){
		var val = $(this).val();
		$(".paper_print_set",".paper_printer_list").val(val);
	});

	/*
	 * 统一设置标签打印
	 */
	$("#unifiedSmSetPrint").live('change',function(){
		var val = $(this).val();
		$(".sm_print_set",".sm_printer_list").val(val);
	});

	$(".sm_type").live('change',function(){
		$(".unifiedSmSetPrint_tr").hide();
		var sm_type = $(this).val();
		seachSmType(sm_type);

		var sm_code = $.trim($("#sm_code").val());
		$("#sm_code").val(sm_code);
		seachSmCode(sm_code);
		
	});

	$("#sm_code").keyup(function (e) {
		var sm_type = $("input[name='type']:checked").val();
		seachSmType(sm_type);
		
        var key = e.which;
		var val = $.trim($(this).val());
		$(this).val(val);
		seachSmCode(val);
        
    });
});

function seachSmType(type_code){
	if(type_code != ''){
		$(".sm_type_" + type_code).show();
	}else{
		$(".unifiedSmSetPrint_tr").show();
	}
	
	updateSmTrColor();
}

function seachSmCode(val){
	if(val == ''){
		return;
	}

	val = val.toUpperCase();
	if($(".unifiedSmSetPrint_tr:visible").size() > 0){
		$(".unifiedSmSetPrint_tr:visible").each(function(k){
			var key = $(this).attr('data_val');

			var reg = new RegExp(""+val+"");  
			var str = key;//要匹配的字符串
			var bol = reg.test(str);
			
			if(!bol){
				$(this).hide();
			}
		});
	}
	
	updateSmTrColor();
}

/*
 * 更新地址标签TR的底色
 */
function updateSmTrColor(){
	if($(".unifiedSmSetPrint_tr:visible").size() > 0){
		$(".unifiedSmSetPrint_tr:visible").each(function(k){
			var s = 'table-module-b2';
			if((k +1) % 2 == 1){
				s = 'table-module-b1';
			}
			$(this).removeClass('table-module-b1');
			$(this).removeClass('table-module-b2');
			$(this).addClass(s);
		});
	}
}

function savePrinterForProfessional(){
	if ($(".item_pint_set").size() > 0) {
		$(".item_pint_set").each(function () {
			var paper = $(this).attr("data_key");
			var val = $(this).val();
			$.cookie(paper, val, {expires: 365, path: '/'});
		});
	}
	$.cookie("wmsPrinterOk", "1", {expires: 365, path: '/'});// 打印机设置成功
	alertTip("<span class='tip-success-message'>设置成功!</span>");
}
</script>
<!-- 打印机初始化 -->
<input type="hidden" id="lodop_init" value="0">
<div id="content" style='padding:10px 20px;'>
	<div style="">
		<div style="padding-left: 5px; padding-top: 5px; width: 360px; float: left;">
			<h2 style="color: #E06B26;">纸张：</h2>
			<table border="0" cellspacing="0" cellpadding="0" class="table_module" style="width: 100%;">
				<tbody>
					<tr style="height: 36px;">
						<td class="module-title" style="font-weight: bold; padding-right: 5px; text-align: center;" colspan="2">"纸张"打印统一指定为:</td>
					</tr>
					<tr>
						<td colspan="2" style="text-align: center;">
							<select id="unifiedPaperSetPrint" class="print_set">
							</select>
						</td>
					</tr>
				</tbody>
			</table>
			<table border="0" cellspacing="0" cellpadding="0" class="table_module paper_printer_list" style="width: 100%; margin-top: 5px;">
				<tbody>
					<{assign var='loop1' value=0}> <{foreach from=$paper key=key item=item}> <{$loop1=$loop1+1}>
					<tr class="<{if ($loop1 +1) % 2 == 1 }>table-module-b2<{else}>table-module-b1<{/if}>">
						<td class="module-title" id="<{$key}>">
							<{$item}><br /> [
							<span style="color: #1B9301;"><{$key}></span>
							]
						</td>
						<td colspan="2" style="text-align: center;">
							<select class="item_pint_set paper_print_set" id="paper_print_set_<{$key}>" data_key="<{$key}>">
							</select>
						</td>
					</tr>
					<{/foreach}>
				</tbody>
			</table>
		</div>
		<div style="clear: both;"></div>
	</div>
	<input type="button" class="save_Btn" style="margin-top: 10px;" onclick="savePrinterForProfessional();" value="保存设置">
</div>
<object id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0>
	<embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0 pluginspage="/lodop/install_lodop32.exe"></embed>
</object>