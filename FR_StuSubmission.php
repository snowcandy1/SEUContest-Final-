<?php
$title = '试卷提交界面';
$csslist = array('main.css');
include 'header.php';
?>
<script src="/static/js/cookie.js" type="text/javascript"></script>
<script src="/static/js/submission.js" type="text/javascript"></script>
<script>
</script>
<div class="mainform" style="text-align:center">
	<h1 id="res">正在提交</h1>
    <h3>正确的题目数</h3>
    <p id="resscore" style="font-size:650%"></p>
</div>

<div class="problist">
	<div id="lists" style="height: 100%;overflow-y: scroll;-ms-overflow-style: -ms-autohiding-scrollbar;-webkit-overflow-scrolling: touch;">

	</div>
</div>

</body>
</html>