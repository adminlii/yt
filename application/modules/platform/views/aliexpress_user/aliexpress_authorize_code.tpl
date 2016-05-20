<script type="text/javascript" src="/js/zclip/jquery.zclip.min.js"></script>
<script>
$(function(){
	$('#zclip').zclip({
	    path: '/js/zclip/ZeroClipboard.swf',
	    copy: function(){//复制内容
	        return $("#aliexpress_code").val();
	    },
	    afterCopy: function(){//复制成功
	        //$("<span id='msg'/>").insertAfter($('#copy_input')).text('复制成功');
	    	alert($.getMessage('copy_success'));
	    }
	});
});
</script>
<style>
body {
    background-color: #eeeeee;
}
.submitReturnFalse{
	background-color: #ffffff;
}
</style>
<div id="module-container">
	<form style="margin: 25px auto; width: 650px; border: 2px dashed #BACFC4; padding: 25px 30px;" onsubmit="return false;" class="submitReturnFalse" name="searchForm">
		<h2 style="color:#E06B26;">敬请注意：</h2>
		<{if isset($aliexpress_code) && $aliexpress_code != ''}>
			<div class="search-module-condition">1、看到该页面，表示您授权操作成功</div>
			<div class="search-module-condition">2、请复制[<font style="color:#478FCA;">临时授权码</font>]，返回之前的页面继续操作</div>
			<div class="search-module-condition">PS：下方的一长串字符，为速卖通返回的临时授权码</div>
			<div style="padding:20px 0;" class="search-module-condition">
				<span class="" style="color:#478FCA;">临时授权码：</span>&nbsp;&nbsp;
				<input type="text" style="width:280px;" class="input_text" id="aliexpress_code" name="aliexpress_code" value="<{$aliexpress_code}>" disabled="disabled">
				&nbsp;&nbsp;
				<input type="button" class="baseBtn submitToSearch" id="zclip" value="复制">
			</div>
		<{else}>
			<div class="search-module-condition" style="font-size: 18px;color: red;text-align: center;">授权失败，未能拿到临时授权码.</div>
		<{/if}>
	</form>
<div data-show="false" class="to_top_div">
<span onclick="toTop();" class="iconToTop" title="返回顶部" style="float: left;cursor: pointer;padding: 0px 2px;"></span><span onclick="toBottom();" class="iconToBottom" title="前往底部" style="float: left;cursor: pointer;padding: 0px 2px;"></span></div></div>