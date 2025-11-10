<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$city = isset($_GET['city']) ? $_GET['city'] : 'cesis,latvia';
$url = 'https://emo.lv/weather-api/forecast/?city=' . urlencode($city);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

if (!$response) {
  echo json_encode(['error' => 'Failed to fetch from API']);
  exit;
}

echo $response;
?>
