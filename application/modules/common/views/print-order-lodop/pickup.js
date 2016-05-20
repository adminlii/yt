
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
	var title = '<{$title}>';
    LODOP.PRINT_INITA("0mm","0mm",w+"mm",h+"mm",title);
    LODOP.SET_PRINT_PAGESIZE(1,w+'mm',h+'mm',title);
    var table = '<{$html}>';
    LODOP.ADD_PRINT_HTM(top+"mm",left+"mm",(w-left-right)+"mm",(h-top)+"mm",table); 
}
function _print(){
    LODOP.PRINT();
}

function _preview(){
    LODOP.PREVIEW();
}

function _setup(){
    LODOP.PRINT_SETUP();
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
	_print();
}

/**
 * 内容预览
 */
function preview_lodop(){	
	var bool = verify();
	if(!bool){
		return;
	}    
	initContent();
	_preview();
}

/**
 * 打印维护
 */
function setup_lodop(){	
	var bool = verify();
	if(!bool){
		return;
	}    
	initContent();
	_setup();
}
