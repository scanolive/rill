<?php
include 'head.php';
include 'include/is_monitor.php';
if (isset($_GET['id']) and $_GET['id'] !== "" )
{
	$define_cmd_id = $_GET['id'];
	$update_sql = "delete  from define_cmd  where id = ".$define_cmd_id;
	do_sql($update_sql);
	save_do($s_u_id,$s_u_level,"用户".$s_u_name." 删除自定义命令成功!");
	alert_go("删除自定义命令成功","define_cmd.php");
}
else 
{
	alert_go("用户数据不完整","define_cmd.php");
}
?>
