<?php include("session.php"); 
  include("con1.php");

?>
<?php require_once('./config.php'); ?>
<?php
if(empty($_SESSION['adminid'])){


echo ("<script>
    window.alert('Please Login First')
    window.location.href='index.php';
    </script>"); 	
	return;
exit;
}

?>
<?php
 if($_GET['id']==""){
echo"<script>
    window.alert('Cant Proceed Need of Order id')
    window.location.href='manage_order.php';
    </script>"; 	
	return;
exit;
}

if(isset($_POST['send']))
{
$select16=$mysqlipre->query("select * FROM `commission_master`");

if($select16->num_rows<=0)
{
$msg="no ";

}
$row16=$select16->fetch_array(MYSQLI_ASSOC);
$commission=$row16['commission'];



$commission_amount=$_POST['amount']*$commission/100;



$cook_amount=$_POST['amount']-$commission_amount;

$amount=round($cook_amount*100);

$dest=$_POST['destinat'];

$charge=\Stripe\Stripe::setApiKey('sk_live_foIJKEZYedxqdDEWtPOJKsuT');
$charge=\Stripe\Transfer::create(array(
  'amount' => $amount,
  'currency' => 'SEK',
  'destination' => $dest  
));

$name=$_POST['name'];
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

$mail->Subject = 'Select Cook App';
$mail->Body    = '<html><body bgcolor="#FF7401">
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FF7401">
  <tr>
    <td><table width="600" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" align="center">
        <tr>
          <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="61"><a href= "" target="_blank"><img src="http://youngdecadeprojects.biz/chefapp/admin/image/PROMO-GREEN2_01_01.jpg" width="61" height="76" border="0" alt=""/></a></td>
                <td width="144"><a href= "" target="_blank"><img src="http://youngdecadeprojects.biz/chefapp/admin/image/logo.png" width="144" height="76" border="0" alt=""/></a></td>
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
                      <td height="30"><img src="http://youngdecadeprojects.biz/chefapp/admin/image/PROMO-GREEN2_01_04.jpg" width="393" height="30" border="0" alt=""/></td>
                    </tr>
                  </table></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td align="center"><a href= "" target="_blank"><img src="http://youngdecadeprojects.biz/chefapp/admin/image/Prep-Produce-Advance.jpg" alt="" width="598" height="323" border="0"/></a></td>
        </tr>
        <tr>
          <td align="center" valign="middle"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="2%">&nbsp;</td>
                <td width="96%" align="center" style="border-bottom:1px solid #000000" height="70"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#68696a; font-size:30px; text-transform:uppercase"><strong>Select Cook App </strong></font></td>
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

                <td width="90%" align="center" valign="middle"><font style="font-family: Verdana, Geneva, sans-serif; color:#68696a; font-size:12px; line-height:20px; text-transform:uppercase"><strong>Hello ,<b style="color:#f58220;"> '.$name.'</b><br>
<br>
Information :</strong></font><br />
                  <font style="font-family:Verdana, Geneva, sans-serif; color:#f58220; font-size:12px; line-height:20px"><a href= "" style="color:#f58220; text-decoration:none"><strong>&lt; &nbsp; Your Payment is Done For Your Item Please Check Your Account Detail &nbsp; &gt;</strong></a></font></td>
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
          <td><img src="http://youngdecadeprojects.biz/chefapp/admin/image/PROMO-GREEN2_07.jpg" width="598" height="7" style="display:block" border="0" alt=""/></td>
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
           <td align="center"><font style="font-family:"Myriad Pro", Helvetica, Arial, sans-serif; color:#231f20; font-size:8px"><strong>Young Decade IT Software Solution, 101,First floor,Westend Corporate,New Palasia Indore | Tel:  0731-6999919 | <a href= "#" style="color:#f58220 text-decoration:none">info@youngdecade.com</a></strong></font></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
      </table></td>
  </tr>
</table>
</body><html>';

if($mail->send()) {



if($_POST['device_type']=='ios')
{

$token=$_POST['ios_token'];

}
if(!empty($token))
{
$deviceToken=$token ;

$message='Admin Transfer Payment of Your Item';

$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck.pem');
stream_context_set_option($ctx, 'ssl', 'passphrase', '1234');

$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', 
    $err, 
    $errstr, 
    60, 
    STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, 
    $ctx);


$body1 = array(
    'badge' => +1,
    'alert' => $message,
    'sound' => 'default'
);


  $body= array("aps" =>$body1);
$payload = json_encode($body);


$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;


$result = fwrite($fp, $msg, strlen($msg));

  }
if($_POST['device_type']=='android')
{

$token=$_POST['android_token'];

}
if(!empty($token))
{
define("GOOGLE_API_KEY","AIzaSyDY0xuW649oMnlQ0DUMc6CxEECrME8hX5I");                        
                     
$registatoin_ids = array($token);
   
  $message = array("Information"=>"Admin Transfer Payment of Your Item");
       
$url = 'https://gcm-http.googleapis.com/gcm/send';
 
        $fields = array(
            'registration_ids' => $registatoin_ids,
            'data' => $message,
        );
 
        $headers = array(
            'Authorization: key=' . GOOGLE_API_KEY,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();
 
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
 
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
 
        // Close connection
        curl_close($ch);
       $result;
}


$mssgg16='<center><h3><div class="alert alert-success alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong> Payment Send Successfully !</strong>
                </div></h3></center>'; 
$sql=$mysqlipre->query("UPDATE `order_chef_master` SET `admin_payment_status`='1' where `id`='".$_GET['id']."'");
$i2=$mysqlipre->affected_rows;
if($i2<=0){
$mssgg16='<center><h3><div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong>Sorry </strong> Error.please try again!
                </div></h3></center>';

}

}
}
  $select="select * FROM `order_chef_master` where `id`='".$_GET['id']."'";
	$obj=$mysqlipre->query($select);
	if($obj->num_rows<=0){

$msg="<b><center><h4>No Order Available</h4></center></b>"; 	
	
	     
                 }
                 $row=$obj->fetch_array(MYSQLI_ASSOC);
				 ?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Select Cook | Order Details</title>

        <link href="css/style.default.css" rel="stylesheet">
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="js/html5shiv.js"></script>
        <script src="js/respond.min.js"></script>
        <![endif]-->
<style>
.btn-danger {
    background-color: #c9302c;
border-color: #ac2925;
}
.btn-success {
background-color: #449D44;
    border-color: #398439;
}


</style>
    </head>

    <body style="font-size: 16px;">
        
         <?php include("header.php");?>
        
        <section>
            <div class="mainwrapper">
			
                <?php include("sidebar.php");?>
                
                <div class="mainpanel">
                    <div class="pageheader">
                        <div class="media">
                            <div class="pageicon pull-left" style="padding-top:10px;">
                                <i class="fa fa-list"></i>
                            </div>
                            <div class="media-body">
                                <ul class="breadcrumb">
                                    <li><a href=""><i class="glyphicon glyphicon-home"></i></a></li>
                                    <li><a href="">Home</a></li>
                                    <li><a href="manageorder.php">Manage Order</a></li>
                                </ul>
                                <h4>Order Details</h4>
                            </div>
                        </div><!-- media -->
                    </div><!-- pageheader -->
<?php echo $mssgg16 ?>
					<?php $result80=$mysqlipre->query("select * FROM `chef_master` where `chef_id`='".$row['chef_id']."' ");

if($result80->num_rows<=0)
{



$msg="no cook found";

}
$row80=$result80->fetch_array(MYSQLI_ASSOC);
?>
                    <div class="contentpanel">
                        
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        
                                        <h5 class="lg-title mb10"></h5>
                                        <img style="border-radius:50px;" src="<?php echo $row80['photo'] ?>" height="50px" width="50px" class="img-circle" alt="" />
                                        <address>
                                            <strong><?php echo $row80['kitchen_name'] ?></strong><br>
<?php echo $row80['name'] ?><br>
											<?php echo $row80['address'] ?><br>
                                            <?php echo $row80['area'] ?><br>
                                            <?php echo $row80['city'] ?><br>
                                            <abbr title="pincode">Pincode:</abbr> <?php echo $row80['pincode'] ?>
                                        </address>
										
                                        <?php $date13=$row80['inserttime'];
										$datetime=explode(' ',$date13);
										$date=$datetime[0];
										$time=$datetime[1];
										?>
                                    </div><!-- col-sm-6 -->
                                    
                                    <div class="col-sm-6 text-right">
									<p><strong>Date:</strong><?php echo $date ?>  </p><!--
                                        <h5 class="subtitle mb10">Invoice No.</h5>
                                        <h4 class="text-primary">INV-000464F4-00</h4>
                                        
                                        <h5 class="subtitle mb10">To</h5>
                                        <address>
                                            <strong>ThemePixels, Inc.</strong><br>
                                            795 Folsom Ave, Suite 600<br>
                                            San Francisco, CA 94107<br>
                                            <abbr title="Phone">P:</abbr> (123) 456-7890
                                        </address>
                                        
                                        <p><strong>Invoice Date:</strong> January 20, 2014</p>
                                        <p><strong>Due Date:</strong> January 22, 2014</p>-->
                                        
                                    </div>
                                </div><!-- row -->
                                
                                <div class="table-responsive">
                                <table class="table table-bordered table-dark table-invoice">
                                <thead>
                                  <tr>
                                    <th style="text-align: center; background-color: #FF7401;">Dish</th>
                                    <th style="text-align: center; background-color: #FF7401;">User</th>
                                    
                                    <th style="text-align: center; background-color: #FF7401;">Image</th>
                                    <th style="text-align: center; background-color: #FF7401;">Price</th>
                                    <th style="text-align: center; background-color: #FF7401;">quantity</th>
                                    <th style="text-align: center; background-color: #FF7401;">Total Amount</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  
								  <?php 
					$select1="select * FROM `order_item_master` where `chef_id`='".$row['chef_id']."' and user_id='".$row['user_id']."' and order_unique_number='".$row['order_unique_number']."'";
	$obj1=$mysqlipre->query($select1);
	if($obj1->num_rows<=0){

echo"<b><center><h4>No cook Available</h4></center></b>"; 	
	
	     
                 }
                 while($row1=$obj1->fetch_array(MYSQLI_ASSOC))
				 {
					$result791=$mysqlipre->query("select * FROM `user_master` where user_id='".$row1['user_id']."' ");

if($result791->num_rows<=0)
{
$msg="no user";

}
$resdata1=$result791->fetch_array(MYSQLI_ASSOC);

$result793=$mysqlipre->query("select * FROM `chef_dish_master` where dish_id='".$row1['dish_id']."'");

if($result793->num_rows<=0)
{



$msg="no dish";

}

$resdata=$result793->fetch_array(MYSQLI_ASSOC);

$result7931=$mysqlipre->query("select * FROM `chef_category_master` where cat_id='".$resdata['cat_id']."'");

if($result7931->num_rows<=0)
{



$msg="no Category";

}

$resdata161=$result7931->fetch_array(MYSQLI_ASSOC);



					
                    ?>          
                                   <tr> <td style="text-align: center;">
<h5><a href=""><?php echo $resdata['dish_name'];?></a></h5>
                                        <p style="word-break:break-word;"><?php echo $resdata['dish_description'];?></p></td>
                                       <td style="text-align: center;"><h5><a href=""><?php echo $resdata1['first_name'];?></a></h5>
                                        <p><?php echo $resdata1['email'];?></p></td>
                                    
                                    <td style="text-align: center;"><img style="border-radius:50px;" height="50px" width="50px" src="<?php echo $resdata161['cat_image'];?>"</img></td>
                                    <td style="text-align: center;"><?php echo $row1['dish_price'];?></td>
                                    <td style="text-align: center;"><?php echo $row1['quantity'];?></td>
                                    <td style="text-align: center;"><?php echo $row1['total_amount'];?></td>
                                                                       
                                  </tr>
								   
				 <?php $total_amount += $row1['total_amount'];
				 }?>  
                                </tbody>
                              </table>
                              </div><!-- table-responsive -->
                              
                                <table class="table table-total">
                                    <tbody>
                                       <!--<tr>
                                            <td>Sub Total:</td>
                                            <td>$849.00</td>
                                        </tr>
                                        <tr>
                                            <td>VAT:</td>
                                            <td>$67.23</td>
                                        </tr>-->
                                        <tr>
                                            <td>TOTAL:</td>
                                            <td style="text-align: center;"><?php echo $total_amount ?>.00</td>
                                        </tr>
				
				 
                                    </tbody>
                                </table>
                            
                                <div class="text-right btn-invoice">
								<?php if($row['status']=='accept')
								{?>
                                    <button class="btn btn-success btn-lg mr5"><i class="fa fa-check mr5"></i>Accept </button>
								<?php } else{ ?>
								<button class="btn btn-danger btn-lg mr5"><i class="fa fa-times mr5"></i>Not Accepted </button>
								<?php }?>
								<?php if($row['flag']=='delivered')
								{?>
                                    <button class="btn btn-success btn-lg"><i class="fa fa-home mr5"></i>Delivered</button>
									<?php } else{ ?>
									<button class="btn btn-danger btn-lg"><i class="fa fa-times mr5"></i>Not Delivered</button>
									<?php }?>
                                </div>
<?php
if($row['admin_payment_status']=='1'){ ?>
                                <div class="text-left btn-invoice"> 
 
<button class="btn btn-success btn-lg" ><i class="fa fa-check mr5"></i>Payment Done</button>

</div> 
<?php
}
else { ?>
<div class="text-left btn-invoice"> 
<form method="post">  
<button class="btn btn-info btn-lg" name="send"><i class="fa fa-dollar mr5"></i>Send Payment</button>
<input type="hidden" name="amount" value="<?php echo $total_amount ?> ">
<input type="hidden" name="name" value="<?php echo $row80['name'] ?>">
<input type="hidden" name="email" value="<?php echo $row80['email']  ?>">
<input type="hidden" name="device_type" value="<?php echo $row80['device_type'] ?> ">
<input type="hidden" name="android_token" value="<?php echo $row80['android_token']  ?>">
<input type="hidden" name="ios_token" value="<?php echo $row80['ios_token']  ?>">
<input type="hidden" name="destinat" value="<?php echo $row80['stripe_user_id'] ?>">
</form>
</div>
<?php } ?>
                                <div class="mb30"></div>
                                
                                <!--<div class="well nomargin">
                                    Thank you for your business. Please make sure all cheques payable to <strong>ThemeForest Web Services, Inc.</strong> Account No. 54353535
                                </div>-->
                                
                                
                            </div><!-- panel-body -->
                        </div><!-- panel -->  
                    
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

        <script src="js/custom.js"></script>
</script>
<script type="text/javascript">
                     
				
  var y=document.getElementById('order'); 



 y.setAttribute("class", "active");


</script>
    </body>
</html>