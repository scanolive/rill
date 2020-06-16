<select name='group' class='anniu' id='group' >
<?php for( $i=0;$i<count($s_u_groups);$i++ ) 
 {
 	if ($s_u_groups[$i][1] == $group )
	{
		echo "<option selected value=";
		echo  $s_u_groups[$i][1];
		echo  ">";
		echo $s_u_groups[$i][0]; 
		echo "</option>";
	}
	else
	{
		echo "<option  value=";
		echo  $s_u_groups[$i][1];
		echo  ">";
		echo $s_u_groups[$i][0]; 
		echo "</option>";		
	}
 }
 echo "</select>";	
?>
