<?php 
include('../../../wp-load.php');
global $wpdb;
$uploadDir = 'uploads/'; 
$response = array( 
    'status' => 0, 
    'message' => 'Form submission failed, please try again.' 
); 
 
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
    $update_status = $_POST['update_status'];	
        // Validate email 
        if(filter_var($emailaddress, FILTER_VALIDATE_EMAIL) === false){ 
            $response['message'] = 'Please enter a valid email.'; 
        }else{ 
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
            } else {
				$uploadedFile = $_POST['hiddencompanylogo'];
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
            } else {
				$uploadedFile2 = $_POST['hiddensampleimage1'];
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
            } else {
				$uploadedFile3 = $_POST['hiddensampleimage2'];
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
            } else {
				$uploadedFile4 = $_POST['hiddensampleimage3'];
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
					'status'=>$update_status
				);
                $insert=$wpdb->update('wp_contractor',$arg,array('id'=>$_POST['userid']) ); 
                if($insert){ 
                    $response['status'] = 1; 
                    $response['message'] = 'Form data update successfully!'; 
                } 
	}
    
} 
 
// Return response 
echo json_encode($response);