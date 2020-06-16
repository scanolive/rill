<?php
include 'head.php';
include 'include/is_monitor.php';
if ( !empty($_POST['delete']) and !empty($_POST['delid']) and $s_u_level == 1)
{
	$url = ($_POST['url']);
	$delids = $_POST['delid'];
	$del_num = count($delids);
	foreach ($delids as $delid)
	{
		$sql_del_id .= $delid.",";
	}
	$sql_del_id = "(".$sql_del_id.$delids[0].")";
	$del_sql = "delete from bg_result where id in ".$sql_del_id;
	do_sql($del_sql);
	save_do($s_u_id,$s_u_level,"用户".$s_u_name." 删除".$del_num."条运行结果操作记录成功!");
	alert_go("运行结果删除成功",$url);
}
else 
{
	alert_go("用户数据不完整",$url);
}
?>

