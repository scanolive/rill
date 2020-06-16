<?php
include 'head.php';
include 'include/is_monitor.php';
if (isset($_GET['id']) and $_GET['id'] !== "" )
{
	$monwebid = $_GET['id'];
	$update_sql = "delete  from monweb  where id = ".$monwebid;
	do_sql($update_sql);
	save_do($s_u_id,$s_u_level,"用户".$s_u_name." 删除页面监控成功!");
	echo '<script language="javascript">showHint_socket("Sync_Db_Monweb","OLIVE_SERVER","'.$_SESSION['verify_str'].'") </script>';
	alert_go("页面监控删除成功","monweb.php");
}
else 
{
	alert_go("用户数据不完整","monweb.php");
}
?>
