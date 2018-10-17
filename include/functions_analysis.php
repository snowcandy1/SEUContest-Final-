<?php

/**
 * @var array $studentlist
 * array [n] n为老师登陆号
 * --string ['name'] 学院名字
 * --array ['students'] 学生
 * ----array ['id'] 学号
 * ------string [n] 一个学号
 * ----array ['name']
 * ------string [n] 和 n 对应学号的姓名
 * 
 */

/**
 * @var array $analysis
 * array ['students'] 学生部分
 * --array [n] n为老师登陆号
 * ----array ['id']
 * ----array ['score']
 * ----array ['time']
 * array ['questions']
 * --arary [n] n为问题id 
 * ----string [n] 一个答案
 */



/**
 * 统计
 * 
 * @param array $data finish后提交的学生数据
 */
function analysis($data){
	
	my_redis()->select(5);
	$analysis=unserialize(engage("analysis"));
	
	$analysis['students'][substr($data['id'],0,5)]['id'][]=$data['id'];
	$analysis['students'][substr($data['id'],0,5)]['score'][]=$data['score'];
	$analysis['students'][substr($data['id'],0,5)]['time'][]=$data['submittime']-$data['starttime'];

	foreach($data['answersheet'] as $id=>$ans){
		$analysis['questions'][$data['exampaper'][$id]['id']][]=$ans;
	}

	$analysis['timeline']['start'][]=$data['starttime'];
	$analysis['timeline']['submit'][]=$data['submittime'];

	unengage("analysis",serialize($analysis));
}


?>