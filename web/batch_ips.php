<?php 
include 'head.php';
include 'include/is_monitor.php';
if (! ( $s_u_ctrl_center_enable  == "YES" and $s_u_ssh_enable == "YES"))
{
	alert_go("控制中心未启用","mon_state.php");
}
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
$gids = "";
$grps = $s_u_groups;
if (!empty($_POST['group']) and ($_POST['group'] !== "All"))
{
	$group = $_POST['group'];
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
if (isset($_POST['desc']))
{	
	$desc = $_POST['desc'];
}
else
{
	$desc = "";
}

if (!empty($_POST['searchip']))
{
	$searchip = trim($_POST['searchip']);
	$searchip_sql = " and devinfo.Ips like '%".$searchip."%' ";
}
else
{
	$searchip = "";
	$searchip_sql = "  ";
}


if (!empty($_POST['order']) and $_POST['order']!== "默认")
{
	$order_select = $_POST['order'];
	$order = $order_arr[$order_select];
	$order = "order by ".$order;
}
else
{
	$order_select = "默认";
	$order = " order by ipinfo.id ";
}
	
	$sql = "select devinfo.HostName,ipinfo.Ip, replace(ips,ipinfo.Ip,''),ipgroup.GroupName,(select MonTime from monitor where monitor.ipid=ipinfo.id order by id desc limit 1) as LastCheckTime,Enable,ipinfo.id,ipgroup.id from ipinfo  Left Join devinfo ON devinfo.Ipid=ipinfo.id Left Join ipgroup ON ipgroup.id = ipinfo.GroupId where ipinfo.ip != '0.0.0.0' and  Isalive = 'alive'  and Enable = 1 and  ClientStatus=1 and ipinfo.GroupId in $gids  $searchip_sql  $order $desc"; 
	$sql_num = "select count(ipinfo.Ip) from ipinfo  Left Join devinfo ON devinfo.Ipid=ipinfo.id Left Join ipgroup ON ipgroup.id = ipinfo.GroupId where ipinfo.ip != '0.0.0.0' and  Isalive = 'alive'  and Enable = 1 and  ClientStatus=1 and ipinfo.GroupId in $gids  $searchip_sql";
	 
$pagesize = $_SESSION['batchdo_pagesize'];
$num_all = getrs($sql_num);
$num_all = $num_all[0][0];
$pages=ceil($num_all/$pagesize);
if (!empty($_POST['page']))
{
	$page=intval($_POST['page']);
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

$selected_ips = "";
if (!empty($_POST['selected_ips']))
{
	$selected_ips = trim($_POST['selected_ips']);
	for( $i=0;$i<count($ip);$i++ )
	{
		$selected_ips = str_replace($ip[$i][1],"",$selected_ips);
	}
}


if (!empty($_POST['delid']))
{
	$delids = $_POST['delid'];
	foreach ( $delids as $delid)
	{
		if (strpos($selected_ips,$delid)===false)
			$selected_ips = $selected_ips." ".$delid." ";
	}
}

?>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=7">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $TITLE_NAME."-";?>批量管理</title>
</head>
<link href="css/style.css" rel="stylesheet" type="text/css">

<body>
<form action="" method="post" name="form1" id="form1" >
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="myhead">
  <tr>
    <td><table width="100%" height="5" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td></td>
      </tr>
    </table>
        
        <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="td_no_down">
          <tr  >
            <td height="33" background="image/tab_bg1.gif"  >&nbsp;&nbsp;&nbsp;&nbsp;<span class="title1">&nbsp;批量管理</span></td>
          </tr>
          <tr>
            <td ><table width="98%" height="30" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td>分组
                  <?php  include 'include/group_select.php';?></td>
                <td>排序
                  <select name="order" class="anniu" id="order" onChange='this.form.submit()'>
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
                <td>降序
                  <input name="desc" type="checkbox" class="radio" id="desc" onChange='this.form.submit()' value="desc"  <?php  if (!empty($_POST['desc'])) echo "checked"; ?> ></td>
                <td>&nbsp;</td>
                <td><a href="javascript:select()">
                  <input name="selected_ips" id="selected_ips" type="hidden" value="<?php echo trim($selected_ips); ?>">
                </a></td>
                <td>IP</td>
                <td><input name="searchip" type="text" id="searchip" onClick="Cleartext(this.id)"  value="<?php echo $searchip;?>" size="16"></td>
                <td><table width="100%" border="0" align="right" cellpadding="0" cellspacing="0">
                    <tr>
                      <td><div align="right"><a href="javascript:select()"> </a>
                              <?php						 
echo "共".$num_all."条记录";
echo "&nbsp;&nbsp;&nbsp;&nbsp;";

$page_url = basename(__FILE__)."?group=".$group."&order=".$order_select."&searchip=".$searchip."&desc=".$desc."&selected_ips=".trim($selected_ips)."&page="; 
//$page_url = basename(__FILE__)."?group=".$group."&order=".$order_select."&searchip=".$searchip."&desc=".$desc."&selected_ips=".trim($selected_ips)."&enable=".$enable."&status=".$status."&page="; 
echo "<a  href='#' onClick=".'"page.value='."'1';form1.submit()".'">首页</a>';
echo "&nbsp;&nbsp;";
if ($page !== 1)
{
	$ppage = $page - 1;
	echo "<a  href='#' onClick=".'"page.value='."'$ppage';form1.submit()".'">上一页</a>';
//	echo "<a href = '".$page_url.($page-1)."'>上一页</a>";
}
else
{
	echo "<a  href='#' onClick=".'"page.value='."'$pages';form1.submit()".'">上一页</a>';
}
echo "&nbsp;&nbsp;";


if ($pages	>= 10)
{
	if  (5 >= $page)
	{			
		for ($i=1;$i<=10;$i++)	
		{	
			if ($i == $page)
			{
				echo "<strong>".$i."&nbsp;&nbsp;</strong>";
			}
			else
			{
				echo "<a  href='#' onClick=".'"page.value='."'$i';form1.submit()".'">'.$i.'</a>';
//				echo "<a href = '".$page_url.$i."'>".$i."</a>&nbsp;&nbsp;";
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
						echo "<strong>".$i."&nbsp;&nbsp;</strong>";
					}
					else
					{
						echo "<a  href='#' onClick=".'"page.value='."'$i';form1.submit()".'">'.$i.'</a>&nbsp;&nbsp;';
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
			echo "<strong>".$i."&nbsp;&nbsp;</strong>";
		}
		else
		{
			echo "<a  href='#' onClick=".'"page.value='."'$i';form1.submit()".'">'.$i.'</a>&nbsp;&nbsp;';
//			echo "<a href = '".$page_url.$i."'>".$i."</a>&nbsp;&nbsp;";
		}	
	}	
}

							
//	echo "&nbsp;";
	echo "<a  href='#' onClick=".'"page.value='."'$pages';form1.submit()".'">尾页</a>';
	echo "&nbsp;";	
	if ($page < $pages )
	{	
		$npage = $page + 1;
		echo "<a  href='#' onClick=".'"page.value='."'$npage';form1.submit()".'">下一页</a>';
//		echo "<a href = '".$page_url.($page+1)."'>下一页</a>";
	}else
	{
		echo "<a  href='#' onClick=".'"page.value='."'1';form1.submit()".'">下一页</a>';
//		echo "<a href = '".$page_url."1'>下一页</a>";
	}
	echo "&nbsp;&nbsp;";
	echo "共".$pages."页";				
?>
                        &nbsp;&nbsp; </div></td>
                      <td><input name="page" type="text" id="page" onClick="Cleartext(this.id)" onKeyUp="value=value.replace(/[^\d]/g,'')" value="<?php echo $page;?>" size="3"></td>
                      <td><input name="Submit" type="submit" class="button1" value="跳转"></td>
                    </tr>
                </table></td>
                <td><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
                    <tr bgcolor="#CCCCCC">
                      <td bgcolor="#FFFFFF"><div align="right"><a href="javascript:select()">全选</a>｜<a href="javascript:fanselect()">反选</a>｜<a href="javascript:noselect()">全不选</a>&nbsp;</div></td>
                      <td width="50" bgcolor="#FFFFFF"><div align="right">
                          <input name="addip" type="submit" class="button1" id="addip"  value="确认">
                      </div></td>
                      <td width="50" bgcolor="#FFFFFF"><div align="right">
                          <input name="done" type="button" class="button1" id="done" onClick="if ( selected_ips.value==''){alert('请选择ip!');}else  {form1.action='batch_cmd.php';form1.submit()}"  value="完成">
                      </div></td>
                    </tr>
                </table></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td ><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td><?php if ($selected_ips!="") {
			echo '<table width="100%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#CCCCCC" >';
			$ips_arr =  explode("  ","已选择IP:  ".$selected_ips);
//			print_r($ips_arr);
			for ($j=0;$j<ceil(count($ips_arr)/10);$j++)
			{
				echo '<tr>';
				for ($i=0;$i<10;$i++)
				{
					echo '<td bgcolor="FFFFFF" class="Ptable">';
					if (isset($ips_arr[$j*10+$i]))
					{		
					echo "&nbsp;".$ips_arr[$j*10+$i];
					}

					echo '</td>';
				}
				echo '</tr>';
			}	
				echo '</table>';
			}
			?></td>
              </tr>
            </table></td>
          </tr>
      </table>
      </td>
  </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top"><table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td><?php if ($selected_ips!="") {
			echo '<table width="100%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#CCCCCC" >';
			$ips_arr =  explode("  ","已选择IP:  ".$selected_ips);
//			print_r($ips_arr);
			for ($j=0;$j<ceil(count($ips_arr)/10);$j++)
			{
				echo '<tr>';
				for ($i=0;$i<10;$i++)
				{
					echo '<td bgcolor="FFFFFF" class="Ptable">';
					if (isset($ips_arr[$j*10+$i]))
					{		
					echo "&nbsp;".$ips_arr[$j*10+$i];
					}
					echo '</td>';
				}
				echo '</tr>';
			}	
				echo '</table>';
			}
			?></td>
          </tr>
        </table></td>
      </tr>
    </table>
      </td>
  </tr>
</table>
<table width="100%" height="68" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top">&nbsp;</td>
  </tr>
</table>
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" class="td_clo_blue">
  <tr>
    <td valign="top"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#CCCCCC">
      <tr>
        <td><table  width=100% border=0 align=center cellpadding="0" cellspacing="1" class="tab_hover_bg_g">
          <tr bgcolor=#33FFFF>
            <td height="26" bgcolor="#33FFFF" >NUM</td>
            <td bgcolor="#33FFFF" >主机名</td>
            <td bgcolor="#33FFFF">IP</td>
            <td bgcolor="#33FFFF">IP2</td>
            <td bgcolor="#33FFFF">所属分组</td>
            <td bgcolor="#33FFFF">最后检测</td>
            <td bgcolor="#33FFFF">选择</td>
          </tr>
          <?php  
for( $i=0;$i<count($ip);$i++ )
{ 
	echo "<tr  height=20 bgcolor=#33FFFF >";
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
          <td ><span class="Ptable">
            <input name="delid[]" type="checkbox" id="delid[]" value="<?php echo $ip[$i][1];?>"   <?php if (strpos($selected_ips,$ip[$i][1])===false)  echo ""; else echo "checked";  ?>>
          </span></td>
          </tr>
          <?php
}
?>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="5"></td>
  </tr>
</table>
</form>
  <?php include 'boot.php'; ?>
</body>
</html>
