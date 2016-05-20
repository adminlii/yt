/**
 * 帮助文档的事件函数
 * @author Frank
 * @date 2013-10-26 11:19:39
 */
var _stepContent;
var _sysHelpDialog;
	$(function(){
		/**
		 *	‘问号’的点击事件
		 */
		$(".sys_help").click(function(){
			var objIframe =$("#main-right-container-iframe > .iframe-container:visible");
			if(objIframe != null && objIframe.length > 0){
				//获取权限ID参数
				var iframeId = objIframe.attr('id');
				var uId = iframeId.substring(17,iframeId.length);

				//加载对应流程状态，状态描述等等数据
				var options = {};
				var bodyWidth = parseInt($("body").css('width'));
				options['dWidth'] = parseInt(bodyWidth * 0.88);
				var guildMenu = $("#menu" + uId);
				options['dTitle'] = guildMenu.find("a").attr('title');
				options['url'] = '/default/system/get-help-data';
				options['paramId'] = uId;
				$("#sys_help_dialog").OpenSysHelpDialog(options);
			}
		});

		/**
		 *	设置帮助弹出窗
		 */
		jQuery.fn.OpenSysHelpDialog = function (options) {
		    var defaults = {
		        jsonData: {},
		        Field: 'paramId',
		        paramId: 0,
		        url: '/',
		        editUrl: '/',
		        dWidth: "550",
		        dHeight: "auto",
		        dTitle: "帮助/Help",
		        successMsg: ""
		    };
		    var options = $.extend(defaults, options);
		    options['dTitle'] += "--帮助说明";
		    
		    if(options.position != 'top'){
		    	options.position = ['center',50];
		    }
		    var div = $(this);
		    var divHtml = div.html();
		    div.html("");
		    _sysHelpDialog = $('<div title="' + options.dTitle + '" id="dialog-edit-alert-tip" class="dialog-edit-alert-tip"><form id="editDataForm" name="editDataForm" class="submitReturnFalse">' + divHtml + '</form><div class="validateTips" id="validateTips"></div></div>').dialog({
		        autoOpen: true,
		        width: options.dWidth,
		        height: options.dHeight,
		        position: options.position,
		        modal: true,
		        show: "slide",
//		        buttons: [
//		            {
//		                text: "关闭(Close)",
//		                click: function () {
//		                    div.html(divHtml);
//		                    $(this).dialog("close");
//		                }
//		            }
//		        ],
		    	 close: function () {
		            $(this).detach();
		            div.html(divHtml);
		        }
		    });

			
		    
		    if (options.url != '/' && options.paramId != '' && options.paramId != '0') {
		    	initializationSteps(options);
		    }else{
		    	setNotContent();
		    }
		};
	});
	
	//主线步骤数据
	var mainStepListJson;
	//并行步骤数据
	var parallelStepListJson;
    var initializationSteps = function (options) {
    	$(".stateFlow").html("");
    	$("#stateContent").myLoading();
        $.ajax({
            type: "post",
            async: false,
            dataType: "json",
            url: options.url,
            data: options.Field + "=" + options.paramId,
            success: function (json) {
                if (json.state) {
                	mainStepListJson = json.main;
                	parallelStepListJson = json.parallel;
                	_stepContent = json.content;
                	//当前进行到第几步
    		    	var currentStep = mainStepListJson[0].StepCode;
    		    	/*
    		    	 * 设置状态流程图插件
    		    	 */
    		    	var StepTool = new Step_Tool("stateFlow","操作流程","helpMycall");
    		    	//使用工具对页面绘制相关流程步骤图形显示
    		    	var tmp = parseInt($(".stateFlow").css('width'));
    		    	var tmp1 = tmp / mainStepListJson.length;
    		    	var tmp2 = (tmp1 / tmp) * 100;
    		    	var nodeWidth = parseInt(tmp2);
    		    	StepTool.drawStep(nodeWidth,mainStepListJson,parallelStepListJson);
    		    	StepTool.belongsStep(currentStep,true);
    		    	_StepTool = StepTool;

    		    	/*
    		    	 * 设置帮助内容
    		    	 */
    		    	$("#stateContent").closeMyLoading();
    		    	setHelpContent(currentStep);
                }else{
                	setNotContent();
	            }
            }
        });
    };
	
    /**
     * 关闭帮助对话框
     */
    function closeHelpDialog(){
    	_sysHelpDialog.dialog("close");
    }
    
	/**
	 * 没有帮助内容
	 */
	function setNotContent(){
		$("#stateContent").closeMyLoading();
		var tips = '<div class="noExist">'+
			    	'<div class="noExist_pic">'+
			        	'<img src="/images/help/base/no_exist_01.gif" style="border: 0px;" align="absmiddle">'+
			        '</div>'+
			    	'<div class="noExist_text" style="margin-top:25px;">'+
			        	'对不起，您查看的页面还没有帮助信息...<br>'+
			            '<span style="font-size:18px;" class="word_gray">Sorry, the page you are viewing has no help information ...</span>'+
			        '</div>'+
			        '<div class="clr"></div>'+
			        '</div>';
		$("#stateContent").html(tips);
	}

	/**
	 * 设置帮助内容
	 */
	function setHelpContent(currentStep){
		//获取帮助内容
		var content = '';
		for ( var int = 0; int < _stepContent.length; int++) {
			var array_element = _stepContent[int];
			if(array_element.key == currentStep){
				content = array_element.val;
				break;
			}
		}
		var bodyheight = windowHeight();//parseInt($("body").css('height'));
		var contentHeight = parseInt(bodyheight * 0.65);
		$("#stateContent").parent().css("height",contentHeight);
		$("#stateContent").css({"max-height":contentHeight,"height":contentHeight,"box-shadow":"1px 1px 3px #999999"});		
		$("#stateContent").html(content);
	}

	/**
	 * 流程插件回调函数
	 */
	var _StepTool;
	function helpMycall(restult,bol){
		//这里可以填充点击步骤的后加载相对应数据的代码
		//alert(restult.StepNum + "--" + restult.StepCode + "--" + restult.StepText + "\n" + bol);
		_StepTool.belongsStep(restult.StepCode,!bol);
		//帮助文档变化
		setHelpContent(restult.StepCode);
	}
	
	
	/*** 初次登陆系统的帮助指引--开始 ***/
		
	/**
	 * 加载初始化帮助
	 */
	function helpInitialize(){
		var url = '/default/index/get-Login-Status';
		$.ajax({
            type: "post",
            async: false,
            dataType: "json",
            url: url,
            success: function (json) {
                if (json.state) {
                	//第一次登陆，开启初始化帮助
                	setTimeout("helpInitializationExecution();",500);
                }else{
                	//不作处理
	            }
            }
        });
	}
	
	/**
	 * 获得帮助信息的请求参数
	 * @returns {___anonymous6185_6186}
	 */
	function getHelpInitOptions(){
		var options = {};
		options['url'] = '/default/system/get-help-data';
		options['Field'] = 'paramId';
		options['position'] = 'top';
		var bodyWidth = parseInt($("body").css('width'));
		options['dWidth'] = parseInt(bodyWidth * 0.95);
		options['dHeight'] = parseInt(windowHeight() * 0.95);
		options['dTitle'] = "系统初始化";
		return options;
	}
	
	/**
	 * 执行初次登陆的初始化帮助
	 */
	function helpInitializationExecution(){
		var li_tag_content = $(".usernav-list1");
		li_tag_content.children().remove();
		
		var steps = '['+
					'{"steps":[{"id":11,"title":"eBay账户授权"}]},' +
					'{"steps":[{"id":28,"title":"PayPal账户关联"}]},' +
					'{"steps":[{"id":22,"title":"用户店铺绑定"}]},' +
					'{"steps":[{"id":46,"title":"SKU建立"},{"id":32,"title":"关系映射"}]},' +
					'{"steps":[{"id":47,"title":"仓库设置"},{"id":48,"title":"运输方式"}]},' +
		        	'{"steps":[{"id":51,"title":"ASN入库"},{"id":52,"title":"收货"}]},' +
		        	'{"steps":[{"id":0,"title":"完成"}]}' +
		        	']';
		
		//<li><a href="javascript:;" data_val="11" data_title="eBay账户绑定" class="hover usernav_tag"><img width="15" height="15" src="/images/help/base/i-right.png">eBay账户绑定</a></li>
		var stepsJson = JSON.parse(steps);
		var img_tag = '<img width="15" height="15" src="/images/help/base/i-right.png">';
		for ( var int = 0; int < stepsJson.length; int++) {
			var li_tag = $("<li>").appendTo(".usernav-list1");
			var a_tag = $("<a>").appendTo(li_tag);
			if(int == 0){
				 a_tag.addClass("hover");
			}
			a_tag.addClass("usernav_tag");
			a_tag.attr("href","javascript:;");
			var a_tag_data_val = "";
			var a_tag_data_title = "";
			var a_tag_text = "";
			for ( var int2 = 0; int2 < stepsJson[int].steps.length; int2++) {
				if(int2 == 0){
					a_tag_data_val += stepsJson[int].steps[int2].id;
					a_tag_data_title += stepsJson[int].steps[int2].title;
					a_tag_text += stepsJson[int].steps[int2].title;
				}else{
					a_tag_data_val += "#" + stepsJson[int].steps[int2].id;
					a_tag_data_title += "#" + stepsJson[int].steps[int2].title;
					a_tag_text += " + " + stepsJson[int].steps[int2].title;
				}
			}
			a_tag.attr("data_val",a_tag_data_val);
			a_tag.attr("data_title",a_tag_data_title);
			a_tag.html(img_tag + a_tag_text);
		}		
		var a_tag = $(".usernav_tag").eq(0);;
		var a_tag_title = a_tag.attr('data_title');
		var a_tag_val = a_tag.attr('data_val');
		//初始化选项卡
		initChildrenOptions(a_tag_val, a_tag_title);
		
		var options = getHelpInitOptions();
		options['paramId'] = a_tag_val.split("#")[0];
		$("#sys_init_help_dialog").OpenSysHelpDialog(options);
		
		/*
		 * 左侧菜单的事件绑定
		 */
		$(".usernav_tag").live('click',function(){
			//移除样式
			$('.usernav_tag').removeClass('hover');
			
			//增加样式
			$(this).addClass('hover');
			
			//获得调试样式
			var val = $(this).attr('data_val');
			var title = $(this).attr('data_title');
			//初始化选项卡
			initChildrenOptions(val, title);
			
			var id = val.split("#")[0];
			if(id != 0){
				options['paramId'] = id;
				initializationSteps(options);
			}else{
				setOverContent();
			}
			
		}); 
	}
	
	/**
	 * 初始子集化选项卡
	 * @param val
	 * @param title
	 */
	var _init_options_bol = false;
	function initChildrenOptions(val,title){

		//移除上次遗留的选项卡
		$(".usernav_tag_children").children().remove();
		
		//拆分值
		var val_arr = val.split("#");
		var title_arr = title.split("#");
		
		//显示选项卡
		for ( var int = 0; int < val_arr.length; int++) {
			var li_tag = $("<li>").appendTo(".usernav_tag_children");
			if(int == 0){
				//更改跳转页面
				setJumpClick(val_arr[int]);
				//样式选中
				li_tag.addClass("chooseTag");
			}
			li_tag.addClass("usernav_tag_children_li");
			li_tag.attr("data_val",val_arr[int]);
			var a_tag = $("<a>").appendTo(li_tag);
			a_tag.attr("href","javascript:;");
			a_tag.html(title_arr[int]);
		}
		
		//添加事件绑定
		if(!_init_options_bol){
			_init_options_bol = true;
			$(".usernav_tag_children_li").live('click',function(){
				if($(this).hasClass("chooseTag")){
					return;
				}
				//移除样式
				$(".usernav_tag_children_li").removeClass("chooseTag");
				//添加样式
				$(this).addClass("chooseTag");
				
				var id = $(this).attr("data_val");
				var options = getHelpInitOptions();
				options['paramId'] = id;
				initializationSteps(options);

				//更改跳转页面
				setJumpClick(id)
			});
		}
		
	}
	
	function setJumpClick(id){
		var obj = $(".iconToPage");
		if(id == "0"){
			obj.hide();
		}else{
			obj.show();
		}
		var menuClass = "sub-menu-id-" + id;
		var menuEvent = parent.$("." + menuClass);
		obj.attr("onclick",menuEvent.attr("onclick"));
	}
	
	/**
	 * 设置初始化指引完成的提示
	 */
	function setOverContent(){
		$(".stateFlow").html("");
		$("#stateContent").closeMyLoading();
		var ebay_binding_id = 11;
		var menuClass = "sub-menu-id-" + ebay_binding_id;
		var menuEvent = parent.$("." + menuClass);
		var menuOnclick = menuEvent.attr("onclick");
		
		var tips = '<div class="noExist">'+
						'<div class="noExist_pic"><img align="absmiddle" style="border: 0px;" src="/images/help/base/smile.gif"></div><div class="noExist_text" style="">'+
						'<p>您已经看完所有的初始化设置，是否继续？</p>'+
						'<p><b>●</b>&nbsp;是的，我要开始<a href="javascript:;" onclick="'+menuOnclick+'" style="color:#0090E1;">绑定ebay账户</a>！</p>'+
						'<p><b>●</b>&nbsp;不了，我想<a href="javascript:;" onclick="closeHelpDialog();" style="color:#0090E1;">进入系统</a>。</p>'+
						'<p style="font-size:12px;padding-left:40px;">更多帮助信息，请查看页面右上角的问号“<b style="color: #0090E1;">？</b>”</p>'
						'</div>'+
						'<div class="clr"></div>'+
					'</div>';
		$("#stateContent").html(tips);
	}
	
	/*** 初次登陆系统的帮助指引--结束 ***/