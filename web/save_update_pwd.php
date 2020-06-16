<?php
include 'head.php';
if (!empty($_POST ['username']) and !empty($_POST ['old_pwd']) and !empty($_POST ['new_pwd']) and !empty($_POST ['new_r_pwd']))
{
	$username = $_POST['username'];
	$old_password = md5($_POST['old_pwd']);
	$new_password = md5($_POST['new_pwd']);
	$new_r_password = md5($_POST['new_r_pwd']);
	if ($new_password !== $new_r_password)
	{
		alert_go("两次新密码不一致","update_pwd.php");
	}
	$check_pwd_sql = "select id from users where UserPasswd='$old_password' and UserName='$username'";
	if (count(getrs($check_pwd_sql)) == 0)
	{
		save_do($s_u_id,$s_u_level,"用户".$s_u_name." 修改密码时旧密码错误！");
		alert_go("旧密码错误","update_pwd.php");
	}
	else
	{
		$update_pwd_sql = "update users set UserPasswd = '$new_password' where UserName='$username'";
		do_sql($update_pwd_sql);
		save_do($s_u_id,$s_u_level,"用户".$s_u_name." 修改密码成功！");
		alert_go("密码修改成功","index.php");
	}
}
else
{
	alert_go("请输入新旧密码","update_pwd.php");
}
?>
