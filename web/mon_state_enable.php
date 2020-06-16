<?php
include 'head.php';
include 'include/is_monitor.php';
if (isset($_GET['id']) and $_GET['id'] !== "" )
{
	$ipid = $_GET['id'];
	$url = ($_GET['url']);
	$url = str_replace("!@!","&",$url);
	$update_sql = "update ipinfo set  Enable = 1  where id = ".$ipid;
	do_sql($update_sql);
	save_do($s_u_id,$s_u_level,"用户".$s_u_name." 启用IP成功!");
	alert_go("启用IP成功",$url);
	echo '<script language="javascript">showHint_socket("Sync_Db","OLIVE_SERVER","'.$_SESSION['verify_str'].'") </script>';
}
else 
{
	alert_go("用户数据不完整","mon_state.php");
}
?>
