<?php 
namespace Goldstar;

class Descriptions
{
    const LOADING = [
        'Container was loaded at Port of Loading to Port of Discharge',
        'Container was loaded at Port of Loading to Transshipment Port',
        'Container was loaded at Transshipment Port to Transshipment Port',
        'Container was loaded at Transsihipment Port to Port of Discharge',
        'Barge was loaded at Port of Loading to Transshipment Port'
    ];

    const DEPARTURE = [
        'Vessel departure from Port of Loading to Port of Discharge',
        'Vessel departure from Port of Loading to Transshipment Port',
        'Vessel departure from first Transshipment Port to next Transshipment Port',
        'Vessel departure from Transshipment Port to Port of Discharge'
    ];

    const ARRIVAL = [
        'Vessel arrival to Port of Discharge',
        'Vessel arrival to Transshipment Port'
    ];

    const DISCHARGE = [
        'Container was discharged at Port of Destination',
        'Container was discharged at Transshipment Port',
        'Barge was discharged at Port of Discharge'
    ];

    const GATE_OUT = [
        'Import gate-out from Port of Discharge to Customer',
        'Import Gate-Out from Port of Discharge to Customer',
        'Import truck departure from inland point to Customer',
        'Import truck departure from Port of Discharge to Customer',
        'Import rail departure from Port of Discharge to intermediate place',
        'Import gate-out from inland point to Customer',
        'Import Gate-Out from inland point to Customer',
        'Strip at Port of Discharge',
        'Gate out to client',
        'Import truck departure from final ramp to Customer',
        //'Import Gate-Out from Port of Discharge to intermediate point',
        'Container was stripped at Port of Discharge',
        'Container was stripped at Inland Location'
    ];
    /*
    const TRANSHIPMENT = [
        'Container was discharged at Transshipment Port',
        'Vessel arrival to Transshipment Port'
    ];
*/
    const EMPTY_RETURN = [
        'Empty return from Customer',
        'Empty container returned from Customer',
        'Direct move from Import to Export (Im)',
        'Empty container gate in'
    ];
}
