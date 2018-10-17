<?php
$title = '统计';
$csslist = array('main.css', 'table.css');
include 'header.php';
?>
<script src="/static/js/cookie.js" type="text/javascript"></script>
<script src="/static/js/scorestat.js" type="text/javascript"></script>
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
    <div id="scorestats"></div>
    <div hidden id="scorelists">
        <button onclick="$('.unfinished').show(500);" class="ui red button">显示全部</button>
        <button onclick="$('.unfinished').hide(500);" class="ui green button">隐藏未完成</button>
        <input type="text" placeholder="搜索学号" id="numsearch" />
        <div id="scorelistsin"></div>
    </div>
    </div>
</div>

<div class="problist">
	<div id="lists" style="height: 100%;overflow-y: scroll;-ms-overflow-style: -ms-autohiding-scrollbar;-webkit-overflow-scrolling: touch;">
        <p><a href="#" onclick="$('#scorestats').show(500);$('#scorelists').hide(500);">全校成绩</a></p>
        <p><a href="#" onclick="$('#scorestats').hide(500);$('#scorelists').show(500);">本院成绩</a></p>
        <p><a href="#" onclick="exportcsv();">导出成绩</a></p>
	</div>
</div>

</body>
</html>