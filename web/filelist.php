<?php
session_start();
include 'include/config.php';
include 'include/is_monitor.php';
$UPFILE_DIR = $_SESSION['upfile_dir'];
$UPFILE_ALLPATH = realpath(dirname(__FILE__)).'/'.$_SESSION['upfile_dir'];
$MAX_UPFILE_SIZE = $_SESSION['max_upfile_size'];

if (!empty($_POST['selected_ips']))
{
	$selected_ips = trim($_POST['selected_ips']);
}
if (!empty($_POST['type']))
{		
$my_type = $_POST ['type'];
$item = $_GET ['item'];
$filedesc = $_POST['filedesc'];
}
$max_file_size = $MAX_UPFILE_SIZE*1024*1024;   //上传文件大小限制, 单位BYTE

if (!file_exists($UPFILE_DIR))
{
	mkdir($UPFILE_DIR,0777);
}	
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($_FILES["upfile"]["error"] == 0)
    {
    	$file_name =  $_FILES["upfile"]["name"];
    	$file_size =  $_FILES["upfile"]["size"];
		if ($file_size > $max_file_size)
		{
			echo "<script>alert('文件不能超过".$MAX_UPFILE_SIZE."M')</script>";
   		}
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
		close_window();
    } 
	else if ($_FILES["upfile"]['error'] == 1)
    {
    	echo "<script>alert('上传失败,文件大小超过系统限制')</script>";
		close_window();
    }
  	else if ($_FILES["upfile"]["error"] == 3)
	{
		echo "<script>alert('上传失败,文件只有部分被上传!')</script>";
		close_window();
	}
	else if ($_FILES["upfile"]["error"] == 4)
	{
		echo "<script>alert('上传失败,没有文件被上传!')</script>";
		close_window();
	}
	else if ($_FILES["upfile"]["error"] == 5)
	{
		echo "<script>alert('上传失败,上传文件大小为0!')</script>";
		close_window();
	}
	else 
	{
    	echo "<script>alert('上传失败,未知错误')</script>";
		close_window();
    }
} 
?>
<html>
<head >
<meta http-equiv="X-UA-Compatible" content="IE=7">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<script src="script/my_function.js" type="text/javascript"></script>
<link href="css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
$sql = "select FileName,FileDesc,FileSize,AddTime,SaveName,id from upfile where Enable=1";
$file_list = getrs($sql);

echo "<table  width='98%' align='center' bgcolor='#CCCCCC' cellspacing='1' class=Ptable>";
echo "<tr >";
echo "<td height='20'  class=Ptable>文件名</td>";
echo "<td  class=Ptable>备注</td>";
echo "<td  class=Ptable>文件大小</td>";
echo "<td  class=Ptable>添加时间</td>";
echo "<td  class=Ptable>选择</td>";
//echo "<td  class=Ptable>操作(<a  onclick="."'showhidediv(".'"upload"'.");' href='#'>上传</a>)</td>";
echo  "</tr>";
for ($i=0;$i<count($file_list);$i++)
{
	echo "<tr class=Ptable>";
	for ($j=0;$j<count($file_list[0])/2-2;$j++)
	{
		echo  "<td height='20' class=Ptable>";
		echo $file_list[$i][$j];
		echo  "</td>";
	}
	echo "<td class=Ptable><input  name='selected_file' type='radio'  class='radio'  value='".$UPFILE_ALLPATH.$file_list[$i][4]."'  onClick=".'"d_file.value='."'".$file_list[$i][0]."';".'file_id.value='."'".$file_list[$i][5]."'".';s_file.value=this.value;" ></td>';
	//	echo "<td class=Ptable><a href='file_delete.php?delid=".$file_list[$i][5]."' target='_blank'>删除</a></td>";
//echo "<td class=Ptable><a href='javascript:showHint_get_filelist()' onclick='ajax_do(".'"file_delete.php?delid='.$file_list[$i][5].'&filename='.$file_list[$i][0].'&savename='.$file_list[$i][4].'"'.")'>删除文件</a></td>";
	echo  "</tr>";
}
echo "</table>";

?>
<div style="display:none" id="upload">
  <div align="center">
    <form action="filelist.php" method="post" enctype="multipart/form-data" name="upform" target="_blank">
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
                <td><input name="upfile" type="file" class="input_txt" size="50" onChange="fileChange(this,<?php echo $MAX_UPFILE_SIZE; ?>);"></td>
                <td width="50"><div align="right">描述:</div></td>
                <td><input name="filedesc" type="text" class="input_txt" id="filedesc" size="40"></td>
                <td width="100"><div align="right">
                    <input name="upload_button" type="button" class="delete"  onClick="if (upfile.value=='' || filedesc.value==''){alert('文件和描述均不能为空'); }else {upform.submit();setTimeout('showHint_get_filelist()',500)}" value="上传" size="17">
                </div></td>
              </tr>
          </table></td>
        </tr>
        <tr>
          <td height="5"></td>
        </tr>
      </table>
    </form>
  </div>
</div>
<table width="98%" height="3" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td></td>
  </tr>
</table>
</body>
</html>
