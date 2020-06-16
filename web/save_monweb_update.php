<?php
include 'head.php';
include 'include/is_monitor.php';
if (!empty($_POST['monname']) and !empty($_POST['monurl']) and !empty($_POST['sgroup']) and isset($_POST['id']) and $_POST['id'] !== "" )
{
	$monname = $_POST ['monname'];
	$monurl = $_POST ['monurl'];
	$gid = $_POST ['sgroup'];
	$monwebid = $_POST['id'];
	$update_sql = "update monweb set  MonName = '$monname',MonUrl = '$monurl',Gid = '$gid' where id = ".$monwebid;
	$update_alarms = "update alarms set Gid=".$gid." where Ip = ".$monwebid;
	do_sql($update_sql);
	do_sql($update_alarms);
	save_do($s_u_id,$s_u_level,"用户".$s_u_name." 修改页面监控成功!");
	echo '<script language="javascript">showHint_socket("Sync_Db_Monweb","OLIVE_SERVER","'.$_SESSION['verify_str'].'") </script>';
	alert_go("页面监控修改成功","monweb.php");
}
else 
{
	alert_go("用户数据不完整","monweb_update.php");
}
?>
