<?php include("session.php");
include("con1.php");

?>
<?php
if(empty($_SESSION['adminid'])){


echo "<script>
    window.alert('Please Login First')
    window.location.href='index.php';
    </script>"; 	
	return;
exit;
}
else  
{ 
?>
<?php
$msgg="";
$msgg1="";
$msgg2="";
$msgg23="";
$select1=$mysqlipre->query("select * FROM `a7rtg_users`");

if($select1->num_rows<=0)
{
$msgg2="No  user ";
}
else
{	
  $count=$select1->num_rows;
}
?>
<?php
$select2=$mysqlipre->query("select * FROM `a7rtg_mynews`");

if($select2->num_rows<=0)
{
$msgg="No news ";
}
else
{	
  $count1=$select2->num_rows;
}
?>
<?php
$select3=$mysqlipre->query("select * FROM `a7rtg_secklioneevent`");

if($select3->num_rows<=0)
{
$msgg1="No seckilling event";
}
else
{	
  $count2=$select3->num_rows;
}
?>
<?php
$select_book=$mysqlipre->query("select distinct(event_id) as event_id FROM `a7rtg_book_userevent`");

if($select_book->num_rows<=0)
{
$msgg23="No Booked event";
}
else
{	
  $count3=$select_book->num_rows;
}
?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Young Yaa | Dashboard</title>

        <link href="css/style.default.css" rel="stylesheet">
        <link href="css/morris.css" rel="stylesheet">
        <link href="css/select2.css" rel="stylesheet" />
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="js/html5shiv.js"></script>
        <script src="js/respond.min.js"></script>
        <![endif]-->
		<style>
		.panel-icon .fa {
    
    padding: 12px 0 0 12px;
}
.panel-info>.panel-heading {
    color: #fff;
    background-color: #31708f;
    border-color: #bce8f1;
}
.panel-warning>.panel-heading {
    color: #fff;
    background-color: orange;
    border-color: #faebcc;
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
                                <i class="fa fa-home"></i>
                            </div>
                            <div class="media-body">
                                <ul class="breadcrumb">
                                    <li><a href=""><i class="glyphicon glyphicon-home"></i></a></li>
                                    <li>Home</li>
                                </ul>
                                <h4>Dashboard</h4>
                            </div>
                        </div><!-- media -->
                    </div><!-- pageheader -->
                    
                    <div class="contentpanel">
                        
                        <div class="row row-stat">
                            <div class="col-md-4">
                                <div class="panel panel-success-alt noborder">
                                    <div class="panel-heading noborder">
                                       <!-- <div class="panel-btns">
                                            <a href="" class="panel-close tooltips" data-toggle="tooltip" title="Close Panel"><i class="fa fa-times"></i></a>
                                        </div><!-- panel-btns -->
                                        <div class="panel-icon"><i class="fa fa-users"></i></div>
                                        <div class="media-body">
                                            <h5 class="md-title nomargin" style="
    text-align: center;
">Total Users</h5>
                                            <h1 class="mt5" style="
    text-align:center;
"><center><h3><?php echo $msgg2 ?></h3></center><?php echo $count ?></h1>
                                        </div><!-- media-body -->
                                        <hr>
                                        <div class="clearfix mt20">
                                           
                                        </div>
                                        
                                    </div><!-- panel-body -->
                                </div><!-- panel -->
                            </div><!-- col-md-4 -->
                            
                            <div class="col-md-4">
                                <div class="panel panel-primary noborder">
                                    <div class="panel-heading noborder">
                                        <!--<div class="panel-btns">
                                            <a href="" class="panel-close tooltips" data-toggle="tooltip" title="Close Panel"><i class="fa fa-times"></i></a>
                                        </div><!-- panel-btns -->
                                        <div class="panel-icon"><i class="fa fa-ge"></i></div>
                                        <div class="media-body">
                                            <h5 class="md-title nomargin"style="
    text-align: center;
">Total News</h5>
                                           <h1 class="mt5" style="
    text-align: center;
"><center><h3><?php echo $msgg ?></h3></center><?php echo $count1 ?></h1>
                                        </div><!-- media-body -->
                                        <hr>
                                        <div class="clearfix mt20">
                                            
                                        </div>
                                        
                                    </div><!-- panel-body -->
                                </div><!-- panel -->
                            </div><!-- col-md-4 -->
                            
            <div class="col-md-4">
                                <div class="panel panel-info noborder">
                                    <div class="panel-heading noborder">
                                        <!--<div class="panel-btns">
                                            <a href="" class="panel-close tooltips" data-toggle="tooltip" title="Close Panel"><i class="fa fa-times"></i></a>
                                        </div><!-- panel-btns -->
                                        <div class="panel-icon"><i class="fa fa-leaf"></i></div>
                                        <div class="media-body">
                                            <h5 class="md-title nomargin"style="
    text-align: center;
">Total Seckilling Event</h5>
                                           <h1 class="mt5" style="
    text-align: center;
"><center><h3><?php echo $msgg1 ?></h3></center><?php echo $count2 ?></h1>
                                        </div><!-- media-body -->
                                        <hr>
                                        <div class="clearfix mt20">
                                            
                                        </div>
                                        
                                    </div><!-- panel-body -->
                                </div><!-- panel -->
                            </div><!-- col-md-4 -->    

							
							<div class="col-md-4">
                                <div class="panel panel-warning noborder">
                                    <div class="panel-heading noborder">
                                        <!--<div class="panel-btns">
                                            <a href="" class="panel-close tooltips" data-toggle="tooltip" title="Close Panel"><i class="fa fa-times"></i></a>
                                        </div><!-- panel-btns -->
                                        <div class="panel-icon"><i class="fa fa-suitcase"></i></div>
                                        <div class="media-body">
                                            <h5 class="md-title nomargin"style="
    text-align: center;
">Total Event Booked</h5>
                                           <h1 class="mt5" style="text-align: center;"><center><h3><?php echo $msgg23 ?></h3></center><?php echo $count3 ?></h1>
                                        </div><!-- media-body -->
                                        <hr>
                                        <div class="clearfix mt20">
                                            
                                        </div>
                                        
                                    </div><!-- panel-body -->
                                </div><!-- panel -->
                            </div><!-- col-md-4 -->  							
                        </div><!-- row -->
                        
                        

                        
                        
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
        
        <script src="js/flot/jquery.flot.min.js"></script>
        <script src="js/flot/jquery.flot.resize.min.js"></script>
        <script src="js/flot/jquery.flot.spline.min.js"></script>
        <script src="js/jquery.sparkline.min.js"></script>
        <script src="js/morris.min.js"></script>
        <script src="js/raphael-2.1.0.min.js"></script>
        <script src="js/bootstrap-wizard.min.js"></script>
        <script src="js/select2.min.js"></script>

        <script src="js/custom.js"></script>
        <script src="js/dashboard.js"></script>
		<script type="text/javascript">
                     
				
  var y=document.getElementById('dashboard'); 



 y.setAttribute("class", "active");


</script>


    </body>

</html>

<?php } ?>