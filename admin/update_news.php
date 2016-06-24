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
 if($_GET['id']==""){
echo"<script>
    window.alert('Cant Proceed Need of Id')
    window.location.href='mange_news.php';
    </script>"; 	
	return;
exit;
}
else  
{  
?>
<?php


$mesage="";


$sel="SELECT * FROM `a7rtg_mynews` WHERE `news_id`='".$_GET['id']."'";
$obj=$mysqlipre->query($sel);

if($obj->num_rows<=0){
	
	
	echo "<center>No news Available</center>";
}
else{
	$row=$obj->fetch_array(MYSQLI_ASSOC);

          $image=$row['image'];
?>
<?php

extract($_POST);
if(isset($_POST['update']))
{

	$insert_time=date("Y-m-d H:i:s");

$url= isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https:' : 'http:'.'//'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);

if(!empty($_FILES['file']['name']))
{
function generateRandomString($length =90) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

$str= generateRandomString();
$str2= rand();
$str3= $str.$str2;
	$target = "images/"; 
 $filename= $str3.basename( $_FILES['file']['name']);

$filename =trim($_FILES['file']['name']); //rename file
$filename=str_replace(' ','_',$filename);
$filename=$str3.$filename;


$data="https://www.youngyaa.com.au/admin/images/".$filename;

//$data=$url.'/images/'.$filename;

if(move_uploaded_file($_FILES['file']['tmp_name'],$target.$filename))
{


}
else
{

echo "<script>alert('Image Not Uploaded');window.location.href='manage_events.php';</script>";
									exit;
									return;

}

}
else
{
	
$data=$image;


}
$mysqlipre->query("UPDATE `a7rtg_mynews` SET title='".$_POST['title']."',description='".$_POST['description']."',created_date='".$_POST['created_date']."', image='$data', insertime='$insert_time' WHERE news_id='".$_GET['id']."'");

if($mysqlipre->affected_rows>0)
{



									$mesage= '<center><h3><div class="alert alert-success alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong>News Updated Successfully.</strong>
                </div></h3></center>';
}

if($mysqlipre->affected_rows<=0)
{


$mesage= '<center><h3><div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong>News Not Updated.please try again!</strong>
                </div></h3></center>';
}



}
?>
<?php
$sel="SELECT * FROM `a7rtg_mynews` WHERE `news_id`='".$_GET['id']."'";
$obj=$mysqlipre->query($sel);

if($obj->num_rows<=0){
	
	
	echo"<center>No Events Available</center>";
}
else{
	$row=$obj->fetch_array(MYSQLI_ASSOC);
?>


<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Young Yaa | Update News</title>

        <link href="css/style.default.css" rel="stylesheet">
        <link href="css/prettyPhoto.css" rel="stylesheet">
          <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="js/html5shiv.js"></script>
        <script src="js/respond.min.js"></script>
        <![endif]-->
		<style>
#basicForm{

    word-break: break-word;



}
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
                                <i class="fa fa-tag"></i>
                            </div>
                            <div class="media-body">
                                <ul class="breadcrumb">
                                    <li><a href=""><i class="glyphicon glyphicon-home"></i></a></li>
                                    <li><a href="">Home</a></li>
                                    <li><a href="manage_news.php">Manage News</a></li>
                                </ul>
                                <h4>Update News </h4>
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
                                          
										   <div class="col-md-12">
                                <form id="basicForm" action="" method="post" enctype="multipart/form-data" autocomplete="off">
                                <div class="panel panel-default">

                                   <div class="panel-heading" style="background-color:#428BCA;color:white;">
                                <h4 class="panel-title">Update News</h4>
                               <!--<p>Searching, ordering, paging etc goodness will be immediately added to the table, as shown in this example.</p>-->
                            </div><!-- panel-heading -->

                                    <div class="panel-body">
                                        <div class="row">
                                                                                  
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Title<span class="asterisk">*</span></label>
                                                <div class="col-sm-9">
                                                                                                    
                                                    <input type="text" name="title" value="<?php echo $row['title']?>" class="form-control" placeholder="Please Enter Title" />
                                                </div>
                                            </div><!-- form-group -->
                                                                      
<div class="form-group">
                                                <label class="col-sm-3 control-label">Description<span class="asterisk">*</span></label>
                                                <div class="col-sm-9">
                                                                                                    
                                                    <input type="text" name="description" value="<?php echo $row['description']?>" class="form-control" placeholder="Please Enter Description" />
                                                </div>
                                            </div><!-- form-group -->
											<div class="form-group">
                                                <label class="col-sm-3 control-label">Created Date<span class="asterisk">*</span></label>
                                                <div class="col-sm-9">
                                                     <div class="input-group">
                                            <input type="text" class="form-control" name="created_date"  value="<?php echo $row['created_date'] ?>" placeholder="dd MM yy" id="datepicker" readonly>
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                        </div>                                               
                                                    <!--<input type="text" name="created_date" class="form-control" placeholder="Please Enter Title" />-->
                                               <label for="datepicker" class="error"></label> </div>
                                            </div><!-- form-group -->

                                              <div class="form-group">
                                                <label class="col-sm-3 control-label"><span class="asterisk"></span></label>
                                                <div class="col-sm-9">
                                           <img class="img-circle" style="width:70px; height:70px; border-radius:70px;
                                             " src="<?php echo $row['image'];?>">  
                                                </div>
                                            </div><!-- form-group -->

                                                       <div class="form-group">
                                                <label class="col-sm-3 control-label">Image<span class="asterisk"></span></label>
                                                <div class="col-sm-9">
                                                                                                    
                                                    <input type="file" name="file" class="form-control" onchange="myFunction(this)" />
                                                </div>
                                            </div><!-- form-group -->
                                                                                                                             
                                        </div><!-- row -->
 
                                    </div><!-- panel-body -->
                                      
                                    
                                
<div class="panel-footer">
                                      <div class="row">
                                        <div class="col-sm-9 col-sm-offset-3">
                                            <button class="btn btn-primary mr5" name="update" >Update</button>
                                            <!--<button type="reset" class="btn btn-dark">Reset</button>-->
                                        </div>
                                      </div>
                                    </div><!-- panel-footer -->
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
        <script src="js/jquery-ui-1.10.3.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/modernizr.min.js"></script>
        <script src="js/pace.min.js"></script>
        <script src="js/retina.min.js"></script>
        <script src="js/jquery.cookies.js"></script>
        
        <script src="js/jquery.autogrow-textarea.js"></script>
        <script src="js/jquery.mousewheel.js"></script>
        <script src="js/jquery.tagsinput.min.js"></script>
        <script src="js/toggles.min.js"></script>
        <script src="js/bootstrap-timepicker.min.js"></script>
        <script src="js/jquery.maskedinput.min.js"></script>
        <script src="js/select2.min.js"></script>
        <script src="js/colorpicker.js"></script>
        <script src="js/dropzone.min.js"></script>
        <script src="js/custom.js"></script>
		
        <script src="js/jquery.prettyPhoto.js"></script>
<script>
            jQuery(document).ready(function() {
                
                               // Date Picker
                jQuery('#datepicker').datepicker({
					
					dateFormat: 'dd MM yy',
					minDate: 0,
					defaultDate: "+0d"
				});
				
                jQuery('#datepicker-inline').datepicker();
                jQuery('#datepicker-multiple').datepicker({
                    numberOfMonths: 3,
                    showButtonPanel: true
                });
                
                
                
           
                
                
            });
        </script>

<script src="js/jquery.validate.min.js"></script>
<script src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/additional-methods.js"></script>

<script type="text/javascript">

$(function() {

  $.validator.addMethod("loginRegex", function(value, element) {
        return this.optional(element) || /^[A-Za-z][A-Za-z\s]+$/i.test(value);
    }, "Title must contain only letters or space.");

$.validator.addMethod("LastRegex", function(value, element) {
        return this.optional(element) || /^[A-Za-z][A-Za-z\s]+$/i.test(value);
    }, "Description must contain only letters or space.");



   $.validator.addMethod("PhoneRegex", function(value, element) {
        return this.optional(element) || /^[0-9]+$/i.test(value);
    }, "Enter price only.");

                $("#basicForm").validate({
                    rules: {
                created_date:{
				 required: true				
            },
               
			          
                   description:{
				 required: true,
				 LastRegex: true,
                           minlength: 3,
			   maxlength: 140
            },           
               title:{
				 required: true,
				loginRegex: true,
                           minlength: 3,
			   maxlength: 30
            },
        
                  file13:{
				 required: true
				
            }
					
					},
                     messages: {
			title: {
		    required:"Please Enter Title "
                 
		  	
			},
                         description: {
		    required:"Please Enter Description"
		  	
			},
            created_date: {
		    required:"Please Select date"
		  	
			},

                  file: {
		    required:"Please Choose Image"
		  	
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
                     
				
  var y=document.getElementById('news'); 



 y.setAttribute("class", "active");



</script>
<script type="text/javascript">




function myFunction(me) {

if(me.files[0].size>2097152)
{
alert('maximum 2 MB file size allowed');
me.value='';
return;


}
    

var val = me.value.toLowerCase();
var regex = new RegExp("(.*?)\.(jpg|jpeg|png)$");
 
if(!(regex.test(val))) {
me.value='';
alert('Unsupported file accept only jpg | jpeg | png file');
return;

} 
   
    
        meid=me.id;
    var divid1=meid.split("-");
     
      
      str1='#d';
var tt=str1.concat(divid1[1]);

    
            var files = !!me.files ? me.files : [];
        if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support
        
        if (/^image/.test( files[0].type)){ // only image file
            var reader = new FileReader(); // instance of the FileReader
            reader.readAsDataURL(files[0]); // read the local file
            
            reader.onloadend = function(){ // set image data as background of div
                $(tt).css("background-image", "url("+this.result+")");
            }
        }
        
        
}




var i="<?php echo $cimage;?>";

var p="<?php echo $cimage;?>";

var k=1;




function removeclick(el)
{



if(el.id.length>0)
{
$.post('removeimage.php', { value : el.id }, responsedb, 'html');
}
 

el.parentNode.parentNode.parentNode.removeChild(el.parentNode.parentNode); 


p--;
if(p<=0)
{
var trm1=document.getElementById('trm1');    
 trm1.setAttribute("style", "");
document.getElementById('uploadFile-90').disabled=false;
}
else
{
var trm1=document.getElementById('trm1');    
 trm1.setAttribute("style", "display:none");
document.getElementById('uploadFile-90').disabled=true;
}

}

function responsedb(data) {

 /*alert(data);*/

   
}
 
function cE(el){ 
this.obj =document.createElement(el); 
return this.obj 
} 
function cA(obj,att,val){ 
obj.setAttribute(att,val); 
return 
} 
</script> 

    </body>
</html>
<?php }}}}?>