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

extract($_POST);
if(isset($_POST['delete'])){
$delete="DELETE FROM `experience_master` WHERE `experience_id`='".$_POST['id']."'";
$obj=$mysqlipre->query($delete);
if($mysqlipre->affected_rows>0){
	
	$msg ='<center><h3><div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong>  Experience Deleted Successfully!</strong>
                </div></h3></center>';
	
				
}else{
	
	
	$msg ='<center><h3><div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong> Error in Deletion of Experience.please try again!</strong>
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

        <title>LifeShare Admin</title>

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
                            <div class="pageicon pull-left">
                                <i class="fa fa-th-list"></i>
                            </div>
                            <div class="media-body">
                                <ul class="breadcrumb">
                                    <li><a href=""><i class="glyphicon glyphicon-home"></i></a></li>
                                    <li><a href="">Home</a></li>
                                    <li>Manage Experience</li>
                                </ul>
                                <h4>Manage Experience</h4>
                            </div>
                        </div><!-- media -->
                    </div><!-- pageheader -->



       <?php echo $msg;?>


                           <?php  
$select="select * FROM `experience_master`";
	$obj=$mysqlipre->query($select);
	if($obj->num_rows<=0){

echo "<script>
    window.alert('Incorrect experience master Data')
    window.location.href='dashboard.php';
    </script>"; 	
	return;
exit;		
	     
                 }
                 else
                 {	

			
?>




                    
                    <div class="contentpanel">
                       <!-- <p class="mb20"><a href="http://datatables.net/" target="_blank">DataTables</a> is a plug-in for the jQuery Javascript library. It is a highly flexible tool, based upon the foundations of progressive enhancement, and will add advanced interaction controls to any HTML table.</p>-->
                    
                        <div class="panel panel-primary-head">
                            <div class="panel-heading">
                                <h4 class="panel-title">Manage Experience</h4>
                                <!--<p>Searching, ordering, paging etc goodness will be immediately added to the table, as shown in this example.</p>-->
                            </div><!-- panel-heading -->
                            
                            <table id="basicTable" class="table table-striped table-bordered responsive">
                                <thead class="">
                                    <tr>
                                        <th style="text-align: center;">Title</th>
                                        <th style="text-align: center;">location</th>
                                       
                                       <th style="text-align: center;">Description</th>
 
                                        <th style="text-align: center;">Action</th>
                                        <th style="text-align: center;">Delete</th>
                                    </tr>
                                </thead>
                         
                                <tbody>
<?php   while($row=$obj->fetch_array(MYSQLI_ASSOC)){
				   
    
		?>
                                  <tr style="text-align:center;">
                                        <td><?php echo $row['title'];?></td>
                                        <td><?php echo $row['location'];?></td>
                                        <td style="
    word-break: break-word;
"><?php echo $row['description'];?></td>
                                         
                                        <td><a href="detail.php?id=<?php echo $row['experience_id']; ?>" class="btn btn-round btn-info">Detail</a></td>
                <td>
									<form method="post">
													
													<input type="hidden" name="id" value="<?php echo $row['experience_id'];?>">
													<input type="Submit" class="btn btn-danger" name="delete" value="Delete" onclick="return confirm('Are you sure you want to Delete !');"/>
								
													
													</form>
								</td>

                                    </tr>
                                            <?php }
												?>
                                    
                                    
                                </tbody>
                            </table>
                        </div><!-- panel -->
                        
                        <br />
                        
                     
                        
                        
                        
                    </div><!-- contentpanel --><?php }?>
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
                     
				
  var y=document.getElementById('post'); 



 y.setAttribute("class", "active");


</script>
    </body>
</html>
<?php } ?>