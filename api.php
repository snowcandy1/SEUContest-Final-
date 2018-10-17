<?php

session_start();
ini_set("error_reporting","E_ALL & ~E_NOTICE");

header('Content-type: application/json');

include_once "./include.php";

$api_user=null; // 新建对象用这个！！会在return_request()中析构！！！

/**
 * return json message
 * die soon
 * 
 * @param array $message
 */
 
function getpass($str) {
	return md5(md5($str).'XSZSJS2018');
}
 
function return_request($message){
	if(!is_array($message)){
		$message=message(ERR_UNKNOWN_REQUEST,"未知命令");
	}
	unset($GLOBALS['api_user']); // 在这里析构！！！重要！！！
	echo json_encode($message,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
	die();
}

$server=new server();
if($server->getStatus()<server::TEST_STARTED){
	return_request(message(ERR_SERVER_NOT_WORKING,$server->getNote()));
}

if (isset($_SESSION['login'])){
	$login = $_SESSION['login'];
	$auth = $_SESSION['auth'];
}

/**
 * check param
 * @param ...string $params
 */
function checkParam(...$params){
	foreach($params as $param){
		if(!isset($_REQUEST[$param])){
			return_request(message(ERR_MISSING_PARAM,"missing param $param"));
		}
	}
}

/**
 * check auth
 * @param string $need
 */
function checkAuth($need){
	global $auth;
	if($auth!=$need){
		return_request(message(ERR_UNAUTHED,"need auth $need"));
	}
}

checkParam('key');

switch($_REQUEST['key']){

	/**
	 * @登录API：
	 *   code：登录成功 0
	 *         账号不存在 2000
	 * 	       密码错误 1000
	 *         禁止 10000
	 */
	case 'login':
		checkParam('account', 'password');
		$api_user = new account($_REQUEST['account']);
		$message=$api_user->login($_REQUEST['password']);
		if($message['code']==REQUEST_SUCCESS){
			$data=$api_user->getData();
			$_SESSION['login']=$data['id'];
			$_SESSION['name']=$data['name'];
			$_SESSION['auth']=$data['auth'];
		}
		return_request($message);
	
	/**
	 * @申诉API：
	 *   code：申诉成功 0
	 *         申诉失败 10000
	 */
	case 'appeal':
		checkParam('account', 'content');
		return_request(createAppeal($_REQUEST['account'],$_REQUEST['content']));

	/**
	 * @查询申诉状态：
	 *   code: 申诉成功 0 返回申诉处理结果
	 *         申诉号不存在 2000
	 *         尚未处理 1000
	 */
	case 'query_appeal':
		checkParam('account','number');
		return_request(queryAppeal($_REQUEST['account'],$_REQUEST['number']));

	/**
	 * @登出
	 */
	case 'logout':
		unset($_SESSION['login']);
		unset($_SESSION['auth']);
		unset($_SESSION['name']);
		return_request(message(REQUEST_SUCCESS,"注销成功"));

	/**
	 * 改密码
	 */
	case 'change_my_password':
		if(!isset($login)){
			return_request(message(REQUEST_FAILED,"没有登陆"));
		}
		checkParam('prevpass','newpass');
		$api_user=new account($login);
		return_request($api_user->changePass(getpass($_REQUEST['prevpass']),getpass($_REQUEST['newpass'])));

	/**
	 * 有改动
	 * 合并了获取之前答案
	 */
	case 'check_status':
		checkAuth("student");
		$api_user = new student($login);
		return_request($api_user->getData());

	/**
	 * 
	 */
	case 'load_test':
		checkAuth("student");
		$api_user=new student($login);
		return_request($api_user->getTest());

	/**
	 * 
	 */
	case 'submit_one':
		checkParam('order','answer');
		checkAuth("student");
		$api_user=new student($login);
		return_request($api_user->submitOne($_REQUEST['order'],$_REQUEST['answer']));

	/**
	 * 
	 */
	case 'finish_test':
		checkAuth("student");
		$api_user=new student($login);
		return_request($api_user->finish());
		
	/**
	 * 
	 */
	case 'change_test':
		checkAuth("student");
		$api_user=new student($login);
		return_request($api_user->changeTest());

	/**
	 * 
	 */
	case 'statistics':
		checkAuth("teacher");
		$api_user=new teacher($login);
		return_request($api_user->getAnalysis());

	/**
	 * 
	 */
	case 'score_list':
		checkAuth("teacher");
		$api_user=new teacher($login);
		return_request($api_user->getScores());

	/**
	 * 
	 */
	case 'get_appeals':
	    checkAuth("admin");
		$api_user=new admin($login);
		return_request($api_user->getQueue());

	/**
	 * 
	 */
	case 'deal_appeal':
		checkParam('number');
	    checkAuth("admin");
		$api_user=new admin($login);
		return_request($api_user->startDeal($_REQUEST['number']));

	/**
	 * 
	 */
	case 'finish_appeal':
		checkParam('result');
	    checkAuth("admin");
		$api_user=new admin($login);
		return_request($api_user->endDeal($_REQUEST['result']));
	
	/**
	 * 
	 */
	case 'dealing':
	    checkAuth("admin");
		$api_user=new admin($login);
		return_request($api_user->getDealing());

	/**
	 * 
	 */
	case 'change_password':
		checkParam('newpassword');
	    checkAuth("admin");
		$api_user=new admin($login);
		return_request($api_user->adminChangePass($_REQUEST['newpassword']));
	
	/**
	 * 
	 */
	case 'create_account':
		checkParam('name','newpassword');
	    checkAuth("admin");
		$api_user=new admin($login);
		return_request($api_user->createStudent($_REQUEST['name'],$_REQUEST['newpassword']));

	/**
	 * 
	 */
	default:
		return_request(message(ERR_UNKNOWN_REQUEST,"未知命令"));

}

?>