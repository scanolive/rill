<?php
include 'head.php';
if ( $s_u_level > 2 )
{
	alert_go("别调皮哦！","index.php");
}
$sql_group_nogroup = "select id from ipgroup where GroupName='NoGroup';";
$nogroup_id = getrs($sql_group_nogroup);
$sql_group = "select GroupName,Description,ipgroup.id from ipgroup";
$group_rs = getrs($sql_group);
?>

<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=7">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $TITLE_NAME."-";?>组别信息</title>
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
          <td height="33" background="image/tab_bg1.gif" class="title1">&nbsp;&nbsp;&nbsp;&nbsp;<span class="title1">&nbsp;组别管理</span></td>
        </tr>
        <tr>
          <td height="8">            </td>
        </tr>
        <tr>
          <td height="10"><div align="center">
            <table width="98%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#F0F0F0" class="Ptable">
              <tr>
                <td height="24">项目组名</td>
                <td>组描述</td>
                <td>管理员</td>
                <td>数量</td>
                <td><a href="group_add.php">添加</a></td>
              </tr>
              <?php  

for( $i=0;$i<count($group_rs);$i++ )
{ 
	echo "<tr>";
	  for( $j=0;$j<count($group_rs[0])/2+1;$j++ ) 
	  if ($j == 2)
	  {
		echo "<td height=21 class=Ptable>";
		
		$sql = "select UserName from userofgroup,users where users.id = userofgroup.Uid and UserType = 'user' and Gid=".$group_rs[$i][2];
		$rs = getrs($sql);
		for ( $k=0;$k<count($rs);$k++ )
		{	
			if ($k == 0)
			{
				echo $rs[$k][0];
			}
			else
			{
				echo "&nbsp;|&nbsp;";
				echo $rs[$k][0];
			}	
		}
		echo "</td>";	  	
	}
	  elseif ($j == 3)
	  {
		echo "<td height=21 class=Ptable>";
		
		$sql_num = "select count(id) from ipinfo where GroupId=".$group_rs[$i][2];
		$rs_num = getrs($sql_num);
		echo $rs_num[0][0]."台";
		echo "</td>";	  	
	}
	  else
	{
			echo "<td height=21 class=Ptable>";	
			echo $group_rs[$i][$j];
			echo "</td>";
	}
	if ($nogroup_id[0][0] != $group_rs[$i][2])
	{
		?>
  <td height=21><a href="group_update.php?groupid=<?php echo $group_rs[$i][2];?>">编辑</a>|<a href="group_delete.php?groupid=<?php echo $group_rs[$i][2];?>&groupname=<?php echo $group_rs[$i][0];?>" onclick='javascript:return p_del()' >删除</a></td>
      <?php 
	}
	else
	{
		echo 	"<td height=21>默认分组</td>";
	}	
	echo  "</tr>";
}
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
