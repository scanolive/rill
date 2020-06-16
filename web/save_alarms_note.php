<?php 
include 'head.php';
if (!empty($_POST['id']))
{	
	$id = $_POST['id'];
	$note = $_POST['note'];
	$gid = ($_POST['gid']);
	$url = ($_POST['url']);
	if ($url=="")
		$url="index.php";
	$url = str_replace("!@!","&",$url);
	if (!in_array($gid,$s_u_gids))
		alert_go("你没有此条备注修改权限",$url);
	$update_sql = "update alarms set Note='$note' where id = '$id'";
//	echo $update_sql;
	do_sql($update_sql);
	save_do($s_u_id,$s_u_level,"用户".$s_u_name." 备注警报".$id."成功！");
	alert_go("修改成功",$url);
}
else
{
	alert_go("post数据错误!","index.php");	
}	
?>
