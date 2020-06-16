<?php
include 'head.php';
include 'include/is_monitor.php';
	$username = $_SESSION['rlll_olive_scan_username'];
	$sql_user = "select UserType from users where UserName='$username';";
	$user_type = getrs( $sql_user );
	$user_type_rs = $user_type[0][0];
if ( $s_u_level == 2 ) 
{
	$sql_admin = "select UserName,UserType,UserMail,UserMobile,id from users where  UserType = 'user' or UserType = 'monitor'";
	$user_rs = getrs($sql_admin);
}
else if ( $s_u_level == 1 ) 
{
	$sql_admin = "select UserName,UserType,UserMail,UserMobile,id from users where  UserType != 'root'";
	$user_rs = getrs($sql_admin);	
}
else 
{
	alert_go("非管理员","index.php");
}
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=7">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $TITLE_NAME."-";?>用户中心</title>
<link href="css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<table width="100%" height="5" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td></td>
  </tr>
</table>
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top"><form action="" method="post" name="form1" >
      <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="td_clo_blue">
        <tr>
          <td height="33" background="image/tab_bg1.gif">&nbsp;&nbsp;&nbsp;&nbsp;<span class="title1">&nbsp;</span><span class="title1">用户管理</span></td>
        </tr>
        <tr>
          <td height="8"></td>
        </tr>
        <tr>
          <td><div align="center">
            <table width="98%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#99FFCC" class="Ptable" >
              <tr>
                <td height="25">用户名</td>
                <td>权限</td>
                <td>Email</td>
                <td> 电话</td>
                <td>可管理组</td>
                <td><a href="user_add.php">添加用户</a></td>
              </tr>
              <?php  

for( $i=0;$i<count($user_rs);$i++ )
{ 
	echo "<tr>";
	  for( $j=0;$j<count($user_rs[0])/2;$j++ ) 
	  if ($j == 4)
	  {
		echo "<td height=21 class=Ptable>";
		if ($user_rs[$i][1] == "user")
		{
			$sql = "select GroupName from userofgroup,ipgroup where ipgroup.id = userofgroup.Gid and Uid=".$user_rs[$i][4];
			$rs = getrs($sql);
			for ( $k=0;$k<count($rs);$k++ )
			{
				echo $rs[$k][0];	
				echo "&nbsp;";
			}
		}
		else
		{
			echo "所有组";
		}
		echo "</td>";	  	
	}
	  else
	{
			echo "<td height=21 class=Ptable>";	
			echo $user_rs[$i][$j];
			echo "</td>";
	}
?>
  <td height=21 class=Ptable><a href="user_update.php?userid=<?php echo $user_rs[$i][4];?>">编辑</a>|<a href="user_delete.php?userid=<?php echo $user_rs[$i][4];?>"  onclick='javascript:return p_del()'>删除</a></td>
      <?php    }		
		?>
            </table>
          </div></td>
        </tr>
        <tr>
          <td height="10"></td>
        </tr>
      </table>
    </form></td>
  </tr>
</table>
<?php include 'boot.php'; ?>
</body>
</html>
