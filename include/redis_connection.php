<?php

require 'config.php';

class server{

    protected $status;
    protected $redis;

    /**
     * 构造函数
     * 连接服务器并获得状态（不刷新状态）
     * 
     */
    public function __construct(){
        $this->redis=new Redis();
        try{
            $this->redis->pconnect('localhost', $GLOBALS['Redis_PORT']);
        }
        catch(RedisException $error){
            $this->status='not_connected';
        }
        $this->redis->select(0);
        $this->staus=$this->redis->get('status');
        if($this->status===false){
            $this->status='uninitialized';
        } else {
			$this->status='running';
		}
    }

    /**
     * 获取状态
     * 返回值 "not_connected"|"uninitialized"|"debugging"|"running"
     * 
     * @return string 
     */
    public function getStatus(){
        return $this->status;
    }

    /**
     * 获取服务器note
     * 
     * @return string
     */
    public function getNote(){
        if($this->status=='not_connected'){
            return 'none';
        }
        $this->redis->select(0);
        return (string)$this->redis->get('note');
    }
}


class account{
    protected $number;
    protected $redis;
    protected $status;

    protected $name;
    protected $auth;
    protected $pass;

    /** 
     * 构造函数
     * 认为服务器能正常连接
     * 
    */
    public function __construct($id){
        $this->number=$id;
        $this->redis=new Redis();
        $this->redis->pconnect('localhost', $GLOBALS['Redis_PORT']);
        $this->redis->select(2);
        if(!$this->redis->exists($this->number.'_login')){
            //账户不存在
            $this->status=false;
        }
        else{
            //就绪
            $this->status=true;
            $temp=$this->redis->hGetAll($this->number."_login");
            $this->name=$temp['name'];
            $this->auth=$temp['auth'];
            $this->pass=$temp['pass'];
        }
    }

    /**
     * 状态
     * false:无账户|true:有账户
     * 
     * @return bool false:无账户|true:有账户 
     */
    public function getStatus(){
        return $this->status;
    }

    /**
     * 验证登陆
     * 返回值 "forbidden"|"failed"|权限auth
     * 
     * @param string $password 加密后的密码
     * @return string 
     */
    public function login($password){
        //未就绪
        if($this->status==false){
            return [10000, "forbidden"];
        }
        
        //错误次数过多
        $this->redis->select(3);
        $trytimes=(int)$this->redis->get($this->number.'_trytimes');
        if($trytimes>4){
            return [10000, "forbidden"];
        }
        
        //成功登陆
        if($this->pass==$password){
            $this->redis->set($this->number.'_lastlogin',time());
            return [0, 'success'];
        }

        //密码错误
        $trytimes ++;
        if($trytimes >= 5){
            $this->redis->setEx($this->number.'_trytimes', 180, $trytimes);
        }
        else{
            $this->redis->set($this->number.'_trytimes', $trytimes);
        }
        return [1000, 'failed'];
    }

    public function getInfo(){
        return array("name"=>$this->name,"auth"=>$this->auth);
    }
}

class student{
    protected $number;
    protected $teacher;
    protected $redis;
    protected $status;

    /**
     * 构造函数
     * 认为 $id 为已存在学生学号
     * 
     */
    public function __construct($id){
        $this->number=$id;
        //老师账号
        $this->teacher=substr($id,0,5);
        $this->redis=new Redis();
        $this->redis->pconnect('localhost', $GLOBALS['Redis_PORT']);
        $this->redis->select(5);
        $this->status=$this->redis->get($this->number.'_status');
        if($this->status===false){
            $this->status='not_started';
        }
    }

    /**
     * 状态
     * 返回值 "not_started"|"in_progress"|"finished"
     * 
     * @return string 
     */
    public function getStatus(){
        return $this->status;
    }

    /**
     * 开始时间
     * 返回时间戳
     * 
     * @return int 
     */
    public function getStartTime(){
        $this->redis->select(5);
        return (int)$this->redis->get($this->number.'_starttime');
    }

    /**
     * 获取试卷
     * json格式
     * 
     * @return string
     */
    public function getTest(){
        $this->redis->select(5);
        $test=$this->redis->get($this->number.'_exampaper');

        //开始考试
        if($this->status=='not_started'){
            $this->status='in_progress';
            $this->redis->set($this->number.'_status','in_progress');
            $this->redis->set($this->number.'_starttime',time());
        }
        return $test;
    }

    /**
     * 获取当前答案
     * 返回json
     * 
     * @return string 
     */
    public function getAnswers(){
        $this->redis->select(5);
        return ($this->redis->hGetAll($this->number.'_answersheet'));
    }

    /**
     * 提交一个答案
     * 返回值 "forbidden"|"success"
     * 
     * @param string|int $order 题目序号
     * @param string $answer 题目答案
     * @return string 
     */
    public function submitOne($order,$answer){
        if($this->status!='in_progress'){
            return 'forbidden';
        }
        $this->redis->select(5);
        if(time()-$this->getStartTime()>1800){
            return 'forbidden';
        }
        $this->redis->hSet($this->number.'_answersheet',$order,$answer);
        return 'success';
    }

    /**
     * 完成试卷
     * 返回值 "forbidden"|"success"
     * 
     * @return string
     */
    public function finish(){
        if($this->status!='in_progress'){
            return 'forbidden';
        }
        $this->status='finished';

        //计算分数
        $this->redis->select(5);
        $correctAnswer=$this->redis->hGetAll($this->number.'_correctanswer');
        $studentAnswer=$this->redis->hGetAll($this->number.'_answersheet');
        $score=0;
        foreach($correctAnswer as $i => $ans){
            if($studentAnswer[$i] == $ans){
                $score++;
            }
        }

        //数据更新
        $pipe=$this->redis->multi(Redis::PIPELINE)
            ->set($this->number.'_score',$score)
            ->set($this->number.'_status','finished')
            ->set($this->number.'_submittime',time())

            //统计数据
            ->select(6)
            ->incr("finished")
            ->incrBy("scoresum",$score)
            ->hIncrBy("fractions",$score,1)
            ->incr($this->teacher."_finished")
            ->incrBy($this->teacher."_scoresum")
            ->hIncrBy($this->teacher."_fractions",$score,1)
            ->hIncrBy("submit",time(),1)
            ->exec();
        return "success";
    }

    /**
     * 更换一份试卷
     * 返回值 "forbidden"|"success"
     * 
     * @return string
     */
    public function changeTest(){
        if($this->status!='in_progress'){
            return 'forbidden';
        }
        $this->redis->select(5);
        if(time()-$this->getStartTime()<600){
            return 'forbidden';
        }
        include "./get_test.php";
        getTest($this->number);
        $pipe=$this->redis->multi(Redis::PIPELINE)
            ->select(5)
            ->del($this->number."_starttime")
            ->del($this->number."_status")
            ->del($this->number."_answersheet")
            ->exec();
        return "success";
    }

    /**
     * 返回分数
     * 
     * @return int
     */
    public function getScore(){
        $this->redis->select(5);
        return (int)$this->redis->get($this->number.'_score');
    }
}

class teacher{
    protected $number;
    protected $redis;

    /**
     * 构造函数
     * 
     * @param string $id 老师id
     */
    public function __construct($id){
        $this->number=$id;
        $this->redis=new Redis();
        $this->redis->pconnect('localhost', $GLOBALS['Redis_PORT']);
    }

    /**
     * 返回统计数据
     * 返回json
     * 
     * @return string
     */
    public function getStatistic(){

        //导出所有院系号和名字
        $this->redis->select(1);
        $keys=$this->redis->keys("*");
        $this->redis->select(2);
        foreach($keys as $key){
            $teachers[$key]=$this->redis->hGet($key."_login","name");
        }

        //导出总统计数据
        $this->redis->select(6);
        $item['name']='all';
        $item['finished']=$this->redis->get("finished");
        $item['average']=(int)$this->redis->get("scoresum") / $item['finished'];
        $item['fractions']=$this->redis->hGetAll("fractions");
        $statistics[]=$item;

        //导出各个院系统计数据
        foreach ($teachers as $teacher=>$name){
            $item['name']=$name;
            $item['finished']=$this->redis->get($teacher."_finished");
            $item['average']=(int)$this->redis->get($teacher."_scoresum") / $item['finished'];
            $item['fractions']=$this->redis->hGetAll($teacher."_fractions");
            $statistics[]=$item;
        }
        return ($statistics);
    }

    /**
     * 返回成绩单
     * 返回json
     * 
     * @return string
     */
    public function getScores(){

        //导出本院系学生学号名字
        $this->redis->select(1);
        $keys=$this->redis->sMembers($this->number);
        $this->redis->select(2);
        foreach($keys as $key){
            $students[$key]=$this->redis->hGet($key."_login");
        }
        $this->redis->select(5);
        
        //导出成绩
        foreach($students as $student=>$name){
            $item['number']=$student;
            $item['name']=$name;
            $item['status']=$this->redis->get($student."_status");
            if($item['status']===false){
                $item['status']='not_started';
            }
            $item['score']=(int)$this->redis->get($student."_score");
            $scores[]=$item;
        }
        return ($scores);
    }
}

class admin{
    protected $number;
    protected $redis;
    protected $dealing;

    /**
     * 构造函数
     * 
     * 
     */
    public function __construct($id){
        $this->number=$id;
        $this->redis=new Redis();
        $this->redis->pconnect('localhost', $GLOBALS['Redis_PORT']);
        $this->redis->select(7);
        $this->dealing=$this->redis->get($this->number."_dealing");
    }

    /**
     * 获取队列中的申诉
     * 返回json
     * 
     * @return string 
     */
    public function getQueue(){
        $this->redis->select(7);
        $keys=$this->redis->sMembers("queue");
        $queue=array();
        foreach($keys as $matter){
            $item=$this->redis->hGetAll($matter."_matter");
            $item['number']=$matter;
            $queue[]=$item;
        }
        return ($queue);
    }

    /**
     * 获取正在处理的申诉
     * 返回json
     * 
     * @return string
     */
    public function getDealing(){
        if($this->dealing===false){
            return "{}";
        }
        $this->redis->select(7);
        $matter=$this->redis->hGetAll($dealing."_matter");
        $this->redis->select(2);
        $account_status=$this->redis->hGetAll($matter['account']."_login");
        $this->redis->select(3);
        $account_status['lastlogin']=$this->redis->get($matter['account']."_lastlogin");
        $rtn['number']=$dealing;
        $rtn['account']=$matter['account'];
        $rtn['content']=$matter['content'];
        $rtn['name']=$account_status['name'];
        $rtn['auth']=$account_status['auth'];
        $rtn['lastlogin']=$account_status['lastlogin'];
        return ($rtn);
    }
    
    /**
     * 开始处理申诉
     * 
     * @param string $id 申诉id
     * @return string "success"|"failed"
     */
    public function startDeal($id){
        $this->redis->select(7);
        if(!$this->redis->sIsMember("queue",$id) 
            or $this->redis->exists($this->number."_dealing")){
            return "failed";
        }
        $pipe=$this->redis->multi(Redis::PIPELIE)
            ->sRem("queue",$id)
            ->set($this->number."_dealing",$id)
            ->hSet($id."_matter","result",date('Y-m-d H:i:s')
                ." 已开始处理，处理人：".$this->number)
            ->exec();
        $this->dealing=$id;
        return "success";
    }

    /**
     * 完成处理申诉
     * 
     * @param string $result 处理结果
     * @return string "success"|"failed"
     */
    public function finishDeal($result){
        if($this->dealing===false){
            return "failed";
        }
        $pipe=$this->redis->multi(Redis::PIPELINE)
            ->select(7)
            ->del($this->number."_dealing")
            ->hSet($id."_matter","result",date('Y-m-d H:i:s')
                ." 已处理，处理人：".$this->number."，结果：".$result)
            ->exec();
        return "success";
    }
    
    /**
     * 修改正在处理账号的密码
     * 
     * @param string $pass 新密码
     * @return string "success"|"failed"
     */
    public function changePass($pass){
        $this->redis->select(7);
        $account=$this->redis->hGet($dealing."_matter","account");
        $this->redis->select(2);
        if(!$this->redis->exists($account."_login") or
            strlen($pass)!=32){
            return [1000, 'failed'];
        }
        $this->redis->hSet($account."_login",'password',$pass);
        return [0, "success"];
    }

    /**
     * 新建账户
     * 
     * @param string $name 名字
     * @param string $pass 密码
     * @return string "success"|"failed"
     */
    public function createAccount($name,$pass){
        $this->redis->select(7);
        $account=$this->redis->hGet($dealing."_matter","account");
        $this->redis->select(2);
        if($this->redis->exists($account."_login") or
            strlen($pass)!=32 or strlen($name)<2 or strlen($account)!=8){
            return [1000, 'failed'];
        }
        $this->redis->hMSet($account."__login",array(
            "name"=>$name,
            "passowrd"=>$pass,
            "auth"=>"student"
        ));
        $this->redis->select(1);
        $this->redis->sAdd(substr($account,0,5),$account);
        return [0, "success"];
    }
}




/**
 * 申诉
 * 返回申诉号
 * 
 * @param string $account 申诉人学号
 * @param string $content 申诉内容
 * @return string 申诉号
 */
function appeal($account,$content){
    $redis=new Redis();
    $redis->redis->pconnect('localhost', $GLOBALS['Redis_PORT']);
    $redis->select(7);
    if($redis->exists($account."_timeout")){
        return array(
				'code' => 10000, 
				'message' => 'forbidden'
				);
    }
    $redis->setEx($account."_timeout",300);
    for($i=0;$i<1000;$i++){
        $number=(string)mt_rand(100000,999999);
        if(!$redis->exists($number."_matter")){
            $redis->hMSet($number."_matter",
                array('account'=>$account,'content'=>$content));
            $redis->sAdd('queue',$number);
            return array(
				'code' => 0, 
				'message' => '申诉成功',
				'appeal_num' => $number
				);
        }
    }
    return array(
				'code' => 1000, 
				'message' => '申诉队列已满，请稍候或联系管理员。'
				);
}

/**
 * 查询申诉状态
 * 返回状态
 * 
 * @param string $account 申诉人学号
 * @param string $number 申诉号
 * @return string 状态
 */
function queryAppeal($account,$number){
    $redis = new Redis();
    $redis->redis->pconnect('localhost', $GLOBALS['Redis_PORT']);
    $redis->select(7);
    $matter=$redis->hGetAll($number."_matter");
    if(count($matter) <= 0 or $matter['account'] != $account){
        return array(
				'code' => 2000, 
				'message' => '查询失败，申诉号 '.$number.' 不存在。'
				);
    }
    if(isset($matter["result"])){
        return $matter["result"];
    }
    return array(
				'code' => 1000, 
				'message' => '暂未开始处理'
				);
}

?>