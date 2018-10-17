<?php

/**
 * 账户类
 * 
 */
class account{

	/** 
	 * @var array|bool
	 * string ['id'] 账号
	 * string ['name'] 名字
	 * string ['password'] 密码（加密后）
	 * array ['errorpass'] 错误尝试
	 * --int ['times'] 错误次数
	 * --int ['lasttry'] 最后尝试时间
	 */
	private $data;

	private $logs;

	private $auth;

	/**
	 * 账户类
	 * 
	 * @param string $id 账号
	 */
	public function __construct($id){
		if(strstr($id,"teacher-")!==false){
			list($this->data,$this->logs)=engageKey(2,$id);
			$this->auth='teacher';
		}
		else if(strstr($id,"admin-")!=false){
			list($this->data,$this->logs)=engageKey(3,$id);
			$this->auth='admin';
		}
		else{
			list($this->data,$this->logs)=engageKey(1,$id);
			$this->auth='student';
		}
		if($this->data===null){
			$this->auth=null;
		}
	}

	/**
	 * 析构函数
	 * 
	 */
	public function __destruct(){
		if(!isset($this->data['id'])){
			return;
		}
		if($this->auth=="student"){
			unengageKey(1,$this->data['id'],$this->data,$this->logs);
		}
		else if($this->auth=="teacher"){
			unengageKey(2,$this->data['id'],$this->data,$this->logs);
		}
		else if($this->auth=="admin"){
			unengageKey(3,$this->data['id'],$this->data,$this->logs);
		}
	}

	/**
	 * 检查可用
	 * 
	 * @return array message
	 */
	public function getStatus(){
		if($this->data===null){
			return false;
		}
		return true;
	}

	/**
	 * 登陆
	 * 
	 * @param string $pass 密码（加密后）
	 * @return message()
	 */
	public function login($pass){

		//没有这个账号
		if($this->data===null){
			return message(REQUEST_OBJECT_NOT_EXSIST,"没有这个账号");
		}

		//试错计时
		if(time()-$this->data['errorpass']['lasttry']>300){
			$this->data['errorpass']['times']=0;
		}

		//试错过多
		if($this->data['errorpass']['times']>5){
			$this->logs[]=mylog("Login forbidden");
			return message(REQUEST_FORBIDDEN,"错误次数过多，五分钟后再试");
		}

		//成功登陆
		if($this->data['password']==$pass){
			$this->logs[]=mylog("Login success");
			$this->data['errorpass']['times']=0;
			return message(REQUEST_SUCCESS,"登陆成功",$this->getData());
		}

		//密码错误
		$this->data['errorpass']['times']++;
		$this->data['errorpass']['lasttry']=time();
		$this->logs[]=mylog("Login failed");
		return message(REQUEST_FAILED,"密码错误");

	}

	/**
	 * 改密码
	 * 参数均为 md5(md5({password})."XSZSJS2018") 的结果
	 * 
	 * @param string $prevpass
	 * @param string $newpass
	 * @return message()
	 */
	public function changePass($prevpass,$newpass){

		//没有这个账号
		if($this->data===null){
			return message(REQUEST_OBJECT_NOT_EXSIST,"没有这个账号");
		}

		//试错计时
		if(time()-$this->data['errorpass']['lasttry']>300){
			$this->data['errorpass']['times']=0;
		}

		//试错过多
		if($this->data['errorpass']['times']>5){
			$this->logs[]=mylog("Change password forbidden");
			return message(REQUEST_FORBIDDEN,"错误次数过多，五分钟后再试");
		}

		//
		if(preg_match("/^[0-9a-f]{32}$/",$newpass)==0){
			$this->logs[]=mylog("Change password failed");
			return message(REQUEST_FAILED,"新密码不合格式");
		}

		//成功登陆
		if($this->data['password']==$prevpass){
			$this->logs[]=mylog("Change password success");
			$this->data['errorpass']['times']=0;
			$this->data['password']=$newpass;
			return message(REQUEST_SUCCESS,"修改成功");
		}

		//密码错误
		$this->data['errorpass']['times']++;
		$this->data['errorpass']['lasttry']=time();
		$this->logs[]=mylog("Change password failed");
		return message(REQUEST_FAILED,"密码错误");
	}

	/**
	 * 资料
	 * 
	 * @return array 
	 */
	public function getData(){

		if($this->data===null){
			return array();
		}

		$rtn=array();

		$rtn['id']=$this->data['id'];
		$rtn['name']=$this->data['name'];
		$rtn['auth']=$this->auth;
		return $rtn;
	}
}


?>