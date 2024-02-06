<?php
/**
 * Template Name: send quote
 */
require $_SERVER['DOCUMENT_ROOT'].'/dompdf/vendor/autoload.php';
use Dompdf\Dompdf;

global $wpdb;

$reciept = $_POST;

// Decode the JSON string and retrieve the styles array
$styles = array();
$index = 0;
while (isset($reciept["style_" . $index])) {
    $raw_json = $reciept["style_" . $index];
    $cleaned_json = stripslashes($raw_json);
    $style_data = json_decode($cleaned_json, true);
    if ($style_data !== null) {
        $styles[] = $style_data;
    }
    $index++;
}
$attach='';
        $to = 'sales-team@bartile.com'; 
		$from = 'info@bartile.com'; 
		$subject = "Quotation";
		
		if($_FILES['eagle-view']['name']){
		    
		$tmp_name = $_FILES['eagle-view']['tmp_name']; // get the temporary file name of the file on the server
    $name     = $_FILES['eagle-view']['name']; // get the name of the file
    $size     = $_FILES['eagle-view']['size']; // get size of the file for size validation
    $type     = $_FILES['eagle-view']['type']; // get type of the file
    $error     = $_FILES['eagle-view']['error']; // get the error (if any)
 
    //validate form field for attaching the file

		//read from the uploaded file & base64_encode content
    $handle = fopen($tmp_name, "r"); // set the file handle only for reading the file
    $data = fread($handle, $size); // reading the file
    fclose($handle);                 // close upon completion
 
    $encoded_content = chunk_split(base64_encode($data));
    $boundary = md5("random"); // define boundary with a md5 hashed value
		    $headers = "MIME-Version: 1.0\r\n"; // Defining the MIME version
    	$headers .= 'From: The Birtile<'.$from.'>' . "\r\n"; 
   // $headers .= "Reply-To: ".$reply_to_email."\r\n"; // Email address to reach back
   // $headers .= "Content-Type: multipart/mixed;"; // Defining Content-Type
   // $headers .= "boundary = $boundary\r\n"; //Defining the Boundary
	//	$headers = "MIME-Version: 1.0" . "\r\n"; 
		$headers .= "Content-type:text/html" . "\r\n"; 
		
	 //attachment
   move_uploaded_file( $_FILES['eagle-view']['tmp_name'], WP_CONTENT_DIR.'/uploads/'.basename( $_FILES['eagle-view']['name'] ) );
                //$attach = WP_CONTENT_DIR.'/uploads/'.basename( $_FILES['eagle-view']['name'] );
                $attach="https://bartile.goaspendigital.com/wp-content/uploads/".basename( $_FILES['eagle-view']['name'] );
   
		}else{
		   
		    $headers = "MIME-Version: 1.0\r\n"; // Defining the MIME version
    	$headers .= 'From: The Birtile<'.$from.'>' . "\r\n"; 
  	$headers .= "Content-type:text/html" . "\r\n"; 
   
$body='quotation.';
		}
$content = '
<html>
<head>
<style>

    .quote-container {
        width: 100%;
        margin: 0;
        font-family: "Open Sans", sans-serif;
        text-align : center;
    }

 
    .quote-container > h1 {
        background: #890a00;
        width: 100%;
        padding: 15px 0px;
        color: #fff;
        text-align: center;
        box-sizing: border-box;
    }

    .details-container {
        width: 100%;
    }

    .details-box {
        width: 49%;
        display : inline-block;
        background: #ebebeb;
        margin : 1% 0px;
        box-sizing: border-box;
        padding : 20px 0px;
        text-align : center;
    }

    .details-box:last-child {
        margin-left: 1%;
    }
    
    .details-box-1 {
         width: 100%;
        display : block;
        background: #ebebeb;
        margin-bottom: 10px;
        box-sizing: border-box;
        text-align : center;
    }
    
    .details-box-1 h2 {
        margin : 0;
        padding : 0;
    }

    .details-box h2 {
        font-size: 17px;
        margin: 0;
        padding: 0;
        line-height: 100%;
        margin-bottom: 5px;
    }

    .details-box p {
        margin: 0;
        font-size: 12px;
    }

    .w-100 {
        width: 100%;
    }

    .images-container {
        width: 100%;
        margin-top: 25px;
    }

    .image-contain {
       display : inline-block;
        width: calc(25% - 8px);
        margin-right: 8px;
        height: auto;
    }

    .image-contain:last-child {
        margin-right: 0;
    }

    .image-contain img {
        width: 120px;
    }

    .image-contain h5 {
        font-size: 18px;
        margin: 5px 0;
        text-align: center;
    }

    .red-strip {
        background: #890a00;
        width: 100%;
        padding: 10px;
        margin-top: 20px;
        clear: both;
    }
    
    .Aerial_view {
        width : 100%;
        max-height : 400px;
    }
    
</style>
</head>
<body>
    <div class="quote-container">
        <h1>New Quote from '.$reciept['rname']. "  ". $reciept["profile"]. '</h1>
        <div class="details-container">
            <div class="details-box">
                <h2>'.$reciept['rname'].'</h2>
                <p>Name</p>
            </div>
            <div class="details-box">
                <h2>'.$reciept['remail'].'</h2>
                <p>Email</p>
            </div>
        </div>
        <div class="details-container">
            <div class="details-box">
                <h2>'.$reciept['rzip'].'</h2>
                <p>Zip code</p>
            </div>
            <div class="details-box">
                <h2>'.$reciept['rphone'].'</h2>
                <p>Phone</p>
            </div>
        </div>
        <div class="details-container">
            <div class="details-box-1">
                <h2>Additional Comments:</h2>
                <p>'.$reciept['additional-comments'].'</p>
            </div>
        </div>';
if($attach){
  
        $content.='<div class="details-container">
            <div class="details-box-full">
                <h5>\'Eagle View\' Aerial View</h5>
                <img src="data:image/jpeg;base64,' .base64_encode(file_get_contents($attach)) .'" class="w-100 Aerial_view" alt="america" />
            </div>
        </div>';
        
      
}
        $content.= '<div class="images-container">';

// Add dynamic images and style names
foreach ($styles as $style_data) {
    $name = $style_data['name'];
    $url =  "https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/".$style_data['url'];
    $image_data = file_get_contents($url);
    $base64_image = base64_encode($image_data);
    
    $content .= '
        <div class="image-contain">
            <img src="data:image/jpeg;base64,'.$base64_image.'" class="thumb-img" alt="'.$name.'" />
     
            <h5>'.$name.'</h5>
        </div>';
 
echo '<img src="data:image/jpeg;base64,' . $base64_image . '" class="thumb-img" alt="' . $name . '" />';    
}

// Continue the rest of the content
$content .= '
        </div>
        <div class="red-strip"></div>
    </div>
</body>
</html>
';
 // die;

	$dompdf = new Dompdf();
		$dompdf->loadHtml($content); 
    
        $dompdf->render();
    $output = $dompdf->output();
    file_put_contents("quote/quote_".str_replace(' ','_',$reciept["rname"]).'_'.time().'.pdf', $output);
       
		 //sleep(2);
	
		     $attachments = array($_SERVER['DOCUMENT_ROOT']."/quote/quote_".str_replace(' ','_',$reciept["rname"]).'_'.time().".pdf");
		 
		
		
        $raw_json = $reciept["style_0"];
        $cleaned_json = stripslashes($raw_json);
        $styles_test = json_decode($cleaned_json, true);
        
    
		  if(wp_mail($to, $subject,'Quotation',$headers,$attachments)){
			
			echo "mail sent.";
		}else{
		
			echo "mail not sent.";
		}  

// 	echo get_footer();
    ?>