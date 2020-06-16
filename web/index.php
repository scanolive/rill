<?php
include 'head.php';
$warntype_sql = "select Type  from alarms GROUP BY Type";
$warntype_rs = getrs($warntype_sql);
if (!empty($_REQUEST['warntype']) and ($_REQUEST['warntype'] !== "All"))
{
	$warntype = $_REQUEST['warntype'];
	$type_sql = " and Type = '$warntype' ";
}
else
{
	$warntype = "All";
	$type_sql = "";
}

if (isset($_REQUEST['isbeok']) and ($_REQUEST['isbeok'] !== "All"))
{
	$isbeok = $_REQUEST['isbeok'];
	$isbeok_sql = " and IsBeOk = '$isbeok' ";
}
else
{
	$isbeok = "All";
	$isbeok_sql = "";
}

if (isset($_REQUEST['isalarm']) and ($_REQUEST['isalarm'] !== "All"))
{
	$isalarm = $_REQUEST['isalarm'];
	$isalarm_sql = " and IsAlarm = '$isalarm' ";
}
else
{
	$isalarm = "All";
	$isalarm_sql = "";
}

$grps = $s_u_groups;
$errgids = "";
for ($i=0;$i<count($grps);$i++)
	{
		$errgids .= $grps[$i][1].",";
	}	
	$errgids = "(".$errgids.$grps[0][1].")";	
$gids ="";
if (!empty($_REQUEST['group']) and ($_REQUEST['group'] !== "All"))
{
	$group = $_REQUEST['group'];
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

if (!empty($_REQUEST['ip'])  and $_REQUEST['ip'] !== "All")
{
	$ip = ($_REQUEST['ip']);
	$ipsql = " and if((Type='monweb')=0,(select Ip from ipinfo where ipinfo.id=alarms.Ipid),(select MonUrl from monweb where monweb.id=alarms.Ipid))	= '$ip' ";
}
else
{
 	$ip = "All";
	$ipsql = "";
}

$date_sle = array(
			'today'        => "今日",
			'yesterday'    => "昨日",
			'lastweek'     => "一周",
			'last2week'    => "两周",
			'all'   	   => "所有",
		);

$date = array(
			'today'        => date("Y-m-d H:i:s" ,strtotime('today')),
			'yesterday'    => date("Y-m-d H:i:s" ,strtotime('-1 day')),
			'lastweek'     => date("Y-m-d H:i:s" ,strtotime('-7 day')),
			'last2week'    => date("Y-m-d H:i:s" ,strtotime('-15 day')),
			'all'   	   => date("Y-m-d H:i:s" ,strtotime('-15000 day'))
		);
if (isset($_REQUEST['mydate']) and !empty($_REQUEST['mydate']) and $_REQUEST['mydate']!="define" and $_REQUEST['mydate']!="define2")
{
	$getdate = $_REQUEST['mydate'];
}
else
{
	$getdate = "today";
}		
		
if (!empty($_REQUEST['starttime']) and !empty($_REQUEST['endtime']) and (strtotime($_REQUEST['endtime']) >= strtotime($_REQUEST['starttime'])))
{
	$s_time = $starttime = $_REQUEST['starttime'];
	$e_time = $endtime = $_REQUEST['endtime'];
}
else if (!empty($_REQUEST['d_starttime']) and !empty($_REQUEST['d_endtime']) and (strtotime($_REQUEST['d_endtime']) >= strtotime($_REQUEST['d_starttime'])))
{
	$s_time = $starttime = $_REQUEST['d_starttime'];
	$e_time = $endtime = $_REQUEST['d_endtime'];
}
else if (!empty($_GET['stime']) and !empty($_GET['etime']) and (strtotime($_GET['etime']) >= strtotime($_GET['stime'])))
{
	$starttime = $_GET['stime'];
	$endtime = $_GET['etime'];
}
else if (isset($_REQUEST['mydate']) and !empty($_REQUEST['mydate']) and $_REQUEST['mydate']!="define" and $_REQUEST['mydate']!="define2")
{
	$getdate = $_REQUEST['mydate'];
	$starttime = $date[$getdate];
	$endtime = date("Y-m-d H:i:s" ,time());
}
else
{
	$getdate = "today";
	$starttime = date("Y-m-d H:i:s" ,strtotime('today'));
	$endtime = date("Y-m-d H:i:s" ,time());
}

$stime = str_split($starttime,10);
$etime = str_split($endtime,10);

if ( (!empty($_REQUEST ['delete_down']) or !empty($_REQUEST ['delete_top']) ) and !empty($_REQUEST ['delid']) and $s_u_level == 1)
{
	$delids = $_REQUEST ['delid'];
	$del_num = count($delids);
	$sql_del_id = '';
	foreach ( $delids as $delid)
	{
		$sql_del_id .= $delid.",";
	}
	$sql_del_id = "(".$sql_del_id.$delids[0].")";

	$del_sql = "delete from alarms where id in ".$sql_del_id;
	do_sql($del_sql);
	save_do($s_u_id,$s_u_level,"用户".$s_u_name." 删除".$del_num."条报警信息成功!");
	echo '<script language="javascript">showHint_socket("Sync_Db_Alarms","OLIVE_SERVER","'.$_SESSION['verify_str'].'") </script>';
	echo "<script charset='UTF-8' language='javascript'>";
	echo "alert('";
	echo "删除成功";
	echo "');";
	echo "</script>";
}
$sql_err_num = "select count(ipinfo.Ip) from ipinfo  Left Join devinfo ON devinfo.Ipid=ipinfo.id Left Join ipgroup ON ipgroup.id = ipinfo.GroupId where ipinfo.ip != '0.0.0.0' and enable=1  and ipinfo.GroupId in $errgids  and  (ClientStatus=0 or (select count(id) from alarms where alarms.ipid=ipinfo.id and IsBeOk=0 and Type!='monweb')>0)"; 
$err_num_all = getrs($sql_err_num);
$err_num_all = $err_num_all[0][0];

$sql_alarms_num = "select count(*) from alarms left join ipinfo on ipinfo.id=Ipid where IsBeOk=0 and IsAlarm=1  and ip!='0.0.0.0'";
$sql_monweb_alarms_num = "select count(*) from alarms where Type='monweb' and IsBeOk=0 and IsAlarm=1";
$alarms_num = getrs($sql_alarms_num)[0][0];
$alarms_monweb_num = getrs($sql_monweb_alarms_num)[0][0];

$sql_alarms = "select alarms.Msg,GroupName,Type,IsSend,IsBeOk,right(alarms.CreateTime,14),right(alarms.UpdateTime,14),Note,IsAlarm,alarms.id,Gid from alarms Left Join ipgroup ON ipgroup.id = Gid  where alarms.CreateTime >= '$starttime' and alarms.CreateTime <= '$endtime'  $isbeok_sql $isalarm_sql $type_sql $ipsql and  Gid in  $gids order by alarms.id desc";
$sql_alarms_num = "select count(alarms.id) from alarms  where alarms.CreateTime >= '$starttime' and alarms.CreateTime <= '$endtime'   $isbeok_sql $isalarm_sql $type_sql $ipsql and Gid in $gids order by alarms.id desc";


$ip_sql = "select DISTINCT ipinfo.Ip from alarms left join ipinfo on alarms.ipid=ipinfo.id where alarms.Gid in $gids and alarms.Type != 'monweb' and ipinfo.Ip!='0.0.0.0'";
$ip_rs = getrs($ip_sql);

$sql_users = "select LoginNum,LastLoginTime from users where id =".$s_u_id;
$users_rs = getrs($sql_users);


$pagesize = $_SESSION['index_pagesize'];
$num_all = getrs($sql_alarms_num);
$num_all = $num_all[0][0];
$pages=ceil($num_all/$pagesize);
if (!empty($_REQUEST['page']))
{
	$page=intval($_REQUEST['page']);
	if ( $page > $pages)
	{
		$page = $pages;
	}	
	if ( $page == 0)
	{
		$page = 1;
	}
}
else if (!empty($_REQUEST['page_del']))
{
	$page=intval($_REQUEST['page_del']);
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
$sql_alarms = $sql_alarms." limit ".$offset.",".$pagesize;
$alarms_rs = getrs($sql_alarms);
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=7">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $TITLE_NAME."-";?>首页</title>
<link href="css/style.css" rel="stylesheet" type="text/css">
<script src="script/selectdate.js" type="text/javascript"></script>
</head>
<body><form action="" method="get" name="form1" >
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="myhead">
   <tr>
	 <td>
		<table width="100%" height="5" border="0" cellpadding="0" cellspacing="0">
         <tr>
           <td></td>
         </tr>
	    </table>
       <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="td_no_down">
         <tr  >
           <td height="33" background="image/tab_bg1.gif"  >&nbsp;&nbsp;&nbsp;&nbsp;<span class="title1">&nbsp;首页</span></td>
         </tr>
		<tr><td height="5"></td></tr>
         <tr>
		   <td height="33" >
			<div align="center">
             <table width="98%" height="32" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFCC" class="nostyle">
               <tr>
                  <td>
                   <?php if ($s_u_level == 1){?>
                  <div align="left">
                	 <table border="0" cellpadding="0" cellspacing="0">
                        <tr>
						   <td width="60">
								<div align="center">
      			                     <input name="delete_down" type="submit" class="button1" id="delete_down" onClick="javascript:return p_del()" value="删除">
							   </div>
						   </td>
		                   <td>
								<div align="left">
                		            <div align="left">
										<a href="javascript:select()"> 全选</a>｜
										<a href="javascript:fanselect()">反选</a>｜
										<a href="javascript:noselect()">全不选</a>&nbsp;
									</div>
		                        </div>
							</td>
		               </tr>
		             </table>
		           </div>
					  <?php } ?>
				</td>
				<td>
					<?php 
						echo "用户:".$s_u_name;
						echo  "&nbsp;&nbsp;&nbsp;共登录".$users_rs[0][0]."次&nbsp;&nbsp;&nbsp;&nbsp;";
						echo  "最后登录:".$users_rs[0][1];
						echo "&nbsp;&nbsp;&nbsp;&nbsp;登录的IP地址为:".getip();
                    ?>
				</td>
				 <td>
					&nbsp;
				</td>
				 <td>
						<?php echo "当前报警总数:".$alarms_num ; ?>
				</td>
				 <td>
					<?php
						if ($err_num_all == 0)
							echo "当前异常服务器数:".$err_num_all;
						else
							echo '<a href="mon_state.php?status=Err"><span class="warn">'."当前异常服务器数:".$err_num_all.'</span></a>';
					?>		
				</td>  
				 <td>
					<?php
						if ($alarms_monweb_num == 0)
							echo "当前异常页面数:".$alarms_monweb_num;
						else
							echo '<a href="monweb.php?isok=Err"><span class="warn">'."当前异常页面数:".$alarms_monweb_num.'</span></a>';
					?>
					</td>  
                 </tr>
             </table>
           </div></td>
         </tr>
         <tr>
           <td ></td>
         </tr>
         <tr>
           <td ><table width="98%" height="34" border="0" align="center" cellpadding="0" cellspacing="0">
             <tr>
			   <td><div align="left">组别
					<?php	 include 'include/group_select.php'; 
				echo		"IP ";
				echo	'<select name="ip" class="anniu" id="ip" width="50" onChange="this.form.submit()">';
	echo  "<option  value='".$ip."'>".$ip."</option>";  
if ($ip !== "All")
echo "<option value='All'>All</option>";
//if ($group !== "All")
//{
	for( $i=0;$i<count($ip_rs);$i++ )
	{
		echo  "<option value=";echo $ip_rs[$i][0];echo  ">"; echo $ip_rs[$i][0]; echo "</option>";
	}
//}	
?>
                 </select>
                 <?php	
	echo "类型
			<select name='warntype' class='anniu' id='warntype' onChange='this.form.submit()'>";
	echo "<option value=";
	echo  "All";
	echo  ">";
	echo  "All";; 
	echo "</option>";		
for( $i=0;$i<count($warntype_rs);$i++ ) 
 {

 	if ($warntype_rs[$i][0] == $warntype )
	{
		echo "<option selected value=";
		echo  $warntype_rs[$i][0];
		echo  ">";
		echo $warntype_rs[$i][0]; 
		echo "</option>";
	}
	else
	{
		echo "<option  value=";
		echo  $warntype_rs[$i][0];
		echo  ">";
		echo $warntype_rs[$i][0]; 
		echo "</option>";		
	}
 }
echo '</select> 
		恢复
	<select name="isbeok" id="isbeok"  onChange="this.form.submit()">';
if ($isbeok == 'All')
	{
		echo '<option value="All" selected>All</option>';
		echo '<option value="1">已恢复</option>';
		echo '<option value="0">未恢复</option>';
	}
	elseif  ($isbeok == 1)
	{
		echo '<option value="1" selected>已恢复</option>';
		echo '<option value="All">All</option>';
		echo '<option value="0">未恢复</option>';
	}
	else
	{
		echo '<option value="0" selected>未恢复</option>';
		echo '<option value="All">All</option>';
		echo '<option value="1">已恢复</option>';
	}	
echo '</select>
		警报
	<select name="isalarm" id="isalarm"  onChange="this.form.submit()">';
	if ($isalarm == 'All')
	{
		echo '<option value="All" selected>All</option>';
		echo '<option value="1">警报</option>';
		echo '<option value="0">恢复</option>';
	}
	elseif  ($isalarm == 1)
	{
		echo '<option value="1" selected>警报</option>';
		echo '<option value="All">All</option>';
		echo '<option value="0">恢复</option>';
	}
	else
	{
		echo '<option value="0" selected>恢复</option>';
		echo '<option value="All">All</option>';
		echo '<option value="1">警报</option>';
	}	
echo '</select>
	时间
	<select name="mydate" id="mydate" onChange="if (value=='."'define'". "||value=='define2') showhidediv('msg'); else this.form.submit()".';">';
    foreach ($date_sle as $key => $value)
   {
  		if ($getdate == $key)
			echo '<option value="'.$key.'" selected>'.$value.'</option>';
		else
			echo '<option value="'.$key.'">'.$value.'</option>';
	}  
    echo   "</select>";
    echo   '<input name="page_del" type="hidden" id="page_del" value="'.$page.'">';
	echo   '<input name="d_endtime" type="hidden" id="d_endtime" value="';
			if (isset($e_time)) echo $e_time; 
	echo   '<input name="d_starttime" type="hidden" id="d_starttime" value="';
			if (isset($s_time)) echo $s_time; 
	echo   '</div></td>';
    echo   '<td><div align="center">';
	if ($group=="All")
			echo "&nbsp;&nbsp; 所有组-";
	else 
			echo $group_name."组-"; 
	if ($ip == "All") 
			echo "所有IP&nbsp;"; 
	else echo $ip."&nbsp;"; 
	if ($getdate == "all") 
			echo "所有时间"; 
	else 
	{
			if (substr($stime[0],0,5)==substr($etime[0],0,5)) 
					echo substr($stime[0],5)."至".substr($etime[0],5); 
			else  
					echo $stime[0]."至".$etime[0];
					echo '</div></td><td>';
	}
echo "&nbsp;共".$num_all."条";
echo "&nbsp;&nbsp;";
$page_url = "index.php?group=".$group."&isalarm=".$isalarm."&isbeok=".$isbeok."&stime=".$starttime."&etime=".$endtime."&warntype=".$warntype."&ip=".$ip."&mydate=".$getdate."&page=";	
$url_now = $page_url.$page;		
echo '</td><td><div align="right">';
echo "<a href = '".$page_url."1'>首页</a>";
echo "&nbsp;";
if ($page !== 1)
{
	echo "<a href = '".$page_url.($page-1)."'>上一页</a>";
}
else
{
	echo "<a href = '".$page_url.$pages."'>上一页</a>";
}
echo "&nbsp;";


if ($pages	>= 10)
{
	if  (5 >= $page)
	{			
		for ($i=1;$i<=10;$i++)	
		{	
			if ($i == $page)
			{
				echo "<strong>".$i."&nbsp;</strong>";
			}
			else
			{
				echo "<a href = '".$page_url.$i."'>".$i."</a>&nbsp;";
			}
		}
				
	}
	else if  ($page <= $pages)
		{
			for ($i=$page-5;$i<$page+5;$i++)
			{
				if  ( $pages >= $i)
				{
					if ($i == $page)
					{
						echo "<strong>".$i."&nbsp;</strong>";
					}
					else
					{
						echo "<a href = '".$page_url.$i."'>".$i."</a>&nbsp;";
					}
				}
			}		
		}	
}
else
{
	for ($i=1;$i<=$pages;$i++)	
	{
		if ($i == $page)
		{
			echo "<strong>".$i."&nbsp;</strong>";
		}
		else
		{
			echo "<a href = '".$page_url.$i."'>".$i."</a>&nbsp;";
		}	
	}	
}

							
	
	if ($page < $pages )
	{	
		echo "<a href = '".$page_url.($page+1)."'>下一页</a>";
	}else
	{
		echo "<a href = '".$page_url."1'>下一页</a>";
	}
	
	echo "&nbsp;";
	echo "<a href = '".$page_url.$pages."'>尾页</a>";
	echo "&nbsp;";
	echo "共".$pages."页&nbsp;&nbsp;";
			
	echo "<select name='page' class='anniu' id='page' onChange='this.form.submit()'>";
	for ($i=1;$i<$pages+1;$i++)
	{	
		if ($i==$page)
			echo '<option value="'.$i.'"  selected>'.$i.'</option>';
		else
			echo '<option value="'.$i.'">'.$i.'</option>';
		
	}
	echo '</select>';	
?>
                 </div></td>
             </tr>
           </table></td>
         </tr>
       </table></td>
   </tr>
 </table>
 <table width="100%" height="110" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top">&nbsp;</td>
  </tr>
</table>
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" class="td_clo_blue">
  <tr>
    <td height="10"><div align="center">
      <table width="98%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC" class="tab_hover_bg_w">
        <tr>
          <?php	  if ($s_u_level == 1){?>
          <td width="24" height="24" class="Ptable">选择</td>
          <?php	 }?>
          <td height="24" class="Ptable">NUM</td>
          <td class="Ptable">消息内容</td>
          <td class="Ptable">所属组</td>
          <td class="Ptable">类型</td>
          <td width="24" class="Ptable">发送</td>
          <td width="24" class="Ptable">恢复</td>
          <td width="120" class="Ptable">报警时间</td>
          <td width="120" class="Ptable">恢复时间</td>
          <td width="150" class="Ptable">备注</td>
        </tr>
        <?php  
for( $i=0;$i<count($alarms_rs);$i++ )
{ 
	echo "<tr>";
	if ($s_u_level == 1)	
	{
	    echo '<td><input name="delid[]" type="checkbox" id="delid[]" value=".$alarms_rs[$i][9]."></td>';
	}
	echo "<td height=18 class=Ptable>";	
	echo $i+1+$pagesize*($page-1);
	echo "</td>";
	for( $j=0;$j<count($alarms_rs[0])/2-3;$j++ )
		 if ($j == 3 or $j == 4)
		{
			if ($alarms_rs[$i][$j] == 1)
			{
				echo "<td height=18 class=Ptable>";	
				echo "YES";
				echo "</td>";	
			}	
			else 
			{
				echo "<td height=18 class=Ptable>";	
				echo "NO";
				echo "</td>";	
			}
		}
		elseif ($j == 6)
		{
			if ($alarms_rs[$i][8]==0)
			{
				echo "<td height=18 class=Ptable>";	
				echo "此条为恢复信息";
				echo "</td>";
			}
			elseif  ($alarms_rs[$i][4]==0)
			{	
				echo "<td height=18 class=Ptable>";	
				echo '<span class="warn">此条警报仍未恢复</span>' ;
				echo "</td>";	
			}	
			else
			{
				echo "<td height=18 class=Ptable>";	
				echo $alarms_rs[$i][$j];
				echo "</td>";			
			}
		}
		elseif ($j == 7)
		{
			if ($alarms_rs[$i][8]==0)
			{
				echo "<td height=18 class=Ptable>";	
				echo "None";
				echo "</td>";
			}		
			elseif ($alarms_rs[$i][7] == "" and $alarms_rs[$i][8]==1)
			{
				echo "<td height=18 class=Ptable>";	
				echo '<a href="alarms_note.php?id='.$alarms_rs[$i][9]."&gid=".$alarms_rs[$i][10].'&url='.str_replace("&","!@!", $url_now).'">备注</a';
				echo "</td>";
			}

			else
			{
				echo "<td height=18 class=Ptable>";	
				echo '<a href="alarms_note.php?id='.$alarms_rs[$i][9]."&gid=".$alarms_rs[$i][10].'&url='.str_replace("&","!@!", $url_now).'">'.$alarms_rs[$i][7].'</a>';
				echo "</td>";
			}			
		}
		else	
		{
			echo "<td height=18 class=Ptable>";	
			echo $alarms_rs[$i][$j];
			echo "</td>";
		}
	echo "</tr>";
}
?>
        </table>
      </div></td>
  </tr>
  <tr>
    <td height="10"></td>
  </tr>
</table>
</form>
<?php include 'boot.php'; ?>
</body>
</html>
