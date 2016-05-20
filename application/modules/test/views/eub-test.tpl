<style>
#products .input_text {
	width: 75%;
}

#edit_products .input_text {
	width: 75%;
}
</style>
<script type="text/javascript">
	function download()
	{

		$.ajax({
			type : "post",
			async : false,
			dataType : "json",
			url : '/test/eub-test/get-label/',
			data : $("#searchForm").serialize(),
			success : function(json) {
				if (json.state) {
					alertTip("下载完成");
				} else {
					alertTip(json.message);
				}
			}
		});
	}
</script>
<div id="module-container">
	<div id="search-module">
		<form id="searchForm" name="searchForm" method='POST' action='' onsubmit='return false;'>			
			<div class="search-module-condition">
				<span class="">单号 : </span>
				<input type="text" name=code class="input_text keyToSearch" />
				<span class="">API: </span>
				<select name="api">
					<option value="EUBOFFLINE">EUB</option>
					<option value="EUBOFFLINE-CS">长沙EUB</option>
				</select>
				<input type="button" value="<{t}>下载<{/t}>" class="baseBtn" onclick="download()" />
			</div>
		</form>
	</div>
	<div id="module-table">
	</div>
</div>