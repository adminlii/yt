EZ.url = '/message/ebay/';
EZ.getListData = function(json) {
	var html = '';
	$(".checkAll").attr('checked', false);
	var status = $('#status').val();
	var opHtml = '';
	opHtml += '<input type="button" value="高亮标记" class="highLightBtn baseBtn"/>';
	// alert($(this).attr('type'));
	switch (status) {
		case '' :// 全部
			
			break;
		case '0' :// 未读
			// opHtml+='<input type="button" value="站内信通知">';
			//opHtml += '<input type="button" value="批量回复" class="feedBackBtn baseBtn "/>';
			//opHtml += '<input type="button" value="标记已读" class="readTagBtn baseBtn"/>';
			opHtml += '<input type="button" value="批量分配" class="fenpeiBtn baseBtn"/>';
			opHtml += '<input type="button" value="标记已回复" class="feedBackBatchBtn baseBtn"/>';
			break;
		case '1' :// 已读
			//opHtml += '<input type="button" value="批量回复" class="feedBackBtn baseBtn "/>';
			//opHtml += '<input type="button" value="批量删除" class="deleteBatchBtn baseBtn"/>';
			opHtml += '<input type="button" value="撤销回复" class="unFeedBackBtn baseBtn"/>';
			break;
		case '3' :// eBay消息
			//opHtml += '<input type="button" value="批量删除" class="feedBackBtn baseBtn"/>';
			break;
		default :

	}

	$("#opDiv").html(opHtml);

	if (json.ask) {
		var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
		$.each(json.data, function(key, val) {
			html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1 messageLine' message_id='" + val.ebay_message_id + "'>" : "<tr class='table-module-b2 messageLine'  message_id='" + val.ebay_message_id + "'>";
			html += "<td class='ec-center'>" + '<input type="checkbox" value="' + val.ebay_message_id + '" name="message_id[]" class="checkItem">' + "</td>";

			html += "<td >" + "<span style='float: left' class='iconlevel" + val.level + "'></span>" + "</td>";
			html += "<td style='word-wrap: break-word;'>" + val.sender_id + "</td>";
			html += "<td style='word-wrap: break-word;'>" + val.receiving_id + "</td>";
			html += "<td style='word-wrap: break-word;'>";
			if(val.response_status=='0'||val.response_status=='3'){
				html += "<a url='#' href='/message/ebay/detail/messageId/" + val.ebay_message_id + "/append/n' class='feedBack viewMessage' target='_blank'>"+val.message_title+"</a>";

			}else{
				html += "<a url='/message/ebay/detail-message/messageId/"+val.ebay_message_id+"' href='javascript:void(0);' class='detailBtn viewMessage' message_id='" + val.ebay_message_id + "'>"+val.message_title+"</a>";

			}

			html += "</td>";
			html += "<td style='word-wrap: break-word;'>" + val.message_type + "</td>";
			html += "<td >";
			if (val.status == 0) {
				html += "未读";
			} else if(val.status == 1) {
				html += "已读";
			}else if(val.status == 4) {
				html += "删除";
			}
			html+=' <br/> ';			
			html += val.status_title;	
			html += "</td>";
			html += "<td >" + "接收时间：" + val.send_time + "<br/>" + "响应时间：" + val.response_time + "</td>";

			html += "</tr>";
		});
	} else {
		html += "<tr class='table-module-b1'><td colspan='9'>无数据</td></tr>";
	}

	return html;
}

function loadData(page, pageSize) {
    EZ.listDate.myLoading();
    $.ajax({
        type: "POST",
        async: false,
        dataType: "json",
        url: EZ.url + "list/page/" + page + "/pageSize/" + pageSize,
        data: EZ.searchObj.serializeArray(),
        error: function () {
            paginationTotal = 0;
            EZ.listDate.EzWmsSetSearchData({msg: 'The URL request error.'});
            return;
        },
        success: function (json) {
            if (!isJson(json)) {
                paginationTotal = 0;
                EZ.listDate.EzWmsSetSearchData({msg: 'Returns the data type error.'});
                return;
            }
            paginationTotal = json.total;
            if (json.state == '1') {
                EZ.listDate.html(EZ.getListData(json));
            } else {
                EZ.listDate.EzWmsSetSearchData({state: 1});
            }

        }
    });
    getMessageGroup();
}
function getMessageGroup(){
	$.ajax({
		type : "POST",
		url : "/message/ebay/group-response-status",
		data : {},
		dataType : 'json',
		success : function(json) {
			$.each(json,function(k,v){
				if(k=='all'){
					k='';
				}
				$('#link_is_response'+k+' span').html('&nbsp;['+v+']');	
			})
		}
	});
}
var content = '';
$(function() {
	getMessageGroup();
	$('.link_is_response').click(function(){
		$('.link_is_response').removeClass('current');
		$(this).addClass('current');
		var val = $(this).attr('val');
		$('#is_response').val(val);
		//$('.submitToSearch').click();
		if(val==''){
			$("#readTag").click();
		}else if(val=='0'){
			$("#readTag0").click();
		}else{
			$("#readTag1").click();
		}
		
	});
	$("#module-container .Tab .readTag").click(function() {
		$("#module-container .Tab li").removeClass('chooseTag');
		$(this).addClass('chooseTag');
		//$('#is_response').val('');
		$('#level').val('');
		$('#high_star_selected').attr('class','iconlevel0');
		$('#status').val($(this).attr('status'));		
		if($(this).attr('status')=='3'){
			$('.link_is_response').removeClass('current');
			$('#is_response').val('');
		}
		$('.submitToSearch').click();
			
		// loadData(1, paginationPageSize);
	});

	$("#module-container .Tab .feedBackTag").click(function() {
		$("#module-container .Tab li").removeClass('chooseTag');
		$(this).addClass('chooseTag');
		$('#status').val('');
		$('#level').val('');
		$('#is_response').val($(this).attr('is_response'));
		$('.submitToSearch').click();

		// loadData(1, paginationPageSize);
	});
	
	/*
	 * 双击(dblclick)的流程是：mousedown，mouseout，click，mousedown，mouseout，click，dblclick；
	 * 在双击事件(dblclick)，触发的两次单击事件(click)中，第一次的单击事件(click)会被屏蔽掉，但第二次不会。
	 * 所以可以使用计时的方式去除掉一个多余的单击事件就行了
	 * (ebay消息，按某个title排序)
	 */
	var _timeSort = null;
	$(".table-module-title .sort").click(function() {
		clearTimeout(_timeSort);
		var _this = $(this);
		_timeSort = setTimeout(function(){
            var sort = _this.attr('sort');
            if(sort==''||sort=='desc'){
            	nowSort = 'asc';
            }else{
            	nowSort = 'desc';
            } 
            
            $('.sort').attr('class','sort');
            _this.addClass(nowSort);
            _this.attr('sort',nowSort);
            $('.sort span').text('');
            $('.asc span').text('↑');
            $('.desc span').text('↓');
            
            
            var type = _this.attr('type');
            $('#sort').val(type+" "+nowSort);
            
            $('.submitToSearch').click();
        },300);
	}).dblclick(function(){
		clearTimeout(_timeSort);
		//干掉排序提示
		$('.sort span').text('');
		//干掉排序值
		$('#sort').val("");
		$('.submitToSearch').click();
	});
	
	
	
	$('#high_star').hover(function(){$('li',this).show();},function(){$('li',this).hide();});

	$('#high_star li').click(function(){
		var level = $(this).attr('level');
		$('#level').val(level);
		level= level==''?'0':level;
		$('#high_star_selected').attr('class','iconlevel'+level);
		$('.submitToSearch').click();
	
	});
	
	$(".checkAll").live('click', function() {
		$(".checkItem").attr('checked', $(this).is(':checked'));
	});

	var area = $('<textarea rows="14" name="content" id="content" cols="80" style="display:none;width: 770px;border:0 none;"></textarea>');
	$("#message_feedback_div").dialog({
		autoOpen : false,
		width : 800,
		height : 500,
		modal : true,
		show : "slide",
		position : 'top',
		buttons : [{
			text : 'Ok',
			click : function() {
				var this_ = $(this);
				var param = $("#feedBackForm").serialize();
				$.ajax({
					type : "POST",
					url : "/message/ebay/feed-back-message",
					data : param,
					dataType : 'json',
					success : function(json) {
						var html = '';
						html += json.message + "<br/>";
						alertTip(html, 500);
						if (json.ask) {
							this_.dialog('close');
							initData(paginationCurrentPage - 1);
						}

					}
				});
			}
		}, {
			text : 'Close',
			click : function() {
				$(this).dialog("close");
			}
		}],
		close : function() {
			$('#editor_wrap').html('');
			$("#messageIds").val('');
			$("#messageSubject").val('');
			area.val('');
			area.xheditor(false);

		},
		open : function() {
			$('#editor_wrap').html(area);
			var select = '<option value="" index="">可选择模板</option>';
			$.each(templateList, function(k, v) {
				select += '<option value="' + v.language + '" index="' + k + '">' + v.name + '</option>';
			});

			$("#select_module").html(select);
			setTimeout(function() {
				area.css('background', 'none');
				// area.xheditor({tools:'Blocktag,FontSize,FontColor,Bold,Italic,Underline,Strikethrough,Source,Fullscreen,About'});
				area.xheditor({
					tools : 'Fullscreen,About'
				});
			}, 500);
		}
	});
	$("#message_highlight_div").dialog({
		autoOpen : false,
		width : 300,
		maxHeight : 500,
		modal : true,
		show : "slide",
		position : 'top',
		buttons : [{
			text : 'Ok',
			click : function() {
				var this_ = $(this);
				var param = $("#listForm").serialize();
				param += "&level=" + $("#select_level").val();
				$.ajax({
					type : "POST",
					url : "/message/ebay/high-light-message",
					data : param,
					dataType : 'json',
					success : function(json) {
						this_.dialog('close');
						var html = '';
						html += json.message + "<br/>";
						alertTip(html, 500);
						if (json.ask) {
							initData(paginationCurrentPage - 1);
						}
					}
				});
			}
		}, {
			text : 'Close',
			click : function() {
				$(this).dialog("close");
			}
		}],
		close : function() {

		},
		open : function() {

		}
	});
	$("#highlight_select li").click(function() {
		var level = $(this).attr('level');
		$('#select_level').val(level);
		$('#highlight_select li').removeClass('selected');
		$(this).addClass('selected');
	});
	$("#message_detail_div").dialog({
		autoOpen : false,
		width : 800,
		maxHeight : 500,
		modal : true,
		show : "slide",
		position : 'top',
		buttons : [{
			text : 'Close',
			click : function() {
				$(this).dialog("close");
			}
		}],
		close : function() {

		},
		open : function() {

		}
	});

	/*$(".detailBtn").live('click', function() {
		
		var dbl_message_id = $(this).attr('message_id');
		$.ajax({
			type : "POST",
			url : "/message/ebay/detail-message",
			data : {
				'messageId' : dbl_message_id
			},
			dataType : 'json',
			success : function(json) {
				$("#currt_content").html(json.currt_content);
				$("#response_content").html(json.response_content);
				$("#message_detail_div").dialog('open');
				if($('#status').val()=='0'){
					initData(paginationCurrentPage-1);//刷新结果					
				}
			}
		});

	});*/
	$('.feedBackBtn').live('click', function() {
		// content =
		// "<p>当前实例调用的Javascript源代码为：</p><pre>loadJS('../xheditor-zh-cn.js',function(){$('#elm1').xheditor();});</pre>";
		if ($(".checkItem:checked").size() == 0) {
			alertTip('Pls Select Messages');
			return false;
		}
		var str = '';
		$(".checkItem:checked").each(function() {
			str += $(this).val() + ";";

		});
		$("#messageIds").val(str);
		$('#message_feedback_div').dialog('open');
	});

	$('#select_module').change(function() {
		var key = $('option:selected', this).attr('index');
		if (key !== '') {
			area.val(templateList[key].content);
			$("#messageSubject").val(templateList[key].subject);
		}
	});

	$(".readTagBtn").live('click', function() {
		if ($(".checkItem:checked").size() == 0) {
			alertTip('Pls Select Messages');
			return false;
		}
		if (!window.confirm('标记选中消息为已读？')) {
			return false;
		}
		var param = $("#listForm").serialize();
		$.ajax({
			type : "POST",
			url : "/message/ebay/read-tag-message",
			data : param,
			dataType : 'json',
			success : function(json) {
				var html = '';
				html += json.message + "<br/>";
				alertTip(html, 500);
				initData(paginationCurrentPage - 1);
			}
		});

	});
	/**
	 * 批量标记已回复
	 */
	$(".feedBackBatchBtn").live('click',function(){
		if ($(".checkItem:checked").size() == 0) {
			alertTip('Pls Select Messages');
			return false;
		}
		alertTip('正在努力标记中....');
		var param = $("#listForm").serialize();
		$.ajax({
			type : "POST",
			url : "/message/ebay/feed-back-message-tag-batch",
			data : param,
			dataType : 'json',
			success : function(json) {
				$('#dialog-auto-alert-tip').dialog('close');
				var bol = false;
				initData(paginationCurrentPage - 1);
				var i = 0;
				var k = 0;
				var html = "";
				$.each(json,function(k,v){
					if(v.ask != '1'){
						$("input[value='"+k+"']").selected();
						i+=1;
						bol = true;
						html += i+": "+v.message + "<br/>";
					}else{
						k+=1;
					}
				});
				if(bol){
					alertTip("标记成功<span style='color:#1B9301;'>"+k+"</span>个、标记失败<span style='color:red;'>"+i+"</span>个，选中记录为标记失败的消息,错误依次为：<br/>"+html, 500);
				}else{
					alertTip("批量标记成功", 500);
				}
			}
		});
	});
	/**
	 * 撤销回复
	 */
	$(".unFeedBackBtn").live('click',function(){
		var param = $("#listForm").serialize();
		var checkItem = $(".checkItem:checked");
		if(checkItem.length > 1){
			alertTip('ebay消息只能单独撤销，不支持批量操作！');
			return false;
		}else if (checkItem.length == 0) {
			alertTip('Pls Select Messages');
			return false;
		}else{
			alertTip('正在努力请求撤销中....');
		}
		$.ajax({
			type : "POST",
			url : "/message/ebay/un-feed-back-message-tag",
			data : param,
			dataType : 'json',
			success : function(json) {
				$('#dialog-auto-alert-tip').dialog('close');
				var html = "";
				if(json.ask != '1'){
					html += "撤销失败，原因： " + json.message;
				}else{
					html += "撤销成功！";
					initData(paginationCurrentPage - 1);
				}
				alertTip(html, 500);
			}
		});
	});
	/**
	 * 批量分配客服
	 */
	$(".fenpeiBtn").live('click', function() {
		if ($(".checkItem:checked").size() == 0) {
			alertTip('Pls Select Messages');
			return false;
		}
		$('#allot_div').dialog('open');		
	});
	$('#allot_div').dialog({
        autoOpen: false,
        width: 400,
        maxHeight: 400,
        modal: true,
        show: "slide",
        buttons: [
            {
                text: '分配',
                click: function () {
					alertTip('客服分配中....');
                	var this_ = $(this);
                	this_.dialog("close");
            		var param = $("#listForm").serialize();
            		var customer_service_id = $("#customer_service_id").val();
            		param+='&customer_service_id='+customer_service_id;
                	$.ajax({
            			type : "POST",
            			url : "/message/ebay/message-allot-batch",
            			data : param,
            			dataType : 'json',
            			success : function(json) {
        					$('#dialog-auto-alert-tip').dialog('close');
            				var html = '';
            				$.each(json,function(k,v){
            					html += k+": "+v.message + "<br/>";		
            				})
            						
            				alertTip(html, 500);
            				initData(paginationCurrentPage - 1);
            			}
            		});
                }
            },
            {
                text: '关闭',
                click: function () {
                	var this_ = $(this);
                	this_.dialog("close");
                }
            }
        ],
        close: function () {
        }
    });

	$(".highLightBtn").live('click', function() {
		if ($(".checkItem:checked").size() == 0) {
			alertTip('Pls Select Messages');
			return false;
		}
		$("#message_highlight_div").dialog('open');
	});

	$(".deleteBatchBtn").live('click', function() {
		if ($(".checkItem:checked").size() == 0) {
			alertTip('Pls Select Messages');
			return false;
		}
		if (!window.confirm('删除选中消息为已读？')) {
			return false;
		}
		var param = $("#listForm").serialize();
		$.ajax({
			type : "POST",
			url : "/message/ebay/delete-message",
			data : param,
			dataType : 'json',
			success : function(json) {
				var html = '';
				html += json.message;

				alertTip(html, 500);
				initData(paginationCurrentPage - 1);
			}
		});
	});

	$('select option').click(function() {

	});
	$('.search_more').toggle(function() {
		$("#search_more_div").show();
		$('#is_more').val('1');
	}, function() {
		$("#search_more_div").hide();
		$('#is_more').val('0');
	});

	$('#code_type').change(function() {
		if ($(this).val() == '0') {
			$('#sender_id').attr('name', 'sender_id');
		} else {
			$('#sender_id').attr('name', 'send_mail');
		}
	})
	
	/*$('.feedBack').live('click',function(){
		var url = $(this).attr('url');		
		var width = parent.windowWidth();
		var height = parent.windowHeight();
        parent.openIframeDialogNew(url,width-100,height-80,'消息回复',quickId,paginationCurrentPage,paginationPageSize);

        //alert($(window.parent.document).width());
		//var returnValue = window.showModalDialog(url,obj,"dialogWidth="+width+"px;dialogHeight="+height+"px;center=true;resizable=false;");
		//alert(returnValue);
	});

	
	$('.detailBtn').live('click',function(){
		var url = $(this).attr('url');		
		var width = parent.windowWidth();
		var height = parent.windowHeight();
        parent.openIframeDialogNew(url,width-100,height-80,'消息查看',quickId,paginationCurrentPage,paginationPageSize);
        //alert($(window.parent.document).width());
		//var returnValue = window.showModalDialog(url,obj,"dialogWidth="+width+"px;dialogHeight="+height+"px;center=true;resizable=false;");
		//alert(returnValue);
	});*/

	$('.viewMessage').live('click',function(){
		
		var url = $(this).attr('url');
		//var className = $(this).attr('class');
		//回复ebay消息打开新页面
		if(url == "#"){
			return ;
		}else{
			var width = parent.windowWidth();
			var height = parent.windowHeight();
			parent.openIframeDialogNew(url,width-100,height-80,'消息查看/回复',quickId,paginationCurrentPage,paginationPageSize);			
		}
		
	});
	
	
	
})
