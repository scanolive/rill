<?php 
include 'head.php';
include 'include/is_monitor.php';
$group = ($_GET['group']);
if (!empty($_GET['ip']))
{
	$ip = ($_GET['ip']);
	$url = ($_GET['url']);
	$url = str_replace("!@!","&",$url);
	if (!check_ip($ip,$s_u_id) and $s_u_level > 2)
	{
		alert_go("你没有此IP的权限",$url);
	}
	else
	{
		$sql = "select SN,devinfo.Ips,DevName,ipgroup.GroupName,Idc,Place,Capex_Price,Opex_Price,HostName from devinfo,ipgroup,ipinfo where ipinfo.Ip='$ip' and ipgroup.id = ipinfo.GroupId and ipinfo.id = devinfo.Ipid;";
		$rs = getrs($sql);
	}
}
?>

<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=7">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="css/style.css" rel="stylesheet" type="text/css">
<title><?php echo $TITLE_NAME."-";?>修改设备信息</title>
</head>
<body>
<table width="98%" height="5" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="3"></td>
  </tr>
</table>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top"><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" class="td_clo_blue">
      <tr>
        <td height="33" background="image/tab_bg1.gif">&nbsp;&nbsp;&nbsp;&nbsp;<span class="title1">&nbsp;</span><span class="title1">修改设备信息</span></td>
      </tr>
      <tr>
        <td height="8"></td>
      </tr>
      <tr>
        <td height="30"><table width="96%" height="30" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
              <td height="3"><form name="form1" method="get" action="">
                <div align="left">
                  <?php  include 'ajax_select.php';?>
                </div>
                            </form></td>
            </tr>

          </table></td>
      </tr>
      <tr>
        <td height="10"></td>
      </tr>
      <tr>
        <td><form action="save_dev_update.php" method="post" name="form" id="form" >
          <table width="96%" align="center" class="td_clo_blue">
            <tr>
              <td height="10" colspan="3" valign="middle">&nbsp;</td>
            </tr>
            <tr>
              <td width="15%" height="28"><input name="url" type="hidden" id="url" value="<?php echo $url; ?>">
			  <input name="SN" type="hidden" id="SN" value="<?php echo $rs[0][0]; ?>">
			  <input name="ip" type="hidden" id="ip" value="<?php echo $ip; ?>"></td>
              <td width="24%">SN</td>
              <td><?php echo $rs[0][0];?></td>
            </tr>
            <tr>
              <td height="28">&nbsp;</td>
              <td>IP </td>
              <td><?php echo $rs[0][1];?>               </td>
            </tr>
            <tr>
              <td height="28">&nbsp;</td>
              <td>主机名</td>
              <td><?php echo $rs[0][8];?></td>
            </tr>
            <tr>
              <td height="28">&nbsp;</td>
              <td>设备备注</td>
              <td><input name="devname" type="text" class="input_txt" id="devname"  value="<?php echo $rs[0][2];?>" size="24" >                  </td>
            </tr>
            
            <tr>
              <td height="28">&nbsp;</td>
              <td>IDC</td>
              <td><input name="idc" type="text" class="input_txt" id="idc" value="<?php echo $rs[0][4];?>" size="24"></td>
            </tr>
            <tr>
              <td height="28">&nbsp;</td>
              <td>位置</td>
              <td><input name="place" type="text" class="input_txt" id="place" value="<?php echo $rs[0][5];?>" size="24"></td>
            </tr>
            <tr>
              <td height="28">&nbsp;</td>
              <td>Capex_Price</td>
              <td><input name="capex" type="text" class="input_txt" id="capex" value="<?php echo $rs[0][6];?>" size="24"></td>
            </tr>
            <tr>
              <td height="28">&nbsp;</td>
              <td>Opex_Price</td>
              <td><input name="opex" type="text" class="input_txt" id="opex" value="<?php echo $rs[0][7];?>" size="24"></td>
            </tr>
			<?php if ($s_u_level < 3){ ?>
			<tr>
              <td height="28">&nbsp;</td>
              <td>所属分组</td>
              <td> <?php include 'include/sgroup.php'; ?></td>
            </tr>
	<?php } ?>		
            <tr>
              <td height="10">&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
          </table>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td height="5" colspan="2"></td>
            </tr>
            <tr>
              <td width="80%"></td>
              <td><input name="Submit2" type="submit" class="delete" value="保存配置"></td>
            </tr>
            <tr>
              <td height="5" colspan="2"></td>
            </tr>
          </table>
        </form>          </td>
      </tr>
      <tr>
        <td height="10"></td>
      </tr>
    </table></td>
  </tr>
</table>
<?php include 'boot.php'; ?>
</body>
</html> 
