<?php 
include 'head.php';
include 'include/is_monitor.php';
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
$sql="select CmdName,FileName,CmdStr,Note,define_cmd.id from define_cmd Left Join upfile on upfile.id=ShellFileId";
$cmd_rs = getrs($sql);
//print_r($cmd_rs);

?>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=7">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $TITLE_NAME."-";?>批量管理</title>
</head>
<link href="css/style.css" rel="stylesheet" type="text/css">

<body>
<form action="save_define_cmd.php" method="post" name="form" id="form" >
<table width="100%" height="5" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td></td>
  </tr>
</table>
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" class="td_clo_blue">
  <tr>
    <td height="33" background="image/tab_bg1.gif">&nbsp;&nbsp;&nbsp;&nbsp;<span class="title1">&nbsp;自定义命令</span></td>
  </tr>
  <tr>
    <td><table width="98%" height="5" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td height="8"></td>
      </tr>
    </table>
      <table width="98%" border="0" align="center" cellpadding="0" cellspacing="1"  bgcolor="#F0F0F0" class="Ptable">
      <tr>
        <td height="24">Num</td>
        <td>命令名称</td>
        <td>Shell文件</td>
        <td>具体命令</td>
        <td>命令说明</td>
        <td width="120">操作|<label onClick='showhidediv("copyfile");showHint_get_filelist();document.getElementById("myDiv").innerHTML = ""' > 
                 <a href="#">添加命令</a></label>
          </td>
      </tr>
      <?php 
				  for ($i=0;$i<count($cmd_rs);$i++)
				  {
				  	echo '<tr>';
/*				  	echo '<label for="CMD_R_'.$cmd_rs[$i][5].'">';
					echo '<input type="radio" name="selected_cmd" class="radio"';
					echo 'id="CMD_R_'.$cmd_rs[$i][5].'" onClick="temp_cmd.value=this.value" value="DEFINE_CMD'.$SEP_STR_SE.$UPFILE_ALLPATH.$cmd_rs[$i][2].$SEP_STR_SE.$cmd_rs[$i][1].$SEP_STR_SE.$cmd_rs[$i][3].'">';
					echo $cmd_rs[$i][0].'</label>';
*/					echo '<td>';
					echo $i+1;
					echo '</td>';
					for ($j=0;$j<count($cmd_rs[$i])/2-1;$j++)
					{
						echo '<td>';
						$cmdstr = $cmd_rs[$i][$j];
			            $cmdstr = str_replace("r#o#s_syh",'"',$cmdstr);
			            $cmdstr = str_replace("r#o#s_dyh","'",$cmdstr);
			            $cmdstr = str_replace("r#o#s_fh",';',$cmdstr);
			            $cmdstr = str_replace("r#o#s_fxg",'\\',$cmdstr);
						echo $cmdstr;
						echo '</td>';
					}
					echo '<td>';
					echo '<a href="define_cmd_delete.php?id='.$cmd_rs[$i][$j].'">删除命令</a>';
					echo '</td>';
					echo '</tr>';
				  }
//				  echo '</table>';
				  ?>
      </table>
      
      <table width="98%" height="5" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td height="3"></td>
        </tr>
      </table>
	  <div style="display:none" id="copyfile" >
      <table   width="98%" height="5" border="0" align="center" cellpadding="0" cellspacing="0" class="td_clo_blue" id="copyfile">
        <tr>
          <td height="3"><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td width="120" height="8">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td colspan="2">&nbsp;</td>
                <td width="100">&nbsp;</td>
              </tr>
              <tr>
                <td height="24">&nbsp;</td>
                <td>名称</td>
                <td colspan="2"><input title="显示的命令名" name="cmdname" type="text" class="input_txt" id="cmdname" size="45">&nbsp;&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td height="24">&nbsp;</td>
                <td>命令(后台运行加&)</td>
                <td colspan="2"><input  title="具体命令,要在后台运行请加&" name="cmdstr" type="text" class="input_txt" id="cmdstr" size="45">&nbsp;&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td height="24">&nbsp;</td>
                <td>说明</td>
                <td colspan="2"><input itle="关于命令的说明" name="note" type="text" class="input_txt" id="note" size="45">&nbsp;&nbsp;
                </td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td height="24">&nbsp;</td>
                <td>Shell文件</td>
                <td colspan="2"><label for="check_shell"><input id="check_shell" type="checkbox" name="check_shell" onClick='showhidediv("filelist");showhidediv("shuaxin");'  class="radio" value=1 />
               若要运行脚本,请点击并选取脚本文件</label></td>
                <td>&nbsp;</td>
              </tr>
            </table>
              <table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td height="2"></td>
                </tr>
                <tr>
                  <td><div style="display:none" id="filelist"></div></td>
                </tr>
                <tr>
                  <td height="2"></td>
                </tr>
              </table>
            <table width="100%" align="center">
                <tr>
                  <td height="24">&nbsp;</td>
                  <td>&nbsp;</td>
                  <td><div id='shuaxin'  align="right">
                      <input name="addip2" type="button" class="delete" id="addip2"    onClick="showHint_get_filelist();"   value="刷新列表">
                  </div></td>
                  <td width="100"><div align="center">
                      <input name="d_file" type="hidden" class="input_txt" id="d_file" >
                      <input name="file_id"  id="file_id" type="hidden" value="">
                      <input name="Submit2" type="submit" class="delete"  value="添加">
                  </div></td>
                  <td width="100">&nbsp;</td>
                </tr>
            </table></td>
        </tr>
      </table>
	  </div>
	  <table width="100%" height="5" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td></td>
        </tr>
      </table></td>
  </tr>
</table>
</form>
<?php include 'boot.php'; ?>
</body>
</html>
