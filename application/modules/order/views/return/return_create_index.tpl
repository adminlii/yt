<script type="text/javascript">
<!--
function openReturnDialog(refId){
    var w = windowWidth();
    var h = windowHeight();
    w = 800;
    h=600;
    var url = '/order/return/create/ref_id/'+refId;
    title = '退件处理';
    $.ajax({
        type: "post",
        dataType: "json",
        url: '/order/return/check-order-exist',
        data: {'ref_id':refId},
        success: function (json) {
            if(json.ask){
            	openIframeDialog(url,w,h,title);
            }else{
                alertTip(json.message);
            }
        }
    });
	
}
$(function(){
	$('.submitToSearch').click(function(){
	    var refId = $('#order_code').val();
	    if($.trim(refId)==''){
		    alertTip('<{t}>pls_input_order_code<{/t}>');
	        return;
	    }
	    openReturnDialog(refId);
	})
	$("#order_code").keyup(function (e) {
        var key = e.which;
        if (key == 13) {
        	$('.submitToSearch').click();
        }
    }).focus();
    
})
//-->
</script>
<style>
.submitReturnFalse{
	background-color: #FFFFFF;
}
.search-module-condition{
	margin-left: 15px;
}
body{
	background-color: #EEEEEE;
}
</style>
<div id="module-container">
	<form name="searchForm" class="submitReturnFalse" onsubmit='return false;' style='margin: 25px auto; width: 650px; border: 2px dashed #BACFC4; padding: 25px 30px;'>
		<h2 style="color:#E06B26;">敬请注意：</h2>
		<div class="search-module-condition">1、订单必须已经出库</div>
		<div class="search-module-condition">2、订单没有建立过退件或者建立的退件单已经已经作废</div>
		<div class="search-module-condition">3、退件数量不可大于订单产品数</div>
		<div class="search-module-condition">4、部分退件，未退产品请填写0</div>
			
		
		<div class="search-module-condition" style='padding:20px 0;'>
			<span style="color:#478FCA;" class=""><{t}>order_code<{/t}>：</span>
			<input type="text" name="order_code" id="order_code" class="input_text " placeholder='<{t}>order_code<{/t}>' style='width:200px;'/>
			&nbsp;&nbsp;
			<input type="button" value="<{t}>submit<{/t}>" class="baseBtn submitToSearch" />
		</div>
	</form>
</div>