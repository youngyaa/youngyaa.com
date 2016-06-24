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

<style>
#basicForm{

    word-break: break-word;

}
.error{

color:red;
}
group .control-label {
margin-top: 11px;
}
</style>

<?php


$mesage="";




$url= isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https:' : 'http:'.'//'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);

extract($_POST);
if(isset($_POST['create'])){


 function generateRandomString($length = 50) {
   $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
   $randomString = '';
   for ($i = 0; $i < $length; $i++) {
       $randomString .= $characters[rand(0, strlen($characters) - 1)];
   }
   return $randomString;
}


$insert_time=date("Y-m-d H:i:s");


if(!empty($_FILES['image']['name']))
{
$str= generateRandomString();
$str2= rand();
$str3= $str.$str2;
	$target = "images/"; 
 $filename= $str3.basename( $_FILES['image']['name']);

$filename =trim($_FILES['image']['name']); //rename file
$filename=str_replace(' ','_',$filename);
$filename=$str3.$filename;

//youngyaa.com.au/public_html/admin
//$data="https://www.youngyaa.com.au/public_html/admin/images/".$filename;

 $data='https://www.youngyaa.com.au/admin/images/'.$filename;

if(move_uploaded_file($_FILES['image']['tmp_name'],$target.$filename))
{


}
else
{

echo "<script>alert('Image Not Uploaded');window.location.href='seklingevent.php';</script>";
									exit;
									return;

}

$rules=implode(",",$_POST['rule']);		
$insert=$mysqlipre->query("INSERT INTO `a7rtg_secklioneevent`(`title`, `rule`, `image`, `start_date`, `end_date`, `insertime`,`description`) VALUES ('".$_POST['title']."','".$rules."','".$data."','".$_POST['start_date']."','".$_POST['end_date']."','$insert_time','".$_POST['description']."')");



if($mysqlipre->affected_rows<=0)	{


$mesage='<center><h3><div class="alert alert-danger alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong>Sorry </strong> Error in inserting Seckilling event.please try again!
                </div></h3></center>';



                
               
}
else{
	
$lastid=$mysqlipre->insert_id;
	




foreach ($_FILES['file']['name'] as $i => $name3)
{
	 
    
    
        $filename = stripslashes($_FILES['file']['name'][$i]);

$str= generateRandomString();
$str2= rand();
$str3= $str.$str2;

 

 $filename= $str3.basename($_FILES['file']['name'][$i]);

$filename =trim($_FILES['file']['name'][$i]); //rename file
		$filename=str_replace(' ','_',$filename);
		$filename=$str3.$filename;


 $target = "images/";

		
 $databasename1="https://www.youngyaa.com.au/admin/images/".$filename;
if(move_uploaded_file($_FILES['file']['tmp_name'][$i], $target.$filename)) 
{

if(!empty($_POST['pprice'][$i])&&!empty($_POST['provider'][$i])&&!empty($_POST['start_time'][$i])&&!empty($_POST['end_time'][$i])&&!empty($_POST['desc'][$i]))
{


$mysqlipre->query("INSERT INTO `a7rtg_secklioneevent_price`(`event_id`, `start_time`, `end_time`, `price` ,`image` , `provider`,`description`, `insertime`) VALUES ('$lastid','".$_POST['start_time'][$i]."','".$_POST['end_time'][$i]."','".$_POST['pprice'][$i]."','".$databasename1."','".$_POST['provider'][$i]."','".$_POST['desc'][$i]."','$insert_time')");

}
  if($mysqlipre->affected_rows<=0)
                                                             {
                                                            


echo "<script> alert('image no upload');window.location.href='seklingevent.php';</script>";

return;
exit;
	

                                                             }
}







}





	$mesage='<center><h3><div class="alert alert-success alert-dismissible" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong> Seckilling  Event Created Successfully </strong>
                </div></h3></center>';	
	
	
}	






}
}




?>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Young Yaa | Seckilling Event</title>

   
        <link href="css/prettyPhoto.css" rel="stylesheet">
	
		 <link href="css/style.default.css" rel="stylesheet">
        <link href="css/jquery.tagsinput.css" rel="stylesheet" />
        <link href="css/toggles.css" rel="stylesheet" />
        <link href="css/bootstrap-timepicker.min.css" rel="stylesheet" />
        <link href="css/select2.css" rel="stylesheet" />
        <link href="css/colorpicker.css" rel="stylesheet" />
        <link href="css/dropzone.css" rel="stylesheet" />
          <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon"/>
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
                                <i class="fa fa-leaf"></i>
                            </div>
                            <div class="media-body">
                                <ul class="breadcrumb">
                                    <li><a href=""><i class="glyphicon glyphicon-home"></i></a></li>
                                    <li><a href="">Home</a></li>
                                    <li><a href="">Seckilling Event</a></li>
                                </ul>
                                <h4>Seckilling Event </h4>
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
                                <h4 class="panel-title">Seckilling Event</h4>
                               <!--<p>Searching, ordering, paging etc goodness will be immediately added to the table, as shown in this example.</p>-->
                            </div><!-- panel-heading -->

                                    <div class="panel-body">
                                        <div class="row">
                                                                                  
                                               
                                          <!--  <div class="form-group">
                                                <label class="col-sm-3 control-label">Category Name<span class="asterisk">*</span></label>
                                                <div class="col-sm-9">
                                                 <select class="form-control" name="category_id"  id="category_id">
												<option value="" >---Select Category---</option>
 <//?php 
						   $select="select * FROM `category_master`";
	                          $obj=$mysqlipre->query($select);
                                  
										  			  ?>
												<//?php while($row=$obj->fetch_array(MYSQLI_ASSOC)){
				                                     	 ?>
												<option value="<//?php echo $row['category_id'];?>"><//?php echo $row['category_name'];?></option>
												<//?php }?>
																</select>                                                   
                                                   
                                                </div>
                                            </div><!-- form-group -->
                                                                      


<!--<div class="form-group">
                                                <label class="col-sm-3 control-label">Subcategory Name<span class="asterisk">*</span></label>
                                                <div class="col-sm-9">
<select class="form-control" name="subcategory_id"  id="subcategory_id">
                                                 <option value="" >---Select Subcategory---</option>
												</select>                                                 
                                                   
                                                </div>
                                            </div><!-- form-group -->


                                        
<div class="form-group">
                                                <label class="col-sm-3 control-label">Title<span class="asterisk">*</span></label>
                                                <div class="col-sm-9">
                                                                                                    
                                                    <input type="text" name="title" class="form-control" placeholder="Please Enter Title" />
                                                </div>
                                            </div><!-- form-group -->

<div class="form-group">
                                                <label class="col-sm-3 control-label">Description<span class="asterisk">*</span></label>
                                                <div class="col-sm-9">
                                                  <textarea name="description"  class="form-control" placeholder="Please enter description"></textarea>                                                  

                                                </div>
                                            </div><!-- form-group -->
											<div class="form-group">
											<label class="col-sm-3 control-label"><span class="asterisk"></span></label>
											<div class="col-sm-1" style="margin-bottom: 10px;">
                                                     <input id="addrules" type="button" class="btn btn-primary" value="Add Rules" />                                               
                                                   </div><div style="clear:both;"></div>
                                                <label class="col-sm-3 control-label">Event Rule<span class="asterisk"></span></label>
                                                <div class="col-sm-9">
												<input type="text" name="rule[]" class="form-control" placeholder="Rules" />
                                   <br/>
												 </div>                                              
                                                   
                                               
                                            </div><!-- form-group -->  	  
                                          
 <div id="TextBoxContainer13">
    <!--Textboxes will be added here -->
</div>    
									   
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Seckilling Event Image<span class="asterisk">*</span></label>
                                                <div class="col-sm-9">
                                                                                                    
                                                    <input type="file" name="image" id="image" class="form-control"  onchange="myFunction(this)" />
                                                </div>
                                            </div><!-- form-group -->



<div class="form-group">
                                                <label class="col-sm-3 control-label">Start Date<span class="asterisk">*</span></label>
                                                <div class="col-sm-9">
                                                     <div class="input-group">
                                            <input type="text" class="form-control" name="start_date" placeholder="yy/mm/dd" id="datepicker" readonly>
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                        </div>                                               
                                                    <!--<input type="text" name="created_date" class="form-control" placeholder="Please Enter Title" />-->
                                               <label for="datepicker" class="error"></label> </div>
                                            </div><!-- form-group -->




<div class="form-group">
                                                <label class="col-sm-3 control-label">End Date<span class="asterisk">*</span></label>
                                               <div class="col-sm-9">
                                                     <div class="input-group">
                                            <input type="text" class="form-control" name="end_date" placeholder="yy/mm/dd" id="datepicker11" readonly>
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                        </div>                                               
                                                    <!--<input type="text" name="created_date" class="form-control" placeholder="Please Enter Title" />-->
                                               <label for="datepicker11" class="error"></label> </div>
                                            </div><!-- form-group -->












                                        <div class="form-group">
                                                <label class="col-sm-12 control-label text-center"><b>Event Add / Edit </b><span class="asterisk"></span></label><br/>
                                               
                                   <div class="col-sm-1">
                                                     <input id="btnAdd" type="button" class="btn btn-primary" value="Add Details" />                                               
                                                   </div>
<div style="clear:both;"></div>
<br>

<div class="form-group"><div class="col-sm-2">
<div class="input-group mb15"><span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
<div class="bootstrap-timepicker"><input id="timepickerrr" class="form-control" type="text" name="start_time[]" placeholder="Start Time" readonly /></div></div>
<label for="timepicker2" class="error"></label></div>
 <div class="col-sm-2"><div class="input-group mb15"><span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span><div class="bootstrap-timepicker"><input id="timepicker" class="form-control" type="text"  name="end_time[]"   placeholder="End Time" readonly /></div></div><label for="timepicker3" class="error"></label> </div>
 <div class="col-sm-2"> <input name = "pprice[]" id= "pprice[]" class="form-control" type="text" value = "" placeholder="Enter Prize"/></div>
 <div class="col-sm-2"> <input name="file[]" id="file[]" class="form-control" type="file" onchange="myFunction(this)" ></div>
 <div class="col-sm-2"> <input name="provider[]" id= "provider[]" class="form-control" type="text" value = "" placeholder=" Prize Provider"/></div>
 <div class="col-sm-2"> <input name="desc[]" id= "desc[]" class="form-control" type="text" value = "" placeholder="Prize Description"/></div>
                                            </div><!-- form-group -->
                                               <div class="form-group">
                                               
                                                <div class="col-sm-12">
                                                    <div id="TextBoxContainer">
    <!--Textboxes will be added here -->
</div>                                              
                                                   
                                                </div>
                                            </div><!-- form-group -->        
                                        </div><!-- row -->
 
                                    </div><!-- panel-body -->
                                      <div class="panel-footer">
                                      <div class="row">
                                        <div class="col-sm-9 col-sm-offset-3">
                                            <button class="btn btn-primary mr5" name="create" >Submit</button>
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
  
  
  
  
  	  <script type="text/javascript">




function myFunction(me) {

if(me.files[0].size>3145728)


{
alert('maximum 3 MB file size allowed');
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

<script type="text/javascript">
$(function () {
    $("#addrules").bind("click", function () {
        var div = $("<div />");
        div.html(GetDynamicTextBox11(""));
        $("#TextBoxContainer13").append(div);
    });
    /*$("#btnGet").bind("click", function () {
        var values = "";
        $("input[name=DynamicTextBox]").each(function () {
            values += $(this).val() + "\n";
        });
        alert(values);
    });*/
    $("body").on("click", ".remove", function () {
        $(this).closest("div").remove();
    });
});
function GetDynamicTextBox11(value) {     
   return '<div class="form-group"> <label class="col-sm-3 control-label"><span class="asterisk"></span></label><div class="col-sm-7"><input name = "rule[]"  id="rule[]" class="form-control" type="text" value = "' + value + '" placeholder="Rules"/>&nbsp;</div>' +
            '<input type="button" value="Remove" class="btn btn-danger remove" /><br/></div>'  
           
            
}
</script>

<script type="text/javascript">
$(function () {
	var tt=1;
    $("#btnAdd").bind("click", function () {
        var div = $("<div />");
        div.html('<div class="form-group"><div class="col-sm-2"><div class="input-group mb15"><span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span><div class="bootstrap-timepicker"><input id="timepicker2'+tt+'" dateFormat="HH:mm:ss" class="form-control" type="text" name="start_time[]" placeholder="Start Time" readonly /></div></div><label for="timepicker2" class="error"></label>&nbsp;</div>'
 +'<div class="col-sm-2"><div class="input-group mb15"><span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span><div class="bootstrap-timepicker"><input id="timepicker'+tt+'" dateFormat="HH:mm:ss" class="form-control" type="text"  name="end_time[]"   placeholder="End Time" readonly /></div></div><label for="timepicker3" class="error"></label>&nbsp;</div>' +'<div class="col-sm-2"> <input name = "pprice[]" id= "pprice[]'+tt+'" class="form-control" type="text" value = "" placeholder="Enter Prize"/>&nbsp;</div>' + '<div class="col-sm-2"> <input name="file[]" id="file[]'+tt+'" class="form-control" type="file" onchange="myFunction(this)" >&nbsp;</div>' +'<div class="col-sm-2"> <input name="provider[]" id= "provider[]'+tt+'" class="form-control" type="text" value = "" placeholder=" Prize Provider"/>&nbsp;</div>' +'<div class="col-sm-2"> <input name="desc[]" id= "desc[]'+tt+'" class="form-control" type="text" value = "" placeholder="Prize Description"/>&nbsp;</div>&nbsp;&nbsp;&nbsp;' +
            '<input type="button" value="Remove" class="btn btn-danger remove" /><br/></div>');
			
			
        $("#TextBoxContainer").append(div);
		jQuery('#timepicker2'+tt).timepicker({showMeridian: false,showSeconds: true});      
		    jQuery('#timepicker'+tt).timepicker({showMeridian: false,showSeconds: true});
		
		
	tt++;
    });
    /*$("#btnGet").bind("click", function () {
        var values = "";
        $("input[name=DynamicTextBox]").each(function () {
            values += $(this).val() + "\n";
        });
        alert(values);
    });*/
    $("body").on("click", ".remove", function () {
        $(this).closest("div").remove();
    });
});
function GetDynamicTextBox(value) {     


    '<div class="form-group"><div class="col-sm-2"><div class="input-group mb15"><span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span><div class="bootstrap-timepicker"><input id="timepicker2'+value+'" class="form-control" type="text" name="start_time[]" placeholder="Start Time" readonly /></div></div><label for="timepicker2" class="error"></label>&nbsp;</div>'
 +'<div class="col-sm-2"><div class="input-group mb15"><span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span><div class="bootstrap-timepicker"><input id="timepicker'+value+'" class="form-control" type="text"  name="end_time[]"   placeholder="End Time" readonly /></div></div><label for="timepicker3" class="error"></label>&nbsp;</div>' +'<div class="col-sm-2"> <input name = "pprice[]" id= "pprice[]" class="form-control" type="text" value = "' + value + '" placeholder="Enter Prize"/>&nbsp;</div>' + '<div class="col-sm-2"> <input name="file[]" id="file[]" class="form-control" type="file" onchange="myFunction(this)" >&nbsp;</div>' +'<div class="col-sm-2"> <input name="provider[]" id= "provider[]" class="form-control" type="text" value = "' + value + '" placeholder=" Prize Provider"/>&nbsp;</div>' +'<div class="col-sm-2"> <input name="desc[]" id= "desc[]" class="form-control" type="text" value = "' + value + '" placeholder="Prize Description"/>&nbsp;</div>&nbsp;&nbsp;&nbsp;' +
            '<input type="button" value="Remove" class="btn btn-danger remove" /><br/></div>'  ;
           
    	 jQuery('#timepicker1'+value).timepicker({showMeridian: false});      
		    jQuery('#timepicker'+value).timepicker({showMeridian: false});        
}
</script>                                           
                                          
<script type="text/javascript">
	$(document).ready(function(){
	$('#category_id').change(function(){
		var category_id = $('#category_id').val();
		if(category_id!= 0)
		{
			$.ajax({
				type:'post',
				 url:'getvalue.php', 
				
				data:{category_id:category_id},
				cache:false,
				success: function(returndata){
					$('#subcategory_id').html(returndata);
				}
			});
		}
	})
})
 	</script> 
	   
  
  
  
  
  
  
  
  
  
<script>
            jQuery(document).ready(function() {
               
                
                // Time Picker
                
                jQuery('#timepickerrr').timepicker({showMeridian: false,showSeconds: true});
                jQuery('#timepicker').timepicker({showMeridian: false,showSeconds: true});
               
                
                
                
                
            });
        </script>
<script>
            jQuery(document).ready(function() {
                
				             
				
				
                               // Date Picker
                jQuery('#datepicker').datepicker({
					
					dateFormat: 'yy/mm/dd',
					minDate: 0,
					defaultDate: "+0d"
				});
				 jQuery('#datepicker11').datepicker({
					
					dateFormat: 'yy/mm/dd',
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
		<script type="text/javascript">
$( document ).ready(function() {






   

$("#datepicker").change(function(){
var ffd=$("#datepicker").val();
var fed=$("#datepicker11").val();


if(fed!=''||fed=='<?php echo date('yy/mm/dd') ?>')
{
if(ffd>fed)
{
alert('start date can not greater then end date');
$("#datepicker").val('');
return;
}
}
});




$("#datepicker11").change(function(){
var ffd=$("#datepicker").val();
var fed=$("#datepicker11").val();


if(ffd!=''||ffd=='<?php echo date('yy/mm/dd') ?>')
{
if(ffd>fed)
{
alert('end date can not less then start date');
$("#datepicker11").val('');
return;
}
}
});



});
    </script> 
<script src="js/jquery.validate.min.js"></script>
<script src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/additional-methods.js"></script>
<script type="text/javascript">
$(function() {

    $.validator.addMethod("loginRegex", function(value, element) {
        return this.optional(element) || /^[A-Za-z][A-Za-z0-9\s_~\.-!@#\$%\^&\*\(\)]+$/i.test(value);
    }, "Accept only letters or space or special character.");

$.validator.addMethod("LastRegex", function(value, element) {
        return this.optional(element) || /^[A-Za-z][A-Za-z0-9\s_~\.-!@#\$%\^&\*\(\)]+$/i.test(value);
    }, "Accept only letters or space or special character.");

 $.validator.addMethod("ruleRegex", function(value, element) {
        return this.optional(element) || /^[A-Za-z][A-Za-z\s]+$/i.test(value);
    }, "Accept only letters or space.");

   $.validator.addMethod("PhoneRegex", function(value, element) {
        return this.optional(element) || /^[A-Za-z][A-Za-z\s]+$/i.test(value);
    }, "Enter price only.");

                $("#basicForm").validate({
                    rules: {
                title:{
				 required: true,
				loginRegex: true,
                           minlength: 3,
			   maxlength: 30
            },
                 description:{
				 required: true,
				 LastRegex: true,
                           minlength: 3,
			   maxlength: 140
            },
			
            "rule[]":{
				 required: true,
				LastRegex: true,
                           minlength: 3,
			 maxlength: 50
            },
                          
                image:{
				 required: true
				
            },
        
                  start_date:{
				 required: true
				
            },
   end_date:{
				 required: true
				
            },
                  "file[]":{
				 required: true
				
            },

 "start_time[]":{
				 required: true,
                               
				
            },

"end_time[]":{
				 required: true,
                               
				
            },

"pprice[]":{
				 required: true,
				 ruleRegex: true,
                     minlength: 3,
			 maxlength: 50           
				
            },


"provider[]":{
				 required: true,
                  loginRegex:true,            
				    minlength: 3,
			   maxlength: 50 
            },
"desc[]":{
				 required: true,
                   loginRegex: true,
                           minlength: 3,
			   maxlength: 50            
				
            },
					
					},
                     messages: {
			title: {
		    required:"Please Enter title "
                 
		  	
			},
                         description: {
		    required:"Please Enter Description"
		  	
			},
            "rule[]": {
		    required:"Please Enter Rules"
		  	
			},

                  image: {
		    required:"Please Select Image"
		  	
			},
			              start_date: {
		    required:"Please Select Start date"
		  	
			},
			              end_date: {
		    required:"Please Select End date"
		  	
			},
       "file[]": {
		    required:"Please Choose file"
		  	
			},

			 "start_time[]":{
			 required:"Please Select Start Time"
                               
				
            },

"end_time[]":{
				 required:"Please Select End Time"
				
            },

"pprice[]":{
					 required:"Please Enter Prize"
                               
				
            },


"provider[]":{
					 required:"Please Enter Provider"
                               
				
            },
	 
			"desc[]":{
					 required:"Please Enter Description"
                               
				
            },	 
			
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
            },



                  
					
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
                     
				
  var y=document.getElementById('seck'); 



 y.setAttribute("class", "active");


</script>

    </body>
</html>