<script type="text/javascript">
<{include file='order/js/order/order_create_tnt.js'}>
</script>
<style>
.info .Validform_wrong {
	background: url("/images/error.png") no-repeat scroll left center
		rgba(0, 0, 0, 0);
	color: red;
	padding-left: 20px;
	white-space: nowrap;
}

.Validform_checktip {
	color: #999;
	font-size: 12px;
	height: 20px;
	line-height: 20px;
	margin-left: 8px;
	overflow: hidden;
}


.Validform_checktip {
	margin-left: 0;
}

.info {
	border: 1px solid #ccc;
	padding: 2px 20px 2px 5px;
	color: #666;
	position: absolute;
	margin-top: -32px;
	margin-left: 10px;
	display: none;
	line-height: 20px;
	background-color: #fff;
	float: left;
}
.dec {
	bottom: -8px;
	display: block;
	height: 8px;
	overflow: hidden;
	position: absolute;
	left: 10px;
	width: 17px;
}

.dec s {
	font-family: simsun;
	font-size: 16px;
	height: 19px;
	left: 0;
	line-height: 21px;
	position: absolute;
	text-decoration: none;
	top: -9px;
	width: 17px;
}

.dec .dec1 {
	color: #ccc;
}

.dec .dec2 {
	color: #fff;
	top: -10px;
}
</style>
<div class="TNT_Form">
	<form method="POST" id="orderForm" action="" class="wrapper" onsubmit="return false;">
		<ul class="tabnav">
			<li class="on"><a href="javascript:void(0)">TNT快件录单</a></li>
			<li><a href="javascript:void(0)" onclick="leftMenu('order-list','订单管理','/order/order-list/list?quick=39')">订单管理</a></li>
		</ul>
		<div class="Floor_comlu">
			<div class="tit">
				<h3>发件人</h3>
				<div class="TxtCenter">
					<a href="javascript:;" id="shipperaddaddress">添加到地址簿</a>
					<a href="javascript:;" id="shipperaddrs" class="btnc1">发件人地址簿</a>
				</div>
			</div>
			<div class="Box">
				<table cellspacing="0" cellpadding="0" border="0";>
					<tr>
						<td class="col"><em>*</em> 联系人姓名：</td>
						<td><input type="text" name="shipper[shipper_name]" class="checkchar1" value="<{if isset($shipperConsignee["shipper_name"])}><{$shipperConsignee.shipper_name}><{/if}>"/></td>
						<td class="col" ><em>*</em> 发件人参考信息：</td>
						<td><input type="text" disabled class="checkchar4 use" value='<{if isset($order)}><{$order.refer_hawbcode}><{/if}>'
							name='order[refer_hawbcode]' id='refer_hawbcode' /></td>
					</tr>
					<tr>
						<td class="col"><em>*</em> 公司名称：</td>
						<td><input type="text" name="shipper[shipper_company]" class="checkchar" value="<{if isset($shipperConsignee["shipper_company"])}><{$shipperConsignee.shipper_company}><{/if}>"/></td>
						<td colspan="2"></td>
					</tr>
					<tr>
						<td class="col"><em>*</em> 国家：</td>
						<td><input type="text"  value="CN" disabled="disabled" style="background:#cfcfcf;"></td>
						<input type="hidden" name="shipper[shipper_countrycode]" value="CN">
						<td class="col"><em>*</em> 城市：</td>
						<td><input type="text" name="shipper[shipper_city]" class="checkchar2" value="<{if isset($shipperConsignee["shipper_city"])}><{$shipperConsignee.shipper_city}><{/if}>">
						<div class="searchbar" id="checkcitydiv" >
							<ul class="checkul" id="checkcity" _type="city_ename">
							</ul>
						</div>
						</td>
					</tr>
					<tr>
						<td class="col"><em>*</em> 地址：</td>
						<td colspan="3">
						<input id="shipperstree" type="text" class="checkchar2"  placeholder="房间号/楼层/楼座/大厦或小区" value="<{if isset($shipperConsignee["shipper_street1"])}><{$shipperConsignee.shipper_street1}><{/if}>"/> 
						<input id="shipperstree1" type="text" class="checkchar2" placeholder="街道/行政区或工业区" value="<{if isset($shipperConsignee["shipper_street2"])}><{$shipperConsignee.shipper_street2}><{/if}>"/>
						<input id="shipperstree2" type="text"  class="checkchar2" value="<{if isset($shipperConsignee["shipper_street3"])}><{$shipperConsignee.shipper_street3}><{/if}>"/></td>
					</tr>
					<tr>
						<td class="col"><em>*</em> 邮编：</td>
						<td><input type="text" name="shipper[shipper_postcode]" value="<{if isset($shipperConsignee["shipper_postcode"])}><{$shipperConsignee.shipper_postcode}><{/if}>">
						<div class="searchbar" id="checkpostcodediv" >
							<ul class="checkul" id="checkpostcode" _type="postcode">
							</ul>
						</div>	
						</td>
						
						
						</td>
						<td class="col"><em>*</em> 电话号码：</td>
						<td><input type="text" name="shipper[shipper_telephone]"  class="order_phone" value="<{if isset($shipperConsignee["shipper_telephone"])}><{$shipperConsignee.shipper_telephone}><{/if}>"></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="Floor_comlu">
			<div class="tit">
				<h3>收件人</h3>
				<div class="TxtCenter">
					<a href="javascript:;" id="consigneeaddaddress">添加到地址簿</a>
					<a href="javascript:;" class="btnc1" id="consigneeaddrs">收件人地址簿</a>
				</div>
			</div>
			<div class="Box">
				<table cellspacing="0" cellpadding="0" border="0";>
					<tr>
						<td class="col"><em>*</em> 公司名称：</td>
						<td><input type="text" class="checkchar" value="<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_company}><{/if}>"
							name='consignee[consignee_company]' id='consignee_company' /></td>
						<td class="col"><em>*</em> 联系人：</td>
						<td><input type="text"  class="checkchar5" value='<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_name}><{/if}>'
							name='consignee[consignee_name]' id='consignee_name' /></td>
					</tr>
					<tr>
						<td class="col"><em>*</em> 国家：</td>
						<td><select class='input_select country_code'
							name='order[country_code]'
							default='<{if isset($order)}><{$order.country_code}><{/if}>'
							id='country_code' >
								<option value='' class='ALL'><{t}>-select-<{/t}></option>
								
								<{foreach from=$country item=c name=c}>
								
								<option value='<{$c.country_code}>' country_id='<{$c.country_id}>' class='<{$c.country_code}>' <{if $c.country_code eq $order.country_code}>selected<{/if}> ><{$c.country_code}> [<{$c.country_cnname}>  <{$c.country_enname}>]</option>
								<{/foreach}>
								 
						</select></td>
						<td class="col"><em>*</em> 州：</td>
						<td><input type="text" placeholder="如果没有州可不填写" class="checkchar2" value="<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_province}><{/if}>"
							name='consignee[consignee_province]' id='consignee_province' /></td>
					</tr>
					<tr>
						<td class="col"><em>*</em> 城市：</td>
						<td><input type="text" class="checkchar3" value="<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_city}><{/if}>"
							name='consignee[consignee_city]' id='consignee_city' />
						<div class="searchbar" id="checkcitydiv1" >
							<ul class="checkul" id="checkcity1" _type="city_ename1">
							</ul>
						</div>
						</td>
						<td colspan="2"></td>
					</tr>
					<tr>
						<td class="col"><em>*</em> 地址：</td>
						<td colspan="3">
						<input type="text" placeholder="房间号/楼层/楼座/大厦或小区" class="checkchar2" value="<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_street}><{/if}>"
							name='consignee[consignee_street]' id='consignee_street' />
						<input type="text" placeholder="街道/行政区或工业区" class="checkchar2" value="<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_street2}><{/if}>" name='consignee[consignee_street2]' id='consignee_street2'/> 
						<input type="text" class="checkchar2" value="<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_street3}><{/if}>"
							name='consignee[consignee_street3]' id='consignee_street3' /></td>
					</tr>
					<tr>
						<td class="col"><em>*</em> 邮编：</td>
						<td class="slideTd">
						<input type="text" value='<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_postcode}><{/if}>'
							name='consignee[consignee_postcode]' id='consignee_postcode' />
						<div class="searchbar" id="checkpostcodediv1" >
							<ul class="checkul" id="checkpostcode1" _type="postcode1">
							</ul>
						</div>
						</td>
						<td class="col"><em>*</em> 电话号码：</td>
						<td><input type="text" class="order_phone" value='<{if isset($shipperConsignee)}><{$shipperConsignee.consignee_telephone}><{/if}>'
							name='consignee[consignee_telephone]' id=consignee_telephone /></td>
					</tr>
					<tr>
						<td class="col">无法送达：</td>
						<td colspan="3"><select name="order[untread]" id="untread" >
							<option value="0">- 请选择 -</option>
							<option value="1">- 退回 -</option>
							<option value="2">- 丢弃 -</option>
						</select></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="Floor_comlu">
			<div class="tit">
				<h3>内件性质</h3>
			</div>
			<p class="natrueBox oflr">
				<label class="clogds">文件：<input type="radio" class="nat1" name="order[mail_cargo_type]" checked value="3"/></label> <label class="opends">物品：<input type="radio"  class="nat2" name="order[mail_cargo_type]"  value="4" /></label></p>
		</div>
		<div class="Floor_comlu">
			<div class="tit">
				<h3>快递详细信息</h3>
			</div>
			<div class="Box oflr">
				<div class="clrOrgn">
					<p><em>*</em> 包装类型</p>
					<ul class="tabUl oflr">
						<li><span>服务类型：</span> 
						<select name="servicecode" id="servicecode">
							<option value="">请选择</option>
							<option value="P15D">EXPRESS(DOCS)全球快递（文件）</option>
						</select>
						</li>
						<li><span>币种：</span> 
						<select id="currencetype" name="currencytype">
							
						</select>
						</li>
						<li><span>订单金额：</span> 
						<input type="text" value="" name="order[invoice_totalcharge_all]"/>
						</li>
					</ul>
				</div>
			</div>
		</div>
<div class="activetable" style="display:none;">
	<table cellspacing="0" cellpadding="0" border="0" class="normTbe model2 hide">
		<tbody>
		<tr class="alonTr2">
			<td><input type="text"/></td>
			<td><input type="text" class="quantity invoice_unitcharge_do"/></td>
			<td><input type="text" class="weight" /></td>
			<td><input type="text" class="invoice_unitcharge invoice_unitcharge_do"/></td>
			<td><input type="text" disabled/></td>
			<td><input type="text" /></td>
			<td><select name='' style="width:118px;">
								<option value='' class='ALL'><{t}>-select-<{/t}></option>
								
								<{foreach from=$country item=c name=c}>
								
								<option value='<{$c.country_code}>' country_id='<{$c.country_id}>' class='<{$c.country_code}>' <{if $c.country_code eq $order.country_code}>selected<{/if}> ><{$c.country_code}> [<{$c.country_cnname}>  <{$c.country_enname}>]</option>
								<{/foreach}>
								 
						</select></td>
			<td><a class="text_a" href="javascript:;" onClick="deltr3(this)">删除</a></td>
		</tr>
		</tbody>
	</table>
	<table cellspacing="0" cellpadding="0" border="0" class="normTbe model1 hide">
		<tbody>
			<tr class="alonTr">
				<td><input type="text" class="quantity weight_do"/></td>
				<td><input type="text" class="weight weight_do"/></td>
				<td><input type="text" disabled/></td>
				<td><input type="text" class="order_volume"/></td>
				<td><input type="text" class="order_volume"/></td>
				<td><input type="text" class="order_volume"/></td>
				<td>
					<a href="javascript:;" class="innerbtn">添加内件(<span>0</span>)</a>
					<div class="pop_box hide">
						<div class="bg"></div>
						<div class="contentP">
							<div class="PTit">
								<h3>内件商品信息</h3>
								<a href="javascript:;" class="closepop">x</a>
							</div>
							<div class="textmian">
								<table class="normTbe neijian" cellspacing="0" cellpadding="0" border="0";>
									<thead>
										<tr>
											<th>内件商品描述</th>
											<th>数量</th>
											<th>重量</th>
											<th>单价</th>
											<th>总价</th>
											<th>HSCODE</th>
											<th>产地</th>
											<th>操作</th>
										</tr>
									</thead>
									<tbody class="tbody2">
									<tr>
										<td><input type="text" /></td>
										<td><input type="text" class="quantity invoice_unitcharge_do"/></td>
										<td><input type="text" class="weight"/></td>
										<td><input type="text" class="invoice_unitcharge invoice_unitcharge_do"/></td>
										<td><input type="text" disabled/></td>
										<td><input type="text" /></td>
										<td><select style="width:118px;" name='' >
								<option value='' class='ALL'><{t}>-select-<{/t}></option>
								
								<{foreach from=$country item=c name=c}>
								
								<option value='<{$c.country_code}>' country_id='<{$c.country_id}>' class='<{$c.country_code}>' <{if $c.country_code eq $order.country_code}>selected<{/if}> ><{$c.country_code}> [<{$c.country_cnname}>  <{$c.country_enname}>]</option>
								<{/foreach}>
								 
						</select></td>
										<td><a class="text_a" href="javascript:;" onClick="deltr2(this)">删除</a></td>
									</tr>
									</tbody>
								</table>
								<div class="btn_a1">
									<a class="dtadd" href="javascript:;">新增内件</a> <a class="closepop" href="javascript:;">确定内件</a>
								</div>
							</div>
						</div>
					</div>
					<br /><a class="text_a" href="javascript:;" onClick="deltr(this)">删除</a>
				</td>
			</tr>
		</tbody>
	</table>
	<div class="itemInfo">
	    <table cellspacing="0" cellpadding="0" border="0" class="normTbe tabInfo">
		    <thead>
		    	<tr>
					<th><em>*</em> 件数：</th>
					<th><em>*</em> 单件重量：</th>
					<th><em>*</em> 总重量：</th>
					<th><em>*</em>长度(厘米)：</th>
					<th><em>*</em>宽度(厘米)：</th>
					<th><em>*</em>高度(厘米)：</th>
					<th>操作</th>
				</tr>
			</thead>
		<tbody class="tbody1">
			<tr>
				<td><input type="text" class="quantity weight_do"/></td>
				<td><input type="text" class="weight weight_do"/></td>
				<td><input type="text" disabled/></td>
				<td><input type="text" class="order_volume"/></td>
				<td><input type="text" class="order_volume"/></td>
				<td><input type="text" class="order_volume"/></td>
				<td>
					<a href="javascript:;" class="innerbtn">添加内件(<span>0</span>)</a>
					<div class="pop_box hide">
						<div class="bg"></div>
						<div class="contentP">
							<div class="PTit">
								<h3>内件商品信息</h3>
								<a href="javascript:;" class="closepop">x</a>
							</div>
							<div class="textmian">
								<table class="normTbe neijian" cellspacing="0" cellpadding="0" border="0";>
									<thead>
										<tr>
											<th>内件商品描述</th>
											<th>数量</th>
											<th>重量</th>
											<th>单价</th>
											<th>总价</th>
											<th>HSCODE</th>
											<th>产地</th>
											<th>操作</th>
										</tr>
									</thead>
									<tbody class="tbody2">
									<tr>
										<td><input type="text" /></td>
										<td><input type="text" class="quantity invoice_unitcharge_do"/></td>
										<td><input type="text" class="weight"/></td>
										<td><input type="text" class="invoice_unitcharge invoice_unitcharge_do"/></td>
										<td><input type="text" disabled/></td>
										<td><input type="text" /></td>
										<td><select style="width:118px;" name='' >
								<option value='' class='ALL'><{t}>-select-<{/t}></option>
								
								<{foreach from=$country item=c name=c}>
								
								<option value='<{$c.country_code}>' country_id='<{$c.country_id}>' class='<{$c.country_code}>' <{if $c.country_code eq $order.country_code}>selected<{/if}> ><{$c.country_code}> [<{$c.country_cnname}>  <{$c.country_enname}>]</option>
								<{/foreach}>
								 
						</select></td>
										<td><a class="text_a" href="javascript:;" onClick="deltr2(this)">删除</a></td>
									</tr>
									</tbody>
								</table>
								<div class="btn_a1">
									<a class="addtr2" href="javascript:;">新增内件</a> <a class="closepop" href="javascript:;">确定内件</a>
								</div>
							</div>
						</div>
					</div>
					<br /><a class="text_a" href="javascript:;" onClick="deltr(this)">删除</a>
				</td>
			</tr>
		</tbody>
	    </table>
	    <div class="copybtn TxtCenter">
			<a href="javascript:;" class="AddTr">新增</a>
			<a href="javascript:;" class="ture btnc1">确定</a>
		</div>

	</div><!-- itemInfo -->
</div>
		<div class="Floor_comlu">
			<div class="tit">
				<h3>订单信息</h3>
			</div>
			<div class="Box oflr">
				<table cellspacing="0" cellpadding="0" border="0";>
					<tr>
						<td class="col">客户留言：</td>
						<td colspan="3"><textarea name="DESCRIPTION"></textarea></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="Floor_comlu" id='invoicediv' style="display:none;">
			<div class="tit">
				<h3>只针对包裹快件(海关要求)</h3>
			</div>
			<div class="Box oflr">
				<div class="clrOrgn">
					<p>请附上形式发票或商业发票的原版和2份复印件。</p>
				</div>
				<table cellspacing="0" cellpadding="0" border="0";>
					<tr>
						<td class="col">发件人增值税号/企业海关十位编码：</td>
						<td><input type="text" name='invoice_shippertax'/></td>
						<td class="col">收件人增值税号/企业海关十位编码：</td>
						<td><input type="text" name='invoice_consigneetax'/></td>
					</tr>
					<tr>
						<td class="col">通关的申报价值（用于商业/形式发票）：</td>
						<td class="InSe" colspan="3"><input type="text" /> 
						</td>
					</tr>
					<tr>
						<td class="col">快件保险（保险价值不得高于申报价值）参见 条款与条件：</td>
						<td colspan="3">
							<ul class="tdUl">
								<li><label><input type="checkbox" id="C2" value="C2"  name="extraservice[]"/> 是</label></li>
								
								<li><div style="position: absolute;width: 450px;height: 53px;"></div>保险价值： <input type="text" placeholder="RMB" name="order[insurance_value_gj]"/></li>
								<li>保费： <input type="text" placeholder="RMB" name="order[insurance_value]"/></li>
							</ul>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="invoice hide">
			<div class="TNT_Form"></div>
			<h3><em></em>制作发票</h3>
			<div class="innerbox hide">
				<div class="invTit">
					<label><input type="radio" name="order[invoice_type]" checked value="1" /> 形式发票</label>
					<label><input type="radio" name="order[invoice_type]" value="2" /> 商业发票</label>
				</div>
				<ul>
					<li>
						<span>日期</span>
						<div class="inptbox">
							<input type="text" class="laydate-icon" placeholder="开始时间" onclick="laydate()" name="order[makeinvoicedate]" id="makeinvoicedate">
						</div>
					</li>
					<li>
						<span>出口类型</span>
						<div class="inptbox">
							<select name="order[export_type]" >
					        	<option value="Permanent">Permanent(永久）</option>
					        	<option value="Temporary">Temporary（临时）</option>
					        	<option value="Repair/Return">Repair/Return(返修货）</option>
					        </select>
						</div>
					</li>
					<li>
						<span>贸易条款</span>
						<div class="inptbox">
							 <select name="order[trade_terms]" >
					        	<option value="DAP-Delivered at Place">DAP-Delivered at Place</option>
					        	<option value="EXW-Ex Works">EXW-Ex Works</option>
					        	<option value="FCA-Free Carrier">FCA-Free Carrier</option>
					        	<option value="CPT-Carried Paid To">CPT-Carried Paid To</option>
					        	<option value="CIP-Carriage and insurance Paid">CIP-Carriage and insurance Paid</option>
					        	<option value="DAT--Delivered at Terminal">DAT--Delivered at Terminal</option>
					        	<option value="DDP-Delivered Duty Paid">DDP-Delivered Duty Paid</option>
					        </select>
						</div>
					</li>
					<li>
						<span>发票号码</span>
						<div class="inptbox">
							<input type="text" name="order[invoicenum]" id="invoicenum"/>
						</div>
					</li>
					<li>
						<span>付款方式</span>
						<div class="inptbox">
							<select  name="order[pay_type]" >
					        	<!--<option value="freight collect">freight collect（到付）</option>-->
					        	<option value="freight prepaid">freight prepaid（预付）</option>
					        </select>
        
						</div>
					</li>
					<li>
						<span>注释</span>
						<div class="inptbox">
							<input type="text"  name="order[fpnote]" id="fpnote">
						</div>
					</li>
				</ul>
			</div>
		</div>
		<div class="Floor_comlu" id="servicediv">
			<div class="tit">
				<h3>额外服务选项</h3>
			</div>
			<div class="Box oflr">
				<ul>
					<li><label><input type="radio" name="extraservice[]" value="C5"/> 文件保障服务 附加费3元/票</label></li>
					<li><label><input type="radio" name="extraservice[]" value="C6"/> 文件保障服务 附加费12元/票</label></li>
				</ul>
				<p class="PNs1">文件保障说明：3元中速险每票快件最高赔偿付5000元，12元中速险每票快件最高赔付22000元需要提供“文件保险通知书”(与文件价值无关)。</p>
			</div>
		</div>
		<div class="Floor_comlu">
			<div class="tit">
				<h3>发件人协议</h3>
			</div>
			<div class="Box oflr">
				<p>除非提供另外的书面协议，我/我们同意下述附件所列运输条款和条件将作为我/我们与中国邮政速递物流有限公司之间的合同条款<a href="">（条款和条件）</a>。</p>
				<p class="agree_pact"><label><input type="checkbox" checked id="allow"/> 我同意</label></p>
			</div>
		</div>
		<div class="fromBtns">
			<!--<input type="reset" value="重置内容" />-->
			<input type="submit" class="tjBtn" value="提交并打印运单" status="P" id="orderSubmitBtn"/>
		</div>
	</form>
</div>
</body>
</html>