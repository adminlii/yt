//页面加载，获取所有的分类的信息
$(document).ready(function(){
	getCatList();
});

function getCatList(){
	$("#catHtml").myLoading();
	$.ajax({
        type: "post",
        dataType: "json",
        url: '/message/template/get-cat-list',
        success: function (json) {
        	html = "";
        	$.each(json,function(k,v){
        		html += '<div class="mail_menu" style="">';
                html +="<dl><dt><a href='javascript:void(0)'>";
        		html += v.group_name_cn;
                html +="</a></dt>";
        		$.each(v.center, function(kk,vv){
        			html += "<dd onmouseout='hidenBi("+vv.group_id+");' onmouseover='showBi("+vv.group_id+");'>";
        			html += '<a href="javascript:void(0)">'+vv.group_name_cn;
                    html+='<img onclick="updateCat('+vv.group_id+');" id="imgBi'+vv.group_id+'" ' +'src="/images/sidebar/bi.jpg" ' +
                    'style="display:none;float:right;padding:3px;" />';
        			html += '</a></dd>';

        			$.each(vv.FinalCat,function(kkk, vvv){
        				html += "<p style='height:auto;padding-left: 5px;' onmouseout='hidenBi("+vvv.group_id+");' onmouseover='showBi("+vvv.group_id+");'>";
        				html += '<a class="catColor" id="catColor'+vvv.group_id+'" href="javascript:void(0); setAddTemplate('+vvv.group_id+')">'+vvv.group_name_cn;
        				html += '<img onclick="updateCat('+vvv.group_id+');" id="imgBi'+vvv.group_id+'" src="/images/sidebar/bi.jpg" style="display:none;float:right;padding:3px;" /></a>';
                        html += '</p>';
        			});

        		});
        		html += '</div>';
        	});
        	$("#catHtml").html(html);
        }
    });
}

/**
 * 修改分类
 */
function updateCat(id){
	
	//获取分类的数据
	$.ajax({
		type: "post",
        dataType: "json",
        url: '/message/template/get-group-info',
        data:{group_id:id},
        success: function (json) {
        	$("#update_group_name_cn").val(json.data.group_name_cn);
        	$("#update_group_name_en").val(json.data.group_name_en);
        	$("#update_group_note").val(json.data.group_note);
        	$("#update_platform").val(json.data.platform);
        	$("#nowGroupId").val(id);
        	$("#updateCatHtml").html(json.html);
        	$("#crumbs").html(json.crumbs);
        }
	});
	$("#updateCatHtmlNew").dialog("open");
	
}

function showBi(id){
	$("#imgBi"+id).css('display','block');
}

function hidenBi(id){
	$("#imgBi"+id).css('display','none');
}

/**
 * 设置模板的分类
 */
function setAddTemplate(groupid){
	$(".catColor").css("color","#666666");
	$("#catColor"+groupid).css("color","red");
	$("#groupTemplateId").css("display","block");
	$("#setAddTemplate").val(groupid);
}

/*获取分类的名，和语言*/
function addTemplate(){
	var group_id = $("#setAddTemplate").val();
    var h=500;
    var w =800;
	openIframeDialog('/message/template/new-iframe/group_id/'+group_id,w,h);
}

function openIframeDialog(url, width, height) {
    iframe = '<iframe id="addCatIfram" name="addCatIfram" class="dialogIframe" name="dialogIframe" scrolling="no" src="' + url + '"/>';
    $(iframe).dialog({
        autoResize: true,
        resizable: false,
        width: width + 30,
        modal: true,
        position: 'top',
        zIndex:4000,
        buttons: {
        	'Ok': function () {
               TemplateData();
            },
            'Cancel': function () {
                $(this).dialog('close');
            }
        },
        close: function () {
            $(this).remove();
        }
    }).width(width).height(height);
}

function showAddCatHtml(){
	//获取第一级分类的值
	$.ajax({
        type: "post",
        dataType: "json",
        url: '/message/template/get-cat-top',
        success: function (json) {
    		html = "";
    		html += '<select style="height:200px;WIDTH:200px" id="topCat" size="10" onclick="getTwoCat(\'topCat\');">';
        	$.each(json,function(k,v){
        		html += '<option value="'+v.group_id+'">'+v.group_name_cn+'</option>';
        	});
        	html += '</select>';
        	$("#oneCatHtml").html(html);
        }
    });
	$("#threeCatHtml").html("");
	$("#twoCatHtml").html("");
	$("#showAddCatHtml").dialog("open");
	$("#add_t").css("display","block");
}

/**
 * 搜索按钮，搜索list
 */
function getMessageTemplateList(){
	$("#table-module-list-data").myLoading();
	var E2 = $("#E1").val();
	var E1 = $("#E2").val();
	
	$.ajax({
        type: "post",
        dataType: "json",
        url: '/message/template/get-list',
        data: {E1:E1,E2:E2},
        success: function (json) {
        	if(json == "false"){
        		html = "没有数据";
        	}else{
	        	html = "";
	        	$.each(json,function(k,v){
	        		html += '<tr class="table-module-b1">';
	        		html += '<td>'+(k+1)+'</td>';
	        		html += '<td>'+v.template_name+'</td>';
	        		html += '<td>'+v.template_type+'</td>';
	        		html += '<td><a href="javascript:editTemplateById('+v.template_id+')">编辑</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:deleteTemplateById('+v.template_id+')">删除</a></td>';
	        		html += '</tr>';
	        	});
        	}
        	$("#table-module-list-data").html(html);
        }
    });
}

/**
 * 获取第二级
 */
function getTwoCat(selectId){
	var selectId = $("#"+selectId).val();
	//alert(selectId);
	$.ajax({
        type: "post",
        dataType: "json",
        url: '/message/template/get-cat-top',
        data:{pid:selectId},
        success: function (json) {
        	$("#nowLevel").val("topCat");
    		html = "";
    		html += '<select style="height:200px;WIDTH:200px" id="CatTwo" size="10" onclick="getFinalCat(\'CatTwo\');">';
        	$.each(json,function(k,v){
        		html += '<option value="'+v.group_id+'">'+v.group_name_cn+'</option>';
        	});
        	html += '</select>';
        	$("#threeCatHtml").html("");
        	$("#twoCatHtml").html(html);
        	$("#add_t").css("display","block");
        }
    });
}

/**
 * 获取第三级
 */
function getFinalCat(selectId){
	var selectId = $("#"+selectId).val();
	//alert(selectId);
	$.ajax({
        type: "post",
        dataType: "json",
        url: '/message/template/get-cat-top',
        data:{pid:selectId},
        success: function (json) {
        	$("#nowLevel").val("CatTwo");
    		html = "";
    		html += '<select style="height:200px;WIDTH:200px" id="CatTwo" size="10" onclick="hidenAddButton();">';
        	$.each(json,function(k,v){
        		html += '<option value="'+v.group_id+'">'+v.group_name_cn+'</option>';
        	});
        	html += '</select>';
        	$("#add_t").css("display","block");
        	$("#threeCatHtml").html(html);
        }
    });
}

/**
 * 最后一级，将添加按钮隐藏
 */
function hidenAddButton(){
	$("#add_t").css("display","none");
	$("#addCatText").css("display","none");
}

/**
 * 添加分类的显示框
 */
function addCat(){
	$("#addCatText").css("display","block");
	var nowLevel = $("#nowLevel").val();
	if(nowLevel == "CatTwo"){
		//添加第二级,获取第一级选中的分类
		var topCat = $("#topCat option:selected").text();
		var CatTwo = '  >  ' + $("#CatTwo option:selected").text();
	}else if(nowLevel == "topCat"){//添加第三极，获取三级全部的分类
		var topCat = $("#topCat option:selected").text();
		var CatTwo = "";
	}
	
	var prompt = topCat+CatTwo;
	$("#prompt").html(prompt);
}

/**
 * 添加分类
 */
function addCatTwo(){
	var nowLevel = $("#nowLevel").val();
	
	//上级ID
	var nowTemplateId = $("#"+nowLevel).val();
	
	var group_name_cn = $("#group_name_cn").val();
	var group_name_en = $("#group_name_en").val();
	var group_note = $("#group_note").val();
	var platform = $("#platform").val();
	
	if(group_name_cn == ''){
		$(".msg-1").html("不能为空");
		$("#group_name_cn").focus();
		return false;
	}
	
	if(group_name_en == ''){
		$(".msg-2").html("不能为空");
		$("#group_name_en").focus();
		return false;
	}
	
	if(group_note == ''){
		$(".msg-3").html("不能为空");
		$("#group_note").focus();
		return false;
	}
	
	if(platform == ''){
		$(".msg-4").html("不能为空");
		$("#platform").focus();
		return false;
	}
	
	$.ajax({
        type: "post",
        dataType: "json",
        url: '/message/template/add-cat',
        data: {group_name_cn:group_name_cn,group_name_en:group_name_en,group_note:group_note,platform:platform,group_pid:nowTemplateId},
        success: function (json) {
        	if (json.state) {
        		var nowLevel = $("#nowLevel").val();
				if(nowLevel == "topCat"){
					//如果是第一级
					getTwoCat("topCat");
				}else{
					getFinalCat("CatTwo");
				}
				$("#group_name_cn").val("");
				$("#group_name_en").val("");
				$("#group_note").val("");
				$("#platform").val("");
        	}
        }
    });
}




//加载框
$(function () {
    $("#showAddCatHtml").dialog({
        autoOpen: false,
        modal: true,
        width: 700,
        height: 500,
        show: "slide",
        buttons: {
            'Cancel': function () {
                $(this).dialog('close');
            }
        },
        close: function () {
        	$("#addTemplateHtml input").val('');
            $(this).dialog('close');
        }
    });

    $("#addTemplateHtml").dialog({
        autoOpen: false,
        modal: true,
        width: 700,
        height: 500,
        show: "slide",
        buttons: {
        	'Ok': function () {
               TemplateData();
            },
            'Cancel': function () {
                $(this).dialog('close');
            }
        },
        close: function () {
        	$("#addTemplateHtml input").val('');
            $(this).dialog('close');
        }
    });
    
    $("#updateCatHtmlNew").dialog({
        autoOpen: false,
        modal: true,
        width: 700,
        height: 500,
        show: "slide",
        buttons: {
        	'Ok': function () {
               updateCatClick();
            },
            'Cancel': function () {
                $(this).dialog('close');
            }
        },
        close: function () {
        	$("#addTemplateHtml input").val('');
            $(this).dialog('close');
        }
    });
});

function updateCatClick(){
	var group_name_cn = $("#update_group_name_cn").val();
	var group_name_en = $("#update_group_name_en").val();
	var group_note = $("#update_group_note").val();
	var platform = $("#update_platform").val();
	
	var nowLevelUpdate = $("#nowLevelUpdate").val();
	var nowGroupId = $("#nowGroupId").val();
	
	$.ajax({
        type: "post",
        dataType: "json",
        url: '/message/template/update-cat-click',
        data: {group_name_cn:group_name_cn,group_name_en:group_name_en,group_note:group_note,platform:platform,nowLevelUpdate:nowLevelUpdate,nowGroupId:nowGroupId},
        success: function (json) {
        	if(json.state){
        		alertTip(json.message);
        		$("#updateCatHtmlNew").dialog('close');
        		getCatList();
        	}
        }
    });
}


/**
 * 添加模板
 */
function TemplateData(){
	var te_id = $("#addCatIfram").contents().find("#te_id").val();
	//var str =dialogIframe.window.demo();
	var con = $(window.frames["addCatIfram"].document).find("#template_content_2").size();
	var val = $(window.frames["addCatIfram"].document).find("#template_content_2").val();
	alert(con+val);return;
	if(te_id != ""){		
		//修改,或添加新语言
		$("#addCatIfram").contents().find("#templateData").ajaxSubmit({
			url:"/message/template/add-template/te_id/"+te_id,
			type: "post",
			datatype:"json",
			success : function(json){
				if(json != "false"){
					alertTip("修改成功");
				}else{
					alertTip("修改成功");
				}
			}
		});
		return false;
	}
	
	$("#addCatIfram").contents().find("#templateData").ajaxSubmit({
		url:"/message/template/add-template",
		type: "post",
		datatype:"json",
		data:$("#templateData").serializeArray(),
		success : function(json){
			if(json != "false"){
				$("#addCatIfram").contents().find("#te_id").val(json);
				alertTip("添加成功");
			}else{
				alertTip("添加成功");
			}
		}
	});
	return false;
	
}

function editTemplateById(id){
	openIframeDialog('/message/template/edit-template-by-id/id/'+id,800,500);
	
	
//	$.ajax({
//        type: "post",
//        dataType: "json",
//        url: '',
//        data: {id:id},
//        success: function (json) {
//        	
//        	//alert("");
//        	//$("#addCatIfram").contents().find("#template_name").val("--");
////        	$("#template_name").val(json.template_name);
////        	$("#template_note").val(json.template_note);
////        	$("#template_type").val(json.template_type);
////        	$("#template_group_name").val(json.group_name);
////        	$("#te_id").val(json.template_id);
//        	
//			$.each(json.centent,function(k,v){
//				$("#template_title"+v.code_name).val(v.template_subject);
//				$("#template_content"+v.code_name).val(v.template_content);
//			});
//        	
//        }
//    });
	
}


function deleteTemplateById(id){
	if (id == '' || id == undefined) {
        return false;
    }
    $.EzWmsDelTemplate(
        {paramId: id,
            Field: "paramId",
            url: EZ.url + "delete"
        }, submitSearch
    );
}

$.EzWmsDelTemplate = function (options, Yfuns, Nfuns) {
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
                getMessageTemplateList();
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

function getTwoCatUpdate(selectId){
	var selectId = $("#"+selectId).val();
	//alert(selectId);
	$.ajax({
        type: "post",
        dataType: "json",
        url: '/message/template/get-cat-top',
        data:{pid:selectId},
        success: function (json) {
    		html = "";
    		html += '<select style="height:200px;WIDTH:200px" id="CatTwoUpdate" size="10" onclick="getFinalCatUpdate(\'CatTwoUpdate\');">';
        	$.each(json,function(k,v){
        		html += '<option value="'+v.group_id+'">'+v.group_name_cn+'</option>';
        	});
        	html += '</select>';
        	$("#threeCatHtmlUpdate").html("");
        	$("#twoCatHtmlUpdate").html(html);
        	$("#nowLevelUpdate").val(selectId);
        }
    });
}

function getFinalCatUpdate(selectId){
	var selectId = $("#"+selectId).val();
	//alert(selectId);
	$.ajax({
        type: "post",
        dataType: "json",
        url: '/message/template/get-cat-top',
        data:{pid:selectId},
        success: function (json) {
        	$("#nowLevelUpdate").val("CatTwo");
    		html = "";
    		html += '<select style="height:200px;WIDTH:200px" size="10">';
        	$.each(json,function(k,v){
        		html += '<option value="'+v.group_id+'">'+v.group_name_cn+'</option>';
        	});
        	html += '</select>';
        	$("#threeCatHtmlUpdate").html(html);
        	$("#nowLevelUpdate").val(selectId);
        }
    });
}