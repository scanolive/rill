<?php 
include 'head.php';
if ( $s_u_level > 2 )
{
	alert_go("别调皮哦！","index.php");
}
$sql_group_nogroup = "select id from ipgroup where GroupName='NoGroup';";
$nogroup_id = getrs($sql_group_nogroup);
$groupid = trim($_REQUEST['groupid']);
if ( $nogroup_id[0][0] == $groupid)
{
	alert_go("默认组不能删除！","group.php");
}

if (isset($_GET['groupid'])  and  $_GET['groupid'] !== "" and !empty($_GET['groupname']))
{
	$sql_group_nogroup = "select id from ipgroup where GroupName='NoGroup';";
	$nogroup_rs = getrs($sql_group_nogroup);
	$nogroup_id = $nogroup_rs[0][0];
	$groupid = $_GET['groupid'];
	$groupname = $_GET['groupname'];
	if ($nogroup_id !== $groupid  and  trim($groupname) !== "NoGroup" )
	{
		$delete_gu = "delete from userofgroup where Gid=".$groupid;	
		$delete_sql = "delete from ipgroup where id=".$groupid;
		$to_nogroup_sql = "update ipinfo set GroupId = ".$nogroup_id." where GroupId = ".$groupid;
		$to_nogroup_sql_alarms = "update alarms set Gid = ".$nogroup_id." where Gid = ".$groupid;
		$to_nogroup_sql_monweb = "update monweb set Gid = ".$nogroup_id." where Gid = ".$groupid;
		do_sql($delete_sql);
		do_sql($delete_gu);
		do_sql($to_nogroup_sql);
		do_sql($to_nogroup_sql_alarms);
		do_sql($to_nogroup_sql_monweb);
		if ($s_u_level < 3)
		{
			$sql_group = "select GroupName,id from ipgroup";
		}
		else
		{
			$sql_group = "select GroupName,ipgroup.id from ipgroup,users,userofgroup where userofgroup.Uid = users.id and  userofgroup.Gid = ipgroup.id  					and users.id=".s_u_id;
		}
		$rs_group = getrs($sql_group);
		$gids_arr = array();
		for ($i=0;$i<count($rs_group);$i++)
		{
			$gids_arr[] = $rs_group[$i][1];	
		}
		$_SESSION['rlll_olive_scan_user_gids'] = $gids_arr;
		$_SESSION['rlll_olive_scan_user_groups'] = $rs_group;
		save_do($s_u_id,$s_u_level,"用户".$s_u_name." 删除".$groupname."组成功！");
		alert_go("删除成功","group.php");
	}
	else
	{
		alert_go("不能删除默认组","group.php");
	}
}
else
{
alert_go("请指定删除组","group.php");
}
 include 'boot.php'; 
 ?>
