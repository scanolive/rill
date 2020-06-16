<?php 
include 'head.php';
include 'include/is_monitor.php';

if (!empty($_POST['ip']))
{
	$ip = $_POST['ip'];
	$disklevel = request($_POST['disklevel']);
	if ($disklevel == "") {$disklevel = 'NULL';}
	$loadlevel = request($_POST['loadlevel']);
	if ($loadlevel == "") {$loadlevel = 'NULL';}
	$networklevel = request($_POST['networklevel']);
	if ($networklevel == "") {$networklevel = 'NULL';}
	$processlevel = request($_POST['processlevel']);
	if ($processlevel == "") {$processlevel = 'NULL';}
	$loginlevel	= request($_POST['loginlevel']);
	if ($loginlevel == "") {$loginlevel = 'NULL';}
	$connectlevel = request($_POST['connectlevel']);
	if ($connectlevel == "") {$connectlevel = 'NULL';}
	$ports = request($_POST['ports']);
	if (!empty($_POST['ports']))
	{
		$ports = $_POST['ports'];
		$mon_ports = "";
		foreach ($ports  as  $port)
		{
			$mon_ports .= $port.",";
		}
		$down_mon_ports_sql = "update  ports,ipinfo set IsMon = 0 where ipinfo.id = ports.Ipid and ipinfo.IP='$ip'";
		$mon_ports = "(".$mon_ports.$ports[0].")";
		$up_mon_ports_sql = "update  ports,ipinfo set IsMon = 1 where ipinfo.id = ports.Ipid and ipinfo.IP='$ip' and ports.id in $mon_ports";
		do_sql($down_mon_ports_sql);
		do_sql($up_mon_ports_sql);
	}
	else
	{
		$down_mon_ports_sql = "update  ports,ipinfo set IsMon = 0 where ipinfo.id = ports.Ipid and ipinfo.IP='$ip'";
		do_sql($down_mon_ports_sql);
	}		

	$update_sql = "update ipinfo set DiskLevel = $disklevel,
		LoadLevel = $loadlevel,
		LoginLevel = $loginlevel,
		NetworkLevel = $networklevel,
		ProcessLevel = $processlevel,
		ConnectLevel =  $connectlevel
		where Ip='$ip';";
	$dict_data = "{'LoadLevel':$loadlevel,'LoginLevel':$loginlevel,'NetworkLevel':$networklevel,'ProcessLevel':$processlevel,'ConnectLevel',$connectlevel}";
	do_sql($update_sql);
	//save_do($s_u_id,$s_u_level,"用户".$s_u_name." 修改".$ip."监控阀值成功！");
	echo '<script language="javascript">showHint_socket("Sync_Db","OLIVE_SERVER","'.$_SESSION['verify_str'].'") </script>';
	echo '<script language="javascript">alert("修改成功");window.location.href="mon_config.php?ip='.$ip.'"+"&"+"t="+Math.random();</script>';
}
else
{
	echo '<script language="javascript">alert("请指定IP");window.location.href="mon_state.php";</script>';
}
?>
