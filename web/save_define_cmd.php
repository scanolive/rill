<?php
include 'head.php';
if ( $s_u_level > 2 ) 
{
	alert_go("别调皮哦！","index.php");
}

if (!empty($_POST['cmdname']) and !empty($_POST['cmdstr']) and !empty($_POST['note']))
{
	$cmdname = $_POST ['cmdname'];
	$d_file = $_POST['d_file'];
	$cmdstr = $_POST ['cmdstr'];
	$note = $_POST ['note'];
	echo	$_POST ['check_shell'];
	if ($_POST ['check_shell'] == 1)
	{
		$file_id = $_POST ['file_id'];
	}		
	else
	{
		$file_id = 0;
	}


	$cmdstr = str_replace('"',"r#o#s_syh",$cmdstr);
	$cmdstr = str_replace("'","r#o#s_dyh",$cmdstr);
	$cmdstr = str_replace(';',"r#o#s_fh",$cmdstr);
	$cmdstr = str_replace('\\',"r#o#s_fxg",$cmdstr);

	$check_sql = "select id from define_cmd where CmdName='$cmdname'";
	if (count(getrs($check_sql)) !== 0)
	{
		alert_go("命令存在","define_cmd.php");
	}
	else
	{

		$insert_sql = "insert into define_cmd set 
		CmdName = '$cmdname',
		ShellFileId = '$file_id',
		CmdStr = '$cmdstr',
		Note= '$note'";
		do_sql($insert_sql); 
		//echo 	$insert_sql;	
		alert_go("添加成功","define_cmd.php");
	}
}	

else 
{
	alert_go("用户数据不完整","define_cmd.php");
}
?>
