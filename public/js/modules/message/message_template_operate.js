
var data = {};
EZ.url = '/message/message-template-operate/';
EZ.getListData = function (json) {
	var html = '';	
	data = json.data;
	$.each(json.data, function (key, val) {
		html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1' >" : "<tr class='table-module-b2'  >";
		html += "<td class='ec-center'>" +(key+1) + "</td>";

		html += "<td class='level_"+val.level+"'>" + val.operate_code + "</td>";
		html += "<td >" + val.operate_name_cn + "</td>";
		html += "<td >" + val.operate_name_en + "</td>";
		html += "<td >" + val.operate_note + "</td>";       

		html += "<td>";
		html += "<a href='javascript:;' class='editBtn' operate_id='"+val.operate_id+"'>编辑</a>";
		html += "&nbsp;&nbsp;<a href='javascript:;' class='deleteBtn' operate_id='"+val.operate_id+"'>删除</a>";
		html += "</td>";
		html += "</tr>";
	});
	return html;
}

$(function(){
	$("#edit_div").dialog({
		autoOpen: false,
		width: 500,
		maxHeight: 500,
		modal: true,
		show: "slide",
		position: 'top',
		buttons: [ 
		          {
		        	  text: 'Ok',
		        	  click: function () {
		        		  var param = $('#editForm').serialize();
		        		  var this_ = $(this);
		        		  $.ajax({
		        			  type: "POST",
		        			  url: "/message/message-template-operate/edit",
		        			  data: param,
		        			  dataType:'json',
		        			  success: function(json){		     		 
		        				  var html = '';	        		    	
		        				  html+=json.message+"<br/>";
		        				  alertTip(html,500);
		        				  if(json.ask){ 
		        					  this_.dialog('close');    
		        					  initData(paginationCurrentPage-1);								
		        				  }
		        			  }
		        		  });	
		        	  }
		          },
		          {
		        	  text: 'Close',
		        	  click: function () {
		        		  $(this).dialog("close");
		        	  }
		          }
		          ],
		          close: function () {

		          },
		          open:function(){

		          }
	});

	$("#addBtn").click(function(){
		$('#editForm input').val('');
		$("#edit_div").dialog('open');
	});

	$('.editBtn').live('click',function(){
		var operate_id = $(this).attr('operate_id');
		//$('#operate_id').val(operate_id);
		$.each(data[operate_id],function(k,v){
			$("#"+k).val(v);
		})
		$("#edit_div").dialog('open');
	});


	$('.deleteBtn').live('click',function(){
		var operate_id = $(this).attr('operate_id');
		$('<div title="Note(Esc)" id="dialog-auto-alert-tip"><p align="">确定要删除吗?</p></div>').dialog({
			autoOpen: true,
			width: 300,
			maxHeight: 300,
			modal: true,
			show: "slide",
			buttons: [
			          {
			        	  text: 'Ok',
			        	  click: function () {

			        		  var this_ = $(this);
			        		  $.ajax({
			        			  type: "POST",
			        			  url: "/message/message-template-operate/delete",
			        			  data: {'operate_id':operate_id},
			        			  dataType:'json',
			        			  success: function(json){		   
			        				  this_.dialog('close');       		 
			        				  var html = '';	        		    	
			        				  html+=json.message+"<br/>";
			        				  alertTip(html,500);
			        				  if(json.ask){
			        					  initData(paginationCurrentPage-1);								
			        				  }
			        			  }
			        		  });	
			        	  }
			          },
			          {
			        	  text: 'Close',
			        	  click: function () {
			        		  $(this).dialog("close");
			        	  }
			          }
			          ],
			          close: function () {
			        	  $(this).detach();
			          }
		});

	});
})