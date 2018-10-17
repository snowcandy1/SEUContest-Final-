<?php

/**
 * 服务器类
 * 
 */
class server{

	const NOT_WORKING = 0;
	const DEBUGGING = 100;
	const TEST_STARTED = 200;
	const TEST_ENDED = 400;
	
	/**
	 * @var array 
	 * 数据
	 * 
	 * string ['status'] 状态
	 * string ['note'] 说明
	 */
	private $data;

	/**
	 * @var int
	 * 
	 * 状态
	 * 
	 */
	private $status;

	/**
	 * 服务器类
	 * 
	 */
	public function __construct(){
		if(my_redis()===false){
			$this->status=server::NOT_WORKING;
			return;
		}
		$this->data=getKeyData(0,'server');
		if($this->data===false){
			$this->status=server::NOT_WORKING;
			return;
		}
		$this->status=$this->data['status'];
	}

	/**
	 * 析构函数
	 * 
	 */
	public function __destruct(){
		
	}

	/**
	 * 获取状态
	 * 
	 * @return int 
	 */
	public function getStatus(){
		return $this->status;
	}

	/**
	 * 获取说明
	 * 
	 * @return string
	 */
	public function getNote(){
		if($this->status==server::NOT_WORKING){
			return "数据服务器未开始工作";
		}
		return $this->data['note'];
	}

}


?>