<?php
include 'head.php';
$grps = $s_u_groups;
$grs = $s_u_groups;
if 	(isset($_GET['ip']) and ($_GET['ip'] !== "select ip"))
{
	$sip = ($_GET['ip']);
	$ip = $_GET['ip'];
}
else
{
	//$sip = "select ip";
	$noip_sql="select Ip,id,GroupId from ipinfo where Ip!='0.0.0.0' and Enable=1 limit 1";
	$noip_sql="select Ip,id,GroupId from ipinfo where Ip!='0.0.0.0' and Enable=1 limit 1";
	$noip_rs=getrs($noip_sql);
	$ip = $noip_rs[0][0];
	$sip =  $ip;
	$ipid=$noip_rs[0][1];
	$group = $noip_rs[0][2];
	if (in_array($group,$s_u_gids))
	{
		$gids = "(".$group.")";
	}

}

if (!empty($_REQUEST['group']) and ($_REQUEST['group'] !== "All") and (!isset($group)))
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
else if (!isset($group))
{
	$group = "All";
	for ($i=0;$i<count($grps);$i++)
	{	
		if ($grps[$i][1] == "NoGroup")
		{
			$gids .= $grps[$i][1].",";
		}
	}	
	$gids = "(".$gids.$grps[0][1].")";
}

$date = array(
			'today'        => date("Y-m-d H:i:s" ,strtotime('today')),
			'yesterday'    => date("Y-m-d H:i:s" ,strtotime('-1 day')),
			'lastweek'     => date("Y-m-d H:i:s" ,strtotime('-7 day')),
			'last2week'    => date("Y-m-d H:i:s" ,strtotime('-14 day')) 
		);

if (isset($_GET['type']))
{
	$type = $_GET['type'];
}
else
{
	$type = "loadstat";
}
if (!empty($_GET['starttime']) and !empty($_GET['endtime']) and (strtotime($_GET['endtime']) > strtotime($_GET['starttime'])))
{
	$starttime = $_GET['starttime'];
	$endtime = $_GET['endtime'];
}
else if (isset($_GET['date']))
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
$s_time = $starttime;
$e_time = $endtime;
if ((strtotime($endtime)-strtotime($starttime)) > 24*3600*7 and (strtotime($endtime)-strtotime($starttime)) <= 24*3600*30 )
{
$interval = 1800;
$correction = 120;
$add_sql = "and ((MINUTE(MonTime)>0 and MINUTE(MonTime)<=5) or (MINUTE(MonTime)>30 and MINUTE(MonTime)<=35))";
$p_interval = 1800000;
}
else if ((strtotime($endtime)-strtotime($starttime)) > 24*3600*30)
{
	$interval = 3600;
	$correction = 240;
	$add_sql = "and (MINUTE(MonTime)>=0 and MINUTE(MonTime)<5) ";
	$p_interval = 3600000;	
}	
else
{
$interval = 300;
$correction = 30;
$add_sql = "";
$p_interval = 300000;
}

$ip_sql = "select ip,id from ipinfo where GroupId in $gids and  Ip!='0.0.0.0'";
$ip_rs = getrs($ip_sql);
if (isset($_GET['ipid']))
{
	$ipid = $_GET['ipid'];
}
if (!isset($ipid) or $ipid=="")
{
	foreach($ip_rs as $iprs)
	{
		if ($ip == $iprs[0])
		$ipid=$iprs[1];
	}
}
$sql = "select * from monitor where Ipid = ".$ipid." order by id desc limit 1";
$rdata=getrs($sql);
$json_str=$rdata[0]['MonText'];
$nodata_str = $json_str;
if(empty($json_str))
{ 
	return false ;
}
	
$data_list=json_decode($json_str,true);
if(!is_array($data_list)){
	$data_list = array();
}

$json_ex=preg_replace('[(:)[0-9]+]',':0',$nodata_str);	
$json_ex=preg_replace('[(:)[0-9]+\.[0-9]+]',':0',$json_ex);
				

$json_arr = json_decode($json_ex,true);
$data_list = array_merge($json_arr ,$data_list);
$drawlist =  $linelist = $drawtoline = array(); 
if(!$data_list){			
	return false;			
}	

foreach($data_list as $data_key => $data_value)
{	
	if(is_array($data_value))
	{				
		foreach($data_value as $dkey => $dvalue)
		{		
			if(is_array($dvalue))
			{		
				$dname = $data_key."|".$dkey;	
				$drawlist[]  = 	$dname;																								
				foreach($dvalue as $dk => $dv)
				{			
					$linelist[] = $dname."|".$dk;	
					$drawtoline[$dname][] = $dname."|".$dk;								
				}											
		}
			else
			{								
				$drawlist[]  = 	$data_key;
				$linelist[] = $data_key."|".$dkey;
				$drawtoline[$data_key][] = 	$data_key."|".$dkey;				
			}													
		}										
	}
	else
	{
		$drawlist[] = 	$data_key;
		$linelist[] =   $data_key;
		$drawtoline[$data_key][] = $data_key;					 
	}			
}					
$drawlist=(array_unique(array_values($drawlist)));
$drawlist = $drawlist ;
$linelist = $linelist ;
$drawtoline = $drawtoline ;			
$list = $drawlist;
$newlist = array();
foreach ($list as $key) 
{
	if(($pos = strripos($key ,$type))!==false)
	{
		array_push($newlist ,$key);
	}
}
$drawlist = $newlist;
$newAlldata = array();

$Qsql = "select * from monitor where Ipid = '$ipid' and MonTime >= '$starttime' and MonTime <= '$endtime' $add_sql  order by MonTime ";
$allData = getrs($Qsql);
//print_r($allData[0]);
$Mindata = $allData[0]['MonTime'];
$recordNum = count($allData);
$n = 0;
for($h=0;$h<$recordNum-1 ;$h++)
{
	$ctime=strtotime($allData[$h]['MonTime']);
	$ntime=strtotime($allData[($h+1)]['MonTime']);
	if($ntime - $ctime > ($interval+$correction))
	{	
		$num=floor(($ntime - $ctime)/$interval);
		for($g=0;$g<=$num;$g++)
		{					

			$n = $n + 1;
			$newAlldata[$n]['MonText'] = 'NoData';
			$newAlldata[$n]['MonTime'] = $allData[$h]['MonTime']+$interval*($g+1);					
		}				
	}
	else
	{			
		$n = $n + 1;
		$newAlldata[$n]['MonText'] = $allData[$h]['MonText'];
		$newAlldata[$n]['MonTime'] = $allData[$h]['MonTime'];
	}
}	

$linedatalist = array(); //保存曲线图数据
$dataNum = count($newAlldata);
//echo $dataNum;
//print_r($newAlldata);
for($j=1;$j<$dataNum+1;$j++) //外循环，遍历数据记录
{
	$json_str=$newAlldata[$j]['MonText'];
	if($json_str=='NoData')
	{
		$json_str=preg_replace('[(:)[0-9]+]',':0',$nodata_str);	
		$json_str=preg_replace('[(:)[0-9]+\.[0-9]+]',':0',$json_str);
	}				
	$data_list=json_decode($json_str,true);	
	foreach($linelist as $lvalue)
	{
		if (!isset($linedatalist[$lvalue]))
		{
			$linedatalist[$lvalue] = "";
		}	

		if(strpos($lvalue,'|'))
		{				
			$llist=explode('|',$lvalue);							
			if(count($llist) == 2)
			{
				$lone=$llist[0];
				$ltwo = $llist[1];							 
				$linedatalist[$lvalue] = trim($linedatalist[$lvalue],',').",".$data_list[$lone][$ltwo];							
			}
			else if(count($llist) == 3)
			{	
				$linedatalist[$lvalue] = trim($linedatalist[$lvalue],',').",".$data_list["$llist[0]"]["$llist[1]"]["$llist[2]"];
			}						
		}
		else
		{
			$linedatalist[$lvalue] = trim($linedatalist[$lvalue],',').",".$data_list[$lvalue];				
		}				
	}	
	if (!isset($linedatalist['datetime']))
	{
			$linedatalist['datetime'] = "";
	}		
	$linedatalist['datetime'] =	trim($linedatalist['datetime'],',').",".strtotime($newAlldata[$j]['MonTime']);		
}
$linemax = $linemin = $lineavg = array();
foreach($linedatalist as $line_k=>$line_value)
{	
	$line_value = substr($line_value,1,strlen($line_value));
	$larray = explode(',',$line_value);
	$linemax[$line_k] = max($larray);
	$linemin[$line_k] = min($larray);
	$lineavg[$line_k] = ceil(array_sum($larray)/count($larray));					
}
$linemax = $linemax ;
$linemin = $linemin ;
$lineavg = $lineavg ;
$drawdatalist = array();
$flag = 1;
foreach($drawlist as $drawvalue)
{		
	$d_list=$drawtoline[$drawvalue]; //曲线名
	if(strpos('_'.$drawvalue,'network')||strpos('_'.$drawvalue,'diskstat')||strpos('_'.$drawvalue,'memory'))
	{
		$danwei = get_ytitle($drawvalue, $drawtoline, $lineavg);
	}
	foreach($d_list as $d_k => $d_v)
	{
		$name = array_reverse(explode('|',$d_v));
		$name = $name[0];	//$data = substr($linedatalist[$d_v],1,strlen($linedatalist[$d_v]));
		$data = trim($linedatalist[$d_v],',');		// 单位转换
		if(strpos('_'.$d_v,'network') || strpos('_'.$d_v,'diskstat') || strpos('_'.$d_v,'memory'))
		{							
			if(strpos('_'.$drawvalue,'network'))
			{		
				$datalist = explode(',',$data);
				$datatemp = $temp = array();
				if($danwei == '千字节(KB)')
				{
					foreach($datalist as $datavalue)
					{
						$datatemp[] = sprintf("%.2f", $datavalue/1024);									
					}
					$data = implode(',',$datatemp);
				}
				else if($danwei == '兆(MB)')
				{
					foreach($datalist as $datavalue)
					{
						$datatemp[] = sprintf("%.2f", ($datavalue/1024)/1024);								
					}
					$data = implode(',',$datatemp);
				}								
			}
		else 
		{						
			$datalist = explode(',',$data);
			$datatemp = $temp = array();
			if($danwei == '千兆字节(GB)')
			{
				foreach($datalist as $datavalue)
				{
					$datatemp[] = sprintf("%.2f", $datavalue/1024);									
				}
				$data = implode(',',$datatemp);
			}else if($danwei == '(TB)')
			{
				foreach($datalist as $datavalue)
				{	
					$datatemp[] = sprintf("%.2f", ($datavalue/1024)/1024);									
				}
				$data = implode(',',$datatemp);
			}							
		}						
	}
	if($flag == '1')
	{
		$datatemp = explode(',',$data);
		$max = max($datatemp);
		$min = min($datatemp);
		$avg = array_sum($datatemp)/count($datatemp);
		$max = empty($max)?number_format('0',2):number_format($max,2);
		$min = empty($min)?number_format('0',2):number_format($min,2);
		$avg = empty($avg)?number_format('0',2):number_format($avg,2);
		$name .= " 最大值:".$max." 最小值:".$min." 平均值:".$avg ;
	}							 
	if (!isset($drawdatalist[$drawvalue]))
	{
			$drawdatalist[$drawvalue] = "";
	}		
	$drawdatalist[$drawvalue] = "{name: '".$name."',data: [".$data."]},".$drawdatalist[$drawvalue];
}	
$drawdatalist[$drawvalue]=trim($drawdatalist[$drawvalue],',');
}

if($starttime == '')
{//默认展示过去一周的数据		
	$starttime = strtotime("-7 day"); 
	$starttime=date("Y-m-d H:i:s",$starttime);		
}		
if(strtotime($starttime) >= strtotime($Mindata))
{							
	$starttime = strtotime($starttime);
	$starttime = date("Y,m,d,H,i,s",strtotime("-1 month",$starttime));
}
else
{			
	$starttime = strtotime($Mindata);
	$stime = str_split($Mindata,10);
	$starttime = date("Y,m,d,H,i,s",strtotime("-1 month",$starttime));
}
$pointInterval = $p_interval;
$xcategories = "pointInterval: ".$pointInterval.",pointStart: (new Date(".$starttime.")).getTime()";
function get_ytitle($phtoname,$drawtoline,$lineavg)
{
	$Ylist = array(
			'loadstat' => '数值',
			'memory' => '兆(MB)',
			'process_num' => '个',
			'diskstat' => '兆(MB)',
			'network' => '字节(bytes)',
			'constat' => '个',
			'login' => '用户'				
				);
	foreach($Ylist as $ykey => $yvalue)
	{
		if(strpos("_".$phtoname,$ykey))
		{			
			$dd_list=$drawtoline[$phtoname]; //曲线名
			$average = 0;
			$tmpd_v = '';
			foreach($dd_list as $d_k => $d_v)
			{					
				// 单位转换
				$average +=$lineavg[$d_v];
				$tmpd_v = $d_v;
			}
			$result = intval($average/count($dd_list));
			if(strpos('_'.$tmpd_v,'network') || strpos('_'.$tmpd_v,'diskstat') || strpos('_'.$tmpd_v,'memory'))
			{		
				if( $result > 1024 && $result < (1024*1024))
				{	
					if(strpos('_'.$phtoname,'network'))
					{		
						$danwei = '千字节(KB)';												
					}
					else
					{	
						$danwei =  '千兆字节(GB)';							
					}							
					return $danwei;							
				}
				else if( $result > (1024*1024))
				{	
					if(strpos('_'.$phtoname,'network'))
					{
						$danwei = '兆(MB)';								
					}
					else
					{								
						$danwei = '(TB)';							
					}	
					return $danwei;						
				}
				else
				{
					return $Ylist[$ykey];					
				}													
			}
			else
			{
				return $Ylist[$ykey];				
			}					
		}		
	}	
}	
	
	
function osa_draw($phtoname,$ip,$drawdatalist,$Mindata,$xcategories ,$ytitle,$starttime,$endtime)
{
	$entocn = array(
					'diskstat'    => '磁盘状态',
					'login'       => '登录用户',
					'loadstat'    => '负载状态',
					'process_num' => '进程数量',
					'memory' 	  => '内存状态',
					'network' 	  => '网络信息',
					'constat'	  => '连接数量'			
		
		);		
	foreach($entocn as $en_k=>$en_v)
	{			
		if(strpos('_'.$phtoname,$en_k))
		{
			$titlename = str_replace($en_k,$en_v,$phtoname);
			break;
		}
	}
	$graphstr='';
	$divname = $phtoname; //应用到的层名
	$charttype = 'area'; //图形类型
	$charttitle = $ip.'_'.$titlename; //标题	
	$yseries = $drawdatalist[$divname]; //Y轴数据
	$starttime = date("Y,m,d,H,i,s",strtotime($starttime));
	$endtime = date("Y,m,d,H,i,s",strtotime($endtime));
	$maxtime = "(new Date(".$endtime.")).getTime()";
	$mintime = "(new Date(".$starttime.")).getTime()";		 
	$Hlist = array('divname','charttype','charttitle','xcategories','ytitle','yseries','maxtime','mintime');
	$contents = file_get_contents('highcharts.html');
	foreach($Hlist as $Hvalue)
	{
		$rstr = "#".$Hvalue."#";
		$contents = str_replace($rstr,$$Hvalue,$contents);
	}
	$graphstr .= $contents;	
	$graphstr .= "<div  align=\"center\" id=\"$divname\" style=\"width: 100%; height: 100%; margin-bottom:5px;\"> </div>";	
	return $graphstr;
}

$graphstr = "";
foreach($drawlist as $draw_value)
{
	$graphstr .= "<tr><td>";
	$ytitle = get_ytitle($draw_value,$drawtoline,$lineavg);
	$graphstr .= osa_draw($draw_value,$ip,$drawdatalist,$Mindata,$xcategories ,$ytitle,$starttime,$endtime);
	$graphstr .= "</td></tr>";
}	
?>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=7">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Type" content="Connection; close" />

<link href="css/style.css" rel="stylesheet" type="text/css"/>
<script src="script/jquery.min.js"  type="text/javascript"></script>
<script src="script/highcharts.js" type="text/javascript"></script>
<script src="script/selectdate.js" type="text/javascript"></script>



<title><?php echo $TITLE_NAME."-";?>图形分析</title>
</head>
<body>
<table width="100%" height="10" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td></td>
  </tr>
</table>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top"><table width="1000" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
      <tr>
        <td valign="top"><div>
          <table width="98%" height="18" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
               
                <td><form name="form_type" method="get" action="">
                  <table width="100%" border="0">
              <tr>
                            <td valign="top"><div align="left">
 <?php 
echo  '<select name="group" class="anniu" id="group" width="50" onChange="showHint_get_allip(this.value,ip.value)">';
for( $i=0;$i<count($grs);$i++ ) 
 {
 	if ($grs[$i][1] == $group )
	{
	echo "<option value=";
	echo  $grs[$i][1];
	echo  ">";
	echo $grs[$i][0]; 
	echo "</option>";
	}
 }

	  
	 for( $i=0;$i<count($grs);$i++ )
	 {
		echo "<option value=";echo $grs[$i][1];echo  ">"; echo $grs[$i][0]; echo "</option>";
	}
echo  "</select>";
?>
                             
                            </div></td>
                            <td width="12" valign="top">&nbsp;</td>
                            <td valign="top"><div id="myDiv">
<?php echo '<select name="ip" class="anniu" id="ip" width="50" onChange="this.form.submit()">';
		echo  '<option>'.$sip.'</option>';                              
	 for( $i=0;$i<count($ip_rs);$i++ )
	 {
		echo  "<option value=";echo $ip_rs[$i][0];echo  ">"; echo $ip_rs[$i][0]; echo "</option>";
	}
	echo " </select>";
?>
                             
                            </div></td>
                        <td width="12" valign="top"><input name="starttime" type="hidden" id="starttime" value="<?php echo $s_time; ?>">
                            <input name="endtime" type="hidden" id="endtime" value="<?php echo $e_time; ?>"></td>
                            <td valign="top"><select id=“type” name="type"  onChange="this.form.submit()">
                              <option value=<?php echo $type;?> selected>
							  <?php if ($type == "memory")
							  {
							  		$type_name = "内存状态";
							  }
							  else if ($type == "login")
							  {
							  		$type_name = "登录用户";
							  }
							  else if ($type == "process_num")
							  {
							  		$type_name = "进程数量";
							  }
							  else if ($type == "diskstat")
							  {
							  		$type_name = "磁盘状态";
							  }
							  else if ($type == "network")
							  {
							  		$type_name = "网络信息";
							  }
							  else if ($type == "constat")
							  {
							  		$type_name = "连接数量";
							  }
							   else if ($type == "loadstat")
							  {
							  		$type_name = "负载状态";
							  }
							  
							  echo $type_name;?></option>
							  <option value="memory" >内存状态</option>
                              <option value="login">登录用户</option>
                              <option value="process_num">进程数量</option>
                              <option value="diskstat">磁盘状态</option>
                              <option value="network">网络信息</option>
                              <option value="constat">连接数量</option>
                              <option value="loadstat">负载状态</option>
                            </select></td>
                      </tr>
                    </table>
                      </form></td>
                <td><div align="center"><?php echo $stime[0];?>至<?php echo $etime[0];?></div></td>
                <td><div>
                  <div align="center">
                    <?php  $url = "graph.php?ip=".$ip."&type=".$type."&group=".$group;?>
                  <a href="<?php echo $url;?>&date=today" id="today"  >今日</a>&nbsp; <a href="<?php echo $url;?>&date=yesterday" id="yesterday">昨日</a>&nbsp;&nbsp;<a href="<?php echo $url;?>&date=lastweek" id="lastweek" class="" >最近1周</a>&nbsp;&nbsp;<a href="<?php echo $url;?>&date=last2week" id="last2week">最近2周</a></div></td>
                      <td >开始:<input name="starttime" type="text" id="starttime"   size="10"></td>
                      <td >结束:<input name="endtime" type="text" id="endtime"   size="10"></td>
                        <td align="right" > <input type="submit" name="Submit" value="查询"></td>
              </tr>
            </table>
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td height="2"></td>
              </tr>
            </table>
            <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td><?php echo $graphstr;?>
                <div align="center"></div></td>
              </tr>
          </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<?php include 'boot.php'; ?>
</body></html>
