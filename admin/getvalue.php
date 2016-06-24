<?php
include('con1.php');

$select="select * FROM `chef_dish_master` WHERE `chef_id`='".$_POST['chef_id']."'";
 $obj=$mysqlipre->query($select);
while($row=$obj->fetch_array(MYSQLI_ASSOC)){
  $select16="select * FROM `chef_master` where `chef_id`='".$row['chef_id']."'";
	$obj16=$mysqlipre->query($select16);
	if($obj16->num_rows<=0){

echo "<center><b>No Chef Available</b></center>";	
	     
                 }
				 $row16=$obj16->fetch_array(MYSQLI_ASSOC);
				 $select19="select * FROM `chef_category_master` where `cat_id`='".$row['cat_id']."'";
	$obj19=$mysqlipre->query($select19);
	if($obj19->num_rows<=0){

echo "<center><b>No Category Available</b></center>";	
	     
                 }
				 $row19=$obj19->fetch_array(MYSQLI_ASSOC);
		?>
	                          


	<tr>
					<td style="text-align: center;"><?php echo $row16['name'];?></td>
                                        <td style="text-align: center;"><?php echo $row19['cat_name'];?></td>
                                        <td style="text-align: center;"><?php echo $row['dish_name'];?></td>
                                        <td style="text-align: center;"><?php echo $row['dish_price'];?></td>
                                        <td style="text-align: center;"><?php echo $row['meal_type'];?></td>
                                       

                                        
                                      
                                    </tr>
<?php } ?>