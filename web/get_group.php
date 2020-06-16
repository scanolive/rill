<?php
include 'include/doconn.php';
$gid = ($_GET['gid']);
if ( $s_u_level == 1)
{
	$getip_sql = "select ip from ipinfo where GroupId = ".$gid;
}
else
{
	$getip_sql = "select ip from ipinfo where ip != '0.0.0.0' and GroupId = ".$gid;
}
$rs = getrs($sql);
?>
<select name="ip" class="anniu" id="ip" width="50">
    <option>select ip</option>
  <?php 	  
	 for( $i=0;$i<count($rs);$i++ )
	 {
		echo "<option>"; echo $rs[$i][0]; echo "</option>";
	}
?>
</select>

