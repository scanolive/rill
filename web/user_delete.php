<?php 
include 'head.php';
if (!empty($_GET['userid']) and  $_GET['userid'] !== 0) 
{
	$userid = $_GET['userid']; 
	$get_userinfo_sql = "select UserType,UserName from users where id =".$userid;
	$userinfo = getrs($get_userinfo_sql);
	$usertype = $userinfo[0][0];
	$username = $userinfo[0][1];
	if ( ($s_u_level == 1 and $usertype !== "root")  or ($s_u_level == 2 and $usertype == "user"))
	{
		$delete_sql = "delete from users where id=".$userid;
		$delete_gu = "delete from userofgroup where Uid=".$userid;
		do_sql($delete_sql);
		do_sql($delete_gu);
		save_do($s_u_id,$s_u_level,"用户".$s_u_name." 删除".$usertype."类型用户".$username."成功!");
		alert_go("删除成功","user_manage.php");
	}
	else
	{
		alert_go("别调皮哦！","index.php");
	}
}	
else
{
	alert_go("请指定删除用户","user_manage.php");
}
 include 'boot.php'; 
 ?>
