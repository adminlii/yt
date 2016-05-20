$(function(){
	$('#customerReport').change(function(){
		var report_id = $(this).val();
		if(!report_id){
			return;
		}
		$.ajax({
			type : "POST",
			url : "/order/order-template/get-template-form",
			data : {report_id:report_id},
			dataType : 'html',
			success : function(html) {
				$('#edit_wrap').html(html);
			}
		});
	});
	$('#customerReport').change();

	$('#deleteTemplateBtn').click(function(){
		jConfirm('<{t}>are_you_sure<{/t}>', '<{t}>删除模板<{/t}>', function(r) {
			if(r){
				var report_id = $('#customerReport').val();				
				$.ajax({
					type : "POST",
					url : "/order/order-template/delete",
					data : {report_id:report_id},
					dataType : 'json',
					success : function(json) {
						var html = json.message;
						if(json.ask){
							$('#edit_wrap').html(''); 
							$('#customerReport option:selected').remove();
							$('#customerReport').change();
						}else{
							if(json.err){
								html+="<ul style='padding:0 25px;list-style-type:decimal;'>";
								$.each(json.err,function(k,v){
									html+="<li>"+v+"</li>";
								})
								html+="</ul>";
							}
						}
						alertTip(html);
					}
				});
			}
		});	

	})
})