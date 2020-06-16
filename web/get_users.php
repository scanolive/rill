<?php include 'include/config.php';?>
<html>
<head >
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache, must-revalidate"> 
<meta http-equiv="Content-Type" content="text/html, charset=UTF-8" />
<meta http-equiv="Content-Type" content="Connection, close" />
<link href="css/style.css" rel="stylesheet" type="text/css">
</title></head>
<body>
<?php
$userid = ($_GET['userid']);
$usertype= ($_GET['usertype']);
if ( $usertype == "user" and isset($_GET['userid']) and $_GET['userid'] !== "")
{
	$groupall = "select GroupName,id from ipgroup;";
	$groupall_rs = getrs($groupall);
	$group_sql = "select GroupName,ipgroup.id  from userofgroup,ipgroup where ipgroup.id = userofgroup.Gid and Uid=".$userid;
	$group_rs = getrs($group_sql);
for ( $i=0;$i<count($groupall_rs);$i++ )
	{ ?>
      <input name="sgroup[]" type="checkbox" class="anniu" id="sgroup[]"  value="<?php echo $groupall_rs[$i][1];?>" 
<?php
	for ( $k=0;$k<count($group_rs);$k++ )
  		{
			if ($groupall_rs[$i][0] == $group_rs[$k][0])
				{
					 echo "checked";
				} 
		} ?> />
      <?php echo $groupall_rs[$i][0];echo "&nbsp;&nbsp;&nbsp;";
	  if (($i+1)%5 == 0)
		echo "<br>";
	 }
}
else if ( $usertype == "user" and empty($_GET['userid']))
{
	$groupall = "select GroupName,id from ipgroup;";
	$groupall_rs = getrs($groupall);
	for ( $i=0;$i<count($groupall_rs);$i++ )
	{ ?>
		<input name="sgroup[]" type="checkbox" class="anniu" id="sgroup[]"  value="<?php echo $groupall_rs[$i][1];?>" /> <?php	
		echo $groupall_rs[$i][0];echo "&nbsp;&nbsp;&nbsp;";
	}
}
else
{
echo "admin用户拥有所有组的权限";
}
?>

</body>
</html>
