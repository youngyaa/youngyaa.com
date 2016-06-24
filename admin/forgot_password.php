<?php include("con1.php");?>
<?php 
$msg="";
$mesage="";

if($_POST){
	 if(!empty($_POST['email']) )
     {

$select="select `first_name`,`password` from `admin_master` where `email`='".$_POST['email']."'";

$obj=$mysqlipre->query($select);
$row=$obj->fetch_array(MYSQLI_ASSOC);
$password=$row['password'];
$name=$row['first_name'];
if($obj->num_rows>0)
{
include("mail/PHPMailerAutoload.php");

$mail = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'mail.youngdecadeprojects.biz';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'youngdecade@youngdecadeprojects.biz';                 // SMTP username
$mail->Password = 'youngdecade';                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 25;                                    // TCP port to connect to

$mail->From ='youngdecade@youngdecadeprojects.biz';
$mail->FromName = 'youngdecade';
//$mail->addAddress('dipika.youngdecade@gmail.com', 'sumit ji');  

//$mail->addAddress('sumit.taskmanager@gmail.com', 'sumit ji');  
$mail->addAddress($_POST['email']);    // Add a recipient
/*
$mail->addAddress('ellen@example.com');               // Name is optional
$mail->addReplyTo('info@example.com', 'Information');
$mail->addCC('cc@example.com');
$mail->addBCC('bcc@example.com');

*/

//$mail->addAttachment('PHPMailer-master.zip');         // Add attachments
//$mail->addAttachment($db);    // Optional name



$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'Password Recovery From Young Yaa';
$mail->Body    = '<html><body bgcolor="#05BCDB">
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#05BCDB">
  <tr>
    <td><table width="600" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" align="center">
        <tr>
          <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="61"><a href= "" target="_blank"><img src="http://youngdecadeprojects.biz/patient_portal/admin/img/PROMO-GREEN2_01_01" width="61" height="76" border="0" alt=""/></a></td>
                <td width="144"><a href= "" target="_blank"><img src="http://youngdecadeprojects.biz/whatsapp1/admin/image/yy_180.png" width="144" height="76" border="0" alt=""/></a></td>
                <td width="393"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td height="46" align="right" valign="middle"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td width="67%" align="right"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#68696a; font-size:8px; text-transform:uppercase"><a href= "" style="color:#68696a; text-decoration:none"><strong></strong></a></font></td>
                            <td width="29%" align="right"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#68696a; font-size:8px"><a href= "" style="color:#68696a; text-decoration:none; text-transform:uppercase"><strong></strong></a></font></td>
                            <td width="4%">&nbsp;</td>
                          </tr>
                        </table></td>
                    </tr>
                    <tr>
                      <td height="30"><img src="http://youngdecadeprojects.biz/patient_portal/admin/img/PROMO-GREEN2_01_04.jpg" width="393" height="30" border="0" alt=""/></td>
                    </tr>
                  </table></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td align="center"><a href= "" target="_blank"><img src="http://myprotector.org/wp-content/uploads/2015/09/Group-of-people-texting-on-their-mobile-640x360.jpg" alt="" width="598" height="323" border="0"/></a></td>
        </tr>
        <tr>
          <td align="center" valign="middle"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="2%">&nbsp;</td>
                <td width="96%" align="center" style="border-bottom:1px solid #000000" height="70"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#05BCDB; font-size:30px; text-transform:uppercase"><strong>Welcome in Young Yaa </strong></font></td>
                <td width="2%">&nbsp;</td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="5%">&nbsp;</td>

                <td width="90%" align="center" valign="middle"><font style="font-family: Verdana, Geneva, sans-serif; color:#68696a; font-size:12px; line-height:20px; text-transform:uppercase"><strong>Hello ,<b style="color:#05BCDB;"> '.$name.'</b><br>
<br>
Your Password is :</strong></font><br />
                  <font style="font-family:Verdana, Geneva, sans-serif; color:#05BCDB; font-size:12px; line-height:20px"><a href= "" style="color:#05BCDB; text-decoration:none"><strong>&lt; &nbsp; '.$password.' &nbsp; &gt;</strong></a></font></td>
                <td width="5%">&nbsp;</td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
         <!-- <td><table width="600" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="18">&nbsp;</td>
                <td width="175" align="center" valign="top"><table width="175" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td  bgcolor="#f58220"><img src="http://youngdecadeprojects.biz/chefapp/admin/image/PROMO-GREEN2_04_01.jpg" width="175" height="14" style="display:block" border="0" alt=""/></td>
                    </tr>
                    <tr>
                      <td height="30" align="center" valign="middle" bgcolor="#f58220"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#ffffff; font-size:20px; text-transform:uppercase"><strong>UPCOMING 2</strong></font></td>
                    </tr>
                    <tr>
                      <td bgcolor="#f58220"><img src="http://youngdecadeprojects.biz/chefapp/admin/image/PROMO-GREEN2_00.jpg" alt="" width="175" height="18" /></td>
                    </tr>
                    <tr>
                      <td align="center" valign="middle" bgcolor="#f58220"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#ffffff; font-size:14px"><strong><a href="" target="_blank" style="color:#ffffff; text-decoration:none">view details</a></strong></font></td>
                    </tr>
                    <tr>
                      <td align="center" valign="middle" bgcolor="#f58220">&nbsp;</td>
                    </tr>
                  </table></td>
                <td width="19">&nbsp;</td>
                <td width="175" align="center" valign="top"><table width="175" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td  bgcolor="#f58220"><img src="http://youngdecadeprojects.biz/chefapp/admin/image/PROMO-GREEN2_04_01.jpg" width="175" height="14" style="display:block" border="0" alt=""/></td>
                  </tr>
                  <tr>
                    <td height="30" align="center" valign="middle" bgcolor="#f58220"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#ffffff; font-size:20px; text-transform:uppercase"><strong>UPCOMING 2</strong></font></td>
                  </tr>
                  <tr>
                    <td bgcolor="#f58220"><img src="http://youngdecadeprojects.biz/chefapp/admin/image/PROMO-GREEN2_00.jpg" alt="" width="175" height="18" /></td>
                  </tr>
                  <tr>
                    <td align="center" valign="middle" bgcolor="#f58220"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#ffffff; font-size:14px"><strong><a href="" target="_blank" style="color:#ffffff; text-decoration:none">view details</a></strong></font></td>
                  </tr>
                  <tr>
                    <td align="center" valign="middle" bgcolor="#f58220">&nbsp;</td>
                  </tr>
                </table></td>
                <td width="19">&nbsp;</td>
                <td width="175" align="center" valign="top"><table width="175" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td  bgcolor="#f58220"><img src="images/PROMO-GREEN2_04_01.jpg" width="175" height="14" style="display:block" border="0" alt=""/></td>
                  </tr>
                  <tr>
                    <td height="30" align="center" valign="middle" bgcolor="#f58220"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#ffffff; font-size:20px; text-transform:uppercase"><strong>UPCOMING 2</strong></font></td>
                  </tr>
                  <tr>
                    <td bgcolor="#f58220"><img src="images/PROMO-GREEN2_00.jpg" alt="" width="175" height="18" /></td>
                  </tr>
                  <tr>
                    <td align="center" valign="middle" bgcolor="#f58220"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#ffffff; font-size:14px"><strong><a href="" target="_blank" style="color:#ffffff; text-decoration:none">view details</a></strong></font></td>
                  </tr>
                  <tr>
                    <td align="center" valign="middle" bgcolor="#f58220">&nbsp;</td>
                  </tr>
                </table></td>
                <td width="19">&nbsp;</td>
              </tr>
            </table></td>-->
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td><img src="http://youngdecadeprojects.biz/patient_portal/admin/img/PROMO-GREEN2_07.jpg" width="598" height="7" style="display:block" border="0" alt=""/></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
         <!-- <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="13%" align="center">&nbsp;</td>
              <td width="14%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><a href= "" style="color:#010203; text-decoration:none"><strong>UNSUBSCRIBE </strong></a></font></td>
              <td width="2%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><strong>|</strong></font></td>
              <td width="9%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><a href= "" style="color:#010203; text-decoration:none"><strong>ABOUT </strong></a></font></td>
              <td width="2%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><strong>|</strong></font></td>
              <td width="10%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><a href= "" style="color:#010203; text-decoration:none"><strong>PRESS </strong></a></font></td>
              <td width="2%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><strong>|</strong></font></td>
              <td width="11%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><a href= "" style="color:#010203; text-decoration:none"><strong>CONTACT </strong></a></font></td>
              <td width="2%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><strong>|</strong></font></td>
              <td width="17%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><a href= "" style="color:#010203; text-decoration:none"><strong>STAY CONNECTED</strong></a></font></td>
              <td width="4%" align="right"><a href="https://www.facebook.com/" target="_blank"><img src="http://youngdecadeprojects.biz/chefapp/admin/image/PROMO-GREEN2_09_01.jpg" alt="facebook" width="21" height="19" border="0" /></a></td>
              <td width="5%" align="center"><a href="https://twitter.com/" target="_blank"><img src="http://youngdecadeprojects.biz/chefapp/admin/image/PROMO-GREEN2_09_02.jpg" alt="twitter" width="23" height="19" border="0" /></a></td>
              <td width="4%" align="right"><a href="http://www.linkedin.com/" target="_blank"><img src="http://youngdecadeprojects.biz/chefapp/admin/image/PROMO-GREEN2_09_03.jpg" alt="linkedin" width="20" height="19" border="0" /></a></td>
              <td width="5%">&nbsp;</td>
            </tr>
          </table></td>-->
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
           <td align="center"><font style="font-family:"Myriad Pro", Helvetica, Arial, sans-serif; color:#231f20; font-size:8px"><strong>Young Decade IT Software Solution, 101,First floor,Westend Corporate,New Palasia Indore | Tel:  0731-6999919 | <a href= "#" style="color:#05BCDB; text-decoration:none">info@youngdecade.com</a></strong></font></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
      </table></td>
  </tr>
</table>
</body><html>';
if($mail->send()) {
				
				$mesage='<center><h3><div class="alert alert-success alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong>Password  Successfully sent on your Email Id.</strong>
                </div></h3></center>';
		
                           }
     else
         {
			 
			 $msg='<center><h3><div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong>Email could not be sent ! Try Again !</strong>
                </div></h3></center>';
            
         }
}
        else
       {
		   
       $msg='<center><h3><div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong>No user exist with this email id ! Check Mail Id And Try Again !</strong>
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

        <title>Young Yaa | Forgot Password</title>

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

    <body class="signin" style="background-color:#24A2DC;">
       
        <center><h3><font color="red"><?php echo $msg;?></font></h3></center>
<center><h3><font color="white"><?php echo $mesage;?></font></h3></center>
        <section>
            
            <div class="panel panel-signup">
 
                <div class="panel-body">
                    <div class="logo text-center">
                        <img src="image/yy_180.png" alt="Young Yaa Logo" >
                    </div>
                    <br />
                    <h4 class="text-center mb5">Forgot Password ?</h4>
                    <p class="text-center">Please enter your Email below</p>
                    
                    <div class="mb30"></div>
                    
                    <form action="" method="post" id="loginform">
                       
                        <div class="row">
						<div class="col-sm-3" ><div class="col-sm-5" ></div>
						<label style="padding: 10px;color:#428bca"> Email :</label>
						</div>
                            <div class="col-sm-7">
                                <div class="input-group mb15">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" placeholder="Enter Email Address">
                                </div><!-- input-group -->
                            </div>
                           
                        </div><!-- row -->
                        <br />
                        <div class="clearfix">
                           
                            <div class="col-sm-10"><div class="col-sm-5" ></div>
                                <button type="submit" class="btn btn-success">Reset Your Password <i class="fa fa-angle-right ml5"></i></button>
                            </div>
                        </div>
                    </form>
                    
                </div><!-- panel-body -->
                <div class="panel-footer">
                    <a href="index.php" class="btn btn-primary btn-block"> Sign In</a>
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
		<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.14.0/jquery.validate.min.js" type="text/javascript"></script>
	
	
	 
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
                   
					
					},
                     messages: {
			
            email: {
		    required:"Please enter email"
		  	
			}
				 
				 
			
        },
        
        
                });
            });
        </script>

    </body>
</html>