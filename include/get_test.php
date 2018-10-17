<?php

/**
 * 为id获得一份新试题
 * 只要执行，总能生成或改变 $id 的试卷（只覆盖exampaper和correanswer）
 * 
 * @param string $id 学生id
 * @return void 
 * 
 */
function getTest($id){

    //各种题型数量
    $multipleChoice=20;
    $tureOrFalse=10;

    //变量
    $questionkeys=array();
    $questoincount=1;
    $redis=new Redis();
    $redis->pconnect('localhost');
    $searchcount=0;

    //构造题目
    $redis->select(4);
    while($multipleChoice>0 or $tureOrFalse>0){

        //查重
        $key=$redis->randomKey();
        if(array_search($key,$questionkeys)){
            $searchcount++;
            if($searchcount>500){
                die("题库数量不足");
            }
            continue;
        }
        array_push($questionkeys,$key);

        //读取题目
        $getQ=$redis->hGetAll($key);
        $ques['question']=$getQ['question'];

        //根据当前题目选择
        //多选
        if($getQ['category']=='1' and $multipleChoice>0){
            $ques['category']=1;
            $options=json_decode($getQ['options']);
            $ans=$getQ['answer'];
            //打乱顺序
            $shuffle=array("1","2","3","4");
            shuffle($shuffle);
            foreach($shuffle as $a=>$b){
                $ques['options'][$a+1]=$options[$b];
                if($ans==$b){
                    $ans=$a+1;
                }
            }
            $multipleChoice--;
        }
        //单选
        elseif($getQ['category']=='0' and $tureOrFalse>0){
            $ques['category']=0;
            $ques['options']=json_decode($getQ['options']);
            $ans=$getQ['answer'];
            $tureOrFalse--;
        }
        //某种题目数量满足
        else{
            continue;
        }
        $questions[$questoincount]=$ques;
        $answers[$questoincount]=$ans;
        $questoincount++;
    }

    //使用pipe导入题目
    $pipe=$redis->multi(Redis::PIPELINE)
        ->select(5)
        ->set($id."_exampaper",json_encode($questions))
        ->del($id."_correctanswer");
    foreach($answers as $i=>$ans){
        $pipe->hSet($id."_correctanswer",$i,$ans);
    }
    $pipe->exec();
}


?>