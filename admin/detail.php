<style>
#basicForm{

    word-break: break-word;


}
</style>


<?php include("session.php"); 

include("con1.php");

?>
<?php
if(empty($_SESSION['adminid'])){


echo ("<script>
    window.alert('Please Login First')
    window.location.href='index.php';
    </script>"); 	
	return;
exit;
}
else  
{ 
?>
<?php
$sel="SELECT * FROM `experience_master` WHERE `experience_id`='".$_GET['id']."'";
$obj=$mysqlipre->query($sel);

if($obj->num_rows<=0){
	
	
	echo "<script>
    window.alert('Incorrect experience master  Data')
    window.location.href='manageexperience.php';
    </script>"; 	
	return;
exit;
}
else{
	$row=$obj->fetch_array(MYSQLI_ASSOC);


$sel1="SELECT * FROM `user_master` WHERE `user_id`='".$row['user_id']."'";
$obj1=$mysqlipre->query($sel1);

if($obj1->num_rows<=0){
	
	
	echo "Incorrect user master  Data"; 	

}
else{
	$row1=$obj1->fetch_array(MYSQLI_ASSOC);



?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>LifeShare Admin</title>

        <link href="css/style.default.css" rel="stylesheet">
        <link href="css/prettyPhoto.css" rel="stylesheet">
         <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="js/html5shiv.js"></script>
        <script src="js/respond.min.js"></script>
        <![endif]-->
    </head>

    <body>
        
       <?php include("header.php");?>
        
        <section>
            <div class="mainwrapper">
               
			   <?php include("sidebar.php");?>
                
                <div class="mainpanel">
                    <div class="pageheader">
                        <div class="media">
                            <div class="pageicon pull-left">
                                <i class="fa fa-user"></i>
                            </div>
                            <div class="media-body">
                                <ul class="breadcrumb">
                                    <li><a href=""><i class="glyphicon glyphicon-home"></i></a></li>
                                    <li><a href="">Home</a></li>
                                    <li><a href="manageexperience.php">Manage Experience</a></li>
                                </ul>
                                <h4>Details </h4>
                            </div>
                        </div><!-- media -->
                    </div><!-- pageheader -->
                    
                    <div class="contentpanel">




                    <?php echo $mesage ?>


                        
                        <div class="row">
                           
                            
                            <div class="col-sm-12 col-md-12">
                              
                                <div class="tab-content nopadding noborder">
                                    <div class="tab-pane active" id="activities">
                                        <div class="activity-list">  
                                          
										   <div class="col-md-12">
                                <form id="basicForm" action="" method="post" enctype="multipart/form-data" autocomplete="off">
                                <div class="panel panel-default">
                                    
                                    <div class="panel-body">
                                        <div class="row">


<?php
                              if($row['media_flag']==1)
{
?>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label"> <span class="asterisk"></span></label>
                                                <div class="col-sm-9">
                                                  <img  alt="no image" title="no image"style="width:215px; height:240px; width="320"
                                             border:1px solid white;" src="<?php echo $row['media'];?>">  
                                                </div>
                                            </div><!-- form-group -->
<?php } else {?>

                                             <div class="form-group">
                                                <label class="col-sm-3 control-label"> <span class="asterisk"></span></label>
                                                <div class="col-sm-9">
                                                  <video width="215px" height="240" controls="controls">
                                                     <source src="<?php echo $row['media'];?>" >
  
                                                                 </video>  
                                                                                                        </div>
                                            </div><!-- form-group -->
<?php } ?>

                                             <div class="form-group">
                                               <div class="col-sm-3"></div> <label class="col-sm-2 control-label">Name<span class="asterisk">*</span></label>
                                                <div class="col-sm-7">
                                                    <b><?php echo $row1['user_name'];?></b>
                                                </div>
                                            </div><!-- form-group -->
                                          
                                            <div class="form-group">
                                               <div class="col-sm-3"></div>  <label class="col-sm-2 control-label">Email <span class="asterisk">*</span></label>
                                                <div class="col-sm-7">
                                                    <b><?php echo $row1['user_email'];?></b>
                                                </div>
                                            </div><!-- form-group -->
                                            
                                             <div class="form-group">
                                                <div class="col-sm-3"></div><label class="col-sm-2 control-label">Phone <span class="asterisk">*</span></label>
                                                <div class="col-sm-7">
                                                   <b><?php echo $row1['user_phone'];?></b>
                                                </div>
                                            </div><!-- form-group -->
                                            

                                                                                      
                                           <div class="form-group">
                                                <div class="col-sm-3"></div><label class="col-sm-2 control-label">gender <span class="asterisk">*</span></label>
                                                <div class="col-sm-7">
                                                  <b> <?php echo $row1['gender'];?></b>
                                                </div>
                                            </div><!-- form-group -->

                                                   <div class="form-group">
                                                <div class="col-sm-3"></div><label class="col-sm-2 control-label">DOB <span class="asterisk">*</span></label>
                                                <div class="col-sm-7">
                                                   <b><?php echo $row1['dob'];?></b>
                                                </div>
                                            </div><!-- form-group -->

                                           <div class="form-group">
                                                <div class="col-sm-3"></div> <label class="col-sm-2 control-label">Profile<span class="asterisk"></span></label>
                                                <div class="col-sm-7">
                                                  <img class="img-circle" style="width:70px; height:70px; border-radius:70px;
                                             " src="<?php echo $row1['user_profile_pic'];?>">  
                                                </div>
                                            </div><!-- form-group -->

                                                 <div class="form-group">
                                                <div class="col-sm-3"></div><label class="col-sm-2 control-label">DOB <span class="asterisk">*</span></label>
                                                <div class="col-sm-7">
                                                   <b><?php echo $row1['dob'];?></b>
                                                </div>
                                            </div><!-- form-group --> 

                                            <div class="form-group">
                                                <div class="col-sm-3"></div><label class="col-sm-2 control-label">About Me <span class="asterisk">*</span></label>
                                                <div class="col-sm-7">
                                                   <b><?php echo $row1['aboutme'];?></b>
                                                </div>
                                            </div><!-- form-group --> 
                                            
                                           
                                        </div><!-- row -->
                                    </div><!-- panel-body -->
                                    
                                </div><!-- panel -->
                                </form>
                                
                            </div><!-- col-md-12 -->

                                      										  
                                        </div><!-- activity-list -->
                                
                                       
                                    </div><!-- tab-pane -->
                                    
                                   
                                
                            </div><!-- tab-content -->
                              
                            </div><!-- col-sm-9 -->
                        </div><!-- row -->  
                    
                    </div><!-- contentpanel -->
                    
                </div>
            </div><!-- mainwrapper -->
        </section>


        <script src="js/jquery-1.11.1.min.js"></script>
        <script src="js/jquery-migrate-1.2.1.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/modernizr.min.js"></script>
        <script src="js/pace.min.js"></script>
        <script src="js/retina.min.js"></script>
        <script src="js/jquery.cookies.js"></script>
        <script src="js/select2.min.js"></script>
        <script src="js/jquery.validate.min.js"></script>
        <script src="js/jquery.prettyPhoto.js"></script>
        <script src="js/holder.js"></script>

        <script src="js/custom.js"></script>


<script src="js/jquery.validate.min.js"></script>
<script src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/additional-methods.js"></script>

         <script type="text/javascript">
            // When the document is ready
            $(document).ready(function () {
            
                $("#basicForm").validate({
                    rules: {
                 first_name:{
				 required: true,
				lettersonly: true,
                           minlength: 3,
			   maxlength: 40
            },
                   last_name:{
				 required: true,
				 lettersonly: true,
                           minlength: 3,
			   maxlength: 40
            },
			
            email:{
				 required: true,
				 email:true,
			 maxlength: 50
            },
                          
              
        
                  phone:{
				 required: true,
				 number:true,
			 maxlength: 50
            }
					
					},
                     messages: {
			first_name: {
		    required:"Please enter First Name"
                 
		  	
			},
                         last_name: {
		    required:"Please enter Last Name"
		  	
			},
            email: {
		    required:"Please enter email"
		  	
			},

                  phone: {
		    required:"Please enter phone"
		  	
			}
				 
				 
			
        },
        
        
                });
            });
        </script>

 <script type="text/javascript">
            // When the document is ready
            $(document).ready(function () {
            
                $("#basicForm13").validate({
                    rules: {
                 
                          
                        password: {
                required: true,
                 
                minlength: 3,
				maxlength: 20
            },
         newpass: {
                required: true,
                 
                minlength: 3,
				maxlength: 20
            },
         repass: {
                required: true,
                 
                minlength: 3,
				maxlength: 20
            }
                  
					
					},
                     messages: {
			
            password: {
                required: "Please enter current password"
                 
                
                 },
newpass: {
                required: "Please enter New password"
                 
                
                 },
repass: {
                required: "Please enter Confirm password"
                 
                
                 }
                 
				 
				 
			
        },
        
        
                });
            });
        </script>


        <script>
            jQuery(document).ready(function(){
              
              jQuery("a[data-rel^='prettyPhoto']").prettyPhoto();
              
            });
        </script>
<script type="text/javascript">
                     
				
  var y=document.getElementById('post'); 



 y.setAttribute("class", "active");


</script>

    </body>
</html>
<?php } }}?>