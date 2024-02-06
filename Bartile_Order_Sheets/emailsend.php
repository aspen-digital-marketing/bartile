<?php

require_once 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$formTitle = isset($_GET["form_title"]) ? $_GET["form_title"] : "";
$distributor = isset($_GET["distributor"]) ? $_GET["distributor"] : "";
$distributorDate = isset($_GET["distributor_date"]) ? $_GET["distributor_date"] : "";
$contractor = isset($_GET["Contractor"]) ? $_GET["Contractor"] : "";
$color = isset($_GET["color"]) ? $_GET["color"] : "";
$customer = isset($_GET["Customer"]) ? $_GET["Customer"] : "";
$stdwt = isset($_GET["std-wt"]) ? ($_GET["std-wt"] == "on" ? "Yes" : "No") : "";

$Ultralite = isset($_GET["Ultralite"]) ? ($_GET["Ultralite"]  == "on" ? "Yes" : "No") : "";
$Superduty = isset($_GET["Superduty"]) ? ($_GET["Superduty"] == "on" ? "Yes" : "No") : "";
$standardcut = isset($_GET["standard_cut"]) ? ($_GET["standard_cut"] == "on" ? "Yes" : "No") : "";
$standardcutqty = isset($_GET["standard_cut_qty"]) ? $_GET["standard_cut_qty"] : "";
$Ruffcut = isset($_GET["Ruff_cut"]) ? ($_GET["Ruff_cut"]  == "on" ? "Yes" : "No") : "";
$Ruffcutqty = isset($_GET["Ruff_cut_qty"]) ? $_GET["Ruff_cut_qty"] : "";
$Cottage = isset($_GET["Cottage"]) ? ($_GET["Cottage"] == "on" ? "Yes" : "No") : "";
$Cottageqty = isset($_GET["Cottage_qty"]) ? $_GET["Cottage_qty"] : "";
$Manchester = isset($_GET["Manchester"]) ? ($_GET["Manchester"] == "on" ? "Yes" : "No") : "";
$Manchesterqty = isset($_GET["Manchester_qty"]) ? $_GET["Manchester_qty"] : "";
$Newcastle = isset($_GET["Newcastle"]) ? ($_GET["Newcastle"] == "on" ? "Yes" : "No") : "";
$Newcastleqty = isset($_GET["Newcastle_qty"]) ? $_GET["Newcastle_qty"] : "";
$RandomSwirlBrush = isset($_GET["Random-Swirl-Brush"]) ? ($_GET["Random-Swirl-Brush"]  == "on" ? "Yes" : "No") : "";
$RandomSwirlBrushqty = isset($_GET["Random-Swirl-Brush_qty"]) ? $_GET["Random-Swirl-Brush_qty"] : "";
$VintageRuffMoss = isset($_GET["Vintage_Ruff_Moss"]) ? ($_GET["Vintage_Ruff_Moss"]  == "on" ? "Yes" : "No") : "";
$VintageRuffMossqty = isset($_GET["Vintage_Ruff_Moss_qty"]) ? $_GET["Vintage_Ruff_Moss_qty"] : "";
$VStyleHipRidge = isset($_GET["V-Style-Hip-Ridge"]) ? ($_GET["V-Style-Hip-Ridge"]  == "on" ? "Yes" : "No") : "";
$VStyleHipRidgeqty = isset($_GET["V-Style-Hip-Ridge_qty"]) ? $_GET["V-Style-Hip-Ridge_qty"] : "";
$UniversalRake = isset($_GET["Universal-Rake"]) ? ($_GET["Universal-Rake"]  == "on" ? "Yes" : "No") : "";
$UniversalRake_qty = isset($_GET["Universal-Rake_qty"]) ? $_GET["Universal-Rake_qty"] : "";
$UserHipRidgeRake = isset($_GET["User-Hip-Ridge-Rake"]) ? ($_GET["User-Hip-Ridge-Rake"] == "on" ? "Yes" : "No") : "";
$UserHipRidgeRake_qty = isset($_GET["User-Hip-Ridge-Rake_qty"]) ? $_GET["User-Hip-Ridge-Rake_qty"] : "";
$SteepRidge = isset($_GET["Steep-Ridge"]) ? ($_GET["Steep-Ridge"] == "on" ? "Yes" : "No") : "";
$SteepRidge_qty = isset($_GET["Steep-Ridge_qty"]) ? $_GET["Steep-Ridge_qty"] : "";
$VStyleHipStarters = isset($_GET["V-Style-Hip-Starters"]) ? ($_GET["V-Style-Hip-Starters"]  == "on" ? "Yes" : "No") : "";
$VStyleHipStarters_qty = isset($_GET["V-Style-Hip-Starters_qty"]) ? $_GET["V-Style-Hip-Starters_qty"] : "";
$USRHipStarters = isset($_GET["USR-Hip-Starters"]) ? ($_GET["USR-Hip-Starters"]  == "on" ? "Yes" : "No") : "";
$USRHipStarters_qty = isset($_GET["USR-Hip-Starters_qty"]) ? $_GET["USR-Hip-Starters_qty"] : "";
$Yorkshire = isset($_GET["Yorkshire"]) ?( $_GET["Yorkshire"]  == "on" ? "Yes" : "No") : "";
$Yorkshire_qty = isset($_GET["Yorkshire_qty"]) ? $_GET["Yorkshire_qty"] : "";

// For Trim Section
$TILERISER = isset($_GET["TILE-RISER"]) ? ($_GET["TILE-RISER"]  == "on" ? "Yes" : "No") : "";
$TILERISER_qty = isset($_GET["TILE-RISER_qty"]) ? $_GET["TILE-RISER_qty"] : "";
$TrimRuffcut = isset($_GET["Trim-Ruff_cut"]) ? ($_GET["Trim-Ruff_cut"]  == "on" ? "Yes" : "No") : "";
$TrimRuffcutqty = isset($_GET["Trim-Ruff_cut_qty"]) ? $_GET["Trim-Ruff_cut_qty"] : "";
$SwirlBrush = isset($_GET["Trim-Swirl-Brush"]) ? ($_GET["Trim-Swirl-Brush"]  == "on" ? "Yes" : "No") : "";
$SwirlBrush_qty = isset($_GET["Trim-Swirl-Brush_qty"]) ? $_GET["Trim-Swirl-Brush_qty"] : "";
$Vintageruffmoss = isset($_GET["Trim-Vintage-Ruff-Moss"]) ? ($_GET["Trim-Vintage-Ruff-Moss"]  == "on" ? "Yes" : "No") : "";
$Vintageruffmoss_qty = isset($_GET["Trim-Vintage-Ruff-Moss_qty"]) ? $_GET["Trim-Vintage-Ruff-Moss_qty"] : "";
$VentillatedBatten = isset($_GET["Ventillated-Batten"]) ? ($_GET["Ventillated-Batten"]  == "on" ? "Yes" : "No") : "";
$VentillatedBatten_qty = isset($_GET["Ventillated-Batten_qty"]) ? $_GET["Ventillated-Batten_qty"] : "";
$Trim70SwirlBrushed = isset($_GET["Trim-70%-Swirl-Brushed"]) ?( $_GET["Trim-70%-Swirl-Brushed"]  == "on" ? "Yes" : "No") : "";
$Trim70SwirlBrushed_qty = isset($_GET["Trim-70%-Swirl-Brushed_qty"]) ? $_GET["Trim-70%-Swirl-Brushed_qty"] : "";
$T70VintageRuffMoss = isset($_GET["70%Vintage-Ruff-Moss"]) ? ($_GET["70%Vintage-Ruff-Moss"]  == "on" ? "Yes" : "No") : "";
$T70VintageRuffMoss_qty = isset($_GET["70%Vintage-Ruff-Moss_qty"]) ? $_GET["70%Vintage-Ruff-Moss_qty"] : "";

//table
 $RandomSwirlBrushed = isset($_GET["Random-Swirl-Brushed"]) ? ($_GET["Random-Swirl-Brushed"] == "on" ? "Yes" : "No") : "";
$RandomSwirlBrushed_qty = isset($_GET["Random-Swirl-Brushed_qty"]) ? $_GET["Random-Swirl-Brushed_qty"] : "";
$EuropeanEaveClosures = isset($_GET["European-Eave-Closures"]) ?( $_GET["European-Eave-Closures"] == "on" ? "Yes" : "No") : "";
$EuropeanEaveClosures_qty = isset($_GET["European-Eave-Closures_qty"]) ? $_GET["European-Eave-Closures_qty"] : "";
$HipRidgeRake = isset($_GET["Hip-Ridge-Rake"]) ? ($_GET["Hip-Ridge-Rake"]  == "on" ? "Yes" : "No") : "";
$HipRidgeRake_qty = isset($_GET["Hip-Ridge-Rake_qty"]) ? $_GET["Hip-Ridge-Rake_qty"] : "";
$HipStarters = isset($_GET["Hip-Starters"]) ? ($_GET["Hip-Starters"]  == "on" ? "Yes" : "No") : "";
$HipStarters_qty = isset($_GET["Hip-Starters_qty"]) ? $_GET["Hip-Starters_qty"] : "";
$TrimSwirlBrushed = isset($_GET["Trim-Swirl-Brushed"]) ? ($_GET["Trim-Swirl-Brushed"]  == "on" ? "Yes" : "No") : "";
$TrimSwirlBrushed_qty = isset($_GET["Trim-Swirl-Brushed_qty"]) ? $_GET["Trim-Swirl-Brushed_qty"] : "";
$TurretClosures = isset($_GET["Turret-Closures"]) ? ($_GET["Turret-Closures"]  == "on" ? "Yes" : "No") : "";
$TurretClosures_qty = isset($_GET["Turret-Closures_qty"]) ? $_GET["Turret-Closures_qty"] : "";
$NeedToSend = isset($_GET["Need-To-Send"]) ? ($_GET["Need-To-Send"]  == "on" ? "Yes" : "No") : "";
$NeedToSend_qty = isset($_GET["Need-To-Send_qty"]) ? $_GET["Need-To-Send_qty"] : "";
$COVERTRACK = isset($_GET["COVER-TRACK"]) ? ($_GET["COVER-TRACK"] == "on" ? "Yes" : "No") : "";
$COVERTRACK_qty = isset($_GET["COVER-TRACK_qty"]) ? $_GET["COVER-TRACK_qty"] : "";
$OldMission = isset($_GET["Old-Mission"]) ? ($_GET["Old-Mission"]  == "on" ? "Yes" : "No") : "";
$OldMission_qty = isset($_GET["Old-Mission_qty"]) ? $_GET["Old-Mission_qty"] : "";
$MissionEaveClosures = isset($_GET["Mission-Eave-Closures"]) ? ($_GET["Mission-Eave-Closures"]  == "on" ? "Yes" : "No") : "";
$MissionEaveClosures_qty = isset($_GET["Mission-Eave-Closures_qty"]) ? $_GET["Mission-Eave-Closures_qty"] : "";
$MissionHipRidgeRake = isset($_GET["Mission-Hip-Ridge-Rake"]) ? ($_GET["Mission-Hip-Ridge-Rake"] == "on" ? "Yes" : "No") : "";
$MissionHipRidgeRake_qty = isset($_GET["Mission-Hip-Ridge-Rake_qty"]) ? $_GET["Mission-Hip-Ridge-Rake_qty"] : "";
$MissionHipStarters = isset($_GET["Mission-Hip-Starters"]) ? ($_GET["Mission-Hip-Starters"]  == "on" ? "Yes" : "No") : "";
$MissionHipStarters_qty = isset($_GET["Mission-Hip-Starters_qty"]) ? $_GET["Mission-Hip-Starters_qty"] : "";


$Toscanacut= isset($_GET["Toscana_cut"]) ? ($_GET["Toscana_cut"]  == "on" ? "Yes" : "No") : "";
$Toscana_cut_qty= isset($_GET["Toscana_cut_qty"]) ? $_GET["Toscana_cut_qty"] : "";
$StraightBrushed= isset($_GET["Straight-Brushed"]) ? ($_GET["Straight-Brushed"]  == "on" ? "Yes" : "No") : "";
$StraightBrushed_qty= isset($_GET["Toscana_cut_qty"]) ? $_GET["Straight-Brushed_qty"] : "";
$T50StraightBrushed= isset($_GET["50%Straight-Brushed"]) ? ($_GET["50%Straight-Brushed"]  == "on" ? "Yes" : "No") : "";
$T50StraightBrushed_qty= isset($_GET["50%Straight-Brushed_qty"]) ? $_GET["50%Straight-Brushed_qty"] : "";
$SignaTrueSlate= isset($_GET["Signa-True-Slate"]) ? ($_GET["Signa-True-Slate"]  == "on" ? "Yes" : "No") : "";
$SignaTrueSlate_qty= isset($_GET["Signa-True-Slate_qty"]) ? $_GET["Signa-True-Slate_qty"] : "";
$LowPitch= isset($_GET["Low-Pitch"]) ? ($_GET["Low-Pitch"]  == "on" ? "Yes" : "No") : "";
$LowPitch_qty= isset($_GET["Low-Pitch_qty"]) ? $_GET["Low-Pitch_qty"] : "";
$RakeMetalOption= isset($_GET["Rake-Metal-Option"]) ? ($_GET["Rake-Metal-Option"]  == "on" ? "Yes" : "No") : "";
$RakeMetalOption_qty= isset($_GET["Rake-Metal-Option_qty"]) ? $_GET["Rake-Metal-Option_qty"] : "";
$SolidGableTiles= isset($_GET["Solid-Gable-Tiles"]) ?( $_GET["Solid-Gable-Tiles"]  == "on" ? "Yes" : "No") : "";
$SolidGableTiles_qty= isset($_GET["Solid-Gable-Tiles_qty"]) ? $_GET["Solid-Gable-Tiles_qty"] : "";
$NailsRingShanks= isset($_GET["Nails-RingShanks"]) ? ($_GET["Nails-RingShanks"]  == "on" ? "Yes" : "No") : "";
$NailsRingShanks_qty= isset($_GET["Nails-RingShanks_qty"]) ? $_GET["Nails-RingShanks_qty"] : "";


$data = [
            'Product_title' => $formTitle,
            'distributor' => $distributor,
            'distributor_date' => $distributorDate,
            'Contractor' => $contractor,
            'color' => $color,
            'Customer' => $customer,
           'std-wt' => $stdwt,
            'Ultralite' => $Ultralite,
            'Super_duty' => $Superduty,
            'standard_cut' => $standardcut,
          'standard_cut_qty' => $standardcutqty,
           'Ruff_cut' => $Ruffcut,
           'Ruff_cut_qty' => $Ruffcutqty,
            'Cottage' => $Cottage ,
           'Cottage_qty' => $Cottageqty,
            'Manchester' => $Manchester,
            'Manchester_qty' => $Manchesterqty,
           'Newcastle' => $Newcastle,
            'Newcastle_qty' => $Newcastleqty,
            'Random-Swirl-Brush' => $RandomSwirlBrush,
            'Random-Swirl-Brush_qty' => $RandomSwirlBrushqty,
            'Vintage_Ruff_Moss' => $VintageRuffMoss,
            'Vintage_Ruff_Moss_qty' => $VintageRuffMossqty,
            'V-Style-Hip-Ridge' => $VStyleHipRidge,
            'V-Style-Hip-Ridge_qty' => $VStyleHipRidgeqty,
            'Universal-Rake' => $UniversalRake,
            'Universal-Rake_qty' => $UniversalRake_qty,
            'User-Hip-Ridge-Rake' => $UserHipRidgeRake,
            'User-Hip-Ridge-Rake_qty' => $UserHipRidgeRake_qty,
            'Steep-Ridge' => $SteepRidge,
            'Steep-Ridge_qty' => $SteepRidge_qty,
            'V-Style-Hip-Starters' => $VStyleHipStarters,
            'V-Style-Hip-Starters_qty' => $VStyleHipStarters_qty,
            'USR-Hip-Starters' => $USRHipStarters,
            'USR-Hip-Starters_qty' => $USRHipStarters_qty,
            'Yorkshire' => $Yorkshire,
            'Yorkshire_qty' => $Yorkshire_qty,
            'TILE-RISER' => $TILERISER,
            'TILE-RISER_qty' => $TILERISER_qty,
            'Trim-Ruff_cut' => $TrimRuffcut,
            'Trim-Ruff_cut_qty' => $TrimRuffcutqty,
            'Trim-Swirl-Brush' => $SwirlBrush,
            'Trim-Swirl-Brush_qty' => $SwirlBrush_qty,
            'Trim-Vintage-Ruff-Moss' => $Vintageruffmoss,
            'Trim-Vintage-Ruff-Moss_qty' => $Vintageruffmoss_qty,
            'Ventillated-Batten' => $VentillatedBatten,
            'Ventillated-Batten_qty' => $VentillatedBatten_qty,
            'Trim-70%-Swirl-Brushed' => $Trim70SwirlBrushed,
            'Trim-70%-Swirl-Brushed_qty' => $Trim70SwirlBrushed_qty,
            '70%Vintage-Ruff-Moss' => $T70VintageRuffMoss,
            '70%Vintage-Ruff-Moss_qty' => $T70VintageRuffMoss_qty,
            'Random-Swirl-Brushed'=> $RandomSwirlBrushed,
            'Random-Swirl-Brushed_qty'=> $RandomSwirlBrushed_qty,
            'European-Eave-Closures'=>  $EuropeanEaveClosures,
            'European-Eave-Closures_qty'=>$EuropeanEaveClosures_qty,
            'Hip-Ridge-Rake' => $HipRidgeRake,
            'Hip-Ridge-Rake_qty' =>$HipRidgeRake_qty,
            'Hip-Starters' =>  $HipStarters,
            'Hip-Starters_qty' =>$HipStarters_qty,
            'Trim-Swirl-Brushed'=>$TrimSwirlBrushed,
            'Trim-Swirl-Brushed_qty'=>$TrimSwirlBrushed_qty ,
            'Turret-Closures'=>$TurretClosures,
            'Turret-Closures_qty'=>$TurretClosures_qty,
             'Need-To-Send'=> $NeedToSend,
             'Need-To-Send_qty'=>$NeedToSend_qty,
            'COVER-TRACK'=> $COVERTRACK,
            'COVER-TRACK_qty'=>$COVERTRACK_qty,
            'Old-Mission'=>$OldMission,
            'Old-Mission_qty'=> $OldMission_qty,
            'Mission-Eave-Closures'=> $MissionEaveClosures ,
             'Mission-Eave-Closures_qty'=> $MissionEaveClosures_qty,
             'Mission-Hip-Ridge-Rake'=>$MissionHipRidgeRake ,
             'Mission-Hip-Ridge-Rake_qty'=>  $MissionHipRidgeRake_qty,
             'Mission-Hip-Starters'=>$MissionHipStarters ,
            'Mission-Hip-Starters_qty'=>$MissionHipStarters_qty,  

            'Toscana_cut'=> $Toscanacut,
             'Toscana_cut_qty'=> $Toscana_cut_qty,
             'Straight-Brushed'=>$StraightBrushed,
             'Straight-Brushed_qty'=>$StraightBrushed_qty,
             '50%Straight-Brushed'=>$T50StraightBrushed,
             '50%Straight-Brushed_qty'=>$T50StraightBrushed_qty,
             'Signa-True-Slate'=>$SignaTrueSlate,
             'Signa-True-Slate_qty'=>$SignaTrueSlate_qty,
             'Low-Pitch'=>$LowPitch,
            'Low-Pitch_qty'=>$LowPitch_qty,
            'Rake-Metal-Option'=>$RakeMetalOption,
            'Rake-Metal-Option_qty'=>$RakeMetalOption_qty,
            'Solid-Gable-Tiles'=> $SolidGableTiles,
            'Solid-Gable-Tiles_qty'=>$SolidGableTiles_qty,
            'Nails-RingShanks'=>$NailsRingShanks,
            'Nails-RingShanks_qty'=>$NailsRingShanks_qty,
        ];

        $html = file_get_contents('template.html');

          // Initialize the $newContent variable as an empty string
            $newContent = '';

            // Generate table rows based on the $data array
            foreach ($data as $name => $value) {
               if($value !== "") {
                $newContent .= '
                    <tr>
                        <td>' . $name . '</td>
                        <td>' . $value . '</td>
                    </tr>
                ';
               }
                
            }

            // Read the contents of the HTML file
            $html = file_get_contents('template.html');

            // Find the position of <tbody> in the HTML content
            $startPos = strpos($html, '<tbody>');

            if ($startPos !== false) {
                // Insert the new content after <tbody>
                $startPos += strlen('<tbody>');
                $html = substr_replace($html, $newContent, $startPos, 0);
            }

         
            $userEmail = $_GET["user_email"];
            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);

            $dompdf = new Dompdf($options);


            // Define the path to the "pdf" folder in the root directory
            $pdfFolderPath = __DIR__ . "/pdf/";

            // Ensure the "pdf" folder exists; if not, create it
            if (!is_dir($pdfFolderPath)) {
                mkdir($pdfFolderPath, 0755, true);
            }

            // Sanitize the email address to remove invalid characters
            $cleanedEmail = filter_var($userEmail, FILTER_SANITIZE_EMAIL);
            // Create a filename
           
   

            $dompdf->loadHtml($html);


            $dompdf->setPaper('A4', 'portrait');


            $dompdf->render();
            $pageCount = $dompdf->getCanvas()->get_page_count();

            $filename = $cleanedEmail . "-" . $pageCount . ".pdf";
           // echo $_GET["user_email"];

          // Save the PDF to the "pdf" folder
            $pdfPath = $pdfFolderPath . $filename;
            file_put_contents($pdfPath, $dompdf->output());


            $thankYouMessage = '';
            if ($pageCount > 0) {
                $thankYouMessage = 'Thank you! we have saved your information. you will soon get docsign mail for next procedure.';
                
                  header("Location: index.php?thankyou=" . urlencode($thankYouMessage));
                exit; 
            }
            else
            {
                echo "Error! Please try again after sometimes";
            }
            
          
        


?>