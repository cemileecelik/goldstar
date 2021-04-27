<?php

use Goldstar\Descriptions;

function createTopInfos($response)
{
    preg_match_all('/<table class="table table1">(.*?)<\/table>/', $response, $topTables);

    $tempTopInfos = [];
    foreach ($topTables[1] as $key => $table) {
        preg_match_all('/<td.*?>(.*?)<\/td>/', $table, $tempInfo);


        foreach ($tempInfo[1] as $v_key => $value) {

            if ($v_key == 0) {
                preg_match('/(.*?).\([A-Z]{3}\)$/', $value, $markedAs);
                $tempTopInfos[$key]['marked'] = $markedAs[1];
            }

            if ($v_key == 1)
                $tempTopInfos[$key]['port'] = $value;

            if ($value == 'Sailing Date')
                $tempTopInfos[$key]['dateOfLoading'] = [
                    'date' => $tempInfo[1][5],
                    'isActual' => false
                ];

            if ($value == 'Estimated Time of Arrival')
                $tempTopInfos[$key]['dateOfDischarge'] = [
                    'date' => $tempInfo[1][5],
                    'isActual' => false
                ];
        }
    }

    return $tempTopInfos;
}

function createRouteInfos($response)
{
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

                $routeInfos[$key][$dateIndex] = [
                    'date' => trim($tempInfo[1][$j_key + 1]),
                    'isActual' => false
                ];
            }
        }
    }
    return $routeInfos;
}



function createContainerInfos($response)
{

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
    return $containers;
}


function getMovements($topInfos, $routeInfos)
{
    //$movements = [];
    if (!in_array('Port of Discharge', $routeInfos[count($routeInfos) - 1]))
        $routeInfos[] = $topInfos[1];

    if (!in_array('Port of Loading', $routeInfos[0]))
        array_unshift($routeInfos, $topInfos[0]);

    return $routeInfos;
}

function createTotalMovs($topInfos, $routeInfos)
{

    $totalMovs = [];
    if (!empty($routeInfos) && !empty($topInfos))
        $totalMovs = getMovements($topInfos, $routeInfos);

    return $totalMovs;
}

function createContainerMovements($containers)
{
    $CInfos = [];
    foreach ($containers as $containerKey => $container) {

        foreach ($container['movements'] as $key => $a) {
            if (in_array($a['lastActivity'], Descriptions::LOADING)) {
                $CInfos[$containerKey][] = [
                    'type' => "LOADING",
                    'marked' => findMarked($a['lastActivity']),
                    'vessel' => $a['vessel'],
                    'date' => $a['date'],
                    'port' => $a['location']
                ];
            }


            if (in_array($a['lastActivity'], Descriptions::DEPARTURE)) {
                $CInfos[$containerKey][] = [
                    'type' => "DEPARTURE",
                    'marked' => findMarked($a['lastActivity']),
                    'vessel' => $a['vessel'],
                    'date' => $a['date'],
                    'port' => $a['location']
                ];
            }

            if (in_array($a['lastActivity'], Descriptions::ARRIVAL)) {
                $CInfos[$containerKey][] = [
                    'type' => "ARRIVAL",
                    'marked' => findMarked($a['lastActivity']),
                    'vessel' => $a['vessel'],
                    'date' => $a['date'],
                    'port' => $a['location']
                ];
            }

            if (in_array($a['lastActivity'], Descriptions::DISCHARGE)) {
                $CInfos[$containerKey][] = [
                    'type' => "DISCHARGE",
                    'marked' => findMarked($a['lastActivity']),
                    'vessel' => $a['vessel'],
                    'date' => $a['date'],
                    'port' => $a['location']
                ];
            }

            if (in_array($a['lastActivity'], Descriptions::GATE_OUT)) {
                $CInfos[$containerKey][] = [
                    'type' => "GATE_OUT",
                    'marked' => findMarked($a['lastActivity']),
                    'vessel' => $a['vessel'],
                    'date' => $a['date'],
                    'port' => $a['location']
                ];
            }

            if (in_array($a['lastActivity'], Descriptions::EMPTY_RETURN)) {
                $CInfos[$containerKey][] = [
                    'type' => "EMPTY_RETURN",
                    'marked' => findMarked($a['lastActivity']),
                    'vessel' => $a['vessel'],
                    'date' => $a['date'],
                    'port' => $a['location']
                ];
            }
        }
    }

    return $CInfos;
}

function findMarked($description)
{
    if (preg_match('/^Container was loaded at Port of Loading to.*?$/', $description))
        return 'Port of Loading';
    if (preg_match('/^Container was loaded at Transshipment .*?$/', $description) || preg_match('/^Container was loaded at Transsihipment .*?$/', $description))
        return 'TS';
    if ($description == 'Container was discharged at Port of Destination' || $description == 'Barge was discharged at Port of Discharge')
        return 'Port of Discharge';
    if ($description == 'Container was discharged at Transshipment Port')
        return 'TS';
}
