<?php
/*
Template Name: builder
*/
$args = array(
    'category' => array( 'builder' ),
    'orderby'  => 'name',
);
$products = wc_get_products( $args );
$title;
$texture;
$Color;
$Edge_Design;
$Weight_options;
$description;
$Layout_Options;
$Trim_Options;


$texture_image;
$Edge_Design_image;
$Weight_options_image;
$Layout_Options_image;
$Trim_Options_image;



foreach ( $products as $product ) { 
    if($product->get_title() == $_GET['product']) {

        $title = $product->get_title();
        $description = $product->get_description();

    }

}


        $apiKey = 'patiGRTQJYUcmo8Mn.3a67be81c12f21260ada8882b7136056641458d9b0666486f3a4e2514feba6ff'; 
        $baseId = 'appsTFypv0n43d6ke'; 
        $tableName = 'Profile-1';
        

        $endpoint = "https://api.airtable.com/v0/{$baseId}/{$tableName}";
        
            // Set up cURL
            $ch1 = curl_init();
            curl_setopt($ch1, CURLOPT_URL, $endpoint);
            curl_setopt($ch1, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $apiKey,
            ]);
            curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true); // Set this option to capture the response
            
            $response1 = curl_exec($ch1);
            
            // Check for cURL errors
            if (curl_errno($ch1)) {
                echo 'Curl error: ' . curl_error($ch1);
            } else {
     
                $data = json_decode($response1, true);
                
                
                 $matchingRow = null;
                foreach ($data['records'] as $record) {
                    $recordName = $record['fields']['Name']; 
                    if ($recordName === $_GET['product']) {
                        $matchingRow = $record;
                        break; 
                    }
                }
                
    $texture = explode(", ",$matchingRow["fields"]["Surface Texture Options"]);
    $Edge_Design = explode(", ",$matchingRow["fields"]["Edge Options"]);
    $Weight_options  = explode(", ",$matchingRow["fields"]["Weight Options"]);
    $Layout_Options = explode(", ",$matchingRow["fields"]["Layout Options"]);
    $Trim_Options = explode(", ",$matchingRow["fields"]["Trim Options"]);
    
    
            }
            
            curl_close($ch1);
            
            
            
        



?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Builder page</title>
</head>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/style_builder.css">
<body>

    <div class="lightbox">
        <img src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/x.svg" class="close-btn" alt="close"/>
        <img src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/VintageSlate.png" class="gallery-image" />
    </div>
    <div class="profile-container">
        <div class="Builder_logo">
       <a href="https://bartile.goaspendigital.com/">
        <img src="/wp-content/uploads/2023/03/Maskgroup.png" class="logoRed" />
        </a>
    </div>
            <!---<h4>Select options based on</h4>--->
            <div class="builder-btn">
                    <a href="https://bartile.goaspendigital.com/" class="primary-btn">
                         <img src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/arrow.png" class="arrow-white"/>
                        Back
                       
                    </a>
                </div>
                
      
    </div>
    <div class="Main-selection_box">
         <h2>Select Profiles</h2>
         <ul>
             <li class="dropdown">
                 <a href="#">Slate <img class="Dropdown"src="https://bartile.goaspendigital.com/wp-content/uploads/2023/10/chevron-down.svg"/></a>
                 <div class="dropdown-content">
                    <a href="https://bartile.goaspendigital.com/builder/?product=New%20England%20Slate">New England Slate</a>
                    <a href="https://bartile.goaspendigital.com/builder/?product=Legendary%20Slate">Legendary Slate</a>
                 </div>
             </li>
              <li class="dropdown">
                 <a href="#">Split Timber <img class="Dropdown"src="https://bartile.goaspendigital.com/wp-content/uploads/2023/10/chevron-down.svg"/></a>
                   <div class="dropdown-content">
                        <a href="https://bartile.goaspendigital.com/builder/?product=Split%20Timber">Split Timber</a>
                       <a href="https://bartile.goaspendigital.com/builder/?product=Legendary%20Split%20Timber">Legendary Split Timber</a>
                  </div>
             </li> 
             <li>
                 <a href="https://bartile.goaspendigital.com/builder/?product=Sierra%20Mission">Sierra Mission</a>
             </li> 
             <li>
                 <a href="https://bartile.goaspendigital.com/builder/?product=European">European</a>
             </li>
              <li class="dropdown">
                 <a href="#">Yorkshire <img class="Dropdown"src="https://bartile.goaspendigital.com/wp-content/uploads/2023/10/chevron-down.svg"/></a>
                    <div class="dropdown-content">
                       <a href="https://bartile.goaspendigital.com/builder/?product=Yorkshire%20Slate">Yorkshire Slate</a>
                       <a href="https://bartile.goaspendigital.com/builder/?product=Yorkshire%20Split%20Timber">Yorkshire Split Timber</a>
                    </div>
             </li>
         </ul>
    </div>
    <div class="contact-info contact-none">
        <img src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/x-circle.svg" class="close-btn"/>
          <div class="submit-header">
                <h1>Get a Quote</h1>
                <h1 class="product-title"><?php echo $title; ?></h1>
          </div>
          <div class="contact-container">
                <form action="/send-quote/" class="submit-form" method="post" enctype="multipart/form-data" name="test">
                <div class="INlineinput">
                <input type="text" name="rname" placeholder="Recipient Name*"/>
                <input type="email" name="remail" placeholder="Recipient Email*"/>
                </div>
                <div class="INlineinput">
                <input type="number" name="rzip" placeholder="Project Zip code*"/>
                <input type="number" name="rphone" placeholder="Phone Number*"/>
                </div>
                <div class="additional">
                <label for="additional-comments">Additional Comments:</label>
  <textarea id="additional-comments" name="additional-comments" rows="4" cols="50"></textarea>
                </div>
                <div class="File-add">
                    <label for="file-upload">Upload 'Eagle View' Aerial View:</label>
  <input type="file" id="file-upload" name="eagle-view" accept="image/*">
                </div>
                <label for="checkbox" class="checkbox">
    <input type="checkbox" id="checkbox" name="acceptance">
    I accept the Terms &amp; Conditions and Privacy Policy
  </label>
  
  <input type="hidden" name="profile" value="<?php echo $title; ?>" />
                    <input type="submit" value="Submit From" class="quote-submit"/>
                    
            
                </form>
                <div class="content-table">
                    <!-- <div class="customer-information">
                        <div class="row">
                            <h1 class="customer-name">madhavendra</h1>
                            <h5 class="customer-email">madhavendra@gmail.com</h5>
                        </div>
                        <div class="row">
                            <h1 class="customer-phone">7017809869</h1>
                            <div class="test"></div>
                        </div>
                        
                    </div> -->
                    <div class="request-quote">
                        <div class="selection-options-bar-quote">
                            
                        </div>
                    </div>
                </div>
        </div>
    </div>
    
    
    <div class="container-builder">
        <div class="builder-images">
            <div class="builder-image-header">
                <div class="builder-title">
                  <div class="selections">
                  <div class="selected">
                <h1>Selected Profile: <span class="in_title"><?php echo $title; ?><img src="https://bartile.goaspendigital.com/wp-content/uploads/2023/10/Buildercheck.svg" class="check-box"/></span></h1>
            </div>
        </div>
                    <!-----<h1><?php echo $title; ?></h1>
                    <p><?php echo $description; ?></p>---->
                </div>
                <a href="#" class="genrate-pdf Dissabeld Top_genrate-pdf d-none">Request A Quote <img src="https://bartile.goaspendigital.com/wp-content/uploads/2023/03/arrow.svg" class="At"/></a>
            </div>
            
            <?php

// Convert the title to lowercase and replace spaces with hyphens
$imageFileName = str_replace(' ', '-', strtolower($title));

// Construct the image URL
$imageURL = "https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/{$imageFileName}.png";
?>
            <div class="container-selected-options">
                <!--<a href="#" class="genrate-pdf Dissabeld Top_genrate-pdf d-none">Request A Quote</a>-->
                <img src="<?php echo $imageURL; ?>" class="main-image" alt="<?php echo $title; ?>" />
                <div class="selection-options-bar">
                    <!-- <img src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/selection/Calais Blend European.jpg" class="selected-img" alt="selected-img"/>
                    <img src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/selection/Standard-Cut.jpg" class="selected-img" alt="selected-img"/> -->
                    <!-- <img src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/selection/Standard-Texture.jpg" class="selected-img" alt="selected-img"/> -->
                    <!-- <img src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/selection/Super-Duty.jpg" class="selected-img" alt="selected-img"/> -->
                </div>
            </div>
           

        </div>
        <div class="builder-options">
            <h1>Select Texture. <spen class="gray-color">Pick your favorite</spen></h1>
            <div class="additional-charges">
                                <img src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/info.png" class="info-icon"/>
                                <p>Additional charges may apply. For price details, get in contact with our Bartile expert in the 'Get a Quote' option.</p>
                            </div>
           <div class="texture-container">
    <?php
    if (!empty($texture)) {
        foreach ($texture as $textur) {
            if (!empty($textur)) {
    ?>
                <div class="texture-type">
                    <div class="<?php echo str_replace(" ","-",$textur); ?> pattern-box" image-type="<?php echo $textur; ?>"></div>
                    <h1><?php echo $textur; ?></h1>
                    <p>The Swirl Brushed option gives our European profile texture and vibrance unsurpassed by any clay tile.</p>
                </div>
    <?php
            }
        }
    }
    ?>
</div>
            
       
           <div class="edge-design-container">
    <?php 
     $filterededgeOptions = array_filter($Edge_Design);
    if (!empty($filterededgeOptions )) {
    ?>
     <h1 id="edge_design">Edge Design</h1>  
    <?php
    }

    foreach ($Edge_Design as $Edge_Designs) {
        if (!empty($Edge_Designs)) {
    ?>
    <div class="edge-design-type">
        <div class="<?php echo str_replace(" ","-",$Edge_Designs); ?> pattern-box" image-type="<?php echo $Edge_Designs; ?>">
        </div>
        <h1><?php echo $Edge_Designs; ?></h1>
        <p>The Swirl Brushed option gives our European profile texture and vibrance unsurpassed by any clay tile.</p>
    </div>
    <?php
        }
    }
    ?>
</div>

            <h1 id="Weight_option">Weight Options</h1>     
           <div class="Weight-container">
               <?php 
               foreach ($Weight_options as $Weight_option) {   
               ?>
                    <div class="Weight-type">
                        <div class="<?php echo str_replace(" ","-",$Weight_option); ?> pattern-box" image-type="<?php echo $Weight_option; ?>">
                        </div>
                        <h1><?php echo $Weight_option; ?></h1>
                        <p>The Swirl Brushed option gives our European profile texture and vibrance unsurpassed by any clay tile.</p>

                    </div>
        <?php
               }
        ?>
            </div>

<div class="Layout-container">
    <?php 
     $filteredLayoutOptions = array_filter($Layout_Options);
    if (!empty($filteredLayoutOptions)) {
    ?>
    <h1 id="Layout_Options">Layout Options</h1>
    <?php
    }

    foreach ($Layout_Options as $Layout_option) {
        if (!empty($Layout_option)) {
    ?>
    <div class="Layout-type">
        <div class="<?php echo str_replace(" ","-",$Layout_option); ?> pattern-box" image-type="<?php echo $Layout_option; ?>">
        </div>
        <h1><?php echo $Layout_option; ?></h1>
        <p>The Swirl Brushed option gives our European profile texture and vibrance unsurpassed by any clay tile.</p>
    </div>
    <?php
        }
    }
    ?>
</div>

            <h1 id="Trim_Options">Trim Options</h1>     
           <div class="Trim-container">
               <?php 
               foreach ($Trim_Options as $Trim_option) {   
               ?>
                    <div class="Trim-type">
                        <div class="<?php echo str_replace(" ","-",$Trim_option); ?> pattern-box" image-type="<?php echo $Trim_option; ?>" >
                        </div>
                        <h1><?php echo $Trim_option; ?></h1>
                        <p>The Swirl Brushed option gives our European profile texture and vibrance unsurpassed by any clay tile.</p>

                    </div>
        <?php
               }
        ?>
            </div>




       <!--  <h1 id="standard-colors">Standard Colors</h1>  -->
       <!--   <h4>Color - Calais Blend European</h4>---->
       <!--<div class="color-options">-->
       <!--     <img src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/selection/CalaisBlendEuropean.jpg" class="color-type CalaisBlend" alt="color-image"/>-->
       <!--     <img src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/selection/Color_ChateauGrayEuropean.jpg" class="color-type ChateauGray" alt="color-image"/>-->
       <!--     <img src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/selection/Color_BarcelonaBlendEuropean.jpg" class="color-type BarcelonaBlend" alt="color-image"/>-->
       <!--     <img src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/selection/Color_CorsicaBlendEuropean.jpg" class="color-type CorsicaBlend" alt="color-image"/>-->
       <!--     <img src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/selection/Color_GranadaRedEuropean.jpg" class="color-type GranadaRed" alt="color-image"/>-->
       <!--      <img src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/selection/Color_MadridClayEuropean.jpg" class="color-type MadridClay" alt="color-image">-->
       <!--     <img src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/img/selection/Color_NormadyBrownEuropean.jpg" class="color-type NormadyBrown" alt="color-image"/>-->
       <!--</div>-->
          <a href="#" class="genrate-pdf Dissabeld">Request A Quote</a>
       </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>    
    <script src="https://bartile.goaspendigital.com/wp-content/themes/twentytwentyone/js/main.js" type="text/javascript"></script>
</body>