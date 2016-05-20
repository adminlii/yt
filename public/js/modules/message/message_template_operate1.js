/**
 * @author Frank
 * @title 消息模板操作符（插入变量）应用方式
 * @date 2013-9-7 15:55:02 
 */
$(function(){
	MTO.getAllMsgTemplateOperate();
});
/**
 * 操作入口
 */
var MTO = {
	dataExists:'N',						//是否取得操作符数据
	liftBraces:"{{",					//包裹操作符的特殊符号--左边
	rightBraces:"}}",					//包裹操作符的特殊符号--右边
    listOperateData:'',					//操作符数据
    listOperateCorrespondingVal:'',		//操作符对应的值
    checkData:function(errorTips){		//验证是否取得数据，能否调用
    	if(this.dataExists == 'N'){
    		alertTip(errorTips);
    		return false;
    	}
    	return true;
    },
    /**
     * 替换消息模板中的操作符
     * @param content
     * @param ebayMsgId
     */
    replaceMessageOperate:function(content,ebayMsgId){
    	//查找消息模板中的操作符，未找到，直接返回内容
    	var operateArr = this.getOperate(content);
    	var response = {"ask":"0","message":"","errorMsg":""};
    	response.message = content;
    	if(operateArr.length == 0){
    		response.ask = 1;
    		return response;
    	}
    	
    	//更加ebay消息ID，已经找到的操作符，请求后台，得到操作符对应的值
    	var params = {};
    	params['paramsOperate'] = operateArr;
    	params['ebayMsgId'] = ebayMsgId;
    	var MTO_ELEMENT = this;
    	$.ajax({
  	        type: "post",
  	        dataType: "json",
  	        data:params,
  	        async:false,
  	        url: '/message/template/get-Template-Operate-Corresponding-Val',
  	        success: function (json) {
  	        	if(json){
  	        		var errorMsg = new Array();
  	        		var errorIndex = 0;
	  	        	for ( var i = 0; i < json.length; i++) {
	  	        		var array_element = json[i];
	  	        		if(array_element.ask){
	  	        			while(true){
	  	        				var braces = MTO_ELEMENT.liftBraces + array_element.code + MTO_ELEMENT.rightBraces;
	  	        				var int = content.indexOf(braces);
	  	        				if(int != -1){
	  	        					content = content.replace(braces , array_element.message);
	  	        				}else{
	  	        					break;
	  	        				}
	  	        			}
	  	        		}else{
	  	        			var tmpTips = MTO_ELEMENT.liftBraces + array_element.code + MTO_ELEMENT.rightBraces;
	  	        			tmpTips += "&nbsp;&nbsp;" + array_element.message;
	  	        			errorMsg[errorIndex] = tmpTips;
	  	        			errorIndex++;
	  	        		}
	  	        	}
	  	        	response.message = content;
	  	        	if(errorIndex != 0){
	  	        		response.errorMsg = errorMsg;
	  	        	}else{
	  	        		response.ask = 1;
	  	        	}
  	        	}else{
  	        		response.errorMsg = ['替换操作符失败'];
  	        	}
  	        }
     	 });
    	
    	return response;
    },
    /**
     * 验证消息模板是否可以提交
     * @param content
     */
    checkMessageOperate:function(content){
    	if(!content){
    		alertTip("消息不能为空");
    		return false;
    	}
    	//检查是否取得操作符数据
    	if(!this.checkData("模板验证失败，原因：未取得操作符数据.")){
    		return false;
    	}
    	//验证是否存在残缺操作符
    	if(!this.checkIncompleteCurlyBraces(content)){
    		return false;
    	}
    	
    	//验证内容中的操作符是否匹配DB数据
    	if(!this.checkOperateExists(content)){
    		return false;
    	}
    	
    	return true;
    },
    /**
     * 验证残缺括弧的操作符
     * @param content
     * @returns {Boolean}
     */
    checkIncompleteCurlyBraces:function(content){
    	 //此处前后加空格，是为了便于正则表达式的匹配
    	 var str = " " + content + " ";
    	 var regIncompleteLift = /[^\{]\{([^\{^\}]+?)([^\}]\}\}[^\}])/g;	//验证左括弧残缺 {XX}}
    	 var arrLift = str.match(regIncompleteLift);
    	 var tips = "";
    	 var bolExists = false;
    	 if(arrLift){
    		 bolExists = true;
			 for (int = 0;int<arrLift.length ; int++){
				 tips += arrLift[int] + "&nbsp;";
			 }
    	 }
    	 
    	 var regIncompleteRight = /[^\{]\{{2}([^\{^\}]+?)([^\}][\}][^\}])/g;		//验证右括弧残缺 {{XX}
    	 var arrRigth = str.match(regIncompleteRight);
    	 if(arrRigth){
    		 bolExists = true;
    		 for (int = 0;int<arrRigth.length ; int++){
    			 tips += arrRigth[int] + "&nbsp;";
    		 }    		 
    	 }
    	 if(bolExists){
    		 tips += "<br/>系统检测存在以上不符合规范的操作符，请检查！";
    		 alertTip(tips);
    		 return false;
    	 }
    	 return true;
    },
    /**
     * 验证操作符是否匹配DB数据
     * @param content
     * @returns {Boolean}
     */
    checkOperateExists:function(content){
    	//提出操作符
    	var arrOperate = this.getOperate(content);
    	//没有检测到操作符，跳出验证
    	if(arrOperate.length == 0){
    		return true;
    	}
    	
    	//开始循环，匹配数据
    	var tips = "";
    	for ( var i = 0; i < arrOperate.length; i++) {
			var operateStr = arrOperate[i];
			var existsBol = false;
			for ( var k = 0; k < this.listOperateData.length; k++) {
				var data = this.listOperateData[k];
				if(data.operate_code == operateStr){
					existsBol = true;
					break;
				}
			}
			if(!existsBol){
				tips += this.liftBraces + operateStr + this.rightBraces + "&nbsp;";
			}
		}
    	
    	if(tips){
    		tips += "<br/>系统检测发现，存在以上匹配失败的操作符，请检查";
    		alertTip(tips);
    		return false;
    	}
    	return true;
    },
    /**
     * 获得模板中操作符数据
     * @param content
     * @returns
     */
    getOperate:function(content){
    	//此处前后加空格，是为了便于正则表达式的匹配
    	var str = " " + content + " ";
    	var reg = /[^\{]\{{2}([^\{^\}]+?)([^\}]\}\}[^\}])/g;
    	var arr = str.match(reg);
    	var returnArr = new Array();
    	if(arr){
	   		for (int = 0;int<arr.length ; int++){
	   			//前后存在正则表达式解除了的字符，需要截掉
	   			var tmp = arr[int];
	   			tmp = tmp.substring(3,tmp.length - 3);
	   			returnArr[int] = tmp;
	   		}
    	}
    	return returnArr;
    },
    /**
     * 获取所有的操作符数据
     */
    getAllMsgTemplateOperate:function(){
    	 $.ajax({
 	        type: "post",
 	        dataType: "json",
 	        url: '/message/template/get-All-Msg-Template-Operate',
 	        success: function (json) {
 	            if(json.ask){
 	               //alert(json.data[0].operate_code);
 	               MTO.dataExists = 'Y';
 	               MTO.listOperateData = json.data;
 	            }
 	        }
    	 });
    },
    callback: function () {				//回调函数
    	alert(this.dataSource);
        return true;
    }
};