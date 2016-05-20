<script type="text/javascript">

	$(function(){
	    $.ajax({
			   type: "POST",
			   url: "/common/region/get-region",
			   data: {'pid':'1'},
			   dataType:'json',
			   success: function(json){
			   		var html = '';
	    			var def = $('#c_level_0').attr('default');
					$.each(json,function(k,v){
						var select = '';
						if(def==v.region_id){
							select = 'selected';
						}
						html+='<option value="'+v.region_id+'" '+select+'>'+v.region_name+'</option>';		
					});
					//alert(html);
					$('#c_level_0').html(html);
					$('#c_level_1').html('');
					$('#c_level_2').html('');
					//if(def!=''){
						$('#c_level_0').change();
					//}
			   }
			});	
			
	    $('#c_level_0').change(function(){
	    	var pid = $(this).val();
	    	var param = {'pid':pid};
	    	$.ajax({
			   type: "POST",
			   url: "/common/region/get-region",
			   data: param,
			   dataType:'json',
			   success: function(json){
			   		var html = '';
    				var def = $('#c_level_1').attr('default');
					$.each(json,function(k,v){
						var select = '';
						if(def==v.region_id){
							select = 'selected';
						}
						html+='<option value="'+v.region_id+'" '+select+'>'+v.region_name+'</option>';		
						//html+='<option value="'+v.region_id+'">'+v.region_name+'</option>';					
					});
					$('#c_level_1').html(html);
					$('#c_level_2').html('');
					//if(def!=''){
						$('#c_level_1').change();
					//}
			   }
			});	
	    });
	    $('#c_level_1').change(function(){
	    	var pid = $(this).val();
	    	var param = {'pid':pid};
	    	$.ajax({
			   type: "POST",
			   url: "/common/region/get-region",
			   data: param,
			   dataType:'json',
			   success: function(json){
			   		var html = '';
    				var def = $('#c_level_2').attr('default');
					$.each(json,function(k,v){
						var select = '';
						if(def==v.region_id){
							select = 'selected';
						}
						html+='<option value="'+v.region_id+'" '+select+'>'+v.region_name+'</option>';					
					});
					$('#c_level_2').html(html);
			   }
			});	
	    });
	})
</script>

<select id='c_level_0' name='cat_id0' class='c_level input_select'
	default='<{if isset($product)}><{$product.cat_id0}><{/if}>'></select>
<select id='c_level_1' name='cat_id1' class='c_level input_select'
	default='<{if isset($product)}><{$product.cat_id1}><{/if}>'></select>
<select id='c_level_2' name='cat_id2' class='c_level input_select'
	default='<{if isset($product)}><{$product.cat_id2}><{/if}>'></select>
