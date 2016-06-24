<?php 
include("con1.php");

?>
<?php
if(empty($_SESSION['adminid'])){


echo"<script>
    window.alert('Please Login First')
    window.location.href='index.php';
    </script>"; 	
	return;
exit;
}
else  
{ 
?>
 <?php
$msg="";
$sel="SELECT * FROM `admin_master` WHERE `adminid`='".$_SESSION['adminid']."'";
$obj=$mysqlipre->query($sel);
$roww=$obj->fetch_array(MYSQLI_ASSOC);
if($obj->num_rows<=0)
{
	
	echo"<script>
    window.alert('Incorrect Data')
    window.location.href='index.php';
    </script>"; 	
	return;
exit;
}
else{
?>





 <header>
            <div class="headerwrapper">
                <div class="header-left">
                    <a href="" class="logo">
                        <img src="image/yy_92.png" alt="Young Yaa" /> 
                    </a>
                    <div class="pull-right">
                        <a href="" class="menu-collapse">
                            <i class="fa fa-bars"></i>
                        </a>
                    </div>
                </div><!-- header-left -->
                
                <div class="header-right">
                    
                    <div class="pull-right">
                        
                       
                        
                        
                        <div class="btn-group btn-group-list btn-group-messages">
                            <span style="color:white;"><?php echo $roww['first_name'];?> </span>
                               <img style="height:35px; width:35px;" src="<?php echo $roww['photo'];?>" alt="user-image" class="img-circle"/>
                                
                           
                            
                        </div><!-- btn-group -->
                        
                        <div class="btn-group btn-group-option">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                              <i class="fa fa-caret-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-right" role="menu">
                              <li><a href="profile.php"><i class="glyphicon glyphicon-user"></i> My Profile</a></li>
                             <!-- <li><a href="#"><i class="glyphicon glyphicon-star"></i> Activity Log</a></li>
                              <li><a href="#"><i class="glyphicon glyphicon-cog"></i> Account Settings</a></li>
                              <li><a href="#"><i class="glyphicon glyphicon-question-sign"></i> Help</a></li>-->
                              <li class="divider"></li>
                              <li><a href="logout.php" onclick="return confirm('Are you sure you want to Logout !');"><i class="glyphicon glyphicon-log-out"></i>Logout</a></li>
                            </ul>
                        </div><!-- btn-group -->
                        
                    </div><!-- pull-right -->
                    
                </div><!-- header-right -->
                
            </div><!-- headerwrapper -->
        </header>
		<?php }}?>