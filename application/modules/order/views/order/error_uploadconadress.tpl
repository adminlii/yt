
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<link rel="stylesheet" href="/css/bootstrap.min.css">
	<meta http-equiv="content-type" content="text/html;charset=utf-8">
	<style>
*{ font-family:"微软雅黑";font-size:14px;padding:0;margin:0;}
p{margin-left:14px;}
	</style>
</head>
<body>
	<div>
	<ul>
	<{foreach from=$html name=c item=c}>
		<li class="list-group-item"><{$c}></li>
	<{/foreach}>
	</ul>
	<p><span>3</span>秒后跳转到收件人地址簿</p>	
	</div>
<script type='text/javascript'>
setTimeout("window.location.href='/order/order/consignee-adress'",3000);

</script>
</body>
</html>
