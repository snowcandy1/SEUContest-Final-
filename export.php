<?php
header('Content-type: application/json');

header("Content-Disposition:attachment;filename='".time().".csv'");

function export_csv($arr) {
	if (!isset($arr)) return null;
	if (!is_array($arr)) return $arr;
	$str = null;
	foreach ($arr as $r) {
		$str .= '"'.export_csv($r).'",';
	}
	return $str."\r\n";
}

$get = json_decode($_REQUEST['key'], 1);
// echo export_csv($get);
$res = "";
foreach ($get as $r) {
	$res .= export_csv($r);
}
echo $res;