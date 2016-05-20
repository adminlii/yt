var LODOP;	
$(function(){
	var max = 500;
	$('.print-batch').text('批量打印配货单');
	LODOP=getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM')); 
	$('.print').click(function(){
		var id = $(this).attr('id');
		//获取js文件，然后运行方法
		$.getScript('/common/print-order-lodop/print/ref_id/301077531957-0/paper/'+id, function(){
			print_lodop(); 
		});    		
	});
	$('.print-batch').click(function(){
		var param = {};
		param.paper = 'pickup-batch';
		var ref_id = [];
		max = $('#num').val();
		if($.trim(max)==''){
			max=10;
		}
		max = parseInt(max);
		if(!max){
			max = 10;
		}

		for(var a=1;a<=max;a++){
			ref_id.push(a+'');    			
		}
		param.ref_id = ref_id;
		$('#wrap').dialog('open');
		$('.ui-dialog-titlebar-close',$('#wrap').parent()).hide();//隐藏关闭按钮
		$('#wrap').dialog( "option", "title",'订单打印中，请稍候。。。' );
		$.ajax({
			type: "POST",
			async: false,//同步
			url: "/common/print-order-lodop/print-batch/",
			data: param,
			dataType:'script',
			success: function(msg){
				print_lodop(); 
				$('.ui-dialog-titlebar-close',$('#wrap').parent()).show();//显示关闭按钮
				$('#wrap').dialog( "option", "title",'打印完成' );
			}
		});

	});

	$('.print-batch-one-piece').click(function(){
		var param = {};
		param.paper = 'pickup-batch';
		var ref_id = [];
		max = $('#num').val();
		if($.trim(max)==''){
			max=10;
		}
		max = parseInt(max);
		if(!max){
			max = 10;
		}

		for(var a=1;a<=max;a++){
			ref_id.push(a+'');    			
		}
		param.ref_id = ref_id;
		$('#wrap').dialog('open');
		$('.ui-dialog-titlebar-close',$('#wrap').parent()).hide();//隐藏关闭按钮
		$('#wrap').dialog( "option", "title",'订单打印中，请稍候。。。' );
		setTimeout(function(){
			$.ajax({
				type: "POST",
				async: false,//同步
				url: "/common/print-order-lodop/print-batch-one-piece/",
				data: param,
				dataType:'script',
				success: function(msg){
					print_lodop(); 
					$('.ui-dialog-titlebar-close',$('#wrap').parent()).show();//显示关闭按钮
					$('#wrap').dialog( "option", "title",'打印完成' );
				}
			}); 
		},500);
		//$('#wrap').dialog('close');

	});

	$('.print-batch-mult-piece').click(function(){
		var param = {};
		param.paper = 'pickup-batch';
		var ref_id = [];
		max = $('#num').val();
		if($.trim(max)==''){
			max=10;
		}
		max = parseInt(max);
		if(!max){
			max = 10;
		}

		for(var a=1;a<=max;a++){
			ref_id.push(a+'');    			
		}
		param.ref_id = ref_id;
		$('#wrap').dialog('open');
		$('.ui-dialog-titlebar-close',$('#wrap').parent()).hide();//隐藏关闭按钮
		$('#wrap').dialog( "option", "title",'订单打印中，请稍候。。。' );
		setTimeout(function(){
			$.ajax({
				type: "POST",
				async: false,//同步
				url: "/common/print-order-lodop/print-batch-mult-piece/",
				data: param,
				dataType:'script',
				success: function(msg){
					print_lodop(); 
					$('.ui-dialog-titlebar-close',$('#wrap').parent()).show();//显示关闭按钮
					$('#wrap').dialog( "option", "title",'订单打印完成' );
				}
			});  
		},500);


	});
	$('.setup').click(function(){
		var id = $(this).attr('id');
		//获取js文件，然后运行方法
		$.getScript('/common/print-order-lodop/print/ref_id/301077531957-0/paper/'+id, function(){
			setup_lodop(); 
		});
	});
	$('.preview').click(function(){
		var id = $(this).attr('id');
		//获取js文件，然后运行方法
		$.getScript('/common/print-order-lodop/print/ref_id/301077531957-0/paper/'+id, function(){
			preview_lodop(); 
		});
	});
	$('#wrap').dialog({
		autoOpen: false,
		width: 800,
		maxHeight: 500,
		modal: true,
		show: "slide",
		position:'top',
		closeOnEscape:false,//关闭Esc快捷按钮事件
		buttons: [

		          ],
		          close: function () {

		          },
		          open:function(){
		        	  $(this).html('');
		          }
	});
})