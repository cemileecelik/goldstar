<?php
include 'descriptions.php';
include 'Functions.php';


$handle = curl_init();

if (!isset($_GET['number']))
    die("number yok");

if (!preg_match('/^[A-Z]{4,7}[0-9]{7,}$/', $_GET['number']))
    die("number eşleşmiyor");


$postParams = [
    'containerid' => $_GET['number']
];

$url = "https://www.gslltd.com.hk/get_tracing.php";
//$url = "https://restcountries.eu/rest/v2";

curl_setopt_array(
    $handle,
    array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => $postParams
    )
);

$output = curl_exec($handle);
curl_close($handle);

$response = preg_replace('/\s+/', ' ', $output);

if (preg_match('/<i class="fa fa-search" aria-hidden="true"><\/i> No Record Found.<\/h3>/', $response))
    die("kayıt yok");

//print_r($response);

$topInfos = createTopInfos($response);
//print_r($topInfos);


$routeInfos = createRouteInfos($response);
//print_r($routeInfos);


$containerInfos = createContainerInfos($response);
print_r($containerInfos);

$totalMovs = createTotalMovs($topInfos, $routeInfos);
//print_r($totalMovs);

$containerMovements = createContainerMovements($containerInfos);
print_r($containerMovements);
die();