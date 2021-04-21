<?php
namespace Goldstar;
use Goldstar\Descriptions;

include_once'webscbr.php';


foreach ($containers as $containerKey => $container) {

    $CInfos = [];
    foreach ($container['movements'] as $key => $a) {
        if (in_array($a['lastActivity'], Descriptions::LOADING)) {
            $CInfos[$key]['type'] = "LOADING";
            $CInfos[$key]['vessel'] = $a['vessel'];
            $CInfos[$key]['date'] = $a['date'];
            $CInfos[$key]['port'] = $a['location'];
        }

        
    if (in_array($a['lastActivity'], Descriptions::DEPARTURE)) {
        $CInfos[$key]['type'] = "DEPARTURE";
        $CInfos[$key]['vessel'] = $a['vessel'];
        $CInfos[$key]['date'] = $a['date'];
        $CInfos[$key]['port'] = $a['location'];
    }

    if (in_array($a['lastActivity'], Descriptions::ARRIVAL)) {
        $CInfos[$key]['type'] = "ARRIVAL";
        $CInfos[$key]['vessel'] = $a['vessel'];
        $CInfos[$key]['date'] = $a['date'];
        $CInfos[$key]['port'] = $a['location'];
    }
    
    if (in_array($a['lastActivity'], Descriptions::DISCHARGE)) {
        $CInfos[$key]['type'] = "DISCHARGE";
        $CInfos[$key]['vessel'] = $a['vessel'];
        $CInfos[$key]['date'] = $a['date'];
        $CInfos[$key]['port'] = $a['location'];
    }

    if (in_array($a['lastActivity'], Descriptions::GATE_OUT)) {
        $CInfos[$key]['type'] = "GATE_OUT";
        $CInfos[$key]['vessel'] = $a['vessel'];
        $CInfos[$key]['date'] = $a['date'];
        $CInfos[$key]['port'] = $a['location'];
    }

    if (in_array($a['lastActivity'], Descriptions::EMPTY_RETURN)) {
        $CInfos[$key]['type'] = "EMPTY_RETURN";
        $CInfos[$key]['vessel'] = $a['vessel'];
        $CInfos[$key]['date'] = $a['date'];
        $CInfos[$key]['port'] = $a['location'];
    }
    }

print_r($CInfos);
}


