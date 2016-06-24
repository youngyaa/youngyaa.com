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







<div class="leftpanel">
                    <div class="media profile-left">
                        <a class="pull-left profile-thumb" href="profile.php">
                            <img class="img-circle" style=" height:40px; width:40px;
							border-radius:40px;" src="<?php echo $roww['photo'];?>" alt="no image">
                        </a>
                        <div class="media-body">
                            <h4 class="media-heading"><?php echo $roww['first_name'];?></h4>
                            <!--<small class="text-muted">Beach Lover</small>-->
                        </div>
                    </div><!-- media -->
                    
                   <!-- <h5 class="leftpanel-title">Navigation</h5>-->
                    <ul class="nav nav-pills nav-stacked">
                        <li id="dashboard" class=""><a href="dashboard.php"><i class="fa fa-home"></i> <span>Dashboard</span></a></li>
                         <li id="profile" class=""><a href="profile.php"><i class="fa fa-user"></i> <span>Profile</span></a></li>
						


<li id="user" class=""><a href="event_booking.php"><i class="fa fa-pencil"></i> <span>Event Booking</span></a></li>
 <li id="member" class=""><a href="membership_user.php" ><i class="fa fa-life-ring"></i> <span>Membership Users</span></a>
<li id="event" class="parent"><a href=""><i class="fa fa-suitcase"></i> <span>Manage Events</span></a>
                            <ul class="children">
                                <li id="event1"><a href="create_events.php">Create Events</a></li>
                                <li id="event2"><a href="manage_events.php">Manage Events</a></li>
                                
                            </ul>
                        </li>
						<li id="news" class="parent"><a href=""><i class="fa fa-ge"></i> <span>Manage News</span></a>
                            <ul class="children">
                                <li id="news1"><a href="create_news.php">Create News</a></li>
                                <li id="news2"><a href="manage_news.php">Manage News</a></li>
                                
                            </ul>
                        </li>
 <li id="seck" class=""><a href="seklingevent.php" ><i class="fa fa-leaf"></i> <span>Seckilling event</span></a>
                            
                        </li>
<!--<li id="chef" class=""><a href="managechef.php"><i class="fa fa-ge"></i> <span>Manage Cook</span></a>
                            
                        </li>
                        <li id="post" class=""><a href="managecategory.php"><span class="pull-right badge"></span><i class="fa fa-image"></i> <span>Manage Category</span></a></li>
                        <li id="report" class=""><a href="managedish.php"><i class="fa fa-suitcase"></i> <span>Manage Dish</span></a>
                            
                        </li>
<li id="order" class=""><a href="manageorder.php"><i class="fa fa-life-ring"></i> <span>Manage Order</span></a>
                            
                        </li>
<li id="order" class=""><a href="commission.php"><i class="fa fa-money"></i> <span>Manage Commission</span></a>
                            
                        </li>

 <li id="ruser" class=""><a href="managereport.php"><i class="fa fa-file-text"></i> <span>Manage Report</span></a>
                          
                        </li>-->
                        <li class=""><a href="logout.php" onclick="return confirm('Are you sure you want to Logout !');"><i class="fa fa-lock"></i> <span>Logout</span></a>
                            
                        </li>
                        
                            
                        </li>
                        
                        

                        
                    </ul>
                    
                </div><!-- leftpanel -->
				<?php }}?>