<style>
<!--

.report_fmbox_lft {
	float: left;
	height: 400px;
	overflow-y: scroll;
	width: 400px;
}

.report_fmbox_rit {
	float: right;
	height: 400px;
	overflow-y: scroll;
	width: 400px;
}

#module-container {
	width: 980px;
}
#revokedTemplateBtn{  
    background: none repeat scroll 0 0 #2283c5;
    border: 1px solid #ccc;
    color: #fff;
    float: right;
    position: absolute;
    right: 22px;
    top: 8px;
    z-index: 999;
}
-->
</style>
<input type="button" style="" class="submitBtn" value="<{t}>保存模板<{/t}>" id="saveTemplateBtn">	

<div class='map_wrap' style='position:relative;'>
	<input type="button" style="" class="report_cx_btn" value="撤消映射" id="revokedTemplateBtn">
	
	<form enctype="multipart/form-data" method="POST" action="/order/order-template/save" name="form" id='dataForm' onsubmit='return false'>
	<input type="hidden" value="<{if $report_id}>edit<{else}>add<{/if}>" id='template_op'>
	<input type="hidden" value="<{$file_name}>" name="file_name" id='file_name'>
	<input type="hidden" value="<{$report_id}>" name="report_id" id='report_id'>
	<div class="report_fmbox">
		<div class="report_fmbox_lft">
			<table cellspacing="0" cellpadding="0" width="100%" bordercolor="#e1e1e1" border="1" bgcolor="#FFFFFF" class="report_form table-module">
				<tbody>
					<tr style="text-align: center;" class="report_form_bk">
						<td colspan="4"><{t}>客户模板<{/t}></td>
					</tr>
					<tr style="background: #e8eff9;">
						<td width="10%" align="center"></td>
						<td width="15%" align="center"><{t}>序号<{/t}></td>
						<td width="40%" align="center"><{t}>名称<{/t}></td>
						<td width="35%" align="center"><{t}>映射名称<{/t}></td>
					</tr>
					<{foreach from=$userTemplate name=o item=o key=k}>
					<tr id="CustomerTR_<{$k}>" class="report_form_bk " onclick='customer_checkbox(this,event);' index='<{$k}>'>
					   
						<td align="center">
							<input type="checkbox" key='<{$k}>' value='<{$o.column_name}>' class='customer_checkbox'  id='customer_checkbox<{$k}>' >
						</td>
						<td align="center"><{$k}></td>
						<td align="left" style="TABLE-LAYOUT: fixed; WORD-BREAK: break-all">
							<div style="TEXT-OVERFLOW: ellipsis; WHITE-SPACE: nowrap; OVERFLOW: hidden; word-wrap: break-word; width: 120px;"><{$o.column_name}></div>
						</td>
						<td align="center" id='td_mapNameValue<{$k}>'>
						    <{if $o.map_title}><{$o.map_title}><{/if}>
						</td>
						<input type="hidden" value="<{$k}>" name="customer_column[<{$k}>][column_id]">
						<input type="hidden" value="<{$o.column_name}>" name="customer_column[<{$k}>][column_name]">
						
					</tr>
					<{/foreach}>
				</tbody>
			</table>
		</div>
		<div style="width: 130px; float: left; margin-top: 200px; margin-left: 10px;position:relative;">
			<div align="center" id="scrollDiv">
				<div style="display: none" id="div_param">
					<{t}>动作<{/t}>:
					<select id="cporct" style="width: 100px;">
						<option value="C" selected="selected"><{t}>直接拷贝<{/t}></option>
						<option value="M"><{t}>字段连接<{/t}></option>
					</select>
				</div>
				<{t}>映射列<{/t}>:
				<input type="text" style="width: 60px;" id="param" value="" name="param">
				<br> <br>
				<input type="button" id="operationBtn" style="width: 60px; cursor: hand;" value="&gt;&gt;&gt;">
			</div>
		</div>
		<div class="report_fmbox_rit" style='position:relative;'>
			
			<table cellspacing="0" cellpadding="0" width="100%" bordercolor="#e1e1e1" border="1" class="report_form table-module">
				<tbody>
					<tr style="background: #fff;">
						<td colspan="4">
							<span style="float: left; padding-left: 100px;"><{t}>标准模板<{/t}></span>
						</td>
					</tr>
					<tr>
						<td width="30px" bgcolor="#D8D8D8" align="center" style="background: #e8eff9; overflow: hidden;"></td>
						<td width="110px" bgcolor="#D8D8D8" align="center" style="background: #e8eff9; overflow: hidden;"><{t}>名称<{/t}></td>
						<td width="70px" bgcolor="#D8D8D8" align="center" style="background: #e8eff9; overflow: hidden;"><{t}>动作<{/t}></td>
						<td width="70px" bgcolor="#D8D8D8" align="center" style="background: #e8eff9; overflow: hidden;"><{t}>映射列<{/t}></td>
						</tr>
						<{foreach from=$standardColumn name=o item=o key=k}>
						<tr id="standardTR_1" class="report_form_bk" onclick='standard_checkbox(this,event);' index='<{$k}>'>
							<td align="center" id="standardTR_<{$o.sc_id}>">
								<input type="checkbox" key='<{$o.sc_id}>' value='<{$o.sc_name}>' class='standard_checkbox' id='standard_checkbox<{$k}>' >
							</td>
							<td align="left"><{$o.sc_name}> <{if $o.sc_require}><span class='msg'>*</span><{/if}></td>
							<td align="center" id="td_mappingType<{$o.sc_id}>">
							     <{if $o.map}><{$o.map.mt_code_title}><{/if}>
							</td>
							<td align="center" id="td_mappingValue<{$o.sc_id}>">
							     <{if $o.map}><{$o.map.mt_value}><{/if}>
							</td>
							
							<input type="hidden" id="standardColumnId<{$o.sc_id}>" value="<{$o.sc_id}>" name="standard_column[<{$o.sc_id}>][sc_id]">
							<input type="hidden" id="mappingType<{$o.sc_id}>" value="<{if $o.map}><{$o.map.mt_code}><{/if}>" name="standard_column[<{$o.sc_id}>][mt_code]">
							<input type="hidden" id="mappingValue<{$o.sc_id}>" value="<{if $o.map}><{$o.map.mt_value}><{/if}>" name="standard_column[<{$o.sc_id}>][mt_value]">
							<input type="hidden" id="standardColumnName<{$o.sc_id}>" value="<{$o.sc_columncode}>" name="standard_column[<{$o.sc_id}>][sc_columncode]"> 
							<input type="hidden" id="standardColumnName<{$o.sc_id}>" value="<{$o.sc_name}>" name="standard_column[<{$o.sc_id}>][sc_name]">
							<input type="hidden" id="standardColumnName<{$o.sc_id}>" value="<{if $o.sc_require}><{$o.sc_require}><{else}>N<{/if}>" name="standard_column[<{$o.sc_id}>][sc_require]">   							<input type="hidden" id="standardColumnName<{$o.sc_id}>" value="<{$o.sc_columncode}>" name="standard_column[<{$o.sc_id}>][sc_columncode]">
							
						</tr>
						<{/foreach}>
					</tbody>
				</table>
			</div>
		</div>
	</form>
</div>