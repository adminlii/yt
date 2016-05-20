<div class='shipper_account'>
	<{foreach from=$submiters name=s item=s key=k}>
	<p>
		<label> <input type='radio' name='consignee[shipper_account]' class='shipper_account' value='<{$s.shipper_account}>'<{if $s.is_default}>checked<{/if}>> 
		<{$s.shipper_company}>&nbsp;&nbsp;&nbsp;&nbsp; 
		<{$s.shipper_countrycode}>&nbsp;&nbsp;&nbsp;&nbsp; 
		<{$s.shipper_province}> <{$s.shipper_city}> <{$s.shipper_street}>&nbsp;&nbsp;&nbsp;&nbsp; 
		<{$s.shipper_name}>&nbsp;&nbsp;&nbsp;&nbsp; 
		<{$s.shipper_telephone}>&nbsp;&nbsp;&nbsp;&nbsp;
		</label>
		<a onclick='submiterOp(this);' href='javascript:;' class='submiterOpBtn' shipper_account='<{$s.shipper_account}>'><{t}>修改信息<{/t}></a>
	</p>
	<{/foreach}>
</div>
<div>
	<a onclick='submiterOp(this);' href='javascript:;' style='font-weight: normal; font-size: 12px; line-height: 35px; margin: 0 0px;' class='submiterOpBtn' shipper_account=''><{t}>点击添加<{/t}></a>
</div>
<script type="text/javascript">
function submiterOp(obj){
	var shipper_account= $(obj).attr('shipper_account');
    var title = '';
    if(shipper_account){
        title = "<{t}>修改发件人<{/t}>";
    }else{
        title = "<{t}>新增发件人<{/t}>";
    }
    var date = new Date();
	var url = "/order/submiter/for-order?shipper_account="+shipper_account+"&t="+date.getMinutes();
	//alert(0);
    //leftMenu('submiter-edit-'+shipper_account,title,url); 
    openIframeDialogNew(url,800 , 500,title,'submiter-edit-'+shipper_account,0,0);
}

$(function() {



});
</script>
