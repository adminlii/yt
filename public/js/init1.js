//init
var EC = $.myProject.attach({});
var paginationTotal = 10;
var paginationPageSize = 20;
var paginationCurrentPage = 1;
var EZ = {
    version: '13.15.2.'+new Date().getDate(),
    lang: 'zh_CN',
    edit: '编辑',
    answer: '回复',
    allStatus:['不可用','可用'],
    del: '删除',
    search: '搜索',
    create: '添加',
    deleteMessage: '确定要删除吗？',
    searchMessage: '没有找到记录，请调整搜索条件。',
    searchDefaultMessage: '请搜索...',
    url: '',
    listDate: '',
    editDialog: '',
    searchObj: '',
    callback: function () {
        return ''
    }
}

$(function () {
	/**
	 * 用户信息小面版监听，没有在面板上点击时，关闭面板
	 */
	$(document).bind("click",function(e){
		var obj = $("#user_info_sys_menu");
		if(obj.length > 0){
			return;
		}else{
			try{
				parent.closeUserInfoMenu();
			}catch (e) {
				//
			}
		}
	});
	
    EZ.listDate = $("#table-module-list-data");
    EZ.editDialog = $("#ez-wms-edit-dialog");
    EZ.searchObj = $("#searchForm");
    $(".keyToSearch").keyup(function (e) {
        var key = e.which;
        if (key == 13) {
            try {
                submitSearch();
            } catch (s) {
                alertTip(s);
            }
        }
    });

    $(".submitReturnFalse").submit(function () {
        return false;
    });
    
    /**
     * 双击复制订单号
     */
    $(".order_no_copy").live('dblclick',function(){
    	var text = $(this).find("a").html();
    	var bol = copy_clip(text);
    });
	
});


function alertTip(tip, width, height) {
    width = width ? width : 400;
    height = height ? height : 'auto';
    $('<div title="操作提示 (Esc)" id="dialog-auto-alert-tip"><p align="">' + tip + '</p></div>').dialog({
        autoOpen: true,
        width: width,
        maxHeight: height,
        modal: true,
        show: "slide",
        buttons: [
            {
                text: 'Close',
                click: function () {
                    $(this).dialog("close");
                }
            }
        ],
        close: function () {
            $(this).detach();
        }
    });
}

function alertConfirmTip(tip,callBack,width,height) {
    width = width ? width : 400;
    height = height ? height : 500;
    $('<div title="操作提示 (Esc)" id="dialog-auto-alertConfirm-tip"><p align="">' + tip + '</p></div>').dialog({
        autoOpen: true,
        width: width,
        maxHeight: height,
        modal: true,
        show: "slide",
        buttons: [
			{
			    text: '确定(OK)',
			    click: function () {
			        $(this).dialog("close");
			        callBack();
			    }
			},
            {
                text: '取消(Cancel)',
                click: function () {
                    $(this).dialog("close");
                }
            }
        ],
        close: function () {
            $(this).detach();
        }
    });
}

function openIframeDialog(url, width, height,title) {
	title = title==undefined?'[Esc]':title;
    iframe = '<iframe  class="dialogIframe" name="dialogIframe" scrolling="yes" src="' + url + '"/>';
    $(iframe).dialog({
        autoResize: true,
        resizable: false,
        width: width + 30,
        modal: true,
        position: 'top',
        title: title,
        close: function () {
            $(this).remove();
        }
    }).width(width).height(height);
}
//当关闭iframe dialog后，是否刷新列表
var reLoadWhenClose = 0;
function openIframeDialogNew(url, width, height,title,quickId,page,pageSize) {
	title = title==undefined?'[Esc]':title;
    iframe = '<iframe  class="dialogIframe" name="dialogIframe" scrolling="yes" src="' + url + '"/>';
    $(iframe).dialog({
        autoResize: true,
        resizable: false,
        width: width + 30,
        modal: true,
        position: ['center',10],
        title: title,
        close: function () {
        	if(reLoadWhenClose==1){
        		if($('#iframe-container-'+quickId).size()>0){
                	window.frames['iframe-container-'+quickId].loadData(page,pageSize);        			
        		}else{
                	loadData(page,pageSize);
        		}
        	}
            $(this).remove();
        },
        open:function(){
        	reLoadWhenClose = 0;
        }
    }).width(width).height(height);
}

jQuery.fn.EzWmsSetSearchData = function (options) {
    var defaults = {
        msg: EZ.searchDefaultMessage,
        state: 0
    }
    var options = $.extend(defaults, options);
    if (options.state == '1' && options.msg == EZ.searchDefaultMessage) options.msg = EZ.searchMessage;
    if ($(this).children("tr").length < 1 || options.state == '1') {
        $(this).html('<tr class="table-module-b1"><td colspan=' + $(this).prev().children("tr").children("td").length + '>' + options.msg + '</td></tr>');
    }
}

jQuery.fn.EzCheckAll = function (obj) {
    if ($(this).is(':checked')) {
        obj.attr('checked', true);
    } else {
        obj.attr('checked', false);
    }
}


$.EzWmsConfirmDialog = function (options, Yfuns, Nfuns) {
    var defaults = {
        funData: [],
        paramId: 0,
        dWidth: "400",
        dHeight: "auto",
        dTitle: "Tips",
        dMessage: EZ.delMessage
    }
    var options = $.extend(defaults, options);
    $('<div title="' + options.dTitle + '" class="dialog-confirm-alert-tip">' + options.dMessage + '</div>').dialog({
        autoOpen: true,
        width: options.dWidth,
        height: options.dHeight,
        modal: true,
        show: "slide",
        buttons: {
            'Ok': function () {
                if (typeof(Yfuns) == "function") {
                    Yfuns(options.paramId);
                }
                $(this).dialog('close');
            },
            'Cancel': function () {
                if (typeof(Nfuns) == "function") {
                    Nfuns();
                }

                $(this).dialog('close');
            }
        },
        close: function () {
            $(this).detach();
        }
    });
};

$.EzWmsDel = function (options, Yfuns, Nfuns) {
    var defaults = {
        funData: [],
        Field: 'id',
        paramId: 0,
        url: '/',
        dWidth: "400",
        dHeight: "auto",
        dTitle: "Tips",
        successMsg: "",
        dMessage: EZ.deleteMessage
    }
    var options = $.extend(defaults, options);
    $('<div title="' + options.dTitle + '" class="dialog-confirm-del-alert-tip"><p style="text-align:left;font-size: 14px;color: red;font-weight: bold;">' + options.dMessage + '</p></div>').dialog({
        autoOpen: true,
        width: options.dWidth,
        height: options.dHeight,
        modal: true,
        show: "slide",
        buttons: {
            'Ok': function () {
                $.ajax({
                    type: "post",
                    async: false,
                    dataType: "json",
                    url: options.url,
                    data: options.Field + "=" + options.paramId,
                    success: function (json) {
                        var state = json && json.state ? json.state : 0;
                        var message = json && json.message ? json.message : options.successMsg;
                        if (state && typeof(Yfuns) == "function") {
                            Yfuns();
                        }
                        alertTip('<p style="text-align:left;font-size: 14px;color: red;font-weight: bold;">' + message + '</p>');
                    }
                });

                $(this).dialog('close');
            },
            'Cancel': function () {
                if (typeof(Nfuns) == "function") {
                    Nfuns();
                }

                $(this).dialog('close');
            }
        },
        close: function () {
            $(this).detach();
        }
    });
};

jQuery.fn.EzWmsEditDataDialog = function (options) {
    var defaults = {
        jsonData: {},
        Field: 'paramId',
        paramId: 0,
        url: '/',
        editUrl: '/',
        dWidth: "550",
        dHeight: "auto",
        dTitle: "系统操作/Operations",
        successMsg: ""
    }
    var options = $.extend(defaults, options);
    var div = $(this);
    $('<div title="' + options.dTitle + '" id="dialog-edit-alert-tip" class="dialog-edit-alert-tip"><form id="editDataForm" name="editDataForm" class="submitReturnFalse">' + div.html() + '</form><div class="validateTips" id="validateTips"></div></div>').dialog({
        autoOpen: true,
        width: options.dWidth,
        height: options.dHeight,
        modal: true,
        show: "slide",
        buttons: [
            {
                text: "确定(Ok)",
                click: function () {
                    if (options.editUrl == '/') {
                        return;
                    }
                    $("#editDataForm").submit();
                }
            },
            {
                text: "取消(Cancel)",
                click: function () {
                    $(this).dialog("close");
                }
            }
        ], close: function () {
            $(this).detach();
        }
    });

    var getJson = function () {
        $.ajax({
            type: "post",
            async: false,
            dataType: "json",
            url: options.url,
            data: options.Field + "=" + options.paramId,
            success: function (json) {
                if (json.state) {
                    $.each(json.data, function (k, v) {
                        $("[name=" + k + "]", "#dialog-edit-alert-tip").val(v);
                    });
                }
            }
        });
    }

    var editSuccess = function (json) {
        switch (json.state) {
            case 1:
                $("#dialog-edit-alert-tip").dialog("close");
                initData(0);
                break;
            case 2:
                $("#dialog-edit-alert-tip").dialog("close");
                loadData(paginationCurrentPage, paginationPageSize);
                alertTip(json.message);
                break;
            default:
                var html = '';
                var tipObj = $("#validateTips");
                if (json.errorMessage == null)return;
                $.each(json.errorMessage, function (key, val) {
                    html += '<span class="tip-error-message">' + val + '</span>';
                });
                tipObj.html(html);
                break;
        }
    }
    if (options.url != '/' && options.paramId != '0') {
        getJson()
    }
    $("#editDataForm").myAjaxForm({api: options.editUrl, success: editSuccess});
}

function submitSearch() {
    initData(0);
}

function success(json) {
    switch (json.state) {
        case 1:
            initData(0);
            $(this).dialog('close');
            dialogClose = 1;
            break;
        case 2:
            loadData(paginationCurrentPage, paginationPageSize);
            dialogClose = 1;
            alertTip(json.message);
            break;
        default:
            var html = '';
            var tipObj = $("#validateTips");
            if (json.errorMsg == null)return;
            $.each(json.errorMsg, function (key, val) {
                html += '<span class="tip-error-message">' + val + '</span>';
            });
            tipObj.html(html);
            break;
    }
}

//Del
function deleteById(id) {
    if (id == '' || id == undefined) {
        return false;
    }
    $.EzWmsDel(
        {paramId: id,
            Field: "paramId",
            url: EZ.url + "delete"
        }, initData
    );
}

//Edit
function editById(id) {
    EZ.editDialog.EzWmsEditDataDialog({
        paramId: id,
        url: EZ.url + "get-by-json",
        editUrl: EZ.url + "edit"
    });
}

//answer message---回答问题
function answerById(id) {
    window.open(EZ.url+"get-by-json/paramId/"+id);
}

function isJson(obj) {
    return typeof(obj) == "object" && Object.prototype.toString.call(obj).toLowerCase() == "[object object]" && !obj.length;
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
            //alert(paginationTotal);
            if (json.state == '1') {
//            	EZ.listDate.html(EZ.getListData(json));
                EZ.listDate.empty();
                $(EZ.getListData(json)).appendTo(EZ.listDate);
                initOperate();
            } else {
                EZ.listDate.EzWmsSetSearchData({state: 1});
            }

        }
    });
}

function windowHeight() {
    // Standard browsers (Mozilla, Safari, etc.)
    if (self.innerHeight)
        return self.innerHeight;
    // IE 6
    if (document.documentElement && document.documentElement.clientHeight)
        return document.documentElement.clientHeight;
    // IE 5
    if (document.body)
        return document.body.clientHeight;
    // Just in case.
    return 0;
}
function windowWidth() {
    // Standard browsers (Mozilla, Safari, etc.)
    if (self.innerWidth)
        return self.innerWidth;
    // IE 6
    if (document.documentElement && document.documentElement.clientWidth)
        return document.documentElement.clientWidth;
    // IE 5
    if (document.body)
        return document.body.clientWidth;
    // Just in case.
    return 0;
}

//头部选项卡菜单管理，注意ID必须加单引号传入
function leftMenu(id, title, url) {
    $(window.parent.document).find(".main-right>.guild>h2").css("background", "transparent");
    var menulen = $(window.parent.document).find(".main-right>.guild>h2").length;
    var guild = $(window.parent.document).find(".main-right>.guild").length;
    if(guild==0){//弹出页面
    	window.open(url);
    	return;
    }
    //读取菜单栏是否已经存在此标签，存在则不再添加
    obj = window.parent.document.getElementById("menu" + id);

    var showTitle;
    //标签太长则省略号
    if (title.length > 12) {
        showTitle = title.substring(0, 12) + "...";
    } else {
        showTitle = title;
    }
    var max_window_mun = 6;
    if (obj == null) {
        if (menulen > max_window_mun) {
            saveAsMenu();
        }
        var tempOnclick = "leftMenu(" + "'" + id + "'" + ',\'' + title + '\',\'' + url + "\')";
        var tempOnclickLast = "closeMenu(this,'"+id+"')";
        var tempHtml = '<H2 style="cursor:pointer " id="' + "menu" + id + "" + '">';
        tempHtml = tempHtml + '<a style="float: left" title="'+title+'"  href="javascript:void(0)"';
        tempHtml = tempHtml + ' onclick="' + tempOnclick + '">' + showTitle + '</a>';
        tempHtml = tempHtml + '<span style="font-size:18px;padding-left: 8px;padding-right:0px;color:darkgray;float: left; " onclick="' + tempOnclickLast + '">×</span></H2>';

        $(window.parent.document).find(".main-right>.guild>h3").before(tempHtml);
    } else {
        obj.style.background = "#fff";
    }

    var tHtml = '';
    tHtml += '<iframe frameborder="0" src="' + url + '" width="100%" height="100%" id="iframe-container-' + id + '" name="iframe-container-' + id + '" class="iframe-container" ></iframe>';
    var iframeObj = $(window.parent.document).find(".iframe-container");
    iframeObj.hide();
    obj = $(window.parent.document).find("#iframe-container-" + id);
    if (obj.size() > 0) {
        if (url != obj.attr('src')) {
            obj.remove();
            $(window.parent.document).find("#main-right-container-iframe").append(tHtml);
        } else {
            obj.css('display', 'block');
        }
    } else {
        $(window.parent.document).find("#main-right-container-iframe").append(tHtml);
    }
    if (iframeObj.size() > (max_window_mun + 1)) {
        iframeObj.eq(1).remove();
    }
    $.cookie('currentPage', id + '{|}' + url + '{|}' + title, {path: '/'});
}

function closeMenu(obj,thisId) {
    var id,title,url;
    var tmpOnclick=$(obj).parent().prev().children().attr("onclick");
   // alert(tmpOnclick);
    var tmpId=tmpOnclick.split(",")[0];
    id=tmpId.substring(tmpId.indexOf("'")+1,tmpId.length-1);

    var tmpTitle=tmpOnclick.split(",")[1];
    title=tmpTitle.substring(tmpTitle.indexOf("'")+1,tmpTitle.length-1);

    var tmpUrl=tmpOnclick.split(",")[2];
    url=tmpUrl.substring(tmpUrl.indexOf("'")+1,tmpUrl.length-2);

     $(obj).parent().remove();
	 $(window.parent.document).find("#iframe-container-" + thisId).remove();
    leftMenu(id, title, url);
}
//关闭历史标签里的菜单
function closeHisMenu(obj){
    $(obj).parent().remove();
}

//历史区域点击事件
function leftMenuHis(id, title, url){
    show();
    leftMenu(id, title, url);
}

//菜单保存到历史区域
function saveAsMenu() {
   // alert($(window.parent.document).find(".main-right>.guild>h2").eq(1).html());

    var id=$(window.parent.document).find(".main-right>.guild>h2").eq(1).attr("id");

    var obj= window.parent.document.getElementById("his"+id)
    if (obj==null)
    {
        //默认保存第二个元素页
        var saveHtml="<h4 id="+'his'+id+">"+$(window.parent.document).find(".main-right>.guild>h2").eq(1).html()+"</h4>";
        //替换关闭的方法，因为历史标签关闭时，不需要回到上一个标签
        saveHtml=saveHtml.replace("closeMenu","closeHisMenu");
        saveHtml=saveHtml.replace("leftMenu","leftMenuHis");

        $(window.parent.document).find(".main-right>.guild>h3>div").append(saveHtml);
    }
    $(window.parent.document).find(".main-right>.guild>h2").eq(1).remove();
}

function showHistory() {
    if ($(window.parent.document).find(".main-right>.guild>h3>div>h4").length > 0) {
        var menudisplay = document.getElementById("menuList").style.display;
        if (menudisplay == "none") {
            menudisplay = "block";
            $("#menuList").css("display", menudisplay);
            $("#menuBtn").attr("src", "/images/pack/bg_guild_drop02.gif");
        } else {
            menudisplay = "none";
            $("#menuBtn").attr("src", "/images/pack/bg_guild_drop01.gif");
        }
        $("#menuList").css("display", menudisplay);
    }
}
//过滤条件搜索
function searchFilterSubmit(inputname,value,obj){
    if(inputname!=null){
        $('input[name='+inputname+']',"#searchForm").attr('value',value);
        $(obj).parent().children("a").removeClass('current');
        $(obj).addClass('current');
        submitSearch();
    }
}

function autoHeight() {
    var header_h = $("#header").height();
    // var w_ = windowWidth();
    var body_h = windowHeight();
    if (body_h < 600) {
        body_h = 600;
    }
    var sidebarH=body_h - header_h - 14;
    $("#sidebar").css('height', sidebarH);
    $("#sidebarIcon").css('height', sidebarH);
    var menuObj=$("#menu-module");
    if(menuObj.height()>sidebarH-150){
        menuObj.css('overflow-y','auto').css('height',sidebarH-110);
    }
    $(".main-right-container").css('height', body_h - header_h - 129);
}

$(function () {
    autoHeight();
});
var resizeTimer = null;
window.onresize = function () {
    resizeTimer = resizeTimer ? null : setTimeout(autoHeight, 0);
};
function toAdvancedSearch(){
    $("#search-module-baseSearch").hide();
    $(".input_text","#search-module-baseSearch").val('');
    $("#search-module-advancedSearch").show();
}

function toBaseSearch(){
    $("#search-module-baseSearch").show();
    $("#search-module-advancedSearch").hide();
    $(".input_text","#search-module-advancedSearch").val('');
}
/**
 * 关闭加载图标
 */
jQuery.fn.closeMyLoading = function close(){
	$(this).find(".loading").remove();
};

$(function(){
	$('.headProduct').hover(function(){
		$('.headPro_drop',this).show();
	},function(){
		$('.headPro_drop',this).hide();
	});
});

$(function() {
	/*
	 * 初始化多选操作按钮
	 */
	initOperate();
	
	/*
	 * 多选操作按钮事件监听
	 */
	$('.dropdown_nav li').live({
		mouseleave: function() {
			$(this).find('.sub_nav').fadeOut(50);			
		},
		mousemove: function() {
			$(this).find('.sub_nav').fadeIn(100);
		}
	});
	
	/**
	 * 设置返回页头的按钮样式
	 */
	setToTopStyleAndHtml();
	/**
	 * 控制返回顶部，和前往底部
	 */
	$(window).scroll(function(){
		var off = $('#table-module-list-data').offset();
		if(!off){
			return;
		}
		var top = off.top;
		var scrollTop = $(this).scrollTop();
		var obj = $(".to_top_div");
		var data_show = obj.attr('data-show');
		if(scrollTop > top){
			if(data_show == "true"){
				return;
			}
			obj.animate({
				right:"2"
			},100);
			obj.attr('data-show','true');
		}else{
			if(data_show == "false"){
				return;
			}
			obj.animate({
				right:"-40"
			},150);
			obj.attr('data-show','false');
		}
		
	});
});

/**
 * 初始化多选操作按钮
 * <ul class="dropdown_nav">
		<li>
			<a href="javascript:;">查看&nbsp;<strong style=""></strong></a>
			<!-- data-width:设置下拉选项的宽度
				 data-float:设置下拉选项左右偏移显示
			 -->
			<ul class="sub_nav" data-width="70" data-float="right">
				<li ><a href="javascript:;">补货策略</a></li>
				<li ><a href="javascript:;">安全库存</a></li>
				<li ><a href="javascript:;">XXOO</a></li>
				<li ><a href="javascript:;">适应性</a></li>
			</ul>
		</li>
	</ul>
 */
function initOperate(){
	$('.dropdown_nav li').find('.sub_nav').hide();
	var listSubNav = $('.sub_nav');
	$.each(listSubNav,function(i){
		var width = $(this).attr('data-width');
		width = (width == '' || width == 0 || typeof(width) == 'undefined')?100:width;
		var float = $(this).attr('data-float');
		float = (float == '' || typeof(float) == 'undefined')?'left':float;
		$(this).css('width',width);
		$(this).css(float,0);
		var liTag = $(this).children();
		liTag.css('width',width);
	});
}

/**
 * 设置返回顶部的按钮样式和html
 */
function setToTopStyleAndHtml(){
	var ss = 1;
	if($("#head").length > 0){
		return;
	}
	
	var _toTopDiv = ($("#module-container").length > 0)?$("#module-container"):$("body");
	_toTopDiv.append('<div class="to_top_div" data-show="false">'+
			'<span style="float: left;cursor: pointer;padding: 0px 2px;" title="返回顶部" class="iconToTop" onclick="toTop();"></span>'+
			'<span style="float: left;cursor: pointer;padding: 0px 2px;" title="前往底部" class="iconToBottom" onclick="toBottom();"></span>'+
	'</div>');
	
	var _style = (!$("<style>"))?$("<style>").eq(0):$("<style>").insertBefore("body");
	_style.append(".to_top_div{display: block;position: fixed;right: -40px;bottom: 70px;z-index: 1;width: 33px;}"+
					".to_top_div .iconToTop{opacity: 0.45;}"+
					".to_top_div .iconToTop:hover{opacity: 1;}"+
					".iconToTop{background: url('/images/base/arrow-up.png') no-repeat scroll 0 0 / 30px auto rgba(0, 0, 0, 0);display: block;height: 30px;vertical-align: middle;white-space: nowrap;width: 30px;}"+
					".to_top_div .iconToBottom{opacity: 0.45;}"+
					".to_top_div .iconToBottom:hover{opacity: 1;}"+
					".iconToBottom{background: url('/images/base/arrow-down.png') no-repeat scroll 0 0 / 30px auto rgba(0, 0, 0, 0);display: block;height: 30px;vertical-align: middle;white-space: nowrap;width: 30px;margin-top: 5px;}");
}

/**
 * 返回页顶
 * @returns {Boolean}
 */
function toTop(){
	$('html,body').animate({
		scrollTop:'0px'
		},500);
	return false;
}

/**
 * 前往底部
 * @returns {Boolean}
 */
function toBottom(){
	var height = $("html").height();
	$('html,body').animate({
		scrollTop:height
		},500);
	return false;
}

/**
 * 将字符串写入粘贴板
 * @param text
 */
function copy_clip(txt){
	if(window.clipboardData)
	 {
	  window.clipboardData.clearData();
	  window.clipboardData.setData("Text", txt);
	  	alertTip("成功复制！到聊天窗口粘贴(Ctrl+v)即可");
	  return true;
	 }
	 else if(navigator.userAgent.indexOf("Opera") != -1)
	 {
		alertTip("此功能不支持Opera,请直接复制单号："+txt);
		return false;
	 }
	 else if (window.netscape)
	 {
	  try
	  {
		  netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
	  }
	  catch (e)
	  {
		  //alert("您的firefox安全限制限制您进行剪贴板操作，请打开'about:config'将 signed.applets.codebase_principal_support'设置为true'之后重试");
		  alertTip("您的firefox安全限制限制您进行剪贴板操作,请直接复制单号："+txt);
		  return false;
	  }
	  var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
	  if (!clip)
	  {
	   return false;
	  }
	  var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
	  if (!trans)
	  {
	   return false;
	  }
	  trans.addDataFlavor('text/unicode');
	  var str = new Object();
	  var len = new Object();
	  var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
	  var copytext = txt;
	  str.data = copytext;
	  trans.setTransferData("text/unicode",str,copytext.length*2);
	  var clipid = Components.interfaces.nsIClipboard;
	  if (!clip)
	  {
	   return false;
	  }
	  clip.setData(trans,null,clipid.kGlobalClipboard);
	  alertTip("成功复制！到聊天窗口粘贴(Ctrl+v)即可");
	  return true;
	 }else{
		 alertTip("由于您的浏览器安全限制限制您进行剪贴板操作,请直接复制单号："+txt);
		 return false;
	 }
}
//遮罩 start
function loadLayer(tip,tip_id) {
	if(!tip_id){
		tip_id = 'dialog-layer-tip';
	}
    $('<div title="Working" id="'+tip_id+'"><p align="">' + tip + '</p></div>').dialog({
        autoOpen: true,
        width: 400,
        modal: true,
        closeOnEscape:false,
        position: ['center'],
        show: "slide",
        buttons: [
            {
                text: 'Close',
                click: function () {
                    $(this).dialog("close");
                }
            }
        ],
        close: function () {
            $(this).remove();
        },
        open:function(){
        	$('.ui-button',$(this).parent()).hide();
        }
    });
}
function loadStart(tip_id,title){
	if(!tip_id){
		tip_id = 'dialog-layer-tip';
	}
	if(!title){
		title = '系统处理中,请稍候...';
	}
	loadLayer(title,tip_id);
    setTimeout(function(){
    	$('.ui-button',$('#'+tip_id).parent()).hide();
    	$('#'+tip_id).dialog('option','closeOnEscape',false);   
    	
    },100);
	     
}

function loadEnd(html,tip_id){
	if(!tip_id){
		tip_id = 'dialog-layer-tip';
	}
	if(html&&html!=''){
		$('#'+tip_id).dialog('option','title','完成');      	
	  	$('.ui-dialog-content',$('#'+tip_id).parent()).html(html);
	   	$('.ui-button',$('#'+tip_id).parent()).show();
		$('#'+tip_id).dialog('option','closeOnEscape',true);
	}
	setTimeout(function(){$('#'+tip_id).dialog('close');},800);
}
//遮罩 end



