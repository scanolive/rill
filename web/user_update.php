<?php
include 'head.php';
if ($_GET['userid'] == "")
{
	alert_go("请指定用户","user_manage.php");
}
else
{
	$userid = trim($_GET ['userid']);
	if (( $s_u_level > 2 ) and ( trim($userid) !== trim($s_u_id )))
	{
		alert_go("别调皮哦！","index.php");
	}
	$groupall = "select GroupName,id from ipgroup;";
	$groupall_rs = getrs($groupall);
	$group_sql = "select GroupName,ipgroup.id  from userofgroup,ipgroup where ipgroup.id = userofgroup.Gid and Uid=".$userid;
	$group_rs = getrs($group_sql);

	$sql = "select UserName,UserMail,UserMobile,UserType,NoticeLevel,DutyDate,DutyTime from users where users.id = ".$userid;
	$rs = getrs($sql);
	$dutystart = substr($rs[0][6],0,2);
	$dutyend = substr($rs[0][6],3,2);
}

?>

<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=7">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $TITLE_NAME."-";?>更新用户信息</title>
<link href="css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<table width="98%" height="5" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="3"></td>
  </tr>
</table>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top"><form action="save_user_update.php" method="post" name="form1" >
      <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="td_clo_blue">
        <tr>
          <td height="33" colspan="2" background="image/tab_bg1.gif">&nbsp;&nbsp;&nbsp;&nbsp;<span class="title1">&nbsp;</span><span class="title1">更新用户信息</span></td>
        </tr>
        <tr>
          <td height="38" colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2"><table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="td_clo_blue">
            <tr>
              <td height="10" colspan="3"><input name="userid" type="hidden" id="userid" value="<?php echo $userid;?>">                </td>
              </tr>
            <tr>
              <td width="30" height="28">&nbsp;</td>
              <td>用户名：</td>
              <td><input name="username" type="text" id="username" value="<?php echo $rs[0][0]; ?>" size="32" readonly="readonly"/></td>
            </tr>
            <tr>
              <td height="28">&nbsp;</td>
              <td width="300">Email：</td>
              <td><input name="usermail" type="text" id="usermail" value="<?php echo $rs[0][1]; ?>" size="32" ></td>
            </tr>
            <tr>
              <td height="28">&nbsp;</td>
              <td>电话：</td>
              <td><input name="usermobile" type="text" id="usermobile" value="<?php echo $rs[0][2]; ?>" size="32" ></td>
            </tr>           
			<?php if ($s_u_level < 3 ) {?>	
			 <tr>
              <td height="28">&nbsp;</td>
              <td>报警方式：</td>
              <td><select name="notice" id="notice">
			  <option selected="selected" value="<?php echo $rs[0][4]; ?>"><?php 
			  if ( $rs[0][4] == 4)
			  {
			  	echo "邮件+短信";
			  }
			  else if ( $rs[0][4] == 3)
			  {
			  	echo "短信报警";
			  }
			  else if ( $rs[0][4] == 2)
			  {
			  	echo "邮件报警";
			  }
			  else if ( $rs[0][4] == 1)
			  {
			  	echo "不接受报警";
			  }			  
			   ?></option>
                <option value="4">邮件+短信</option>
                <option value="2">邮件报警</option>
                <option value="3">短信报警</option>
                <option value="1">不接受报警</option>
                            </select></td>
            </tr>
			
			            <tr>
              <td height="10">&nbsp;</td>
              <td>工作时间：</td>
              <td>
 <input name="dutydate[]" type="checkbox" class="radio" id="dutydate[]"  value="0" <?php if (strpos($rs[0][5],'0')) echo 'checked'; ?> />周一 
 <input name="dutydate[]" type="checkbox" class="radio" id="dutydate[]"  value="1" <?php if (strpos($rs[0][5],'1')) echo 'checked'; ?> />周二 
 <input name="dutydate[]" type="checkbox" class="radio" id="dutydate[]"  value="2" <?php if (strpos($rs[0][5],'2')) echo 'checked'; ?> />周三 		
 <input name="dutydate[]" type="checkbox" class="radio" id="dutydate[]"  value="3" <?php if (strpos($rs[0][5],'3')) echo 'checked'; ?> />周四 
 <input name="dutydate[]" type="checkbox" class="radio" id="dutydate[]"  value="4" <?php if (strpos($rs[0][5],'4')) echo 'checked'; ?> />周五 
 <input name="dutydate[]" type="checkbox" class="radio" id="dutydate[]"  value="5" <?php if (strpos($rs[0][5],'5')) echo 'checked'; ?> />周六 
 <input name="dutydate[]" type="checkbox" class="radio" id="dutydate[]"  value="6" <?php if (strpos($rs[0][5],'6')) echo 'checked';?> />周日
		&nbsp;&nbsp;&nbsp;&nbsp;
		<select name="dutystart" id="dutystart">
                  <?php  for ($i=0;$i<24;$i++)
				{	if ($i < 10)
					$i = "0".$i;
					if ($i == $dutystart)
					{
						echo '<option selected="selected"  value="'.$i.'">'.$i.'</option>';
					}
					else
					{
						echo '<option value="'.$i.'">'.$i.'</option>';
					}
				}
				  ?>
                    </select>
                至
                <select name="dutyend" id="dutyend">
                <?php  for ($i=24;$i>0;$i--)
				{	if ($i < 10)
					$i = "0".$i;
					if ($i == $dutyend)
					{
						echo '<option selected="selected"  value="'.$i.'">'.$i.'</option>';
					}
					else
					{
						echo '<option value="'.$i.'">'.$i.'</option>';
					}
				}
				  ?>
                    </select></td>
            </tr>	
			
			<?php } if ($s_u_level < 2 ) {?>			
	           
			 <tr>
              <td height="28">&nbsp;</td>
              <td>启用控制中心(有风险谨慎开启)</td>
              <td><input name="ctrl_center_enable" type="checkbox" class="radio" id="ctrl_center_enable" value="YES" <?php if ($_SESSION['ctrl_center_enable'] == "YES") echo "checked"; ?>>              </td>
            </tr>		
			 <tr>
              <td height="28">&nbsp;</td>
              <td>启用WebShell(启用控制中心才生效)</td>
              <td><input name="ssh_enable" type="checkbox" class="radio" id="ssh_enable" value="YES" <?php if ($_SESSION['ssh_enable'] == "YES") echo "checked"; ?>>
              </td>
            </tr>		
			
<?php }
			

	if ($s_u_level < 2 and $rs[0][3] != 'root')
	{ 
    ?>
            <tr>
              <td height="28">&nbsp;</td>
              <td>用户类型：</td>
              <td><label>
                <select name="usertype" id="usertype" onChange="showHint_get_users(this.value,userid.value)">
                  <option value="<?php echo $rs[0][3]; ?>"><?php echo $rs[0][3]; ?></option>
                  <option value="monitor">monitor</option>
                  <option value="user">user</option>
                  <option value="admin">admin</option>
                </select>
              </label></td>
            </tr>
			<?php 
	}
	else
	{
		?>  
	<input name="usertype" type="hidden" id="usertype" value="<?php echo $rs[0][3];?>">
	<?php }
					
			

if ($s_u_level < 3 )
{
?>  
			<tr>
              <td height="28">&nbsp;</td>
              <td>管理项目：</td>
              <td><div id="myDiv">
		<?php 
	if ( $rs[0][3] == "user" )
	{
		for ( $i=0;$i<count($groupall_rs);$i++ )
		{ ?>
    		 <input name="sgroup[]" type="checkbox" class="radio" id="sgroup[]"  value="<?php echo $groupall_rs[$i][1];?>" <?php 
				for ( $k=0;$k<count($group_rs);$k++ )
				{
					if ($groupall_rs[$i][0] == $group_rs[$k][0])
					{ 
						echo "checked";
					} 
				} ?> />
				<?php echo $groupall_rs[$i][0];echo "&nbsp;&nbsp;&nbsp;";
				if (($i+1)%5 == 0)
				echo "<br>";				
			}
		}
	else if( $rs[0][3] == "admin" )
	{
		echo "admin用户拥有所有组的权限";
		
	}
	else if( $rs[0][3] == "root" )
	{
		echo "root超级用户拥有所有组的权限";
	}
		?>
			</div></td>
            </tr>
<?php 
}
?>
            <tr>
              <td height="10">&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td height="10" colspan="2"></td>
        </tr>
        <tr>
          <td width="80%"></td>
          <td><input name="Submit" type="submit" onClick="javascript:return (checkMobile('usermobile') && checkMail('usermail'))" class="delete" value="保存配置"></td>
        </tr>
        <tr>
          <td height="10" colspan="2"></td>
        </tr>
      </table>
    </form></td>
  </tr>
</table>
<?php include 'boot.php'; ?>
</body>
</html>
