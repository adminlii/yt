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
        		html += '<div style="background:url(/images/sidebar/bg_mail_menu01.gif) no-repeat scroll 1px 2px transparent;padding-left:15px;">';
        		html += v.group_name_cn;
        		
        		$.each(v.center, function(kk,vv){
        			html += '<div onclick="addTemplateCatId('+vv.group_id+',\''+vv.group_name_cn+'\');" style="background:url(/images/sidebar/bg_mail_menu01.gif) no-repeat scroll 1px 2px transparent;padding-left:15px;margin-top:5px;">';
        			html += '<a hre="javascript:void(0);">'+vv.group_name_cn+'</a>';
        			
        			$.each(vv.template,function(kkk, vvv){
        				html += '<div style="padding-left:15px;background:url(/images/sidebar/bg_mail_menu01.gif) no-repeat scroll 1px 2px transparent;">'+vvv.template_title+'</div>';
        			});
        			html += '</div>';
        		});
        		html += '</div>';
        	});
        	$("#catHtml").html(html);
        }
    });
}

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
        	html = "";
        	$.each(json,function(k,v){
        		html += '<tr class="table-module-b1">';
        		html += '<td>'+(k+1)+'</td>';
        		html += '<td class="ec-center">'+v.template_title+'</td>';
        		html += '<td>'+v.template_name+'</td>';
        		html += '<td>'+v.template_type+'</td>';
        		html += '<td><a href="javascript:editTemplateById('+v.template_id+')">编辑</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:deleteTemplateById('+v.template_id+')">删除</a></td>';
        		html += '</tr>';
        		$("#table-module-list-data").html(html);
        	});
        }
    });
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
                getCatList();
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


function editCatTwo(){
	var groupNameCn = $("#groupNameCn").val();
	var groupNameEn = $("#groupNameEn").val();
	var groupNote = $("#groupNote").val();
	var gid = $("#gid").val();
	var platform = $("#platform").val();
	if(groupNameCn == '' || platform == ''){
		alertTip("请核对信息!");
		return false;
	}
	$.ajax({
        type: "post",
        dataType: "json",
        url: '/message/template/edit-cat',
        data: {gid:gid,groupNameCn:groupNameCn,groupNameEn:groupNameEn,groupNote:groupNote,platform:platform},
        success: function (json) {
        	if (json.state) {
        		alertTip('<p style="text-align:left;font-size: 14px;color: red;font-weight: bold;">修改成功</p>');
        		location.href = '/message/Template/list';
        	}
        }
    });
}

function showAddCat(){
	
	$("#addCatHtml").css("display","block");
}

/*点击确认添加，将客户填写的信息加入至数据库*/
function addCatTwo(){
	var groupNameCn = $("#group_name_cn").val();
	var groupNameEn = $("#group_name_en").val();
	var groupNote = $("#group_note").val();
	var platform = $("#platform").val();
	var pid = $("input[name='catOne']:checked").val();
	
	if(groupNameCn == ''){
		$(".msg-1").html("不能为空");
		$("#group_name_cn").focus();
		return false;
	}
	
	if(groupNameEn == ''){
		$(".msg-2").html("不能为空");
		$("#group_name_en").focus();
		return false;
	}
	
	if(groupNote == ''){
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
        data: {groupNameCn:groupNameCn,groupNameEn:groupNameEn,groupNote:groupNote,platform:platform,pid:pid},
        success: function (json) {
        	if (json.state) {
        		alertTip('<p style="text-align:left;font-size: 14px;color: red;font-weight: bold;">添加成功</p>');
        		getCatList();
        	}
        }
    });
    
}

function editTemplateById(id){
	$("#addTemplateHtml").css("display","block");
	$("#add_t").css("display","none");
	$.ajax({
        type: "post",
        dataType: "json",
        url: '/message/template/edit-template-by-id',
        data: {id:id},
        success: function (json) {
        	$("#template_title").val(json.template_title);
        	$("#template_name").val(json.template_name);
        	$("#template_note").val(json.template_note);
        	$("#template_type").val(json.template_type);
        	$("#template_content").val(json['content'].template_content);
        	$("#template_group_name").val(json.group_name);
        	$("#add_t").css("display","none");
        	$("#update_t").css("display","block");
        	$("#te_id").val(json.template_id);
        }
    });
}

function addTemplateCatId(id,name){
	$("#addTemplateHtml").css("display","block");
	$("#add_t").css("display","block");
    $("#update_t").css("display","none");
	$("#template_group_name").val(name);
	$("#template_group_id").val(id);
}


