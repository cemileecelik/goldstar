<?php
include 'descriptions.php';


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

preg_match_all('/<table class="table table1">(.*?)<\/table>/', $response, $topTables);

$topInfos = [];
foreach ($topTables[1] as $key => $table) {
    preg_match_all('/<td.*?>(.*?)<\/td>/', $table, $tempInfo);


    foreach ($tempInfo[1] as $v_key => $value) {

        if ($v_key == 0) {
            preg_match('/(.*?).\([A-Z]{3}\)$/', $value, $markedAs);
            $topInfos[$key]['marked'] = $markedAs[1];
        }

        if ($v_key == 1)
            $topInfos[$key]['port'] = $value;

        if ($value == 'Sailing Date')
            $topInfos[$key]['dateOfLoading'] = $tempInfo[1][5];

        if ($value == 'Estimated Time of Arrival')
            $topInfos[$key]['dateOfDischarge'] = $tempInfo[1][5];
    }
}

//print_r($topInfos);

preg_match_all('/<div class="table-responsive p-1">(.*?)<\/table>/', $response, $routeTables);


$routeInfos = [];
foreach ($routeTables[1] as $key => $table) {
    preg_match_all('/<p.*?>(.*?)<\/p>/', $table, $tempInfo);

    foreach ($tempInfo[1] as $j_key => $value) {

        if (in_array($value, ['Port of Loading', 'Transshipment', 'Port of Discharge'])) {
            $routeInfos[$key]['marked'] = $value;
            $routeInfos[$key]['port'] = $tempInfo[1][$j_key + 1];
        }
        if (trim($value) == 'Vessel / Voyage')
            $routeInfos[$key]['vessel'] = $tempInfo[1][$j_key + 1];

        if (in_array(trim($value), ['ETD', 'ETA'])) {
            $dateIndex = (trim($value) == 'ETD') ? 'dateOfLoading' : 'dateOfDischarge';

            $routeInfos[$key][$dateIndex] = trim($tempInfo[1][$j_key + 1]);
        }
    }
    // $routeInfos[] = $tempInfo[1];
}

//print_r($routeInfos);

preg_match_all('/<tr class="accordion-toggle (?:collapsed)?".(.*?)>.<\/tr>/', $response, $contTables);

$contInfos = [];
foreach ($contTables[1] as $table) {
    preg_match_all('/<td.*?>(.*?)<\/td>/', $table, $tempInfo);

    $contInfos[] = $tempInfo[1];
}

//print_r($contTables);



preg_match_all('/<table class="table table2">.(.*?).\/table>/', $response, $movementTables);

$movementInfos = [];
foreach ($movementTables[1] as $table) {
    preg_match_all('/<td.*?>(.*?)<\/td>/', $table, $tempInfo);

    $movementInfos[] = $tempInfo[1];
}

//print_r($movementInfos);

$containers = [];
for ($i = 0; $i < count($contInfos); $i++) {
    $number = explode(' ', $contInfos[$i][0])[0];
    $size = substr(explode(' ', $contInfos[$i][0])[1], 0, 2);
    $type = substr(explode(' ', $contInfos[$i][0])[1], 2, 2);

    $containers[$i]['container'] = [
        'number' => $number,
        'size' => $size,
        'type' => $type
    ];

    for ($j = 0; $j < count($movementInfos[$i]); $j = $j + 5) {

        $containers[$i]['movements'][] = [
            'lastActivity' => $movementInfos[$i][$j + 1],
            'location' => $movementInfos[$i][$j + 2],
            'date' => $movementInfos[$i][$j + 3],
            'vessel' => $movementInfos[$i][$j + 4]
        ];
    }
}

//print_r($containers);

function getMovements($topInfos, $routeInfos)
{
    //$movements = [];
    if (!in_array('Port of Discharge', $routeInfos[count($routeInfos) - 1])) {
        $routeInfos[] = $topInfos[1];
    }

    if (!in_array('Port of Loading', $routeInfos[0])) {
        array_unshift($routeInfos, $topInfos[0]);
    }

    return $routeInfos;
}

$totalMovs = [];

if (!empty($routeInfos) && !empty($topInfos))
    $totalMovs = getMovements($topInfos, $routeInfos);



//print_r($totalMovs);


//  exit;

