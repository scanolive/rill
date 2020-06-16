<?php
include 'head.php';
if ( $s_u_level > 2)
alert_go("没有权限","mon_state.php");

if (isset($_GET['id']) and $_GET['id'] !== "" )
{
	$url = ($_GET['url_now']);
	$url = str_replace("!@!","&",$url);
	$ipid = $_GET['id'];
	$delete_ipinfo_sql = "delete  from ipinfo  where id = ".$ipid;
	$delete_devinfo_sql = "delete  from devinfo  where Ipid = ".$ipid;
	$delete_alarms_sql = "delete  from alarms  where Ipid = ".$ipid;
	$delete_devinfo_sql = "delete  from devinfo  where Ipid = ".$ipid;
	$delete_info_day_sql = "delete  from info_day  where Ipid = ".$ipid;
	//$insert_delipid_sql = "insert into  delipid  set Ipid = ".$ipid;
	$delete_mondata = "delete from  monitor where ipid=".$ipid;
	$delete_ports_sql = "delete  from ports  where Ipid = ".$ipid;
	$delete_portstat_sql = "delete  from portstat  where Ipid = ".$ipid;
	
	do_sql($delete_ipinfo_sql);
	do_sql($delete_devinfo_sql);
	do_sql($delete_alarms_sql);
	do_sql($delete_devinfo_sql);
	do_sql($delete_info_day_sql);
	//do_sql($insert_delipid_sql);
	do_sql($delete_ports_sql);
	do_sql($delete_portstat_sql);
	do_sql($delete_mondata_sql);
//	do_sql($update_sql);
	save_do($s_u_id,$s_u_level,"用户".$s_u_name." 删除IP成功!");
	echo '<script language="javascript">showHint_socket("Sync_Db_IpAlarms","OLIVE_SERVER","'.$_SESSION['verify_str'].'") </script>';
	alert_go("IP删除成功",$url);
}
else 
{
	alert_go("用户数据不完整","mon_state.php");
}
?>
