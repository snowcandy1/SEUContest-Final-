<?php
$title = '答题页面';
$csslist = array('main.css');
include 'header.php';
?>
<script src="/static/js/cookie.js" type="text/javascript"></script>
<script src="/static/js/quiz.js" type="text/javascript"></script>
<script>
</script>
<div class="mainform">
	<div id="probsheet" class="formq">
        题目加载中，若长期停留此对话，建议刷新或联系管理员。
	</div>
	<div class="buttons" style="float:right">
		<span id="lastTime" style="font-size:110%"></span>
        <button onclick="changeTest()" class="ui yellow button" style="right:0%">更换试卷</button>
        <button onclick="postAnswer()" class="ui orange button" style="right:0%">提交</button>
	</div>
</div>

<div class="problist">
	<div id="lists" style="height: 100%;overflow-y: scroll;-ms-overflow-style: -ms-autohiding-scrollbar;-webkit-overflow-scrolling: touch;">

	</div>
</div>

</body>
</html>