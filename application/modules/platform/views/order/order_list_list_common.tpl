
<script src="/js/jquery-ui-timepicker-addon.js" type="text/javascript"></script>
<script src="/js/jquery.overlay.min.js" type="text/javascript"></script> 
<script type="text/javascript">
</script>
<div id='biaojifahuo_div' title='标记发货' class='dialog_div'>
	<form enctype="multipart/form-data" method="POST" action="" onsubmig='return false;' id='biaojifahuo_form'>
		
	</form>
</div>
<div id="module-container" style=''>
	<div id="fix_header_content" style="z-index: 9;">
		<div class='clone'>
			<div class="Tab">
				<ul>
					<{foreach from=$statusArr item=status name=status key=k}>
					<li style='position: relative;' class='mainStatus'>
						<a href="javascript:void (0)" class="statusTag <{if $k!=''}>statusTag<{$k}><{else}>statusTagAll<{/if}>" status='<{$k}>'>
							<span class='order_title<{$k}>' title='<{$status.name}>'><{$status.name}></span>
							<span class='count'></span>
						</a>						
					</li>
					<{/foreach}>
				</ul>				
			</div>
			<div class="opration_area" style="margin-left: 10px;clear:both;height:auto;">
				<div class='opDiv'></div>
			</div>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
				<tbody class='table-module-list-header-tpl'>
					<tr class="table-module-title">
						<td width="15px" class="ec-center">
							<input type="checkbox" class="checkAll">
						</td>
						<td width='165px' class="ec-center order_info_td"><{t}>order_info<{/t}></td><!-- 订单信息 -->
						<td class="ec-center"><{t}>order_detail<{/t}></td><!-- 订单明细 -->
						<td width='85px' class="ec-center"><{t}>order_amount<{/t}></td><!-- 订单金额 -->
						<td width='165px' class="ec-center delivery_info_td"><{t}>delivery_information<{/t}></td><!-- 配送信息 -->
						<td width='130px' class="ec-center date_info_td"><{t}>date<{/t}></td><!-- 时间 -->
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div id='fix_header' style='position: fixed; top: 0px; display: none; background: #fff; z-index: 9; width: 99.2%'></div>
	<div id="module-table" style='overflow: auto;'>
		<form action="" id='listForm' method='POST'>
			<table width="100%" cellspacing="0" cellpadding="0" border="0" class="table-module">
				<tbody id='table-module-list-header'>
					<!-- 通过js复制表头到此处 -->
				</tbody>
				<tbody id="table-module-list-data">
				</tbody>
			</table>
		</form>
		<div class="pagination"></div>
	</div>
</div>