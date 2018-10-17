<?php

/**
 * 管理员类
 * 
 */
class admin{

	private $data;
	private $logs;

	/**
	 * 管理员类
	 * 
	 * @param string $id
	 */
	public function __construct($id){
		list($this->data,$this->logs)=engageKey(3,$id);
		if($this->data==null){
			return;
		}
	}

	public function __destruct(){
		if($this->data==null){
			return;
		}
		unengageKey(3,$this->data['id'],$this->data,$this->logs);
	}

	/**
	 * 获取队列
	 * 
	 * @return message()
	 */
	public function getQueue(){
		$keys=getKeys(5,"[0-9]*");
		$result=[];
		foreach($keys as $key){
			$appeal=getKeyData(5,$key);
			if($appeal['status']=="not dealing"){
				unset($appeal['status']);
				$result[]=$appeal;
			}
		}
		return message(REQUEST_SUCCESS,"请求成功",$result);
	}

	/**
	 * 获取正在处理
	 * 
	 * @return message()
	 */
	public function getDealing(){
		if($this->data['dealing']==null){
			return message(REQUEST_OBJECT_NOT_EXSIST,"没有正在处理的请求");
		}
		$result['appeal']=getKeyData(5,$this->data['dealing']);
		$account=getKeyAll(1,$result['appeal']['account']);
		if($account!=null){
			$result['account']['name']=$account[0]['name'];
			$result['account']['studentid']=$account[0]['info']['studentid'];
			$result['account']['major']=$account[0]['info']['major'];
			$result['accountlogs']=$account[1];
		}

		return message(REQUEST_SUCCESS,"请求成功",$result);
	}

	/**
	 * 开始处理
	 * 
	 * @param string $id
	 * @return message()
	 */
	public function startDeal($id){
		if($this->data['dealing']!=null){
			return message(REQUEST_FORBIDDEN,"正在处理上一个申诉");
		}
		list($appeal,$logs)=engageKey(5,$id);
		if($appeal==null){
			return message(REQUEST_OBJECT_NOT_EXSIST,"没有这个申诉");
			unengageKey(5,$id);
		}
		if($appeal['status']!=="not dealing"){
			return message(REQUEST_FORBIDDEN,"这个申诉已经受理");
			unengageKey(5,$id);
		}

		$logs[]=mylog("Started dealing by admin {$this->data['name']}");
		$appeal['status']="dealing";

		$this->data['dealing']=$id;
		$this->logs[]=mylog("Started dealing appeal {$id}");

		unengageKey(5,$id,$appeal,$logs);

		return message(REQUEST_SUCCESS,"请求成功");
		
	}

	/**
	 * 结束处理
	 * 
	 * @param string $result
	 * @return message()
	 */
	public function endDeal($result){
		if($this->data['dealing']==null){
			return message(REQUEST_FAILED,"没有正在解决的申诉");
		}
		list($appeal,$logs)=engageKey(5,$this->data['dealing']);
		$appeal['status']="dealt";
		$logs[]=mylog("End dealing. Result : $result");
		
		unengageKey(5,$this->data['dealing'],$appeal,$logs);
		$this->logs[]=mylog("Ended dealing appeal {$this->data['dealing']}");
		$this->data['dealing']=null;

		return message(REQUEST_SUCCESS,"请求成功");
	}

	/**
	 * 改密码
	 * 
	 * @param string $newpass
	 * @return message()
	 */
	public function adminChangePass($newpass){
		if($this->data['dealing']==false){
			return message(REQUEST_FAILED,"没有正在解决的申诉");
		}
		if(preg_match("/^[0-9a-f]{32}$/",$newpass)==0){
			return message(REQUEST_FAILED,"新密码不合格式");
		}
		list($appeal,$appeallogs)=engageKey(5,$this->data['dealing']);
		list($account,$accountlog)=engageKey(1,$appeal['account']);
		if($account==null){
			unengageKey(5,$this->data['dealing']);
			return message(REQUEST_FAILED,"申诉账户不存在");
		}
		$account['password']=$newpass;
		$account['errorpass']['trytimes']=0;
		$appeallogs[]=mylog("Changed password");
		$accountlog[]=mylog("Changed password by {$this->data['name']}");
		unengageKey(5,$this->data['dealing'],$appeal,$appeallogs);
		unengageKey(1,$appeal['account'],$account,$accountlog);
		return message(REQUEST_SUCCESS,"修改密码成功");
	}

	/**
	 * 创建学生账号
	 * 
	 * @param string $name
	 * @param string $studentid
	 * @return message()
	 */
	public function createStudent($name,$studentid){
		if($this->data['dealing']==false){
			return message(REQUEST_FAILED,"没有正在解决的申诉");
		}
		if(preg_match("/^[0-9]{8}$/",$studentid)==0){
			return message(REQUEST_FAILED,"学号不合格式");
		}
		list($appeal,$appeallogs)=engageKey(5,$this->data['dealing']);
		if(keyExists(1,$appeal['account'])){
			unengageKey(5,$this->data['dealing']);
			return message(REQUEST_FAILED,"申诉账户已存在");
		}
		$teachers=getKeys(2);
		foreach($teachers as $teacher){
			$teacher=getKeyData(2,$teacher);
			foreach($teacher['info']['majors'] as $major){
				if(strstr($major,substr($studentid,0,3))!==false){
					$student['id']=$appeal['account'];
					$student['name']=$name;
					$student['password']=md5(md5($studentid)."XSZSJS2018");
					$student['errorpass']['times']=0;
					$student['errorpass']['lasttry']=0;
					$student['info']['department']=$teacher['info']['department'];
					$student['info']['major']=$major;
					$student['info']['studentid']=$studentid;
					$student['test']['status']=student::NOT_STARTED;
					$student['test']['exampaper']=newTest();
					$student['test']['starttime']=0;
					$student['test']['submittime']=0;
					$student['test']['answersheet']=[];
					$student['test']['score']=0;
					$logs[]=mylog("Created by admin {$this->data['name']}");
					unengageKey(1,$appeal['account'],$student,$logs);
					$appeallogs[]=mylog("Created account");
					unengageKey(5,$this->data['dealing'],$appeal,$appeallogs);
					$this->logs[]=mylog("Created account {$appeal['account']}");
					return message(REQUEST_SUCCESS,"创建账号成功");
				}
			}
		}
		unengageKey(5,$this->data['dealing']);
		return message(REQUEST_FAILED,"找不到老师");
	}
}

?>