<?php


 include("con1.php");
if(isset($_POST['clk'])){

$to  = 'sanjay.youngdecade@gmail.com';	
$subject = 'Account Activation Mail From Select Cook';

 $message='<html><body>
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#FF7401">
  <tr>
    <td><table width="600" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" align="center">
        <tr>
          <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="61"><a href= "" target="_blank"><img src="http://youngdecadeprojects.biz/chefapp/admin/image/logo.png" width="61" height="76" border="0" alt=""/></a></td>
                <td width="144"><a href= "" target="_blank"><img src="http://youngdecadeprojects.biz/chefapp/admin/image/PROMO-GREEN2_01_02.jpg" width="144" height="76" border="0" alt=""/></a></td>
                <td width="393"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td height="46" align="right" valign="middle"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr>
                            <td width="67%" align="right"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#68696a; font-size:8px; text-transform:uppercase"><a href= "" style="color:#68696a; text-decoration:none"><strong>SEND TO A FRIEND</strong></a></font></td>
                            <td width="29%" align="right"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#68696a; font-size:8px"><a href= "" style="color:#68696a; text-decoration:none; text-transform:uppercase"><strong>VIEW AS A WEB PAGE</strong></a></font></td>
                            <td width="4%">&nbsp;</td>
                          </tr>
                        </table></td>
                    </tr>
                    <tr>
                      <td height="30"><img src="http://youngdecadeprojects.biz/chefapp/admin/image/PROMO-GREEN2_01_04.jpg" width="393" height="30" border="0" alt=""/></td>
                    </tr>
                  </table></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td align="center"><a href= "" target="_blank"><img src="http://youngdecadeprojects.biz/chefapp/admin/image/PROMO-GREEN2_02.jpg" alt="" width="598" height="323" border="0"/></a></td>
        </tr>
        <tr>
          <td align="center" valign="middle"><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="2%">&nbsp;</td>
                <td width="96%" align="center" style="border-bottom:1px solid #000000" height="70"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#68696a; font-size:46px; text-transform:uppercase"><strong>PROMOTION TITLE</strong></font></td>
                <td width="2%">&nbsp;</td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="5%">&nbsp;</td>
                <td width="90%" align="center" valign="middle"><font style="font-family: Verdana, Geneva, sans-serif; color:#68696a; font-size:12px; line-height:20px; text-transform:uppercase">Lorem Ipsum. Proin gravida nibh vel  auctor aliquet. Aenean sollicitudin,  lorem quis bibendum auctor, nisi elit consequat ipsum, nec sagittis sem </font><br />
                  <font style="font-family:Verdana, Geneva, sans-serif; color:#f58220; font-size:12px; line-height:20px"><a href= "" style="color:#f58220; text-decoration:none"><strong>&lt; view more details &gt;</strong></a></font></td>
                <td width="5%">&nbsp;</td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td><table width="600" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="18">&nbsp;</td>
                <td width="175" align="center" valign="top"><table width="175" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td  bgcolor="#f58220"><img src="http://youngdecadeprojects.biz/chefapp/admin/image/PROMO-GREEN2_04_01.jpg" width="175" height="14" style="display:block" border="0" alt=""/></td>
                    </tr>
                    <tr>
                      <td height="30" align="center" valign="middle" bgcolor="#f58220"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#ffffff; font-size:20px; text-transform:uppercase"><strong>UPCOMING 2</strong></font></td>
                    </tr>
                    <tr>
                      <td bgcolor="#f58220"><img src="http://youngdecadeprojects.biz/chefapp/admin/image/PROMO-GREEN2_00.jpg" alt="" width="175" height="18" /></td>
                    </tr>
                    <tr>
                      <td align="center" valign="middle" bgcolor="#f58220"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#ffffff; font-size:14px"><strong><a href="" target="_blank" style="color:#ffffff; text-decoration:none">view details</a></strong></font></td>
                    </tr>
                    <tr>
                      <td align="center" valign="middle" bgcolor="#f58220">&nbsp;</td>
                    </tr>
                  </table></td>
                <td width="19">&nbsp;</td>
                <td width="175" align="center" valign="top"><table width="175" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td  bgcolor="#f58220"><img src="http://youngdecadeprojects.biz/chefapp/admin/image/PROMO-GREEN2_04_01.jpg" width="175" height="14" style="display:block" border="0" alt=""/></td>
                  </tr>
                  <tr>
                    <td height="30" align="center" valign="middle" bgcolor="#f58220"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#ffffff; font-size:20px; text-transform:uppercase"><strong>UPCOMING 2</strong></font></td>
                  </tr>
                  <tr>
                    <td bgcolor="#f58220"><img src="http://youngdecadeprojects.biz/chefapp/admin/image/PROMO-GREEN2_00.jpg" alt="" width="175" height="18" /></td>
                  </tr>
                  <tr>
                    <td align="center" valign="middle" bgcolor="#f58220"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#ffffff; font-size:14px"><strong><a href="" target="_blank" style="color:#ffffff; text-decoration:none">view details</a></strong></font></td>
                  </tr>
                  <tr>
                    <td align="center" valign="middle" bgcolor="#f58220">&nbsp;</td>
                  </tr>
                </table></td>
                <td width="19">&nbsp;</td>
                <td width="175" align="center" valign="top"><table width="175" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td  bgcolor="#f58220"><img src="images/PROMO-GREEN2_04_01.jpg" width="175" height="14" style="display:block" border="0" alt=""/></td>
                  </tr>
                  <tr>
                    <td height="30" align="center" valign="middle" bgcolor="#f58220"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#ffffff; font-size:20px; text-transform:uppercase"><strong>UPCOMING 2</strong></font></td>
                  </tr>
                  <tr>
                    <td bgcolor="#f58220"><img src="images/PROMO-GREEN2_00.jpg" alt="" width="175" height="18" /></td>
                  </tr>
                  <tr>
                    <td align="center" valign="middle" bgcolor="#f58220"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#ffffff; font-size:14px"><strong><a href="" target="_blank" style="color:#ffffff; text-decoration:none">view details</a></strong></font></td>
                  </tr>
                  <tr>
                    <td align="center" valign="middle" bgcolor="#f58220">&nbsp;</td>
                  </tr>
                </table></td>
                <td width="19">&nbsp;</td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td><img src="http://youngdecadeprojects.biz/chefapp/admin/image/PROMO-GREEN2_07.jpg" width="598" height="7" style="display:block" border="0" alt=""/></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="13%" align="center">&nbsp;</td>
              <td width="14%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><a href= "" style="color:#010203; text-decoration:none"><strong>UNSUBSCRIBE </strong></a></font></td>
              <td width="2%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><strong>|</strong></font></td>
              <td width="9%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><a href= "" style="color:#010203; text-decoration:none"><strong>ABOUT </strong></a></font></td>
              <td width="2%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><strong>|</strong></font></td>
              <td width="10%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><a href= "" style="color:#010203; text-decoration:none"><strong>PRESS </strong></a></font></td>
              <td width="2%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><strong>|</strong></font></td>
              <td width="11%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><a href= "" style="color:#010203; text-decoration:none"><strong>CONTACT </strong></a></font></td>
              <td width="2%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><strong>|</strong></font></td>
              <td width="17%" align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#010203; font-size:9px; text-transform:uppercase"><a href= "" style="color:#010203; text-decoration:none"><strong>STAY CONNECTED</strong></a></font></td>
              <td width="4%" align="right"><a href="https://www.facebook.com/" target="_blank"><img src="http://youngdecadeprojects.biz/chefapp/admin/image/PROMO-GREEN2_09_01.jpg" alt="facebook" width="21" height="19" border="0" /></a></td>
              <td width="5%" align="center"><a href="https://twitter.com/" target="_blank"><img src="http://youngdecadeprojects.biz/chefapp/admin/image/PROMO-GREEN2_09_02.jpg" alt="twitter" width="23" height="19" border="0" /></a></td>
              <td width="4%" align="right"><a href="http://www.linkedin.com/" target="_blank"><img src="http://youngdecadeprojects.biz/chefapp/admin/image/PROMO-GREEN2_09_03.jpg" alt="linkedin" width="20" height="19" border="0" /></a></td>
              <td width="5%">&nbsp;</td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td align="center"><font style="font-family:Myriad Pro, Helvetica, Arial, sans-serif; color:#231f20; font-size:8px"><strong>Head Office &amp; Registered Office | Company name Ltd, Adress Line, Company Street, City, State, Zip Code | Tel: 123 555 555 | <a href= "" style="color:#010203; text-decoration:none">customercare@company.com</a></strong></font></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
      </table></td>
  </tr>
</table>
</body>';



 $headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
$headers .= 'From:Select Cook App<youngdecade@youngdecadeprojects.biz>' . "\r\n";






if (mail($to,$subject,$message,$headers))
 {

$record1=array('success'=>'true','msg'=>'Thank You Please Check Your Mail For Verify Your Account'); 
 $data = json_encode($record1);
echo $data;
return;
}

else
{
 $record=array('success'=>'false','msg'=>'Mail Not Send'); 
 $data = json_encode($record);
echo $data;
return;




}

}

?>