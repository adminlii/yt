<script type="text/javascript">
    $(function () {
        $("#edit-permission-dialog").dialog({
            autoOpen: false,
            modal: true,
            width: 700,
            height: 500,
            show: "slide",
            buttons: {
                'Ok(确定)': function () {
                    editData();
                },
                'Cancel(取消)': function () {
                    $(this).dialog('close');
                }
            },
            close: function () {
                $(this).dialog('close');
            }
        });

        $("#view-permission-dialog").dialog({
            autoOpen: false,
            modal: true,
            width: 700,
            height: 500,
            show: "slide",
            buttons: {
                'Close(关闭)': function () {
                    $(this).dialog('close');
                }
            },
            close: function () {
                $(this).dialog('close');
            }
        });

        $("input[type='checkbox']").live('click',function(){
			var s = $(this).is(":checked");
			if(s){
				$(this).next().removeClass("unChecked");
				$(this).next().addClass("checked");
			}else{
				$(this).next().removeClass("checked");
				$(this).next().addClass("unChecked");
			}
        });
    });
    //修改权限
    function bindAction(uid) {
        $("#permissionId").val(uid);
        $.ajax({
            type: "POST",
            async: false,
            dataType: "json",
            url: "/auth/position/get-right",
            data: {
                'paramId': uid
            },
            success: function (json) {
                if (isJson(json) && json.state == '1') {
                	processPostitonData(json,1);
                }
            }
        });
    }
	//查看权限
    function viewBindAction(uid){
    	$.ajax({
        	type: "POST",
        	async: false,
        	dataType: "json",
	        url: "/auth/position/get-right",
    	    data: {
            'paramId': uid
        	},
        	success: function (json) {
            	if (isJson(json) && json.state == '1') {
            		processPostitonData(json,0);
            	}
        	}
    	});
    }

    function processPostitonData(json,type){
    	var data = json.data;
        var ids = json.ids;
        var html = '';
        var menu = '';
        $.each(data, function (k, v) {
            var disabled_html = 'disabled="disabled" title="抱歉，您只能查看权限"';
			var append_type_html = (type == 0)?disabled_html:'';
            disabled_html
            if (v.menu_id != menu) {
                if (v.menu_id != menu && menu != '') {
                    html += "</div>";
                }
                menu = v.menu_id;
                html += "<div class='module'>";
                html += '<label style="cursor: pointer;" '+append_type_html+'><input type="checkbox" onclick="checkAll(this)" ref="checkbox' + menu + '" '+append_type_html+'><span class="module-title"  ref="' + menu + '" >&nbsp;&nbsp;' + v.menu + "</span></label>";
                html += "</div>";
                html += "<div class='actionBox action_" + menu + "'>";
            }
            html += "<div class='actionDiv'>";
            if (ids[v.id]) {
                html += "<label style='cursor: pointer;' "+append_type_html+"><input type='checkbox' value='" + v.id + "' name='actionId[" + v.id + "]' checked='checked' class='actionId checkbox" + menu + "' "+append_type_html+"/><span class='checked'>" + v.title + "</span></label>";
            } else {
                html += "<label style='cursor: pointer;' "+append_type_html+"><input type='checkbox' value='" + v.id + "' name='actionId[" + v.id + "]' class='actionId checkbox" + menu + "' "+append_type_html+"/><span>" + v.title + "</span></label>";
            }
            html += "</div>";
            if (k == (data.length - 1)) {
                html += "</div>";
            }
        });
        var html_container = null;
        var dialog_container = null;
        if(type == 1){
        	html_container = $("#edit-action-list");
        	dialog_container = $("#edit-permission-dialog");
        }else{
        	html_container = $("#view-action-list");
        	dialog_container = $("#view-permission-dialog");
        }
        
        html_container.html(html);
        dialog_container.dialog("open");
        
        $("#edit-action-list > .module").click(function (event) {
        	var element = event.target;
            if(element.tagName !='DIV'){
				return;
            }
            var module = $(this).find(".module-title");
            var isClass = module.attr('ref');
            var obj = $(".action_" + isClass);
            if (obj.css('display') == 'none') {
                obj.show();
            } else {
                obj.hide();
            }
        });
    }

    function checkAll(obj) {
        var cname = $(obj).attr('ref');
        if ($(obj).attr("checked") == 'checked') {
            $('.' + cname).attr('checked', 'true');

        } else {
            $('.' + cname).attr('checked', false);
        }
    }
    function editData() {
        var uid = $('#permissionId').val();
        if (uid == '' || uid == '0') {
            alertTip('err');
            return;
        }
        if ($(".actionId:checked").length < 1) {
            alertTip('<{t}>pleaseOne<{/t}>');
            return;
        }
        $.ajax({
            type: "POST",
            async: false,
            dataType: "json",
            url: "/auth/position/edit-right",
            data: $("#editRightForm").serializeArray(),
            success: function (json) {
                if (isJson(json)) {
                    if (json.state) {
                        $("#permission-dialog").dialog("close");
                    }
                    alertTip(json.message);
                }

            }
        });

    }
</script>
<style type="text/css">
.module {
	/*
	clear: both;
	width: 100%;
	height: 28px;
	line-height: 28px;
	background: url("/images/pack/bg_manage_form01.gif") repeat-x scroll 0 0 transparent;
	border-bottom: 1px solid #ccc;
	*/
	background-color: #6FB3E0;
    border-bottom: 1px solid #CCCCCC;
    border-radius: 4px;
    clear: both;
    height: 28px;
    line-height: 28px;
    margin-bottom: 0;
    width: 100%;
    margin-bottom: 2px;
}
.actionBox {
	height: auto;
	overflow: hidden;
	padding-left: 3%;
}

.actionBox div {
	float: left;
	width: 32%;
}

.module-title {
	cursor: pointer;
}

.checked {
	color: #DF920C;
}

.unChecked{
	color:#68474E;
}
</style>
<div style="display: none" id="edit-permission-dialog" title="编辑权限">
	<form id="editRightForm" name="editRightForm" class="submitReturnFalse">
		<input type="hidden" name="permissionId" id="permissionId" value="" />
		<div id="edit-action-list"></div>
	</form>
</div>
<div style="display: none" id="view-permission-dialog" title="查看权限">
	<div id="view-action-list"></div>
</div>