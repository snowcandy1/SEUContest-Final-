<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name=viewport content="width=device-width,initial-scale=1">
<title>东大首家线上自测，基佬在线发题</title>
<link href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.bootcss.com/bootflat/2.0.4/css/bootflat.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Inconsolata" rel="stylesheet">
<style>body{font-family:Arial,微软雅黑,Microsoft Jhenghei,monospace;background:#eee;margin-bottom:140px;}</style>

</head>
<body>
<nav class="navbar navbar-inverse" role="navigation">
<div class="container-fluid">
 
<div class="navbar-header">
<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
<span class="sr-only">Toggle navigation</span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
<a class="navbar-brand" href="/">Yohane.cc</a>
</div>
<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
<ul class="nav navbar-nav">
<li><a href="/seu">首页</a></li>
<li><a href="/seu/teacher/cur.php">查课程</a></li>
<li><a href="/seu/teacher/cur_2.php">查班级</a></li>


</ul>
</li>
</ul>
<form class="navbar-form navbar-right" role="search">
<div class="form-search search-only">
<i class="search-icon glyphicon glyphicon-search"></i>
<input type="text" class="form-control search-query">
</div>
</form>
</div> 
</div> 
</nav>
<div class="container">
<div class="thumbnail">
<div class="caption text-center">

<h1><font size="200%">试题自测</font></h1>
<marquee>东大首家线上自测题库，基佬在线发题</marquee>
<?php
error_reporting(0);
echo '<h2>当前题库：系统解剖学</h2>';
$sel = isset($_GET['sel']) ? $_GET['sel'] : 0;
if ($sel >= 2) {echo '<h2>当前为顺序模式，<a href="index.php">点击恢复正常</a>或<a href="index.php?sel=3">切换为顺序单选模式</a></h2>';}
elseif ($sel != 1){echo '<h2>此题库含有“多选题”，<a href="index.php?sel=1">点击切换为仅单选模式</a>或<a href="index.php?sel=2">切换为顺序模式</a></h2>';}
else {echo '<h2>当前为仅单选模式，<a href="index.php">点击恢复正常</a></h2>';}
$dbh = new PDO('sqlite:question.db');
$sql = "select * from `q1`";
$res = $dbh->query($sql)->fetchAll(PDO::FETCH_ASSOC);
$c = @count($res);
if($sel>=2) {
		echo '<form name="form2" method="post"><div class="input-group form-search">
<input type="text" class="form-control search-query" name="id" placeholder="跳题目">
<span class="input-group-btn">
<button type="submit" class="btn btn-primary" name="sub">跳！</button>
</span>
</div></form>';
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	//print_r($_POST);exit;
	$id = $_POST['id'];$q = $res[$id];
	$ans = $res[$id]['ans'];
	$myans = trim(strtoupper($_POST["answer"]));
	if(!$myans){
		if($_POST['sel_a']) $myans .= 'A';
		if($_POST['sel_b']) $myans .= 'B';
		if($_POST['sel_c']) $myans .= 'C';
		if($_POST['sel_d']) $myans .= 'D';
		if($_POST['sel_e']) $myans .= 'E';
	}
	if($myans == $ans) echo '<h2><font color="#0000FF">回答正确！正确答案：'.$ans.'</font></h2>';
	else {
		echo '<script src="http://ajax.aspnetcdn.com/ajax/jquery/jquery-1.11.1.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
  $("#hide").click(function(){
  $("#myans").hide(300);
  });
  $("#show").click(function(){
  $("#myans").show(300);
  });
});
</script>
</head>
<body>
<h2><font color="#FF0000"><p id="myans" style="line-height:180%;display:none">'.$q['id'].".".$q['question'].'<br>A.'.$q['c1'].'<br>B.'.$q['c2'].'<br>C.'.$q['c3'].'<br>D.'.$q['c4'].'<br>E.'.$q['c5'].'</p></font></h2>
<button id="hide" type="button">隐藏错题</button>
<button id="show" type="button">显示错题</button>
</body>';
		echo '';
		echo '<h2><font color="#FF0000">回答错误：正确答案：'.$ans.'</font><br>你的答案：'.$myans.'</h2>';
	}
}
suba:
	$id = ($sel<=2)?(mt_rand(0,$c-1)):($id+1);$q = $res[$id];
	if (($sel == 1 or $sel == 3) and mb_strlen($q['ans'])>1) goto suba;
if(mb_strlen($q['ans'])>1) $q['question'] .= '<b>（多选）</b>';
echo '<form name="form1" method="post">';
echo '<input type="hidden" name="id" value="'.$id.'">
<p style="line-height:180%">'.$q['id'].".".$q['question'].'<br>
<input type="checkbox" name="sel_a">A.'.$q['c1'].'<br>
<input type="checkbox" name="sel_b">B.'.$q['c2'].'<br>
<input type="checkbox" name="sel_c">C.'.$q['c3'].'<br>
<input type="checkbox" name="sel_d">D.'.$q['c4'].'<br>
<input type="checkbox" name="sel_e">E.'.$q['c5'];echo '</p>
<div class="input-group form-search">
<input type="text" class="form-control search-query" name="answer" placeholder="输入答案或点击">
<span class="input-group-btn">
<button type="submit" class="btn btn-primary" name="sub">提交</button>
</span>
</div>
</form>
';

?>
</div>
</div>
</div>
<div style="text-align: center;"><script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1262018087'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s13.cnzz.com/z_stat.php%3Fid%3D1262018087%26online%3D1%26show%3Dline' type='text/javascript'%3E%3C/script%3E"));</script></div>
</html>