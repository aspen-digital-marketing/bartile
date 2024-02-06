<?php 
require __DIR__ . '/vendor/autoload.php';

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
print_r($values);

echo "dfgd"; ?>