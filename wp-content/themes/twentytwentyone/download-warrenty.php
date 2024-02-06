<?php 
error_reporting(1);
include('../../../wp-load.php');
global $wpdb;
$id=$_GET['id'];
$data = $wpdb->get_results("SELECT * FROM wp_warranty WHERE id='".$id."'" );
$data=$data[0];
$typeoftitle = $data->typeoftitle;
$address=explode(', ',$data->mailingaddress);
$l=sizeof($address);
/* $datetxt=explode('/',$data->dateofpuchase);
$newtext=$datetxt[2].'-'.$datetxt[0].'-'.$datetxt[1];
$date=date_create($newtext); */
$date=$data->dateofpuchase;

require $_SERVER['DOCUMENT_ROOT'].'/dompdf/vendor/autoload.php';

$basepath=$_SERVER['DOCUMENT_ROOT'];



use Dompdf\Dompdf;
$file_name = 'Bartile_Warranty.pdf';
class Pdf extends Dompdf{

 public function __construct(){
  parent::__construct();
 }
}


$html_code ='';
 $html_code.='<html><head><link type="text/css" href="'.$basepath.'/wp-content/themes/Bartile/pdfstyle.css" rel="stylesheet" /> </head>
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
                     <p>'.$data->addressofproject.'</p>
                  </div>
                  <div style="text-align: center;">
                     <p>has a Bartile Roof that was installed on</p>
                  </div>
                  <div style="text-align: center; font-size: 30px; font-style: italic; color:#5c1611">
                     <p>'.date("F d, Y",strtotime($data->dateofpuchase)).'</p>
                  </div>
                  <div style="text-align: center;">
                     <p style="margin-bottom:0">Bartile Premium Roof Specifications: <span style=" font-style: italic; color:#5c1611">'.$typeoftitle.'</span></p>
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
	 $pdf->set_base_path($basepath.'/wp-content/themes/Bartile/pdfstyle.css');
	 $pdf->stream($file_name);