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

$result33=$mysqlipre->query("SELECT * FROM `user_master` where `active_flag`='yes' and `status`='yes'");
$result33->num_rows;

$result34=$mysqlipre->query("SELECT * FROM `user_master` where `active_flag`='no' and `status`='no'");
$result34->num_rows;
?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Select Cook | Manage Report</title>

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
                            <div class="pageicon pull-left" style="padding-top:10px;">
                                <i class="fa fa-list"></i>
                            </div>
                            <div class="media-body">
                                <ul class="breadcrumb">
                                    <li><a href=""><i class="glyphicon glyphicon-home"></i></a></li>
                                    <li><a href="">Home</a></li>
                                    <li>Manage Report</li>
                                </ul>
                                <h4>Manage Report</h4>
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
                                          
										   <div class="col-md-6">
                                <form id="basicForm" action="" method="post" enctype="multipart/form-data" autocomplete="off">
                                <div class="panel panel-default">
                                    
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="form-group">
                                                <script type="text/javascript" src="https://www.google.com/jsapi"></script>
										<script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Users', 'Detail'],
          [' Active Users',     <?php echo $result33->num_rows ?>],
         
          ['Deactive Users',     <?php echo $result34->num_rows ?>]
          
        ]);

        var options = {
          title: 'Users',
colors:['green','red','#FF9900','blue']
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart1'));

        chart.draw(data, options);
      }
    </script>
									
					<div id="piechart1" style="width: 100%; height: 50%;"></div>
                                            </div><!-- form-group -->
                                            
                                           
                                        </div><!-- row -->
                                    </div><!-- panel-body -->
                                   
                                </div><!-- panel -->
                                </form>
                                
                            </div><!-- col-md-12 -->









 <div class="col-md-6">
                                <form id="basicForm" action="" method="post" enctype="multipart/form-data" autocomplete="off">
                                <div class="panel panel-default">
                                    
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="form-group">


        <script type="text/javascript" src="https://www.google.com/jsapi"></script>

    <script type="text/javascript">
      google.load("visualization", "1.1", {packages:["bar"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([

       ['Year', 'Cook'],
         
		 
	
<?php  


$sql22=$mysqlipre->query("SELECT distinct(YEAR(`inserttime`)) as date FROM chef_master");

while($rww1=$sql22->fetch_array(MYSQLI_ASSOC)){ 


$ree=$rww1['date'];


$sql23=$mysqlipre->query("select * FROM `chef_master` where YEAR(`inserttime`)='$ree'");

$roo=$sql23->num_rows;




?>



        ['<?php echo $ree;?>',  <?php echo $roo;?>],
         

<? } ?>	 
		 
		 
		 
        ]);

        var options = {
          chart: {
            title: 'Cook',
           
               }
        };

        var chart = new google.charts.Bar(document.getElementById('columnchart_material'));

        chart.draw(data, options);
      }
    </script>							
		

		    <div id="columnchart_material"    style="width:100%; height: 320px;"></div>
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








  <div class="col-sm-12 col-md-12">
                              
                                <div class="tab-content nopadding noborder">
                                    <div class="tab-pane active" id="activities">
                                        <div class="activity-list">  
                                          
										   <div class="col-md-12">
                                <form id="basicForm" action="" method="post" enctype="multipart/form-data" autocomplete="off">
                                <div class="panel panel-default">
                                    
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="form-group">
                                                <script type="text/javascript" src="https://www.google.com/jsapi"></script>

 <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Year', 'User'],
         

        

      


<?php  


$sql22=$mysqlipre->query("SELECT distinct(YEAR(`inserttime`)) as date FROM user_master ");

while($rww1=$sql22->fetch_array(MYSQLI_ASSOC)){ 




 $ree=$rww1['date'];



$sql23=$mysqlipre->query("select * FROM user_master where YEAR(`inserttime`)='$ree' ");

$roo=$sql23->num_rows;


 $roo;
?>



        ['<?php echo $ree; ?>',  <?php echo $roo ?>],
         

<? } ?>

]);  var options = {
          title: 'User',
          hAxis: {title: 'Year',  titleTextStyle: {color: '#333'}},
          vAxis: {minValue: 0}
        };

        var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>

									
					<div id="chart_div" style="width:100%; height: 500px;"></div>
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
                     
				
  var y=document.getElementById('ruser'); 



 y.setAttribute("class", "active");


</script>

    </body>
</html>
<?php } ?>