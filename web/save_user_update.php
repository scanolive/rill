<?php 
include 'head.php';
if (isset($_POST['userid']) and $_POST['userid'] !== "" and !empty($_POST['usermobile']) and !empty($_POST['usermail'])  and empty($_POST['sgroup']))
{

	$userid = $_POST['userid'];
	$username = $_POST['username'];
	$usermobile = $_POST['usermobile'];
	$usermail = $_POST['usermail'];
	$usertype = $_POST['usertype'];
	$dutydate = "";
	if (!empty($_POST['notice']))
	{	
		$dutydate_arr = $_POST['dutydate'];
		$dutystart = $_POST['dutystart'];
		$dutyend = $_POST['dutyend'];
		$dutytime = $dutystart."-".$dutyend;
		foreach ($dutydate_arr as $value)
		{
			$dutydate = $dutydate.'|'.$value;
		}
		$notice = $_POST['notice'];
		$update_sql = "update users set 
		UserMobile='$usermobile',
		UserType = '$usertype',
		UserMail = '$usermail',
		DutyDate = '$dutydate',
		DutyTime = '$dutytime',
		NoticeLevel = $notice
		where id=".$userid;	
	}
	else
	{
		$update_sql = "update users set 
		UserMobile='$usermobile',
		UserType = '$usertype',
		UserMail = '$usermail'
		where id=".$userid;	
	}	
	do_sql($update_sql);
	if ($s_u_level < 2 )
	{
		if (!empty($_POST['ssh_enable']))
		{
			$ssh_enable = $_POST['ssh_enable'];
		}
		else
		{
			$ssh_enable = "NO";	
		}
		if (!empty($_POST['ctrl_center_enable']))
		{
			$ctrl_center_enable = $_POST['ctrl_center_enable'];
		}
		else
		{
			$ctrl_center_enable = "NO";	
		}
		update_config("private","ssh_enable",$ssh_enable,$userid);
		update_config("private","ctrl_center_enable",$ctrl_center_enable,$userid);
		$_SESSION['ssh_enable'] = $ssh_enable;
		$_SESSION['ctrl_center_enable'] = $ctrl_center_enable;
	}	
	save_do($s_u_id,$s_u_level,"用户".$s_u_name." 修改用户".$username."信息成功!");	
	if ($s_u_id == $userid)
	{
		$url = "user_update.php?userid=".$s_u_id;
		alert_go("修改成功",$url);
	}
	else
	{	
		alert_go("修改成功","user_manage.php");
	}	
//	
}
else if (isset($_POST['userid']) and $_POST['userid'] !== "" and !empty($_POST['usermobile']) and !empty($_POST['usermail']) and !empty($_POST['notice']) and !empty($_POST['sgroup']))
{
	$gids = $_POST['sgroup'];
	$userid = $_POST['userid'];
	$username = $_POST['username'];
	$usertype = $_POST['usertype'];
	$usermobile = $_POST['usermobile'];
	$usermail = $_POST['usermail'];
	$notice = $_POST['notice'];
	$dutydate_arr = $_POST['dutydate'];
	$dutystart = $_POST['dutystart'];
	$dutyend = $_POST['dutyend'];
	$dutytime = $dutystart."-".$dutyend;
	foreach ($dutydate_arr as $value)
	{
		$dutydate = $dutydate.'|'.$value;
	}
	
	$update_sql = "update users set 
		UserMobile='$usermobile',
		UserType = '$usertype',
		UserMail = '$usermail',
		DutyDate = '$dutydate',
		DutyTime = '$dutytime',
		NoticeLevel = $notice 
		where id=".$userid;	
	do_sql($update_sql);
	$del_sql = "delete from userofgroup where Uid=".$userid;
	do_sql($del_sql);
	for ($i=0;$i<count($gids);$i++)
	{
		$gid = $gids[$i];
		$sql = "insert into userofgroup set Gid = ".$gid.",Uid = ".$userid.";";
		do_sql($sql);
	}
	if ($s_u_level < 2 )
	{
		if (!empty($_POST['ssh_enable']))
		{
			$ssh_enable = $_POST['ssh_enable'];
		}
		else
		{
			$ssh_enable = "NO";	
		}
		if (!empty($_POST['ctrl_center_enable']))
		{
			$ctrl_center_enable = $_POST['ctrl_center_enable'];
		}
		else
		{
			$ctrl_center_enable = "NO";	
		}
		update_config("private","ssh_enable",$ssh_enable,$userid);
		update_config("private","ctrl_center_enable",$ctrl_center_enable,$userid);
		$_SESSION['ssh_enable'] = $ssh_enable;
		$_SESSION['ctrl_center_enable'] = $ctrl_center_enable;
	}	
	save_do($s_u_id,$s_u_level,"用户".$s_u_name." 修改用户".$username."信息成功!");
	if ($s_u_id == $userid)
	{
		$url = "user_update.php?userid=".$s_u_id;
		alert_go("修改成功",$url);
	}
	else
	{	
		alert_go("修改成功","user_manage.php");
	}		
//	$_SESSION['ssh_enable'] = $ssh_enable;
//	$_SESSION['ctrl_center_enable'] = $ctrl_center_enable;	
}
/*else if ($_POST['userid'] !== "" and !empty($_POST['usermobile']) and !empty($_POST['usermail'])  and !empty($_POST['notice']))
{
	
	$userid = $_POST['userid'];
	$username = $_POST['username'];
	$usermobile = $_POST['usermobile'];
	$usermail = $_POST['usermail'];
	$usertype = $_POST['usertype'];
	$notice = $_POST['notice'];
	
	$update_sql = "update users set 
		UserMobile='$usermobile',
		UserMail = '$usermail',
		UserType = '$usertype',
		NoticeLevel = $notice 
		where id=".$userid;	
	do_sql($update_sql);
	
	$del_sql = "delete from userofgroup where Uid=".$userid;
	do_sql($del_sql);
	if ( $usertype == "user" and !empty($_POST['sgroup']))
	{
		$gids = $_POST['sgroup'];
		for ($i=0;$i<count($gids);$i++)
		{
			$gid = $gids[$i];
			$sql = "insert into userofgroup set Gid = ".$gid.",Uid = ".$userid.";";
			do_sql($sql);
		}
	}
save_do($s_u_id,$s_u_level,"用户".$s_u_name." 修改用户".$username."信息成功!");	
alert_go("修改成功","user_manage.php");
}
*/
else
{
	if (empty($_POST))
	{
		$userid = "";
	}
	if ($s_u_id == $userid)
	{
		$url = "user_update.php?userid=".$s_u_id;
		alert_go("用户数据不完整!",$url);
	}
	else
	{	
		alert_go("用户数据不完整!","user_manage.php");
	}	
}
?>
