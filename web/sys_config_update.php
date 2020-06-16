<?php 
include 'head.php';
if ($s_u_level > 1)
{
	alert_go("你没有权限","index.php");
}
$sql = "select * from sys_config where Uid='NoUID'";
$sys_rs = getrs($sql);
$SYSCONFIG = array();
for( $i=0;$i<count($sys_rs);$i++ )
{       
	$SYSCONFIG[$sys_rs[$i][3]] = $sys_rs[$i][4];
}           

?>

<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=7">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="css/style.css" rel="stylesheet" type="text/css">
<title><?php echo $TITLE_NAME."-";?>修改系统配置</title>
</head>
<body>
<table width="98%" height="5" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="3"></td>
  </tr>
</table>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="td_clo_blue">
  <tr>
    <td height="33" background="image/tab_bg1.gif">&nbsp;&nbsp;&nbsp;&nbsp;<span class="title1">&nbsp;</span><span class="title1">修改系统配置</span></td>
  </tr>
  <tr>
    <td height="8"></td>
  </tr>
  <tr>
    <td height="20">&nbsp;</td>
  </tr>
  <tr>
    <td height="10"></td>
  </tr>
  <tr>
    <td><form action="save_sys_config.php" method="post" name="form" id="form" >
      <table width="80%" border="0" align="center" class="td_clo_blue">
		<tr>	
          <td height="10">&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
		</tr>
        <tr>
          <td width="20" height="28">&nbsp;</td>
          <td width="190">默认操作记录每页记录数</td>
          <td><input name="history" type="text" class="input_txt" id="history" value="<?php echo (get_config("public","history_pagesize","NoUid",$SYSCONFIG));?>" size="26"></td>
          <td width="210">默认设备信息每页记录数</td>
          <td><input name="devinfo" type="text" class="input_txt" id="devinfo" value="<?php echo (get_config("public","devinfo_pagesize","NoUid",$SYSCONFIG));?>" size="26" > </td>
        </tr>
        <tr>
          <td height="28">&nbsp;</td>
          <td>默认首页信息每页记录数</td>
          <td><input name="index" type="text" class="input_txt" id="index"  value="<?php echo (get_config("public","index_pagesize","NoUid",$SYSCONFIG));?>" size="26" >
                </td>
          <td>默认监控信息每页记录数</td>
          <td><input name="monstate" type="text" class="input_txt" id="monstate" value="<?php echo (get_config("public","monstate_pagesize","NoUid",$SYSCONFIG));?>" size="26"></td>
        </tr>
        <tr>
          <td height="28">&nbsp;</td>
          <td>默认页面监控每页记录数</td>
          <td><input name="monweb" type="text" class="input_txt" id="monweb" value="<?php echo (get_config("public","monweb_pagesize","NoUid",$SYSCONFIG));?>" size="26"></td>
          <td>默认批量管理每页记录数</td>
          <td><input name="batchdo" type="text" class="input_txt" id="batchdo" value="<?php echo (get_config("public","batchdo_pagesize","NoUid",$SYSCONFIG));?>" size="26"></td>
        </tr>
					 <tr>
              <td height="28">&nbsp;</td>
              <td>运默认行结果每页记录数</td>
              <td><input name="bg_result" type="text" class="input_txt" id="bg_result" value="<?php echo (get_config("public","bg_result_pagesize","NoUid",$SYSCONFIG));?>" size="26" onKeyUp="value=value.replace(/[^\d]/g,'')"></td>
              <td>默认系统日志每页记录数</td>
              <td><input name="err_logs" type="text" class="input_txt" id="err_logs" value="<?php echo (get_config("public","err_logs_pagesize","NoUid",$SYSCONFIG));?>" size="26" onKeyUp="value=value.replace(/[^\d]/g,'')"></td>
            </tr>
					 <tr>
              <td height="28">&nbsp;</td>
              <td>默认自定义系统标题名称</td>
              <td><input name="system_name" type="text" class="input_txt" id="system_name" value="<?php echo (get_config("public","system_name","NoUid",$SYSCONFIG));?>" size="26" ></td>
			<td>不可操作客户机的命令</td>
              <td><input name="norun_cmd" type="text" class="input_txt" id="norun_cmd" value="<?php echo (get_config("public","norun_cmd","NoUid",$SYSCONFIG));?>" size="26" ></td>
            </tr>
					 <tr>
              <td height="28">&nbsp;</td>
              <td>自定义文件上传目录</td>
              <td><input name="upfile_dir" type="text" class="input_txt" id="upfile_dir" value="<?php echo (get_config("public","upfile_dir","NoUid",$SYSCONFIG));?>" size="26" ></td>
              <td>最大上传文件大小(单位M)</td>
              <td><input name="max_upfile_size" type="text" class="input_txt" id="max_upfile_size" value="<?php echo (get_config("public","max_upfile_size","NoUid",$SYSCONFIG));?>" size="26" ></td>
            </tr>
        <tr>
          <td height="28">&nbsp;</td>
          <td>默认监控端口(需重启py程序)</td>
		  <td><input name="def_mon_ports" type="text" class="input_txt" id="def_mon_ports" value="<?php echo (get_config("public","def_mon_ports","NoUid",$SYSCONFIG));?>" size="26"></td>
          <td>页面检测间隔(需重启py程序)</td>
		  <td><input name="monweb_interval" type="text" class="input_txt" id="monweb_interval" value="<?php echo (get_config("public","monweb_interval","NoUid",$SYSCONFIG));?>" size="26"></td>
        </tr>
        <tr>
          <td height="28">&nbsp;</td>
          <td>python服务器IP</td>
		  <td><input name="python_server_ip" type="text" class="input_txt" id="python_server_ip" value="<?php echo (get_config("public","python_server_ip","NoUid",$SYSCONFIG));?>" size="26"></td>
          <td>python服务器端口</td>
		  <td><input name="python_server_port" type="text" class="input_txt" id="python_server_port" value="<?php echo (get_config("public","python_server_port","NoUid",$SYSCONFIG));?>" size="26"></td>
        </tr>
        <tr>
          <td height="28">&nbsp;</td>
          <td>python服务器OCT_CMD_PRE</td>
		  <td><input name="python_oct_cmd_pre" type="text" class="input_txt" id="python_oct_cmd_pre" value="<?php  echo (get_config("public","python_oct_cmd_pre","NoUid",$SYSCONFIG));?>" size="26"></td>
          <td>python服务器END_CMD_STR</td>
		  <td><input name="python_end_cmd_str" type="text" class="input_txt" id="python_end_cmd_str" value="<?php  echo (get_config("public","python_end_cmd_str","NoUid",$SYSCONFIG));?>" size="26"></td>
        </tr>
        <tr>
          <td height="28">&nbsp;</td>
          <td>python服务器END_STR</td>
		  <td><input name="python_end_str" type="text" class="input_txt" id="python_end_str" value="<?php  echo (get_config("public","python_end_str","NoUid",$SYSCONFIG));?>" size="26"></td>
          <td>python服务器SEP_STR</td>
		  <td><input name="python_sep_str" type="text" class="input_txt" id="python_sep_str" value="<?php  echo (get_config("public","python_sep_str","NoUid",$SYSCONFIG));?>" size="26"></td>
        </tr>
        <tr>
          <td height="28">&nbsp;</td>
          <td>python服务器SEP_STR_SE</td>
		  <td><input name="python_sep_str_se" type="text" class="input_txt" id="python_sep_str_se" value="<?php  echo (get_config("public","python_sep_str_se","NoUid",$SYSCONFIG));?>" size="26"></td>
          <td>页面超时(单位秒）</td>
		  <td><input name="php_timeout" type="text" class="input_txt" id="php_timeout" value="<?php echo (get_config("public","php_timeout","NoUid",$SYSCONFIG));?>" size="26"></td>
        </tr>
        <tr>
          <td height="28">&nbsp;</td>
          <td>启用控制中心(有风险)</td>
          <td><input name="ctrl_center_enable" type="checkbox" id="ctrl_center_enable" value="YES"  <?php if (get_config("public","ctrl_center_enable","NoUid",$SYSCONFIG) == "YES") echo "checked"; ?> >          </td>
          <td>WebShell(有风险需启用控制中心)</td>
          <td><input name="ssh_enable" type="checkbox" id="ssh_enable"  value="YES"   <?php if (get_config("public","ssh_enable","NoUid",$SYSCONFIG) == "YES") echo "checked"; ?> >
            </td>
        </tr>
        <?php if ($s_u_level < 3){ ?>
        <?php } ?>
        <tr>
          <td height="10">&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
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
    </form></td>
  </tr>
  <tr>
    <td height="10"></td>
  </tr>
</table>
<?php include 'boot.php'; ?>
</body>
</html> 
