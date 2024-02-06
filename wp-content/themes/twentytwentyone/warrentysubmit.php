<?php 
include('../../../wp-load.php');
//require_once 'dompdf/autoload.inc.php';
require $_SERVER['DOCUMENT_ROOT'].'/dompdf/vendor/autoload.php';
$basepath=$_SERVER['DOCUMENT_ROOT'];
use Dompdf\Dompdf;

class Pdf extends Dompdf{

 public function __construct(){
  parent::__construct();
 }
}
$html_code ='';
session_start();
global $wpdb;
$uploadDir = 'uploads/'; 
$uploadDir2 = $basepath.'/warrenty/'; 
$f_name='Bartile_Warranty_'.time().'.pdf';
$file_name = $uploadDir.$f_name;
$file_name2 = $uploadDir2.$f_name;
$response = array( 
    'status' => 0, 
    'message' => 'Form submission failed, please try again.' 
); 
if($_POST['mailingaddressifdifferent'] == 1){ $val='TRUE'; } else{ $val='FALSE'; }

	if($val == 'TRUE'){
		$mailingaddres=$_POST['mailingaddress'];
	}else{
		$mailingaddres=$_POST['addressofproject'];
}

$address=explode(', ',$mailingaddres);
$l=sizeof($address);
/* $datetxt=explode('/',$_POST['dateofpuchase']);
$newtext=$datetxt[2].'-'.$datetxt[0].'-'.$datetxt[1];
$date=date_create($newtext); */
$date=$_POST['dateofpuchase'];
if(isset($_POST['emailaddress'])){ 
   $html_code.='<html><head><link type="text/css" href="'.$basepath.'/wp-content/themes/twentytwentyone/pdfstyle.css" rel="stylesheet" /> </head>
<body style="font-family: Arial, Helvetica, sans-serif;font-size:20px; margin:0;padding:0">
 <img src="topimg.png" alt="" width="100%;height:370px;"/>
      <table border="0" cellspacing="0" style="width:100%; background: #c9c5b5; margin:0px auto;border: none;" class="maintable">
	    
         <tbody>
            <tr style="padding:0">
               <td style="margin-bottom:50px;">
                  <div style="text-align: center;">
                     <p>This certifies that the home located at </p>
                  </div>
                  <div style="text-align: center; font-size: 30px; font-style: italic; color:#5c1611">
                     <p>'.$_POST['addressofproject'].'</p>
                  </div>
                  <div style="text-align: center;">
                     <p>has a Bartile Roof that was installed on</p>
                  </div>
                  <div style="text-align: center; font-size: 30px; font-style: italic; color:#5c1611">
                     <p>'.date("F d, Y",strtotime($date)).'</p>
                  </div>
                  <div style="text-align: center;">
                     <p style="margin-bottom:0">Bartile Premium Roof Specifications: <span style=" font-style: italic; color:#5c1611">'.$address[0].'</span></p>
                  </div>
                  <div style="text-align: center;margin-bottom:20px">';
				     $k=0;
				     for($i=1;$i<$l;$i++){
                        $html_code.='<p style="margin-left:205px; font-style: italic; color:#5c1611; margin-top:0;margin-bottom:0">'.$address[$i].'</p>';
						$k=$k+1;
					 }
					if($k < 2){
						$html_code.='<p style="margin-left:205px; font-style: italic; color:#5c1611; margin-top:0;margin-bottom:0">&nbsp;</p>';
					}
                   $html_code.='</div>
               </td>
            </tr>
            <tr>
               <td style="padding-top:0px;margin-top:50px;">
                  <p style="border-bottom:25px solid #5c1611;padding:0; margin-bottom:0"></p>
               </td>
            </tr>
         </tbody>
      </table>
	  <div class="page_break"></div>
	  <table border="0" cellspacing="0" style="width:800px; margin:20px auto;border: none;">
         <tbody>
            <tr>
               <td style="text-align:center; font-size:27px;">
                  <h1 style="text-align:center; font-size:25px;">LIMITED BARTILE CONCRETE TILE PRODUCT WARRANTY</h1>
               </td>
            </tr>
            <tr>
               <td style="font-size:17px;font-family: Arial, Helvetica, sans-serif;font-weight:500;">
                  <p>Thank    you for selecting   Bartile for your    new roof.   We  believe that    your    Bartile Roof    will    give    quality long    term    service for your    home    or  building.</p>
                  <p>
                     We  at  Bartile Roofs   guarantee   the Bartile Concrete    Tile    purchased   for seventy five    years   against faulty  material    defects.    This    warranty    Specifically    
                     defines “defects”   as  Integrity   Compromising    Disintegration and  Decomposition,  but expressly   excludes    Breakage    of  any type,   Misuse  of  Tile,   Acts    
                     of  Nature, Color   Variance,   Shading and Normal  Weathering
                  </p>
                  <p>
                     This    warranty    is  expressly   limited to  the replacement of  tile.   It  does    not cover   Incidental  or  consequential   damage, installation    labor   or  freight costs.
                  </p>
                  <p>
                     Except  as  expressly   set forth   herein, Bartile disclaims   and makes   no  other   express,    implied or  statutory   warranty    with    respect to  the Bartile 
                     Roofing Tile.   Some    states  prohibit    the disclaimer  of  Implied warranties, so  the foregoing   disclaimer  may not apply   to  you.
                  </p>
                  <p>
                     To  be  valid   this    warranty    must    be  registered  within  60  days    following   the Installation    date.   This    warranty    is  transferrable   if  notice  is  given   to  
                     Bartile at  the address below   within  60  days    of  property    transfer. This  warranty    is  not valid   until   signed  by  a   Bartile Executive.
                  </p>
               </td>
            </tr>
         </tbody>
      </table>
      <table border="0" cellspacing="0" style="width:800px; margin:20px auto;border: none;">
         <tbody>
            <tr>
               <td class="input-field" style="font-size:17px;font-family: Arial, Helvetica, sans-serif;font-weight:500;">
                  Authorized  Signature 
                  <input type="text" />
               </td>
               <td class="input-field" style="font-size:17px;font-family: Arial, Helvetica, sans-serif;font-weight:500;">
                  Data    Registered
                  <input type="text" />
               </td>
            </tr>
         </tbody>
      </table>
      <table border="0" cellspacing="0" style="width:800px; margin:20px auto;border: none;">
         <tfoot>
            <tr>
               <td class="address-txt" style="font-size:17px;font-family: Arial, Helvetica, sans-serif;font-weight:500;">
                  <p>Corporate   Office  725 North   1000    West,   Centerville,    Utah    840148</p>
                  <p>Phone:   801-295-3443</p>
                  <p>Toll Free:   800-933-5038</p>
                  <p>Fax: 801-295-3485</p>
               </td>
            </tr>
         </tfoot>
      </table>
   </body></html>';
    $pdf = new Pdf();
	// $pdf->setOptions('defaultFont', 'lobster');
	 $pdf->load_html($html_code);
	  $pdf->setPaper('A4', 'Landscape');
	 $pdf->render();
	 $file = $pdf->output();
	 file_put_contents($file_name, $file);
	 file_put_contents($file_name2, $file);
	
    $to = $_POST['emailaddress'];
	$subject = 'Warranty of Bartile Premium Roofing Tiles';
	$from = get_field('admin_email','options');
    
	// To send HTML mail, the Content-type header must be set
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	 
	// Create email headers
	$headers .= 'From: '.$from."\r\n".
		'Reply-To: '.$from."\r\n" .
		'X-Mailer: PHP/' . phpversion();
	 
	// Compose a simple HTML email message
	$message = '<html><body>';
    $message .= '<p><strong>Type of Tile</strong>: '.$_POST['typeoftitle'].'</p><p><strong>Date of Purchase</strong>: '.$_POST['dateofpuchase'].'</p><p><strong>Email Address</strong>: '.$_POST['emailaddress'].'</p><p><strong>Address of Project</strong>: '.$_POST['addressofproject'].'</p><p><strong>Mailing address different?</strong>: '.$val.'</p><p><strong>Mailing address</strong>: '.$mailingaddres.'</p><p>Please download your warranty file</p>'; 
	$message .= '</body></html>';
	//echo $message;
	$attachments = array($file_name);
	if(wp_mail($to, $subject, $message, $headers,$attachments)){
	    $perm=array("name"=>$_POST['emailaddress'],"url"=>$f_name,"id"=>rand(1000, 9999));
	    webhook($perm);
     $response['status'] = 1; 
     $response['message'] = 'Form data submitted successfully!'; 
		$arg=array(
				    'typeoftitle'=>$_POST['typeoftitle'],
					'dateofpuchase'=>$_POST['dateofpuchase'],
					'emailaddress'=>$_POST['emailaddress'],
					'addressofproject'=>$_POST['addressofproject'],
					'mailingaddressifdifferent'=>$val,
					'mailingaddress'=>$mailingaddres
				);
          $insert=$wpdb->insert('wp_warranty',$arg ); 
	} else{
		$response['message'] = 'Unable to send email. Please try again.';
	}

}
echo json_encode($response);

function webhook($perm=array()){
   $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://hook.eu1.make.com/g1abcnrq59tj89604yhvokin09oobsx7',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_POSTFIELDS =>json_encode($perm),
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
//echo $response; 
}