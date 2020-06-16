<?php 
include 'head.php';
include 'include/is_monitor.php';
$def_level_sql = "select DiskLevel,LoadLevel,LoginLevel,NetworkLevel,ProcessLevel,ConnectLevel from ipinfo where Ip='0.0.0.0';";
$def_level_rs = getrs($def_level_sql);
if (!empty($_GET['ip']))
{
	$ip = ($_GET['ip']);
	if (isset($_GET['group']))
	{		
		$group = ($_GET['group']);
	}
	if ( $s_u_level or $ip == "0.0.0.0")
	{
		$getlevel_sql = "select DiskLevel,LoadLevel,LoginLevel,NetworkLevel,ProcessLevel,ConnectLevel from ipinfo where Ip='$ip';";
		$getports_sql = "select Port,Service,IsMon,ports.id  from ports,ipinfo where ports.Ipid = ipinfo.id and ipinfo.Ip='$ip'";
		$level_rs = getrs($getlevel_sql);
		$ports_rs = getrs($getports_sql);
	}
	else
	{
		if (check_ip($ip,$s_u_id))
		{
			$getlevel_sql = "select DiskLevel,LoadLevel,LoginLevel,NetworkLevel,ProcessLevel,ConnectLevel from ipinfo where Ip='$ip';";
			$getports_sql = "select Port,Service,IsMon,ports.id from ports,ipinfo where ports.Ipid = ipinfo.id and ipinfo.Ip='$ip'";
			$level_rs = getrs($getlevel_sql);
			$ports_rs = getrs($getports_sql);
		}
		else
		{
			alert_go("你没有此IP的权限","mon_state.php");
		}
	}
}	
?>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=7">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate"> 
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="css/style.css" rel="stylesheet" type="text/css">
<title><?php echo $TITLE_NAME."-";?>配置报警阀值</title>
</head>
<body>
<table width="98%" height="5" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="3"></td>
  </tr>
</table>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top"><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="td_clo_blue">
      <tr>
        <td height="33" background="image/tab_bg1.gif">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="title1">配置报警阀值</span></td>
      </tr>
      <tr>
        <td height="8"></td>
      </tr>
      <tr>
        <td height="30"><table width="80%" height="30" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
              <td><form name="form1" method="get" action="">
                <div align="left">
                  <?php include 'ajax_select.php'; ?>
                </div>
                </form></td>
            </tr>
          </table></td>
      </tr>
      <tr>
        <td height="10"></td>
      </tr>
      <tr>
        <td><form action="save_mon_config.php" method="post" name="form" id="form" >
          <table width="80%" align="center" class="td_clo_blue">
            <tr>
              <td height="10" colspan="3" valign="middle">&nbsp;</td>
            </tr>
            <tr>
              <td width="15%" height="28">&nbsp;</td>
              <td width="24%">IP </td>
              <td><input name="ip" type="text" class="input_txt" id="ip"  readonly="readonly" value="<?php echo $ip;?>" size="24" >
                <?php if ($ip == "0.0.0.0") echo "&nbsp;&nbsp;此IP为配置默认监控阀值所用";?>				</td>
            </tr>
            <tr>
              <td height="28">&nbsp;</td>
              <td>磁盘阀值</td>
              <td><input name="disklevel" type="text" onKeyUp="value=value.replace(/[^\d]/g,'')" class="input_txt" id="disklevel" <?php if ($s_u_level > 2 and $ip =="0.0.0.0") echo " readonly='readonly' ";?>  value="<?php echo $level_rs[0][0];?>" size="24" > 
              &nbsp;&nbsp;磁盘使用超过此阀值报警 (默认值<?php echo $def_level_rs[0][0];?>,单位%) </td> 
            </tr>
            <tr>
              <td height="28">&nbsp;</td>
              <td>负载阀值</td>
              <td><input name="loadlevel" type="text" onKeyUp="value=value.replace(/[^\d]/g,'')" class="input_txt" id="loadlevel" <?php if ($s_u_level > 2 and $ip =="0.0.0.0") echo " readonly='readonly' ";?> value="<?php echo $level_rs[0][1];?>" size="24">
&nbsp;&nbsp;系统负载超过此阀值报警 (默认值<?php echo $def_level_rs[0][1];?>,单位%) </td>
            </tr>
            <tr>
              <td height="28">&nbsp;</td>
              <td height="10">用户阀值</td>
              <td height="10"><input name="loginlevel" onKeyUp="value=value.replace(/[^\d]/g,'')" type="text" class="input_txt" id="loginlevel" <?php if ($s_u_level > 2 and $ip =="0.0.0.0") echo " readonly='readonly' ";?>  value="<?php echo $level_rs[0][2];?>" size="24" >
&nbsp;&nbsp;登录用户超过此阀值报警 (默认值<?php echo $def_level_rs[0][2];?>)</td>
            </tr>
            <tr>
              <td height="28">&nbsp;</td>
              <td height="10">带宽阀值</td>
              <td height="10"><input name="networklevel" onKeyUp="value=value.replace(/[^\d]/g,'')" type="text" class="input_txt" id="networklevel" <?php if ($s_u_level > 2 and $ip =="0.0.0.0") echo " readonly='readonly' ";?> value="<?php echo $level_rs[0][3];?>" size="24" >
&nbsp;&nbsp;下载带宽超过此阀值报警 (默认值<?php echo $def_level_rs[0][3];?>,单位KB) </td>
            </tr>
            <tr>
              <td height="28">&nbsp;</td>
              <td height="10">进程阀值</td>
              <td height="10"><input name="processlevel" onKeyUp="value=value.replace(/[^\d]/g,'')" type="text" class="input_txt" id="processlevel" <?php if ($s_u_level > 2 and $ip =="0.0.0.0") echo " readonly='readonly' ";?> value="<?php echo $level_rs[0][4];?>" size="24" >
&nbsp;&nbsp;进程数超过此阀值报警 (默认值<?php echo $def_level_rs[0][4];?>)</td>
            </tr>
            <tr>
              <td height="28">&nbsp;</td>
              <td height="10">连接阀值</td>
              <td height="10"><input name="connectlevel" onKeyUp="value=value.replace(/[^\d]/g,'')" type="text" class="input_txt" id="connectlevel" <?php if ($s_u_level > 2 and $ip =="0.0.0.0") echo " readonly='readonly' ";?> value="<?php echo $level_rs[0][5];?>" size="24" >
&nbsp;&nbsp;连接数超过此阀值报警 (默认值<?php echo $level_rs[0][5];?>)</td>
            </tr>
            <tr>
              <td height="28">&nbsp;</td>
              <td valign="top">监控端口</td>
              <td><?php 

		for ( $i=0;$i<count($ports_rs);$i++ )
		{ ?>
                <input name="ports[]" type="checkbox" class="radio" id="ports[]"  value="<?php echo $ports_rs[$i][3];?>" 
				<?php 
				if ($ports_rs[$i][2] == 1)
					{ 
						echo "checked";
					} 
				?> /><?php echo $ports_rs[$i][0]."_".$ports_rs[$i][1];echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				if (fmod($i+1,3) == 0 and $i !== 0)
				{
					echo "<br />";
				}
		}

		?> </td>
            </tr>
            <tr>
              <td height="10" colspan="3">&nbsp;</td>
            </tr>
          </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td height="5" colspan="2"></td>
            </tr>
            <tr>
              <td width="80%"></td>
       <td><?php if ($s_u_level < 3 or $ip !=="0.0.0.0"){?><input name="Submit" type="submit" class="delete" id="Submit" value="保存配置"><?php }?></td>
            </tr>
            <tr>
              <td height="5" colspan="2"></td>
            </tr>
          </table>
        </form></td>
        </tr>
      <tr>
        <td height="10"></td>
      </tr>
    </table>    </td>
  </tr>
</table>
<?php include 'boot.php'; ?>
</body>
</html> 
