<?php
include 'head.php';
if ( $s_u_level > 2 ) 
{
	alert_go("别调皮哦！","index.php");
}

if (!empty($_POST['username']) and !empty($_POST['password']) and !empty($_POST['usermail']) and !empty($_POST['usermobile']) and !empty($_POST['usertype'])  and !empty($_POST['notice']) and !empty($_POST['sgroup']))
{
	$username = $_POST ['username'];
	$password = md5($_POST ['password']);
	$usermail = $_POST ['usermail'];
	$usermobile = $_POST ['usermobile'];
	$usertype = $_POST ['usertype'];
	$notice = $_POST ['notice'];
	$dutydate_arr = $_POST['dutydate'];
	$dutystart = $_POST['dutystart'];
	$dutyend = $_POST['dutyend'];
	$gids = $_POST ['sgroup'];
	$dutytime = $dutystart."-".$dutyend;
	$dutydate = "";
	foreach ($dutydate_arr as $value)
	{
		$dutydate = $dutydate.'|'.$value;
	}
//	echo $dutydate;
//	echo $dutytime;
	
	$check_sql = "select id from users where UserName='$username'";
	if (count(getrs($check_sql)) !== 0)
	{
		alert_go("用户已存在","user_add.php");
	}
	else
	{
		$create_time = date('Y-m-d H:i:s');
		$get_uid_sql = "select id from users order by id desc limit 1";
		$uid_rs = getrs($get_uid_sql);
		$userid = $uid_rs[0][0]+1;
		$insert_sql = "insert into users set 
		UserName = '$username',
		UserPasswd = '$password',
		UserMobile = '$usermobile',
		DutyDate = '$dutydate',
		DutyTime = '$dutytime',
		UserMail = '$usermail',
		UserType = '$usertype',
		CreateTime = '$create_time',
		NoticeLevel = ".$notice.",
		id = ".$userid;	
		do_sql($insert_sql); 
		for ($i=0;$i<count($gids);$i++)
		{
			$gid = $gids[$i];
			$sql = "insert into userofgroup set Gid = ".$gid.",Uid = ".$userid.";";
			do_sql($sql);
		}
		save_do($s_u_id,$s_u_level,"用户".$s_u_name." 添加用户".$username."成功!");
		alert_go("用户添加成功","user_manage.php");
	}
}	
else if (!empty($_POST['username']) and !empty($_POST['password']) and !empty($_POST['usermail']) and !empty($_POST['usermobile']) and !empty($_POST['usertype'])  and !empty($_POST['notice']) and empty($_POST['sgroup']))
{
	$username = $_POST ['username'];
	$password = md5($_POST ['password']);
	$usermail = $_POST ['usermail'];
	$usermobile = $_POST ['usermobile'];
	$dutydate_arr = $_POST['dutydate'];
	$dutystart = $_POST['dutystart'];
	$dutyend = $_POST['dutyend'];	
	$usertype = $_POST ['usertype'];
	$notice = $_POST ['notice'];
	$dutytime = $dutystart."-".$dutyend;
	$dutydate = "";
	foreach ($dutydate_arr as $value)
	{
		$dutydate = $dutydate.'|'.$value;
	}
//	echo $dutydate;
//	echo $dutytime;
	
	$check_sql = "select id from users where UserName='$username'";
	if (count(getrs($check_sql)) !== 0)
	{
		alert_go("用户已存在","user_add.php");
	}
	else
	{
		$create_time = date('Y-m-d H:i:s');
		$insert_sql = "insert into users set 
		UserName = '$username',
		UserPasswd = '$password',
		UserMobile = '$usermobile',
		UserMail = '$usermail',
		DutyDate = '$dutydate',
		DutyTime = '$dutytime',
		UserType = '$usertype',
		CreateTime = '$create_time',
		NoticeLevel = ".$notice;	
		do_sql($insert_sql); 
		save_do($s_u_id,$s_u_level,"用户".$s_u_name." 添加用户".$username."成功!");
		alert_go("用户添加成功","user_manage.php");
	}
}
else 
{
	alert_go("用户数据不完整","user_add.php");
}
?>
