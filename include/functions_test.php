<?php

/**
 * 返回一张试卷
 * see student::$data['exampaper']
 * 
 * @return array
 */
function newTest($keys=null){

	$tof=10;
	$mc=20;

	if($keys===null){
		$keys=getKeys(4);
	}
	shuffle($keys);

	foreach($keys as $key){
		if(strstr($key,"mc-")!==false and $mc>0){
			$mc--;
			$testkeys[]=$key;
		}
		else if(strstr($key,"tof-")!==false and $tof>0){
			$tof--;
			$testkeys[]=$key;
		}
		if($tof<=0 and $mc<=0){
			break;
		}
	}
	foreach($testkeys as $id=>$key){
		$exampaper[$id+1]['id']=$key;
		if(strstr($key,"mc-")!==false){
			$exampaper[$id+1]['arrange']=mt_rand(0,23);
		}
		else{
			$exampaper[$id+1]['arrange']=-1;
		}
	}
	return $exampaper;
}



?>