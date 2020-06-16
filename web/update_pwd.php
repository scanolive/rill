<?php
include 'head.php';
?>


<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $TITLE_NAME."-";?>修改密码</title>
<link href="css/style.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="98%" height="5" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="3"></td>
  </tr>
</table>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top"><form action="save_update_pwd.php" method="post" name="form" id="form" >
      <table  border="0" align="center" cellpadding="0" cellspacing="0" class="td_clo_blue">
        <tr>
          <td height="33" colspan="2" background="image/tab_bg1.gif">&nbsp;&nbsp;&nbsp;&nbsp;<span class="title1">修改密码</span></td>
        </tr>
        <tr>
          <td height="10" colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2"><table width="30%" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
              <td  height="28">&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td height="32"  >&nbsp;</td>
              <td >用户名：</td>
              <td><input name="username" type="text" class="input" id="username" value="<?php echo $s_u_name; ?>" readonly="readonly" /></td>
            </tr>
            <tr>
              <td height="32">&nbsp;</td>
              <td>旧密码：</td>
              <td><input name="old_pwd" type="password" class="input" id="old_pwd" /></td>
            </tr>
            <tr>
              <td height="32">&nbsp;</td>
              <td>新密码：</td>
              <td><input name="new_pwd" type="password" class="input" id="new_pwd" /></td>
            </tr>
            <tr>
              <td height="32">&nbsp;</td>
              <td>重复新密码：</td>
              <td><input name="new_r_pwd" type="password" class="input" id="new_r_pwd" /></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td width="80%" height="30">&nbsp;</td>
          <td><input name="Submit" type="submit" class="delete" value="保存配置"></td>
        </tr>
        <tr>
          <td height="10" colspan="2"></td>
        </tr>
      </table>
    </form></td>
  </tr>
</table>

<?php include 'boot.php'; ?>
</body>
</html>
