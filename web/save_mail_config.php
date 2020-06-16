<?php 
if (!empty($_POST['server']) and !empty($_POST['port']) and !empty($_POST['user']) and !empty($_POST['passwd']) and !empty($_POST['address']) and !empty($_POST['username']))
{
$port = $_POST['port'];
$server	= $_POST['server'];
$user = $_POST['user'];
$passwd = $_POST['passwd'];
$username = $_POST['username'];
$address = $_POST['address'];
include 'include/doconn.php';
$sql_num = "select count(id) from mail_config;";
$num = mysql_fetch_array (mysql_query ( $sql_num));
$nu=$num[0];
if ($nu == 0)
{
$sql = "insert mail_config set Name ='$user',Port =$port, Host = '$server', Passwd = '$passwd', SendName = '$username', Address = '$address';";
}
else
{
$sql = "update mail_config set Name ='$user',Port =$port, Host = '$server', Passwd = '$passwd', SendName = '$username', Address = '$address';";
}
$result = mysql_query($sql);
mysql_close ();
echo "配置保存成功！";
}
else
{
echo "POST数据不完整";
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>无标题文档</title>
</head>
<body>
</body>
</html>
