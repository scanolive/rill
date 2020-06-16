<?php
include 'head.php';
$date = array(
			'today'        => date("Y-m-d H:i:s" ,strtotime('today')),
			'yesterday'    => date("Y-m-d H:i:s" ,strtotime('-1 day')),
			'lastweek'     => date("Y-m-d H:i:s" ,strtotime('-7 day')),
			'last2week'    => date("Y-m-d H:i:s" ,strtotime('-15 day')),
			'all'   	   => date("Y-m-d H:i:s" ,strtotime('-15000 day'))
		);

$s_time = "";   
$e_time = "";
$getdate = "";
if (!empty($_GET['starttime']) and !empty($_GET['endtime']) and (strtotime($_GET['endtime']) >= strtotime($_GET['starttime'])))
{
	$s_time = $starttime = $_GET['starttime'];
	$e_time = $endtime = $_GET['endtime'];
}
else if (!empty($_GET['d_starttime']) and !empty($_GET['d_endtime']) and (strtotime($_GET['d_endtime']) >= strtotime($_GET['d_starttime'])))
{
	$s_time = $starttime = $_GET['d_starttime'];
	$e_time = $endtime = $_GET['d_endtime'];
}
else if (!empty($_GET['stime']) and !empty($_GET['etime']) and (strtotime($_GET['etime']) >= strtotime($_GET['stime'])))
{
	$starttime = $_GET['stime'];
	$endtime = $_GET['etime'];
}
else if (isset($_GET['date']) and !empty($_GET['date']))
{
	$getdate = $_GET['date'];
	$starttime = $date[$getdate];
	$endtime = date("Y-m-d H:i:s" ,time());
}
else
{
	$starttime = date("Y-m-d H:i:s",time()-7*24*3600);
	$endtime = date("Y-m-d H:i:s" ,time());
}


$stime = str_split($starttime,10);
$etime = str_split($endtime,10);

if ( !empty($_GET ['delete']) and !empty($_GET ['delid']) and $s_u_level == 1)
{
	$delids = $_GET ['delid'];
	$del_num = count($delids);
	foreach ( $delids as $delid)
	{
		$sql_del_id .= $delid.",";
	}
	$sql_del_id = "(".$sql_del_id.$delids[0].")";
	$del_sql = "delete from history where id in ".$sql_del_id;
	do_sql($del_sql);
	save_do($s_u_id,$s_u_level,"用户".$s_u_name." 删除".$del_num."条用户操作记录成功!");
	echo "<script charset='UTF-8' language='javascript'>";
	echo "alert('";
	echo "删除成功";
	echo "');";
	echo "</script>";
}
if (empty($_GET ['userid']) and !isset($_GET ['userid']) )
{
	$uid = $s_u_id;
}
else
{
	$uid = $_GET ['userid'];
}

if (!empty($_REQUEST['group']))
{
	$gid = $_REQUEST['group'];
}

if (isset($_REQUEST['user']) and ($_REQUEST['user'] != "All"))
{
	$user = $_REQUEST['user'];
	for( $i=0;$i<count($s_u_users);$i++ ) 
	{
 		if ($s_u_users[$i][1] == $user )
		{
			$user_name = $s_u_users[$i][0];
		}
	}
	$user_sql = "and Uid = ".$user;
}
else
{
	$user = "All";
	$user_name = "All";
	$user_sql = "";
}

if ($s_u_level > 2)
{
	$sql_history= "select Content,history.CreateTime,history.id from history where  CreateTime >= '$starttime' and CreateTime <= '$endtime'  and history.Uid=".$uid." order by id desc";
	$sql_history_num = "select count(Content) from history where  CreateTime >= '$starttime' and CreateTime <= '$endtime'  and history.Uid=".$uid;
}
else if ($s_u_level == 2)
{
	$sql_history= "select Content,history.CreateTime,history.id from history where  CreateTime >= '$starttime' and CreateTime <= '$endtime'  and ( TypeLevel > 2 or   Uid='$uid') $user_sql  order by id desc";
	$sql_history_num = "select count(Content) from history where  CreateTime >= '$starttime' and CreateTime <= '$endtime'    and ( TypeLevel > 2 or   Uid='$uid') $user_sql ";
}	
else if ($s_u_level == 1)
{
	$sql_history= "select Content,history.CreateTime,history.id from history where  CreateTime >= '$starttime' and CreateTime <= '$endtime' $user_sql order by id desc";
	$sql_history_num = "select count(Content) from history where  CreateTime >= '$starttime' and CreateTime <= '$endtime'  $user_sql";
}

$pagesize = $_SESSION['history_pagesize'];
$num_all = getrs($sql_history_num);
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
else if (!empty($_GET['page_del']))
{
	$page=intval($_GET['page_del']);
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
$sql_history = $sql_history." limit ".$offset.",".$pagesize;
$history = getrs( $sql_history );
	
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=7">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $TITLE_NAME."-";?>用户中心</title>
<script src="script/selectdate.js" type="text/javascript"></script>
<link href="css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<form action="" method="get" name="form1" >
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="myhead">
  <tr>
    <td><table width="100%" height="5" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td></td>
      </tr>
    </table>
        <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" class="td_no_down">
          <tr  >
            <td height="33" background="image/tab_bg1.gif"  >&nbsp;&nbsp;&nbsp;&nbsp;<span class="title1">&nbsp;用户中心</span></td>
          </tr>
          <tr>
            <td ><table width="98%" height="32" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td><?php	 
				if ($s_u_level < 3)
				{
                 echo '用户：&nbsp;&nbsp;';
                 echo "<select name='user' class='anniu' id='user'  onChange='this.form.submit()'>";  
if (isset($user) and ($user !== "All"))
{
	for( $i=0;$i<count($s_u_users);$i++ ) 
	{
 		if ($s_u_users[$i][1] == $user )
		{
			echo "<option value='";
			echo  $s_u_users[$i][1];
			echo  "'  selected>";
			echo $s_u_users[$i][0];
			echo "</option>";
		}
		else
		{
			echo "<option value='";
			echo $s_u_users[$i][1];
			echo  "'>"; 
			echo $s_u_users[$i][0]; 
			echo "</option>";
		}	
	}
	echo "<option value='All'>All</option>";
}
else
{
	echo "<option value='All'  selected>All</option>";
	for( $i=0;$i<count($s_u_users);$i++ )
	{
		echo "<option value='";
		echo $s_u_users[$i][1];
		echo  "'>"; 
		echo $s_u_users[$i][0]; 
		echo "</option>";
	}		
}	
echo "</select>";
echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
}?>
				                  <input name="page_del" type="hidden" id="page_del" value="<?php echo $page; ?>">
                  <input name="d_endtime" type="hidden" id="d_endtime" value="<?php echo $e_time; ?>">
                  <input name="d_starttime" type="hidden" id="d_starttime" value="<?php echo $s_time; ?>">
                  <input name="date" type="hidden" id="date" value="<?php echo $getdate; ?>">
                  <?php if ($s_u_level > 2) echo "用户".$s_u_name."&nbsp;"; else {  if ($user == "All") echo "所有用户&nbsp;&nbsp;&nbsp;";  else echo "用户".$user_name."&nbsp;&nbsp;&nbsp;";} if ($getdate == "all") echo "&nbsp;&nbsp;所有时间"; else echo $stime[0]."至".$etime[0];?>
                  </td>
                <td><?php
echo "共".$num_all."条记录";
echo "&nbsp;&nbsp;&nbsp;&nbsp;";	
?></td>
                <td><div>
                    <div align="center">
                      <?php  $url = "user_center.php";?>
                      <a href="user_center.php?user=<?php echo $user;?>&date=today" id="today"  >今日</a>&nbsp; <a href="user_center.php?user=<?php echo $user;?>&date=yesterday" id="yesterday">昨日</a>&nbsp;&nbsp;<a href="user_center.php?user=<?php echo $user;?>&date=lastweek" id="lastweek" class="" >最近7天</a>&nbsp;&nbsp;<a href="user_center.php?user=<?php echo $user;?>&date=last2week" id="last2week">最近15天</a> <a href="user_center.php?user=<?php echo $user;?>&date=all" id="all">所有</a></div>
                </div></td>
                      <td >开始日期:<input name="starttime" type="text" id="starttime"   size="12"></td>
                      <td >结束日期:<input name="endtime" type="text" id="endtime"   size="12"></td>
                        <td align="right" > <input type="submit" name="Submit" value="提交查询"></td>
              </tr>
            </table>
          </tr>
      </table></td>
  </tr>
</table>
<table width="98%" height="70" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top">&nbsp;</td>
  </tr>
</table>
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" class="td_clo_blue">
  <tr>
    <td><div align="center">
      <table width="98%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC" class="Ptable" >
        <tr>
          <?php	  if ($s_u_level == 1){?>
          <td width="28" height="25">选择</td>
          <?php	 }?>
          <td>NUM</td>
          <td>用户操作</td>
          <td width="140"><a href="javascript:select()">时间</a></td>
        </tr>
        <?php  
for( $i=0;$i<count($history);$i++ )
{ 
	echo  "<tr>";	
	 if ($s_u_level == 1)	{?>
        <td height=18 class=Ptable><input name="delid[]" type="checkbox" class="radio" id="delid[]" value="<?php echo $history[$i][2];?>"></td>
              <?php }
	echo "<td height=18 class=Ptable>";	
	echo $i+1+$pagesize*($page-1);
	echo "</td>";
	for( $j=0;$j<count($history[0])/2-1;$j++ ) 
	{
		echo "<td height=18 class=Ptable>";	
		echo $history[$i][$j];
		echo "</td>";
	}
?>
              <?php    }		
		?>
        </table>
      <table width="98%" height="32" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td><?php	  if ($s_u_level == 1){?>
                <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
                  <tr>
                    <td width="60"><div align="left">
                        <input name="delete" type="submit" class="button1" id="delete" value="删除"  onClick="javascript:return p_del()">
                    </div></td>
                    <td><div align="left"><a href="javascript:select()">全选</a>｜<a href="javascript:fanselect()">反选</a>｜<a href="javascript:noselect()">全不选</a>&nbsp;</div></td>
                  </tr>
                </table>
            <?php	 }?></td>
          <td>&nbsp;</td>
          <td><div align="right">
            <?php
$page_url = basename(__FILE__)."?user=".$user."&stime=".$starttime."&etime=".$endtime."&page=";
echo "<a href = '".$page_url."1'>首页</a>";
echo "&nbsp;&nbsp;";
if ($page !== 1)
{
	echo "<a href = '".$page_url.($page-1)."'>上一页</a>";
}
else
{
	echo "<a href = '".$page_url.$pages."'>上一页</a>";
}
echo "&nbsp;&nbsp;";


if ($pages	>= 10)
{
	if  (5 >= $page)
	{			
		for ($i=1;$i<=10;$i++)	
		{	
			if ($i == $page)
			{
				echo "<strong>".$i."&nbsp;&nbsp;</strong>";
			}
			else
			{
				echo "<a href = '".$page_url.$i."'>".$i."</a>&nbsp;&nbsp;";
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
						echo "<strong>".$i."&nbsp;&nbsp;</strong>";
					}
					else
					{
						echo "<a href = '".$page_url.$i."'>".$i."</a>&nbsp;&nbsp;";
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
			echo "<strong>".$i."&nbsp;&nbsp;</strong>";
		}
		else
		{
			echo "<a href = '".$page_url.$i."'>".$i."</a>&nbsp;&nbsp;";
		}	
	}	
}

							
	echo "&nbsp;&nbsp;";
	echo "<a href = '".$page_url.$pages."'>尾页</a>";
	echo "&nbsp;&nbsp;";	
	if ($page < $pages )
	{	
		echo "<a href = '".$page_url.($page+1)."'>下一页</a>";
	}else
	{
		echo "<a href = '".$page_url."1'>下一页</a>";
	}
	echo "&nbsp;&nbsp;";
	echo "共".$pages."页";				
?>
            <input name="page" type="text" id="page" size="3" onClick="Cleartext(this.id)" onKeyUp="value=value.replace(/[^\d]/g,'')" value="<?php echo $page;?>">
          </div></td>
          <td width="50"><div align="right">
            <input name="Submit" type="submit" class="button1" value="跳转">
          </div></td>
        </tr>
      </table>
    </div></td>
  </tr>
  <tr>
    <td height="5"></td>
  </tr>
</table>
</form>
<?php include 'boot.php'; ?>
</body>
</html>
