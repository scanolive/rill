<?php 
include 'head.php';
if ( $s_u_level < 3 )
{
	$group_sql = "select GroupName,id from ipgroup";	
}
else
{
	$group_sql = "select GroupName,ipgroup.id from ipgroup,userofgroup where userofgroup.Gid = ipgroup.id and userofgroup.Uid=$s_u_id";
}
if (!empty($_REQUEST['SN']))
{
	$SN= trim($_REQUEST['SN']);
	$SN_sql = " and devinfo.SN like '%".$SN."%' ";
}
else
{
	$SN = "";
	$SN_sql = "  ";
}
if (!empty($_REQUEST['vendor']) and ($_REQUEST['vendor'] !== "All") )
{
	$vendor = $_REQUEST['vendor'];
	$vendor_sql = "and  Vendor ='".$vendor."'";
}
else
{
	$vendor = "All";
	$vendor_sql = "";
}
if (!empty($_REQUEST['model']) and ($_REQUEST['model'] !== "All") )
{
	$model = $_REQUEST['model'];
	$model_sql = "and  Model ='$model'";
}
else
{
	$model = "All";
	$model_sql = "";
}
if (!empty($_REQUEST['enable']) and ($_REQUEST['enable'] !== "All") )
{
	$enable = trim($_REQUEST['enable']);
	if ($enable == "enable" )
	$enable_sql = "and  Enable = 1 ";
	else
	$enable_sql = "and  Enable = 0 ";	
}
else
{
	$enable = "All";
	$enable_sql = " ";
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

if (!empty($_REQUEST['searchip']))
{
	$searchip = trim($_REQUEST['searchip']);
	$searchip_sql = " and ipinfo.Ip like '%".$searchip."%' ";
}
else
{
	$searchip = "";
	$searchip_sql = "  ";
}

$gids = "";
$grps = getrs($group_sql);
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


	$vendor_rs_sql = "select  distinct Vendor from devinfo";
	$model_rs_sql = "select  distinct Model from devinfo";
	$IDC_rs_sql = "select  distinct IDC from devinfo";
	$vendor_rs = getrs($vendor_rs_sql);
	$model_rs = getrs($model_rs_sql);
	$IDC_rs = getrs($IDC_rs_sql);
	$sql = "select SN,HostName,DevName,ipgroup.GroupName,ipinfo.ip,replace(ips,ipinfo.ip,''),concat(Vendor,'-',Model),Disk,Memory,concat_ws('',substring(Cpu_Model,23,10),'|',Cpu_Num,'/',devinfo.Cpu_Pro),concat(Idc,'/',Place),Enable,devinfo.id,ipgroup.id from devinfo Left Join ipinfo ON ipinfo.id=devinfo.Ipid Left Join ipgroup ON ipgroup.id = ipinfo.GroupId  where ipinfo.GroupId in $gids $SN_sql $model_sql $vendor_sql $IDC_sql $searchip_sql $enable_sql";
	$sql_num = "select count(SN) from devinfo Left Join ipinfo ON ipinfo.id=devinfo.Ipid Left Join ipgroup ON ipgroup.id = ipinfo.GroupId  where ipinfo.GroupId in $gids $SN_sql $model_sql $vendor_sql $IDC_sql $searchip_sql $enable_sql";
$pagesize = $_SESSION['devinfo_pagesize'];
$num_all = getrs($sql_num);
$num_all = $num_all[0][0];
$pages=intval($num_all/$pagesize)+1;
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
$dev = getrs($sql);


?>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=7">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="css/style.css" rel="stylesheet" type="text/css">
<title><?php echo $TITLE_NAME."-";?>设备信息</title></head>
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
            <td height="33" background="image/tab_bg1.gif"  >&nbsp;&nbsp;&nbsp;&nbsp;<span class="title1">&nbsp;设备信息</span></td>
          </tr>
          <tr> 
            <td ><form action="" method="get" name="form1" >
              <table width="98%" height="32" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
              <tr>
                <td><div align="left">分组
                  <?php  include 'include/group_select.php';?>
                </div></td>
                <td>&nbsp;启用
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
                <td>&nbsp;厂商
                  <select name="vendor" id="vendor" onChange="this.form.submit()">
                      <option value="<?php echo $vendor;?>" selected>
                      <?php  echo $vendor;?>
                      </option>
                      <?php if ($vendor !== "All"){ ?>
                      <option value="All" >All</option>
                      <?php }for ($i=0;$i<count($vendor_rs);$i++)
				{	
					if ($vendor_rs[$i][0] !== $vendor)
					{
						echo "<option value='".$vendor_rs[$i][0]."'>".$vendor_rs[$i][0]."</option>";
					}
				}
				?>
                  </select></td>
                <td>&nbsp;型号
                  <select name="model" id="model" onChange="this.form.submit()">
                      <option value="<?php echo $model;?>" selected>
                      <?php  echo $model;?>
                      </option>
                      <?php if ($model !== "All"){ ?>
                      <option value="All" >All</option>
                      <?php }for ($i=0;$i<count($model_rs);$i++)
				{	
					if ($model_rs[$i][0] !== $model)
					{
						echo "<option value='".$model_rs[$i][0]."'>".$model_rs[$i][0]."</option>";
					}
				}
				?>
                  </select></td>
                <td>&nbsp;IDC
                  <select name="IDC" id="IDC" onChange="this.form.submit()">
                      <option value="<?php echo $IDC;?>" selected>
                      <?php  echo $IDC;?>
                      </option>
                      <?php if ($IDC !== "All"){ ?>
                      <option value="All" >All</option>
                      <?php }for ($i=0;$i<count($IDC_rs);$i++)
				{	
					if ($IDC_rs[$i][0] !== $IDC and $IDC_rs[$i][0] !=  "")
					{
						echo "<option value='".$IDC_rs[$i][0]."'>".$IDC_rs[$i][0]."</option>";
					}
				}
				?>
                  </select></td>
                <td>&nbsp;IP
                  <input name="searchip" type="text" id="searchip" onClick="Cleartext(this.id)"  value="<?php echo $searchip;?>" size="15">
                </td>
                <td>&nbsp;SN
                  <input name="SN" type="text" id="SN" value="<?php echo $SN;?>" size="14"></td>
                <td><div align="right">
                    <?php
echo "共".$num_all."条";
echo "&nbsp;&nbsp;";
$page_url = "devinfo.php?group=".$group."&enable=".$enable."&SN=".$SN."&IDC=".$IDC."&model=".$model."&vendor=".$vendor."&page=";
$url_now = $page_url.$page;
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
          <td valign="top"><div align="center">
            <table  width=99% border=0 cellpadding="0" cellspacing="1" bgcolor="#999999" class="tab_hover_bg_w">
              <tr>
                <td height="26" >NUM</td>
                <td >SN</td>
                <td>主机名</td>
                <td>设备备注</td>
                <td>分组</td>
                <td>IP</td>
                <td>IP2</td>
                <td>型号</td>
                <td>硬盘</td>
                <td>内存</td>
                <td>CPU</td>
                <td>IDC位置</td>
                <td>状态</td>
<?php
if ( $s_u_level < 4)
{		
		echo              ' <td>操作</td>';
}		
 echo            ' </tr>';
for( $i=0;$i<count($dev);$i++ )
{ 
	echo "<tr height=20 class=Ptable>";
	echo "<td class=Ptable>";	
	echo $i+1;
	echo "</td>";
	if ($dev[$i][11] == 1)
	{
		$dev[$i][11] = "启用";
	}
	else
	{
		$dev[$i][11] = "禁用";
	}		
	for( $j=0;$j<count($dev[0])/2-2;$j++ )
	{ 
		echo "<td class=Ptable>";	
		echo $dev[$i][$j];
		echo "</td>";	
	}
	if ( $s_u_level < 4)
	{		
		echo '<td ><a href="dev_update.php?ip='.$dev[$i][4];
		echo '&group='.$dev[$i][13];
		echo '&url='.str_replace("&","!@!", $url_now);
		echo '">编辑</a></td>';
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
    </td>
  </tr>
</table>
<?php include 'boot.php'; ?>
</body>
</html>
