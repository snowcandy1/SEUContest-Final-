<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>东南大学校史知识竞赛</title>
<link rel="stylesheet" href="/static/css/login.css">
</head>
<script src="/static/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<body>

<div class="xwbox" style="text-align:center">
	<h1>东南大学</h1><h1>校史知识竞赛</h1>
	<h2 id="nowtime"><?=date("H:i:s");?></h2>
	<div class="mb2"><a class="act-but submit" href="/login.php" style="color: #FFFFFF">进入</a></div>
		
</div>

</body>
</html>

<script>
function a() {
	var x = Math.floor(Math.random() * 16) + 1;
	$(top.document.body).css("background", "url('/static/img/seu/" + x + ".jpg') fixed center center");
	return x;
}
a();setInterval("a()", 5000);
function append_0(x) {
	if (x < 10) return "0" + x;
	return x;
}
function getRTime(){ 
	var t = new Date().getTime() + 28800000; 
	var h=Math.floor(t/1000/60/60%24); 
	var m=Math.floor(t/1000/60%60); 
	var s=Math.floor(t/1000%60); 
	var nnn = h + ":"+append_0(m) + ":"+ append_0(s); 
	document.getElementById("nowtime").innerHTML=nnn;
} 
setInterval("getRTime()",1000); 
</script>