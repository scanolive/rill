<?php
//include 'include/config.php';
$s_u_id = $_SESSION['rlll_olive_scan_userid'];
if ( $_SESSION['rlll_olive_scan_level'] < 3 )
{
	$sql_group = "select GroupName,id from ipgroup";
}
else
{
	 $sql_group = "select GroupName,ipgroup.id from ipgroup,users,userofgroup where userofgroup.Uid = users.id and  userofgroup.Gid = ipgroup.id  and users.id=".$s_u_id;
}
$grs = getrs($sql_group);
if (empty($group))
{
	$group = $grs[0][1];
}

if ( $s_u_level == 1)
{
	$ip_sql = "select ip from ipinfo where IP != '0.0.0.0' and Enable=1 and GroupId = ".$group;
}
else
{
	$ip_sql = "select ip from ipinfo where  IP != '0.0.0.0' and Enable=1 and ipinfo.GroupId=".$group;
}



$ip_rs = getrs($ip_sql);
if (!empty($_GET['ip']))
{
	$ip = ($_GET['ip']);
}
else
{
 	$ip = "select ip";
}
?>
<html>
<head >
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate"> 
<meta http-equiv="Content-Type" content="text/html, charset=UTF-8" />
<meta http-equiv="Content-Type" content="Connection, close" />
<link href="css/style.css" rel="stylesheet" type="text/css">
<script src="script/my_function.js" type="text/javascript"></script>

<title></title></head>
<body>
<table border="0">
  <tr>
    <td valign="top">所属组：
      <select name="group" class="anniu" id="group" width="50" onChange="showHint_get_ip(this.value,ip.value)">
<?php for( $i=0;$i<count($grs);$i++ ) 
 {
 	if ($grs[$i][1] == $group )
	{
	echo "<option value=";
	echo  $grs[$i][1];
	echo  " selected>";
	echo $grs[$i][0]; 
	echo "</option>";
	}
 }

	  
	for( $i=0;$i<count($grs);$i++ )
	{
		if ($grs[$i][1] !== $group )
		echo "<option value=";echo $grs[$i][1];echo  ">"; echo $grs[$i][0]; echo "</option>";
	}
?>
</select>    </td>
    <td width="20" valign="top">&nbsp;</td>
    <td valign="top"><div id="IpDiv">
IP：
    <select name="ip" class="anniu" id="ip" width="50" onChange="this.form.submit()">
   <option><?php echo $ip; ?></option>
  <?php 	  
	 for( $i=0;$i<count($ip_rs);$i++ )
	 {
	 	if ($ip_rs[$i][0] !== $ip )
		echo  "<option value=";echo $ip_rs[$i][0];echo  ">"; echo $ip_rs[$i][0]; echo "</option>";
	}
?>
</select>
    </div></td>
  </tr>
</table>
</body>
</html>
