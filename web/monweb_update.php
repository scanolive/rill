<?php 
include 'head.php';
include 'include/is_monitor.php';
if ($_GET['id'] == "")
{
	alert_go("请指定条目","monweb.php");
}
else
{
	$monwebid = trim($_GET ['id']);
	$group = trim($_GET ['group']);
	$groupall = "select GroupName,id from ipgroup;";
	$groupall_rs = getrs($groupall);
	$group_sql = "select GroupName,ipgroup.id  from userofgroup,ipgroup where ipgroup.id = userofgroup.Gid and Uid=".$s_u_id;
	$group_rs = getrs($group_sql);

	$sql = "select MonName,MonUrl,Gid from monweb where id = ".$monwebid;
	$rs = getrs($sql);
	
}
if (!(in_array($rs[0][2],$s_u_gids)))
{
	alert_go("别调皮哦！","monweb.php");
}
?>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=7">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate"> 
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="css/style.css" rel="stylesheet" type="text/css">
<title><?php echo $TITLE_NAME."-";?>修改页面监控</title>
</head>
<body>
<table width="98%" height="5" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="3"></td>
  </tr>
</table>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" class="td_clo_blue">
      <tr>
        <td height="33" background="image/tab_bg1.gif">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="title1">修改页面监控</span></td>
      </tr>
      <tr>
        <td height="8"></td>
      </tr>
      <tr>
        <td><form action="save_monweb_update.php" method="post" name="form" id="form" >
          <table width="80%" align="center" class="td_clo_blue">
            <tr>
              <td height="10" colspan="3" valign="middle">&nbsp;</td>
            </tr>
            <tr>
              <td width="5%" height="28"><input name="id" type="hidden" id="id" value="<?php echo $monwebid; ?>" ></td>
              <td width="10%">监控名称 </td>
              <td><input name="monname" type="text" class="input_txt" id="monname" value="<?php echo $rs[0][0]; ?>"  size="24" >
                  </h2></td>
            </tr>
            <tr>
              <td height="28">&nbsp;</td>
              <td valign="top">监控页面</td>
              <td><textarea name="monurl" cols="72" rows="3"  class="input_txt" id="monurl" ><?php echo $rs[0][1]; ?></textarea></td>
            </tr>
			 <tr>
              <td height="28">&nbsp;</td>
              <td>所属分组</td>
              <td>
                <?php include 'include/sgroup.php'; ?>
              </select></td>
			 </tr>
            <tr>
              <td height="10" colspan="3">&nbsp;</td>
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
        </form></td>
        </tr>
      <tr>
        <td height="10"></td>
      </tr>
    </table>    </td>
  </tr>
</table>
<?php include 'boot.php'; ?>
</body>
</html> 
