<script type="text/javascript">
    $(function () {
        $("#permission-dialog").dialog({
            autoOpen: false,
            modal: true,
            width: 700,
            height: 500,
            show: "slide",
            buttons: {
                'Ok': function () {
                    editData();
                },
                'Cancel': function () {
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
    function bindAction(uid) {
        $("#permissionId").val(uid);
        $.ajax({
            type: "POST",
            async: false,
            dataType: "json",
            url: "/auth/user-right/get-action",
            data: {
                'paramId': uid
            },
            success: function (json) {
                if (isJson(json) && json.state == '1') {
                    var data = json.data;
                    var ids = json.ids;
                    var html = '';
                    var module = '';
                    $.each(data, function (k, v) {
                        if (v.module != module) {
                            if (v.module != module && module != '') {
                                html += "</div>";
                            }
                            module = v.module;
                            html += "<div class='module'>";
                            html += '<label style="cursor: pointer;" ><input type="checkbox" onclick="checkAll(this)" ref="checkbox' + module + '"><span class="module-title"  ref="' + module + '" >&nbsp;&nbsp;Module&nbsp;:&nbsp;' + module + "</span></label>";
                            html += "</div>";
                            html += "<div class='actionBox action_" + module + "'>";
                        }
                        html += "<div class='actionDiv'>";
                        if (ids[v.id]) {
                            html += "<label style='cursor: pointer;' ><input type='checkbox' value='" + v.id + "' name='actionId[" + v.id + "]' checked='checked' class='actionId checkbox module-title" + module + "' /><span class='checked'>" + v.title + "</span></label>";
                        } else {
                            html += "<label style='cursor: pointer;' ><input type='checkbox' value='" + v.id + "' name='actionId[" + v.id + "]' class='actionId checkbox" + module + " module-title' /><span>" + v.title + "</span></label>";
                        }
                        html += "</div>";
                        if (k == (data.length - 1)) {
                            html += "</div>";
                        }
                    });
                    $("#action-list").html(html);
                    $("#permission-dialog").dialog("open");
                    

                    $("#action-list > .module").click(function (event) {
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
            url: "/auth/user-right/edit-right",
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
<div style="display: none" id="permission-dialog" title="Permission">
	<form id="editRightForm" name="editRightForm" class="submitReturnFalse">
		<input type="hidden" name="permissionId" id="permissionId" value="" />
		<div id="action-list"></div>
	</form>
</div>
