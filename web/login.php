<?php include 'include/config.php'; 
$sql = "select UserName from users where  UserType = 'root';";
$user_name = getrs($sql);
if (count($user_name) == 0)
{
	alert_go("","init.php");
}

$sql = "select KeyValue from sys_config where KeyName='system_name'";
$TITLE_NAME = getrs($sql)[0][0];
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $TITLE_NAME."-";?>用户登录</title>
<link href="css/style.css" rel="stylesheet" type="text/css">
</head>

<body>

	<br>
    <br>
    <br>
  <br>
  <br>
  <br>
  <br>
<table width="360" align="center" bgcolor="#FFFFFF" class="td_clo_blue">
  <tr>
    <td height="32" background="image/tab_bg1.gif" class="title1">&nbsp;&nbsp;&nbsp;&nbsp;用户登录</td>
  </tr>
  <tr>
    <td><form action="login_do.php" method="post" name="send" id="send" onSubmit="return ChkFields()">
      <br />
      <table border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td height="32">用户名：</td>
          <td colspan="2"><input name="username" type="text" class="input_txt" id="username" /></td>
        </tr>
        <tr>
          <td height="32">密码：</td>
          <td colspan="2"><input name="pwd" type="password" class="input_txt" id="pwd" /></td>
        </tr>
        <tr>
          <td height="32" colspan="3"><table width="200" height="46" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
              <td><div align="center">
                  <input name="Submit" type="submit" class="input" value="提交" />
              </div></td>
              <td><div align="center">
                  <input name="Submit2" type="reset" class="input" value="重置" />
              </div></td>
            </tr>
          </table></td>
          </tr>
      </table>
      </form></td>
  </tr>
</table>
<script language="javascript">
function ChkFields(){
if(document.send.username.value==""){
   window.alert("请输入姓名")
   return false
}
if(document.send.pwd.value==""){
   window.alert("请输入密码")
   return false
}
return true
}
</script>
</body>
</html>
