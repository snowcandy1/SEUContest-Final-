<?php
$title = '设置';
$csslist = array('main.css', 'table.css');
include 'header.php';
?>
<script src="/static/js/cookie.js" type="text/javascript"></script>
<script src="/static/js/settings.js" type="text/javascript"></script>
<script>
    var dep = new Array(1000);
    var details = new Array(1000);
</script>
<div class="ui modal">
    <div class="header" id="utitle">学院名</div>
    <div class="scrolling content">
        <h2>分数统计：</h2>
        <div id="ucon"></div>
    </div>
    <div class="actions">
        <div class="ui approve button">确定</div>
    </div>
</div>
<div class="mainform">
    <div id="probsheet" class="formq">
    <div id="changepswd">
        <h2>修改密码</h2>
        <p>旧密码：<input type="password" id="oldpass" style="border:0;border-radius: 5px;text-shadow: 0 0 2px black;"/></p>
        <p>新密码：<input type="password" id="newpass" style="border:0;border-radius: 5px;text-shadow: 0 0 2px black;"/></p>
        <p>确认新密码：<input type="password" id="newpass2" style="border:0;border-radius: 5px;text-shadow: 0 0 2px black;"/></p>
        <p>
            <button onclick="changePsWd();" class="ui orange button">修改</button>
        </p>
    </div>
    <div hidden id="scorelists">
        这个界面就不写了-。=
    </div>
    </div>
</div>

<div class="problist">
	<div id="lists" style="height: 100%;overflow-y: scroll;-ms-overflow-style: -ms-autohiding-scrollbar;-webkit-overflow-scrolling: touch;">
        <p><a href="#" onclick="$('#changepswd').show(500);$('#scorelists').hide(500);">修改密码</a></p>
        <p><a href="#" onclick="$('#changepswd').hide(500);$('#scorelists').show(500);">捐助我们</a></p>
	</div>
</div>

</body>
</html>