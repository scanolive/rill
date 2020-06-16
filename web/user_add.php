<?php
include 'head.php';
if ( $s_u_level > 2 ) 
{
	alert_go("别调皮哦！","index.php");
}
$groupall = "select GroupName,id from ipgroup;";
$groupall_rs = getrs($groupall);
 
?>

<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=7">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $TITLE_NAME."-";?>添加用户</title>
<link href="css/style.css" rel="stylesheet" type="text/css" >
<style type="text/css">
<!--
.STYLE1 {font-size: 14px}
-->
</style>
</head>
<body>
<table width="98%" height="5" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="3"></td>
  </tr>
</table>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top"><form action="save_user_add.php" method="post" name="form" id="form" >
      <table  border="0" align="center" cellpadding="0" cellspacing="0" class="td_clo_blue">
        <tr>
          <td height="33" colspan="2" background="image/tab_bg1.gif">&nbsp;&nbsp;&nbsp;&nbsp;<span class="title1">添加用户</span></td>
        </tr>
        <tr>
          <td height="38" colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2"><table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" class="td_clo_blue">
            <tr>
              <td height="10" colspan="3">&nbsp;</td>
            </tr>
            <tr>
              <td width="10%" height="28">&nbsp;</td>
              <td>用户名：</td>
              <td><input name="username" type="text" id="username" size="32" > 
                <span class="warn STYLE1">* </span></td>
            </tr>
            <tr>
              <td height="28">&nbsp;</td>
              <td width="20%">密码：</td>
              <td><input name="password" type="password" id="password" size="32" >
                <span class="warn STYLE1">* </span></td>
            </tr>
            <tr>
              <td height="28">&nbsp;</td>
              <td>Email：</td>
              <td><input name="usermail" type="text" id="usermail" size="32" >
                <span class="warn STYLE1">* </span></td>
            </tr>
			<tr>
              <td height="28">&nbsp;</td>
              <td>电话：</td>
              <td><input name="usermobile" type="text" id="usermobile" size="32" >
                <span class="warn STYLE1">* </span></td>
			</tr>
            <tr>
              <td height="28">&nbsp;</td>
              <td>报警方式：</td>
              <td><select name="notice" id="notice">
                <option value="4" selected>邮件+短信</option>
                <option value="2">邮件报警</option>
                <option value="3">短信报警</option>
                <option value="1">不接受报警</option>
              </select></td>
            </tr>
			            <tr>
              <td height="10">&nbsp;</td>
              <td>工作时间：</td>
              <td>
                <input name="dutydate[]" type="checkbox" class="radio" id="dutydate[]"  value="0" checked />周一 
                <input name="dutydate[]" type="checkbox" class="radio" id="dutydate[]"  value="1" checked />周二 
                <input name="dutydate[]" type="checkbox" class="radio" id="dutydate[]"  value="2" checked />周三 		
                <input name="dutydate[]" type="checkbox" class="radio" id="dutydate[]"  value="3" checked />周四 
                <input name="dutydate[]" type="checkbox" class="radio" id="dutydate[]"  value="4" checked />周五 
                <input name="dutydate[]" type="checkbox" class="radio" id="dutydate[]"  value="5" />周六 
                <input name="dutydate[]" type="checkbox" class="radio" id="dutydate[]"  value="6" />周日
		&nbsp;&nbsp;&nbsp;&nbsp;
		<select name="dutystart" id="dutystart">
                  <?php  for ($i=0;$i<24;$i++)
				{	if ($i < 10)
					$i = "0".$i;
					echo '<option value="'.$i.'">'.$i.'</option>';
				}
				  ?>
                    </select>
                至
                <select name="dutyend" id="dutyend">
                 <?php  for ($i=24;$i>0;$i--)
				{	if ($i < 10)
					$i = "0".$i;
					echo '<option value="'.$i.'">'.$i.'</option>';
				}
				  ?>
                    </select></td>
            </tr>		
           
            <?php if ($s_u_level < 3 ){ ?>
            <tr>
              <td height="28">&nbsp;</td>
              <td>用户类型：</td>
              <td><label>
                <select name="usertype" id="usertype" onChange="showHint_get_users(this.value)">
				 <option value="monitor" selected>monitor</option>
				 <option value="user" >user</option>
				<?php  if ( $s_u_level == 1 ) {?>
                  <option value="admin">admin</option>
				  <?php }?>
                </select>
              </label></td>
            </tr>
            <?php }
			else {
			?>
			<input name="usertype" type="hidden" id="usertype" value="user">
		<?php }
			
			
		?>

			 <?php if ($s_u_level < 3 ){?>
            <tr>
              <td height="28">&nbsp;</td>
              <td>管理项目</td>
              <td><div id="myDiv"><?php for ( $i=0;$i<count($groupall_rs);$i++ )
	{ ?>
		<input name="sgroup[]" type="checkbox" class="radio" id="sgroup[]"  value="<?php echo $groupall_rs[$i][1];?>" /><?php	
		echo $groupall_rs[$i][0];echo "&nbsp;&nbsp;&nbsp;";
	}
	?></div></td>
            </tr>
            <?php }?>
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
          <td width="80%">&nbsp;</td>
          <td><input name="Submit" onClick="javascript:return (checkMobile('usermobile') && checkMail('usermail'))"   type="submit" class="delete" value="提交保存" /></td>
        </tr>
        <tr>
          <td height="10" colspan="2"></td>
        </tr>
      </table>
    </form></td>
  </tr>
</table>
<?php 
include 'boot.php';
?>
</body>
</html>
