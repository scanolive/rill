<?php 
include 'head.php';
include 'include/is_monitor.php';
$UPFILE_ALLPATH = $s_s_upfile_allpath;
if (isset($_GET['delid']))
{
	$delid = $_GET['delid'];
	$sql = "select FileName,SaveName from upfile where id=".$delid;
	$file_rs = getrs($sql);
	$filename = $file_rs[0][0];
	$savename = $file_rs[0][1];
	$delete_sql = "delete from upfile where id=".$delid;
	do_sql($delete_sql);
	if (unlink($UPFILE_ALLPATH.$savename))
	{	
		save_do($s_u_id,$s_u_level,"用户".$s_u_name." 删除文件".$filename."成功!");
		alert_go("删除文件成功!","file_manage.php");
	}
	else
	{
		alert_go("删除文件失败!","");
	}		
}
else
{
	alert_go("请指定删除文件","");
}
include 'boot.php'; 
//close_window();
 ?>
