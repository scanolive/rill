<?php
include 'head.php';
$fault_arr = array(
			'客户端'		   => 'client',
			'连接数'        => 'connect',
			'磁盘空间'           => 'disk',
			'宕机'         => 'isalive',
			'负载'         => 'load',
			'网络流量'         => 'network',
			'端口'      => 'port',
			'进程数量'      => 'process',
			'登录用户数'        => 'login'
		);

if (!empty($_GET['ip']) )
{
	$sip = ($_GET['ip']);
	$ipsql = " and Ip = '$sip' ";
}
else
{
 	$sip = "All";
	$ipsql = "";
}
$grps = $s_u_groups;
$grs = $s_u_groups;
$gids = "";
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
$date = array(
			'today'        => date("Y-m-d H:i:s" ,strtotime('today')),
			'yesterday'    => date("Y-m-d H:i:s" ,strtotime('-1 day')),
			'lastweek'     => date("Y-m-d H:i:s" ,strtotime('-7 day')),
			'last2week'    => date("Y-m-d H:i:s" ,strtotime('-15 day')) 
		);
if 	(isset($_GET['ip']) and ($_GET['ip'] !== "All"))
{
	$ip = $_GET['ip'];
	$ipsql = " and Ip = '$ip' ";
}
	
else
{
	$ip = "All";
	$ipsql = "";
}
if ( $ip  == "0.0.0.0" )
{
	$ip = $SERVER_IP;
	$sip = $SERVER_IP;
}
$starttime = "";
$endtime = "";
if (!empty($_GET['starttime']) and !empty($_GET['endtime']) and (strtotime($_GET['endtime']) > strtotime($_GET['starttime'])))
{
	$starttime = $_GET['starttime'];
	$endtime = $_GET['endtime'];
	$time_sql = "and alarms.CreateTime >= '$starttime' and alarms.CreateTime <= '$endtime'";
}
else if (isset($_GET['date']))
{
	$getdate = $_GET['date'];
	$starttime = $date[$getdate];
	$endtime = date("Y-m-d H:i:s" ,time());
	$time_sql = "and alarms.CreateTime >= '$starttime' and alarms.CreateTime <= '$endtime'";
}
else
{
	$time_sql = "";
}
$stime = str_split($starttime,10);
$etime = str_split($endtime,10);
$s_time = $starttime;
$e_time = $endtime;

$ip_sql = "select DISTINCT Ip from alarms left join ipinfo on alarms.Ipid=ipinfo.id where Gid in $gids and Type != 'monweb' and Ip!='0.0.0.0'";
$ip_rs = getrs($ip_sql);

$sql = "select Type, count(alarms.id)  from alarms left join ipinfo on alarms.Ipid=ipinfo.id  where Type !='monweb' and Gid in $gids and IsAlarm=1 $time_sql $ipsql GROUP BY Type";
$fault_rs = getrs($sql);
$yAjaxstr = "";
$xAjaxstr = "";
for ( $i = 0;$i < count($fault_rs);$i++)
{
	if ($i < count($fault_rs) -1 )
	{
		$yAjaxstr .= $fault_rs[$i][1].",";
		$xAjaxstr .= "'".$fault_rs[$i][0]."'".",";
	}
	else
	{
		$yAjaxstr .= $fault_rs[$i][1];
		$xAjaxstr .= "'".$fault_rs[$i][0]."'";
	}	
}	

foreach( $fault_arr  as $key => $word)
{
	$xAjaxstr=str_replace($word,$key,$xAjaxstr);
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



<title><?php echo $TITLE_NAME."-";?>故障分析</title>
</head>
<body>
<form name="form_type" method="get" action="">
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
          <table width="99%" height="18" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
               
                <td>
                  <table width="98%" border="0" align="center">
              <tr>
                <td valign="top"><div align="left">
                   <select name="group" class="anniu" id="group" width="50" onChange="this.form.submit()">
					
<?php 
					  
for( $i=0;$i<count($grs);$i++ ) 
 {
 	if ($grs[$i][1] == $group )
	{
	$group_name = $grs[$i][0];
	echo "<option value='";
	echo  $grs[$i][1];
	echo  "' selected='selected'>";
	echo $grs[$i][0]; 
	echo "</option>";
	}
 }
if ($group !== "All")
echo "<option value='All' >All</option>";
else 
echo "<option value='All' selected='selected'>All</option>";
	  
	 for( $i=0;$i<count($grs);$i++ )
	 {
		echo "<option value=";echo $grs[$i][1];echo  ">"; echo $grs[$i][0]; echo "</option>";
	}
	
?>
                    </select>
                </div></td>
                            <td width="12" valign="top">&nbsp;</td>
                            <td valign="top"><div id="myDiv">
                             <select name="ip" class="anniu" id="ip" width="50" onChange="this.form.submit()">
<option  value="<?php echo $sip; ?>" ><?php echo $sip; ?></option>
 <?php 	  
if ($sip !== "All")
echo "<option value='All'>All</option>";
	for( $i=0;$i<count($ip_rs);$i++ )
	{
		echo  "<option value=";echo $ip_rs[$i][0];echo  ">"; echo $ip_rs[$i][0]; echo "</option>";
	}
?>
                                </select>
                        </div></td>
                      </tr>
                    </table>
                      </form></td>
				<td><div align="center"><?php if ($time_sql == "") echo "所有时间"; else echo $stime[0]."至".$etime[0];?></div></td>
                <td><div>
                  <div align="center">
                  <?php  $url = "faultreport.php?ip=".$ip."&group=".$group;?>&nbsp;<a href="<?php echo $url;?>&date=lastweek" id="lastweek" class="" >最近7天</a>&nbsp;&nbsp;<a href="<?php echo $url;?>&date=last2week" id="last2week">最近15天</a> <a href="<?php echo $url;?>" id="last2week">全部时间</a></div></td>
			                        <td >开始:<input name="starttime" type="text" id="starttime"   size="12"></td>
				                      <td >结束:<input name="endtime" type="text" id="endtime"   size="12"></td>
			                          <td align="right" > <input type="submit" name="Submit" value="提交"></td>
              </tr>
            </table>
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
              <tr>
                <td height="2"></td>
              </tr>
            </table>
            <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
              <tr>
                <td><div  style="margin:3px 3px 10px 10px;" id="containerid">
                  <script type="text/javascript">
				 	var chart;
				    $(document).ready(function() {
				        chart = new Highcharts.Chart({
				            chart: {
				                renderTo: 'containerid',
				                type: 'column',
								borderWidth: 1,
								borderColor: '#B5D6E6'
				            },
				            title: {
				                text: '故障分析'
				            },
				            xAxis: {
				                categories: [
				                   	<?php echo $xAjaxstr;?>
				                ]
				            },
				            yAxis: {
				                min: 0,
				                title: {
				                    text: '单位：次数'
				                }
				            },
				            legend: {
				            	layout: 'vertical',
				    		
	
				    			borderWidth: 0
				            },
				            tooltip: {
				                formatter: function() {
				                    return ''+
				                        this.x +': '+ this.y +' 次';
				                }
				            },
				            plotOptions: {
				                column: {
				                    pointPadding: 0.2,
				                    borderWidth: 0
				                }
				            },
			                series: [{
					            name:'故障数量',
				                data: [<?php echo $yAjaxstr;?>]		    
				            }]
				        });
				    });
			    </script>
                </div></td>
              </tr>
          </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<?php include 'boot.php'; ?>
</form>
</body></html>
