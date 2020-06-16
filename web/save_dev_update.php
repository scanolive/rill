<?php 
include 'head.php';
include 'include/is_monitor.php';

//if (!empty($_POST['ip']) and !empty($_POST['disklevel']) and !empty($_POST['loadlevel']) and !empty($_POST['networklevel']) and !empty($_POST['processlevel']) and !empty($_POST['connectlevel']) and !empty($_POST['loginlevel']))

if (!empty($_POST['ip']))
{	
	$url = ($_POST['url']);
	$ip = $_POST['ip'];
	$SN = $_POST['SN'];
	if (!empty($_POST['devname']))
	{
		$devname	= $_POST['devname'];
	}
	else
	{
		$devname = "NULL";
	}
			
	if (!empty($_POST['idc']))
	{
		$idc = $_POST['idc'];
	}
	else
	{
		$idc = "NULL";
	}
	if (!empty($_POST['place']))
	{
		$place	= $_POST['place'];
	}
	else
	{
		$place = "NULL";
	}
	if (!empty($_POST['capex']))
	{
		$capex	= $_POST['capex'];
	}
	else
	{
		$capex = "NULL";
	}
	if (!empty($_POST['opex']))
	{
		$opex = $_POST['opex'];
	}
	else
	{
		$opex = "NULL";
	}
	if (!empty($_POST['sgroup']))
	{
		$gid = $_POST['sgroup'];
		$update_gid = "update ipinfo set GroupId=".$gid." where Ip = '$ip'";
		$update_alarms = "update alarms set Gid=".$gid." where Ip = '$ip'";
	}
$update_sql = "update devinfo set
		Idc = '$idc',
		DevName = '$devname',
		Place = '$place',
		Capex_Price = $capex,
		Opex_Price = $opex
		where Ip='$ip' and
		SN = '$SN';";
		
//echo $update_sql;
do_sql($update_gid);
do_sql($update_alarms);
do_sql($update_sql);
save_do($s_u_id,$s_u_level,"用户".$s_u_name." 修改".$ip."设备信息成功！");
alert_go("修改成功",$url);
}
else
{
alert_go("NO IP!","devinfo.php");
}
?>
