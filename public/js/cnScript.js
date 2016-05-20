/**
 * 
 * @authors Your Name (you@example.org)
 * @date    2016-05-05 10:40:35
 * @version $Id$
 */


$(document).ready(function() {
	var nice = $("html").niceScroll();  
	
    
    $("#boxscroll").niceScroll({cursorborder:"",cursorcolor:"#0085fc"}); 
    $("#boxscroll2").niceScroll({cursorborder:"",cursorcolor:"#0085fc"});


    $(".head .userhead").click(function(){

		$(this).find('.openWindw').stop().slideToggle("fast");
	});

		
    
  });


 $(function () {
			$(".userhead").click(function(){
			if($(this).hasClass("on")){
				$(this).removeClass("on");
			}else{
				$(this).addClass("on").siblings(".userhead").removeClass("on");
			}
	})

  });



//$(document).ready(function() {
//		$("#calendar1").bootstrapDatepickr({date_format: "d-m-Y"});
//	});

// 新增表单
$(function () {
	var show_count = 20;   //要显示的条数
	var count = 1;    //递增的开始值，这里是你的ID
	$("#btn_addtr").click(function () {

		var length = $("#dynamicTable tbody tr").length;
		//alert(length);
		if (length < show_count)    //点击时候，如果当前的数字小于递增结束的条件
		{
			$("#tab11 tbody tr").clone().appendTo("#dynamicTable tbody");   //在表格后面添加一行
			changeIndex();//更新行号
		}
	});


});
function changeIndex() {
	var i = 1;
	$("#dynamicTable tbody tr").each(function () { //循环tab tbody下的tr
		$(this).find("input[name='NO']").val(i++);//更新行号
	});
}

function deltr(opp) {
	var length = $("#dynamicTable tbody tr").length;
	//alert(length);
	if (length <= 1) {
		alert("至少保留一行");
	} else {
		$(opp).parent().parent().remove();//移除当前行
		changeIndex();
	}
}