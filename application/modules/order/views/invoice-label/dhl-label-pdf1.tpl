<div id = "warp">
	<div class="div1 bt1 bt2">
		<h1 style="margin-top: 10px;">Shipper:</h1>
		<p><{$o.shipper_consignee.shipper_name}></p>
		<p></p>
		<p><{$o.shipper_consignee.shipper_province}></p>
		<p><{$o.shipper_consignee.shipper_district}></p>
		<p><{$o.shipper_consignee.shipper_street}></p>
		<p><{$o.shipper_consignee.shipper_city}><span style="width: 30px;display: inline-block;"></span><{$o.shipper_consignee.shipper_postcode}><span style="width: 30px;display: inline-block;"></span><{$o.shipper_consignee.shipper_countrycode}></p>
	
		<p style="margin-bottom:3px;">Phone:<{if $o.shipper_consignee.shipper_telephone}><{$o.shipper_consignee.shipper_telephone}><{else}><{$o.shipper_consignee.shipper_mobile}><{/if}></p>
		<p style="margin-bottom:1px;">VAT/GST No:<{$o.invoice[0]['invoice_shippertax']}></p>
	</div>
	<div class="div1 bt1">
	 <p style="padding: 76px 71px;font-size: 30px;line-height: 30px;height: 30px;">COMMERCIAL INVOICE</p>
	</div>
	<div class="clear"></div>
	<div class="div2 bt1 bt2">
		<h1 style="margin-top: 10px;">Receiver:</h1>
		<p><{$o.shipper_consignee.consignee_name}></p>
		<p></p>
		<p><{$o.shipper_consignee.consignee_province}></p>
		<p><{$o.shipper_consignee.consignee_district}></p>
		<p><{$o.shipper_consignee.consignee_street}><{if $o.shipper_consignee.consignee_street2}>,<{$o.shipper_consignee.consignee_street2}><{/if}><{if $o.shipper_consignee.consignee_street3}>,<{$o.shipper_consignee.consignee_street3}><{/if}></p>
		<p><{$o.shipper_consignee.consignee_city}><span style="width: 30px;display: inline-block;"></span><{$o.shipper_consignee.consignee_postcode}><span style="width: 30px;display: inline-block;"></span><{$o.shipper_consignee.consignee_countrycode}></p>
		<p style="margin-bottom:3px;">Phone:<{if $o.shipper_consignee.consignee_telephone}><{$o.shipper_consignee.consignee_telephone}><{else}><{$o.shipper_consignee.consignee_mobile}><{/if}></p>
		<p style="margin-bottom:1px;">VAT/GST No:<{$o.invoice[0]['invoice_consigneetax']}></p>
	</div>
	<div class="div2 bt1">
		<div class="comment bt3">
			<p style="padding-top: 15px;">Date:<{$o.order.makeinvoicedate}></p>
		</div>
		<div class="comment bt3">
			<p style="padding-top: 15px;">Invoice Number:<{$o.order.invoicenum}></p>
		</div>
		<div class="comment bt3">
			<p style="padding-top: 15px;">Order Number:</p>
		</div>
		<div style="height:50px">
			<p style="padding-top: 15px;">Comments:<{$o.order.fpnote}></p>
		</div>
	</div>
	<div class="clear"></div>
	<div class="div3 bt1 bt2">
		<div>
			<div class="bt3 bt4 invoice" style="width:45px;float:left;"><p class="p1">NO.</p></div>
			<div class="bt3 bt4 invoice" style="width:406px;float:left;"><p class="p1">Full Description of Goods</p></div>
			<div class="bt3  invoice" style="width:45px;float:left;"><p class="p1">QTY</p></div>
			<div class="clear"></div>
		</div>
		<{foreach from=$o.label key=ink  name=inv item=inv}>
		<div>
			<div class="  invoice" style="width:45px;float:left;"><p class="p1"><{$ink+1}></p></div>
			<div class="  invoice" style="width:406px;float:left;"><p class="p1"><{$inv.invoice_note}></p></div>
			<div class="  invoice" style="width:45px;float:left;"><p class="p1"><{$inv.invoice_quantity}></p></div>
			<div class="clear"></div>
		</div>
		<{/foreach}>
	</div>
	<div class="div3 bt1">
		<div>
			<div class="bt3 bt4 invoice" style="width:173px;float:left;"><p  class="p1">Commodity Code</p></div>
			<div class="bt3 bt4 invoice" style="width:150px;float:left;"><p class="p1">Unit Value</p></div>
			<div class="bt3  invoice" style="width:173px;float:left;"><p class="p1">Country of Origin</p></div>
			<div class="clear"></div>
		</div>
		<{foreach from=$o.label   name=inv1 item=inv1}>
		<div>
			<div class="  invoice" style="width:173px;float:left;"><p  class="p1"><{$inv1.invoice_shipcode}></p></div>
			<div class="  invoice" style="width:150px;float:left;"><p class="p1"><{$inv1.invoice_unitcharge}></p></div>
			<div class="  invoice" style="width:173px;float:left;"><p class="p1"><{$inv1.invoice_proplace}></p></div>
			<div class="clear"></div>
		</div>
		<{/foreach}>
	</div>
	<div class="clear"></div>
	<div class="div4 bt">
		<div class="total bt2 bt3"><p style="height: 40px;line-height: 40px;">Total Declared Value:<{$total_Value}></p></div>
		<div class="total bt2 bt3"><p style="height: 40px;line-height: 40px;">Total Pieces:<{$total_pice}></p></div>
		<div class="total bt2 bt3"><p style="height: 40px;line-height: 40px;">Total Gross Weight:<{$total_weight}><span></span></p></div>
		<div>
			<div>
			 <div class="div5"><span class="span1">Type of Exprot:<{$o.order.export_type}></span> <span class="span1" style="margin-left: 0px;">Currency Code:<{$o.invoice[0]['invoice_currencycode']}></span></div>
			 <div class="div5"><span class="span1">Terms of:<{$o.order.trade_terms}></span> <span class="span1" style="margin-left: 0px;">Incoterm:</span></div>
			</div>
			<p>I/We here by certify that the infomation of this invoice is true and correct and that contents of this shipment are as  stated above.</p>
			<div>			
			 <div class="div5" style="margin-top: 20px;position: relative;">
			 <div style="position: absolute;border-bottom: 1px solid black;width: 200px;left: 61px;height: 30px;"></div>
			 <span class="span1">Signature:<span></span></span> </div>
			 <div class="div5"><span class="span1">Name of Company:<{$o.shipper_consignee.shipper_company}></span> <span class="span1" style="margin-left: 0px;"x`>Company Stamp:</span></div>	
			</div>
		</div>
	  
	</div>
</div>
