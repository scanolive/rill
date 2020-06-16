<?php 
include 'include/config.php';

$sql=" select replace(OutStr,'R!I@L#L','<br>'),StartTime,EndTime from bg_result where ip='172.24.1.4'  limit 1;";
$cmd_rs = getrs($sql);
?>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=7">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="refresh" content="5">
<title>恢复结果</title>

</head>
<link href="css/style.css" rel="stylesheet" type="text/css">

<body>
<br>
<br>
<br>
<table width="50%" height="30" border="0" align="center" cellpadding="0" cellspacing="0" class="td_clo_blue">
  <tr>
    <td  height="50">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $cmd_rs[0][0];?></td>
  </tr>
</table>
</body>
</html>
