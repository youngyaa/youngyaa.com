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
$msg="";
$mesage="";
extract($_POST);
$insert_time= date('Y-m-d H:i:s');

if(isset($_POST['approve']))
{

$mysqlipre->query("UPDATE `chef_master` SET `admin_status`='yes',`inserttime`='$insert_time' WHERE chef_id='".$_POST['id']."'");

if($mysqlipre->affected_rows>0)
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

$mail->Subject = 'Approval Mail From Select Cook App admin';
$mail->Body    = '<body marginheight="0" topmargin="0" marginwidth="0" leftmargin="0" style="margin: 0px; background-color: #FF7401;  background-repeat: repeat;color:#fff;background:url();  padding: 4%;" bgcolor="#652416">

<h1 style="text-align: center;color:#fff;text-shadow: 6px 0px black;">Select Cook App</h1>
		
						<table cellspacing="0" cellpadding="0" width="100%" border="0" align="center" bgcolor="#FF7401" style="padding:4%;color:#000;  border: 2px dashed;" >
							<tbody>
								<tr>
									<td >
										<h2> Dear : '.$_POST['name'].' </h2>
									</td>
								</tr>

                                                                 <tr>

                                                                         <td>
										Your Profile Approved  By Admin 
									</td>
								</tr>


							</tbody>
						</table>
					
	</body>';

if($mail->send()) {

$sql16=$mysqlipre->query("select * from chef_master where chef_id='".$_POST['id']."'");

if($sql16->num_rows<=0){

$row16="no cook";
}
$row16=$sql16->fetch_array(MYSQLI_ASSOC);

if($row16['device_type']=='ios')
{

$token=$row16['ios_token'];

}
if(!empty($token))
{
$deviceToken=$token ;

$message='you have Approved by Admin ';

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


$mesage= '<center><h3><div class="alert alert-success alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong> Approve Cook successfully.</strong>
                </div></h3></center>'; 

}
 
else
{
$msg= '<center><h3><div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong> Mail not sent.please try again!</strong>
                </div></h3></center>';  


}

				     
				}
if($mysqlipre->affected_rows<=0)
				{
				 $msg= '<center><h3><div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong> Invalid Data. try again!</strong>
                </div></h3></center>';   
				}

}

if(isset($_POST['unapprove']))
{

$mysqlipre->query("UPDATE `chef_master` SET `admin_status`='no',`inserttime`='$insert_time' WHERE chef_id='".$_POST['id']."'");

if($mysqlipre->affected_rows>0)
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

$mail->Subject = 'Unapproval Mail From Select Cook App';
$mail->Body    = '<body marginheight="0" topmargin="0" marginwidth="0" leftmargin="0" style="margin: 0px; background-color: #FF7401;  background-repeat: repeat;color:#fff;background:url();  padding: 4%;" bgcolor="#652416">

<h1 style="text-align: center;color:#fff;text-shadow: 6px 0px black;">Select Cook App</h1>
		
						<table cellspacing="0" cellpadding="0" width="100%" border="0" align="center" bgcolor="#FF7401" style="padding:4%;color:#000;  border: 2px dashed;" >
							<tbody>
								<tr>
									<td >
										<h2> Dear : '.$_POST['name'].' </h2>
									</td>
								</tr>

                                                                 <tr>

                                                                         <td>
										Your Profile Unapproved  By Admin 
									</td>
								</tr>


							</tbody>
						</table>
					
	</body>';

if($mail->send()) {

$sql16=$mysqlipre->query("select * from chef_master where chef_id='".$_POST['id']."'");

if($sql16->num_rows<=0){

$row16="no cook";
}
$row16=$sql16->fetch_array(MYSQLI_ASSOC);

if($row16['device_type']=='ios')
{

$token=$row16['ios_token'];

}
if(!empty($token))
{
$deviceToken=$token ;

$message='you have Unapproved by Admin ';

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


$mesage='<center><h3><div class="alert alert-success alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong> Unapprove Cook successfully</strong>
                </div></h3></center>';
}
else
{
	$msg='<center><h3><div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong> Mail not sent.please try again!</strong>
                </div></h3></center>'; 
}
		    
	}
if($mysqlipre->affected_rows<=0)
		{
	$msg='<center><h3><div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong> Invalid Data. try again!</strong>
                </div></h3></center>';      
	}

}
if(isset($_POST['delete'])){
	
	
$delete="DELETE FROM `chef_master` WHERE `chef_id`='".$_POST['id']."'";
$obj=$mysqlipre->query($delete);
if($mysqlipre->affected_rows>0){
	
	
	
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

$mail->Subject = 'Remove Mail From Select Cook App';
$mail->Body    = 'Hello ! Admin Remove You Successfully.Kindly Contact Us....';
 
if($mail->send()) {
                $mesage='<center><h3><div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong>  Cook  Deleted Successfully!</strong>
                </div></h3></center>';
				 
			
				
                           }
     else
         {
            $msg='<center><h3><div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong> Message could not be sent, please try again!</strong>
                </div></h3></center>';
         }
}
else{
	
	$msg='<center><h3><div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong> Error in Deletion of Cook, please try again!</strong>
                </div></h3></center>';
}

}

?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Select Cook | Manage Cook</title>

        <link href="css/style.default.css" rel="stylesheet">
        <link href="css/select2.css" rel="stylesheet" />
        <link href="css/style.datatables.css" rel="stylesheet">
        <link href="//cdn.datatables.net/responsive/1.0.1/css/dataTables.responsive.css" rel="stylesheet">
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
                            <div class="pageicon pull-left" style="padding-top:10px;">
                                <i class="fa fa-users"></i>
                            </div>
                            <div class="media-body">
                                <ul class="breadcrumb">
                                    <li><a href=""><i class="glyphicon glyphicon-home"></i></a></li>
                                    <li><a href="">Home</a></li>
                                    <li>Manage Cook</li>
                                </ul>
                                <h4>Manage Cook</h4>
                            </div>
                        </div><!-- media -->
                    </div><!-- pageheader -->



                         <?php echo $msg ?>
                          <?php echo $mesage ?>

                    		<?php  
$select="select * FROM `chef_master`";
	$obj=$mysqlipre->query($select);
	if($obj->num_rows<=0){

echo "<center><b>No Cook Available</b></center>";	
	     
                 }
                 else
                 {	
?>



                    
                    <div class="contentpanel">
                       <!-- <p class="mb20"><a href="http://datatables.net/" target="_blank">DataTables</a> is a plug-in for the jQuery Javascript library. It is a highly flexible tool, based upon the foundations of progressive enhancement, and will add advanced interaction controls to any HTML table.</p>-->
                    
                        <div class="panel panel-primary-head">
                            <div class="panel-heading">
                                <h4 class="panel-title">Manage Cook</h4>
                                <!--<p>Searching, ordering, paging etc goodness will be immediately added to the table, as shown in this example.</p>-->
                            </div><!-- panel-heading -->
                            
                            <table id="basicTable" class="table table-striped table-bordered responsive">
                                <thead class="">
                                    <tr>
                                        <th style="text-align: center;">Name</th>
										<th style="text-align: center;">Kitchen Name</th>
                                        <th style="text-align: center;">Email</th>
                                        <th style="text-align: center;">gender</th>
                                       
                                        <th style="text-align: center;">city</th>
                                        <th style="text-align: center;" >Photo</th>
                                        <th style="text-align: center;">Action</th>
                                        <!--<th style="text-align: center;">Delete</th>-->
                                    </tr>
                                </thead>
                         
                                <tbody>

           
  <?php  while($row=$obj->fetch_array(MYSQLI_ASSOC)){
				   
    
		?>
                                   <tr >
                                        <td style="text-align: center;"><?php echo $row['name'];?></td>
                                        <td style="text-align: center; word-break: break-word;"><?php echo $row['kitchen_name'];?></td>
                                        <td style="
     text-align: center;
"><?php echo $row['email'];?></td>
<td style="text-align: center;"><?php echo $row['gender'];?></td>

<td style="text-align: center;"><?php echo $row['city'];?></td>

<td style="text-align: center;"><img class="img-circle" alt="No image" style="width:50px; height:50px; border-radius:50px;
                                             border:1px solid white;" src="<?php echo $row['photo'];?>"></td>

                                        
                                        <td class="hidden-phone" style="padding-top:8px;text-align:center;"><form method="post">
						  <input type="hidden" name="id" value="<?php echo $row['chef_id']; ?>">
						  <input type="hidden" name="name" value="<?php echo $row['name']; ?>">
                           <input type="hidden" name="email" value="<?php echo $row['email']; ?>">
						  

<?php if($row['admin_status']=='yes')
{
?>
<button  onclick="return confirm('Are you sure you want to Unapprove this Cook?')" type="submit" name="unapprove" class="btn btn-danger"><i class="fa fa-times"></i>UnApprove</button>
<?php } if($row['admin_status']=='no')

{ ?>
<button  onclick="return confirm('Are you sure you want to Approve this Cook?')" type="submit" name="approve" class="btn btn-success"><i class="fa fa-check"></i>Approve</button>
			<?php }?>
			</form></td>
                                         <!--<td style="text-align: center;">
									<form method="post">
													
													<input type="hidden" name="id" value="<//?php echo $row['chef_id'];?>">
 <input type="hidden" name="email" value="<//?php echo $row['email']; ?>">
													<input type="Submit" class="btn btn-danger" name="delete" value="Delete" onclick="return confirm('Are you sure you want to Delete !');"/>
								
													
													</form>
								</td>-->
                                    </tr>
                                            <?php }
												?>
                                    
                                    
                                </tbody>
                            </table>
                        </div><!-- panel -->
                        
                        <br />
                        
                     
                        
                        
                        
                    </div><!-- contentpanel --> <?php }
												?>
                </div><!-- mainpanel -->
            </div><!-- mainwrapper -->
        </section>

        <script src="js/jquery-1.11.1.min.js"></script>
        <script src="js/jquery-migrate-1.2.1.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/modernizr.min.js"></script>
        <script src="js/pace.min.js"></script>
        <script src="js/retina.min.js"></script>
        <script src="js/jquery.cookies.js"></script>
        
        <script src="js/jquery.dataTables.min.js"></script>
        <script src="//cdn.datatables.net/plug-ins/725b2a2115b/integration/bootstrap/3/dataTables.bootstrap.js"></script>
        <script src="//cdn.datatables.net/responsive/1.0.1/js/dataTables.responsive.js"></script>
        <script src="js/select2.min.js"></script>

        <script src="js/custom.js"></script>
        <script>
            jQuery(document).ready(function(){
                
                jQuery('#basicTable').DataTable({
                    responsive: true,
                    language: {
        
        searchPlaceholder: "Search..."
}
                });
                
                var shTable = jQuery('#shTable').DataTable({
                    "fnDrawCallback": function(oSettings) {
                        jQuery('#shTable_paginate ul').addClass('pagination-active-dark');
                    },
                    responsive: true
                });
                
                // Show/Hide Columns Dropdown
                jQuery('#shCol').click(function(event){
                    event.stopPropagation();
                });
                
                jQuery('#shCol input').on('click', function() {

                    // Get the column API object
                    var column = shTable.column($(this).val());
 
                    // Toggle the visibility
                    if ($(this).is(':checked'))
                        column.visible(true);
                    else
                        column.visible(false);
                });
                
                var exRowTable = jQuery('#exRowTable').DataTable({
                    responsive: true,
                    "fnDrawCallback": function(oSettings) {
                        jQuery('#exRowTable_paginate ul').addClass('pagination-active-success');
                    },
                    "ajax": "ajax/objects.txt",
                    "columns": [
                        {
                            "class":          'details-control',
                            "orderable":      false,
                            "data":           null,
                            "defaultContent": ''
                        },
                        { "data": "name" },
                        { "data": "position" },
                        { "data": "office" },
                        { "data": "salary" }
                    ],
                    "order": [[1, 'asc']] 
                });
                
                // Add event listener for opening and closing details
                jQuery('#exRowTable tbody').on('click', 'td.details-control', function () {
                    var tr = $(this).closest('tr');
                    var row = exRowTable.row( tr );
             
                    if ( row.child.isShown() ) {
                        // This row is already open - close it
                        row.child.hide();
                        tr.removeClass('shown');
                    }
                    else {
                        // Open this row
                        row.child( format(row.data()) ).show();
                        tr.addClass('shown');
                    }
                });
               
                
                // DataTables Length to Select2
                jQuery('div.dataTables_length select').removeClass('form-control input-sm');
                jQuery('div.dataTables_length select').css({width: '60px'});
                jQuery('div.dataTables_length select').select2({
                    minimumResultsForSearch: -1
                });
    
            });
            
            function format (d) {
                // `d` is the original data object for the row
                return '<table class="table table-bordered nomargin">'+
                    '<tr>'+
                        '<td>Full name:</td>'+
                        '<td>'+d.name+'</td>'+
                    '</tr>'+
                    '<tr>'+
                        '<td>Extension number:</td>'+
                        '<td>'+d.extn+'</td>'+
                    '</tr>'+
                    '<tr>'+
                        '<td>Extra info:</td>'+
                        '<td>And any further details here (images etc)...</td>'+
                    '</tr>'+
                '</table>';
            }
        </script>
<script type="text/javascript">
                     
				
  var y=document.getElementById('chef'); 



 y.setAttribute("class", "active");


</script>
    </body>
</html>
<?php } ?>