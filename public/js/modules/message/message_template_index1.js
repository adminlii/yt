//页面加载，获取所有的分类的信息
$(document).ready(function(){
	getCatList();
	
	$(".keyToSearchTemplate").keyup(function (e) {
        var key = e.which;
        if (key == 13) {
            try {
            	submitForm();
            } catch (s) {
                alertTip(s);
            }
        }
    });
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
                html +="<dl><dt><a href='javascript:void(0)' onclick='showThisMail(this)'>";
        		html += v.group_name_cn;
                html +="</a></dt>";
                html+="<div style='display: none'>";
        		$.each(v.center, function(kk,vv){
        			html += "<dd onmouseout='hidenBi("+vv.group_id+");' onmouseover='showBi("+vv.group_id+");'>";
        			html += '<a href="javascript:void(0)"  onclick="showThisMail(this)">'+vv.group_name_cn;
                    html+='<img onclick="updateCat('+vv.group_id+');" id="imgBi'+vv.group_id+'" ' +'src="/images/sidebar/bi.jpg" ' +
                    'style="display:none;float:right;padding:3px;" />';
        			html += '</a></dd>';
                    html+="<div style='display: none'>";
        			$.each(vv.FinalCat,function(kkk, vvv){
        				html += "<p style='height:auto;padding-left: 5px;' onmouseout='hidenBi("+vvv.group_id+");' onmouseover='showBi("+vvv.group_id+");'>";
        				html += '<a class="catColor" id="catColor'+vvv.group_id+'" href="javascript:void(0); setAddTemplate('+vvv.group_id+')">'+vvv.group_name_cn;
        				html += '<img onclick="updateCat('+vvv.group_id+');" id="imgBi'+vvv.group_id+'" src="/images/sidebar/bi.jpg" style="display:none;float:right;padding:3px;" /></a>';
                        html += '</p>';
        			});
                    html += '</div>';
        		});
                html += '</div>';
        		html += '</div>';
        	});
        	$("#catHtml").html(html);
            $("#prompt").html("");
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

function showCurrtGroup(id){
    editGroupShow();
    //获取分类的数据
    $.ajax({
        type: "post",
        dataType: "json",
        url: '/message/template/get-group-info',
        data:{group_id:id},
        success: function (json) {
//              alert(json.data.group_name_cn);

            $("#group_name_cn_edit").val(json.data.group_name_cn);
            $("#group_name_en_edit").val(json.data.group_name_en);
            $("#group_note_edit").val(json.data.group_note);
            $("#platform_edit").val(json.data.platform);
            $("#groupid_edit").val(id);
//            $("#updateCatHtml").html(json.html);
//            $("#crumbs").html(json.crumbs);
        }
    });

}
/**
 * 修改分类
 */
function updateCurtGroup(){

    var group_name_cn = $("#group_name_cn_edit").val();
    var group_name_en = $("#group_name_en_edit").val();
    var group_note = $("#group_note_edit").val();
    var platform = $("#platform_edit").val();
    var nowGroupId = $("#groupid_edit").val();

    $.ajax({
        type: "post",
        dataType: "json",
        url: '/message/template/update-cat-click',
        data: {group_name_cn:group_name_cn,group_name_en:group_name_en,group_note:group_note,platform:platform,nowGroupId:nowGroupId},
        success: function (json) {
            if(json.state){
                alertTip(json.message);
                showAddCatHtml();
            }
        }
    });

}


function showBi(id){
//	$("#imgBi"+id).css('display','block');
}

function hidenBi(id){
//	$("#imgBi"+id).css('display','none');
}

/**
 * 设置模板的分类
 */
function setAddTemplate(groupid){
	$(".catColor").css("color","#666666");
	$("#catColor"+groupid).css({"color":"#008000","font-weight":"bold"});
	//$("#groupTemplateId").css("display","block");
	$("#setAddTemplate").val(groupid);
    $("#E3").val(groupid);
    $("#E1").val("");
    $("#E2").val("");
    getMessageTemplateList();
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
    $("#editGroup").css("display","none");
    $("#prompt").html("&nbsp;");


}
function updateStatus(tid,status){
	var tips = "确认该操作？";
	if(status == 0){
		tips = "确认让该消息模板“<span style='color:red;'>失效</span>”？";
	}else if(status == 2){
		tips = "确认<span style='color:red;'>审核通过</span>，该消息模板？";
	}
	alertConfirmTip("<span class='tip-warning-message'>"+ tips +"</span>",function(){
		$.ajax({
	        type: "post",
	        dataType: "json",
	        url: '/message/template/update-status',
	        data:{'tid':tid,'status':status},
	        success: function (json) {
	    		alertTip(json.message);
	    		if(json.ask){
	    			getMessageTemplateList();
	    		}
	        }
	    });
	});	
}

/**
 * 搜索按钮
 */
function submitForm(){
	var groupid = $("#E3").val();
	$("#catColor"+groupid).css({"color":"#666666","font-weight":"normal"});
	$("#E3").val("");
	$("#setAddTemplate").val("");
	getMessageTemplateList();
}

/**
 * 搜索模板list
 */
function getMessageTemplateList(){
	$("#table-module-list-data").myLoading();
	var E1 = $("#E1").val();
	var E2 = $("#E2").val();
    var E3 = $("#E3").val();
	
	$.ajax({
        type: "post",
        dataType: "json",
        url: '/message/template/get-list',
        data: {E1:E1,E2:E2,E3:E3},
        success: function (json) {
        	if(json == "false"){
        		if($("#E3").val() != ""){
        			html = "<tr class='table-module-b1'><td class='ec-center' colspan='8' style='color:red;'>该分类下还没有消息模板，请点击“添加模板”进行添加！</td></tr>";
        		}else{
        			html = "<tr class='table-module-b1'><td class='ec-center' colspan='8' align='center'>没有数据</td></tr>";
        		}
        	}else{
	        	html = "";
	        	var key = 0;
	        	$.each(json,function(k,v){
	        		html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
	        		key ++;
	        		//html += '<tr class="table-module-b1">';
	        		html += '<td class="ec-center">'+(k+1)+'</td>';
	        		html += '<td class="ellipsis" title="'+v.template_name+'">'+v.template_name+'</td>';
	        		html += '<td>'+v.template_short_name+'</td>';
	        		var template_type_title = "未定义";
	        		if(v.template_type){
	        			template_type_title = v.template_type == 0?"消息":"Email";
	        		}
	        		html += '<td>'+template_type_title+'</td>';
	        		html += '<td>'+((v.status != '2')?"<span style='color:red;'>"+v.status_title+"</span>":v.status_title)+'</td>';
	        		html += '<td class="ellipsis" title="'+v.group_name+'">'+v.group_name+'</td>';
	        		var template_note_title = (v.template_note?v.template_note:"");
	        		html += '<td class="ec-center ellipsis" title="'+template_note_title+'">'+template_note_title+'</td>';
	        		html += '<td class="ec-center" >';
	        		/*
	        		html += '<a href="javascript:editTemplateById('+v.template_id+')">编辑</a>&nbsp;&nbsp;';
	        		html += '|&nbsp;&nbsp;<a href="javascript:;" onclick="deleteTemplateById('+v.template_id+')">删除</a>';
	        		switch(v.status){
	        			case '0':
	        				//html += '|&nbsp;&nbsp;<a href="javascript:updateStatus('+v.template_id+')">删除</a>';
		        			break;
	        			case '1':
	        				html += ' |&nbsp;&nbsp;<a href="javascript:;" onclick="updateStatus('+v.template_id+',2)">审核通过</a>';
	        				html += ' |&nbsp;&nbsp;<a  href="javascript:;" onclick="updateStatus('+v.template_id+',0)"">失效</a>';
		        			break;
	        			case '2':
	        				html += ' |&nbsp;&nbsp;<a  href="javascript:;" onclick="updateStatus('+v.template_id+',0)"">失效</a>';
		        			break;
	        		}
	        		*/
		        		html += '<ul class="dropdown_nav">';
		        		html +=	'<li>';
		        		html +=		'<a href="javascript:editTemplateById('+v.template_id+');">编辑&nbsp;<strong style=""></strong></a>';
		        		html +=		'<ul class="sub_nav" data-width="70" data-float="right">';
		        		html +=			'<li ><a href="javascript:deleteTemplateById('+v.template_id+');">删除</a></li>';
		        		switch(v.status){
		        			case '0':
		        				html +=			'<li ><a href="javascript:updateStatus('+v.template_id+',2);">审核通过</a></li>';
		        				break;
		        			case '1':
		        				html +=			'<li ><a href="javascript:updateStatus('+v.template_id+',2);">审核通过</a></li>';
		    	        		html +=			'<li ><a href="javascript:updateStatus('+v.template_id+',0);">失效</a></li>';
			        			break;
		        			case '2':
		        				html +=			'<li ><a href="javascript:updateStatus('+v.template_id+',0);">失效</a></li>';
			        			break;
		        		}
		        		html +=		'</ul>';
		        		html +=	'</li>';
		        		html +='</ul>';
	        		
	        		html += '</td>';
	        		html += '</tr>';
	        	});
        	}
        	$("#table-module-list-data").html(html);
        	initOperate();
        }
    });
}
/**
 * 获取第二级
 */
function getTwoCat(selectId){
	var selectId = $("#"+selectId).val();
    showCurrtGroup(selectId);
   // $("#prompt").html("&nbsp;");
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
    showCurrtGroup(selectId);
    if(selectId==null){
	    alert("没有下一级");return;
    }

	$.ajax({
        type: "post",
        dataType: "json",
        url: '/message/template/get-cat-top',
        data:{pid:selectId},
        success: function (json) {
        	$("#nowLevel").val("CatTwo");
    		html = "";
    		html += '<select style="height:200px;WIDTH:200px" id="CatTwo" size="10" onclick="showGroupByThis(this);">';
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
function showGroupByThis(obj){
    var selectId = $(obj).val();
    showCurrtGroup(selectId);
}

/**
 * 添加分类的显示框
 */
function addCat(){
	$("#addCatText").css("display","block");
    $("#editGroup").css("display","none");
	var nowLevel = $("#nowLevel").val();
	var prompt = "";
	if(nowLevel == "CatTwo"){
		//添加第二级,获取第一级选中的分类
		var topCat = $("#topCat option:selected").text();
		var CatTwo = '  >  ' + $("#CatTwo option:selected").text();
	}else if(nowLevel == "topCat"){
	    //添加第三极，获取三级全部的分类
		var topCat = $("#topCat option:selected").text();
		var CatTwo = "";
	}else{
		prompt = "无分类";
	}
	
	prompt = (prompt)?prompt:topCat+CatTwo;
	$("#prompt").html(prompt);
}

/**
 * 编辑分类的显示框
 */
function editGroupShow(){
    $("#addCatText").css("display","none");
    $("#editGroup").css("display","block");
        //添加第二级,获取第一级选中的分类
        var topCat = $("#topCat option:selected").text();
        var CatTwo = '  >  ' + $("#CatTwo option:selected").text();

    var prompt = topCat+CatTwo;
    $("#currtGroup").html(prompt);
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
    if(nowTemplateId==''||nowTemplateId==null){
        nowTemplateId="0";
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
                    if(nowLevel==""||nowLevel==null){
                        //重新填充第一级
                        showAddCatHtml();
                    }else{
					    getFinalCat("CatTwo");
                    }
				}
				$("#group_name_cn").val("");
				$("#group_name_en").val("");
				$("#group_note").val("");
				getCatList();
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
 * 初始化编辑模板界面
 */
var te_id = '';
$(function(){
	$("#templateWrap").dialog({
        autoOpen: false,
        width: 820,
        height: 600,
        modal: true,
        show: "slide",
        position:'top',
        buttons: [
			{
			    text: 'Ok',
			    click: function () {
				   var param = $("#templateData").serialize();
				   //验证插入变量的正文内容
				   if(!checkTemplateOperate()){
					   return;
				   }
				   var this_= $(this);
				    $.ajax({
				        type: "post",
				        dataType: "json",
				        url:"/message/template/edit-template/te_id/"+te_id,
				        data: param,
				        success: function (json) {
					        alertTip(json.message);
				        	if(json.ask){
				        		this_.dialog("close");	
				        		getMessageTemplateList();						
				        	}
				        }
				    });
			        //$(this).dialog("close");
			    }
			}
			,{
			    text: 'Cancel',
			    click: function () {
			        $(this).dialog("close");
			    }
			}
        ],
        close: function () {
            
        }
    });
});
/**
 * 打开编辑模板
 */
function editTemplateById(id){
	te_id = id;
	var groupId = $("#setAddTemplate").val();
	if(groupId == ""){
		alertTip("<span class='tip-warning-message'>请先在“<b>消息模板</b>”中选择一个模板分类！</span>");
		return;
	}
	$.get('/message/template/edit-template/te_id/'+id+'/groupId/'+groupId,function(html){		
		$("#templateWrap").html(html);
		$("#templateWrap").dialog('open');
		$(".guild .lanTab").eq(0).click();
	});
	
}

/**
 * 验证模板操作符
 */
function checkTemplateOperate(){
	var element = $(".lanTab ");
	var lan = new Array(); 
	var content = new Array();
	element.each(function(i){
		lan[i] = $(this).find("a").html();
		content[i] =  $("#template_content_"+$(this).attr("key")).val();
	});
	
	var bol = true;   	//验证是否通过
	for(var i = 0; i < element.length; i++) {
		if(!MTO.checkMessageOperate(content[i])){
			bol = false;
		}
	}
	return bol;
}

function deleteTemplateById(id){
	te_id = id;
	alertConfirmTip("<span class='tip-warning-message'>确定要<span style='color:red;'>删除</span>该模板？</span>",function(){
		$.get('/message/template/delete-template/te_id/'+id,function(json){	
			if(json.ask){
				getMessageTemplateList();
			}
			alertTip(json.message);
		},'json');
	});	
	
	
}

/**
 * 添加分类的显示框
 */
function deleteGroup(){
    var nowGroupId = $("#groupid_edit").val();
    $.ajax({
        type: "post",
        dataType: "json",
        url: '/message/template/delete-Group',
        data: {nowGroupId:nowGroupId},
        success: function (json) {
            if(json.state){
                alertTip(json.message);
                showAddCatHtml();

            }
        }
    });


} 
var key = 1;
var editor = 0;
$(function() {
	$(".guild .lanTab").live('click',function(){
		$(".guild .lanTab").removeClass('act');
		$(".tagContent").hide();
		$(this).addClass('act');
		key = $(this).attr('key');
		$("#tabs-"+key).show();
		setTimeout(function(){
			editor = $("#template_content_"+key).xheditor({tools:'Fullscreen'});
//			alert(editor.getSource());//获取内容
		},500);
		
	});	

    $('#bianliang option').live('click',function(){
        var text = $(this).attr('value');
        //组装前后特殊符号
        text = MTO.liftBraces + text + MTO.rightBraces;
        editor.focus();    
		editor.pasteText(text);
		$('#bianliang').focus();
		//return false;
       });
});

function showThisMail(obj){

    var tmpObj= $(obj).parent().next("div").css("display");
    if(tmpObj=="none"){
        $(obj).parent().next("div").show();
        $(obj).css("background-image","url(/images/sidebar/bg_mail_menu01.gif)");

    }else{
        $(obj).parent().next("div").hide();
        $(obj).css("background-image","url(/images/sidebar/bg_mail_menu02.gif)");
    }

}

function showThis(obj){
    $(obj).show();
}

function closeThis(obj){
    $(obj).hide();
}