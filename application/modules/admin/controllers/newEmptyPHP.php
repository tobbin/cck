<?php include 'header.php' ?>
<?php include 'nav.php' ?>
<?php //include 'banner.php' ?>
<?php
	if(isset($_POST['name']) && $_POST['name'] != '') {
		$sql = "INSERT INTO `contact` (
					`name` ,
					`email` ,
					`phone` ,
					`message` 
					)
					VALUES ('".$_POST['name']."', '".$_POST['email']."', '".$_POST['phone']."', 

'".$_POST['message']."')";
		mysql_query($sql);
		$fromEmail = $_POST['email'];
		$msg = $_POST['message'];
		
		// multiple recipients
		$to  = 'contact@kennelvombello.com'; 
		// subject
		$subject = 'KENNEL VOM BELLO Contact Enquiry';
		
		// message
		$message = '<html>
		<head>
		  <title>KENNEL VOMBELLA</title>
		</head>
		<body>
		  <table width="66%" border="0">
                      <tr>
                        <td width="33%">Name</td>
                        <td width="2%">&nbsp;</td>
                        <td width="65%">'.$_POST['name'].'</td>
                      </tr>
                       <tr>
                        <td width="33%">Date</td>
                        <td width="2%">&nbsp;</td>
                        <td width="65%">'.date("d-m-Y").'</td>
                      </tr>
                      <tr>
                        <td>Email</td>
                        <td>&nbsp;</td>
                        <td>'.$_POST['email'].'</td>
                      </tr>
                        <tr>
                        <td>Phone</td>
                        <td>&nbsp;</td>
                        <td>'.$_POST['phone'].'</td>
                      </tr>	  <tr>
                        <td>Message</td>
                        <td>&nbsp;</td>
                        <td>'.$_POST['message'].'</td>
                      </tr>
                    </table>
		</body>
		</html>';
		
		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		// Additional headers
		$headers .= "To: Admin <{$to}>" . "\r\n";
		$headers .= "From: {$_POST['name']} <{$_POST['email']}>" . "\r\n";
		
		// Mail it
		mail($to, $subject, $message, $headers);
		
		header('location:contact.php?msg=send');
	}
?>
<div class="content cont-sub">
      <h1 class="border">Contact Us</h1>
	  <?php if(isset($_GET['msg'])) { ?>
		<script type="text/javascript">
		$(document).ready(function() {
			$('#msg').fadeIn(3000);
			$('#msg').fadeOut(3000);
			});
		</script>
		
	  <?php } ?>
      <div style="color:#FF0000; font-size:14px; text-align:center; height:20px; font-weight:bold;"><span id="msg" style="display:none;">Your message sent 

successfully</span></div>
      <div class="contact-box">
        <div class="contact-form" style="float: left !important; margin: 20px !important; border-right: 1px dashed #949494;  padding: 30px !important;">
          <form action="" method="post" id="formContact" name="formContact">
            <ul>
              <li><span class="left">
                <label>Name</label>
                </span><span class="right">
                <input name="name" id="name" type="text"  />
                </span></li>
              <li><span class="left">
                <label>E-mail</label>
                </span><span class="right">
                <input name="email" id="email" type="text" />
                </span></li>
              <li><span class="left">
                <label>Phone</label>
                </span><span class="right">
                <input name="phone" id="phone" type="text" />
                </span></li>
              <li><span class="left">
                <label>Message</label>
                </span><span class="right">
                <textarea name="message" id="message" cols="" rows=""></textarea>
                </span></li>
              <li class="padding-left01">
                <input name="btn_submit" type="button" value="Submit" class="button" onclick="validateContact()" />
                <input name="btn_clear" type="button" value="Clear" class="button" onclick="clearContact()" />
              </li>
              <li class="padding-left01">
               <span class="left" id="errorSpan"><b></b></span>
              </li>
            </ul>
          </form>
        </div>
        <div style="float: left; margin-left: 20px;  padding:150px 0 0 50px; font-size: 15px; font-weight: bold; color: #EEA82E">
            Mr. Sunil Sathyan
            <br>Chavakkad
            <br>Kerala-india
        </div>
          <div style="clear: both">&nbsp;</div>
      </div>
    </div>
         <?php include 'footer.php' ?>