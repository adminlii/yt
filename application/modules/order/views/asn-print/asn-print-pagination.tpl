<style>
<!--
#container {
	width: 210mm;
}
table{margin:0 auto;width:208mm;}
.ec-center{	
	word-warp: break-word; /*内容将在边界内换行*/
	word-break: break-all; /*允许非亚洲语言文本行的任意字内断开*/	
}
-->
</style>
<div id='container'>
	<{foreach from=$dataArr item=data name=data key=k}>
	<table width="100%" cellspacing="0" cellpadding="0" border="0" style='margin:10px 0;'>
		<tbody>
			<tr class="table-module-b1">
				<td align='right' valign='middle' width='50%' style='line-height:30px;'>交货公司 <{$customer_code}>(<{$csi_customer.customer_shortname}>):
				<img
					src='/default/index/barcode1?code=<{$customer_code}>' style='float:right;'/>
				</td>
				<td class="ec-left" style='line-height:30px;'>
					<{if $customer_channel}> 
					<span style='float: left;'>子账号<{$customer_channel.customer_channelcode}>(<{$customer_channel.customer_channelname}>):</span><img
					src='/default/index/barcode1?code=<{$customer_channel.customer_channelcode}>'  style='float: left;'/>
					<{/if}>
				</td>
			</tr>
		</tbody>
	</table>
	<table width="100%" cellspacing="0" cellpadding="0" border="0"
		class="table-module">
		<tbody id="table-module-list-header">
			<tr class="table-module-title">
				<td class="ec-center">序号</td>
				<td class="ec-center">预报单号</td>
				<td class="ec-center">转单号</td>
				<td class="ec-center">销售产品</td>
				<td class="ec-center">件数</td>
				<td class="ec-center">目的地</td>
				<td class="ec-center">重量</td>
				<td class="ec-center">备注</td>
			</tr>
		</tbody>
		<tbody id="table-module-list-data">
			<{foreach from=$data item=o name=o key=kk}>
			<tr class="table-module-b1">
				<td class="ec-center"><{$pageSize*($k)+$kk+1}></td>
				<td class="ec-center"><{$o['预报单号']}></td>
				<td class="ec-center"><{$o['转单号']}></td>
				<td class="ec-center"><{$o['销售产品']}></td>
				<td class="ec-center"><{$o['件数']}></td>
				<td class="ec-center"><{$o['目的地']}></td>
				<td class="ec-center"><{$o['重量']}></td>
				<td class="ec-center"><{$o['备注']}></td>
			</tr>
			<{/foreach}>
		</tbody>
	</table>
	<{if !$smarty.foreach.data.last}> 
	<h1 style="page-break-after: always; clear: both;background:red;"></h1>
 	<{/if}>
	<{/foreach}>
</div>