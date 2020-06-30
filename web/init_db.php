<?php
include 'include/config.php';
$db_config_file = dirname(__FILE__)."/include/db_config.php";
function test_db($db_host,$db_port,$db_name,$db_username,$db_password)
{
	$conn = @mysql_connect($db_host.":".$db_port,$db_username,$db_password);
	if (!$conn)
	{
		$test_db = 'Could not connect mysql: ' . mysql_error();	
	}
	else
	{
		$use_db = @mysql_select_db($db_name);
		if ( ! $use_db )
		{
			$test_db = "NO DB ".$db_name;
		}	
		else
		{
			$test_db = "OK";	
		}	
	}
		return $test_db;
}

if (file_exists($db_config_file))
{
	include 'include/db_config.php';
	if ((test_db(DBHOST,DBPORT,DBNAME,DBUSERNAME,DBPASSWD) == "OK" ))
	{
		alert_go("数据库已配置","init.php");			
	}			
}		

if  (!empty($_POST['usertype']))
{
	if (!empty($_POST['db_host']) and !empty($_POST['db_port']) and !empty($_POST['db_name']) and !empty($_POST['db_username']) and !empty($_POST['db_password']))
	{
		$db_host = $_POST ['db_host'];
		$db_port = $_POST ['db_port'];
		$db_name = $_POST ['db_name'];
		$db_username = $_POST ['db_username'];
		$db_password = $_POST ['db_password'];

		if (test_db($db_host,$db_port,$db_name,$db_username,$db_password) == "OK")
		{
				$db_txt = <<<string

define ("DBHOST","$db_host");
define ("DBPORT",$db_port);
define ("DBNAME","$db_name");
define ("DBUSERNAME","$db_username");
define ("DBPASSWD","$db_password");

string;
			$path = dirname(__FILE__)."/include/";
			if(is_writable($path))
			{
				$myfile = fopen($path."db_config.php", "w") or die("Unable to open file!");
				fwrite($myfile, '<?php '.$db_txt.' ?>');
				fclose($myfile);
				alert_go("数据库配置成功","init.php");
			}
			else
			{
				alert_go($path."没有写入权限!","init_db.php");
			}
		}
		else
		{
			alert_go("数据库连接失败","init_db.php");
		}
	}
	else
	{
		alert_go("数据不完整","init_db.php");
	}
}	
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=7">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="css/style.css" rel="stylesheet" type="text/css">
<title>配置数据库</title></head>
<body>
<br />
<br />
<br />
<br />
<br />
<form action="" method="post" name="form" id="form" >
        <table width="300" border="0" align="center" cellpadding="0" cellspacing="0" >
          <tr>
            <td height="32">数据库主机:</td>
            <td ><input name="db_host" type="text" id="db_host" size="28" /></td>
          </tr>
          <tr>
            <td height="32" >数据库端口:</td>
            <td ><input name="db_port" type="text" id="db_host" size="28" /></td>
          </tr>
          <tr>
            <td height="32">数据库名:</td>
            <td ><input name="db_name" type="text" id="db_name" size="28" /></td>
          </tr>
          <tr>
            <td  height="32">数据库用户名:</td>
            <td ><input name="db_username" type="text" id="db_username" size="28" /></td>
          </tr>
          <tr>
            <td height="32">数据库密码:</td>
            <td ><input name="db_password" type="password" id="db_password" size="28" /></td>
          </tr>
        </table>
<input name="usertype" type="hidden" id="usertype" value="root" />
		<table width="300"  border="0" align="center" cellpadding="0" cellspacing="0" >
          <tr><td align="center"   height="50"> <input name="submit" type="submit"  /> </td></tr>
		</table>
    </form>
<DIV style="DISPLAY:none"   id="goTopBtn"><IMG src="image/top.png" border=0></DIV>
  <SCRIPT type=text/javascript>goTopEx();</SCRIPT>
</body>
</html>
