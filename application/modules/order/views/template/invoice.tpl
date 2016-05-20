<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh" lang="zh" dir="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>打印</title>
</head>

<style type="text/css">
    body{
        font-size:12px;
    }
</style>
<body style="padding:0;">
<!--发票-->
<div style="width:19cm;page-break-after:always;">
    <div style="font-size:18px;margin:auto;text-align:center;height:80px;">
        <div style="width:98%;height:20px;text-align:right"><{$data.order.print_sort}><{if $page && $page!=''}>&nbsp;_&nbsp;<{$page}><{/if}></div>
        <span><b>INVOICE&nbsp;发票</b></span>
    </div>
    <div style="width:19cm">
        <table style="width:18.5cm">
            <tbody>
            <tr>
                <td width="150">Date&nbsp;日期:</td>
                <td style="border-bottom: #000 1px solid" ><{$data.sDate}>&nbsp; </td>
                <td >Connote&nbsp;no&nbsp;订单号:</td>
                <td style="border-bottom: #000 1px solid" ><{$data.ship.tracking_number}>&nbsp;
                </td>
            </tr>
            <tr>
                <td >Shipper&nbsp;寄件人:</td>
                <td style="border-bottom: #000 1px solid" ><{$data.shipAddress.Shipper}>&nbsp; </td>
                <td >Telephone&nbsp;电话:</td>
                <td style="border-bottom: #000 1px solid" ><{$data.shipAddress.Telephone}>&nbsp;
                </td>
            </tr>
            <tr>
                <td valign=top>Address&nbsp;地址:</td>
                <td style="border-bottom: #000 1px solid" colspan=3><{$data.shipAddress.Address}></td>
            </tr>
            <tr>
                <td ></td>
                <td style="border-bottom: #000 1px solid" colspan=3>
                    <{$data.shipAddress.City}>,<{$data.shipAddress.State}> &nbsp;<{$data.shipAddress.postCode}>
                </td>
            </tr>
            <tr>
                <td ></td>
                <td style="border-bottom: #000 1px solid" colspan=3><{$data.shipAddress.countryName}></td>
            </tr>
            <tr>
                <td >Consignee&nbsp;收件人:</td>
                <td style="border-bottom: #000 1px solid" colspan=3><{$data.address.oab_firstname}> <{$data.address.oab_lastname}></td>
            </tr>
            <tr>
                <td valign=top>Address&nbsp;地址:</td>
                <td style="border-bottom: #000 1px solid"
                    colspan=3><{$data.address.oab_street_address1}> <{$data.address.oab_street_address2}></td>
            </tr>
            <tr>
                <td ></td>
                <td style="border-bottom: #000 1px solid" colspan=3><{$data.address.oab_city}>,<{if $data.address.oab_state!=''}><{$data.address.oab_state}>,<{/if}>&nbsp;<{$data.address.oab_postcode}>
                </td>
            </tr>
            <tr>
                <td ></td>
                <td style="border-bottom: #000 1px solid" colspan=3><{$data.address.country.country_name_en}></td>
            </tr>
            <tr>
                <td  >Telephone&nbsp;电话:</td>
                <td style="border-bottom: #000 1px solid" ><{$data.address.oab_phone}>&nbsp;
                </td>
                <td  >Fax&nbsp;传真:</td>
                <td style="border-bottom: #000 1px solid" ><{$data.address.oab_fax}>&nbsp;
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div style="width:19cm;margin-top:36px;">
        <table style="text-align: center;"
                cellspacing=0 cellpadding=0 width=100% height="100%">
            <tbody>
            <tr>
                <td
                        style="border-top: #000 1px solid; border-right: #000 1px solid; border-bottom: #000 1px solid; border-left: #000 1px solid"
                         valign=top width=205>
                    <font style="text-align: center">Quantity</font></td>
                <td
                        style="border-top: #000 1px solid; border-right: #000 1px solid; border-bottom: #000 1px solid"
                        width=456>Full Descrption of Goods
                </td>
                <td
                        style="border-top: #000 1px solid; border-right: #000 1px solid; border-bottom: #000 1px solid"
                        width=158>Unit Value
                </td>
                <td
                        style="border-top: #000 1px solid; border-right: #000 1px solid; border-bottom: #000 1px solid"
                        width=164>Amount
                </td>
            </tr>
            <tr>
                <td
                        style="border-right: #000 1px solid; border-bottom: #000 1px solid; border-left: #000 1px solid"
                        valign=top width=205>
                    <font style="text-align: center">数量</font></td>
                <td style="border-right: #000 1px solid; border-bottom: #000 1px solid"
                    width=456>详细的商品名称
                </td>
                <td style="border-right: #000 1px solid; border-bottom: #000 1px solid"
                    width=158>单价
                </td>
                <td style="border-right: #000 1px solid; border-bottom: #000 1px solid"
                    width=164>价值
                </td>
            </tr>
            <{foreach from=$data.orderProduct key=key item=val}>
            <tr>
                <td
                        style="border-right: #000 1px solid; border-bottom: #000 1px solid; border-left: #000 1px solid"
                        ><span contenteditable=true><{$val.op_quantity}>&nbsp;</span></td>
                <td
                        style="border-right: #000 1px solid; border-bottom: #000 1px solid"><span
                        contenteditable=true><{$val.product_title_en}></span></td>
                <td
                        style="border-right: #000 1px solid; border-bottom: #000 1px solid"><span
                        contenteditable=true>$<{$val.invoice_value}></span></td>
                <td
                        style="border-right: #000 1px solid; border-bottom: #000 1px solid"><span
                        contenteditable=true>$<{$val.invoice_value*$val.op_quantity}></span></td>
            </tr>
            <{/foreach}>
            <tr>
                <td
                        style="border-right: #000 1px solid; border-bottom: #000 1px solid; border-left: #000 1px solid"
                         colspan=2>&nbsp;</td>
                <td
                        style="border-right: #000 1px solid; border-bottom: #000 1px solid">Total
                </td>
                <td
                        style="border-right: #000 1px solid; border-bottom: #000 1px solid"><span
                        contenteditable=true>$<{$data.invoiceTotal}></span></td>
            </tr>
            </tbody>
        </table>
        <p style="font-size:14px; letter-spacing: 2px">I/&nbsp;we hereby certify
            that the information on this invoice is true and correct to the best of my
            knowledge and that the contents of this shipment are as<br>stated
            above.</p>

        <p style="font-size:14px; font-weight: 400">本人认为本发票所载内容属实和确认无误。</p>
    </div>
</div>
</body>
</html>