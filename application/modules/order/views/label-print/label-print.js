
/**
 * 打印前，需要预先判断
 * 如LODOP是否初始化，是否设置了默认打印机，是否修改打印机
 * 
 */

var lodopCp = '北京中电亿商网络技术有限责任公司';
var lodopKey='653726081798577778794959892839';
var lodopToken = '';
function verify(){
	if(!LODOP){
        LODOP=getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM'));
    }
    return true;
}
function log(msg){
	return; 
}
function initContent(){
	//需要判断是否分页
	var w=<{$w}>;
	var h=<{$h}>;
	var left = right = 0;
	var top = 1; 
 
    var title = '<{$title}>';
    LODOP.PRINT_INITA("0mm","0mm",w+"mm",h+"mm",title);
    LODOP.SET_PRINT_PAGESIZE(0,w+'mm',h+'mm',title);
    LODOP.SET_PRINTER_INDEX(<{$printerNo}>);
    <{foreach from=$lableArr item=img name=img}>
    LODOP.ADD_PRINT_IMAGE(0, 0, w+'mm', h+'mm', "<img border='0' src='data:image/gif;base64,<{$img.LableFile}>'/>");
	LODOP.SET_PRINT_STYLEA(0,"Stretch",2);//按原图比例(不变形)缩放模式
	LODOP. NEWPAGE ();
    <{/foreach}>
		
	LODOP.SET_PRINT_MODE("CUSTOM_TASK_NAME",title);//为每个打印单独设置任务名	
    LODOP.SET_LICENSES(lodopCp, lodopKey, lodopToken, "");
	LODOP.PRINT(); 
}

/**
 * 内容打印
 */
function lodop_print(){	
	<{if $return['ask']}>
	var bool = verify();
	if(!bool){
		return;
	}    
	initContent();
	<{else}>	
	alertTip('<{$return.message}>');
	<{/if}>
}
