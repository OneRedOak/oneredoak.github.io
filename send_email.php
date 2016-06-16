<?php

// Start the session
session_start();

if($_GET['code']) {

// Send a image
create_image();
exit();
}

function create_image(){
    // Generate a random string
    $md5_hash = md5(rand(0,999));
     
    // Make it 5 characters long
    $security_code = substr($md5_hash, 15, 5); 

    // Storing the security code in the session
    $_SESSION["security_code"] = $security_code;

    // Create the image
    $image = @imagecreatefrompng("images/captcha_bg.png");

    // Making the font color
    $black = imagecolorallocate($image, 255, 255, 255);

    // Make the background black 
    // imagefill($image, 0, 0, $bgImg); 

    // Set some variables for positioning and font-size, "5" is the largest I could get to work
    $vPos = 16;
    $hPos = 36;
    $fontSize = 5;
    
    imagestring($image, $fontSize, $hPos, $vPos, $security_code, $black); 
 
    // Tell the browser what kind of file this is 
    header("Content-Type: image/png"); 

    // Output image as a png
    imagepng($image);
   
    // Free up stuff
    imagedestroy($image);
}

    // We need to get our variables first
    
    $emailSub   = "New DOEKits.com Feedback Received"; // The standard subject for email
    $emailTo    = "mike@doekits.com"; // The address to which the email will be sent
    $name       = strip_tags($_POST['name']);
    $emailFrom  = strip_tags($_POST['email']);
    $subject    = strip_tags($_POST['subject']);
    $message    = strip_tags(stripslashes($_POST['message']));
    $captcha    = strip_tags($_POST['captcha']);
    
    // Combining and creating the message body
    
    $body = "Name: ".$name."\n";
    $body .= "Email: ".$emailFrom."\n";
    $body .= "Subject: ".$subject."\n";
    $body .= "Message: ".$message."\n";
    
    // The $headers variable is for the additional headers in the mail function
     
    $headers  = "MIME-Version: 1.0\r\n";
    $headers  = "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "Content-type: text/plain; charset=UTF-8\r\n";
    $headers .= "From: " . $emailFrom . " <" . $name . "> \n";
    $headers .= "Reply-To: " . $emailFrom . "\n\n";

    // Check email subject availability

    if($subject) {
        $emailSub = $subject;
    }
    
    // Validate security code

    if($_SESSION['security_code'] == $captcha) {
        $success = mail($emailTo, $emailSub, $body, $headers);
        
        if($success){
            unset($_SESSION['security_code']); // Remove session
            echo "sent"; // Sending this text to the ajax request telling it that the mail is sent
        }else {
            echo "error"; // Or this one to tell it that it wasn't sent
    }
}

?>