<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!-- <title>打印形式发票</title> -->
<link href="/css/invoice/yuntu_invoice.css" rel="stylesheet" type="text/css" />

</head>
<style>
#tab_info{
	border-left:1px solid black;border-right:1px solid black;position:relative;
}
.border_1{position:absolute;border-right:1px solid black;height:200px;width:1px;left:373px;}
.border_2{position:absolute;border-right:1px solid black;height:200px;width:1px;left:497px;}
.border_3{position:absolute;border-right:1px solid black;height:200px;width:1px;left:622px;}
#tab_left{
	width:123px;height:200px;float:left;border-left:1px solid black;border-right:1px solid black;
	text-align:center;margin-left:-1px;
}
#tab_right{
	width:623px;height:200px;float:right;border-right:1px solid black;margin-right:-1px;
}
ul{margin:0;padding:2px 3px;}
li{list-style-type:none;}
.enname{display:inline-block;width:240px;}
.quantity{display:inline-block;width:115px;padding-left:8px;}
.unitcharge{display:inline-block;width:110px;padding-left:8px;}
.totalcharge{display:inline-block;width:110px;padding-left:8px;}
</style>
<body>
	<{foreach from=$orderArr name=o item=o}>
	<div class="content">
		
		<div class="title">SHENZHEN HuaHan  LOGISTICS  LTD</div>
		

		<div class="b_from_content3_1" >
		XINXINBuilding,ZhenHuaRoad,Futian,ShenZhen,China
		<!-- <{$o.shipper_consignee.shipper_street}>,<{$o.shipper_consignee.shipper_city}>,<{$o.shipper_consignee.shipper_province}>,<{$o.shipper_consignee.shipper_province}>,<{$o.shipper_consignee.shipper_country_name}>&nbsp; -->
		</div>
		
		<div class="b_from">
			<div class="b_from_content3_1" >755-82518733<!-- TEL:<{$o.shipper_consignee.consignee_telephone}> -->
			 &nbsp; &nbsp;                  FAX:755-82549314<!-- <{$o.shipper_consignee.consignee_fax}>&nbsp; -->
			</div>
		</div>
		
		<div class="b_from">
			<div class="b_from_content3_1" >
			Commercial Invoice
			</div>	
		</div>
		
		<div class="b_from">
			<div class="b_from_content3_1" style="font-size:24px;">
			商业发票
			</div>
		</div>

	</div>

	<div class="content" >
	
		<table class='table' cellpadding='0'cellspacing='0'>
			<tr style="height:60px;">
				<td colspan="3" class="td_left">International Air Waybill No.<br/>
				            航空运单: <{$o.order.server_hawbcode}>
				</td>
				<td colspan="3" class="td_right">Date of Exportation <br/>
				           出口日期：<{$o.order.checkout_date|date_format:"%Y-%m-%d"}> 
				</td>
				
			</tr>
			
			<tr style="height:200px;">
				<td colspan="3" class="td_left">Shipper/Exporter(Complete name and address)<br/>
                                                             发件人:<{$o.shipper_consignee.shipper_name}>,
				    <{$o.shipper_consignee.shipper_street}>, <{$o.shipper_consignee.shipper_city}>, <{$o.shipper_consignee.shipper_province}>&nbsp; <br />       
				</td>
				<td colspan="3"  class="td_right">Consignee(Complete name and address) <br/>
				           公司名 :<{$o.shipper_consignee.consignee_company}><br/>
				           收件人 :<{$o.shipper_consignee.consignee_name}><br/>
				           地址:<{$o.shipper_consignee.consignee_street}>, <{$o.shipper_consignee.consignee_city}>, <{$o.shipper_consignee.consignee_province}>&nbsp; <br /> 
                                                           电话:<{$o.shipper_consignee.consignee_telephone}><br/>	    
				</td>
				
			</tr>
			
			<tr style="height:60px;">
				<td colspan="3" class="td_left">Country of Export/Country of Origial <br/>
                                                 出口国: CN

				</td>
				<td colspan="3"  class="td_right">Country of destination<br/>
                                                进口国: <{$o.shipper_consignee.consignee_country_name}>
				</td>
				
			</tr>
			
			<tr>
			   <td style="width:60px;border-style:solid;">No.of pkgs<br/>
			                    件  数:
			   
               </td>
			   <td  colspan="2" style="border-style:solid none solid none;">Description of Goods<br/>
                                                         货物描述:
               </td>
              <td style="border-style:solid;text-align: center;" > QTY<br/>
                                                      数量:</td>
              <td style="border-style:solid solid solid none;text-align: center;">
                  Unit Value<br />
                                                     单价:                                  
               </td>
               <td  style="border-style:solid solid solid none;text-align: center;">
                  Total Value<br />
                                                      总价 :<{$iv.invoice_totalcharge}>
               </td>    
			</tr>
			</table>
			<div id="tab_info">
			<div class="border_1"></div>
			<div class="border_2"></div>
			<div class="border_3"></div>
			<div id="tab_left"><{<{$o.order.order_pieces}>}></div>
			<div id="tab_right">
				 <{foreach from=$o.invoice name=iv key=k item=iv}>
				<ul>
					<li>
						<span class="enname"><{$iv.invoice_enname}></span>
						<span class="quantity"><{$iv.invoice_quantity}></span>
						<span class="unitcharge"><{$iv.invoice_unitcharge}></span>
						<span class="totalcharge"><{$iv.invoice_totalcharge}></span>
					</li>
				</ul>
				<{/foreach}>
			</div>
			
			
			</div>
			<table class='table' cellpadding='0'cellspacing='0'>
			<tr>
			   <td style="width:119px;text-align: center;" >Total No.ofPkgs<br/>
			       (总件数):<br/>
			       <{$o.order.order_pieces}>
               </td>
			   <td  colspan="2"  style="border-style:solid none none none;"><br />
                                                         
               </td>
              <td style="border-style:solid none none none;">
              </td>
              <td style="border-style:solid none none none;">
                 
               </td>
               <td style="border-style:solid;text-align: center;">
                  Total Inv.Value<br />
                                                      总 价 值:	<br/>
                 
                 USD<{$total_Value}>
                       
               </td>
            
               
               
			</tr>
			
		</table>
		<div style="float:left;width:375px;height:50px;margin-top:20px;" >Shipper’s Signature & Stamp:<br/>
			                 发件人签字、盖章:	 
		</div>
		
		<div style="float:left;width:375px;height:50px;margin-top:20px;"><br/>
		Date(签字日期):	                 	 
		</div>
		
		<div style="float:right;width:130px;">
			  <{$smarty.now|date_format:"%Y-%m-%d"}> 
		</div>

	</div>


	<{if !$smarty.foreach.o.last}> 
	<h1 style="page-break-after: always; clear: both;background:red;"></h1>
 	<{/if}>
	<{/foreach}>

</body>
</html>