<?php
include 'include/config.php';
$gid = ($_GET['gid']);
$ip = "select ip";
if ($gid !== "All")
{
	if ( $s_u_level == 1)
	{	
		$getip_sql = "select ip from ipinfo where  Enable=1  and GroupId = ".$gid;
	}
	else
	{
		$getip_sql = "select ip from ipinfo where  Enable=1  and ip != '0.0.0.0' and GroupId = ".$gid;
	}
$ip_rs = getrs($getip_sql);
}
?>
<select name="ip" class="anniu" id="ip" width="50" onChange="this.form.submit()">
    <option><?php echo $ip; ?></option>
  <?php 	  
	 for( $i=0;$i<count($ip_rs);$i++ )
	 {
		echo  "<option value=";echo $ip_rs[$i][0];echo  ">"; echo $ip_rs[$i][0]; echo "</option>";
	}
?>
</select>

