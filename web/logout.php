<?php
include 'head.php';
$update_sql = "update users set IsOnline=0 where  UserName='".$_SESSION['rlll_olive_scan_username']."'"; 
session_destroy();
save_do($s_u_id,$s_u_level,"用户".$s_u_name." 退出系统成功！");
alert_go("注销成功！","login.php");
?> 
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
</body>
</html>
