<?php 
include 'head.php';
include 'include/is_monitor.php';
$id = ($_GET['id']);
$gid = ($_GET['gid']);
$url = ($_GET['url']);
if ($url=="")
$url="index.php";
if (!in_array($gid,$s_u_gids))
alert_go("你没有此条备注修改权限",$url);


$sql = "select Note,Msg from alarms where id='$id';";
$rs = getrs($sql);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $TITLE_NAME."-";?>警报备注</title>
</head>

<body>
<table width="100%" height="5" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td></td>
  </tr>
</table>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top"><form action="save_alarms_note.php" method="post" name="form" id="form" >
      <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" class="td_clo_blue">
        <tr>
          <td height="33" background="image/tab_bg1.gif" class="title1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;页面监控</td>
        </tr>
        <tr>
          <td height="10"></td>
        </tr>
        <tr>
          <td height="10"><table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
              <td width="100" height="24">消息内容
                <input name="id" type="hidden" id="id" value="<?php echo $id;?>" />
                <input name="gid" type="hidden" id="gid" value="<?php echo $gid;?>" />
                <input name="url" type="hidden" id="url" value="<?php echo $url;?>" /></td>
              <td><?php echo $rs[0][1]; ?></td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td height="24">备注                </td>
              <td><input name="note" type="text" id="note" value="<?php echo $rs[0][0];?>" size="36" /> 
                最多60汉字或120字符 </td>
              <td><input name="Submit2" type="submit" class="delete" value="保存配置" /></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td height="10"></td>
        </tr>
      </table>
    </form></td>
  </tr>
</table>
</body>
</html>
