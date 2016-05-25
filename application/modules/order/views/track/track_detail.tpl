<script type="text/javascript">
<!--
function verc() {
    $('.verifyCode').attr('src', '/default/index/verify-code/' + Math.random());
}
$(function(){
	
        $(".tab").live('click',function(){
            $(".tabContent", "#dialog-auto-alert-tip").hide();
            $(this).parent().removeClass("chooseTag");
            $(this).parent().siblings().addClass("chooseTag");
            $("#"+$(this).attr("id").replace("tab_",""), "#dialog-auto-alert-tip").show();
        });
})

	
//-->
</script>
<div style='padding: 8px;' class='nbox_c'>
	<div class="ntitle">
		<span style="">
			<h2 class="ntitle" style="padding-left: 0px;">订单追踪</h2>
		</span>
	</div>
	<form action="/default/index/get-track-detail" method='post'>
		<textarea name="code" rows="" cols="" style='width: 90%; height: 70px; overflow: auto; font-size: 13px;' placeholder='如需查询多个单号，请使用","隔开(最多支持20个单号同时查询)'></textarea>
		<div style='height: 5px;'></div>
		<{if !$user}>
        <input type="text" class="input_text keyToSearch" name="authCode" style='width:70px;' placeholder='验证码'/>
    	<img align="absmiddle" src="/default/index/verify-code" style='width:72px;height:23px;cursor:pointer;' class='verifyCode' onclick='verc();'></label>
    	<{/if}>
		<input type="submit" class="baseBtn submitToSearch" value="<{t}>查询<{/t}>"><span class='msg' style='color:red;padding:0 15px;'><{if $trackErrMsg}><{$trackErrMsg}><{/if}></span>
	</form>
</div>
<div id="module-table">
	<table cellspacing="0" cellpadding="0" width="100%" border="0" class="table-module">
		<tbody>
			<tr class="table-module-title">
				<td width="175"><{t}>服务商单号<{/t}></td>
				<!-- <td width="165"><{t}>客户单号<{/t}></td> -->
				<td><{t}>目的地<{/t}></td>
				<td><{t}>收件人<{/t}></td>
				<td width="135"><{t}>发生时间<{/t}></td>
				<td><{t}>发生地点<{/t}></td>
				<td><{t}>最新状态<{/t}></td>
				<td><{t}>转运记录<{/t}></td>
			</tr>
		</tbody>
		<tbody id="table-module-list-data">
			<{if $rsArr}> <{foreach from=$rsArr name=rs_data item=rs_data}> <{if $rs_data.ask}> <{$rs = $rs_data.data}>
			<tr class="table-module-b1">
				<td><{$rs.server_hawbcode}></td>
				<!-- 
				<td><{$rs.shipper_hawbcode}></td>
				 -->
				<td><{$rs.country_code}></td>
				<td><{$rs.signatory_name}></td>
				<td><{$rs.new_track_date}></td>
				<td><{$rs.new_track_location}></td>
				<td><{$rs.new_track_comment}></td>
				<td>
					<a href="javascript:;" onclick='alertTip($("#track_<{$rs.tbs_id}>").html(),800,400);'>查看</a>
				</td>
			</tr>
			<tr style='display: none;'>
				<td colspan='8' id='track_<{$rs.tbs_id}>'>
					<div class="depotTab2">
						 <ul>
			                <li>
			                    <a hhref="javascript:void(0);" id='tab_cn_<{$rs.tbs_id}>' class='tab' style="cursor:pointer">中文</a>
			                </li>
			               <!-- <li class="chooseTag">
			                    <a href="javascript:void(0);" id="tab_en_<{$rs.tbs_id}>" class="tab" style="cursor:pointer">English</a>
			                </li> -->
			             </ul>
		            </div>
		            <div class="tabContent" id="cn_<{$rs.tbs_id}>" style="display:block;">
						<table cellspacing="0" cellpadding="0" width="100%" border="0" class="table-module">
							<caption style='text-align: left;line-height:30px;'><!-- <{t}>服务商<{/t}>：<{$rs.product_code}>&nbsp;&nbsp;&nbsp;&nbsp; --> <{t}>服务商单号<{/t}>:<{$rs.server_hawbcode}></caption>
							<tbody>
								<tr class="table-module-title">
									<td width="135"><{t}>发生时间<{/t}></td>
									<td><{t}>发生地点<{/t}></td>
									<td><{t}>轨迹内容<{/t}></td>
								</tr>
							</tbody>
							<{foreach from=$rs.detail name=d item=d}>
							<tr>
								<td width='135'><{if $d.Datetime}><{$d.Datetime}><{else}><{$d.track_occur_date}><{/if}></td>
								<td width='300'><{if $d.Location}><{$d.Location}><{else}><{$d.track_area_description}><{/if}></td>
								<td><{if $d.Info}><{$d.Info}><{else}><{$d.track_description}><{/if}></td>
							</tr>
							<{/foreach}>
						</table>
					</div>
		            <div class="tabContent" id="en_<{$rs.tbs_id}>" style="display:none;">
						<table cellspacing="0" cellpadding="0" width="100%" border="0" class="table-module">
							<caption style='text-align: left;line-height:30px;'>Code:<{$rs.server_hawbcode}></caption>
							<tbody>
								<tr class="table-module-title">
									<td width="135">Time</td>
									<td>Place</td>
									<td>Content</td>
								</tr>
							</tbody>
							<{foreach from=$rs.detail name=d item=d}>
							<tr>
								<td width='135'><{if $d.Datetime}><{$d.Datetime}><{else}><{$d.track_occur_date}><{/if}></td>
								<td width='300'><{if $d.Location}><{$d.Location}><{else}><{$d.track_area_description_en}><{/if}></td>
								<td><{if $d.Info}><{$d.Info}><{else}><{$d.track_description_en}><{/if}></td>
							</tr>
							<{/foreach}>
						</table>
					</div>
				</td>
			</tr>
			<{else}>
			<tr class="table-module-b1">
				<td colspan='8'><{$rs_data.server_hawbcode}><{$rs_data.message}></td>
			</tr>
			<{/if}> <{/foreach}> <{/if}>
		</tbody>
	</table>
</div>