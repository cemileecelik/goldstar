<?php

namespace Goldstar;

use Goldstar\Descriptions;

include_once 'webscbr.php';


foreach ($containers as $containerKey => $container) {

    $CInfos = [];
    foreach ($container['movements'] as $key => $a) {
        if (in_array($a['lastActivity'], Descriptions::LOADING)) {
            $CInfos[$key]['type'] = "LOADING";
            $CInfos[$key]['marked'] = findMarked($a['lastActivity']);
            $CInfos[$key]['vessel'] = $a['vessel'];
            $CInfos[$key]['date'] = $a['date'];
            $CInfos[$key]['port'] = $a['location'];
        }


        if (in_array($a['lastActivity'], Descriptions::DEPARTURE)) {
            $CInfos[$key]['type'] = "DEPARTURE";
            $CInfos[$key]['marked'] = findMarked($a['lastActivity']);
            $CInfos[$key]['vessel'] = $a['vessel'];
            $CInfos[$key]['date'] = $a['date'];
            $CInfos[$key]['port'] = $a['location'];
        }

        if (in_array($a['lastActivity'], Descriptions::ARRIVAL)) {
            $CInfos[$key]['type'] = "ARRIVAL";
            $CInfos[$key]['marked'] = findMarked($a['lastActivity']);
            $CInfos[$key]['vessel'] = $a['vessel'];
            $CInfos[$key]['date'] = $a['date'];
            $CInfos[$key]['port'] = $a['location'];
        }

        if (in_array($a['lastActivity'], Descriptions::DISCHARGE)) {
            $CInfos[$key]['type'] = "DISCHARGE";
            $CInfos[$key]['marked'] = findMarked($a['lastActivity']);
            $CInfos[$key]['vessel'] = $a['vessel'];
            $CInfos[$key]['date'] = $a['date'];
            $CInfos[$key]['port'] = $a['location'];
        }

        if (in_array($a['lastActivity'], Descriptions::GATE_OUT)) {
            $CInfos[$key]['type'] = "GATE_OUT";
            $CInfos[$key]['marked'] = findMarked($a['lastActivity']);
            $CInfos[$key]['vessel'] = $a['vessel'];
            $CInfos[$key]['date'] = $a['date'];
            $CInfos[$key]['port'] = $a['location'];
        }

        if (in_array($a['lastActivity'], Descriptions::EMPTY_RETURN)) {
            $CInfos[$key]['type'] = "EMPTY_RETURN";
            $CInfos[$key]['marked'] = findMarked($a['lastActivity']);
            $CInfos[$key]['vessel'] = $a['vessel'];
            $CInfos[$key]['date'] = $a['date'];
            $CInfos[$key]['port'] = $a['location'];
        }
    }
}

print_r($CInfos);

function findMarked($description)
{
    if (preg_match('/^Container was loaded at .*?$/', $description) || preg_match('/Barge was loaded at Port of Loading to Transshipment Port.*?$/', $description))
        return 'Port of Loading';
    if (preg_match('/^Vessel departure from.*?$/', $description))
        return 'ts';
    if (preg_match('/^Vessel arrival to.*?$/', $description))
        return 'port of discharge';
    if (preg_match('/^Container was discharged.*?$/', $description) || preg_match('/^Container was discharged.*?$/', $description))
        return 'Port of Discharge';
    if (preg_match('/^Empty.*?$/', $description) || preg_match('/^Direct move from Import to Export.*?$/', $description))
        return 'Empty Return';
    if (preg_match('/^(Import|Strip at|Container was stripped|Gate out)/', $description))
        return 'Gate Out';
}

