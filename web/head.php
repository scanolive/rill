<?php
include 'include/config.php';
if (empty($_SESSION['rlll_olive_scan_level']))
{
	alert_go("请先登录","login.php");
	exit;
}
else
{		
	$s_u_name = trim($_SESSION['rlll_olive_scan_username']);
	$s_u_id = trim($_SESSION['rlll_olive_scan_userid']);
	$s_u_type = trim($_SESSION['rlll_olive_scan_usertype']);
	$s_u_level = trim($_SESSION['rlll_olive_scan_level']);
	$s_u_gids = $_SESSION['rlll_olive_scan_user_gids'];
	$s_u_groups = $_SESSION['rlll_olive_scan_user_groups'];
	$s_u_users = $_SESSION['rlll_olive_scan_users_rs'];
	//$s_u_gids_sql = $_SESSION['sql_user_gids'];
	$s_u_history_pagesize = $_SESSION['history_pagesize'];
	$s_u_devinfo_pagesize = $_SESSION['devinfo_pagesize'];
	$s_u_index_pagesize = $_SESSION['index_pagesize'];
	$s_u_monstate_pagesize = $_SESSION['monstate_pagesize'];
	$s_u_monweb_pagesize = $_SESSION['monweb_pagesize'];
	$s_u_err_logs_pagesize = $_SESSION['err_logs_pagesize'];
	$s_u_bg_result_pagesize = $_SESSION['bg_result_pagesize'];
	$s_u_batchdo_pagesize = $_SESSION['batchdo_pagesize'];
	$s_u_ssh_enable = $_SESSION['ssh_enable']; 
	$s_u_ctrl_center_enable = $_SESSION['ctrl_center_enable']; 
	$s_s_server_ip = $_SESSION['python_server_ip'];
	$s_s_server_port = $_SESSION['python_server_port'];
	$s_s_upfile_dir = $_SESSION['upfile_dir'];
	$s_s_upfile_allpath	= realpath(dirname(__FILE__)).'/'.$_SESSION['upfile_dir'];
	$s_s_timeout = $_SESSION['php_timeout'];
	$SYSTEM_NAME = $_SESSION['system_name'];
	$TITLE_NAME = $SYSTEM_NAME;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=7">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<script src="script/my_function.js" type="text/javascript"></script>
<link href="css/style.css" rel="stylesheet" type="text/css">
<script type=text/javascript> if (window.screen.width < 1300) {document.write('<style type="text/css"> .tab_width  {width: 98%;}</style>');} else{document.write('<style type="text/css"> .tab_width {width: 90%;}</style>');}</script>
</head>
<body>
<table width="100%" height="80" border="0" cellpadding="0" cellspacing="0" background="image/header_bg.gif" class="myhead">
  <tr>
	<td width="21%" rowspan="2" valign="bottom">
		<table width="240" height="72" border="0" align="right" cellpadding="0" cellspacing="0" background="image/logo.png">
		   <tr>
	        <td width="32">&nbsp;</td>
    	    <td width="232"><span class="logo"><?php echo $SYSTEM_NAME; ?></span></td>
  	    </tr>
		</table>
    </td>
	<td valign="top">
		<table width="98%" border="0" align="left" cellpadding="0" cellspacing="0" class="title1">
        	<tr>
 		         <td valign="top" class="title1">
					<table width="100%" height="24" border="0" align="right" cellpadding="0" cellspacing="0">
		            <tr>
        		      <td ><div align="right">
<?php
echo "当前登录:"; 
echo $s_u_name;
echo "&nbsp;&nbsp;&nbsp;";
echo "用户组:";
echo $s_u_type;
echo "&nbsp;&nbsp;&nbsp;|&nbsp;";
if ($s_u_level == 1) 
{
	echo "<a href='sys_config_update.php'>系统配置</a>"; 
	echo "&nbsp;|&nbsp;";
	echo "<a href='mail_config.php'>邮件报警设置</a>";
	echo "&nbsp;|&nbsp;";
	echo "<a href='mon_config.php?ip=0.0.0.0'>监控阀值</a>";
	echo "&nbsp;|&nbsp;";
} 
echo '<a href="user_update.php?userid='.$s_u_id.'">个人资料</a>';
echo "&nbsp;|&nbsp;";
echo '<a href="user_config_update.php">显示设置</a>';
echo "&nbsp;|&nbsp;";
echo '<a href="update_pwd.php">修改密码</a>';
echo "&nbsp;|&nbsp;";
echo '<a href="logout.php">退出</a>'; 
?>
					</div></td>
              <td></td>
            </tr>
          </table></td>
        </tr>
        </table>
    </td>
  </tr>
  <tr>
    <td valign="bottom"><table width="98%" height="30" border="0" align="right" cellpadding="0" cellspacing="0">
      <tr class="title1">
        <td width="9" height="30" background="image/tbg_r.gif" class="title1">&nbsp;</td>
        <td background="image/tbg_m.gif" class="title1"><div align="center"><a href="index.php">首页</a></div></td>
        <td background="image/tbg_m.gif" class="title1"><div align="center"><a href="mon_state.php">状态监控</a></div></td>
        <td background="image/tbg_m.gif" class="title1"><div align="center"><a href="devinfo.php">设备信息</a></div></td>
        <td background="image/tbg_m.gif" class="title1"><div align="center"><a href="graph.php">图形分析</a></div></td>
		<td background="image/tbg_m.gif" class="title1"><div align="center"><a href="faultreport.php">故障分析</a></div></td>
        <td background="image/tbg_m.gif" class="title1"><div align="center"><a href="monweb.php">页面监控</a></div></td>
		<?php if( $s_u_level < 4)
		{
		?>
		 <td background="image/tbg_m.gif" class="title1"><div align="center"><a href="file_manage.php">文件管理</a></div></td>
		 <?php 
		}
		?>	
        <?php if( $s_u_level < 3)
		  {
		  ?>
        <td background="image/tbg_m.gif" class="title1"><div align="center"><a href="group.php">组别管理</a></div></td>
        <td background="image/tbg_m.gif" class="title1"><div align="center"><a href="define_cmd.php">命令管理</a></div></td>
		<td background="image/tbg_m.gif" class="title1"><div align="center"><a href="user_manage.php">用户管理</a></div></td>
		<td background="image/tbg_m.gif" class="title1"><div align="center"><a href="err_logs.php">系统日志</a></div></td>
		        <?php 	
		  }  ?>
		<td background="image/tbg_m.gif" class="title1"><div align="center"><a href="user_center.php">用户行为</a></div></td>
        <?php if  ($_SESSION['ssh_enable'] == "YES" and $_SESSION['ctrl_center_enable'] == "YES" and $s_u_level < 4 ) 
		  {
	?>
        
        <td background="image/tbg_m.gif" class="title1"><div align="center"><a href="batch_ips.php">批量管理</a></div></td>
	    <td background="image/tbg_m.gif" class="title1"><div align="center"><a href="bg_result.php">运行结果</a></div></td>
	    <?php 	
		  }  ?>
        <td width="9" background="image/tbg_l.gif" class="title1">&nbsp;</td>
		<td width="2%"></td>
      </tr>
    </table></td>
  </tr>
  
  <tr>
    <td height="2" colspan="2" bgcolor="#339999"></td>
  </tr>
</table>
<table width="100%" height="80" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
<DIV style="DISPLAY:none"   id="goTopBtn"><IMG src="image/top.png" border=0></DIV>
  <SCRIPT type=text/javascript>goTopEx();</SCRIPT>
</body>
</html>

