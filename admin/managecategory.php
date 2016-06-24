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

$mysqlipre->query("UPDATE `chef_category_master` SET `active_flag`='activate',`inserttime`='$insert_time' WHERE cat_id='".$_POST['id']."'");

if($mysqlipre->affected_rows>0)
				{


$to  = $_POST['email'];


// subject
$subject = 'Approval Mail From Select Cook App admin';

// message
$message = '';
$message = '<body marginheight="0" topmargin="0" marginwidth="0" leftmargin="0" style="margin: 0px; background-color: #FF7401;  background-repeat: repeat;color:#fff;background:url();  padding: 4%;" bgcolor="#652416">

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
										Your Category Approved  By Admin 
									</td>
								</tr>


							</tbody>
						</table>
					
	</body>';




// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Additional headers

$headers .= 'From:Select Cook App<youngdecade@youngdecadeprojects.biz>' . "\r\n";


// Mail it

if(mail($to, $subject, $message, $headers))
{

$mesage= '<center><h3><div class="alert alert-success alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong> Approve Category successfully.</strong>
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

$mysqlipre->query("UPDATE `chef_category_master` SET `active_flag`='deactivate',`inserttime`='$insert_time' WHERE cat_id='".$_POST['id']."'");

if($mysqlipre->affected_rows>0)
				{

$to= $_POST['email'];


// subject
$subject = 'Unapproval Mail From Select Cook App';

// message
$message = '';
$message = '<body marginheight="0" topmargin="0" marginwidth="0" leftmargin="0" style="margin: 0px; background-color: #FF7401;  background-repeat: repeat;color:#fff;background:url();  padding: 4%;" bgcolor="#652416">

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
										Your Category Unapproved  By Admin 
									</td>
								</tr>


							</tbody>
						</table>
					
	</body>';




// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Additional headers

$headers .= 'From:Select Cook App<youngdecade@youngdecadeprojects.biz>' . "\r\n";


// Mail it

if(mail($to, $subject, $message, $headers))
{



$mesage='<center><h3><div class="alert alert-success alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong> Unapprove Category successfully.</strong>
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
	
	$to= $_POST['email'];
$delete="DELETE FROM `chef_category_master` WHERE `cat_id`='".$_POST['id']."'";
$obj=$mysqlipre->query($delete);
if($mysqlipre->affected_rows>0){
	
	
	
	$subject = "This is subject";
 $message = "";
 $message .="Hello ! Admin Remove You successfully.Kindly Contact Us...."; 
 
 
// To send HTML mail, the Content-type header must be set
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

// Additional headers

$headers .= 'From:Select Cook App<youngdecade@youngdecadeprojects.biz>' . "\r\n";


if(mail($to, $subject, $message, $headers))
{
                $mesage='<center><h3><div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong>  Category  Deleted Successfully!</strong>
                </div></h3></center>';
				 $delete14="DELETE FROM `chef_dish_master` WHERE `cat_id`='".$_POST['id']."'";
$obj=$mysqlipre->query($delete14);
			
				
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
                  <strong> Error in Deletion of Category, please try again!</strong>
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

        <title>Select Cook | Manage Category</title>

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
                                <i class="fa fa-empire"></i>
                            </div>
                            <div class="media-body">
                                <ul class="breadcrumb">
                                    <li><a href=""><i class="glyphicon glyphicon-home"></i></a></li>
                                    <li><a href="">Home</a></li>
                                    <li>Manage Category</li>
                                </ul>
                                <h4>Manage Category</h4>
                            </div>
                        </div><!-- media -->
                    </div><!-- pageheader -->



                         
<div class="panel-body">
                                        <div class="row">
                                                                                  
                                            <form id="basicForm" action="" method="post" enctype="multipart/form-data" autocomplete="off">   
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Cook Name<span class="asterisk">*</span></label>
                                                <div class="col-sm-9">
                                                 <select class="form-control" name="chef_id"  id="chef_id">
												<option value="" >---Select Cook---</option>
 <?php 
						   $select="select * FROM `chef_master`";
	                          $obj=$mysqlipre->query($select);
                                  
										  			  ?>
												<?php while($row=$obj->fetch_array(MYSQLI_ASSOC)){
				                                     	 ?>
												<option value="<?php echo $row['chef_id'];?>"><?php echo $row['name'];?></option>
												<?php }?>
																</select>                                                   
                                                   
                                                </div>
                                            </div><!-- form-group -->

                                      <div class="row">
                                        <div class="col-sm-9 col-sm-offset-3" style="
    text-align: center;
">
                                            <button class="btn btn-primary mr5" name="create" >Submit</button>
                                            <!--<button type="reset" class="btn btn-dark">Reset</button>-->
                                        </div>
                                      </div>
                                   
</form></div></div>
<hr>
                    	                		
<?php echo $msg ?>
                          <?php echo $mesage ?>




<?php  
if(isset($_POST['create']))
{
$select="select * FROM `chef_category_master` where chef_id='".$_POST['chef_id']."'";
	$obj=$mysqlipre->query($select);
	if($obj->num_rows<=0){

echo "<center><b>No Category Available</b></center>";	
	     
                 }
                 else
                 {	
?>



                    
                    <div class="contentpanel">
                       <!-- <p class="mb20"><a href="http://datatables.net/" target="_blank">DataTables</a> is a plug-in for the jQuery Javascript library. It is a highly flexible tool, based upon the foundations of progressive enhancement, and will add advanced interaction controls to any HTML table.</p>-->
                    
                        <div class="panel panel-primary-head">
                            <div class="panel-heading">
                                <h4 class="panel-title">Manage Category</h4>
                                <!--<p>Searching, ordering, paging etc goodness will be immediately added to the table, as shown in this example.</p>-->
                            </div><!-- panel-heading -->
                            
                            <table id="basicTable" class="table table-striped table-bordered responsive">
                                <thead class="">
                                    <tr>
					<th style="text-align: center;">Cook Name</th>
                                        <th style="text-align: center;">Category Name</th>
                                        <th style="text-align: center;">Image</th>
                                       
                                        <th style="text-align: center;">Action</th>
                                        <!--<th style="text-align: center;">Delete</th>-->
                                    </tr>
                                </thead>
                         
                                <tbody>

           
  <?php  while($row=$obj->fetch_array(MYSQLI_ASSOC)){
				   
    $select16="select * FROM `chef_master` where `chef_id`='".$row['chef_id']."'";
	$obj16=$mysqlipre->query($select16);
	if($obj16->num_rows<=0){

echo "<center><b>No Cook Available</b></center>";	
	     
                 }
				 $row16=$obj16->fetch_array(MYSQLI_ASSOC);
		?>
                                   <tr >
								        <td style="text-align: center;"><?php echo $row16['name'];?></td>
                                        <td style="text-align: center;"><?php echo $row['cat_name'];?></td>
                                       

<td style="text-align: center;"><img class="img-circle" alt="deactivate image" style="width:70px; height:70px; border-radius:50px;
                                             border:1px solid white;" src="<?php echo $row['cat_image'];?>"></td>

                                        
                                        <td class="hidden-phone" style="padding-top:8px;text-align:center;"><form method="post">
						  <input type="hidden" name="id" value="<?php echo $row['cat_id']; ?>">
						  
						  <?php $select15="select * FROM `chef_master` where `chef_id`='".$row['chef_id']."'";
	$obj15=$mysqlipre->query($select15);
	if($obj15->num_rows<=0){

echo "<center><b>No Cook Available</b></center>";	
	     
                 }
				 $row15=$obj15->fetch_array(MYSQLI_ASSOC);
                 ?>
						  <input type="hidden" name="name" value="<?php echo $row15['name']; ?>">
                           <input type="hidden" name="email" value="<?php echo $row15['email']; ?>">
						  

<?php if($row['active_flag']=='activate')
{
?>
<button  onclick="return confirm('Are you sure you want to Unapprove this Category?')" type="submit" name="unapprove" class="btn btn-danger"><i class="fa fa-times"></i>UnApprove</button>
<?php } if($row['active_flag']=='deactivate')

{ ?>
<button  onclick="return confirm('Are you sure you want to Approve this Category ?')" type="submit" name="approve" class="btn btn-success"><i class="fa fa-check"></i>Approve</button>
			<?php }?>
			</form></td>
                                         <!--<td style="text-align: center;">
									<form method="post">
													
													<input type="hidden" name="id" value="<//?php echo $row['cat_id'];?>">
													  <//?php $select15="select * FROM `chef_master` where `chef_id`='".$row['chef_id']."'";
	$obj15=$mysqlipre->query($select15);
	if($obj15->num_rows<=0){

echo "<center><b>No Cook Available</b></center>";	
	     
                 }
				 $row15=$obj15->fetch_array(MYSQLI_ASSOC);
                 ?>
 <input type="hidden" name="email" value="<//?php echo $row15['email']; ?>">
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
                        
                     
                        
                      
                        
                    </div><!-- contentpanel --> <?php }}
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
                return '<table class="table table-bordered deactivatemargin">'+
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
                     
				
  var y=document.getElementById('post'); 



 y.setAttribute("class", "active");


</script>
<script src="js/jquery.validate.min.js"></script>
<script src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/additional-methods.js"></script>
<script type="text/javascript">
$(function() {

    $.validator.addMethod("loginRegex", function(value, element) {
        return this.optional(element) || /^[a-z0-9\-\s]+$/i.test(value);
    }, "name must contain only letters, numbers, or space.");

            
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
				 loginRegex: true,
                           minlength: 3,
			   maxlength: 40
            },
			
            chef_id:{
				 required: true,
				
            },
                          
              
        
                  phone:{
				 required: true,
				 number:true,
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
            chef_id: {
		    required:"Please Select Cook"
		  	
			},

                  phone: {
		    required:"Please enter phone"
		  	
			}
				 
				 
			
        },
        
        
                });
            });
        </script>
    </body>
</html>
<?php } ?>