<?php

/**
 * 学生类
 * 
 */
class student{

	const NOT_STARTED = 100;
	const IN_PROGRESS = 200;
	const FINISHED = 400;

	/**
	 * 数据
	 * 
	 * @var array 
	 * string ['id'] 学号
	 * int ['status'] 状态
	 * int ['starttime'] 开始时间
	 * int ['submittime'] 结束时间
	 * array ['exampaper'] 试卷
	 * --array [n(0..29)] 题目
	 * ----string ['id'] 题目id
	 * ----int ['arrange'] 序列
	 * array ['answersheet'] 答卷
	 * --string [n(1..30)] 一个答案
	 * int ['score'] 分数
	 * 
	 */
	private $data;

	private $logs;

	/**
	 * 学生类
	 * 
	 * @param string 学号
	 */
	public function __construct($id){
		list($this->data,$this->logs)=engageKey(1,$id);
	}

	/**
	 * 析构函数
	 * 
	 */
	public function __destruct(){
		if($this->data===null){
			return;
		}
		unengageKey(1,$this->data['id'],$this->data,$this->logs);
	}

	/**
	 * 获取试卷
	 * 返回 message
	 * 
	 * @return message()
	 */
	public function getTest(){
		if($this->data===null){
			return message(REQUEST_OBJECT_NOT_EXSIST,"没有此学生");
		}
		if($this->data['test']['status']==student::FINISHED){
			return message(REQUEST_FORBIDDEN,"考试已完成");
		}
		foreach($this->data['test']['exampaper'] as $id=>$ques){
			$questions[$id]=getKeyData(4,$ques['id']);
		}
		foreach($this->data['test']['exampaper'] as $id=>$ques){
			$rtn[$id]['question']=$questions[$id]['question'];
			$rtn[$id]['category']=$questions[$id]['category'];
			//打乱选项
			if($questions[$id]['category']=='1'){
				$arrange=student::arrange($ques['arrange']);
				foreach($arrange as $a=>$b){
					$rtn[$id]['options'][$a]=$questions[$id]['options'][$b];
				}
			}
		}
		if($this->data['test']['status']==student::NOT_STARTED){
			$this->data['test']['starttime']=time();
			$this->data['test']['status']=student::IN_PROGRESS;
			$this->logs[]=date('Y-m-d H:i:s : ')."Start test";
		}
		$this->logs[]=date('Y-m-d H:i:s : ')."Get test";
		return message(REQUEST_SUCCESS,"请求成功",$rtn);
	}

	/**
	 * 提交一个题的答案
	 * 
	 * @param string $id 题目id
	 * @param string $ans 题目答案
	 * @return message()
	 */
	public function submitOne($id,$ans){
		if($this->data===null){
			return message(REQUEST_OBJECT_NOT_EXSIST,"没有此学生");
		}
		if($this->data['test']['status']!=student::IN_PROGRESS){
			return message(REQUEST_FORBIDDEN,"没有开始考试");
		}
		if(time()-$this->data['test']['starttime']>1800){
			return message(REQUEST_FORBIDDEN,"已超时");
		}
		if(!array_key_exists($id,$this->data['test']['exampaper'])
			or !in_array($ans,array('1','2','3','4'))){
			return message(REQUEST_FAILED,"提交有误");
		}
		if($this->data['test']['exampaper'][$id]['arrange']!=-1){
			$arrange=student::arrange($this->data['test']['exampaper'][$id]['arrange']);
			$ans=$arrange[$ans];
		}
		$this->data['test']['answersheet'][$id]=$ans;
		$this->logs[]=date('Y-m-d H:i:s : ')."Submit qustion $id id "
			."{$this->data['test']['exampaper'][$id]['id']} answer $ans";

		return message(REQUEST_SUCCESS,"提交成功",$this->getData()['data']);
	}

	/**
	 * 完成试卷
	 * 
	 * @return message()
	 */
	public function finish(){
		if($this->data===null){
			return message(REQUEST_OBJECT_NOT_EXSIST,"没有此学生");
		}
		if($this->data['test']['status']!=student::IN_PROGRESS){
			return message(REQUEST_FORBIDDEN,"没有进行考试");
		}
		foreach($this->data['test']['exampaper'] as $id => $ques){
			$questions[$id]=getKeyData(4,$ques['id']);
		}
		$score=0;
		foreach($questions as $id=>$ques){
			if(isset($this->data['test']['answersheet'][$id]) and $ques['answer']==$this->data['test']['answersheet'][$id]){
				$score++;
			}
		}
		$this->data['test']['score']=$score;
		$this->data['test']['status']=student::FINISHED;
		$this->data['test']['submittime']=time();
		$this->logs[]=date('Y-m-d H:i:s : ')."Finished test. Score $score";

		return message(REQUEST_SUCCESS,"已完成考试");
	}

	/**
	 * 更换一份试卷
	 * 
	 * @return message()
	 */
	public function changeTest(){
		if($this->data===null){
			return message(REQUEST_OBJECT_NOT_EXSIST,"没有此学生");
		}
		if($this->data['test']['status']!=student::IN_PROGRESS){
			return message(REQUEST_FORBIDDEN,"未开始考试");
		}
		if(time()-$this->data['test']['starttime']<300){
			return message(REQUEST_FORBIDDEN,"未满五分钟");
		}
		include_once WEB_ROOT."/include/functions_test.php";
		$this->data['test']['exampaper']=newTest();
		$this->data['test']['status']=student::NOT_STARTED;
		$this->data['test']['starttime']=0;
		$this->data['test']['submittime']=0;
		$this->data['test']['answersheet']=array();
		$this->logs[]=date('Y-m-d H:i:s : ')."Changed test";
		return message(REQUEST_SUCCESS,"更换试卷成功");
	}

	/**
	 * 获取信息
	 * 
	 * @return message()
	 */
	public function getData(){
		if($this->data===null){
			return message(REQUEST_OBJECT_NOT_EXSIST,"没有此学生");
		}
		$rtn['id']=$this->data['id'];
		$rtn['status']=$this->data['test']['status'];
		$rtn['starttime']=$this->data['test']['starttime'];
		$rtn['submittime']=$this->data['test']['submittime'];
		$rtn['answersheet']=$this->data['test']['answersheet'];
		foreach($rtn['answersheet'] as $id=>$ans){
			if($this->data['test']['exampaper'][$id]['arrange']!=-1){
				$rtn['answersheet'][$id]=student::arrange($this->data['test']['exampaper'][$id]['arrange'],true)[$ans];
			}
		}
		$rtn['score']=$this->data['test']['score'];
		$rtn['servertime']=time();
		return message(REQUEST_SUCCESS,"请求成功",$rtn);
	}
	

	/**
	 * 排列
	 * 1-4 => A-D
	 * 
	 * @param int $num 字典序号 0-23
	 * @param bool $rev 反转
	 * @return array
	 */
	static private function arrange($num,$rev=false){
		$rtn=array();
		$arrange=array('1','2','3','4');
		for($i=4;$i>1;$i--){
			$n=$num % $i;
			$num=($num-$n)/$i;
			$rtn[(string)(5-$i)]=$arrange[$n];
			array_splice($arrange,$n,1);
		}
		$rtn['4']=$arrange[0];
		if($rev){
			foreach($rtn as $a=>$b){
				$temp[$b]=(string)$a;
			}
			$rtn=$temp;
		}
		return $rtn;
	}

}
?>