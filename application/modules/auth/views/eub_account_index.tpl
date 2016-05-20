<script type="text/javascript">
    EZ.url = '/auth/eub-account/';
    EZ.getListData = function (json) {
        var html = '';
        var i = paginationCurrentPage < 1 ? 1 : paginationPageSize * (paginationCurrentPage - 1) + 1;
        $.each(json.data, function (key, val) {
            html += (key + 1) % 2 == 1 ? "<tr class='table-module-b1'>" : "<tr class='table-module-b2'>";
            html += "<td class='ec-center'>" + (i++) + "</td>";
            
                    html += "<td >" + val.E1 + "</td>";
                    html += "<td >" + val.E2 + "</td>";
                    html += "<td  style='word-wrap:break-word;width: 200px'>" + val.E3 + "</td>";
            html += "<td><a href=\"javascript:editById(" + val.E0 + ")\">" + EZ.edit + "</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href=\"javascript:deleteById(" + val.E0 + ")\">" + EZ.del + "</a></td>";
            html += "</tr>";
        });
        return html;
    }
</script>
<div id="module-container">
	<div id="ez-wms-edit-dialog" style="display: none;">
		<table class="dialog-module" border="0" cellpadding="0" cellspacing="0">
			<tbody>
				<input type="hidden" name="E0" id="E0" value="" />
				<tr>
					<td class="dialog-module-title">ebay账号:</td>
					<td>
						<input type="text" name="E1" id="E1" class="input_text" />
					</td>
					<td>对应EUB系统“EBAY账号”</td>
				</tr>
				<tr>
					<td class="dialog-module-title">开发者id:</td>
					<td>
						<input type="text" name="E2" id="E2" class="input_text" />
					</td>
					<td>对应EUB系统“API开发者ID”</td>
				</tr>
				<tr>
					<td class="dialog-module-title">开发签名:</td>
					<td>
						<input type="text" name="E3" id="E3" class="input_text" />
					</td>
					<td>对应EUB系统“API签名”</td>
				</tr>
			</tbody>
		</table>
		<table width="100%" cellspacing="1" cellpadding="0" border="0" style="line-height: 150%">
			<tbody>
				<tr>
					<td height="40" colspan="2">
						<table border="0">
							<tbody>
								<tr>
									<td>
										<font color="#808080"><b><img border="0" height="24px" src="/images/header/20120127231732261.png"></b></font>
									</td>
									<td>
										<font color="#E06B26"><h3>eub账号添加指引：</h3></font>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td align="left">
						<table border="0">
							<tbody>
								<tr>
									<td>
										<b><span style="font-size: 16px; font-weight: normal; color: #69f">①</span>添加之前，<font color="#FF3399">必须拥有EUB账号</font>，现在去登录<a href="http://shippingtool.ebay.cn/" target="_blank">http://shippingtool.ebay.cn/！</a></b>
									</td>
								</tr>
								<tr>
									<td>
										<ul class="list_style_1">
											<li>登录说明：必须使用eub账号进行登录，不能使用ebay账号登陆</li>
											<li>
												<font color="#FF3399">没有eub账号？使用ebay账号登陆-》系统设置-》修改国际E邮宝密码-》填写密码-》保存，牢记国际E邮宝账号和密码，现在您可以使用EUB账号进行登陆了！</font>
											</li>
										</ul>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table border="0">
							<tbody>
								<tr>
									<td>
										<b><span style="font-size: 16px; font-weight: normal; color: #69f">②</span>EUB偏好设置</b>
									</td>
								</tr>
								<tr>
									<td>
										<ul class="list_style_1">
											<li>使用EUB账号登陆之后， 选择系统设置-》习惯设定-》订单来源设置-》选择手工上传订单-》保存</li>
										</ul>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table border="0">
							<tbody>
								<tr>
									<td>
										<b><span style="font-size: 16px; font-weight: normal; color: #69f">③</span>添加账号</b>
									</td>
								</tr>
								<tr>
									<td>
										<ul class="list_style_1">
											<li>使用EUB账号登陆之后， 选择系统设置-》ebay帐号管理-》查看API凭证，将您所见的参数信息一一填写到系统中</li>
										</ul>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table border="0">
							<tbody>
								<tr>
									<td>
										<b><span style="font-size: 16px; font-weight: normal; color: #69f">④</span>添加成功</b>
									</td>
								</tr>
								<tr>
									<td>
										<ul class="list_style_1">
											<li>恭喜您，现在可以正常使用EUBAPI了</li>
										</ul>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td></td>
				</tr>
				<tr>
					<td></td>
					<td>&nbsp;</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="search-module">
		<form id="searchForm" name="searchForm" class="submitReturnFalse">
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;">ebay账户：</span>
				<input type="text" name="E1" id="E1" class="input_text keyToSearch" />
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;">开发者id：</span>
				<input type="text" name="E2" id="E2" class="input_text keyToSearch" />
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;">开发签名：</span>
				<input type="text" name="E3" id="E3" class="input_text keyToSearch" />
			</div>
			<div class="search-module-condition">
				<span class="searchFilterText" style="width: 90px;"></span>
				<input type="button" value="<{t}>search<{/t}>" class="baseBtn submitToSearch" />
				<input type="button" id="createButton" value="<{t}>create<{/t}>" class="baseBtn" />
			</div>
		</form>
	</div>
	<div id="module-table">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="table-module">
			<tr class="table-module-title">
				<td width="3%" class="ec-center">NO.</td>
				<td>ebay账号</td>
				<td>开发者id</td>
				<td>开发签名</td>
				<td><{t}>operate<{/t}></td>
			</tr>
			<tbody id="table-module-list-data"></tbody>
		</table>
	</div>
	<div class="pagination"></div>
</div>
