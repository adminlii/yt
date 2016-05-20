/**
 * 首页看板统计、公告栏(版本信息)
 * @author Frank
 * @date 2013-9-13 15:19:263
 */

/**
 * 无面板数据，设置一个提示
 */
function notPanel(tip){  
	tip = tip?tip:"无面板数据";
	
	var divContainer = $("<div>").appendTo(_panelContainer);
	divContainer.addClass("panel_div");
	divContainer.css({"text-align":"center","border":"1px solid #D9D9D9","height":"100px","line-height":"98px","clear":"both"});
	var tipTag = $("<h2>").appendTo(divContainer);
	tipTag.html(tip);
	
}

/**
 * 获得版本信息
 * @param container		放置公告的容器
 * @param code			应用代码
 * @param request        判断是否为按钮请求0初次自动请求，1按钮请求
 */
function loadVersions(container,code,request) {
	
	//构建公告栏
/*	var taskTag = $("<div>").appendTo(container);
	taskTag.addClass("admin_task");
	
	var titleTag = $("<div>").appendTo(taskTag);
	titleTag.addClass("table-module-title");
	titleTag.css("text-align","center");
	var titleTextTag = $("<h2>").appendTo(titleTag);
	titleTextTag.html($.getMessage('home_bulletin_board'));*///"公告栏"
	
	var ulTag = $("<ul>").appendTo(".admin_task");
	ulTag.addClass("versions-list-data-li");
	
	//设置公告栏样式
	//setAnnouncementStyle();
	
	//请求地址,及参数
    var url="/default/system/get-bulletin-board";
    var data={code:code,pageSize:"8"};
	//数据展示区域
    var element = ulTag;
    element.myLoading();
    
    $.getJSON(url,data,function(json){
    	element.closeMyLoading();
        if (isJson(json) && json.state == '1' && json.total != 0) {
        	//初次自动请求时给滚动公告赋值
        	if(request=='0'){
        		if(json.data.length>=3){
        		for(var i=0;i<3;i++){
        			var li=$("<li>").appendTo('#scrollDiv > ul');
        			li.addClass("showLine");
        			var div1=$("<div>").appendTo(li);
        			div1.addClass("admin_task_title");
        			var a=$("<a>").appendTo(div1);
        			a.attr("href","javascript:;");
        			a.html(json.data[i].v_title);
        			var p=$("<p>").appendTo(div1);
        			p.hide();
        			p.html(json.data[i].v_content);
        			var div2=$("<div>").appendTo(li);
        			div2.addClass("admin_task_time");
        			div2.hide();
        			div2.html(json.data[i].v_published);			
        		    }
        		} else{
        			for(var i=0;i<json.data.length;i++){
            			var li=$("<li>").appendTo('#scrollDiv > ul');
            			li.addClass("showLine");
            			var div1=$("<div>").appendTo(li);
            			div1.addClass("admin_task_title");
            			var a=$("<a>").appendTo(div1);
            			a.attr("href","javascript:;");
            			a.html(json.data[i].v_title);
            			var p=$("<p>").appendTo(div1);
            			p.hide();
            			p.html(json.data[i].v_content);
            			var div2=$("<div>").appendTo(li);
            			div2.addClass("admin_task_time");
            			div2.hide();
            			div2.html(json.data[i].v_published);			
            		    }
        		}
        	} 
            //定义最大显示条数，追加公告 	
            var showMaxNum = 6;
            $.each(json.data, function (k, v) {
                var container = $("<li>").appendTo(element);   
                var liClassName = (k >= showMaxNum)?"noneLine":"showLine";
                container.addClass(liClassName);
                
                var titleTag = $("<div>").appendTo(container);
                titleTag.addClass("admin_task_title");
                var titleText = $("<a>").appendTo(titleTag);
                titleText.attr("href","javascript:;");
                titleText.html(v.v_title);
 
                var contentText = $("<p>").appendTo(titleTag);
                contentText.hide();
                contentText.html(v.v_content);

                var timeText = $("<div>").appendTo(container);
                timeText.addClass("admin_task_time");
                timeText.html(v.v_published);

            });
			//是否显示更多
            if(element.find(".noneLine").length > 0){
				var moreBt = $("<div>").appendTo(element);
				moreBt.addClass("admin_task_more");
				var moreBtText = $("<a>").appendTo(moreBt);
				moreBtText.attr("href","javascript:;");
				moreBtText.html($.getMessage('sys_more'));//"更多"
				//邦定事件
				$(".admin_task_more > a").live('click',function(){
			        var aText = element.find(".admin_task_more > a");
			    	var hideLine = element.find(".noneLine:hidden");
					if(hideLine.length > 0){
						hideLine.show();
						$(this).html($.getMessage('sys_hide'));//"隐藏"
					}else{
						$(".versions-list-data-li").find(".noneLine:visible").hide();
						$(this).html($.getMessage('sys_more'));//"更多"
					}
				});
            }
        } else {
       		var container = $("<li>").appendTo(element);
       		var tmp = $("<div>").appendTo(container);
       		tmp.html($.getMessage('home_no_bulletin'));//"暂无公告"
        }
    });
    
}

//公告详情
$(".admin_task_title > a").live('click',function(){
	//标题
	var title = $(this).html();
	//公告内容
	var content = $(this).next().html();
	//时间
	var time = $(this).parent().next().html();

	var width = 750;
	var height = 500;
	var html = '<div title="'+$.getMessage('home_bulletin')+'" id="dialog-auto-tip">'
					+'<div style="text-align:center;">'
						+'<h2>'+title+'</h2>'
					+'</div>'
					+'<div style="border: 1px solid #D9D9D9;padding:10px 5px;">'
						+'<p>'+content+'</p>'
						+'<p style="text-align: right;height: 18px;">'+time+'</p>'
					+'</div>'
				+'</div>';

	$(html).dialog({
	        autoOpen: true,
	        width: width,
	        maxHeight: height,
	        modal: true,
	        show: "slide",
	        close: function () {
	            $(this).detach();
	        }
	});
});


