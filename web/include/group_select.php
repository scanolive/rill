<?php
echo "<select name='group' class='anniu' id='group'  onChange='this.form.submit()'>";  
if (!empty($group) and ($group !== "All"))
{
	for( $i=0;$i<count($grps);$i++ ) 
	{
 		if ($grps[$i][1] == $group )
		{
			$group_name = $grps[$i][0];
			echo "<option value='";
			echo  $grps[$i][1];
			echo  "'  selected='selected'>";
			echo $grps[$i][0];
			echo "</option>";
		}
	}
	for( $i=0;$i<count($grps);$i++ )
	{
		if ($grps[$i][1] !== $group )
		{
			echo "<option value=";echo $grps[$i][1];echo  ">"; echo $grps[$i][0]; echo "</option>";
		}	
	}
	echo "<option value='All'>All</option>";
}
else
{
	echo "<option value='All'  selected='selected'>All</option>";
	for( $i=0;$i<count($grps);$i++ )
	{
		if ($grps[$i][1] !== $group )
		{
			echo "<option value=";echo $grps[$i][1];echo  ">"; echo $grps[$i][0]; echo "</option>";
		}	
	}	
}	
echo "</select>";
?>



