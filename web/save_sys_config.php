<?php 
include 'head.php';
if (!empty($_POST['bg_result']) and !empty($_POST['err_logs']) and !empty($_POST['history']) and !empty($_POST['devinfo']) and !empty($_POST['index']) and !empty($_POST['monstate']) and !empty($_POST['monweb']) and !empty($_POST['python_server_ip']) and !empty($_POST['python_server_port']) and !empty($_POST['php_timeout']) and !empty($_POST['python_oct_cmd_pre']) and !empty($_POST['python_end_cmd_str']) and !empty($_POST['python_end_str']) and !empty($_POST['python_sep_str']) and !empty($_POST['python_sep_str_se']) and !empty($_POST['upfile_dir']) and !empty($_POST['system_name']) and !empty($_POST['max_upfile_size']) and !empty($_POST['norun_cmd']) and !empty($_POST['def_mon_ports']) and !empty($_POST['monweb_interval']))
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
	$history = $_POST['history'];
	$err_logs = $_POST['err_logs'];
	$bg_result = $_POST['bg_result'];
	$devinfo = $_POST['devinfo'];
	$index = $_POST['index'];
	$monstate = $_POST['monstate'];
	$monweb = $_POST['monweb'];
	$batchdo = $_POST['batchdo'];
	$upfile_dir = $_POST['upfile_dir'];
	$system_name = $_POST['system_name'];
	$max_upfile_size = $_POST['max_upfile_size'];
	$norun_cmd = $_POST['norun_cmd'];
	$python_server_ip = $_POST['python_server_ip'];
	$python_server_port = $_POST['python_server_port'];
	$php_timeout = $_POST['php_timeout'];
	$python_oct_cmd_pre = $_POST['python_oct_cmd_pre'];
	$python_end_cmd_str = $_POST['python_end_cmd_str'];
	$python_end_str = $_POST['python_end_str'];
	$python_sep_str = $_POST['python_sep_str'];
	$python_sep_str_se = $_POST['python_sep_str_se'];
	$monweb_interval = $_POST['monweb_interval'];
	$def_mon_ports = $_POST['def_mon_ports'];

	update_config("public","ctrl_center_enable",$ctrl_center_enable,"NoUid");
	update_config("public","ssh_enable",$ssh_enable,"NoUid");
	update_config("public","bg_result_pagesize",$bg_result,"NoUid");
	update_config("public","err_logs_pagesize",$err_logs,"NoUid");
	update_config("public","history_pagesize",$history,"NoUid");
	update_config("public","devinfo_pagesize",$devinfo,"NoUid");
	update_config("public","index_pagesize",$index,"NoUid");
	update_config("public","monstate_pagesize",$monstate,"NoUid");
	update_config("public","monweb_pagesize",$monweb,"NoUid");
	update_config("public","batchdo_pagesize",$monweb,"NoUid");
	update_config("public","php_timeout",$php_timeout,"NoUid");
	update_config("public","upfile_dir",$upfile_dir,"NoUid");
	update_config("public","system_name",$system_name,"NoUid");
	update_config("public","max_upfile_size",$max_upfile_size,"NoUid");
	update_config("public","norun_cmd",$norun_cmd,"NoUid");
	update_config("public","python_server_ip",$python_server_ip,"NoUid");
	update_config("public","python_server_port",$python_server_port,"NoUid");
	update_config("public","python_oct_cmd_pre",$python_oct_cmd_pre,"NoUid");
	update_config("public","python_end_cmd_str",$python_end_cmd_str,"NoUid");
	update_config("public","python_end_str",$python_end_str,"NoUid");
	update_config("public","python_sep_str",$python_sep_str,"NoUid");
	update_config("public","python_sep_str_se",$python_sep_str_se,"NoUid");
	update_config("public","def_mon_ports",$def_mon_ports,"NoUid");
	update_config("public","monweb_interval",$monweb_interval,"NoUid");
/*	update_pagesize("history",$history,$s_u_id);
	update_pagesize("devinfo",$devinfo,$s_u_id);
	update_pagesize("index",$index,$s_u_id);
	update_pagesize("monstate",$monstate,$s_u_id);
	update_pagesize("monweb",$monweb,$s_u_id);
*/	

	$_SESSION['history_pagesize'] = $history;
	$_SESSION['err_logs_pagesize'] = $err_logs;
	$_SESSION['bg_result_pagesize'] = $bg_result;
	$_SESSION['devinfo_pagesize'] = $devinfo;
	$_SESSION['index_pagesize'] = $index;
	$_SESSION['monstate_pagesize'] = $monstate;
	$_SESSION['monweb_pagesize'] = $monweb;
	$_SESSION['batchdo_pagesize'] = $batchdo;
	$_SESSION['ssh_enable'] = $ssh_enable;
	$_SESSION['ctrl_center_enable'] = $ctrl_center_enable;
	$_SESSION['python_server_ip'] = $python_server_ip;
	$_SESSION['python_server_port'] = $python_server_port;
	$_SESSION['php_timeout'] = $php_timeout;
	$_SESSION['norun_cmd_arr'] = explode(',',$norun_cmd);	
	
	
	echo '<script language="javascript">showHint_socket("Sync_Db_Sys_Config","OLIVE_SERVER","'.$_SESSION['verify_str'].'") </script>';
	alert_go("修改成功","index.php");
}
else
{
	alert_go("POST数据不完整！","sys_config_update.php");
}
?>
