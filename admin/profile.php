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


$mesage="";

$result3=$mysqlipre->query("SELECT * FROM `admin_master` WHERE adminid='".$_SESSION['adminid']."' ");
if($result3->num_rows<=0)
{
echo "<script>alert('user not found');
window.location.href='dashboard.php';</script>";
									exit;
									return;



}

else
{
$row1=$result3->fetch_array(MYSQLI_ASSOC);


 $photo= $row1['photo'];
$name1= $row1['first_name'];
$name2= $row1['last_name'];
$phone= $row1['phone'];
$email= $row1['email'];
$password= $row1['password'];

}



$date= date('Y-m-d H:i:s');

if(isset($_POST['changepic']))

{


if(!empty($_FILES['file']['name']))
{
$url= isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https:' : 'http:'.'//'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
 
function generateRandomString($length =90) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

$str= generateRandomString();
$str2= rand();
$str3= $str.$str2;
	$target ="images/"; 

 $filename= $str3.basename( $_FILES['file']['name']);

$filename =trim($_FILES['file']['name']); //rename file
$filename=str_replace(' ','_',$filename);
$filename=$str3.$filename;



$data='https://www.youngyaa.com.au/admin/images/'.$filename;

if(move_uploaded_file($_FILES['file']['tmp_name'],$target.$filename))
{


}
else
{



echo "<script>alert('Image Not Uploaded');window.location.href='profile.php';</script>";
									exit;
									return;


}

}
else
{
	
$data=$photo;


}
$mysqlipre->query("UPDATE admin_master SET first_name='".$_POST['first_name']."',last_name='".$_POST['last_name']."',email='".$_POST['email']."',phone='".$_POST['phone']."', photo='$data', insertimephp='$date' WHERE adminid='".$_SESSION['adminid']."'");

if($mysqlipre->affected_rows>0)
{

/* echo "<script>alert('Profile Updated Successfully');window.location.href='setting.php';</script>";
									exit;
									return; */

									$mesage= '<center><h3><div class="alert alert-success alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong> Profile Updated Successfully.</strong>
                </div></h3></center>';
}

if($mysqlipre->affected_rows<=0)
{

/* echo "<script>alert('Sorry Try Later !');window.location.href='setting.php';</script>";
									exit;
									return; */
$mesage= '<center><h3><div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong> Sorry Try Later !!</strong>
                </div></h3></center>';
}



}





if(isset($_POST['changepassword']))
{
if($_POST['password']==$password)
{


if($_POST['newpass']==$_POST['repass'])
{

$mysqlipre->query("UPDATE admin_master SET password='".$_POST['repass']."', insertimephp='$date' WHERE adminid='".$_SESSION['adminid']."' ");



if($mysqlipre->affected_rows>0)
{

$mesage= '<center><h3><div class="alert alert-success alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong>  your password is changed now.</strong>
                </div></h3></center>';


}

if($mysqlipre->affected_rows<=0)
{

$mesage= '<center><h3><div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong> your password is not change.please try again!</strong>
                </div></h3></center>';


}


}
else
{
$mesage= '	<center><h3><div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong> New password and Confirm password do not match!</strong>
                </div></h3></center>';




}


}
else
{


$mesage='<center><h3><div class="alert alert-block alert-danger fade in">
                                <button data-dismiss="alert" class="close close-sm" type="button">
                                    <i class="fa fa-times"></i>
                                </button>
                                <strong>Please enter correct current password..</strong>
                            </div></h3></center>';

}
}

?>

<?php




$result3=$mysqlipre->query("SELECT * FROM `admin_master` WHERE adminid='".$_SESSION['adminid']."' ");
if($result3->num_rows<=0)
{
echo "<script>alert('user not found');
window.location.href='dashboard.php';</script>";
									exit;
									return;



}

else
{
$row1=$result3->fetch_array(MYSQLI_ASSOC);
?>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Young Yaa | Profile</title>

        <link href="css/style.default.css" rel="stylesheet">
        <link href="css/prettyPhoto.css" rel="stylesheet">
       <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="js/html5shiv.js"></script>
        <script src="js/respond.min.js"></script>
        <![endif]-->

<style>

.error{
	
	color:red;
}
</style>



    </head>

    <body>
        
       <?php include("header.php");?>
        
        <section>
            <div class="mainwrapper">
               
			   <?php include("sidebar.php");?>
                
                <div class="mainpanel">
                    <div class="pageheader">
                        <div class="media">
                            <div class="pageicon pull-left" style="padding-top:10px;">
                                <i class="fa fa-user"></i>
                            </div>
                            <div class="media-body">
                                <ul class="breadcrumb">
                                    <li><a href=""><i class="glyphicon glyphicon-home"></i></a></li>
                                    <li><a href="">Home</a></li>
                                    <li>Profile </li>
                                </ul>
                                <h4>Profile </h4>
                            </div>
                        </div><!-- media -->
                    </div><!-- pageheader -->
                    
                    <div class="contentpanel">




                    <?php echo $mesage ?>


                        
                        <div class="row">
                            <div class="col-sm-4 col-md-3">
                                <div class="text-center">
                                    <img src="<?php echo $row1['photo'];?>"  style="width:200px; height:200px; border-radius:200px; class="img-circle img-offline img-responsive img-profile" alt="" />
                                    <h4 class="profile-name mb5"><?php echo $row1['first_name'];?></h4>
                                    <!--<div><i class="fa fa-map-marker"></i> San Francisco, California, USA</div>
                                    <div><i class="fa fa-briefcase"></i> Software Engineer at <a href="">Company, Inc.</a></div>-->
                                
                                    <div class="mb20"></div>
                                
                                   <!-- <div class="btn-group">
                                        <button class="btn btn-primary btn-bordered">Following</button>
                                        <button class="btn btn-primary btn-bordered">Followers</button>
                                    </div>-->
                                </div><!-- text-center -->
                                
                                <br />
                              
                               <!-- <h5 class="md-title">About</h5>
                                <p class="mb30">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitat... <a href="">Show More</a></p>
                              
                                <h5 class="md-title">Connect</h5>
                                <ul class="list-unstyled social-list">
                                    <li><i class="fa fa-twitter"></i> <a href="">twitter.com/eileensideways</a></li>
                                    <li><i class="fa fa-facebook"></i> <a href="">facebook.com/eileen</a></li>
                                    <li><i class="fa fa-youtube"></i> <a href="">youtube.com/eileen22</a></li>
                                    <li><i class="fa fa-linkedin"></i> <a href="">linkedin.com/4ever-eileen</a></li>
                                    <li><i class="fa fa-pinterest"></i> <a href="">pinterest.com/eileen</a></li>
                                    <li><i class="fa fa-instagram"></i> <a href="">instagram.com/eiside</a></li>
                                </ul>
                              
                                <div class="mb30"></div>
                              
                                <h5 class="md-title">Address</h5>
                                <address>
                                    795 Folsom Ave, Suite 600<br>
                                    San Francisco, CA 94107<br>
                                    <abbr title="Phone">P:</abbr> (123) 456-7890
                                </address>-->
                              
                            </div><!-- col-sm-4 col-md-3 -->
                            
                            <div class="col-sm-8 col-md-9">
                              
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs nav-line" id="myTab">
                                    <li class="active"><a href="#activities" data-toggle="tab"><strong>Setting</strong></a></li>
                                    <li><a href="#followers" data-toggle="tab"><strong>Password</strong></a></li>
                                    
                                </ul>
                            
                                <!-- Tab panes -->
                                <div class="tab-content nopadding noborder">
                                    <div class="tab-pane active" id="activities">
                                        <div class="activity-list">  
                                          
										   <div class="col-md-12">
                                <form id="basicForm" action="" method="post" enctype="multipart/form-data" autocomplete="off">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        
                                        <h4 class="panel-title">Edit Profile</h4>
                                        <!--<p>Please provide your name, email address (won't be published) and a comment.</p>-->
                                    </div><!-- panel-heading -->
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">First Name <span class="asterisk">*</span></label>
                                                <div class="col-sm-9">
                                                    <input type="text" name="first_name" class="form-control" placeholder="Please Enter First Name" value="<?php echo $row1['first_name'] ?>"  />
                                                </div>
                                            </div><!-- form-group -->

                                             <div class="form-group">
                                                <label class="col-sm-3 control-label">Last Name <span class="asterisk">*</span></label>
                                                <div class="col-sm-9">
                                                    <input type="text" name="last_name" class="form-control" placeholder="Please Enter Last Name" value="<?php echo $row1['last_name'] ?>" />
                                                </div>
                                            </div><!-- form-group -->
                                          
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Email <span class="asterisk">*</span></label>
                                                <div class="col-sm-9">
                                                    <input type="email" name="email" class="form-control" placeholder="Please Enter Email" value="<?php echo $row1['email'] ?>"  />
                                                </div>
                                            </div><!-- form-group -->
                                            
                                             <div class="form-group">
                                                <label class="col-sm-3 control-label">Phone <span class="asterisk">*</span></label>
                                                <div class="col-sm-9">
                                                    <input type="text" name="phone" class="form-control" placeholder="Please Enter Phone" value="<?php echo $row1['phone'] ?>"  />
                                                </div>
                                            </div><!-- form-group -->
                                            

                                             <div class="form-group">
                                                 <label class="col-sm-3 control-label"><span class="asterisk"></span></label>
                                                <div class="col-sm-9">
                                                  <img class="img-circle" style="width:70px; height:70px; border-radius:70px;
                                             " src="<?php echo $row1['photo'];?>">  
                                                </div>
                                            </div><!-- form-group -->  
                                          
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Image <span class="asterisk">*</span></label>
                                                <div class="col-sm-9">
                                                    <input type="file" name="file" onchange="myFunction(this)"class="form-control" />
                                                </div>
                                            </div><!-- form-group -->
                                            
                                           
                                        </div><!-- row -->
                                    </div><!-- panel-body -->
                                    <div class="panel-footer">
                                      <div class="row">
                                        <div class="col-sm-9 col-sm-offset-3">
                                            <button class="btn btn-primary mr5" name="changepic">Save Changes</button>
                                           <!-- <button type="reset" class="btn btn-dark">Reset</button>-->
                                        </div>
                                      </div>
                                    </div><!-- panel-footer -->  
                                </div><!-- panel -->
                                </form>
                                
                            </div><!-- col-md-12 -->

                                      										  
                                        </div><!-- activity-list -->
                                
                                       
                                    </div><!-- tab-pane -->
                                    
                                    <div class="tab-pane" id="followers">
                                
                                        <div class="follower-list">
                                          
										   <div class="col-md-12">
                                <form id="basicForm13" action="" method="post" enctype="multipart/form-data" autocomplete="off">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        
                                        <h4 class="panel-title">Change Password</h4>
                                        <!--<p>Please provide your name, email address (won't be published) and a comment.</p>-->
                                    </div><!-- panel-heading -->
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Current Password<span class="asterisk">*</span></label>
                                                <div class="col-sm-9">
                                                    <input type="password" name="password" class="form-control" placeholder="Please Enter Current Password" />
                                                </div>
                                            </div><!-- form-group -->
                                          
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">New Password<span class="asterisk">*</span></label>
                                                <div class="col-sm-9">
                                                    <input type="password" name="newpass" class="form-control" placeholder="Please Enter New Password"  />
                                                </div>
                                            </div><!-- form-group -->
                                          
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Confirm Password</label>
                                                <div class="col-sm-9">
                                                    <input type="password" name="repass" class="form-control" Placeholder="please Enter Confirm Password" />
                                                </div>
                                            </div><!-- form-group -->
                                            
                                            
                                        </div><!-- row -->
                                    </div><!-- panel-body -->
                                    <div class="panel-footer">
                                      <div class="row">
                                        <div class="col-sm-9 col-sm-offset-3">
                                            <button class="btn btn-primary mr5" name="changepassword" >Submit</button>
                                            <!--<button type="reset" class="btn btn-dark">Reset</button>-->
                                        </div>
                                      </div>
                                    </div><!-- panel-footer -->  
                                </div><!-- panel -->
                                </form>
                                
                            </div><!-- col-md-12 -->
											
											
											
                                        </div><!--follower-list -->
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
<script type="text/javascript">
$(document).ready(function(){
	$('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
		localStorage.setItem('activeTab', $(e.target).attr('href'));
	});
	var activeTab = localStorage.getItem('activeTab');
	if(activeTab){
		$('#myTab a[href="' + activeTab + '"]').tab('show');
	}
});
</script>

<script src="js/jquery.validate.min.js"></script>
<script src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/additional-methods.js"></script>
<script type="text/javascript">
$(function() {

    $.validator.addMethod("loginRegex", function(value, element) {
        return this.optional(element) || /^[A-Za-z][A-Za-z\s]+$/i.test(value);
    }, "First name must contain only letters or space.");

$.validator.addMethod("LastRegex", function(value, element) {
        return this.optional(element) || /^[A-Za-z][A-Za-z\s]+$/i.test(value);
    }, "Last name must contain only letters or space.");



   $.validator.addMethod("PhoneRegex", function(value, element) {
        return this.optional(element) || /^[0-9]+$/i.test(value);
    }, "Enter Numbers only.");


            
                $("#basicForm").validate({
                    rules: {
                 first_name:{
				 required: true,
				loginRegex: true,
                           minlength: 3,
			   maxlength: 40
            },
                   last_name:{
				 required: true,
				 LastRegex: true,
                           minlength: 3,
			   maxlength: 40
            },
			
            email:{
				 required: true,
				 email:true,
			 maxlength: 50
            },
                          
              
        
                  phone:{

PhoneRegex:true,
				 required: true,
				 number:true,
 minlength: 10,
			 maxlength: 10
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
                 alphanumeric:true,
                minlength: 3,
				maxlength: 20
            },
         newpass: {
                required: true,
                 alphanumeric:true,
                minlength: 3,
				maxlength: 20
            },
         repass: {
                required: true,
                 alphanumeric:true,
                equalTo:"#newpass",
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
                     
				
  var y=document.getElementById('profile'); 



 y.setAttribute("class", "active");


</script>

<script type="text/javascript">




function myFunction(me) {

if(me.files[0].size>2097152)
{
alert('maximum 2 MB file size allowed');
me.value='';
return;


}
    

var val = me.value.toLowerCase();
var regex = new RegExp("(.*?)\.(jpg|jpeg|png)$");
 
if(!(regex.test(val))) {
me.value='';
alert('Unsupported file accept only jpg | jpeg | png file');
return;

} 
   
    
        meid=me.id;
    var divid1=meid.split("-");
     
      
      str1='#d';
var tt=str1.concat(divid1[1]);

    
            var files = !!me.files ? me.files : [];
        if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support
        
        if (/^image/.test( files[0].type)){ // only image file
            var reader = new FileReader(); // instance of the FileReader
            reader.readAsDataURL(files[0]); // read the local file
            
            reader.onloadend = function(){ // set image data as background of div
                $(tt).css("background-image", "url("+this.result+")");
            }
        }
        
        
}




var i="<?php echo $cimage;?>";

var p="<?php echo $cimage;?>";

var k=1;




function removeclick(el)
{



if(el.id.length>0)
{
$.post('removeimage.php', { value : el.id }, responsedb, 'html');
}
 

el.parentNode.parentNode.parentNode.removeChild(el.parentNode.parentNode); 


p--;
if(p<=0)
{
var trm1=document.getElementById('trm1');    
 trm1.setAttribute("style", "");
document.getElementById('uploadFile-90').disabled=false;
}
else
{
var trm1=document.getElementById('trm1');    
 trm1.setAttribute("style", "display:none");
document.getElementById('uploadFile-90').disabled=true;
}

}

function responsedb(data) {

 /*alert(data);*/

   
}
 
function cE(el){ 
this.obj =document.createElement(el); 
return this.obj 
} 
function cA(obj,att,val){ 
obj.setAttribute(att,val); 
return 
} 
</script> 

    </body>
</html>
<?php } }?>