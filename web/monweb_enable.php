<?php
include 'include/config.php';
include 'include/is_monitor.php';
if (empty($_SESSION['rlll_olive_scan_level']))
{
	alert_go("请先登录","login.php");
}
if (isset($_GET['id']) and $_GET['id'] !== "" )
{
	$monwebid = $_GET['id'];
	$update_sql = "update monweb set  Enable = 1  where id = ".$monwebid;
	do_sql($update_sql);
	save_do($s_u_id,$s_u_level,"用户".$s_u_name." 启用页面监控成功!");
//	alert_go("页面监控启用成功","monweb.php");
	echo "页面监控启用成功";
}
else 
{
	alert_go("用户数据不完整","monweb.php");
}
?>
