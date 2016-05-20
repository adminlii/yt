
/**
 * 打印前，需要预先判断
 * 如LODOP是否初始化，是否设置了默认打印机，是否修改打印机
 * 
 */
function verify(){
	if(!LODOP){
        LODOP=getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM'));
    }
	LODOP.SET_LICENSES("北京中电亿商网络技术有限责任公司", "653726081798577778794959892839", "", "");
//    var printerNo = getPrinterNo('A4');
//    if (printerNo === null) {
//        printerSetup();
//        return false;
//    }
    return true;
}

function initContent(){
	//需要判断是否分页
	var w=<{$w}>;
	var h=<{$h}>;
	var left = right = 8;
	var top = 2;
	$('#wrap').html('<p>共<{$refIds|@count}>，分<{$htmlArrChunk|@count}>批打印，开始打印</p>');
    <{foreach from=$htmlArrChunk item=htmlArr name=htmlArr key=k}>
	    var title = '<{$title}>-<{$k+1}>';
	    LODOP.PRINT_INITA("0mm","0mm",w+"mm",h+"mm",title);
	    LODOP.SET_PRINT_PAGESIZE(0,w+'mm',h+'mm',title); 
	    		
	    <{foreach from=$htmlArr item=html name=html}>
	    LODOP.NewPage();
	    LODOP.ADD_PRINT_HTM(top+"mm",left+"mm",(w-left-right)+"mm",(h-top)+"mm",'<{$html}>'); 
	    <{/foreach}>
			
		LODOP.SET_PRINT_MODE("CUSTOM_TASK_NAME",title);//为每个打印单独设置任务名	
		LODOP.PRINT();
		$('#wrap').append('<p>第<{$k+1}>批打印完成</p>');
	<{/foreach}>
	$('#wrap').append('<p>打印完成</p>');
}

/**
 * 内容打印
 */
function print_lodop(){	
	var bool = verify();
	if(!bool){
		return;
	}    
	initContent();
}
