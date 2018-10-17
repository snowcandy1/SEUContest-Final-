<?php

include_once "../include.php";

/**
 * 导入数据
 */
function import(){
	importAdmin();
	importMultipleChoice();
	importTrueOrFalse();
	importStudentTeacher();
}


/**
 * 从"./import/admin.txt"导入管理员数据
 * 有//的行将被忽略
 * 格式  id (tab) 名字 (tab) 密码 
 * 
 */
function importAdmin(){
	//打开文件
	appendLog("开始导入管理员数据");
	
	if(!file_exists("./import/admin.txt")){
		appendLog("未找到\"./import/admin.txt\"");
		return;
	}
	$file=fopen("./import/admin.txt","r");
	$line=0;
	$count=0;
	//按行导入数据
	While(!feof($file)){
		$line++;
		$oneline=trim(fgets($file));
		//只要有//就将整行视为注释
		if($oneline==="" or strstr($oneline,"//")!==false){
			continue;
		}
		//分割数据
		$input=[];
		$input['id']="admin-".trim(strtok($oneline,"\t"));
		$input['name']=trim(strtok("\t"));
		$input['password']=trim(strtok("\t"));
		if($input['password']===false){
			appendLog("行".$line."：数据不完整");
			continue;
		}
		$input['password']=md5(md5($input['password'])."XSZSJS2018");
		//检查id重复
		if(keyExists(3,$input['id'])){
			appendLog("行".$line."：id重复");
			continue;
		}
		//导入数据
		$input['errorpass']['lasttry']=0;
		$input['errorpass']['times']=0;

		$input['dealing']=null;
		unengageKey(3,$input['id'],$input,[]);
		$count++;
	}
	appendLog("管理员数据已导入，共".$count."条");
}

/**
 * 从"./import/student.txt"导入学生数据
 * 有//的行将被忽略
 * 格式  学院 tab 专业 tab 学号 tab 姓名 tab 一卡通号
 * 
 */
function importStudentTeacher(){
	//打开文件
	appendLog("开始导入学生数据");
	if(!file_exists("./import/student.txt")){
		appendLog("未找到\"./import/student.txt\"");
		return;
	}
	$file=fopen("./import/student.txt","r");
	$line=0;
	$count=0;
	$teachers=array();
	$questionkeys=getKeys(4);
	//按行导入数据
	While(!feof($file)){
		$line++;
		$oneline=trim(fgets($file));
		//只要有//就将整行视为注释
		if($oneline==="" or strstr($oneline,"//")!==false){
			continue;
		}
		//分割数据
		$input=[];
		$input['info']['department']=trim(strtok($oneline,"\t"));
		$input['info']['major']=trim(strtok("\t"));
		$input['info']['studentid']=trim(strtok("\t"));
		$input['name']=trim(strtok("\t"));
		$input['id']=trim(strtok("\t"));
		if($input['id']===false){
			appendLog("行".$line."：数据不完整");
			continue;
		}
		$input['password']=md5(md5($input['info']['studentid'])."XSZSJS2018");
		//检查id重复
		if(keyExists(0,$input['id'])){
			appendLog("行".$line."：id重复");
			continue;
		}
		//导入数据
		$input['errorpass']['lasttry']=0;
		$input['errorpass']['times']=0;

		$input['test']['status']=student::NOT_STARTED;
		$input['test']['exampaper']=newTest($questionkeys);
		$input['test']['starttime']=0;
		$input['test']['submittime']=0;
		$input['test']['answersheet']=[];
		$input['test']['score']=0;

		unengageKey(1,$input['id'],$input,[]);
		$count++;

		$teachers[$input['info']['department']][]=$input['info']['major'];
		$teachers[$input['info']['department']]=array_unique($teachers[$input['info']['department']]);
		
	}
	appendLog("学生数据已导入，共".$count."条");
	appendLog("开始生成老师数据");
	$file=fopen("./import/teacherOutput.txt","a");
	fwrite($file,"// 老师账号 tab 院系名 tab 初始密码\n");
	foreach($teachers as $teacher=>$majors){
		$input=[];
		preg_match("/^\[(.*)\]/",$teacher,$match);
		$input['id']="teacher-".$match[1];
		preg_match("/\](.*)$/",$teacher,$match);
		$input['name']=$match[1];
		$password=mt_rand(100000,999999);
		$input['password']=md5(md5($password).'XSZSJS2018');
		$input['errorpass']['lasttry']=0;
		$input['errorpass']['times']=0;
		$input['info']['department']=$teacher;
		$input['info']['majors']=$majors;
		unengageKey(2,$input['id'],$input,[]);
		fwrite($file,$input['id']."\t".$input['name']."\t".$password."\n");

	}
	fwrite($file,"// 本次导出结束");
	$count=count($teachers);
	appendLog("老师数据已导出至./import/teacherOutput.txt，共{$count}条");
}

/**
* 从"./import/multiplechoice.txt"导入单选数据
* 有//的行将被忽略
* 格式  题目 (tab) 答案（1，2，3或4） (tab) 选项A(tab) 选项B (tab) 选项C (tab) 选项D
* 
*/
function importMultipleChoice(){
	//打开文件
	appendLog("开始导入单选题数据");
	if(!file_exists("./import/multiplechoice.txt")){
		appendLog("未找到\"./import/multiplechoice.txt\"");
		return;
	}
	$file=fopen("./import/multiplechoice.txt","r");
	$line=0;
	$count=0;
	//按行导入数据
	While(!feof($file)){
		$line++;
		$oneline=trim(fgets($file));
		//只要有//就将整行视为注释
		if($oneline==="" or strstr($oneline,"//")!==false){
			continue;
		}
		//分割数据
		$input=[];
		$input['question']=trim(strtok($oneline,"\t"));
		$input['category']=1;
		$input['answer']=trim(strtok("\t"));
		$input['options'][1]=trim(strtok("\t"));
		$input['options'][2]=trim(strtok("\t"));
		$input['options'][3]=trim(strtok("\t"));
		$input['options'][4]=trim(strtok("\t"));
		if($input['options'][4]===false){
			appendLog("行".$line."：数据不完整");
			continue;
		}
		//检查id重复
		while(1){
			$input['id']='mc-'.mt_rand(100000,999999);
			if(!keyExists(4,$input['id'])){
				break;
			}
		}
		//导入数据
		unengageKey(4,$input['id'],$input,[]);
		$count++;
	}
	if($count<20){
		appendLog("单选题数量不足20");
		die();
	}
	appendLog("单选题数据已导入，共".$count."条");
}

/**
* 从"./import/trueorfalse.txt"导入判断题数据
* 有//的行将被忽略
* 格式  题目 (tab) 答案（1真或2假）
* 
*/
function importTrueOrFalse(){
	//打开文件
	appendLog("开始导入判断题数据");
	if(!file_exists("./import/trueorfalse.txt")){
		appendLog("未找到\"./import/trueorfalse.txt\"");
		return;
	}
	$file=fopen("./import/trueorfalse.txt","r");
	$line=0;
	$count=0;
	//按行导入数据
	While(!feof($file)){
		$line++;
		$oneline=trim(fgets($file));
		//只要有//就将整行视为注释
		if($oneline==="" or strstr($oneline,"//")!==false){
			continue;
		}
		//分割数据
		$input=[];
		$input['question']=trim(strtok($oneline,"\t"));
		$input['category']=0;
		$input['answer']=trim(strtok("\t"));
		if($input['answer']===false){
			appendLog("行".$line."：数据不完整");
			continue;
		}
		//检查id重复
		while(1){
			$input['id']='tof-'.rand(100000,999999);
			if(!keyExists(4,$input['id'])){
				break;
			}
		}
		//导入数据
		unengageKey(4,$input['id'],$input,[]);
		$count++;
	}
	if($count<10){
		appendLog("判断题数量不足10");
		die();
	}
	appendLog("判断题数据已导入，共".$count."条");
}

?>