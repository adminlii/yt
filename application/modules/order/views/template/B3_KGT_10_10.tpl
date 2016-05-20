<!-- 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
         -->
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <script type="text/javascript" src="/js/jquery-1.8.3.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/css/label/bgd3.css?20140611">
    <link rel="stylesheet" type="text/css" href="/css/label/table2.css?20140611">
    <link rel="stylesheet" type="text/css" href="/css/label/styleBaoguan.css?20140611">
    <title>WMS-KGT</title>
</head>
<body>
<style>
    <!--
    body {
        margin: 0px; padding:0px;
    }
	.tabBq {
	    border: 1px dashed #cccccc;
	    table-layout: fixed;
	    width: 9.8cm;
	    word-wrap: break-word;
	}
    -->
    
</style>
<script type="text/javascript">
$(function(){
    $(".inputcheckbox").each(function(k,v){
        if( $(this).val()=='5'){
            $(this).attr("checked",true);
        }else{
            $(this).attr("checked",false);
        }
    });
});
</script>
<!-- 地址标签 -->
<table cellpadding="0" cellspacing="0" class="tabBq" style="height:1.5cm;">
	<!-- 新增标题 -->
	<tr>
		<td style="font-size: 7px; border: solid #000 1px; background-color: #ffffff;border-color: #000000;height: 0.8cm;" colspan="2" >
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td style="width: 25%;" align="center">
						<img src="/images/label/post.bmp" style="width:75px;"/><br/>
						<div style="font-size: 12px;font-weight: bold;">邮&nbsp;&nbsp;2113</div>
					</td>
					<td  align="center">
						<div style="width:100%;height:30px;overflow:hidden;margin-bottom:3px;padding-top: 2px;">
							<img src="/default/index/barcode/code/<{$data.ship.tracking_number}>"/>
						</div>
						<div style="width:100%;font-size: 12px;font-weight: bold;">
							<b><{$data.ship.tracking_number}></b>
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<!-- 新增协议客户 
	<tr>
		<td style="font-size: 10px; border: solid #000 1px;height: 0.8cm ;" colspan="2" >
			协议客户:<span style="font-family: 黑体;">恒光(44190002159000)</span>
		</td>
	</tr>
	-->
</table>
<table cellpadding="0" cellspacing="0" class="tabBq" style="height:2.0cm;">
	<tr>
		<td valign="top" style=" border: solid #000 1px;  width: 7.5cm; padding: 0; height:2.6cm;font-size:11px;" align="left">
			<table width="100%" height="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td valign="top" style="font-weight:bold;width:0.9cm">
							<b>To:</b>
						</td>
						<td valign="top" style="width:6.5cm;font-size:11px;">
                            <{$data.address.oab_firstname}>&nbsp;<{$data.address.oab_lastname}><br>
					        <{if $data.address.oab_street_address1!=''}><{$data.address.oab_street_address1}><br/><{/if}>
		                    <{if $data.address.oab_street_address2!=''}><{$data.address.oab_street_address2}><br/><{/if}>
		                    <{if $data.address.oab_city!=''}>
		                        <{$data.address.oab_city}>
		                        <{if $data.address.oab_state==''}>
		                            &nbsp;<{$data.address.oab_postcode}>
		                        <{/if}>
		                        <br/>
		                    <{/if}>
		                    <{if $data.address.oab_state!=''}>
		                        <{$data.address.oab_state}>&nbsp;<{$data.address.oab_postcode}>
		                        <br/>
		                    <{/if}>
		                    <strong><{$data.address.country.country_name_en}></strong> <{$data.address.country.country_name}>
						</td>
						
					</tr>
			</table>
		</td>
	</tr>	
	<tr>
		<td valign="bottom" style="border-left: solid #000 1px;border-right: solid #000 1px; border-bottom:solid #000 1px;  width: 7.5cm; padding: 0; height:0.5cm;font-size:11px;" align="left">
			<table width="100%" height="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td colspan="2">
					<table  style="width: 100%;height: 100%; font-size:12px;" cellspacing="0" cellpadding="0">
					<tr>
						<td width="70%"><b>Tel:</b><{$data.address.oab_phone}></td>
						<td width="30%"><b>Zip:</b>
						 <{$data.address.oab_postcode}>
	                    </td>
					</tr>
					</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<!-- SKU * 数量 
	<tr>
	    <td style="font-size: 7px; border: solid #000 1px;" colspan="2">
	        <div>
	            &nbsp;<{foreach from=$data.orderProduct key=key item=val}>
	            <{$val.product_barcode}> X <{$val.op_quantity}>&nbsp;&nbsp;
	            <{/foreach}>
	        </div>
	    </td>
	</tr>
	-->
</table>

<table class="table" width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size: 10px;width: 9.8CM;">
		  <tr>
		    <td width="64%" >Description of Contents(内件详细名称和数量)</td>
		    <td width="18%">Weight(kg)<br/>重量(千克)</td>
		    <td width="18%">Value<br/>(价值)</td>
		  </tr>
		  <tr>
			<td id="value" style="height: 16px;">
				<{assign var=num value='0'}>
				<{foreach from=$data.orderProductCategory key=key item=val}>
					<{$num=$num+1}>
					<{if $num<2}>
						<div none="1" name="value"><{$val.pc_name_en}></div>
					<{/if}>
				<{/foreach}>
			</td>
		   	<td>
		   		<{$data.ship.so_weight}>
		   	</td>
		    <td>
		    	<div><{$data.ship.declared_value}></div>
		    </td>
		  </tr>
		  <tr>
		  	<td style="height: 35px;" colspan="3" >&nbsp;</td>
		  </tr>
		  <tr>
		    <td >
		    	Total Gross Weight(kg)(总价值重量)
		    <td >
		    	<{$data.ship.so_weight}>
			</td>
		    <td >
		     	<{$data.ship.declared_value}>
		    </td>
		  </tr>
		      <tr>
		    <td colspan="3" class="th">
				    我保证上述申报准确无误，本函件内未装寄法律或邮政和海关规章禁止寄递的任何危险物品
				    (I,the undersigned,whose name and address are given on the itme,
				certify that particulars given in this declaration are correct and
				that this item dose not contain any dangerous article or artices
				prohibited by legislation or by postal or customs regulations
				Date and sender`s signature)<br/><br/>
				<div style="float: left;font-size: 12px;padding-bottom: 2px;">
					寄件人签字 (Sender's signature):
				</div>
				<div style="float: right;font-size: 12px;font-weight:bold; padding-bottom: 2px;">
					CN22
				</div>
			</td>
		    </tr>
		</table>

</body>
</html>