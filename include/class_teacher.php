<?php

class teacher{

	private $data;
	private $logs;

	/**
	 * 老师类
	 * 
	 * @param string $id id 
	 */
	public function __construct($id){
		list($this->data,$this->logs)=engageKey(2,$id);
	}

	public function __destruct(){
		if($this->data==null){
			return;
		}
		unengageKey(2,$this->data['id'],$this->data,$this->logs);
	}

	/**
	 * 获取统计数据
	 * 
	 * @return message()
	 */
	public function getAnalysis(){
		if($this->data==null){
			return message(REQUEST_OBJECT_NOT_EXSIST,"没有这个老师");
		}

		$keys=getKeys(1);
		$students=getKeysDataRaw($keys);
		foreach($students as $student){
			$student=igbinary_unserialize($student);
			$lists[$student["info"]['department']]['count'][]=$student['info']['studentid'];
			if($student['test']['status']==student::FINISHED){
				$lists[$student["info"]['department']]['finished'][]=$student['info']['studentid'];
				$lists[$student["info"]['department']]['scores'][]=$student['test']['score'];
				$lists[$student["info"]['department']]['time'][]=$student['test']['submittime']-$student['test']['starttime'];
			}
		}
		$all['count']=0;
		$all['finished']=0;
		$scoresum=0;
		$timesum=0;
		$all['maxscore']=0;
		$all['minscore']=30;
		$all['maxtime']=0;
		$all['mintime']=3600;
		foreach($lists as $department=>$list){
			$all['count']+=$result[$department]['count']=count($list['count']);
			$all['finished']+=$result[$department]['finished']=isset($list['finished'])?count($list['finished']):0;
			if($result[$department]['finished']==0){
				continue;
			}
			foreach($list['scores'] as $score){
				if(isset($result[$department]['scoresection'][$score])){
					$result[$department]['scoresection'][$score]++;
				}
				else{
					$result[$department]['scoresection'][$score]=1;
				}
			}
			$result[$department]['averagescore']=array_sum($list['scores']) / $result[$department]['finished'];
			$scoresum+=array_sum($list['scores']);
			$result[$department]['averagetime']=array_sum($list['time']) / $result[$department]['finished'];
			$timesum+=array_sum($list['time']);
			$result[$department]['maxscore']=max($list['scores']);
			$all['maxscore']=max($result[$department]['maxscore'],$all['maxscore']);
			$result[$department]['minscore']=min($list['scores']);
			$all['minscore']=min($result[$department]['minscore'],$all['minscore']);
			$result[$department]['maxtime']=max($list['time']);
			$all['maxtime']=max($result[$department]['maxtime'],$all['maxtime']);
			$result[$department]['mintime']=min($list['time']);
			$all['mintime']=min($result[$department]['mintime'],$all['mintime']);
		}
		if($all['finished']==0){
			unset($all['maxscore']);
			unset($all['minscore']);
			unset($all['maxtime']);
			unset($all['mintime']);
		}
		else{
			$all['averagescore']=$scoresum/$all['finished'];
			$all['averagetime']=$timesum/$all['finished'];
		}
		$result['all']=$all;

		return message(REQUEST_SUCCESS,"查询成功",$result);
	}

	/**
	 * 获取成绩单
	 * 
	 * @return message()
	 */
	public function getScores(){
		if($this->data==null){
			return message(REQUEST_OBJECT_NOT_EXSIST,"没有这个老师");
		}

		$keys=getKeys(1);
		$students=getKeysDataRaw($keys);
		foreach($students as $student){
			$student=igbinary_unserialize($student);
			if($this->data['info']['department']==$student['info']['department']){
				$item=[];
				$item['studentid']=$student['info']['studentid'];
				$item['name']=$student['name'];
				switch($student['test']['status']){
					case student::NOT_STARTED:$item['status']="未开始";break;
					case student::IN_PROGRESS:$item['status']="考试中";break;
					case student::FINISHED:
						$item['status']="已完成";
						$item['score']=$student['test']['score'];
						$item['time']=$student['test']['submittime']-$student['test']['starttime'];
						break;
				}
				$result[]=$item;
			}
		}
		
		return message(REQUEST_SUCCESS,"查询成功",$result);
	}

}

?>