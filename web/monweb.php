<?php
include 'head.php';
$gids = "";
$grps = $s_u_groups;
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
$isok = "";
if (!empty($_REQUEST['isok']) and ($_REQUEST['isok'] !== "All") )
{
	$isok = $_REQUEST['isok'];
	if ($isok == 'Ok')
	{
		$isok_sql = "and  RstCode = 200";
	}
	else
	{
		$isok_sql = "and  RstCode != 200";
	}
}
else
{
	$isok_sql = "";
}


$sql_monweb = "select MonName,GroupName,MonUrl,replace(RstCode,'600','TimeOut'),monweb.id,Enable,Gid from monweb Left Join ipgroup ON monweb.Gid = ipgroup.id where Gid in $gids $isok_sql";
$sql_num = "select count(MonName) from monweb Left Join ipgroup ON monweb.Gid = ipgroup.id where Gid in $gids $isok_sql";
$monweb_rs = getrs($sql_monweb);

$pagesize = $_SESSION['monweb_pagesize'];
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
$sql_monweb = $sql_monweb." limit ".$offset.",".$pagesize;
$monweb_rs = getrs($sql_monweb);
?>

<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=7">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $TITLE_NAME."-";?>页面监控</title>
<link href="css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="myhead">
  <tr>
    <td><table width="100%" height="5" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td></td>
      </tr>
    </table>
        <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" class="td_no_down">
          <tr  >
            <td height="33" background="image/tab_bg1.gif"  >&nbsp;&nbsp;&nbsp;&nbsp;<span class="title1">&nbsp;页面监控</span></td>
          </tr>
          <tr>
            <td ><form action="" method="get" name="form1" >
              <table width="98%" height="32" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="180"><div align="left">分组：&nbsp;
                          <?php  include 'include/group_select.php';?>
                  </div></td>
                  <td width="120"><div align="left"> 状态：&nbsp;
                          <select name="isok" id="isok" onChange='this.form.submit()'>
                            <?php  if (empty($isok))
					  { 
					  		echo "<option value='All' selected>All</option>";
							
					  }
					  else
					  {
					  		echo "<option value='$isok' selected>$isok</option>";		
					  }
					  ?>
                            <option value="Err">Err</option>
                            <option value="Ok">Ok</option>
                            <option value="All">All</option>
                          </select>
                  </div></td>
                  <td></td>
                  <td><div align="right">
                      <?php						 
echo "共".$num_all."条记录";
echo "&nbsp;&nbsp;&nbsp;&nbsp;";
$page_url = "monweb.php?group=".$group."&isok=".$isok."&page=";

echo "<a href = '".$page_url."1'>首页</a>";
echo "&nbsp;";
if ($page !== 1)
{
	echo "<a href = '".$page_url.($page-1)."'>上一页</a>";
}
else
{
	echo "<a href = '".$page_url.$pages."'>上一页</a>";
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
				echo "<a href = '".$page_url.$i."'>".$i."</a>&nbsp;";
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
						echo "<a href = '".$page_url.$i."'>".$i."</a>&nbsp;";
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
			echo "<a href = '".$page_url.$i."'>".$i."</a>&nbsp;";
		}	
	}	
}

	echo "&nbsp;";
	if ($page < $pages )
	{	
		echo "<a href = '".$page_url.($page+1)."'>下一页</a>";
	}else
	{
		echo "<a href = '".$page_url."1'>下一页</a>";
	}

								
	echo "&nbsp;";
	echo "<a href = '".$page_url.$pages."'>尾页</a>";
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
<table width="98%" height="70" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top">&nbsp;</td>
  </tr>
</table>
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" class="td_clo_blue">
  <tr>
    <td height="10"><div align="center">
      <table width="98%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#66CCFF" class="Ptable">
        <tr>
          <td height="24">监控名称</td>
          <td>所属组</td>
          <td>监控页面</td>
          <td>状态</td>
 <?php
if ($s_u_level < 4)
{
 	echo      '<td>操作&nbsp;|&nbsp;<a href="monweb_add.php"> 添加</a></td>';
}
   echo    ' </tr>';
for( $i=0;$i<count($monweb_rs);$i++ )
{ 
		echo "<tr>";

	for( $j=0;$j<count($monweb_rs[0])/2-3;$j++ )
	{
		if ($j == 2 )
		{
			echo "<td height=21 class=Ptable>";	
			echo "<a href='".$monweb_rs[$i][$j]."' target='_blank' >";
			echo $monweb_rs[$i][$j];
			echo "</a>";
			echo "</td>";
		}
		else
		{
			echo "<td height=21 class=Ptable>";	
			echo $monweb_rs[$i][$j];
			echo "</td>";
		}		
	}
	if ($s_u_level < 4)
	{
			echo "<td height=21 class=Ptable>";	
			echo "<a href='monweb_update.php?id=".$monweb_rs[$i][4]."&group=".$monweb_rs[$i][6]."'>编辑</a>";
			echo "&nbsp;|&nbsp;";
			if ( $monweb_rs[$i][5] == 1)
				echo "<a href='javascript:setTimeout(window.location.reload(),200);' onclick='javascript:if (p_del()){ajax_do(".'"monweb_disable.php?id='.$monweb_rs[$i][4].'"'.")}' >禁用</a>";			
//				echo "<a href='monweb_disable.php?id=".$monweb_rs[$i][4]."'>禁用</a>";
			else
//				echo "<a href='monweb_enable.php?id=".$monweb_rs[$i][4]."'>启用</a>";
				echo "<a href='javascript:setTimeout(window.location.reload(),200);' onclick='javascript:if (p_del()){ajax_do(".'"monweb_enable.php?id='.$monweb_rs[$i][4].'"'.")}' >启用</a>";
			echo "&nbsp;|&nbsp;";
			echo "<a href='monweb_delete.php?id=".$monweb_rs[$i][4]."'  onclick='javascript:return p_del()'>删除</a>";
			echo "</td>";
	}
	echo "</tr>";
}
?>
      </table>
    </div></td>
  </tr>
  <tr>
    <td height="10"></td>
  </tr>
</table>
<?php include 'boot.php'; ?>
</body>
</html>
