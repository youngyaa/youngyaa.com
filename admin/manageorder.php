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
?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Select Cook | Manage Order</title>

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
                                <i class="fa fa-database"></i>
                            </div>
                            <div class="media-body">
                                <ul class="breadcrumb">
                                    <li><a href=""><i class="glyphicon glyphicon-home"></i></a></li>
                                    <li><a href="">Home</a></li>
                                    <li>Manage Order</li>
                                </ul>
                                <h4>Manage Order</h4>
                            </div>
                        </div><!-- media -->
                    </div><!-- pageheader -->



                         <?php echo $msg ?>
                          <?php echo $mesage ?>

                    		<?php  
$select="select * FROM `order_chef_master` ORDER BY `id` DESC ";
	$obj=$mysqlipre->query($select);
	if($obj->num_rows<=0){

echo "<center><b>No order Available</b></center>";	
	     
                 }
                 else
                 {	
?>



                    
                    <div class="contentpanel">
                       <!-- <p class="mb20"><a href="http://datatables.net/" target="_blank">DataTables</a> is a plug-in for the jQuery Javascript library. It is a highly flexible tool, based upon the foundations of progressive enhancement, and will add advanced interaction controls to any HTML table.</p>-->
                    
                        <div class="panel panel-primary-head">
                            <div class="panel-heading">
                                <h4 class="panel-title">Manage Order</h4>
                                <!--<p>Searching, ordering, paging etc goodness will be immediately added to the table, as shown in this example.</p>-->
                            </div><!-- panel-heading -->
                            
                            <table id="basicTable" class="table table-striped table-bordered responsive">
                                <thead class="">
                                    <tr>
                                        <th style="text-align: center; display
:none; ">id</th>
                                        <th style="text-align: center;">Order Unique Number</th>
										<th style="text-align: center;">User Name</th>
                                        <th style="text-align: center;">Cook Name</th>
  <th style="text-align: center;">Date</th>
                                        <th style="text-align: center;">Detail</th>
                                        
                                    </tr>
                                </thead>
                         
                                <tbody>

           
  <?php  while($row=$obj->fetch_array(MYSQLI_ASSOC)){
$date13=$row['inserttime'];
										$datetime=explode(' ',$date13);
										$date=$datetime[0];
										$time=$datetime[1];



		 $select16="select * FROM `chef_master` where `chef_id`='".$row['chef_id']."'";
	$obj16=$mysqlipre->query($select16);
	if($obj16->num_rows<=0){

$msg="<center><b>No Cook Available</b></center>";	
	     
                 }
				 $row16=$obj16->fetch_array(MYSQLI_ASSOC);
				 $select19="select * FROM `user_master` where `user_id`='".$row['user_id']."'";
	$obj19=$mysqlipre->query($select19);
	if($obj19->num_rows<=0){

$msg="<center><b>No User Available</b></center>";	
	     
                 }
				 $row19=$obj19->fetch_array(MYSQLI_ASSOC);		   
    
		?>
                                   <tr >
                                         <td style="text-align: center; display:none;"><?php echo $row['id'];?></td>
                                        <td style="text-align: center;"><?php echo $row['order_unique_number'];?></td>
                                        <td style="text-align: center; word-break: break-word;"><?php echo $row19['first_name'];?></td>
                                        <td style="
     text-align: center;
"><?php echo $row16['name'];?></td>
<td style="
     text-align: center;
"><?php echo $date ;?></td>

<td style="
     text-align: center;
"><a href="order_detail.php?id=<?php echo $row['id']; ?>" class="btn btn-round btn-info">View</a></td>
                                
                              
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
                   "order": [[0, 'desc']] ,
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
                    "order": [[1, 'desc']] 
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
                     
				
  var y=document.getElementById('order'); 



 y.setAttribute("class", "active");


</script>
    </body>
</html>
<?php } ?>