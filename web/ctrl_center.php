<?php 
include 'head.php';
include 'include/is_monitor.php';
$UPFILE_ALLPATH = $s_s_upfile_allpath;

if ((get_config("private","ctrl_center_enable",$s_u_id,$SYSCONFIG)) !== "YES"  or (get_config("public","ctrl_center_enable","NoUid",$SYSCONFIG)) !== "YES")
{
	alert_go("控制中心未启用","mon_state.php");
}

if (!empty($_GET['ip']))
{
	$ip = ($_GET['ip']);
	$client_status = get_client_status($ip);
	if (( $s_u_level > 2 ) and !(check_ip($ip,$s_u_id)))
	{
		alert_go("你没有此IP的权限","mon_state.php");
	}
}	 
else 
{
	alert_go("请指定IP","mon_state.php");
}
$grps = $s_u_groups;
$client_status = get_client_status($ip);
if (!$client_status == "1")
alert_go("此IP客户端异常","mon_state.php");

if (!empty($_GET['group']) and ($_GET['group'] !== "All"))
{
	$group = request($_GET['group']);
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


if (!empty($_GET['ip']))
{
	$selected_ips = request(trim($_GET['ip']));	
}
$sql="select CmdName,FileName,SaveName,CmdStr,Note,define_cmd.id from define_cmd left join upfile on   upfile.id=ShellFileId";
$cmd_rs = getrs($sql);
?>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=7">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $TITLE_NAME."-";?>控制中心</title>

</head>
<link href="css/style.css" rel="stylesheet" type="text/css">

<body>
<table width="98%" height="5" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="3"></td>
  </tr>
</table>
<table width="98%" height="5" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="3"></td>
  </tr>
</table>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="td_clo_blue">
  <tr>
    <td height="33" background="image/tab_bg1.gif">&nbsp;&nbsp;&nbsp;&nbsp;<span class="title1">&nbsp;控制中心</span></td>
  </tr>
  <tr>
    <td height="5"><table width="98%" height="5" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td height="3"></td>
      </tr>
    </table>
    <table width="98%" height="33" border="0" align="center" cellpadding="0" cellspacing="0" class="td_clo_blue">
      <tr>
        <td>
            <form name="form1" method="get" action="">
              <div align="left">
			<?php include 'ajax_select.php'; ?>
        </div>
            </form>          </td>
        <td width="80"><div align="center"><a href="nowinfo.php?ip=<?php echo $ip.'&group='.$group;?>" target="_blank" class="delete">即时信息</a></div></td>
        <td width="80"><div align="center"><a href="dayinfo.php?ip=<?php echo $ip.'&group='.$group;?>" target="_blank" class="delete">历史信息</a></div></td>
        <td width="80"><div align="center"><a href="graph.php?ip=<?php echo $ip.'&group='.$group;?>" target="_blank" class="delete">图形分析</a></div></td>
        <td width="80"><div align="center"><a href="mon_config.php?ip=<?php echo $ip.'&group='.$group;?>" target="_blank" class="delete">监控配置</a></div></td>
        </tr>
    </table>
    <table width="98%" height="5" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td height="3"></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td valign="top"><form name="form1" method="post" action="">
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#999999" class="td_suojin">
  <tr>
    <td width="10%" height="28" bgcolor="#FFFFFF"><table width="100%" border="1" align="center" class='bgah'>
      <tr>
        <td height="28">系统命令</td>
      </tr>
    </table>
      </td>
    <td height="24" bgcolor="#FFFFFF"><label for="update_client">
      </label>
      <table width="100%" border="1" align="center" class='bgah'>
        <tr>
          <td width="20%" height="28"><label for="update_client">
      <input name="selected_cmd" type="radio" class="radio"  id="update_client"  onClick="temp_cmd.value=this.value" value="UPDATE_CLIENT" >
      更新重启客户端</label>      </td>
          <td width="20%"><label  for="update_client_file"> <input type="radio" name="selected_cmd" class="radio"   id="update_client_file"  onClick="temp_cmd.value=this.value" value="UPDATE_CLIENT_FILE">
      更新客户端文件</label></td>
          <td width="20%"><label for="restart_client">
      <input type="radio" name="selected_cmd" id="restart_client" class="radio"  onClick="temp_cmd.value=this.value" value="RESTART_CLIENT">
      重启客户端</label> </td>
          <td width="20%"><label for="update_shfile">
      <input type="radio" name="selected_cmd" id="update_shfile" class="radio"  onClick="temp_cmd.value=this.value" value="UPDATE_SHFILE">
      更新shell文件</label></td>
          <td width="20%">
            <div align="center">
              <input name="temp_cmd"  id="temp_cmd" type="hidden" value="" >
              <input name="selected_ips"  id="selected_ips" type="hidden" value="<?php echo trim($selected_ips); ?>">
              <input name="file_id"  id="file_id" type="hidden" value="">
              <input name="cpfile" type="button" class="delete" id="cpfile"  style="display:inline;"  onClick='showhidediv("copyfile");showHint_get_filelist();document.getElementById("myDiv").innerHTML = ""'   value="拷贝文件">
              <input name="runcmd" type="button" class="delete" id="runcmd"   style="display:inline;" onClick="if (temp_cmd.value=='' || selected_ips.value==''){alert('IP和命令均不能为空!'); }else {showHint_socket(temp_cmd.value,selected_ips.value,'<?php echo $_SESSION['verify_str'];?>')}"   value="运行">
              </div></td>
        </tr>
      </table>
      
            
          
      
      </td>
    </tr>
  <tr>
    <td height="28" bgcolor="#FFFFFF"><table width="100%"  border="1" align="center" class='bgah'>
        <tr>
          <td width="100%" height="<?php echo ceil(count($cmd_rs)/5)*30-6; ?>" >自定义命令 </td>
        </tr>
      </table></td>
    <td height="24" bgcolor="#FFFFFF">
<?php 
echo '<table width="100%" border="1" class="bgah">';
for ($j=0;$j<ceil(count($cmd_rs)/5);$j++)
{
	echo '<tr>';
	for ($i=0;$i<5;$i++)
	{
		echo '<td  height="24" width="20%" >';
		if (isset($cmd_rs[$j*5+$i]))
		{
			$cmdstr = $cmd_rs[$j*5+$i][3];
			/*
		    $cmdstr = str_replace("r#o#s_syh",'"',$cmdstr);
		    $cmdstr = str_replace("r#o#s_dyh","'",$cmdstr);
		    $cmdstr = str_replace("r#o#s_fh",';',$cmdstr);
		    $cmdstr = str_replace("r#o#s_fxg",'\\',$cmdstr);
			*/
			echo '<label title="';
			if ($cmd_rs[$j*5+$i][1] != "")
			{
				echo  '拷贝'.$cmd_rs[$j*5+$i][1].'到主机,并运行'.$cmdstr;
				echo '" for="CMD_R_'.$cmd_rs[$j*5+$i][5].'">';
				echo '<input type="radio" name="selected_cmd" class="radio"';
				echo 'id="CMD_R_'.$cmd_rs[$j*5+$i][5].'" onClick="temp_cmd.value=this.value" value="DEFINE_CMD'.$SEP_STR_SE.$UPFILE_ALLPATH.$cmd_rs[$j*5+$i][2].$SEP_STR_SE.$cmd_rs[$j*5+$i][1].$SEP_STR_SE.$cmdstr.'">';
				echo $cmd_rs[$j*5+$i][0].'</label>';
			}	
			else
		 	{
		  		echo '运行'.$cmdstr;
				echo '" for="CMD_R_'.$cmd_rs[$j*5+$i][5].'">';
				echo '<input type="radio" name="selected_cmd" class="radio"';
				echo 'id="CMD_R_'.$cmd_rs[$j*5+$i][5].'" onClick="temp_cmd.value=this.value" value="'.$cmdstr.'">';
				echo $cmd_rs[$j*5+$i][0].'</label>';	
		 	}							
		}
		echo '</td>';
	}
	echo '</tr>';
}	
echo '</table>'; ?></td>
  </tr>
</table></form>
<?php if ((get_config("private","ssh_enable",$s_u_id,$SYSCONFIG)) == "YES" and (get_config("public","ssh_enable","NoUid",$SYSCONFIG)) == "YES") {?>
      <table width="98%" height="32" border="0" align="center" cellpadding="0" cellspacing="0"  >
        <tr>
          <td width="5%"><div align="left">
              <input name="ip" type="hidden" id="ip" value="<?php echo $selected_ips;?>">
              <?php echo $selected_ips;?>#</div></td>
		  <td><input name="cmd" class="ssh-input"  id="cmd" onKeyDown="if(event.keyCode==13){showHint_socket(this.value,'<?php echo $selected_ips;?>','<?php echo $_SESSION['verify_str'];?>');cmd.value=''}" size="115" ></td>
        </tr>
      </table>
      <?php  }?>
      <table width="98%" height="0" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td><div  id="myDiv" ></div></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td height="3"></td>
  </tr>
  <tr>
    <td><div style="display:none" id="copyfile">
      <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" class="td_clo_blue">
        <tr>
          <td height="33" background="image/tab_bg1.gif"><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td>&nbsp;&nbsp;&nbsp;&nbsp;<span class="title1">&nbsp;</span>文件列表</td>
              <td width="80"><input name="addip2" type="button" class="delete" id="addip2"    onClick="showHint_get_filelist();"   value="刷新列表"></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td height="4"></td>
        </tr>
        <tr>
          <td height="4"><div id="filelist"></div></td>
        </tr>
        <tr>
          <td height="3"></td>
        </tr>
      </table>
      <table width="100%" height="2" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td></td>
        </tr>
      </table>
      <table width="98%" height="45" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#F0F0F0" class="td_clo">
        <tr>
          <td width="60"><div align="right"> 源文件: </div></td>
          <td><input name="s_file" type="text" class="input_txt" id="s_file" size="45"></td>
          <td width="60"><div align="right">目标文件:</div></td>
          <td ><input name="d_file" type="text" class="input_txt" id="d_file" size="45"></td>
          <td width="100" ><div align="center">
          <input name="Submit" type="button" onClick="if (s_file.value=='' || d_file.value==''||'<?php echo trim($selected_ips); ?>'==''){alert('源文件,目标文件和IP均不能为空'); }else {showHint_socket('CPFILE '+s_file.value+' '+d_file.value,'<?php echo trim($selected_ips); ?>','<?php echo $_SESSION['verify_str'];?>')}" class="delete" value="执行">
          </div></td>
        </tr>
      </table>
    </div></td>
  </tr>
  <tr>
    <td height="3"></td>
  </tr>
</table>
<?php include 'boot.php'; ?>
</body>
</html>
