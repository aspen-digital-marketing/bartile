<?php 
include('../../../wp-load.php');
session_start();
global $wpdb;
$uploadDir = 'uploads/'; 
$response = array( 
    'status' => 0, 
    'message' => 'Quiz submission failed, please try again.' 
); 
$number=0;
$data = $wpdb->get_results("SELECT * FROM wp_quizanswer" );

$data=$data[0];
if(isset($_POST['clientmail'])){ 
    if($_POST['ques1']==1){ $ques1='TRUE'; } else { $ques1='FALSE'; }
	if($_POST['ques2']==1){ $ques2='TRUE'; } else { $ques2='FALSE'; }
	if($_POST['ques3']==1){ $ques3='TRUE'; } else { $ques3='FALSE'; }
	if($_POST['ques4']==1){ $ques4='TRUE'; } else { $ques4='FALSE'; }
	if($_POST['ques5']==1){ $ques5='TRUE'; } else { $ques5='FALSE'; }
	if($_POST['ques6']==1){ $ques6='TRUE'; } else { $ques6='FALSE'; }
	if($_POST['ques7']==1){ $ques7='TRUE'; } else { $ques7='FALSE'; }
	if($_POST['ques8']==1){ $ques8='TRUE'; } else { $ques8='FALSE'; }
	if($_POST['ques9']==1){ $ques9='TRUE'; } else { $ques9='FALSE'; }
	if($_POST['ques10']==1){ $ques10='TRUE'; } else { $ques10='FALSE'; }
	if($_POST['ques11']==1){ $ques11='TRUE'; } else { $ques11='FALSE'; }
	if($_POST['ques12']==1){ $ques12='TRUE'; } else { $ques12='FALSE'; }
	if($_POST['ques13']==1){ $ques13='TRUE'; } else { $ques13='FALSE'; }
	if($_POST['ques14']==1){ $ques14='TRUE'; } else { $ques14='FALSE'; }
	if($_POST['ques15']==1){ $ques15='TRUE'; } else { $ques15='FALSE'; }
	if($_POST['ques16']==1){ $ques16='TRUE'; } else { $ques16='FALSE'; }
	if($_POST['ques17']==1){ $ques17='TRUE'; } else { $ques17='FALSE'; }
	if($_POST['ques18']==1){ $ques18='TRUE'; } else { $ques18='FALSE'; }
	if($_POST['ques19']==1){ $ques19='TRUE'; } else { $ques19='FALSE'; }
	
	for($count=1;$count<=19;$count++){
		$questionnumber='ques'.$count;
		$answernumber='ques'.$count;
		if($_POST[$questionnumber] == $data->$answernumber){
		  $number=$number+1;	
		}
	}
    $to = $_POST['clientmail'];
	$subject = 'Quiz Bartile Premium Roofing Tiles';
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
	$message .= '<table><tbody><tr><td>1. The term "Cottage" refers to the staggering of the tile.</td><td>'.$ques1.'</td></tr><tr><td>2. Legendary tile is designed to be laid without battens on pitches 12/12 or less.</td><td>'.$ques2.'</td></tr><tr><td>3. Rake metal, Solid Gable tile, LUR Rake and USR Rake are all types of gable finishes.</td><td>'.$ques3.'</td></tr><tr><td>4. There are 4 different widths of tile on a Yorkshire Cottage installation.</td><td>'.$ques4.'</td></tr><tr><td>5. Bartile offers tile in Standard Weight, Super Duty and Ultralite weights.</td><td>'.$ques5.'</td></tr><tr><td>6. Bartile provides a 75 Year Warranty.</td><td>'.$ques6.'</td></tr><tr><td>7. Bartile does not make custom orders and colors.</td><td>'.$ques7.'</td></tr><tr><td>8. Bartile makes an interlocking turret tile..</td><td>'.$ques8.'</td></tr><tr><td>9. Old World Vintage is 100 year old salvaged tile.</td><td>'.$ques9.'</td></tr><tr><td>10. Counter Batten and Ventilated Batten systems greatly prolong the life of a Bartile Roof.</td><td>'.$ques10.'</td></tr><tr><td>11. Pipe penetrations on a Bartile roof require only one pipe flashing.</td><td>'.$ques11.'</td></tr><tr><td>12. The USR round trim tile can be used on the hip, ridge and rake.</td><td>'.$ques12.'</td></tr><tr><td>13. Bartile makes a tile with simulated moss and lichen growth on it.</td><td>'.$ques13.'</td></tr><tr><td>14. Bartile makes a pan and cap turret tile for the Sierra Mission tile.</td><td>'.$ques14.'</td></tr><tr><td>15. Using 100% ice and water shield under a Bartile Roof will void its warranty.</td><td>'.$ques15.'</td></tr><tr><td>16. Bartile can fabricate any of the discontinued tiles they have made since 1942.</td><td>'.$ques16.'</td></tr><tr><td>17. Steep Ridges can be used on pitches 12/12 or higher.</td><td>'.$ques17.'</td></tr><tr><td>18. Tile Risers and cover metal are types of eave edge details.</td><td>'.$ques18.'</td></tr><tr><td>19. Bartile is a family owned company started in 1942.</td><td>'.$ques19.'</td></tr></tbody></table>';
	$message .= '</body></html>';
	//echo $message;
	if(wp_mail($to, $subject, $message, $headers)){
     $response['status'] = 1; 
     $response['message'] = 'Quiz data submitted successfully!'; 
		
		/* Email Send to Admin*/
		$to1 = get_field('admin_email','options');
		$from1 = $_POST['clientmail'];
		$headers1  = 'MIME-Version: 1.0' . "\r\n";
		$headers1 .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers1 .= 'From: '.$from1."\r\n".
			'Reply-To: '.$from1."\r\n" .
			'X-Mailer: PHP/' . phpversion();
		
		wp_mail($to1, $subject, $message, $headers1);
		$arg=array(
				    'email'=>$_POST['clientmail'],
					'ques1'=>$_POST['ques1'],
					'ques2'=>$_POST['ques2'],
					'ques3'=>$_POST['ques3'],
					'ques4'=>$_POST['ques4'],
					'ques5'=>$_POST['ques5'],
					'ques6'=>$_POST['ques6'],
					'ques7'=>$_POST['ques7'],
					'ques8'=>$_POST['ques8'],
					'ques9'=>$_POST['ques9'],
					'ques10'=>$_POST['ques10'],
					'ques11'=>$_POST['ques11'],
					'ques12'=>$_POST['ques12'],
					'ques13'=>$_POST['ques13'],
					'ques14'=>$_POST['ques14'],
					'ques15'=>$_POST['ques15'],
					'ques16'=>$_POST['ques16'],
					'ques17'=>$_POST['ques17'],
					'ques18'=>$_POST['ques18'],
					'ques19'=>$_POST['ques19'],
					'result'=>$number
				);
          $insert=$wpdb->insert('wp_quiz',$arg ); 
		
	 session_destroy();
	} else{
		$response['message'] = 'Unable to send email. Please try again.';
	}

}
echo json_encode($response);