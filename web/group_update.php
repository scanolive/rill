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
	alert_go("默认组不能编辑！","group.php");
}

if  (isset($_POST['groupid']) and $_POST['groupid'] !== "" and !empty($_POST['groupname']) and !empty($_POST['groupdesc']))
{
	$users = $_POST['users'];
	$groupid = trim($_POST['groupid']);
	$groupname = $_POST['groupname'];
	$groupdesc = $_POST['groupdesc'];
	$del_sql = "delete from userofgroup where Gid=".$groupid;
	do_sql($del_sql);
	$update_sql = "update ipgroup set GroupName='$groupname',Description = '$groupdesc' where id=".$groupid;
	do_sql($update_sql);
	for ($i=0;$i<count($users);$i++)
	{
		$userid = $users[$i];
		$sql = "insert into userofgroup set Gid = ".$groupid.",Uid = ".$userid.";";
		do_sql($sql);
	}
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
	save_do($s_u_id,$s_u_level,"用户".$s_u_name." 更新".$groupname."组信息成功");
	alert_go("更新成功","group.php");
}

else if ( isset($_GET['groupid']) and  $_GET['groupid'] !== "")
{
	$groupid = ($_GET['groupid']);
	$userall_sql = "select UserName,id from users where UserType = 'user';";
	$user_sql = "select UserName,users.id  from userofgroup,users where users.id = userofgroup.Uid and Gid=$groupid;";
	$group_sql = "select GroupName,Description from ipgroup where  ipgroup.id=$groupid";
	$userall_rs = getrs($userall_sql);
	$group_rs = getrs($group_sql);
	$user_rs = getrs($user_sql); 
}
else 
{
alert_go("请指定更新的组","group.php");
}
?>

<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=7">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="css/style.css" rel="stylesheet" type="text/css">
<title><?php echo $TITLE_NAME."-";?>修改组信息</title>
</head>
<body>
<table width="98%" height="5" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="3"></td>
  </tr>
</table>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top"><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="td_clo_blue">
      <tr>
        <td height="33" background="image/tab_bg1.gif">&nbsp;&nbsp;&nbsp;&nbsp;<span class="title1">修改组信息</span></td>
      </tr>
      <tr>
        <td height="8"></td>
      </tr>
      <tr>
        <td height="30">&nbsp;</td>
      </tr>
      <tr>
        <td><form action="" method="post" name="form" id="form" >
          <table width="80%" align="center" class="td_clo_blue">
            <tr>
              <td height="10" colspan="3" valign="middle"><input name="groupid" type="hidden" id="groupid" value="<?php echo $groupid;?>"></td>
            </tr>
            <tr>
              <td width="15%" height="28">&nbsp;</td>
              <td>组名：</td>
              <td><input name="groupname" type="text" class="input_txt" id="groupname" value="<?php echo $group_rs[0][0];?>" size="24"  >              </td>
            </tr>
            <tr>
              <td height="28">&nbsp;</td>
              <td valign="top">描述：</td>
              <td><textarea name="groupdesc" cols="48" rows="8" class="input_txt" id="groupdesc"><?php echo $group_rs[0][1];?></textarea></h2></td>
            </tr>
            <tr>
              <td height="28">&nbsp;</td>
              <td>管理员：</td>
              <td><?php for ( $i=0;$i<count($userall_rs);$i++ ){ ?>
                  <input name="users[]" type="checkbox" class="radio" id="users[]"  value="<?php echo $userall_rs[$i][1];?>" <?php for ( $k=0;$k<count($user_rs);$k++ ){if ($userall_rs[$i][0] == $user_rs[$k][0]){ echo "checked";}  } ?> ><?php echo $userall_rs[$i][0];echo "&nbsp;&nbsp;&nbsp;";}?></td>
            </tr>
            <tr>
              <td height="10">&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
          </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td height="5" colspan="2"></td>
            </tr>
            <tr>
              <td width="80%"></td>
              <td><input name="Submit" type="submit" class="delete" id="Submit" value="保存配置"></td>
            </tr>
            <tr>
              <td height="5" colspan="2"></td>
            </tr>
          </table>
        </form>            </td>
      </tr>
      <tr>
        <td height="10"></td>
      </tr>
    </table></td>
  </tr>
</table>
<?php include 'boot.php'; ?>
</body>
</html> 
