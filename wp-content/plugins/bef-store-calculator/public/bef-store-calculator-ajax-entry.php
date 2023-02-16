<?php

if (!defined('WPINC')) { // MUST have WordPress.
    exit('Do not access this file directly.');
}
//AJAX request
add_action("wp_ajax_my_egrid", "my_egrid");
add_action("wp_ajax_nopriv_my_egrid", "my_egrid");

// Flight Distance calculations
add_action("wp_ajax_get_flight_distance", "get_flight_distance");
add_action("wp_ajax_nopriv_get_flight_distance", "get_flight_distance");

// All Airports Request
add_action("wp_ajax_get_all_airports", "get_all_airports");
add_action("wp_ajax_nopriv_get_all_airports", "get_all_airports");

/**
 * Pulls from $_REQUEST.   Queries individual result
 */
function my_egrid() {

    if ( !wp_verify_nonce( $_REQUEST['nonce'], "bef-store-calculator")) {
        exit("No access.  Missing information!");
    }

    $zipCode = $_REQUEST['zipcode'];

    $egrid_code = get_egrid_data($zipCode);

    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
//        $encoded = mb_convert_encoding($egrid_code, 'UTF-8', 'UTF-8');
        $result = json_encode($egrid_code);
        echo $result;
    }
    else {
        header("Location: ".$_SERVER["HTTP_REFERER"]);
    }
    die();
}

/**
 * Pulls from $_REQUEST.   Queries individual result
 */
function get_flight_distance() {

    if ( !wp_verify_nonce( $_REQUEST['nonce'], "bef-store-calculator")) {
        exit("No access.  Missing information!");
    }

    // DEPARTURE & ARRIVAL REQUESTS
    $airport_a = $_REQUEST['airport_a'];
    $airport_b = $_REQUEST['airport_b'];
    $result = '';

    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $result = get_flight_calculation($airport_a, $airport_b);
    }
    else {
        header("Location: ".$_SERVER["HTTP_REFERER"]);
    }

    echo $result;

    die();

}


/**
 * Pulls from $_REQUEST.   Queries individual result
 */
function get_all_airports() {

    if ( !wp_verify_nonce( $_REQUEST['nonce'], "bef-store-calculator")) {
        exit("No access.  Missing information!");
    }

    // DEPARTURE & ARRIVAL REQUESTS
    $airport = $_REQUEST['airport'];
    $titles = array();
    $results = '';

    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $result = get_airport_response($airport);

        $result = $result[0];

        foreach( $result as $r )

            $titles[] = [
                'label' => $r->name . ' (' . ($r->iatacode ? $r->iatacode : $r->localcode) . ')',
                'value' => ($r->iatacode ? $r->iatacode : $r->localcode)
            ];

        $results = json_encode($titles);
    }
    else {
        header("Location: ".$_SERVER["HTTP_REFERER"]);
    }

    echo $results;
    die();

}

/**
 * Takes in 2 airport codes, finds lat/long results and calculates miles between points.
 * If no airport codes are given, then return zero as result.
 * This function will ONLY be called if either of those conditions are met.
 * This gate logic exists in the radioFlightComponent rowListener function.
 * @param $airport_a
 * @param $airport_b
 * @return float|int
 */
function get_flight_calculation($airport_a, $airport_b) {
    if ($airport_a !== '' && $airport_b !== '') {
        $functions = new bef_store_flight_calculator_airports();
        $query_results = $functions->get_airports($airport_a, $airport_b);

        $result = get_haversine_calculation(
            $query_results[0]->latitude,
            $query_results[0]->longitude,
            $query_results[1]->latitude,
            $query_results[1]->longitude,
        );
        return $result * 2;
    } else {
        return 0;
    }
};


/**
 * Pulls request from $_REQUEST.   Queries individual result
 */
function get_egrid_data($zipcode)
{
    $functions = new bef_store_calculator_electrcity_grid();
    return $functions->get_egrid($zipcode);
}

/**
 * Pulls request from $_REQUEST.   Queries individual result
 */
function get_airport_response($airport)
{
    $functions = new bef_store_flight_calculator_airports();
    return $functions->get_total_airports($airport);

}

/**
 * Haversine formula
 * This formula calculates the flight distance between two airports
 */
function get_haversine_calculation($lat1, $lon1, $lat2, $lon2)
{
    $pi_calc = (M_PI / 180);

    $R = 6371000; // Earth's radius in meters
    $latitude_1 = $lat1 * $pi_calc;
    $latitude_2 = $lat2 * $pi_calc;
    $latitude_radians = ($lat2-$lat1) * $pi_calc;
    $longitude_radians = ($lon2-$lon1) * $pi_calc;

    $a = sin($latitude_radians/2) * sin($latitude_radians/2) +
        cos($latitude_1) * cos($latitude_2) *
        sin($longitude_radians/2) * sin($longitude_radians/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));

    $d = $R * $c; // in metres

    // meters * 0.000621371 = miles

    $result = ($d / 1000) / 1.609;

    return round($result);

}