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
            url: "/auth/user/get-right-tms",
            data: {
                'paramId': uid
            },
            success: function (json) {
                if (isJson(json) && json.state == '1') {
                    var data = json.data; 
                    var html = ''; 
                    $.each(data, function (k, v) {
                        var menu = v.menu;
                        var right = v.right;
                        
                         
                            html += "<div class='module'>";
                            html += '<label style="cursor: pointer;" ><input type="checkbox" onclick="checkAll(this)" ref="checkbox' + menu.um_id + '"><span class="module-title"  ref="' + menu.um_id + '" >&nbsp;&nbsp;' + menu.um_title + "</span></label>";
                            html += "</div>";
                            html += "<div class='actionBox action_" + menu.um_id + "'>";
                        	$.each(right,function(kk,vv){
                        		html += "<div class='actionDiv'>";
    	                        if (vv.visiable==1) {
    	                            html += "<label style='cursor: pointer;' ><input type='checkbox' value='" + vv.ur_id + "' name='actionId[" + vv.ur_id + "]' checked='checked' class='actionId checkbox" + menu.um_id + "' /><span class='checked'>" + vv.ur_name + "</span></label>";
    	                        } else {
    	                            html += "<label style='cursor: pointer;' ><input type='checkbox' value='" + vv.ur_id + "' name='actionId[" + vv.ur_id + "]' class='actionId checkbox" + menu.um_id + "' />" + vv.ur_name + "</label>";
    	                        }
    	                        html += "</div>"; 

                            })
                       		
                            html += "</div>"; 
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
            url: "/auth/user/edit-right",
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
	margin-bottom: 4px;
}

.actionBox div {
	float: left;
	width: 32%;
}

.module-title {
	cursor: pointer;
	color: #FFFFFF;
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