<?php 
include('../../../wp-load.php');
session_start();
global $wpdb;
$uploadDir = 'uploads/'; 
$response = array( 
    'status' => 0, 
    'message' => 'Form submission failed, please try again.' 
); 
$number=0;
$data = $wpdb->get_results("SELECT * FROM wp_quizanswer" );
$data=$data[0];

// If form is submitted 
if(isset($_POST['submitedate'])){ 
    $submitedate = $_POST['submitedate']; 
    $companyname = $_POST['companyname']; 
    $pcontactnumber = $_POST['pcontactnumber']; 
    $contractorstatelno = $_POST['contractorstatelno']; 
    $address1 = $_POST['address1']; 
    $address2 = $_POST['address2']; 
    $city = $_POST['city']; 
    $state = $_POST['state']; 
    $zipcode = $_POST['zipcode']; 
    $phone = $_POST['phone']; 
    $emailaddress = $_POST['emailaddress']; 
    $website = $_POST['website']; 
    $howmanyemp = $_POST['howmanyemp']; 
    $estimateanroof = $_POST['estimateanroof']; 
    $servicveprovider = implode(', ',$_POST['servicveprovider']); 
    $yesrbusiness = $_POST['yesrbusiness']; 	
	$roofcomepleteperyear = $_POST['roofcomepleteperyear']; 
    $commercialroofcomplete = $_POST['commercialroofcomplete']; 
    $agreeourcode = $_POST['agreeourcode']; 
    $companylogo = $_FILES["companylogo"]["name"]; 
    $sampleimage1 = $_FILES["sampleimage1"]["name"];
    $sampleimage2 = $_FILES["sampleimage2"]["name"];
    $sampleimage3 = $_FILES["sampleimage3"]["name"];
    $whatdifferentiates = $_POST['whatdifferentiates']; 
    $testimonials = $_POST['testimonials']; 
    $accountpasord = '1234567899';  
        // Validate email 
        if(filter_var($emailaddress, FILTER_VALIDATE_EMAIL) === false){ 
            $response['message'] = 'Please enter a valid email.'; 
        }else{ 
		    $checking=$wpdb->get_var('SELECT COUNT(*) FROM wp_contractor WHERE emailaddress="'.$emailaddress.'"');
			if($checking == 0 ) {
            $uploadStatus = 1; 
            $uploadStatus2 = 1; 
			$uploadStatus3 = 1; 
			$uploadStatus4 = 1; 
            // Upload file 
            $uploadedFile = ''; 
            if(!empty($_FILES["companylogo"]["name"])){ 
                 
                // File path config 
                $fileName = 'c'.date('mdyhis').basename($_FILES["companylogo"]["name"]); 
                $targetFilePath = $uploadDir . $fileName; 
                $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION); 
                 
                // Allow certain file formats 
                $allowTypes = array( 'jpg', 'png', 'jpeg', 'gif'); 
                if(in_array($fileType, $allowTypes)){ 
                    // Upload file to the server 
                    if(move_uploaded_file($_FILES["companylogo"]["tmp_name"], $targetFilePath)){ 
                        $uploadedFile = $fileName; 
                    }else{ 
                        $uploadStatus = 0; 
                        $response['message'] = 'Sorry, there was an error uploading your file.'; 
                    } 
                }else{ 
                    $uploadStatus = 0; 
                    $response['message'] = 'Sorry, only JPG, JPEG, GIF & PNG files are allowed to upload.'; 
                } 
            } 
			$uploadedFile2 = ''; 
            if(!empty($_FILES["sampleimage1"]["name"])){ 
                 
                // File path config 
                $fileName ='sample1'.date('mdyhis').basename($_FILES["sampleimage1"]["name"]); 
                $targetFilePath = $uploadDir . $fileName; 
                $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION); 
                 
                // Allow certain file formats 
                $allowTypes = array( 'jpg', 'png', 'jpeg', 'gif'); 
                if(in_array($fileType, $allowTypes)){ 
                    // Upload file to the server 
                    if(move_uploaded_file($_FILES["sampleimage1"]["tmp_name"], $targetFilePath)){ 
                        $uploadedFile2 = $fileName; 
                    }else{ 
                        $uploadStatus2 = 0; 
                        $response['message'] = 'Sorry, there was an error uploading your file.'; 
                    } 
                }else{ 
                    $uploadStatus2 = 0; 
                    $response['message'] = 'Sorry, only JPG, JPEG, GIF & PNG files are allowed to upload.'; 
                } 
            } 
			$uploadedFile3 = ''; 
            if(!empty($_FILES["sampleimage2"]["name"])){ 
                 
                // File path config 
                $fileName = 'sample2'.date('mdyhis').basename($_FILES["sampleimage2"]["name"]); 
                $targetFilePath = $uploadDir . $fileName; 
                $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION); 
                 
                // Allow certain file formats 
                $allowTypes = array( 'jpg', 'png', 'jpeg', 'gif'); 
                if(in_array($fileType, $allowTypes)){ 
                    // Upload file to the server 
                    if(move_uploaded_file($_FILES["sampleimage2"]["tmp_name"], $targetFilePath)){ 
                        $uploadedFile3 = $fileName; 
                    }else{ 
                        $uploadStatus3 = 0; 
                        $response['message'] = 'Sorry, there was an error uploading your file.'; 
                    } 
                }else{ 
                    $uploadStatus3 = 0; 
                    $response['message'] = 'Sorry, only JPG, JPEG, GIF & PNG files are allowed to upload.'; 
                } 
            } 
			$uploadedFile4 = ''; 
            if(!empty($_FILES["sampleimage3"]["name"])){ 
                 
                // File path config 
                $fileName = 'sample3'.date('mdyhis').basename($_FILES["sampleimage3"]["name"]); 
                $targetFilePath = $uploadDir . $fileName; 
                $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION); 
                 
                // Allow certain file formats 
                $allowTypes = array( 'jpg', 'png', 'jpeg', 'gif'); 
                if(in_array($fileType, $allowTypes)){ 
                    // Upload file to the server 
                    if(move_uploaded_file($_FILES["sampleimage3"]["tmp_name"], $targetFilePath)){ 
                        $uploadedFile4 = $fileName; 
                    }else{ 
                        $uploadStatus4 = 0; 
                        $response['message'] = 'Sorry, there was an error uploading your file.'; 
                    } 
                }else{ 
                    $uploadStatus4 = 0; 
                    $response['message'] = 'Sorry, only JPG, JPEG, GIF & PNG files are allowed to upload.'; 
                } 
            } 
                $arg=array(
				    'submitedate'=>$submitedate,
					'companyname'=>$companyname,
					'pcontactnumber'=>$pcontactnumber,
					'contractorstatelno'=>$contractorstatelno,
					'address1'=>$address1,
					'address2'=>$address2,
					'city'=>$city,
					'state'=>$state,
					'zipcode'=>$zipcode,
					'phone'=>$phone,
					'emailaddress'=>$emailaddress,
					'website'=>$website,
					'howmanyemp'=>$howmanyemp,
					'estimateanroof'=>$estimateanroof,
					'servicveprovider'=>$servicveprovider,
					'yesrbusiness'=>$yesrbusiness,
					'roofcomepleteperyear'=>$roofcomepleteperyear,
					'commercialroofcomplete'=>$commercialroofcomplete,
					'agreeourcode'=>$agreeourcode,
					'companylogo'=>$uploadedFile,
					'sampleimage1'=>$uploadedFile2,
					'sampleimage2'=>$uploadedFile3,
					'sampleimage3'=>$uploadedFile4,
					'whatdifferentiates'=>$whatdifferentiates,
					'testimonials'=>$testimonials,
					'acpassword'=>$accountpasord
				);
                $insert=$wpdb->insert('wp_contractor',$arg ); 
                if($insert){ 
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
					$to = $emailaddress;
					$subject = 'Quiz Bartile Premium Roofing Tiles';
					//$from = 'kcheshier@seonow.io';
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
										'email'=>$emailaddress,
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
					}
                    $response['status'] = 1; 
                    $response['message'] = 'Form data submitted successfully!'; 
					//$_SESSION["clientmailaddress"] = $emailaddress;
                } 
		}else{
			$response['message'] = 'Email address already used. Please try with another email address!'; 
		}
	}
    
} 
 
// Return response 
echo json_encode($response);