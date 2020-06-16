<?php
include 'head.php';
include 'include/is_monitor.php';
if (!empty($_POST['monname']) and !empty($_POST['monurl']) and !empty($_POST['sgroup']))
{
	$monname = $_POST ['monname'];
	$monurl = $_POST ['monurl'];
	$gid = $_POST ['sgroup'];
	$insert_sql = "insert into monweb set RstCode=200, MonName = '$monname',MonUrl = '$monurl',Gid = '$gid',AddUid = '$s_u_id'";
	do_sql($insert_sql);
	save_do($s_u_id,$s_u_level,"用户".$s_u_name." 添加页面监控成功!");
	echo '<script language="javascript">showHint_socket("Sync_Db_Monweb","OLIVE_SERVER","'.$_SESSION['verify_str'].'") </script>';
	alert_go("页面监添加成功","monweb.php");
}
else 
{
	alert_go("用户数据不完整","monweb_add.php");
}
?>
