<?php include 'head.php';?>
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<link href="css/style.css" rel="stylesheet" type="text/css">
<title><?php echo $TITLE_NAME."-";?>配置邮件发送帐户</title></head>

<?php
require 'phpmailer/class.phpmailer.php';  
if (!empty($_POST['server']) and !empty($_POST['port']) and !empty($_POST['user']) and !empty($_POST['passwd']) and !empty($_POST['address']) and !empty($_POST['username']) and !empty($_POST['test_adr'])  and !empty($_POST['test']))
{
	try {
		$mail = new PHPMailer(true); //New instance, with exceptions enabled 
		$port =	$mail->Port = $_POST['port'];
		$server = $mail->Host = $_POST['server'];
		$user =	$mail->Username = $_POST['user'];
		$passwd = $mail->Password = $_POST['passwd'];
		$username =	$mail->FromName = $_POST['username'];
		$address = $mail->From = $_POST['address'];
		$to = $_POST['test_adr'];
	
		$body = '这是一封测试邮件，如果你看到说明设置正确!';
		$mail->CharSet = "UTF-8";
 		$mail->IsSMTP();      
        $mail->SMTPAuth   = true;                  // enable SMTP authentication  
		$mail->Subject  = "测试邮件"; 
		$mail->AddReplyTo("scani@163.com","scan");  //写自己邮箱，名字 对方点回复时默认选项          
	    $mail->AddAddress($to);    
		$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test  
        $mail->WordWrap   = 80; // set word wrap  
        $mail->MsgHTML($body);  
        $mail->IsHTML(ture); // send as HTML  
		$mail->Send(); 
		$MSG = "测试邮件发送成功，请保存配置！"; 
		?>
		<script language="javascript">
   			 alert("邮件发送成功");
		</script> 
 		  <?php
    }
	 catch (phpmailerException $e) 
	 {  
	 $errstr = $e->errorMessage();
	 $errstr = str_replace("<strong>","",$errstr);
	 $err = str_replace("</strong><br />","",$errstr);
	 $MSG = "测试邮件发送失败！".$err;
	 ?> 
<input type="hidden" name="did" id="did" value="<?php echo $err;?>">
<script language="javascript" type="text/javascript">
alert(did.value);
</script>
   <?php
	 }  
}
else if (!empty($_POST['server']) and !empty($_POST['port']) and !empty($_POST['user']) and !empty($_POST['passwd']) and !empty($_POST['address']) and !empty($_POST['username']) and !empty($_POST['test_adr'])  and !empty($_POST['save']))
{
	$port = $_POST['port'];
	$server	= $_POST['server'];
	$user = $_POST['user'];
	$passwd = $_POST['passwd'];
	$username = $_POST['username'];
	$address = $_POST['address'];

	$sql_num = "select count(id) from mail_config;";
	$num = mysql_fetch_array (mysql_query ( $sql_num));
	$nu=$num[0];
	if ($nu == 0)
	{
		$sql = "insert mail_config set Name ='$user',Port =$port, Host = '$server', Passwd = '$passwd', SendName = '$username', Address = '$address';";
	}
	else
	{
		$sql = "update mail_config set Name ='$user',Port =$port, Host = '$server', Passwd = '$passwd', SendName = '$username', Address = '$address';";
	}
	$result = mysql_query($sql);
	mysql_close ();
	$MSG = "配置保存成功！";
}
else
{
	$sql_num = "select count(id) from mail_config;";
	$num_rs = getrs($sql_num);
	$nu = $num_rs[0][0];
	if ($nu == 0)
	{
		$MSG = "当前配置为空！";
	}
	else
	{
		$sql =  "select * from mail_config;";
		$rs = getrs($sql);
			$port = trim($rs[0][3]);
			$server	= trim($rs[0][2]);
			$user = trim($rs[0][1]);
			$passwd = trim($rs[0][4]);
			$username = trim($rs[0][6]);
			$address = trim($rs[0][5]);
		$MSG = "当前配置";
	}
}
    ?>
 
<body>	
   <table width="98%" height="5" border="0" align="center" cellpadding="0" cellspacing="0">
     <tr>
       <td height="3"></td>
     </tr>
   </table>
   <table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
     <tr>
       <td width="220" valign="top" class="td_clo_blue">&nbsp;</td>
       <td width="16"></td>
       <td width="1" bgcolor="#E8E8E9"></td>
       <td width="8"></td>
       <td valign="top"><table width="96%" border="0" align="center" cellpadding="0" cellspacing="0" class="td_clo_blue">
           <tr>
             <td height="33" background="image/tab_bg1.gif">&nbsp;&nbsp;&nbsp;&nbsp;<span class="title1">配置邮件发送帐户</span></td>
           </tr>
           <tr>
             <td height="8"></td>
           </tr>

           <tr>
             <td height="9"></td>
           </tr>
           <tr>
             <td><form action="" method="post" >
               <table width="96%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="td_clo_blue">
                 <tr>
                   <td><table width="100%">
                       <tr>
					   <td height="33" colspan="2" class="title1">
					     <div align="center">&gt;&gt;&gt;&nbsp;<?php echo $MSG; ?>&nbsp;&lt;&lt;&lt;</div></td>
					   </tr>
					   <tr>
                         <td width="20%">*SMTP服务器：</td>
                         <td>
<input name="server" type="text" id="server" value="<?php  echo $server; ?>" size="24">                           
输入SMTP服务器的主机名或IP，如:smtp.163.com。</td>
                       </tr>
                       <tr>
                         <td>*SMTP端口：</td>
<td><input name="port" type="text" id="port" value="<?php  echo $port; ?>" size="24">
                           SMTP服务器的端口，默认：25。</td>
                       </tr>
                       <tr>
                         <td>*SMTP用户名：</td>
                         <td><input name="user" type="text" id="user" value="<?php  echo $user; ?>" size="24">
                           登录到SMTP服务器的用户名。</td>
                       </tr>
                       <tr>
                         <td>*SMTP密码：</td>
                         <td><input name="passwd" type="password" id="passwd" value="<?php  echo $passwd; ?>" size="24">
                           登录到SMTP服务器的用户名对应的密码。</td>
                       </tr>
                       <tr>
                         <td>*发件人地址：</td>
                         <td><input name="address" type="text" id="address" value="<?php  echo $address; ?>" size="24">
                           用于发送报警邮件的邮件地址</td>
                       </tr>
                       <tr>
                         <td>*发件人名称：</td>
                         <td><input name="username" type="text" id="username" value="<?php  echo $username; ?>" size="24">
                           发件人名字</td>
                       </tr>
                       <tr>
                         <td>*测试邮件接收地址：</td>
                         <td><input name="test_adr" type="text" id="test_adr" value="yourname@163.com" size="24">
                           用于接收测试邮件。</td>
                       </tr>
                       <tr>
                         <td>&nbsp;</td>
                         <td>&nbsp;</td>
                       </tr>
                       <tr>
                         <td>&nbsp;</td>
                         <td><label>
                           <input type="submit" name="test" value="发送测试邮件">
                         </label></td>
                       </tr>
                       <tr>
                         <td>&nbsp;</td>
                         <td>&nbsp;</td>
                       </tr>
                   </table></td>
                 </tr>
               </table>
               <table width="100%" border="0" cellspacing="0" cellpadding="0">
                   <tr>
                     <td height="5" colspan="2"></td>
                   </tr>
                   <tr>
                     <td width="8%"></td>
                     <td><input name="save" type="submit" class="delete" id="save" value="保存配置"></td>
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
       </table></td>
     </tr>
   </table>

</body>
</html>
<?php include 'boot.php'; ?>