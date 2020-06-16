<?php 
include 'head.php';
if (isset($_GET['group']))
{
	$group = ($_GET['group']);
}

if (!empty($_GET['ip']))
{
	$ip = ($_GET['ip']);
	if (!check_ip($ip,$s_u_id) and $s_u_level > 2)
	{
		alert_go("你没有此IP的权限","devinfo.php");
	}
	else
	{
		$sql = "select SN,devinfo.Ip,DevName,ipgroup.GroupName,Idc,Place,Capex_Price,Opex_Price from devinfo,ipgroup,ipinfo where devinfo.Ip='$ip' and ipgroup.id = ipinfo.GroupId and ipinfo.Ip = devinfo.Ip;";
		$rs = getrs($sql);
	}
}
?>

<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=7">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="css/style.css" rel="stylesheet" type="text/css">
<title><?php echo $TITLE_NAME."-";?>修改显示配置</title>
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
        <td height="33" background="image/tab_bg1.gif">&nbsp;&nbsp;&nbsp;&nbsp;<span class="title1">&nbsp;</span><span class="title1">修改每页记录数</span></td>
      </tr>
      <tr>
        <td height="8"></td>
      </tr>
      <tr>
        <td height="30">&nbsp;</td>
      </tr>
      <tr>
        <td><form action="save_user_config.php" method="post" name="form" id="form" >
          <table width="80%" align="center" class="td_clo_blue">
            <tr>
              <td height="10" colspan="3" valign="middle">&nbsp;</td>
            </tr>
            <tr>
              <td width="15%" height="28">&nbsp;</td>
              <td width="24%">操作记录每页记录条数</td>
              <td><input name="history" type="text" class="input_txt" id="history" value="<?php echo $s_u_history_pagesize;?>" size="24" onKeyUp="value=value.replace(/[^\d]/g,'')">              </td>
            </tr>
            <tr>
              <td height="28">&nbsp;</td>
              <td>设备信息每页记录条数</td>
              <td><input name="devinfo" type="text" class="input_txt" id="devinfo" value="<?php echo $s_u_devinfo_pagesize;?>" size="24" onKeyUp="value=value.replace(/[^\d]/g,'')">
                  </h2></td>
            </tr>
            <tr>
              <td height="28">&nbsp;</td>
              <td>首页信息每页记录条数</td>
              <td><input name="index" type="text" class="input_txt" id="index"  value="<?php  echo $s_u_index_pagesize;?>" size="24" onKeyUp="value=value.replace(/[^\d]/g,'')">
                  </h2></td>
            </tr>
            
            <tr>
              <td height="28">&nbsp;</td>
              <td>监控信息每页记录条数</td>
              <td><input name="monstate" type="text" class="input_txt" id="monstate" value="<?php echo $s_u_monstate_pagesize;?>" size="24" onKeyUp="value=value.replace(/[^\d]/g,'')"></td>
            </tr>
            <tr>
              <td height="28">&nbsp;</td>
              <td>页面监控每页记录条数</td>
              <td><input name="monweb" type="text" class="input_txt" id="monweb" value="<?php echo $s_u_monweb_pagesize;?>" size="24" onKeyUp="value=value.replace(/[^\d]/g,'')"></td>
            </tr>
		     <tr>
              <td height="28">&nbsp;</td>
              <td>批量管理每页记录条数</td>
              <td><input name="batchdo" type="text" class="input_txt" id="batchdo" value="<?php echo $s_u_batchdo_pagesize;?>" size="24" onKeyUp="value=value.replace(/[^\d]/g,'')"></td>
            </tr>
			 <tr>
              <td height="28">&nbsp;</td>
              <td>运行结果每页记录条数</td>
              <td><input name="bg_result" type="text" class="input_txt" id="bg_result" value="<?php echo $s_u_bg_result_pagesize;?>" size="24" onKeyUp="value=value.replace(/[^\d]/g,'')"></td>
            </tr>
			 <tr>
              <td height="28">&nbsp;</td>
              <td>系统日志每页记录条数</td>
              <td><input name="err_logs" type="text" class="input_txt" id="err_logs" value="<?php echo $s_u_err_logs_pagesize;?>" size="24" onKeyUp="value=value.replace(/[^\d]/g,'')"></td>
            </tr>
            <tr>
              <td height="28">&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
			<?php if ($s_u_level < 3){ ?>
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
