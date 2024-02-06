<?php
     // Gather form data
     $formTitle = $_GET["form_title"];
     $distributor = $_GET["distributor"];
     $distributorDate = $_GET["distributor_date"];
     $contractor = $_GET["Contractor"];
     $color = $_GET["color"];
     $customer = $_GET["Customer"];

     echo $formTitle;

     $email_curl = curl_init();
     $token = 'patDiUeGElMBmWbRb.42f3726d5ef4dc17c1c2c3fd544667ae7cf8c118bd5561dde28198ee615db1a6';
      
       $emailHeaders = [
             'Content-Type: application/json',
             'Authorization: Bearer ' . $token,
         ];

         $data = [
            'form_title' => $formTitle,
            'distributor' => $distributor,
            'distributor_date' => $distributorDate,
            'Contractor' => $contractor,
            'color' => $color,
            'Customer' => $customer
        ];

        $data1 = [
            'fields' => $data,
        ];

        curl_setopt_array($email_curl, [
            CURLOPT_URL => 'https://api.airtable.com/v0/appzVDDeIe43rL1XL/bartile_form_submit',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data1),
            CURLOPT_HTTPHEADER => $emailHeaders,
        ]);
        
        $userDownloadResponse = curl_exec($email_curl);

        print_r($userDownloadResponse);



?>
