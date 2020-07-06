<?php
include 'include/config.php';
if  (!empty($_POST['usertype']))
{
	if (!empty($_POST['username']) and !empty($_POST['password']) and !empty($_POST['usermail']) and !empty($_POST['usermobile']) and !empty($_POST['usertype'])  and !empty($_POST['notice']))
	{
		$username = $_POST ['username'];
		$password = md5($_POST ['password']);
		$usermail = $_POST ['usermail'];
		$usermobile = $_POST ['usermobile'];
		$usertype = $_POST ['usertype'];
		$notice = $_POST ['notice'];
		$insert_sql = "insert into users set 
		UserName = '$username',
		UserPasswd = '$password',
		UserMobile = '$usermobile',
		UserMail = '$usermail',
		UserType = '$usertype',
		DutyDate = '|0|1|2|3|4',
		DutyTime = '00-24',
		NoticeLevel = ".$notice;	
		do_sql($insert_sql); 
		alert_go("基本用户添加成功,登录后请配置报警邮件并调整各默认值","login.php");
	}
	else
	{
		alert_go("数据不完整","init.php");
	}
}	

function test_db()
{
	$conn = @mysql_connect(DBHOST.":".DBPORT,DBUSERNAME,DBPASSWD);
	if (!$conn)
	{
		$test_db = 'Could not connect mysql: ' . mysql_error();	
	}
	else
	{
		$use_db = @mysql_select_db(DBNAME);
		if ( ! $use_db )
		{
			$test_db = "NO DB ".DBNAME;
		}	
		else
		{
			$test_db = "OK";	
		}	
	}
	return $test_db;
}

function test_phpsocket()
{
	$socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	if ($socket < 0)
	{
 		$test_phpsocket = "PHP socket disable";
	}
	else
	{
		$test_phpsocket = "OK";	
	}
	return $test_phpsocket;
}

function test_user()
{
	$sql = "select UserName from users where  UserType = 'root';";
	$user_name = getrs($sql);
	if (count($user_name) == 0)
	{
		return 	"NO";
	}
	else
	{
		return  "OK";
	}
}

//$_SESSION['rlll_olive_scan_level'] = 1;

$test_db = test_db();
$test_phpsocket = test_phpsocket();

if ($test_db == "OK")
{
	$sql = "select KeyValue from sys_config where KeyName='upfile_dir';";
	$UPFILE_DIR = getrs($sql)[0][0];
}	

if(!is_writable($UPFILE_DIR))
{
	$test_upload_dir = "NO"; 	
}
else
{
	$test_upload_dir = "YES";
}

if ($test_db == "OK")
{
	$test_user = test_user();
}
else
{
	alert_go("","init_db.php");		
}
if ($test_db == "OK" and  $test_user == "OK" and $test_upload_dir == "YES")
{
	alert_go("系统检测成功","login.php");
}
$sql = "select KeyValue from sys_config where KeyName='system_name'";
$TITLE_NAME = getrs($sql)[0][0];
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=7">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="css/style.css" rel="stylesheet" type="text/css">
<title><?php echo $TITLE_NAME."-";?>初始化</title></head>
<body>
<br />
<br />
<br />
<br />
<br />
<table width="60%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#66CCFF">
  <tr>
    <td height="24">&nbsp;第一步 系统检测</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="24">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;数据库连接测试</td>
    <td><div align="right"><?php echo $test_db; ?>&nbsp;&nbsp;&nbsp;</div></td>
  </tr>
  <tr>
    <td height="24">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;php是否启用socket测试</td>
    <td><div align="right"><?php echo $test_phpsocket; ?>&nbsp;&nbsp;&nbsp;</div></td>
  </tr>
  <tr>
    <td height="24">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;默认上传目录写入权限测试</td>
    <td><div align="right"><?php echo $test_upload_dir ; ?>&nbsp;&nbsp;&nbsp;</div></td>
  </tr>
  <tr>
    <td height="24">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;默认post_max_size</td>
    <td><div align="right"><?php echo ini_get("post_max_size") ; ?>&nbsp;&nbsp;&nbsp;</div></td>
  </tr>
  <tr>
    <td height="24">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;默认upload_max_filesize</td>
    <td><div align="right"><?php echo ini_get("upload_max_filesize") ; ?>&nbsp;&nbsp;&nbsp;</div></td>
  </tr>
  <tr>
    <td height="10"></td>
    <td></td>
  </tr>
  <tr>
    <td height="24">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php if (ini_get("upload_max_filesize") == "2M" or ini_get("post_max_size") == "8M") echo "<font color='red'>**** 默认upload_max_filesize和post_max_size值较小,建议在php.ini中修改 </font>" ; ?> </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td height="10"></td>
    <td></td>
  </tr>
</table>
<?php if ($test_db == "OK"  and  $test_phpsocket == "OK") {?>
<table width="60%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#33FFFF">
  <tr>
    <td valign="top"><form action="" method="post" name="form" id="form" >
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="td_clo_blue">
          <tr>
            <td height="24" colspan="4">&nbsp;第二步 添加系统基本用户</td>
          </tr>
          <tr>
            <td width="10%" height="28">&nbsp;</td>
            <td>用户名:</td>
            <td colspan="2"><input name="username" type="text" id="username" size="28" /></td>
          </tr>
          <tr>
            <td height="28">&nbsp;</td>
            <td width="20%">密码：</td>
            <td colspan="2"><input name="password" type="password" id="password" size="28" /></td>
          </tr>
          <tr>
            <td height="28">&nbsp;</td>
            <td>Email：</td>
            <td colspan="2"><input name="usermail" type="text" id="usermail" size="28" /></td>
          </tr>
          <tr>
            <td height="28">&nbsp;</td>
            <td>电话：</td>
            <td colspan="2"><input name="usermobile" type="text" id="usermobile" size="28" /></td>
          </tr>
          <tr>
            <td height="28">&nbsp;</td>
            <td>报警方式：</td>
            <td><select name="notice" id="notice">
                <option value="4" selected="selected">邮件+短信</option>
                <option value="2">邮件报警</option>
                <option value="3">短信报警</option>
                <option value="1">不接受报警</option>
            </select>
              <input name="usertype" type="hidden" id="usertype" value="root" />
              <div align="center"></div></td>
            <td><input name="Submit" type="submit" class="delete" value="提交保存" /></td>
          </tr>
        </table>
    </form></td>
  </tr>
</table>
<?php } ?>
<DIV style="DISPLAY:none"   id="goTopBtn"><IMG src="image/top.png" border=0></DIV>
  <SCRIPT type=text/javascript>goTopEx();</SCRIPT>
</body>
</html>

