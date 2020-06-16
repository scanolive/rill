<?php 
include 'head.php';
include 'include/is_monitor.php';

$OCT_CMD_PRE = $_SESSION['python_oct_cmd_pre'];
$END_CMD_STR = $_SESSION['python_end_cmd_str'];
$END_STR = $_SESSION['python_end_str'];
$SEP_STR = $_SESSION['python_sep_str'];
$SEP_STR_SE = $_SESSION['python_sep_str_se'];

$UPFILE_DIR = $_SESSION['upfile_dir'];
$UPFILE_ALLPATH = $s_s_upfile_allpath;

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

	$sql = "select devinfo.HostName,ipinfo.Ip, replace(ips,ipinfo.Ip,''),ipgroup.GroupName,(select MonTime from monitor where monitor.ipid=ipinfo.id order by id desc limit 1) as LastCheckTime,Enable,ipinfo.id,ipgroup.id from ipinfo  Left Join devinfo ON devinfo.Ipid=ipinfo.id Left Join ipgroup ON ipgroup.id = ipinfo.GroupId where ipinfo.ip != '0.0.0.0' and  Isalive = 'alive'  and Enable = 1 and  ClientStatus=1 and ipinfo.GroupId in $gids"; 
	$sql_num = "select count(ipinfo.Ip) from ipinfo  Left Join devinfo ON devinfo.Ipid=ipinfo.id Left Join ipgroup ON ipgroup.id = ipinfo.GroupId where ipinfo.ip != '0.0.0.0' and  Isalive = 'alive'  and Enable = 1 and  ClientStatus=1 and ipinfo.GroupId in $gids  ";
	 
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


if (!empty($_POST['selected_ips']))
{
	$selected_ips = trim($_POST['selected_ips']);
}
else
{
	$selected_ips = "";
}
//print_r($ip);


$sql="select CmdName,FileName,SaveName,CmdStr,Note,define_cmd.id from define_cmd left join upfile on   upfile.id=ShellFileId";
$cmd_rs = getrs($sql);
?>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=7">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $TITLE_NAME."-";?>批量管理</title>

</head>
<link href="css/style.css" rel="stylesheet" type="text/css">

<body>
<table width="100%" height="5" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td></td>
  </tr>
</table>
<table  border="0" align="center" cellpadding="0" cellspacing="0" class="tab_width" >
  <tr>
    <td width="140" valign="top" class="td_clo_blue"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="33" background="image/tab_bg1.gif"><span class="title1">&nbsp;&nbsp;&nbsp;&nbsp;已选择IP</span></td>
      </tr>
      <tr>
        <td>
<?php $myarr = explode (" ",$selected_ips);
for ($i=0;$i<count($myarr);$i++)
{
	if ($myarr[$i] != "")
	{
		echo "<div id='$myarr[$i]'>&nbsp;";
		echo '  <a href="javascript:void(0)">  <img src="image/del.png" onclick="delip('."'".$myarr[$i]."'".')"  border="0" align="absbottom"  width="13" height="13" /></a>   ';
		echo $myarr[$i];
		echo "</div>";
	}
}?></td>
      </tr>
    </table>      
    </td>
    <td width="5" valign="top">&nbsp;</td>
    <td valign="top" class="td_clo_blue">
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td height="33" background="image/tab_bg1.gif">&nbsp;&nbsp;&nbsp;&nbsp;<span class="title1">&nbsp;选择操作</span></td>
          </tr>
          <tr>
            <td height="5"></td>
          </tr>
          <tr>
            <td valign="top"><form name="form1" method="post" action="">
              <table width="98%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#F0F0F0"  class="td_suojin">
                <tr>
                  <td width="8%" height="32" bgcolor="#FFFFFF">系统命令</td>
                  <td bgcolor="#FFFFFF"><table width="100%" border="1" class="bgah">
                    <tr>
                      <td width="20%" height="28"><label for="update_client">
                        <input name="selected_cmd" type="radio" class="radio"  id="update_client"  onClick="temp_cmd.value=this.value" value="UPDATE_CLIENT" >
                        更新重启客户端</label></td>
                      <td width="20%"><label for="update_client_file">
                        <input type="radio" name="selected_cmd" class="radio"   id="update_client_file"  onClick="temp_cmd.value=this.value" value="UPDATE_CLIENT_FILE">
                        更新客户端文件</label></td>
                      <td width="20%"><label for="restart_client">
                        <input type="radio" name="selected_cmd" id="restart_client" class="radio"  onClick="temp_cmd.value=this.value" value="RESTART_CLIENT">
                        重启客户端</label></td>
                      <td width="20%"><label for="update_shfile">
                        <input type="radio" name="selected_cmd" id="update_shfile" class="radio"  onClick="temp_cmd.value=this.value" value="UPDATE_SHFILE">
                        更新shell文件</label></td>
                      <td width="20%"><input name="temp_cmd"  id="temp_cmd" type="hidden" value="" >
                        <input name="selected_ips"  id="selected_ips" type="hidden" value="<?php echo trim($selected_ips); ?>">
                        <input name="file_id"  id="file_id" type="hidden" value="">
                        
                          <input name="cpfile" type="button" class="delete" id="cpfile" style="display:inline;"   onClick='showhidediv("copyfile");showHint_get_filelist();document.getElementById("myDiv").innerHTML = ""'   value="拷贝文件">
                       
                          
                            <input name="runcmd" type="button" class="delete" id="runcmd"  style="display:inline;"  onClick="if (temp_cmd.value=='' || selected_ips.value==''){alert('IP和命令均不能为空!'); }else {showHint_socket(temp_cmd.value,selected_ips.value,'<?php echo $_SESSION['verify_str'];?>')}"   value="运行">
                        </td>
                    </tr>
                  </table></td>
                </tr>
                <tr>
                  <td height="24" bgcolor="#FFFFFF">自定义命令</td>
                  <td bgcolor="#FFFFFF"><?php 
echo '<table width="100%" border="1" class="bgah">';
for ($j=0;$j<ceil(count($cmd_rs)/5);$j++)
{
	echo '<tr>';
	for ($i=0;$i<5;$i++)
	{
		echo '<td  height="24" width="20%" >';
		if (isset($cmd_rs[$j*5+$i]))
		{
			echo '<label title="';
			if ($cmd_rs[$j*5+$i][1] != "")
			{
				echo  '拷贝'.$cmd_rs[$j*5+$i][1].'到主机,并运行'.$cmd_rs[$j*5+$i][3];
				echo '" for="CMD_R_'.$cmd_rs[$j*5+$i][5].'">';
				echo '<input type="radio" name="selected_cmd" class="radio"';
				echo 'id="CMD_R_'.$cmd_rs[$j*5+$i][5].'" onClick="temp_cmd.value=this.value" value="DEFINE_CMD'.$SEP_STR_SE.$UPFILE_ALLPATH.$cmd_rs[$j*5+$i][2].$SEP_STR_SE.$cmd_rs[$j*5+$i][1].$SEP_STR_SE.$cmd_rs[$j*5+$i][3].'">';
				echo $cmd_rs[$j*5+$i][0].'</label>';
			}	
			else
		 	{
		  		echo '运行'.$cmd_rs[$j*5+$i][3];
				echo '" for="CMD_R_'.$cmd_rs[$j*5+$i][5].'">';
				echo '<input type="radio" name="selected_cmd" class="radio"';
				echo 'id="CMD_R_'.$cmd_rs[$j*5+$i][5].'" onClick="temp_cmd.value=this.value" value="'.$cmd_rs[$j*5+$i][3].'">';
				echo $cmd_rs[$j*5+$i][0].'</label>';	
		 	}							
		}
		echo '</td>';
	}
	echo '</tr>';
}	
echo '</table>'; ?></td>
                </tr>
              </table>
              </form>
              <table width="96%" height="24" border="0" align="center" cellpadding="0" cellspacing="0"  >
                <tr>
                  <td> shell命令#
                      <input name="cmd" class="ssh-input"  id="cmd" onKeyDown="if(event.keyCode==13){showHint_socket(this.value,selected_ips.value,'<?php echo $_SESSION['verify_str'];?>');cmd.value=''}" size="108" ></td>
                </tr>
              </table>
              <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td height="4" valign="top"></td>
                </tr>
                <tr>
                  <td height="1" valign="top" bgcolor="#33CCCC"></td>
                </tr>
                <tr>
                  <td height="4" valign="top"></td>
                </tr>
              </table>
              <div style="display:none" id="copyfile">
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
                 <td width="60"><div align="right">
                    源文件: </div></td>
                 <td><input name="s_file" type="text" class="input_txt" id="s_file" size="45"></td>
                 <td width="60"><div align="right">目标文件:</div></td>
                 <td ><input name="d_file" type="text" class="input_txt" id="d_file" size="45"></td>
                 <td width="100" ><div align="center">
                     <input name="Submit" type="button" onClick="if (s_file.value=='' || d_file.value==''||'<?php echo trim($selected_ips); ?>'==''){alert('源文件,目标文件和IP均不能为空'); }else {showHint_socket('CPFILE '+s_file.value+' '+d_file.value,'<?php echo trim($selected_ips); ?>','<?php echo $_SESSION['verify_str'];?>')}" class="delete" value="执行">
                 </div></td>
               </tr>
             </table>
             </div>
</td>
          </tr>
          <tr>
            <td><table width="98%" height="0" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td><div  id="myDiv" ></div></td>
  </tr>
</table>
              <table width="98%" height="3" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td></td>
                </tr>
              </table></td>
          </tr>
        </table>
   </td>
  </tr>
</table>
<?php include 'boot.php'; ?>
</body>
</html>
