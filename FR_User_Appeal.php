<?php
$title = '用户申诉';
$csslist = array('main.css', 'table.css');
$BG = '/static/img/bg2.jpg';
include 'header.php';
?>
<script src="/static/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="/static/js/login.js" type="text/javascript"></script>
<div class="mainform">
    <div class="appeal">
        <h3>您的账号因多次密码错误，需要进行申诉。</h3>
        <p>用户名：<input id="uname" type="text" onkeydown="if(event.keyCode==13) userAppeal();"></p>
        <p>申诉信息:<input id="umess" type="text" onkeydown="if(event.keyCode==13) userAppeal();"></p>
        <button class="ui orange button" onclick="userAppeal()">提交</button>
        <div id="showresu1t"></div>
    </div>
    <div hidden class="queryapp">
        <h3>查询申诉</h3>
        <p>用户名：<input id="unames" type="text" onkeydown="if(event.keyCode==13) queryAppeal();"></p>
        <p>回执编号：<input id="unum" type="text" onkeydown="if(event.keyCode==13) queryAppeal();"></p>
        <button class="ui orange button" onclick="queryAppeal()">提交</button>
        <div id="showresult"></div>
    </div>
</div>
<div class="problist">
    <p><a href="#" onclick="$('.appeal').show(500);$('.queryapp').hide(500);">申诉</a></p>
    <p><a href="#" onclick="$('.appeal').hide(500);$('.queryapp').show(500);">查询申诉</a></p>

</div>