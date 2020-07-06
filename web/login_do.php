<?php
include 'include/config.php';
$SYSCONFIG = get_sysconfig_arr();

if (!empty($_POST['username']) and !empty($_POST['pwd']))
{
	$sql = "select * from sys_config where Uid='NoUID'";
	$sys_rs = getrs($sql);
	$SYSCONFIG = array();
	for( $i=0;$i<count($sys_rs);$i++ )
	{       
		$SYSCONFIG[$sys_rs[$i][3]] = $sys_rs[$i][4];
	}           
	
	$user=$_POST['username'];
	$pwd=md5($_POST['pwd']);
	$sql="select UserType,id from users where UserName='".$user."' and UserPasswd='".$pwd."' ";
	$rs = getrs($sql);
	
	if(count($rs) == 1)
	{
		if ($rs[0][0] == "root")
		{
			$_SESSION['rlll_olive_scan_level'] = 1;
			$sql_group = "select GroupName,id from ipgroup";
			$sql_users = "select UserName,id from users";
			$_SESSION['rlll_olive_scan_users_rs'] = getrs($sql_users);
		}
		else if ($rs[0][0] == "admin")
		{
			$_SESSION['rlll_olive_scan_level'] = 2;
			$sql_group = "select GroupName,id from ipgroup";
			$sql_users = "select UserName,id from users where UserType = 'user' or UserType = 'monitor' or id = " .$rs[0][1];
			$_SESSION['rlll_olive_scan_users_rs'] = getrs($sql_users);
		}
		else if ($rs[0][0] == "user")
		{
			$_SESSION['rlll_olive_scan_level'] = 3;
			$sql_group = "select GroupName,ipgroup.id from ipgroup,users,userofgroup where userofgroup.Uid = users.id and  userofgroup.Gid = ipgroup.id  and users.id=".$rs[0][1];
			$_SESSION['rlll_olive_scan_users_rs'] =  "";
		}		
		else if ($rs[0][0] == "monitor")
		{
			$_SESSION['rlll_olive_scan_level'] = 4;
			$sql_group = "select GroupName,ipgroup.id from ipgroup,users,userofgroup where userofgroup.Uid = users.id and  userofgroup.Gid = ipgroup.id  and users.id=".$rs[0][1];
			$_SESSION['rlll_olive_scan_users_rs'] =  "";
		}
		$_SESSION['rlll_olive_scan_username'] = $user;
		$_SESSION['rlll_olive_scan_userid'] = $rs[0][1];
		$_SESSION['rlll_olive_scan_usertype'] = $rs[0][0];
		$rs_group = getrs($sql_group);
		$gids_arr = array();
		for ($i=0;$i<count($rs_group);$i++)
		{
			$gids_arr[] = $rs_group[$i][1];	
		}
		$_SESSION['rlll_olive_scan_user_gids'] = $gids_arr;
		$_SESSION['rlll_olive_scan_user_groups'] = $rs_group;
		$update_sql = "update users set LoginNum = LoginNum+1,IsOnline=1 where  UserName='".$user."'"; 
		do_sql($update_sql);
		
		$_SESSION['history_pagesize'] = get_config("private","history_pagesize",$rs[0][1],$SYSCONFIG);
		$_SESSION['bg_result_pagesize'] = get_config("private","bg_result_pagesize",$rs[0][1],$SYSCONFIG);
		$_SESSION['err_logs_pagesize'] = get_config("private","err_logs_pagesize",$rs[0][1],$SYSCONFIG);
		$_SESSION['devinfo_pagesize'] = get_config("private","devinfo_pagesize",$rs[0][1],$SYSCONFIG);
		$_SESSION['index_pagesize'] = get_config("private","index_pagesize",$rs[0][1],$SYSCONFIG);
		$_SESSION['monstate_pagesize'] = get_config("private","monstate_pagesize",$rs[0][1],$SYSCONFIG);
		$_SESSION['monweb_pagesize'] = get_config("private","monweb_pagesize",$rs[0][1],$SYSCONFIG);
		$_SESSION['batchdo_pagesize'] = get_config("private","batchdo_pagesize",$rs[0][1],$SYSCONFIG);

		$_SESSION['ssh_enable'] = get_config("private","ssh_enable",$rs[0][1],$SYSCONFIG);
		$_SESSION['ctrl_center_enable'] = get_config("private","ctrl_center_enable",$rs[0][1],$SYSCONFIG);
		$_SESSION['verify_str'] = randstr(24);

		$_SESSION['python_server_ip'] = $SYSCONFIG['python_server_ip'];
		$_SESSION['python_server_port'] = $SYSCONFIG['python_server_port'];
		$_SESSION['python_oct_cmd_pre'] = $SYSCONFIG['python_oct_cmd_pre'];
		$_SESSION['python_end_cmd_str'] = $SYSCONFIG['python_end_cmd_str'];
		$_SESSION['python_end_str'] = $SYSCONFIG['python_end_str'];
		$_SESSION['python_sep_str'] = $SYSCONFIG['python_sep_str'];
		$_SESSION['python_sep_str_se'] = $SYSCONFIG['python_sep_str_se'];

		$_SESSION['upfile_dir'] = $SYSCONFIG['upfile_dir'].'/';
		$_SESSION['system_name'] = $SYSCONFIG['system_name'];
		$_SESSION['norun_cmd_arr'] = explode(',',$SYSCONFIG['norun_cmd']);
		$_SESSION['max_upfile_size'] = $SYSCONFIG['max_upfile_size'];
		$_SESSION['php_timeout'] = $SYSCONFIG['php_timeout'];
		

		$client_ip = getip();
		save_do($rs[0][1],$_SESSION['rlll_olive_scan_level'],"用户".$user." 自".$client_ip." 登录成功！");
		if ($user == "aqm_syn")
		{	
			echo "<script>window.location.href='syn_aiqiumi.php'</script>";
		}		
		else
		{		
			echo "<script>window.location.href='index.php'</script>";
		}
   	}
	else
	{
		save_do("NULL","NULL","用户".$user."  密码".$_POST['pwd']."尝试登录失败！");
		alert_go("的密码或者账号错误！","login.php");
	}
}
else
{
	alert_go("用户名密码不能为空！","login.php");
}
?> 
 
