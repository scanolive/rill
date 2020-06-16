<?php 
include 'head.php';
if ( $s_u_level > 2 )
{
		    alert_go("别调皮哦！","index.php");
}
if  (!empty($_POST['groupname']) and !empty($_POST['groupdesc']))
{	
	$users = $_POST['users'];
	$groupname = $_POST['groupname'];
	$groupdesc = $_POST['groupdesc'];
	$check_sql = "select id from ipgroup where GroupName='$groupname'"; 
	if (count(getrs($check_sql)) !== 0)
	{
		alert_go("分组已存在","group_add.php");
	}
	else
	{
		$get_gid_sql = "select id from ipgroup order by id desc limit 1";
		$gid_rs = getrs($get_gid_sql);
		$gid = $gid_rs[0][0] + 1;
		$insert_sql = "insert into ipgroup set GroupName='$groupname',Description = '$groupdesc', id = ".$gid; 
		do_sql($insert_sql);	
		
		$del_sql = "delete from userofgroup where Gid=".$gid;
		do_sql($del_sql);
		for ($i=0;$i<count($users);$i++)
		{
			$userid = $users[$i];
			$sql = "insert into userofgroup set Gid = ".$gid.",Uid = ".$userid.";";
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
		save_do($s_u_id,$s_u_level,"用户".$s_u_name." 添加".$groupname."组成功！");
		alert_go("添加成功","group.php");
	}
}
else
{
	$userall_sql = "select UserName,id from users where UserType = 'user';";
	$userall_rs = getrs($userall_sql);
}
?>

<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=7">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="css/style.css" rel="stylesheet" type="text/css">
<title><?php echo $TITLE_NAME."-";?>添加分组</title>
</head>
<body>
<table width="100%" height="5" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="3"></td>
  </tr>
</table>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top"><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="td_clo_blue">
      <tr>
        <td height="33" background="image/tab_bg1.gif">&nbsp;&nbsp;&nbsp;&nbsp;<span class="title1">添加分组</span></td>
      </tr>
      <tr>
        <td height="38">&nbsp;</td>
      </tr>
      <tr>
        <td><form action="" method="post" name="form" id="form" >
          <table width="80%" align="center" class="td_clo_blue">
            <tr>
              <td height="10" colspan="3" valign="middle"><input name="groupid" type="hidden" id="groupid" value="<?php echo $groupid;?>"></td>
            </tr>
            <tr>
              <td width="5%" height="28">&nbsp;</td>
              <td>组名(必填)：</td>
              <td><input name="groupname" type="text" class="input_txt" id="groupname" size="24" >              </td>
            </tr>
            <tr>
              <td height="28">&nbsp;</td>
              <td valign="top">描述(必填)：</td>
              <td><textarea name="groupdesc" cols="48" rows="8" class="input_txt" id="groupdesc"></textarea></td>
            </tr>
            <tr>
              <td height="28">&nbsp;</td>
              <td>管理员：</td>
              <td><?php for ( $i=0;$i<count($userall_rs);$i++ ){ ?>
                  <input name="users[]" type="checkbox" class="anniu" id="users[]"  value="<?php echo $userall_rs[$i][1];?>" <?php for ( $k=0;$k<count($user_rs);$k++ ){if ($userall_rs[$i][0] == $user_rs[$k][0]){ echo "checked";}  } ?> >
                  <?php echo $userall_rs[$i][0];echo "&nbsp;&nbsp;&nbsp;";}?></td>
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
