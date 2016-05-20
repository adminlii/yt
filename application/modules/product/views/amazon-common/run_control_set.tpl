
<script type="text/javascript"> 
$(function(){
	$('.submitToSearch').click(function(){

        var key = prompt('该功能为系统设置，对整个系统影响很大，如需操作，请输入解锁码','');
        if(!key||key==''){
            return;
        }
	    var param = $('#editDataForm').serialize();
	    param+='&key='+key;
	    $.ajax({
            type: "post",
            async: false,
            dataType: "json",
            url: '/product/amazon-common/list/',
            data: param,
            success: function (json) {
                alertTip(json.message);
            }
        });
	})

	$('.user_account').change(function(){
		var clazz = $('option:selected',this).attr('platform');
		//alert(clazz);
	    $('.method option').hide();
	    $('.method .'+clazz).show();
	    $('.method .all').show();
	}).change();
})
</script>
<style>
<!--
.search-module-condition{line-height:35px;padding:10px ;}
.searchFilterText{font-weight:bold;}
-->
</style>
<div id="module-container" style=''>
	<form class="submitReturnFalse" name="editDataForm" id="editDataForm">
		<div class="search-module-condition">
			<span style="width: 125px;" class="searchFilterText"><{t}>user_account<{/t}>：</span>
			<select name="user_account" class='user_account'>
				<{foreach from=$user_account_arr name=ob item=ob}>
				<option value='[<{$ob.platform}>]<{$ob.user_account}>' platform='<{$ob.platform}>'><{$ob.user_account}>(<{$ob.platform_user_name}>)[<{$ob.platform}>]</option>
				<{/foreach}>
			</select>
		</div>
		<div class="search-module-condition">
			<span style="width: 125px;" class="searchFilterText"><{t}>任务名<{/t}>：</span>
			<select name="method" class='method'>
				<option value='' class='all'><{t}>all<{/t}></option>
				<{foreach from=$methods name=ob item=ob}>
				<option value='<{$ob.method}>' class='<{$ob.clazz}>'><{$ob.method}></option>
				<{/foreach}>
			</select>
		</div>
		<div class="search-module-condition">
			<span style="width: 125px;" class="searchFilterText"><{t}>status<{/t}>：</span>
			<select name="status">
				<{foreach from=$statusArr name=ob item=ob key=k}>
				<option value='<{$k}>' class=''><{$ob}></option>
				<{/foreach}>
			</select>
		</div>
		<div class="search-module-condition">
			<span style="width: 125px;" class="searchFilterText">&nbsp;</span>
			<input type="button" class="baseBtn submitToSearch" value="<{t}>submit<{/t}>">
		</div>
	</form>
</div>