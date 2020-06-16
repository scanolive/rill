<?php
include 'head.php';
include 'include/is_monitor.php';
if ( !empty($_REQUEST['delete']) and !empty($_REQUEST['delid']) and $s_u_level == 1)
{
	$delids = $_REQUEST['delid'];
	$del_num = count($delids);
	foreach ( $delids as $delid)
	{
		$sql_del_id .= $delid.",";
	}
	$sql_del_id = "(".$sql_del_id.$delids[0].")";
	$del_sql = "delete from bg_result where id in ".$sql_del_id;
	do_sql($del_sql);
	save_do($s_u_id,$s_u_level,"用户".$s_u_name." 删除".$del_num."条运行结果操作记录成功!");
	echo "<script charset='UTF-8' language='javascript'>";
	echo "alert('";
	echo "删除成功";
	echo "');";
	echo "</script>";
}
$grps = $s_u_groups;
$gids = "";
if (!empty($_REQUEST['group']) and ($_REQUEST['group'] !== "All"))
{
	$group = $_REQUEST['group'];
	if (in_array($group,$s_u_gids))
	{
		$gids = "(".$group.")";
	}
	else
	{
		$group = "All";
		for ($i=0;$i<count($grps);$i++)
		{
			$gids .= $grps[$i][1].",";
		}	
		$gids = "(".$gids.$grps[0][1].")";	
	}	
}
else
{
	$group = "All";
	for ($i=0;$i<count($grps);$i++)
	{
		$gids .= $grps[$i][1].",";
	}	
	$gids = "(".$gids.$grps[0][1].")";
}

if (!empty($_REQUEST['markid'])) 
{
	$markid = $_REQUEST['markid'];
	$markid_sql = "and  MarkId=".$markid;
}
else
{
	$markid = "";
	$markid_sql = "";
}

if (!empty($_REQUEST['ip'])) 
{
	$ip = $_REQUEST['ip'];
	$ip_sql = "and  bg_result.Ip=".$ip;
}
else
{
	$ip = "";
	$ip_sql = "";
}

$ip_sql = "select bg_result.Ip from bg_result,ipinfo where ipinfo.ip=bg_result.ip and GroupId in $gids GROUP BY bg_result.Ip";
$ip_rs = getrs($ip_sql);

$sql_bg_result = "select bg_result.Ip,CmdStr,replace(OutStr,'R!I@L#L','<br>'),replace(ErrStr,'R!I@L#L','<br>'),StartTime,EndTime,bg_result.id from bg_result,ipinfo where ipinfo.ip=bg_result.ip and GroupId in $gids $markid_sql order by bg_result.id desc";
$sql_num = "select  count(bg_result.Ip) from bg_result,ipinfo where  ipinfo.ip=bg_result.ip and GroupId in $gids $markid_sql";
$bg_result_rs = getrs($sql_bg_result);

$pagesize = $_SESSION['bg_result_pagesize'];
$num_all = getrs($sql_num);
$num_all = $num_all[0][0];
$pages=ceil($num_all/$pagesize);
if (!empty($_GET['page']))
{
	$page=intval($_GET['page']);
	if ( $page > $pages)
	{
		$page = $pages;
	}
	if ( $page == 0)
	{
		$page = 1;
	}	
}
else
{
	$page=1; 
} 
$offset=$pagesize*($page - 1);
$sql_bg_result = $sql_bg_result." limit ".$offset.",".$pagesize;
$bg_result_rs = getrs($sql_bg_result);
?>

<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=7">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $TITLE_NAME."-";?>运行结果</title>
<link href="css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="myhead">
  <tr>
	<td>
	<table width="100%" height="5" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td></td>
      </tr>
    </table>
        <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" class="td_no_down">
          <tr  >
            <td height="33" background="image/tab_bg1.gif"  >&nbsp;&nbsp;&nbsp;&nbsp;<span class="title1">&nbsp;运行结果</span></td>
          </tr>
          <tr>
            <td ><form action="" method="get" name="form1" >
              <table width="98%" height="32" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td>&nbsp;</td>
                  <td><div align="left">分组：&nbsp;
                        <?php  include 'include/group_select.php';?>
                  </div></td>
                  <td><div align="left"><?php	if ($s_u_level < 3){
	echo "&nbsp;IP:&nbsp;";
	echo "<select name='ip' class='anniu' id='ip' onChange='this.form.submit()'>";
	echo "<option value=";
	echo  "All";
	echo  ">";
	echo  "All";; 
	echo "</option>";		
for( $i=0;$i<count($ip_rs);$i++ ) 
 {

 	if ($ip_rs[$i][0] == $ip )
	{
		echo "<option selected value=";
		echo  $ip_rs[$i][0];
		echo  ">";
		echo $ip_rs[$i][0]; 
		echo "</option>";
	}
	else
	{
		echo "<option  value=";
		echo  $ip_rs[$i][0];
		echo  ">";
		echo $ip_rs[$i][0]; 
		echo "</option>";		
	}
 }
echo "</select>";	
}?>
                  </div></td>
                  <td></td>
                  <td><div align="right">
                      <?php						 
echo "共".$num_all."条记录";
echo "&nbsp;&nbsp;&nbsp;&nbsp;";
$bg_result_url = "bg_result.php?group=".$group."&ip=".$ip."&markid=".$markid."&page=";

echo "<a href = '".$bg_result_url."1'>首页</a>";
echo "&nbsp;";
if ($page !== 1)
{
	echo "<a href = '".$bg_result_url.($page-1)."'>上一页</a>";
}
else
{
	echo "<a href = '".$bg_result_url.$pages."'>上一页</a>";
}
echo "&nbsp;";


if ($pages	>= 10)
{
	if  (5 >= $page)
	{			
		for ($i=1;$i<=10;$i++)	
		{	
			if ($i == $page)
			{
				echo "<strong>".$i."&nbsp;</strong>";
			}
			else
			{
				echo "<a href = '".$bg_result_url.$i."'>".$i."</a>&nbsp;";
			}
		}
				
	}
	else if  ($page <= $pages)
		{
			for ($i=$page-5;$i<$page+5;$i++)
			{
				if  ( $pages >= $i)
				{
					if ($i == $page)
					{
						echo "<strong>".$i."&nbsp;</strong>";
					}
					else
					{
						echo "<a href = '".$bg_result_url.$i."'>".$i."</a>&nbsp;";
					}
				}
			}		
		}	
}
else
{
	for ($i=1;$i<=$pages;$i++)	
	{
		if ($i == $page)
		{
			echo "<strong>".$i."&nbsp;</strong>";
		}
		else
		{
			echo "<a href = '".$bg_result_url.$i."'>".$i."</a>&nbsp;";
		}	
	}	
}

	echo "&nbsp;";
	if ($page < $pages )
	{	
		echo "<a href = '".$bg_result_url.($page+1)."'>下一页</a>";
	}else
	{
		echo "<a href = '".$bg_result_url."1'>下一页</a>";
	}

								
	echo "&nbsp;";
	echo "<a href = '".$bg_result_url.$pages."'>尾页</a>";
	echo "&nbsp;";	
	echo "共".$pages."页";				
?>
                      <input name="page" type="text" id="page" size="3" onClick="Cleartext(this.id)" onKeyUp="value=value.replace(/[^\d]/g,'')" value="<?php echo $page;?>">
                  </div></td>
                  <td width="50"><div align="right">
                      <input name="Submit" type="submit" class="button1" value="跳转">
                  </div></td>
                </tr>
              </table>
            </form></td>
          </tr>
      </table></td>
  </tr>
</table>
<table width="90%" height="70" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top">&nbsp;</td>
  </tr>
</table>
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" class="td_clo_blue">
  <tr>
    <td height="10"><form name="form2" method="post" action="bg_result_delete.php">
      <div align="center">
        <table width="98%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#66CCFF" class="Ptable">
          <tr>
            <?php	  if ($s_u_level == 1){?>
            <td width="28" height="24">选择</td>
            <?php	 }?>
            <td>NUM</td>
            <td height="24">IP</td>
            <td>命令</td>
            <td>结果</td>
            <td>错误</td>
            <td>开始时间<a href="bg_result_add.php"></a></td>
            <td>结束时间</td>
          </tr>
          <?php  
for( $i=0;$i<count($bg_result_rs);$i++ )
{ 
		echo "<tr>";
	 if ($s_u_level == 1)	{?>
          <td height=18 class=Ptable><input name="delid[]" type="checkbox" class="radio" id="delid[]" value="<?php echo $bg_result_rs[$i][6];?>"></td>
        <?php }
		echo "<td height=18 class=Ptable>";	
	echo $i+1+$pagesize*($page-1);
	echo "</td>";		  
	for( $j=0;$j<count($bg_result_rs[0])/2-1;$j++ )
	{

			echo "<td height=21 class=Ptable>";	
			echo $bg_result_rs[$i][$j];
			echo "</td>";

	}
	echo "</tr>";
}
?>
          </table>
      </div>
        <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td><?php	  if ($s_u_level == 1){?>
                <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
                  <tr>
					<tr><td height=5></td></tr>
                    <td width="60"><div align="left">
                        <input name="delete" type="submit" class="button1" id="delete" value="删除"  onClick="javascript:delid.value=abc_delid.value;return p_del()">
                        <input name="url" type="hidden" id="url" value="<?php echo $bg_result_url.$page ?>">
                    </div></td>
                    <td><div align="left"><a href="javascript:select()">全选</a>｜<a href="javascript:fanselect()">反选</a>｜<a href="javascript:noselect()">全不选</a>&nbsp;</div></td>
                  </tr>
                </table>
              <?php	 }?></td>
          </tr>
        </table>
    </form>
    </td>
  </tr>
  <tr>
    <td height="10"></td>
  </tr>
</table>
<?php include 'boot.php'; ?>
</body>
</html>
