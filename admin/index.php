<?php include("con1.php");?>
<?php 
$msg="";
$mesage="";


if($_POST){
	extract($_POST);
	session_start();
	
 if(!empty($_POST['email']) && !empty($_POST['password']))
     {
     
	
$select=$mysqlipre->query("SELECT * FROM `admin_master` WHERE `email`='$email' AND `password`='$password'");
if($select->num_rows>0){
	
	if(isset($_POST['remember']))
	{




$year=60*60*24*365;
setcookie('email',$_POST['email'], time()+9999999);
setcookie('password', $_POST['password'], time()+9999999);
setcookie('remember', $_POST['password'],time()+9999999);
}

else {

setcookie('email', 'content', 1);
setcookie('password', 'content', 1);
setcookie('remember', 'content', 1);

}
$roww=$select->fetch_array(MYSQLI_ASSOC);

$_SESSION['adminid']=$roww['adminid'];	

 $mesage=" Login Successfully !!! Please Wait... ";
 header("refresh:1;url=dashboard.php"); 

}
else{
	
	$msg='<center><h3><div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong> Email and Password are not match ! Try Again!</strong>
                </div></h3></center>';
	  
	 
}
	 }
}
?>



<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Young Yaa | Index</title>

        <link href="css/style.default.css" rel="stylesheet">
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

    <body class="signin"  style="background-color:#24A2DC;">
	
        <center><h3><font color="red"><?php echo $msg;?></font></h3></center>
<center><h3><font color="white"><?php echo $mesage;?></font></h3></center>
        
        <section>
            
            <div class="panel panel-signin">
                <div class="panel-body">
                    <div class="logo text-center">
                        <img src="image/yy_170.png" alt="Young Yaa Logo" >
                    </div>
                    <br />
                    <!--<h4 class="text-center mb5">Already a Member?</h4>-->
                    <p class="text-center">Sign in to your account</p>
                    
                    <div class="mb30"></div>
                    
                    <form action="" method="post" id="loginform">
                        <div class="input-group mb15">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                            <input type="text" class="form-control" name="email" placeholder="Enter Email" value="<?php if(isset($_COOKIE['email'])) echo $_COOKIE['email']; ?>"/>
                        </div><!-- input-group -->
                        <div class="input-group mb15">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            <input type="password" class="form-control" name="password" placeholder="Enter Password" value="<?php if(isset($_COOKIE['password'])) echo $_COOKIE['password']; ?>"/>
                        </div><!-- input-group -->
                        
                        <div class="clearfix">
                            <div class="pull-left">
                                <div class="ckbox ckbox-primary mt10">
								
                                    <input name="remember" type="checkbox" id="rememberMe" value="remember" class="skin-square-orange" <?php if(isset($_COOKIE['remember'])) {
		echo 'checked="checked"';
	}
	else {
		echo '';
	}
	?> value="1"/>
                                    <label for="rememberMe">Remember Me</label>
                                </div>
                            </div>
                            <div class="pull-right">
                                <button type="submit" class="btn btn-success">Sign In <i class="fa fa-angle-right ml5"></i></button>
                            </div>
                        </div>                      
                    </form>
                    
                </div><!-- panel-body -->
                <div class="panel-footer">
                    <a href="forgot_password.php" class="btn btn-primary btn-block">Forgot Password ? Click Here..</a>
                </div><!-- panel-footer -->
            </div><!-- panel -->
            
        </section>


        <script src="js/jquery-1.11.1.min.js"></script>
        <script src="js/jquery-migrate-1.2.1.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/modernizr.min.js"></script>
        <script src="js/pace.min.js"></script>
        <script src="js/retina.min.js"></script>
        <script src="js/jquery.cookies.js"></script>

        <script src="js/custom.js"></script>
		 
     
     <script src="js/jquery.validate.min.js"></script>
	
	
	 
        <script type="text/javascript">
            // When the document is ready
            $(document).ready(function () {
            
                $("#loginform").validate({
                    rules: {
			
            email:{
				 required: true,
				 email:true,
			 maxlength: 50
            },
                          
                        password: {
                required: true,
                minlength: 3,
				maxlength: 20
            }
					
					},
                     messages: {
			
            email: {
		    required:"Please enter email"
		  	
			},
            password: {
                required: "Please enter password"
                
                 }
				 
				 
			
        },
        
        
                });
            });
        </script>

    </body>
</html>
