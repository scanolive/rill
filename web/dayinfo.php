<?php
include 'head.php';
$date_today = date("Y-m-d" ,time());
$group = ($_GET['group']);
error_reporting(0);  
if (!empty($_GET['ip']))
{
	$ip = ($_GET['ip']);
	
	if (( $s_u_level > 2 ) and !(check_ip($ip,$s_u_id)))
	{
		alert_go("你没有此IP的权限","mon_state.php");
	}
}	 
else 
{
	alert_go("请指定IP","mon_state.php");
}
if (!empty($_GET['check_date']))
{
	$check_date = ($_GET['check_date']);
}
else
{
	$check_date = date("Y-m-d" ,time());
}
	$date_sql = "select  distinct  Addtime from info_day Left Join ipinfo ON ipinfo.id=info_day.Ipid  where ip='$ip' order by Addtime desc";
	$date_rs = getrs($date_sql);
	$sql="select Data_txt from info_day Left Join ipinfo ON ipinfo.id=info_day.Ipid where ip='$ip' and AddTime = '$check_date' order by info_day.id  desc  limit 1";
	$data=getrs($sql);
	$data = $data[0][0];
	
$one = explode('||',$data);
for($i=0;$i<count($one);$i++)
{
	$two[$i]=explode('=>',$one[$i]);
	$jsonstr=substr($two[$i][1],1,strlen($two[$i][1]) - 2 );
	$key=str_replace('\'','',$two[$i][0]);
	$jsonlist[$key]=json_decode($jsonstr,true);
}
function getDetailinfo($rlist){
	$detail_rlist = array();
	foreach($rlist as $r_key => $r_value)
	{  //数组分解
		$r_key='_'.$r_key;
		if(strpos($r_key,'check_disk_'))
		{
			$d_key=substr($r_key,strlen($r_key) -2 ,2);
			$disk_list[$d_key]=$r_value;
		}
		else if(strpos($r_key,'get_bandwidth_'))
		{
			$n_key=substr($r_key,strlen($r_key) -1 ,1);
			$net_list[$n_key]=$r_value;
		}
		else if(strpos($r_key,'get_10_of_cpu_'))
		{
			$c_key=substr($r_key,strlen($r_key) -1 ,1);
			$cpu_list[$c_key]=$r_value;
		}
		else if(strpos($r_key,'get_10_of_mem_'))
		{
			$m_key=substr($r_key,strlen($r_key) -1 ,1);
			$mem_list[$m_key]=$r_value;
		}
		else if(strpos($r_key,'check_cpuinfo'))
		{
			$detail_rlist['cpuinfo']=$r_value;
		}
		else if(strpos($r_key,'check_meminfo'))
		{
			$detail_rlist['meminfo']=$r_value;
		}
		else if(strpos($r_key,'check_load'))
		{
			$detail_rlist['topinfo']=$r_value;
		}
		else if(strpos($r_key,'getonlineuser'))
		{
			$detail_rlist['logininfo']=$r_value;
		}
		else if(strpos($r_key,'getonlinetime'))
		{
			$detail_rlist['uptimeinfo']=$r_value;
		}
		else if(strpos($r_key,'check_service'))
		{
			$detail_rlist['seviceinfo']=$r_value;
		}
		else if(strpos($r_key,'all_pro_'))
		{
			$p_key=substr($r_key,strlen($r_key) -2,2 );
			$pro_list[$p_key]=$r_value;
		}
	}
	$detail_rlist['disklist'] = $disk_list;
	$detail_rlist['cpulist'] = $cpu_list;
	$detail_rlist['netlist'] = $net_list;
	$detail_rlist['memlist'] = $mem_list;
	$detail_rlist['prolist'] = $pro_list;
	return $detail_rlist;
}

$detail_list = getDetailinfo($jsonlist);
socket_close($socket);
?>
<html>
<head >
<meta http-equiv="X-UA-Compatible" content="IE=7">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache;must-revalidate"> 
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Type" content="Connection; close" />
<link href="css/style.css" rel="stylesheet" type="text/css">
<title><?php echo $TITLE_NAME."-";?>每日信息</title></head>
<body>

<table width="98%" height="5" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td height="3"></td>
  </tr>
</table>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0" class="td_clo_blue">
      <tr>
        <td height="33" background="image/tab_bg1.gif" class="title1">&nbsp;&nbsp;&nbsp;&nbsp;每日信息</td>
      </tr>
      <tr>
        <td height="8"></td>
      </tr>
      <tr>
        <td><form action="" method="get" name="form1" target="_top"><table width="96%" height="30" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td height="3">
                <div align="left">
                  <?php include 'ajax_select_allip.php'; ?>
                </div>
                 <a href="" onClick="javascript:history.go(0);"></a></td>
            <td><div align="right"> 检测日期：
              <select name="check_date" id="check_date" onChange="this.form.submit()">
                    <option value="<?php echo $check_date;?>" selected>
                    <?php if($check_date == $date_today) echo "今日"; else echo $check_date;?>
                    </option>
                    <?php for ($i=0;$i<count($date_rs);$i++)
				{	
					if ($date_rs[$i][0] !== $check_date)
					{
						 if ($date_rs[$i][0] == $date_today)
						{
							echo "<option value='".$date_rs[$i][0]."' >今日</option>";
						}
						else
						{
							echo "<option value='".$date_rs[$i][0]."'>".$date_rs[$i][0]."</option>";
						}
					}
				}
				?>
                  </select>
            </div></td>
            <td width="3"></td>
            </tr>
        </table>
        </form></td>
      </tr>
      <tr>
        <td width="5" height="5"></td>
      </tr>
      <tr>
        <td><table width="96%" border="1" align="center" bgcolor="#F9FBFA" class="bgah">
          <tr>
            <td width="15%" >CPU信息：</td>
            <td>型号：<?php echo $detail_list['cpuinfo']['cpu_type'];?>主频：<?php echo $detail_list['cpuinfo']['cpu_mhz'];?>物理个数：<?php echo $detail_list['cpuinfo']['cpu_number'];?></td>
          </tr>
          <tr>
            <td>内存状态：</td>
            <td><table width="100%" border="1" class="bgah">
                <tr>
                  <td width="20%">物理内存信息</td>
                  <td width="80%">总大小:<?php echo $detail_list['meminfo']['mem_total'];?>,剩余大小:<?php echo $detail_list['meminfo']['mem_free'];?>,Buffers:<?php echo $detail_list['meminfo']['mem_buffer'];?>,Cached:<?php echo $detail_list['meminfo']['mem_cache'];?></td>
                </tr>
                <tr>
                  <td>SWAP内存信息</td>
                  <td>总大小:<?php echo $detail_list['meminfo']['mem_swap_total'];?>,剩余大小:<?php echo $detail_list['meminfo']['mem_swap_free'];?></td>
                </tr>
            </table></td>
          </tr>
          <tr >
            <td>硬盘状态：</td>
            <td><table width="100%" border="1" class="bgah">
                <tr>
                  <td width="25%">Filestem</td>
                  <td width="15%">Size</td>
                  <td width="15%">Used</td>
                  <td width="15%">Avail</td>
                  <td width="15%">Use%</td>
                  <td width="15%">Mounted</td>
                </tr>
                <?php foreach($detail_list['disklist'] as $d_key=>$d_value){ ?>
                <tr>
                  <?php foreach ($d_value as $dd_value){ ?>
                  <td><?php echo $dd_value;?></td>
                  <?php }?>
                </tr>
                <?php }	?>
            </table></td>
          </tr>
          <tr>
            <td>网站状态：</td>
            <td><table width="100%" border="1" class="bgah">
                <tr>
                  <td width="10%">device</td>
                  <td width="45%">in</td>
                  <td width="45%">out</td>
                </tr>
                <?php foreach($detail_list['netlist'] as $n_key=>$n_value){ ?>
                <tr>
                  <?php foreach($n_value as $nn_key=>$nn_value){
											
												if(is_numeric($nn_value)){ ?>
                  <td><?php echo $nn_value;?> bytes(<?php echo round(($nn_value/1024/1024/1024),2);?> GiB)</td>
                  <?php }else{ ?>
                  <td><?php echo $nn_value;?> </td>
                  <?php 	}       
											 } ?>
                </tr>
                <?php } ?>
            </table></td>
          </tr>
          <tr >
            <td>运行的服务：</td>
            <td><table width="100%" border="1" class="bgah">
                <tr>
                  <?php $ii=0;							
										foreach($detail_list['seviceinfo'] as $sev_key=>$sev_value){
											if( ($ii%6)==0 ){ ?>
                </tr>
                <tr>
                  <?php }?>
                  <td><?php echo $sev_key;?></td>
                  <?php 	$ii++;
										}?>
                </tr>
            </table></td>
          </tr>
          <tr>
            <td>负载状态：</td>
            <td><?php echo "1min: ".$detail_list['topinfo']['1min']." , 5min:".$detail_list['topinfo']['5min']." ,15min:".$detail_list['topinfo']['15min'];?></td>
          </tr>
          <tr >
            <td>运行时间：</td>
            <td><?php echo $detail_list['uptimeinfo']['onlinetime']?></td>
          </tr>
          <tr>
            <td>登录人数：</td>
            <td><?php echo $detail_list['logininfo']['onlineuse'];?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td height="10"></td>
      </tr>
    </table>
      <br>
      <table width="100%" border="0" cellpadding="0" cellspacing="0" class="td_clo_blue">
        <tr>
          <td height="33" background="image/tab_bg1.gif" class="title1">&nbsp;&nbsp;&nbsp;&nbsp;进程信息</td>
        </tr>
        <tr>
          <td height="10"></td>
        </tr>
        <tr>
          <td><table width="96%" border="1" align="center" class="bgah">
            <tr>
              <td valign="top"><table width="100%" border="1" align="center" class="bgah">
                  <tr>
                    <td colspan="4">占用CPU最多的十个进程</td>
                  </tr>
                  <tr>
                    <td >No.</td>
                    <td >PID</td>
                    <td >%cpu</td>
                    <td >command</td>
                  </tr>
                  <?php foreach($detail_list['cpulist'] as $cpu_key=>$cpu_value){ ?>
                  <tr>
                    <td><?php echo $cpu_key;?></td>
                    <?php foreach($cpu_value as $ccpu_value){?>
                    <td><?php echo $ccpu_value;?></td>
                    <?php }?>
                  </tr>
                  <?php }?>
              </table></td>
              <td valign="top"><table width="100%" border="1" align="center" class="bgah">
                  <tr>
                    <td colspan="4">占用内存最多的十个进程</td>
                  </tr>
                  <tr>
                    <td >No.</td>
                    <td >PID</td>
                    <td >%mem</td>
                    <td >command</td>
                  </tr>
                  <?php foreach($detail_list['memlist'] as $mem_key=>$mem_value){ ?>
                  <tr>
                    <td><?php echo $mem_key;?></td>
                    <?php foreach($mem_value as $mmem_value){?>
                    <td><?php echo $mmem_value;?></td>
                    <?php }?>
                  </tr>
                  <?php }?>
              </table></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td height="10"></td>
        </tr>
      </table>
      <br>
      <table width="100%" border="0" cellpadding="0" cellspacing="0" class="td_clo_blue">
        <tr>
          <td height="33" background="image/tab_bg1.gif" class="title1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;当前主要进程</td>
        </tr>
        <tr>
          <td height="10"></td>
        </tr>
        <tr>
          <td><table width="96%" border="1" align="center" bgcolor="#F9FBFA" class="bgah">
            <tr>
              <td >USER</td>
              <td >PID</td>
              <td >CMD</td>
            </tr>
            <?php foreach($detail_list['prolist'] as $cpu_key=>$cpu_value){ ?>
            <tr>
              <?php foreach($cpu_value as $ccpu_value){?>
              <td><?php echo $ccpu_value;?></td>
              <?php }?>
            </tr>
            <?php }?>
          </table></td>
        </tr>
        <tr>
          <td height="10"></td>
        </tr>
      </table>    </td>
  </tr>
</table>
<?php 
include 'boot.php';
?>
</body>
</html>
