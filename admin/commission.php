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

?>
<?php

if(isset($_POST['update'])){

$date=date("Y-m-d H:i:s");

$sql=$mysqlipre->query("UPDATE `commission_master` SET `commission`='".$_POST['com']."',`inserttime`='$date'");



$i2=$mysqlipre->affected_rows;


if($i2<=0){

$mesage='<center><h3><div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong>Sorry </strong> Error in Update .please try again!
                </div></h3></center>';

}


else{

$mesage='<center><h3><div class="alert alert-success alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong>Commission Updated  Successfully </strong>
                </div></h3></center>';


}
}

?>


 <?php  



$select="select * from commission_master";
	$obj=$mysqlipre->query($select);
	if($obj->num_rows<=0){

echo "Error in Commission";	
	     
                 }
                

$row=$obj->fetch_array(MYSQLI_ASSOC);

			
?>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Select Cook | Commission</title>

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
                                    <li><a href="">Commission</a></li>
                                </ul>
                                <h4>Commission</h4>
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
                                          
										<div class="col-md-1"></div>   <div class="col-md-10">
                                <form id="basicForm" action="" method="post" enctype="multipart/form-data" autocomplete="off">
                                <div class="panel panel-default">

                                   <div class="panel-heading" style="background-color:#FF7401;color:white;>
                                <h4 class="panel-title">Commission</h4>
                               <!--<p>Searching, ordering, paging etc goodness will be immediately added to the table, as shown in this example.</p>-->
                            </div><!-- panel-heading -->

                                    <div class="panel-body">
                                        <div class="row">
                                                                                  
                                               
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Enter Commission<span class="asterisk">*</span></label>
                                                <div class="col-sm-9">
                                                    <div class="input-group mb15">
                                                        <input type="text" name="com" class="form-control" value="<?php echo $row['commission']
?>" placeholder="Please Enter Commission" />
                                                        <span class="input-group-addon">%</span>
                                                    </div><label for="com" class="error"></label>
</div>
                                            </div><!-- form-group -->
                                                                                 
                                            
                                                       
                                        </div><!-- row -->
 
                                    </div><!-- panel-body -->
                                      <div class="panel-footer">
                                      <div class="row">
                                        <div class="col-sm-9 col-sm-offset-3">
                                            <button class="btn btn-primary mr5 text-center" name="update" >Update</button>
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
$(function() {

    $.validator.addMethod("loginRegex", function(value, element) {
        return this.optional(element) || /^[0-9\-\s]+$/i.test(value);
    }, "Accept Only Numbers");

                $("#basicForm").validate({
                    rules: {
                 com:{
				 required: true,
				loginRegex: true,
                           minlength:1,
			   maxlength: 3
            },
                   last_name:{
				 required: true,
				 lettersonly: true,
                           minlength: 3,
			   maxlength: 30
            },
			
            email:{
				 required: true,
				 email:true,
			 maxlength: 50
            },
                          
              
        
                  file:{
				 required: true
				
            }
					
					},
                     messages: {
			com: {
		    required:"Please Enter Commission "
                 
		  	
			},
                         last_name: {
		    required:"Please enter Last Name"
		  	
			},
            email: {
		    required:"Please enter email"
		  	
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
                     
				
  var y=document.getElementById('broad'); 



 y.setAttribute("class", "active");


 


</script>
<script type="text/javascript">




function myFunction(me) {

if(me.files[0].size>2097152)
{
alert('maximum 1 MB file size allowed');
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