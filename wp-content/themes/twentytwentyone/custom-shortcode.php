<?php
 /**
    * Template Name: google sheet
    */
    
    
    get_header();
    
    if($_POST){
        	require $_SERVER['DOCUMENT_ROOT'].'/googlesheet/vendor/autoload.php';

$client = new \Google_Client();

$client->setApplicationName('googlesheetbartile');

$client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);

$client->setAccessType('offline');

$client->setAuthConfig(__DIR__ . '/credentials.json');

$service = new Google_Service_Sheets($client);
 $spreadsheetId = "1sG2zjnqlaOxZPlmNPBpRIou382r7JZk3FTytMoYlzMo"; //It is present in your URL

       $get_range = 'Sheet1!A1:H10';
	   $response = $service->spreadsheets_values->get($spreadsheetId,$get_range);

       $values = $response->getValues();
$newRow = [
    '456740',
    'Hellboy',
    'https://image.tmdb.org/t/p/w500/bk8LyaMqUtaQ9hUShuvFznQYQKR.jpg',
    "Hellboy comes to England, where he must defeat Nimue, Merlin's consort and the Blood Queen. But their battle will bring about the end of the world, a fate he desperately tries to turn away.",
    '1554944400',
    'Fantasy, Action'
];
$rows = [$newRow]; // you can append several rows at once
$valueRange = new \Google_Service_Sheets_ValueRange();
$valueRange->setValues($rows);
$range = 'Sheet1'; // the service will detect the last row of this sheet
$options = ['valueInputOption' => 'USER_ENTERED'];
$service->spreadsheets_values->append($spreadsheetId, $range, $valueRange, $options);

echo "dfgd";
   }
?>

   <div class="contact-info-relative">
          <div class="submit-header">
                <h1>Get a Quote</h1>
                <h1 class="product-title"><?php echo $title; ?></h1>
          </div>
          <div class="contact-container">
                <form action="#" class="submit-form" method="post" enctype="multipart/form-data" name="test">
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

                <label for="checkbox" class="checkbox">
    <input type="checkbox" id="checkbox" name="acceptance">
    I accept the Terms &amp; Conditions and Privacy Policy
  </label>
                    <input type="submit" value="Submit From" class="quote-submit"/>
                </form>
               
        </div>
    </div>
    <?php get_footer(); ?>