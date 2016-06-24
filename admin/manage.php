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

$mysqlipre->query("UPDATE `user_master` SET `status`='yes',`insertime`='$insert_time' WHERE user_id='".$_POST['id']."'");

if($mysqlipre->affected_rows>0)
				{


$to  = $_POST['user_email'];


// subject
$subject = 'Approval Mail From ShareApp admin';

// message
$message = '';
$message = '<body marginheight="0" topmargin="0" marginwidth="0" leftmargin="0" style="margin: 0px; background-color: #652416;  background-repeat: repeat;color:#fff;background:url();  padding: 4%;" bgcolor="#652416">

<h1 style="text-align: center;color:#fff;text-shadow: 6px 0px black;">ShareApp</h1>
		
						<table cellspacing="0" cellpadding="0" width="100%" border="0" align="center" bgcolor="#fff" style="padding:4%;color:#000;  border: 2px dashed;" >
							<tbody>
								<tr>
									<td >
										<h2> Dear : '.$_POST['user_name'].' </h2>
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




// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Additional headers

$headers .= 'From:ShareApp<youngdecede@youngdecadeprojects.website>' . "\r\n";


// Mail it

if(mail($to, $subject, $message, $headers))
{

echo '<center><h3><div class="alert alert-success alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong> Approve User successfully.</strong>
                </div></h3></center>'; 

}
 
else
{
echo '<center><h3><div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong> Mail not sent.please try again!</strong>
                </div></h3></center>';  


}

				     
				}
if($mysqlipre->affected_rows<=0)
				{
				 echo '<center><h3><div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong> Invalid Data. try again!</strong>
                </div></h3></center>';   
				}

}

if(isset($_POST['unapprove']))
{

$mysqlipre->query("UPDATE `user_master` SET `status`='no',`insertime`='$insert_time' WHERE user_id='".$_POST['id']."'");

if($mysqlipre->affected_rows>0)
				{

$to= $_POST['user_email'];


// subject
$subject = 'Unapproval Mail From ShareApp';

// message
$message = '';
$message = '<body marginheight="0" topmargin="0" marginwidth="0" leftmargin="0" style="margin: 0px; background-color: #652416;  background-repeat: repeat;color:#fff;background:url();  padding: 4%;" bgcolor="#652416">

<h1 style="text-align: center;color:#fff;text-shadow: 6px 0px black;">ShareApp</h1>
		
						<table cellspacing="0" cellpadding="0" width="100%" border="0" align="center" bgcolor="#fff" style="padding:4%;color:#000;  border: 2px dashed;" >
							<tbody>
								<tr>
									<td >
										<h2> Dear : '.$_POST['user_name'].' </h2>
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




// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Additional headers

$headers .= 'From:ShareApp<youngdecede@youngdecadeprojects.website>' . "\r\n";


// Mail it

if(mail($to, $subject, $message, $headers))
{



echo '<center><h3><div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong> Unapprove user successfully</strong>
                </div></h3></center>';
}
else
{
	echo '<center><h3><div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong> Mail not sent.please try again!</strong>
                </div></h3></center>'; 
}
		    
	}
if($mysqlipre->affected_rows<=0)
		{
	echo '<center><h3><div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong> Invalid Data. try again!</strong>
                </div></h3></center>';      
	}

}
if(isset($_POST['delete'])){
	
	$email=$row['user_email'];
$delete="DELETE FROM `user_master` WHERE `user_id`='".$_POST['id']."'";
$obj=$mysqlipre->query($delete);
if($mysqlipre->affected_rows>0){
	$mesage='<center><h3><div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong>  User  Deleted Successfully!</strong>
                </div></h3></center>';
	
	
	$subject = "This is subject";
 $message = "<b>This is  message.</b>";
 $message .="Hello ! ; Admin Remove You successfully.Kindly Contact Us...."; 
 
 $headers .= 'From:ShareApp<youngdecede@youngdecadeprojects.website>' . "\r\n";
 $header = "Cc:afgh@somedomain.com \r\n";
 $header .= "MIME-Version: 1.0\r\n";
 $header .= "Content-type: text/html\r\n";
 


$mailsent= mail ($email,$subject,$message,$header);

      if( $mailsent == true ){
                 $mesage= '<center><h3><div class="alert alert-success alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong> Mail send successfully.</strong>
                </div></h3></center>';
				 
				 
				 
				 
				 echo '<script>window.onload=manageuser.php; </script>';
				
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
}else{
	
	$msg='<center><h3><div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong> Error in Deletion of user, please try again!</strong>
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

        <title>ShareApp Admin</title>

        <link href="css/style.default.css" rel="stylesheet">
        <link href="css/select2.css" rel="stylesheet" />
        <link href="css/style.datatables.css" rel="stylesheet">
        <link href="//cdn.datatables.net/responsive/1.0.1/css/dataTables.responsive.css" rel="stylesheet">

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
                                <i class="fa fa-th-list"></i>
                            </div>
                            <div class="media-body">
                                <ul class="breadcrumb">
                                    <li><a href=""><i class="glyphicon glyphicon-home"></i></a></li>
                                    <li><a href="">Tables</a></li>
                                    <li>Manage User</li>
                                </ul>
                                <h4>Manage User</h4>
                            </div>
                        </div><!-- media -->
                    </div><!-- pageheader -->



                         <?php echo $msg ?>
                          <?php echo $mesage ?>

                               		<?php  
$select="select * FROM `user_master`";
	$obj=$mysqlipre->query($select);
	if($obj->num_rows<=0){

echo "<script>
    window.alert('Incorrect user master Data')
    window.location.href='dashboard.php';
    </script>"; 	
	return;
exit;		
	     
                 }
                 else
                 {	

			
?>




                    
                    <div class="contentpanel">
                        <p class="mb20"><a href="http://datatables.net/" target="_blank">DataTables</a> is a plug-in for the jQuery Javascript library. It is a highly flexible tool, based upon the foundations of progressive enhancement, and will add advanced interaction controls to any HTML table.</p>
                    
                        <div class="panel panel-primary-head">
                            <div class="panel-heading">
                                <h4 class="panel-title">Basic Configuration</h4>
                                <p>Searching, ordering, paging etc goodness will be immediately added to the table, as shown in this example.</p>
                            </div><!-- panel-heading -->
                            
                            <table id="basicTable" class="table table-striped table-bordered responsive">
                                <thead class="">
                                    <tr style="text-align: center;">
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Profile</th>
                                        <th>Action</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                         
                                <tbody>
<?php   while($row=$obj->fetch_array(MYSQLI_ASSOC)){
				   
    
		?>
                                   <tr style="text-align:center;">
                                        <td><?php echo $row['user_name'];?></td>
                                        <td><?php echo $row['user_email'];?></td>
<td><?php echo $row['user_phone'];?></td>

<td><img class="img-circle" alt="No image" style="width:50px; height:50px; border-radius:50px;
                                             border:1px solid white;" src="<?php echo $row['user_profile_pic'];?>"></td>

                                        
                                        <td class="hidden-phone" style="padding-top:8px;text-align:center;"><form method="post">
						  <input type="hidden" name="id" value="<?php echo $row['user_id']; ?>">
						  <input type="hidden" name="user_name" value="<?php echo $row['user_name']; ?>">
                           <input type="hidden" name="user_email" value="<?php echo $row['user_email']; ?>">
						  

<?php if($row['status']=='yes')
{
?>
<button  onclick="return confirm('Are you sure to Unapprove this User?')" type="submit" name="unapprove" class="btn btn-danger"><i class="fa fa-times"></i>UnApprove</button>
<?php } if($row['status']=='no')

{ ?>
<button  onclick="return confirm('Are you sure to Approve this User?')" type="submit" name="approve" class="btn btn-success"><i class="fa fa-check"></i>Approve</button>
			<?php }?>
			</form></td>
                                         <td>
									<form method="post">
													<table align="center">
													<tr><td ><input type="hidden" name="id" value="<?php echo $row['user_id'];?>"></td></tr>
													<tr><td><input type="Submit" class="btn btn-danger" name="delete" value="Delete" onclick="return confirm('Are you sure you want to Delete !');"/></td></tr>
								
													
													</table></form>
								</td>
                                    </tr>
                                            <?php }}
												?>
                                    
                                    
                                </tbody>
                            </table>
                        </div><!-- panel -->
                        
                        <br />
                        
                     
                        
                        
                        
                    </div><!-- contentpanel -->
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
                    responsive: true
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
                     
				
  var y=document.getElementById('user'); 



 y.setAttribute("class", "active");


</script>
    </body>
</html>
<?php } ?>