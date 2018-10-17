<?php
include 'function.php';
if (isset($_SESSION['auth'])) {
    if ($_SESSION['auth'] == 'student')
       header("Location: /test");
    elseif ($_SESSION['auth'] == 'admin')
		header("Location: /panel");
	else
        header("Location: /stats");
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	?>
	<script src="/static/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="/static/js/login.js" type="text/javascript"></script>
    <script>userLogin('<?=addslashes($_POST['logname'])?>', '<?=addslashes(getpasswd($_POST['logpass']))?>');</script>
	<?php
} else {
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<link rel="stylesheet" href="/static/css/login.css">
</head>
<body>

<script src="/static/js/jquery-1.7.2.min.js" type="text/javascript"></script>

<div class="xwbox">
	<h3>Login</h3>
	<form action="#" id="log1n" name="f" method="post">
		<div class="input_outer">
			<span class="u_user"></span>
			<input name="logname" class="text" onFocus=" if(this.value=='User') this.value=''" onBlur="if(this.value=='') this.value='User'" value="User" style="color: #FFFFFF !important" type="text">
		</div>
		<div class="input_outer">
			<span class="us_uer"></span>
			<label class="l-login login_password" style="color: rgb(255, 255, 255);display: block;">Password</label>
			<input name="logpass" class="text" style="color: #FFFFFF !important; position:absolute; z-index:100;" onFocus="$('.login_password').hide()" onBlur="if(this.value=='') $('.login_password').show()" value="" type="password">
		</div>
		<div class="mb2"><a class="act-but submit" href="#" onclick="document.getElementById('log1n').submit()" style="color: #FFFFFF">登录</a></div>
		<!--<input name="savesid" value="0" id="check-box" class="checkbox" type="checkbox"><span>记住用户名</span>-->
	</form>
	<div class="sas">
		<a href="/userappeal">申诉</a>
	</div>
	
</div>

</body>
</html>
<?php
}