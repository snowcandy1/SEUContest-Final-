<?php
$title = '管理界面';
$csslist = array('main.css', 'table.css');
include 'header.php';
?>
<script src="/static/js/cookie.js" type="text/javascript"></script>
<script src="/static/js/admins.js" type="text/javascript"></script>
<script>
    var dep = new Array(1000);
    var details = new Array(1000);
</script>
<div class="ui modal">
    <div class="header" id="utitle">申诉记录</div>
    <div class="scrolling content" id="ucon">

    </div>
    <div class="actions">
        <div class="ui approve button">确定</div>
    </div>
</div>
<div class="mainform">
    <div id="probsheet" class="formq">
    <div id="dealappeal">

    </div>
    <div hidden id="scorelists">
        <h2>创建账号</h2>
        <p>账号：<input type="text" id="account" style="border:0;border-radius: 5px;text-shadow: 0 0 2px black;"/></p>
        <p>密码：<input type="password" id="pass" style="border:0;border-radius: 5px;text-shadow: 0 0 2px black;"/></p>
        <p>
            <button onclick="createacc();" class="ui orange button">创建</button>
        </p>
    </div>
    </div>
</div>

<div class="problist">
	<div id="lists" style="height: 100%;overflow-y: scroll;-ms-overflow-style: -ms-autohiding-scrollbar;-webkit-overflow-scrolling: touch;">
        <p><a href="#" onclick="$('#dealappeal').show(500);$('#scorelists').hide(500);">处理申诉</a></p>
        <p><a href="#" onclick="$('#dealappeal').hide(500);$('#scorelists').show(500);">创建账号</a></p>
	</div>
</div>

</body>
</html>