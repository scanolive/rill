<?php
include 'include/config.php';
include 'include/is_monitor.php';
$my_type = $_POST ['type'];
$item = $_GET ['item'];
$filedesc = $_POST['filedesc'];
?>

<?php
$max_file_size = $MAX_UPFILE_SIZE;   //上传文件大小限制, 单位BYTE

if (!file_exists($UPFILE_DIR))
{
	mkdir($UPFILE_DIR,0777);
}	
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($_FILES["upfile"]["error"] > 0)
    {
    	echo "Return Code: " . $_FILES["upfile"]["error"] . "<br />";
    }
  	else
    {
    	$file_name =  $_FILES["upfile"]["name"];
    	$file_size =  $_FILES["upfile"]["size"];
   
		$file_exte_name = pathinfo($_FILES["upfile"]["name"], PATHINFO_EXTENSION);
		$time_str = date(YmdHis,time());
		$save_name = $time_str .".". $file_exte_name;
		$all_path_name = $upfile_allpath.$save_name;
		move_uploaded_file($_FILES["upfile"]["tmp_name"],$UPFILE_DIR.$save_name);
		$sql = "insert upfile set FileName = '$file_name', SaveName = '$save_name', FileSize = '$file_size',  FileDesc='$filedesc',UserId = '$s_u_id';";
		do_sql($sql);
		echo $save_name;
		echo "<br/>";
		echo $file_name;
		echo "<br/>";
		echo $file_size;
    } 
} 
?>
<html>
<head>
<title>上传文件</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<link href="css/style.css" rel="stylesheet" type="text/css">
</head>

<body>
<form action="upload.php" method="post" enctype="multipart/form-data" name="upform">
<table width="98%" height="4" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td></td>
  </tr>
</table>
<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#F0F0F0" class="td_clo_blue">
  <tr>
    <td height="5"></td>
  </tr>
  <tr>
    <td height="4"><table border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td width="50" height="30"><div align="right">文件:</div></td>
        <td><input name="upfile" type="file" class="input_txt" size="50"></td>
        <td width="50"><div align="right">描述:</div></td>
        <td><input name="filedesc" type="text" class="input_txt" id="filedesc" size="40"></td>
        <td width="100">          <div align="right">
          <input name="submit" type="submit" class="delete"  value="上传" size="17">        
        </div></td>
      </tr>

    </table></td>
  </tr>
  <tr>
    <td height="5"></td>
  </tr>
</table>
</form>
</body>
</html>
