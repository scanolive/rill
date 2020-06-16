<?php 
include 'head.php';
include 'include/is_monitor.php';
if (!empty($_POST['bg_result']) and !empty($_POST['err_logs']) and !empty($_POST['history']) and !empty($_POST['devinfo']) and !empty($_POST['index']) and !empty($_POST['monstate']) and !empty($_POST['monweb']) and !empty($_POST['batchdo']))
{
	$history = $_POST['history'];
	$devinfo = $_POST['devinfo'];
	$index = $_POST['index'];
	$monstate = $_POST['monstate'];
	$monweb = $_POST['monweb'];
	$batchdo = $_POST['batchdo'];
	$err_logs = $_POST['err_logs'];
	$bg_result = $_POST['bg_result'];
	



	update_config("private","history_pagesize",$history,$s_u_id);
	update_config("private","bg_result_pagesize",$bg_result,$s_u_id);
	update_config("private","err_logs_pagesize",$err_logs,$s_u_id);
	update_config("private","devinfo_pagesize",$devinfo,$s_u_id);
	update_config("private","index_pagesize",$index,$s_u_id);
	update_config("private","monstate_pagesize",$monstate,$s_u_id);
	update_config("private","monweb_pagesize",$monweb,$s_u_id);
	update_config("private","batchdo_pagesize",$batchdo,$s_u_id);
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

	alert_go("修改成功","index.php");
}
else
{
	alert_go("POST数据不完整！","user_config_update.php");
}
?>
