<?php 
include 'head.php';
$order_arr = array(
			'默认'		   => 'ipinfo.id',
			'主机名'        => 'HostName',
			'Ip'           => 'Ip',
			'分组'         => 'GroupName',
			'网络'         => 'Isalive',
			'警报'         => 'AlarmType',
			'最后检测'      => 'LastCheckTime',
			'错误端口'      => 'ErrPort',
			'运行时间'      => 'Uptime',
			'客户端'        => 'ClientStatus'
		);

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

if (!empty($_REQUEST['isalive']) and ($_REQUEST['isalive'] !== "All") )
{
	$isalive = $_REQUEST['isalive'];
	$alive_sql = "and  Isalive ='".$isalive."'";
}
else
{
	$isalive = "";
	$alive_sql = "";
}
if (!empty($_REQUEST['enable']) and ($_REQUEST['enable'] !== "enable") )
{
	$enable = trim($_REQUEST['enable']);
	if ($enable == "All" )
	$enable_sql = " ";
	else
	$enable_sql = "and  Enable = 0 ";	
}
else
{
	$enable = "enable";
	$enable_sql = "and  Enable = 1 ";
}
if (empty($_REQUEST['status']) or ($_REQUEST['status'] == "All") )
{
	$status= "All";
	$status_sql = " ";
}
else if ($_REQUEST['status'] == "OK")
{
	$status = "OK";
	$status_sql = " and ClientStatus=1 and  (select count(id) from alarms where alarms.ipid=ipinfo.id and IsBeOk=0)=0";
}
else if ($_REQUEST['status'] == "Err")
{
	$status = "Err";
	$status_sql = " and  (ClientStatus=0 or (select count(id) from alarms where alarms.ipid=ipinfo.id and IsBeOk=0)>0)";	
}

if (!empty($_REQUEST['desc']))
{
	$desc = $_REQUEST['desc'];
}
else
{
	$desc = "";
}

if (!empty($_REQUEST['searchip']))
{
	$searchip = trim($_REQUEST['searchip']);
	$searchip_sql = " and devinfo.Ips like '%".$searchip."%' ";
}
else
{
	$searchip = "";
	$searchip_sql = "  ";
}
if (!empty($_REQUEST['IDC']) and ($_REQUEST['IDC'] !== "All") )
{
	$IDC = $_REQUEST['IDC'];
	$IDC_sql = "and  IDC ='$IDC'";
}
else
{
	$IDC = "All";
	$IDC_sql = "";
}



if (!empty($_REQUEST['order']) and $_REQUEST['order']!== "默认")
{
	$order_select = $_REQUEST['order'];
	$order = $order_arr[$order_select];
	$order = "order by ".$order;
}
else
{
	$order_select = "默认";
	$order = " order by ipinfo.id ";
}
	$IDC_rs_sql = "select  distinct IDC from devinfo";
	$IDC_rs = getrs($IDC_rs_sql);
		
$sql = "select devinfo.HostName,ipinfo.Ip, replace(ips,ipinfo.Ip,''),DevName,ipgroup.GroupName,concat(Idc,'/',Place),ipinfo.Isalive,concat_ws(' ',(select group_concat(alarms.Type) from alarms where alarms.ipid=ipinfo.id and IsBeOk=0 and Type!='port' and Type!='client' and Type!='isalive' and Type!='monweb'),concat('',(select  group_concat(concat('EP_',ports.port)) from ports where ipid = ipinfo.id and IsMon=1 and Status=0))),ClientStatus,Uptime,(select MonTime from monitor where monitor.ipid=ipinfo.id order by id desc limit 1) as LastCheckTime,Enable,ipinfo.id,ipgroup.id              from ipinfo  Left Join devinfo ON devinfo.Ipid=ipinfo.id              Left Join ipgroup ON ipgroup.id = ipinfo.GroupId   where ipinfo.ip != '0.0.0.0' $alive_sql and ipinfo.GroupId in $gids  $searchip_sql $enable_sql $status_sql $IDC_sql $order $desc"; 

$sql_num = "select count(ipinfo.Ip) from ipinfo  left Join devinfo ON devinfo.Ipid=ipinfo.id Left Join ipgroup ON ipgroup.id = ipinfo.GroupId where ipinfo.ip != '0.0.0.0' $alive_sql and ipinfo.GroupId in $gids  $searchip_sql $enable_sql $status_sql  $IDC_sql"; 
$pagesize = $_SESSION['monstate_pagesize'];
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
$sql = $sql." limit ".$offset.",".$pagesize;
$ip = getrs($sql);

?>
<html>
<head>
<meta http-equiv="refresh" content="120">
<meta http-equiv="X-UA-Compatible" content="IE=7">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $TITLE_NAME."-";?>状态监控</title></head>
<link href="css/style.css" rel="stylesheet" type="text/css">

<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="myhead">
  <tr>
    <td><table width="100%" height="5" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td></td>
      </tr>
    </table>
    <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="td_no_down">
      <tr  >
        <td height="33" background="image/tab_bg1.gif"  >&nbsp;&nbsp;&nbsp;&nbsp;<span class="title1">&nbsp;状态监控</span></td>
      </tr>
      <tr>
        <td ><form action="" method="get" name="form1" >
          <table width="98%" height="32" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
          <tr>
            <td>启用
              <select name="enable" id="enable" onChange='this.form.submit()'>
                      <?php  if ( $enable == "All" )
					  { 
					  		echo "<option value='enable'>启用</option>";
							echo "<option value='All' selected >All</option>";
							echo "<option value='disable'>禁用</option>";							
					  }
					  elseif ( $enable == "enable" )
					  {
					  		echo "<option value='enable' selected>启用</option>";
							echo "<option value='disable'>禁用</option>";	
							echo "<option value='All'>All</option>";	
					  }
					  else
					  {
					  		echo "<option value='disable' selected>禁用</option>";	
							echo "<option value='enable'>启用</option>";	
							echo "<option value='All'>All</option>";
					  }	
					  ?>
                  </select>
            </td>
            <td>分组
              <?php  include 'include/group_select.php';?></td>
            <td>&nbsp;网络
              <select name="isalive" id="isalive" onChange='this.form.submit()'>
                      <?php  if (empty($isalive))
					  { 
					  		echo "<option value='All' selected>All</option>";							
					  }
					  else
					  {
					  		echo "<option value='$isalive' selected>$isalive</option>";		
					  }
					  ?>
                      <option value="down">down</option>
                      <option value="alive">alive</option>
                      <option value="All">All</option>
                  </select>
            </td>
            <td>状态
              <select name="status" id="status" onChange='this.form.submit()'>
                      <?php  if ( $status == "All" )
					  { 
					  		echo "<option value='OK'>正常</option>";
							echo "<option value='All' selected >All</option>";
							echo "<option value='Err'>异常</option>";							
					  }
					  elseif ( $status == "OK" )
					  {
					  		echo "<option value='OK' selected>正常</option>";
							echo "<option value='Err'>异常</option>";	
							echo "<option value='All'>All</option>";	
					  }
					  else
					  {
					  		echo "<option value='Err' selected>异常</option>";	
							echo "<option value='OK'>正常</option>";	
							echo "<option value='All'>All</option>";
					  }	
					  ?>
                  </select></td>
            <td>&nbsp;</td>
            <td>IDC
              <select name="IDC" id="IDC" onChange="this.form.submit()">
                      <option value="<?php echo $IDC;?>" selected>
                      <?php  echo $IDC;?>
                      </option>
                      <?php if ($IDC !== "All"){ ?>
                      <option value="All" >All</option>
                      <?php }for ($i=0;$i<count($IDC_rs);$i++)
				{	
					if ($IDC_rs[$i][0] !== $IDC and $IDC_rs[$i][0]!="")
					{
						echo "<option value='".$IDC_rs[$i][0]."'>".$IDC_rs[$i][0]."</option>";
					}
				}
				?>
                  </select></td>
            <td>排序
              <select name="order" id="order" onChange='this.form.submit()'>
                      <option value="<?php echo $order_select;?>" selected> <?php echo $order_select;?></option>
                      <option value="Ip">Ip</option>
                      <option value="默认">默认</option>
                      <option value="网络">网络</option>
                      <option value="最后检测">最后检测</option>
                      <option value="分组">分组</option>
                      <option value="主机名">主机名</option>
                      <option value="警报">警报</option>
                      <option value="客户端">客户端</option>
                      <option value="运行时间">运行时间</option>
                      <option value="错误端口">错误端口</option>
                  </select></td>
            <td width="24">降序</td>
            <td width="30"><input name="desc" type="checkbox" class="anniu" id="desc" onChange='this.form.submit()' value="desc"  <?php  if (!empty($_REQUEST['desc'])) echo "checked"; ?> ></td>
            <td>&nbsp;</td>
            <td><div align="right">IP
              <input name="searchip" type="text" id="searchip" onClick="Cleartext(this.id)"  value="<?php echo $searchip;?>" size="15">
            </div></td>
            <td bgcolor="#FFFFFF"><div align="right">
              <?php						 
echo "共".$num_all."条";
echo "&nbsp;";
$page_url = "mon_state.php?group=".$group."&order=".$order_select."&searchip=".$searchip."&desc=".$desc."&isalive=".$isalive."&enable=".$enable."&status=".$status."&IDC=".$IDC."&page=";
$url_now = $page_url.$page;
$url_now = str_replace("&","!@!", $url_now);
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

//	echo "&nbsp;";
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
              <input name="page" type="text" id="page" onClick="Cleartext(this.id)" onKeyUp="value=value.replace(/[^\d]/g,'')" value="<?php echo $page;?>" size="2" maxlength="3">
            </div></td>
            <td width="50" bgcolor="#FFFFFF"><div align="right">
              <input name="Submit" type="submit" class="button1" value="跳转">
            </div></td>
          </tr>
        </table>
          </form></td>
      </tr>
    </table></td>
  </tr>
</table>
<table width="100%" height="70" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top">
      <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="td_clo_blue">
        <tr>
          <td valign="top"><table width="99%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#CCCCCC">
              <tr>
                <td><table  width=100% border=0 align=center cellpadding="0" cellspacing="1" class="tab_hover_bg_g">
                  <tr bgcolor=#33FFFF>
                    <td height="26" bgcolor="#33FFFF" >NUM</td>
                    <td bgcolor="#33FFFF" >主机名</td>
                    <td width="100" bgcolor="#33FFFF">IP</td>
                    <td bgcolor="#33FFFF">IP2</td>
                    <td bgcolor="#33FFFF">备注</td>
                    <td bgcolor="#33FFFF">分组</td>
                    <td bgcolor="#33FFFF">IDC位置</td>
                    <td bgcolor="#33FFFF">网络</td>
                    <td bgcolor="#33FFFF">警报</td>
                    <td bgcolor="#33FFFF">客户端</td>
                    <td bgcolor="#33FFFF">Uptime</td>
                    <td bgcolor="#33FFFF">最后检测
					<td bgcolor="#33FFFF">控制中心
					<?php 	
  				  if ($s_u_level < 3)
				  { 
					echo   '| <a href="mon_config.php?ip=0.0.0.0">配置默认阀值</a>';
                  }
				   else
				   {
				   		echo '| <a href="mon_config.php?ip=0.0.0.0">查看默认阀值</a></td>';
                   }
				if ($s_u_level < 4)
				{
                    echo '<td bgcolor="#33FFFF">操作</td>';
				}
                echo  '</tr>';
for( $i=0;$i<count($ip);$i++ )
{ 
	if ($ip[$i][6] !== "alive")
	{
		echo "<tr  height=20 bgcolor=#FF0000 >";
	}
	else if ( $ip[$i][8] == 0 )
	{
		echo "<tr  height=20 bgcolor=#FFCC00 >";
		$ip[$i][8] = "NO";
	}

	else if ( $ip[$i][7] != "" and $ip[$i][7] != "NULL")
	{
		echo "<tr  height=20 bgcolor=#FFFF00  class='tab_hover_bg_w' >";
		$ip[$i][8] = "OK";
	}

	else 
	{
		$ip[$i][8] = "OK";
		$ip[$i][7] = "NONE";
		echo "<tr  height=20 bgcolor=#33FFFF >";
	}
		echo "<td>";	
		echo $i+1+$pagesize*($page-1);
		echo "</td>";
	for( $j=0;$j<count($ip[0])/2-3;$j++ )
	{
			echo "<td >";	
			echo $ip[$i][$j];
			echo "</td>";		
	}	


?>
  <td >
<?php 
if ($ip[$i][8] == "OK")
{
if ($_SESSION['ctrl_center_enable'] == "YES" and $_SESSION['ssh_enable'] == "YES" and $s_u_level < 4)  
	{ 
		echo '<a href="ctrl_center.php?ip='.$ip[$i][1].'&group='.$ip[$i][13].'" >控制</a>｜';
	}
	echo '<a href="nowinfo.php?ip='.$ip[$i][1].'&group='.$ip[$i][13].'" >即时</a>｜';
}
else
{
	if ($_SESSION['ctrl_center_enable'] == "YES" and $_SESSION['ssh_enable'] == "YES"  and $s_u_level < 4) 
	{ 
		echo '控制｜';
	}
	echo "即时｜";
}
echo '<a href="graph.php?ip='.$ip[$i][1].'&ipid='.$ip[$i][12].'&group='.$ip[$i][13].'" >图</a>｜';
if ($s_u_level < 4)
{		
	echo '<a href="mon_config.php?ip='.$ip[$i][1].'&group='.$ip[$i][13].'" >配置</a>｜';
}
echo '<a href="dayinfo.php?ip='.$ip[$i][1].'&group='.$ip[$i][13].'" >历史</a>';
echo "</td>";
if ($s_u_level < 4)
{		
	echo "<td>";
	if ($ip[$i][11] == 1)
		{
		echo "<a href='mon_state_disable.php?id=".$ip[$i][12];
		echo '&url='.str_replace("&","!@!", $url_now);
		echo  "'>禁用</a>";
		}
	else
	{
		echo "<a href='mon_state_enable.php?id=".$ip[$i][12];
		echo '&url='.str_replace("&","!@!", $url_now);
		echo "'>启用</a>";
		if ($s_u_level < 3)
		{
	  		echo "|";
	  		echo "<a href='mon_state_delete.php?id=".$ip[$i][12]."&url_now=".$url_now."' onclick='javascript:return p_del()'>删除</a>";
		}
	}
	echo '|<a href="dev_update.php?ip='.$ip[$i][1];
	echo '&group='.$ip[$i][13];
	echo '&url='.str_replace("&","!@!", $url_now);
	echo '">编辑</a>';
	  echo     "</td>";
}
}
?>
	</tr>                </table>                  </td>

              </tr>
            </table>            </td>
        </tr>
        <tr>
          <td height="10"></td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<?php include 'boot.php'; ?>
</body>
</html>
