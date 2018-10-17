<?php
session_start();
function tokenGet() {
	$txt = '1234567890abcdef';
	$res = null;
	for ($i = 0; $i < 20; $i++) {
		$needle = substr($txt, mt_rand(0, strlen($txt) - 1), 1);
		$res .= $needle;
	}
	return $res;
}

/*
 @ 以下部分为API调用MySQL时的，如不需要，可以删除

function isExpire($token) {
	global $dbh;
	$sql = 'select * from `token` where `token` = '.$dbh->quote($token).' and `expire` >= '.time();
	$res = $dbh->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	if (isset($res[0])) return $res[0]['uid'];
	return false;
}

function SQLquery($sql) {
	global $dbh;
	$res = $dbh->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	return $res;
}
//*/

function getpasswd($passwd) { // 设置密码的储存格式
	return md5(md5($passwd).'XSZSJS2018');
}


function sendRequest($url,$post='',$cookie='', $returnCookie=0){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
    curl_setopt($curl, CURLOPT_REFERER, "https://yohane.cc");
    if($post) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
    }
    if($cookie) {
        curl_setopt($curl, CURLOPT_COOKIE, $cookie);
    }
    curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    if (curl_errno($curl)) {
        return 'Error('.curl_errno($curl).'): '.curl_error($curl);
    }
    curl_close($curl);
    if($returnCookie){
        list($header, $body) = explode("\r\n\r\n", $data, 2);
        preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
        $info['cookie']  = substr($matches[1][0], 1);
        $info['content'] = $body;
        return $info;
    }else{
        return $data;
    }
}

function setck($k, $v, $expire = 3600) {
	global $useSession, $_SESSION;
	if ($useSession == true)
		$_SESSION[$k] = $v;
	else
		setcookie($k, $v, time() + $expire);
	return;
}

$ExpireTime = 7200;
$useSession = true;
$SCHEME = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
$hostname = $SCHEME.'://'.$_SERVER['HTTP_HOST'].'/';
if ($useSession == true)
	$ck = $_SESSION;
else 
	$ck = $_COOKIE;
