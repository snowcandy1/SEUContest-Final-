<?php
$title = '试题选择';
$csslist = array('main.css', 'table.css');
$BG = '/static/img/bg2.jpg';
include 'header.php';
?>
<script src="/static/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="/static/js/stu.main.js" type="text/javascript"></script>
<div class="mainform">
    <div class="table-responsive">
        <table class="table table-condensed">
            <thead>
            <tr>
                <td>序号</td>
                <td>考试名称</td>
                <td>开始时间</td>
                <td>最后提交时间</td>
                <td>当前状态</td>
                <td id="scoreres">得分</td>
                <td></td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>1</td>
                <td>东南大学校史知识竞赛</td>
                <td id="time_start"></td>
                <td id="time_end"></td>
                <td id="now_status">Pending</td>
                <td id="scores">---</td>
                <td id="enter"><a href="/quiz">进入</a></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="problist">

</div>